<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use App\User;
use Hash;

class CompradevolucionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        return view(sistema_view().'/compradevolucion/tabla',[
          'tienda'            => $tienda,
        ]);
    }
  
    public function create(Request $request, $idtienda)
    {
         $request->user()->authorizeRoles($request->path(),$idtienda);
         $tienda = DB::table('tienda')->whereId($idtienda)->first();
         return view(sistema_view().'/compradevolucion/create',[
            'tienda'      => $tienda
         ]);
    }
  
  
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if($request->input('view') == 'registrar'){
            $rules = [
               'motivo'          => 'required',
            ]; 
            $messages = [
               'motivo.required' => 'El "Motivo" es Obligatorio.',
            ];
            
            $this->validate($request,$rules,$messages);
          
            $post_produtos = json_decode($request->input('productos'));
            //Apertura
            $idaperturacierre = 0;
            $apertura = sistema_apertura([
                'idtienda'          => $idtienda,
                'idsucursal'        => Auth::user()->idsucursal,
                'idusersrecepcion'  => Auth::user()->id,
            ]);
            if($apertura['resultado']!='ABIERTO'){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La Caja debe estar Aperturada.'
                ]);
            }
            $idaperturacierre = $apertura['idapertura'];
            
            //Fin apertura
            $compra = DB::table('s_compra')->whereId($request->input('idcompra'))->first();

            $igv = 1.18;
            $total_preciounitario = 0;
            $total_precioventa    = 0;
            $total_valorunitario  = 0;
            $total_valorventa     = 0;
            $total_impuesto       = 0;
            foreach ($post_produtos as $item_producto2) {
                $cantidad       = $item_producto2->productCant;
                $preciounitario = number_format($item_producto2->productUnidad, 2, '.', '');
                $precioventa    = number_format($preciounitario*$cantidad,2, '.', '');
                $valorunitario  = number_format(($preciounitario/$igv),2, '.', '');
                $valorventa     = number_format($valorunitario*$cantidad,2, '.', '');
                $impuesto       = number_format($precioventa-$valorventa,2, '.', '');

                $total_preciounitario = $total_preciounitario+$preciounitario;
                $total_precioventa    = $total_precioventa+$precioventa;
                $total_valorunitario  = $total_valorunitario+$valorunitario;
                $total_valorventa     = $total_valorventa+$valorventa;
                $total_impuesto       = $total_impuesto+$impuesto;
            }
           // obtener ultimo código
            $s_compradevolucion = DB::table('s_compradevolucion')
                ->where('s_compradevolucion.idtienda',$idtienda)
                ->orderBy('s_compradevolucion.codigoimpresion','desc')
                ->limit(1)
                ->first();

            $codigoimpresion = 1;
            if($s_compradevolucion!=''){
                $codigoimpresion = $s_compradevolucion->codigoimpresion+1;
            }
            $responsable = DB::table('users')->whereId(Auth::user()->id)->first();
            $moneda = DB::table('s_moneda')->whereId(1)->first();

            $idcompra = DB::table('s_compradevolucion')->insertGetId([
                'fecharegistro'          => Carbon::now(),
                'fechaconfirmacion'      => Carbon::now(),
                'codigo'                 => $compra->codigo,
                'codigoimpresion'        => $codigoimpresion,
                'total'                  => $request->input('total'),
                'totalredondeado'        => $request->input('totalredondeado'),
                'motivo'                 => $request->input('motivo'),
                'idcompra'               => $compra->id,
                'idusuarioresponsable'   => Auth::user()->id,
                'db_idmoneda'               => $moneda->nombre,
                'db_idusuarioresponsable'   => $responsable->nombrecompleto,
                'db_idestado'               => 'CONFIRMADO',
                's_idmoneda'             => 1,
                's_idaperturacierre'     => $idaperturacierre,
                'idtienda'               => $idtienda,
                's_idestado'             => 2, // 1 = PENDIENTE, 2 = CONFIRMADO, 3 = ANULADO

            ]);
            foreach ($post_produtos as $item_producto3){
                  $cantidad       = $item_producto3->productCant;
                  $preciounitario = number_format($item_producto3->productUnidad,2, '.', '');
                  $precioventa    = number_format($preciounitario*$cantidad,2, '.', '');
                  $valorunitario  = number_format(($preciounitario/$igv),2, '.', '');
                  $valorventa     = number_format($valorunitario*$cantidad,2, '.', '');
                  $impuesto       = number_format($precioventa-$valorventa,2, '.', '');

                  $compradetalle = DB::table('s_compradetalle')->whereId($item_producto3->idcompradetalle)->first();
               
                  DB::table('s_compradevoluciondetalle')->insert([
                      'concepto'           => $compradetalle->concepto,
                      'cantidad'           => $cantidad,
                      'preciounitario'     => $preciounitario,
                      'preciototal'        => $precioventa,
                      'idproducto'         => $compradetalle->s_idproducto,
                      'idcompradetalle'    => $item_producto3->idcompradetalle,
                      'idcompradevolucion' => $idcompra,
                      'idtienda'           => $idtienda,
                      'idestado'           => 1,
                   ]);
              
            }
            
            return response()->json([
                  'resultado'  => 'CORRECTO',
                  'mensaje'    => 'Se ha registrado correctamente.',
                  'idcompra'   => $idcompra
            ]);
        }
    }
  
    public function show(Request $request, $idtienda, $id)
    {
        if($id == 'show_table'){
            $idsucursal = Auth::user()->idsucursal;
            $idusuario = Auth::user()->id;
            $idapertura = sistema_apertura([
                'idtienda'          => $idtienda,
                'idsucursal'        => $idsucursal,
                'idusersrecepcion'  => $idusuario,
            ])['idapertura'];
      
            $compradevolucion = DB::table('s_compradevolucion')
                                ->join('s_compra','s_compra.id','s_compradevolucion.idcompra')
                                ->where('s_compradevolucion.idtienda',$idtienda)
                                ->where('s_compradevolucion.idsucursal',$idsucursal)
                                ->where('s_compradevolucion.idusuarioresponsable',$idusuario)

                                ->where('s_compra.codigo','LIKE','%'.$request['columns'][0]['search']['value'].'%')
                                ->where('s_compradevolucion.codigoimpresion','LIKE','%'.$request['columns'][1]['search']['value'].'%')
                                ->where('s_compradevolucion.totalredondeado','LIKE','%'.$request['columns'][2]['search']['value'].'%')
                                ->where('s_compra.fecharegistro','LIKE','%'.$request['columns'][3]['search']['value'].'%')
                                ->where('s_compra.db_idusuarioresponsable','LIKE','%'.$request['columns'][4]['search']['value'].'%')
                                ->where('s_compra.db_idcomprobante','LIKE','%'.$request['columns'][5]['search']['value'].'%')
                                ->where('s_compra.db_idusuarioproveedor','LIKE','%'.$request['columns'][6]['search']['value'].'%')
                                ->where('s_compradevolucion.motivo','LIKE','%'.$request['columns'][7]['search']['value'].'%')
                                ->where('s_compradevolucion.db_idestado','LIKE','%'.$request['columns'][8]['search']['value'].'%')

                                ->select(
                                        's_compradevolucion.*',
                                        's_compra.codigo as codigocompra',
                                        's_compra.db_idusuarioproveedor as proveedor',
                                        's_compra.db_idcomprobante as comprobantenombre'
                                    )
                                ->orderBy('s_compradevolucion.id','desc')
                                ->paginate($request->length,'*',null,($request->start/$request->length)+1);
      
            $tabla = [];
            foreach($compradevolucion as $value){
     
                $opcion = [];
                switch($value->s_idestado){
                    case 1:
                        $opcion[] = [
                            'nombre'  => 'Ticket de Anulacion',
                            'onclick' => '/'.$idtienda.'/compradevolucion/'.$value->id.'/edit?view=ticket',
                            'icono'   => 'receipt'
                        ];
                        break;
                    case 2:
                        $opcion[] = [
                            'nombre'  => 'Detalle',
                            'onclick' => '/'.$idtienda.'/compradevolucion/'.$value->id.'/edit?view=detalle',
                            'icono'   => 'edit'
                        ];
                        $opcion[] = [
                            'nombre'  => 'Ticket de Anulacion',
                            'onclick' => '/'.$idtienda.'/compradevolucion/'.$value->id.'/edit?view=ticket',
                            'icono'   => 'receipt'
                        ];
                        if ($idapertura==$value->s_idaperturacierre) {
                            $opcion[] = [
                                'nombre'  => 'Anular',
                                'onclick' => '/'.$idtienda.'/compradevolucion/'.$value->id.'/edit?view=anular',
                                'icono'   => 'ban'
                            ];
                        }
                        break;
                    case 3:
                        $opcion[] = [
                            'nombre'  => 'Detalle',
                            'onclick' => '/'.$idtienda.'/compradevolucion/'.$value->id.'/edit?view=detalle',
                            'icono'   => 'edit'
                        ];
                        break;
                }

              
                $tabla[] = [
                    'id' => $value->id,
                    'codigo' => str_pad($value->codigocompra, 8, "0", STR_PAD_LEFT) ,
                    'codigoimpresion' => str_pad($value->codigoimpresion, 8, "0", STR_PAD_LEFT) ,
                    'totalredondeado' => $value->totalredondeado,
                    'fecharegistro' => date_format(date_create($value->fecharegistro),"d/m/Y h:i:s A"),
                    'nombreresponsable' => $value->db_idusuarioresponsable,
                    'comprobantenombre' => $value->comprobantenombre,
                    'proveedor' => $value->proveedor,
                    'motivo' => $value->motivo,
                    'estado' => $value->db_idestado,
                    'opcion' => $opcion
                ];
            }
            return response()->json([
                'start'           => $request->start,
                'draw'            => $request->draw,
                'recordsTotal'    => $request->length,
                'recordsFiltered' => $compradevolucion->total(),
                'data'            => $tabla,
            ]);
        }
        elseif($id == 'show_buscarcompra'){
            $compra = DB::table('s_compra')
                ->join('users','users.id','s_compra.s_idusuarioproveedor')
                ->join('s_tipocomprobante','s_tipocomprobante.id','s_compra.s_idcomprobante')
                ->where('s_compra.codigo',$request->input('compracodigo'))
                ->where('s_compra.idtienda',$idtienda)
                ->select(
                    's_compra.*',
                    's_tipocomprobante.nombre as tipocomprobantenombre',
                    'users.nombrecompleto as proveedor'
                )
                ->first(); 
            if(!is_null($compra)){
                $compradetalle = DB::table('s_compradetalle')
                    ->where('s_compradetalle.s_idcompra',$compra->id)
                    ->orderBy('s_compradetalle.id','asc')
                    ->get();  
                $html_detalle = '';
            
                foreach($compradetalle as $value){
                    $compradetalle = DB::table('s_compradetalle')->where('s_compradetalle.idcompradetalle',$value->id);
                    $html_detalle = $html_detalle.'<tr id="'.$value->id.'" idcompradetalle="'.$value->id.'" style="background-color: #008cea;color: #fff;height: 40px;">
                        <td>'.$value->concepto.'</td>
                        <td><input class="form-control" type="text" value="'.$value->cantidad.'" disabled></td>   
                        <td><input class="form-control" type="number" value="'.$value->preciounitario.'" step="0.01" min="0" disabled></td> 
                        <td><input class="form-control" id="productCant'.$value->id.'" type="number" value="'.$value->cantidad.'" min="1" onkeyup="calcularmonto()" onclick="calcularmonto()"></td>
                        <td><input class="form-control" id="productUnidad'.$value->id.'" type="number" value="'.$value->preciounitario.'" step="0.01" min="0" disabled></td>   
                        <td><input class="form-control" id="productTotal'.$value->id.'" type="text" value="'.$value->preciototal.'" step="0.01" min="0" disabled></td>   
                        <td class="with-btn" width="10px"><a id="'.$value->id.'" href="javascript:;" onclick="eliminarproducto('.$value->id.')" class="btn btn-danger big-btn"><i class="fas fa-trash-alt"></i> Quitar</a></td>
                        </tr>';
                  }
                  return [ 
                    'compra'        => $compra,
                    'compradetalle' => $html_detalle,
                  ];
              } 
         }
    }
    public function edit(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      
      $compradevolucion = DB::table('s_compradevolucion')
          ->join('s_compra','s_compra.id','s_compradevolucion.idcompra')
          ->join('tienda','tienda.id','s_compra.idtienda')
          ->where('s_compradevolucion.id',$id)
          ->select(
                's_compradevolucion.*',
                's_compradevolucion.*',
                's_compra.codigo as codigocompra',
                's_compra.db_idusuarioproveedor as proveedor',
                's_compra.db_idusuarioresponsable as responsable',
                's_compra.db_idcomprobante as comprobantenombre',
                'tienda.nombre as tiendanombre',
                'tienda.direccion as  tiendadireccion'
            )
           ->orderBy('s_compradevolucion.id','desc')
           ->first();
      
      
      
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
       if($request->input('view') == 'detalle') {
         $compradevoluciondetalles = DB::table('s_compradevoluciondetalle')
              ->where('s_compradevoluciondetalle.idcompradevolucion',$compradevolucion->id)
              ->select(
                's_compradevoluciondetalle.*',
              )
              ->orderBy('s_compradevoluciondetalle.id','asc')
              ->get();
         
         return view(sistema_view().'/compradevolucion/detalle',[
               'compradevolucion'         => $compradevolucion,
               'tienda'                   => $tienda,
               'compradevoluciondetalles' => $compradevoluciondetalles,
              
            ]);
        }elseif($request->input('view') == 'ticket') {
        
          return view(sistema_view().'/compradevolucion/ticket',[
                'tienda'           => $tienda,
                'compradevolucion' => $compradevolucion,
              
            ]);
        }elseif($request->input('view') == 'ticketpdf') {
            
           $compradevoluciondetalles = DB::table('s_compradevoluciondetalle')
              ->where('s_compradevoluciondetalle.idcompradevolucion',$compradevolucion->id)
              ->select(
                's_compradevoluciondetalle.*',
              )
              ->orderBy('s_compradevoluciondetalle.id','asc')
              ->get();
         
            $pdf = PDF::loadView(sistema_view().'/compradevolucion/ticketpdf',[
                    'tienda'                    => $tienda,      
                    'compradevolucion'          => $compradevolucion,
                    'compradevoluciondetalles'  => $compradevoluciondetalles,
              
            ]);
            $ticket = 'Ticket_'.str_pad($compradevolucion->codigo, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');

        }elseif($request->input('view') == 'anular') {
         
           $compradevoluciondetalles = DB::table('s_compradevoluciondetalle')
              ->where('s_compradevoluciondetalle.idcompradevolucion',$compradevolucion->id)
              ->select(
                's_compradevoluciondetalle.*',
              )
              ->orderBy('s_compradevoluciondetalle.id','asc')
              ->get();
         
            return view(sistema_view().'/compradevolucion/anular',[
              'compradevolucion'         => $compradevolucion,
              'tienda'                   => $tienda,
              'compradevoluciondetalles' => $compradevoluciondetalles,
            ]);
        }
    }
    public function update(Request $request, $idtienda, $idcompra)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        
        if($request->input('view') == 'anular') {

            DB::table('s_compradevolucion')->whereId($idcompra)->update([
               'fechaanulacion' => Carbon::now(),
               'db_idestado'    => 'ANULADO',
               's_idestado'     => 3,
            ]);
            
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha anulado correctamente.'
             ]);
        } 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idtienda, $s_idventa)
    {

    }
}
