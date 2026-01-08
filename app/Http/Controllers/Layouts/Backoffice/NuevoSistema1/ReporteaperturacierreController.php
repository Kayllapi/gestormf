<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use App\Exports\ReporteaperturacierreExport;
use Maatwebsite\Excel\Facades\Excel;

class  ReporteaperturacierreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
 
        $where = [];
        if($request->input('idcaja')!=''){
            $where[] = ['s_caja.id',$request->input('idcaja')];
        }
        if($request->input('idusersresponsable')!=''){
            $where[] = ['usersresponsable.id',$request->input('idusersresponsable')];
        }
        if($request->input('idusers')!=''){
            $where[] = ['usersrecepcion.id',$request->input('idusers')];
        }
        if($request->input('fechainicio')!=''){
            $where[] = ['s_aperturacierre.fechaconfirmacion','>=',$request->input('fechainicio').' 00:00:00'];
        }
        if($request->input('fechafin')!=''){
            $where[] = ['s_aperturacierre.fechaconfirmacion','<=',$request->input('fechafin').' 23:59:59'];
        }
         if($request->input('tipo')=='excel'){
             
        $s_aperturacierres = DB::table('s_aperturacierre')
            ->join('users as usersresponsable','usersresponsable.id','s_aperturacierre.idusersresponsable')
            ->join('users as usersrecepcion','usersrecepcion.id','s_aperturacierre.idusersrecepcion')
            ->join('s_caja','s_caja.id','s_aperturacierre.s_idcaja')
            ->where('s_caja.idtienda',$idtienda)
            ->where($where)
            ->select(
                's_aperturacierre.*',
                'usersresponsable.nombre as usersresponsablenombre',
                'usersresponsable.apellidos as usersresponsableapellidos',
                'usersrecepcion.nombre as usersrecepcionnombre',
                'usersrecepcion.apellidos as usersrecepcionapellidos',
                's_caja.nombre as cajanombre'
            )
            ->orderBy('s_aperturacierre.id','desc')
            ->get();
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Aperturas y Cierres';
            $fecha  = '';

            if($inicio != '' && $fin != ''){
              $fecha = '('.$inicio.' hasta '.$fin.')';
            }
            elseif($inicio != ''){                
              $fecha = '('.$inicio.')';
            }
            elseif($fin != ''){
              $fecha = '('.$fin.')';
            }
            else{
              $fecha = '';
            }

            return Excel::download(new 
                                    ReporteaperturacierreExport($s_aperturacierres, $inicio, $fin, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
            
        }else{
            $s_aperturacierres = DB::table('s_aperturacierre')
                ->join('users as usersresponsable','usersresponsable.id','s_aperturacierre.idusersresponsable')
                ->join('users as usersrecepcion','usersrecepcion.id','s_aperturacierre.idusersrecepcion')
                ->join('s_caja','s_caja.id','s_aperturacierre.s_idcaja')
                ->where('s_caja.idtienda',$idtienda)
                ->where($where)
                ->select(
                    's_aperturacierre.*',
                    'usersresponsable.nombre as usersresponsablenombre',
                    'usersresponsable.apellidos as usersresponsableapellidos',
                    'usersrecepcion.nombre as usersrecepcionnombre',
                    'usersrecepcion.apellidos as usersrecepcionapellidos',
                    's_caja.nombre as cajanombre'
                )
                ->orderBy('s_aperturacierre.id','desc')
                ->paginate(10);

            $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->get();
            $users = DB::table('users')->where('idtienda',$idtienda)->get();

            return view('layouts/backoffice/tienda/sistema/reporteaperturacierre/index',[
                'tienda' => $tienda,
                's_aperturacierres' => $s_aperturacierres,
                's_cajas' => $s_cajas,
                'users' => $users
            ]);
       }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }
}
