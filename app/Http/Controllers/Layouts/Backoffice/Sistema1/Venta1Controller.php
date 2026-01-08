<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use NumeroALetras;
use App\User;
use Hash;
use Auth;
use PDF;
use DB;

class VentaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        // aperturacaja
        $caja = caja($idtienda,Auth::user()->id);
        $idaperturacierre = 0;
        if($caja['resultado']=='ABIERTO'){
            $idaperturacierre = $caja['apertura']->id;
        }
      
        $where = [];
        //$where[] = ['s_venta.s_idaperturacierre',$idaperturacierre];
        $where[] = ['s_venta.codigo','LIKE','%'.$request->input('codigo').'%'];
        $where[] = ['s_tipocomprobante.nombre','LIKE','%'.$request->input('comprobante').'%'];
        $where[] = ['s_tipoentrega.nombre','LIKE','%'.$request->input('tipoentrega').'%'];
        $where[] = ['cliente.nombre','LIKE','%'.$request->input('cliente').'%'];
        $where[] = ['s_venta.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
        $where[] = ['s_venta.fechaconfirmacion','LIKE','%'.$request->input('fechavendida').'%'];
        $where[] = ['responsable.id',Auth::user()->id];
        
        $where1 = [];
        //$where1[] = ['s_venta.s_idaperturacierre',$idaperturacierre];
        $where1[] = ['s_venta.codigo','LIKE','%'.$request->input('codigo').'%'];
        $where1[] = ['s_tipocomprobante.nombre','LIKE','%'.$request->input('comprobante').'%'];
        $where1[] = ['cliente.apellidos','LIKE','%'.$request->input('cliente').'%'];
        $where1[] = ['s_venta.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
        $where1[] = ['s_venta.fechaconfirmacion','LIKE','%'.$request->input('fechavendida').'%'];
        $where1[] = ['responsable.id',Auth::user()->id];
      
        $where2 = [];
        //$where2[] = ['s_venta.s_idaperturacierre',$idaperturacierre];
        $where2[] = ['s_venta.codigo','LIKE','%'.$request->input('codigo').'%'];
        $where2[] = ['s_tipocomprobante.nombre','LIKE','%'.$request->input('comprobante').'%'];
        $where2[] = ['s_tipoentrega.nombre','LIKE','%'.$request->input('tipoentrega').'%'];
        $where2[] = ['cliente.nombre','LIKE','%'.$request->input('cliente').'%'];
        $where2[] = ['s_venta.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
        $where2[] = ['s_venta.fechaconfirmacion','LIKE','%'.$request->input('fechavendida').'%'];
        $where2[] = ['responsable.id',Auth::user()->id];
        
        $where3 = [];
        //$where3[] = ['s_venta.s_idaperturacierre',$idaperturacierre];
        $where3[] = ['s_venta.codigo','LIKE','%'.$request->input('codigo').'%'];
        $where3[] = ['s_tipocomprobante.nombre','LIKE','%'.$request->input('comprobante').'%'];
        $where3[] = ['cliente.apellidos','LIKE','%'.$request->input('cliente').'%'];
        $where3[] = ['s_venta.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
        $where3[] = ['s_venta.fechaconfirmacion','LIKE','%'.$request->input('fechavendida').'%'];
        $where3[] = ['responsable.id',Auth::user()->id];
      
        /*if($request->input('puntoconsumo')!=''){
            $where[] = ['s_configpuntoconsumo.nombre','LIKE','%'.$request->input('puntoconsumo').'%'];
        }*/
        
        $s_venta = DB::table('s_venta')
            ->join('s_tipocomprobante','s_tipocomprobante.id','s_venta.s_idcomprobante')
            ->join('s_tipoentrega','s_tipoentrega.id','s_venta.s_idtipoentrega')
            ->join('users as cliente','cliente.id','s_venta.s_idusuariocliente')
            ->join('users as responsableregistro','responsableregistro.id','s_venta.s_idusuarioresponsableregistro')
            ->join('users as responsable','responsable.id','s_venta.s_idusuarioresponsable')
            
            ->orWhere(function($query) use ($idtienda,$where,$where1) {
                $query->orWhere('s_venta.idtienda',$idtienda)
                      ->where($where)
                      ->orWhere('s_venta.idtienda',$idtienda)
                      ->where($where1);
            })
          
            ->orWhere(function($query) use ($idtienda,$where2,$where3) {
                $query->orWhere('s_venta.idtienda',$idtienda)
                      ->where($where2)
                      ->orWhere('s_venta.idtienda',$idtienda)
                      ->where($where3);
            })
          
            ->select(
                's_venta.*',
                's_tipocomprobante.nombre as nombreComprobante',
                's_tipoentrega.nombre as tipoentreganombre',
                DB::raw('IF(cliente.idtipopersona=1,
                CONCAT(cliente.apellidos,", ",cliente.nombre),
                CONCAT(cliente.apellidos)) as cliente'),
                'responsable.nombre as responsablenombre',
                'responsableregistro.nombre as responsableregistronombre'
            )
            ->orderBy('s_venta.codigo','desc')
            ->paginate(10);
      
        
      
        return view('layouts/backoffice/tienda/sistema/venta/index',[
            'tienda' => $tienda,
            's_venta' => $s_venta,
            'idapertura' => $idaperturacierre,
        ]);
    }

    public function create(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $agencia = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
        $tipoentregas = DB::table('s_tipoentrega')->get();
        $tipopersonas = DB::table('tipopersona')->get();
        $monedas = DB::table('s_moneda')->get();
        $tipopago = DB::table('s_tipopago')->get();
   
        if($request->input('view')=='ventarapida'){
            $cuentabancarias = DB::table('s_cuentabancaria')
              ->where('s_cuentabancaria.idtienda', $idtienda)
              ->where('s_cuentabancaria.idestado', 1)
              ->get();
            return view('layouts/backoffice/tienda/sistema/venta/create',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'tipopersonas' => $tipopersonas,
                'tipoentregas' => $tipoentregas,
                'monedas' => $monedas,
                'tipopago' => $tipopago,
                'cuentabancarias' => $cuentabancarias,
            ]);
        }
        elseif($request->input('view')=='ventapedido'){
            
            return view('layouts/backoffice/tienda/sistema/venta/ventapedido',[
                'tienda' => $tienda,
                'tipopersonas' => $tipopersonas,
            ]);
        }
        elseif($request->input('view')=='prueba'){
            
 /*
          
          $ch = curl_init();
          $headers  = [
              'Authorization: Bearer EAAMTqEDmuXABAObQ4D1FOXE6m0cDQUQ3ZBnYTeavXSNqvCmjaEsoPwUkX0ABfZCCjeWUFSTvDU0DKT0w8P8Abwf0hAnzLcDmDa3ZAbKGkDdQF4y2VKytRqjlZCnXpWhWoTAHDBb6qhheeXlYFHeiL3SlwjzpHvlUs2u7Enlag7SjTEjljeSqxqkO3BVypZCwpT6g0P1KnQYl6LUuEDLZAg',
              'Content-Type: application/json'
          ];
          $postData = [
              'messaging_product' => 'whatsapp',
                'to' => '51954827186',
                'type' => 'template',
                'template' => [ 
                    'name' => 'envio_comprobante',
                    'language' => [
                        'code' => 'es'
                    ],
                    'components' => [
                    [
                        'type' => 'header',
                        'parameters' => [
                            [
                            'type' => 'document',
                            'document' => [
                                'link' => 'https://kayllapi.com/public/backoffice/consumidor/kayllapi-consumidor.pdf',
                                'filename' =>'20600060679-03-B003-000001'
                            ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'body',
                        'parameters' => [
                        [
                            'type' => 'text',
                            'text' => '23/12/2022 05:10 PM'
                        ],
                        [
                            'type' => 'text',
                            'text' => 'B001-000023'
                        ],
                        [
                            'type' => 'text',
                            'text' => '300.00'
                        ]]
                    ]]
                ]
          ];
          curl_setopt($ch, CURLOPT_URL,"https://graph.facebook.com/v15.0/114798004807450/messages");
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));           
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
          $result     = curl_exec($ch);
          $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          
          return $result;
          */
        }
    }

    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'registrar') {
            $rules = [
                'idcliente'      => 'required',
                'direccion'      => 'required',
                'idubigeo'       => 'required',
                'idmoneda'       => 'required',
                'idagencia'      => 'required',
                'idcomprobante'  => 'required',
                'productos'      => 'required',
                //'idtipoentrega'  => 'required',
            ];
            /*if($request->input('idtipoentrega')==2){
                $rules = array_merge($rules,[
                    'costoenvio' => 'required',
                    'delivery_fecha' => 'required',
                    'delivery_hora' => 'required',
                    'delivery_pernonanombre' => 'required',
                    'delivery_numerocelular' => 'required',
                    'delivery_direccion' => 'required',
                    'mapa_ubicacion_lat' => 'required',
                    'mapa_ubicacion_lng' => 'required',
                ]);
            }*/
          
            if($request->input('nivelventa')==1){
                if($request->input('idestado')=='on'){
                    $rules = array_merge($rules,[
                        'montorecibido' => 'required',
                    ]);
                }
            }
          
            $nivelventa = 1;
            if(configuracion($idtienda,'sistema_nivelventa')['resultado']=='CORRECTO'){
                $nivelventa = configuracion($idtienda,'sistema_nivelventa')['valor'];
            }
          
            $messages = [];
          
            $monto_deposito = 0;
            if(isset($request->formapago_contado_seleccionar)){
                foreach(json_decode($request->formapago_contado_seleccionar) as $value){
                    if($request->input('formapago_idcuentabancaria'.$value->num)==''){
                        $rules = array_merge($rules,[
                            'formapago_idcuentabancaria'.$value->num => 'required',
                            'formapago_montodeposito'.$value->num => 'required',
                            'formapago_voucher'.$value->num => 'required',
                        ]);
                        $messages = array_merge($messages,[
                            'formapago_idcuentabancaria'.$value->num.'.required' => 'La "Cuenta Bancaria" es Obligatorio.',
                            'formapago_montodeposito'.$value->num.'.required' => 'El "Monto" es Obligatorio.',
                            'formapago_voucher'.$value->num.'.required' => 'El "Voucher" es Obligatorio.',
                        ]);
                    }
                    $monto_deposito = $monto_deposito+$request->input('formapago_montodeposito'.$value->num);
                }
            
                /*$formapago_selecionar = explode('/&/', $request->input('formapago_selecionar'));
                for($i = 1;$i <  count($formapago_selecionar);$i++){
                    $item = explode('/,/', $formapago_selecionar[$i]);
                    if($item[1]==''){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'La Ultima Fecha es Obligatorio.'
                        ]);
                        break;
                    } 
                    elseif($item[2]==''){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El Importe es Obligatorio.'
                        ]);
                        break;
                    }   
                    elseif($item[2]<=0){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El Importe minímo es 1.'
                        ]);
                        break;
                    }                     
                } */
            }
            
            $messages = array_merge($messages,[
              'idcliente.required' => 'El "Cliente" es Obligatorio.',
              'direccion.required' => 'La "Dirección" es Obligatorio.',
              'idubigeo.required' => 'El "Ubigeo" es Obligatorio.',
              'idmoneda.required' => 'La "Moneda" es Obligatorio.',
              'idagencia.required' => 'El "Empresa" es Obligatorio.',
              'idcomprobante.required' => 'El "Comprobante" es Obligatorio.',
              'productos.required' => 'Los "Productos" son Obligatorio.',
              'formapago_selecionar.required' => 'La "Forma de Pago" son Obligatorio.',
              'montorecibido.required' => 'El "Moto recibido" es obligatorio.',
              //'idtipoentrega.required' => 'El "Tipo de entrega" es Obligatorio.',
              //'costoenvio.required' => 'El "Costo de envio" es Obligatorio.',
              'ventacomida_numeromesa.required' => 'El "Número de Mesa" es Obligatorio.',
              'delivery_fecha.required' => 'La "Fecha" es Obligatorio.',
              'delivery_hora.required' => 'La "Hora" es Obligatorio.',
              'delivery_pernonanombre.required' => 'El "Nombre de peresa a entregar" es Obligatorio.',
              'delivery_numerocelular.required' => 'El "Número de celular de entrega" es Obligatorio.',
              'delivery_direccion.required' => 'El "Dirección de entrega" es Obligatorio.',
              'mapa_ubicacion_lat.required' => 'La "Ubicación de entrega" es Obligatorio.',
              'mapa_ubicacion_lng.required' => '',
            ]);
          
            $this->validate($request,$rules,$messages);
          
            $monto_efectivo = $request->monto_efectivo-$monto_deposito;
            if($monto_efectivo<0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Efectivo no puede ser negativo.'
                ]);
            }
          
          
            $idestado = 1;
            $idaperturacierre = 0;
            $montorecibido = $monto_efectivo;
            $vuelto = 0;
          
            if($nivelventa==1){
                if($request->input('idestado')=='on'){
                    if($request->input('montorecibido')!='undefined'){
                      $montorecibido = $request->input('montorecibido');
                      if($montorecibido<$request->input('total_redondeado')){
                          return response()->json([
                              'resultado' => 'ERROR',
                              'mensaje'   => 'El Monto recibido debe ser igual ó mayor al Total.'
                          ]);
                      }
                      $vuelto = $montorecibido-$request->input('total_redondeado');
                    }
                    $idestado = 3;
                    // aperturacaja
                    $caja = caja($idtienda, Auth::user()->id);
                    if($caja['resultado']!='ABIERTO'){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'La Caja debe estar Aperturada.'
                        ]);
                    }
                    $idaperturacierre = $caja['apertura']->id;
                    // fin aperturacaja
                }
            }elseif($nivelventa==2){
                if($request->input('idestado')=='on'){
                    $idestado = 2;
                }
            }
          
             /*$monto= 0;
             if($monto<$request->input('total_redondeado')){
                  return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'El Saldo debe ser igual ó mayor al Total.'
                   ]);
             }*/
          
            /*if($request->input('idtipoentrega')==2){
                if($request->input('costoenvio')<0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El costo de envio, minímo es 0.00.'
                    ]);
                }
            }*/
          
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
                if(configuracion($idtienda,'sistema_estadostock')['resultado']=='CORRECTO'){
                    if(configuracion($idtienda,'sistema_estadostock')['valor']==1){
                        $productosaldo = productosaldo($idtienda,$item[0]);
                        if($productosaldo['stock']<$item[1]){
                            return response()->json([
                                'resultado' => 'ERROR',
                                'mensaje'   => 'El Producto <b>"'.$item[3].'"</b> no cuenta con stock suficiente, ingrese otro producto!!.'
                            ]);
                            break;
                        }
                    }  
                }                        
            } 
          
          
            if($request->input('idcomprobante')==2 or $request->input('idcomprobante')==3){
                $agencia = DB::table('s_agencia')->whereId($request->input('idagencia'))->first();
                if($agencia->idestadofacturacion!=1){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Las Boletas y Facturas de la Empresa "'.$agencia->ruc.' - '.$agencia->nombrecomercial.'" estan deshabilitadas.'
                    ]);
                }
              
                if($request->input('idcomprobante')==3){
                    $cliente = DB::table('users')->whereId($request->input('idcliente'))->first();
                    if($cliente->idtipopersona==1){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'Para emitir el Comprobante, el cliente a Facturar debe ser con RUC.'
                        ]);
                    }
                }  
            }
          
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
          
            // obtener ultimo código
            $s_venta = DB::table('s_venta')
                ->where('s_venta.idtienda',$idtienda)
                ->orderBy('s_venta.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($s_venta!=''){
                $codigo = $s_venta->codigo+1;
            }
            // fin obtener ultimo código
          
            // Actualizar Usuario
            /*DB::table('users')->whereId($request->input('idcliente'))->update([
                'direccion' => $request->input('direccion'),
                'idubigeo' => $request->input('idubigeo')
            ]);*/
            // Fin Actualizar Usuario
          
           // Actualizar Usuario
            /*DB::table('s_usuariosaldo')->whereId($request->input('idusuariosaldo'))->update([
                'monto' => $request->input('montorestante')
            ]);*/
            // Fin Actualizar Usuario

            $totalventa = $request->input('subtotal');
            $totaldescuento = 0;
            if(configuracion($idtienda,'sistema_estadodescuento')['valor']==1){
                $totalventa = $request->input('totalventa');
                $totaldescuento = $request->input('totaldescuento');
            }
          
            $descuento_total = 0;
            $descuento_totalapagar = 0;
            if(configuracion($idtienda,'sistema_estadodescuentoventatotal')['valor']==1){
                $descuento_total = $request->input('descuento_total')!=''?$request->input('descuento_total'):0;
                $descuento_totalapagar = $request->input('descuento_totalapagar')!=''?$request->input('descuento_totalapagar'):0;
            }

            $idventa = DB::table('s_venta')->insertGetId([
               'codigo' => $codigo,
               'fecharegistro' => Carbon::now(),
               'fechapedido' => Carbon::now(),
               'fechaconfirmacion' => Carbon::now(),
               'totalventa' => $totalventa,
               'totaldescuento' => $totaldescuento,
               'descuento_total' => $descuento_total,
               'descuento_totalapagar' => $descuento_totalapagar,
               'subtotal' => $request->input('subtotal'),
               'envio' => 0,
               'total' => $request->input('total'),
               'totalredondeado' => $request->input('total_redondeado'),
               'monto_efectivo' => $monto_efectivo,
               'monto_deposito' => $monto_deposito,
               'montorecibido' => $montorecibido,
               'vuelto' => $vuelto,
               'nivelventa' => $nivelventa,
               'cliente_idubigeo' => $request->input('idubigeo'),
               'cliente_direccion' => $request->input('direccion'),
               'config_sistema_estadodescuentoventatotal' => configuracion($idtienda,'sistema_estadodescuentoventatotal')['valor'],
               's_idaperturacierre' => $idaperturacierre,
               's_idusuarioresponsableregistro' => Auth::user()->id,
               's_idusuarioresponsable' => Auth::user()->id,
               's_idusuariocliente' => $request->input('idcliente'),
               's_idagencia' =>  $request->input('idagencia'),
               's_idcomprobante' =>  $request->input('idcomprobante'),
               's_idtipoentrega' => 1,
               's_idmoneda' => $request->input('idmoneda'),
               's_idtipoventa' => 1, // 1 = sistema, 2 = tienda virtual
               's_idestado' => $idestado,
               'idtienda' => $idtienda,
            ]);
            
            $productos = explode('/&/', $request->input('productos'));
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
                  'por' => $producto->por,
                  'idunidadmedida' => $producto->idunidadmedida,
                  's_idproducto' => $producto->id,
                  's_idventa' => $idventa,
                  'idtienda' => $idtienda,
                  'idestado' => 1,
                ]);
              
                if($idestado==3){
                    // SALDO
                    productosaldo_actualizar(
                        $tienda->id,
                        $producto->id,
                        'VENTA',
                        $item[1],
                        $producto->por,
                        $producto->idunidadmedida,
                        $idventadetalle
                    );
                }  
            }   
          
            /*if($request->input('idtipoentrega')==2){
                DB::table('s_ventadelivery')->insertGetId([
                   'fecha' => $request->input('delivery_fecha'),
                   'hora' => $request->input('delivery_hora'),
                   'nombre' => $request->input('delivery_pernonanombre'),
                   'telefono' => $request->input('delivery_numerocelular'),
                   'direccion' => $request->input('delivery_direccion'),
                   'mapa_ubicacion_lat' => $request->input('mapa_ubicacion_lat'),
                   'mapa_ubicacion_lng' => $request->input('mapa_ubicacion_lng'),
                   's_idestadoenvio' => $request->input('idestadoenvio'),
                   's_idventa' => $idventa,
                   'idtienda' => $idtienda,
                   'idestado' => 1,
                ]);
            }*/
          
            if(configuracion($idtienda,'sistema_estadodescuento')['resultado']=='CORRECTO'){
                if(configuracion($idtienda,'sistema_estadodescuento')['valor']==1){
                    $productos = explode('/&/', $request->input('productos_descuento'));
                    for($i = 1; $i < count($productos); $i++){
                        $item = explode('/,/',$productos[$i]);
                        $idventadescuento = DB::table('s_ventadescuento')->insertGetId([
                            'fecharegistro' => Carbon::now(),
                            'total' => $item[0],
                            'montodescuento' => $item[1],
                            'totalpack' => $item[2],
                            's_idventa' => $idventa,
                            'idtienda' => $idtienda,
                            'idestado' => 1,
                        ]);
                        $item1 = explode(',',$item[3]);
                        for($x = 1; $x < count($item1); $x++){
                            DB::table('s_ventadescuentodetalle')->insert([
                                's_idproducto' => $item1[$x],
                                's_idventadescuento' => $idventadescuento,
                                'idtienda' => $idtienda,
                                'idestado' => 1,
                            ]);
                        }
                    }    
                }  
            }
          
            
            // REGISTRAR DEPOSITO
            if(isset($request->formapago_contado_seleccionar)){
                $formadepago = formadepago(
                    $idtienda,
                    $request,
                    's_idventa',
                    $idventa,
                );
            }
          
            //comida
            if($tienda->idcategoria==30 && $request->input('comida_idpedido')!=null){
                DB::table('s_comida_ordenpedidoventa')->insertGetId([
                    'fecharegistro' => Carbon::now(),
                    's_idcomida_ordenpedido' => $request->input('comida_idpedido'),
                    's_idventa' => $idventa,
                    'idtienda' => $idtienda,
                    'idestado' => 1,
                ]);
              
                DB::table('s_comida_ordenpedidodetalle')
                        ->where('s_comida_ordenpedidodetalle.idcomida_ordenpedido',$request->input('comida_idpedido'))
                        ->update([
                            'idestadoventa' => 2,
                        ]);
              
                $productosquitado = explode('/,/', $request->input('productoquitado'));
              
                for($i = 1; $i < count($productosquitado); $i++){
                    DB::table('s_comida_ordenpedidodetalle')
                        ->where('s_comida_ordenpedidodetalle.idcomida_ordenpedido',$request->input('comida_idpedido'))
                        ->where('s_comida_ordenpedidodetalle.id',$productosquitado[$i])
                        ->update([
                            'idestadoventa' => 1,
                        ]);
                }  
              
                $ordenpedidodetalle = DB::table('s_comida_ordenpedidodetalle')
                        ->where('s_comida_ordenpedidodetalle.idcomida_ordenpedido',$request->input('comida_idpedido'))
                        ->where('s_comida_ordenpedidodetalle.idestadoventa',1)
                        ->get();
              
                if(count($ordenpedidodetalle)==0){
                    DB::table('s_comida_ordenpedido')->whereId($request->input('comida_idpedido'))->update([
                        'fechaconfirmacion' => Carbon::now(),
                        'idestadoordenpedido' => 2,
                    ]);
                }
            }
          
            // Emitir Comprobante
            $idfacturacionboletafactura = 0;
            if($request->input('idcomprobante')==2 or $request->input('idcomprobante')==3){
                $result = facturar_venta(
                    $idtienda,
                    $request->input('idcomprobante'),
                    $request->input('idagencia'),
                    $idventa
                );
                $idfacturacionboletafactura = $result['idfacturacionboletafactura'];
                /*if($result['resultado']=='ERROR'){
                    return response()->json([
                        'resultado' => $result['resultado'],
                        'mensaje'   => $result['mensaje']
                    ]);
                }*/
            }
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.',
              'idventa'   => $idventa,
              'idestado'   => $idestado,
              'idcomprobante'   => $request->input('idcomprobante'),
              'idfacturacionboletafactura'   => $idfacturacionboletafactura
            ]);
        }
        if($request->input('view') == 'registrarventa') {
            $rules = [];
            $messages = [];
          
            $monto_deposito = 0;
            if(isset($request->formapago_contado_seleccionar)){
                foreach(json_decode($request->formapago_contado_seleccionar) as $value){
                    if($request->input('formapago_idcuentabancaria'.$value->num)==''){
                        $rules = array_merge($rules,[
                            'formapago_idcuentabancaria'.$value->num => 'required',
                            'formapago_montodeposito'.$value->num => 'required',
                            'formapago_voucher'.$value->num => 'required',
                        ]);
                        $messages = array_merge($messages,[
                            'formapago_idcuentabancaria'.$value->num.'.required' => 'La "Cuenta Bancaria" es Obligatorio.',
                            'formapago_montodeposito'.$value->num.'.required' => 'El "Monto" es Obligatorio.',
                            'formapago_voucher'.$value->num.'.required' => 'El "Voucher" es Obligatorio.',
                        ]);
                    }
                    $monto_deposito = $monto_deposito+$request->input('formapago_montodeposito'.$value->num);
                }
            
                /*$formapago_selecionar = explode('/&/', $request->input('formapago_selecionar'));
                for($i = 1;$i <  count($formapago_selecionar);$i++){
                    $item = explode('/,/', $formapago_selecionar[$i]);
                    if($item[1]==''){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'La Ultima Fecha es Obligatorio.'
                        ]);
                        break;
                    } 
                    elseif($item[2]==''){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El Importe es Obligatorio.'
                        ]);
                        break;
                    }   
                    elseif($item[2]<=0){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El Importe minímo es 1.'
                        ]);
                        break;
                    }                     
                } */
            }
          
            $this->validate($request,$rules,$messages);
          
            $monto_efectivo = $request->monto_efectivo-$monto_deposito;
            if($monto_efectivo<0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Efectivo no puede ser negativo.'
                ]);
            }
   
     
          
          
            // obtener ultimo código
            $s_venta = DB::table('s_venta')
                ->where('s_venta.idtienda',$idtienda)
                ->orderBy('s_venta.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($s_venta!=''){
                $codigo = $s_venta->codigo+1;
            }
            // fin obtener ultimo código
        

            $totalventa = $request->input('subtotal');
            $totaldescuento = 0;
            if(configuracion($idtienda,'sistema_estadodescuento')['valor']==1){
                $totalventa = $request->input('totalventa');
                $totaldescuento = $request->input('totaldescuento');
            }
          
            $descuento_total = 0;
            $descuento_totalapagar = 0;
            if(configuracion($idtienda,'sistema_estadodescuentoventatotal')['valor']==1){
                $descuento_total = $request->input('descuento_total')!=''?$request->input('descuento_total'):0;
                $descuento_totalapagar = $request->input('descuento_totalapagar')!=''?$request->input('descuento_totalapagar'):0;
            }

            $idventa = DB::table('s_venta')->insertGetId([
               'codigo' => $codigo,
               'fecharegistro' => Carbon::now(),
               'fechapedido' => Carbon::now(),
               'fechaconfirmacion' => Carbon::now(),
               'totalventa' => $totalventa,
               'totaldescuento' => $totaldescuento,
               'descuento_total' => $descuento_total,
               'descuento_totalapagar' => $descuento_totalapagar,
               'subtotal' => $request->input('subtotal'),
               'envio' => 0,
               'total' => $request->input('total'),
               'totalredondeado' => $request->input('total_redondeado'),
               'monto_efectivo' => $monto_efectivo,
               'monto_deposito' => $monto_deposito,
               'montorecibido' => $montorecibido,
               'vuelto' => $vuelto,
               'nivelventa' => $nivelventa,
               'cliente_idubigeo' => $request->input('idubigeo'),
               'cliente_direccion' => $request->input('direccion'),
               'config_sistema_estadodescuentoventatotal' => configuracion($idtienda,'sistema_estadodescuentoventatotal')['valor'],
               's_idaperturacierre' => $idaperturacierre,
               's_idusuarioresponsableregistro' => Auth::user()->id,
               's_idusuarioresponsable' => Auth::user()->id,
               's_idusuariocliente' => $request->input('idcliente'),
               's_idagencia' =>  $request->input('idagencia'),
               's_idcomprobante' =>  $request->input('idcomprobante'),
               's_idtipoentrega' => 1,
               's_idmoneda' => $request->input('idmoneda'),
               's_idtipoventa' => 1, // 1 = sistema, 2 = tienda virtual
               's_idestado' => $idestado,
               'idtienda' => $idtienda,
            ]);
            
            $productos = explode('/&/', $request->input('productos'));
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
                  'por' => $producto->por,
                  'idunidadmedida' => $producto->idunidadmedida,
                  's_idproducto' => $producto->id,
                  's_idventa' => $idventa,
                  'idtienda' => $idtienda,
                  'idestado' => 1,
                ]);
              
                if($idestado==3){
                    // SALDO
                    productosaldo_actualizar(
                        $tienda->id,
                        $producto->id,
                        'VENTA',
                        $item[1],
                        $producto->por,
                        $producto->idunidadmedida,
                        $idventadetalle
                    );
                }  
            }   
          
            /*if($request->input('idtipoentrega')==2){
                DB::table('s_ventadelivery')->insertGetId([
                   'fecha' => $request->input('delivery_fecha'),
                   'hora' => $request->input('delivery_hora'),
                   'nombre' => $request->input('delivery_pernonanombre'),
                   'telefono' => $request->input('delivery_numerocelular'),
                   'direccion' => $request->input('delivery_direccion'),
                   'mapa_ubicacion_lat' => $request->input('mapa_ubicacion_lat'),
                   'mapa_ubicacion_lng' => $request->input('mapa_ubicacion_lng'),
                   's_idestadoenvio' => $request->input('idestadoenvio'),
                   's_idventa' => $idventa,
                   'idtienda' => $idtienda,
                   'idestado' => 1,
                ]);
            }*/
          
            if(configuracion($idtienda,'sistema_estadodescuento')['resultado']=='CORRECTO'){
                if(configuracion($idtienda,'sistema_estadodescuento')['valor']==1){
                    $productos = explode('/&/', $request->input('productos_descuento'));
                    for($i = 1; $i < count($productos); $i++){
                        $item = explode('/,/',$productos[$i]);
                        $idventadescuento = DB::table('s_ventadescuento')->insertGetId([
                            'fecharegistro' => Carbon::now(),
                            'total' => $item[0],
                            'montodescuento' => $item[1],
                            'totalpack' => $item[2],
                            's_idventa' => $idventa,
                            'idtienda' => $idtienda,
                            'idestado' => 1,
                        ]);
                        $item1 = explode(',',$item[3]);
                        for($x = 1; $x < count($item1); $x++){
                            DB::table('s_ventadescuentodetalle')->insert([
                                's_idproducto' => $item1[$x],
                                's_idventadescuento' => $idventadescuento,
                                'idtienda' => $idtienda,
                                'idestado' => 1,
                            ]);
                        }
                    }    
                }  
            }
          
            
            // REGISTRAR DEPOSITO
            if(isset($request->formapago_contado_seleccionar)){
                $formadepago = formadepago(
                    $idtienda,
                    $request,
                    's_idventa',
                    $idventa,
                );
            }
          
            //comida
            if($tienda->idcategoria==30 && $request->input('comida_idpedido')!=null){
                DB::table('s_comida_ordenpedidoventa')->insertGetId([
                    'fecharegistro' => Carbon::now(),
                    's_idcomida_ordenpedido' => $request->input('comida_idpedido'),
                    's_idventa' => $idventa,
                    'idtienda' => $idtienda,
                    'idestado' => 1,
                ]);
              
                DB::table('s_comida_ordenpedidodetalle')
                        ->where('s_comida_ordenpedidodetalle.idcomida_ordenpedido',$request->input('comida_idpedido'))
                        ->update([
                            'idestadoventa' => 2,
                        ]);
              
                $productosquitado = explode('/,/', $request->input('productoquitado'));
              
                for($i = 1; $i < count($productosquitado); $i++){
                    DB::table('s_comida_ordenpedidodetalle')
                        ->where('s_comida_ordenpedidodetalle.idcomida_ordenpedido',$request->input('comida_idpedido'))
                        ->where('s_comida_ordenpedidodetalle.id',$productosquitado[$i])
                        ->update([
                            'idestadoventa' => 1,
                        ]);
                }  
              
                $ordenpedidodetalle = DB::table('s_comida_ordenpedidodetalle')
                        ->where('s_comida_ordenpedidodetalle.idcomida_ordenpedido',$request->input('comida_idpedido'))
                        ->where('s_comida_ordenpedidodetalle.idestadoventa',1)
                        ->get();
              
                if(count($ordenpedidodetalle)==0){
                    DB::table('s_comida_ordenpedido')->whereId($request->input('comida_idpedido'))->update([
                        'fechaconfirmacion' => Carbon::now(),
                        'idestadoordenpedido' => 2,
                    ]);
                }
            }
          
            // Emitir Comprobante
            $idfacturacionboletafactura = 0;
            if($request->input('idcomprobante')==2 or $request->input('idcomprobante')==3){
                $result = facturar_venta(
                    $idtienda,
                    $request->input('idcomprobante'),
                    $request->input('idagencia'),
                    $idventa
                );
                $idfacturacionboletafactura = $result['idfacturacionboletafactura'];
                /*if($result['resultado']=='ERROR'){
                    return response()->json([
                        'resultado' => $result['resultado'],
                        'mensaje'   => $result['mensaje']
                    ]);
                }*/
            }
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.',
              'idventa'   => $idventa,
              'idestado'   => $idestado,
              'idcomprobante'   => $request->input('idcomprobante'),
              'idfacturacionboletafactura'   => $idfacturacionboletafactura
            ]);
        }
        elseif($request->input('view') == 'registrarpedido') {

            $rules = [
                'idcliente' => 'required',
                'direccion' => 'required',
                'idubigeo' => 'required',
                'idmoneda' => 'required',
                'idagencia' => 'required',
                'idcomprobante' => 'required',
                'montorecibido' => 'required',
            ];
            
            $messages = [
              'idcliente.required' => 'El "Cliente" es obligatorio.',
              'direccion.required' => 'La "Direción" es obligatorio.',
              'idubigeo.required' => 'El "Ubigeo" es obligatorio.',
              'idmoneda.required' => 'La "Moneda" es obligatorio.',
              'idagencia.required' => 'La "Agencia" es obligatorio.',
              'idcomprobante.required' => 'El "Comprobante" es obligatorio.',
              'montorecibido.required' => 'El "Moto recibido" es obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            $nivelventa = null;
            if(configuracion($idtienda,'sistema_nivelventa')['resultado']=='CORRECTO'){
                $nivelventa = configuracion($idtienda,'sistema_nivelventa')['valor'];
            }
          
            // aperturacaja
            if($request->input('montorecibido')<$request->input('total')){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Monto recibido debe ser igual ó mayor al Total.'
                ]);
            }

            // aperturacaja
            $caja = caja($idtienda,Auth::user()->id);
            if($caja['resultado']!='ABIERTO'){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La Caja debe estar Aperturada.'
                ]);
            }
            $idaperturacierre = $caja['apertura']->id;
            // fin aperturacaja
          
            
            // obtener ultimo código
            $s_venta = DB::table('s_venta')
                ->where('s_venta.idtienda',$idtienda)
                ->orderBy('s_venta.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($s_venta!=''){
                $codigo = $s_venta->codigo+1;
            }
            // fin obtener ultimo código
          
            // Actualizar Usuario
            /*DB::table('users')->whereId($request->input('idcliente'))->update([
                'direccion' => $request->input('direccion'),
                'idubigeo' => $request->input('idubigeo')
            ]);*/
            // Fin Actualizar Usuario

            $idventa = DB::table('s_venta')->insertGetId([
               'codigo' => $codigo,
               'fecharegistro' => Carbon::now(),
               'fechapedido' => Carbon::now(),
               'fechaconfirmacion' => Carbon::now(),
               'totalventa' => $request->input('total'),
               'totaldescuento' => 0,
               'subtotal' => $request->input('total'),
               'envio' => 0,
               'total' => $request->input('total'),
               'totalredondeado' => $request->input('total_redondeado'),
               'montorecibido' => $request->input('montorecibido'),
               'vuelto' => $request->input('vuelto'),
               'nivelventa' => $nivelventa,
               'cliente_idubigeo' => $request->input('idubigeo'),
               'cliente_direccion' => $request->input('direccion'),
               's_idcomida_ordenpedido' => $request->idordenpedido,
               's_idaperturacierre' => $idaperturacierre,
               's_idusuarioresponsableregistro' => Auth::user()->id,
               's_idusuarioresponsable' => Auth::user()->id,
               's_idusuariocliente' => $request->input('idcliente'),
               's_idusuariosaldo' => 0,
               's_idagencia' =>  $request->input('idagencia'),
               's_idcomprobante' =>  $request->input('idcomprobante'),
               's_idtipoentrega' => 1,
               's_idmoneda' => $request->input('idmoneda'),
               's_idtipoventa' => 1, // 1 = sistema, 2 = tienda virtual
               's_idestado' => 3,
               'idtienda' => $idtienda,
            ]);
            
            $ordenpedidodetalles = DB::table('s_comida_ordenpedidodetalle')
              ->join('s_producto', 's_producto.id', 's_comida_ordenpedidodetalle.idproducto')
              ->where('s_comida_ordenpedidodetalle.idcomida_ordenpedido', $request->idordenpedido)
              ->select(
                's_comida_ordenpedidodetalle.*',
                's_producto.codigo as productocodigo',
                's_producto.nombre as productonombre',
                's_producto.por as por',
                's_producto.idunidadmedida as idunidadmedida'
              )
              ->orderBy('s_comida_ordenpedidodetalle.id', 'asc')
              ->get();
          
            foreach($ordenpedidodetalles as $value){
                $idventadetalle = DB::table('s_ventadetalle')->insertGetId([
                  'codigo' => $value->productocodigo,
                  'concepto' => $value->productonombre,
                  'cantidad' => $value->cantidad,
                  'preciounitario' => $value->precio,
                  'descuento' => 0,
                  'total' => $value->total,
                  'detalle' => '',
                  'por' => $value->por,
                  'idunidadmedida' => $value->idunidadmedida,
                  's_idproducto' => $value->idproducto,
                  's_idventa' => $idventa,
                  'idtienda' => $idtienda,
                  'idestado' => 1,
                ]);
              
                    productosaldo_actualizar(
                        $tienda->id,
                        $value->idproducto,
                        'VENTA',
                        $value->cantidad,
                        $value->por,
                        $value->idunidadmedida,
                        $idventadetalle
                    );
            }
          
            // Emitir Comprobante
            $idfacturacionboletafactura = 0;
            if($request->input('idcomprobante')==2 or $request->input('idcomprobante')==3){
                $result = facturar_venta(
                    $idtienda,
                    $request->input('idcomprobante'),
                    $request->input('idagencia'),
                    $idventa
                );
                $idfacturacionboletafactura = $result['idfacturacionboletafactura'];
            }
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha confirmado correctamente.',
              'idventa'   => $idventa,
              'idestado'   => 3,
              'idcomprobante'   => $request->input('idcomprobante'),
              'idfacturacionboletafactura'   => $idfacturacionboletafactura
            ]);
        }
        elseif($request->input('view') == 'registrarcliente') {
            if($request->input('cliente_idtipopersona')==1){
                $rules = [
                    'cliente_dni' => 'required|numeric|digits:8',
                    'cliente_nombre' => 'required',
                    'cliente_apellidos' => 'required',
                    'cliente_idubigeo' => 'required',
                    'cliente_direccion' => 'required'
                ];
                $identificacion = $request->input('cliente_dni');
                $nombre = $request->input('cliente_nombre');
                $apellidos = $request->input('cliente_apellidos');
            }else{
                $rules = [
                    'cliente_ruc' => 'required|numeric|digits:11',
                    'cliente_nombrecomercial' => 'required',
                    'cliente_razonsocial' => 'required',
                    'cliente_idubigeo' => 'required',
                    'cliente_direccion' => 'required'
                ];
                $identificacion = $request->input('cliente_ruc');
                $nombre = $request->input('cliente_nombrecomercial');
                $apellidos = $request->input('cliente_razonsocial');
            }
            $messages = [
                    'cliente_dni.required'   => 'El "DNI" es Obligatorio.',
                    'cliente_dni.numeric'   => 'El "DNI" debe ser Númerico.',
                    'cliente_dni.digits'   => 'El "DNI" debe ser de 8 Digitos.',
                    'cliente_nombre.required'   => 'El "Nombre" es Obligatorio.',
                    'cliente_apellidos.required'   => 'El "Apellidos" es Obligatorio.',
                    'cliente_ruc.required'   => 'El "RUC" es Obligatorio.',
                    'cliente_ruc.numeric'   => 'El "RUC" debe ser Númerico.',
                    'cliente_ruc.digits'   => 'El "RUC" debe ser de 11 Digitos.',
                    'cliente_nombrecomercial.required'   => 'El "Nombre Comercial" es Obligatorio.',
                    'cliente_razonsocial.required'   => 'El "Razón Social" es Obligatorio.',
                    'cliente_numerotelefono.required' => 'El "Número de Teléfono" es Obligatorio.',
                    'cliente_email.required'    => 'El "Correo Electrónico" es Obligatorio.',
                    'cliente_email.email'    => 'El "Correo Electrónico" es Incorrecto.',
                    'cliente_idubigeo.required'    => 'El "Ubicación (Ubigeo)" es Obligatorio.',
                    'cliente_direccion.required'    => 'La "Dirección" es Obligatorio.',
                    'cliente_idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $usuario = DB::table('users')
                ->where('identificacion',$identificacion)
                ->where('idtienda',$idtienda)
                ->first();
            if($usuario!=''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "DNI/RUC" ya existe, Ingrese Otro por favor.'
                ]);
            }
          
            $user = User::create([
                'nombre'         => $nombre,
                'apellidos'      => $apellidos!=null?$apellidos:'',
                'identificacion' => $identificacion!=null?$identificacion:'',
                'email'          => $request->input('cliente_email')!=null ? $request->input('cliente_email') : '',
                'email_verified_at' => Carbon::now(),
                'usuario'        => Carbon::now()->format("Ymdhisu"),
                'clave'          => '123',
                'password'       => Hash::make('123'),
                'numerotelefono' => $request->input('cliente_numerotelefono')!=null?$request->input('cliente_numerotelefono'):'',
                'direccion'      => $request->input('cliente_direccion'),
                'imagen'         => '',
                'iduserspadre'=> 0,
                'idubigeo'       => $request->input('cliente_idubigeo'),
                'idtipopersona'  => $request->input('cliente_idtipopersona'),
                'idtipousuario'  => 2,
                'idtienda'       => $idtienda,
                'idestado'       => 2
            ]);
          
            $ubigeocliente = DB::table('ubigeo')->whereId($request->input('cliente_idubigeo'))->first();
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.',
              'cliente' => $user,
              'ubigeocliente' => $ubigeocliente
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        
        if($id=='showstock'){
            return productosaldo($idtienda,$request->input('idproducto'));
        }elseif($id=='showdescuento'){
            return descuento_producto($idtienda,$request->input('idproducto'))['data'];
        }elseif($id=='showseleccionarproducto'){
            $producto = producto($idtienda,$request->input('idproducto'));
            if($producto['producto']==''){
                return [ 
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No existe el producto, ingrese otro código.',
                ];
            }
            return [ 
              'producto' => $producto['producto'],
              'stock' => $producto['stock']
            ];
        }elseif($id=='showstockproducto'){
            return producto($idtienda,$request->input('idproducto'));
        }elseif($id=='showseleccionarunidadproducto'){
            return unidad_productos($idtienda,$request->input('idproducto'));
        }
        elseif($id=='showseleccionarusuario'){
            $usuario = DB::table('users')
                ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
                ->where('users.idtienda',$idtienda)
                ->where('users.id',$request->input('idusuario'))
                ->select(
                    'users.*',
                    'ubigeo.nombre as ubigeonombre'
                )
                ->first();
            return [ 
              'usuario' => $usuario
            ];
        }
        elseif($id=='showseleccionarusuariosaldo'){
            $usuariosaldo = DB::table('s_usuariosaldo')
                ->join('users','users.id','s_usuariosaldo.idusuariosaldo')
                ->where('s_usuariosaldo.idusuariosaldo',$request->input('idusuariosaldo'))
                ->select(
                    's_usuariosaldo.*'
                )
                ->first();
            return [ 
              'usuariosaldo' => $usuariosaldo
            ];
        }
        elseif($id == 'showcarritocompra'){
          
            DB::table('s_carritocompra')->where('s_idusuariocliente',Auth::user()->id)->delete();
          
            if($request->input('productos')!=''){
                foreach($request->input('productos') as $value){
                    $idcarritocompra = DB::table('s_carritocompra')->insertGetId([
                        'cantidad' => $value['producto_cantidad'],
                        'preciounitario' => $value['producto_precioalpublico'],
                        'descuento' => '0.00',
                        's_idproducto' => $value['idproducto'],
                        's_idusuariocliente' => Auth::user()->id,
                        'idtienda' => $value['idtienda'],
                    ]);
                }
            }
          
            // listar carrito de compra
            $tiendas = DB::table('tienda')
                ->join('s_carritocompra','s_carritocompra.idtienda','=','tienda.id')
                ->where('s_idusuariocliente',Auth::user()->id)
                ->select(
                    'tienda.id as idtienda',
                    'tienda.nombre as tiendanombre'
                )
                ->orderBy('tienda.nombre','asc')
                ->distinct()
                ->get();
            $html = '';
            $carrito_total = 0;
            foreach($tiendas as $value){
                $s_carritocompras = DB::table('s_carritocompra')
                    ->join('s_producto','s_producto.id','=','s_carritocompra.s_idproducto')
                    ->where('s_carritocompra.s_idusuariocliente',Auth::user()->id)
                    ->where('s_carritocompra.idtienda',$value->idtienda)
                    ->select(
                          's_carritocompra.*',
                          's_producto.nombre as productonombre'
                    )
                    ->orderBy('s_producto.nombre','asc')
                    ->get();
                $html = $html.'<b>'.$value->tiendanombre.'</b><br>';
                $tienda_total = 0;
                $item = 1;
                foreach($s_carritocompras as $cvalue){
                    $tienda_subtotal = number_format(($cvalue->cantidad*$cvalue->preciounitario), 2, '.', '');
                    $html = $html.'<b>'.str_pad($item, 2, "0", STR_PAD_LEFT).'</b> - ('.$cvalue->cantidad.') '.$cvalue->productonombre.' - '.$tienda_subtotal.'<br>';
                    $tienda_total = $tienda_total+$tienda_subtotal;
                    $item++;
                }
                $html = $html.'<b>Total de compra: '.number_format($tienda_total, 2, '.', '').'</b><br>';
                //$html = $html.'<b>Costo de envio: 0.00</b><br>';
                $html = $html.'<b>Fecha de entrega: 2020-05-10 04:30:15</b><br>';
                $html = $html.'------------------------------------------------<br>';
                $carrito_total = $carrito_total+number_format($tienda_total, 2, '.', '');
            }
            $html = $html.'<b>TOTAL: '.number_format($carrito_total, 2, '.', '').'</b> <!--select id="delivery_idmetodopago1"><option value="1">Pagar con Visa o Mastercard</option><option value="2">Pagar cuando reciba el producto</option></select><button type="submit" id="pagacontraentrega1" style="float: none;background-color: #343a40;" class="price-link"> Solicitar Pedido</button--><br>';
            return $html;
        }
        elseif($id == 'showbuscaridentificacion'){
            return consultaDniRuc($request->input('buscar_identificacion'), $request->input('tipo_persona'));
        }
      
        // COMIDA
        elseif ($id == 'show-comida-seleccionarproducto') {
            $pedido = DB::table('s_comida_ordenpedido')
                    ->where('s_comida_ordenpedido.idtienda',$idtienda)
                    ->where('s_comida_ordenpedido.idestado',1)
                    ->where('s_comida_ordenpedido.id',$request->idordenpedido)
                    ->select(
                        's_comida_ordenpedido.*',
                    )
                    ->first();
            $mensaje = '';
            $pedidodetalles = [];
      
            if($pedido!=''){
                    $ordenpedidodetalles = DB::table('s_comida_ordenpedidodetalle')
                        ->join('s_producto', 's_producto.id', 's_comida_ordenpedidodetalle.idproducto')
                        ->join('tienda','tienda.id','s_producto.idtienda')
                        ->where('s_comida_ordenpedidodetalle.idestadoventa', 1)
                        ->where('s_comida_ordenpedidodetalle.idcomida_ordenpedido', $pedido->id)
                        ->select(
                            's_comida_ordenpedidodetalle.*',
                            's_producto.codigo as productocodigo',
                            's_producto.nombre as productonombre',
                            's_producto.s_idestadodetalle as idestadodetalle',
                            'tienda.nombre as tiendanombre',
                            'tienda.link as tiendalink',
                            DB::raw('(SELECT imagen FROM s_productogaleria WHERE s_idproducto=s_producto.id ORDER BY orden ASC LIMIT 1) as productoimagen'),
                        )
                        ->orderBy('s_comida_ordenpedidodetalle.id', 'asc')
                        ->get();
                    foreach($ordenpedidodetalles as $value){
                        $pedidodetalles[] = [
                            'idordenpedidodetalle' => $value->id,
                            'idproducto' => $value->idproducto,
                            'productocodigo' => $value->productocodigo,
                            'productoimagen' => $value->productoimagen,
                            'productonombre' => $value->productonombre,
                            'productopreciopublico' => $value->precio,
                            'idtienda' => $idtienda,
                            'tiendalink' => $value->tiendalink,
                            'tiendanombre' => $value->tiendanombre,
                            'cantidad' => $value->cantidad,
                            'idestadodetalle' => $value->idestadodetalle,
                        ];
                    }
                    $mensaje = 'Mesa '.str_pad($pedido->numeromesa, 2, "0", STR_PAD_LEFT);
            }else{
                $mensaje = 'Mesa '.str_pad($request->numeromesa, 2, "0", STR_PAD_LEFT).', no tiene ningún pedido.';
            }
          
            
            return [ 
              'pedidodetalles' => $pedidodetalles,
              'pedido' => $pedido,
              'mensaje' => $mensaje,
            ];
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $venta = DB::table('s_venta')
            ->join('users','users.id','s_venta.s_idusuariocliente')
            ->join('tienda','tienda.id','s_venta.idtienda')
            ->join('s_tipocomprobante','s_tipocomprobante.id','s_venta.s_idcomprobante')
            ->join('s_tipoentrega','s_tipoentrega.id','s_venta.s_idtipoentrega')
            ->join('s_moneda','s_moneda.id','s_venta.s_idmoneda')
            ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
            ->where('s_venta.id',$id)
            ->select(
                's_venta.*',
                'tienda.nombre as tiendanombre',
                'tienda.link as tiendalink',
                'users.idubigeo as idubigeo',
                's_tipocomprobante.nombre as comprobantenombre',
                's_tipoentrega.nombre as tipoentreganombre',
                'users.id as idcliente',
                DB::raw('IF(users.idtipopersona=1,
                CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre),
                CONCAT(users.identificacion," - ",users.apellidos)) as cliente'),
                'users.direccion as clientedireccion',
                'users.idtipopersona as idtipopersona',
                'ubigeo.id as idubigeo',
                'ubigeo.codigo as ubigeocodigo',
                'ubigeo.nombre as ubigeonombre',
                's_moneda.nombre as monedanombre'
            )
            ->first();   
      
        if($request->input('view') == 'editar') {
          
            $s_ventadetalles = DB::table('s_ventadetalle')
              ->join('s_producto','s_producto.id','s_ventadetalle.s_idproducto')
              ->join('s_venta','s_venta.id','s_ventadetalle.s_idventa')
              ->join('tienda','tienda.id','s_venta.idtienda')
              ->where('s_ventadetalle.s_idventa',$venta->id)
              ->select(
                's_ventadetalle.*',
                'tienda.id as idtienda',
                'tienda.link as tiendalink',
                'tienda.nombre as tiendanombre',
                's_producto.id as idproducto',
                's_producto.codigo as productocodigo',
                's_producto.nombre as productonombre',
                DB::raw('(SELECT imagen FROM s_productogaleria WHERE s_idproducto=s_producto.id ORDER BY orden ASC LIMIT 1) as productoimagen'),
                's_producto.precioalpublico as productoprecioalpublico',
                's_producto.s_idestadodetalle as idestadodetalle'
              )
              ->orderBy('s_ventadetalle.id','asc')
              ->get();
          
            $s_ventadelivery = DB::table('s_ventadelivery')->where('s_idventa',$venta->id)->first();
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $agencia = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
            $comprobante = DB::table('s_tipocomprobante')->get();
            $tipoentregas = DB::table('s_tipoentrega')->get();
            $tipopersonas = DB::table('tipopersona')->get();
            $monedas = DB::table('s_moneda')->get();

            return view('layouts/backoffice/tienda/sistema/venta/edit',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'comprobante' => $comprobante,
                'tipopersonas' => $tipopersonas,
                'tipoentregas' => $tipoentregas,
                'venta' => $venta,
                'ventadetalles' => $s_ventadetalles,
                'ventadelivery' => $s_ventadelivery,
                'monedas' => $monedas,
            ]);
          
        }
        elseif($request->input('view') == 'confirmar') {
          
            $s_ventadetalles = DB::table('s_ventadetalle')
              ->join('s_producto','s_producto.id','s_ventadetalle.s_idproducto')
              ->join('s_venta','s_venta.id','s_ventadetalle.s_idventa')
              ->join('tienda','tienda.id','s_venta.idtienda')
              ->where('s_ventadetalle.s_idventa',$venta->id)
              ->select(
                's_ventadetalle.*',
                'tienda.id as idtienda',
                'tienda.link as tiendalink',
                'tienda.nombre as tiendanombre',
                's_producto.id as idproducto',
                's_producto.codigo as productocodigo',
                's_producto.nombre as productonombre',
                's_producto.precioalpublico as productoprecioalpublico'
              )
              ->orderBy('s_ventadetalle.id','asc')
              ->get();
          
            $s_ventadelivery = DB::table('s_ventadelivery')->where('s_idventa',$venta->id)->first();
          
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $agencia = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
            $comprobante = DB::table('s_tipocomprobante')->get();
            $tipoentregas = DB::table('s_tipoentrega')->get();
            $tipopersonas = DB::table('tipopersona')->get();
            $monedas = DB::table('s_moneda')->get();

            return view('layouts/backoffice/tienda/sistema/venta/confirmar',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'comprobante' => $comprobante,
                'tipopersonas' => $tipopersonas,
                'tipoentregas' => $tipoentregas,
                'venta' => $venta,
                'ventadetalles' => $s_ventadetalles,
                'ventadelivery' => $s_ventadelivery,
                'monedas' => $monedas,
            ]);
          
        }
        elseif($request->input('view') == 'rechazar') {
          
            $s_ventadetalles = DB::table('s_ventadetalle')
              ->join('s_producto','s_producto.id','s_ventadetalle.s_idproducto')
              ->join('s_venta','s_venta.id','s_ventadetalle.s_idventa')
              ->join('tienda','tienda.id','s_venta.idtienda')
              ->where('s_ventadetalle.s_idventa',$venta->id)
              ->select(
                's_ventadetalle.*',
                'tienda.id as idtienda',
                'tienda.link as tiendalink',
                'tienda.nombre as tiendanombre',
                's_producto.id as idproducto',
                's_producto.codigo as productocodigo',
                's_producto.nombre as productonombre',
                's_producto.precioalpublico as productoprecioalpublico'
              )
              ->orderBy('s_ventadetalle.id','asc')
              ->get();
          
            $s_ventadelivery = DB::table('s_ventadelivery')->where('s_idventa',$venta->id)->first();
          
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $agencia = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
            $comprobante = DB::table('s_tipocomprobante')->get();
            $tipoentregas = DB::table('s_tipoentrega')->get();
            $tipopersonas = DB::table('tipopersona')->get();
            $monedas = DB::table('s_moneda')->get();

            return view('layouts/backoffice/tienda/sistema/venta/rechazar',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'comprobante' => $comprobante,
                'tipopersonas' => $tipopersonas,
                'tipoentregas' => $tipoentregas,
                'venta' => $venta,
                'ventadetalles' => $s_ventadetalles,
                'ventadelivery' => $s_ventadelivery,
                'monedas' => $monedas,
            ]);
          
        }
        elseif($request->input('view') == 'eliminar') {
          
            $s_ventadetalles = DB::table('s_ventadetalle')
              ->join('s_producto','s_producto.id','s_ventadetalle.s_idproducto')
              ->join('s_venta','s_venta.id','s_ventadetalle.s_idventa')
              ->join('tienda','tienda.id','s_venta.idtienda')
              ->where('s_ventadetalle.s_idventa',$venta->id)
              ->select(
                's_ventadetalle.*',
                'tienda.id as idtienda',
                'tienda.link as tiendalink',
                'tienda.nombre as tiendanombre',
                's_producto.id as idproducto',
                's_producto.codigo as productocodigo',
                's_producto.nombre as productonombre',
                's_producto.precioalpublico as productoprecioalpublico'
              )
              ->orderBy('s_ventadetalle.id','asc')
              ->get();
          
            $s_ventadelivery = DB::table('s_ventadelivery')->where('s_idventa',$venta->id)->first();
          
            $s_ventadescuentos = DB::table('s_ventadescuento')
                ->where('s_ventadescuento.s_idventa',$venta->id)
                ->orderBy('s_ventadescuento.id','asc')
                ->get();
          
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $agencia = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
            $comprobante = DB::table('s_tipocomprobante')->get();
            $tipoentregas = DB::table('s_tipoentrega')->get();
            $tipopersonas = DB::table('tipopersona')->get();
            $monedas = DB::table('s_moneda')->get();

            return view('layouts/backoffice/tienda/sistema/venta/eliminar',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'comprobante' => $comprobante,
                'tipopersonas' => $tipopersonas,
                'tipoentregas' => $tipoentregas,
                'venta' => $venta,
                'ventadetalles' => $s_ventadetalles,
                'ventadelivery' => $s_ventadelivery,
                'ventadescuentos' => $s_ventadescuentos,
                'monedas' => $monedas,
            ]);
          
        }
        elseif($request->input('view') == 'detalle') {
            $s_ventadetalles = DB::table('s_ventadetalle')
              ->join('s_producto','s_producto.id','s_ventadetalle.s_idproducto')
              ->join('s_venta','s_venta.id','s_ventadetalle.s_idventa')
              ->join('tienda','tienda.id','s_venta.idtienda')
              ->where('s_ventadetalle.s_idventa',$venta->id)
              ->select(
                's_ventadetalle.*',
                'tienda.id as idtienda',
                'tienda.link as tiendalink',
                'tienda.nombre as tiendanombre',
                's_producto.id as idproducto',
                's_producto.codigo as productocodigo',
                's_producto.nombre as productonombre'
              )
              ->orderBy('s_ventadetalle.id','asc')
              ->get();
          
            $s_ventadelivery = DB::table('s_ventadelivery')->where('s_idventa',$venta->id)->first();
          
            $s_ventadescuentos = DB::table('s_ventadescuento')
                ->where('s_ventadescuento.s_idventa',$venta->id)
                ->orderBy('s_ventadescuento.id','asc')
                ->get();

            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $agencia = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
            $comprobante = DB::table('s_tipocomprobante')->get();
            $tipoentregas = DB::table('s_tipoentrega')->get();
            $tipopersonas = DB::table('tipopersona')->get();
            $monedas = DB::table('s_moneda')->get();

            return view('layouts/backoffice/tienda/sistema/venta/detalle',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'comprobante' => $comprobante,
                'tipopersonas' => $tipopersonas,
                'tipoentregas' => $tipoentregas,
                'venta' => $venta,
                'ventadetalles' => $s_ventadetalles,
                'ventadelivery' => $s_ventadelivery,
                'ventadescuentos' => $s_ventadescuentos,
                'monedas' => $monedas,
            ]);
        }
        elseif($request->input('view')=='facturar'){
           $s_ventadetalles = DB::table('s_ventadetalle')
              ->join('s_producto','s_producto.id','s_ventadetalle.s_idproducto')
              ->join('s_venta','s_venta.id','s_ventadetalle.s_idventa')
              ->join('tienda','tienda.id','s_venta.idtienda')
              ->where('s_ventadetalle.s_idventa',$venta->id)
              ->select(
                's_ventadetalle.*',
                'tienda.id as idtienda',
                'tienda.link as tiendalink',
                'tienda.nombre as tiendanombre',
                's_producto.id as idproducto',
                's_producto.codigo as productocodigo',
                's_producto.nombre as productonombre'
              )
              ->orderBy('s_ventadetalle.id','asc')
              ->get();
            $s_ventadelivery = DB::table('s_ventadelivery')->where('s_idventa',$venta->id)->first();

            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $agencias = DB::table('s_agencia')
                ->where('idtienda',$idtienda)
                ->where('idestadofacturacion',1)
                ->get();
            if($venta->idtipopersona==1){
                $comprobante = DB::table('s_tipocomprobante')->where('id',2)->get();
            }elseif($venta->idtipopersona==2){
                $comprobante = DB::table('s_tipocomprobante')->where('id',2)->orWhere('id',3)->get();
            }
            
            $tipoentregas = DB::table('s_tipoentrega')->get();
            $tipopersonas = DB::table('tipopersona')->get();
            $monedas = DB::table('s_moneda')->get();

      //   dd($s_ventadetalles);
          
          return view('layouts/backoffice/tienda/sistema/venta/facturar',[
                'tienda' => $tienda,
                'agencias' => $agencias,
                'comprobante' => $comprobante,
                'tipopersonas' => $tipopersonas,
                'tipoentregas' => $tipoentregas,
                'venta' => $venta,
                'ventadetalles' => $s_ventadetalles,
                'ventadelivery' => $s_ventadelivery,
                'monedas' => $monedas,
            ]);
        }
        elseif($request->input('view') == 'anular') {
            $s_ventadetalles = DB::table('s_ventadetalle')
              ->join('s_producto','s_producto.id','s_ventadetalle.s_idproducto')
              ->join('s_venta','s_venta.id','s_ventadetalle.s_idventa')
              ->join('tienda','tienda.id','s_venta.idtienda')
              ->where('s_ventadetalle.s_idventa',$venta->id)
              ->select(
                's_ventadetalle.*',
                'tienda.id as idtienda',
                'tienda.link as tiendalink',
                'tienda.nombre as tiendanombre',
                's_producto.id as idproducto',
                's_producto.codigo as productocodigo',
                's_producto.nombre as productonombre'
              )
              ->orderBy('s_ventadetalle.id','asc')
              ->get();
          
            $s_ventadelivery = DB::table('s_ventadelivery')->where('s_idventa',$venta->id)->first();
          
            $s_ventadescuentos = DB::table('s_ventadescuento')
                ->where('s_ventadescuento.s_idventa',$venta->id)
                ->orderBy('s_ventadescuento.id','asc')
                ->get();
          
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $agencia = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
            $comprobante = DB::table('s_tipocomprobante')->get();
            $tipoentregas = DB::table('s_tipoentrega')->get();
            $tipopersonas = DB::table('tipopersona')->get();
            $monedas = DB::table('s_moneda')->get();

            return view('layouts/backoffice/tienda/sistema/venta/anular',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'comprobante' => $comprobante,
                'tipopersonas' => $tipopersonas,
                'tipoentregas' => $tipoentregas,
                'venta' => $venta,
                'ventadetalles' => $s_ventadetalles,
                'ventadelivery' => $s_ventadelivery,
                'ventadescuentos' => $s_ventadescuentos,
                'monedas' => $monedas,
            ]);
        }
        elseif($request->input('view') == 'ticket') {
            return view('layouts/backoffice/tienda/sistema/venta/ticket',[
                'tienda' => $tienda,
                'venta' => $venta
            ]);
        }
        elseif($request->input('view') == 'ticketpdf') {
            $agencia = DB::table('s_agencia')
                ->leftJoin('ubigeo','ubigeo.id','s_agencia.idubigeo')
                ->where('s_agencia.id',$venta->s_idagencia)
                ->select(
                  's_agencia.*',
                  'ubigeo.nombre as ubigeonombre'
                )
                ->first();
          
            $cliente = DB::table('users')
                ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
                ->where('users.id',$venta->s_idusuariocliente)
                ->select(
                    'users.*',
                    'ubigeo.nombre as ubigeonombre'
                )
                ->first();
            $vendedor = DB::table('users')
                ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
                ->where('users.id',$venta->s_idusuarioresponsable)
                ->select(
                    'users.*',
                    'ubigeo.nombre as ubigeonombre'
                )
                ->first();
            $s_ventadetalles = DB::table('s_ventadetalle')
                ->join('s_producto','s_producto.id','s_ventadetalle.s_idproducto')
                ->where('s_ventadetalle.s_idventa',$venta->id)
                ->select(
                  's_ventadetalle.*',
                  's_producto.codigo as productocodigo',
                  's_producto.nombre as productonombre'
                )
                ->orderBy('s_ventadetalle.id','asc')
                ->get();
          
            $s_ventadescuentos = DB::table('s_ventadescuento')
                ->where('s_ventadescuento.s_idventa',$venta->id)
                ->orderBy('s_ventadescuento.id','asc')
                ->get();
          
            $s_ventadelivery = DB::table('s_ventadelivery')->where('s_idventa',$venta->id)->first();
          
            //comida
            $comida_venta = null;
            if($tienda->idcategoria==30){
                $comida_venta = DB::table('s_comida_ordenpedidoventa')
                  ->join('s_comida_ordenpedido','s_comida_ordenpedido.id','s_comida_ordenpedidoventa.s_idcomida_ordenpedido')
                  ->join('users as mesero','mesero.id','s_comida_ordenpedido.idresponsable')
                  ->where('s_comida_ordenpedidoventa.s_idventa',$venta->id)
                  ->select(
                    'mesero.nombre as mesero_nombre',
                    's_comida_ordenpedido.numeromesa as numeromesa',
                  )
                  ->first();
            }

            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/venta/ticketpdf',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'cliente' => $cliente,
                'vendedor' => $vendedor,
                'venta' => $venta,
                's_ventadetalles' => $s_ventadetalles,
                's_ventadelivery' => $s_ventadelivery,
                'ventadescuentos' => $s_ventadescuentos,
                'comida_venta' => $comida_venta
            ]);
            $ticket = 'Ticket_'.str_pad($venta->codigo, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');

        }
        elseif($request->input('view') == 'buscarcodigo'){
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $agencia = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
            $monedas = DB::table('s_moneda')->get();
            $comprobante = DB::table('s_tipocomprobante')->get();
           
          
            $ordenpedido = DB::table('s_comida_ordenpedido')
                ->where('s_comida_ordenpedido.idtienda',$idtienda)
                ->where('s_comida_ordenpedido.codigo',$id)
                ->select(
                    's_comida_ordenpedido.*',
                )
                ->limit(1)
                ->first();
          
            
          
            $ordenpedidodetalles = '';
            if($ordenpedido!=''){
                $ordenpedidodetalles = DB::table('s_comida_ordenpedidodetalle')
                    ->join('s_producto', 's_producto.id', 's_comida_ordenpedidodetalle.idproducto')
                    ->where([
                      ['s_comida_ordenpedidodetalle.idcomida_ordenpedido', $ordenpedido->id]
                    ])
                    ->select(
                      's_comida_ordenpedidodetalle.*',
                      's_producto.codigo as productocodigo',
                      's_producto.nombre as productonombre'
                    )
                    ->orderBy('s_comida_ordenpedidodetalle.id', 'asc')
                    ->get();
            }
          
            

            return view('layouts/backoffice/tienda/sistema/venta/ventapedidodetalle',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'comprobante' => $comprobante,
                'ordenpedido' => $ordenpedido,
                'ordenpedidodetalles' => $ordenpedidodetalles,
                'monedas' => $monedas,
            ]);
        }
    }

    public function update(Request $request, $idtienda, $s_idventa)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if($request->input('view') == 'confirmar') {

            $rules = [
                'idagencia' => 'required',
                'idcomprobante' => 'required',
                'montorecibido' => 'required',
            ];
            
            $messages = [
              'idagencia.required' => 'La "Agencia" es obligatorio.',
              'idcomprobante.required' => 'El "Comprobante" es obligatorio.',
              'montorecibido.required' => 'El "Moto recibido" es obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            // aperturacaja
            if($request->input('montorecibido')<$request->input('total')){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Monto recibido debe ser igual ó mayor al Total.'
                ]);
            }

            // aperturacaja
            $caja = caja($idtienda,Auth::user()->id);
            if($caja['resultado']!='ABIERTO'){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La Caja debe estar Aperturada.'
                ]);
            }
            $idaperturacierre = $caja['apertura']->id;
            // fin aperturacaja
          

            DB::table('s_venta')->whereId($s_idventa)->update([
               'fechaconfirmacion' => Carbon::now(),
               'montorecibido' => $request->input('montorecibido'),
               'vuelto' => $request->input('vuelto')!=null ? $request->input('vuelto') : 0,
               's_idaperturacierre' => $idaperturacierre,
               's_idusuarioresponsable' => Auth::user()->id,
               's_idagencia' =>  $request->input('idagencia'),
               's_idcomprobante' =>  $request->input('idcomprobante'),
               's_idestado' => 3,
            ]);
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha confirmado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'rechazar') {

            DB::table('s_venta')->whereId($s_idventa)->update([
               'fecharechazo' => Carbon::now(),
               's_idestado' => 1,
            ]);
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha rechazado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'anular') {

            DB::table('s_venta')->whereId($s_idventa)->update([
               'fechaanulacion' => Carbon::now(),
               's_idestado' => 4,
            ]);
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha anulado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'facturarventa'){
          
          $facturacionboletafactura = DB::table('s_facturacionboletafactura')
              ->where('idventa',$s_idventa)
              ->limit(1)
              ->first();
          
          if($facturacionboletafactura!=''){
              // reenviar comprobante
              $result = facturador_facturaboleta($facturacionboletafactura->id);
          }else{
              $rules = [
                  'idagencia' => 'required',
                  'idtipocomprobante' => 'required',
              ];
              $messages = [
                  'idagencia.required' => 'La "Empresa" es Obligatorio.',
                  'idtipocomprobante.required' => 'El "Comprobante" es Obligatorio.',
              ];

              $this->validate($request,$rules,$messages);
              // registrar venta y enviar comprobante
              $result = facturar_venta(
                  $idtienda,
                  $request->input('idtipocomprobante'),
                  $request->input('idagencia'),
                  $s_idventa
              );
          }
           
          return response()->json([
              'resultado' => $result['resultado'],
              'mensaje'   => $result['mensaje']
          ]);
        }
        else if($request->input('view') == 'editar') {
            $rules = [
                'idcliente'      => 'required',
                'direccion'      => 'required',
                'idubigeo'       => 'required',
               // 'idmoneda'       => 'required',
                'idagencia'      => 'required',
                'idcomprobante'  => 'required',
                'productos'      => 'required',
                'idtipoentrega'  => 'required',
            ];
            if($request->input('idtipoentrega')==2){
                $rules = array_merge($rules,[
                    'costoenvio' => 'required',
                    'delivery_fecha' => 'required',
                    'delivery_hora' => 'required',
                    'delivery_pernonanombre' => 'required',
                    'delivery_numerocelular' => 'required',
                    'delivery_direccion' => 'required',
                    'mapa_ubicacion_lat' => 'required',
                    'mapa_ubicacion_lng' => 'required',
                ]);
            }
          
            if($request->input('nivelventa')==1){
                if($request->input('idestado')=='on'){
                    $rules = array_merge($rules,[
                        'montorecibido' => 'required',
                    ]);
                }
            }
          
            $nivelventa = null;
            if(configuracion($idtienda,'sistema_nivelventa')['resultado']=='CORRECTO'){
                $nivelventa = configuracion($idtienda,'sistema_nivelventa')['valor']; 
            }
            
            $messages = [
              'idcliente.required' => 'El "Cliente" es Obligatorio.',
              'direccion.required' => 'La "Dirección" es Obligatorio.',
              'idubigeo.required' => 'El "Ubigeo" es Obligatorio.',
              //'idmoneda.required' => 'La "Moneda" es Obligatorio.',
              'idagencia.required' => 'El "Empresa" es Obligatorio.',
              'idcomprobante.required' => 'El "Comprobante" es Obligatorio.',
              'productos.required' => 'Los "Productos" son Obligatorio.',
              'montorecibido.required' => 'El "Moto recibido" es obligatorio.',
              'idtipoentrega.required' => 'El "Tipo de entrega" es Obligatorio.',
              'costoenvio.required' => 'El "Costo de envio" es Obligatorio.',
              'ventacomida_numeromesa.required' => 'El "Número de Mesa" es Obligatorio.',
              'delivery_fecha.required' => 'La "Fecha" es Obligatorio.',
              'delivery_hora.required' => 'La "Hora" es Obligatorio.',
              'delivery_pernonanombre.required' => 'El "Nombre de peresa a entregar" es Obligatorio.',
              'delivery_numerocelular.required' => 'El "Número de celular de entrega" es Obligatorio.',
              'delivery_direccion.required' => 'El "Dirección de entrega" es Obligatorio.',
              'mapa_ubicacion_lat.required' => 'La "Ubicación de entrega" es Obligatorio.',
              'mapa_ubicacion_lng.required' => '',
            ];
          
            $this->validate($request,$rules,$messages);
          
            
            $idestado = 1;
            $idaperturacierre = 0;
            $montorecibido = 0;
          
            if($nivelventa==1){
                if($request->input('idestado')=='on'){
                    $montorecibido = $request->input('montorecibido');
                    $idestado = 3;
                    if($montorecibido<$request->input('total_redondeado')){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El Monto recibido debe ser igual ó mayor al Total.'
                        ]);
                    }
                    // aperturacaja
                    $caja = caja($idtienda,Auth::user()->id);
                    if($caja['resultado']!='ABIERTO'){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'La Caja debe estar Aperturada.'
                        ]);
                    }
                    $idaperturacierre = $caja['apertura']->id;
                    // fin aperturacaja
                }
            }elseif($nivelventa==2){
                if($request->input('idestado')=='on'){
                    $idestado = 2;
                }
            }
          
             /*$monto= 0;
             if($monto<$request->input('total_redondeado')){
                  return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'El Saldo debe ser igual ó mayor al Total.'
                   ]);
             }*/
          
            if($request->input('idtipoentrega')==2){
                if($request->input('costoenvio')<0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El costo de envio, minímo es 0.00.'
                    ]);
                }
            }
          
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
                if(configuracion($idtienda,'sistema_estadostock')['resultado']=='CORRECTO'){
                    if(configuracion($idtienda,'sistema_estadostock')['valor']==1){
                        $productosaldo = productosaldo($idtienda,$item[0]);
                        if($productosaldo['stock']<$item[1]){
                            return response()->json([
                                'resultado' => 'ERROR',
                                'mensaje'   => 'El Producto <b>"'.$item[3].'"</b> no cuenta con stock suficiente, ingrese otro producto!!.'
                            ]);
                            break;
                        }
                    }  
                }                        
            } 
          
            if($request->input('idcomprobante')==2 or $request->input('idcomprobante')==3){
                $agencia = DB::table('s_agencia')->whereId($request->input('idagencia'))->first();
                if($agencia->idestadofacturacion!=1){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Las Boletas y Facturas de la Empresa "'.$agencia->ruc.' - '.$agencia->nombrecomercial.'" estan deshabilitadas.'
                    ]);
                }
              
                if($request->input('idcomprobante')==3){
                    $cliente = DB::table('users')->whereId($request->input('idcliente'))->first();
                    if($cliente->idtipopersona==1){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'Para emitir el Comprobante, el cliente a Facturar debe ser con RUC.'
                        ]);
                    }
                }  
            }
            // obtener ultimo código
            $s_venta = DB::table('s_venta')
                ->where('s_venta.idtienda',$idtienda)
                ->orderBy('s_venta.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($s_venta!=''){
                $codigo = $s_venta->codigo+1;
            }
            // fin obtener ultimo código
          
            // Actualizar Usuario
            DB::table('users')->whereId($request->input('idcliente'))->update([
                'direccion' => $request->input('direccion'),
                'idubigeo' => $request->input('idubigeo')
            ]);
            // Fin Actualizar Usuario
          
           // Actualizar Usuario
            DB::table('s_usuariosaldo')->whereId($request->input('idusuariosaldo'))->update([
                'monto' => $request->input('montorestante')
            ]);
            // Fin Actualizar Usuario

            $totaldescuento = 0;
            $totalventa = $request->input('subtotal');
            if(configuracion($idtienda,'sistema_estadodescuento')['resultado']=='CORRECTO'){
                if(configuracion($idtienda,'sistema_estadodescuento')['valor']==1){
                    $totalventa = $request->input('totalventa');
                    $totaldescuento = $request->input('totaldescuento');
                }
            }

            DB::table('s_venta')->whereId($s_idventa)->update([
               'codigo' => $codigo,
               'fecharegistro' => Carbon::now(),
               'fechaconfirmacion' => Carbon::now(),
               'totalventa' => $totalventa,
               'totaldescuento' => $totaldescuento,
               'subtotal' => $request->input('subtotal'),
               'envio' => $request->input('costoenvio')!=null ? $request->input('costoenvio') : 0,
               'total' => $request->input('total'),
               'totalredondeado' => $request->input('total_redondeado'),
               'montorecibido' => $montorecibido,
               'vuelto' => $request->input('vuelto')!=null ? $request->input('vuelto') : 0,
               'nivelventa' => $nivelventa,
               'cliente_idubigeo' => $request->input('idubigeo'),
               'cliente_direccion' => $request->input('direccion'),
               's_idusuarioresponsableregistro' => Auth::user()->id,
               's_idaperturacierre' => $idaperturacierre,
               's_idusuariocliente' => $request->input('idcliente'),
               's_idusuariosaldo' => $request->input('idusuariosaldo'),
               's_idagencia' =>  $request->input('idagencia'),
               's_idcomprobante' =>  $request->input('idcomprobante'),
               's_idtipoentrega' => $request->input('idtipoentrega'),
               's_idestado' => $idestado,
               'idtienda' => $idtienda,
            ]);
          
            DB::table('s_ventadetalle')->where('s_idventa',$s_idventa)->delete();
          
            $productos = explode('/&/', $request->input('productos'));
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
                  'por' => $producto->por,
                  'idunidadmedida' => $producto->idunidadmedida,
                  's_idproducto' => $item[0],
                  's_idventa' => $s_idventa,
                  'idtienda' => $idtienda,
                  'idestado' => 1,
                ]);
              
                if($idestado==3){
                    // SALDO
                    productosaldo_actualizar(
                        $tienda->id,
                        $producto->id,
                        'VENTA',
                        $item[1],
                        $producto->por,
                        $producto->idunidadmedida,
                        $idventadetalle
                    );
                }  
            }   
          
            if($request->input('idtipoentrega')==2){
                DB::table('s_ventadelivery')->insertGetId([
                   'fecha' => $request->input('delivery_fecha'),
                   'hora' => $request->input('delivery_hora'),
                   'nombre' => $request->input('delivery_pernonanombre'),
                   'telefono' => $request->input('delivery_numerocelular'),
                   'direccion' => $request->input('delivery_direccion'),
                   'mapa_ubicacion_lat' => $request->input('mapa_ubicacion_lat'),
                   'mapa_ubicacion_lng' => $request->input('mapa_ubicacion_lng'),
                   's_idestadoenvio' => $request->input('idestadoenvio'),
                   's_idventa' => $s_idventa,
                   'idtienda' => $idtienda,
                   'idestado' => 1,
                ]);
            }
          
            if(configuracion($idtienda,'sistema_estadodescuento')['resultado']=='CORRECTO'){
                if(configuracion($idtienda,'sistema_estadodescuento')['valor']==1){
                    $productos = explode('/&/', $request->input('productos_descuento'));
                    for($i = 1; $i < count($productos); $i++){
                        $item = explode('/,/',$productos[$i]);
                        $idventadescuento = DB::table('s_ventadescuento')->insertGetId([
                            'fecharegistro' => Carbon::now(),
                            'total' => $item[0],
                            'montodescuento' => $item[1],
                            'totalpack' => $item[2],
                            's_idventa' => $s_idventa,
                            'idtienda' => $idtienda,
                            'idestado' => 1,
                        ]);
                        $item1 = explode(',',$item[3]);
                        for($x = 1; $x < count($item1); $x++){
                            DB::table('s_ventadescuentodetalle')->insert([
                                's_idproducto' => $item1[$x],
                                's_idventadescuento' => $idventadescuento,
                                'idtienda' => $idtienda,
                                'idestado' => 1,
                            ]);
                        }
                    }    
                }  
            }
          
            // Emitir Comprobante
            if($request->input('idcomprobante')==2 or $request->input('idcomprobante')==3){
                $result = facturar_venta(
                    $idtienda,
                    $request->input('idcomprobante'),
                    $request->input('idagencia'),
                    $s_idventa
                );
            
            }
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.',
              'idventa'   => $s_idventa,
              'idestado'   => $idestado
            ]);
        }
    }
  
    public function destroy(Request $request, $idtienda, $s_idventa)
    {
        if($request->input('view') == 'eliminar') {
            
            DB::table('s_ventadescuentodetalle')
                ->join('s_ventadescuento','s_ventadescuento.id','s_ventadescuentodetalle.s_idventadescuento')
                ->where('s_ventadescuento.s_idventa',$s_idventa)->delete();
            DB::table('s_ventadescuento')->where('s_idventa',$s_idventa)->delete();
          
            DB::table('s_ventadelivery')->where('s_idventa',$s_idventa)->delete();
            DB::table('s_ventadetalle')->where('s_idventa',$s_idventa)->delete();
            DB::table('s_venta')
                ->whereId($s_idventa)
                ->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
