<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class ProductoAhorroController extends Controller
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
            return view(sistema_view().'/productoahorro/tabla',[
              'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->view == 'registrar') {
            return view(sistema_view().'/productoahorro/create',[
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
            ];
          
            $messages = [
                'idtipo_ahorro.required' => 'El Campo es Obligatorio.',
                'producto.required' => 'El Campo es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
              
            $idtarifario = DB::table('producto_ahorro')->insertGetId([
              'idtipo_ahorro' => $request->input('idtipo_ahorro'),
              'producto'      => $request->input('producto'),
            ]);
          
            $codCredito = str_pad($idtarifario, 4, '0', STR_PAD_LEFT);
            DB::table('producto_ahorro')->where('id', $idtarifario)->update(['codigo' => 'PH'.$codCredito]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'showtable'){
          $creditos = DB::table('producto_ahorro')
                            ->join('tipo_ahorro','tipo_ahorro.id','producto_ahorro.idtipo_ahorro')
                            ->select(
                                'producto_ahorro.*',
                                'tipo_ahorro.nombre as nombretipoahorro'          
                            )
                            ->orderBy('producto_ahorro.id','desc')
                            ->get();
          
          $html = '';
          foreach($creditos as $key => $value){
              $estado = $value->estado == 'ACTIVO' ? 'checked' : '';
              $html .= "<tr data-valor-columna='{$value->id}' onclick='load_form_edit(this)'>
                            <td>".($key+1)."</td>
                            <td>{$value->codigo}</td>
                            <td>{$value->producto}</td>
                            <td>{$value->nombretipoahorro}</td>
                            <td><input type='checkbox' {$estado} disabled> </td>
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
      
      $tarifario = DB::table('producto_ahorro')
                            ->join('tipo_ahorro','tipo_ahorro.id','producto_ahorro.idtipo_ahorro')
                            ->where('producto_ahorro.id',$id)
                            ->select(
                                'producto_ahorro.*',
                                'tipo_ahorro.nombre as nombretipoahorro'          
                            )
                            ->first();
      
      if($request->input('view') == 'editar') {

        return view(sistema_view().'/productoahorro/edit',[
          'tienda' => $tienda,
          'tarifario' => $tarifario,
          'tipo_ahorro' => $this->tipo_ahorro
        ]);
      }
      else if($request->input('view') == 'eliminar'){
        return view(sistema_view().'/productoahorro/delete',[
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
            ];
          
            $messages = [
                'idtipo_ahorro.required' => 'El Campo es Obligatorio.',
                'producto.required' => 'El Campo es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
              
            DB::table('producto_ahorro')->whereId($id)->update([
              'idtipo_ahorro' => $request->input('idtipo_ahorro'),
              'producto'      => $request->input('producto'),
              'estado'         => $request->input('estado'),
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
        DB::table('producto_ahorro')->whereId($id)->delete();
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha elimino correctamente.'
        ]);
      }
      
    
    }
}
