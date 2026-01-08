<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class PropuestaCreditoasesorController extends Controller
{
    public function __construct()
    {
        //
    }
    public function index(Request $request,$idtienda)
    {
      
        // ACTUALIZAR e eliminar durante el dia
        $creditos = DB::table('credito')
            ->whereIn('credito.estado',['PROCESO','APROBADO'])
            ->orderBy('credito.id','asc')
            ->get();
        $fecha = Carbon::now();
        foreach($creditos as $value){
            $ultimafecha = date_format(date_create($value->fecha_proceso),"Y-m-d").' 23:59:59';
            if($fecha>=$ultimafecha){
                DB::table('credito')->whereId($value->id)->update([
                  //'idadministrador' => Auth::user()->id,
                  'estado' => 'PENDIENTE',
                ]);
                DB::table('credito')->whereId($value->id)->update([
                  'aprobacion_tipo_validacion' => '',
                  'aprobacion_nivel_validacion' => 0,
                ]);
                DB::table('credito_aprobacion')->where('idcredito',$value->id)->delete();
                DB::table('credito_formapago')->where('idcredito',$value->id)->delete();
            }
        }
        // FIN ACTUALIZAR 
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/propuestacreditoasesor/tabla',[
              'tienda' => $tienda,
            ]);
        }
            
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

    public function update(Request $request, $idtienda, $id)
    {
        //
    }


    public function destroy(Request $request, $idtienda, $id)
    {
        //
    }
}
