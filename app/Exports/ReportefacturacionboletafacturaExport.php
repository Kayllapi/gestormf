<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportefacturacionboletafacturaExport implements FromView, ShouldAutoSize
{
    public function __construct($facturacionboletafactura, $inicio, $fin, $titulo)
    {
        $this->facturacionboletafactura       = $facturacionboletafactura;
        $this->titulo           = $titulo;
        $this->inicio           = $inicio;
        $this->fin              = $fin;
    }

    public function view(): View
    {
        return view('layouts/backoffice/tienda/sistema/reportefacturacionboletafactura/tablaexcel',[
          
            'facturacionboletafactura'      => $this->facturacionboletafactura,
            'titulo'          => $this->titulo,
            'inicio'          => $this->inicio,
            'fin'             => $this->fin
          
        ]);
    }
}