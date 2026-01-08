<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class ReporteAhorroRecaudacionController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        return view('layouts/backoffice/tienda/sistema/reporte/reporteahorrorecaudacion/index',[
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
            $where = [];
            $where1 = [];
            if($request->input('fechainicio')!=''){
                $where[] = ['s_prestamo_ahorrorecaudacionlibre.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
                $where1[] = ['s_prestamo_ahorroretirolibre.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
            }
            if($request->input('fechafin')!=''){
                $where[] = ['s_prestamo_ahorrorecaudacionlibre.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
                $where1[] = ['s_prestamo_ahorroretirolibre.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
            }

            if($request->input('listarpor')==1){
              
                $s_prestamo_ahorroretirolibres = DB::table('s_prestamo_ahorroretirolibre')
                    ->join('s_prestamo_ahorro', 's_prestamo_ahorro.id', 's_prestamo_ahorroretirolibre.idprestamo_ahorro')
                    ->join('users as cliente', 'cliente.id', 's_prestamo_ahorroretirolibre.idcliente')
                    ->join('users as cajero', 'cajero.id', 's_prestamo_ahorroretirolibre.idcajero')
                    ->where('s_prestamo_ahorroretirolibre.idtienda', $tienda->id)
                    ->where('s_prestamo_ahorroretirolibre.idestadoahorroretirolibre',2)
                    ->where('s_prestamo_ahorroretirolibre.idestado',1)
                    ->where($where1)
                    ->select(
                        's_prestamo_ahorroretirolibre.fecharegistro as fecharegistro',
                        's_prestamo_ahorroretirolibre.codigo as codigo',
                        's_prestamo_ahorroretirolibre.monto_efectivo as monto_efectivo',
                        's_prestamo_ahorroretirolibre.monto_deposito as monto_deposito',
                        's_prestamo_ahorro.codigo as ahorrocodigo',
                        'cliente.identificacion as cliente_identificacion',
                        DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente'),
                        'cajero.identificacion as cajero_identificacion',
                        'cajero.apellidos as cajero_apellidos',
                        'cajero.nombre as cajero_nombre',
                        DB::raw('CONCAT(cajero.apellidos, ", ", cajero.nombre) as cajero'),
                        DB::raw('CONCAT("RETIRO") as tipo'),
                    );

                $ahorrorecaudaciones = DB::table('s_prestamo_ahorrorecaudacionlibre')
                    ->join('s_prestamo_ahorro', 's_prestamo_ahorro.id', 's_prestamo_ahorrorecaudacionlibre.idprestamo_ahorro')
                    ->join('users as cliente', 'cliente.id', 's_prestamo_ahorrorecaudacionlibre.idcliente')
                    ->join('users as cajero', 'cajero.id', 's_prestamo_ahorrorecaudacionlibre.idcajero')
                    ->where('s_prestamo_ahorrorecaudacionlibre.idtienda', $tienda->id)
                    ->where('s_prestamo_ahorrorecaudacionlibre.idestadorecaudacion',2)
                    ->where('s_prestamo_ahorrorecaudacionlibre.idestado',1)
                    ->union($s_prestamo_ahorroretirolibres)
                    ->where($where)
                    ->select(
                        's_prestamo_ahorrorecaudacionlibre.fecharegistro as fecharegistro',
                        's_prestamo_ahorrorecaudacionlibre.codigo as codigo',
                        's_prestamo_ahorrorecaudacionlibre.monto_efectivo as monto_efectivo',
                        's_prestamo_ahorrorecaudacionlibre.monto_deposito as monto_deposito',
                        's_prestamo_ahorro.codigo as ahorrocodigo',
                        'cliente.identificacion as cliente_identificacion',
                        DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente'),
                        'cajero.identificacion as cajero_identificacion',
                        'cajero.apellidos as cajero_apellidos',
                        'cajero.nombre as cajero_nombre',
                        DB::raw('CONCAT(cajero.apellidos, ", ", cajero.nombre) as cajero'),
                        DB::raw('CONCAT("RECAUDACIÓN") as tipo'),
                    )
                    ->orderBy('fecharegistro','asc')
                    ->get();
  
                $totalfinal_total = 0;
                $total_recaudacion = 0;
                $total_retiro = 0;
                $total_total = 0;
              
                $ahorrorecaudaciones_tabla = [];
                foreach($ahorrorecaudaciones as $value){
                    $monto_total = 0;
                    if($value->tipo=='RECAUDACIÓN'){
                        $monto_total = number_format($value->monto_efectivo+$value->monto_deposito, 2, '.', '');
                        $total_recaudacion = $total_recaudacion+$monto_total;
                    }
                    elseif($value->tipo=='RETIRO'){
                        $monto_total = number_format(-$value->monto_efectivo+$value->monto_deposito, 2, '.', '');
                        $total_retiro = $total_retiro+$monto_total;
                    }
                    $ahorrorecaudaciones_tabla[] = [
                        'tipo' => $value->tipo,
                        'cajero_identificacion' => $value->cajero_identificacion,
                        'cajero_apellidos' => $value->cajero_apellidos,
                        'cajero_nombre' => $value->cajero_nombre,
                        'cajero' => $value->cajero,
                        'cliente_identificacion' => $value->cliente_identificacion,
                        'cliente' => $value->cliente,
                        'fecharegistro' => date_format(date_create($value->fecharegistro), "d/m/Y h:i A"),
                        'codigo' => str_pad($value->codigo, 8, "0", STR_PAD_LEFT),
                        'ahorrocodigo' => str_pad($value->ahorrocodigo, 8, "0", STR_PAD_LEFT),
                        'monto_total' => number_format($value->monto_efectivo+$value->monto_deposito, 2, '.', ''),
                    ];
                    $total_total = $total_total+number_format($value->monto_efectivo+$value->monto_deposito, 2, '.', '');
                }
                $totalfinal_total = $total_total;
            }
            elseif($request->input('listarpor')==2){
                if($request->input('idcliente')!=''){
                    $where[] = ['cliente.id',$request->input('idcliente')];
                    $where1[] = ['cliente.id',$request->input('idcliente')];
                }
              
                $s_prestamo_ahorroretirolibre = DB::table('s_prestamo_ahorroretirolibre')
                    ->join('users as cliente', 'cliente.id', 's_prestamo_ahorroretirolibre.idcliente')
                    ->where('s_prestamo_ahorroretirolibre.idtienda', $idtienda)
                    ->where('s_prestamo_ahorroretirolibre.idestadoahorroretirolibre',2)
                    ->where('s_prestamo_ahorroretirolibre.idestado',1)
                    ->where($where1)
                    ->select(
                        'cliente.id as idcliente',
                        'cliente.identificacion as cliente_identificacion',
                        DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente'),
                    );
              
                $ahorrorecaudaciones = DB::table('s_prestamo_ahorrorecaudacionlibre')
                    ->join('users as cliente', 'cliente.id', 's_prestamo_ahorrorecaudacionlibre.idcliente')
                    ->where('s_prestamo_ahorrorecaudacionlibre.idtienda', $idtienda)
                    ->where('s_prestamo_ahorrorecaudacionlibre.idestadorecaudacion',2)
                    ->where('s_prestamo_ahorrorecaudacionlibre.idestado',1)
                    ->union($s_prestamo_ahorroretirolibre)
                    ->where($where)
                    ->select(
                        'cliente.id as idcliente',
                        'cliente.identificacion as cliente_identificacion',
                        DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente'),
                    )
                    ->orderBy('cliente','asc')
                    ->distinct()
                    ->get();
              
                $totalfinal_total = 0;
                $ahorrorecaudaciones_tabla = [];
                foreach($ahorrorecaudaciones as $value){
                  
                    $s_prestamo_ahorroretirolibres = DB::table('s_prestamo_ahorroretirolibre')
                        ->join('s_prestamo_ahorro', 's_prestamo_ahorro.id', 's_prestamo_ahorroretirolibre.idprestamo_ahorro')
                        ->join('users as cajero', 'cajero.id', 's_prestamo_ahorroretirolibre.idcajero')
                        ->where('s_prestamo_ahorroretirolibre.idtienda', $tienda->id)
                        ->where('s_prestamo_ahorroretirolibre.idestadoahorroretirolibre',2)
                        ->where('s_prestamo_ahorroretirolibre.idestado',1)
                        ->where('s_prestamo_ahorroretirolibre.idcliente',$value->idcliente)
                        ->where($where1)
                        ->select(
                            's_prestamo_ahorroretirolibre.fecharegistro as fecharegistro',
                            's_prestamo_ahorroretirolibre.codigo as codigo',
                            's_prestamo_ahorroretirolibre.monto_efectivo as monto_efectivo',
                            's_prestamo_ahorroretirolibre.monto_deposito as monto_deposito',
                            's_prestamo_ahorro.codigo as ahorrocodigo',
                            'cajero.identificacion as cajero_identificacion',
                            DB::raw('CONCAT(cajero.apellidos, ", ", cajero.nombre) as cajero'),
                            DB::raw('CONCAT("RETIRO") as tipo'),
                        );
                  
                    $detalleahorrorecaudaciones = DB::table('s_prestamo_ahorrorecaudacionlibre')
                        ->join('s_prestamo_ahorro', 's_prestamo_ahorro.id', 's_prestamo_ahorrorecaudacionlibre.idprestamo_ahorro')
                        ->join('users as cajero', 'cajero.id', 's_prestamo_ahorrorecaudacionlibre.idcajero')
                        ->where('s_prestamo_ahorrorecaudacionlibre.idtienda', $tienda->id)
                        ->where('s_prestamo_ahorrorecaudacionlibre.idestadorecaudacion',2)
                        ->where('s_prestamo_ahorrorecaudacionlibre.idestado',1)
                        ->where('s_prestamo_ahorrorecaudacionlibre.idcliente',$value->idcliente)
                        ->union($s_prestamo_ahorroretirolibres)
                        ->where($where)
                        ->select(
                            's_prestamo_ahorrorecaudacionlibre.fecharegistro as fecharegistro',
                            's_prestamo_ahorrorecaudacionlibre.codigo as codigo',
                            's_prestamo_ahorrorecaudacionlibre.monto_efectivo as monto_efectivo',
                            's_prestamo_ahorrorecaudacionlibre.monto_deposito as monto_deposito',
                            's_prestamo_ahorro.codigo as ahorrocodigo',
                            'cajero.identificacion as cajero_identificacion',
                            DB::raw('CONCAT(cajero.apellidos, ", ", cajero.nombre) as cajero'),
                            DB::raw('CONCAT("RECAUDACIÓN") as tipo'),
                        )
                        ->orderBy('fecharegistro','asc')
                        ->get();
                  
                    $total_recaudacion = 0;
                    $total_retiro = 0;
                    $total_total = 0;
                  
                    $detalleahorrorecaudaciones_tabla = [];
                    foreach($detalleahorrorecaudaciones as $valuedetalle){
                        $monto_total = 0;
                        if($valuedetalle->tipo=='RECAUDACIÓN'){
                            $monto_total = number_format($valuedetalle->monto_efectivo+$valuedetalle->monto_deposito, 2, '.', '');
                            $total_recaudacion = $total_recaudacion+$monto_total;
                        }
                        elseif($valuedetalle->tipo=='RETIRO'){
                            $monto_total = number_format(-$valuedetalle->monto_efectivo+$valuedetalle->monto_deposito, 2, '.', '');
                            $total_retiro = $total_retiro+$monto_total;
                        }
                        $detalleahorrorecaudaciones_tabla[] = [
                            'tipo' => $valuedetalle->tipo,
                            'cajero_identificacion' => $valuedetalle->cajero_identificacion,
                            'cajero' => $valuedetalle->cajero,
                            'fecharegistro' => date_format(date_create($valuedetalle->fecharegistro), "d/m/Y h:i A"),
                            'codigo' => str_pad($valuedetalle->codigo, 8, "0", STR_PAD_LEFT),
                            'ahorrocodigo' => str_pad($valuedetalle->ahorrocodigo, 8, "0", STR_PAD_LEFT),
                            'monto_total' => $monto_total,
                        ];
                        $total_total = $total_total+$monto_total;
                    }
                    
                    if(count($detalleahorrorecaudaciones)>0){
                        $ahorrorecaudaciones_tabla[] = [
                            'cliente_identificacion' => $value->cliente_identificacion,
                            'cliente' => $value->cliente,
                            'total_recaudacion' => number_format($total_recaudacion, 2, '.', ''),
                            'total_retiro' => number_format($total_retiro, 2, '.', ''),
                            'total_total' => number_format($total_total, 2, '.', ''),
                            'detalle' => $detalleahorrorecaudaciones_tabla
                        ];
                        $totalfinal_total = $totalfinal_total+number_format($total_total, 2, '.', '');
                    }
                }
            }
            elseif($request->input('listarpor')==3){
                if($request->input('idcajero')!=''){
                    $where[] = ['cajero.id',$request->input('idcajero')];
                    $where1[] = ['cajero.id',$request->input('idcajero')];
                }
              
                $s_prestamo_ahorroretirolibre = DB::table('s_prestamo_ahorroretirolibre')
                    ->join('users as cajero', 'cajero.id', 's_prestamo_ahorroretirolibre.idcajero')
                    ->where('s_prestamo_ahorroretirolibre.idtienda', $idtienda)
                    ->where('s_prestamo_ahorroretirolibre.idestadoahorroretirolibre',2)
                    ->where('s_prestamo_ahorroretirolibre.idestado',1)
                    ->where($where1)
                    ->select(
                        'cajero.id as idcajero',
                        'cajero.identificacion as cajero_identificacion',
                        DB::raw('CONCAT(cajero.apellidos, ", ", cajero.nombre) as cajero'),
                    );
              
                $s_prestamo_ahorrorecaudacionlibre = DB::table('s_prestamo_ahorrorecaudacionlibre')
                    ->join('users as cajero', 'cajero.id', 's_prestamo_ahorrorecaudacionlibre.idcajero')
                    ->where('s_prestamo_ahorrorecaudacionlibre.idtienda', $idtienda)
                    ->where('s_prestamo_ahorrorecaudacionlibre.idestadorecaudacion',2)
                    ->where('s_prestamo_ahorrorecaudacionlibre.idestado',1)
                    ->union($s_prestamo_ahorroretirolibre)
                    ->where($where)
                    ->select(
                        'cajero.id as idcajero',
                        'cajero.identificacion as cajero_identificacion',
                        DB::raw('CONCAT(cajero.apellidos, ", ", cajero.nombre) as cajero'),
                    )
                    ->orderBy('cajero','asc')
                    ->distinct()
                    ->get();
              
                $totalfinal_total = 0;
                $ahorrorecaudaciones_tabla = [];
                foreach($s_prestamo_ahorrorecaudacionlibre as $value){
                  
                    $s_prestamo_ahorroretirolibres = DB::table('s_prestamo_ahorroretirolibre')
                        ->join('s_prestamo_ahorro', 's_prestamo_ahorro.id', 's_prestamo_ahorroretirolibre.idprestamo_ahorro')
                        ->join('users as cliente', 'cliente.id', 's_prestamo_ahorroretirolibre.idcliente')
                        ->where('s_prestamo_ahorroretirolibre.idtienda', $tienda->id)
                        ->where('s_prestamo_ahorroretirolibre.idestadoahorroretirolibre',2)
                        ->where('s_prestamo_ahorroretirolibre.idestado',1)
                        ->where('s_prestamo_ahorroretirolibre.idcajero',$value->idcajero)
                        ->where($where1)
                        ->select(
                            's_prestamo_ahorroretirolibre.fecharegistro as fecharegistro',
                            's_prestamo_ahorroretirolibre.codigo as codigo',
                            's_prestamo_ahorroretirolibre.monto_efectivo as monto_efectivo',
                            's_prestamo_ahorroretirolibre.monto_deposito as monto_deposito',
                            's_prestamo_ahorro.codigo as ahorrocodigo',
                            'cliente.identificacion as cliente_identificacion',
                            DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente'),
                            DB::raw('CONCAT("RETIRO") as tipo'),
                        );
                  
                    $detalleahorrorecaudaciones = DB::table('s_prestamo_ahorrorecaudacionlibre')
                        ->join('s_prestamo_ahorro', 's_prestamo_ahorro.id', 's_prestamo_ahorrorecaudacionlibre.idprestamo_ahorro')
                        ->join('users as cliente', 'cliente.id', 's_prestamo_ahorrorecaudacionlibre.idcliente')
                        ->where('s_prestamo_ahorrorecaudacionlibre.idtienda', $tienda->id)
                        ->where('s_prestamo_ahorrorecaudacionlibre.idestadorecaudacion',2)
                        ->where('s_prestamo_ahorrorecaudacionlibre.idestado',1)
                        ->where('s_prestamo_ahorrorecaudacionlibre.idcajero',$value->idcajero)
                        ->union($s_prestamo_ahorroretirolibres)
                        ->where($where)
                        ->select(
                            's_prestamo_ahorrorecaudacionlibre.fecharegistro as fecharegistro',
                            's_prestamo_ahorrorecaudacionlibre.codigo as codigo',
                            's_prestamo_ahorrorecaudacionlibre.monto_efectivo as monto_efectivo',
                            's_prestamo_ahorrorecaudacionlibre.monto_deposito as monto_deposito',
                            's_prestamo_ahorro.codigo as ahorrocodigo',
                            'cliente.identificacion as cliente_identificacion',
                            DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente'),
                            DB::raw('CONCAT("RECAUDACIÓN") as tipo'),
                        )
                        ->orderBy('fecharegistro','asc')
                        ->get();
                  
                    $total_recaudacion = 0;
                    $total_retiro = 0;
                    $total_total = 0;
                  
                    $detalleahorrorecaudaciones_tabla = [];
                    foreach($detalleahorrorecaudaciones as $valuedetalle){
                        $monto_total = 0;
                        if($valuedetalle->tipo=='RECAUDACIÓN'){
                            $monto_total = number_format($valuedetalle->monto_efectivo+$valuedetalle->monto_deposito, 2, '.', '');
                            $total_recaudacion = $total_recaudacion+$monto_total;
                        }
                        elseif($valuedetalle->tipo=='RETIRO'){
                            $monto_total = number_format(-$valuedetalle->monto_efectivo+$valuedetalle->monto_deposito, 2, '.', '');
                            $total_retiro = $total_retiro+$monto_total;
                        }
                        $detalleahorrorecaudaciones_tabla[] = [
                            'tipo' => $valuedetalle->tipo,
                            'cliente_identificacion' => $valuedetalle->cliente_identificacion,
                            'cliente' => $valuedetalle->cliente,
                            'fecharegistro' => date_format(date_create($valuedetalle->fecharegistro), "d/m/Y h:i A"),
                            'codigo' => str_pad($valuedetalle->codigo, 8, "0", STR_PAD_LEFT),
                            'ahorrocodigo' => str_pad($valuedetalle->ahorrocodigo, 8, "0", STR_PAD_LEFT),
                            'monto_total' => $monto_total,
                        ];
                        $total_total = $total_total+$monto_total;
                    }
                    
                    if(count($detalleahorrorecaudaciones)>0){
                        $ahorrorecaudaciones_tabla[] = [
                            'cajero_identificacion' => $value->cajero_identificacion,
                            'cajero' => $value->cajero,
                            'total_recaudacion' => number_format($total_recaudacion, 2, '.', ''),
                            'total_retiro' => number_format($total_retiro, 2, '.', ''),
                            'total_total' => number_format($total_total, 2, '.', ''),
                            'detalle' => $detalleahorrorecaudaciones_tabla
                        ];
                        $totalfinal_total = $totalfinal_total+number_format($total_total, 2, '.', '');
                    }
                }
            }
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/reporte/reporteahorrorecaudacion/tablapdf',[
                'tienda' => $tienda,
                'ahorrorecaudaciones' => $ahorrorecaudaciones_tabla,
                'totalfinal_total' => number_format($totalfinal_total, 2, '.', ''),
                'listarpor' => $request->input('listarpor'),
                'fechainicio' => $request->input('fechainicio'),
                'fechafin' => $request->input('fechafin'),
            ]);
            return $pdf->stream('REPORTE_DE_COBRANZA.pdf');
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
