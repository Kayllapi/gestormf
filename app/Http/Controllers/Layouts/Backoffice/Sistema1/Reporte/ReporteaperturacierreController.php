<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class  ReporteaperturacierreController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        $s_cajas = DB::table('s_caja')
            ->where('idestado',1)
            ->where('idtienda',$idtienda)
            ->get();
      
        return view('layouts/backoffice/tienda/sistema/reporte/reporteaperturacierre/index',[
            'tienda' => $tienda,
            's_cajas' => $s_cajas,
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
      
        if($id == 'showtablapdf'){
            if($request->input('listarpor')==1){
              
                $where = [];
                if($request->input('idcaja')!=''){
                    $where[] = ['s_aperturacierre.s_idcaja',$request->input('idcaja')];
                }
                if($request->input('fechainicio')!=''){
                    $where[] = ['s_aperturacierre.fechaconfirmacion','>=',$request->input('fechainicio').' 00:00:00'];
                }
                if($request->input('fechafin')!=''){
                    $where[] = ['s_aperturacierre.fechaconfirmacion','<=',$request->input('fechafin').' 23:59:59'];
                }
              
                $cajas = DB::table('s_caja')
                    ->join('s_aperturacierre','s_aperturacierre.s_idcaja','s_caja.id')
                    ->where('s_aperturacierre.idtienda',$idtienda)
                    ->where('s_aperturacierre.idestado',1)
                    ->where($where)
                    ->select(
                        's_caja.id as idcaja',
                        's_caja.nombre as cajanombre'
                    )
                    ->orderBy('s_caja.nombre','asc')
                    ->distinct()
                    ->get();
                
                $totalaperturafinal = 0;
                $totalcierrefinal = 0;
                $data = [];
                foreach($cajas as $value){
                  
                    $datadetalle = DB::table('s_aperturacierre')
                        ->join('users as usersresponsable','usersresponsable.id','s_aperturacierre.idusersresponsable')
                        ->join('users as usersrecepcion','usersrecepcion.id','s_aperturacierre.idusersrecepcion')
                        ->where('s_aperturacierre.idtienda',$idtienda)
                        ->where('s_aperturacierre.idestado',1)
                        ->where('s_aperturacierre.s_idcaja',$value->idcaja)
                        ->select(
                            's_aperturacierre.*',
                            'usersresponsable.nombre as usersresponsablenombre',
                            'usersresponsable.apellidos as usersresponsableapellidos',
                            'usersrecepcion.nombre as usersrecepcionnombre',
                            'usersrecepcion.apellidos as usersrecepcionapellidos'
                        )
                        ->orderBy('s_aperturacierre.id','desc')
                        ->get();
                    $totalapertura = 0;
                    $totalcierre = 0;
                    $detalle = [];
                    foreach($datadetalle as $valuedetalle){
                        $detalle[] = [
                            'responsable' => $valuedetalle->usersresponsableapellidos.', '.$valuedetalle->usersresponsablenombre,
                            'recepcion' => $valuedetalle->usersrecepcionapellidos.', '.$valuedetalle->usersrecepcionnombre,
                            'montoapertura' => $valuedetalle->montoasignar,
                            'montocierre' => $valuedetalle->montocierre,
                            'fechaapertura' => date_format(date_create($valuedetalle->fechaconfirmacion), 'd/m/y h:iA'),
                            'fechacierre' => date_format(date_create($valuedetalle->fechacierreconfirmacion), 'd/m/y h:iA'),
                            'idestado' => $valuedetalle->idestado,
                        ];
                        $totalapertura = $totalapertura+$valuedetalle->montoasignar;
                        $totalcierre = $totalcierre+$valuedetalle->montocierre;
                    }
                  
                    $data[] = [
                        'cajanombre' => $value->cajanombre,
                        'totalapertura' => number_format($totalapertura, 2, '.', ''),
                        'totalcierre' => number_format($totalcierre, 2, '.', ''),
                        'detalle' => $detalle
                    ];
                    $totalaperturafinal = $totalaperturafinal+$totalapertura;
                    $totalcierrefinal = $totalcierrefinal+$totalcierre;
                }
            }
            elseif($request->input('listarpor')==2){
                
                $where = [];
                if($request->input('idusersresponsable')!=''){
                    $where[] = ['s_aperturacierre.idusersresponsable',$request->input('idusersresponsable')];
                }
                if($request->input('fechainicio')!=''){
                    $where[] = ['s_aperturacierre.fechaconfirmacion','>=',$request->input('fechainicio').' 00:00:00'];
                }
                if($request->input('fechafin')!=''){
                    $where[] = ['s_aperturacierre.fechaconfirmacion','<=',$request->input('fechafin').' 23:59:59'];
                }
              
                $cajas = DB::table('users')
                    ->join('s_aperturacierre','s_aperturacierre.idusersresponsable','users.id')
                    ->where('s_aperturacierre.idtienda',$idtienda)
                    ->where('s_aperturacierre.idestado',1)
                    ->where($where)
                    ->select(
                        'users.id as idusuario',
                        'users.nombre as usuarionombre',
                        'users.apellidos as usuarioapellidos'
                    )
                    ->orderBy('users.nombre','asc')
                    ->distinct()
                    ->get();
                
                $totalaperturafinal = 0;
                $totalcierrefinal = 0;
                $data = [];
                foreach($cajas as $value){
                  
                    $datadetalle = DB::table('s_aperturacierre')
                        ->join('s_caja','s_caja.id','s_aperturacierre.s_idcaja')
                        ->join('users as usersrecepcion','usersrecepcion.id','s_aperturacierre.idusersrecepcion')
                        ->where('s_aperturacierre.idtienda',$idtienda)
                        ->where('s_aperturacierre.idestado',1)
                        ->where('s_aperturacierre.idusersresponsable',$value->idusuario)
                        ->select(
                            's_aperturacierre.*',
                            's_caja.nombre as cajanombre',
                            'usersrecepcion.nombre as usersrecepcionnombre',
                            'usersrecepcion.apellidos as usersrecepcionapellidos'
                        )
                        ->orderBy('s_aperturacierre.id','desc')
                        ->get();
                    $totalapertura = 0;
                    $totalcierre = 0;
                    $detalle = [];
                    foreach($datadetalle as $valuedetalle){
                        $detalle[] = [
                            'cajanombre' => $valuedetalle->cajanombre,
                            'recepcion' => $valuedetalle->usersrecepcionapellidos.', '.$valuedetalle->usersrecepcionnombre,
                            'montoapertura' => $valuedetalle->montoasignar,
                            'montocierre' => $valuedetalle->montocierre,
                            'fechaapertura' => date_format(date_create($valuedetalle->fechaconfirmacion), 'd/m/y h:iA'),
                            'fechacierre' => date_format(date_create($valuedetalle->fechacierreconfirmacion), 'd/m/y h:iA'),
                            'idestado' => $valuedetalle->idestado,
                        ];
                        $totalapertura = $totalapertura+$valuedetalle->montoasignar;
                        $totalcierre = $totalcierre+$valuedetalle->montocierre;
                    }
                  
                    $data[] = [
                        'usuarionombre' => $value->usuarionombre.', '.$value->usuarioapellidos,
                        'totalapertura' => number_format($totalapertura, 2, '.', ''),
                        'totalcierre' => number_format($totalcierre, 2, '.', ''),
                        'detalle' => $detalle
                    ];
                    $totalaperturafinal = $totalaperturafinal+$totalapertura;
                    $totalcierrefinal = $totalcierrefinal+$totalcierre;
                }
            }
            elseif($request->input('listarpor')==3){
                
                $where = [];
                if($request->input('idusersrecepcion')!=''){
                    $where[] = ['s_aperturacierre.idusersrecepcion',$request->input('idusersrecepcion')];
                }
                if($request->input('fechainicio')!=''){
                    $where[] = ['s_aperturacierre.fechaconfirmacion','>=',$request->input('fechainicio').' 00:00:00'];
                }
                if($request->input('fechafin')!=''){
                    $where[] = ['s_aperturacierre.fechaconfirmacion','<=',$request->input('fechafin').' 23:59:59'];
                }
              
                $cajas = DB::table('users')
                    ->join('s_aperturacierre','s_aperturacierre.idusersrecepcion','users.id')
                    ->where('s_aperturacierre.idtienda',$idtienda)
                    ->where('s_aperturacierre.idestado',1)
                    ->where($where)
                    ->select(
                        'users.id as idusuario',
                        'users.nombre as usuarionombre',
                        'users.apellidos as usuarioapellidos'
                    )
                    ->orderBy('users.nombre','asc')
                    ->distinct()
                    ->get();
                
                $totalaperturafinal = 0;
                $totalcierrefinal = 0;
                $data = [];
                foreach($cajas as $value){
                  
                    $datadetalle = DB::table('s_aperturacierre')
                        ->join('s_caja','s_caja.id','s_aperturacierre.s_idcaja')
                        ->join('users as usersresponsable','usersresponsable.id','s_aperturacierre.idusersresponsable')
                        ->where('s_aperturacierre.idtienda',$idtienda)
                        ->where('s_aperturacierre.idestado',1)
                        ->where('s_aperturacierre.idusersrecepcion',$value->idusuario)
                        ->select(
                            's_aperturacierre.*',
                            's_caja.nombre as cajanombre',
                            'usersresponsable.nombre as usersresponsablenombre',
                            'usersresponsable.apellidos as usersresponsableapellidos',
                        )
                        ->orderBy('s_aperturacierre.id','desc')
                        ->get();
                    $totalapertura = 0;
                    $totalcierre = 0;
                    $detalle = [];
                    foreach($datadetalle as $valuedetalle){
                        $detalle[] = [
                            'cajanombre' => $valuedetalle->cajanombre,
                            'responsable' => $valuedetalle->usersresponsableapellidos.', '.$valuedetalle->usersresponsablenombre,
                            'montoapertura' => $valuedetalle->montoasignar,
                            'montocierre' => $valuedetalle->montocierre,
                            'fechaapertura' => date_format(date_create($valuedetalle->fechaconfirmacion), 'd/m/y h:iA'),
                            'fechacierre' => date_format(date_create($valuedetalle->fechacierreconfirmacion), 'd/m/y h:iA'),
                            'idestado' => $valuedetalle->idestado,
                        ];
                        $totalapertura = $totalapertura+$valuedetalle->montoasignar;
                        $totalcierre = $totalcierre+$valuedetalle->montocierre;
                    }
                  
                    $data[] = [
                        'usuarionombre' => $value->usuarionombre.', '.$value->usuarioapellidos,
                        'totalapertura' => number_format($totalapertura, 2, '.', ''),
                        'totalcierre' => number_format($totalcierre, 2, '.', ''),
                        'detalle' => $detalle
                    ];
                    $totalaperturafinal = $totalaperturafinal+$totalapertura;
                    $totalcierrefinal = $totalcierrefinal+$totalcierre;
                }
            }
        
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/reporte/reporteaperturacierre/tablapdf',[
                'tienda' => $tienda,
                'data' => $data,
                'totalaperturafinal' => number_format($totalaperturafinal, 2, '.', ''),
                'totalcierrefinal' => number_format($totalcierrefinal, 2, '.', ''),
                'listarpor' => $request->input('listarpor'),
                'fechainicio' => $request->input('fechainicio'),
                'fechafin' => $request->input('fechafin'),
            ]);
            return $pdf->stream('REPORTE_DE_APERTURA_Y_CIERRE_DE_CAJA.pdf');
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
