<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportePrestamoInfocorpExport implements FromView, ShouldAutoSize
{
    public function __construct($prestamomoras,$listarpor)
    {
        $this->prestamomoras  = $prestamomoras;
        $this->listarpor      = $listarpor;
    }

    public function view(): View
    {
        if($this->listarpor==1){
            return view('layouts/backoffice/tienda/sistema/reporte/reporteprestamoinfocorp/tablaequifaxpdf',[
                'prestamomoras' => $this->prestamomoras,
            ]);
        }
        elseif($this->listarpor==2){
            return view('layouts/backoffice/tienda/sistema/reporte/reporteprestamoinfocorp/tablasentinelpdf',[
                'prestamomoras' => $this->prestamomoras,
            ]);
        }  
    }
}