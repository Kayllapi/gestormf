<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class FichageneradaController extends Controller
{
    public function __construct()
    {
        //
    }
    public function index(Request $request,$idtienda)
    {
        // ACTUALIZAR e eliminar durante el dia
        $credito_garantias = DB::table('credito_garantia')
              ->join('credito','credito.id','credito_garantia.idcredito')
              ->join('users as cliente','cliente.id','credito_garantia.idcliente')
              ->where('credito.idliquidaciongarantia',1)
              ->select(
                'credito.*',
              )
              ->get();
      
        $fecha = Carbon::now();
        foreach($credito_garantias as $value){
            $ultimafecha = date_format(date_create($value->fechaliquidaciongarantia),"Y-m-d").' 23:59:59';
            if($fecha>=$ultimafecha){
                DB::table('credito')->whereId($value->id)->update([
                    'fechaliquidaciongarantia' => null,
                    'idliquidaciongarantia' => 0,
                    'idliquidaciongarantiaresponsable' => 0,
                ]);
            }
        }
        // FIN ACTUALIZAR 
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            
            $agencias = DB::table('tienda')->get();
          
            return view(sistema_view().'/fichagenerada/tabla',[
              'tienda' => $tienda,
              'agencias' => $agencias,
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

        if($id == 'showtable'){
          
          $where = [];
          if($request->idagencia!=''){
              $where[] = ['credito.idtienda',$request->idagencia];
          }
     
          if($request->fecha_inicio!='' and $request->fecha_fin!=''){
              $where[] = ['credito.fechaliquidaciongarantia','>=',$request->fecha_inicio.' 00:00:00'];
              $where[] = ['credito.fechaliquidaciongarantia','<=',$request->fecha_fin.' 23:59:59'];
          }
          
          $credito_garantias = DB::table('credito_garantia')
              ->join('credito','credito.id','credito_garantia.idcredito')
              ->join('users as cliente','cliente.id','credito_garantia.idcliente')
              ->whereIn('credito.idliquidaciongarantia',[1,2])
              ->where($where)
              ->select(
                'credito_garantia.*',
                'cliente.nombrecompleto as clientenombrecompleto',
                'cliente.identificacion as dni'
              )
              ->orderBy('credito_garantia.fecharegistro_listaremate','asc')
              ->get();
          $porcentaje_descuento_liquidacion = configuracion($idtienda,'porcentaje_descuento_liquidacion')['valor'];
          $html = '';
          foreach($credito_garantias as $key => $value){
                  
              $valor_comercial_descuento = number_format($value->valor_comercial - ($value->valor_comercial * $porcentaje_descuento_liquidacion / 100), 2, '.', '');

              $html .= "<tr id='show_data_select' idcredito='{$value->id}'>
                                <td>{$value->garantias_codigo}</td>
                                <td>{$value->clientenombrecompleto}</td>
                                <td>{$value->dni}</td>
                                <td>{$value->garantias_tipogarantia}</td>
                                <td>{$value->descripcion}</td>
                                <td>{$value->garantias_serie_motor_partida}</td>
                                <td>{$value->garantias_modelo_tipo}</td>
                                <td style='text-align:right;'>{$value->valor_comercial}</td>
                                <td style='text-align:right;'>{$valor_comercial_descuento}</td>
                                <td style='text-align:right;'>{$value->valor_realizacion}</td>
                                <td style='text-align:right;'>{$value->precioliquidacion}</td>
                                <td>{$value->garantias_accesorio_doc}</td>
                                <td>{$value->garantias_color}</td>
                                <td>{$value->garantias_fabricacion}</td>
                                <td>{$value->garantias_placa}</td>
                        </tr>";
          }
          if(count($credito_garantias)==0){
              $html.= '<tr><td colspan="15" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
          }
          return array(
            'html' => $html
          );
          
        }

    }

    public function edit(Request $request, $idtienda, $id)
    {
      $tienda = DB::table('tienda')->whereId($idtienda)->first();

        if($request->input('view') == 'exportar') {
            return view(sistema_view().'/fichagenerada/exportar',[
                'tienda' => $tienda,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'idagencia' => $request->idagencia,
            ]);
        }
        else if( $request->input('view') == 'exportar_pdf' ){
              
            
          $where = [];
          if($request->idagencia!=''){
              $where[] = ['credito.idtienda',$request->idagencia];
          }
          if($request->fecha_inicio!='' and $request->fecha_fin!=''){
              $where[] = ['credito.fechaliquidaciongarantia','>=',$request->fecha_inicio.' 00:00:00'];
              $where[] = ['credito.fechaliquidaciongarantia','<=',$request->fecha_fin.' 23:59:59'];
          }
          
          $creditos = DB::table('credito')
              ->where($where)
              ->select(
                'credito.*',
              )
              ->orderBy('credito.id','desc')
              ->get();
          
            $agencia = DB::table('tienda')->whereId($request->idagencia)->first();
        
            $pdf = PDF::loadView(sistema_view().'/fichagenerada/exportar_pdf',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'creditos' => $creditos,
            ]); 
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('CREDITO_DESEMBOLSADO.pdf');
        }  
        elseif($request->input('view') == 'eliminar') {
        
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[1,2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.id as idpermiso','permiso.nombre as nombrepermiso')
                ->get();
            
            $credito_garantia = DB::table('credito_garantia')
                    ->whereId($id)
                    ->first();
          
            $credito = DB::table('credito')
                    ->whereId($credito_garantia->idcredito)
                    ->first();
          
            return view(sistema_view().'/fichagenerada/eliminar',[
                'tienda' => $tienda,
                'usuarios' => $usuarios,
                'idcredito_garantia' => $id,
                'credito' => $credito,
            ]);
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        if($request->input('view') == 'eliminar'){
            $rules = [
                'idresponsable' => 'required',          
                'responsableclave' => 'required',              
            ];

            $messages = [
                'idresponsable.required' => 'El "Responsable" es Obligatorio.',
                'responsableclave.required' => 'La "Contraseña" es Obligatorio.',
            ];

            $this->validate($request,$rules,$messages);

            $usuario = DB::table('users')
                ->where('users.id',$request->idresponsable)
                ->where('users.clave',$request->responsableclave)
                ->first();
            $idresponsable = 0;
            if($usuario!=''){
                $idresponsable = $usuario->id;
            }else{
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El usuario y/o la contraseña es incorrecta!!.'
                ]);
            }
            $credito_garantia = DB::table('credito_garantia')
                ->whereId($id)
                ->first();
          
            DB::table('credito')->whereId($credito_garantia->idcredito)->update([
                'fechaliquidaciongarantia' => null,
                'idliquidaciongarantia' => 0,
                'idliquidaciongarantiaresponsable' => 0,
            ]);
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha eliminado correctamente.',
            ]);
        }
    }

    public function destroy(Request $request, $idtienda, $id)
    {
    }
}
