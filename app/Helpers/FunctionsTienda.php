<?php
function tienda_link($linktienda=''){
    if($linktienda!=''){
            $tienda = DB::table('tienda')
              ->join('categoria','categoria.id','=','tienda.idcategoria')
              ->join('codigotelefonico','codigotelefonico.id','=','tienda.idcodigotelefonico')
              ->where('tienda.idestadoprivacidad',1)
              ->where('tienda.idestado',1)
              ->where('tienda.link',$linktienda)
              ->select(
                  'tienda.*',
                  'categoria.nombre as categorianombre',
                  'codigotelefonico.codigopais as codigotelefonicocodigo'
              )
              ->first();
    }else{
        $http_host = $_SERVER["HTTP_HOST"]; 
        $htttp_list = explode('www.', $_SERVER["HTTP_HOST"]);
        if(count($htttp_list)>1){
            $http_host = $htttp_list[1];
        }
   
        $tienda_personalizado = DB::table('tienda')->where('dominio_personalizado',$http_host)->first();
        if($tienda_personalizado!=''){
            $tienda = DB::table('tienda')
              ->join('categoria','categoria.id','=','tienda.idcategoria')
              ->join('codigotelefonico','codigotelefonico.id','=','tienda.idcodigotelefonico')
              ->where('tienda.idestadoprivacidad',1)
              ->where('tienda.id',$tienda_personalizado->id)
              ->select(
                  'tienda.*',
                  'categoria.nombre as categorianombre',
                  'codigotelefonico.codigopais as codigotelefonicocodigo'
              )
              ->limit(1)
              ->first();
        }else{
            $linktienda = Request::path();
            $linktiendapath = explode('/',$linktienda);
            if(count($linktiendapath)>1){
                $linktienda = $linktiendapath[0];
            }  

            $tienda = DB::table('tienda')
              ->join('categoria','categoria.id','=','tienda.idcategoria')
              ->join('codigotelefonico','codigotelefonico.id','=','tienda.idcodigotelefonico')
              ->where('tienda.idestadoprivacidad',1)
              ->where('tienda.link',urldecode($linktienda))
              ->select(
                  'tienda.*',
                  'categoria.nombre as categorianombre',
                  'codigotelefonico.codigopais as codigotelefonicocodigo'
              )
              ->first();
        }
      
    }
  
    return $tienda;
}