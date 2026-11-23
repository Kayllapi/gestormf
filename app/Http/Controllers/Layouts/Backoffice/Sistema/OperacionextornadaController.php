<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class OperacionextornadaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            
            $agencias = DB::table('tienda')->get();
            return view(sistema_view().'/operacionextornada/tabla',[
                'tienda' => $tienda,
                'agencias' => $agencias,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
    }
  
    public function store(Request $request, $idtienda)
    {
        
    }

    public function show(Request $request, $idtienda, $id)
    {
    }

    public function edit(Request $request, $idtienda, $id)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->input('view') == 'pdf_extorno'){
            
          $where1 = [];
          $where2 = [];
          $where3 = [];
          $where4 = [];
          $where5 = [];
          $where6 = [];
          if($request->idagencia!=''){
              $where1[] = ['credito_cobranzacuota.idtienda',$request->idagencia];
              $where2[] = ['credito.idtienda',$request->idagencia];
              $where3[] = ['gastoadministrativooperativo.idtienda',$request->idagencia];
              $where4[] = ['ingresoextraordinario.idtienda',$request->idagencia];
              $where5[] = ['asignacioncapital.idtienda',$request->idagencia];
              $where6[] = ['movimientointernodinero.idtienda',$request->idagencia];
          }
          if($request->fecha_inicio!=''){
              $where1[] = ['credito_cobranzacuota.fechaextorno','>=',$request->fecha_inicio.' 00:00:00'];
              $where2[] = ['credito.fecha_eliminado','>=',$request->fecha_inicio.' 00:00:00'];
              $where3[] = ['gastoadministrativooperativo.fecha_eliminado','>=',$request->fecha_inicio.' 00:00:00'];
              $where4[] = ['ingresoextraordinario.fecha_eliminado','>=',$request->fecha_inicio.' 00:00:00'];
              $where5[] = ['asignacioncapital.fecha_eliminado','>=',$request->fecha_inicio.' 00:00:00'];
              $where6[] = ['movimientointernodinero.fecha_eliminado','>=',$request->fecha_inicio.' 00:00:00'];
          }
          if($request->fecha_fin!=''){
              $where1[] = ['credito_cobranzacuota.fechaextorno','<=',$request->fecha_fin.' 23:59:59'];
              $where2[] = ['credito.fecha_eliminado','<=',$request->fecha_fin.' 23:59:59'];
              $where3[] = ['gastoadministrativooperativo.fecha_eliminado','<=',$request->fecha_fin.' 23:59:59'];
              $where4[] = ['ingresoextraordinario.fecha_eliminado','<=',$request->fecha_fin.' 23:59:59'];
              $where5[] = ['asignacioncapital.fecha_eliminado','<=',$request->fecha_fin.' 23:59:59'];
              $where6[] = ['movimientointernodinero.fecha_eliminado','<=',$request->fecha_fin.' 23:59:59'];
          }
          
          $ingresoextraordinarios = DB::table('ingresoextraordinario')
              ->join('s_sustento_comprobante','s_sustento_comprobante.id','ingresoextraordinario.s_idsustento_comprobante')
              ->leftJoin('users as responsable','responsable.id','ingresoextraordinario.idresponsable')
              ->leftJoin('tienda','tienda.id','ingresoextraordinario.idtienda')
              ->leftJoin('users as responsableeliminado','responsableeliminado.id','ingresoextraordinario.idresponsble_eliminado')
              ->where('ingresoextraordinario.idestadoeliminado',2)
              ->where($where4)
              ->select(
                  DB::raw('CONCAT("ELIM. INGRESO") as operacion'),
                  DB::raw('CONCAT("--") as cuenta'),
                //   DB::raw('CONCAT(ingresoextraordinario.codigoprefijo,ingresoextraordinario.codigo) as cuenta'),
                  'ingresoextraordinario.fecha_eliminado as fechaextorno',
                  DB::raw('CONCAT("--") as pago_cuota'),
                  'ingresoextraordinario.monto as total_pagar',
                  'ingresoextraordinario.banco as banco',
                  'ingresoextraordinario.cuenta as cuenta_bancaria',
                  'ingresoextraordinario.numerooperacion as numerooperacion',
                  DB::raw('CONCAT(ingresoextraordinario.codigoprefijo,ingresoextraordinario.codigo) as codigo'),
                  DB::raw("CASE WHEN s_sustento_comprobante.nombre = 'Credito Castigado' THEN ingresoextraordinario.descripcion ELSE CONCAT(ingresoextraordinario.descripcion, ' / ', s_sustento_comprobante.nombre) END as nombrecliente"),
                  //DB::raw('CONCAT("--") as nombrecliente'),
                  'responsable.codigo as codigoresponsable',
                  'tienda.nombreagencia as tiendanombre',
                   'responsableeliminado.codigo as codigoresponsableeliminado',
                  DB::raw('CONCAT("") as fc')
              );
          
          $gastoadministrativooperativos = DB::table('gastoadministrativooperativo')
              ->join('s_sustento_comprobante','s_sustento_comprobante.id','gastoadministrativooperativo.s_idsustento_comprobante')
              ->leftJoin('users as responsable','responsable.id','gastoadministrativooperativo.idresponsable')
              ->leftJoin('tienda','tienda.id','gastoadministrativooperativo.idtienda')
              ->leftJoin('users as responsableeliminado','responsableeliminado.id','gastoadministrativooperativo.idresponsble_eliminado')
              ->where($where3)
              ->select(
                  DB::raw('CONCAT("ELIM. GASTO") as operacion'),
                  DB::raw('CONCAT("--") as cuenta'),
                //   DB::raw('CONCAT(gastoadministrativooperativo.codigoprefijo,gastoadministrativooperativo.codigo) as cuenta'),
                  'gastoadministrativooperativo.fecha_eliminado as fechaextorno',
                  DB::raw('CONCAT("--") as pago_cuota'),
                  'gastoadministrativooperativo.monto as total_pagar',
                  'gastoadministrativooperativo.banco as banco',
                  'gastoadministrativooperativo.cuenta as cuenta_bancaria',
                  'gastoadministrativooperativo.numerooperacion as numerooperacion',
                  DB::raw('CONCAT(gastoadministrativooperativo.codigoprefijo,gastoadministrativooperativo.codigo) as codigo'),
                  DB::raw('CONCAT(gastoadministrativooperativo.descripcion," / ",s_sustento_comprobante.nombre," / ",gastoadministrativooperativo.sustento_descripcion) as nombrecliente'),
                  //DB::raw('CONCAT("--") as nombrecliente'),
                  'responsable.codigo as codigoresponsable',
                  'tienda.nombreagencia as tiendanombre',
                  'responsableeliminado.codigo as codigoresponsableeliminado',
                  DB::raw('CONCAT("") as fc')
              );
          
          $creditos = DB::table('credito')
              ->join('users as cliente','cliente.id','credito.idcliente')
              ->leftJoin('credito_formapago','credito_formapago.idcredito','credito.id')
              ->leftJoin('users as responsable','responsable.id','credito.idasesor')
              ->leftJoin('users as responsableeliminado','responsableeliminado.id','credito.idasesor_eliminacion')
              ->leftJoin('tienda','tienda.id','credito.idtienda')
              ->where('credito.estado','ELIMINADO')
              ->where($where2)
              ->select(
                  DB::raw('CONCAT("ELIM. CRÉDITO") as operacion'),
                  DB::raw('CONCAT("C",credito.cuenta) as cuenta'),
                  'credito.fecha_eliminado as fechaextorno',
                  'credito.cuotas as pago_cuota',
                  'credito.monto_solicitado as total_pagar',
                  'credito_formapago.banco as banco',
                  'credito_formapago.cuenta as cuenta_bancaria',
                  'credito_formapago.numerooperacion as numerooperacion',
                  'credito_formapago.codigo as codigo',
                  DB::raw("CONCAT(CASE WHEN credito.estado_previo_eliminacion = 'PROCESO' THEN 'EN PROCESO' ELSE COALESCE(credito.estado_previo_eliminacion, 'ESTADO NO REGISTRADO') END, ' / ', cliente.nombrecompleto) as nombrecliente"),
                  'responsable.codigo as codigoresponsable',
                  'tienda.nombreagencia as tiendanombre',
                  'responsableeliminado.codigo as codigoresponsableeliminado',
                  DB::raw("CASE
                      WHEN credito.idforma_credito = 1 THEN 'CP'
                      WHEN credito.idforma_credito = 2 THEN 'CNP'
                      WHEN credito.idforma_credito = 3 THEN 'CC'
                      ELSE ''
                  END as fc")
              );
          
          $asignaciones = DB::table('asignacioncapital')
              ->leftJoin('credito_tipooperacion', 'credito_tipooperacion.id', 'asignacioncapital.idtipooperacion')
              ->leftJoin('users as responsable', 'responsable.id', 'asignacioncapital.idresponsable')
              ->leftJoin('users as responsableeliminado', 'responsableeliminado.id', 'asignacioncapital.idresponsble_eliminado')
              ->leftJoin('tienda', 'tienda.id', 'asignacioncapital.idtienda')
              ->where('asignacioncapital.idestadoeliminado', 2)
              ->where($where5)
              ->select(
                  DB::raw("'ELIM. CAPITAL' as operacion"),
                  DB::raw("'--' as cuenta"),
                  'asignacioncapital.fecha_eliminado as fechaextorno',
                  DB::raw("'--' as pago_cuota"),
                  'asignacioncapital.monto as total_pagar',
                  'asignacioncapital.banco as banco',
                  'asignacioncapital.cuenta as cuenta_bancaria',
                  'asignacioncapital.numerooperacion as numerooperacion',
                  DB::raw("CONCAT(asignacioncapital.codigoprefijo, asignacioncapital.codigo) as codigo"),
                  DB::raw("CONCAT(COALESCE(credito_tipooperacion.nombre, ''), ' / ', COALESCE(asignacioncapital.descripcion, '')) as nombrecliente"),
                  'responsable.codigo as codigoresponsable',
                  'tienda.nombreagencia as tiendanombre',
                  'responsableeliminado.codigo as codigoresponsableeliminado',
                  DB::raw("'' as fc")
              );

          $movimientos = DB::table('movimientointernodinero')
              ->leftJoin('credito_fuenteretiro', 'credito_fuenteretiro.id', 'movimientointernodinero.idfuenteretiro')
              ->leftJoin('users as responsable', 'responsable.id', 'movimientointernodinero.idresponsable')
              ->leftJoin('users as responsableeliminado', 'responsableeliminado.id', 'movimientointernodinero.idresponsble_eliminado')
              ->leftJoin('tienda', 'tienda.id', 'movimientointernodinero.idtienda')
              ->where('movimientointernodinero.idestadoeliminado', 2)
              ->where('movimientointernodinero.extorno_agrupado', false)
              ->where($where6)
              ->select(
                  DB::raw("'ELIM. MOVIMIENTO' as operacion"),
                  DB::raw("'--' as cuenta"),
                  'movimientointernodinero.fecha_eliminado as fechaextorno',
                  DB::raw("'--' as pago_cuota"),
                  'movimientointernodinero.monto as total_pagar',
                  'movimientointernodinero.banco as banco',
                  'movimientointernodinero.cuenta as cuenta_bancaria',
                  'movimientointernodinero.numerooperacion as numerooperacion',
                  DB::raw("CONCAT(movimientointernodinero.codigoprefijo, movimientointernodinero.codigo) as codigo"),
                  DB::raw("CONCAT_WS(' / ', NULLIF(movimientointernodinero.descripcion, ''), NULLIF(credito_fuenteretiro.nombre, '')) as nombrecliente"),
                  'responsable.codigo as codigoresponsable',
                  'tienda.nombreagencia as tiendanombre',
                  'responsableeliminado.codigo as codigoresponsableeliminado',
                  DB::raw("'' as fc")
              );

          $creditos_extornados = DB::table('credito_cobranzacuota')
              ->join('credito','credito.id','credito_cobranzacuota.idcredito')
              ->join('users as cliente','cliente.id','credito.idcliente')
              ->leftJoin('users as responsable','responsable.id','credito_cobranzacuota.idcajero')
              ->leftJoin('users as responsableeliminado','responsableeliminado.id','credito_cobranzacuota.idresponsableextorno')
              ->leftJoin('tienda','tienda.id','credito_cobranzacuota.idtienda')
              ->where('credito_cobranzacuota.idestadocredito_cobranzacuota',1)
              ->where('credito_cobranzacuota.idestadoextorno',2)
              ->where($where1)
              ->union($creditos)
              ->union($gastoadministrativooperativos)
              ->union($ingresoextraordinarios)
              ->union($asignaciones)
              ->union($movimientos)
              ->select(
                  DB::raw('CONCAT("EXT. CUOTAS") as operacion'),
                  DB::raw('CONCAT("C",credito.cuenta) as cuenta'),
                  'credito_cobranzacuota.fechaextorno as fechaextorno',
                  'credito_cobranzacuota.pago_cuota as pago_cuota',
                  'credito_cobranzacuota.total_pagar as total_pagar',
                  'credito_cobranzacuota.banco as banco',
                  'credito_cobranzacuota.cuenta as cuenta_bancaria',
                  'credito_cobranzacuota.numerooperacion as numerooperacion',
                  DB::raw('CONCAT("OP",LPAD(credito_cobranzacuota.codigo, 8, "0")) as codigo'),
                  'cliente.nombrecompleto as nombrecliente',
                  'responsable.codigo as codigoresponsable',
                  'tienda.nombreagencia as tiendanombre',
                  'responsableeliminado.codigo as codigoresponsableeliminado',
                  DB::raw("CASE
                      WHEN credito.idforma_credito = 1 THEN 'CP'
                      WHEN credito.idforma_credito = 2 THEN 'CNP'
                      WHEN credito.idforma_credito = 3 THEN 'CC'
                      ELSE ''
                  END as fc")
              )
              ->orderBy('fechaextorno','asc')
              ->get();
          
          $agencia = DB::table('tienda')->whereId($request->idagencia)->first();
          
          $pdf = PDF::loadView(sistema_view().'/operacionextornada/pdf_extorno',[
              'tienda' => $tienda,
              'agencia' => $agencia,
              'creditos_extornados' => $creditos_extornados,
              'fecha_inicio' => date("d-m-Y",strtotime(date($request->fecha_inicio))),
              'fecha_fin' => date("d-m-Y",strtotime(date($request->fecha_fin))),
          ]); 
          $pdf->setPaper('A4', 'landscape');
          return $pdf->stream('OPERACIONES_EXTORNADAS.pdf');
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        
    }


    public function destroy(Request $request, $idtienda, $id)
    {

    }
}
