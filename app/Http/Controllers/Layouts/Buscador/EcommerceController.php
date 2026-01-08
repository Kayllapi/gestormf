<?php

namespace App\Http\Controllers\Layouts\Buscador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class EcommerceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$linktienda='')
    { 
        // DOMINIUO PERSONALIZADO
        $http_host = $_SERVER["HTTP_HOST"]; 
        $htttp_list = explode('www.', $_SERVER["HTTP_HOST"]);
        if(count($htttp_list)>1){
            $http_host = $htttp_list[1];
        }
        $tienda_personalizado = DB::table('tienda')->where('dominio_personalizado',$http_host)->first();
        $valid_tienda = 0;
        if($tienda_personalizado!=''){
            $tienda = DB::table('tienda')
              ->join('categoria','categoria.id','=','tienda.idcategoria')
              ->join('codigotelefonico','codigotelefonico.id','=','tienda.idcodigotelefonico')
              ->where('tienda.id',$tienda_personalizado->id)
              ->select(
                  'tienda.*',
                  'categoria.nombre as categorianombre',
                  'codigotelefonico.codigopais as codigotelefonicocodigo'
              )
              ->limit(1)
              ->first();
            $url_link = ''; 
            $valid_tienda = 1;
        }else{
            $tienda = DB::table('tienda')
              ->join('categoria','categoria.id','=','tienda.idcategoria')
              ->join('codigotelefonico','codigotelefonico.id','=','tienda.idcodigotelefonico')
              ->where('tienda.link',$linktienda)
              ->select(
                  'tienda.*',
                  'categoria.nombre as categorianombre',
                  'codigotelefonico.codigopais as codigotelefonicocodigo'
              )
              ->first();
            if($tienda!=''){
                $url_link = $tienda->link;
                $valid_tienda = 1;
            }
        }
        // FIN DOMINIUO PERSONALIZADO
        if($valid_tienda==1){
            $s_categorias = DB::table('s_categoria')
                ->where('s_idcategoria',0)
                ->where('idtienda',$tienda->id)
                ->orderBy('orden','asc')
                ->get();
            $s_ecommerceportada = DB::table('s_ecommerceportada')
                ->where('idtienda',$tienda->id)
                ->orderBy('orden','asc')
                ->get();
            $productosvalorados = DB::table('s_producto')
                    ->join('s_categoria','s_categoria.id','=','s_producto.s_idcategoria1')
                    ->where('s_categoria.idtienda',$tienda->id)
                    ->select('s_producto.*','s_categoria.nombre as categorianombre')
                    ->orderBy('s_producto.id','desc')
                    ->paginate(10);
            return view('layouts/buscador/ecommerce/inicio',[
                'tienda' => $tienda,
                'url_link' => $url_link,
                's_categorias' => $s_categorias,
                's_ecommerceportada' => $s_ecommerceportada,
                'productosvalorados' => $productosvalorados,
            ]);  
        }else{
            //return redirect('/');
            return abort(404);
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request,$linktienda,$s_idcategoria,$s_idproducto=0)
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
    public function show(Request $request, $linktienda, $pagina)
    {
        $http_host = $_SERVER["HTTP_HOST"]; 
        $htttp_list = explode('www.', $_SERVER["HTTP_HOST"]);
        if(count($htttp_list)>1){
            $http_host = $htttp_list[1];
        }
        $tienda_personalizado = DB::table('tienda')->where('dominio_personalizado',$http_host)->first();
        $valid_tienda = 0;
        if($tienda_personalizado!=''){
            $tienda = DB::table('tienda')
              ->join('categoria','categoria.id','=','tienda.idcategoria')
              ->join('codigotelefonico','codigotelefonico.id','=','tienda.idcodigotelefonico')
              ->where('tienda.id',$tienda_personalizado->id)
              ->select(
                  'tienda.*',
                  'categoria.nombre as categorianombre',
                  'codigotelefonico.codigopais as codigotelefonicocodigo'
              )
              ->limit(1)
              ->first();
            $url_link = ''; 
            $valid_tienda = 1;
        }else{
            $tienda = DB::table('tienda')
              ->join('categoria','categoria.id','=','tienda.idcategoria')
              ->join('codigotelefonico','codigotelefonico.id','=','tienda.idcodigotelefonico')
              ->where('tienda.link',$linktienda)
              ->select(
                  'tienda.*',
                  'categoria.nombre as categorianombre',
                  'codigotelefonico.codigopais as codigotelefonicocodigo'
              )
              ->first();
            if($tienda!=''){
                $url_link = $tienda->link;
                $valid_tienda = 1;
            }
        }
      
        if($valid_tienda==1){
            $s_categorias = DB::table('s_categoria')
                ->where('s_idcategoria',0)
                ->where('idtienda',$tienda->id)
                ->orderBy('orden','asc')
                ->get();
            if($pagina=='producto'){
                $s_marca = DB::table('s_marca')->where('idtienda',$tienda->id)->get();
                $s_productos = DB::table('s_producto')
                    ->join('s_categoria','s_categoria.id','=','s_producto.s_idcategoria1')
                    ->where('s_categoria.idtienda',$tienda->id)
                    ->where('s_producto.nombre','LIKE','%'.$request->input('search').'%')
                    ->where('s_categoria.nombre','LIKE','%'.$request->input('categoria').'%')
                    ->select('s_producto.*','s_categoria.nombre as categorianombre')
                    ->orderBy('s_producto.id','desc')
                    ->paginate(9);
              
                return view('layouts/buscador/ecommerce/producto',[
                    'tienda' => $tienda,
                    'url_link' => $url_link,
                    's_categorias' => $s_categorias,
                    's_marca' => $s_marca,
                    's_productos' => $s_productos,
                ]); 
            }
        }else{
            return abort(404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
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
