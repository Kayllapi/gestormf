<?php 

namespace App\Exports\NuevoSistema;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReporteRespuestaSunat implements FromView, ShouldAutoSize
{
    private $dataComprobantes;
    private $tituloReporte;
    private $fechaInicio;
    private $fechaFin;

    public function __construct($dataComprobantes, $fechaInicio, $fechaFin, $tituloReporte)
    {
        $this->dataComprobantes = $dataComprobantes;
        $this->tituloReporte = $tituloReporte;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function view(): View
    {
        return view('layouts/backoffice/tienda/nuevosistema/reportesunat/tablaexcel_sunat',[
          
            'comprobantes' => $this->dataComprobantes,
            'tituloReporte' => $this->tituloReporte,
            'fechaInicio' => $this->fechaInicio,
            'fechaFin' => $this->fechaFin
          
        ]);
    }
}