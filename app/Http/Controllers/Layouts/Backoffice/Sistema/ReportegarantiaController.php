<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use App\Exports\ReportehistorialpagocreditoExport;
use Maatwebsite\Excel\Facades\Excel;

class ReporteGarantiaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            
            $agencias = DB::table('tienda')->get();
            return view(sistema_view().'/reportegarantia/tabla',[
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
        if($id == 'show_asesor'){
            $where = [];
            if($request->idtienda!=0){
                $where[] = ['users_permiso.idtienda', $request->idtienda];
            }
          
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[3,4,7,11])
                ->where($where)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();
          
            $html = '<option></option><option value="0">TODO</option>';
            foreach($usuarios as $value){
                $html .= '<option value="'.$value->id.'">'.$value->nombrecompleto.' ('.$value->nombrepermiso.')</option>';
            }
            return $html;
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->input('view') == 'pdf_reporte'){
          $where = [];
          if($request->idagencia!='' && $request->idagencia!=0){
              $where[] = ['credito.idtienda',$request->idagencia];
          }
          if($request->idmodalidad!='TODO'){
              $where[] = ['credito.idforma_credito',$request->idmodalidad];
          }
          if($request->idasesor!='' && $request->idasesor!=0){
              $where[] = ['credito.idasesor',$request->idasesor];
          }
          
          $credito_garantias = DB::table('credito_garantia')
              ->join('credito','credito.id','credito_garantia.idcredito')
              ->leftJoin('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
              // Obtener los creditos pendientes en una sola lectura del cronograma.
              ->leftJoinSub(DB::table('credito_cronograma')
                  ->select('idcredito')
                  ->whereIn('idestadocredito_cronograma', [1, 3])
                  ->distinct(), 'creditos_pendientes', function ($join) {
                      $join->on('creditos_pendientes.idcredito', '=', 'credito.id');
                  })
              ->where($where)
              ->where('credito.estado','DESEMBOLSADO')
              //->whereIn('credito_garantia.idestadoentrega',[1,2])
              //->where('credito.idestadocredito',2)
              //->where('credito.idforma_credito',1)
              ->select(
                  'credito_garantia.id',
                  'credito_garantia.idcredito',
                  'credito_garantia.tipo',
                  'credito_garantia.idestadoentrega',
                  'credito_garantia.garantias_idestado_garantia',
                  'credito_garantia.garantias_codigo',
                  'credito_garantia.descripcion',
                  'credito_garantia.garantias_serie_motor_partida',
                  'credito_garantia.garantias_placa',
                  'credito_garantia.garantias_accesorio_doc',
                  'credito_garantia.garantias_detalle_garantia',
                  'credito_garantia.garantias_cobertura',
                  'credito_garantia.garantias_valorcomercial',
                  'credito.clientenombrecompleto as clientenombrecompleto',
                  'credito.clienteidentificacion as clienteidentificacion',
                  'credito.avalnombrecompleto as avalnombrecompleto',
                  'credito.avalidentificacion as avalidentificacion',
                  'credito.fecha_cancelado as fecha_cancelado',
                  'credito.monto_solicitado as monto_solicitado',
                  'credito.cuenta as cuentacredito',
                  'credito.idforma_credito as idforma_credito',
                  'credito.cuotas as cuotas',
                  'credito.total_pendientepago as total_pendientepago',
                  'credito.idtienda as idtienda',
                  'credito_prendatario.modalidad as modalidadproductocredito',
                  // Sin cuotas de estado 1 o 3, select_cronograma devuelve deuda cero.
                  'creditos_pendientes.idcredito as tiene_cuotas_pendientes',
              )
             ->get();
          
          $agencia = DB::table('tienda')->whereId($request->idagencia)->first();
          $asesor = DB::table('users')->whereId($request->idasesor)->first();
          
          $pdf = PDF::loadView(sistema_view().'/reportegarantia/pdf_reporte',[
              'tienda' => $tienda,
              'agencia' => $agencia,
              'credito_garantias' => $credito_garantias,
              'asesor' => $asesor,
              'idmodalidad' => $request->idmodalidad,
          ]); 
          $pdf->setPaper('A4', 'landscape');
          return $pdf->stream('HISTORIAL_PAGO_CREDITOS.pdf');
        }
        else if( $request->input('view') == 'exportar_excel' ){
              
            
          $where = [];
          if($request->idagencia!=''){
              $where[] = ['credito.idtienda',$request->idagencia];
          }
          $where[] = ['credito_cobranzacuota.fecharegistro','>=',$request->fechainicio.' 00:00:00'];
          $where[] = ['credito_cobranzacuota.fecharegistro','<=',$request->fechafin.' 23:59:59'];
          $where2[] = ['credito_cobranzacuota.fecharegistro','>=',$request->fechainicio.' 00:00:00'];
          $where2[] = ['credito_cobranzacuota.fecharegistro','<=',$request->fechafin.' 23:59:59'];
          
          $credito_cobranzacuotas = DB::table('credito_cobranzacuota')
              ->join('credito','credito.id','credito_cobranzacuota.idcredito')
              ->join('users as cliente','cliente.id','credito.idcliente')
              ->leftjoin('users as cajero','cajero.id','credito_cobranzacuota.idcajero')
              ->join('ubigeo','ubigeo.id','cliente.idubigeo')
              ->where('credito_cobranzacuota.idestadocredito_cobranzacuota',1)
              ->where('credito_cobranzacuota.idestadoextorno',0)
              ->where($where)
              ->select(
                  'credito_cobranzacuota.*',
            
                  'credito.cuenta as cuentacredito',
                  'credito.idforma_credito as idforma_credito',
                  'cliente.id as idcliente',
                  'cliente.identificacion as identificacion',
                  'cliente.nombrecompleto as nombrecliente',
                  'cliente.direccion as clientedireccion',
                  'ubigeo.nombre as ubigeonombre',
                  'cajero.codigo as usuariocajero',
              )
              ->orderBy('credito_cobranzacuota.id','asc')
              ->get();
          
          $agencia = DB::table('tienda')->whereId($request->idagencia)->first();
        
            return Excel::download(
                new ReportehistorialpagocreditoExport(
                    $tienda,
                    $agencia,
                    $credito_cobranzacuotas,
                    $request->fechainicio,
                    $request->fechafin,
                    'HISTORIAL DE PAGO DETALLADO DE CRÉDITOS'
                ),
                'historial_pago_credito.xls'
            );
        } 
    }

    public function update(Request $request, $idtienda, $id)
    {
        
    }


    public function destroy(Request $request, $idtienda, $id)
    {

    }
}
