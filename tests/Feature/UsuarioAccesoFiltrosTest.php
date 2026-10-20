<?php

namespace Tests\Feature;

use App\Http\Controllers\Layouts\Backoffice\Sistema\UsuarioaccesoController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UsuarioAccesoFiltrosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'filtros', 'database.connections.filtros' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id');
            foreach(['codigo', 'identificacion', 'nombrecompleto', 'usuario', 'clave'] as $campo){
                $table->string($campo)->default('prueba');
            }
            $table->integer('idestado')->default(1);
            $table->integer('idtipousuario')->default(1);
            $table->integer('idestadousuario')->default(1);
        });
        Schema::create('tienda', function (Blueprint $table) {
            $table->integer('id');
            $table->string('nombre');
            $table->string('nombreagencia');
        });
        Schema::create('permiso', function (Blueprint $table) {
            $table->integer('id');
            $table->string('nombre');
        });
        Schema::create('users_permiso', function (Blueprint $table) {
            $table->integer('idusers');
            $table->integer('idtienda');
            $table->integer('idpermiso');
        });
        DB::table('users')->insert([['id' => 10], ['id' => 20], ['id' => 30]]);
        DB::table('tienda')->insert([
            ['id' => 194, 'nombre' => 'SERFIP', 'nombreagencia' => 'Ciudad U'],
            ['id' => 198, 'nombre' => 'MERCADITO', 'nombreagencia' => 'SAÑOS'],
        ]);
        DB::table('permiso')->insert([
            ['id' => 1, 'nombre' => 'Administrador'],
            ['id' => 2, 'nombre' => 'Analista'],
        ]);
        DB::table('users_permiso')->insert([
            ['idusers' => 10, 'idtienda' => 194, 'idpermiso' => 1],
            ['idusers' => 10, 'idtienda' => 194, 'idpermiso' => 2],
            ['idusers' => 10, 'idtienda' => 198, 'idpermiso' => 2],
            ['idusers' => 20, 'idtienda' => 194, 'idpermiso' => 1],
            ['idusers' => 30, 'idtienda' => 198, 'idpermiso' => 2],
        ]);
    }

    private function listar(array $filtros = [], int $inicio = 0, int $cantidad = 10): array
    {
        $columnas = array_fill(0, 8, ['search' => ['value' => '']]);
        foreach($filtros as $indice => $valor){
            $columnas[$indice]['search']['value'] = $valor;
        }
        $request = Request::create('/', 'GET', [
            'columns' => $columnas, 'start' => $inicio, 'length' => $cantidad, 'draw' => 1,
        ]);

        return (new UsuarioaccesoController)->show($request, 194, 'show_table')->getData(true);
    }

    public static function filtros(): array
    {
        return [
            'empresa de otra asignacion' => [[4 => 'MERCADITO']],
            'agencia con eñe' => [[5 => 'SAÑOS']],
            'empresa y agencia' => [[4 => 'MERCADITO', 5 => 'SAÑOS']],
            'columnas con asignaciones distintas' => [[4 => 'SERFIP', 5 => 'SAÑOS']],
            'cargo y agencia' => [[6 => 'Analista', 5 => 'SAÑOS']],
        ];
    }

    #[DataProvider('filtros')]
    public function test_busca_asignaciones_visibles_sin_incluir_usuarios_ajenos(array $filtros): void
    {
        $resultado = $this->listar($filtros);
        $this->assertSame([10], array_column($resultado['data'], 'id'));
        $this->assertSame(1, $resultado['recordsFiltered']);
        $this->assertStringContainsString('MERCADITO', $resultado['data'][0]['empresa']);
        $this->assertStringContainsString('SAÑOS', $resultado['data'][0]['agencia']);
    }

    public function test_mantiene_la_agencia_actual_sin_duplicados_y_la_paginacion(): void
    {
        $resultado = $this->listar();
        $this->assertSame([20, 10], array_column($resultado['data'], 'id'));
        $this->assertSame(2, $resultado['recordsFiltered']);
        $pagina = $this->listar([], 1, 1);
        $this->assertSame([10], array_column($pagina['data'], 'id'));
        $this->assertSame(2, $pagina['recordsFiltered']);
    }

    public function test_busqueda_sin_coincidencias_no_devuelve_usuarios(): void
    {
        $resultado = $this->listar([4 => 'INEXISTENTE']);
        $this->assertSame([], $resultado['data']);
        $this->assertSame(0, $resultado['recordsFiltered']);
    }
}
