<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class ValorizacionGarantiaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $tipo_garantia = DB::table('tipo_garantia')->where('tipo_garantia.estado','ACTIVO')->get();
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/valorizaciongarantia/tabla',[
              'tienda' => $tienda,
              'tipo_garantia' => $tipo_garantia,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $metodo_valorizacion = DB::table('metodo_valorizacion')->get();
        $tipo_garantia = DB::table('tipo_garantia')->where('tipo_garantia.estado','ACTIVO')->get();
        
        if($request->view == 'registrar') {
            return view(sistema_view().'/valorizaciongarantia/create',[
                'tienda' => $tienda,
                'metodo_valorizacion' => $metodo_valorizacion,
                'tipo_garantia' => $tipo_garantia,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
            $rules = [
                'idtipogarantia' => 'required',         
                'idmetodovalorizacion' => 'required',         
                'antiguedad_compra' => 'required',         
                'valor_comercial' => 'required',         
                'cobertura' => 'required',         
            ];
          
            $messages = [
                'idtipogarantia.required' => 'El "Tipo de Garantia" es Obligatorio.',
                'idmetodovalorizacion.required' => 'El "Método de Valorización" es Obligatorio.',
                'antiguedad_compra.required' => 'La "Antiguedad" es Obligatorio.',
                'valor_comercial.required' => 'El "Valor Comercial" es Obligatorio.',
                'cobertura.required' => 'La "Cobertura" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('tipo_garantia_detalle')->insert([
               'idtipo_garantia'        => $request->input('idtipogarantia'),
               'idmetodo_valorizacion'  => $request->input('idmetodovalorizacion'),
               'antiguedad'             => $request->input('antiguedad_compra'),
               'valor_comercial'        => $request->input('valor_comercial'),
               'cobertura'              => $request->input('cobertura'),
               
            ]);

          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id=='show_table'){
            $columns = $request->input('columns');
            $tipogarantiaSearch = $columns[0]['search']['value'];

            $tipogarantia = DB::table('tipo_garantia_detalle')
                            ->join('tipo_garantia','tipo_garantia.id','tipo_garantia_detalle.idtipo_garantia')
                            ->join('metodo_valorizacion','metodo_valorizacion.id','tipo_garantia_detalle.idmetodo_valorizacion')
                            // ->where('permiso.idtienda', $idtienda)
                            ->when($tipogarantiaSearch, function ($query, $tipogarantiaSearch) {
                                  return $query->where('tipo_garantia_detalle.idtipo_garantia', $tipogarantiaSearch);
                            })
                            ->select(
                                'tipo_garantia_detalle.*',
                                'tipo_garantia.nombre as nombre_tipogarantia',
                                'metodo_valorizacion.nombre as nombre_metodovalorizacion'
                            )
                            ->orderBy('tipo_garantia_detalle.id','desc')
                            ->paginate($request->length,'*',null,($request->start/$request->length)+1);
          

            $tabla = [];
            foreach($tipogarantia as $value){
                
              $tabla[]=[
                  'id'                        => $value->id,
                  'nombre_tipogarantia'       => $value->nombre_tipogarantia,
                  'nombre_metodovalorizacion' => $value->nombre_metodovalorizacion.' ('.$value->antiguedad.')',
                  'valor_comercial'           => $value->valor_comercial,
                  'cobertura'                 => $value->cobertura,
                  'opcion' => [
                     [
                      'nombre' => 'Editar',
                      'onclick' => '/'.$idtienda.'/valorizaciongarantia/'.$value->id.'/edit?view=editar',
                      'icono' => 'edit',
                    ],
                    [
                      'nombre' => 'Eliminar',
                      'onclick' => '/'.$idtienda.'/valorizaciongarantia/'.$value->id.'/edit?view=eliminar',
                      'icono' => 'trash',
                    ]
                  ],
              ];
            }
            
            return response()->json([
                'start'           => $request->start,
                'draw'            => $request->draw,
                'recordsTotal'    => $request->length,
                'recordsFiltered' => $tipogarantia->total(),
                'data'            => $tabla,
            ]);
        }
        else if($id == 'showlistavalorizaciongarantia'){
          
            $idtipogarantia = $request->idtipogarantia != '' ? $request->idtipogarantia : 0;
            $idmetodovalorizacion = $request->idmetodovalorizacion != '' ? $request->idmetodovalorizacion : 0;
            $where[] = ['tipo_garantia_detalle.idtipo_garantia',$request->idtipogarantia];
            if($request->idmetodovalorizacion != ''){
              $where[] = ['tipo_garantia_detalle.idmetodo_valorizacion',$request->idmetodovalorizacion];
            }
            $valorizacion = DB::table('tipo_garantia_detalle')
                            ->join('tipo_garantia','tipo_garantia.id','tipo_garantia_detalle.idtipo_garantia')
                            ->join('metodo_valorizacion','metodo_valorizacion.id','tipo_garantia_detalle.idmetodo_valorizacion')
                            // ->where('permiso.idtienda', $idtienda)
                            ->where($where)
//                             ->where('tipo_garantia_detalle.idmetodo_valorizacion',$request->idmetodovalorizacion)
                            ->select(
                                'tipo_garantia_detalle.*',
                                'tipo_garantia.nombre as nombre_tipogarantia',
                                'metodo_valorizacion.nombre as nombre_metodovalorizacion'
                            )
                            ->orderBy('tipo_garantia_detalle.id','asc')
                            ->get();
          $html = '';
          foreach($valorizacion as $value){
              $html .= "<tr data-valor-columna='{$value->id}' onclick='show_editar_valorizacion(this)'>
                            <td>{$value->nombre_metodovalorizacion}</td>
                            <td>{$value->antiguedad}</td>
                            <td>{$value->valor_comercial}</td>
                            <td>{$value->cobertura}</td>
                        </tr>";
          }
          return array(
            'html' => $html
          );
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        
      
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      $metodo_valorizacion = DB::table('metodo_valorizacion')->get();
      $tipo_garantia = DB::table('tipo_garantia')->get();
  
      
      $tipogarantiadetalle = DB::table('tipo_garantia_detalle')
                      ->join('tipo_garantia','tipo_garantia.id','tipo_garantia_detalle.idtipo_garantia')
                      ->where('tipo_garantia_detalle.id',$id)
                      ->select(
                          'tipo_garantia_detalle.*',
                          'tipo_garantia.nombre as nombre_tipogarantia',
                      )
                      ->orderBy('tipo_garantia_detalle.id','desc')
                      ->first();
      
      if($request->input('view') == 'editar') {

        return view(sistema_view().'/valorizaciongarantia/edit',[
          'tienda' => $tienda,
          'metodo_valorizacion' => $metodo_valorizacion,
          'tipo_garantia' => $tipo_garantia,
          'tipogarantiadetalle' => $tipogarantiadetalle,
          'idtienda' => $idtienda,
        ]);
      }
      else if($request->input('view') == 'eliminar'){
        return view(sistema_view().'/valorizaciongarantia/delete',[
          'tienda' => $tienda,
          'metodo_valorizacion' => $metodo_valorizacion,
          'tipo_garantia' => $tipo_garantia,
          'tipogarantiadetalle' => $tipogarantiadetalle,
          'idtienda' => $idtienda,
        ]);
      }
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        if($request->input('view') == 'editar') {

            $rules = [
                'idtipogarantia' => 'required',         
                'idmetodovalorizacion' => 'required',         
                'antiguedad_compra' => 'required',         
                'valor_comercial' => 'required',         
                'cobertura' => 'required',         
            ];
          
            $messages = [
                'idtipogarantia.required' => 'El "Tipo de Garantia" es Obligatorio.',
                'idmetodovalorizacion.required' => 'El "Método de Valorización" es Obligatorio.',
                'antiguedad_compra.required' => 'La "Antiguedad" es Obligatorio.',
                'valor_comercial.required' => 'El "Valor Comercial" es Obligatorio.',
                'cobertura.required' => 'La "Cobertura" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('tipo_garantia_detalle')->whereId($id)->update([
               'idtipo_garantia'        => $request->input('idtipogarantia'),
               'idmetodo_valorizacion'  => $request->input('idmetodovalorizacion'),
               'antiguedad'             => $request->input('antiguedad_compra'),
               'valor_comercial'        => $request->input('valor_comercial'),
               'cobertura'              => $request->input('cobertura'),
               
            ]);
          
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
    
    }


    public function destroy(Request $request, $idtienda, $id)
    {
//         $request->user()->authorizeRoles($request->path(),$idtienda);
      if( $request->input('view') == 'eliminar' ){
        DB::table('tipo_garantia_detalle')->whereId($id)->delete();
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha elimino correctamente.'
        ]);
      }
      
    
    }
}
