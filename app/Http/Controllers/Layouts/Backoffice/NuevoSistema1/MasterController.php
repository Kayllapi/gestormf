<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class MasterController extends Controller
{
    public function index(Request $request)
    {
        $categorias = DB::table('categoria')->orderBy('categoria.nombre','asc')->get();
        $tienda = DB::table('tienda')
            ->whereId(Auth::user()->idtienda)
            ->first();
        $moneda_soles = DB::table('s_moneda')->whereId(1)->first();
        $moneda_dolares = DB::table('s_moneda')->whereId(2)->first();
        $dominio_perzonalizado = obtener_dominio_perzonalizado();
        return view('layouts/backoffice/tienda/nuevosistema/master/index',[
            'categorias' => $categorias,
            'tienda' => $tienda,
            'moneda_soles' => $moneda_soles,
            'moneda_dolares' => $moneda_dolares,
            'dominio_perzonalizado' => $dominio_perzonalizado,
        ]);
    }

    public function create(Request $request)
    {
        //
    }
    
    public function store(Request $request)
    {
        //
    }

    public function show(Request $request, $id)
    {
        //
    }
  
    public function edit(Request $request, $id)
    {
        //
    }
  
    public function update(Request $request, $idsuario)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
