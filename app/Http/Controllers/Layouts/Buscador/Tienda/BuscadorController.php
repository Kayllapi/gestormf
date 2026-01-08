<?php

namespace App\Http\Controllers\Layouts\Buscador\Tienda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF; 

class BuscadorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$linktienda)
    { 
        $tienda = DB::table('tienda')
              ->where('tienda.link',$linktienda)
              ->first();
        
        if($tienda==''){
            return redirect('/');
        }
      
        $where = [];
        if($request->input('marca')!=''){
            $where[] = ['s_marca.nombre',$request->input('marca')];
        }
        $orderbyname = 's_producto.id';
        $orderbyorder = 'desc';
        if($request->input('precio')=='mayor-precio'){
            $orderbyname = 's_producto.precioalpublico';
            $orderbyorder = 'desc';
        }elseif($request->input('precio')=='menor-precio'){
            $orderbyname = 's_producto.precioalpublico';
            $orderbyorder = 'asc';
        }
        $s_productos = DB::table('s_producto')
            ->join('s_categoria','s_categoria.id','=','s_producto.s_idcategoria1')
            ->leftJoin('s_marca','s_marca.id','=','s_producto.s_idmarca')
            ->where('s_categoria.idtienda',$tienda->id)
            ->where('s_producto.nombre','LIKE','%'.$request->input('search').'%')
            ->where('s_producto.s_idestadotiendavirtual',1)
            ->where('s_producto.s_idestado',1)
            ->where($where)
            ->select('s_producto.*')
            ->orderBy($orderbyname,$orderbyorder)
            ->paginate(12);
        $menucategoria = '';
        if($request->input('search')!=''){
            $menucategoria = $menucategoria.' / '.$request->input('search');
        }else{
            $menucategoria = $menucategoria.' / Todo los productos';
        }
      
            
        $s_categorias = DB::table('s_categoria')
            ->where('idtienda',$tienda->id)
            ->where('s_idcategoria',0)
            ->orderBy('s_categoria.nombre','asc')
            ->get();
          
        $marcas = DB::table('s_marca')->where('idtienda',$tienda->id)->get();
      
        return view('layouts/buscador/tienda/buscador/index',[
            'tienda' => $tienda,
            's_categorias' => $s_categorias,
            's_productos' => $s_productos,
            'menucategoria' => $menucategoria,
            'marcas' => $marcas,
        ]);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request,$linktienda)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$linktienda)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$linktienda, $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $linktienda, $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
