<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportefacturacionnotacreditoSUNATExport implements FromView, ShouldAutoSize
{
    public function __construct($facturacionnotacredito, $inicio, $fin, $titulo , $idtienda)
    {
      
      
        $this->facturacionnotacredito       = $facturacionnotacredito;
        $this->titulo           = $titulo;
        $this->inicio           = $inicio;
        $this->fin              = $fin;
        $this->idtienda         = $idtienda;
    }

    public function view(): View
    {
        return view('layouts/backoffice/tienda/sistema/reportefacturacionnotacredito/tablaexcel_sunat',[
          
            'facturacionnotacredito'        => $this->facturacionnotacredito,
            'titulo'          => $this->titulo,
            'inicio'          => $this->inicio,
            'fin'             => $this->fin,
            'idtienda'             => $this->idtienda
          
        ]);
    }
}