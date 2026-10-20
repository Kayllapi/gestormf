<?php

namespace Tests\Feature;

use App\Http\Controllers\Layouts\Backoffice\Sistema\UsuarioaccesoController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UsuarioAccesoJerarquiaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'jerarquia', 'database.connections.jerarquia' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('permiso', function (Blueprint $t) {
            $t->integer('id'); $t->integer('rango');
        });
        Schema::create('users_permiso', function (Blueprint $t) {
            $t->increments('id'); $t->integer('idusers'); $t->integer('idtienda');
            $t->integer('idpermiso'); $t->integer('idestado'); $t->integer('idsession');
        });
        Schema::create('userspermisoacceso', function (Blueprint $t) {
            $t->integer('idusers'); $t->integer('idtienda'); $t->integer('idpermiso');
            $t->integer('idmodulo'); $t->integer('idestado');
        });
        Schema::create('permisoacceso', function (Blueprint $t) {
            $t->integer('idpermiso'); $t->integer('idmodulo');
        });
        Schema::create('modulo', fn (Blueprint $t) => $t->integer('id'));
        DB::table('permiso')->insert([
            ['id' => 1, 'rango' => 3], ['id' => 2, 'rango' => 1],
            ['id' => 4, 'rango' => 7], ['id' => 5, 'rango' => 2],
        ]);
        foreach([1 => 2, 2 => 1, 4 => 1, 5 => 1] as $cargo => $sesion){
            DB::table('users_permiso')->insert([
                'idusers' => 10, 'idtienda' => 194, 'idpermiso' => $cargo,
                'idestado' => 1, 'idsession' => $sesion,
            ]);
        }
        DB::table('modulo')->insert(['id' => 99]);
        DB::table('userspermisoacceso')->insert([
            'idusers' => 10, 'idtienda' => 194, 'idpermiso' => 2,
            'idmodulo' => 99, 'idestado' => 1,
        ]);
        Auth::shouldReceive('user')->andReturn((object) ['id' => 10]);
    }

    private function filas(): array
    {
        return DB::table('users_permiso')->where('idusers', 10)->get()
            ->map(fn ($f) => ['idtienda' => $f->idtienda, 'idpermiso' => $f->idpermiso, 'idestado' => $f->idestado])->all();
    }

    private function guardar(array $filas, array $modulos = []): array
    {
        $request = Request::create('/', 'PUT', [
            'view' => 'permiso', 'accesos' => json_encode($filas), 'modulos' => json_encode($modulos),
        ]);
        return (new UsuarioaccesoController)->update($request, 194, 10)->getData(true);
    }

    public function test_edita_fila_inferior_del_propio_usuario_y_conserva_filas_protegidas(): void
    {
        $antes = DB::table('users_permiso')->whereIn('idpermiso', [1, 2, 5])->get()->all();
        $filas = $this->filas();
        $filas[2] = ['idtienda' => 198, 'idpermiso' => 4, 'idestado' => 2];
        $this->assertSame('CORRECTO', $this->guardar($filas)['resultado']);
        $this->assertEquals($antes, DB::table('users_permiso')->whereIn('idpermiso', [1, 2, 5])->get()->all());
        $this->assertSame(2, DB::table('users_permiso')->where('idpermiso', 4)->value('idestado'));
        $this->assertSame(198, DB::table('users_permiso')->where('idpermiso', 4)->value('idtienda'));
    }

    public function test_rechaza_cambiar_estado_de_cada_rango_protegido(): void
    {
        foreach([1, 2, 5] as $cargo){
            $filas = $this->filas();
            foreach($filas as &$fila){ if($fila['idpermiso'] === $cargo){ $fila['idestado'] = 2; } }
            unset($fila);
            $this->assertSame('ERROR', $this->guardar($filas)['resultado']);
            $this->assertSame(1, DB::table('users_permiso')->where('idpermiso', $cargo)->value('idestado'));
        }
    }

    public function test_rechaza_quitar_o_mover_una_fila_protegida(): void
    {
        $filas = $this->filas();
        array_shift($filas);
        $this->assertSame('ERROR', $this->guardar($filas)['resultado']);
        $filas = $this->filas();
        $filas[0]['idtienda'] = 198;
        $this->assertSame('ERROR', $this->guardar($filas)['resultado']);
    }

    public function test_rechaza_asignar_un_cargo_protegido_a_otra_agencia(): void
    {
        $filas = $this->filas();
        $filas[] = ['idtienda' => 198, 'idpermiso' => 1, 'idestado' => 1];
        $this->assertSame('ERROR', $this->guardar($filas)['resultado']);
        $this->assertSame(4, DB::table('users_permiso')->count());
    }

    public function test_conserva_permisos_protegidos_y_guarda_permisos_de_fila_editable(): void
    {
        $this->assertSame('CORRECTO', $this->guardar($this->filas(), [
            '194_2' => ['99' => 2], '194_4' => ['99' => 1],
        ])['resultado']);
        $this->assertSame(1, DB::table('userspermisoacceso')->where('idpermiso', 2)->value('idestado'));
        $this->assertSame(1, DB::table('userspermisoacceso')->where('idpermiso', 4)->value('idestado'));
    }
}
