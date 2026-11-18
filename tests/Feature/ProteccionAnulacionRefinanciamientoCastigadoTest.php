<?php

namespace Tests\Feature;

use App\Http\Controllers\Layouts\Backoffice\Sistema\PropuestaCreditoController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProteccionAnulacionRefinanciamientoCastigadoTest extends TestCase
{
    public function test_no_permite_anulacion_generica_de_una_cuenta_con_acuerdo(): void
    {
        config(['database.default' => 'proteccion_acuerdo_test', 'database.connections.proteccion_acuerdo_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('refinanciamiento_castigado_acuerdos', fn (Blueprint $table) => $table->integer('idcredito_nuevo'));
        DB::table('refinanciamiento_castigado_acuerdos')->insert(['idcredito_nuevo' => 42]);
        $controller = (new \ReflectionClass(PropuestaCreditoController::class))->newInstanceWithoutConstructor();
        $respuesta = $controller->update(Request::create('/', 'PUT', ['view' => 'cambiar_estado', 'estado' => 'ELIMINAR']), 1, 42);
        $this->assertSame('ERROR', $respuesta->getData(true)['resultado']);
        $this->assertDatabaseHas('refinanciamiento_castigado_acuerdos', ['idcredito_nuevo' => 42]);
    }
}
