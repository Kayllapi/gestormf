<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class ReportePrestamoHistorialclienteController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        return view('layouts/backoffice/tienda/sistema/reporte/reporteprestamohistorialcliente/index',[
            'tienda' => $tienda,
        ]);
    }

    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
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
        if($id == 'showtablapdf') {
          
            $clientes = DB::table('users')
                ->where('users.idtienda',$idtienda)
                ->where('users.id',$request->input('idcliente'))
                ->get();
          
            $cliente_tabla = [];
            foreach($clientes as $value){
                $prestamoscredito = DB::table('s_prestamo_credito')
                    ->join('s_prestamo_frecuencia','s_prestamo_frecuencia.id','s_prestamo_credito.idprestamo_frecuencia')
                    ->where('s_prestamo_credito.idestado', 1)
                    ->where('s_prestamo_credito.idtienda', $idtienda)
                    ->where('s_prestamo_credito.idestadocredito', 4)
                    ->where('s_prestamo_credito.idestadoaprobacion', 1)
                    ->where('s_prestamo_credito.idestadodesembolso', 1)
                    ->where('s_prestamo_credito.idcliente',$value->id)
                    ->select(
                          's_prestamo_credito.*',
                          's_prestamo_frecuencia.nombre as frecuencianombre',
                    )
                    ->orderBy('s_prestamo_credito.fechadesembolsado','desc')
                    ->get();

                $credito_tabla = [];
                foreach($prestamoscredito as $valuedetalle){
                    $cronograma = prestamo_cobranza_cronograma($idtienda,$valuedetalle->id,0,0,1,$valuedetalle->numerocuota);
                    $cuotas_tabla= [];
                    $icuota = 1;
                    foreach($cronograma['cuotas_canceladas'] as $valuecronograma){
                        $cuotas_tabla[] = [
                            'numero' => $valuecronograma['tabla_numero'],
                            'atraso' => $valuecronograma['tabla_atraso'],
                        ];
                        $icuota++;
                    }
                    for($i=$icuota; $i<=$valuedetalle->numerocuota; $i++){
                        $cuotas_tabla[] = [
                            'numero' => str_pad($i, 2, "0", STR_PAD_LEFT),
                            'atraso' => '-',
                        ];
                    }
                  
                    $credito_tabla[] = [
                        'creditocodigo' => str_pad($valuedetalle->codigo, 8, "0", STR_PAD_LEFT),
                        'creditodesembolso' => $valuedetalle->monto,
                        'creditotasa' => $valuedetalle->tasa,
                        'creditocuota' => $valuedetalle->cuota,
                        'creditonumerocuota' => $valuedetalle->numerocuota.' CUOTAS',
                        'creditofrecuencia' => $valuedetalle->frecuencianombre,
                        'creditofechadesembolso' => date_format(date_create($valuedetalle->fechadesembolsado), "d/m/Y h:i A"),
                        'creditotipo' => $valuedetalle->tipocreditogenerado,
                        'cuotas' => $cuotas_tabla,
                    ];
                }
                $cliente_tabla[] = [
                    'cliente' => $value->identificacion.' - '.$value->apellidos.', '.$value->nombre,
                    'detalle' => $credito_tabla,
                ];
            }
          
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/reporte/reporteprestamohistorialcliente/tablapdf',[
                'tienda' => $tienda,
                'prestamocreditos' => $cliente_tabla,
            ]);
            return $pdf->stream('REPORTE_DE_HISTORIAL_DE_CLIENTE.pdf');
        }
    }

    public function edit(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    public function update(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    public function destroy(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }
}
