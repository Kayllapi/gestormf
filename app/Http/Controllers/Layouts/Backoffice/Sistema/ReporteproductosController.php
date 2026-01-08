<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use App\Exports\ReporteproductosExport;
use Maatwebsite\Excel\Facades\Excel;

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
        
    
       json_reporteproductos($idtienda,$request->name_modulo);
          return view(sistema_view().'/reporteproductos/index',[
            'tienda'      => $tienda,
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
            $producto = DB::table('s_producto')
                ->join('tienda','tienda.id','s_producto.idtienda')
                ->join('s_categoria','s_categoria.id','s_producto.s_idcategoria1')
                ->join('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                ->join('s_marca','s_marca.id','s_producto.s_idmarca')
                ->where('s_producto.idtienda',$idtienda)
                ->select(
                        's_producto.*',
                        'unidadmedida.nombre as nombreummedida',
                        's_marca.nombre as nombremarca',
                        's_categoria.nombre as nombrecategoria'
                )
                ->orderBy('s_producto.id','desc')
                ->get();

          
            $titulo = 'Reporte de Productos';

            return Excel::download(new 
                                    ReporteproductosExport($producto, $titulo),
                                    $titulo.'.xls'
                                  );
            
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