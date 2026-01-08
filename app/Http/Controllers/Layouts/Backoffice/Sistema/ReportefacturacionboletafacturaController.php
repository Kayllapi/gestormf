<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NuevoSistema\ReporteFacturacionBoletaFacturaExport;

class  ReportefacturacionboletafacturaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/reporte/reportefacturacionboletafactura/tabla',[
                'tienda' => $tienda,
            ]);
        }
    }

    public function create(Request $request,$idtienda)
    {
        //
    }

    public function store(Request $request, $idtienda)
    {
        //
    }

    public function createJson($idtienda, $name_modulo, $data, $idadicional = '')
    {
        $directorio = getcwd().'/public/backoffice/tienda/'.$idtienda.'/reporte/comprobantes_electronicos';
        if (!file_exists($directorio)) { 
            mkdir($directorio, 0777, true); 
        }
        $file = $directorio.'/'.$name_modulo.$idadicional.'.json';
        $json_string = json_encode(array('data' => $data));
        file_put_contents($file, $json_string);
    }

    public function show(Request $request, $idtienda, $id)
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
    
            
        $facturacionboletafactura = $this->getComprobantes($request, $tienda)->facturacionboletafactura->get();

        $this->createJson($tienda->id, 'reportefacturacionboletafactura', $facturacionboletafactura);
        $jsonComprobantes = json_decode(file_get_contents('public/backoffice/tienda/'.$idtienda.'/reporte/comprobantes_electronicos/reportefacturacionboletafactura.json'), true);

//         $notasDeCredito = $this->getComprobantes($request, $tienda)->notasDeCredito->get();
//         $notasDeDebito = $this->getComprobantes($request, $tienda)->notasDeDebito->get();
        
        if ($id == 'showtabla') {
            return view(sistema_view().'/reporte/reportefacturacionboletafactura/tabla-data',[
                'tienda' => $tienda,
            ]);
        } else if($id == 'showtablapdf') {
          
            $pdf = PDF::setPaper('legal', 'landscape')
            ->loadView(sistema_view().'/reporte/reportefacturacionboletafactura/tablapdf',[
                'tienda'      => $tienda,
                'comprobantes' => $jsonComprobantes
            ]);

            return $pdf->stream('REPORTE_DE_FACTUAS_BOLETAS.pdf');
        } else if ($id == 'showexcel') {
            return Excel::download(
                new ReporteFacturacionBoletaFacturaExport(
                    $jsonComprobantes,
                    $request->fechainicio,
                    $request->fechafin,
                    'REPORTE DE FACTURAS Y BOLETAS'
                ),
                'reporte_factura.xls'
            );
        }
    }

    public function getComprobantes(Request $request, $tienda)
    {
        $where = [];
        $where_nc = [];
        $where_nd = [];
        if($request->input('fechainicio')!=''){
            $where[] = ['s_facturacionboletafactura.venta_fechaemision','>=',$request->input('fechainicio').' 00:00:00'];
            $where_nc[] = ['s_facturacionnotacredito.notacredito_fechaemision','>=',$request->input('fechainicio').' 00:00:00'];
            $where_nd[] = ['s_facturacionnotadebito.notadebito_fechaemision','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if($request->input('fechafin')!=''){
            $where[] = ['s_facturacionboletafactura.venta_fechaemision','<=',$request->input('fechafin').' 23:59:59'];
            $where_nc[] = ['s_facturacionnotacredito.notacredito_fechaemision','<=',$request->input('fechafin').' 23:59:59'];
            $where_nd[] = ['s_facturacionnotadebito.notadebito_fechaemision','<=',$request->input('fechafin').' 23:59:59'];
        }
      
        $facturacionboletafactura = DB::table('s_facturacionboletafactura')
                                      ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacionboletafactura','s_facturacionboletafactura.id')
                                      ->where('s_facturacionboletafactura.idtienda', $tienda->id)
                                      ->where($where)
                                      ->select(
                                          's_facturacionboletafactura.id',
                                          's_facturacionboletafactura.venta_fechaemision as fechaemision',
                                          's_facturacionboletafactura.venta_serie as serie',
                                          's_facturacionboletafactura.venta_correlativo as correlativo',
                                          's_facturacionboletafactura.venta_tipodocumento as tipodocumento',
                                          's_facturacionboletafactura.venta_montoimpuestoventa as total',
                                          's_facturacionboletafactura.venta_valorventa as valorventa',
                                          's_facturacionboletafactura.venta_montoigv as montoigv',
                                          's_facturacionboletafactura.cliente_numerodocumento as ruc_cliente',
                                          's_facturacionboletafactura.cliente_razonsocial as razonsocial_cliente',
                                          's_facturacionboletafactura.emisor_ruc as ruc_emisor',
                                          's_facturacionboletafactura.emisor_razonsocial as razonsocial_emisor',
                                          's_facturacionboletafactura.venta_tipomoneda as tipomoneda',
                                          's_facturacionrespuesta.estado as respuestaestado'
                                      )
                                      ->selectRaw("IF(s_facturacionboletafactura.venta_tipodocumento = '01', 'FACTURA', 'BOLETA') AS nombre_documento")
                                      ->selectRaw("'' AS documento_afectado");
        
        $facturacionnotacredito = DB::table('s_facturacionnotacredito')
                                    ->leftJoin('users as responsable','responsable.id','s_facturacionnotacredito.idusuarioresponsable')
                                    ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacionnotacredito','s_facturacionnotacredito.id')
                                    ->where('s_facturacionnotacredito.idtienda', $tienda->id)
                                    ->where($where_nc)
                                    ->select(
                                        's_facturacionnotacredito.id',
                                        's_facturacionnotacredito.notacredito_fechaemision as fechaemision',
                                        's_facturacionnotacredito.notacredito_serie as serie',
                                        's_facturacionnotacredito.notacredito_correlativo as correlativo',
                                        's_facturacionnotacredito.notacredito_tipodocumento as tipodocumento',
                                        's_facturacionnotacredito.notacredito_montoimpuestoventa as total',
                                        's_facturacionnotacredito.notacredito_valorventa as valorventa',
                                        's_facturacionnotacredito.notacredito_montoigv as montoigv',
                                        's_facturacionnotacredito.cliente_numerodocumento as ruc_cliente',
                                        's_facturacionnotacredito.cliente_razonsocial as razonsocial_cliente',
                                        's_facturacionnotacredito.emisor_ruc as ruc_emisor',
                                        's_facturacionnotacredito.emisor_razonsocial as razonsocial_emisor',
                                        's_facturacionnotacredito.notacredito_tipomoneda as tipomoneda',
                                        's_facturacionrespuesta.estado as respuestaestado',


                                    )
                                    ->selectRaw("'NOTA DE CRÉDITO' AS nombre_documento")
                                    ->selectRaw("s_facturacionnotacredito.notacredito_tipodocafectado as documento_afectado");
        
        $facturacionnotadebito = DB::table('s_facturacionnotadebito')
                                  ->leftJoin('users as responsable','responsable.id','s_facturacionnotadebito.idusuarioresponsable')
                                  ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacionnotadebito','s_facturacionnotadebito.id')
                                  ->where('s_facturacionnotadebito.idtienda', $tienda->id)
                                  ->where($where_nd)
                                  ->select(
                                      's_facturacionnotadebito.id',
                                      's_facturacionnotadebito.notadebito_fechaemision as fechaemision',
                                      's_facturacionnotadebito.notadebito_serie as serie',
                                      's_facturacionnotadebito.notadebito_correlativo as correlativo',
                                      's_facturacionnotadebito.notadebito_tipodocumento as tipodocumento',
                                      's_facturacionnotadebito.notadebito_montoimpuestoventa as total',
                                      's_facturacionnotadebito.notadebito_valorventa as valorventa',
                                      's_facturacionnotadebito.notadebito_montoigv as montoigv',
                                      's_facturacionnotadebito.cliente_numerodocumento as ruc_cliente',
                                      's_facturacionnotadebito.cliente_razonsocial as razonsocial_cliente',
                                      's_facturacionnotadebito.emisor_ruc as ruc_emisor',
                                      's_facturacionnotadebito.emisor_razonsocial as razonsocial_emisor',
                                      's_facturacionnotadebito.notadebito_tipomoneda as tipomoneda',
                                      's_facturacionrespuesta.estado as respuestaestado',
                                  )
                                  ->selectRaw("'NOTA DE DÉBITO' AS nombre_documento")
                                  ->selectRaw("s_facturacionnotadebito.notadebito_tipodocafectado as documento_afectado");
        
        $facturacion_electronica = $facturacionboletafactura
                                      ->union($facturacionnotacredito)
                                      ->union($facturacionnotadebito)
                                      ->orderBy('fechaemision', 'desc');
      
      
//         $facturacionboletafactura = DB::table('s_facturacionboletafactura')
//                 ->join('users as responsable','responsable.id','s_facturacionboletafactura.idusuarioresponsable')
//                 ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacionboletafactura','s_facturacionboletafactura.id')
//                 ->leftJoin('s_facturacioncomunicacionbajadetalle','s_facturacioncomunicacionbajadetalle.idfacturacionboletafactura','s_facturacionboletafactura.id')
//                 ->leftJoin('s_facturacioncomunicacionbaja','s_facturacioncomunicacionbaja.id','s_facturacioncomunicacionbajadetalle.idfacturacioncomunicacionbaja')
//                 ->leftJoin('s_facturacionresumendiariodetalle','s_facturacionresumendiariodetalle.idfacturacionboletafactura','s_facturacionboletafactura.id')
//                 ->leftJoin('s_facturacionresumendiario','s_facturacionresumendiario.id','s_facturacionresumendiariodetalle.idfacturacionresumendiario')
//                 ->leftJoin('s_venta', 's_venta.id', 's_facturacionboletafactura.idventa')
//                 ->where('s_facturacionboletafactura.idtienda', $tienda->id)
//                 ->where($where)
//                 ->where('s_facturacionboletafactura.idsucursal', Auth::user()->idsucursal)
//                 ->select(
//                     's_facturacionboletafactura.*',
//                     'responsable.nombrecompleto as responsablenombre',
//                     's_facturacionrespuesta.estado as respuestaestado',
//                     's_venta.codigo as codigo',
//                     's_facturacioncomunicacionbaja.comunicacionbaja_correlativo as comunicacionbaja_correlativo',
//                     's_facturacionresumendiariodetalle.estado as resumen_estado',
//                     's_facturacionresumendiario.resumen_correlativo as resumen_correlativo'
//                 )
//                 ->selectRaw(
//                     "CASE 
//                     WHEN s_facturacionboletafactura.venta_tipodocumento = '01' THEN (SELECT COUNT(id) AS cantidad_documento_comunicacion
//                     FROM s_facturacioncomunicacionbajadetalle 
//                     WHERE s_facturacioncomunicacionbajadetalle.idfacturacionboletafactura =  s_facturacionboletafactura.id)
//                     WHEN s_facturacionboletafactura.venta_tipodocumento = '03' THEN (SELECT COUNT(id) AS cantidad_documento_resumendiario
//                     FROM s_facturacionresumendiariodetalle 
//                     WHERE s_facturacionresumendiariodetalle.idfacturacionboletafactura =  s_facturacionboletafactura.id)
//                     ELSE '0'
//                     END AS cantidad_anulado,
//                     IF(s_facturacionboletafactura.venta_tipodocumento = '01', 'FACTURA', 'BOLETA') AS venta_tipodocumento"
//                 )
//                 ->orderBy('s_facturacionboletafactura.id','desc');
        
//         $notasDeCredito = DB::table('s_facturacionnotacredito')
//             ->join('s_moneda','s_moneda.codigo','s_facturacionnotacredito.notacredito_tipomoneda')
//             ->join('users as responsable','responsable.id','s_facturacionnotacredito.idusuarioresponsable')
//             ->join('users as cliente','cliente.id','s_facturacionnotacredito.idusuariocliente')
//             ->leftJoin('s_facturacionboletafactura', 's_facturacionboletafactura.id', 's_facturacionnotacredito.idfacturacionrespuesta')
//             ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacionnotacredito','s_facturacionnotacredito.id')
//             ->where('s_facturacionnotacredito.idtienda', $tienda->id)
//             ->select(
//                 's_facturacionnotacredito.*',
//                 's_facturacionrespuesta.estado as respuestaestado',
//                 'responsable.nombrecompleto as responsablenombre',
//             )
//             ->selectRaw(
//                 'CONCAT(s_facturacionboletafactura.venta_serie,"-",s_facturacionboletafactura.venta_correlativo) as documento_afectado'
//             )
//             ->orderBy('s_facturacionnotacredito.id','desc');
    
//         $notasDeDebito = DB::table('s_facturacionnotadebito')
//             ->join('s_moneda','s_moneda.codigo','s_facturacionnotadebito.notadebito_tipomoneda')
//             ->join('users as responsable','responsable.id','s_facturacionnotadebito.idusuarioresponsable')
//             ->join('users as cliente','cliente.id','s_facturacionnotadebito.idusuariocliente')
//             ->leftJoin('s_facturacionboletafactura', 's_facturacionboletafactura.id', 's_facturacionnotadebito.idfacturacionrespuesta')
//             ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacionnotadebito','s_facturacionnotadebito.id')
//             ->where('s_facturacionnotadebito.idtienda', $tienda->id)
//             ->select(
//                 's_facturacionnotadebito.*',
//                 'responsable.nombrecompleto as responsablenombre',
//                 'cliente.nombrecompleto as clientenombre',
//                 's_facturacionrespuesta.estado as respuestaestado'
//             )
//             ->selectRaw(
//                 'CONCAT(s_facturacionboletafactura.venta_serie,"-",s_facturacionboletafactura.venta_correlativo) as documento_afectado'
//             )
//             ->orderBy('s_facturacionnotadebito.id','desc');

        return (object) [
            'facturacionboletafactura' => $facturacion_electronica,
//             'facturacionboletafactura' => $facturacionboletafactura,
//             'notasDeCredito' => $notasDeCredito,
//             'notasDeDebito' => $notasDeDebito
        ];
    }

    public function edit(Request $request, $idtienda, $idmarca)
    {
        //
    }

    public function update(Request $request, $idtienda, $idmarca)
    {
        //
    }

    public function destroy(Request $request, $idtienda, $idmarca)
    {
        //
    }
}
