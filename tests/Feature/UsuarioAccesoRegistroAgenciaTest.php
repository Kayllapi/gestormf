<?php

namespace Tests\Feature;

use App\Http\Controllers\Layouts\Backoffice\Sistema\UsuarioaccesoController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UsuarioAccesoRegistroAgenciaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'registro', 'database.connections.registro' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('tienda', fn (Blueprint $t) => $t->integer('id'));
        Schema::create('permiso', function (Blueprint $t) {
            $t->integer('id');
            $t->integer('rango');
        });
        Schema::create('users_permiso', function (Blueprint $t) {
            foreach (['idsession', 'idusers', 'idpermiso', 'idtienda', 'idestado'] as $campo) {
                $t->integer($campo);
            }
        });
        Schema::create('users', function (Blueprint $t) {
            $t->increments('id');
            $t->integer('idtienda');
            foreach (['codigo', 'idtipopersona', 'nombre', 'apellidopaterno', 'apellidomaterno',
                'razonsocial', 'nombrecompleto', 'identificacion', 'email', 'imagen',
                'numerotelefono', 'direccion', 'mapa_latitud', 'mapa_longitud', 'email_verified_at',
                'usuario', 'clave', 'password', 'idubigeo', 'iduserspadre', 'idtipousuario',
                'idestadousuario', 'idestado', 'profesion', 'nivelcredito', 'ecaja', 'idgenero',
                'fechanacimiento', 'idestadocivil', 'idnivelestudio'] as $campo) {
                $t->string($campo)->nullable();
            }
        });
        Schema::create('role_user', function (Blueprint $t) {
            $t->integer('user_id');
            $t->integer('role_id');
        });
        Schema::create('roles', fn (Blueprint $t) => $t->integer('id'));
        DB::table('tienda')->insert([['id' => 194], ['id' => 198]]);
        DB::table('permiso')->insert(['id' => 2, 'rango' => 1]);
        DB::table('users_permiso')->insert([
            'idusers' => 10, 'idpermiso' => 2, 'idtienda' => 194, 'idestado' => 1, 'idsession' => 2,
        ]);
        Auth::shouldReceive('user')->andReturn((object) ['id' => 10]);
    }

    private function registrar($agencia = 198)
    {
        return (new UsuarioaccesoController)->store(Request::create('/', 'POST', [
            'view' => 'registrar', 'idagencia' => $agencia, 'usuario' => 'nuevo',
            'password' => 'secreto', 'idestadousuario' => 1, 'apellido_parterno' => 'Perez',
            'apellido_marterno' => 'Lopez', 'nombres' => 'Ana', 'identificacion' => '12345678',
            'direccion' => 'Calle 1', 'idubigeo' => 1, 'fecha_nacimiento' => '1990-01-01',
            'celular' => '999999999', 'idestadodivil' => 1, 'profesion' => 'Contador',
            'accesos' => json_encode([['idtienda' => 198, 'idpermiso' => 2]]),
        ]), 194);
    }

    public function test_registra_en_agencia_seleccionada_aunque_el_login_exista_en_la_actual(): void
    {
        DB::table('users')->insert(['idtienda' => 194, 'usuario' => 'nuevo']);
        $this->assertSame('CORRECTO', $this->registrar()->getData(true)['resultado']);
        $this->assertSame(1, DB::table('users')->where('idtienda', 198)->where('usuario', 'nuevo')->count());
    }

    public function test_rechaza_login_duplicado_en_agencia_destino_sin_crear_usuario(): void
    {
        DB::table('users')->insert(['idtienda' => 198, 'usuario' => 'nuevo']);
        $this->assertSame('ERROR', $this->registrar()->getData(true)['resultado']);
        $this->assertSame(1, DB::table('users')->count());
    }

    public function test_exige_agencia_existente_antes_de_crear_usuario(): void
    {
        foreach (['', 999] as $agencia) {
            try {
                $this->registrar($agencia);
                $this->fail('Debe rechazar la agencia.');
            } catch (ValidationException $e) {
                $this->assertArrayHasKey('idagencia', $e->errors());
                $this->assertSame(0, DB::table('users')->count());
            }
        }
    }
}
