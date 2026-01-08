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

class HistorialpagocreditoController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            
            $agencias = DB::table('tienda')->get();
            return view(sistema_view().'/historialpagocredito/tabla',[
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
        if($request->input('view') == 'pdf_reporte'){
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
          
          $pdf = PDF::loadView(sistema_view().'/historialpagocredito/pdf_reporte',[
              'tienda' => $tienda,
              'agencia' => $agencia,
              'credito_cobranzacuotas' => $credito_cobranzacuotas,
              'fechainicio' => $request->fechainicio,
              'fechafin' => $request->fechafin,
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
