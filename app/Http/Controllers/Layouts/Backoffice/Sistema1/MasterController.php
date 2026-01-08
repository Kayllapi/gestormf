<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class MasterController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $categorias = DB::table('categoria')->orderBy('categoria.nombre','asc')->get();
        $tienda = DB::table('tienda')
            ->whereId($idtienda)
            ->first();
        $moneda_soles = DB::table('s_moneda')->whereId(1)->first();
        $moneda_dolares = DB::table('s_moneda')->whereId(2)->first();
        $dominio_perzonalizado = obtener_dominio_perzonalizado();
        return view('layouts/backoffice/tienda/sistema/master/index',[
            'categorias' => $categorias,
            'tienda' => $tienda,
            'moneda_soles' => $moneda_soles,
            'moneda_dolares' => $moneda_dolares,
            'dominio_perzonalizado' => $dominio_perzonalizado,
        ]);
    }

    public function create(Request $request,$idtienda)
    {
        //
    }
    
    public function store(Request $request, $idtienda)
    {
        //
    }

    public function show(Request $request, $idtienda, $id)
    {
        //
    }
  
    public function edit(Request $request, $idtienda, $id)
    {
        //
    }
  
    public function update(Request $request, $idtienda, $idsuario)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
