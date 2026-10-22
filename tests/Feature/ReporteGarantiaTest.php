<?php

namespace Tests\Feature;

use App\Http\Controllers\Layouts\Backoffice\Sistema\ReporteGarantiaController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReporteGarantiaTest extends TestCase
{
    public function test_omite_calculos_sin_deuda_y_conserva_las_filas_del_reporte(): void
    {
        $this->actingAs(User::findOrFail(6277));
        $pdf = new class {
            public array $datos = [];

            public function loadView($vista, $datos) { $this->datos = $datos; return $this; }
            public function setPaper(...$args) { return $this; }
            public function stream(...$args) { return ''; }
        };
        app()->bind('dompdf.wrapper', fn () => $pdf);
        $omitidos = 0;
        $pendientes = 0;

        foreach (DB::table('tienda')->pluck('id') as $idagencia) {
            foreach (['TODO', '1', '2'] as $modalidad) {
                (new ReporteGarantiaController)->edit(Request::create('/', 'GET', [
                    'view' => 'pdf_reporte',
                    'idagencia' => $idagencia,
                    'idmodalidad' => $modalidad,
                    'idasesor' => 0,
                ]), $idagencia, 0);

                $datos = $pdf->datos;
                $omitidos += $datos['credito_garantias']->whereNull('tiene_cuotas_pendientes')->count();
                $pendientes += $datos['credito_garantias']->whereNotNull('tiene_cuotas_pendientes')->count();
                DB::enableQueryLog();
                DB::flushQueryLog();
                $optimizado = view('layouts.backoffice.sistema.reportegarantia.pdf_reporte', $datos)->render();
                $consultasOptimizadas = count(DB::getQueryLog());
                DB::flushQueryLog();

                // Referencia: ejecutar el calculo original para TODOS los creditos,
                // incluidos los cancelados, y comparar todas las filas y saldos.
                $datos['credito_garantias'] = $datos['credito_garantias']->map(function ($fila) {
                    $copia = clone $fila;
                    $copia->tiene_cuotas_pendientes = 1;
                    return $copia;
                });
                $original = view('layouts.backoffice.sistema.reportegarantia.pdf_reporte', $datos)->render();
                $consultasOriginales = count(DB::getQueryLog());
                DB::disableQueryLog();
                preg_match('/<tbody>(.*?)<\/tbody>/s', $optimizado, $filasOptimizadas);
                preg_match('/<tbody>(.*?)<\/tbody>/s', $original, $filasOriginales);
                $this->assertSame($filasOriginales[1], $filasOptimizadas[1], "Agencia {$idagencia}, modalidad {$modalidad}");
                $this->assertLessThanOrEqual($consultasOriginales, $consultasOptimizadas);
            }
        }

        $this->assertGreaterThan(0, $omitidos, 'Se necesitan creditos sin cuotas pendientes para verificar la optimizacion.');
        $this->assertGreaterThan(0, $pendientes, 'Se necesitan creditos con cuotas pendientes para verificar los saldos.');
    }
}
