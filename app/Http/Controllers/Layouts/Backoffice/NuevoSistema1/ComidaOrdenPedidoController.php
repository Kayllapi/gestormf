<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;

class ComidaOrdenPedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      return view('layouts/backoffice/tienda/sistema/comidaordenpedido/index', [
          'tienda' => $tienda,
      ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
    
      $pisos = DB::table('s_comida_piso')
          ->where('s_comida_piso.idtienda', $idtienda)
          ->get();
      return view('layouts/backoffice/tienda/sistema/comidaordenpedido/create', [
          'tienda' => $tienda,
          'pisos' => $pisos,
      ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      if ($request->view == 'registrarmesa') {
          
        $s_comida_ordenpedido = DB::table('s_comida_ordenpedido')
            ->where('s_comida_ordenpedido.idtienda',$idtienda)
            ->orderBy('s_comida_ordenpedido.codigo','desc')
            ->limit(1)
            ->first();
        $codigo_ordenpedido = 1;
        if($s_comida_ordenpedido!=''){
            $codigo_ordenpedido = $s_comida_ordenpedido->codigo+1;
        }
        
        DB::table('s_comida_ordenpedido')->insert([
          'fecharegistro' => Carbon::now(),
          'codigo' => $codigo_ordenpedido,
          'total' => 0,
          'idresponsable' => Auth::user()->id,
          'idpiso' => $request->idpiso,
          'idambiente' => $request->idambiente,
          'idmesa' => $request->idmesa,
          'idtienda' => $idtienda,
          'idestado' => 1,
        ]);
        
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha registrado correctamente.',
        ]);
      }
      elseif ($request->view == 'registrarproducto') {
        
          /* idestado
          * 1 = pedido
          * 2 = comandado
          */
          DB::table('s_comida_ordenpedidodetalle')->insert([
                'fecharegistro' => Carbon::now(),
                'precio' => $request->precio,
                'cantidad' => $request->cantidad,
                'total' => $request->precio*$request->cantidad,
                'observacion' => $request->observacion,
                'idproducto' => $request->idproducto,
                'idcomida_ordenpedido' => $request->idpedido,
                'idestado' => 1
          ]);

          $total = DB::table('s_comida_ordenpedidodetalle')->where('idcomida_ordenpedido',$request->idpedido)->sum('total');

          DB::table('s_comida_ordenpedido')->whereId($request->idpedido)->update([
            'total' => $total
          ]);

          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha registrado correctamente.',
          ]);
      }
      elseif ($request->view == 'registrarproductocantidad') {

          DB::table('s_comida_ordenpedidodetalle')->whereId($request->idpedidodetalle)->update([
                'precio' => $request->precio,
                'cantidad' => $request->cantidad,
                'total' => $request->precio*$request->cantidad,
          ]);
        
          $total = DB::table('s_comida_ordenpedidodetalle')->where('idcomida_ordenpedido',$request->idpedido)->sum('total');
        
          DB::table('s_comida_ordenpedido')->whereId($request->idpedido)->update([
            'total' => $total
          ]);

          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha registrado correctamente.',
          ]);
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);

      if ($id == 'show-indexordenpedido') {
            $ordenpedidos = DB::table('s_comida_ordenpedido as ordenpedido')
                ->join('s_comida_piso as piso', 'piso.id', 'ordenpedido.idpiso')
                ->join('s_comida_ambiente as ambiente', 'ambiente.id', 'ordenpedido.idambiente')
                ->join('s_comida_mesa as mesa', 'mesa.id', 'ordenpedido.idmesa')
                ->join('users as responsable', 'responsable.id', 'ordenpedido.idresponsable')
                ->where('ordenpedido.idtienda', $idtienda)
                ->select(
                    'ordenpedido.*',
                    'piso.nombre as nombrepiso',
                    'ambiente.nombre as nombreambiente',
                    'mesa.numero_mesa as nombremesa',
                    'responsable.nombre as responsablenombre',
                    'responsable.apellidos as responsableapellidos'
                )
                ->orderBy('ordenpedido.id','desc')
                ->paginate($request->input('length'), ['*'], 'page', (($request->input('start')/$request->input('length'))+1));

            $tabla = [];
            foreach($ordenpedidos as $value){
                $estado = '';
                if ($value->idestado == 1) {
                  $estado = '<span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span>';
                } elseif ($value->idestado == 2) {
                  $estado = '<span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Comandado</span>';
                } elseif ($value->idestado == 3) {
                  $estado = '<span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Vendido</span>';
                } elseif ($value->idestado == 4) {
                  $estado = '<span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span>';
                }
              
                $opcion = "";
                if ($value->idestado == 1) {
                    $opcion = '<li><a href="'.url('backoffice/tienda/sistema/'.$idtienda.'/comidaordenpedido/create?idpedido='.$value->id).'"><i class="fa fa-check"></i> Pedidos</a></li>';
                }elseif ($value->idestado == 2) {
                    $opcion = '<li><a href="javascript:;" onclick="anular_ordenpedido('.$value->id.')"><i class="fa fa-ban"></i> Anular</a></li>';
                }elseif ($value->idestado == 3) {
                    $opcion = '<li><a href="javascript:;" onclick="detalle_ordenpedido('.$value->id.')"><i class="fa fa-list"></i> Detalle</a></li>';
                }elseif ($value->idestado == 4) {
                    $opcion = '<li><a href="javascript:;" onclick="detalle_ordenpedido('.$value->id.')"><i class="fa fa-list"></i> Detalle</a></li>';
                }
              
                $fechapedido = !is_null($value->fecharegistro) ? date_format(date_create($value->fecharegistro), "d/m/Y h:i:s A") : '---';
                $fechacomandado = !is_null($value->fechaconfirmacion) ? date_format(date_create($value->fechaconfirmacion), "d/m/Y h:i:s A") : '---';
                $fechavendido = !is_null($value->fechavendido) ? date_format(date_create($value->fechavendido), "d/m/Y h:i:s A") : '---';
                $tabla[] = [
                    'codigo' => str_pad($value->codigo, 6, "0", STR_PAD_LEFT),
                    'datapedido' => 'Mesa '.str_pad($value->nombremesa, 2, "0", STR_PAD_LEFT),
                    'responsable' => $value->responsableapellidos.', '.$value->responsablenombre,
                    'fechapedido' => $fechapedido,
                    'fechacomandado' => $fechacomandado,
                    'fechavendido' => $fechavendido,
                    'totalpedido' => $value->total,
                    'estado' => $estado,
                    'opcion' => $opcion
                ];
            }
            return json_encode([
                'draw' => $request->input('draw'),
                'recordsTotal' => $ordenpedidos->total(),
                'recordsFiltered' => $ordenpedidos->total(),
                'data' => $tabla
            ]);
        }
      
      elseif ($id == 'show-seleccionarmesa') {
          $ordenpedidos = DB::table('s_comida_ordenpedido as ordenpedido')
                ->join('s_comida_piso as piso', 'piso.id', 'ordenpedido.idpiso')
                ->join('s_comida_ambiente as ambiente', 'ambiente.id', 'ordenpedido.idambiente')
                ->join('s_comida_mesa as mesa', 'mesa.id', 'ordenpedido.idmesa')
                ->join('users as responsable', 'responsable.id', 'ordenpedido.idresponsable')
                ->where([
                  ['ordenpedido.idtienda', $idtienda],
                  ['ordenpedido.idestado', 1],
                  ['ordenpedido.idresponsable', Auth::user()->id]
                ])
                ->select(
                    'ordenpedido.*',
                    'piso.nombre as nombrepiso',
                    'ambiente.nombre as nombreambiente',
                    'mesa.numero_mesa as nombremesa',
                    'responsable.nombre as responsablenombre',
                    'responsable.apellidos as responsableapellidos'
                )
                ->orderBy('ordenpedido.id','desc')
                ->get();
          $pedidos = '';
        
          foreach($ordenpedidos as $value){
              $datetime1 = date_create($value->fecharegistro);
              $datetime2 = date_create(date('Y-m-d h:i:s'));
              $contador  = date_diff($datetime2, $datetime1);
              $differenceFormat = '%i';
              $tiempoxminuto  = $contador->format($differenceFormat) * 1;
            
              $pedidos = $pedidos.'<tr>
                                     <td>'.str_pad($value->codigo, 6, "0", STR_PAD_LEFT).'</td>
                                     <td>Mesa '.str_pad($value->nombremesa, 2, "0", STR_PAD_LEFT).'</td>
                                     <td>Hace '.$tiempoxminuto.' minutos</td>
                                     <td>'.$value->responsablenombre.'</td>
                                     <td>'.$value->total.'</td>
                                     <td><a href="javascript:;" onclick="mostrar_producto('.$value->id.',\'Mesa '.str_pad($value->nombremesa, 2, "0", STR_PAD_LEFT).'\')" class="btn btn-warning big-btn" style="padding: 10px 15px;"><i class="fa fa-plus"></i> Pedido</a></td>
                                     <td><a href="javascript:;" id="modal-eliminarmesa" onclick="mesa_modal_eliminar('.$value->id.')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-trash"></i> Eliminar</a></td>
                                 </tr>';
          }
          return [ 
            'pedidos' => $pedidos
          ];
      }
      elseif ($id == 'show-seleccionarproducto') {
          $pedido = DB::table('s_comida_ordenpedido')
                ->whereId($request->idpedido)
                ->first();
          $ordenpedidodetalles = DB::table('s_comida_ordenpedidodetalle')
              ->join('s_producto as producto', 'producto.id', 's_comida_ordenpedidodetalle.idproducto')
              ->where('s_comida_ordenpedidodetalle.idcomida_ordenpedido', $request->idpedido)
              ->select(
                's_comida_ordenpedidodetalle.*',
                'producto.nombre as productonombre'
              )
              ->orderBy('s_comida_ordenpedidodetalle.id', 'asc')
              ->get();
          $pedidodetalles = '';
          foreach($ordenpedidodetalles as $value){
              $datetime1 = date_create($value->fecharegistro);
              $datetime2 = date_create(date('Y-m-d h:i:s'));
              $contador  = date_diff($datetime2, $datetime1);
              $differenceFormat = '%i';
              $tiempo  = $contador->format($differenceFormat) * 1;
            
              $estado = '';
              if ($value->idestado == 1) {
                $estado = '<span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span>';
              } elseif ($value->idestado == 2) {
                $estado = '<span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Comandado</span>';
              }
            
              $fecharegistro = !is_null($value->fecharegistro) ? date_format(date_create($value->fecharegistro), "d/m/Y h:i:s A") : '---';
              $fechacomandado = !is_null($value->fechacomandado) ? date_format(date_create($value->fechacomandado), "d/m/Y h:i:s A") : '---';
            
              $pedidodetalles = $pedidodetalles.'<tr id="'.$value->id.'" idproducto="'.$value->idproducto.'">
                                     <td>'.$fecharegistro.'</td>
                                     <td>'.$fechacomandado.'</td>
                                     <td>'.$value->productonombre.'</td>
                                     <td><input type="text" id="pedido_producto_observacion_lista'.$value->id.'" value="'.$value->observacion.'" disabled></td>
                                     <td>Hace '.$tiempo.' minutos</td>
                                     <td>'.$estado.'</td>
                                     <td>
                                     <div class="custom-form">
                                     <div class="quantity fl-wrap">
                                        <div class="quantity-item">
                                            <input type="button" value="-" class="minus" onclick="operador_cantidad_lista('.$value->idcomida_ordenpedido.',\'-\','.$value->id.')" style="width: 30%;height: 38px;">
                                            <input type="text" id="pedido_producto_cantidad_lista'.$value->id.'" class="qty" min="1" step="1" value="'.$value->cantidad.'" style="padding-left: 0px;float: left;width: 40%;height: 38px;background: #008cea;" disabled>
                                            <input type="button" value="+" class="plus" onclick="operador_cantidad_lista('.$value->idcomida_ordenpedido.',\'+\','.$value->id.')" style="width: 30%;height: 38px;">
                                        </div>
                                     </div>  
                                     </div> 
                                     </td>
                                     <td><input type="text" id="pedido_producto_precio_lista'.$value->id.'" value="'.$value->precio.'" disabled></td>
                                     <td><input type="text" id="pedido_producto_total_lista'.$value->id.'" value="'.$value->total.'" disabled></td>
                                     <td><a href="javascript:;" onclick="producto_eliminar('.$value->id.')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-trash"></i> Eliminar</a></td>
                                 </tr>';
            
          }
          return [ 
            'pedidodetalles' => $pedidodetalles,
            'pedido' => $pedido
          ];
      }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda = DB::table('tienda')->whereId($idtienda)->first();

      /*if ($request->view == 'ordenpedido') {
        $numero_mesa = $id;
        $categorias = DB::table('s_categoria')
          ->where([
            ['idtienda', $idtienda],
            ['s_idcategoria', 0]
          ])
          ->orderBy('id', 'desc')
          ->get();
        
        $agencia = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
        $tipoentregas = DB::table('s_tipoentrega')->get();
        $tipopersonas = DB::table('tipopersona')->get();
        $monedas = DB::table('s_moneda')->get();
        $tipopago = DB::table('s_tipopago')->get();
        $configuracion = configuracion_comercio($idtienda);
        $configuracion_facturacion = configuracion_facturacion($idtienda);
        
        return view('layouts/backoffice/tienda/sistema/comidaordenpedido/ordenpedido', [
            'tienda' => $tienda,
            'agencia' => $agencia,
            'tipopersonas' => $tipopersonas,
            'tipoentregas' => $tipoentregas,
            'monedas' => $monedas,
            'configuracion_facturacion' => $configuracion_facturacion,
            'tipopago' => $tipopago,
          
            'numero_mesa' => $numero_mesa,
          
            'configuracion' => $configuracion,
            'categorias' => $categorias
        ]);
      }
      elseif ($request->view == 'comida_detalleordenpedido') {
        $ordenpedido = DB::table('s_comida_ordenpedido as ordenpedido')->whereId($id)->first();
        $ordenpedidodetalles = DB::table('s_comida_ordenpedidodetalle')
          ->join('s_comida_ordenpedido as ordenpedido', 'ordenpedido.id', 's_comida_ordenpedidodetalle.idcomida_ordenpedido')
          ->join('s_producto as producto', 'producto.id', 's_comida_ordenpedidodetalle.idproducto')
          ->where([
            ['s_comida_ordenpedidodetalle.idcomida_ordenpedido', $id]
          ])
          ->select(
            's_comida_ordenpedidodetalle.*',
            'producto.codigo as productocodigo',
            'producto.nombre as productonombre'
          )
          ->orderBy('s_comida_ordenpedidodetalle.id', 'asc')
          ->get();
        return view('layouts/backoffice/tienda/sistema/comidaordenpedido/detalle', [
          'tienda' => $tienda,
          'ordenpedido' => $ordenpedido,
          'ordenpedidodetalles' => $ordenpedidodetalles
        ]);
      }
      elseif ($request->view == 'comida_anularordenpedido') {
        $ordenpedido = DB::table('s_comida_ordenpedido as ordenpedido')->whereId($id)->first();
        $ordenpedidodetalles = DB::table('s_comida_ordenpedidodetalle')
          ->join('s_comida_ordenpedido as ordenpedido', 'ordenpedido.id', 's_comida_ordenpedidodetalle.idcomida_ordenpedido')
          ->join('s_producto as producto', 'producto.id', 's_comida_ordenpedidodetalle.idproducto')
          ->where([
            ['s_comida_ordenpedidodetalle.idcomida_ordenpedido', $id]
          ])
          ->select(
            's_comida_ordenpedidodetalle.*',
            'producto.codigo as productocodigo',
            'producto.nombre as productonombre'
          )
          ->orderBy('s_comida_ordenpedidodetalle.id', 'asc')
          ->get();
        return view('layouts/backoffice/tienda/sistema/comidaordenpedido/anular', [
          'tienda' => $tienda,
          'ordenpedido' => $ordenpedido,
          'ordenpedidodetalles' => $ordenpedidodetalles
        ]);
      }
      elseif ($request->view == 'comida_eliminarordenpedido') {
        $ordenpedido = DB::table('s_comida_ordenpedido as ordenpedido')
          ->join('s_comida_mesa', 's_comida_mesa.id', 'ordenpedido.idmesa')
          ->where('ordenpedido.id',$id)
          ->select(
            'ordenpedido.*',
            's_comida_mesa.numero_mesa as mesanombre'
          )
          ->first();
        $ordenpedidodetalles = DB::table('s_comida_ordenpedidodetalle')
          ->join('s_comida_ordenpedido as ordenpedido', 'ordenpedido.id', 's_comida_ordenpedidodetalle.idcomida_ordenpedido')
          ->join('s_producto as producto', 'producto.id', 's_comida_ordenpedidodetalle.idproducto')
          ->where([
            ['s_comida_ordenpedidodetalle.idcomida_ordenpedido', $id]
          ])
          ->select(
            's_comida_ordenpedidodetalle.*',
            'producto.codigo as productocodigo',
            'producto.nombre as productonombre'
          )
          ->orderBy('s_comida_ordenpedidodetalle.id', 'asc')
          ->get();
        return view('layouts/backoffice/tienda/sistema/comidaordenpedido/delete', [
          'tienda' => $tienda,
          'ordenpedido' => $ordenpedido,
          'ordenpedidodetalles' => $ordenpedidodetalles
        ]);
      }*/
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(), $idtienda);
        if ($request->view == 'enviar_pedido') {
        
            $productos = explode('/&/', $request->input('selectproductos'));
            for($i = 1;$i <  count($productos);$i++){
                $item = explode('/,/', $productos[$i]);
                if($item[1]<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad minímo es 1.'
                    ]);
                    break;
                }elseif($item[2]<0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Precio minímo es 0.00.'
                    ]);
                    break;
                }                     
            } 
        
            DB::table('s_comida_ordenpedido')->whereId($id)->update([     
                'fechaconfirmacion'=> Carbon::now(),
                'observacion'=> $request->observacion ?? '',
                'idestado'=> 2
            ]);
        
            DB::table('s_comida_ordenpedidodetalle')->where('idcomida_ordenpedido',$id)->delete();
            for($i = 1; $i < count($productos); $i++){
                $item = explode('/,/',$productos[$i]);
                DB::table('s_comida_ordenpedidodetalle')->insert([
                      'fecharegistro' => Carbon::now(),
                      'fechacomandado' => Carbon::now(),
                      'precio' => $item[2],
                      'cantidad' => $item[1],
                      'total' => $item[2]*$item[1],
                      'observacion' => $item[3],
                      'idproducto' => $item[0],
                      'idcomida_ordenpedido' => $id,
                      'idestado' => 2
                ]);
            }  
          
            $pedido = DB::table('s_comida_ordenpedido')
                  ->whereId($id)
                  ->first();

            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.',
              'codigopedido'   => str_pad($pedido->codigo, 6, "0", STR_PAD_LEFT),
            ]);
        }
      /*elseif ($request->view == 'anularordenpedido') {
          DB::table('s_comida_ordenpedido')->whereId($id)->update([     
              'fechaanulacion'=> Carbon::now(),
              'idestado'=> 3
          ]);
          return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha anulado correctamente.'
          ]);
      }*/
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      if ($request->view == 'eliminarmesa') {
          DB::table('s_comida_ordenpedidodetalle')->where('idcomida_ordenpedido',$id)->delete();
          DB::table('s_comida_ordenpedido')->whereId($id)->delete();
          return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha eliminado correctamente.'
          ]);
      }
      elseif ($request->view == 'registrarproductocantidadeliminar') {
        
          DB::table('s_comida_ordenpedidodetalle')->whereId($id)->delete();
        
          $total = DB::table('s_comida_ordenpedidodetalle')->where('idcomida_ordenpedido',$request->idpedido)->sum('total');

          DB::table('s_comida_ordenpedido')->whereId($request->idpedido)->update([
            'total' => $total
          ]);
          return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha eliminado correctamente.'
          ]);
      }
    }
}
