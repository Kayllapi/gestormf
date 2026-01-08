<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use App\Exports\ReportecompraExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ReportemargengananciaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        if($request->input('view') == 'tabla'){
            return view('layouts/backoffice/tienda/nuevosistema/reporte/reportemargenganancia/tabla',[
                'tienda' => $tienda,
            ]);
        }
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
  
        if ($id == 'showtablapdf') {
            $where = [];
            $marca = '';
            $categoria = '';
            $sucursal = '';
          
//             if($request->input('marca')!=''){
//                 $where[] = ['s_producto.s_idmarca','=',$request->input('marca')];
//                 $marca = DB::table('s_marca')->whereId($request->marca)->first()->nombre;
//             }
//             if($request->input('categoria')!=''){
//                 $where[] = ['s_producto.s_idcategoria1','=',$request->input('categoria')];
//                 $categoria = DB::table('s_categoria')->whereId($request->categoria)->first()->nombre;
              
//             } 
//             if($request->input('sucursal')!=''){
//                 $where[] = ['s_producto.idtienda','=',$request->input('sucursal')];
//                 $sucursal = $tienda->nombre;
//             }
            
            $allProductos = DB::table('s_producto')
              ->leftJoin('s_marca', 's_marca.id', 's_producto.s_idmarca')
              ->join('s_categoria', 's_categoria.id', 's_producto.s_idcategoria1')
              ->join('s_unidadmedida as unidadmedida_producto', 'unidadmedida_producto.id', 's_producto.idunidadmedida')
              ->where('s_producto.idtienda', $idtienda)
              ->where($where)
              ->select(
                's_producto.*',
                's_categoria.nombre as categoria_nombre',
                's_marca.nombre as marca',
                's_categoria.nombre as categoria'
              )
              ->get();
          
             $pdf = PDF::loadView('layouts/backoffice/tienda/nuevosistema/reporte/reportemargenganancia/tablapdf',[
                'tienda' => $tienda,
                'productos' => $allProductos,
                'fechageneracion' => Carbon::now(),
                'marca' => $marca,
                'categoria' => $categoria, 
                'sucursal' => $sucursal,
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
