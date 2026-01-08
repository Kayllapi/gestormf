<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class PenalidadComisionController extends Controller
{
    public function __construct()
    {
        $this->tipo_credito = DB::table('tipo_credito')->get();
    }
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/penalidadcomision/tabla',[
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
      
      
      if($request->input('view') == 'editar') {
        $tipo_garantia = DB::table('tipo_garantia')->get();
        $tipo_garantia_noprendaria = DB::table('subtipo_garantia_noprendaria_ii')
            ->join('subtipo_garantia_noprendaria','subtipo_garantia_noprendaria.id','subtipo_garantia_noprendaria_ii.idsubtipo_garantia_noprendaria')
            ->join('tipo_garantia_noprendaria','tipo_garantia_noprendaria.id','subtipo_garantia_noprendaria.idtipo_garantia_noprendaria')
            ->select(
              'subtipo_garantia_noprendaria_ii.*',
              'subtipo_garantia_noprendaria.nombre as subtipo_garantia_nombre',
              'tipo_garantia_noprendaria.nombre as tipo_garantia_nombre',
            )
            ->get();
        
        return view(sistema_view().'/penalidadcomision/edit',[
          'tienda' => $tienda,
          'tipo_garantia' => $tipo_garantia,
          'tipo_garantia_noprendaria' => $tipo_garantia_noprendaria,
        ]);
      }
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        if($request->input('view') == 'editar') {
  
            $garantia_prendario = json_decode($request->input('prendario'), true);
            $garantia_noprendario = json_decode($request->input('noprendario'), true);
            foreach($garantia_prendario as $value){
              DB::table('tipo_garantia')->whereId($value['id'])->update([
                'penalidad' => $value['penalidad'],
              ]);
            }
            foreach($garantia_noprendario as $value){
              DB::table('subtipo_garantia_noprendaria_ii')->whereId($value['id'])->update([
                'penalidad' => $value['penalidad'],
              ]);
            }
          
            configuracion_update($idtienda,'dias_maximo_penalidad',$request->dias_maximo_penalidad);
            configuracion_update($idtienda,'penalidad_couta_simple',$request->penalidad_couta_simple);
            configuracion_update($idtienda,'penalidad_couta_compuesto',$request->penalidad_couta_compuesto);
            configuracion_update($idtienda,'dias_tolerancia',$request->dias_tolerancia);
            configuracion_update($idtienda,'penalidad_couta_simple_noprendaria',$request->penalidad_couta_simple_noprendaria);
            configuracion_update($idtienda,'penalidad_couta_compuesto_noprendaria',$request->penalidad_couta_compuesto_noprendaria);
            configuracion_update($idtienda,'dias_tolerancia_garantia',$request->dias_tolerancia_garantia);
            configuracion_update($idtienda,'tasa_moratoria',$request->tasa_moratoria);
            configuracion_update($idtienda,'tipo_cambio_dolar',$request->tipo_cambio_dolar);
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
