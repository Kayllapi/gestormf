<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class ReporteHojaRepartoController extends Controller
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
 
        return view('layouts/backoffice/tienda/sistema/reportehojareparto/index',[
            'tienda' => $tienda,
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
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tablapdf') {
          
            $where = [];
            $where[] = ['s_venta.fechaconfirmacion','>=',$request->input('fechainicio').' 00:00:00'];
            $where[] = ['s_venta.fechaconfirmacion','<=',$request->input('fechafin').' 23:59:59'];
          
            $s_ventas = DB::table('s_venta')
                ->join('users as cliente','cliente.id','s_venta.s_idusuariocliente')
                ->where('s_venta.idtienda',$idtienda)
                ->where('s_venta.s_idestado',3)
                ->where($where)
                ->select(
                    's_venta.id as idventa',
                    's_venta.codigo as ventacodigo',
                    'cliente.identificacion as clienteidentificacion',
                    DB::raw('IF(cliente.idtipopersona=2,
                    CONCAT(cliente.apellidos),
                    CONCAT(cliente.apellidos,", ",cliente.nombre)) as cliente'),
                )
                ->orderBy('s_venta.codigo','asc')
                ->get();
          
            $configuracion_facturacion = configuracion_facturacion($idtienda);
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/reportehojareparto/tablapdf',[
                'tienda' => $tienda,
                's_ventas' => $s_ventas,
                'fechainicio' => $request->input('fechainicio'),
                'fechafin' => $request->input('fechafin'),
                'configuracion_facturacion' => $configuracion_facturacion
            ]);
            $tabla = 'Hoja_de_Reparto';
            return $pdf->stream($tabla.'.pdf');
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
