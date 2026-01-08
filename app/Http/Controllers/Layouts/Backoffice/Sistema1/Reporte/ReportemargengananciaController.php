<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class  ReportemargengananciaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        return view('layouts/backoffice/tienda/sistema/reporte/reportemargenganancia/index',[
            'tienda' => $tienda,
        ]);
    }

    public function create(Request $request,$idtienda)
    {
        //
    }

    public function store(Request $request, $idtienda)
    {
        //
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
            if($request->input('fechainicio')!=''){
                $where[] = ['s_venta.fechaconfirmacion','>=',$request->input('fechainicio').' 00:00:00'];
            }
            if($request->input('fechafin')!=''){
                $where[] = ['s_venta.fechaconfirmacion','<=',$request->input('fechafin').' 23:59:59'];
            }
            
            /*$compradetalle  =   DB::table('s_compradetalle')
              ->join('s_compra as compra','compra.id','s_compradetalle.s_idcompra')
              ->join('s_producto as producto','producto.id','s_compradetalle.s_idproducto')
              ->where('compra.idtienda',$idtienda)
              ->where($where)
              ->select(
                  's_compradetalle.*',
                  'compra.codigo as codigocompra',
                  'compra.fechaconfirmacion as fechacompra',
                  'compra.seriecorrelativo as seriecorrelativo',
                  'producto.nombre as nombreproducto',
                  'producto.codigo as codigo',
              )
              ->orderBy('compra.id','desc')
              ->get();*/
          
            /*$productos = DB::table('s_producto')
                ->join('tienda','tienda.id','s_producto.idtienda')
                ->leftJoin('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                ->where('s_producto.idtienda',$idtienda)
                ->where('s_producto.s_idestado',1)
                ->select(
                  's_producto.id as id',
                  's_producto.codigo as codigo',
                  's_producto.nombre as nombre',
                  's_producto.precioalpublico as precioalpublico',
                   DB::raw('(SELECT imagen FROM s_productogaleria WHERE s_idproducto=s_producto.id ORDER BY orden ASC LIMIT 1) as imagen')
                )
                ->get();*/
          
            $s_ventadetalle  =   DB::table('s_ventadetalle')
              ->join('s_venta','s_venta.id','s_ventadetalle.s_idventa')
              ->join('s_producto','s_producto.id','s_ventadetalle.s_idproducto')
              ->where('s_venta.idtienda',$idtienda)
              ->where('s_venta.s_idestado',3)
              ->where($where)
              ->select(
                  //'s_ventadetalle.preciounitario as preciounitario',
                  's_producto.id as idproducto',
                  's_producto.codigo as codigo',
                  's_producto.nombre as nombreproducto',
                  DB::raw('SUM(preciounitario) as precioventa')
              )
              //->orderBy('s_producto.id','desc')
              ->groupBy('s_producto.id')
              ->groupBy('s_producto.codigo')
              ->groupBy('s_producto.nombre')
              ->get();
            $productos_tabla = [];
            foreach($s_ventadetalle as $value){
              
                $preciocompra  =   DB::table('s_compradetalle')
                    ->join('s_compra','s_compra.id','s_compradetalle.s_idcompra')
                    ->where('s_compra.idtienda',$idtienda)
                    ->where('s_compra.s_idestado',2)
                    ->where('s_compradetalle.s_idproducto',$value->idproducto)
                    ->sum('s_compradetalle.preciounitario');
                $productos_tabla[] = [
                    'codigo'        => $value->codigo,
                    'nombre'        => $value->nombreproducto,
                    'preciocompra'  => number_format($preciocompra, 2, '.', ''),
                    'precioventa'   => $value->precioventa,
                    'ganancia'      => number_format($value->precioventa-$preciocompra, 2, '.', ''),
                    'gananciapor'   => number_format(((number_format($value->precioventa-$preciocompra, 2, '.', ''))/$value->precioventa)*100, 2, '.', ''),
                ];
            }
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/reporte/reportemargenganancia/tablapdf',[
                'tienda' => $tienda,
                'productos' => $productos_tabla,
                'fechainicio' => $request->input('fechainicio'),
                'fechafin' => $request->input('fechafin'),
            ]);
            return $pdf->stream('REPORTE_DE_VENTA.pdf');
        }
    }

    public function edit(Request $request, $idtienda, $idmarca)
    {
        //
    }

    public function update(Request $request, $idtienda, $idmarca)
    {
        //
    }

    public function destroy(Request $request, $idtienda, $idmarca)
    {
        //
    }
}
