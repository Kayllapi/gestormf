<?php

namespace Tests\Feature;

use App\Http\Controllers\Layouts\Backoffice\Sistema\RefinanciamientoCastigadosController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class VigenciaBorradorRefinanciamientoCastigadoTest extends TestCase
{
    public function test_limpia_solo_borradores_anteriores_al_dia_de_la_agencia(): void
    {
        config(['database.default' => 'borradores_test', 'database.connections.borradores_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('credito', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('idtienda');
        });
        Schema::create('refinanciamiento_castigado_borradores', function (Blueprint $table) {
            $table->integer('idcredito')->primary();
            $table->timestamp('updated_at');
        });
        DB::table('credito')->insert([
            ['id' => 1, 'idtienda' => 1], ['id' => 2, 'idtienda' => 1], ['id' => 3, 'idtienda' => 2],
        ]);
        DB::table('refinanciamiento_castigado_borradores')->insert([
            ['idcredito' => 1, 'updated_at' => '2026-11-17 23:59:59'],
            ['idcredito' => 2, 'updated_at' => '2026-11-18 00:00:00'],
            ['idcredito' => 3, 'updated_at' => '2026-11-17 23:59:59'],
        ]);
        Carbon::setTestNow(Carbon::parse('2026-11-18 10:00:00', 'America/Lima'));
        try {
            $controller = (new \ReflectionClass(RefinanciamientoCastigadosController::class))->newInstanceWithoutConstructor();
            $method = new \ReflectionMethod($controller, 'limpiarBorradoresVencidos');
            $method->invoke($controller, 1);
            $this->assertDatabaseMissing('refinanciamiento_castigado_borradores', ['idcredito' => 1]);
            $this->assertDatabaseHas('refinanciamiento_castigado_borradores', ['idcredito' => 2]);
            $this->assertDatabaseHas('refinanciamiento_castigado_borradores', ['idcredito' => 3]);
        } finally {
            Carbon::setTestNow();
        }
    }
}
