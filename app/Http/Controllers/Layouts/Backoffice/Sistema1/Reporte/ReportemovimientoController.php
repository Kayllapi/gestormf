<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class  ReportemovimientoController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        return view('layouts/backoffice/tienda/sistema/reporte/reportemovimiento/index',[
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
      
        if($id == 'showtablapdf'){
            if($request->input('listarpor')==1){
              
                $where = [];
                if($request->input('concepto')!=''){
                    $where[] = ['s_movimiento.tipomovimientonombre',$request->input('concepto')];
                }
                if($request->input('fechainicio')!=''){
                    $where[] = ['s_movimiento.fechaconfirmacion','>=',$request->input('fechainicio').' 00:00:00'];
                }
                if($request->input('fechafin')!=''){
                    $where[] = ['s_movimiento.fechaconfirmacion','<=',$request->input('fechafin').' 23:59:59'];
                }
              
                $conceptomovimientos = DB::table('s_movimiento')
                    ->where('s_movimiento.idtienda',$idtienda)
                    ->where('s_movimiento.idestado',1)
                    ->where($where)
                    ->select(
                        's_movimiento.tipomovimiento as tipomovimiento'
                    )
                    ->orderBy('s_movimiento.tipomovimiento','desc')
                    ->distinct()
                    ->get();
                
                $totalfinal = 0;
                $data = [];
                foreach($conceptomovimientos as $value){
                  
                    $datadetalle = DB::table('s_movimiento')
                        ->join('users as usersresponsable','usersresponsable.id','s_movimiento.s_idusuario')
                        ->leftJoin('users as usersresponsableentregado','usersresponsableentregado.id','s_movimiento.idresponsableentrega')
                        ->where('s_movimiento.idtienda',$idtienda)
                        ->where('s_movimiento.idestado',1)
                        ->where('s_movimiento.tipomovimiento',$value->tipomovimiento)
                        ->where($where)
                        ->select(
                            's_movimiento.*',
                            'usersresponsable.nombre as usersresponsablenombre',
                            'usersresponsable.apellidos as usersresponsableapellidos',
                            'usersresponsableentregado.nombre as usersresponsableentregadonombre',
                            'usersresponsableentregado.apellidos as usersresponsableentregadoapellidos',
                        )
                        ->orderBy('s_movimiento.id','asc')
                        ->get();
                    $total = 0;
                    $totalegreso = 0;
                    $detalle = [];
                    foreach($datadetalle as $valuedetalle){
                        $detalle[] = [
                            'responsable' => $valuedetalle->usersresponsableapellidos.', '.$valuedetalle->usersresponsablenombre,
                            'responsableentregado' => $valuedetalle->usersresponsableentregadoapellidos.', '.$valuedetalle->usersresponsableentregadonombre,
                            'codigo' => str_pad($valuedetalle->codigo, 8, "0", STR_PAD_LEFT),
                            'concepto' => $valuedetalle->tipomovimientonombre,
                            'descripcion' => $valuedetalle->concepto,
                            'monto' => $valuedetalle->monto,
                            'fechaconfirmado' => date_format(date_create($valuedetalle->fechaconfirmacion), 'd/m/y h:iA'),
                            'idestado' => $valuedetalle->idestado,
                        ];
                      
                        if($value->tipomovimiento=='INGRESO'){
                            $total = $total+$valuedetalle->monto;
                        }elseif($value->tipomovimiento=='EGRESO'){
                            $total = $total-$valuedetalle->monto;
                        }
                    }
                  
                    $data[] = [
                        'tipo' => $value->tipomovimiento,
                        'total' => number_format($total, 2, '.', ''),
                        'detalle' => $detalle
                    ];
                    $totalfinal = $totalfinal+number_format($total, 2, '.', '');
                }
            }
            elseif($request->input('listarpor')==2){
                
                $where = [];
                if($request->input('idusersresponsable')!=''){
                    $where[] = ['s_movimiento.s_idusuario',$request->input('idusersresponsable')];
                }
                if($request->input('fechainicio')!=''){
                    $where[] = ['s_movimiento.fechaconfirmacion','>=',$request->input('fechainicio').' 00:00:00'];
                }
                if($request->input('fechafin')!=''){
                    $where[] = ['s_movimiento.fechaconfirmacion','<=',$request->input('fechafin').' 23:59:59'];
                }
                $totalfinal = 0;
              
                $cajas = DB::table('users')
                    ->join('s_movimiento','s_movimiento.s_idusuario','users.id')
                    ->where('s_movimiento.idtienda',$idtienda)
                    ->where('s_movimiento.idestado',1)
                    ->where($where)
                    ->select(
                        'users.id as idusuario',
                        'users.nombre as usuarionombre',
                        'users.apellidos as usuarioapellidos',
                    )
                    ->orderBy('users.nombre','asc')
                    ->distinct()
                    ->get();
                
                $data = [];
                foreach($cajas as $value){
                  
                    $datadetalle = DB::table('s_movimiento')
                        ->leftJoin('users as usersresponsableentregado','usersresponsableentregado.id','s_movimiento.idresponsableentrega')
                        ->where('s_movimiento.idtienda',$idtienda)
                        ->where('s_movimiento.idestado',1)
                        ->where('s_movimiento.s_idusuario',$value->idusuario)
                        ->where($where)
                        ->select(
                            's_movimiento.*',
                            'usersresponsableentregado.nombre as usersresponsableentregadonombre',
                            'usersresponsableentregado.apellidos as usersresponsableentregadoapellidos',
                        )
                        ->orderBy('s_movimiento.id','asc')
                        ->get();
                    $total = 0;
                    $detalle = [];
                    foreach($datadetalle as $valuedetalle){
                        $signo = '';
                        if($valuedetalle->tipomovimiento=='INGRESO'){
                            $total = $total+$valuedetalle->monto;
                        }elseif($valuedetalle->tipomovimiento=='EGRESO'){
                            $total = $total-$valuedetalle->monto;
                            $signo = '-';
                        }
                        $detalle[] = [
                            'responsableentregado' => $valuedetalle->usersresponsableentregadoapellidos.', '.$valuedetalle->usersresponsableentregadonombre,
                            'conceptomovimientonombre' => $valuedetalle->tipomovimiento.' - '.$valuedetalle->tipomovimientonombre,
                            'codigo' => str_pad($valuedetalle->codigo, 8, "0", STR_PAD_LEFT),
                            'descripcion' => $valuedetalle->concepto,
                            'monto' => $signo.$valuedetalle->monto,
                            'fechaconfirmado' => date_format(date_create($valuedetalle->fechaconfirmacion), 'd/m/y h:iA'),
                            'idestado' => $valuedetalle->idestado,
                        ];
                    }
                  
                    $data[] = [
                        'usuarionombre' => $value->usuarionombre.', '.$value->usuarioapellidos,
                        'total' => number_format($total, 2, '.', ''),
                        'detalle' => $detalle
                    ];
                }
            }
        
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/reporte/reportemovimiento/tablapdf',[
                'tienda' => $tienda,
                'data' => $data,
                'totalfinal' => number_format($totalfinal, 2, '.', ''),
                'listarpor' => $request->input('listarpor'),
                'fechainicio' => $request->input('fechainicio'),
                'fechafin' => $request->input('fechafin'),
            ]);
            return $pdf->stream('REPORTE_DE_MOVIMIENTO.pdf');
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
