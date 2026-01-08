<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReporteproductosExport implements FromView, ShouldAutoSize
{
    public function __construct($producto, $titulo)
    {
        $this->producto         = $producto;
        $this->titulo           = $titulo;
    }

    public function view(): View
    {
        return view('layouts/backoffice/tienda/sistema/reporte/reporteproductos/tablaexcel',[
          
            'producto'        => $this->producto,
            'titulo'          => $this->titulo
        ]);
    }
}