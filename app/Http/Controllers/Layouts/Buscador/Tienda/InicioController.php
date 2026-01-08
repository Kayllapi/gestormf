<?php

namespace App\Http\Controllers\Layouts\Buscador\Tienda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF; 

class InicioController extends Controller
{
    public function index(Request $request,$linktienda='')
    { 
        $tienda = tienda_link($linktienda);
        if($tienda==''){
            return redirect('/');
        }    
      
        $s_productos = DB::table('s_producto')
            ->join('s_categoria','s_categoria.id','=','s_producto.s_idcategoria1')
            ->where('s_categoria.idtienda',$tienda->id)
            ->where('s_producto.s_idestadotiendavirtual',1)
            ->where('s_producto.s_idestado',1)
            ->select('s_producto.*')
            ->orderBy('s_producto.id','desc')
            ->paginate(12);
      
        $s_ecommerceportada = DB::table('s_ecommerceportada')
            ->where('idtienda',$tienda->id)
            ->orderBy('orden','asc')
            ->get();

        $s_categorias = DB::table('s_categoria')
            ->where('idtienda',$tienda->id)
            ->where('s_idcategoria',0)
            ->orderBy('s_categoria.nombre','asc')
            ->get();
      
        $marcas = DB::table('s_marca')->where('idtienda',$tienda->id)->get();
      
        return view('layouts/buscador/tienda/inicio/index',[
            'tienda' => $tienda,
            's_ecommerceportada' => $s_ecommerceportada,
            's_productos' => $s_productos,
            's_categorias' => $s_categorias,
            'marcas' => $marcas,
        ]);
        
    }

    public function create(Request $request,$linktienda)
    {
       //
    }

    public function store(Request $request,$linktienda)
    {
        //
    }

    public function show(Request $request,$linktienda, $id)
    {
        //
    }

    public function edit(Request $request, $linktienda, $id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
