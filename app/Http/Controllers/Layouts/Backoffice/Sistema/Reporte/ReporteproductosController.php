<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class  ReporteproductosController extends Controller
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
        $idsucursal = Auth::user()->idsucursal;

        $productos = DB::table('s_producto')
                ->join('tienda','tienda.id','s_producto.idtienda')
                ->join('s_categoria','s_categoria.id','s_producto.s_idcategoria1')
                ->join('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                ->join('s_marca','s_marca.id','s_producto.s_idmarca')
                 ->leftJoin('s_productostock', function($leftJoin) use ($idtienda,$idsucursal){
                      $leftJoin->on('s_productostock.s_idproducto','s_producto.id')
                          ->where('s_productostock.idtienda',$idtienda)
                          ->where('s_productostock.idsucursal',$idsucursal);
                  })
                ->where('s_producto.idtienda',$idtienda)
                ->select(
                        's_producto.*',
                        'unidadmedida.nombre as nombreummedida',
                        's_marca.nombre as nombremarca',
                        's_categoria.nombre as nombrecategoria',
                        's_productostock.cantidad as stock',
                )
                ->orderBy('s_producto.id','desc')
                ->get();
      
        if($request->input('view') == 'tabla'){
            return view('layouts/backoffice/tienda/nuevosistema/reporteproductos/tabla',[
                'tienda' => $tienda,
                'productos' => $productos 
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
        $idsucursal = Auth::user()->idsucursal;
        $tienda = DB::table('tienda')
            ->leftJoin('ubigeo', 'ubigeo.id', 'tienda.idubigeo')
            ->select(
                'tienda.*',
                'ubigeo.nombre as ubigeonombre',
            )
            ->where('tienda.id',$idtienda)
            ->first();

        $where = [];
        $nombreTienda = '';
        $nombreCategoria = '';
        $nombreMarca = '';
        if($request->input('idtienda')!=''){
            $where[] = ['s_producto.idtienda', $request->idtienda];
        }
        if($request->input('idcategoria')!=''){
            $where[] = ['s_producto.s_idcategoria1', $request->idcategoria];
        }
        if($request->input('idmarca')!=''){
            $where[] = ['s_producto.s_idmarca', $request->idmarca];
        }
          
        $productos = DB::table('s_producto')
            ->join('tienda','tienda.id','s_producto.idtienda')
            ->join('s_categoria','s_categoria.id','s_producto.s_idcategoria1')
            ->join('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
            ->join('s_marca','s_marca.id','s_producto.s_idmarca')
                ->leftJoin('s_productostock', function($leftJoin) use ($idtienda,$idsucursal){
                    $leftJoin->on('s_productostock.s_idproducto','s_producto.id')
                        ->where('s_productostock.idtienda',$idtienda)
                        ->where('s_productostock.idsucursal',$idsucursal);
                })
            ->where($where)
            ->where('s_producto.idtienda',$idtienda)
            ->select(
                    's_producto.*',
                    'unidadmedida.nombre as nombreummedida',
                    's_marca.nombre as nombremarca',
                    's_categoria.nombre as nombrecategoria',
                    's_productostock.cantidad as stock',
            )
            ->orderBy('s_producto.id','desc')
            ->get();

        if ($id == 'showtablapdf') {
            $pdf = PDF::loadView('layouts/backoffice/tienda/nuevosistema/reporteproductos/tablapdf',[
                'productos' => $productos,
                'idsucursal' => $tienda,
                'tienda' => $tienda
            ]);

            return $pdf->stream('REPORTE_DE_PRODUCTOS.pdf');
        } else if ($id == 'showtabla') {
            return view('layouts/backoffice/tienda/nuevosistema/reporteproductos/tabla-data',[
                'tienda' => $tienda,
                'productos' => $productos 
            ]);
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