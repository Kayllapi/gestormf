<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;
use PDF;
use NumeroALetras;
use Mail;

class FacturacionNotacreditoController extends Controller
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
        $facturacionnotacredito = DB::table('s_facturacionnotacredito')
            ->where('s_facturacionnotacredito.idtienda', $tienda->id)
            ->leftJoin('users as responsable','responsable.id','s_facturacionnotacredito.idusuarioresponsable')
            ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacionnotacredito.idfacturacionrespuesta')
            ->select(
                's_facturacionnotacredito.*',
                'responsable.nombre as responsablenombre',
                's_facturacionrespuesta.codigo as respuestacodigo',
                's_facturacionrespuesta.estado as respuestaestado',
            )
            ->orderBy('s_facturacionnotacredito.id','desc')
            ->paginate(10);
        return view('layouts/backoffice/tienda/sistema/facturacionnotacredito/index', [
            'tienda'                 => $tienda,
            'facturacionnotacredito' => $facturacionnotacredito
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
        $agencias = DB::table('s_agencia')->where('idtienda',$idtienda)->where('idestadofacturacion',1)->get();
        
      
        return view('layouts/backoffice/tienda/sistema/facturacionnotacredito/create',[
            'tienda'        => $tienda,
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
               'idmotivonotacredito'          => 'required',
               'motivonotacredito_descripcion'          => 'required',
               'productos'                              => 'required',
            ]; 
            $messages = [
               'idmotivonotacredito.required' => 'El "Motivo" es Obligatorio.',
               'motivonotacredito_descripcion.required' => 'El "Motivo" es Obligatorio.',
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
              
              $facturacionnotacreditodetalle = DB::table('s_facturacionnotacreditodetalle')
                        ->where('s_facturacionnotacreditodetalle.idfacturacionboletafacturadetalle',$item_producto->idfacturacionboletafacturadetalle)
                        ->sum('s_facturacionnotacreditodetalle.cantidad');
              
              $facturacionboletafacturadetalle = DB::table('s_facturacionboletafacturadetalle')->whereId($item_producto->idfacturacionboletafacturadetalle)->first();
              
              $restante = $facturacionboletafacturadetalle->cantidad - $facturacionnotacreditodetalle;
              
              if($facturacionboletafacturadetalle->cantidad<$item_producto->productCant){
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
                    ['s_facturacionboletafactura.id', $request->idfactura]
//                     ['s_facturacionboletafactura.idagencia', $request->input('idagencia')],
//                     ['s_facturacionboletafactura.venta_serie', $request->input('facturador_serie')],
//                     ['s_facturacionboletafactura.venta_correlativo', $request->input('facturador_correlativo')]
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
                $count_restante = 0;
                foreach($facturacionboletafacturadetalle as $value){

                    $facturacionnotacreditodetalle = DB::table('s_facturacionnotacreditodetalle')
                        ->where('s_facturacionnotacreditodetalle.idfacturacionboletafacturadetalle',$value->id)
                        ->sum('s_facturacionnotacreditodetalle.cantidad');

                    $restante = $value->cantidad - $facturacionnotacreditodetalle;

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
                        $count_restante++;
                    }

                }
                if($count_restante==0){
                    return [ 
                      'resultado' => 'ERROR',
                      'mensaje' => 'Los productos de la Boleta/Factura ya fueron emitos por otra NOTA DE CREDITO.' 
                    ];
                }else{
                    return [ 
                      'resultado' => 'CORRECTO',
                      'mensaje' => 'Correcto!.',
                      'facturacionboletafactura'  => $facturacionboletafactura,
                      'facturacionboletafacturadetalle' => $html_detalle,
                    ];
                }
            } else {
                return [ 
                  'resultado' => 'ERROR',
                  'mensaje' => 'No existe la Boleta/Factura!.' 
                ];
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
        }
        elseif($id == 'show-boletasfacturas-fechaactual') {
             $where = [];
          
             if ($request->buscar == '') {
               $where[] = ['s_facturacionboletafactura.venta_fechaemision', 'LIKE', '%'.date('Y-m-d').'%'];
             } 
            
             $facturacionboletafactura = DB::table('s_facturacionboletafactura')
                ->where([
                    ['s_facturacionboletafactura.idtienda', $idtienda],
                    ['s_facturacionboletafactura.idagencia', $request->input('idagencia')]
                ])
                ->where($where)
                ->select(
                    's_facturacionboletafactura.*',
                    DB::raw('CONCAT(
                    s_facturacionboletafactura.venta_serie," - ",s_facturacionboletafactura.venta_correlativo," / ",
                    s_facturacionboletafactura.cliente_numerodocumento," - ",s_facturacionboletafactura.cliente_razonsocial
                    ) as text')
                )
                ->get();
          
            $data = [];
          
            foreach ($facturacionboletafactura as $factura) {
                $exist_comunicacionbaja = DB::table('s_facturacioncomunicacionbajadetalle')
                  ->where('s_facturacioncomunicacionbajadetalle.idfacturacionboletafactura', $factura->id)
                  ->exists();
                
                 if(!$exist_comunicacionbaja) {
                    $data[] = [
                        'id' => $factura->id,
                        'text' => $factura->text,
                    ];     
                 }
            }
            
            return $data;
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

        $facturacionnotacredito = DB::table('s_facturacionnotacredito')
            ->join('users as responsable','responsable.id','s_facturacionnotacredito.idusuarioresponsable')
            ->join('s_agencia','s_agencia.id','s_facturacionnotacredito.idagencia')
            ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacionnotacredito.idfacturacionrespuesta')
            ->where('s_facturacionnotacredito.id',$id)  
            ->select(
                's_facturacionnotacredito.*',
                'responsable.nombre as responsablenombre',
                's_agencia.logo as agencialogo',
                's_facturacionrespuesta.qr as respuestaqr',
            )
            ->first();
       
        if($request->input('view') == 'detalle') {
            $facturacionnotacreditodetalles = DB::table('s_facturacionnotacreditodetalle')
              ->join('s_producto','s_producto.id','s_facturacionnotacreditodetalle.idproducto')
              ->where('s_facturacionnotacreditodetalle.idfacturacionnotacredito',$facturacionnotacredito->id)
              ->select(
                's_facturacionnotacreditodetalle.*',
                's_producto.codigo as productocodigo',
                's_producto.nombre as productonombre'
              )
              ->orderBy('s_facturacionnotacreditodetalle.id','asc')
              ->get();
            return view('layouts/backoffice/tienda/sistema/facturacionnotacredito/detalle',[
                'tienda'                              => $tienda,
                'facturacionnotacredito'              => $facturacionnotacredito,
                'facturacionnotacreditodetalles'      => $facturacionnotacreditodetalles,
            ]);

        }
        elseif($request->input('view') == 'ticket') {
            return view('layouts/backoffice/tienda/sistema/facturacionnotacredito/ticket',[
                'tienda'                   => $tienda,
                'facturacionnotacredito' => $facturacionnotacredito
            ]);
        }elseif($request->input('view') == 'ticketpdf') {

            $facturacionnotacreditodetalles = DB::table('s_facturacionnotacreditodetalle')
                ->join('s_producto','s_producto.id','s_facturacionnotacreditodetalle.idproducto')
                ->where('s_facturacionnotacreditodetalle.idfacturacionnotacredito',$facturacionnotacredito->id)
                ->select(
                    's_facturacionnotacreditodetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->orderBy('s_facturacionnotacreditodetalle.id','asc')
                ->get();
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionnotacredito/ticketpdf',[
                'tienda' => $tienda,
                'facturacionnotacredito' => $facturacionnotacredito,
                'notacreditodetalle' => $facturacionnotacreditodetalles,
            ]);
            $ticket = 'Ticket_'.str_pad($facturacionnotacredito->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');
        }
        elseif($request->input('view') == 'a4pdf') {
            $facturacionnotacreditodetalles = DB::table('s_facturacionnotacreditodetalle')
                ->join('s_producto','s_producto.id','s_facturacionnotacreditodetalle.idproducto')
                ->where('s_facturacionnotacreditodetalle.idfacturacionnotacredito',$facturacionnotacredito->id)
                ->select(
                    's_facturacionnotacreditodetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->orderBy('s_facturacionnotacreditodetalle.id','asc')
                ->get();
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionnotacredito/a4pdf',[
                'tienda' => $tienda,
                'facturacionnotacredito' => $facturacionnotacredito,
                'notacreditodetalle' => $facturacionnotacreditodetalles,
            ]);
            $a4pdf = 'PDF_A4_'.str_pad($facturacionnotacredito->id, 8, "0", STR_PAD_LEFT);
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

            $result = facturador_notacredito($id);

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
          
              $facturacionnotacredito = DB::table('s_facturacionnotacredito')
                  ->join('users as responsable','responsable.id','s_facturacionnotacredito.idusuarioresponsable')
                  ->join('s_agencia','s_agencia.id','s_facturacionnotacredito.idagencia')
                  ->where('s_facturacionnotacredito.id',$request->input('idfacturacionnotacredito'))
                  ->select(
                      's_facturacionnotacredito.*',
                      'responsable.nombre as responsablenombre',
                      's_agencia.logo as agencialogo',
                       DB::raw('CONCAT(s_facturacionnotacredito.cliente_numerodocumento," - ",s_facturacionnotacredito.cliente_razonsocial) as cliente'),
                       DB::raw('CONCAT(s_facturacionnotacredito.cliente_departamento, " , ", s_facturacionnotacredito.cliente_provincia, " , ", s_facturacionnotacredito.cliente_distrito) as ubigeo'),
                       DB::raw('CONCAT(s_facturacionnotacredito.emisor_ruc, " - ", s_facturacionnotacredito.emisor_nombrecomercial) as agencia')
                    )
                  ->first();
              $notacreditodetalle = DB::table('s_facturacionnotacreditodetalle')
                  ->join('s_producto','s_producto.id','s_facturacionnotacreditodetalle.idproducto')
                  ->where('s_facturacionnotacreditodetalle.idfacturacionnotacredito',$facturacionnotacredito->id)
                  ->select(
                      's_facturacionnotacreditodetalle.*',
                      's_producto.codigo as productocodigo',
                      's_producto.nombre as productonombre'
                  )
                  ->orderBy('s_facturacionnotacreditodetalle.id','asc')
                  ->get();
          
              $tienda = DB::table('tienda')->whereId($idtienda)->first();

              $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionnotacredito/ticketpdf',[
                  'tienda'                    => $tienda,
                  'facturacionnotacredito'    => $facturacionnotacredito,
                  'notacreditodetalle'        => $notacreditodetalle,
              ]);
          
              $a4pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionnotacredito/a4pdf',[
                  'tienda' => $tienda,
                  'facturacionnotacredito' => $facturacionnotacredito,
                  'notacreditodetalle' => $notacreditodetalle,
              ]);
          
              $output = $pdf->output();
              $a4_output = $a4pdf->output();

              $user = array (
                 'correo' => 'ventas@kayllapi.com',
                 'nombre' => strtoupper($facturacionnotacredito->emisor_nombrecomercial),
                 'correo_destino' => $request->input('enviarcorreo_email'),
                 'titulo' => 'NOTA DE CRÉDITO '.$facturacionnotacredito->notacredito_serie.'-'.str_pad($facturacionnotacredito->notacredito_correlativo, 6, "0", STR_PAD_LEFT),
                 'pdf' => $output,
                 'a4pdf' => $a4_output,
                 'nombrepdf'=>'NOTA_DE_CREDITO_'.$facturacionnotacredito->notacredito_serie.'_'.str_pad($facturacionnotacredito->notacredito_correlativo, 6, "0", STR_PAD_LEFT).'.pdf',
                 'xml' => 'public/backoffice/tienda/'.$tienda->id.'/sunat/produccion/notacredito/'.$facturacionrespuesta->nombre.'.xml',
              );

              Mail::send('app/email_notacredito',  [
                  'user' => $user,
                  'tienda'                   => $tienda,
                  'facturacionnotacredito' => $facturacionnotacredito,
                  'notacreditodetalle'     => $notacreditodetalle,
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
