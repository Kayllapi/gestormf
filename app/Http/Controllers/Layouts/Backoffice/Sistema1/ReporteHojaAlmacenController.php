<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class ReporteHojaAlmacenController extends Controller
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

        return view('layouts/backoffice/tienda/sistema/reportehojaalmacen/index',[
            'tienda'          => $tienda,
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
          
            $s_ventadetalles = DB::table('s_ventadetalle')
                ->join('s_venta','s_venta.id','s_ventadetalle.s_idventa')
                ->join('s_producto','s_producto.id','s_ventadetalle.s_idproducto')
                ->where('s_venta.idtienda',$idtienda)
                ->where('s_venta.s_idestado',3)
                ->where($where)
                ->select(
                    's_producto.codigo as codigo',
                    's_producto.nombre as nombreproducto',
                    's_ventadetalle.cantidad as cantidad',
                )
                ->orderBy('s_producto.nombre','asc')
                ->get();
          
            $configuracion_facturacion = configuracion_facturacion($idtienda);
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/reportehojaalmacen/tablapdf',[
                'tienda' => $tienda,
                's_ventadetalles' => $s_ventadetalles,
                'fechainicio' => $request->input('fechainicio'),
                'fechafin' => $request->input('fechafin'),
                'configuracion_facturacion' => $configuracion_facturacion
            ]);
            $tabla = 'Hoja_de_Almacen';
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
