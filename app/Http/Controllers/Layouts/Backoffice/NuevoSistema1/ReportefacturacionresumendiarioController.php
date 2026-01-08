<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use App\Exports\ReportefacturacionresumendiarioExport;
use Maatwebsite\Excel\Facades\Excel;

class  ReportefacturacionresumendiarioController extends Controller
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
      
        $where  = [];
        if($request->input('correlativo')!= ''){
          $where[] = ['s_facturacionresumendiario.resumen_correlativo',$request->input('correlativo')];
        }
       if($request->input('idestado')!= ''){
          $where[] = ['s_facturacionrespuesta.estado',$request->input('correlativo')];
        }

      if($request->input('idresponsable')!=''){
            $where[] = ['responsable.id',$request->input('idresponsable')];
        }
       if($request->input('idcliente')!=''){
            $where[] = ['cliente.id',$request->input('idcliente')];
        }
       if($request->input('idagencia')!=''){
            $where[] = ['s_facturacionresumendiario.idagencia',$request->input('idagencia')];
        }
        if($request->input('fechainicio')!=''){
            $where[] = ['s_facturacionresumendiario.resumen_fechageneracion','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if($request->input('fechafin')!=''){
            $where[] = ['s_facturacionresumendiario.resumen_fechageneracion','<=',$request->input('fechafin').' 24:00:00'];
        }
         if($request->input('tipo')=='excel'){
          $facturacionresumendiario = DB::table('s_facturacionresumendiariodetalle')
                ->join('s_facturacionresumendiario','s_facturacionresumendiario.id','s_facturacionresumendiariodetalle.idfacturacionresumendiario')
                ->join('users as responsable','responsable.id','s_facturacionresumendiario.idusuarioresponsable')
                ->leftJoin('users as cliente','cliente.id','s_facturacionresumendiariodetalle.idusuariocliente')
                ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacionresumendiario','s_facturacionresumendiario.id')
               ->where('s_facturacionresumendiario.idtienda',$idtienda)
               ->where($where)
                ->select(
                's_facturacionresumendiariodetalle.*',
             's_facturacionrespuesta.estado as respuestaestado',
                's_facturacionresumendiario.resumen_fechageneracion as resumen_fechageneracion',
                's_facturacionresumendiario.resumen_correlativo as resumen_correlativo',
                's_facturacionresumendiario.resumen_fecharesumen as resumen_fecharesumen',
                's_facturacionresumendiario.emisor_ruc as emisor_ruc',
                's_facturacionresumendiario.emisor_nombrecomercial as emisor_nombrecomercial',
                'responsable.nombre as responsablenombre',
                DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente')
            )
            ->orderBy('s_facturacionresumendiario.id','desc')
            ->get();
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte Resúmenes Diarios';
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
                                    ReportefacturacionresumendiarioExport($facturacionresumendiario, $inicio, $fin, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
            
        }else{

        $facturacionresumendiario = DB::table('s_facturacionresumendiariodetalle')
               ->join('s_facturacionresumendiario','s_facturacionresumendiario.id','s_facturacionresumendiariodetalle.idfacturacionresumendiario')
              ->join('users as responsable','responsable.id','s_facturacionresumendiario.idusuarioresponsable')
              ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacionresumendiario','s_facturacionresumendiario.id')
              ->leftJoin('users as cliente','cliente.id','s_facturacionresumendiariodetalle.idusuariocliente')
              ->where('s_facturacionresumendiario.idtienda',$idtienda)
              ->where($where)
              ->select(
                's_facturacionresumendiariodetalle.*',
                's_facturacionrespuesta.estado as respuestaestado',
                's_facturacionresumendiario.resumen_fechageneracion as resumen_fechageneracion',
                's_facturacionresumendiario.resumen_correlativo as resumen_correlativo',
                's_facturacionresumendiario.resumen_fecharesumen as resumen_fecharesumen',
                's_facturacionresumendiario.emisor_ruc as emisor_ruc',
                's_facturacionresumendiario.emisor_nombrecomercial as emisor_nombrecomercial',
                'responsable.nombre as responsablenombre',
                DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente')
            )
            ->orderBy('s_facturacionresumendiario.id','desc')
            ->paginate(10);
        
        $agencia = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
      
        return view('layouts/backoffice/tienda/sistema/reportefacturacionresumendiario/index',[
            'tienda'                   => $tienda,
            'facturacionresumendiario' => $facturacionresumendiario,
            'agencia'                  => $agencia,
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
