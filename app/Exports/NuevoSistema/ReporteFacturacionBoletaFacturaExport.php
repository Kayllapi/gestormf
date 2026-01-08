<?php 

namespace App\Exports\NuevoSistema;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReporteFacturacionBoletaFacturaExport implements FromView, ShouldAutoSize
{
    private $dataFacturasBoletas;
    private string $tituloReporte = '';
    private string $fechaInicio = '';
    private string $fechaFin = '';

    public function __construct($facturacionboletafactura, $inicio, $fin, $titulo)
    {
        $this->dataFacturasBoletas = $facturacionboletafactura;
        $this->tituloReporte = $titulo;
        $this->fechaInicio = $inicio;
        $this->fechaFin = $fin;
    }

    public function view(): View
    {
        return view('layouts/backoffice/tienda/nuevosistema/reporte/reportefacturacionboletafactura/tablaexcel_sunat',[
            'comprobantes' => $this->dataFacturasBoletas,
            'titulo' => $this->tituloReporte,
            'inicio' => $this->fechaInicio,
            'fin' => $this->fechaFin
        ]);
    }
}