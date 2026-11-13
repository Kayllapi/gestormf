<?php

namespace Tests\Feature;

use App\Http\Controllers\Layouts\Backoffice\Sistema\MasterController;
use App\Http\Controllers\Layouts\Backoffice\Sistema\UsuarioaccesoController;
use App\Http\Controllers\Layouts\Backoffice\Sistema\UsuarioaccesoMasterController;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class UsuarioMasterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'master_test', 'database.connections.master_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('users', function (Blueprint $t) {
            $t->increments('id');
            $t->string('usuario')->unique();
            $t->string('password')->nullable();
            $t->string('clave')->nullable();
            $t->boolean('es_master')->default(false);
            $t->integer('idtipousuario')->default(1);
            $t->integer('idestadousuario')->default(1);
            $t->integer('idestado')->default(1);
            $t->integer('idtienda')->default(194);
            $t->integer('intentos_maximo')->default(3);
            $t->integer('intentos_fallidos')->default(0);
            $t->integer('iduser_modificacion')->default(0);
            foreach (['codigo', 'idtipopersona', 'nombre', 'apellidopaterno', 'apellidomaterno',
                'nombrecompleto', 'razonsocial', 'identificacion', 'email', 'imagen', 'numerotelefono',
                'direccion', 'mapa_latitud', 'mapa_longitud', 'email_verified_at', 'idubigeo',
                'iduserspadre', 'profesion', 'nivelcredito', 'ecaja', 'idgenero', 'fechanacimiento',
                'idestadocivil', 'idnivelestudio', 'estadocreditoprendario', 'estadocreditonoprendario'] as $campo) {
                $t->string($campo)->nullable();
            }
            $t->timestamps();
        });
        Schema::create('tienda', function (Blueprint $t) {
            $t->integer('id'); $t->string('nombre'); $t->integer('idestado')->default(1);
            $t->string('nombreagencia')->default('Agencia');
        });
        Schema::create('permiso', function (Blueprint $t) {
            $t->integer('id'); $t->string('nombre'); $t->integer('rango');
            $t->integer('idtienda')->default(194);
        });
        Schema::create('modulo', function (Blueprint $t) {
            $t->integer('id'); $t->integer('idestado')->default(1);
            $t->string('controlador')->default('');
            $t->string('nombre')->default('Modulo'); $t->string('vista')->default('');
            $t->integer('idmodulo')->default(7); $t->integer('orden')->default(1);
        });
        Schema::create('ubigeo', fn (Blueprint $t) => $t->integer('id'));
        Schema::create('f_estadocivil', function (Blueprint $t) {
            $t->integer('id'); $t->string('nombre');
        });
        DB::table('ubigeo')->insert(['id' => 1]);
        DB::table('f_estadocivil')->insert(['id' => 1, 'nombre' => 'Soltero']);
        Schema::create('s_config', function (Blueprint $t) {
            $t->integer('idtienda'); $t->string('nombre'); $t->string('valor');
        });
        Schema::create('permisoacceso', function (Blueprint $t) {
            $t->integer('idpermiso'); $t->integer('idmodulo');
        });
        Schema::create('userspermisoacceso', function (Blueprint $t) {
            foreach (['idusers', 'idtienda', 'idpermiso', 'idmodulo', 'idestado'] as $campo) {
                $t->integer($campo);
            }
        });
        Schema::create('users_permiso', function (Blueprint $t) {
            $t->increments('id');
            foreach (['idusers', 'idtienda', 'idpermiso', 'idestado', 'idsession'] as $campo) {
                $t->integer($campo);
            }
        });
        DB::table('users')->insert([
            ['id' => 10, 'usuario' => 'master', 'es_master' => true],
            ['id' => 20, 'usuario' => 'normal', 'es_master' => false],
        ]);
        DB::table('tienda')->insert([
            ['id' => 194, 'nombre' => 'Agencia A'], ['id' => 198, 'nombre' => 'Agencia B'],
        ]);
        DB::table('permiso')->insert(['id' => 2, 'nombre' => 'Gerente', 'rango' => 1]);
        DB::table('modulo')->insert([['id' => 1, 'idestado' => 1], ['id' => 2, 'idestado' => 2]]);
        $this->actingAs(User::find(10));
        unset($GLOBALS['__idtienda_actual_cache']);
        unset($GLOBALS['__configuracion_cache']);
        Route::resource('/prueba-master/{idtienda}/usuarios', UsuarioaccesoMasterController::class);
    }

    private function cambiarAgencia(int $agencia)
    {
        $request = Request::create('/', 'GET', ['idtienda' => $agencia]);
        $request->setLaravelSession($this->app['session.store']);
        return (new MasterController)->show($request, 194, 'show_cambiarsucursal');
    }

    public function test_master_tiene_modulos_actuales_y_futuros_sin_asignaciones(): void
    {
        $this->assertSame([1], modulos_acceso());
        DB::table('modulo')->insert(['id' => 3]);
        $this->assertSame([1, 3], modulos_acceso());
        $this->assertTrue(auth()->user()->hasRole('backoffice/194/usuarioacceso', 194));
        $this->assertSame(0, DB::table('users_permiso')->count());
    }

    public function test_puede_cambiar_a_agencia_nueva_sin_permisos_asignados(): void
    {
        DB::table('tienda')->insert(['id' => 200, 'nombre' => 'Nueva']);
        $this->assertSame('CORRECTO', $this->cambiarAgencia(200)->getData(true)['resultado']);
        $this->assertSame(200, user_permiso()->idtienda);
        $this->assertSame(200, idtienda_actual());
        $this->assertSame('Master', user_permiso()->nombrepermiso);
        $this->cambiarAgencia(198);
        $this->assertSame(198, idtienda_actual());
    }

    public function test_no_puede_cambiar_a_agencia_inactiva(): void
    {
        DB::table('tienda')->whereId(198)->update(['idestado' => 2]);
        $this->expectException(HttpException::class);
        $this->cambiarAgencia(198);
    }

    public function test_usuario_comun_conserva_sus_permisos_y_excepciones(): void
    {
        $this->actingAs(User::find(20));
        DB::table('permisoacceso')->insert(['idpermiso' => 2, 'idmodulo' => 1]);
        $this->assertSame([1], modulos_acceso(2));
        DB::table('userspermisoacceso')->insert([
            'idusers' => 20, 'idtienda' => 194, 'idpermiso' => 2, 'idmodulo' => 1, 'idestado' => 2,
        ]);
        $this->assertSame([], modulos_acceso(2));
    }

    public function test_usuario_comun_no_puede_seleccionar_acceso_de_otro_usuario(): void
    {
        DB::table('users_permiso')->insert([
            'id' => 30, 'idusers' => 10, 'idtienda' => 198, 'idpermiso' => 2, 'idestado' => 1, 'idsession' => 1,
        ]);
        $this->actingAs(User::find(20));
        $request = Request::create('/', 'GET', ['idpermiso' => 30]);
        $this->expectException(HttpException::class);
        (new MasterController)->show($request, 194, 'show_cambiarsucursal');
    }

    public function test_usuario_comun_no_puede_modificar_cuenta_master(): void
    {
        $this->actingAs(User::find(20));
        $this->expectException(HttpException::class);
        (new UsuarioaccesoController)->update(Request::create('/', 'PUT', ['view' => 'permiso']), 194, 10);
    }

    public function test_activar_y_revocar_por_comando_sin_modificar_cargos(): void
    {
        $this->artisan('usuario:master normal')->assertSuccessful();
        $this->assertTrue(User::find(20)->es_master);
        $this->artisan('usuario:master normal --revocar')->assertSuccessful();
        $this->assertFalse(User::find(20)->es_master);
        $this->assertSame(0, DB::table('users_permiso')->count());
    }

    public function test_comando_rechaza_cuentas_inactivas_y_logins_inexistentes(): void
    {
        DB::table('users')->whereId(20)->update(['idestadousuario' => 2]);
        $this->artisan('usuario:master normal')->assertFailed();
        $this->artisan('usuario:master inexistente')->assertFailed();
        $this->assertFalse(User::find(20)->es_master);
    }

    public function test_campo_master_no_es_asignable_desde_formularios(): void
    {
        $usuario = User::find(20);
        $usuario->fill(['es_master' => true]);
        $this->assertFalse($usuario->es_master);
    }

    public function test_cuenta_master_inactiva_no_obtiene_privilegios(): void
    {
        auth()->user()->idestadousuario = 2;
        $this->assertFalse(usuario_es_master());
    }

    public function test_master_puede_solicitar_y_confirmar_castigos_en_cualquier_agencia(): void
    {
        $this->assertTrue(\App\Services\CastigoCredito::puedeSolicitar(10, 198));
        $this->assertTrue(\App\Services\CastigoCredito::puedeConfirmar(10, 198));
        $this->assertFalse(\App\Services\CastigoCredito::puedeSolicitar(20, 198));
        $this->assertFalse(\App\Services\CastigoCredito::puedeConfirmar(20, 198));
    }

    public function test_perfil_no_permite_modificar_otro_usuario(): void
    {
        $this->actingAs(User::find(20));
        $this->expectException(HttpException::class);
        (new MasterController)->update(Request::create('/', 'PUT', ['view' => 'editarperfil']), 194, 10);
    }

    public function test_master_cambia_su_password_desde_otra_agencia_con_su_clave_actual(): void
    {
        $usuario = auth()->user();
        $usuario->password = \Illuminate\Support\Facades\Hash::make('actual');
        $usuario->save();
        $request = Request::create('/', 'PUT', [
            'view' => 'editarpassword', 'antpassword' => 'actual',
            'password' => 'nueva', 'password_confirmation' => 'nueva',
        ]);
        $this->assertSame('CORRECTO', (new MasterController)->update($request, 198, 10)->getData(true)['resultado']);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('nueva', User::find(10)->password));
    }

    public function test_crea_master_desde_terminal_listo_para_iniciar_sesion_sin_cargos(): void
    {
        $this->artisan('usuario:master nuevo_master --crear')
            ->expectsQuestion('Nombre completo', 'Administrador Master')
            ->expectsQuestion('Contrasena (6 a 50 caracteres)', 'Abc123')
            ->expectsQuestion('Confirmar contrasena', 'Abc123')
            ->assertSuccessful();
        $usuario = User::where('usuario', 'nuevo_master')->sole();
        $this->assertTrue($usuario->esMaster());
        $this->assertSame(0, $usuario->intentos_maximo);
        $this->assertSame(0, $usuario->intentos_fallidos);
        $this->assertSame('Administrador Master', $usuario->nombrecompleto);
        $this->assertNotNull($usuario->email_verified_at);
        $this->assertSame('U'.str_pad($usuario->id, 8, '0', STR_PAD_LEFT), $usuario->codigo);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('Abc123', $usuario->password));
        $this->assertSame(0, DB::table('users_permiso')->where('idusers', $usuario->id)->count());
        $this->assertTrue(auth()->attempt(['usuario' => 'nuevo_master', 'password' => 'Abc123', 'idestadousuario' => 1]));
        $this->assertSame([1], modulos_acceso());
        $this->assertSame(194, user_permiso()->idtienda);
    }

    public function test_crear_no_sobrescribe_login_existente_ni_admite_opciones_contradictorias(): void
    {
        $this->artisan('usuario:master normal --crear')->assertFailed();
        $this->artisan('usuario:master nuevo --crear --revocar')->assertFailed();
        $this->assertFalse(User::find(20)->es_master);
        $this->assertSame(2, User::count());
    }

    public function test_crear_rechaza_datos_invalidos_sin_guardar_cuentas(): void
    {
        foreach ([['', 'ClaveSegura123!', 'ClaveSegura123!'],
            ['Master', 'corta', 'corta'], ['Master', 'ClaveSegura123!', 'otra'],
            ['Master', str_repeat('a', 51), str_repeat('a', 51)]] as [$nombre, $password, $confirmacion]) {
            $this->artisan('usuario:master nuevo --crear')
                ->expectsQuestion('Nombre completo', $nombre)
                ->expectsQuestion('Contrasena (6 a 50 caracteres)', $password)
                ->expectsQuestion('Confirmar contrasena', $confirmacion)
                ->assertFailed();
        }
        $this->assertSame(2, User::count());
    }

    public function test_crear_requiere_agencia_activa(): void
    {
        DB::table('tienda')->update(['idestado' => 2]);
        $this->artisan('usuario:master nuevo --crear')->assertFailed();
        $this->assertSame(2, User::count());
    }

    public function test_registro_gerente_admite_password_corto_sin_aplicar_el_minimo_del_master(): void
    {
        $this->postJson('/prueba-master/194/usuarios', $this->datosGerente(['password' => '123']))
            ->assertOk()->assertJson(['resultado' => 'CORRECTO']);
        $usuario = User::where('usuario', 'gerente_nuevo')->sole();
        $this->assertFalse($usuario->es_master);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('123', $usuario->password));
        $this->assertTrue(auth()->attempt(['usuario' => 'gerente_nuevo', 'password' => '123', 'idestadousuario' => 1]));
    }

    private function datosGerente(array $cambios = []): array
    {
        return array_replace([
            'view' => 'registrar', 'idagencia' => 198, 'usuario' => 'gerente_nuevo',
            'password' => 'ClaveSegura123!', 'idestadousuario' => 1,
            'apellido_parterno' => 'Perez', 'apellido_marterno' => 'Lopez', 'nombres' => 'Ana',
            'identificacion' => '12345678', 'direccion' => 'Calle 1', 'idubigeo' => 1,
            'fecha_nacimiento' => '1990-01-01', 'celular' => '999999999',
            'idestadodivil' => 1, 'profesion' => 'Administrador',
        ], $cambios);
    }

    public function test_modulo_gerentes_rechaza_usuario_comun_incluso_gerente_general(): void
    {
        $this->actingAs(User::find(20));
        DB::table('users_permiso')->insert([
            'idusers' => 20, 'idpermiso' => 2, 'idtienda' => 194, 'idestado' => 1, 'idsession' => 2,
        ]);
        foreach (['/prueba-master/194/usuarios', '/prueba-master/194/usuarios/create',
            '/prueba-master/194/usuarios/show_table', '/prueba-master/194/usuarios/10/edit'] as $ruta) {
            $this->getJson($ruta)->assertForbidden();
        }
        $this->postJson('/prueba-master/194/usuarios', $this->datosGerente())->assertForbidden();
        $this->putJson('/prueba-master/194/usuarios/10', [])->assertForbidden();
        $this->deleteJson('/prueba-master/194/usuarios/10')->assertForbidden();
        $this->assertSame(2, User::count());
    }

    public function test_master_registra_solo_gerente_en_agencia_seleccionada_sin_poder_inyectar_permisos(): void
    {
        DB::table('permiso')->insert(['id' => 4, 'nombre' => 'Analista', 'rango' => 7]);
        $this->postJson('/prueba-master/194/usuarios', $this->datosGerente([
            'es_master' => true, 'idpermiso' => 4, 'idtienda' => 194,
            'accesos' => json_encode([['idtienda' => 194, 'idpermiso' => 4]]),
        ]))->assertOk()->assertJson(['resultado' => 'CORRECTO']);
        $usuario = User::where('usuario', 'gerente_nuevo')->sole();
        $this->assertSame(198, $usuario->idtienda);
        $this->assertFalse($usuario->es_master);
        $acceso = DB::table('users_permiso')->where('idusers', $usuario->id)->sole();
        $this->assertSame(198, $acceso->idtienda);
        $this->assertSame(2, $acceso->idpermiso);
        $this->assertSame(2, $acceso->idsession);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('ClaveSegura123!', $usuario->password));
        $this->getJson('/prueba-master/194/usuarios/show_table')->assertOk()
            ->assertJsonPath('recordsTotal', 1)->assertJsonPath('data.0.id', $usuario->id);
        $this->assertTrue(auth()->attempt(['usuario' => 'gerente_nuevo', 'password' => 'ClaveSegura123!', 'idestadousuario' => 1]));
        $this->assertSame(198, user_permiso()->idtienda);
        $this->assertSame(2, user_permiso()->idpermiso);
    }

    public function test_formulario_gerente_muestra_agencia_despues_de_datos_generales_y_cargo_fijo(): void
    {
        DB::table('tienda')->insert(['id' => 200, 'nombre' => 'Inactiva', 'nombreagencia' => 'Inactiva', 'idestado' => 2]);
        $this->get('/prueba-master/194/usuarios/create?view=registrar')->assertOk()
            ->assertSeeInOrder(['Datos Generales', 'id="idagencia"', 'Gerente General'], false)
            ->assertSee('value="198"', false)->assertDontSee('value="200"', false)
            ->assertDontSee('tabla-permisoacceso', false)->assertDontSee('listar_permisos', false);
    }

    public function test_registro_gerente_rechaza_agencias_invalidas_y_datos_incompletos(): void
    {
        DB::table('tienda')->whereId(198)->update(['idestado' => 2]);
        foreach (['', 198, 999] as $agencia) {
            $this->postJson('/prueba-master/194/usuarios', $this->datosGerente(['idagencia' => $agencia]))
                ->assertUnprocessable()->assertJsonValidationErrors('idagencia');
        }
        $this->postJson('/prueba-master/194/usuarios', $this->datosGerente(['idagencia' => 194, 'nombres' => '']))
            ->assertUnprocessable()->assertJsonValidationErrors('nombres');
        $this->assertSame(2, User::count());
        $this->assertSame(0, DB::table('users_permiso')->count());
    }

    public function test_registro_gerente_rechaza_login_duplicado_sin_crear_registros(): void
    {
        $this->postJson('/prueba-master/194/usuarios', $this->datosGerente(['usuario' => 'normal']))
            ->assertUnprocessable()->assertJsonValidationErrors('usuario');
        $this->assertSame(2, User::count());
        $this->assertSame(0, DB::table('users_permiso')->count());
    }

    public function test_registro_gerente_no_crea_cuenta_si_falta_el_cargo(): void
    {
        DB::table('permiso')->delete();
        $this->postJson('/prueba-master/194/usuarios', $this->datosGerente())
            ->assertOk()->assertJson(['resultado' => 'ERROR']);
        $this->assertSame(2, User::count());
    }

    public function test_registro_gerente_revierte_cuenta_si_no_puede_guardar_el_acceso(): void
    {
        Schema::drop('users_permiso');
        $this->postJson('/prueba-master/194/usuarios', $this->datosGerente())
            ->assertOk()->assertJson(['resultado' => 'ERROR'])->assertDontSee('ClaveSegura123!');
        $this->assertSame(2, User::count());
    }

    public function test_modulo_gerentes_no_se_habilita_a_cuentas_comunes_mediante_cargo_o_excepciones(): void
    {
        DB::table('modulo')->insert([
            'id' => 3, 'controlador' => 'Layouts\\Backoffice\\Sistema\\UsuarioaccesoMasterController',
        ]);
        DB::table('permisoacceso')->insert(['idpermiso' => 2, 'idmodulo' => 3]);
        $this->actingAs(User::find(20));
        $this->assertSame([], modulos_acceso(2));
        DB::table('userspermisoacceso')->insert([
            'idusers' => 20, 'idtienda' => 194, 'idpermiso' => 2, 'idmodulo' => 3, 'idestado' => 1,
        ]);
        $this->assertSame([], modulos_acceso(2));
        $this->actingAs(User::find(10));
        $this->assertContains(3, modulos_acceso());
    }

    public function test_instala_y_revierte_el_modulo_de_gerentes_sin_borrar_usuarios(): void
    {
        Schema::drop('modulo');
        Schema::create('modulo', function (Blueprint $t) {
            $t->increments('id'); $t->integer('idmodulo'); $t->integer('orden');
            $t->integer('idestado'); $t->string('nombre'); $t->string('vista'); $t->string('controlador');
        });
        DB::table('modulo')->insert([
            'id' => 188, 'idmodulo' => 47, 'orden' => 410, 'idestado' => 1, 'nombre' => 'Usuarios',
            'vista' => 'backoffice/{idtienda}/usuarioacceso',
            'controlador' => 'Layouts\\Backoffice\\Sistema\\UsuarioaccesoController',
        ]);
        $migration = require database_path('migrations/2026_11_12_000002_add_usuarioaccesomaster_module.php');
        $migration->up();
        $modulo = DB::table('modulo')->where('vista', 'backoffice/{idtienda}/usuarioaccesomaster')->sole();
        $this->assertSame(47, $modulo->idmodulo);
        $this->assertSame('Layouts\\Backoffice\\Sistema\\UsuarioaccesoMasterController', $modulo->controlador);
        $this->assertContains($modulo->id, modulos_acceso());
        $migration->down();
        $this->assertSame(1, DB::table('modulo')->count());
        $this->assertSame(2, User::count());
    }

    public function test_master_no_se_deshabilita_por_errores_de_password_y_puede_ingresar_despues(): void
    {
        config(['cache.default' => 'array']);
        auth()->logout();
        DB::table('users')->whereId(10)->update([
            'password' => \Illuminate\Support\Facades\Hash::make('ClaveSegura123!'),
            'intentos_maximo' => 3, 'intentos_fallidos' => 1,
        ]);
        for ($i = 0; $i < 4; $i++) {
            $this->postJson('/login', ['usuario' => 'master', 'password' => 'incorrecta'])->assertUnprocessable();
        }
        $usuario = User::find(10);
        $this->assertSame(1, $usuario->idestadousuario);
        $this->assertSame(1, $usuario->intentos_fallidos);
        $this->postJson('/login', ['usuario' => 'master', 'password' => 'ClaveSegura123!'])->assertNoContent();
        $this->assertAuthenticatedAs($usuario);
        $this->assertSame(0, User::find(10)->intentos_fallidos);
    }

    public function test_usuario_comun_conserva_bloqueo_por_intentos_fallidos(): void
    {
        config(['cache.default' => 'array']);
        auth()->logout();
        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/login', ['usuario' => 'normal', 'password' => 'incorrecta'])->assertUnprocessable();
        }
        $usuario = User::find(20);
        $this->assertSame(2, $usuario->idestadousuario);
        $this->assertSame(3, $usuario->intentos_fallidos);
    }

    public function test_master_deshabilitado_manualmente_no_puede_iniciar_sesion(): void
    {
        config(['cache.default' => 'array']);
        auth()->logout();
        DB::table('users')->whereId(10)->update([
            'password' => \Illuminate\Support\Facades\Hash::make('ClaveSegura123!'), 'idestadousuario' => 2,
        ]);
        $this->postJson('/login', ['usuario' => 'master', 'password' => 'ClaveSegura123!'])->assertUnprocessable();
        $this->assertGuest();
        $this->assertSame(2, User::find(10)->idestadousuario);
    }

    public function test_selectores_registrar_y_editar_solo_ofrecen_agencias_activas_asignadas_al_operador(): void
    {
        DB::table('tienda')->insert([
            ['id' => 200, 'nombre' => 'C', 'nombreagencia' => 'C', 'idestado' => 1],
            ['id' => 201, 'nombre' => 'D', 'nombreagencia' => 'D', 'idestado' => 2],
        ]);
        DB::table('permiso')->insert(['id' => 4, 'nombre' => 'Analista', 'rango' => 7]);
        foreach([[194, 2, 1], [194, 4, 1], [198, 2, 2], [200, 2, 1], [201, 2, 1]] as [$agencia, $cargo, $estado]){
            DB::table('users_permiso')->insert([
                'idusers' => 20, 'idtienda' => $agencia, 'idpermiso' => $cargo, 'idestado' => $estado, 'idsession' => 1,
            ]);
        }
        $this->actingAs(User::find(20));
        $controller = new UsuarioaccesoController;
        $create = $controller->create(Request::create('/', 'GET', ['view' => 'registrar']), 194)->getData();
        $edit = $controller->edit(Request::create('/', 'GET', ['view' => 'permiso']), 194, 20)->getData();
        $this->assertEqualsCanonicalizing([194, 200], $create['tiendas']->pluck('id')->all());
        $this->assertEqualsCanonicalizing([194, 200], $edit['tiendas']->pluck('id')->all());
        $this->assertArrayHasKey(198, $edit['agencias_asignadas']->all());
        $html = view('layouts.backoffice.sistema.usuarioacceso.permiso', $edit)->render();
        $this->assertStringContainsString('var AGENCIAS_A_CARGO = [194,200];', $html);
        $this->assertStringNotContainsString('<option value="198">', $html);
        $this->actingAs(User::find(10));
        $master = $controller->create(Request::create('/', 'GET', ['view' => 'registrar']), 194)->getData();
        $this->assertEqualsCanonicalizing([194, 198, 200], $master['tiendas']->pluck('id')->all());
    }

    public function test_registrar_no_permite_inyectar_agencia_fuera_de_las_asignadas(): void
    {
        DB::table('users_permiso')->insert([
            'idusers' => 20, 'idtienda' => 194, 'idpermiso' => 2, 'idestado' => 1, 'idsession' => 2,
        ]);
        $this->actingAs(User::find(20));
        try {
            (new UsuarioaccesoController)->store(Request::create('/', 'POST', $this->datosGerente([
                'accesos' => json_encode([['idtienda' => 198, 'idpermiso' => 2]]),
            ])), 194);
            $this->fail('No debe permitir asignar una agencia ajena.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertArrayHasKey('accesos.0.idtienda', $e->errors());
            $this->assertSame(2, User::count());
        }
    }

    public function test_operador_sin_agencias_asignadas_no_recibe_todas_por_defecto(): void
    {
        $this->actingAs(User::find(20));
        $datos = (new UsuarioaccesoController)->create(Request::create('/', 'GET', ['view' => 'registrar']), 194)->getData();
        $this->assertCount(0, $datos['tiendas']);
    }
}
