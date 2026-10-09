<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class GarantiasNoPrendarioAdminController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $tipo_garantia = DB::table('tipo_garantia')->get();
        $estado_garantia = DB::table('estado_garantia')->get();
        $estado_garantia_ref = DB::table('estado_garantia_ref')->get();
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/garantiasnoprendarioadmin/tabla',[
              'tienda' => $tienda,
              'tipo_garantia' => $tipo_garantia,
              'estado_garantia' => $estado_garantia,
              'estado_garantia_ref' => $estado_garantia_ref,
            ]);
        }
    }
  
    public function create(Request $request,$idtienda)
    {}
  
    public function store(Request $request, $idtienda)
    {}

    public function show(Request $request, $idtienda, $id)
    {}

    public function edit(Request $request, $idtienda, $id)
    {}

    public function update(Request $request, $idtienda, $id)
    {}

    public function destroy(Request $request, $idtienda, $id)
    {}
}
