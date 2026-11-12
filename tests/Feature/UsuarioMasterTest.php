<?php

namespace Tests\Feature;

use App\Http\Controllers\Layouts\Backoffice\Sistema\MasterController;
use App\Http\Controllers\Layouts\Backoffice\Sistema\UsuarioaccesoController;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
            foreach (['codigo', 'idtipopersona', 'nombre', 'apellidopaterno', 'apellidomaterno',
                'nombrecompleto', 'razonsocial', 'identificacion', 'email', 'imagen', 'numerotelefono',
                'direccion', 'mapa_latitud', 'mapa_longitud', 'email_verified_at', 'idubigeo',
                'iduserspadre', 'profesion', 'nivelcredito', 'ecaja', 'idgenero', 'fechanacimiento',
                'idestadocivil', 'idnivelestudio'] as $campo) {
                $t->string($campo)->nullable();
            }
            $t->timestamps();
        });
        Schema::create('tienda', function (Blueprint $t) {
            $t->integer('id'); $t->string('nombre'); $t->integer('idestado')->default(1);
        });
        Schema::create('permiso', function (Blueprint $t) {
            $t->integer('id'); $t->string('nombre'); $t->integer('rango');
        });
        Schema::create('modulo', function (Blueprint $t) {
            $t->integer('id'); $t->integer('idestado')->default(1);
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
            ->expectsQuestion('Contrasena (12 a 50 caracteres)', 'ClaveSegura123!')
            ->expectsQuestion('Confirmar contrasena', 'ClaveSegura123!')
            ->assertSuccessful();
        $usuario = User::where('usuario', 'nuevo_master')->sole();
        $this->assertTrue($usuario->esMaster());
        $this->assertSame('Administrador Master', $usuario->nombrecompleto);
        $this->assertNotNull($usuario->email_verified_at);
        $this->assertSame('U'.str_pad($usuario->id, 8, '0', STR_PAD_LEFT), $usuario->codigo);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('ClaveSegura123!', $usuario->password));
        $this->assertSame(0, DB::table('users_permiso')->where('idusers', $usuario->id)->count());
        $this->assertTrue(auth()->attempt(['usuario' => 'nuevo_master', 'password' => 'ClaveSegura123!', 'idestadousuario' => 1]));
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
                ->expectsQuestion('Contrasena (12 a 50 caracteres)', $password)
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
}
