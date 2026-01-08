<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class CotizarGarantiaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $tipo_garantia = DB::table('tipo_garantia')->get();
        $estado_garantia = DB::table('estado_garantia')->get();
        $estado_garantia_ref = DB::table('estado_garantia_ref')->get();
        $tipo_joyas = DB::table('tipo_joyas')->where('tipo_joyas.estado','ACTIVO')->get();
        $metodo_valorizacion = DB::table('metodo_valorizacion')->get();
        $descuento_joya = DB::table('descuento_joya')->get();
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/cotizargarantia/tabla',[
              'idcliente' => $request->idcliente,
              'tienda' => $tienda,
              'tipo_garantia' => $tipo_garantia,
              'metodo_valorizacion' => $metodo_valorizacion,
              'tipo_joyas' => $tipo_joyas,
              'estado_garantia' => $estado_garantia,
              'estado_garantia_ref' => $estado_garantia_ref,
              'descuento_joya' => $descuento_joya,
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

        if($id=='show_table'){
          
            $garantias = DB::table('garantias')
                            ->join('users','users.id','garantias.idcliente')
                            ->select(
                                'garantias.*',
                                'users.nombrecompleto as nombrecliente'
                            )
                            ->orderBy('garantias.id','desc')
                            ->paginate($request->length,'*',null,($request->start/$request->length)+1);
          

            $tabla = [];
            foreach($garantias as $value){
                
              $tabla[]=[
                  'id'          => $value->id,
                  'nombrecliente' => $value->nombrecliente,
                  'descripcion' => $value->descripcion,
                  'cobertura'   => $value->cobertura,
                  'click' => true,
//                   'opcion' => [
//                      [
//                       'nombre' => 'Editar',
//                       'onclick' => '/'.$idtienda.'/garantias/'.$value->id.'/edit?view=editar',
//                       'icono' => 'edit',
//                     ],
//                     [
//                       'nombre' => 'Eliminar',
//                       'onclick' => '/'.$idtienda.'/garantias/'.$value->id.'/edit?view=eliminar',
//                       'icono' => 'trash',
//                     ]
//                   ],
              ];
            }
            
            return response()->json([
                'start'           => $request->start,
                'draw'            => $request->draw,
                'recordsTotal'    => $request->length,
                'recordsFiltered' => $garantias->total(),
                'data'            => $tabla,
            ]);
        }
        else if($id == 'showlistagarantias'){
          $cliente = DB::table('users')->whereId($request->idcliente)->select('users.id','users.nombrecompleto','users.identificacion')->first();
          $garantias = DB::table('garantias')
//                             ->join('users','users.id','garantias.idcliente')
                            ->where('garantias.idcliente', $request->idcliente)
                            ->select(
                                'garantias.*'
//                                 'users.nombrecompleto as nombrecliente'
                            )
                            ->orderBy('garantias.id','desc')
                            ->get();
          $html = '';
          foreach($garantias as $value){
              $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data(this)'>
                            <td>{$value->descripcion}</td>
                            <td>S/ {$value->cobertura}</td>
                        </tr>";
          }
          return array(
            'cliente' => $cliente,
            'html' => $html
          );
          
        }
        else if($id == 'showtipogarantia'){
//           $valorizacion = DB::table('tipo_garantia_detalle')
//                             ->join('metodo_valorizacion','metodo_valorizacion.id','tipo_garantia_detalle.idmetodo_valorizacion')
//                             ->where('tipo_garantia_detalle.idtipo_garantia',$request->idtipogarantia)
//                             ->select(
//                               'tipo_garantia_detalle.*',
//                               'metodo_valorizacion.nombre as nombremetodo'
//                             )
//                             ->orderBy('tipo_garantia_detalle.id','desc')
//                             ->get();
          

          $valorizacion = DB::table('tipo_garantia_detalle')
                            ->join('metodo_valorizacion','metodo_valorizacion.id','tipo_garantia_detalle.idmetodo_valorizacion')
                            ->where('tipo_garantia_detalle.idtipo_garantia',$request->idtipogarantia)
                            ->where('tipo_garantia_detalle.idmetodo_valorizacion',$request->idmetodovalorizacion)
                            ->select(
                              'tipo_garantia_detalle.*',
                              'metodo_valorizacion.nombre as nombremetodo'
                            )
                            ->orderBy('tipo_garantia_detalle.id','desc')
                            ->get();
           return $valorizacion;
        }
        else if($id == 'showtarifario'){
          $tarifario = DB::table('tarifario_joyas')
                            ->where('tarifario_joyas.estado','ACTIVO')
                            ->select(
                              'tarifario_joyas.*'
                            )
                            ->orderBy('tarifario_joyas.id','desc')
                            ->get();
           return $tarifario;
        }
        else if($id=='showdescuentojoya'){
          
          
          
          $where[] = ['valorizacion_descuento.iddescuento_joya',$request->iddescuento_joya];
          

          $valorizacion = DB::table('valorizacion_descuento')
                          ->where($where)
                          ->select(
                              'valorizacion_descuento.*'
                          )
                          ->orderBy('valorizacion_descuento.id','desc')
                          ->get();
          return $valorizacion;
        }
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
