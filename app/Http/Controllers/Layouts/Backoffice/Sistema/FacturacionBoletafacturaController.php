<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use Mail;
use PDF; 
use Carbon\Carbon;
use NumeroALetras;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Hash;
use Dompdf\Dompdf;
// use Illuminate\Http\Request;
// use App\Http\Controllers\Layouts\Backoffice\Sistema\FacturacionNotacreditoController;


class FacturacionBoletafacturaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/facturacionboletafactura/tabla',[
                'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if(Auth::user()->idsucursal == 0){
            $agencia = DB::table('tienda')->where('tienda.id',$idtienda)->select('tienda.sucursal_facturacionagencia as facturacionagencia')->first();
        }else{
            $agencia = DB::table('s_sucursal')->where('s_sucursal.id',Auth::user()->idsucursal)->select('s_sucursal.facturacionagencia')->first();
        }

        if($request->view == 'registrar') {
            return view(sistema_view().'/facturacionboletafactura/create',[
                'tienda' => $tienda,
                'agencia_facturacion' => $agencia
            ]);
        }
        else if($request->view == 'emision_notacredito'){
            
            $agencias = DB::table('s_agencia')->where('idtienda',$idtienda)->where('idestadofacturacion',1)->get();
            $facturacionboletafactura = DB::table('s_facturacionboletafactura')
                ->where([
                    ['s_facturacionboletafactura.idtienda', $idtienda],
                    ['s_facturacionboletafactura.id', $request->idcomprobante]
                ])
                ->select(
                    's_facturacionboletafactura.*',
                    DB::raw('DATE_FORMAT(s_facturacionboletafactura.venta_fechaemision, "%d/%m/%Y %h:%i:%s %p") as venta_fechaemision'),
                )
                ->first();

            if(is_null($facturacionboletafactura)){
                return [ 
                    'resultado' => 'ERROR',
                    'mensaje' => 'No existe la Boleta/Factura!.' 
                  ];
            }

            $facturacionboletafacturadetalle = DB::table('s_facturacionboletafacturadetalle')
                ->where('s_facturacionboletafacturadetalle.idfacturacionboletafactura',$facturacionboletafactura->id)
                ->orderBy('s_facturacionboletafacturadetalle.id','asc')
                ->get();  
            
            $html_detalle = '';
            $count_restante = 0;
            foreach($facturacionboletafacturadetalle as $value){

                $facturacionnotacreditodetalle = DB::table('s_facturacionnotacreditodetalle')
                    ->where('s_facturacionnotacreditodetalle.idfacturacionboletafacturadetalle',$value->id)
                    ->sum('s_facturacionnotacreditodetalle.cantidad');

                $restante = $value->cantidad - $facturacionnotacreditodetalle;

                if($restante>0){
                    $html_detalle = $html_detalle.'<tr id="'.$value->id.'" idfacturacionboletafacturadetalle="'.$value->id.'" idproducto="'.$value->idproducto.'" 
                            style="background-color: #008cea;color: #fff;height: 40px;">
                            <!-- <td>'.$value->codigoproducto.'</td> -->
                            <td>'.$value->descripcion.'</td>
                            <td><input class="form-control" type="text" value="'.$restante.'" disabled></td>                                
                            <td><input class="form-control" type="number" value="'.$value->montopreciounitario.'" step="0.01" min="0" disabled></td> 
                            <td><input class="form-control" id="productCant'.$value->id.'" type="number" value="'.$restante.'" onkeyup="calcularmonto()"></td>                                
                            <td><input class="form-control" id="productUnidad'.$value->id.'" type="number" value="'.$value->montopreciounitario.'" step="0.01" min="0" disabled></td>                        
                            <td><input class="form-control" id="productTotal'.$value->id.'" type="text" value="'.number_format($restante*$value->montopreciounitario,2, '.', '').'" step="0.01" min="0" disabled></td>                                      
                            <td class="with-btn" width="10px"><a id="'.$value->id.'" href="javascript:;" onclick="eliminarproducto('.$value->id.')" class="btn btn-danger big-btn"><i class="fas fa-trash-alt"></i></a></td>
                            </tr>';
                    $count_restante++;
                }

            }
            if($count_restante==0){
                return [ 
                    'resultado' => 'ERROR',
                    'mensaje' => 'Los productos de la Boleta/Factura ya fueron emitos por otra NOTA DE CREDITO.' 
                ];
            }

            return view(sistema_view().'/facturacionboletafactura/notacredito',[
                'tienda' => $tienda,
                'agencias' => $agencias,
                'facturacionboletafactura'  => $facturacionboletafactura,
                'facturacionboletafacturadetalle' => $html_detalle,
            ]);
            
            
            
        }
        else if($request->view == 'emision_notadebito'){
            $agencias = DB::table('s_agencia')->where('idtienda',$idtienda)->where('idestadofacturacion',1)->get();
            $facturacionboletafactura = DB::table('s_facturacionboletafactura')
                ->where([
                    ['s_facturacionboletafactura.idtienda', $idtienda],
                    ['s_facturacionboletafactura.id', $request->idcomprobante]
                ])
                ->select(
                    's_facturacionboletafactura.*',
                    DB::raw('DATE_FORMAT(s_facturacionboletafactura.venta_fechaemision, "%d/%m/%Y %h:%i:%s %p") as venta_fechaemision'),
                )
                ->first();
            if(is_null($facturacionboletafactura)){
                return [ 
                    'resultado' => 'ERROR',
                    'mensaje' => 'No existe la Boleta/Factura!.' 
                    ];
            }

            $facturacionboletafacturadetalle = DB::table('s_facturacionboletafacturadetalle')
                ->where('s_facturacionboletafacturadetalle.idfacturacionboletafactura',$facturacionboletafactura->id)
                ->orderBy('s_facturacionboletafacturadetalle.id','asc')
                ->get();  
            $html_detalle = '';
            foreach($facturacionboletafacturadetalle as $value){
                
                $facturacionnotadebitodetalle = DB::table('s_facturacionnotadebitodetalle')
                    ->where('s_facturacionnotadebitodetalle.idfacturacionboletafacturadetalle',$value->id)
                    ->sum('s_facturacionnotadebitodetalle.cantidad');
            
                $restante = $value->cantidad - $facturacionnotadebitodetalle;
            
                if($restante>0){
                    $html_detalle = $html_detalle.'<tr id="'.$value->id.'" idfacturacionboletafacturadetalle="'.$value->id.'" idproducto="'.$value->idproducto.'" 
                        style="background-color: #008cea;color: #fff;height: 40px;">
                        <!-- <td>'.$value->codigoproducto.'</td> -->
                        <td>'.$value->descripcion.'</td>
                        <td><input class="form-control" type="text" value="'.$restante.'" disabled></td>                                
                        <td><input class="form-control" type="number" value="'.$value->montopreciounitario.'" step="0.01" min="0" disabled></td> 
                        <td><input class="form-control" id="productCant'.$value->id.'" type="number" value="'.$restante.'" onkeyup="calcularmonto()"></td>                                
                        <td><input class="form-control" id="productUnidad'.$value->id.'" type="number" value="'.$value->montopreciounitario.'" step="0.01" min="0" disabled></td>                        
                        <td><input class="form-control"   id="productTotal'.$value->id.'" type="text" value="'.number_format($restante*$value->montopreciounitario,2, '.', '').'" step="0.01" min="0" disabled></td>                                      
                        <td class="with-btn" width="10px"><a id="'.$value->id.'" href="javascript:;" onclick="eliminarproducto('.$value->id.')" class="btn btn-danger big-btn"><i class="fas fa-trash-alt"></i></a></td>
                        </tr>';
                }
                    
            }
            return view(sistema_view().'/facturacionboletafactura/notadebito',[
                'tienda' => $tienda,
                'agencias' => $agencias,
                'facturacionboletafactura'  => $facturacionboletafactura,
                'facturacionboletafacturadetalle' => $html_detalle,
            ]);
            
            
        }
        else if($request->view == 'emision_guia'){
            $agencias = DB::table('s_agencia')->where('idtienda',$idtienda)->where('idestadofacturacion',1)->get();
            $motivos = DB::table('s_sunat_motivotraslado')->get();
            $facturacionboletafactura = DB::table('s_facturacionboletafactura')
                ->where([
                    ['s_facturacionboletafactura.id', $request->idcomprobante]
                ])
                ->first();
          
              
            if ($facturacionboletafactura->venta_tipodocumento == '03') {
                $comprobante = 'BOLETA';
            } else if ($facturacionboletafactura->venta_tipodocumento == '01') {
                $comprobante = 'FACTURA';
            } else {
                $comprobante = 'TICKET';
            }
            
            $agencia = DB::table('s_agencia as agencia')
                ->whereId($facturacionboletafactura->idagencia)
                ->select(
                    'agencia.*',
                    DB::raw('CONCAT(agencia.ruc, " - ", agencia.razonsocial) as agenciaCompleto')
                )
                ->first();
            $tienda = DB::table('tienda')
                ->join('ubigeo', 'ubigeo.id', 'tienda.idubigeo')
                ->where('tienda.id', $facturacionboletafactura->idtienda)
                ->select(
                    'tienda.*',
                    'ubigeo.nombre as ubigeonombre'
                )
                ->first();
            $cliente = DB::table('users')
                ->leftJoin('ubigeo', 'ubigeo.id', 'users.idubigeo')
                ->where('users.id', $facturacionboletafactura->idusuariocliente)
                ->select(
                    'users.*',
                    'ubigeo.codigo as ubigeocodigo',
                    'ubigeo.nombre as ubigeonombre',
                    DB::raw('CONCAT(users.identificacion, " - ", users.nombrecompleto) as nombreCompleto')
                )
                ->first();
            
            $detalle = DB::table('s_facturacionboletafacturadetalle')
                ->join('s_producto as product', 'product.id', 's_facturacionboletafacturadetalle.idproducto')
                ->where('s_facturacionboletafacturadetalle.idfacturacionboletafactura', $facturacionboletafactura->id)
                ->select(
                    's_facturacionboletafacturadetalle.*',
                    'product.nombre as nombreProduct',
                    'product.codigo as codigoProduct',
                    'product.imagen as imagen',
                )
                ->get();
                
            $html_detalle = '';
            $num = 1;
            foreach($detalle as $item){
                $guia_emitidas = DB::table('s_facturacionguiaremision')
                                ->join('s_facturacionguiaremisiondetalle','s_facturacionguiaremisiondetalle.idfacturacionguiaremision','s_facturacionguiaremision.id')
                                ->where('s_facturacionguiaremision.idfacturacionboletafactura',$facturacionboletafactura->id)
                                ->where('s_facturacionguiaremisiondetalle.idproducto',$item->idproducto)
                                ->sum('s_facturacionguiaremisiondetalle.cantidad');
                
                $cantidad_emision = $item->cantidad - $guia_emitidas;

            //   dump($cantidad_emision);

                if( $cantidad_emision > 0 ){
                
                $html_detalle .= '<tr id="'.$num.'" idproducto="'.$item->idproducto.'" nombreproducto="'.$item->codigoproducto.' - '.$item->descripcion.'" >
                                    <td>'.$item->codigoproducto.'</td>
                                    <td>'.$item->descripcion.'</td>
                                    <td>'.$cantidad_emision.'</td>
                                    <td class="mx-td-input"><input class="form-control" id="productCant'.$num.'" type="number" value="'.$cantidad_emision.'" step="0.01" min="0" onkeyup="calcularmonto()" onchange="calcularmonto()"></td>
                                    <td><a id="del'.$num.'" href="javascript:;" onclick="eliminarproducto('.$num.')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a></td>
                                </tr>';
                $num++;
                }
                
            }
            if( $num == 1){
                return [
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Todos los Productos fueron entregados'
                ];
            }else{
            
               
                return view(sistema_view().'/facturacionboletafactura/guiaremision',[
                    'tienda' => $tienda,
                    'agencia' => $agencias,
                    'motivos' => $motivos,
                    'cliente' => $cliente,
                    'facturacionboletafactura'  => $facturacionboletafactura,
                    'facturacionboletafacturadetalle' => $html_detalle,
                ]);
            }
              

            
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
            
            $rules = [
                'idtipopersona' => 'required',
            ];
            if ($request->idtipopersona == 1) {
                $rules = array_merge($rules,[
                    'dni' => 'required|numeric',
                    'idcliente' => 'required',
                ]);
            }
            elseif ($request->idtipopersona == 2) {
                $rules = array_merge($rules,[
                    'ruc' => 'required|numeric',
                    'idcliente' => 'required',
                ]);
            }
            elseif ($request->idtipopersona == 3) {
                $rules = array_merge($rules,[
                    'carnetextranjeria' => 'required',
                ]);
            }
          
            $rules = array_merge($rules,[
                'idagencia'                  => 'required',
                'idcomprobante'              => 'required',
                'idmoneda'                   => 'required',
                'productos'                  => 'required',
            ]);
            $messages = [
                'idtipopersona.required' => 'El "Tipo de Persona" es Obligatorio.',
                'dni.required' => 'El "DNI" es Obligatorio.',
                'dni.numeric'   => 'El "DNI" debe ser Númerico.',
                'dni.digits'   => 'El "DNI" debe ser de 8 Digitos.',
                'ruc.required' => 'El "RUC" es Obligatorio.',
                'ruc.numeric'   => 'El "RUC" debe ser Númerico.',
                'ruc.digits'   => 'El "RUC" debe ser de 11 Digitos.',
                'idcliente.required'         => 'El "Cliente" es Obligatorio.',
                'idagencia.required'         => 'La "Agencia" es Obligatorio.',
                'idmoneda.required'          => 'La "Moneda" es Obligatorio.',
                'idcomprobante.required'     => 'El "Tipo de comprobante" es Obligatorio.',
                'productos.required'         => 'Los "Productos" son Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
            /*
            COMPROBANTES
              $request->idcomprobante
              - NOTA DE VENTA = 1
              - BOLETA = 2
              - FACTURA = 3
            */
            
            $cliente_identificacion = 0;
            if($request->idtipopersona==1){
                $cliente_identificacion = $request->dni;
            }
            elseif($request->idtipopersona==2){
                $cliente_identificacion = $request->ruc;
            }
            elseif($request->idtipopersona==3){
              
            }
          

            $cliente_ubigeo = 'NONE';
            $cliente_departamento = 'NONE';
            $cliente_provincia = 'NONE';
            $cliente_distrito = 'NONE';
            $idusuariocliente = 0;
            if($cliente_identificacion==0){
                if($request->idtipopersona==1){
                    $cliente_tipodocumento  = 1;
                }
                elseif($request->idtipopersona==2){
                    $cliente_tipodocumento  = 6;
                }
              
                $cliente_pordefecto  = DB::table('users')->whereId(configuracion($idtienda,'facturacion_clientepordefecto')['valor'])->first();
                $cliente_nombrecompleto = 'EVENTUAL';
                if($cliente_pordefecto!=''){
                    $cliente_nombrecompleto = $cliente_pordefecto->nombrecompleto;
                    DB::table('users')->whereId($cliente_pordefecto->id)->update([
                       'direccion'  => $request->input('direccion'),
                    ]);
                }
            }else{
                $resultado_consultadniruc = consultaDniRuc($cliente_identificacion, $request->idtipopersona);
                $buscar_users = DB::table('users')->where('users.identificacion',$cliente_identificacion)->first();

                if(!$buscar_users){

                    if($request->idtipopersona==1){
                        $db_users_nombre = $resultado_consultadniruc['nombres'];
                        $db_users_appaterno = $resultado_consultadniruc['apellidoPaterno'];
                        $db_users_apmaterno = $resultado_consultadniruc['apellidoMaterno'];
                        $db_users_nombrecompleto = $resultado_consultadniruc['nombrecompleto'];
                        $db_users_razonsocial = '';
                    }
                    elseif($request->idtipopersona==2){
                        $db_users_nombre = $resultado_consultadniruc['nombreComercial'];
                        $db_users_appaterno = '';
                        $db_users_apmaterno = '';
                        $db_users_razonsocial = $resultado_consultadniruc['razonSocial'];
                        $db_users_nombrecompleto = $resultado_consultadniruc['nombrecompleto'];
                    }
                    elseif($request->idtipopersona==3){
                        dd("sin parametros");
                    }

                    $idusuariocliente = DB::table('users')->insertGetId([
                      'idtipopersona'       => $request->idtipopersona,
                      'nombre'              => $db_users_nombre,
                      'apellidopaterno'     => $db_users_appaterno,
                      'apellidomaterno'     => $db_users_apmaterno,
                      'razonsocial'         => $db_users_razonsocial,
                      'nombrecompleto'      => $db_users_nombrecompleto,
                      'identificacion'      => $cliente_identificacion,
                      'email'               => '',
                      'imagen'              => '',
                      'numerotelefono'      => '',
                      'direccion'           => '',
                      'mapa_latitud'        => '',
                      'mapa_longitud'       => '',
                      'email_verified_at'   => Carbon::now(),
                      'usuario'             => Carbon::now()->format("Ymdhisu").'@'.$idtienda.'.com',
                      'clave'               => '123',
                      'password'            => Hash::make('123'),
                      'idubigeo'            => 0,
                      'iduserspadre'        => 0,
                      'idtipousuario'       => 2, // 3=usuario sistema
                      'idtienda'            => $idtienda,
                      'idestadousuario'     => 2,
                      'idestado'            => 1
                  ]);

                }else{
                    $idusuariocliente = $buscar_users->id;
                }

                DB::table('users')->where('users.identificacion',$cliente_identificacion)->update([
                   'direccion'  => $request->input('direccion'),
                ]);
                if($request->idtipopersona==1){
                    $cliente_tipodocumento  = 1;
                }
                elseif($request->idtipopersona==2){
                    $cliente_tipodocumento  = 6;
                    $cliente_ubigeo = $resultado_consultadniruc['codigo'];
                    $cliente_departamento = $resultado_consultadniruc['departamento'];
                    $cliente_provincia = $resultado_consultadniruc['provincia'];
                    $cliente_distrito = $resultado_consultadniruc['distrito'];
                }
                $cliente_nombrecompleto = $resultado_consultadniruc['nombrecompleto'];
            }
          
            if( ( $request->idcomprobante == 2 && $cliente_tipodocumento == 6 )  || ( $request->idcomprobante == 3 )   ){
                $rules['direccion']               = 'required';
                $messages['direccion.required']   = 'La "Dirección" es Obligatorio.';
            }
            $this->validate($request,$rules,$messages);
            
            if($request->idcomprobante==2){
                if($request->input('total')>=700){
                    if($cliente_identificacion==0){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El DNI es Obligatorio, ya que el monto es mayor a S/. 700.00.'
                        ]);
                    }
                }
              }
            if($request->idcomprobante==3){
                if($request->idtipopersona==1){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Para emitir una Factura, el cliente debe ser con RUC.'
                    ]);
                }
            } 
            // Recorriendo los productos, capturados
          
            $productos = explode('/&/', $request->input('productos'));
            for($i = 1;$i <  count($productos);$i++){
                $item = explode('/,/', $productos[$i]);
                if($item[3]<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La unidad de medida es obligatoria.'
                    ]);
                    break;
                }elseif($item[1]<=0){
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
            } 
          
            if ($request->input('idcomprobante')==1) {
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No esta permitido enviar este tipo de comprobante por este modulo.'
                ]);
            }
          
            $agencia = DB::table('s_agencia')
                ->where('s_agencia.id',$request->input('idagencia'))
                ->first();

            $moneda = DB::table('s_moneda')->whereId($request->input('idmoneda'))->first();
            

            if(Auth::user()->idsucursal == 0){
                $tienda = DB::table('tienda')
                    ->leftJoin('s_ubigeo','s_ubigeo.id','tienda.idubigeo')
                    ->where('tienda.id',$idtienda)
                    ->select(
                        'tienda.id as tiendaserie',
                        's_ubigeo.codigo as tiendaubigeocodigo',
                        's_ubigeo.distrito as tiendaubigeodistrito',
                        's_ubigeo.provincia as tiendaubigeoprovincia',
                        's_ubigeo.departamento as tiendaubigeodepartamento',
                        'tienda.direccion as tiendadireccion',
                        'tienda.sucursal_facturacionserie as facturacionserie',
                        'tienda.sucursal_facturacioncorrelativo as facturacioncorrelativo',
                    )
                    ->first();
            }else{
                $tienda = DB::table('s_sucursal')
                        ->leftJoin('s_ubigeo','s_ubigeo.id','s_sucursal.idubigeo')
                        ->where('s_sucursal.id',Auth::user()->idsucursal)
                        ->select(
                            's_ubigeo.codigo as tiendaubigeocodigo',
                            's_ubigeo.distrito as tiendaubigeodistrito',
                            's_ubigeo.provincia as tiendaubigeoprovincia',
                            's_ubigeo.departamento as tiendaubigeodepartamento',
                            's_sucursal.direccion as tiendadireccion',
                            's_sucursal.facturacionserie',
                            's_sucursal.facturacioncorrelativo',
                        )
                        ->first();
            }
          
            

            if($request->input('idcomprobante')==2) {
                $venta_tipodocumento  = '03';
                $venta_serie          = 'B'.str_pad($tienda->facturacionserie, 3, "0", STR_PAD_LEFT);
            }else if($request->input('idcomprobante')==3) {
                $venta_tipodocumento  = '01';
                $venta_serie          = 'F'.str_pad($tienda->facturacionserie, 3, "0", STR_PAD_LEFT);
            }
          
            $correlativo = DB::table('s_facturacionboletafactura')
                ->where('venta_tipodocumento',$venta_tipodocumento)
                ->where('emisor_ruc',$agencia->ruc)
                ->where('venta_serie',$venta_serie)
                ->orderBy('venta_correlativo','desc')
                ->limit(1)
                ->first();

            if(!is_null($correlativo) ){
                $venta_correlativo = $correlativo->venta_correlativo+1;
            }else{
                $venta_correlativo = 1;
            }
          
            $igv = ((configuracion($idtienda,'facturacion_igv')['resultado']=='CORRECTO'?configuracion($idtienda,'facturacion_igv')['valor']:18)/100)+1;
            $total_precioventa    = 0;
            $total_valorunitario  = 0;
            $total_valorventa     = 0;
            $total_impuesto       = 0;
            for($i = 1; $i < count($productos); $i++){
                $item           = explode('/,/',$productos[$i]);
                $cantidad       = $item[1];
                $preciounitario = number_format($item[2],2, '.', '');
                $precioventa    = number_format($preciounitario*$cantidad,2, '.', '');
                $valorunitario  = number_format(($preciounitario/$igv),2, '.', '');
                $valorventa     = number_format($valorunitario*$cantidad,2, '.', '');
                $impuesto            = number_format($precioventa-$valorventa,2, '.', '');

                $total_precioventa    = $total_precioventa+$precioventa;
                $total_valorunitario  = $total_valorunitario+$valorunitario;
                $total_valorventa     = $total_valorventa+$valorventa;
                $total_impuesto       = $total_impuesto+$impuesto;
            }
            $users_responsable = DB::table('users')->whereId(Auth::user()->id)->first();
            
            //dd($cliente_tipodocumento.$cliente_identificacion.$cliente_nombrecompleto.$cliente_ubigeo.$cliente_departamento.$cliente_provincia.$cliente_distrito);
            // Almacenando en la base de datos la facturacion
            $idfacturacionboletafactura = DB::table('s_facturacionboletafactura')->insertGetId([
                'emisor_ruc'                  => $agencia->ruc,
                'emisor_razonsocial'          => $agencia->razonsocial,
                'emisor_nombrecomercial'      => $agencia->nombrecomercial,
                'emisor_ubigeo'               => $tienda->tiendaubigeocodigo,
                'emisor_departamento'         => $tienda->tiendaubigeodepartamento,
                'emisor_provincia'            => $tienda->tiendaubigeoprovincia,
                'emisor_distrito'             => $tienda->tiendaubigeodistrito,
                'emisor_urbanizacion'         => '',
                'emisor_direccion'            => $tienda->tiendadireccion,
                'cliente_tipodocumento'       => $cliente_tipodocumento,
                'cliente_numerodocumento'     => $cliente_identificacion,
                'cliente_razonsocial'         => $cliente_nombrecompleto,
                'cliente_ubigeo'              => $cliente_ubigeo,
                'cliente_departamento'        => $cliente_departamento,
                'cliente_provincia'           => $cliente_provincia,
                'cliente_distrito'            => $cliente_distrito,
                'cliente_urbanizacion'        => '',
                'cliente_direccion'           => $request->input('direccion') != '' ? $request->input('direccion') : 'S/N' ,
                'venta_ublversion'            => '2.1',
                'venta_tipooperacion'         => '0101',
                'venta_tipodocumento'         => $venta_tipodocumento,
                'venta_serie'                 => $venta_serie,
                'venta_correlativo'           => $venta_correlativo,
                'venta_fechaemision'          => Carbon::now(),
                'venta_tipomoneda'            => $moneda->codigo,
                'venta_montooperaciongravada' => number_format($total_valorventa,2, '.', ''),
                'venta_montoigv'              => number_format($total_impuesto,2, '.', ''),
                'venta_totalimpuestos'        => number_format($total_impuesto,2, '.', ''),
                'venta_valorventa'            => number_format($total_valorventa,2, '.', ''),
                'venta_subtotal'              => number_format($total_precioventa,2, '.', ''),
                'venta_montoimpuestoventa'    => number_format($total_precioventa,2, '.', ''),
                'venta_igv'                   => configuracion($idtienda,'facturacion_igv')['resultado']=='CORRECTO'?configuracion($idtienda,'facturacion_igv')['valor']:18,
                'leyenda_codigo'              => '1000',
                'leyenda_value'               => NumeroALetras::convertir(number_format($total_precioventa,2, '.', '')).' CON  00/100 '.$moneda->nombre,
                'idventa'                     => 0,
                'idagencia'                   => $request->input('idagencia'),
                'idusuarioresponsable'        => Auth::user()->id,
                'db_idusersresponsable'       => $users_responsable->nombrecompleto,
                'idusuariocliente'            => $idusuariocliente,
                'idsucursal'                  => Auth::user()->idsucursal,
                'idtienda'                    => $idtienda,
            ]);
     
            for($i = 1; $i < count($productos); $i++){
                $item                 = explode('/,/',$productos[$i]);
                $producto             = DB::table('s_producto')->whereId($item[0])->first();
                $productounidadmedida = DB::table('s_unidadmedida')->whereId($item[3])->first();

                $cantidad             = $item[1];
                $preciounitario       = number_format($item[2],2, '.', '');
                $precioventa          = number_format($preciounitario*$cantidad,2, '.', '');
                $valorunitario        = number_format(($preciounitario/$igv),2, '.', '');
                $valorventa           = number_format($valorunitario*$cantidad,2, '.', '');
                $impuesto             = number_format($precioventa-$valorventa,2, '.', '');

                DB::table('s_facturacionboletafacturadetalle')->insert([
                    'codigoproducto'             => str_pad($producto->codigo, 6, "0", STR_PAD_LEFT),
                    'unidad'                     => $productounidadmedida->codigo,
                    'cantidad'                   => $item[1],
                    'descripcion'                => $item[6],
                    'montobaseigv'               => $valorventa,
                    'porcentajeigv'              => configuracion($idtienda,'facturacion_igv')['resultado']=='CORRECTO'?configuracion($idtienda,'facturacion_igv')['valor']:18,
                    'igv'                        => $impuesto,
                    'tipoafectacionigv'          => '10',
                    'totalimpuestos'             => $impuesto,
                    'montovalorventa'            => $valorventa,
                    'montovalorunitario'         => $valorunitario,
                    'montopreciounitario'        => $preciounitario,
                    'idproducto'                 => $producto->id,
                    'idfacturacionboletafactura' => $idfacturacionboletafactura,
                    'idtienda'                   => $idtienda,
               ]);
            }
            // Fin de Facturacion
          
            // Enviando a la Sunat
            $result = facturador_facturaboleta($idfacturacionboletafactura);
          
            return [
                  'resultado'                     => $result['resultado'],
                  'mensaje'                       => $result['mensaje'],
                  'idfacturacionboletafactura'    => $idfacturacionboletafactura,
            ];
        }
        else if($request->input('view') == 'emitir_notacredito'){
            $rules = [
                'idmotivonotacredito' => 'required',
                'motivonotacredito_descripcion' => 'required',
                'productos' => 'required',
            ]; 
            $messages = [
                'idmotivonotacredito.required' => 'El "Motivo" es Obligatorio.',
                'motivonotacredito_descripcion.required' => 'El "Motivo" es Obligatorio.',
                'productos.required'=> 'Los "Productos" son Obligatorio.',
            ];
             
            $this->validate($request,$rules,$messages);
        
            $post_produtos = json_decode($request->input('productos'));
            foreach ($post_produtos as $item_producto) {
            $producto = DB::table('s_producto')->whereId($item_producto->idproducto)->first();
            if ($item_producto->productCant <= 0) {
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La cantidad miníma del producto "'.$producto->nombre.'" debe ser 1.'
                ]);
            }elseif ($item_producto->productUnidad <= 0) {
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Precio Unitario minímo del producto "'.$producto->nombre.'" debe ser mayor a 0.00.'
                ]);
            }
            
            $facturacionnotacreditodetalle = DB::table('s_facturacionnotacreditodetalle')
                        ->where('s_facturacionnotacreditodetalle.idfacturacionboletafacturadetalle',$item_producto->idfacturacionboletafacturadetalle)
                        ->sum('s_facturacionnotacreditodetalle.cantidad');
            
            $facturacionboletafacturadetalle = DB::table('s_facturacionboletafacturadetalle')->whereId($item_producto->idfacturacionboletafacturadetalle)->first();
            
            $restante = $facturacionboletafacturadetalle->cantidad - $facturacionnotacreditodetalle;
            
            if( $facturacionboletafacturadetalle->cantidad < $item_producto->productCant ){
                return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad del producto "'.$producto->nombre.'" de "NOTA DE CRÉDITO" debe ser menor o igual a la cantidad de "FACTURA/BOLETA".'
                ]);
            }
            elseif($restante<=0){
                return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Ya no hay el productos "'.$producto->nombre.'", para generar una NOTA DE CREDITO!.'
                ]);
            }
            }   
            
            
            $facturacionboletafactura = DB::table('s_facturacionboletafactura')->whereId($request->input('idfacturacionboletafactura'))->first();

            if($facturacionboletafactura->venta_tipodocumento=='03') {
                    $list = explode('B',$facturacionboletafactura->venta_serie);
                    $notacredito_serie = 'BB'.str_pad(intval($list[1]), 2, "0", STR_PAD_LEFT);
            }elseif($facturacionboletafactura->venta_tipodocumento=='01') {
                    $list = explode('F',$facturacionboletafactura->venta_serie);
                    $notacredito_serie = 'FF'.str_pad(intval($list[1]), 2, "0", STR_PAD_LEFT);
            }else{
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El tipo de documento no es valido, ingrese otro.'
                ]);
            }
        
            $correlativo = DB::table('s_facturacionnotacredito')
                ->where('notacredito_tipodocumento','07')
                ->where('emisor_ruc',$facturacionboletafactura->emisor_ruc)
                ->where('notacredito_serie',$notacredito_serie)
                ->orderBy('notacredito_correlativo','desc')
                ->limit(1)
                ->first();
        
            if($correlativo!=''){
                $notacredito_correlativo = $correlativo->notacredito_correlativo+1;
            }else{
                $notacredito_correlativo = 1;
            }
        
            $notacredito_tipomonedanombre='';
            if($facturacionboletafactura->venta_tipomoneda=='PEN') {
                $notacredito_tipomonedanombre  = 'SOLES';
            }else if($facturacionboletafactura->venta_tipomoneda=='USD') {
                $notacredito_tipomonedanombre  = 'DOLARES';
            }
        
            $igv = ($facturacionboletafactura->venta_igv/100)+1;
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
        

            $idfacturacionnotacredito = DB::table('s_facturacionnotacredito')->insertGetId([
                'emisor_ruc'                          => $facturacionboletafactura->emisor_ruc,
                'emisor_razonsocial'                  => $facturacionboletafactura->emisor_razonsocial,
                'emisor_nombrecomercial'              => $facturacionboletafactura->emisor_nombrecomercial,
                'emisor_ubigeo'                       => $facturacionboletafactura->emisor_ubigeo,
                'emisor_departamento'                 => $facturacionboletafactura->emisor_departamento,
                'emisor_provincia'                    => $facturacionboletafactura->emisor_provincia,
                'emisor_distrito'                     => $facturacionboletafactura->emisor_distrito,
                'emisor_urbanizacion'                 => $facturacionboletafactura->emisor_urbanizacion,
                'emisor_direccion'                    => $facturacionboletafactura->emisor_direccion,
                'cliente_tipodocumento'               => $facturacionboletafactura->cliente_tipodocumento,
                'cliente_numerodocumento'             => $facturacionboletafactura->cliente_numerodocumento,
                'cliente_razonsocial'                 => $facturacionboletafactura->cliente_razonsocial,
                'cliente_ubigeo'                      => $facturacionboletafactura->cliente_ubigeo, 
                'cliente_departamento'                => $facturacionboletafactura->cliente_departamento, 
                'cliente_provincia'                   => $facturacionboletafactura->cliente_provincia, 
                'cliente_distrito'                    => $facturacionboletafactura->cliente_distrito, 
                'cliente_urbanizacion'                => $facturacionboletafactura->cliente_urbanizacion, 
                'cliente_direccion'                   => $facturacionboletafactura->cliente_direccion, 
                'notacredito_ublversion'              => $facturacionboletafactura->venta_ublversion,
                'notacredito_numerodocumentoafectado' => $facturacionboletafactura->venta_serie.'-'.$facturacionboletafactura->venta_correlativo,
                'notacredito_tipodocafectado'         => $facturacionboletafactura->venta_tipodocumento,
                'notacredito_codigomotivo'            => $request->input('idmotivonotacredito'),
                'notacredito_descripcionmotivo'       => $request->input('motivonotacredito_descripcion'),
                'notacredito_tipodocumento'           => '07', // nota de credito
                'notacredito_serie'                   => $notacredito_serie,
                'notacredito_correlativo'             => $notacredito_correlativo,
                'notacredito_fechaemision'            => Carbon::now(),
                'notacredito_tipomoneda'              => $facturacionboletafactura->venta_tipomoneda,
                'notacredito_montooperaciongravada'   => number_format($total_precioventa-$total_impuesto,2, '.', ''),
                'notacredito_montoigv'                => number_format($total_impuesto,2, '.', ''),
                'notacredito_totalimpuestos'          => number_format($total_impuesto,2, '.', ''),
                'notacredito_valorventa'              => number_format($total_valorventa,2, '.', ''),
                'notacredito_montoimpuestoventa'      => number_format($total_precioventa,2, '.', ''),
                'leyenda_codigo'                      => '1000',
                'leyenda_value'                       => NumeroALetras::convertir(number_format($total_precioventa,2, '.', '')).' CON  00/100 '.$notacredito_tipomonedanombre,
                'idfacturacionboletafactura'          => $facturacionboletafactura->id,
                'idagencia'                           => $facturacionboletafactura->idagencia,
                'idsucursal'                          => Auth::user()->idsucursal,
                'idtienda'                            => $facturacionboletafactura->idtienda,
                'idusuarioresponsable'                => Auth::user()->id,
                'idusuariocliente'                    => $facturacionboletafactura->idusuariocliente,
            ]);
            foreach ($post_produtos as $item_producto3) {
                $cantidad       = $item_producto3->productCant;
                $preciounitario = number_format($item_producto3->productUnidad,2, '.', '');
                $precioventa    = number_format($preciounitario*$cantidad,2, '.', '');
                $valorunitario  = number_format(($preciounitario/$igv),2, '.', '');
                $valorventa     = number_format($valorunitario*$cantidad,2, '.', '');
                $impuesto       = number_format($precioventa-$valorventa,2, '.', '');

                $facturacionboletafacturadetalle = DB::table('s_facturacionboletafacturadetalle')->whereId($item_producto3->idfacturacionboletafacturadetalle)->first();
            
                DB::table('s_facturacionnotacreditodetalle')->insert([
                    'codigoproducto'                    => $facturacionboletafacturadetalle->codigoproducto,
                    'unidad'                            => $facturacionboletafacturadetalle->unidad,
                    'cantidad'                          => $cantidad,
                    'descripcion'                       => $facturacionboletafacturadetalle->descripcion,
                    'montobaseigv'                      => $valorventa,
                    'porcentajeigv'                     => $facturacionboletafactura->venta_igv,
                    'igv'                               => $impuesto,
                    'tipoafectacionigv'                 => '10',
                    'totalimpuestos'                    => $impuesto,
                    'montovalorventa'                   => $valorventa,
                    'montovalorunitario'                => $valorunitario,
                    'montopreciounitario'               => $preciounitario,
                    'idproducto'                        => $facturacionboletafacturadetalle->idproducto,
                    'idfacturacionboletafacturadetalle' => $item_producto3->idfacturacionboletafacturadetalle,
                    'idfacturacionnotacredito'          => $idfacturacionnotacredito,
                    'idtienda'                          => $facturacionboletafactura->idtienda,
                ]);
            }
        
            $result = facturador_notacredito($idfacturacionnotacredito);

            return [
                'resultado' => $result['resultado'],
                'mensaje'   => $result['mensaje'],
                'idfacturacionnotacredito' => $idfacturacionnotacredito
            ];
        }
        else if($request->input('view') == 'emitir_notadebito'){
            $rules = [
                'idmotivonotadebito'             => 'required',
                'motivonotadebito_descripcion'   => 'required',
                'productos'                      => 'required',
            ]; 
            $messages = [
                'idmotivonotadebito.required'            => 'El "Motivo" es Obligatorio.',
                'motivonotadebito_descripcion.required' => 'El "Motivo" es Obligatorio.',
                'productos.required'                     => 'Los "Productos" son Obligatorio.',
            ];
            
            $this->validate($request,$rules,$messages);
        
            $post_produtos = json_decode($request->input('productos'));
            foreach ($post_produtos as $item_producto) {
                $producto = DB::table('s_producto')->whereId($item_producto->idproducto)->first();
                if ($item_producto->productCant <= 0) {
                    return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'La cantidad miníma del producto "'.$producto->nombre.'" debe ser 1.'
                    ]);
                }elseif ($item_producto->productUnidad <= 0) {
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Precio Unitario minímo del producto "'.$producto->nombre.'" debe ser mayor a 0.00.'
                    ]);
                }
                
                $facturacionnotadebitodetalle = DB::table('s_facturacionnotadebitodetalle')
                            ->where('s_facturacionnotadebitodetalle.idfacturacionboletafacturadetalle',$item_producto->idfacturacionboletafacturadetalle)
                            ->sum('s_facturacionnotadebitodetalle.cantidad');
                
                $facturacionboletafacturadetalle = DB::table('s_facturacionboletafacturadetalle')->whereId($item_producto->idfacturacionboletafacturadetalle)->first();
                
                $restante = $facturacionboletafacturadetalle->cantidad - $facturacionnotadebitodetalle;
                
                if($facturacionboletafacturadetalle->cantidad<$item_producto->productCant){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad del producto "'.$producto->nombre.'" de "NOTA DE DÉBITO" debe ser menor o igual a la cantidad de "FACTURA/BOLETA".'
                    ]);
                }
                elseif($restante<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Ya no hay el productos "'.$producto->nombre.'", para generar una NOTA DE DEBITO!.'
                    ]);
                }
            }   
            
            $facturacionboletafactura = DB::table('s_facturacionboletafactura')->whereId($request->input('idfacturacionboletafactura'))->first();

            if($facturacionboletafactura->venta_tipodocumento=='03') {
                    $list = explode('B',$facturacionboletafactura->venta_serie);
                    $notadebito_serie = 'BB'.str_pad(intval($list[1]), 2, "0", STR_PAD_LEFT);
            }elseif($facturacionboletafactura->venta_tipodocumento=='01') {
                    $list = explode('F',$facturacionboletafactura->venta_serie);
                    $notadebito_serie = 'FF'.str_pad(intval($list[1]), 2, "0", STR_PAD_LEFT);
            }else{
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El tipo de documento no es valido, ingrese otro.'
                ]);
            }
        
            $correlativo = DB::table('s_facturacionnotadebito')
                ->where('notadebito_tipodocumento','08')
                ->where('emisor_ruc',$facturacionboletafactura->emisor_ruc)
                ->where('notadebito_serie',$notadebito_serie)
                ->orderBy('notadebito_correlativo','desc')
                ->limit(1)
                ->first();
        
            if($correlativo!=''){
                $notadebito_correlativo = $correlativo->notadebito_correlativo+1;
            }else{
                $notadebito_correlativo = 1;
            }
        
            $notadebito_tipomonedanombre='';
            if($facturacionboletafactura->venta_tipomoneda=='PEN') {
                $notadebito_tipomonedanombre  = 'SOLES';
            }else if($facturacionboletafactura->venta_tipomoneda=='USD') {
                $notadebito_tipomonedanombre  = 'DOLARES';
            }
        
            $igv = ($facturacionboletafactura->venta_igv/100)+1;
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
        

            $idfacturacionnotadebito = DB::table('s_facturacionnotadebito')->insertGetId([
                'emisor_ruc'                          => $facturacionboletafactura->emisor_ruc,
                'emisor_razonsocial'                  => $facturacionboletafactura->emisor_razonsocial,
                'emisor_nombrecomercial'              => $facturacionboletafactura->emisor_nombrecomercial,
                'emisor_ubigeo'                       => $facturacionboletafactura->emisor_ubigeo,
                'emisor_departamento'                 => $facturacionboletafactura->emisor_departamento,
                'emisor_provincia'                    => $facturacionboletafactura->emisor_provincia,
                'emisor_distrito'                     => $facturacionboletafactura->emisor_distrito,
                'emisor_urbanizacion'                 => $facturacionboletafactura->emisor_urbanizacion,
                'emisor_direccion'                    => $facturacionboletafactura->emisor_direccion,
                'cliente_tipodocumento'               => $facturacionboletafactura->cliente_tipodocumento,
                'cliente_numerodocumento'             => $facturacionboletafactura->cliente_numerodocumento,
                'cliente_razonsocial'                 => $facturacionboletafactura->cliente_razonsocial,
                'cliente_ubigeo'                      => $facturacionboletafactura->cliente_ubigeo, 
                'cliente_departamento'                => $facturacionboletafactura->cliente_departamento, 
                'cliente_provincia'                   => $facturacionboletafactura->cliente_provincia, 
                'cliente_distrito'                    => $facturacionboletafactura->cliente_distrito, 
                'cliente_urbanizacion'                => $facturacionboletafactura->cliente_urbanizacion, 
                'cliente_direccion'                   => $facturacionboletafactura->cliente_direccion, 
                'notadebito_ublversion'              => $facturacionboletafactura->venta_ublversion,
                'notadebito_numerodocumentoafectado' => $facturacionboletafactura->venta_serie.'-'.$facturacionboletafactura->venta_correlativo,
                'notadebito_tipodocafectado'         => $facturacionboletafactura->venta_tipodocumento,
                'notadebito_codigomotivo'            => $request->input('idmotivonotadebito'),
                'notadebito_descripcionmotivo'       => $request->input('motivonotadebito_descripcion'),
                'notadebito_tipodocumento'           => '08', // nota de debito
                'notadebito_serie'                   => $notadebito_serie,
                'notadebito_correlativo'             => $notadebito_correlativo,
                'notadebito_fechaemision'            => Carbon::now(),
                'notadebito_tipomoneda'              => $facturacionboletafactura->venta_tipomoneda,
                'notadebito_montooperaciongravada'   => number_format($total_precioventa-$total_impuesto,2, '.', ''),
                'notadebito_montoigv'                => number_format($total_impuesto,2, '.', ''),
                'notadebito_totalimpuestos'          => number_format($total_impuesto,2, '.', ''),
                'notadebito_valorventa'              => number_format($total_valorventa,2, '.', ''),
                'notadebito_montoimpuestoventa'      => number_format($total_precioventa,2, '.', ''),
                'leyenda_codigo'                      => '1000',
                'leyenda_value'                       => NumeroALetras::convertir(number_format($total_precioventa,2, '.', '')).' CON  00/100 '.$notadebito_tipomonedanombre,
                'idfacturacionboletafactura'          => $facturacionboletafactura->id,
                'idagencia'                           => $facturacionboletafactura->idagencia,
                'idtienda'                            => $facturacionboletafactura->idtienda,
                'idsucursal'                          => Auth::user()->idsucursal,
                'idusuarioresponsable'                => Auth::user()->id,
                'idusuariocliente'                    => $facturacionboletafactura->idusuariocliente,
            ]);
            foreach ($post_produtos as $item_producto3) {
                $cantidad       = $item_producto3->productCant;
                $preciounitario = number_format($item_producto3->productUnidad,2, '.', '');
                $precioventa    = number_format($preciounitario*$cantidad,2, '.', '');
                $valorunitario  = number_format(($preciounitario/$igv),2, '.', '');
                $valorventa     = number_format($valorunitario*$cantidad,2, '.', '');
                $impuesto       = number_format($precioventa-$valorventa,2, '.', '');

                $facturacionboletafacturadetalle = DB::table('s_facturacionboletafacturadetalle')->whereId($item_producto3->idfacturacionboletafacturadetalle)->first();
            
                DB::table('s_facturacionnotadebitodetalle')->insert([
                    'codigoproducto'                    => $facturacionboletafacturadetalle->codigoproducto,
                    'unidad'                            => $facturacionboletafacturadetalle->unidad,
                    'cantidad'                          => $cantidad,
                    'descripcion'                       => $facturacionboletafacturadetalle->descripcion,
                    'montobaseigv'                      => $valorventa,
                    'porcentajeigv'                     => $facturacionboletafactura->venta_igv,
                    'igv'                               => $impuesto,
                    'tipoafectacionigv'                 => '10',
                    'totalimpuestos'                    => $impuesto,
                    'montovalorventa'                   => $valorventa,
                    'montovalorunitario'                => $valorunitario,
                    'montopreciounitario'               => $preciounitario,
                    'idproducto'                        => $facturacionboletafacturadetalle->idproducto,
                    'idfacturacionboletafacturadetalle' => $item_producto3->idfacturacionboletafacturadetalle,
                    'idfacturacionnotadebito'           => $idfacturacionnotadebito,
                    'idtienda'                          => $facturacionboletafactura->idtienda,
                ]);
            }
        
            $result = facturador_notadebito($idfacturacionnotadebito);
            
            return [
                'resultado' => $result['resultado'],
                'mensaje'   => $result['mensaje'],
                'idfacturacionnotadebito' => $idfacturacionnotadebito
            ];
        }
        else if($request->input('view') == 'emitir_guia') {
            
            $rules    = [
                'agencia'               => 'required',
                'puntopartida'          => 'required',
                'direccionpartida'      => 'required',
                'destinatario'          => 'required',
                'destinatario_ubigeo'   => 'required',
                'destinatario_direccion'=> 'required',
                'motivo'                => 'required',
                'fechatraslado'         => 'required',
                // 'transportista'         => 'required',
                'peso_neto_gre'           => 'required',
            ];
            $messages = [
                'agencia.required'                => 'El "Remitente" es Obligatorio.',
                'puntopartida.required'           => 'El "Punto de Partida" es Obligatorio.',
                'direccionpartida.required'       => 'La "Direccion de Partida" es Obligatorio.',
                'destinatario_nombre.required'    => 'El "Destinatario" es Obligatorio.',
                'destinatario_ubigeo.required'    => 'El "Punto de LLegada" es Obligatorio.',
                'destinatario_direccion.required' => 'La "Direccion de LLegada" es Obligatorio.',
                'motivo.required'                 => 'El "Motivo" es Obligatorio.',
                'fechatraslado.required'          => 'La "Fecha de Traslado" es Obligatorio.',
                // 'transportista.required'          => 'El "Transportista" es Obligatorio.',
                'peso_neto_gre.required'            => 'El "Peso Total" es Obligatorio.',
            ];
            if( $request->input('idmodalidadtraslado') == 1 ){
              $rules['transportista']              = 'required';
              $messages['transportista.required']  = 'El campo "Empresa de Transporte" es obligatorio.';

            }
            else if( $request->input('idmodalidadtraslado') == 2 ){
              $rules['transportista']                  = 'required';
              $rules['placavehiculoprincipal']              = 'min:6|required';
              $messages['transportista.required']      = 'El campo "Conductor" es obligatorio.';
              $messages['placavehiculoprincipal.required']  = 'El campo "Placa Principal" es obligatorio.';
              $messages['placavehiculoprincipal.min']       = 'El campo "Placa Principal" debe tener 6 caracteres.';
              if( $request->placavehiculosecundario!='' ){
                $rules['placavehiculosecundario']             = 'min:6|required';
                $messages['placavehiculosecundario.required'] = 'El campo "Placa Secundario" es obligatorio.';
                $messages['placavehiculosecundario.min']      = 'El campo "Placa Secundario" debe tener 6 caracteres.';
              }
            }
          
            $this->validate($request,$rules,$messages);

            // Capturando los PRODUCTOS
            $productos = json_decode($request->productos);
            
            if (empty($productos)) {
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Debe ingresar mínimo un producto!!.'
                ]);
            }
          
            
          
            foreach ($productos as $item) {
               if($item->cantidad <= 0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad miníma es 1.'
                    ]);
                }
            }
            
            $tiendas = DB::table('tienda')
              ->join('ubigeo','ubigeo.id','tienda.idubigeo')
              ->where('tienda.id',$idtienda)
              ->select(
                  'tienda.*',
                  'tienda.direccion as tiendadireccion',
                  'ubigeo.codigo as tiendaubigeocodigo',
                  'ubigeo.distrito as tiendaubigeodistrito',
                  'ubigeo.provincia as tiendaubigeoprovincia',
                  'ubigeo.departamento as tiendaubigeodepartamento'
              )
              ->first();
      
            $facturacionboletafactura = DB::table('s_facturacionboletafactura')->whereId($request->input('idfacturacionboletafactura'))->first();
       
            if($facturacionboletafactura->venta_tipodocumento=='01'){
                $list = explode('F',$facturacionboletafactura->venta_serie);
            }
            elseif($facturacionboletafactura->venta_tipodocumento=='03'){
                $list = explode('B',$facturacionboletafactura->venta_serie);
            }
          
          
            $despacho_serie = 'T'.str_pad(intval($list[1]), 3, "0", STR_PAD_LEFT);


            //$agencia = DB::table('s_agencia')->whereId($request->input('agencia'))->first();
            $ubigeo  = DB::table('s_ubigeo')->whereId($request->input('puntopartida'))->first();

            // Datos del destinatario o despacho
            $tienda         = DB::table('tienda')->whereId($idtienda)->first();
            $despacho       = DB::table('users')
                ->join('tipopersona','tipopersona.id','users.idtipopersona')
                ->where('users.id',$request->input('destinatario'))
                ->select(
                    'users.*',
                    'tipopersona.codigo as tipodocumento'
                )
                ->first();
            //$despacho_serie = 'T'.str_pad($agencia->facturacion_serie, 3, "0", STR_PAD_LEFT);
            
            $correlativo    = DB::table('s_facturacionguiaremision')
                ->where('s_facturacionguiaremision.emisor_ruc', $facturacionboletafactura->emisor_ruc)
                ->where('s_facturacionguiaremision.despacho_serie', $despacho_serie)
                ->orderBy('s_facturacionguiaremision.despacho_correlativo','desc')
                ->first();


            $despacho_correlativo = ($correlativo == '') ? 1 : $correlativo->despacho_correlativo + 1;

            // Datos del transportista
            $transportista = DB::table('users')
                                ->join('tipopersona','tipopersona.id','users.idtipopersona')
                                ->where('users.id',$request->input('transportista'))
                                ->select(
                                    'users.*',
                                    'tipopersona.codigo as tipodocumento'
                                )
                                ->first();
                                
            if( $request->input('idmodalidadtraslado') == 1 && $transportista->idtipopersona==1 ){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'EL TRANSPORTISTA DEBE SER UNA IMPRESA CON RUC 20'
                ]);
            }
              
                // Datos del motivo o envio
            $sunat_motivotraslado = DB::table('s_sunat_motivotraslado')->whereId($request->input('motivo'))->first();
            $sunat_modalidadtraslado = DB::table('s_sunat_modalidadtraslado')->whereId($request->input('idmodalidadtraslado'))->first();
            $puntopartida = DB::table('s_ubigeo')->whereId($request->input('puntopartida'))->first();
            $puntollegada = DB::table('s_ubigeo')->whereId($request->input('destinatario_ubigeo'))->first();
            
            
            
            
            // Almacenando la guia de remision
            $idfacturacionguiaremision = DB::table('s_facturacionguiaremision')->insertGetId([
                'emisor_ruc'                            => $facturacionboletafactura->emisor_ruc,
                'emisor_razonsocial'                    => $facturacionboletafactura->emisor_razonsocial,
                'emisor_nombrecomercial'                => $facturacionboletafactura->emisor_nombrecomercial,
                'emisor_ubigeo'                         => $tiendas->tiendaubigeocodigo,
                'emisor_departamento'                   => $tiendas->tiendaubigeodepartamento,
                'emisor_provincia'                      => $tiendas->tiendaubigeoprovincia,
                'emisor_distrito'                       => $tiendas->tiendaubigeodistrito,
                'emisor_urbanizacion'                   => '',
                'emisor_direccion'                      => $tiendas->tiendadireccion,
              
                'despacho_tipodocumento'                => '09',
                'despacho_serie'                        => $despacho_serie,
                'despacho_correlativo'                  => $despacho_correlativo,
                'despacho_fechaemision'                 => Carbon::now(),
                'despacho_destinatario_tipodocumento'   => $despacho->tipodocumento,
                'despacho_destinatario_numerodocumento' => $despacho->identificacion,
                'despacho_destinatario_razonsocial'     => $despacho->nombrecompleto,
                'despacho_tercero_tipodocumento'        => '',
                'despacho_tercero_numerodocumento'      => '',
                'despacho_tercero_razonsocial'          => '',
                'despacho_observacion'                  => !is_null($request->input('observacion')) ? $request->input('observacion') : '',
              
                'transporte_tipodocumento'              => 6,
                'transporte_numerodocumento'            => $request->input('idmodalidadtraslado') == 1 ? $transportista->identificacion : '',
                'transporte_razonsocial'                => $request->input('idmodalidadtraslado') == 1 ? $transportista->nombrecompleto : '',
              
                'transporte_placa'                      => $request->input('idmodalidadtraslado') != 1 ? $request->placavehiculoprincipal: '000000',
                'transporte_chofertipodocumento'        => $transportista->tipodocumento,
                'transporte_choferdocumento'            => $transportista->identificacion,
             
                'transporte_chofernombres'              => $transportista->nombre,
                'transporte_choferapellidos'            => $transportista->apellidopaterno.' '.$transportista->apellidomaterno,
                'transporte_choferlicencia'             => $request->licencia_conductor,
              
                'envio_codigotraslado'                  => $sunat_motivotraslado->codigo,
                'envio_descripciontraslado'             => $sunat_motivotraslado->nombre,
                'envio_modtraslado'                     => $sunat_modalidadtraslado->codigo,
                'envio_fechatraslado'                   => $request->input('fechatraslado'),
                'envio_codigopuerto'                    => '',
                'envio_indtransbordo'                   => '',
                'envio_pesototal'                       => $request->input('peso_neto_gre'),
                'envio_unidadpesototal'                 => 'KGM', // KGM !! TNE
                'envio_numerocontenedor'                => '',
                'envio_direccionllegadacodigoubigeo'    => $puntollegada->codigo,
                'envio_direccionllegada'                => $request->input('destinatario_direccion'),
                'envio_direccionpartidacodigoubigeo'    => $puntopartida->codigo,
                'envio_direccionpartida'                => $request->input('direccionpartida'),
              
                'idfacturacionboletafactura'            => !is_null($request->input('idfacturacionboletafactura')) ? $request->input('idfacturacionboletafactura') : 0,
                'idventa'                               => !is_null($request->input('idventa')) ? $request->input('idventa') : 0,
                'idcompra'                              => !is_null($request->input('idcompra')) ? $request->input('idcompra') : 0,
                'idagencia'                             => $request->input('agencia'),
                'idsucursal'                            => Auth::user()->idsucursal,
                'idtienda'                              => $idtienda,
                'idusuarioresponsable'                  => Auth::user()->id,
                'idusuariocliente'                      => $despacho->id,
                'idusuariochofer'                       => $transportista->id
            ]);
          
            foreach ($productos as $item) {
                $producto = DB::table('s_producto')
                    ->join('unidadmedida','unidadmedida.id','s_producto.s_idunidadmedida')
                    ->where('s_producto.id',$item->idproducto)
                    ->select(
                        's_producto.*',
                        'unidadmedida.codigo as unidadmedidacodigo'
                    )
                    ->first();
                DB::table('s_facturacionguiaremisiondetalle')->insert([
                    'cantidad'                  => $item->cantidad,
                    'unidad'                    => $producto->unidadmedidacodigo,
                    'descripcion'               => $producto->nombre,
                    'codigo'                    => $producto->codigo,
                    'codprodsunat'              => $producto->codigo,
                    'idproducto'                => $producto->id,
                    'idfacturacionguiaremision' => $idfacturacionguiaremision,
                    'idtienda'                  => $idtienda,
                ]);
            }
          
            // Enviando a la Sunat
            $result = facturador_guiaremision_api($idfacturacionguiaremision);
 
            
            return [
                  'resultado' => $result['tipo'],
                  'mensaje'   => $result['mensaje'],
                  'idfacturacionguiaremision' => $idfacturacionguiaremision
            ];
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        if($id=='show_table'){
            $facturacionboletafactura = DB::table('s_facturacionboletafactura')
                ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacionboletafactura','s_facturacionboletafactura.id')
                ->where('s_facturacionboletafactura.idtienda', $idtienda)
                ->where('s_facturacionboletafactura.idsucursal', Auth::user()->idsucursal)
                ->select(
                    's_facturacionboletafactura.id',
                    's_facturacionboletafactura.venta_fechaemision as fechaemision',
                    's_facturacionboletafactura.venta_serie as serie',
                    's_facturacionboletafactura.venta_correlativo as correlativo',
                    's_facturacionboletafactura.venta_tipodocumento as tipodocumento',
                    's_facturacionboletafactura.venta_montoimpuestoventa as total',
                    's_facturacionboletafactura.cliente_numerodocumento as ruc_cliente',
                    's_facturacionboletafactura.cliente_razonsocial as razonsocial_cliente',
                    's_facturacionboletafactura.emisor_ruc as ruc_emisor',
                    's_facturacionboletafactura.emisor_razonsocial as razonsocial_emisor',
                    's_facturacionboletafactura.venta_tipomoneda as tipomoneda',
                    's_facturacionrespuesta.estado as respuestaestado'
                )
                ->selectRaw(
                  "CASE 
                   WHEN s_facturacionboletafactura.venta_tipodocumento = '01' THEN (SELECT COUNT(id) AS cantidad_documento_comunicacion
                    FROM s_facturacioncomunicacionbajadetalle 
                    WHERE s_facturacioncomunicacionbajadetalle.idfacturacionboletafactura =  s_facturacionboletafactura.id)
                   WHEN s_facturacionboletafactura.venta_tipodocumento = '03' THEN (SELECT COUNT(id) AS cantidad_documento_resumendiario
                    FROM s_facturacionresumendiariodetalle 
                    WHERE s_facturacionresumendiariodetalle.idfacturacionboletafactura =  s_facturacionboletafactura.id)
                   ELSE '0'
                   END AS cantidad_anulado",
                )
                ->selectRaw("'00' AS documento_afectado");

            $facturacionnotacredito = DB::table('s_facturacionnotacredito')
                ->leftJoin('users as responsable','responsable.id','s_facturacionnotacredito.idusuarioresponsable')
                ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacionnotacredito','s_facturacionnotacredito.id')
                ->where('s_facturacionnotacredito.idtienda', $idtienda)
                ->where('s_facturacionnotacredito.idsucursal', Auth::user()->idsucursal)
                ->select(
                    's_facturacionnotacredito.id',
                    's_facturacionnotacredito.notacredito_fechaemision as fechaemision',
                    's_facturacionnotacredito.notacredito_serie as serie',
                    's_facturacionnotacredito.notacredito_correlativo as correlativo',
                    's_facturacionnotacredito.notacredito_tipodocumento as tipodocumento',
                    's_facturacionnotacredito.notacredito_montoimpuestoventa as total',
                    's_facturacionnotacredito.cliente_numerodocumento as ruc_cliente',
                    's_facturacionnotacredito.cliente_razonsocial as razonsocial_cliente',
                    's_facturacionnotacredito.emisor_ruc as ruc_emisor',
                    's_facturacionnotacredito.emisor_razonsocial as razonsocial_emisor',
                    's_facturacionnotacredito.notacredito_tipomoneda as tipomoneda',
                    's_facturacionrespuesta.estado as respuestaestado',

                    
                )
                ->selectRaw(
                    "CASE 
                    WHEN s_facturacionnotacredito.notacredito_tipodocafectado = '01' THEN (SELECT COUNT(id) AS cantidad_documento_comunicacion
                    FROM s_facturacioncomunicacionbajadetalle 
                    WHERE s_facturacioncomunicacionbajadetalle.idfacturacionnotacredito =  s_facturacionnotacredito.id)
                    WHEN s_facturacionnotacredito.notacredito_tipodocafectado = '03' THEN (SELECT COUNT(id) AS cantidad_documento_resumendiario
                    FROM s_facturacionresumendiariodetalle 
                    WHERE s_facturacionresumendiariodetalle.idfacturacionnotacredito =  s_facturacionnotacredito.id)
                    ELSE '0'
                    END AS cantidad_anulado",
                    
                )
                ->selectRaw("s_facturacionnotacredito.notacredito_tipodocafectado as documento_afectado");

            $facturacionnotadebito = DB::table('s_facturacionnotadebito')
                ->where('s_facturacionnotadebito.idtienda', $idtienda)
                ->where('s_facturacionnotadebito.idsucursal', Auth::user()->idsucursal)
                ->leftJoin('users as responsable','responsable.id','s_facturacionnotadebito.idusuarioresponsable')
                ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacionnotadebito','s_facturacionnotadebito.id')
                ->select(
                    's_facturacionnotadebito.id',
                    's_facturacionnotadebito.notadebito_fechaemision as fechaemision',
                    's_facturacionnotadebito.notadebito_serie as serie',
                    's_facturacionnotadebito.notadebito_correlativo as correlativo',
                    's_facturacionnotadebito.notadebito_tipodocumento as tipodocumento',
                    's_facturacionnotadebito.notadebito_montoimpuestoventa as total',
                    's_facturacionnotadebito.cliente_numerodocumento as ruc_cliente',
                    's_facturacionnotadebito.cliente_razonsocial as razonsocial_cliente',
                    's_facturacionnotadebito.emisor_ruc as ruc_emisor',
                    's_facturacionnotadebito.emisor_razonsocial as razonsocial_emisor',
                    's_facturacionnotadebito.notadebito_tipomoneda as tipomoneda',
                    's_facturacionrespuesta.estado as respuestaestado',
                )
                ->selectRaw(
                    "CASE 
                    WHEN s_facturacionnotadebito.notadebito_tipodocafectado = '01' THEN (SELECT COUNT(id) AS cantidad_documento_comunicacion
                        FROM s_facturacioncomunicacionbajadetalle 
                        WHERE s_facturacioncomunicacionbajadetalle.idfacturacionnotadebito =  s_facturacionnotadebito.id)
                    WHEN s_facturacionnotadebito.notadebito_tipodocafectado = '03' THEN (SELECT COUNT(id) AS cantidad_documento_resumendiario
                        FROM s_facturacionresumendiariodetalle 
                        WHERE s_facturacionresumendiariodetalle.idfacturacionnotadebito =  s_facturacionnotadebito.id)
                    ELSE '0'
                    END AS cantidad_anulado"
                )
                ->selectRaw("s_facturacionnotadebito.notadebito_tipodocafectado as documento_afectado");

            $guiaremision  = DB::table('s_facturacionguiaremision')
                ->join('users as responsable', 'responsable.id', 's_facturacionguiaremision.idusuarioresponsable')
                ->leftJoin('users as transportista', 'transportista.id', 's_facturacionguiaremision.idusuariochofer')
                ->join('s_sunat_motivotraslado', 's_sunat_motivotraslado.codigo', 's_facturacionguiaremision.envio_modtraslado')
                ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacionguiaremision','s_facturacionguiaremision.id')
                ->where('s_facturacionguiaremision.idtienda', $idtienda)
                ->where('s_facturacionguiaremision.idsucursal', Auth::user()->idsucursal)
                ->select(
                    's_facturacionguiaremision.id',
                    's_facturacionguiaremision.despacho_fechaemision as fechaemision',
                    's_facturacionguiaremision.despacho_serie as serie',
                    's_facturacionguiaremision.despacho_correlativo as correlativo',
                    's_facturacionguiaremision.despacho_tipodocumento as tipodocumento',
                    DB::raw("'0' AS total"),
                    's_facturacionguiaremision.despacho_destinatario_numerodocumento as ruc_cliente',
                    's_facturacionguiaremision.despacho_destinatario_razonsocial as razonsocial_cliente',
                    's_facturacionguiaremision.emisor_ruc as ruc_emisor',
                    's_facturacionguiaremision.emisor_razonsocial as razonsocial_emisor',
                    DB::raw("'S/N' AS tipomoneda"),
                    's_facturacionrespuesta.estado as respuestaestado',
                )
                ->selectRaw("'0' AS cantidad_anulado")
                ->selectRaw("'00' AS documento_afectado");

            $facturacion_electronica = $facturacionboletafactura
                                        ->union($facturacionnotacredito)
                                        ->union($facturacionnotadebito)
                                        ->union($guiaremision)
                                        ->orderBy('fechaemision', 'desc')
                                        ->paginate($request->length,'*',null,($request->start/$request->length)+1);
            
            
            $tabla = [];
            foreach($facturacion_electronica as $value){
            
                $facturacionnotacredito = DB::table('s_facturacionnotacredito')
                        ->where('s_facturacionnotacredito.idfacturacionboletafactura',$value->id)
                        ->select(
                            's_facturacionnotacredito.notacredito_serie',
                            's_facturacionnotacredito.notacredito_correlativo'
                        )
                        ->get();
            
                $nota_credito_doc = '';
                $nota_debito_doc = '';
                
                // $fecha_emi  = date_format(date_create($value->fechaemision), 'd/m/Y h:i:s A');
                $comprobante = '';
                $serie_numero = $value->serie.'-'.str_pad($value->correlativo, 8, "0", STR_PAD_LEFT);
                $opcion = [];
                switch($value->tipodocumento){
                    case '01':
                        $comprobante = 'FACTURA';
                        break;
                    case '03':
                        $comprobante  = 'BOLETA';
                            break;
                    case '07':
                        $comprobante  = 'NOTA DE CRÉDITO';
                            break;
                    case '08':
                        $comprobante  = 'NOTA DE DÉBITO';
                            break;
                    case '09':
                        $comprobante  = 'GUIA DE REMISIÓN';
                            break;

                }
                $estado='';
                $estadoAnulado='';
                $opcionAnular = [];
                $opcionReenviar = [];
                $opcionComprobante = [];
                $opcionNotaCredito = [];
                $opcionNotaDebito = [];
                $opcionGuiaRemision = [];
                if($value->tipodocumento=='01' || $value->tipodocumento == '03'){
                    foreach($facturacionnotacredito as $nc_value){
                        $nota_credito_doc .= $nc_value->notacredito_serie.'-'.$nc_value->notacredito_correlativo.'<br>';
                    }
                
                    $facturacionnotadebito = DB::table('s_facturacionnotadebito')
                            ->where('s_facturacionnotadebito.idfacturacionboletafactura',$value->id)
                            ->select(
                                's_facturacionnotadebito.notadebito_serie',
                                's_facturacionnotadebito.notadebito_correlativo'
                            )
                            ->get();
                
                    
                    foreach($facturacionnotadebito as $nd_value){
                        $nota_debito_doc .= $nd_value->notadebito_serie.'-'.$nd_value->notadebito_correlativo.'<br>';
                    }
                    $opcionNotaCredito = [
                        'nombre' => 'Nota de Crédito',
                        'onclick' => '/'.$idtienda.'/facturacionboletafactura/create?view=emision_notacredito&idcomprobante='.$value->id.'&modulo=facturacionnotacredito',
                        'icono' => 'receipt',
                    ];
                    $opcionNotaDebito = [
                        'nombre' => 'Nota de Débito',
                        'onclick' => '/'.$idtienda.'/facturacionboletafactura/create?view=emision_notadebito&idcomprobante='.$value->id.'&modulo=facturacionnotadebito',
                        'icono' => 'receipt',
                    ];
                    $opcionGuiaRemision = [
                        'nombre'  => 'Guia de remisión',
                        'onclick' => '/'.$idtienda.'/facturacionboletafactura/create?view=emision_guia&idcomprobante='.$value->id.'&modulo=facturacionguiaremision',
                        'icono'   => 'receipt',
                    ];

                    $opcionComprobante = [
                        'nombre' => 'Comprobante',
                        'onclick' => '/'.$idtienda.'/facturacionboletafactura/'.$value->id.'/edit?view=ticket',
                        'icono' => 'receipt',
                    ];
                    switch($value->respuestaestado){
                        case 'ACEPTADA':
                            $estado = 'ACEPTADA';
                            if ($value->cantidad_anulado > 0) {
                                $estadoAnulado = 'ANULADO';
                                $opcionNotaCredito = [];
                                $opcionNotaDebito = [];
                                $opcionGuiaRemision = [];
                            }
                        
                            if ($value->tipodocumento == '01' && $value->cantidad_anulado <= 0) {
                                $opcionAnular = [
                                    'nombre' => 'Anular',
                                    'onclick' => '/'.$idtienda.'/facturacionboletafactura/'.$value->id.'/edit?view=anular_comunicacionbaja',
                                    'icono' => 'receipt',
                                ];
                            }
                        
                            if ($value->tipodocumento == '03' && $value->cantidad_anulado <= 0) {
                                $opcionAnular = [
                                    'nombre' => 'Anular',
                                    'onclick' => '/'.$idtienda.'/facturacionboletafactura/'.$value->id.'/edit?view=anular_resumendiario',
                                    'icono' => 'receipt',
                                ];
                            }
                            break;
                        case 'OBSERVACIONES':
                            $estado  = 'OBSERVACIONES';
                            break;
                        case 'RECHAZADA':
                            $estado  = 'RECHAZADA';
                            $opcionReenviar = [
                                'nombre' => 'Reenviar',
                                'onclick' => '/'.$idtienda.'/facturacionboletafactura/'.$value->id.'/edit?view=reenviar',
                                'icono' => 'receipt',
                            ];
                            break;
                        case 'EXCEPCION':
                            $estado  = 'EXCEPCIÓN';
                            break;
                        default:
                            $estado  = 'NO ENVIADO';
                            $opcionReenviar = [
                                'nombre' => 'Reenviar',
                                'onclick' => '/'.$idtienda.'/facturacionboletafactura/'.$value->id.'/edit?view=reenviar',
                                'icono' => 'receipt',
                            ];
                    }
                }
                else if($value->tipodocumento=='07'){
                  

                    $NotaCreditoController = app()->make(FacturacionNotacreditoController::class);
                    $request_nota = new Request(); 
                    $request_nota->merge(['view' => 'edit']);
                    // dump($NotaCreditoController->edit($request_nota, $idtienda ,  ));
                  
                    $opcionComprobante = [
                        'nombre' => 'Comprobantes',
                        'onclick' => '/'.$idtienda.'/facturacionboletafactura/'.$value->id.'/edit?view=ticketnc',
//                         'onclick' => '/'.$idtienda.'/facturacionnotacredito/'.$value->id.'/edit?view=ticket',
                        'icono' => 'receipt',
                    ];
                    switch($value->respuestaestado){
                        case 'ACEPTADA':
                            $estado = 'Aceptada';
                            if ($value->cantidad_anulado > 0) {
                                $estadoAnulado = 'ANULADO';
                                $estado = 'ANULADO';
                                $opcionAnular = [];
                            }
                            if ($value->documento_afectado == '01' && $value->cantidad_anulado <= 0) {
                                $opcionAnular = [
                                    'nombre' => 'Anular NC',
                                    'onclick' => '/'.$idtienda.'/facturacionnotacredito/'.$value->id.'/edit?view=anular_comunicacionbaja',
                                    'icono' => 'receipt',
                                ];
                            }
    
                            if ($value->documento_afectado == '03' && $value->cantidad_anulado <= 0) {
                                $opcionAnular = [
                                    'nombre' => 'Anular NC',
                                    'onclick' => '/'.$idtienda.'/facturacionnotacredito/'.$value->id.'/edit?view=anular_resumendiario',
                                    'icono' => 'receipt',
                                ];
                            }
                            break;
                        case 'OBSERVACIONES':
                            $estado  = 'Observaciones';
                            break;
                        case 'RECHAZADA':
                            $estado  = 'Rechazada';
                            break;
                        case 'EXCEPCION':
                            $estado  = 'Excepción';
                            break;
                        default:
                            $estado  = 'No enviado';
                    }
                }
                else if($value->tipodocumento=='08'){
                    $opcionComprobante = [
                        'nombre' => 'Comprobantes',
                        'onclick' => '/'.$idtienda.'/facturacionnotadebito/'.$value->id.'/edit?view=ticket',
                        'icono' => 'receipt',
                    ];
                    switch($value->respuestaestado){
                        case  'ACEPTADA':
                        $estado =  'Aceptada';
                        if ($value->cantidad_anulado > 0) {
                            $estadoAnulado = 'Anulado';
                            $estado = 'Anulado';
                            $opcionAnular = [];
                        }
                        if ($value->documento_afectado == '01' && $value->cantidad_anulado <= 0) {
                            $opcionAnular = [
                                'nombre' => 'Anular ND',
                                'onclick' => '/'.$idtienda.'/facturacionnotadebito/'.$value->id.'/edit?view=anular_comunicacionbaja',
                                'icono' => 'receipt',
                            ];
                        }
    
                            if ($value->documento_afectado == '03' && $value->cantidad_anulado <= 0) {
                                $opcionAnular = [
                                    'nombre' => 'Anular ND',
                                    'onclick' => '/'.$idtienda.'/facturacionnotadebito/'.$value->id.'/edit?view=anular_resumendiario',
                                    'icono' => 'receipt',
                                ];
                            }
                            break;
                        case 'OBSERVACIONES':
                            $estado = 'Observaciones';
                            break;
                        case 'RECHAZADA':
                            $estado =  'Rechazada';
                            break;
                        case 'EXCEPCION':
                            $estado =  'Excepción';
                            break;
                        default:
                            $estado = 'No enviado';
                    }
                }
                else if($value->tipodocumento=='09'){
                    $opcionComprobante = [
                        'nombre' => 'Comprobantes',
                        'onclick' => '/'.$idtienda.'/facturacionboletafactura/'.$value->id.'/edit?view=ticket_gre',
                        //'onclick' => '/'.$idtienda.'/facturacionguiaremision/'.$value->id.'/edit?view=ticket',
                        'icono' => 'receipt',
                    ];
                }
                $simbolo_moneda = '';
                switch($value->tipomoneda){
                    case 'PEN':
                        $simbolo_moneda = 'S/';
                        break;
                    case 'USD':
                        $simbolo_moneda  = '$';
                            break;
                    default:
                        $simbolo_moneda  = '';
                            break;

                }

                $tabla[] = [
                    'id'  => $value->id,
                    'fecha_emision' => date_format(date_create($value->fechaemision), 'd/m/Y h:i:s A'),
                    'comprobante' => $comprobante.'<br>'.$serie_numero,
                    'moneda' => $simbolo_moneda,
                    'total' => $simbolo_moneda.' '.$value->total,
                    'cliente' => $value->ruc_cliente.' - '.$value->razonsocial_cliente,
                    'emisor' => $value->ruc_emisor.' - '.$value->razonsocial_emisor,
                    'estado_envio' => $estado.'<br>'.$estadoAnulado,
                    'nota_credito' => $nota_credito_doc,
                    'nota_debito' => $nota_debito_doc,
                    'opcion' => [
                        $opcionComprobante,
                        $opcionReenviar,
                        $opcionAnular,
                        $opcionGuiaRemision,
                        $opcionNotaCredito,
                        $opcionNotaDebito,
                    ]
                ];
            }
            return response()->json([
                'start'           => $request->start,
                'draw'            => $request->draw,
                'recordsTotal'    => $request->length,
                'recordsFiltered' => $facturacion_electronica->total(),
                'data'            => $tabla,
            ]);
        }
        else if($id == 'show_clientes'){
            // dump($request->buscar);
            $usuarios = DB::table('users')
                ->join('s_tipopersona','s_tipopersona.id','=','users.idtipopersona')
                ->leftJoin('s_ubigeo','s_ubigeo.id','=','users.idubigeo')
                ->where('users.idestado',1)
                ->where('users.idtienda',$idtienda)
                
                ->where(DB::raw("CONCAT(users.identificacion, ' - ', users.nombrecompleto)"), 'LIKE', '%' . $request->input('buscar') . '%')
                ->select(
                    'users.*',
                    's_tipopersona.nombre as tipopersonanombre',
                    's_ubigeo.codigo as ubigeocodigo',
                    's_ubigeo.nombre as ubigeonombre',
                )
                ->orderBy('users.id','desc')
                ->get();
    
            $tabla = [];
            foreach($usuarios as $value){
            
                $tabla[] = [
                    'id'              => $value->id,
                    'text'            => ($value->identificacion!=0?$value->identificacion.' - ':'').$value->nombrecompleto,
                    'idtipopersona'   => $value->idtipopersona,
                    'persona'         => $value->tipopersonanombre,
                    'identificacion'  => $value->identificacion!=0?$value->identificacion:'',
                    'cliente'         => $value->nombrecompleto,
                    'telefono'        => $value->numerotelefono,
                    'direccion'       => $value->direccion,
                    'idubigeo'        => $value->idubigeo,
                ];
            }
    
    
            return $tabla;
          }

    }

    public function edit(Request $request, $idtienda, $id)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $facturacionboletafactura = DB::table('s_facturacionboletafactura')
            // ->join('users as responsable','responsable.id','s_facturacionboletafactura.idusuarioresponsable')
            ->join('s_agencia','s_agencia.id','s_facturacionboletafactura.idagencia')
            ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacionboletafactura.idfacturacionrespuesta')
            ->leftJoin('s_facturacioncomunicacionbajadetalle','s_facturacioncomunicacionbajadetalle.idfacturacionboletafactura','s_facturacionboletafactura.id')
            ->leftJoin('s_facturacioncomunicacionbaja','s_facturacioncomunicacionbaja.id','s_facturacioncomunicacionbajadetalle.idfacturacioncomunicacionbaja')
            ->leftJoin('s_facturacionresumendiariodetalle','s_facturacionresumendiariodetalle.idfacturacionboletafactura','s_facturacionboletafactura.id')
            ->leftJoin('s_facturacionresumendiario','s_facturacionresumendiario.id','s_facturacionresumendiariodetalle.idfacturacionresumendiario')
            ->where('s_facturacionboletafactura.id',$id)
            ->select(
                's_facturacionboletafactura.*',
                's_facturacionboletafactura.db_idusersresponsable as responsablenombre',
                's_agencia.logo as agencialogo',
                's_facturacionrespuesta.codigo as respuestacodigo',
                's_facturacionrespuesta.estado as respuestaestado',
                's_facturacionrespuesta.mensaje as respuestamensaje',
                's_facturacionrespuesta.nombre as respuestanombre',
                's_facturacionrespuesta.qr as respuestaqr',
                 DB::raw('CONCAT(s_facturacionboletafactura.cliente_numerodocumento," - ",s_facturacionboletafactura.cliente_razonsocial) as cliente'),
                 DB::raw('CONCAT(s_facturacionboletafactura.cliente_departamento, " , ", s_facturacionboletafactura.cliente_provincia, " , ", s_facturacionboletafactura.cliente_distrito) as ubigeo'),
                 DB::raw('CONCAT(s_facturacionboletafactura.emisor_ruc, " - ", s_facturacionboletafactura.emisor_nombrecomercial) as agencia'),
                's_facturacioncomunicacionbaja.comunicacionbaja_correlativo as comunicacionbaja_correlativo',
                's_facturacionresumendiariodetalle.estado as resumen_estado',
                's_facturacionresumendiario.resumen_correlativo as resumen_correlativo'
              )
              ->selectRaw(
                "CASE 
                 WHEN s_facturacionboletafactura.venta_tipodocumento = '01' THEN (SELECT COUNT(id) AS cantidad_documento_comunicacion
                  FROM s_facturacioncomunicacionbajadetalle 
                  WHERE s_facturacioncomunicacionbajadetalle.idfacturacionboletafactura =  s_facturacionboletafactura.id)
                 WHEN s_facturacionboletafactura.venta_tipodocumento = '03' THEN (SELECT COUNT(id) AS cantidad_documento_resumendiario
                  FROM s_facturacionresumendiariodetalle 
                  WHERE s_facturacionresumendiariodetalle.idfacturacionboletafactura =  s_facturacionboletafactura.id)
                 ELSE '0'
                 END AS cantidad_anulado"
              )
            ->first();
      
        if($request->input('view') == 'detalle') {
            $boletafacturadetalle     = DB::table('s_facturacionboletafacturadetalle')
                ->join('s_producto','s_producto.id','s_facturacionboletafacturadetalle.idproducto')
                ->where('s_facturacionboletafacturadetalle.idfacturacionboletafactura',$facturacionboletafactura->id)
                ->select(
                    's_facturacionboletafacturadetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->orderBy('s_facturacionboletafacturadetalle.id','asc')
                ->get();
            return view(sistema_view().'/facturacionboletafactura/detalle',[
                'facturacionboletafactura'=> $facturacionboletafactura,
                'boletafacturadetalle'    => $boletafacturadetalle,
                'tienda'                  => $tienda
            ]);

        }
        elseif($request->input('view') == 'ticket') {
            

            return view(sistema_view().'/facturacionboletafactura/ticket',[
                'tienda' => $tienda,
                'facturacionboletafactura'=> $facturacionboletafactura
            ]);
        }
        
        elseif($request->input('view') == 'ticketpdf') {
            $boletafacturadetalle     = DB::table('s_facturacionboletafacturadetalle')
                    ->join('s_producto','s_producto.id','s_facturacionboletafacturadetalle.idproducto')
                    ->where('s_facturacionboletafacturadetalle.idfacturacionboletafactura',$facturacionboletafactura->id)
                    ->select(
                        's_facturacionboletafacturadetalle.*',
                        's_producto.codigo as productocodigo',
                        's_producto.nombre as productonombre'
                    )
                    ->orderBy('s_facturacionboletafacturadetalle.id','asc')
                    ->get();

                //comida
                $comida_venta = '';
                if($tienda->idcategoria==30){
                    $comida_venta = DB::table('s_comida_ordenpedidoventa')
                    ->join('s_comida_ordenpedido','s_comida_ordenpedido.id','s_comida_ordenpedidoventa.s_idcomida_ordenpedido')
                    ->join('users as mesero','mesero.id','s_comida_ordenpedido.idresponsable')
                    ->where('s_comida_ordenpedidoventa.s_idventa',$facturacionboletafactura->idventa)
                    ->select(
                        'mesero.nombre as mesero_nombre',
                        's_comida_ordenpedido.numeromesa as numeromesa',
                    )
                    ->first();
                }
            
            $ticket = new \stdClass();
            //DATOS EMISOR
            $ticket->tipo_fuente = configuracion($tienda->id,'sistema_tipoletra')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_tipoletra')['valor']:'Helvetica';
            $ticket->ancho_ticket = configuracion($tienda->id,'sistema_anchoticket')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_anchoticket')['valor']:'7';
            $ticket->ruc_emision = $facturacionboletafactura->emisor_ruc;
            $ticket->razonsocial_emisor = strtoupper($facturacionboletafactura->emisor_razonsocial);
            $ticket->logotipo = url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$facturacionboletafactura->agencialogo);
            $ticket->direccion_emisor = strtoupper($facturacionboletafactura->emisor_direccion);
            $ticket->ubigeo_emisor = strtoupper($facturacionboletafactura->emisor_distrito.' - '.$facturacionboletafactura->emisor_provincia.' - '.$facturacionboletafactura->emisor_departamento);
            
            $ticket->tipo_documento = getTipoDocumento( $facturacionboletafactura->venta_tipodocumento );
            $ticket->serie_documento = $facturacionboletafactura->venta_serie;
            $ticket->correlativo_documento = $facturacionboletafactura->venta_correlativo;
            $ticket->estado_cpe = $facturacionboletafactura->cantidad_anulado;
            $ticket->fechaemision = date_format(date_create($facturacionboletafactura->venta_fechaemision),"d/m/Y h:i:s A");
            $ticket->moneda = getTipoMoneda( $facturacionboletafactura->venta_tipomoneda );
            $ticket->simbolo_moneda = $facturacionboletafactura->venta_tipomoneda == 'PEN' ? " S/ " : "";
            if($comida_venta != ''){
                $ticket->nromesa  = strtoupper($comida_venta->numeromesa);
                $ticket->mesero   = strtoupper($comida_venta->mesero_nombre);
            }
            
            
            $ticket->razonsocial_cliente = strtoupper($facturacionboletafactura->cliente_razonsocial) ;
            $ticket->documento_cliente = $facturacionboletafactura->cliente_numerodocumento ;
            $ticket->direccion_cliente = strtoupper($facturacionboletafactura->cliente_direccion) ;
            $ticket->ubigeo_cliente = $facturacionboletafactura->cliente_departamento != 'NONE' ? strtoupper($facturacionboletafactura->cliente_distrito.' - '.$facturacionboletafactura->cliente_provincia.' - '.$facturacionboletafactura->cliente_departamento) : '';
            $ticket->responble_atencion = strtoupper($facturacionboletafactura->responsablenombre);
            
            $items = [];
            foreach( $boletafacturadetalle as $value ){
                $productounidadmedida = DB::table('s_unidadmedida')->where('codigo',$value->unidad)->first();
                $items[] = [
                            'codigoProducto' => 'S/N',
                            'descripcion'    => strtoupper($value->descripcion).' - '.$productounidadmedida->nombre,
                            'cantidad'       => $value->cantidad ,
                            'precio'         => $value->montopreciounitario,
                            'total'          => number_format($value->cantidad*$value->montopreciounitario, 2, '.', '')
                            ];
                
            }
            $ticket->items = $items;
            $ticket->operacion_gravada = $facturacionboletafactura->venta_valorventa;
            $ticket->igv = $facturacionboletafactura->venta_totalimpuestos;
            $ticket->total_venta = $facturacionboletafactura->venta_montoimpuestoventa;
            $ticket->leyenda = $facturacionboletafactura->leyenda_value;
            $ticket->qr = $facturacionboletafactura->respuestaqr;
            $ticket->link_consulta_cpe = url('/').'/'.$tienda->link.'/comprobante';

            $agencia = DB::table('s_agencia')->whereId($facturacionboletafactura->idagencia)->first();
            $pdf = PDF::loadView(sistema_view().'/facturacionboletafactura/ticketpdf',[
                'tienda' => $tienda,
                'ticket' => $ticket,
                'agencia' => $agencia,
            ]);
            $ticket = 'COMPROBANTE_TICKET_'.$facturacionboletafactura->emisor_ruc.'_'.$facturacionboletafactura->venta_serie.'_'.str_pad($facturacionboletafactura->venta_correlativo, 8, "0", STR_PAD_LEFT);
//             $pdf->setPaper(array(0, 0, 226.77, 1000.46), 'portrait');
            return $pdf->stream($ticket.'.pdf');
        }
        else if($request->input('view') == 'reenviar'){
            return view(sistema_view().'/facturacionboletafactura/reenviar',[
                'tienda' => $tienda,
                'facturacionboletafactura'=> $facturacionboletafactura
            ]);
        }
        
        elseif($request->input('view') == 'a4pdf') {
            $boletafacturadetalle     = DB::table('s_facturacionboletafacturadetalle')
                ->join('s_producto','s_producto.id','s_facturacionboletafacturadetalle.idproducto')
                ->where('s_facturacionboletafacturadetalle.idfacturacionboletafactura',$facturacionboletafactura->id)
                ->select(
                    's_facturacionboletafacturadetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->orderBy('s_facturacionboletafacturadetalle.id','asc')
                ->get();
            $agencia = DB::table('s_agencia')->whereId($facturacionboletafactura->idagencia)->first();
            
            $pdf = PDF::loadView(sistema_view().'/facturacionboletafactura/a4pdf',[
                'tienda'                   => $tienda,
                'facturacionboletafactura' => $facturacionboletafactura,
                'agencia' => $agencia,
                'boletafacturadetalle'     => $boletafacturadetalle,
            ]);
            $a4pdf = 'COMPROBANTE_A4_'.$facturacionboletafactura->emisor_ruc.'_'.$facturacionboletafactura->venta_serie.'_'.str_pad($facturacionboletafactura->venta_correlativo, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($a4pdf.'.pdf');
        }
        elseif($request->input('view') == 'a5pdf') {
            $boletafacturadetalle     = DB::table('s_facturacionboletafacturadetalle')
                ->join('s_producto','s_producto.id','s_facturacionboletafacturadetalle.idproducto')
                ->where('s_facturacionboletafacturadetalle.idfacturacionboletafactura',$facturacionboletafactura->id)
                ->select(
                    's_facturacionboletafacturadetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->orderBy('s_facturacionboletafacturadetalle.id','asc')
                ->get();
            $agencia = DB::table('s_agencia')->whereId($facturacionboletafactura->idagencia)->first();
            
            $pdf = PDF::loadView(sistema_view().'/facturacionboletafactura/a5pdf',[
                'tienda'                   => $tienda,
                'facturacionboletafactura' => $facturacionboletafactura,
                'agencia' => $agencia,
                'boletafacturadetalle'     => $boletafacturadetalle,
            ]);
            $a5pdf = 'COMPROBANTE_A5_'.$facturacionboletafactura->emisor_ruc.'_'.$facturacionboletafactura->venta_serie.'_'.str_pad($facturacionboletafactura->venta_correlativo, 8, "0", STR_PAD_LEFT);
            $pdf->setPaper('a4','landscape');
            
            return $pdf->stream($a5pdf.'.pdf');
            
        }
        elseif ($request->input('view') == 'anular_comunicacionbaja') {
           $agencias = DB::table('s_agencia')->where('idtienda',$idtienda)->where('idestadofacturacion',1)->get();
        
           $facturacionboletafactura = DB::table('s_facturacionboletafactura')
                    ->join('users as usuariocliente','usuariocliente.id','s_facturacionboletafactura.idusuariocliente')
                    ->where('s_facturacionboletafactura.id',$id)
                    ->select(
                        's_facturacionboletafactura.*',
                        DB::raw('IF(usuariocliente.idtipopersona=1,
                        CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombrecompleto),
                        CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombrecompleto)) as cliente')
                    )
                    ->first();   

          $facturacioncomunicacionbaja = DB::table('s_facturacioncomunicacionbajadetalle')
              ->where('s_facturacioncomunicacionbajadetalle.idfacturacionboletafactura', $facturacionboletafactura->id)
              ->first();
        
          $data =  [
            'tipo'                        => 'FACTURA',
            'id'                          => $facturacionboletafactura->id,
            'serie'                       => $facturacionboletafactura->venta_serie,
            'correlativo'                 => str_pad($facturacionboletafactura->venta_correlativo, 8, "0", STR_PAD_LEFT),
            'cliente'                     => $facturacionboletafactura->cliente,
            'emision'                     => date_format(date_create($facturacionboletafactura->venta_fechaemision), 'd-m-Y h:i:s A'),
            'moneda'                      => $facturacionboletafactura->venta_tipomoneda=='PEN'?'SOLES':'DOLARES',
            'venta_montooperaciongravada' => $facturacionboletafactura->venta_montooperaciongravada,
            'venta_montoigv'              => $facturacionboletafactura->venta_montoigv,
            'venta_montoimpuestoventa'    => $facturacionboletafactura->venta_montoimpuestoventa,
          ];
        
         return view(sistema_view().'/facturacionboletafactura/comunicacionbaja',[
              'tienda' => $tienda,
              'agencias' => $agencias,
              'facturacionboletafactura'=> $facturacionboletafactura
         ]);
        }
        elseif ($request->input('view') == 'anular_resumendiario') {
          return view(sistema_view().'/facturacionboletafactura/resumendiario',[
            'tienda' => $tienda,
            'facturacionboletafactura'=> $facturacionboletafactura
          ]);
        }
      
//         FORMATOS NOTAS DE CREDITO
        else if( $request->input('view') == 'ticketnc'){
          $NotaCreditoController = app()->make(FacturacionNotacreditoController::class);

          $request_nota = new Request(); 
          $request_nota->merge(['view' => 'ticket']);
          $res_nc = $NotaCreditoController->edit($request_nota, $tienda->id , $id );
          
          return $res_nc;
          
        }
        else if( $request->input('view') == 'ticketpdf_nc' ){
           $NotaCreditoController = app()->make(FacturacionNotacreditoController::class);

            $request_nota = new Request(); 
            $request_nota->merge(['view' => 'ticketpdf']);
            $res_nc = $NotaCreditoController->edit($request_nota, $tienda->id , $id );

            return $res_nc;
        }
        else if( $request->input('view') == 'a4pdf_nc' ){
           $NotaCreditoController = app()->make(FacturacionNotacreditoController::class);

            $request_nota = new Request(); 
            $request_nota->merge(['view' => 'a4pdf']);
            $res_nc = $NotaCreditoController->edit($request_nota, $tienda->id , $id );

            return $res_nc;
        }
        else if( $request->input('view') == 'a5pdf_nc' ){
           $NotaCreditoController = app()->make(FacturacionNotacreditoController::class);

            $request_nota = new Request(); 
            $request_nota->merge(['view' => 'a5pdf']);
            $res_nc = $NotaCreditoController->edit($request_nota, $tienda->id , $id );

            return $res_nc;
        }
        else if( $request->input('view') == 'ticket_gre' ){
           $CPEController = app()->make(FacturacionGuiaremisionController::class);

          $request_cpe = new Request(); 
          $request_cpe->merge(['view' => 'ticket']);
          $res_gre = $CPEController->edit($request_cpe, $tienda->id , $id );
          
          return $res_gre;
        }
        else if( $request->input('view') == 'ticketpdf_gre' ){
           $CPEController = app()->make(FacturacionGuiaremisionController::class);

          $request_cpe = new Request(); 
          $request_cpe->merge(['view' => 'ticketpdf']);
          $res_gre = $CPEController->edit($request_cpe, $tienda->id , $id );
          
          return $res_gre;
        }
        else if( $request->input('view') == 'a4pdf_gre' ){
           $CPEController = app()->make(FacturacionGuiaremisionController::class);

          $request_cpe = new Request(); 
          $request_cpe->merge(['view' => 'a4pdf']);
          $res_gre = $CPEController->edit($request_cpe, $tienda->id , $id );
          
          return $res_gre;
        }
        else if( $request->input('view') == 'a5pdf_gre' ){
           $CPEController = app()->make(FacturacionGuiaremisionController::class);

          $request_cpe = new Request(); 
          $request_cpe->merge(['view' => 'a5pdf']);
          $res_gre = $CPEController->edit($request_cpe, $tienda->id , $id );
          
          return $res_gre;
        }
      
      


    }

    public function update(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        
        if($request->input('view') == 'reenviarcomprobante'){

            $result = facturador_facturaboleta($id,$request->fechaemision.' '.$request->horaemision);
            return response()->json([
                'resultado' => $result['resultado'],
                'mensaje'   => $result['mensaje']
            ]);
        }
        elseif($request->input('view') == 'enviarcorreo'){
            
              $rules = [
                  'enviarcorreo_email' => 'required|email',
              ];
              $messages = [
                  'enviarcorreo_email.required' => 'El "Correo Electrónico" es Obligatorio.',
                  'enviarcorreo_email.email' => 'El "Correo Electrónico" es Invalido, ingrese otro por favor.',
              ];

              $this->validate($request,$rules,$messages);
          
          
            //   $facturacionboletafactura = DB::table('s_facturacionboletafactura as facturaboleta')
            //       ->join('users as responsable','responsable.id','facturaboleta.idusuarioresponsable')
            //       ->join('s_agencia','s_agencia.id','facturaboleta.idagencia')
            //       ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','facturaboleta.idfacturacionrespuesta')
            //       ->where('facturaboleta.id',$request->input('idfacturacionboletafactura'))
            //       ->select(
            //           'facturaboleta.*',
            //           'responsable.nombre as responsablenombre',
            //           's_agencia.logo as agencialogo',
            //           's_facturacionrespuesta.codigo as respuestacodigo',
            //           's_facturacionrespuesta.estado as respuestaestado',
            //           's_facturacionrespuesta.mensaje as respuestamensaje',
            //           's_facturacionrespuesta.nombre as respuestanombre',
            //           's_facturacionrespuesta.qr as respuestaqr',
            //            DB::raw('CONCAT(facturaboleta.cliente_numerodocumento," - ",facturaboleta.cliente_razonsocial) as cliente'),
            //            DB::raw('CONCAT(facturaboleta.cliente_departamento, " , ", facturaboleta.cliente_provincia, " , ", facturaboleta.cliente_distrito) as ubigeo'),
            //            DB::raw('CONCAT(facturaboleta.emisor_ruc, " - ", facturaboleta.emisor_nombrecomercial) as agencia')
            //         )
            //       ->first();
            //   $boletafacturadetalle = DB::table('s_facturacionboletafacturadetalle')
            //       ->join('s_producto','s_producto.id','s_facturacionboletafacturadetalle.idproducto')
            //       ->where('s_facturacionboletafacturadetalle.idfacturacionboletafactura',$facturacionboletafactura->id)
            //       ->select(
            //           's_facturacionboletafacturadetalle.*',
            //           's_producto.codigo as productocodigo',
            //           's_producto.nombre as productonombre'
            //       )
            //       ->orderBy('s_facturacionboletafacturadetalle.id','asc')
            //       ->get();
        
              $tienda = DB::table('tienda')->whereId($idtienda)->first();
          
              //comida
              $comida_venta = '';
            //   if($tienda->idcategoria==30){
            //       $comida_venta = DB::table('s_comida_ordenpedidoventa')
            //         ->join('s_comida_ordenpedido','s_comida_ordenpedido.id','s_comida_ordenpedidoventa.s_idcomida_ordenpedido')
            //         ->join('users as mesero','mesero.id','s_comida_ordenpedido.idresponsable')
            //         ->join('s_comida_mesa','s_comida_mesa.id','s_comida_ordenpedido.idmesa')
            //         ->where('s_comida_ordenpedidoventa.s_idventa',$facturacionboletafactura->idventa)
            //         ->select(
            //           'mesero.nombre as mesero_nombre',
            //           's_comida_mesa.numero_mesa as mesa_numero_mesa'
            //         )
            //         ->first();
            //   }

            //   $pdf = PDF::loadView(sistema_view().'/facturacionboletafactura/ticketpdf',[
            //       'tienda'                   => $tienda,
            //       'facturacionboletafactura' => $facturacionboletafactura,
            //       'boletafacturadetalle'     => $boletafacturadetalle,
            //       'comida_venta'             => $comida_venta
            //   ]);
            $facturacionboletafactura = DB::table('s_facturacionboletafactura')
                                    // ->join('users as responsable','responsable.id','s_facturacionboletafactura.idusuarioresponsable')
                                    ->join('s_agencia','s_agencia.id','s_facturacionboletafactura.idagencia')
                                    ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacionboletafactura.idfacturacionrespuesta')
                                    ->leftJoin('s_facturacioncomunicacionbajadetalle','s_facturacioncomunicacionbajadetalle.idfacturacionboletafactura','s_facturacionboletafactura.id')
                                    ->leftJoin('s_facturacioncomunicacionbaja','s_facturacioncomunicacionbaja.id','s_facturacioncomunicacionbajadetalle.idfacturacioncomunicacionbaja')
                                    ->leftJoin('s_facturacionresumendiariodetalle','s_facturacionresumendiariodetalle.idfacturacionboletafactura','s_facturacionboletafactura.id')
                                    ->leftJoin('s_facturacionresumendiario','s_facturacionresumendiario.id','s_facturacionresumendiariodetalle.idfacturacionresumendiario')
                                    ->where('s_facturacionboletafactura.id',$request->input('idfacturacionboletafactura'))
                                    ->select(
                                        's_facturacionboletafactura.*',
                                        's_facturacionboletafactura.db_idusersresponsable as responsablenombre',
                                        's_agencia.logo as agencialogo',
                                        's_facturacionrespuesta.codigo as respuestacodigo',
                                        's_facturacionrespuesta.estado as respuestaestado',
                                        's_facturacionrespuesta.mensaje as respuestamensaje',
                                        's_facturacionrespuesta.nombre as respuestanombre',
                                        's_facturacionrespuesta.qr as respuestaqr',
                                        DB::raw('CONCAT(s_facturacionboletafactura.cliente_numerodocumento," - ",s_facturacionboletafactura.cliente_razonsocial) as cliente'),
                                        DB::raw('CONCAT(s_facturacionboletafactura.cliente_departamento, " , ", s_facturacionboletafactura.cliente_provincia, " , ", s_facturacionboletafactura.cliente_distrito) as ubigeo'),
                                        DB::raw('CONCAT(s_facturacionboletafactura.emisor_ruc, " - ", s_facturacionboletafactura.emisor_nombrecomercial) as agencia'),
                                        's_facturacioncomunicacionbaja.comunicacionbaja_correlativo as comunicacionbaja_correlativo',
                                        's_facturacionresumendiariodetalle.estado as resumen_estado',
                                        's_facturacionresumendiario.resumen_correlativo as resumen_correlativo'
                                    )
                                    ->first();
            $boletafacturadetalle     = DB::table('s_facturacionboletafacturadetalle')
                ->join('s_producto','s_producto.id','s_facturacionboletafacturadetalle.idproducto')
                ->where('s_facturacionboletafacturadetalle.idfacturacionboletafactura',$facturacionboletafactura->id)
                ->select(
                    's_facturacionboletafacturadetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->orderBy('s_facturacionboletafacturadetalle.id','asc')
                ->get();
            $agencia = DB::table('s_agencia')->whereId($facturacionboletafactura->idagencia)->first();

            $pdfa4 = PDF::loadView(sistema_view().'/facturacionboletafactura/a4pdf',[
                'tienda'                   => $tienda,
                'facturacionboletafactura' => $facturacionboletafactura,
                'agencia'                  => $agencia,
                'boletafacturadetalle'     => $boletafacturadetalle,
            ]);
          
            //   $output = $pdf->output();
            $output_pdfa4 = $pdfa4->output();
        
            $comprobante = '';
            if($facturacionboletafactura->venta_tipodocumento=='03'){
                $comprobante = 'BOLETA';
            }elseif($facturacionboletafactura->venta_tipodocumento=='01'){
                $comprobante = 'FACTURA';
            }

            $user = array (
                'correo'       => 'ventas@kayllapi.com',
                'nombre'       => strtoupper($facturacionboletafactura->emisor_nombrecomercial),
                'correo_destino' => $request->input('enviarcorreo_email'),
                'titulo'       => $comprobante.' '.$facturacionboletafactura->venta_serie.'-'.str_pad($facturacionboletafactura->venta_correlativo, 6, "0", STR_PAD_LEFT),
                //  'pdf' => $output,
                'nombrepdf'    => $comprobante.'_'.$facturacionboletafactura->venta_serie.'_'.str_pad($facturacionboletafactura->venta_correlativo, 6, "0", STR_PAD_LEFT).'.pdf',
                'pdfa4'        => $output_pdfa4,
                'nombrepdf_a4' => $comprobante.'_'.$facturacionboletafactura->venta_serie.'_'.str_pad($facturacionboletafactura->venta_correlativo.'_a4', 6, "0", STR_PAD_LEFT).'.pdf',
                'xml'          => 'public/backoffice/tienda/'.$tienda->id.'/sunat/produccion/boletafactura/'.$facturacionboletafactura->respuestanombre.'.xml',
            );
          
            Mail::send('app/email_comprobante',  [
                'user' => $user,
                'tienda' => $tienda,
                'facturacionboletafactura' => $facturacionboletafactura,
                'boletafacturadetalle' => $boletafacturadetalle,
            ], function ($message) use ($user) {
                $message->from($user['correo'],$user['nombre']);
                $message->to($user['correo_destino'])->subject($user['titulo']);
                $message->attach($user['xml']);
            //   $message->attachData($user['pdf'], $user['nombrepdf'], [ 'mime' => 'application/pdf' ]);
                $message->attachData($user['pdfa4'], $user['nombrepdf_a4'], [ 'mime' => 'application/pdf' ]);
            });


            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha enviado correctamente.'
            ]);
        }  
        else if( $request->input('view') == 'validar_guia' ){
          
          $result = facturador_guiaremision_api($id);
          return [
            'resultado' => $result['tipo'],
            'mensaje'   => $result['mensaje'],
            'idfacturacionguiaremision' => $id
          ];
        }
    }


    public function destroy(Request $request, $idtienda, $id)
    {
       $request->user()->authorizeRoles($request->path(),$idtienda);
    }
}
