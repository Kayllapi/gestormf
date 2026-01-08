<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use App\Exports\ReportemovimientoExport;
use Maatwebsite\Excel\Facades\Excel;

class  ReportemovimientoController extends Controller
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
        if($request->input('idconceptomovimiento')!=''){
            $where[] = ['s_conceptomovimiento.id',$request->input('idconceptomovimiento')];
        }
        if($request->input('concepto')!=''){
            $where[] = ['s_movimiento.concepto','LIKE','%'.$request->input('concepto').'%'];
        }
        if($request->input('idusuarioresponsable')!=''){
            $where[] = ['responsable.id',$request->input('idusuarioresponsable')];
        }
        if($request->input('fechainicio')!=''){
            $where[] = ['s_movimiento.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
        }
        if($request->input('fechafin')!=''){
            $where[] = ['s_movimiento.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
        }
      
       if($request->input('tipo')=='excel'){
          $s_movimientos  = DB::table('s_movimiento')
            ->join('s_conceptomovimiento','s_conceptomovimiento.id','s_movimiento.s_idconceptomovimiento')
            ->leftJoin('s_aperturacierre','s_aperturacierre.id','s_movimiento.s_idaperturacierre')
            ->leftJoin('s_caja','s_caja.id','s_aperturacierre.s_idcaja')
            ->join('users as responsable','responsable.id','s_movimiento.s_idusuario')
            ->where('s_caja.idtienda',$idtienda)
            ->where($where)
            ->select(
                's_movimiento.*',
                's_caja.nombre as cajanombre',
                's_conceptomovimiento.tipo as conceptomovimientotipo',
                's_conceptomovimiento.nombre as conceptomovimientonombre',
                'responsable.nombre as responsablenombre',
                'responsable.apellidos as responsableapellidos'
            )
            ->orderBy('s_movimiento.id','desc')
            ->get();
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Movimientos';
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
                                    ReportemovimientoExport($s_movimientos, $inicio, $fin, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
            
        }else{
           $s_movimientos  = DB::table('s_movimiento')
            ->join('s_conceptomovimiento','s_conceptomovimiento.id','s_movimiento.s_idconceptomovimiento')
            ->leftJoin('s_aperturacierre','s_aperturacierre.id','s_movimiento.s_idaperturacierre')
            ->leftJoin('s_caja','s_caja.id','s_aperturacierre.s_idcaja')
            ->join('users as responsable','responsable.id','s_movimiento.s_idusuario')
            ->where('s_caja.idtienda',$idtienda)
            ->where($where)
            ->select(
                's_movimiento.*',
                's_caja.nombre as cajanombre',
                's_conceptomovimiento.tipo as conceptomovimientotipo',
                's_conceptomovimiento.nombre as conceptomovimientonombre',
                'responsable.nombre as responsablenombre',
                'responsable.apellidos as responsableapellidos'
            )
            ->orderBy('s_movimiento.id','desc')
            ->paginate(10);
      
        $s_conceptomovimientos = DB::table('s_conceptomovimiento')->get();
      
        return view('layouts/backoffice/tienda/sistema/reportemovimiento/index',[
            'tienda' => $tienda,
            's_movimientos' => $s_movimientos,
            's_conceptomovimientos' => $s_conceptomovimientos
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
    public function show(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if($id=='showlistarusuario'){
            $usuarios = DB::table('users')
                ->where('idtienda',$idtienda)
                ->where('users.nombre','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('idtienda',$idtienda)
                ->where('users.apellidos','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('idtienda',$idtienda)
                ->where('users.identificacion','LIKE','%'.$request->input('buscar').'%')
                ->select(
                  'users.id as id',
                   DB::raw('CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre) as text')
                )
                ->get();
            return $usuarios;
        }
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
