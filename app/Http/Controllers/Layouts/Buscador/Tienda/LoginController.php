<?php

namespace App\Http\Controllers\Layouts\Buscador\Tienda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF; 

class LoginController extends Controller
{
    public function index(Request $request)
    { 
        $http_host = '';
        if(isset($_SERVER["REQUEST_URI"])){
            $http_host = $_SERVER["REQUEST_URI"]; 
            $htttp_list = explode('/', $_SERVER["REQUEST_URI"]);
            if(count($htttp_list)>1){
                $http_host = $htttp_list[1];
            }
        }
      
        $tienda = DB::table('tienda')
          ->where('tienda.link','<>','')
          ->where('tienda.link',urldecode($http_host))
          //->where('tienda.idestado',1)
          ->limit(1)
          ->first();
      
        /*$tiendavalid = DB::table('tienda')
          ->where('tienda.link','<>','')
          ->where('tienda.link',urldecode($http_host))
          ->where('tienda.idestado',2)
          ->limit(1)
          ->first();*/
        //if($tienda!=''){
            return view('layouts/buscador/tienda/login/index',[
                'tienda' => $tienda,
                //'tiendavalid' => $tiendavalid,
            ]);
        //}else{
        //    return redirect('/');
        //}
    }

    public function create(Request $request)
    {
        //  
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Request $request, $id)
    {
        
        if($id == 'showlistartiendas'){
            $tiendas = DB::table('tienda')
                ->join('categoria','categoria.id','=','tienda.idcategoria')
                ->where('idestado',1)
                ->where('tienda.nombre','LIKE','%'.$request->input('buscar').'%')
                ->select(
                    'tienda.id as id',
                    'tienda.nombre as text',
                    //'categoria.nombre as categorianombre',
                )
                ->orderBy('tienda.nombre','asc')
                ->get();
            return $tiendas;
        }
    }

    public function edit(Request $request, $id)
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
