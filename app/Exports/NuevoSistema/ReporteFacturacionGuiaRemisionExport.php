<?php 

namespace App\Exports\NuevoSistema;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReporteFacturacionGuiaRemisionExport implements FromView, ShouldAutoSize
{
    private $dataGuiaRemision;
    private $tituloReporte;
    private $fechaInicio;
    private $fechaFin;

    public function __construct($dataGuiaRemision, $fechaInicio, $fechaFin, $tituloReporte)
    {
        $this->dataGuiaRemision = $dataGuiaRemision;
        $this->tituloReporte = $tituloReporte;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function view(): View
    {
        return view('layouts/backoffice/tienda/nuevosistema/reporte/reportefacturacionguiaremision/tablaexcel_sunat',[
          
            'dataGuiaRemision' => $this->dataGuiaRemision,
            'tituloReporte' => $this->tituloReporte,
            'fechaInicio' => $this->fechaInicio,
            'fechaFin' => $this->fechaFin
          
        ]);
    }
}