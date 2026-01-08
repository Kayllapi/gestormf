<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class TarifarioTasaPasivaController extends Controller
{
    public function __construct()
    {
        $this->tipo_credito = DB::table('tipo_credito')->get();
        $this->tipo_ahorro = DB::table('tipo_ahorro')->get();
    }
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/tarifariotasapasiva/tabla',[
              'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->view == 'registrar') {
            return view(sistema_view().'/tarifariotasapasiva/create',[
                'tienda' => $tienda,
                'tipo_ahorro' => $this->tipo_ahorro
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
      
        if($request->input('view') == 'registrar') {
            
            $rules = [
                'idtipo_ahorro' => 'required',               
                'producto' => 'required',               
                'monto' => 'required',               
                'plazo' => 'required',               
                'tea' => 'required',               
            ];
          
            $messages = [
                'idtipo_ahorro.required' => 'El Campo es Obligatorio.',
                'producto.required' => 'El Campo es Obligatorio.',
                'monto.required' => 'El Campo es Obligatorio.',
                'plazo.required' => 'El Campo es Obligatorio.',
                'tea.required' => 'El Campo es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
              
            $idtarifario = DB::table('tarifario_tpasivas')->insertGetId([
              'idtipo_ahorro' => $request->input('idtipo_ahorro'),
              'producto'      => $request->input('producto'),
              'monto'         => $request->input('monto'),
              'plazo'         => $request->input('plazo'),
              'tea'           => $request->input('tea'),
            ]);
            $codCredito = str_pad($idtarifario, 4, '0', STR_PAD_LEFT);
            DB::table('tarifario_tpasivas')->where('id', $idtarifario)->update(['codigo' => 'TP'.$codCredito]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'showtable'){
          $where = [];
          if($request->input('idtipo_ahorro') != ''){
            $where[] = ['tarifario_tpasivas.idtipo_ahorro', $request->input('idtipo_ahorro')];  
          }
          $creditos = DB::table('tarifario_tpasivas')
                            ->join('tipo_ahorro','tipo_ahorro.id','tarifario_tpasivas.idtipo_ahorro')
                            ->where($where)
                            ->select(
                                'tarifario_tpasivas.*',
                                'tipo_ahorro.nombre as nombretipoahorro'          
                            )
                            ->orderBy('tarifario_tpasivas.id','asc')
                            ->get();
          
          $html = '';
          foreach($creditos as $key => $value){
              $plazo = $value->idtipo_ahorro == 2 ? $value->plazo : ' - ';
              $html .= "<tr data-valor-columna='{$value->id}' onclick='load_form_edit(this)'>
                            <td>".($key+1)."</td>
                            <td>{$value->codigo}</td>
                            <td>{$value->monto}</td>
                            <td>{$value->tea}</td>
                            <td>{$value->nombretipoahorro}</td>
                            <td>{$value->producto}</td>
                            <td>{$plazo}</td>
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
      
      $tarifario = DB::table('tarifario_tpasivas')
                            ->join('tipo_ahorro','tipo_ahorro.id','tarifario_tpasivas.idtipo_ahorro')
                            ->where('tarifario_tpasivas.id',$id)
                            ->select(
                                'tarifario_tpasivas.*',
                                'tipo_ahorro.nombre as nombretipoahorro'          
                            )
                            ->first();
      
      if($request->input('view') == 'editar') {

        return view(sistema_view().'/tarifariotasapasiva/edit',[
          'tienda' => $tienda,
          'tarifario' => $tarifario,
          'tipo_ahorro' => $this->tipo_ahorro
        ]);
      }
      else if($request->input('view') == 'eliminar'){
        return view(sistema_view().'/tarifariotasapasiva/delete',[
          'tienda' => $tienda,
          'tarifario' => $tarifario
          
        ]);
      }
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        if($request->input('view') == 'editar') {
  
            $rules = [
                'idtipo_ahorro' => 'required',               
                'producto' => 'required',               
                'monto' => 'required',               
                'plazo' => 'required',               
                'tea' => 'required',               
            ];
          
            $messages = [
                'idtipo_ahorro.required' => 'El Campo es Obligatorio.',
                'producto.required' => 'El Campo es Obligatorio.',
                'monto.required' => 'El Campo es Obligatorio.',
                'plazo.required' => 'El Campo es Obligatorio.',
                'tea.required' => 'El Campo es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
              
            DB::table('tarifario_tpasivas')->whereId($id)->update([
              'idtipo_ahorro' => $request->input('idtipo_ahorro'),
              'producto'      => $request->input('producto'),
              'monto'         => $request->input('monto'),
              'plazo'         => $request->input('plazo'),
              'tea'           => $request->input('tea'),
            ]);
     
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
    
    }


    public function destroy(Request $request, $idtienda, $id)
    {
      
      if( $request->input('view') == 'eliminar' ){
        DB::table('tarifario_tpasivas')->whereId($id)->delete();
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha elimino correctamente.'
        ]);
      }
      
    
    }
}
