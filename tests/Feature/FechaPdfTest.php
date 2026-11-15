<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FechaPdfTest extends TestCase
{
    public static function fechas(): array
    {
        return [
            'fecha de base de datos' => ['2026-11-14', '14/11/2026'],
            'año primero con barras' => ['2026/11/14', '14/11/2026'],
            'fecha con guiones' => ['14-11-2026', '14/11/2026'],
            'fecha correcta' => ['14/11/2026', '14/11/2026'],
            'día y mes ambiguos' => ['01/02/2026', '01/02/2026'],
            'rellena ceros' => ['2026-2-1', '01/02/2026'],
            'año corto de reportes antiguos' => ['14/11/26 09:15AM', '14/11/2026 09:15AM'],
            'conserva hora de 24 horas' => ['2026-11-14 18:25:32', '14/11/2026 18:25:32'],
            'conserva hora de 12 horas' => ['14-11-2026 06:25:32 PM', '14/11/2026 06:25:32 PM'],
            'fecha ISO con zona horaria' => ['2026-11-14T18:25:32-05:00', '14/11/2026 18:25:32-05:00'],
            'bisiesto' => ['2024-02-29', '29/02/2024'],
            'fecha inexistente' => ['2026-02-29', '2026-02-29'],
            'fecha nula' => [null, ''],
            'fecha vacía' => ['', ''],
            'marcador sin fecha' => ['---', '---'],
            'espacios' => [' 2026-11-14 ', '14/11/2026'],
        ];
    }

    #[DataProvider('fechas')]
    public function test_normaliza_fecha_sin_cambiar_la_hora_o_inventar_fechas($entrada, string $esperado): void
    {
        $this->assertSame($esperado, sistema_fecha_pdf($entrada));
    }

    public function test_acepta_objetos_fecha_sin_mutarlos(): void
    {
        $fecha = new \DateTimeImmutable('2026-11-14 18:25:32');
        $this->assertSame('14/11/2026 18:25:32', sistema_fecha_pdf($fecha));
        $this->assertSame('2026-11-14 18:25:32', $fecha->format('Y-m-d H:i:s'));
    }

    public function test_todas_las_plantillas_pdf_compilan_a_php_valido(): void
    {
        $errores = [];
        $cantidad = 0;
        $compiler = app('blade.compiler');
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(resource_path('views')));
        foreach ($files as $file) {
            if (!$file->isFile() || !str_ends_with($file->getFilename(), '.blade.php') || stripos($file->getFilename(), 'pdf') === false) {
                continue;
            }
            $cantidad++;
            try {
                token_get_all($compiler->compileString(file_get_contents($file->getPathname())), TOKEN_PARSE);
            } catch (\ParseError $error) {
                $errores[] = $file->getPathname().': '.$error->getMessage();
            }
        }
        $this->assertGreaterThan(100, $cantidad);
        $this->assertSame([], $errores, implode("\n", $errores));
    }

    public function test_reportes_renderizan_rango_y_detalle_sin_alterar_fechas_originales(): void
    {
        $this->actingAs(new \App\Models\User(['id' => 1, 'codigo' => 'TEST']));
        $registro = (object) [
            'fechapago' => '2026-11-13 18:25:32', 'banco' => '', 'cuenta' => '',
            'numerooperacion' => '', 'codigoprefijo' => 'PR', 'codigo' => 1,
            'monto' => '100.00', 'descripcion' => 'Prueba',
            's_sustento_comprobantenombre' => 'Recibo', 'sustento_descripcion' => 'Detalle',
            'credito_tipoformapagonombre' => 'Caja', 'codigo_responsable' => 'TEST',
            'idformapago' => 1,
        ];
        foreach (['ingresoextraordinario', 'gastoadministrativooperativo'] as $reporte) {
            $html = view('layouts.backoffice.sistema.'.$reporte.'.exportar_pdf', [
                'tienda' => (object) ['nombre' => 'Prueba', 'nombreagencia' => 'Agencia Prueba'],
                $reporte => collect([$registro]),
                'fechainicio' => '2026-11-01', 'fechafin' => '2026/11/14',
            ])->render();
            $this->assertStringContainsString('01/11/2026', $html);
            $this->assertStringContainsString('14/11/2026', $html);
            $this->assertStringContainsString('13/11/2026 18:25:32 PM', $html);
            $this->assertStringNotContainsString('2026-11-13', $html);
            $pdf = app('dompdf.wrapper')->loadHTML($html)->setPaper('A4', 'landscape')->output();
            $this->assertStringStartsWith('%PDF-', $pdf);
        }
        $this->assertSame('2026-11-13 18:25:32', $registro->fechapago);
    }
}
