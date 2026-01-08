<?php

namespace App\Http\Controllers\Layouts\Buscador\Tienda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF; 

class CategoriaController extends Controller
{
    public function index(Request $request,$linktienda,$data0='',$data1='',$data2='')
    { 
        $tienda = tienda_link($linktienda);

        if($tienda==''){
            return redirect('/');
        }
        $tiendagalerias = DB::table('tiendagaleria')
            ->where('idtienda',$tienda->id)
            ->orderBy('fecharegistro','desc')
            ->get();
        $tiendavideos = DB::table('tiendavideo')
            ->where('idtienda', $tienda->id)
            ->get();
        $recomendaciones = DB::table('recomendacion')
            ->where('idtienda',$tienda->id)
            ->where('idtiporecomendacion',1)
            ->count();
        $s_categorias = DB::table('s_categoria')
            ->where('idtienda',$tienda->id)
            ->where('s_idcategoria',0)
            ->orderBy('s_categoria.nombre','asc')
            ->get();
      
        $where = [];
        $where[] = ['s_producto.idtienda',$tienda->id];
        $menucategoria = '';
        $menucategorianombre = '';
        if($data2!=''){
          if($data2!='searchtienda'){
            $where[] = ['categoria3.nombre',str_replace('-',' ',$data2)];
          }
            $menucategoria = $menucategoria.' / '.str_replace('-',' ',ucfirst(mb_strtolower($data2)));
            $menucategorianombre = $menucategorianombre.' <a href="'.url($tienda->link.'/categoria/'.$data0.'/'.$data1.'/'.$data2).'" class="mx-href"> / '.str_replace('-',' ',ucfirst(mb_strtolower($data2))).'</a>';
        }
        elseif($data1!=''){
          if($data1!='searchtienda'){
            $where[] = ['categoria2.nombre',str_replace('-',' ',$data1)];
          }
            $menucategoria = $menucategoria.' / '.str_replace('-',' ',ucfirst(mb_strtolower($data1)));
            $menucategorianombre = $menucategorianombre.' <a href="'.url($tienda->link.'/categoria/'.$data0.'/'.$data1).'" class="mx-href"> / '.str_replace('-',' ',ucfirst(mb_strtolower($data1))).'</a>';
        }
        elseif($data0!=''){
          if($data0!='searchtienda'){
            $where[] = ['categoria1.nombre',str_replace('-',' ',$data0)];
          }
            $menucategoria = $menucategoria.' / '.str_replace('-',' ',ucfirst(mb_strtolower($data0)));
            $menucategorianombre = $menucategorianombre.' <a href="'.url($tienda->link.'/categoria/'.$data0).'" class="mx-href"> / '.str_replace('-',' ',ucfirst(mb_strtolower($data0))).'</a>';
        }
      
        if($request->input('marca')!=''){
            $where[] = ['s_marca.nombre',str_replace('-',' ',$request->input('marca'))];
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
            ->join('s_categoria as categoria1','categoria1.id','=','s_producto.s_idcategoria1')
            ->leftJoin('s_categoria as categoria2','categoria2.id','=','s_producto.s_idcategoria2')
            ->leftJoin('s_categoria as categoria3','categoria3.id','=','s_producto.s_idcategoria3')
            ->leftJoin('s_marca','s_marca.id','=','s_producto.s_idmarca')
            ->where('s_producto.s_idestadotiendavirtual',1)
            ->where('s_producto.s_idestado',1)
            ->where($where)
            ->select('s_producto.*')
            ->orderBy($orderbyname,$orderbyorder)
            ->paginate(12);
      
      
        /*$s_categorias = DB::table('s_categoria')
            ->where('idtienda',$tienda->id)
            ->where('s_idcategoria',0)
            ->orderBy('s_categoria.nombre','asc')
            ->get();*/
          
        $marcas = DB::table('s_marca')->where('idtienda',$tienda->id)->get();
      
      
        return view('layouts/buscador/tienda/categoria/index',[
                    'tienda' => $tienda,
                    'marcas' => $marcas,
                    'menucategoria' => $menucategoria,
                    'menucategorianombre' => $menucategorianombre,
                    's_categorias' => $s_categorias,
                    's_productos' => $s_productos
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
