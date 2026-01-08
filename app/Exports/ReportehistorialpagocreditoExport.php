<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportehistorialpagocreditoExport implements FromView, ShouldAutoSize
{
    public function __construct($tienda, $agencia, $credito_cobranzacuotas, $fechainicio, $fechafin, $titulo)
    {
        $this->tienda = $tienda;
        $this->agencia = $agencia;
        $this->credito_cobranzacuotas = $credito_cobranzacuotas;
        $this->fechainicio = $fechainicio;
        $this->fechafin = $fechafin;
        $this->titulo = $titulo;
    }

    public function view(): View
    {
        return view('layouts/backoffice/sistema/historialpagocredito/tablaexcel',[
            'tienda' => $this->tienda,
            'agencia' => $this->agencia,
            'credito_cobranzacuotas' => $this->credito_cobranzacuotas,
            'fechainicio' => $this->fechainicio,
            'fechafin' => $this->fechafin,
            'titulo' => $this->titulo
        ]);
    }
}