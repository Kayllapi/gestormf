<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportecarteracreditoExport implements FromView, ShouldAutoSize
{
    public function __construct($tienda, $agencia, $creditos, $fecha_inicio, $idformacredito, $asesor, $titulo)
    {
        $this->tienda           = $tienda;
        $this->agencia          = $agencia;
        $this->creditos         = $creditos;
        $this->fecha_inicio     = $fecha_inicio;
        $this->idformacredito   = $idformacredito;
        $this->asesor           = $asesor;
        $this->titulo           = $titulo;
    }

    public function view(): View
    {
        return view('layouts/backoffice/sistema/carteracredito/tablaexcel',[
            'tienda'          => $this->tienda,
            'agencia'         => $this->agencia,
            'creditos'        => $this->creditos,
            'fecha_inicio'    => $this->fecha_inicio,
            'idformacredito'  => $this->idformacredito,
            'asesor'          => $this->asesor,
            'titulo'          => $this->titulo
        ]);
    }
}