<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use Auth;
use Hash;
use DB;
use PDF; 
use Mail;
use NumeroALetras;

class FacturacionNotacreditoController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        // dump("index nc");
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/facturacionnotacredito/tabla',[
                'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda         = DB::table('tienda')->whereId($idtienda)->first();  
        $agencias = DB::table('s_agencia')->where('idtienda',$idtienda)->where('idestadofacturacion',1)->get();
        
        return view(sistema_view().'/facturacionnotacredito/create',[
            'tienda'        => $tienda,
            'agencias'      => $agencias
        ]);
    }
  
    public function store(Request $request, $idtienda)
    {
       $request->user()->authorizeRoles($request->path(),$idtienda);
        if($request->input('view') == 'registrar') {
          
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
          
            // json_facturacionnotacredito($idtienda, Auth::user()->idsucursal, Auth::user()->id);

            return [
                'resultado' => $result['resultado'],
                'mensaje'   => $result['mensaje'],
                'idfacturacionnotacredito' => $idfacturacionnotacredito
            ];
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if($id=='show_table'){
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $idsucursal = Auth::user()->idsucursal;
            $idusuario = Auth::user()->id;

            $facturacionnotacredito = DB::table('s_facturacionnotacredito')
                ->leftJoin('users as responsable','responsable.id','s_facturacionnotacredito.idusuarioresponsable')
                ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacionnotacredito','s_facturacionnotacredito.id')
                ->where('s_facturacionnotacredito.idtienda', $tienda->id)
                ->where('s_facturacionnotacredito.idusuarioresponsable',$idusuario)
                ->where('s_facturacionnotacredito.idsucursal', $idsucursal)
                ->select(
                    's_facturacionnotacredito.*',
                    'responsable.nombre as responsablenombre',
                    's_facturacionrespuesta.codigo as respuestacodigo',
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
                    END AS cantidad_anulado"
                )
                ->orderBy('s_facturacionnotacredito.id','desc')
                ->paginate($request->length,'*',null,($request->start/$request->length)+1);
            
            $tabla = [];
            foreach($facturacionnotacredito as $value){
                $fecha_emi  = date_format(date_create($value->notacredito_fechaemision), 'd/m/Y h:i:s A');
                $serie_corre = $value->notacredito_serie.' '.str_pad($value->notacredito_correlativo, 8, "0", STR_PAD_LEFT);
                $moneda = '';

                $estado='';
                $estadoAnulado='';
                $opcionAnular = [];
                switch($value->respuestaestado){
                    case 'ACEPTADA':
                        $estado = 'Aceptada';
                        if ($value->cantidad_anulado > 0) {
                            $estadoAnulado = 'Anulado';
                            $estado = 'Anulado';
                            $opcionAnular = [];
                        }
                        if ($value->notacredito_tipodocafectado == '01' && $value->cantidad_anulado <= 0) {
                            $opcionAnular = [
                                'nombre' => 'Anular',
                                'onclick' => '/'.$idtienda.'/facturacionnotacredito/'.$value->id.'/edit?view=anular_comunicacionbaja',
                                'icono' => 'receipt',
                            ];
                        }

                        if ($value->notacredito_tipodocafectado == '03' && $value->cantidad_anulado <= 0) {
                            $opcionAnular = [
                                'nombre' => 'Anular',
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
                
                
                
        
                $tabla[] = [
                'id'  => $value->id,
                'serie_correlativo' => $serie_corre,
                'total' => $value->notacredito_totalimpuestos,
                'fecha_emision' => $fecha_emi,
                'cliente' => $value->cliente_numerodocumento.' - '.$value->cliente_razonsocial,
                'emisor' => $value->emisor_ruc.' - '.$value->emisor_nombrecomercial,
                'documento_afectado' => $value->notacredito_numerodocumentoafectado,
                'motivo' => $value->notacredito_descripcionmotivo,
                'estado_envio' => $estado,
                'estado_anulado' => $estadoAnulado,
                'opcion' => [
                    [
                    'nombre' => 'Comprobantes',
                    // 'onclick' => '/'.$idtienda.'/facturacionboletafactura/'.$value->id.'/edit?view=ticket',
                    'onclick' => '/'.$idtienda.'/facturacionnotacredito/'.$value->id.'/edit?view=ticket',
                    'icono' => 'receipt',
                    ],
                    $opcionAnular
                ],
                
                ];
            }
            return response()->json([
                'start'           => $request->start,
                'draw'            => $request->draw,
                'recordsTotal'    => $request->length,
                'recordsFiltered' => $facturacionnotacredito->total(),
                'data'            => $tabla,
            ]);
        }
        else if($id == 'show-seleccionarboletafactura'){
            $facturacionboletafactura = DB::table('s_facturacionboletafactura')
                ->where([
                    ['s_facturacionboletafactura.idtienda', $idtienda],
                    ['s_facturacionboletafactura.id', $request->idfactura]
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
                              <!-- <td>'.$value->codigoproducto.'</td> -->
                              <td>'.$value->descripcion.'</td>
                              <td><input class="form-control" type="text" value="'.$restante.'" disabled></td>                                
                              <td><input class="form-control" type="number" value="'.$value->montopreciounitario.'" step="0.01" min="0" disabled></td> 
                              <td><input class="form-control" id="productCant'.$value->id.'" type="number" value="'.$restante.'" onkeyup="calcularmonto()"></td>                                
                              <td><input class="form-control" id="productUnidad'.$value->id.'" type="number" value="'.$value->montopreciounitario.'" step="0.01" min="0" disabled></td>                        
                              <td><input class="form-control"   id="productTotal'.$value->id.'" type="text" value="'.number_format($restante*$value->montopreciounitario,2, '.', '').'" step="0.01" min="0" disabled></td>                                      
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
               ->whereRaw('CONCAT( s_facturacionboletafactura.venta_serie,"-",s_facturacionboletafactura.venta_correlativo ) LIKE "%'.$request->buscar.'%"')
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

    public function edit(Request $request, $idtienda, $id)
    {
//         $request->user()->authorizeRoles($request->path(),$idtienda);
      
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
            return view(sistema_view().'/facturacionnotacredito/detalle',[
                'tienda'                              => $tienda,
                'facturacionnotacredito'              => $facturacionnotacredito,
                'facturacionnotacreditodetalles'      => $facturacionnotacreditodetalles,
            ]);

        }
        elseif($request->input('view') == 'ticket') {

            return view(sistema_view().'/facturacionnotacredito/ticket',[
                'tienda'                   => $tienda,
                'facturacionnotacredito' => $facturacionnotacredito
            ]);
        }
        elseif($request->input('view') == 'ticketpdf') {

           
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

            $ticket = new \stdClass();
            //DATOS EMISOR
            $ticket->tipo_fuente = configuracion($tienda->id,'sistema_tipoletra')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_tipoletra')['valor']:'Helvetica';
            $ticket->ancho_ticket = configuracion($tienda->id,'sistema_anchoticket')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_anchoticket')['valor']:'7';
            $ticket->ruc_emision = $facturacionnotacredito->emisor_ruc;
            $ticket->razonsocial_emisor = strtoupper($facturacionnotacredito->emisor_razonsocial);
            $ticket->logotipo = url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$facturacionnotacredito->agencialogo);
            $ticket->direccion_emisor = strtoupper($facturacionnotacredito->emisor_direccion);
            $ticket->ubigeo_emisor = strtoupper($facturacionnotacredito->emisor_distrito.' - '.$facturacionnotacredito->emisor_provincia.' - '.$facturacionnotacredito->emisor_departamento);
            $ticket->tipo_documento = getTipoDocumento( $facturacionnotacredito->notacredito_tipodocumento );
            $ticket->serie_documento = $facturacionnotacredito->notacredito_serie;
            $ticket->correlativo_documento = $facturacionnotacredito->notacredito_correlativo;
            $ticket->fechaemision = date_format(date_create($facturacionnotacredito->notacredito_fechaemision),"d/m/Y h:i:s A");
            $ticket->moneda = getTipoMoneda( $facturacionnotacredito->notacredito_tipomoneda );
            $ticket->simbolo_moneda = $facturacionnotacredito->notacredito_tipomoneda == 'PEN' ? " S/ " : "";
            

            $ticket->razonsocial_cliente = strtoupper($facturacionnotacredito->cliente_razonsocial) ;
            $ticket->documento_cliente = $facturacionnotacredito->cliente_numerodocumento ;
            $ticket->direccion_cliente = strtoupper($facturacionnotacredito->cliente_direccion) ;
            $ticket->ubigeo_cliente = strtoupper($facturacionnotacredito->cliente_distrito.' - '.$facturacionnotacredito->cliente_provincia.' - '.$facturacionnotacredito->cliente_departamento) ;
            $ticket->idfacturacionboletafactura = $facturacionnotacredito->idfacturacionboletafactura;

            $ticket->motivoanulacion = strtoupper($facturacionnotacredito->notacredito_descripcionmotivo);


            $items = [];
            foreach( $facturacionnotacreditodetalles as $value ){
                $items[] = [
                            'codigoProducto' => 'S/N',
                            'descripcion'    => strtoupper($value->descripcion),
                            'cantidad'       => $value->cantidad ,
                            'precio'         => $value->montopreciounitario,
                            'total'          => number_format($value->cantidad*$value->montopreciounitario, 2, '.', '')
                            ];

            }
            $ticket->items = $items;
            $ticket->operacion_gravada = $facturacionnotacredito->notacredito_valorventa;
            $ticket->igv = $facturacionnotacredito->notacredito_montoigv;
            $ticket->total_venta = $facturacionnotacredito->notacredito_montoimpuestoventa;
            $ticket->leyenda = $facturacionnotacredito->leyenda_value;
            $ticket->qr = $facturacionnotacredito->respuestaqr;
            $ticket->link_consulta_cpe = url('/').'/'.$tienda->link.'/comprobante';

            $agencia = DB::table('s_agencia')->whereId($facturacionnotacredito->idagencia)->first();
            $pdf = PDF::loadView(sistema_view().'/facturacionnotacredito/ticketpdf',[
                'ticket' => $ticket,
                'agencia' => $agencia,
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
          
            $agencia = DB::table('s_agencia')->whereId($facturacionnotacredito->idagencia)->first();
            $pdf = PDF::loadView(sistema_view().'/facturacionnotacredito/a4pdf',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'facturacionnotacredito' => $facturacionnotacredito,
                'notacreditodetalle' => $facturacionnotacreditodetalles,
            ]);
            $a4pdf = 'PDF_A4_'.str_pad($facturacionnotacredito->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($a4pdf.'.pdf');
        }
        elseif($request->input('view') == 'a5pdf') {
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
          
            $agencia = DB::table('s_agencia')->whereId($facturacionnotacredito->idagencia)->first();
            $pdf = PDF::loadView(sistema_view().'/facturacionnotacredito/a5pdf',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'facturacionnotacredito' => $facturacionnotacredito,
                'notacreditodetalle' => $facturacionnotacreditodetalles,
            ]);
            $a5pdf = 'PDF_A4_'.str_pad($facturacionnotacredito->id, 8, "0", STR_PAD_LEFT);
            $pdf->setPaper('a4','landscape');
            return $pdf->stream($a5pdf.'.pdf');
            
        }
        else if( $request->input('view') == 'anular_resumendiario' ){
          return view(sistema_view().'/facturacionnotacredito/resumendiario',[
            'tienda'                   => $tienda,
            'facturacionnotacredito' => $facturacionnotacredito
          ]);
        }
        else if( $request->input('view') == 'anular_comunicacionbaja' ){
          return view(sistema_view().'/facturacionnotacredito/comunicacionbaja',[
            'tienda'                   => $tienda,
            'facturacionnotacredito' => $facturacionnotacredito
          ]);
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
          
            // json_facturacionnotacredito($idtienda, Auth::user()->idsucursal, Auth::user()->id);
            
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
                    ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacionnotacredito.idfacturacionrespuesta')
                    ->where('s_facturacionnotacredito.id',$request->input('idfacturacionnotacredito'))
                    ->select(
                        's_facturacionnotacredito.*',
                        'responsable.nombre as responsablenombre',
                        's_agencia.logo as agencialogo',
                        's_facturacionrespuesta.qr as respuestaqr',
                        's_facturacionrespuesta.nombre as nombre',
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

            //   $pdf = PDF::loadView(sistema_view().'/facturacionnotacredito/ticketpdf',[
            //       'tienda'                    => $tienda,
            //       'facturacionnotacredito'    => $facturacionnotacredito,
            //       'notacreditodetalle'        => $notacreditodetalle,
            //   ]);
          
                $agencia = DB::table('s_agencia')->whereId($facturacionnotacredito->idagencia)->first();
                $a4pdf = PDF::loadView(sistema_view().'/facturacionnotacredito/a4pdf',[
                    'tienda' => $tienda,
                    'facturacionnotacredito' => $facturacionnotacredito,
                    'notacreditodetalle' => $notacreditodetalle,
                    'agencia' => $agencia,
                ]);
          
            //   $output = $pdf->output();
              $a4_output = $a4pdf->output();

              $user = array (
                 'correo' => 'ventas@kayllapi.com',
                 'nombre' => strtoupper($facturacionnotacredito->emisor_nombrecomercial),
                 'correo_destino' => $request->input('enviarcorreo_email'),
                 'titulo' => 'NOTA DE CRÉDITO '.$facturacionnotacredito->notacredito_serie.'-'.str_pad($facturacionnotacredito->notacredito_correlativo, 6, "0", STR_PAD_LEFT),
                //  'pdf' => $output,
                 'a4pdf' => $a4_output,
                 'nombrepdf'=>'NOTA_DE_CREDITO_'.$facturacionnotacredito->notacredito_serie.'_'.str_pad($facturacionnotacredito->notacredito_correlativo, 6, "0", STR_PAD_LEFT).'.pdf',
                 'xml' => 'public/backoffice/tienda/'.$tienda->id.'/sunat/produccion/notacredito/'.$facturacionnotacredito->nombre.'.xml',
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
                //   $message->attachData($user['pdf'], $user['nombrepdf'], [ 'mime' => 'application/pdf' ]);
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