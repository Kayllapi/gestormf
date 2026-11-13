<?php

namespace Tests\Feature;

use App\Exports\ReportecarteracreditoExport;
use App\Exports\ReportecreditocastigadoExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel as FormatoExcel;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CarteraCreditoExcelTest extends TestCase
{
    public static function exportadores(): array
    {
        return [
            'cartera' => [ReportecarteracreditoExport::class],
            'castigados' => [ReportecreditocastigadoExport::class],
        ];
    }

    #[DataProvider('exportadores')]
    public function test_excel_incluye_aval_deuda_y_totales_en_las_columnas_correctas(string $exportador): void
    {
        // Solo lectura: usar un cronograma real para verificar el importe mostrado en pantalla.
        $credito = DB::table('credito')
            ->join('credito_prendatario', 'credito_prendatario.id', 'credito.idcredito_prendatario')
            ->where('credito.estado', 'DESEMBOLSADO')->where('credito.idestadocredito', 1)
            ->select('credito.*', 'credito_prendatario.modalidad as modalidadproductocredito')
            ->orderBy('credito.id')->first();
        $this->assertNotNull($credito, 'Se necesita un credito desembolsado para verificar el cronograma.');
        $tienda = DB::table('tienda')->whereId($credito->idtienda)->first();
        $credito->identificacioncliente = '12345678';
        $credito->nombrecliente = 'Cliente de prueba';
        $credito->identificacionaval = '00123456';
        $credito->nombreaval = 'Aval <Prueba>';
        $credito->nombreproductocredito = 'Producto';
        $credito->nombremodalidadcredito = 'Modalidad';
        $credito->frecuencianombre = 'Mensual';
        $credito->telefonocliente = '999999999';
        $credito->direccioncliente = 'Direccion';
        $credito->ubigeonombre = 'Distrito';
        $sinAval = clone $credito;
        $sinAval->identificacionaval = null;
        $sinAval->nombreaval = null;

        $cronograma = select_cronograma($tienda->id, $credito->id, $credito->idforma_credito,
            $credito->modalidadproductocredito, $credito->cuotas, 0, 0, 0, 0, 0, 0, 0, 0, 1, 'detalle_cobranza');
        $export = new $exportador($tienda, $tienda, collect([$credito, $sinAval]),
            date('Y-m-d'), 0, null, 'REPORTE DE CARTERA DE CREDITO');
        $archivo = tmpfile();
        try {
            fwrite($archivo, Excel::raw($export, FormatoExcel::XLS));
            $libro = IOFactory::load(stream_get_meta_data($archivo)['uri']);
            $hoja = $libro->getActiveSheet();
            $cabecera = null;
            $total = null;
            for ($fila = 1; $fila <= $hoja->getHighestRow(); $fila++) {
                if ($hoja->getCell('C'.$fila)->getValue() === 'CUENTA') {
                    $cabecera = $fila;
                }
                if (trim((string) $hoja->getCell('B'.$fila)->getValue()) === 'TOTAL S/.') {
                    $total = $fila;
                }
            }
            $this->assertNotNull($cabecera);
            $this->assertNotNull($total);
            $this->assertSame('RUC/DNI/CE (Aval)', $hoja->getCell('F'.$cabecera)->getValue());
            $this->assertSame('Ape. Nom. Aval', $hoja->getCell('G'.$cabecera)->getValue());
            $this->assertSame('Saldo Deuda T. (S/.)', $hoja->getCell('K'.$cabecera)->getValue());
            $this->assertSame('00123456', (string) $hoja->getCell('F'.($cabecera + 1))->getValue());
            $this->assertSame('Aval <Prueba>', $hoja->getCell('G'.($cabecera + 1))->getValue());
            $this->assertSame('', (string) $hoja->getCell('F'.($cabecera + 2))->getValue());
            $this->assertSame('', (string) $hoja->getCell('G'.($cabecera + 2))->getValue());
            $this->assertEqualsWithDelta((float) $cronograma['cuota_pendiente'], (float) $hoja->getCell('K'.($cabecera + 1))->getValue(), 0.005);
            $this->assertEqualsWithDelta(2 * $credito->monto_solicitado, (float) $hoja->getCell('I'.$total)->getValue(), 0.005);
            $this->assertEqualsWithDelta(2 * $credito->saldo_pendientepago, (float) $hoja->getCell('J'.$total)->getValue(), 0.005);
            $this->assertEqualsWithDelta(2 * (float) $cronograma['cuota_pendiente'], (float) $hoja->getCell('K'.$total)->getValue(), 0.005);
            $this->assertSame('T', $hoja->getHighestColumn());
            $libro->disconnectWorksheets();
        } finally {
            fclose($archivo);
        }
    }

    #[DataProvider('exportadores')]
    public function test_reporte_vacio_conserva_ancho_y_totales(string $exportador): void
    {
        $html = (new $exportador((object) ['id' => 0], null, collect(),
            '2026-11-12', 0, null, 'CARTERA'))->view()->render();
        $this->assertStringContainsString('colspan="19"', $html);
        $this->assertStringContainsString('Saldo Deuda T. (S/.)', $html);
        $this->assertStringContainsString('No hay ningún dato!!', $html);
        $this->assertStringContainsString('colspan="7"', $html);
    }
}
