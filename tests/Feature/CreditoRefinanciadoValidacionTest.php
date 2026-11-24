<?php

namespace Tests\Feature;

use App\Http\Controllers\Layouts\Backoffice\Sistema\CreditoController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CreditoRefinanciadoValidacionTest extends TestCase
{
    public function test_solo_un_refinanciamiento_no_cancelado_bloquea_el_credito_no_prendario(): void
    {
        config(['database.default' => 'credito_refinanciado_test', 'database.connections.credito_refinanciado_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);

        Schema::create('credito', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('idcliente');
            $table->integer('idforma_credito');
            $table->integer('idmodalidad_credito');
            $table->integer('idestadocredito')->nullable();
            $table->string('estado');
            $table->integer('cuenta');
        });

        DB::table('credito')->insert([
            ['id' => 1, 'idcliente' => 10, 'idforma_credito' => 2, 'idmodalidad_credito' => 4, 'idestadocredito' => 1, 'estado' => 'DESEMBOLSADO', 'cuenta' => 187],
            ['id' => 2, 'idcliente' => 10, 'idforma_credito' => 2, 'idmodalidad_credito' => 4, 'idestadocredito' => 2, 'estado' => 'DESEMBOLSADO', 'cuenta' => 188],
            ['id' => 3, 'idcliente' => 10, 'idforma_credito' => 2, 'idmodalidad_credito' => 4, 'idestadocredito' => 1, 'estado' => 'ELIMINADO', 'cuenta' => 189],
        ]);

        $controller = (new \ReflectionClass(CreditoController::class))->newInstanceWithoutConstructor();
        $request = Request::create('/', 'GET', ['idcliente' => 10, 'idforma_credito' => 2]);
        $resultado = $controller->show($request, 1, 'show_verificarcreditorefinanciado');
        $this->assertSame('TIENE REFINANCIADO', $resultado['resultado']);
        $this->assertSame('C00000187', $resultado['cuenta']);

        DB::table('credito')->where('id', 1)->update(['idestadocredito' => 2]);
        $resultado = $controller->show($request, 1, 'show_verificarcreditorefinanciado');
        $this->assertSame('', $resultado['resultado']);
        $this->assertSame('', $resultado['cuenta']);
    }
}
