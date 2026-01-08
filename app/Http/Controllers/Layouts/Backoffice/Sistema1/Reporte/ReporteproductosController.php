<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use App\Exports\ReporteproductosExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;


class  ReporteproductosController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        $where = [];
        if($request->input('codigo')!=''){
            $where[] = ['s_producto.codigo','LIKE','%'.$request->input('codigo').'%'];
        }
        if($request->input('nombre')!=''){
            $where[] = ['s_producto.nombre','LIKE','%'.$request->input('nombre').'%'];
        }
        if($request->input('idcategoria')!=''){
            $where[] = ['s_categoria.id','LIKE','%'.$request->input('idcategoria').'%'];
        }
        if($request->input('idmarca')!=''){
            $where[] = ['s_marca.id','LIKE','%'.$request->input('idmarca').'%'];
        }
        if($request->input('idestado')!=''){
            $where[] = ['s_producto.s_idestado',$request->input('idestado')];
        }
      
       if($request->input('tipo')=='excel'){
            $producto = DB::table('s_producto')
                ->join('tienda','tienda.id','s_producto.idtienda')
                ->join('s_categoria','s_categoria.id','s_producto.s_idcategoria1')
                ->join('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                ->join('s_marca','s_marca.id','s_producto.s_idmarca')
                ->where('s_producto.idtienda',$idtienda)
                ->where('s_producto.s_idestado',1)
                ->where($where)
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
            
        }else{
         $producto  = DB::table('s_producto')
                ->join('tienda','tienda.id','s_producto.idtienda')
                ->join('s_categoria','s_categoria.id','s_producto.s_idcategoria1')
                ->join('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                ->join('s_marca','s_marca.id','s_producto.s_idmarca')
                ->where('s_producto.idtienda',$idtienda)
                ->where('s_producto.s_idestado',1)
                ->where($where)
                ->select(
                        's_producto.*',
                        'unidadmedida.nombre as nombreummedida',
                        's_marca.nombre as nombremarca',
                        's_categoria.nombre as nombrecategoria'
                )
                ->orderBy('s_producto.id','desc')
                ->paginate(10);
         
         $marca      = DB::table('s_marca')->where('idtienda',$idtienda)->get();
         $categoria  = DB::table('s_categoria')
                ->where('s_categoria.idtienda',$idtienda)
                ->where('s_categoria.s_idcategoria',0)
                ->orderBy('s_categoria.nombre','asc')
                ->get();
      
          return view('layouts/backoffice/tienda/sistema/reporte/reporteproductos/index',[
            'tienda'      => $tienda,
            'producto'    => $producto,
            'categoria'   => $categoria,
            'marca'       => $marca,
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
      
       $tienda = DB::table('tienda')
            ->leftJoin('ubigeo', 'ubigeo.id', 'tienda.idubigeo')
            ->select(
                'tienda.*',
                'ubigeo.nombre as ubigeonombre',
            )
            ->where('tienda.id',$idtienda)
            ->first();
  
       if ($id == 'showtablapdf') {
         
          $where = [];
          if($request->input('idproducto')!=''){
              $where[] = ['s_producto.id','LIKE','%'.$request->input('idproducto').'%'];
          }
          if($request->input('idcategoria')!=''){
              $where[] = ['s_categoria.id','LIKE','%'.$request->input('idcategoria').'%'];
          }
          if($request->input('idmarca')!=''){
              $where[] = ['s_marca.id','LIKE','%'.$request->input('idmarca').'%'];
          }
          if($request->input('idestado')!=''){
              $where[] = ['s_producto.s_idestado',$request->input('idestado')];
          }
         
          $producto  = DB::table('s_producto')
                ->join('tienda','tienda.id','s_producto.idtienda')
                ->join('s_categoria','s_categoria.id','s_producto.s_idcategoria1')
                ->join('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                ->leftJoin('s_marca','s_marca.id','s_producto.s_idmarca')
                ->where('s_producto.idtienda',$idtienda)
                ->where('s_producto.s_idestado',1)
                ->where($where)
                ->select(
                        's_producto.*',
                        'unidadmedida.nombre as nombreummedida',
                        's_marca.nombre as nombremarca',
                        's_categoria.nombre as nombrecategoria'
                )
                ->orderBy('s_producto.id','desc')
                ->get();
         
           $producto_tabla = [];
           foreach ($producto as $value) {
             $producto_tabla[] = [
               'codigo'        => str_pad($value->codigo, 8, "0", STR_PAD_LEFT),
               'nombre'        => $value->nombre,
               'categoria'     => $value->nombrecategoria,
               'marca'         => $value->nombremarca,
               'unidad_medida' => $value->nombreummedida,
               'precio'        => $value->precioalpublico,
               'estado'        => $value->s_idestado==1 ? 'Activado' : 'Desactivado',
             ];
           }
         
           $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/reporte/reporteproductos/tablapdf',[
                'tienda'      => $tienda,
                'productos'   => $producto_tabla,
            ]);
          
            return $pdf->stream('REPORTE_DE_PRODUCTOS.pdf');
       }
       elseif ($id == 'showtablaexcel') {
         
          $where = [];
          if($request->input('idproducto')!=''){
              $where[] = ['s_producto.id','LIKE','%'.$request->input('idproducto').'%'];
          }
          if($request->input('idcategoria')!=''){
              $where[] = ['s_categoria.id','LIKE','%'.$request->input('idcategoria').'%'];
          }
          if($request->input('idmarca')!=''){
              $where[] = ['s_marca.id','LIKE','%'.$request->input('idmarca').'%'];
          }
          if($request->input('idestado')!=''){
              $where[] = ['s_producto.s_idestado',$request->input('idestado')];
          }
         
          $producto  = DB::table('s_producto')
                ->join('tienda','tienda.id','s_producto.idtienda')
                ->join('s_categoria','s_categoria.id','s_producto.s_idcategoria1')
                ->join('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                ->leftJoin('s_marca','s_marca.id','s_producto.s_idmarca')
                ->where('s_producto.idtienda',$idtienda)
                ->where('s_producto.s_idestado',1)
                ->where($where)
                ->select(
                        's_producto.*',
                        'unidadmedida.nombre as nombreummedida',
                        's_marca.nombre as nombremarca',
                        's_categoria.nombre as nombrecategoria'
                )
                ->orderBy('s_producto.id','desc')
                ->get();
         
            $titulo = 'REPORTE DE PRODUCTOS';

            return Excel::download(new 
                                    ReporteproductosExport($producto, $titulo),
                                    $titulo.'.xls'
                                  );
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
