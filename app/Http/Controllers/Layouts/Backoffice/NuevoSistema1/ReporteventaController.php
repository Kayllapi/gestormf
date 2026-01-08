<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use App\Exports\ReporteventaExport;
use Maatwebsite\Excel\Facades\Excel;

class  ReporteventaController extends Controller
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
       json_reporteventa($idtienda,$request->name_modulo);
        $agencia = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
        $comprobante = DB::table('s_tipocomprobante')->get();
        $tipopersonas = DB::table('tipopersona')->get();
        $tipoentregas = DB::table('s_tipoentrega')->get();
      
        return view('layouts/backoffice/tienda/nuevosistema/reporteventa/index',[
            'tienda'        => $tienda,
            'agencia'       => $agencia,
            'comprobante'   => $comprobante,
            'tipopersonas'  => $tipopersonas,
            'tipoentregas'  => $tipoentregas,
        ]);
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
      if($request->input('tipo')=='excel'){
            $s_venta = DB::table('s_venta')
              ->join('s_tipocomprobante','s_tipocomprobante.id','s_venta.s_idcomprobante')
              ->join('s_tipoentrega','s_tipoentrega.id','s_venta.s_idtipoentrega')
              ->join('users as responsable','responsable.id','s_venta.s_idusuarioresponsable')
              ->join('users as responsableregistro','responsableregistro.id','s_venta.s_idusuarioresponsableregistro')
              ->join('users as cliente','cliente.id','s_venta.s_idusuariocliente')
              ->where('s_venta.idtienda',$idtienda)
              ->where($where)
              ->select(
                  's_venta.*',
                  's_tipocomprobante.nombre as nombreComprobante',
                  's_tipoentrega.nombre as tipoentreganombre',
                  'responsable.nombre as responsablenombre',
                  'responsableregistro.nombre as responsableregistronombre',
                  'cliente.nombre as clientenombre'
              )
              ->orderBy('s_venta.id','desc')
              ->get();
          
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Ventas';
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
            return Excel::download(new ReporteventaExport($s_venta, $inicio, $fin, $titulo),$titulo.' '.$fecha.'.xls');
            
        }
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
