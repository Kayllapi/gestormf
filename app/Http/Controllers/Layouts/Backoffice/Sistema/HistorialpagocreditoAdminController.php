<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use App\Exports\ReportehistorialpagocreditoAdminExport;
use Maatwebsite\Excel\Facades\Excel;

class HistorialpagocreditoAdminController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            $agencias = DB::table('tienda')->get();
            return view(sistema_view().'/historialpagocreditoadmin/tabla',[
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
          $credito_cobranzacuotas = $this->obtenerPagosCredito($request);
          $agencia = DB::table('tienda')->whereId($request->idagencia)->first();

          $pdf = PDF::loadView(sistema_view().'/historialpagocreditoadmin/pdf_reporte',[
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
          $credito_cobranzacuotas = $this->obtenerPagosCredito($request);
          $agencia = DB::table('tienda')->whereId($request->idagencia)->first();

            return Excel::download(
                new ReportehistorialpagocreditoAdminExport(
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
     * Pagos de creditos para pdf_reporte y exportar_excel (misma consulta para ambos).
     * Los pagos de creditos castigados se muestran en "Historial de Pagos Castigados Admin"
     * (HistorialpagocreditoCastigadoAdminController), no aqui.
     * No se unen ubigeo ni el cajero: ninguno de los dos reportes usa esos datos.
     */
    private function obtenerPagosCredito(Request $request)
    {
        $where = [
            ['credito.castigo_estado','!=','CONFIRMADO'],
            ['credito_cobranzacuota.fecharegistro','>=',$request->fechainicio.' 00:00:00'],
            ['credito_cobranzacuota.fecharegistro','<=',$request->fechafin.' 23:59:59'],
        ];
        if($request->idagencia!=''){
            $where[] = ['credito.idtienda',$request->idagencia];
        }

        $credito_cobranzacuotas = DB::table('credito_cobranzacuota')
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

        $this->aplicarTotalesPorConcepto($credito_cobranzacuotas);

        return $credito_cobranzacuotas;
    }

    /**
     * Sobrescribe en cada pago los totales por concepto (capital, interes, cargo, comision,
     * tenencia, penalidad, compensatorio) con lo realmente cobrado (ver totales_conceptos_cobranzacuota):
     * los guardados en credito_cobranzacuota solo cubren las cuotas que el pago cerro, y quedan
     * en 0 para un Pago a Cuenta que no cierra ninguna.
     */
    private function aplicarTotalesPorConcepto($credito_cobranzacuotas)
    {
        $adelantos_por_pago = DB::table('credito_adelanto')
            ->whereIn('credito_adelanto.idcredito_cobranzacuota', $credito_cobranzacuotas->pluck('id'))
            ->get()
            ->groupBy('idcredito_cobranzacuota');

        foreach ($credito_cobranzacuotas as $pago) {
            $totales = totales_conceptos_cobranzacuota($pago, $adelantos_por_pago->get($pago->id, collect()));
            $pago->total_amortizacion  = $totales['capital'];
            $pago->total_interes       = $totales['interes'];
            $pago->total_cargo         = $totales['cargo'];
            $pago->total_comision      = $totales['comision'];
            $pago->total_tenencia      = $totales['tenencia'];
            $pago->total_penalidad     = $totales['penalidad'];
            $pago->total_compensatorio = $totales['compensatorio'];
        }
    }
}
