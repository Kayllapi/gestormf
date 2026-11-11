<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use App\Exports\ReportehistorialpagocreditoCastigadoExport;
use Maatwebsite\Excel\Facades\Excel;

class HistorialpagocreditoCastigadoController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            
            $agencias = DB::table('tienda')->get();
            return view(sistema_view().'/historialpagocreditocastigado/tabla',[
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
          $credito_cobranzacuotas = $this->obtenerPagosCreditoCastigado($request);
          $agencia = DB::table('tienda')->whereId($request->idagencia)->first();

          $pdf = PDF::loadView(sistema_view().'/historialpagocreditocastigado/pdf_reporte',[
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
          $credito_cobranzacuotas = $this->obtenerPagosCreditoCastigado($request);
          $agencia = DB::table('tienda')->whereId($request->idagencia)->first();

            return Excel::download(
                new ReportehistorialpagocreditoCastigadoExport(
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

    /**
     * Pagos de creditos castigados para pdf_reporte y exportar_excel (misma consulta para ambos).
     * Solo se muestran los pagos de creditos castigados; los demas se ven en HistorialpagocreditoController.
     * No se unen ubigeo ni el cajero: ninguno de los dos reportes usa esos datos.
     */
    private function obtenerPagosCreditoCastigado(Request $request)
    {
        $where = [
            ['credito.castigo_estado','CONFIRMADO'],
            ['credito_cobranzacuota.fecharegistro','>=',$request->fechainicio.' 00:00:00'],
            ['credito_cobranzacuota.fecharegistro','<=',$request->fechafin.' 23:59:59'],
        ];
        if($request->idagencia!=''){
            $where[] = ['credito.idtienda',$request->idagencia];
        }

        return DB::table('credito_cobranzacuota')
            ->join('credito','credito.id','credito_cobranzacuota.idcredito')
            ->join('users as cliente','cliente.id','credito.idcliente')
            ->where('credito_cobranzacuota.idestadocredito_cobranzacuota',1)
            ->where('credito_cobranzacuota.idestadoextorno',0)
            ->where($where)
            ->select(
                'credito_cobranzacuota.id',
                'credito_cobranzacuota.idformapago',
                'credito_cobranzacuota.pago_cuota',
                'credito_cobranzacuota.fecharegistro',
                'credito_cobranzacuota.total_amortizacion',
                'credito_cobranzacuota.total_interes',
                'credito_cobranzacuota.total_cargo',
                'credito_cobranzacuota.total_comision',
                'credito_cobranzacuota.total_tenencia',
                'credito_cobranzacuota.total_penalidad',
                'credito_cobranzacuota.total_compensatorio',
                'credito_cobranzacuota.cobrar_cargo',
                'credito_cobranzacuota.total_pagar',
                'credito_cobranzacuota.total_recibido',
                'credito_cobranzacuota.opcion_pago',
                'credito_cobranzacuota.estadocargo',
                'credito.cuenta as cuentacredito',
                'credito.idforma_credito as idforma_credito',
                'cliente.identificacion as identificacion',
                'cliente.nombrecompleto as nombrecliente',
            )
            ->orderBy('credito_cobranzacuota.id','asc')
            ->get();
    }
}
