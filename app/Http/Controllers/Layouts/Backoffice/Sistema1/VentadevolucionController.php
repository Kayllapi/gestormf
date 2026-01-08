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

class VentadevolucionController extends Controller
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
      $ventadevolucion = DB::table('s_ventadevolucion')
          ->join('s_venta','s_venta.id','s_ventadevolucion.idventa')
          ->join('users as cliente','cliente.id','s_venta.s_idusuariocliente')
          ->join('s_moneda as moneda','moneda.id','s_venta.s_idmoneda')
          ->join('users as responsable','responsable.id','s_ventadevolucion.idusuarioresponsable')
          ->join('s_tipoentrega as entrega','entrega.id','s_venta.s_idtipoentrega')
          ->join('s_tipocomprobante as comprobante','comprobante.id','s_venta.s_idcomprobante')
          ->where('s_ventadevolucion.idtienda',$idtienda)
          ->where('s_ventadevolucion.idestado',1)
          ->select(
                's_ventadevolucion.*',
                'cliente.nombre as nombrecliente',
                'cliente.apellidos as apellidoscliente',
                'cliente.identificacion as identificacioncliente',
                'moneda.nombre as nombremoneda',
                'responsable.nombre as nombreresponsable',
                'entrega.nombre as entreganombre',
                'comprobante.nombre as comprobantenombre'
            )
           ->orderBy('s_ventadevolucion.id','desc')
           ->paginate(10);
        $caja = caja($idtienda,Auth::user()->id);
        $idaperturacierre = 0;
           if($caja['resultado']=='ABIERTO'){
            $idaperturacierre = $caja['apertura']->id;
            }
       return view('layouts/backoffice/tienda/sistema/ventadevolucion/index',[
              'tienda'          => $tienda,
              'ventadevolucion' => $ventadevolucion,
              'idapertura'      => $idaperturacierre
           ]);
    }
  
  
  
   public function create(Request $request, $idtienda)
    {
     $request->user()->authorizeRoles($request->path(),$idtienda);
     
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
           return view('layouts/backoffice/tienda/sistema/ventadevolucion/create',[
             'tienda' => $tienda
        ]);
    }
  
  
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if($request->input('view') == 'registrar') {
            $rules = [
               'motivo'           => 'required',
            ]; 
            $messages = [
               'motivo.required'   => 'El "Motivo" es Obligatorio.',
            ];
            
            $this->validate($request,$rules,$messages);
          
            $post_produtos = json_decode($request->input('productos'));
          
            $idaperturacierre = 0;
            $caja = caja($idtienda,Auth::user()->id);
                    if($caja['resultado']!='ABIERTO'){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'La Caja debe estar Aperturada.'
                        ]);
                    }
            $idaperturacierre = $caja['apertura']->id;
            
            $venta= DB::table('s_venta')->whereId($request->input('idventa'))->first();
          
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
            $s_ventadevolucion = DB::table('s_ventadevolucion')
                ->where('s_ventadevolucion.idtienda',$idtienda)
                ->orderBy('s_ventadevolucion.codigoimpresion','desc')
                ->limit(1)
                ->first();
            $codigoimpresion = 1;
            if($s_ventadevolucion!=''){
                $codigoimpresion = $s_ventadevolucion->codigoimpresion+1;
            }
            $idventadevolucion = DB::table('s_ventadevolucion')->insertGetId([
                'fecharegistro'          => Carbon::now(),
                'fechaconfirmacion'      => Carbon::now(),
                'codigo'                 => $venta->codigo,
                'codigoimpresion'        => $codigoimpresion,
                'total'                  => $request->input('total'),
                'totalredondeado'        => $request->input('totalredondeado'),
                'motivo'                 => $request->input('motivo'),
                'idventa'                => $venta->id,
                'idusuarioresponsable'   => Auth::user()->id,
                'idmoneda'               => 1,
                'idaperturacierre'       => $idaperturacierre,
                'idestadoventadevolucion'=> 2,
                'idtienda'               => $idtienda,
                'idestado'               => 1,
            ]);

            foreach ($post_produtos as $item_producto3) {
                $cantidad       = $item_producto3->productCant;
                $preciounitario = number_format($item_producto3->productUnidad,2, '.', '');
                $precioventa    = number_format($preciounitario*$cantidad,2, '.', '');
                $valorunitario  = number_format(($preciounitario/$igv),2, '.', '');
                $valorventa     = number_format($valorunitario*$cantidad,2, '.', '');
                $impuesto       = number_format($precioventa-$valorventa,2, '.', '');

                $ventadetalle = DB::table('s_ventadetalle')->whereId($item_producto3->idventadetalle)->first();
                $producto = DB::table('s_producto')->whereId($ventadetalle->s_idproducto)->first();

                DB::table('s_ventadevoluciondetalle')->insert([
                    'codigo'            => $ventadetalle->codigo,
                    'concepto'          => $ventadetalle->concepto,
                    'cantidad'          => $cantidad,
                    'preciounitario'    => $preciounitario,
                    'total'             => $precioventa,
                    'por'               => $producto->por,
                    'idunidadmedida'    => $producto->idunidadmedida,
                    'idproducto'        => $ventadetalle->s_idproducto,
                    'idventadetalle'    => $item_producto3->idventadetalle,
                    'idventadevolucion' => $idventadevolucion,
                    'idtienda'          => $idtienda,
                    'idestado'          => 1,
                ]);
            
                productosaldo_actualizar(
                    $idtienda,
                    $ventadetalle->s_idproducto,
                    'DEVOLUCION VENTA',
                    $cantidad,
                    $producto->por,
                    $producto->idunidadmedida,
                    $item_producto3->idventadetalle
                );
            }
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.',
                'idventa'   => $idventadevolucion
            ]);
        }
    }
  
    public function show(Request $request, $idtienda, $id)
    {
        if($id == 'show_buscarventa'){
            $venta = DB::table('s_venta')
                ->join('users','users.id','s_venta.s_idusuariocliente')
                ->join('s_tipoentrega','s_tipoentrega.id','s_venta.s_idtipoentrega')
                ->where('s_venta.idtienda',$idtienda)
                ->where('s_venta.codigo',$request->input('ventacodigo'))
                ->select(
                    's_venta.*',
                    's_tipoentrega.nombre as tipoentreganombre',
                    DB::raw('IF(users.idtipopersona=1,
                    CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre),
                    CONCAT(users.identificacion," - ",users.apellidos)) as cliente'),
                )
                ->first(); 
          
          if ( !is_null($venta) ) {

                $ventadetalle = DB::table('s_ventadetalle')
                    ->where('s_ventadetalle.s_idventa',$venta->id)
                    ->orderBy('s_ventadetalle.id','asc')
                    ->get();  
            
                $html_detalle = '';
            
                foreach($ventadetalle as $value){
                    $cantidad_ventadevoluciondetalle = DB::table('s_ventadevoluciondetalle')
                        ->where('s_ventadevoluciondetalle.idproducto',$value->s_idproducto)
                        ->sum('s_ventadevoluciondetalle.cantidad');
                    $cantidad = $value->cantidad-$cantidad_ventadevoluciondetalle;
                  
                    if($cantidad>0){
                        $html_detalle = $html_detalle.'<tr id="'.$value->id.'" idventadetalle="'.$value->id.'" 
                              style="background-color: #008cea;color: #fff;height: 40px;">
                              <td>'.$value->codigo.'</td>
                              <td>'.$value->concepto.'</td>
                               <td><input class="form-control" type="text" value="'.$cantidad.'" disabled></td>   
                               <td><input class="form-control" type="number" value="'.$value->preciounitario.'" step="0.01" min="0" disabled></td> 
                               <td><input class="form-control" id="productCant'.$value->id.'" type="number" value="'.$cantidad.'"  min="1" onkeyup="calcularmonto()" onclick="calcularmonto()"></td>
                               <td><input class="form-control" id="productUnidad'.$value->id.'" type="number" value="'.$value->preciounitario.'" step="0.01" min="0" disabled></td>   
                               <td><input class="form-control" id="productTotal'.$value->id.'" type="text" value="'.$value->total.'" step="0.01" min="0" disabled></td>   
                              <td class="with-btn" width="10px"><a id="'.$value->id.'" href="javascript:;" onclick="eliminarproducto('.$value->id.')" class="btn btn-danger big-btn"><i class="fas fa-trash-alt"></i> Quitar</a></td>
                              </tr>';
                    }
                }
                return [ 
                  'venta'        => $venta,
                  'ventadetalle' => $html_detalle,
                ];
              
            } 
    
                
        }
    }
    public function edit(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      
      $ventadevolucion = DB::table('s_ventadevolucion')
          ->join('s_venta','s_venta.id','s_ventadevolucion.idventa')
          ->join('users as cliente','cliente.id','s_venta.s_idusuariocliente')
          ->join('s_moneda as moneda','moneda.id','s_venta.s_idmoneda')
          ->join('users as responsable','responsable.id','s_ventadevolucion.idusuarioresponsable')
          ->join('s_tipoentrega as entrega','entrega.id','s_venta.s_idtipoentrega')
          ->join('s_tipocomprobante as comprobante','comprobante.id','s_venta.s_idcomprobante')
          ->join('s_agencia as agencia','agencia.id','s_venta.s_idagencia')
          ->join('ubigeo','ubigeo.id','agencia.idubigeo')
          ->where('s_ventadevolucion.id',$id)
          ->select(
                's_ventadevolucion.*',
                'cliente.nombre as nombrecliente',
                'cliente.apellidos as apellidoscliente',
                'cliente.identificacion as identificacioncliente',
                'cliente.direccion as direccioncliente',
                'moneda.nombre as nombremoneda',
                'responsable.nombre as nombreresponsable',
                'entrega.nombre as entreganombre',
                'comprobante.nombre as comprobantenombre',
                'agencia.nombrecomercial as agencianombrecomercial',
                'agencia.ruc as agenciaruc',
                'agencia.direccion as agenciadireccion',
                'ubigeo.nombre as nombreubigeo'
           
            )
          ->orderBy('s_ventadevolucion.id','asc')
          ->first(); 
      
//       $configuracion = tienda_configuracion($idtienda);
      
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
       if($request->input('view') == 'detalle') {
         $ventadevoluciondetalles = DB::table('s_ventadevoluciondetalle')
              ->where('s_ventadevoluciondetalle.idventadevolucion',$ventadevolucion->id)
              ->select(
                's_ventadevoluciondetalle.*',
              )
              ->orderBy('s_ventadevoluciondetalle.id','asc')
              ->get();
         
         return view('layouts/backoffice/tienda/sistema/ventadevolucion/detalle',[
               'ventadevolucion'        => $ventadevolucion,
               'tienda'                 => $tienda,
               'ventadevoluciondetalles'=>$ventadevoluciondetalles,
              
            ]);
        }elseif($request->input('view') == 'ticket') {
        
            return view('layouts/backoffice/tienda/sistema/ventadevolucion/ticket',[
                'tienda'          => $tienda,
                'ventadevolucion' => $ventadevolucion,
              
            ]);
        }elseif($request->input('view') == 'ticketpdf') {
       
           $ventadevoluciondetalles = DB::table('s_ventadevoluciondetalle')
              ->where('s_ventadevoluciondetalle.idventadevolucion',$ventadevolucion->id)
              ->select(
                's_ventadevoluciondetalle.*',
              )
              ->orderBy('s_ventadevoluciondetalle.id','asc')
              ->get();
         
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/ventadevolucion/ticketpdf',[
                    'tienda'                   => $tienda,      
                    'ventadevolucion'          => $ventadevolucion,
                    'ventadevoluciondetalles'  =>$ventadevoluciondetalles,
                    'configuracion'            => $configuracion,
              
            ]);
            $ticket = 'Ticket_'.str_pad($ventadevolucion->codigo, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');

        }elseif($request->input('view') == 'anular') {
         $ventadevoluciondetalles = DB::table('s_ventadevoluciondetalle')
              ->where('s_ventadevoluciondetalle.idventadevolucion',$ventadevolucion->id)
              ->select(
                's_ventadevoluciondetalle.*',
              )
              ->orderBy('s_ventadevoluciondetalle.id','asc')
              ->get();
         
            return view('layouts/backoffice/tienda/sistema/ventadevolucion/anular',[
              'ventadevolucion'         => $ventadevolucion,
              'tienda'                  => $tienda,
              'ventadevoluciondetalles' =>$ventadevoluciondetalles,
            ]);
        }
    }
    public function update(Request $request, $idtienda, $idventa)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        
        if($request->input('view') == 'anular') {

            DB::table('s_ventadevolucion')->whereId($idventa)->update([
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
