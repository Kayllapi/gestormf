<?php

namespace App\Http\Controllers\Layouts\Buscador\Tienda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF; 

class ProductoController extends Controller
{
    public function index(Request $request,$linktienda,$producto)
    { 
        $tienda = tienda_link($linktienda);
        if($tienda==''){
            return redirect('/');
        }
        $producto = str_replace('-----','/',$producto);

        $s_producto = DB::table('s_producto')
            ->where('s_producto.s_idestadotiendavirtual',1)
            ->where('s_producto.s_idestado',1)
            ->where('s_producto.idtienda',$tienda->id)
            ->where('s_producto.nombre',$producto)
            ->orWhere('s_producto.s_idestadotiendavirtual',1)
            ->where('s_producto.s_idestado',1)
            ->where('s_producto.idtienda',$tienda->id)
            ->where('s_producto.id',$producto)
            ->first();
        //dd(str_replace('-',' ',$producto));
        $s_categorias = DB::table('s_categoria')
            ->where('idtienda',$tienda->id)
            ->where('s_idcategoria',0)
            ->orderBy('s_categoria.nombre','asc')
            ->get();
          
        $marcas = DB::table('s_marca')->where('idtienda',$tienda->id)->get();
      
        return view('layouts/buscador/tienda/producto/index',[
            'tienda' => $tienda,
            'marcas' => $marcas,
            's_categorias' => $s_categorias,
            's_producto' => $s_producto
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
