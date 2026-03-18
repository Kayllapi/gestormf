<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use App\Exports\ReportecarteracreditoExport;
use Maatwebsite\Excel\Facades\Excel;

class GarantiaremateagenciaasesorController extends Controller
{
    public function __construct()
    {
        //
    }
    public function index(Request $request,$idtienda)
    {
        // ACTUALIZAR e eliminar durante el dia
        $credito_garantias = DB::table('credito_garantia')
              ->join('credito','credito.id','credito_garantia.idcredito')
              ->join('users as cliente','cliente.id','credito_garantia.idcliente')
              ->where('credito.idliquidaciongarantia',1)
              ->select(
                'credito.*',
              )
              ->get();
      
        $fecha = Carbon::now();
        foreach($credito_garantias as $value){
            $ultimafecha = date_format(date_create($value->fechaliquidaciongarantia),"Y-m-d").' 23:59:59';
            if($fecha>=$ultimafecha){
                DB::table('credito')->whereId($value->id)->update([
                    'fechaliquidaciongarantia' => null,
                    'idliquidaciongarantia' => 0,
                    'idliquidaciongarantiaresponsable' => 0,
                ]);
            }
        }
        // FIN ACTUALIZAR 
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            
            $agencias = DB::table('tienda')->get();
          
            return view(sistema_view().'/garantiaremateagenciaasesor/tabla',[
              'tienda' => $tienda,
              'agencias' => $agencias,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
    }
  
    public function store(Request $request, $idtienda)
    {
    }

    public function show(Request $request, $idtienda, $id)
    {
    }

    public function edit(Request $request, $idtienda, $id)
    {

    }

    public function update(Request $request, $idtienda, $id)
    {
    }

    public function destroy(Request $request, $idtienda, $id)
    {
    }
}
