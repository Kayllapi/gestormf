<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class CvreporteconsolidadoopeadminController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            $agencias = DB::table('tienda')->get();
            return view(sistema_view().'/cvreporteconsolidadoopeadmin/tabla',[
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
            $co_actual = cvconsolidadooperaciones($tienda,$request->idagencia,$request->corte);

            $fechaCorte = Carbon::createFromFormat('Y-m-d', $request->corte);
            $fechaAnterior = $fechaCorte->copy()->subDay()->format('Y-m-d');
            $co_anterior = DB::table('cvarqueocaja')
                ->where('idagencia', $request->idagencia)
                ->where('corte', $fechaAnterior)
                ->orderByDesc('id')
                ->first();
            if (!$co_anterior) {
                $ultimo = DB::table('cvarqueocaja')
                    ->where('idagencia', $request->idagencia)
                    ->orderByDesc('id')
                    ->first();

                if ($ultimo && $ultimo->corte >= $request->corte) {
                    $co_anterior = DB::table('cvarqueocaja')
                        ->where('idagencia', $request->idagencia)
                        ->where('corte', '<', $request->corte)
                        ->orderByDesc('corte')
                        ->orderByDesc('id')
                        ->first();
                } else {
                    $co_anterior = $ultimo;
                }
            }

            $data_actual = DB::table('cvmovimientointernodinero')
                ->where('idtienda',$idtienda)
                ->where('idfuenteretiro',6)
                ->first();

            $pdf = PDF::loadView(sistema_view().'/cvreporteconsolidadoopeadmin/pdf_reporte', compact(
                'co_actual',
                'co_anterior',
                'data_actual',
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('REPORTE_CONSOLIDADO_OPE_ADMIN.pdf');
        }
        else if($request->input('view') == 'validar_limites') {
            $co_actual = cvconsolidadooperaciones($tienda,$request->idagencia,$request->corte);
            if ($co_actual['saldos_reserva'] > $tienda->credito_limitemaximo_reserva) {
                $calculo = $co_actual['saldos_reserva'] - $tienda->credito_limitemaximo_reserva;
                $mensaje = 'El saldo de Reserva CF excede el límite máximo permitido. Depositar a Banco: '.$calculo;
                return $mensaje;
            }
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        
    }


    public function destroy(Request $request, $idtienda, $id)
    {

    }
}
