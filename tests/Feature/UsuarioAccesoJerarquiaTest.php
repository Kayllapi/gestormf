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
        Schema::create('tienda', function (Blueprint $t) {
            $t->integer('id'); $t->integer('idestado')->default(1); $t->string('nombreagencia');
        });
        DB::table('tienda')->insert([
            ['id' => 194, 'nombreagencia' => 'A'], ['id' => 198, 'nombreagencia' => 'B'],
            ['id' => 200, 'nombreagencia' => 'Fuera de alcance'],
        ]);
        Schema::create('users', fn (Blueprint $t) => $t->integer('id'));
        DB::table('users')->insert(['id' => 20]);
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
            ['id' => 6, 'rango' => 10],
        ]);
        foreach([1 => 2, 2 => 1, 4 => 1, 5 => 1] as $cargo => $sesion){
            DB::table('users_permiso')->insert([
                'idusers' => 10, 'idtienda' => 194, 'idpermiso' => $cargo,
                'idestado' => 1, 'idsession' => $sesion,
            ]);
        }
        DB::table('users_permiso')->insert([
            'idusers' => 10, 'idtienda' => 198, 'idpermiso' => 6, 'idestado' => 1, 'idsession' => 1,
        ]);
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

    private function guardar(array $filas, array $modulos = [], int $idusuario = 10): array
    {
        $request = Request::create('/', 'PUT', [
            'view' => 'permiso', 'accesos' => json_encode($filas), 'modulos' => json_encode($modulos),
        ]);
        return (new UsuarioaccesoController)->update($request, 194, $idusuario)->getData(true);
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
        $this->assertSame(5, DB::table('users_permiso')->count());
    }

    public function test_conserva_permisos_protegidos_y_guarda_permisos_de_fila_editable(): void
    {
        $this->assertSame('CORRECTO', $this->guardar($this->filas(), [
            '194_2' => ['99' => 2], '194_4' => ['99' => 1],
        ])['resultado']);
        $this->assertSame(1, DB::table('userspermisoacceso')->where('idpermiso', 2)->value('idestado'));
        $this->assertSame(1, DB::table('userspermisoacceso')->where('idpermiso', 4)->value('idestado'));
    }

    public function test_rechaza_asignar_agencia_ajena_aunque_el_cargo_sea_editable(): void
    {
        $filas = $this->filas();
        $filas[] = ['idtienda' => 200, 'idpermiso' => 4, 'idestado' => 1];
        $this->assertSame('ERROR', $this->guardar($filas)['resultado']);
        $this->assertSame(0, DB::table('users_permiso')->where('idtienda', 200)->count());
    }

    public function test_conserva_accesos_y_excepciones_de_agencia_ajena_al_editar_una_permitida(): void
    {
        $filas = [
            ['idtienda' => 194, 'idpermiso' => 1, 'idestado' => 1],
            ['idtienda' => 194, 'idpermiso' => 4, 'idestado' => 2],
            ['idtienda' => 200, 'idpermiso' => 4, 'idestado' => 1],
        ];
        foreach($filas as $fila){
            DB::table('users_permiso')->insert(array_merge($fila, ['idusers' => 20, 'idsession' => 1]));
        }
        DB::table('userspermisoacceso')->insert([
            'idusers' => 20, 'idtienda' => 200, 'idpermiso' => 4, 'idmodulo' => 99, 'idestado' => 1,
        ]);
        $antes = DB::table('users_permiso')->where('idusers',20)->where('idtienda',200)->first();
        $this->assertSame('CORRECTO', $this->guardar($filas, ['200_4' => ['99' => 2]], 20)['resultado']);
        $this->assertEquals($antes, DB::table('users_permiso')->where('idusers',20)->where('idtienda',200)->first());
        $this->assertSame(1, DB::table('userspermisoacceso')->where('idusers',20)->where('idtienda',200)->value('idestado'));
        $filas[2]['idestado'] = 2;
        $this->assertSame('ERROR', $this->guardar($filas, [], 20)['resultado']);
        array_pop($filas);
        $this->assertSame('ERROR', $this->guardar($filas, [], 20)['resultado']);
        $this->assertEquals($antes, DB::table('users_permiso')->where('idusers',20)->where('idtienda',200)->first());
    }
}
