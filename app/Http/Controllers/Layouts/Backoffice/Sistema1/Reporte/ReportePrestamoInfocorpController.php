<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use App\Exports\ReportePrestamoInfocorpExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportePrestamoInfocorpController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        return view('layouts/backoffice/tienda/sistema/reporte/reporteprestamoinfocorp/index',[
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
    }

    public function edit(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $prestamocreditos_cliente = DB::table('s_prestamo_credito')
            ->join('users as cliente','cliente.id','s_prestamo_credito.idcliente')
            ->join('ubigeo','ubigeo.id','cliente.idubigeo')
            ->where('s_prestamo_credito.idestadocobranza','<>', 2)
            ->where('s_prestamo_credito.idestadocredito', 4)
            ->where('s_prestamo_credito.idestadoaprobacion', 1)
            ->where('s_prestamo_credito.idestadodesembolso', 1)
            ->where('s_prestamo_credito.idtienda', $idtienda)
            ->select(
                'cliente.id as idcliente',
                'cliente.idtipopersona as idtipopersona',
                'cliente.identificacion as cliente_identificacion',
                'cliente.apellidos as cliente_apellido',
                'cliente.apellidopaterno as cliente_apellidopaterno',
                'cliente.apellidomaterno as cliente_apellidomaterno',
                'cliente.nombre as cliente_nombre',
                'cliente.numerotelefono as cliente_numerotelefono',
                'cliente.direccion as cliente_direccion',
                'ubigeo.distrito as cliente_distrito',
                'ubigeo.provincia as cliente_provincia',
                'ubigeo.departamento as cliente_departamento',
            )
            ->orderBy('s_prestamo_credito.fechadesembolsado', 'asc')
            ->orderBy('cliente.apellidos', 'asc')
            ->distinct()
            ->get();
      
        $prestamomoras_tabla = [];
      
        foreach($prestamocreditos_cliente as $value){
                    $prestamocreditos = DB::table('s_prestamo_credito')
                        ->where('s_prestamo_credito.idestadocobranza','<>', 2)
                        ->where('s_prestamo_credito.idestadocredito', 4)
                        ->where('s_prestamo_credito.idestadoaprobacion', 1)
                        ->where('s_prestamo_credito.idestadodesembolso', 1)
                        ->where('s_prestamo_credito.idtienda', $idtienda)
                        ->where('s_prestamo_credito.idcliente', $value->idcliente)
                        ->select(
                            's_prestamo_credito.id as idcredito',
                            's_prestamo_credito.numerocuota as numerocuota',
                            's_prestamo_credito.fechadesembolsado as fechadesembolsado',
                        )
                        //->orderBy('s_prestamo_credito.cronograma_total_vencida_atraso', 'asc')
                        //->orderBy('s_prestamo_credito.cronograma_total_restante_atraso', 'asc')
                        ->orderBy('s_prestamo_credito.fechadesembolsado', 'asc')
                        ->get();
            
                    $fechadesembolsado = '';
                    $deudavigente = 0;
                    $deudadirectavencida1 = 0;
                    $deudadirectavencida2 = 0;
                    $numerodiasvencidos = 0;
                    $fechavencimiento = '';
                    $totalvencido = 0;
                    $calificacion = '';
                    foreach($prestamocreditos as $valuedetalle){
                      
                        $cronograma = prestamo_cobranza_cronograma($idtienda,$valuedetalle->idcredito,0,0,1,$valuedetalle->numerocuota);
                      
                        $totalvencido = $totalvencido+($cronograma['total_vencida_cuotaapagar']);
                    
                        if($request->input('view') == 'reporte_equifax'){
                            $fechadesembolsado = date_format(date_create($valuedetalle->fechadesembolsado), "m/Y");
                        }
                        elseif($request->input('view') == 'reporte_sentinel'){
                            $fechadesembolsado = date_format(date_create($valuedetalle->fechadesembolsado), "Y/m");
                        }
                        $deudavigente = $deudavigente+number_format($cronograma['total_pendiente_cuotaapagar'], 2, '.', '');
                        $numerodiasvencidos = $numerodiasvencidos+($cronograma['primeratraso']<0?0:$cronograma['primeratraso']);
                        $fechavencimiento = $cronograma['creditosolicitud']->ultimafecha;
                    }
              
                    if($numerodiasvencidos>=0 && $numerodiasvencidos<=30){
                        $deudadirectavencida1 = number_format($totalvencido, 2, '.', '');
                    }
                    elseif($numerodiasvencidos> 30){
                        $deudadirectavencida2 = number_format($totalvencido, 2, '.', '');
                    }
          
                    if($numerodiasvencidos>=0 && $numerodiasvencidos<=7){
                        $calificacion = 0;
                    }
                    elseif($numerodiasvencidos>=8 && $numerodiasvencidos<=30){
                        $calificacion = 1;
                    }
                    elseif($numerodiasvencidos>=31 && $numerodiasvencidos<=60){
                        $calificacion = 2;
                    }
                    elseif($numerodiasvencidos>=61 && $numerodiasvencidos<=120){
                        $calificacion = 3;
                    }
                    elseif($numerodiasvencidos>120){
                        $calificacion = 4;
                    }
          
                    $prestamomoras_tabla[] = [
                        'fechaperiodo' => $fechadesembolsado,
                        'codigoentidad' => '79056',
                        'codigotarjeta' => '',
                        'codigoprestamo' => '',
                        'codigoagencia' => '',
                        'tipodocumento' => $value->idtipopersona==1?'1':($value->idtipopersona==2?'6':'3'),
                        'documentoentidad' => $value->cliente_identificacion,
                        'razonsocial' => $value->idtipopersona==2?$value->cliente_apellido:'',
                        'apellidopaterno' => ($value->idtipopersona==1 or $value->idtipopersona==3)?$value->cliente_apellidopaterno:'',
                        'apellidomaterno' => ($value->idtipopersona==1 or $value->idtipopersona==3)?$value->cliente_apellidomaterno:'',
                        'nombres' => ($value->idtipopersona==1 or $value->idtipopersona==3)?$value->cliente_nombre:'',
                        'tipopersona' => $value->idtipopersona,
                        'modalidadcredito' => '5',
                        'deudavigente' => number_format($deudavigente, 2, '.', ''),
                        'deudadirectavencida1' => $deudadirectavencida1!=0?number_format($deudadirectavencida1, 2, '.', ''):'',
                        'deudadirectavencida2' => $deudadirectavencida2!=0?number_format($deudadirectavencida2, 2, '.', ''):'',
                        'calificacion' => $calificacion,
                        'numerodiasvencidos' => $numerodiasvencidos,
                        'direccion' => $value->cliente_direccion,
                        'distrito' => $value->cliente_distrito,
                        'provincia' => $value->cliente_provincia,
                        'departamento' => $value->cliente_departamento,
                        'telefono' => $value->cliente_numerotelefono,
                        'fechavencimiento' => $fechavencimiento,
                    ];  
        }
      
                    

        if ($request->input('view') == 'reporte') {
            if($request->input('listarpor') == 1){
                return view('layouts/backoffice/tienda/sistema/reporte/reporteprestamoinfocorp/tablaequifaxpdf', [
                    'tienda' => $tienda,
                    'prestamomoras' => $prestamomoras_tabla,
                ]);
            }
            elseif($request->input('listarpor') == 2){
                return view('layouts/backoffice/tienda/sistema/reporte/reporteprestamoinfocorp/tablasentinelpdf', [
                    'tienda' => $tienda,
                    'prestamomoras' => $prestamomoras_tabla,
                ]);
            }
        }elseif ($request->input('view') == 'excel') {
            return Excel::download(new ReportePrestamoInfocorpExport($prestamomoras_tabla,$request->input('listarpor')),'REPORTE_EQUIFAX.xls');
        }
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
