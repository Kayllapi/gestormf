<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

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
     
      json_compradevolucion($idtienda,$request->name_modulo);
          return view('layouts/backoffice/tienda/nuevosistema/compradevolucion/index',[
              'tienda'            => $tienda,
           ]);
    }
  
    public function create(Request $request, $idtienda)
    {
         $request->user()->authorizeRoles($request->path(),$idtienda);
         $tienda = DB::table('tienda')->whereId($idtienda)->first();
             return view('layouts/backoffice/tienda/nuevosistema/compradevolucion/create',[
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
                $caja = caja($idtienda,Auth::user()->id);
                    if($caja['resultado']!='ABIERTO'){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'La Caja debe estar Aperturada.'
                        ]);
                    }
            $idaperturacierre = $caja['apertura']->id;
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
          $idcompra = DB::table('s_compradevolucion')->insertGetId([
                'fecharegistro'          => Carbon::now(),
                'fechaconfirmacion'      => Carbon::now(),
                'codigo'                 => $compra->codigo,
                'codigoimpresion'        => $codigoimpresion,
                'total'                  => $request->input('total'),
                'totalredondeado'        => $request->input('totalredondeado'),
                'motivo'                 => $request->input('motivo'),
                'idcompra'               => $compra->id,
                'idtienda'               => $compra->idtienda,
                'idusuarioresponsable'   => Auth::user()->id,
                's_idmoneda'             => 1,
                's_idaperturacierre'     => $idaperturacierre,
                's_idestado'             => 2,
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
                      'cantidad'           => $compradetalle->cantidad,
                      'preciounitario'     => $compradetalle->preciounitario,
                      'preciototal'        => $compradetalle->preciototal,
                      'idproducto'         => $compradetalle->s_idproducto,
                      'idcompradetalle'    => $item_producto3->idcompradetalle,
                      'idcompradevolucion' => $idcompra,
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
        if($id == 'show_buscarcompra'){
            $compra = DB::table('s_compra')
                ->join('users','users.id','s_compra.s_idusuarioproveedor')
                ->join('s_tipocomprobante','s_tipocomprobante.id','s_compra.s_idcomprobante')
                ->where('s_compra.codigo',$request->input('compracodigo'))
                ->where('s_compra.idtienda',$idtienda)
                ->select(
                    's_compra.*',
                    's_tipocomprobante.nombre as tipocomprobantenombre',
                    DB::raw('IF(users.idtipopersona=1,
                    CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre),
                    CONCAT(users.identificacion," - ",users.apellidos)) as proveedor'),
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
         }else if($id =='show-moduloactualizar'){
           json_compradevolucion($idtienda,$request->name_modulo);
        }
    }
    public function edit(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      
      $compradevolucion = DB::table('s_compradevolucion')
          ->join('s_compra','s_compra.id','s_compradevolucion.idcompra')
          ->join('users as proveedor','proveedor.id','s_compra.s_idusuarioproveedor')
          ->join('users as responsable','responsable.id','s_compradevolucion.idusuarioresponsable')
          ->join('s_tipocomprobante as comprobante','comprobante.id','s_compra.s_idcomprobante')
          ->join('tienda','tienda.id','s_compra.idtienda')
          ->where('s_compradevolucion.id',$id)
          ->select(
                's_compradevolucion.*',
                'proveedor.nombre as nombreproveedor',
                'proveedor.apellidos as apellidosproveedor',
                'proveedor.identificacion as identificacionproveedor',
                'proveedor.direccion as direccionproveedor',
                'responsable.nombre as nombreresponsable',
                'comprobante.nombre as comprobantenombre',
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
         
         return view('layouts/backoffice/tienda/nuevosistema/compradevolucion/detalle',[
               'compradevolucion'         => $compradevolucion,
               'tienda'                   => $tienda,
               'compradevoluciondetalles' => $compradevoluciondetalles,
              
            ]);
        }elseif($request->input('view') == 'ticket') {
        
          return view('layouts/backoffice/tienda/nuevosistema/compradevolucion/ticket',[
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
         
            $configuracion = configuracion_facturacion($idtienda);
         
            $pdf = PDF::loadView('layouts/backoffice/tienda/nuevosistema/compradevolucion/ticketpdf',[
                    'tienda'                    => $tienda,      
                    'compradevolucion'          => $compradevolucion,
                    'compradevoluciondetalles'  => $compradevoluciondetalles,
                    'configuracion'             => $configuracion,
              
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
         
            return view('layouts/backoffice/tienda/nuevosistema/compradevolucion/anular',[
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
               's_idestado'       => 3,
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
