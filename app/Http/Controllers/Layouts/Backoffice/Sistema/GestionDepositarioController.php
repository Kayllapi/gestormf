<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class GestionDepositarioController extends Controller
{
    public function __construct()
    {
        //$this->tipo_credito = DB::table('tipo_credito')->get();
    }
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $credito_gestiondepositario1 = DB::table('credito_gestiondepositario')->where('constituciongarantia_id',1)->get();
        $credito_gestiondepositario2 = DB::table('credito_gestiondepositario')->where('constituciongarantia_id',2)->get();
        $credito_representantecomun = DB::table('credito_representantecomun')->get();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/gestiondepositario/tabla',[
              'tienda' => $tienda,
              'credito_gestiondepositario1' => $credito_gestiondepositario1,
              'credito_gestiondepositario2' => $credito_gestiondepositario2,
              'credito_representantecomun' => $credito_representantecomun,
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
        if($request->input('view') == 'editar') {
          
            $credito_gestiondepositario = DB::table('credito_gestiondepositario')->delete();
            
            foreach(json_decode($request->seleccionar_conentregaposesion) as $value){
                if($value->custodiagarantia_id==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Custodia de Garantía Obligatorio!!.'
                    ]);
                }
                if($value->estado_id==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Estado Obligatorio!!.'
                    ]);
                }
            }
          
            foreach(json_decode($request->seleccionar_conentregaposesion) as $value){
                DB::table('credito_gestiondepositario')
                    ->where('credito_gestiondepositario.constituciongarantia_id',$value->constituciongarantia_id)
                    ->insert([
                        'custodiagarantia_id' => $value->custodiagarantia_id,
                        'custodiagarantia_nombre' => $value->custodiagarantia_nombre,
                        'nombre' => $value->nombre,
                        'doeruc' => $value->doeruc,
                        'direccion' => $value->direccion,
                        'representante_doeruc' => $value->representante_doeruc,
                        'representante_nombre' => $value->representante_nombre,
                        'estado_id' => $value->estado_id,
                        'estado_nombre' => $value->estado_nombre,
                        'constituciongarantia_id' => $value->constituciongarantia_id,
                        'constituciongarantia_nombre' => $value->constituciongarantia_nombre,
                    ]);
            }
          
            foreach(json_decode($request->seleccionar_sinentregaposesion) as $value){
                if($value->custodiagarantia_id==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Custodia de Garantía Obligatorio!!.'
                    ]);
                }
                if($value->estado_id==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Estado Obligatorio!!.'
                    ]);
                }
            }
          
            foreach(json_decode($request->seleccionar_sinentregaposesion) as $value){
                DB::table('credito_gestiondepositario')
                    ->where('credito_gestiondepositario.constituciongarantia_id',$value->constituciongarantia_id)
                    ->insert([
                        'custodiagarantia_id' => $value->custodiagarantia_id,
                        'custodiagarantia_nombre' => $value->custodiagarantia_nombre,
                        'nombre' => $value->nombre,
                        'doeruc' => $value->doeruc,
                        'direccion' => $value->direccion,
                        'representante_doeruc' => $value->representante_doeruc,
                        'representante_nombre' => $value->representante_nombre,
                        'estado_id' => $value->estado_id,
                        'estado_nombre' => $value->estado_nombre,
                        'constituciongarantia_id' => $value->constituciongarantia_id,
                        'constituciongarantia_nombre' => $value->constituciongarantia_nombre,
                    ]);
            }
          
          
            foreach(json_decode($request->seleccionar_representantecomun) as $value){
                if($value->nombre==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Nombre y Apellidos	es Obligatorio!!.'
                    ]);
                }
                if($value->estado_id==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Estado Obligatorio!!.'
                    ]);
                }
            }
          
            $credito_representantecomun = DB::table('credito_representantecomun')->delete();
          
            foreach(json_decode($request->seleccionar_representantecomun) as $value){
                DB::table('credito_representantecomun')
                    ->insert([
                        'nombre' => $value->nombre,
                        'doi' => $value->doi,
                        'direccion' => $value->direccion,
                        'ubigeo_id' => $value->ubigeo_id,
                        'ubigeo_nombre' => $value->ubigeo_nombre,
                        'estado_id' => $value->estado_id,
                        'estado_nombre' => $value->estado_nombre,
                    ]);
            }
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
    }


    public function destroy(Request $request, $idtienda, $id)
    {
      
      if( $request->input('view') == 'eliminar' ){
        DB::table('credito_prendatario')->whereId($id)->delete();
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha elimino correctamente.'
        ]);
      }
      
    
    }
}
