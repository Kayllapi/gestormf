<?php

namespace Tests\Feature;

use App\Services\NumeracionCuentaCredito;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class NumeracionCuentaCreditoTest extends TestCase
{
    public function test_asigna_numeros_siguientes_sin_reutilizar_y_revierte_si_no_existe_credito(): void
    {
        config(['database.default' => 'numeracion_test', 'database.connections.numeracion_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('secuencia_cuentas_credito', function (Blueprint $table) {
            $table->integer('id')->primary(); $table->integer('ultimo');
        });
        Schema::create('credito', function (Blueprint $table) {
            $table->integer('id')->primary(); $table->integer('cuenta')->default(0); $table->string('estado')->default('PROCESO');
        });
        DB::table('secuencia_cuentas_credito')->insert(['id' => 1, 'ultimo' => 0]);
        DB::table('credito')->insert([['id' => 1, 'cuenta' => 50], ['id' => 2, 'cuenta' => 0], ['id' => 3, 'cuenta' => 0]]);
        $this->assertSame(51, NumeracionCuentaCredito::asignar(2, ['estado' => 'DESEMBOLSADO']));
        $this->assertSame(52, NumeracionCuentaCredito::asignar(3));
        try {
            NumeracionCuentaCredito::asignar(999);
            $this->fail('Debe fallar si el credito no existe.');
        } catch (\RuntimeException $e) {
            $this->assertSame(52, DB::table('secuencia_cuentas_credito')->value('ultimo'));
        }
        $this->assertSame('DESEMBOLSADO', DB::table('credito')->where('id', 2)->value('estado'));
    }
}
