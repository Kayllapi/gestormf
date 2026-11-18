<?php

namespace Tests\Feature;

use App\Http\Controllers\Layouts\Backoffice\Sistema\CobranzacuotaController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CobranzaCreditoCerradoTest extends TestCase
{
    public function test_rechaza_pago_a_credito_cerrado_antes_de_realizar_operaciones(): void
    {
        config(['database.default' => 'cobranza_cerrado_test', 'database.connections.cobranza_cerrado_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('credito', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('estado');
            $table->integer('idestadocredito');
        });
        DB::table('credito')->insert(['id' => 1, 'estado' => 'DESEMBOLSADO', 'idestadocredito' => 2]);
        $controller = (new \ReflectionClass(CobranzacuotaController::class))->newInstanceWithoutConstructor();
        $this->expectException(ValidationException::class);
        $controller->store(Request::create('/', 'POST', ['view' => 'registrar', 'idcredito' => 1]), 1);
    }
}
