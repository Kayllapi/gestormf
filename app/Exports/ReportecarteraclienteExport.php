<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportecarteraclienteExport implements FromView, ShouldAutoSize
{
    public function __construct($tienda, $agencia, $users, $idformacredito, $asesor, $titulo)
    {
        $this->tienda           = $tienda;
        $this->agencia          = $agencia;
        $this->users            = $users;
        $this->idformacredito   = $idformacredito;
        $this->asesor           = $asesor;
        $this->titulo           = $titulo;
    }

    public function view(): View
    {
        return view('layouts/backoffice/sistema/carteradecliente/tablaexcel',[
            'tienda'          => $this->tienda,
            'agencia'         => $this->agencia,
            'users'           => $this->users,
            'idformacredito'  => $this->idformacredito,
            'asesor'          => $this->asesor,
            'titulo'          => $this->titulo
        ]);
    }
}