<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Comida;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;
use DateTime;

class ComidaOrdenPedidoController extends Controller
{
    public function index(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $ordenpedidos = DB::table('s_comida_ordenpedido as ordenpedido')
                ->where('ordenpedido.idtienda', $idtienda)
                ->where('ordenpedido.idestado', 1)
                ->select(
                    'ordenpedido.*',
                )
                ->orderBy('ordenpedido.id','desc')
                ->paginate(10);
      
        return view('layouts/backoffice/tienda/sistema/comida/comidaordenpedido/index', [
            'tienda' => $tienda,
            'ordenpedidos' => $ordenpedidos,
        ]);
    }

    public function create(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        if($request->view == 'mesa') {
            return view('layouts/backoffice/tienda/sistema/comida/comidaordenpedido/mesa', [
                'tienda' => $tienda,
            ]);
        }
        elseif($request->view == 'pedido') {
            return view('layouts/backoffice/tienda/sistema/comida/comidaordenpedido/pedido', [
                'tienda' => $tienda,
                'idpedido' => $request->idordenpedido,
                'numeromesa' => $request->numeromesa,
            ]);
        }
        elseif($request->view == 'categoria') {
            $s_categorias = DB::table('s_categoria')
                ->where('s_categoria.idtienda',$idtienda)
                ->where('s_categoria.s_idcategoria',0)
                ->orderBy('s_categoria.id','asc')
                ->get();
          
            return view('layouts/backoffice/tienda/sistema/comida/comidaordenpedido/categoria', [
                'tienda' => $tienda,
                'categorias' => $s_categorias,
                'numeromesa' => $request->numeromesa,
            ]);
        }
        elseif($request->view == 'producto') {
            
            $s_categoria = DB::table('s_categoria')
                ->where('s_categoria.idtienda',$idtienda)
                ->where('s_categoria.s_idcategoria',0)
                ->where('s_categoria.id',$request->idcategoria)
                ->first();
          
            $s_productos = DB::table('s_producto')
                ->where('s_producto.idtienda',$idtienda)
                ->where('s_producto.s_idcategoria1',$request->idcategoria)
                ->where('s_producto.s_idestado',1)
                ->orderBy('s_producto.id','asc')
                ->get();
          
            return view('layouts/backoffice/tienda/sistema/comida/comidaordenpedido/producto', [
                'tienda' => $tienda,
                'productos' => $s_productos,
                'categoria' => $s_categoria,
                'numeromesa' => $request->numeromesa,
            ]);
        }
        elseif($request->view == 'ordenpedido') {
           $pedido = DB::table('s_comida_ordenpedido')
                ->where('s_comida_ordenpedido.idtienda', $idtienda)
                ->where('s_comida_ordenpedido.idestado', 1)
                ->where('s_comida_ordenpedido.id', $request->idordenpedido)
                ->first();
            $pedidodetalle = DB::table('s_comida_ordenpedidodetalle')
                ->where('s_comida_ordenpedidodetalle.idestadoventa', 1)
                ->where('s_comida_ordenpedidodetalle.idcomida_ordenpedido', $request->idordenpedido)
                ->select(
                    's_comida_ordenpedidodetalle.*',
                )
                ->orderBy('s_comida_ordenpedidodetalle.id','asc')
                ->get();
            return view('layouts/backoffice/tienda/sistema/comida/comidaordenpedido/ordenpedido', [
                'tienda' => $tienda,
                'pedido' => $pedido,
                'pedidodetalle' => $pedidodetalle,
                'numeromesa' => $request->numeromesa,
            ]);
        }
    }

    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);

        if ($request->view == 'registrarordenpedido') {

            $rules = [
                //'productos' => 'required',
                'numeromesa' => 'required'
            ];
            $messages = [
                'numeromesa.required' => 'El "Número de Mesa" es Obligatorio.',
                //'productos.required' => 'Los "Productos" son Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $productos = explode('/&/', $request->input('productos'));
            for($i = 1;$i <  count($productos);$i++){
                $item = explode('/,/', $productos[$i]);
                if($item[1]<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad minímo es 1.'
                    ]);
                    break;
                }                      
            } 
            $idordenpedido = $request->idordenpedido;
            $cantidad_pedido = 0;
            if(count($productos)==1){
                DB::table('s_comida_ordenpedido')->whereId($idordenpedido)->delete();
                DB::table('s_comida_ordenpedidodetalle')->where('idcomida_ordenpedido',$idordenpedido)->delete();
                $idordenpedido = 0;
            }else{
                if($idordenpedido==0){
                    // obtener ultimo código
                    $s_comida_ordenpedido = DB::table('s_comida_ordenpedido')
                        ->where('s_comida_ordenpedido.idtienda',$idtienda)
                        ->orderBy('s_comida_ordenpedido.codigo','desc')
                        ->limit(1)
                        ->first();
                    $codigo = 1;
                    if($s_comida_ordenpedido!=''){
                        $codigo = $s_comida_ordenpedido->codigo+1;
                    }
                    // fin obtener ultimo código

                    $idordenpedido = DB::table('s_comida_ordenpedido')->insertGetId([
                        'fecharegistro' => Carbon::now(),
                        'codigo' => $codigo,
                        'numeromesa' => $request->numeromesa,
                        'total' => $request->total,
                        'observacion' => '',
                        'idresponsable' => Auth::user()->id,
                        'idestadoordenpedido' => 1,
                        'idtienda' => $idtienda,
                        'idestado' => 1
                    ]);
                }else{
                    DB::table('s_comida_ordenpedido')->whereId($idordenpedido)->update([
                        'total' => $request->total,
                        'observacion' => '',
                    ]);
                }


                DB::table('s_comida_ordenpedidodetalle')
                    ->where('s_comida_ordenpedidodetalle.idestadoventa',1)
                    ->where('s_comida_ordenpedidodetalle.idcomida_ordenpedido',$idordenpedido)
                    ->delete();
                for($i = 1;$i <  count($productos);$i++){
                    $item = explode('/,/', $productos[$i]);
                    if($item[1]<=0){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'La cantidad minímo es 1.'
                        ]);
                        break;
                    } 
                    if($item[6]=='on'){
                        $idestadoenviococina = 3;
                    }else{
                        $idestadoenviococina = 1;
                    }
                    if($item[5]=='on'){
                        $idestadoenviococina = 2;
                        $cantidad_pedido = 1;
                    }
                    $producto = DB::table('s_producto')->whereId($item[0])->first();
                    DB::table('s_comida_ordenpedidodetalle')->insert([
                        'fecharegistro' => Carbon::now(),
                        'producto' => $producto->nombre,
                        'cantidad' => $item[1],
                        'precio' => $item[2],
                        'total' => $item[3],
                        'observacion' => $item[4],
                        'idproducto' => $item[0],
                        'idcomida_ordenpedido' => $idordenpedido,
                        'idestadoventa' => 1,
                        'idestadoenviococina' => $idestadoenviococina, // 1=noenviado,2=enviado,3=cofirmado
                        'idtienda' => $idtienda,
                        'idestado' => 1
                    ]);
                }
            }
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.',
              'idordenpedido'   => $idordenpedido,
              'cantidad_pedido'   => $cantidad_pedido,
            ]);
        }
        /*elseif ($request->view == 'editarordenpedido') {

            $rules = [
                'productos' => 'required',
                'editar_numeromesa' => 'required'
            ];
            $messages = [
                'editar_numeromesa.required' => 'El "Número de Mesa" es Obligatorio.',
                'productos.required' => 'Los "Productos" son Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $productos = explode('/&/', $request->input('productos'));
            for($i = 1;$i <  count($productos);$i++){
                $item = explode('/,/', $productos[$i]);
                if($item[1]<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad minímo es 1.'
                    ]);
                    break;
                }                      
            }
          
            DB::table('s_comida_ordenpedido')->whereId($request->editar_idordenpedido)->update([
                  'total' => $request->editar_total,
            ]);
          
            DB::table('s_comida_ordenpedidodetalle')->where('idcomida_ordenpedido',$request->editar_idordenpedido)->delete();
            for($i = 1;$i <  count($productos);$i++){
                $item = explode('/,/', $productos[$i]);
                if($item[1]<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad minímo es 1.'
                    ]);
                    break;
                } 
                $producto = DB::table('s_producto')->whereId($item[0])->first();
                DB::table('s_comida_ordenpedidodetalle')->insert([
                    'fecharegistro' => Carbon::now(),
                    'producto' => $producto->nombre,
                    'cantidad' => $item[1],
                    'precio' => $item[2],
                    'total' => $item[3],
                    'observacion' => $request->observacion,
                    'idproducto' => $item[0],
                    'idcomida_ordenpedido' => $request->editar_idordenpedido,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
            }

            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha actualizado correctamente.',
              'idordenpedido'   => $request->editar_idordenpedido,
            ]);
        }*/
        /*elseif ($request->view == 'finalzarordenpedido') {

   
            DB::table('s_comida_ordenpedido')->whereId($request->finalizar_idordenpedido)->update([
                  'fechaconfirmacion' => Carbon::now(),
                  'idestadoordenpedido' => 2,
            ]);
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha finalizado correctamente.',
            ]);
        }*/
    }

    public function show(Request $request, $idtienda, $id)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        /*if ($id == 'show-ordenpedido') {
            $ordenpedidos_pendiente = DB::table('s_comida_ordenpedido as ordenpedido')
                ->where('ordenpedido.idtienda', $idtienda)
                ->where('ordenpedido.idestado', 1)
                ->where('ordenpedido.idestadoordenpedido', 1)
                ->select(
                    'ordenpedido.*',
                )
                ->orderBy('ordenpedido.fecharegistro','desc')
                ->get();

            $tabla = '<div class="table-responsive">
                   <table class="table" id="tabla-ordenpedidopendiente">
                      <thead class="thead-dark">
                        <tr>
                          <th style="text-align: center;">Código</th>
                          <th style="text-align: center;">Mesa</th>
                          <th style="text-align: center;">Total</th>
                          <th style="text-align: center;">Tiempo</th>
                          <th width="10px"></th> 
                        </tr>
                      </thead>
                      <tbody>';
            $fechaactual  = new DateTime(Carbon::now()->format("h:i:s"));
            foreach($ordenpedidos_pendiente as $value){
                $ordenpedidos_pendientedetalle = DB::table('s_comida_ordenpedidodetalle')
                            ->where('s_comida_ordenpedidodetalle.idcomida_ordenpedido', $value->id)
                            ->select(
                                's_comida_ordenpedidodetalle.*',
                            )
                            ->orderBy('s_comida_ordenpedidodetalle.id','asc')
                            ->get();
              
                
                $fecharegistro = new DateTime($value->fecharegistro);
                $intvl = $fechaactual->diff($fecharegistro);
                $hora = $intvl->h>0 ? $intvl->h.($intvl->h==1 ? " hora y ":" horas y "):'';
                $minuto = $intvl->i>0 ? $intvl->i.($intvl->i==1 ? " minuto ":" minutos "):'';
                $tiempo = $hora.$minuto;
                  
           
                $tabla = $tabla."<tr>
                          <td style='text-align: center;'>". str_pad($value->codigo, 6, '0', STR_PAD_LEFT) ."</td>
                          <td style='text-align: center;'>". str_pad($value->numeromesa, 2, '0', STR_PAD_LEFT) ."</td>
                          <td style='text-align: center;'>".$value->total."</td>
                          <td style='text-align: center;'>".$tiempo."</td>
                          <td>
                            <div class='header-user-menu menu-option' id='menu-opcion'>
                                <a href='javascript:;' class='btn btn-info'>Opción <i class='fa fa-angle-down'></i></a>
                                <ul>
                                    <li><a href='javascript:;' onclick='editar_ordenpedido(".$value->id.",\"Mesa ".str_pad($value->numeromesa, 2, '0', STR_PAD_LEFT)."\",".utf8_decode($ordenpedidos_pendientedetalle).")'><i class='fa fa-check'></i> Orden de Pedido</a></li>
                                    <li><a href='javascript:;' onclick='finalizarpedido(".$value->id.")'><i class='fa fa-receipt'></i> Ticket de Pedido</a></td> </li>
                                </ul>
                            </div>
                        </tr>";
                        
            }
            $tabla = $tabla.'</tbody>
                   </table>
                </div>';
            return [
                'ordenpedido' => $tabla
            ];
        }
        elseif ($id == 'show-cargarmesa') {
        
            
        }*/

      /*if ($id == 'show-indexordenpedido') {
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
                    $opcion = '<li><a href="'.url('backoffice/tienda/sistema/'.$idtienda.'/comida/comidaordenpedido/create?idpedido='.$value->id).'"><i class="fa fa-check"></i> Pedidos</a></li>';
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
                                     <td>'.$value->fecharegistro.' / Hace '.$tiempoxminuto.' minutos</td>
                                     <td>'.$value->responsablenombre.'</td>
                                     <td>'.$value->total.'</td>
                                     <td><a href="javascript:;" onclick="mostrar_producto('.$value->id.',\'Mesa '.str_pad($value->nombremesa, 2, "0", STR_PAD_LEFT).'\')" class="btn btn-warning big-btn" style="padding: 10px 15px;"><i class="fa fa-plus"></i> Pedido</a></td>
                                     <td><a href="javascript:;" id="modal-ticketmesa" onclick="ticket_producto('.$value->id.')" class="btn btn-primary big-btn" style="padding: 10px 15px;"><i class="fa fa-receipt"></i> Ticket</a></td>
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
      }*/
    }

    public function edit(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda = DB::table('tienda')
            ->leftJoin('ubigeo', 'ubigeo.id', 'tienda.idubigeo')
            ->select(
                'tienda.*',
                'ubigeo.nombre as ubigeonombre',
            )
            ->where('tienda.id',$idtienda)
            ->first();


      /*if ($request->view == 'comida_anularordenpedido') {
        $ordenpedido = DB::table('s_comida_ordenpedido')
          ->leftJoin('s_comida_mesa', 's_comida_mesa.id', 's_comida_ordenpedido.idmesa')
          ->where('s_comida_ordenpedido.id',$id)
          ->select(
            's_comida_ordenpedido.*',
            's_comida_mesa.numero_mesa as mesa_numero_mesa',
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
        return view('layouts/backoffice/tienda/sistema/comida/comidaordenpedido/anular', [
          'tienda' => $tienda,
          'ordenpedido' => $ordenpedido,
          'ordenpedidodetalles' => $ordenpedidodetalles
        ]);
      }*/
      if ($request->view == 'ticketpdf') {
        
          $ordenpedido = DB::table('s_comida_ordenpedido')
              ->leftJoin('users', 'users.id', 's_comida_ordenpedido.idresponsable')
              ->where('s_comida_ordenpedido.id',$id)
              ->select(
                's_comida_ordenpedido.*',
                'users.nombre as mesero_nombre',
                'users.apellidos as mesero_apellidos',
              )
              ->first();
        
          $ordenpedidodetalles = DB::table('s_comida_ordenpedidodetalle')
              ->join('s_producto as producto', 'producto.id', 's_comida_ordenpedidodetalle.idproducto')
              ->where([
                ['s_comida_ordenpedidodetalle.idcomida_ordenpedido', $id],
                ['s_comida_ordenpedidodetalle.idestadoenviococina', 2]
              ])
              ->select(
                's_comida_ordenpedidodetalle.*',
                'producto.codigo as productocodigo',
                'producto.nombre as productonombre'
              )
              ->orderBy('s_comida_ordenpedidodetalle.id', 'asc')
              ->get();


              $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/comida/comidaordenpedido/ticketpdf',[
                  'tienda' => $tienda,
                  'ordenpedido' => $ordenpedido,
                  'ordenpedidodetalles' => $ordenpedidodetalles
              ]);
              $ticket = 'Ticket_'.str_pad($ordenpedido->codigo, 8, "0", STR_PAD_LEFT);
              return $pdf->stream($ticket.'.pdf');
      }
    }

    public function update(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(), $idtienda);
      
        if($request->view == 'enviar_cocina'){
            $ordenpedidodetalles = DB::table('s_comida_ordenpedidodetalle')
              ->where([
                ['s_comida_ordenpedidodetalle.idcomida_ordenpedido', $id],
                ['s_comida_ordenpedidodetalle.idestadoenviococina', 2]
              ])
              ->select(
                's_comida_ordenpedidodetalle.*',
              )
              ->orderBy('s_comida_ordenpedidodetalle.id', 'asc')
              ->get();
            foreach($ordenpedidodetalles as $value){
                    DB::table('s_comida_ordenpedidodetalle')->whereId($value->id)->update([
                        'idestadoenviococina' => 3, // 1=noenviado,2=enviado,3=cofirmado
                    ]);
            }
        }
        /*if ($request->view == 'enviar_pedido') {
        
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
        }*/
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
