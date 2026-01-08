<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class ParametrosEvaluacionController extends Controller
{
    public function __construct()
    {
        $this->tipo_credito = DB::table('tipo_credito')->get();
    }
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/parametrosevaluacion/tabla',[
              'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->view == 'registrar') {
            return view(sistema_view().'/creditoordinario/create',[
                'tienda' => $tienda,
                'tipo_credito' => $this->tipo_credito
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {

    }

    public function show(Request $request, $idtienda, $id)
    {



    }

    public function edit(Request $request, $idtienda, $id)
    {
        
      
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      $agencias = DB::table('tienda')->get();
      
      
      if($request->input('view') == 'editar') {
        
        
        return view(sistema_view().'/parametrosevaluacion/edit',[
          'tienda' => $tienda,
          'agencias' => $agencias,
        ]);
      }
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        if($request->input('view') == 'editar') {
            $capital = json_decode($request->input('capital'), true);
            foreach($capital as $value){
              DB::table('tienda')->whereId($value['id'])->update([
                'capital_agencia' => $value['capital']
              ]);
            }
             
         
            configuracion_update($idtienda,'provision_gastos_familiares',$request->provision_gastos_familiares);
            configuracion_update($idtienda,'porcentaje_min_muestra',$request->porcentaje_min_muestra);
            configuracion_update($idtienda,'reporte_institucional',$request->reporte_institucional);
            configuracion_update($idtienda,'capital_asignado',$request->capital_asignado);
            configuracion_update($idtienda,'rango_menor',$request->rango_menor);
            configuracion_update($idtienda,'rango_tope',$request->rango_tope);
            configuracion_update($idtienda,'ciclo_negocio_maximo',$request->ciclo_negocio_maximo);
            configuracion_update($idtienda,'tope_vinculacion_riesgo',$request->tope_vinculacion_riesgo);
            configuracion_update($idtienda,'relacion_couta_ingreso',$request->relacion_couta_ingreso);
            configuracion_update($idtienda,'relacion_cuota_venta',$request->relacion_cuota_venta);
            configuracion_update($idtienda,'rango_diferencia',$request->rango_diferencia);
            configuracion_update($idtienda,'rango_tope_dependiente',$request->rango_tope_dependiente);
            configuracion_update($idtienda,'entidades_maxima',$request->entidades_maxima);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
    
    }


    public function destroy(Request $request, $idtienda, $id)
    {
      
   
      
    
    }
}
