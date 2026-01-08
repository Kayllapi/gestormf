<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;
use PDF;
use NumeroALetras;
use Mail;
use App\User;
use Hash;

class FacturacionNotadebitoController extends Controller
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
        json_facturacionnotadebito($idtienda,$request->name_modulo);
        return view('layouts/backoffice/tienda/nuevosistema/facturacionnotadebito/index', [
            'tienda'                 => $tienda,
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
        $tienda         = DB::table('tienda')->whereId($idtienda)->first();  
        $configuracion  = configuracion_facturacion($idtienda);
        $agencias = DB::table('s_agencia')->where('idtienda',$idtienda)->where('idestadofacturacion',1)->get();
        return view('layouts/backoffice/tienda/nuevosistema/facturacionnotadebito/create',[
            'tienda'        => $tienda,
            'configuracion' => $configuracion,
            'agencias'      => $agencias
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
        if($request->input('view') == 'registrar') {
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
                        'mensaje'   => 'La cantidad del producto "'.$producto->nombre.'" de "NOTA DE CRÉDITO" debe ser menor o igual a la cantidad de "FACTURA/BOLETA".'
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
                    'idfacturacionnotadebito'           => $idfacturacionnotadebito
                ]);
            }
          
            $result = facturador_notadebito($idfacturacionnotadebito);
            
            return [
                'resultado' => $result['resultado'],
                'mensaje'   => $result['mensaje']
            ];
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
        if($id == 'show-seleccionarboletafactura'){
            $facturacionboletafactura = DB::table('s_facturacionboletafactura')
                ->where([
                    ['s_facturacionboletafactura.idtienda', $idtienda],
                    ['s_facturacionboletafactura.idagencia', $request->input('idagencia')],
                    ['s_facturacionboletafactura.venta_serie', $request->input('facturador_serie')],
                    ['s_facturacionboletafactura.venta_correlativo', $request->input('facturador_correlativo')]
                ])
                ->select(
                    's_facturacionboletafactura.*',
                    DB::raw('DATE_FORMAT(s_facturacionboletafactura.venta_fechaemision, "%d/%m/%Y %h:%i:%s %p") as venta_fechaemision'),
                )
                ->first();
          
            if ( !is_null($facturacionboletafactura) ) {

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
                              <td>'.$value->codigoproducto.'</td>
                              <td>'.$value->descripcion.'</td>
                              <td><input class="form-control" type="text" value="'.$restante.'" disabled></td>                                
                              <td><input class="form-control" type="number" value="'.$value->montopreciounitario.'" step="0.01" min="0" disabled></td> 
                              <td><input class="form-control" id="productCant'.$value->id.'" type="number" value="'.$restante.'" onkeyup="calcularmonto()"></td>                                
                              <td><input class="form-control" id="productUnidad'.$value->id.'" type="number" value="'.$value->montopreciounitario.'" step="0.01" min="0" disabled></td>                        
                              <td><input class="form-control"   id="productTotal'.$value->id.'" type="text" value="'.number_format($restante*$value->montopreciounitario,2, '.', '').'" step="0.01" min="0" disabled></td>                                      
                              <td class="with-btn" width="10px"><a id="'.$value->id.'" href="javascript:;" onclick="eliminarproducto('.$value->id.')" class="btn btn-danger big-btn"><i class="fas fa-trash-alt"></i> Quitar</a></td>
                              </tr>';
                    }
                        
                }
                return [ 
                  'facturacionboletafactura'  => $facturacionboletafactura,
                  'facturacionboletafacturadetalle' => $html_detalle,
                ];
              
            } else {
                return [ 'facturacionboletafactura' => $facturacionboletafactura ];
            }
          
        }
        elseif($id == 'show-selecionarserie'){
            $agencia = DB::table('s_agencia')->whereId($request->input('idagencia'))->first();
            $agenciaoption = '';
            if($agencia!=''){
                $facturacion_serie = str_pad($agencia->facturacion_serie, 3, "0", STR_PAD_LEFT);
                $agenciaoption = '<option></option>
                                  <option value="B'.$facturacion_serie.'">B'.$facturacion_serie.'</option>
                                  <option value="F'.$facturacion_serie.'">F'.$facturacion_serie.'</option>';
            }
            return [ 'agenciaoption' => $agenciaoption ];
        }elseif($id == 'show-moduloactualizar'){
               json_facturacionnotadebito($idtienda,$request->name_modulo);


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

        $facturacionnotadebito = DB::table('s_facturacionnotadebito')
            ->join('users as responsable','responsable.id','s_facturacionnotadebito.idusuarioresponsable')
            ->join('s_agencia','s_agencia.id','s_facturacionnotadebito.idagencia')
            ->where('s_facturacionnotadebito.id',$id)  
            ->select(
                's_facturacionnotadebito.*',
                'responsable.nombre as responsablenombre',
                's_agencia.logo as agencialogo',
            )
            ->first();
       
        if($request->input('view') == 'detalle') {
            $facturacionnotadebitodetalles = DB::table('s_facturacionnotadebitodetalle')
              ->join('s_producto','s_producto.id','s_facturacionnotadebitodetalle.idproducto')
              ->where('s_facturacionnotadebitodetalle.idfacturacionnotadebito',$facturacionnotadebito->id)
              ->select(
                's_facturacionnotadebitodetalle.*',
                's_producto.codigo as productocodigo',
                's_producto.nombre as productonombre'
              )
              ->orderBy('s_facturacionnotadebitodetalle.id','asc')
              ->get();
            return view('layouts/backoffice/tienda/nuevosistema/facturacionnotadebito/detalle',[
                'tienda'                              => $tienda,
                'facturacionnotadebito'              => $facturacionnotadebito,
                'facturacionnotadebitodetalles'      => $facturacionnotadebitodetalles,
            ]);

        }
        elseif($request->input('view') == 'ticket') {
            return view('layouts/backoffice/tienda/nuevosistema/facturacionnotadebito/ticket',[
                'tienda'                   => $tienda,
                'facturacionnotadebito' => $facturacionnotadebito
            ]);
        }elseif($request->input('view') == 'ticketpdf') {

            $facturacionnotadebitodetalles = DB::table('s_facturacionnotadebitodetalle')
                ->join('s_producto','s_producto.id','s_facturacionnotadebitodetalle.idproducto')
                ->where('s_facturacionnotadebitodetalle.idfacturacionnotadebito',$facturacionnotadebito->id)
                ->select(
                    's_facturacionnotadebitodetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->orderBy('s_facturacionnotadebitodetalle.id','asc')
                ->get();
            $facturacionrespuesta = DB::table('s_facturacionrespuesta')
                  ->where('s_facturacionrespuesta.s_idfacturacionnotadebito',$facturacionnotadebito->id)
                  ->first();
            $configuracion = configuracion_facturacion($idtienda);

            $pdf = PDF::loadView('layouts/backoffice/tienda/nuevosistema/facturacionnotadebito/ticketpdf',[
                'tienda' => $tienda,
                'facturacionnotadebito' => $facturacionnotadebito,
                'notadebitodetalle' => $facturacionnotadebitodetalles,
                'configuracion' => $configuracion,
                'respuesta' => $facturacionrespuesta
            ]);
            $ticket = 'Ticket_'.str_pad($facturacionnotadebito->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');
        }
        elseif($request->input('view') == 'a4pdf') {
            $facturacionnotadebitodetalles = DB::table('s_facturacionnotadebitodetalle')
                ->join('s_producto','s_producto.id','s_facturacionnotadebitodetalle.idproducto')
                ->where('s_facturacionnotadebitodetalle.idfacturacionnotadebito',$facturacionnotadebito->id)
                ->select(
                    's_facturacionnotadebitodetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->orderBy('s_facturacionnotadebitodetalle.id','asc')
                ->get();
            $facturacionrespuesta = DB::table('s_facturacionrespuesta')
                  ->where('s_facturacionrespuesta.s_idfacturacionnotadebito',$facturacionnotadebito->id)
                  ->first();
            $configuracion = configuracion_facturacion($idtienda);
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/nuevosistema/facturacionnotadebito/a4pdf',[
                'tienda' => $tienda,
                'facturacionnotadebito' => $facturacionnotadebito,
                'notadebitodetalle' => $facturacionnotadebitodetalles,
                'configuracion' => $configuracion,
                'respuesta' => $facturacionrespuesta
            ]);
            $a4pdf = 'PDF_A4_'.str_pad($facturacionnotadebito->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($a4pdf.'.pdf');
        }
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
        $request->user()->authorizeRoles($request->path(),$idtienda);
        
        if($request->input('view') == 'reenviarcomprobante'){

            $result = facturador_notadebito($id);

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
          
          
              $facturacionnotadebito = DB::table('s_facturacionnotadebito')
                  ->join('users as responsable','responsable.id','s_facturacionnotadebito.idusuarioresponsable')
                  ->join('s_agencia','s_agencia.id','s_facturacionnotadebito.idagencia')
                  ->where('s_facturacionnotadebito.id',$request->input('idfacturacionnotadebito'))
                  ->select(
                      's_facturacionnotadebito.*',
                      'responsable.nombre as responsablenombre',
                      's_agencia.logo as agencialogo',
                       DB::raw('CONCAT(s_facturacionnotadebito.cliente_numerodocumento," - ",s_facturacionnotadebito.cliente_razonsocial) as cliente'),
                       DB::raw('CONCAT(s_facturacionnotadebito.cliente_departamento, " , ", s_facturacionnotadebito.cliente_provincia, " , ", s_facturacionnotadebito.cliente_distrito) as ubigeo'),
                       DB::raw('CONCAT(s_facturacionnotadebito.emisor_ruc, " - ", s_facturacionnotadebito.emisor_nombrecomercial) as agencia')
                    )
                  ->first();
              $notadebitodetalle = DB::table('s_facturacionnotadebitodetalle')
                  ->join('s_producto','s_producto.id','s_facturacionnotadebitodetalle.idproducto')
                  ->where('s_facturacionnotadebitodetalle.idfacturacionnotadebito',$facturacionnotadebito->id)
                  ->select(
                      's_facturacionnotadebitodetalle.*',
                      's_producto.codigo as productocodigo',
                      's_producto.nombre as productonombre'
                  )
                  ->orderBy('s_facturacionnotadebitodetalle.id','asc')
                  ->get();
              $facturacionrespuesta = DB::table('s_facturacionrespuesta')
                  ->where('s_facturacionrespuesta.s_idfacturacionnotadebito',$facturacionnotadebito->id)
                  ->first();
          
          
              if($facturacionrespuesta==''){
                  return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No se puede enviar un comprobante con error, revise por favor.'
                  ]);
              }
          
              $tienda = DB::table('tienda')->whereId($idtienda)->first();
              $configuracion = configuracion_facturacion($idtienda);

              $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionnotadebito/ticketpdf',[
                  'tienda'                    => $tienda,
                  'facturacionnotadebito'    => $facturacionnotadebito,
                  'notadebitodetalle'        => $notadebitodetalle,
                  'configuracion'             => $configuracion,
                  'respuesta'                 => $facturacionrespuesta,
              ]);
          
              $a4pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionnotadebito/a4pdf',[
                  'tienda' => $tienda,
                  'facturacionnotadebito' => $facturacionnotadebito,
                  'notadebitodetalle' => $notadebitodetalle,
                  'configuracion' => $configuracion,
                  'respuesta' => $facturacionrespuesta
              ]);
          
              $output = $pdf->output();
              $a4_output = $a4pdf->output();

              $user = array (
                 'correo' => 'ventas@kayllapi.com',
                 'nombre' => strtoupper($facturacionnotadebito->emisor_nombrecomercial),
                 'correo_destino' => $request->input('enviarcorreo_email'),
                 'titulo' => 'NOTA DE DEBITO '.$facturacionnotadebito->notadebito_serie.'-'.str_pad($facturacionnotadebito->notadebito_correlativo, 6, "0", STR_PAD_LEFT),
                 'pdf' => $output,
                 'a4pdf' => $a4_output,
                 'nombrepdf'=>'NOTA_DE_DEBITO_'.$facturacionnotadebito->notadebito_serie.'_'.str_pad($facturacionnotadebito->notadebito_correlativo, 6, "0", STR_PAD_LEFT).'.pdf',
                 'xml' => 'public/backoffice/tienda/'.$tienda->id.'/sunat/produccion/notadebito/'.$facturacionrespuesta->nombre.'.xml',
              );

              Mail::send('app/email_notadebito',  [
                  'user' => $user,
                  'tienda'                   => $tienda,
                  'facturacionnotadebito' => $facturacionnotadebito,
                  'notadebitodetalle'     => $notadebitodetalle,
                  'configuracion'            => $configuracion,
                  'respuesta'                => $facturacionrespuesta,
                ], function ($message) use ($user) {
                  $message->from($user['correo'],$user['nombre']);
                  $message->to($user['correo_destino'])->subject($user['titulo']);
                  $message->attach($user['xml']);
                  $message->attachData($user['pdf'], $user['nombrepdf'], [ 'mime' => 'application/pdf' ]);
                  $message->attachData($user['a4pdf'], $user['nombrepdf'], [ 'mime' => 'application/pdf' ]);
              });


              return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha enviado correctamente.'
              ]);
        }
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
    }
}

