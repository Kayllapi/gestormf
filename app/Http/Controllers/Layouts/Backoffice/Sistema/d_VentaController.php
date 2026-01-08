<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class VentaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/venta/tabla',[
                'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->view == 'registrar') {
            return view(sistema_view().'/venta/create',[
                'tienda' => $tienda,
            ]);
        }
        elseif($request->input('view') == 'formapago') {
          
            return view(sistema_view().'/venta/formapago',[
              'tienda' => $tienda,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar_venta') {
   
            /* =================================================  VALIDAR */
            $rules = [
                'idcliente'             => 'required',
                'selectproductos'       => 'required',
                'formapago_idformapago' => 'required',
            ];
            $messages = [];
          
            $cliente  = DB::table('users')->whereId($request->idcliente)->first();
            if($cliente->idtipopersona==2){
                $rules = array_merge($rules,[
                    'venta_direccion' => 'required',
                    // 'venta_idubigeo'  => 'required',
                ]);
            }
          
            /* Productos */
            $db_ventadetalle = [];
            $subtotal = 0;
            if($request->selectproductos!=''){
                $i = 1;
                foreach(json_decode($request->selectproductos) as $value){
                    $rules = array_merge($rules,[
                        'productCant'.$value->num  => 'required|numeric|gte:0',
                    ]);

                    if($request->idestadodetalle==1){
                        $rules = array_merge($rules,[
                            'productDetalle'.$value->num  => 'required',
                        ]);
                    }
                    $messages = array_merge($messages,[
                        'productCant'.$value->num.'.required' => 'La "Cantidad" es Obligatorio.',
                        'productCant'.$value->num.'.numeric'  => 'La "Cantidad", debe ser númerico.',
                        'productCant'.$value->num.'.gte'      => 'La "Cantidad", debe ser mayor a 0.',
                        'productDetalle'.$value->num.'.required'  => 'La "Cantidad" es Obligatorio.',
                    ]);

                    if(configuracion($idtienda,'sistema_estadostock')['valor']==1){
                        $stockproducto = sistema_productosaldo([
                            'idtienda'    => $idtienda,
                            'idsucursal'  => Auth::user()->idsucursal,
                            'idproducto'  => $value->idproducto,
                        ])['stock'];
                        if($stockproducto<$value->producto_cantidad){
                            return response()->json([
                                'resultado' => 'ERROR',
                                'mensaje'   => 'El Producto no cuenta con stock suficiente, ingrese otro producto!!.'
                            ]);
                            break;
                        }
                    }     
                    $totalproducto = number_format($value->producto_cantidad*$value->producto_precio, 2, '.', '');
                    $db_ventadetalle[] = [
                        'codigo'          => $value->producto_codigo,
                        'concepto'        => $value->producto_nombre,
                        'cantidad'        => $value->producto_cantidad,
                        'preciounitario'  => $value->producto_precio,
                        'total'           => $totalproducto,
                        'detalle'         => isset($value->producto_detalle)?$value->producto_detalle:'',
                        'por'             => $value->producto_por,
                        'idunidadmedida'  => $value->idunidadmedida,
                        'idproducto'      => $value->idproducto,
                    ];
                    $i++;  
                  
                    $subtotal = $subtotal+$totalproducto;           
                } 
            }
            /* Forma de pago */
            $rules = array_merge($rules,[
                'formapago_idformapago' => 'required',
            ]);
          
            $total_efectivo = 0;
            $total_deposito = 0;
            $db_formapago = [];
            if($request->formapago_idformapago==1){
                $i = 1;
                foreach(json_decode($request->formapagos) as $value){
                  
                    if($request->input('formapago_idtipopago'.$value->num)==''){
                        $rules = array_merge($rules,[
                            'formapago_idtipopago'.$value->num => 'required'
                        ]);
                        $messages = array_merge($messages,[
                            'formapago_idtipopago'.$value->num.'.required' => 'El "Tipo de Pago" es Obligatorio.',
                        ]);
                    }
                    $formapagomonto = 0;
                    if($request->input('formapago_idtipopago'.$value->num)==1){
                        if($request->input('formapago_efectivo_montoefectivo'.$value->num)==''){
                            $rules = array_merge($rules,[
                                'formapago_efectivo_montoefectivo'.$value->num => 'required'
                            ]);
                            $messages = array_merge($messages,[
                                'formapago_efectivo_montoefectivo'.$value->num.'.required' => 'El "Monto en Efectivo" es Obligatorio.',
                            ]);
                        }
                        $formapagomonto = $request->input('formapago_efectivo_montoefectivo'.$value->num);
                        $total_efectivo = $total_efectivo+$formapagomonto;
                    }
                    elseif($request->input('formapago_idtipopago'.$value->num)==2){
                        if($request->input('formapago_deposito_idcuentabancaria'.$value->num)==''){
                            $rules = array_merge($rules,[
                                'formapago_deposito_idcuentabancaria'.$value->num => 'required'
                            ]);
                            $messages = array_merge($messages,[
                                'formapago_deposito_idcuentabancaria'.$value->num.'.required' => 'La "Cuenta Bancaria" es Obligatorio.',
                            ]);
                        }
                        if($request->input('formapago_deposito_numerooperacion'.$value->num)==''){
                            $rules = array_merge($rules,[
                                'formapago_deposito_numerooperacion'.$value->num => 'required'
                            ]);
                            $messages = array_merge($messages,[
                                'formapago_deposito_numerooperacion'.$value->num.'.required' => 'El "Número de Operación" es Obligatorio.',
                            ]);
                        }
                        if($request->input('formapago_deposito_montodeposito'.$value->num)==''){
                            $rules = array_merge($rules,[
                                'formapago_deposito_montodeposito'.$value->num => 'required'
                            ]);
                            $messages = array_merge($messages,[
                                'formapago_deposito_montodeposito'.$value->num.'.required' => 'El "Monto en Depósito" es Obligatorio.',
                            ]);
                        }
                        $formapagomonto = $request->input('formapago_deposito_montodeposito'.$value->num);
                        $total_deposito = $total_deposito+$formapagomonto;
                    }   
                    $db_formapago[] = [
                        'orden'           => $i,
                        'idtipopago'      => $request->input('formapago_idtipopago'.$value->num),
                        'monto'           => $formapagomonto,
                        'cuentabancaria'  => $request->input('formapago_deposito_idcuentabancaria'.$value->num),
                        'numerooperacion' => $request->input('formapago_deposito_numerooperacion'.$value->num),
                    ];
                    $i++;
                }
                // formapago_totalpagado
                if($request->total != $request->formapago_totalpagado){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El "Total Pagado" debe ser igual al "Total a Pagar".',
                    ]);
                }
            }
            elseif($request->formapago_idformapago==2){
                $rules = array_merge($rules,[
                    'formapago_credito_fechainicio' => 'required',
                    'formapago_credito_ultimafecha' => 'required',
                ]);
            }
            /* Fin Forma de pago */
          
            $rules = array_merge($rules,[
                'idmoneda'        => 'required',
                'idagencia'       => 'required',
                'idcomprobante'   => 'required',
                'selectproductos' => 'required',
            ]);
          
            $messages = array_merge($messages,[
                'idcliente.required'        => 'El "Cliente" es Obligatorio.',
                'venta_direccion.required'  => 'La "Dirección" es Obligatorio.',
                // 'venta_idubigeo.required'   => 'El "Ubigeo" es Obligatorio.',
                'idmoneda.required'         => 'La "Moneda" es Obligatorio.',
                'idagencia.required'        => 'El "Empresa" es Obligatorio.',
                'idcomprobante.required'    => 'El "Comprobante" es Obligatorio.',
                'selectproductos.required'  => 'Los "Productos" son Obligatorio.',
              
                'formapago_idformapago.required'          => 'La "Forma de Pago" es Obligatorio.',
                'formapago_credito_fechainicio.required'  => 'La "Fecha inicio" es Obligatorio.',
                'formapago_credito_ultimafecha.required'  => 'La "Última fecha" es Obligatorio.',
            ]);
          
            $this->validate($request,$rules,$messages);
       
            /* =================================================  VALIDAR APERTURA DE CAJA */
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
          
          
            /* =================================================  VALIDAR COMPROBANTE */
            if($request->idcomprobante==2 or $request->idcomprobante==3){
                $agencia = DB::table('s_agencia')->whereId($request->idagencia)->first();
                if($agencia->idestadofacturacion!=1){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Emisión de Boletas y Facturas de la Empresa "'.$agencia->ruc.' - '.$agencia->nombrecomercial.'" estan deshabilitadas.'
                    ]);
                }
              
                $cliente = DB::table('users')->whereId($request->idcliente)->first();
          
                if($request->idcomprobante==2){
                  if($request->input('total')>=700){
                      if($cliente->identificacion==0){
                          return response()->json([
                              'resultado' => 'ERROR',
                              'mensaje'   => 'El DNI es Obligatorio, ya que el monto es mayor a S/. 700.00.'
                          ]);
                      }
                  }
                }
              
                if($request->idcomprobante==3){
                    if($cliente->idtipopersona==1){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'Para emitir una Factura, el cliente debe ser con RUC.'
                        ]);
                    }
                }  
            }

            /* =================================================  OBTENER ULTIMO CODIGO */
            $s_venta = DB::table('s_venta')
                ->where('s_venta.idtienda',$idtienda)
                ->orderBy('s_venta.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($s_venta!=''){
                $codigo = $s_venta->codigo+1;
            }
          
            /* =================================================  OBTENER DB */
            $s_usersresponsableregistro = DB::table('users')->whereId(Auth::user()->id)->first();
            $s_usersresponsable = DB::table('users')->whereId(Auth::user()->id)->first();
            $s_userscliente = DB::table('users')->where('users.id',$request->idcliente)->first();
            $s_agencia = DB::table('s_agencia')->whereId($request->idagencia)->first();
            $s_moneda = DB::table('s_moneda')->whereId($request->idmoneda)->first();
            $s_tipocomprobante = DB::table('s_tipocomprobante')->whereId($request->idcomprobante)->first();
            $s_formapago = DB::table('s_formapago')->whereId($request->formapago_idformapago)->first();
          
            /* =================================================  UNIDAD DE MEDIDA */
            // $idubigeocliente = $request->input('venta_idubigeo')!=null?$request->input('venta_idubigeo'):0;
            // $db_idubigeocliente = '';
            // if($idubigeocliente!=0){
            //     $s_ubigeo = DB::table('s_ubigeo')->whereId($idubigeocliente)->first();
            //     $db_idubigeocliente = $s_ubigeo->nombre;
            // }
            
            
            $idventa = DB::table('s_venta')->insertGetId([
                'codigo'                          => $codigo,
                'fecharegistro'                   => Carbon::now(),
                'fechaventa'                      => Carbon::now(),
                'subtotal'                        => $subtotal,
                'totaldescuento'                  => 0,
                'totalventa'                      => $subtotal,
                'totalredondeado'                 => $subtotal,
                'totalefectivo'                   => $total_efectivo,
                'totaldeposito'                   => $total_deposito,
                'totalrecibido'                   => 0,
                'vuelto'                          => 0,
              
                'db_ventadetalle'                 => json_encode($db_ventadetalle),
                'db_formapago'                    => json_encode($db_formapago),
              
                'db_idusersresponsableregistro'   => $s_usersresponsableregistro->nombrecompleto,
                'db_idusersresponsableventa'      => $s_usersresponsable->nombrecompleto,
                'db_idusersclienteidentificacion' => $s_userscliente->identificacion,
                'db_iduserscliente'               => $s_userscliente->nombrecompleto,
                'db_idusersclientedireccion'      => $s_userscliente->direccion,
                'db_idusersclienteubigeo'         => '',
                // 'db_idusersclienteubigeo'         => $db_idubigeocliente,
                'db_idagencia'                    => $s_agencia->razonsocial,
                'db_idtipocomprobante'            => $s_tipocomprobante->nombre,
                'db_idmoneda'                     => $s_moneda->simbolo,
                'db_idformapago'                  => $s_formapago->nombre,
              
                's_idaperturacierre'              => $idaperturacierre,
                's_idusersresponsableregistro'    => Auth::user()->id,
                's_idusersresponsableventa'       => Auth::user()->id,
                's_iduserscliente'                => $request->idcliente,
                's_idagencia'                     => $request->idagencia,
                's_idtipocomprobante'             => $request->idcomprobante,
                's_idmoneda'                      => $request->idmoneda,
                's_idformapago'                   => $request->formapago_idformapago,
                's_idestadosistema'               => 1,
                's_idestadotiendavirtual'         => 2,
                's_idestadoventa'                 => 2, // 1 = PENDIENTE, 2 = VENTA, 3=ANULADO
                'idtienda'                        => $idtienda,
                'idestado'                        => 1,
            ]);
            
            /* =================================================  ACTUALIZAR INVENTARIO */
            foreach(json_decode($request->selectproductos) as $value){
                /* =================================================  UNIDAD DE MEDIDA */
       
                $s_unidadmedida = DB::table('s_unidadmedida')->whereId($value->idunidadmedida)->first();
        
                sistema_inventario([
                    'idtienda'      => $idtienda,
                    'idsucursal'    => Auth::user()->idsucursal,
                    'idproducto'    => $value->idproducto,
                    'responsable'   => $s_usersresponsable->nombrecompleto,
                    'tipo'          => 'SALIDA',
                    'referencia'    => 'VENTA',
                    'concepto'      => $value->producto_nombre.' - '.$s_unidadmedida->nombre.' x '.$value->producto_por,
                    'cantidad'      => $value->producto_cantidad,
                    'por'           => $value->producto_por,
                    'precio'        => 0,
                    'total'         => 0,
                ]);
            }
            
          
            
            /*$productos = explode('/&/', $request->input('productos'));
            for($i = 1; $i < count($productos); $i++){
                $item = explode('/,/',$productos[$i]);
                $producto = DB::table('s_producto')->whereId($item[0])->first();
                $idventadetalle = DB::table('s_ventadetalle')->insertGetId([
                    'codigo' => $producto->codigo,
                    'concepto' => $producto->nombre,
                    'cantidad' => $item[1],
                    'preciounitario' => $item[2],
                    'descuento' => 0,
                    'total' => $item[1]*$item[2],
                    'detalle' => $item[4],
                    'por' => $item[7],
                    'idunidadmedida' => $item[6],
                    's_idproducto' => $item[0],
                    's_idventa' => $idventa,
                    'idtienda' => $idtienda,
                    'idestado' => 1,
                ]);
            }*/
          
            /* =================================================  EMITIR COMPROBANTE */
            $idfacturacionboletafactura = 0;
            if($request->idcomprobante==2 or $request->idcomprobante==3){
                $result = facturar_venta(
                    $idtienda,
                    $request->idcomprobante,
                    $request->idagencia,
                    $idventa
                );
                $idfacturacionboletafactura = $result['idfacturacionboletafactura'];
            }
            
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.',
                'idventa'   => $idventa,
                'idfacturacionboletafactura'   => $idfacturacionboletafactura,
            ]);
        }
        else if($request->input('view') == 'registrar_devolucion'){
            $rules = [
                'motivo' => 'required',           
            ];
          
            $messages = [
                'motivo.required' => 'El "Motivo" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
            $venta= DB::table('s_venta')->whereId($request->input('idventa'))->first();
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
            

            $s_ventadevolucion = DB::table('s_ventadevolucion')
                ->where('s_ventadevolucion.idtienda',$idtienda)
                ->orderBy('s_ventadevolucion.codigoimpresion','desc')
                ->limit(1)
                ->first();
            $codigoimpresion = 1;
            if($s_ventadevolucion!=''){
                $codigoimpresion = $s_ventadevolucion->codigoimpresion+1;
            }
            $productos = json_decode($request->input('selectproductos'));
            $idventadevolucion = DB::table('s_ventadevolucion')->insertGetId([
                'fecharegistro'          => Carbon::now(),
                'fechaconfirmacion'      => Carbon::now(),
                'codigo'                 => $venta->codigo,
                'codigoimpresion'        => $codigoimpresion,
                'total'                  => $request->input('total'),
                'totalredondeado'        => $request->input('total_redondeado'),
                'motivo'                 => $request->input('motivo'),
                'idventa'                => $venta->id,
                'idusuarioresponsable'   => Auth::user()->id,
                'idmoneda'               => 1,
                'idaperturacierre'       => $idaperturacierre,
                'idestadoventadevolucion'=> 2,
                'idtienda'               => $idtienda,
                'idestado'               => 2,
            ]);

            foreach ($productos as $itemproducto) {
                $cantidad       = $itemproducto->producto_cantidad;
                $preciounitario = number_format($itemproducto->producto_precio,2, '.', '');
                $precioventa    = number_format($preciounitario*$cantidad,2, '.', '');
                

                DB::table('s_ventadevoluciondetalle')->insert([
                    'codigo'            => $itemproducto->producto_codigo,
                    'concepto'          => $itemproducto->producto_nombre,
                    'cantidad'          => $itemproducto->producto_cantidad,
                    'preciounitario'    => $preciounitario,
                    'total'             => $precioventa,
                    'por'               => $itemproducto->producto_por,
                    'idunidadmedida'    => $itemproducto->idunidadmedida,
                    'idproducto'        => $itemproducto->idproducto,
                    'idventadetalle'    => 0,
                    'idventadevolucion' => $idventadevolucion,
                    'idtienda'          => $idtienda,
                    'idestado'          => 1,
                ]);
            
                
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
        if($id=='show_table'){
            $idsucursal = Auth::user()->idsucursal;
            $idusuario = Auth::user()->id;

            $s_ventas = DB::table('s_venta')
                ->where('s_venta.idtienda',$idtienda)
                ->where('s_venta.idsucursal',$idsucursal)
                ->where('s_venta.idestado',1)
                ->where('s_venta.s_idusersresponsableventa',$idusuario)
                ->where('s_venta.codigo','LIKE','%'.$request['columns'][0]['search']['value'].'%')
                ->where('s_venta.db_iduserscliente','LIKE','%'.$request['columns'][2]['search']['value'].'%')
                ->where('s_venta.fechaventa','LIKE','%'.$request['columns'][5]['search']['value'].'%')
                ->orderBy('s_venta.codigo','desc')
                ->paginate($request->length,'*',null,($request->start/$request->length)+1);
        
            $tabla = [];
            foreach($s_ventas as $value){
                $comprobante = DB::table('s_facturacionboletafactura')
                                ->where('idventa',$value->id)
                                ->limit(1)
                                ->first();
                $opcion = [];
                $style = '';
                $estadoventa = '';
                $serie_numero_comprobante = '';
                if($value->s_idestadoventa==1){
                    $opcion[] = [
                        'nombre'  => 'Confirmar',
                        'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=confirmar',
                        'icono'   => 'check'
                    ];
                    $opcion[] = [
                        'nombre'  => 'Eliminar',
                        'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=eliminar',
                        'icono'   => 'trash'
                    ];
                    $estadoventa = 'COTIZACIÓN';
                }elseif($value->s_idestadoventa==2){
                    $opcion[] = [
                        'nombre'  => 'Ticket de Venta',
                        // 'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=ticketventa',
                        'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=ticket',
                        'icono'   => 'edit'
                    ];
                    $opcion[] = [
                        'nombre'  => 'Devolución',
                        'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=devolucion',
                        'icono'   => 'ban'
                    ];
                    // $opcion[] = [
                    //     'nombre'  => 'Anular',
                    //     'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=anular',
                    //     'icono'   => 'ban'
                    // ];
                    if($comprobante){
                        $serie_numero_comprobante = $comprobante->venta_serie.'-'.$comprobante->venta_correlativo;
                        $opcion[] = [
                            'nombre'  => 'Comprobante',

                            'onclick' => '/'.$idtienda.'/facturacionboletafactura/'.$comprobante->id.'/edit?view=ticket',
                            'icono'   => 'note'
                        ];
                        $opcionComprobante = [
                            'nombre' => 'Comprobante',
                            'onclick' => '/'.$idtienda.'/facturacionboletafactura/'.$value->id.'/edit?view=ticket',
                            'icono' => 'receipt',
                        ];
                    }else{
                        $opcion[] = [
                            'nombre'  => 'Emitir Comprobante',
                            'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=facturar',
                            'icono'   => 'check'
                        ];
                    }
                    $estadoventa = 'VENDIDO';
                }elseif($value->s_idestadoventa==3){
                    $opcion[] = [
                        'nombre'  => 'Ticket de Venta',
                        'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=ticket',
                        'icono'   => 'edit'
                    ];
                    $estadoventa = 'ANULADO';
                }
            
                $tabla[] = [
                    'id'                => $value->id,
                    'style'             => $style,
                    'codigo'            => str_pad($value->codigo, 8, "0", STR_PAD_LEFT),
                    'comprobante'       => $value->db_idtipocomprobante,
                    'formapago'         => $value->db_idformapago,
                    'textformapago'     => '<span class="badge bg-success">'.$value->db_idformapago.'</span>',
                    'total_venta'       => $value->db_idmoneda.' '.$value->totalventa,
                    'total_redondeado'  => $value->db_idmoneda.' '.$value->totalredondeado,
                    'total_efectivo'    => $value->db_idmoneda.' '.$value->totalefectivo,
                    'total_deposito'    => $value->db_idmoneda.' '.$value->totaldeposito,
                    'cliente'           => $value->db_iduserscliente,
                    'fecharegistro'     => date_format(date_create($value->fecharegistro),"d/m/Y h:i A"),
                    'fechaventa'        => $value->fechaventa!=''?date_format(date_create($value->fechaventa),"d/m/Y h:i A"):'',
                    'idestadoventa'     => $value->s_idestadoventa,
                    'cpe'               => $serie_numero_comprobante,
                    'estadoventa'       => $estadoventa,
                    'opcion'            => $opcion
                ];
            }
            return response()->json([
                'start'           => $request->start,
                'draw'            => $request->draw,
                'recordsTotal'    => $request->length,
                'recordsFiltered' => $s_ventas->total(),
                'data'            => $tabla,
            ]);

        }
        else if($id == 'show_productostock') {
            $presentaciones = sistema_producto_presentaciones([
                'idtienda'    => $idtienda,
                'idsucursal'  => Auth::user()->idsucursal,
                'idproducto'  => $request->idproducto,
            ]);
          
            $moneda_soles   = DB::table('s_moneda')->whereId(1)->first();
            $moneda_dolares = DB::table('s_moneda')->whereId(2)->first();
          
            $tabla = '<table class="table table-striped">
              <thead class="table-dark">
                <tr>
                  <th>Producto</th>
                  <th width="100px">U. Medida</th>';
                  if(configuracion($idtienda,'sistema_moneda_usar')['valor']==2){
                      if(configuracion($idtienda,'sistema_estadopreciominimo')['valor']==1){
                          $tabla = $tabla.'<th width="90px">Precio Mínimo $</th>
                                    <th width="90px">Precio Público $</th>';
                      }else{
                          $tabla = $tabla.'<th width="90px">Precio Público $</th>';
                      }
                  }
                  elseif(configuracion($idtienda,'sistema_moneda_usar')['valor']==3){
                      if(configuracion($idtienda,'sistema_estadopreciominimo')['valor']==1){
                          $tabla = $tabla.'<th width="90px">Precio Mínimo S/.</th>
                                    <th width="90px">Precio Público S/.</th>';
                      }else{
                          $tabla = $tabla.'<th width="90px">Precio Público S/.</th>';
                      }
                      if(configuracion($idtienda,'sistema_estadopreciominimo')['valor']==1){
                          $tabla = $tabla.'<th width="90px">Precio Mínimo $</th>
                                    <th width="90px">Precio Público $</th>';
                      }else{
                          $tabla = $tabla.'<th width="90px">Precio Público $</th>';
                      }
                  }
                  else{
                      if(configuracion($idtienda,'sistema_estadopreciominimo')['valor']==1){
                          $tabla = $tabla.'<th width="90px">Precio Mínimo S/.</th>
                                    <th width="90px">Precio Público S/.</th>';
                      }else{
                          $tabla = $tabla.'<th width="90px">Precio Público S/.</th>';
                      }
                  }
                  $tabla = $tabla.'<th width="80px">Stock</th>
                  <th width="10px"></th>
                </tr>
              </thead>
              <tbody>';

                foreach($presentaciones as $value){
                  if($value['idproducto']==$request->idproducto){
                  $tabla = $tabla.'<tr>
                      <td>'.($value['productocodigo']!=''?$value['productocodigo'].' - ':'').$value['productonombre'].'</td>
                      <td style="white-space: nowrap;">'.$value['productounidadmedida'].'</td>';
                      if(configuracion($idtienda,'sistema_moneda_usar')['valor']==2){
                          if(configuracion($idtienda,'sistema_estadopreciominimo')['valor']==1){
                              $tabla = $tabla.'<td style="text-align: right;">'.$moneda_dolares->simbolo.' '.$value['productopreciominimo_dolares'].'</th>
                                        <td style="text-align: right;">'.$moneda_dolares->simbolo.' '.$value['productoprecio_dolares'].'</th>';
                          }else{
                              $tabla = $tabla.'<td style="text-align: right;">'.$moneda_dolares->simbolo.' '.$value['productoprecio_dolares'].'</th>';
                          }
                      }
                      elseif(configuracion($idtienda,'sistema_moneda_usar')['valor']==3){
                          if(configuracion($idtienda,'sistema_estadopreciominimo')['valor']==1){
                              $tabla = $tabla.'<td style="text-align: right;">'.$moneda_soles->simbolo.' '.$value['productopreciominimo'].'</th>
                                        <td style="text-align: right;">'.$moneda_soles->simbolo.' '.$value['productoprecio'].'</th>';
                          }else{
                              $tabla = $tabla.'<td style="text-align: right;">'.$moneda_soles->simbolo.' '.$value['productoprecio'].'</th>';
                          }
                          if(configuracion($idtienda,'sistema_estadopreciominimo')['valor']==1){
                              $tabla = $tabla.'<td style="text-align: right;">'.$moneda_dolares->simbolo.' '.$value['productopreciominimo_dolares'].'</th>
                                        <td style="text-align: right;">'.$moneda_dolares->simbolo.' '.$value['productoprecio_dolares'].'</th>';
                          }else{
                              $tabla = $tabla.'<td style="text-align: right;">'.$moneda_dolares->simbolo.' '.$value['productoprecio_dolares'].'</th>';
                          }
                      }
                      else{
                          if(configuracion($idtienda,'sistema_estadopreciominimo')['valor']==1){
                              $tabla = $tabla.'<td style="text-align: right;">'.$moneda_soles->simbolo.' '.$value['productopreciominimo'].'</th>
                                        <td style="text-align: right;">'.$moneda_soles->simbolo.' '.$value['productoprecio'].'</th>';
                          }else{
                              $tabla = $tabla.'<td style="text-align: right;">'.$moneda_soles->simbolo.' '.$value['productoprecio'].'</th>';
                          }
                      }
                      $tabla = $tabla.'<td style="text-align: center;">'.$value['stock'].' '.($value['stockadicional']>0?'('.$value['stockadicional'].')':'').'</td>
                  </tr>';
                  foreach($value['presentaciones'] as $valuepresentacion){
                  $tabla = $tabla.'<tr>
                      <td></td>
                      <td style="white-space: nowrap;">'.$valuepresentacion['productounidadmedida'].'</td>';
                      if(configuracion($idtienda,'sistema_moneda_usar')['valor']==2){
                          if(configuracion($idtienda,'sistema_estadopreciominimo')['valor']==1){
                            $tabla = $tabla.'<td style="text-align: right;">'.$moneda_dolares->simbolo.' '.$valuepresentacion['productopreciominimo_dolares'].'</th>
                                      <td style="text-align: right;">'.$moneda_dolares->simbolo.' '.$valuepresentacion['productoprecio_dolares'].'</th>';
                          }else{
                            $tabla = $tabla.'<td style="text-align: right;">'.$moneda_dolares->simbolo.' '.$valuepresentacion['productoprecio_dolares'].'</th>';
                          }
                      }
                      elseif(configuracion($idtienda,'sistema_moneda_usar')['valor']==3){
                          if(configuracion($idtienda,'sistema_estadopreciominimo')['valor']==1){
                            $tabla = $tabla.'<td style="text-align: right;">'.$moneda_soles->simbolo.' '.$valuepresentacion['productopreciominimo'].'</th>
                                      <td style="text-align: right;">'.$moneda_soles->simbolo.' '.$valuepresentacion['productoprecio'].'</th>';
                          }else{
                            $tabla = $tabla.'<td style="text-align: right;">'.$moneda_soles->simbolo.' '.$valuepresentacion['productoprecio'].'</th>';
                          }
                          if(configuracion($idtienda,'sistema_estadopreciominimo')['valor']==1){
                            $tabla = $tabla.'<td style="text-align: right;">'.$moneda_dolares->simbolo.' '.$valuepresentacion['productopreciominimo_dolares'].'</th>
                                      <td style="text-align: right;">'.$moneda_dolares->simbolo.' '.$valuepresentacion['productoprecio_dolares'].'</th>';
                          }else{
                            $tabla = $tabla.'<td style="text-align: right;">'.$moneda_dolares->simbolo.' '.$valuepresentacion['productoprecio_dolares'].'</th>';
                          }
                      }
                      else{
                          if(configuracion($idtienda,'sistema_estadopreciominimo')['valor']==1){
                            $tabla = $tabla.'<td style="text-align: right;">'.$moneda_soles->simbolo.' '.$valuepresentacion['productopreciominimo'].'</th>
                                      <td style="text-align: right;">'.$moneda_soles->simbolo.' '.$valuepresentacion['productoprecio'].'</th>';
                          }else{
                            $tabla = $tabla.'<td style="text-align: right;">'.$moneda_soles->simbolo.' '.$valuepresentacion['productoprecio'].'</th>';
                          }
                      }
                      $tabla = $tabla.'<td style="text-align: center;">'.$valuepresentacion['stock'].' '.($valuepresentacion['stockadicional']>0?'('.$valuepresentacion['stockadicional'].')':'').'</td>
                  </tr>';
                }
                  }
                }
            $tabla = $tabla.'</tbody>
          </table>';

            return $tabla;
        }
       
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
      
        $s_venta = DB::table('s_venta')
            ->join('s_moneda','s_moneda.id','s_venta.s_idmoneda')
            ->leftJoin('s_tipocomprobante','s_tipocomprobante.id','s_venta.s_idtipocomprobante')
            ->leftJoin('s_agencia','s_agencia.id','s_venta.s_idagencia')
            ->leftJoin('ubigeo as agenciaubigeo','agenciaubigeo.id','s_agencia.idubigeo')
            ->where('s_venta.idtienda',$idtienda)
            ->where('s_venta.id',$id)
            ->select(
                's_venta.*',
                's_tipocomprobante.nombre as comprobantenombre',
                's_venta.db_iduserscliente as cliente',
                's_venta.db_idusersclientedireccion as clientedireccion',
                's_venta.db_idusersclienteubigeo as ubigeonombre',
                's_moneda.nombre as monedanombre',
                's_moneda.simbolo as monedasimbolo',
                's_agencia.nombrecomercial as agencianombrecomercial',
                's_agencia.razonsocial as agenciarazonsocial',
                's_agencia.direccion as agenciadireccion',
                's_agencia.logo as agencialogo',
                'agenciaubigeo.nombre as agenciaubigeonombre',
            )
            ->first(); 

        if($request->input('view') == 'confirmar') {
            $s_ventadetalles = DB::table('s_ventadetalle')
                ->join('s_producto','s_producto.id','s_ventadetalle.s_idproducto')
                ->leftJoin('s_productostock', function($leftJoin) use ($idtienda){
                    $leftJoin->on('s_productostock.s_idproducto','s_producto.id')
                        ->where('s_productostock.idtienda',$idtienda)
                        ->where('s_productostock.idsucursal',Auth::user()->idsucursal);
                })
                ->where('s_ventadetalle.s_idventa',$s_venta->id)
                ->select(
                  's_ventadetalle.*',
                  's_producto.id as idproducto',
                  's_producto.codigo as productocodigo',
                  's_producto.nombre as productonombre',
                  DB::raw('(SELECT imagen FROM s_productogaleria WHERE s_idproducto=s_producto.id ORDER BY orden ASC LIMIT 1) as productoimagen'),
                  's_producto.precioalpublico as productoprecio',
                  's_producto.s_idestadodetalle as idestadodetalle',
                   's_productostock.cantidad as stock',
                )
                ->orderBy('s_ventadetalle.id','asc')
                ->get();
            return view(sistema_view().'/venta/confirmar',[
              'tienda'          => $tienda,
              's_venta'         => $s_venta,
              's_ventadetalles' => $s_ventadetalles,
            ]);
        }
        elseif($request->input('view') == 'anular') {
            return view(sistema_view().'/venta/anular',[
              'tienda' => $tienda,
              's_venta' => $s_venta,
            ]);
        }
        else if($request->input('view') == 'devolucion'){
            
            return view(sistema_view().'/venta/devolucion',[
                'tienda'    => $tienda,
                's_venta'   => $s_venta,
            ]);
        }
        elseif($request->input('view') == 'ticket') {

            return view(sistema_view().'/venta/pdfcontainer',[
                'tienda'  => $tienda,
                's_venta' => $s_venta
            ]);

        }
        elseif($request->input('view') == 'ticketventa') {
            
            $cliente = DB::table('users')
                ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
                ->where('users.id',$s_venta->s_iduserscliente)
                ->select(
                    'users.*',
                    'ubigeo.nombre as ubigeonombre'
                )
                ->first();
          
            $s_ventadescuentos = DB::table('s_ventadescuento')
                ->where('s_ventadescuento.s_idventa',$s_venta->id)
                ->orderBy('s_ventadescuento.id','asc')
                ->get();
          
            $s_ventadelivery = DB::table('s_ventadelivery')->where('s_idventa',$s_venta->id)->first();
            $ticket = new \stdClass();
            $ticket->tipo_fuente = configuracion($tienda->id,'sistema_tipoletra')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_tipoletra')['valor']:'Helvetica';
            $ticket->ancho_ticket = configuracion($tienda->id,'sistema_anchoticket')['resultado']=='CORRECTO'?(configuracion($tienda->id,'sistema_anchoticket')['valor']-1):'8'.'cm';

            $agencia = DB::table('s_agencia')->whereId($s_venta->s_idagencia)->first();
            $pdf = PDF::loadView(sistema_view().'/venta/ticket',[
                'ticket'    => $ticket,
                'tienda'    => $tienda,
                'cliente'   => $cliente,
                'venta'     => $s_venta,
                'agencia'   => $agencia,
                's_ventadelivery' => $s_ventadelivery,
                'ventadescuentos' => $s_ventadescuentos
            ]);
            $ticket = 'VENTA_'.str_pad($s_venta->codigo, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');

            
        }
        // elseif($request->input('view') == 'ticketpdf') {
          
        //     $cliente = DB::table('users')
        //         ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
        //         ->where('users.id',$s_venta->s_idusuariocliente)
        //         ->select(
        //             'users.*',
        //             'ubigeo.nombre as ubigeonombre'
        //         )
        //         ->first();

        //     $s_ventadetalles = DB::table('s_ventadetalle')
        //         ->join('s_producto','s_producto.id','s_ventadetalle.s_idproducto')
        //         ->where('s_ventadetalle.s_idventa',$s_venta->id)
        //         ->select(
        //           's_ventadetalle.*',
        //           's_producto.codigo as productocodigo',
        //           's_producto.nombre as productonombre'
        //         )
        //         ->orderBy('s_ventadetalle.id','asc')
        //         ->get();
          
        //     $s_ventadescuentos = DB::table('s_ventadescuento')
        //         ->where('s_ventadescuento.s_idventa',$s_venta->id)
        //         ->orderBy('s_ventadescuento.id','asc')
        //         ->get();
          
        //     $s_ventadelivery = DB::table('s_ventadelivery')->where('s_idventa',$s_venta->id)->first();

        //     $pdf = PDF::loadView(sistema_view().'/venta/ticketpdf',[
        //         'tienda' => $tienda,
        //         'cliente' => $cliente,
        //         'venta' => $s_venta,
        //         's_ventadetalles' => $s_ventadetalles,
        //         's_ventadelivery' => $s_ventadelivery,
        //         'ventadescuentos' => $s_ventadescuentos,
        //     ]);
        //     $ticket = 'VENTA_'.str_pad($s_venta->codigo, 8, "0", STR_PAD_LEFT);
        //     return $pdf->stream($ticket.'.pdf');
        // }
        elseif($request->input('view') == 'eliminar') {
            return view(sistema_view().'/venta/delete',[
              'tienda' => $tienda,
              's_venta' => $s_venta,
            ]);
        }
        elseif($request->input('view')=='facturar'){
           
            $s_ventadelivery = DB::table('s_ventadelivery')->where('s_idventa',$s_venta->id)->first();

            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $agencias = DB::table('s_agencia')
                ->where('idtienda',$idtienda)
                ->where('idestadofacturacion',1)
                ->get();
            // if($s_venta->idtipopersona==1){
            //     $comprobante = DB::table('s_tipocomprobante')->where('id',2)->get();
            // }elseif($s_venta->idtipopersona==2){
            //     $comprobante = DB::table('s_tipocomprobante')->where('id',2)->orWhere('id',3)->get();
            // }
            $comprobante = DB::table('s_tipocomprobante')->get();
            
            // $tipoentregas = DB::table('s_tipoentrega')->get();
            // $tipopersonas = DB::table('tipopersona')->get();
            $monedas = DB::table('s_moneda')->get();

      //   dd($s_ventadetalles);
          
          return view(sistema_view().'/venta/facturar',[
                'tienda' => $tienda,
                'agencias' => $agencias,
                'comprobante' => $comprobante,
                // 'tipopersonas' => $tipopersonas,
                // 'tipoentregas' => $tipoentregas,
                'venta' => $s_venta,
                'ventadelivery' => $s_ventadelivery,
                'monedas' => $monedas,
            ]);
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'confirmar') {
            $rules = [
                'idcliente'     => 'required',
            ];
          
            $cliente  = DB::table('users')->whereId($request->idcliente)->first();
            if($cliente->idtipopersona==2){
                $rules = array_merge($rules,[
                    'venta_direccion' => 'required',
                    'venta_idubigeo'  => 'required',
                ]);
            }
          
            $rules = array_merge($rules,[
                'idmoneda'      => 'required',
                'idagencia'     => 'required',
                'idcomprobante' => 'required',
                'productos'     => 'required',
                'montorecibido' => 'required',
            ]);
          
            $messages = [];
          
            $messages = array_merge($messages,[
              'idcliente.required' => 'El "Cliente" es Obligatorio.',
              'venta_direccion.required' => 'La "Dirección" es Obligatorio.',
              'venta_idubigeo.required' => 'El "Ubigeo" es Obligatorio.',
              'idmoneda.required' => 'La "Moneda" es Obligatorio.',
              'idagencia.required' => 'El "Empresa" es Obligatorio.',
              'idcomprobante.required' => 'El "Comprobante" es Obligatorio.',
              'productos.required' => 'Los "Productos" son Obligatorio.',
              'montorecibido.required' => 'El "Moto recibido" es obligatorio.',
            ]);
          
            $this->validate($request,$rules,$messages);
          
            if($request->input('montorecibido')<$request->input('total_redondeado')){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Monto recibido debe ser igual ó mayor al Total redondeado.'
                ]);
            }
       
            // aperturacaja
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
            // fin aperturacaja
            /* ----- FIN VALIDAR CAMPOS ----- */
          
            $productos = explode('/&/', $request->input('productos'));
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
                }elseif($item[5]==1){
                    if($item[4]==''){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'Es Obligatorio el Detalle.'
                        ]);
                        break;
                    }
                }
                // stock
                if(configuracion($idtienda,'sistema_estadostock')['valor']==1){
                    $productosaldo = sistema_productosaldo([
                        'idtienda'    => $idtienda,
                        'idsucursal'  => Auth::user()->idsucursal,
                        'idproducto'  => $item[0],
                    ]);
                    if($productosaldo['stock']<$item[1]){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El Producto <b>"'.$item[3].'"</b> no cuenta con stock suficiente, ingrese otro producto!!.'
                        ]);
                        break;
                    }
                }                     
            } 
          
          
            if($request->idcomprobante==2 or $request->idcomprobante==3){
                $agencia = DB::table('s_agencia')->whereId($request->idagencia)->first();
                if($agencia->idestadofacturacion!=1){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Emisión de Boletas y Facturas de la Empresa "'.$agencia->ruc.' - '.$agencia->nombrecomercial.'" estan deshabilitadas.'
                    ]);
                }
              
                if($request->idcomprobante==3){
                    $cliente = DB::table('users')->whereId($request->idcliente)->first();
                    if($cliente->idtipopersona==1){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'Para emitir una Factura, el cliente debe ser con RUC.'
                        ]);
                    }
                }  
            }

            DB::table('s_venta')->whereId($id)->update([
               'fechavendido' => Carbon::now(),
               'montorecibido' => $request->input('montorecibido'),
               'vuelto' => $request->input('montorecibido')-$request->input('total_redondeado'),
               'cliente_idubigeo' => $request->input('venta_idubigeo')!=null?$request->input('venta_idubigeo'):0,
               'cliente_direccion' => $request->input('venta_direccion')!=null?$request->input('venta_direccion'):'',
               's_idaperturacierre' => $idaperturacierre,
               's_idusuarioresponsable' => Auth::user()->id,
               's_idagencia' =>  $request->idagencia,
               's_idcomprobante' =>  $request->idcomprobante,
               's_idestado' => 3,
               'db_idestado' => 'VENDIDO',
            ]);
          
            // Emitir Comprobante
            $idfacturacionboletafactura = 0;
            if($request->idcomprobante==2 or $request->idcomprobante==3){
                $result = facturar_venta(
                    $idtienda,
                    $request->idcomprobante,
                    $request->idagencia,
                    $id
                );
                $idfacturacionboletafactura = $result['idfacturacionboletafactura'];
                /*if($result['resultado']=='ERROR'){
                    return response()->json([
                        'resultado' => $result['resultado'],
                        'mensaje'   => $result['mensaje']
                    ]);
                }*/
            }
            
            json_venta($idtienda,Auth::user()->idsucursal,Auth::user()->id);
            json_cotizacion($idtienda,Auth::user()->idsucursal,Auth::user()->id);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha confirmado correctamente.',
                'idventa'   => $id,
                'idfacturacionboletafactura'   => $idfacturacionboletafactura
            ]);
        }
        elseif($request->input('view') == 'anular') {
            
            // aperturacaja
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
            // fin aperturacaja
          
            $s_venta = DB::table('s_venta')
                ->where('s_venta.idtienda',$idtienda)
                ->where('s_venta.id',$id)
                ->first(); 
            
            if($s_venta->s_idaperturacierre!=$idaperturacierre){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No se puede anular este producto, ya que no pertenece a esta apertura!!.'
                ]);
            }

            DB::table('s_venta')->whereId($id)->update([
               'fechaanulado' => Carbon::now(),
               's_idestadoventa' => 4,
            //    'db_idestado' => 'ANULADO',
            ]);
          
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha anulado correctamente.',
            ]);
        }
        elseif($request->input('view') == 'facturarventa'){
         
          $facturacionboletafactura = DB::table('s_facturacionboletafactura')
              ->where('idventa',$id)
              ->limit(1)
              ->first();
          
        //   if($facturacionboletafactura!=''){
        //       // reenviar comprobante
        //       $result = facturador_facturaboleta($facturacionboletafactura->id);
        //   }else{
          
                $idfacturacionboletafactura = 0;
                
                if($request->input('idtipocomprobante')==2 or $request->input('idtipocomprobante')==3){
                    $rules = [
                        'idagencia' => 'required',
                        'idtipocomprobante' => 'required',
                        //'direccion' => 'required',
                        //'idubigeo' => 'required',
                    ];
                    $messages = [
                        'idagencia.required' => 'La "Empresa" es Obligatorio.',
                        'idtipocomprobante.required' => 'El "Comprobante" es Obligatorio.',
                        'direccion.required' => 'La "Dirección" es Obligatorio.',
                        'idubigeo.required' => 'El "Ubigeo" es Obligatorio.',
                    ];
                    $this->validate($request,$rules,$messages);
                    $cliente = DB::table('users')->whereId($request->idcliente)->first();
                    if( $cliente ){
                      
                        if( $cliente->idtipopersona == 1 &&  $request->input('idtipocomprobante')== 3 ){
                            return response()->json([
                                'resultado' => 'ERROR',
                                'mensaje'   => 'No puede emitir una FACTURA con DNI'
                            ]);
                        }
                        $nuevo_cliente = [
                          'id' => $request->idcliente,
                          'direccion' => $request->direccion,
                          'idubigeo' => $request->idubigeo
                        ];
//                     
                        $result = facturar_venta(
                            $idtienda,
                            $request->input('idtipocomprobante'),
                            $request->idagencia,
                            $id,
                            $cliente->id
                        );
                        $idfacturacionboletafactura = $result['idfacturacionboletafactura'];
                        
                    }
                    
  
                }
//           }
           
          return response()->json([
//               'resultado' => $result['resultado'],
//               'mensaje'   => $result['mensaje']
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha confirmado correctamente.',
            'idventa'   => $id,
            'idfacturacionboletafactura'   => $idfacturacionboletafactura
          ]);
        }
    }


    public function destroy(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
            
            $countproductos = DB::table('s_producto')
                ->where('s_producto.idtienda',$idtienda)
                ->where('s_producto.s_idventa',$id)
                ->count();
            if($countproductos>0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Hay Productos, no se puede eliminar.'
                ]);
            }
       
            $s_venta = DB::table('s_venta')->whereId($id)->first();
            uploadfile_eliminar($s_venta->imagen,'/public/backoffice/tienda/'.$idtienda.'/sistema/');
            DB::table('s_venta')
                ->where('idtienda',$idtienda)
                ->where('id',$id)
                ->delete();
  
            json_venta($idtienda,Auth::user()->idsucursal,Auth::user()->id); 
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
