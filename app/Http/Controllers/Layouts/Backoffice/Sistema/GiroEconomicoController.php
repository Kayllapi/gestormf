<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use PDF;
use Carbon\Carbon;

class GiroEconomicoController extends Controller
{
    public function __construct()
    {
        $this->tipo_giro_economico = DB::table('tipo_giro_economico')->get();
    }
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/giroeconomico/tabla',[
              'tienda' => $tienda
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        if($request->view == 'registrar') {
            return view(sistema_view().'/giroeconomico/create',[
                'tienda' => $tienda,
                'tipo_giro_economico' => $this->tipo_giro_economico,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
            $rules = [
                'nombre' => 'required',         
                'porcentaje' => 'required',        
                'idtipo_giro_economico' => 'required',        
            ];
          
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'porcentaje.required' => 'El "Porcentaje" es Obligatorio.',
                'idtipo_giro_economico.required' => 'El "Tipo de Giro Economico" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('giro_economico_evaluacion')->insert([
               'idtipo_giro_economico' => $request->input('idtipo_giro_economico'),
               'nombre'        => $request->input('nombre'),
               'porcentaje'   => $request->input('porcentaje'),
               'estado'   => $request->input('estado'),
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
            
            if($request->input('idtipo_giro_economico') != ''){
              $where[] = ['giro_economico_evaluacion.idtipo_giro_economico', $request->input('idtipo_giro_economico')];  
            }
            if($request->input('estado') != ''){
              $where[] = ['giro_economico_evaluacion.estado', $request->input('estado')];  
            }
            $giro = DB::table('giro_economico_evaluacion')
                            ->join('tipo_giro_economico','tipo_giro_economico.id','giro_economico_evaluacion.idtipo_giro_economico')
                            ->where($where)
                            ->select(
                                'giro_economico_evaluacion.*',
                                'tipo_giro_economico.nombre as nombretipogiroeconomico'
              
                            )
                            ->orderBy('giro_economico_evaluacion.id','ASC')
                            ->get();
  
            $html = '';
            foreach($giro as $key => $value){

                $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data(this)'>
                              <td>".($key+1)."</td>
                              <td>{$value->nombretipogiroeconomico}</td>
                              <td>{$value->nombre}</td>
                              <td>{$value->porcentaje}</td>
                              <td>{$value->estado}</td>
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
  
      
      $giro = DB::table('giro_economico_evaluacion')
                      ->where('giro_economico_evaluacion.id',$id)
                      ->select(
                          'giro_economico_evaluacion.*',
                      )
                      ->orderBy('giro_economico_evaluacion.id','desc')
                      ->first();
      
      if($request->input('view') == 'editar') {

        return view(sistema_view().'/giroeconomico/edit',[
          'tienda' => $tienda,
          'giro' => $giro,
          'idtienda' => $idtienda,
          'tipo_giro_economico' => $this->tipo_giro_economico,
        ]);
      }
      else if($request->input('view') == 'eliminar'){
        return view(sistema_view().'/giroeconomico/delete',[
          'tienda' => $tienda,
          'giro' => $giro,
          'idtienda' => $idtienda,
        ]);
      }
      else if( $request->input('view') == 'container' ){
        return view(sistema_view().'/giroeconomico/container',[
          'tienda' => $tienda,
          'giro' => $giro,
          'idtienda' => $idtienda,
          'tipo_giro_economico' => $this->tipo_giro_economico,
        ]);
      }
      else if( $request->input('view') == 'pdf' ){
        
        if($request->input('idtipo_giro_economico') != ''){
          $where[] = ['giro_economico_evaluacion.idtipo_giro_economico', $request->input('idtipo_giro_economico')];  
        }
        if($request->input('estado') != ''){
          $where[] = ['giro_economico_evaluacion.estado', $request->input('estado')];  
        }
        $giros = DB::table('giro_economico_evaluacion')
                        ->join('tipo_giro_economico','tipo_giro_economico.id','giro_economico_evaluacion.idtipo_giro_economico')
                        ->where($where)
                        ->select(
                            'giro_economico_evaluacion.*',
                            'tipo_giro_economico.nombre as nombretipogiroeconomico'

                        )
                        ->orderBy('giro_economico_evaluacion.id','ASC')
                        ->get();
        $pdf = PDF::loadView(sistema_view().'/giroeconomico/pdf',[
          'tienda' => $tienda,
          'giros' => $giros,
          'idtienda' => $idtienda,
          'tipo_giro_economico' => $this->tipo_giro_economico,
        ]); 
        $pdf->setPaper('A4');
        // $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('GIRO ECONOMICO.pdf');
      }
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        if($request->input('view') == 'editar') {

            $rules = [
                'nombre' => 'required',         
                'porcentaje' => 'required',        
                'idtipo_giro_economico' => 'required',        
            ];
          
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'porcentaje.required' => 'El "Porcentaje" es Obligatorio.',
                'idtipo_giro_economico.required' => 'El "Tipo de Giro Economico" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('giro_economico_evaluacion')->whereId($id)->update([
               'idtipo_giro_economico' => $request->input('idtipo_giro_economico'),
               'nombre'        => $request->input('nombre'),
               'porcentaje'   => $request->input('porcentaje'),
               'estado'        => $request->input('estado'),
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
        DB::table('giro_economico_evaluacion')->whereId($id)->delete();
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha elimino correctamente.'
        ]);
      }
      
    
    }
}
