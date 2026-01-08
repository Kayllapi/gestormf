<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class PruebaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $categorias = DB::table('categoria')->orderBy('categoria.nombre','asc')->get();
        return view('layouts/backoffice/tienda/sistema/prueba/index',[
            'categorias' => $categorias,
            'idtienda' => $idtienda,
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
