<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class TarifarioController extends Controller
{
    public function __construct()
    {
        $this->tipo_credito = DB::table('tipo_credito')->get();
        $this->forma_pago_credito = DB::table('forma_pago_credito')->get();
        $this->forma_credito = DB::table('forma_credito')->get();
    }
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/tarifario/tabla',[
              'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->view == 'registrar') {
            return view(sistema_view().'/tarifario/create',[
                'tienda' => $tienda,
                'forma_pago_credito' => $this->forma_pago_credito,
                'forma_credito' => $this->forma_credito
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
      
        if($request->input('view') == 'registrar') {
            
            $rules = [                
                'idforma_credito' => 'required',                 
                'idcredito_prendatario' => 'required',                 
                'idforma_pago_credito' => 'required',                 
                'monto' => 'required',                 
                'cuotas' => 'required',                 
                'tem' => 'required',                 
                'cargos_otros' => 'required',                 
            ];
          
            $messages = [
                'idforma_credito.required' => 'El Campo es Obligatorio.',
                'idcredito_prendatario.required' => 'El Campo es Obligatorio.',
                'idforma_pago_credito.required' => 'El Campo es Obligatorio.',
                'monto.required' => 'El Campo es Obligatorio.',
                'cuotas.required' => 'El Campo es Obligatorio.',
                'tem.required' => 'El Campo es Obligatorio.',
                'cargos_otros.required' => 'El Campo es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
              
            $idtarifario = DB::table('tarifario')->insertGetId([
              'idforma_credito'       => $request->input('idforma_credito'),
              'idcredito_prendatario' => $request->input('idcredito_prendatario'),
              'idforma_pago_credito'  => $request->input('idforma_pago_credito'),
              'monto'                 => $request->input('monto'),
              'cuotas'                => $request->input('cuotas'),
              'tem'                   => $request->input('tem'),
              'cargos_otros'          => $request->input('cargos_otros')
            ]);
            $codCredito = str_pad($idtarifario, 4, '0', STR_PAD_LEFT);
            DB::table('tarifario')->where('id', $idtarifario)->update(['codigo' => 'T'.$codCredito]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'showtarifario'){
          $where = [];
          if($request->input('tipo') != ''){
            $where[] = ['tarifario.idforma_credito', $request->input('tipo')];  
          }
          if($request->input('idcredito_prendatario') != ''){
            $where[] = ['tarifario.idcredito_prendatario', $request->input('idcredito_prendatario')];  
          }
          if($request->input('idforma_pago_credito') != ''){
            $where[] = ['tarifario.idforma_pago_credito', $request->input('idforma_pago_credito')];  
          }
          
          $creditos = DB::table('tarifario')
                            ->join('forma_pago_credito','forma_pago_credito.id','tarifario.idforma_pago_credito')
                            ->join('forma_credito','forma_credito.id','tarifario.idforma_credito')
                            ->join('credito_prendatario','credito_prendatario.id','tarifario.idcredito_prendatario')
                            ->where($where)
                            ->select(
                                'tarifario.*',
                                'forma_pago_credito.nombre as nombreformapago',
                                'forma_credito.nombre as nombreformacredito',
                                'credito_prendatario.nombre as nombreproductocredito'            
                            )
                            ->orderBy('tarifario.id','asc')
                            ->get();
          
          $html = '';
          foreach($creditos as $key => $value){
              
              $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data(this)'>
                            <td>".($key+1)."</td>
                            <td>{$value->codigo}</td>
                            <td>{$value->monto}</td>
                            <td>{$value->cuotas}</td>
                            <td>{$value->tem}</td>
                            <td>{$value->cargos_otros}</td>
                            <td>{$value->nombreformapago}</td>
                            <td>{$value->nombreformacredito}</td>
                            <td>{$value->nombreproductocredito}</td>
                        </tr>";
          }
          return array(
            'html' => $html
          );
          
        }
        else if($id == 'showproductocredito'){
          
          $producto_credito = DB::table('credito_prendatario')
                              ->where('credito_prendatario.idforma_credito',$request->input('tipo'))
                              ->select('credito_prendatario.*')
                              ->orderBy('credito_prendatario.id', 'asc')
                              ->get();

          return $producto_credito;
        }

    }

    public function edit(Request $request, $idtienda, $id)
    {
        
      
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
      $tarifario = DB::table('tarifario')
                            ->join('forma_pago_credito','forma_pago_credito.id','tarifario.idforma_pago_credito')
                            ->join('credito_prendatario','credito_prendatario.id','tarifario.idcredito_prendatario')
                            ->where('tarifario.id',$id)
                            ->select(
                                'tarifario.*',
                                'forma_pago_credito.nombre as nombreformapago',
                                'credito_prendatario.nombre as nombrecredito'            
                            )
                            ->orderBy('tarifario.id','desc')
                            ->first();
      
      if($request->input('view') == 'editar') {

        return view(sistema_view().'/tarifario/edit',[
          'tienda' => $tienda,
          'tarifario' => $tarifario,
          'forma_pago_credito' => $this->forma_pago_credito,
          'forma_credito' => $this->forma_credito
        ]);
      }
      else if($request->input('view') == 'eliminar'){
        return view(sistema_view().'/tarifario/delete',[
          'tienda' => $tienda,
          'tarifario' => $tarifario
          
        ]);
      }
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        if($request->input('view') == 'editar') {
  
            $rules = [
                'idforma_credito' => 'required',                   
                'idcredito_prendatario' => 'required',                 
                'idforma_pago_credito' => 'required',                 
                'monto' => 'required',                 
                'cuotas' => 'required',                 
                'tem' => 'required',                 
                'cargos_otros' => 'required',                 
            ];
          
            $messages = [
                'idforma_credito.required' => 'El Campo es Obligatorio.',
                'idcredito_prendatario.required' => 'El Campo es Obligatorio.',
                'idforma_pago_credito.required' => 'El Campo es Obligatorio.',
                'monto.required' => 'El Campo es Obligatorio.',
                'cuotas.required' => 'El Campo es Obligatorio.',
                'tem.required' => 'El Campo es Obligatorio.',
                'cargos_otros.required' => 'El Campo es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
              
            $idtarifario = DB::table('tarifario')->whereId($id)->update([
              'idforma_credito'       => $request->input('idforma_credito'),
              'idcredito_prendatario' => $request->input('idcredito_prendatario'),
              'idforma_pago_credito'  => $request->input('idforma_pago_credito'),
              'monto'                 => $request->input('monto'),
              'cuotas'                => $request->input('cuotas'),
              'tem'                   => $request->input('tem'),
              'cargos_otros'          => $request->input('cargos_otros'),
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
        DB::table('tarifario')->whereId($id)->delete();
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha elimino correctamente.'
        ]);
      }
      
    
    }
}
