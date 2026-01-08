<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class CreditoPrendatarioController extends Controller
{
    public function __construct()
    {
        $this->tipo_credito = DB::table('tipo_credito')->get();
    }
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/creditoprendatario/tabla',[
              'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->view == 'registrar') {
            return view(sistema_view().'/creditoprendatario/create',[
                'tienda' => $tienda,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
      
        if($request->input('view') == 'registrar') {
            
            $rules = [
                'nombre' => 'required',    
                'modalidad' => 'required',   
                'garantiaprendatario' => 'required',   
                'conevaluacion' => 'required',                
            ];
          
            $messages = [
                'nombre.required' => 'El campo "Nombre de Producto" es Obligatorio.',
                'modalidad.required' => 'El campo "Modalidad de Calculo" es Obligatorio.',
                'garantiaprendatario.required' => 'El campo "Garantia Prendaria" es Obligatorio.',
                'conevaluacion.required' => 'El campo "Con Evaluación" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
              
            $idcredito = DB::table('credito_prendatario')->insertGetId([
                'nombre'        => $request->input('nombre'),
                'idforma_credito' => 1, // 1 = PRENDARIO | 2 = NO PRENDARIO
                'idtipo_credito'          => 0,
                'modalidad'     => $request->input('modalidad'),
                'garantiaprendatario' => $request->input('garantiaprendatario'),
                'conevaluacion' => $request->input('conevaluacion'),
            ]);
            $codCredito = str_pad($idcredito, 4, '0', STR_PAD_LEFT);
            DB::table('credito_prendatario')->where('id', $idcredito)->update(['codigo' => 'CP'.$codCredito]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'showcreditos'){
          $creditos = DB::table('credito_prendatario')
                            ->where('credito_prendatario.idforma_credito',1)
                            ->select(
                                'credito_prendatario.*'
                            )
                            ->orderBy('credito_prendatario.id','asc')
                            ->get();
          $html = '';
          foreach($creditos as $key => $value){
              $estado = $value->estado == 'ACTIVO' ? 'checked' : '';
              $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data(this)'>
                            <td>".($key+1)."</td>
                            <td>{$value->codigo}</td>
                            <td>{$value->nombre}</td>
                            <td>{$value->modalidad}</td>
                            <td>{$value->garantiaprendatario}</td>
                            <td><input type='checkbox' {$estado} disabled> </td>
                            <td>{$value->conevaluacion}</td>
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
      
      $credito = DB::table('credito_prendatario')
                            ->where('credito_prendatario.id',$id)
                            ->select(
                                'credito_prendatario.*'
                            )
                            ->orderBy('credito_prendatario.id','desc')
                            ->first();
      
      if($request->input('view') == 'editar') {

        return view(sistema_view().'/creditoprendatario/edit',[
          'tienda' => $tienda,
          'credito' => $credito
        ]);
      }
      else if($request->input('view') == 'eliminar'){
        return view(sistema_view().'/creditoprendatario/delete',[
          'tienda' => $tienda,
          'credito' => $credito
          
        ]);
      }
      else if($request->input('view') == 'diasdegracia'){
        $diasdegracia = DB::table('diasdegracia')->where('diasdegracia.nombre','PRENDARIA')->first();
        return view(sistema_view().'/creditoprendatario/diasdegracia',[
          'tienda' => $tienda,       
          'diasdegracia' => $diasdegracia,       
        ]);
      }
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        if($request->input('view') == 'editar') {
  
            $rules = [
                'nombre' => 'required', 
                'modalidad' => 'required',   
                'garantiaprendatario' => 'required',   
                'conevaluacion' => 'required',                     
            ];
          
            $messages = [
                'nombre.required' => 'El "Cliente" es Obligatorio.',
                'modalidad.required' => 'El campo "Modalidad de Calculo" es Obligatorio.',
                'garantiaprendatario.required' => 'El campo "Garantia Prendaria" es Obligatorio.',
                'conevaluacion.required' => 'El campo "Con Evaluación" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
              
            DB::table('credito_prendatario')->whereId($id)->update([
                'nombre'        => $request->input('nombre'),
                'idforma_credito'          => 1,
                'idtipo_credito'          => 0,
                'modalidad'     => $request->input('modalidad'),
                'garantiaprendatario' => $request->input('garantiaprendatario'),
                'conevaluacion' => $request->input('conevaluacion'),
                'estado' => $request->input('estado')
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        else if( $request->input('view') == 'diasdegracia' ){
            $rules = [
                'dias' => 'required',                  
            ];
          
            $messages = [
                'dias.required' => 'El "Dias" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
              
            DB::table('diasdegracia')->where('diasdegracia.nombre','PRENDARIA')->update([
                'dias' => $request->input('dias')
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
        DB::table('credito_prendatario')->whereId($id)->delete();
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha elimino correctamente.'
        ]);
      }
      
    
    }
}
