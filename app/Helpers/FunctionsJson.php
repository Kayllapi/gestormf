<?php
function json_create($idtienda,$name_modulo,$data,$idadicional=''){
    $directorio = getcwd().'/public/backoffice/tienda/'.$idtienda.'';
    if (!file_exists($directorio)) { 
    //   mkdir($directorio, 0777); 
      mkdir($directorio,0777, true);
      chmod($directorio,0777);

    }
    $directorio = getcwd().'/public/backoffice/tienda/'.$idtienda.'/sistema_json';
    if (!file_exists($directorio)) { 
    //   mkdir($directorio, 0777); 
      mkdir($directorio,0777, true);
      chmod($directorio,0777);
    }
    $file = $directorio.'/'.$name_modulo.$idadicional.'.json';
    $json_string = json_encode(array('data' => $data));
    file_put_contents($file, $json_string);
}
// funciones JSON
function json_update($idtienda,$idsucursal=0,$idusuario=0){
    json_estado();
    json_formapago();
    json_tipoproductomovimiento();
    json_tipopago();
    //json_tipopersona();
    json_tipocomprobante();
    json_tipocotizacion();
    json_moneda();
    json_ubigeo();
    json_unidadmedida();
  
    json_agencia($idtienda);
    json_usuario($idtienda);
    json_usuarioacceso($idtienda);
    json_categoria($idtienda);
    json_marca($idtienda);
    json_caja($idtienda);
    json_cuentabancaria($idtienda);
    json_producto($idtienda);
  
    //json_productomovimiento($idtienda,$idsucursal);
    //json_productotransferencia($idtienda,$idsucursal);
    //json_cajaapertura($idtienda,$idsucursal);
  
    //json_movimiento($idtienda,$idsucursal,$idusuario);
    //json_compra($idtienda,$idsucursal,$idusuario);
    //json_compradevolucion($idtienda,$idsucursal,$idusuario);
    //json_preprensa($idtienda,$idsucursal,$idusuario);
    //json_prensa($idtienda,$idsucursal,$idusuario);
    //json_postprensa($idtienda,$idsucursal,$idusuario);
    //json_cotizacion($idtienda,$idsucursal,$idusuario);
    //json_ordenservicio($idtienda,$idsucursal,$idusuario);
    //json_venta($idtienda,$idsucursal,$idusuario);
    //json_ventacredito($idtienda,$idsucursal);
    //json_cobranzacredito($idtienda, $idsucursal);
    //json_ventadevolucion($idtienda,$idsucursal,$idusuario);
    // json_facturacionboletafactura($idtienda, $idsucursal, $idusuario);
    // json_facturacionnotacredito($idtienda, $idsucursal, $idusuario);
    // json_facturacionnotadebito($idtienda, $idsucursal, $idusuario);
    // json_facturacionguiaremision($idtienda, $idsucursal, $idusuario);
    // json_facturacionresumendiario($idtienda, $idsucursal, $idusuario);
    // json_facturacioncomunicacionbaja($idtienda, $idsucursal, $idusuario);
  
    
    //json_ventadevolucion($idtienda,$idsucursal,$idusuario);
}
function json_delete($idtienda){
    $directorio = getcwd().'/public/backoffice/tienda/'.$idtienda.'/sistema_json/movimiento.json';
    if (file_exists($directorio)) { 
        unlink($directorio); 
    }
}
function json_estado(){
    $estados = DB::table('s_estado')->get();
    $tabla = [];
    foreach($estados as $value){
        $tabla[] = [
            'id'            => $value->id,
            'text'          => $value->nombre,
        ];
    }
    $file = getcwd().'/public/nuevosistema/librerias/json/estado.json';
    $json_string = json_encode(array('data' => $tabla));
    file_put_contents($file, $json_string);
}
function json_formapago(){
    $formapagos = DB::table('s_formapago')->get();
    $tabla = [];
    foreach($formapagos as $value){
        $tabla[] = [
            'id'            => $value->id,
            'text'          => $value->nombre,
        ];
    }
    $file = getcwd().'/public/nuevosistema/librerias/json/formapago.json';
    $json_string = json_encode(array('data' => $tabla));
    file_put_contents($file, $json_string);
}
function json_tipoproductomovimiento(){
    $tipoproductomovimientos = DB::table('s_tipoproductomovimiento')->get();
    $tabla = [];
    foreach($tipoproductomovimientos as $value){
        $tabla[] = [
            'id'            => $value->id,
            'text'          => $value->nombre,
        ];
    }
    $file = getcwd().'/public/nuevosistema/librerias/json/tipoproductomovimiento.json';
    $json_string = json_encode(array('data' => $tabla));
    file_put_contents($file, $json_string);
}
function json_tipopago(){
    $tipopagos = DB::table('s_tipopago')->get();
    $tabla = [];
    foreach($tipopagos as $value){
        $tabla[] = [
            'id'            => $value->id,
            'text'          => $value->nombre,
        ];
    }
    $file = getcwd().'/public/nuevosistema/librerias/json/tipopago.json';
    $json_string = json_encode(array('data' => $tabla));
    file_put_contents($file, $json_string);
}
/*function json_tipopersona(){
    $tipopersonas = DB::table('tipopersona') ->get();
    $tabla = [];
    foreach($tipopersonas as $value){
        $tabla[] = [
            'id'            => $value->id,
            'text'          => $value->nombre,
        ];
    }
    $file = getcwd().'/public/nuevosistema/librerias/json/tipopersona.json';
    $json_string = json_encode(array('data' => $tabla));
    file_put_contents($file, $json_string);
}*/
function json_tipomovimiento(){
    $tipomovimientos = DB::table('s_tipomovimiento')->get();
    $tabla = [];
    foreach($tipomovimientos as $value){
        $tabla[] = [
            'id'            => $value->id,
            'text'          => $value->nombre,
        ];
    }
    $file = getcwd().'/public/nuevosistema/librerias/json/tipomovimiento.json';
    $json_string = json_encode(array('data' => $tabla));
    file_put_contents($file, $json_string);
}
function json_tipocomprobante(){
    $tipocomprobantes = DB::table('s_tipocomprobante')->get();
    $tabla = [];
    foreach($tipocomprobantes as $value){
        $tabla[] = [
            'id'            => $value->id,
            'text'          => $value->nombre,
        ];
    }
    $file = getcwd().'/public/nuevosistema/librerias/json/tipocomprobante.json';
    $json_string = json_encode(array('data' => $tabla));
    file_put_contents($file, $json_string);
}
function json_tipocotizacion(){
    $tipocotizacions = DB::table('s_tipocotizacion')->get();
    $tabla = [];
    foreach($tipocotizacions as $value){
        $tabla[] = [
            'id'            => $value->id,
            'text'          => $value->nombre,
        ];
    }
    $file = getcwd().'/public/nuevosistema/librerias/json/tipocotizacion.json';
    $json_string = json_encode(array('data' => $tabla));
    file_put_contents($file, $json_string);
}
function json_moneda(){
    $monedas = DB::table('s_moneda')->get();
    $tabla = [];
    foreach($monedas as $value){
        $tabla[] = [
            'id'      => $value->id,
            'text'    => $value->nombre,
            'nombre'  => $value->nombre,
            'simbolo' => $value->simbolo,
        ];
    }
    $file = getcwd().'/public/nuevosistema/librerias/json/moneda.json';
    $json_string = json_encode(array('data' => $tabla));
    file_put_contents($file, $json_string);
}
function json_ubigeo(){
    $ubigeos = DB::table('ubigeo')->get();
    $tabla = [];
    foreach($ubigeos as $value){
        $tabla[] = [
            'id'            => $value->id,
            'distrito'      => $value->distrito,
            'provincia'     => $value->provincia,
            'departamento'  => $value->departamento,
            'nombre'        => $value->nombre,
            'text'          => $value->codigo." - ".$value->nombre,
        ];
    }
    $file = getcwd().'/public/nuevosistema/librerias/json/ubigeo.json';
    $json_string = json_encode(array('data' => $tabla));
    file_put_contents($file, $json_string);
}
function json_unidadmedida(){
    $unidadmedidas = DB::table('s_unidadmedida')->get();
    $tabla = [];
    foreach($unidadmedidas as $value){
        $tabla[] = [
            'id'            => $value->id,
            'text'          => $value->nombre,
        ];
    }
    $file = getcwd().'/public/nuevosistema/librerias/json/unidadmedida.json';
    $json_string = json_encode(array('data' => $tabla));
    file_put_contents($file, $json_string);
}
function json_agencia($idtienda){
    $agencias = DB::table('s_agencia')
        // ->where('s_agencia.idtienda',$idtienda)
        ->where('s_agencia.idestado',1)
        ->orderBy('s_agencia.id','desc')
        ->get();

    $tabla = [];
    foreach($agencias as $value){
        $tabla[] = [
            'id'              => $value->id,
            'text'            => $value->nombrecomercial,
            'ruc'             => $value->ruc,
            'nombrecomercial' => $value->nombrecomercial,
            'razonsocial'     => $value->razonsocial,
            'direccion'       => $value->direccion,
            'facturacion'     => $value->idestadofacturacion,
            'idubigeo'        => $value->idubigeo,
            'logo'            => $value->logo!=''?'/'.$idtienda.'/sistema/'.$value->logo:'',
            'opcion'          => [
                [
                    'nombre'  => 'Editar',
                    'onclick' => '/'.$idtienda.'/agencia/'.$value->id.'/edit?view=editar',
                    'icono'   => 'edit'
                ],
                /*[
                    'nombre'  => 'Eliminar',
                    'onclick' => '/'.$idtienda.'/agencia/'.$value->id.'/edit?view=eliminar',
                    'icono'   => 'trash'
                ]*/
            ]
        ];
    }

    json_create($idtienda,'agencia',$tabla);
}
function json_sucursal($idtienda){
    
    $tiendas = DB::table('tienda')
        ->leftJoin('ubigeo', 'ubigeo.id', 'tienda.idubigeo')
        ->select(
            'tienda.*',
            'ubigeo.nombre as ubigeonombre',
        )
        // ->where('tienda.id',$idtienda)
        ->orderBy('tienda.id','desc')
        ->get();
    foreach($tiendas as $value){
        $tabla[] = [
            'id'              => $value->id,
            'text'            => $value->nombreagencia.'('.$value->nombre.')',
            'nombreagencia'   => $value->nombreagencia,
            'nombre'          => $value->nombre,
            'representante'   => $value->representante,
            'direccion'       => $value->direccion.' - '.$value->ubigeonombre,
            'telefono'        => $value->numerotelefono,
            'tipoempresa'     => $value->tipo_empresa,
            'opcion'          => [
                [
                    'nombre'  => 'Editar',
                    'onclick' => '/'.$idtienda.'/sucursal/'.$value->id.'/edit?view=editar',
                    'icono'   => 'edit'
                ],
            ]
        ];
    }
    

    json_create($idtienda,'sucursal',$tabla);
}
function json_usuario($idtienda){
  
    $usuarios = DB::table('users')
        ->join('tipopersona','tipopersona.id','=','users.idtipopersona')
        ->leftJoin('ubigeo','ubigeo.id','=','users.idubigeo')
        ->leftJoin('s_users_prestamo','s_users_prestamo.id_s_users','users.id')
        ->where('users.idestado',1)
        ->where('users.idtipousuario',2)
        // ->where('users.idtienda',$idtienda)
        ->select(
            'users.*',
            's_users_prestamo.db_idtipodocumento as tipodocumento_persona',
            'tipopersona.nombre as tipopersonanombre',
            'ubigeo.codigo as ubigeocodigo',
            'ubigeo.nombre as ubigeonombre',
        )
        ->orderBy('users.id','desc')
        ->get();

    $tabla = [];
    foreach($usuarios as $value){
      
        $tabla[] = [
            'id'              => $value->id,
            'text'            => ($value->identificacion!=0?$value->identificacion.' - ':'').$value->nombrecompleto,
            'codigo'          => $value->codigo,
            'idtipopersona'   => $value->idtipopersona,
            'tipodocumento'   => $value->tipodocumento_persona,
            'persona'         => $value->tipopersonanombre,
            'identificacion'  => $value->identificacion!=0?$value->identificacion:'',
            'cliente'         => $value->nombrecompleto,
            'telefono'        => $value->numerotelefono,
            'direccion'       => $value->direccion,
            'idubigeo'        => $value->idubigeo,
            'ubigeo'          => $value->ubigeocodigo!=''?$value->ubigeocodigo.' - '.$value->ubigeonombre:'',
            'opcion'          => [
                [
                    'nombre'  => 'Editar',
                    'onclick' => '/'.$idtienda.'/usuario/'.$value->id.'/edit?view=editar',
                    'icono'   => 'edit'
                ],
                [
                    'nombre'  => 'Editar Ubicación',
                    'onclick' => '/'.$idtienda.'/usuario/'.$value->id.'/edit?view=ubicacion',
                    'icono'   => 'location-dot'
                ],
                [
                    'nombre'  => 'Ficha',
                    'onclick' => '/'.$idtienda.'/usuario/'.$value->id.'/edit?view=ficha',
                    'icono'   => 'list'
                ],
                /*[
                    'nombre'  => 'Eliminar',
                    'onclick' => '/'.$idtienda.'/usuario/'.$value->id.'/edit?view=eliminar',
                    'icono'   => 'trash'
                ]*/
            ]
        ];
    }
    json_create($idtienda,'usuario',$tabla);
}
function json_usuarioacceso($idtienda){
  
    $usuarios = DB::table('users')
        // ->join('role_user','role_user.user_id','users.id')
        // ->join('roles','roles.id','role_user.role_id')
        // ->where('users.idtienda',$idtienda)
        ->where('users.idestado',1)
        ->where('users.idtipousuario',1)
        ->where('users.id','<>',1)
        ->select(
            'users.*',
            // 'roles.id as idroles',
            // 'role_user.cargo as cargo'
        )
        ->orderBy('users.id','desc')
        ->get();

    $tabla = [];
    foreach($usuarios as $value){
      
        
          $tienda_permiso = DB::table('users_permiso')
                              ->join('permiso','permiso.id','users_permiso.idpermiso')
                              ->join('tienda','tienda.id','users_permiso.idtienda')
                              ->where('users_permiso.idusers',$value->id)
                              ->select(
                                'users_permiso.*',
                                'permiso.nombre as nombrepermiso',
                                'tienda.nombre as tiendanombre',
                                'tienda.nombreagencia as nombreagencia',
                              )
                              ->get();

      
            $permiso = '';
            foreach($tienda_permiso as $val_permiso){
                $permiso = $val_permiso->tiendanombre.' / '.$val_permiso->nombreagencia.' ('.$val_permiso->nombrepermiso.')'.'<br>'.$permiso;
            }

      
        $tabla[] = [
            'id'              => $value->id,
            'text'            => ($value->identificacion!=0?$value->identificacion.' - ':'').$value->nombrecompleto,
            'codigo'  => $value->codigo,
            'identificacion'  => $value->identificacion,
            'cliente'         => $value->nombrecompleto,
            'cargo'         => $permiso,
            'usuario'         => $value->usuario.' '.($idtienda==0?'('.$value->clave.')':''),
            'idestadousuario' => $value->idestadousuario,
            'opcion' => [
                // [
                //     'nombre'  => 'Editar',
                //     'onclick' => '/'.$idtienda.'/usuarioacceso/'.$value->id.'/edit?view=editar',
                //     'icono'   => 'edit'
                // ],
                [
                    'nombre'  => 'Editar',
                    'onclick' => '/'.$idtienda.'/usuarioacceso/'.$value->id.'/edit?view=permiso',
                    'icono'   => 'edit'
                ],
                // [
                //     'nombre'  => 'Eliminar',
                //     'onclick' => '/'.$idtienda.'/usuarioacceso/'.$value->id.'/edit?view=eliminar',
                //     'icono'   => 'trash'
                // ]
            ]
        ];
    }
    json_create($idtienda,'usuarioacceso',$tabla);
}
function json_categoria($idtienda){
    $categorias = DB::table('s_categoria')
        ->where('s_categoria.idtienda',$idtienda)
        ->where('s_categoria.idestado',1)
        ->where('s_categoria.s_idcategoria',0)
        ->orderBy('s_categoria.id','desc')
        ->get();

    $tabla = [];
    foreach($categorias as $value){
      
        $tabla[] = [
            'id'            => $value->id,
            'text'          => $value->nombre,
            'nombre'        => $value->nombre,
            'imagen'        => $value->imagen!=''?'/'.$idtienda.'/sistema/'.$value->imagen:'',
            'opcion'        => [
                [
                    'nombre'  => 'Editar',
                    'onclick' => '/'.$idtienda.'/categoria/'.$value->id.'/edit?view=editar',
                    'icono'   => 'edit'
                ],
                [
                    'nombre'  => 'Registrar Sub Categoria',
                    'onclick' => '/'.$idtienda.'/categoria/create?view=registrar&idcategoria='.$value->id,
                    'icono'   => 'save'
                ],
                [
                    'nombre'  => 'Eliminar',
                    'onclick' => '/'.$idtienda.'/categoria/'.$value->id.'/edit?view=eliminar',
                    'icono'   => 'trash'
                ]
            ]
        ];

        $categorias2 = DB::table('s_categoria')
            ->where('s_categoria.idtienda',$idtienda)
            ->where('s_categoria.s_idcategoria',$value->id)
            ->where('s_categoria.idestado',1)
            ->orderBy('s_categoria.id','desc')
            ->get();
        foreach($categorias2 as $value2){
          
            $categoria_nombre = $value->nombre.' / '.$value2->nombre;
          
            $tabla[] = [
                'id'            => $value2->id,
                'text'          => $categoria_nombre,
                'nombre'        => $categoria_nombre,
                'imagen'        => $value2->imagen!=''?'/'.$idtienda.'/sistema/'.$value2->imagen:'',
                'opcion' => [
                    [
                        'nombre'  => 'Editar',
                        'onclick' => '/'.$idtienda.'/categoria/'.$value2->id.'/edit?view=editar',
                        'icono'   => 'edit'
                    ],
                    [
                        'nombre'  => 'Registrar Sub Categoria',
                        'onclick' => '/'.$idtienda.'/categoria/create?view=registrar&idcategoria='.$value2->id,
                        'icono'   => 'save'
                    ],
                    [
                        'nombre'  => 'Eliminar',
                        'onclick' => '/'.$idtienda.'/categoria/'.$value2->id.'/edit?view=eliminar',
                        'icono'   => 'trash'
                    ]
                ]
            ];
            $categorias3 = DB::table('s_categoria')
                ->where('s_categoria.idtienda',$idtienda)
                ->where('s_categoria.s_idcategoria',$value2->id)
                ->where('s_categoria.idestado',1)
                ->orderBy('s_categoria.id','desc')
                ->get();
            foreach($categorias3 as $value3){
              
                $categoria_nombre = $value->nombre.' / '.$value2->nombre.' / '.$value3->nombre;

                $tabla[] = [
                    'id'            => $value3->id,
                    'text'          => $categoria_nombre,
                    'nombre'        => $categoria_nombre,
                    'imagen'        => $value3->imagen!=''?'/'.$idtienda.'/sistema/'.$value3->imagen:'',
                    'opcion' => [
                        [
                            'nombre'  => 'Editar',
                            'onclick' => '/'.$idtienda.'/categoria/'.$value3->id.'/edit?view=editar',
                            'icono'   => 'edit'
                        ],
                        [
                            'nombre'  => 'Eliminar',
                            'onclick' => '/'.$idtienda.'/categoria/'.$value3->id.'/edit?view=eliminar',
                            'icono'   => 'trash'
                        ]
                    ]
                ];
            }
        }

    }
    json_create($idtienda,'categoria',$tabla);      
}
function json_marca_data($idtienda,$param){

    $rutaimagen = getcwd().'/public/backoffice/tienda/'.$idtienda.'/sistema/'.$param['imagen']; 
    if(file_exists($rutaimagen) && $param['imagen']!=''){
        $urlimagen = url('/public/backoffice/tienda/'.$idtienda.'/sistema/'.$param['imagen']);
    }else{
        $urlimagen = url('public/backoffice/sistema/sin_imagen_cuadrado.png');
    }

    $countproductos = DB::table('s_producto')
        ->where('s_producto.idtienda',$idtienda)
        ->where('s_producto.s_idmarca',$param['id'])
        ->count();

    $btn_eliminar = [];
    if($countproductos==0){
        $btn_eliminar = [
            'nombre'  => 'Eliminar',
            'onclick' => '/'.$idtienda.'/marca/'.$param['id'].'/edit?view=eliminar',
            'icono'   => 'trash'
        ];
    }

    $data = [
        'id' => $param['id'],
        'nombre' => $param['nombre'],
        'imagen' => $urlimagen,
        'cantidad_productos' => $countproductos,
        'text' => $param['nombre'],
        'opcion' => [
            [
                'nombre'  => 'Editar',
                'onclick' => '/'.$idtienda.'/marca/'.$param['id'].'/edit?view=editar',
                'icono'   => 'edit'
            ],
            $btn_eliminar
        ]
    ];
  
    return $data;
}
function json_marca_mysql($idtienda){
    $marcas = DB::table('s_marca')
        ->where('s_marca.idtienda',$idtienda)
        ->orderBy('s_marca.id','desc')
        ->get();

    $data = [];
    foreach($marcas as $value){
        $data[] = json_marca_data($idtienda,[
            'id'      => $value->id,
            'nombre'  => $value->nombre,
            'imagen'  => $value->imagen,
        ]);
    }
    json_create($idtienda,'marca',$data);   
}
function json_marca($idtienda,$param=[]){
    $marcas = DB::table('s_marca')
        ->where('s_marca.idtienda',$idtienda)
        ->orderBy('s_marca.id','desc')
        ->get();
    $tabla = [];
    foreach($marcas as $value){
        $data[] = [
            'id'      => $value->id,
            'text'    => $value->nombre,
            'nombre'  => $value->nombre,
            'imagen'  => $value->imagen!=''?'/'.$idtienda.'/sistema/'.$value->imagen:'',
            'opcion'  => [
                [
                    'nombre'  => 'Editar',
                    'onclick' => '/'.$idtienda.'/marca/'.$value->id.'/edit?view=editar',
                    'icono'   => 'edit'
                ],
                [
                    'nombre'  => 'Eliminar',
                    'onclick' => '/'.$idtienda.'/marca/'.$value->id.'/edit?view=eliminar',
                    'icono'   => 'trash'
                ]
            ]
        ];
    }
    json_create($idtienda,'marca',$data); 
    /*if(isset($param['json'])){
        $json_data = json_decode(file_get_contents('public/backoffice/tienda/'.$idtienda.'/sistema_json/marca.json'), true);
        if($param['json']=='insert'){
            $data = json_marca_data($idtienda,[
                'id'      => $param['id'],
                'nombre'  => $param['nombre'],
                'imagen'  => $param['imagen'],
            ]);
            array_unshift($json_data['data'], $data);
            $json_data = $json_data['data'];
        }
        elseif($param['json']=='update'){
            foreach ($json_data['data'] as $key => $value) {
                if (in_array($param['id'], $value)) {
                    $data = json_marca_data($idtienda,[
                        'id'      => $param['id'],
                        'nombre'  => $param['nombre'],
                        'imagen'  => $param['imagen'],
                    ]);

                    $json_data['data'][$key] = $data;
                }
            }
            $json_data = array_values($json_data['data']);
        }
        elseif($param['json']=='delete'){
            foreach ($json_data['data'] as $key => $value) {
                if (in_array($param['id'], $value)) {
                    unset($json_data['data'][$key]);
                }
            }
            $json_data = array_values($json_data['data']);
        }
        json_create($idtienda,'marca',$json_data); 
    }
    else{
      
    
    }  */
}
function json_producto($idtienda){
    $productos = DB::table('s_producto')
        ->where('s_producto.idtienda',$idtienda)
        ->where('s_producto.idestado',1)
        ->select(
          's_producto.*',
        )
        ->orderBy('s_producto.id','desc')
        ->get();

    $moneda_soles = DB::table('s_moneda')->whereId(1)->first();
    $moneda_dolares = DB::table('s_moneda')->whereId(2)->first();
  
    $tabla = [];
    foreach($productos as $value){
       
        $style = '';
        if($value->s_idestadosistema==2){
            $style = 'table-mark-anulado';
        }
        elseif($value->db_presentacion!=''){
            $style = 'table-mark-presentacion';
        }
 
        $tabla[] = [
            'style' => $style,
            'id' => $value->id,
            'text' => $value->codigo.' / '.$value->nombre,
          
            'codigo' => $value->codigo,
            'nombre' => $value->nombre,
            'por' => $value->por,
            'preciominimo' => $moneda_soles->simbolo.' '.$value->preciominimo,
            'preciopublico' => $moneda_soles->simbolo.' '.$value->preciopublico,
            'preciominimo_dolares' => $moneda_dolares->simbolo.' '.$value->preciominimo_dolares,
            'preciopublico_dolares' => $moneda_dolares->simbolo.' '.$value->preciopublico_dolares,
            'imagen'  => $value->imagen!=''?'/'.$idtienda.'/producto/'.$value->imagen:'',
            'estadodetalle' => $value->s_idestadodetalle,
            'estadosistema' => $value->s_idestadosistema,
            'estadotiendavirtual' => $value->s_idestadotiendavirtual,
            'idunidadmedida' => $value->s_idunidadmedida,
            'categorianombre' => $value->db_idcategoria,
            'marcanombre' => $value->db_idmarca,
            'unidadmedidanombre' => $value->db_idunidadmedida,
            'db_codigo' => json_decode($value->db_codigo),
            'db_imagen' => json_decode($value->db_imagen),
            'db_presentacion' => json_decode($value->db_presentacion),
            'db_stock' => json_decode($value->db_stock),
            'opcion' => [
                [
                    'nombre'  => 'Editar',
                    'onclick' => '/'.$idtienda.'/producto/'.$value->id.'/edit?view=editar',
                    'icono'   => 'edit'
                ],
                [
                    'nombre'  => 'Editar Precios',
                    'onclick' => '/'.$idtienda.'/producto/'.$value->id.'/edit?view=editarpresentacion',
                    'icono'   => 'edit'
                ],
                [
                    'nombre'  => 'Editar Imagenes',
                    'onclick' => '/'.$idtienda.'/producto/'.$value->id.'/edit?view=imagen',
                    'icono'   => 'image'
                ],
                [
                    'nombre'  => 'Código de Barras',
                    'onclick' => '/'.$idtienda.'/producto/'.$value->id.'/edit?view=codigobarra',
                    'icono'   => 'barcode'
                ],
                [
                    'nombre'  => 'Eliminar',
                    'onclick' => '/'.$idtienda.'/producto/'.$value->id.'/edit?view=eliminar',
                    'icono'   => 'trash'
                ]
            ]
        ];

    }

    json_create($idtienda,'producto',$tabla);
}
function json_caja($idtienda){
    $cajas = DB::table('s_caja')
        ->where('s_caja.idtienda',$idtienda)
        ->where('s_caja.idestado',1)
        ->orderBy('s_caja.id','desc')
        ->get();

    $tabla = [];
    foreach($cajas as $value){
      
        /*$efectivocaja_soles = sistema_caja_efectivo([
            'idtienda'  => $idtienda,
            'idcaja'    => $value->id,
            'idmoneda'  => 1,
        ]);
        $efectivocaja_dolares = sistema_caja_efectivo([
            'idtienda'  => $idtienda,
            'idcaja'    => $value->id,
            'idmoneda'  => 2,
        ]);*/
      
        $tabla[] = [
            'id' => $value->id,
            'nombre' => $value->nombre,
            'text' => $value->nombre.' (S/. 0 - $ 0)',
            'total_soles' => 0,
            'total_dolares' => 0,
            'opcion' => [
                [
                    'nombre'  => 'Editar',
                    'onclick' => '/'.$idtienda.'/caja/'.$value->id.'/edit?view=editar',
                    'icono'   => 'edit'
                ],
                [
                    'nombre'  => 'Eliminar',
                    'onclick' => '/'.$idtienda.'/caja/'.$value->id.'/edit?view=eliminar',
                    'icono'   => 'trash'
                ]
            ]
        ];

    }
    json_create($idtienda,'caja',$tabla);      
}
function json_cuentabancaria($idtienda){
    $cuentabancarias = DB::table('s_cuentabancaria')
        ->where('s_cuentabancaria.idtienda',$idtienda)
        ->where('s_cuentabancaria.idestado',1)
        ->orderBy('s_cuentabancaria.id','desc')
        ->get();

    $tabla = [];
    foreach($cuentabancarias as $value){
        $tabla[] = [
            'id'            => $value->id,
            'text'          => $value->banco.' - '.$value->numerocuenta,
            'banco'         => $value->banco,
            'numerocuenta'  => $value->numerocuenta,
            'opcion'  => [
                [
                    'nombre'  => 'Editar',
                    'onclick' => '/'.$idtienda.'/cuentabancaria/'.$value->id.'/edit?view=editar',
                    'icono'   => 'edit'
                ],
                [
                    'nombre'  => 'Eliminar',
                    'onclick' => '/'.$idtienda.'/cuentabancaria/'.$value->id.'/edit?view=eliminar',
                    'icono'   => 'trash'
                ]
            ]
        ];

    }
    json_create($idtienda,'cuentabancaria',$tabla);      
}
function json_movimiento($idtienda,$idsucursal,$idusuario){
    $idapertura = sistema_apertura([
        'idtienda'          => $idtienda,
        'idsucursal'        => $idsucursal,
        'idusersrecepcion'  => $idusuario,
    ])['idapertura'];
  
   $s_movimientos  = DB::table('s_movimiento')
        ->join('s_moneda','s_moneda.id','s_movimiento.s_idmoneda')
        ->leftJoin('s_aperturacierre','s_aperturacierre.id','s_movimiento.s_idaperturacierre')
        ->leftJoin('s_caja','s_caja.id','s_aperturacierre.s_idcaja')
        ->join('users as responsable','responsable.id','s_movimiento.s_idusuario')
        ->where('s_movimiento.idtienda',$idtienda)
        ->where('s_movimiento.idsucursal',$idsucursal)
        ->where('s_movimiento.idestado',1)
        ->where('s_movimiento.s_idaperturacierre',$idapertura)
        ->where('responsable.id',$idusuario)
        ->select(
            's_movimiento.*',
            's_caja.nombre as cajanombre',
            's_movimiento.tipomovimiento as conceptomovimientotipo',
            's_movimiento.tipomovimientonombre as conceptomovimientonombre',
            's_moneda.simbolo as monedasimbolo',
            'responsable.nombre as responsablenombre'
        )
        ->orderBy('s_movimiento.id','desc')
        ->get();
  
    $tabla = [];
  
    foreach($s_movimientos as $value){
        $opcion = [];
        $estadomovimiento = '';
        $style = '';
        if($value->idestadomovimiento==1){
            /*$opcion[] = [
                'nombre'  => 'Editar',
                'onclick' => '/'.$idtienda.'/movimiento/'.$value->id.'/edit?view=editar',
                'icono'   => 'edit'
            ];
            $opcion[] = [
                'nombre'  => 'Eliminar',
                'onclick' => '/'.$idtienda.'/movimiento/'.$value->id.'/edit?view=eliminar',
                'icono'   => 'trash'
            ];*/
            $estadomovimiento = 'PENDIENTE';
        }elseif($value->idestadomovimiento==2){
            $opcion[] = [
                'nombre'  => 'Ticket',
                'onclick' => '/'.$idtienda.'/movimiento/'.$value->id.'/edit?view=ticket',
                'icono'   => 'file-text'
            ];
            //if($idapertura==$value->s_idaperturacierre){
                $opcion[] = [
                    'nombre'  => 'Anular',
                    'onclick' => '/'.$idtienda.'/movimiento/'.$value->id.'/edit?view=anular',
                    'icono'   => 'ban'
                ];
                //$style = 'table-mark-aperturado';
            //}
            $estadomovimiento = 'CONFIRMADO';
        }elseif($value->idestadomovimiento==3){
            $estadomovimiento = 'ANULADO';
            $style = 'table-mark-anulado';
        }

        $tabla[] = [
            'id' => $value->id,
            'style' => $style,
            'codigo' => str_pad($value->codigo, 8, "0", STR_PAD_LEFT),
            'conceptomovimientotipo' => $value->conceptomovimientotipo,
            'conceptomovimientonombre' => $value->conceptomovimientonombre,
            'concepto' => $value->concepto,
            'monto' => $value->monedasimbolo.' '.$value->monto,
            'fecharegistro' => date_format(date_create($value->fecharegistro),"d/m/Y h:i A"),
            'estadomovimiento' => $estadomovimiento,
            'opcion' => $opcion
        ];
    }

    json_create($idtienda,'movimiento_'.$idsucursal.'_'.$idusuario,$tabla);
}
function json_compra($idtienda, $idsucursal, $idusuario){
    $idapertura = sistema_apertura([
        'idtienda'          => $idtienda,
        'idsucursal'        => $idsucursal,
        'idusersrecepcion'  => $idusuario,
    ])['idapertura'];

    $compras = DB::table('s_compra')
              ->join('users','users.id','s_compra.s_idusuarioproveedor')
              ->join('s_tipocomprobante','s_tipocomprobante.id','s_compra.s_idcomprobante')
              ->join('users as responsable','responsable.id','s_compra.s_idusuarioresponsable')
              ->join('s_moneda','s_moneda.id','s_compra.s_idmoneda')
              ->where('s_compra.idtienda',$idtienda)
              ->where('s_compra.idsucursal',$idsucursal)
              ->where('s_compra.s_idusuarioresponsable',$idusuario)
             // ->where('s_compra.s_idaperturacierre',$idapertura)
              ->select(
                  's_compra.*',
                  'users.nombre as nombreProveedor',
                  'users.nombrecompleto as apellidoProveedor',
                  's_tipocomprobante.nombre as nombreComprobante',
                  'responsable.nombre as responsablenombre',
                  's_moneda.codigo as monedacodigo',
              )
              ->orderBy('s_compra.id','desc')
              ->get();

      $tabla = [];
      foreach($compras as $value){
          $estado = '';
          if($value->s_idestado==1){
              $estado = 'PENDIENTE';
          }elseif($value->s_idestado==2){
              $estado = 'COMPRADO';
          }elseif($value->s_idestado==3){
              $estado = 'ANULADO';
          }

          if($value->totalredondeado == 0){
            $mon_total = DB::table('s_compradetalle')->where('s_idcompra',$value->id)->sum('preciototal');
            $total = number_format($mon_total, 2, '.', '');
          }else{
            $total = $value->totalredondeado;
          }

          $fecharegistro = $value->fecharegistro != '' ? date_format(date_create($value->fecharegistro),"d/m/Y h:i:s A") : '---';
          $tabla[] = [
              'id' => $value->id,
              'codigo' => str_pad($value->codigo, 8, "0", STR_PAD_LEFT),
              'comprobante' => $value->nombreComprobante,
              'correlativo' => $value->seriecorrelativo,
              'moneda' => $value->monedacodigo,
              'total' => $total,
              'proveedor' => $value->nombreProveedor,
              'fecha_registro' => $fecharegistro,
              'responsable' => $value->responsablenombre,
              'estado' => $estado,
              'opcion'  => [
                 [
                  'nombre' => 'Editar',
                  'onclick' => '/'.$idtienda.'/compra/'.$value->id.'/edit?view=editar',
                  'icono' => 'edit',
                ],
                [
                  'nombre' => 'Detalle',
                  'onclick' => '/'.$idtienda.'/compra/'.$value->id.'/edit?view=detalle',
                  'icono' => 'circle-info',
                ],
                [
                  'nombre' => 'Eliminar',
                  'onclick' => '/'.$idtienda.'/compra/'.$value->id.'/edit?view=eliminar',
                  'icono' => 'trash',
                ],
                [
                  'nombre' => 'Comprobante',
                  'onclick' => '/'.$idtienda.'/compra/'.$value->id.'/edit?view=ticket',
                  'icono' => 'invoice',
                ]
              ],
          ];
      }
  
      $sucursal = "compra_{$idsucursal}_{$idusuario}";

      json_create($idtienda, $sucursal, $tabla);
}
function json_compradevolucion($idtienda, $idsucursal, $idusuario){
        $idapertura = sistema_apertura([
            'idtienda'          => $idtienda,
            'idsucursal'        => $idsucursal,
            'idusersrecepcion'  => $idusuario,
        ])['idapertura'];
  
        $compradevolucion = DB::table('s_compradevolucion')
          ->join('s_compra','s_compra.id','s_compradevolucion.idcompra')
          ->join('users as proveedor','proveedor.id','s_compra.s_idusuarioproveedor')
          ->join('users as responsable','responsable.id','s_compradevolucion.idusuarioresponsable')
          ->join('s_tipocomprobante as comprobante','comprobante.id','s_compra.s_idcomprobante')
          ->where('s_compradevolucion.idtienda',$idtienda)
          ->where('s_compradevolucion.idsucursal',$idsucursal)
          ->where('s_compradevolucion.idusuarioresponsable',$idusuario)
          ->select(
                's_compradevolucion.*',
                'proveedor.nombre as nombreproveedor',
                'proveedor.apellidos as apellidosproveedor',
                'proveedor.identificacion as identificacionproveedor',
                'responsable.nombre as nombreresponsable',
                'comprobante.nombre as comprobantenombre'
            )
           ->orderBy('s_compradevolucion.id','desc')
           ->get();
  
        $tabla = [];
        foreach($compradevolucion as $value){
 
             $estado = '';
             $opcion = [];
             switch($value->s_idestado){
              case 1:
                    $opcion[] = [
                        'nombre'  => 'Ticket de Anulacion',
                        'onclick' => '/'.$idtienda.'/compradevolucion/'.$value->id.'/edit?view=ticket',
                        'icono'   => 'receipt'
                    ];
                    $estado = 'Pendiente';
                    break;
              case 2:
                    $opcion[] = [
                        'nombre'  => 'Detalle',
                        'onclick' => '/'.$idtienda.'/compradevolucion/'.$value->id.'/edit?view=detalle',
                        'icono'   => 'edit'
                    ];
                    $opcion[] = [
                        'nombre'  => 'Ticket de Anulacion',
                        'onclick' => '/'.$idtienda.'/compradevolucion/'.$value->id.'/edit?view=ticket',
                        'icono'   => 'receipt'
                    ];
                    if ($idapertura==$value->s_idaperturacierre) {
                      $opcion[] = [
                          'nombre'  => 'Anular',
                          'onclick' => '/'.$idtienda.'/compradevolucion/'.$value->id.'/edit?view=anular',
                          'icono'   => 'ban'
                      ];
                    }
                    $estado = 'Confirmado';
                    break;
              case 3:
                    $opcion[] = [
                        'nombre'  => 'Detalle',
                        'onclick' => '/'.$idtienda.'/compradevolucion/'.$value->id.'/edit?view=detalle',
                        'icono'   => 'edit'
                    ];
                    $estado = 'Anulado';
                    break;
            }
          
            $tabla[] = [
                'id' => $value->id,
                'codigo' => str_pad($value->codigo, 8, "0", STR_PAD_LEFT) ,
                'codigoimpresion' => str_pad($value->codigoimpresion, 8, "0", STR_PAD_LEFT) ,
                'totalredondeado' => $value->totalredondeado,
                'fecharegistro' => date_format(date_create($value->fecharegistro),"d/m/Y h:i:s A"),
                'nombreresponsable' => $value->nombreresponsable,
                'comprobantenombre' => $value->comprobantenombre,
                'identificacionproveedor' =>  $value->identificacionproveedor,
                'proveedor' => $value->apellidosproveedor.' '.$value->nombreproveedor,
                'motivo' => $value->motivo,
                'estado' => $estado,
                'opcion' => $opcion
            ];
        }

        json_create($idtienda,'compradevolucion_'.$idsucursal.'_'.$idusuario,$tabla);
}
function json_venta($idtienda,$idsucursal,$idusuario){
  
    $s_ventas = DB::table('s_venta')
        ->join('s_moneda','s_moneda.id','s_venta.s_idmoneda')
        ->join('s_formapago','s_formapago.id','s_venta.s_idformapago')
        ->join('s_tipocomprobante','s_tipocomprobante.id','s_venta.s_idcomprobante')
        ->join('users as cliente','cliente.id','s_venta.s_idusuariocliente')
        ->join('users as responsableregistro','responsableregistro.id','s_venta.s_idusuarioresponsableregistro')
        ->join('users as responsable','responsable.id','s_venta.s_idusuarioresponsable')
        ->where('s_venta.idtienda',$idtienda)
        ->where('s_venta.idsucursal',$idsucursal)
        ->whereIn('s_venta.s_idestado',[3,4])
        ->where('s_venta.s_idusuarioresponsable',$idusuario)
        ->select(
            's_venta.*',
            's_tipocomprobante.nombre as nombreComprobante',
            's_formapago.nombre as nombreformapago',
            DB::raw('IF(cliente.idtipopersona=1,
            CONCAT(cliente.nombrecompleto),
            CONCAT(cliente.nombrecompleto)) as cliente'),
            'responsable.nombre as responsablenombre',
            'responsableregistro.nombre as responsableregistronombre',
            's_moneda.simbolo as monedasimbolo'
        )
        ->orderBy('s_venta.codigo','desc')
        ->get();
      
        $tabla = [];
    foreach($s_ventas as $value){
        $comprobante = DB::table('s_facturacionboletafactura')
                        ->where('idventa',$value->id)
                        ->limit(1)
                        ->first();
        $opcion = [];
        $style = '';
        $estadoventa = '';
        $serie_numero_comprobante = '';
        if($value->s_idestado==1){
            $opcion[] = [
                'nombre'  => 'Ticket de Venta',
                'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=ticketventa',
                'icono'   => 'edit'
            ];
            $opcion[] = [
                'nombre'  => 'Eliminar',
                'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=eliminar',
                'icono'   => 'trash'
            ];
            $estadoventa = 'PENDIENTE';
        }elseif($value->s_idestado==2){
            $opcion[] = [
                'nombre'  => 'Confirmar',
                'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=confirmar',
                'icono'   => 'check'
            ];
            $opcion[] = [
                'nombre'  => 'Ticket de Venta',
                'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=ticketventa',
                'icono'   => 'edit'
            ];
            $estadoventa = 'COTIZACIÓN';
        }elseif($value->s_idestado==3){
            $opcion[] = [
                'nombre'  => 'Ticket de Venta',
                'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=ticketventa',
                'icono'   => 'edit'
            ];
            $opcion[] = [
                'nombre'  => 'Anular',
                'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=anular',
                'icono'   => 'ban'
            ];
            if($comprobante){
              $serie_numero_comprobante = $comprobante->venta_serie.'-'.$comprobante->venta_correlativo;
              $opcion[] = [
                'nombre'  => 'Comprobante',

                'onclick' => '/'.$idtienda.'/facturacionboletafactura/'.$comprobante->id.'/edit?view=ticket',
                'icono'   => 'note'
              ];
            }else{
              $opcion[] = [
                'nombre'  => 'Emitir Comprobante',
                'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=facturar',
                'icono'   => 'check'
              ];
            }
            
            $estadoventa = 'VENDIDO';
        }elseif($value->s_idestado==4){
            $opcion[] = [
                'nombre'  => 'Ticket de Venta',
                'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=ticketventa',
                'icono'   => 'edit'
            ];
            $estadoventa = 'ANULADO';
        }
      
        $tabla[] = [
            'id' => $value->id,
            'style' => $style,
            'text' => $value->cliente.' ('.$value->monedasimbolo.' '.$value->total.')',
            'codigo' => str_pad($value->codigo, 8, "0", STR_PAD_LEFT),
            'comprobante' => $value->nombreComprobante,
            'formapago' => $value->nombreformapago,
            'textformapago' => '<span class="badge bg-success">'.$value->nombreformapago.'</span>',
            'total' => $value->totalredondeado,
            'total_simbolo' => $value->monedasimbolo.' '.$value->totalredondeado,
            'totalefectivo_simbolo' => $value->monedasimbolo.' '.$value->monto_efectivo,
            'totaldeposito_simbolo' => $value->monedasimbolo.' '.$value->monto_deposito,
            'cliente' => $value->cliente,
            'fecharegistro' => date_format(date_create($value->fecharegistro),"d/m/Y h:i A"),
            'fechaventa' => $value->fechaconfirmacion!=''?date_format(date_create($value->fechaconfirmacion),"d/m/Y h:i A"):'',
            'estado' => $estadoventa,
            'cpe' => $serie_numero_comprobante,
            'opcion' => $opcion
        ];
    }
                
    json_create($idtienda,'venta_'.$idsucursal.'_'.$idusuario,$tabla);
}
function json_ventacredito($idtienda,$idsucursal){
  
    $s_ventas = DB::table('s_venta')
        ->join('s_moneda','s_moneda.id','s_venta.s_idmoneda')
        ->join('s_formapago','s_formapago.id','s_venta.s_idformapago')
        ->join('s_formapagodetalle','s_formapagodetalle.s_idventa','s_venta.id')
        ->join('s_tipocomprobante','s_tipocomprobante.id','s_venta.s_idcomprobante')
        ->join('users as cliente','cliente.id','s_venta.s_idusuariocliente')
        ->join('users as responsableregistro','responsableregistro.id','s_venta.s_idusuarioresponsableregistro')
        ->join('users as responsable','responsable.id','s_venta.s_idusuarioresponsable')
        ->where('s_venta.idtienda',$idtienda)
        ->where('s_venta.idsucursal',$idsucursal)
        ->where('s_venta.s_idformapago',2)
        ->whereIn('s_venta.s_idestado',[3,4])
        ->select(
            's_venta.*',
            's_tipocomprobante.nombre as nombreComprobante',
            's_formapago.nombre as nombreformapago',
      
            's_formapagodetalle.formapago_credito_fechainicio as formapago_credito_fechainicio',
            's_formapagodetalle.formapago_credito_ultimafecha as formapago_credito_ultimafecha',
      
            DB::raw('IF(cliente.idtipopersona=1,
            CONCAT(cliente.nombrecompleto),
            CONCAT(cliente.nombrecompleto)) as cliente'),
            'responsable.nombre as responsablenombre',
            'responsableregistro.nombre as responsableregistronombre',
            's_moneda.simbolo as monedasimbolo'
        )
        ->orderBy('s_venta.codigo','desc')
        ->get();
      
    $tabla = [];
    foreach($s_ventas as $value){
        $monto_pagado = DB::table('s_formapagodetalle')
                        ->where('s_formapagodetalle.idventacobranza',$value->id)
                        ->where('s_formapagodetalle.idestado',1)
                        ->sum('s_formapagodetalle.monto');
        $comprobante = DB::table('s_facturacionboletafactura')
                        ->where('idventa',$value->id)
                        ->limit(1)
                        ->first();
        $opcion = [];
        $style = '';
        $estadoventa = '';
        $serie_numero_comprobante = '';
        if($value->s_idestado==1){
            $opcion[] = [
                'nombre'  => 'Ticket de Venta',
                'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=ticketventa',
                'icono'   => 'edit'
            ];
            $opcion[] = [
                'nombre'  => 'Eliminar',
                'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=eliminar',
                'icono'   => 'trash'
            ];
            $estadoventa = 'PENDIENTE';
        }elseif($value->s_idestado==2){
            $opcion[] = [
                'nombre'  => 'Confirmar',
                'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=confirmar',
                'icono'   => 'check'
            ];
            $opcion[] = [
                'nombre'  => 'Ticket de Venta',
                'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=ticketventa',
                'icono'   => 'edit'
            ];
            $estadoventa = 'COTIZACIÓN';
        }elseif($value->s_idestado==3){
            $opcion[] = [
                'nombre'  => 'Ticket de Venta',
                'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=ticketventa',
                'icono'   => 'edit'
            ];
            $opcion[] = [
                'nombre'  => 'Anular',
                'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=anular',
                'icono'   => 'ban'
            ];
            if($comprobante){
              $serie_numero_comprobante = $comprobante->venta_serie.'-'.$comprobante->venta_correlativo;
              $opcion[] = [
                'nombre'  => 'Comprobante',
                // route:'{{ url('backoffice/tienda/nuevosistema/'.$tienda->id.'/facturacionboletafactura') }}/'+resultado['idfacturacionboletafactura']+'/edit?view=ticket',
                'onclick' => '/'.$idtienda.'/facturacionboletafactura/'.$comprobante->id.'/edit?view=ticket',
                'icono'   => 'note'
              ];
            }else{
              $opcion[] = [
                'nombre'  => 'Emitir Comprobante',
                'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=facturar',
                'icono'   => 'check'
              ];
            }
            
            $estadoventa = 'VENDIDO';
        }elseif($value->s_idestado==4){
            $opcion[] = [
                'nombre'  => 'Ticket de Venta',
                'onclick' => '/'.$idtienda.'/venta/'.$value->id.'/edit?view=ticketventa',
                'icono'   => 'edit'
            ];
            $estadoventa = 'ANULADO';
        }
      if($monto_pagado < $value->totalredondeado){
        $tabla[] = [
            'id'                    => $value->id,
            'style'                 => $style,
            'text'                  => $value->codigo.' - '.$value->cliente.' ('.$value->monedasimbolo.' '.$value->total.')',
            'codigo'                => str_pad($value->codigo, 8, "0", STR_PAD_LEFT),
            'comprobante'           => $value->nombreComprobante,
            'formapago'             => $value->nombreformapago,
            'textformapago'         => '<span class="badge bg-success">'.$value->nombreformapago.'</span>',
            'total'                 => $value->totalredondeado,
            'montopagado'           => $monto_pagado,
            'total_simbolo'         => $value->monedasimbolo.' '.$value->totalredondeado,
            'totalefectivo_simbolo' => $value->monedasimbolo.' '.$value->monto_efectivo,
            'totaldeposito_simbolo' => $value->monedasimbolo.' '.$value->monto_deposito,
            'cliente'               => $value->cliente,
            'fecharegistro'         => date_format(date_create($value->fecharegistro),"d/m/Y h:i A"),
            'fechaventa'            => $value->fechaconfirmacion!=''?date_format(date_create($value->fechaconfirmacion),"d/m/Y h:i A"):'',
            'fechafinalpagocredito' => $value->formapago_credito_ultimafecha,
            'estado'                => $estadoventa,
            'cpe'                   => $serie_numero_comprobante,
            'opcion'                => $opcion
        ];
      }
        
    }
                
    json_create($idtienda,'ventacredito_'.$idsucursal,$tabla);
}
function json_cobranzacredito($idtienda, $idsucursal){
    // $s_ventas = DB::table('s_venta')
    //     ->join('s_moneda','s_moneda.id','s_venta.s_idmoneda')
    //     ->join('s_formapago','s_formapago.id','s_venta.s_idformapago')
    //     ->join('s_formapagodetalle','s_formapagodetalle.idventacobranza','s_venta.id')
    //     ->join('s_tipocomprobante','s_tipocomprobante.id','s_venta.s_idcomprobante')
    //     ->join('users as cliente','cliente.id','s_venta.s_idusuariocliente')
    //     ->join('users as responsableregistro','responsableregistro.id','s_venta.s_idusuarioresponsableregistro')
    //     ->join('users as responsable','responsable.id','s_venta.s_idusuarioresponsable')
    //     ->where('s_venta.idtienda',$idtienda)
    //     ->where('s_venta.idsucursal',$idsucursal)
    //     ->where('s_venta.s_idformapago',2)
    //     ->whereIn('s_venta.s_idestado',[3,4])
    //     ->select(
    //         's_venta.*',
    //         's_tipocomprobante.nombre as nombreComprobante',
    //         's_formapago.nombre as nombreformapago',
      
    //         's_formapagodetalle.id as idformapagodetalle',
    //         's_formapagodetalle.monto as montopagocredito',
    //         's_formapagodetalle.fecharegistro as formapago_fecharegistro',
    //         's_formapagodetalle.formapago_credito_fechainicio as formapago_credito_fechainicio',
    //         's_formapagodetalle.formapago_credito_ultimafecha as formapago_credito_ultimafecha',
    //         's_formapagodetalle.idestado as formapago_estado',
      
    //         DB::raw('IF(cliente.idtipopersona=1,
    //         CONCAT(cliente.nombrecompleto),
    //         CONCAT(cliente.nombrecompleto)) as cliente'),
    //         'responsable.nombre as responsablenombre',
    //         'responsableregistro.nombre as responsableregistronombre',
    //         's_moneda.simbolo as monedasimbolo'
    //     )
    //     ->orderBy('s_formapagodetalle.id','desc')
    //     ->get();
      
    $forma_pago_detalle = DB::table('s_formapagodetalle')
                            ->select(
                                's_formapagodetalle.id as idformapagodetalle',
                                's_formapagodetalle.monto as montopagocredito',
                                's_formapagodetalle.fecharegistro as formapago_fecharegistro',
                                's_formapagodetalle.formapago_credito_fechainicio as formapago_credito_fechainicio',
                                's_formapagodetalle.formapago_credito_ultimafecha as formapago_credito_ultimafecha',
                                's_formapagodetalle.idestado as formapago_estado',
                            )
                            ->get();
                            dd($forma_pago_detalle);
    $tabla = [];
    foreach($forma_pago_detalle as $value){
      
        $opcion = [];
        $style = '';
        $estadoventa = '';
        $opcion[] = [
                'nombre'  => 'Ticket de Cobranza',
                'onclick' => '/'.$idtienda.'/cobranzacredito/'.$value->idformapagodetalle.'/edit?view=ticketcobranza',
                'icono'   => 'invoice'
            ];
        
        $estado = 'ANULADO';
        if( $value->formapago_estado == 1 ){
          $estado = 'ACEPTADO';
          $opcion[] = [
                'nombre'  => 'Anular',
                'onclick' => '/'.$idtienda.'/cobranzacredito/'.$value->idformapagodetalle.'/edit?view=anular',
                'icono'   => 'remove'
            ];
        }
        
        $tabla[] = [
            'id'                    => $value->id,
            // 'text'                  => $value->codigo.' - '.$value->cliente.' ('.$value->monedasimbolo.' '.$value->total.')',
            // 'codigo'                => str_pad($value->codigo, 8, "0", STR_PAD_LEFT),
            'montopagado'           => $value->montopagocredito,
            // 'cliente'               => $value->cliente,
            'fecharegistro'         => date_format(date_create($value->formapago_fecharegistro),"d/m/Y h:i A"),
            'estado'                => $estado,
            'opcion'                => $opcion
        ];
        
    }
                
    json_create($idtienda,'cobranzacredito_'.$idsucursal,$tabla);
}
function json_ventadevolucion($idtienda,$idsucursal,$idusuario){
        $ventadevolucion = DB::table('s_ventadevolucion')
            ->join('s_venta','s_venta.id','s_ventadevolucion.idventa')
            ->join('users as cliente','cliente.id','s_venta.s_idusuariocliente')
            ->join('s_moneda as moneda','moneda.id','s_venta.s_idmoneda')
            ->join('users as responsable','responsable.id','s_ventadevolucion.idusuarioresponsable')
            ->join('s_tipoentrega as entrega','entrega.id','s_venta.s_idtipoentrega')
            ->join('s_tipocomprobante as comprobante','comprobante.id','s_venta.s_idcomprobante')
            ->where('s_ventadevolucion.idtienda',$idtienda)
            ->where('s_ventadevolucion.idsucursal',$idsucursal)
            ->where('s_ventadevolucion.idusuarioresponsable',$idusuario)
            ->select(
                    's_ventadevolucion.*',
                    'cliente.nombre as nombrecliente',
                    'cliente.nombrecompleto as apellidoscliente',
                    'cliente.identificacion as identificacioncliente',
                    'moneda.nombre as nombremoneda',
                    'responsable.nombre as nombreresponsable',
                    'entrega.nombre as entreganombre',
                    'comprobante.nombre as comprobantenombre'
                )
            ->orderBy('s_ventadevolucion.id','desc')
            ->get();
       
        $tabla = [];
       foreach($ventadevolucion as $value){   
            $estado = '';
            $opcion = [];
            switch($value->idestado){
              case 1:
                    $opcion[] = [
                        'nombre'  => 'Ticket de Venta',
                        'onclick' => '/'.$idtienda.'/ventadevolucion/'.$value->id.'/edit?view=ticket',
                        'icono'   => 'receipt'
                    ];
                    $estado = 'Pendiente';
                    break;
              case 2:
                    $opcion[] = [
                        'nombre'  => 'Detalle',
                        'onclick' => '/'.$idtienda.'/ventadevolucion/'.$value->id.'/edit?view=detalle',
                        'icono'   => 'edit'
                    ];
                    $opcion[] = [
                        'nombre'  => 'Ticket de Venta',
                        'onclick' => '/'.$idtienda.'/ventadevolucion/'.$value->id.'/edit?view=ticket',
                        'icono'   => 'receipt'
                    ];
                    // if ($idapertura==$value->idaperturacierre) {
                    //     $opcion[] = [
                    //         'nombre'  => 'Anular',
                    //         'onclick' => '/'.$idtienda.'/ventadevolucion/'.$value->id.'/edit?view=anular',
                    //         'icono'   => 'ban'
                    //     ];
                    // }
                    $estado = 'Confirmado';
                    break;
              case 3:
                    $opcion[] = [
                        'nombre'  => 'Detalle',
                        'onclick' => '/'.$idtienda.'/ventadevolucion/'.$value->id.'/edit?view=detalle',
                        'icono'   => 'edit'
                    ];
                    $estado = 'Anulado';
                    break;
            }
            $tabla[] = [
              'id' => $value->id,
              'codigo_venta' => str_pad($value->codigo, 8, "0", STR_PAD_LEFT),
              'codigo_impresion' => str_pad($value->codigoimpresion, 8, "0", STR_PAD_LEFT),
              'total' => $value->total,
              'moneda' => $value->nombremoneda,
              'fecha_registro' => date_format(date_create($value->fecharegistro),"d/m/Y h:i:s A"),
              'responsable' => $value->nombreresponsable,
              'comprobantenombre' => $value->comprobantenombre,
              'identificacioncliente' => $value->identificacioncliente,
              'cliente' => $value->apellidoscliente.' '.$value->nombrecliente,
              'entreganombre' => $value->entreganombre,
              'motivo' => $value->motivo,
              'estado' => $estado,
              'opcion' => $opcion
            ];
       }
       json_create($idtienda,'ventadevolucion_'.$idsucursal.'_'.$idusuario,$tabla);
}
function json_transferenciasaldo($idtienda,$name_modulo){
           $cajas = DB::table('s_transferenciasaldo')
            ->join('s_caja as cajaorigen', 'cajaorigen.id', 's_transferenciasaldo.idcajaorigen')
            ->join('s_caja as cajadestino', 'cajadestino.id', 's_transferenciasaldo.idcajadestino')
            ->join('users as responsableorigen', 'responsableorigen.id', 's_transferenciasaldo.idresponsableorigen')
            ->leftjoin('users as responsabledestino', 'responsabledestino.id', 's_transferenciasaldo.idresponsabledestino')
            ->select(
                's_transferenciasaldo.*',
                'cajaorigen.nombre as cajaorigen_nombre',
                'cajadestino.nombre as cajadestino_nombre',
                'responsableorigen.nombre as responsableorigen_nombre',
                'responsabledestino.nombre as responsabledestino_nombre'
            )
            ->orderBy('s_transferenciasaldo.id', 'desc')
            ->get();
                  
            $tabla = [];
            foreach($cajas as $value){
                   if($value->idestado==1){
                    $estado = '<span><i class="fa fa-sync-alt"></i> Pendiente</span>';
                }elseif($value->idestado==2){
                    $estado = '<span><i class="fa fa-check"></i> Confirmado</span>';
                }elseif($value->idestado==3){
                    $estado = '<span><i class="fa fa-unlink"></i> Anulado</span>';
                }
                $tabla[] = [
                    'id' => $value->id,
                    'titulo' => 'CÓDIGO<br>CAJA ORIGEN<br>CAJA DESTINO<br>MOTIVO<br>',
                    'nombre' => ': '.str_pad($value->codigo, 6, "0", STR_PAD_LEFT).'<br>: '.$value->cajaorigen_nombre."($value->responsableorigen_nombre)".'<br>: '.$value->cajadestino_nombre."($value->responsabledestino_nombre)".'<br>: '.$value->motivo.'<br>',
                    'titulo2' => 'FECHA SOLICITUD<br>FECHA RECEPCIÓN<br>ESTADO<br>',
                    'nombre2' => ': '.date_format(date_create($value->fechasolicitud), "d/m/Y - h:i A" ).'<br>: '.date_format(date_create($value->fecharecepcion), "d/m/Y - h:i A") .'<br>: '.$estado,
                    'option'    => '<div class="option3">
                                        <a href="javascript:;" onclick="table_modal('.$value->id.',\'Editar Transferencia de Saldo\',\'editar\')" class="btn-tabla"><div class="btn-tabla-edit"></div>Editar</a>
                                        <a href="javascript:;" onclick="table_modal('.$value->id.',\'Detalle de Transferencia de Saldo\',\'detalle\')" class="btn-tabla"><div class="btn-tabla-detail"></div>Detalle</a>
                                        <a href="javascript:;" onclick="table_modal('.$value->id.',\'Eliminar Transferencia de Saldo\',\'anular\')" class="btn-tabla"><div class="btn-tabla-delete"></div>Eliminar</a>
                                    </div>',
                ];
            }

             json_create($idtienda,$name_modulo,$tabla);
}
function json_ecommerceportada($idtienda,$name_modulo){  
}
/*function json_facturacionboletafactura($idtienda, $idsucursal){
    $tienda = DB::table('tienda')->whereId($idtienda)->first();   
    $facturacionboletafactura = DB::table('s_facturacionboletafactura')
          ->join('users as responsable','responsable.id','s_facturacionboletafactura.idusuarioresponsable')
          ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacionboletafactura','s_facturacionboletafactura.id')
          ->where('s_facturacionboletafactura.idtienda', $tienda->id)
//           ->where('s_facturacionboletafactura.idusuarioresponsable',$idusuario)
          ->where('s_facturacionboletafactura.idsucursal', $idsucursal)
          ->select(
              's_facturacionboletafactura.*',
              'responsable.nombre as responsablenombre',
              's_facturacionrespuesta.estado as respuestaestado'
          )
          ->selectRaw(
            "CASE 
             WHEN s_facturacionboletafactura.venta_tipodocumento = '01' THEN (SELECT COUNT(id) AS cantidad_documento_comunicacion
              FROM s_facturacioncomunicacionbajadetalle 
              WHERE s_facturacioncomunicacionbajadetalle.idfacturacionboletafactura =  s_facturacionboletafactura.id)
             WHEN s_facturacionboletafactura.venta_tipodocumento = '03' THEN (SELECT COUNT(id) AS cantidad_documento_resumendiario
              FROM s_facturacionresumendiariodetalle 
              WHERE s_facturacionresumendiariodetalle.idfacturacionboletafactura =  s_facturacionboletafactura.id)
             ELSE '0'
             END AS cantidad_anulado"
          )
          ->orderBy('s_facturacionboletafactura.id','desc')
          ->get();
  
      $tabla = [];
      foreach($facturacionboletafactura as $value){
        
          $facturacionnotacredito = DB::table('s_facturacionnotacredito')
                    ->where('s_facturacionnotacredito.idfacturacionboletafactura',$value->id)
                    ->select(
                      's_facturacionnotacredito.notacredito_serie',
                      's_facturacionnotacredito.notacredito_correlativo'
                    )
                    ->get();
        
          $nota_credito_doc = '';
          foreach($facturacionnotacredito as $nc_value){
            $nota_credito_doc .= $nc_value->notacredito_serie.'-'.$nc_value->notacredito_correlativo.'<br>';
          }
        
          $facturacionnotadebito = DB::table('s_facturacionnotadebito')
                    ->where('s_facturacionnotadebito.idfacturacionboletafactura',$value->id)
                    ->select(
                      's_facturacionnotadebito.notadebito_serie',
                      's_facturacionnotadebito.notadebito_correlativo'
                    )
                    ->get();
        
          $nota_debito_doc = '';
          foreach($facturacionnotadebito as $nd_value){
            $nota_debito_doc .= $nd_value->notadebito_serie.'-'.$nd_value->notadebito_correlativo.'<br>';
          }
          $fecha_emi  = date_format(date_create($value->venta_fechaemision), 'd/m/Y h:i:s A');
          $comprobante = '';
          $serie_corre = $value->venta_serie.'-'.str_pad($value->venta_correlativo, 8, "0", STR_PAD_LEFT);
        
          switch($value->venta_tipodocumento){
            case '01':
                $comprobante = 'FACTURA';
               break;
            case '03':
                $comprobante  = 'BOLETA';
                 break;
            default:
              $comprobante  = 'TICKET';
          }
          $estado='';
          $estadoAnulado='';
          $opcionAnular = [];
          $opcionNotaCredito = [
              'nombre' => 'Nota de Crédito',
              'onclick' => '/'.$idtienda.'/facturacionnotacredito/create?view=registrar&idcomprobante='.$value->id.'&modulo=facturacionnotacredito',
              'icono' => 'receipt',
            ];
          $opcionNotaDebito = [
              'nombre' => 'Nota de Débito',
              'onclick' => '/'.$idtienda.'/facturacionnotadebito/create?view=registrar&idcomprobante='.$value->id.'&modulo=facturacionnotadebito',
              'icono' => 'receipt',
            ];
          $opcionGuiaRemision = [
              'nombre'  => 'Guia de remisión',
              'onclick' => '/'.$idtienda.'/facturacionguiaremision/create?view=registrar&idcomprobante='.$value->id.'&modulo=facturacionguiaremision',
              'icono'   => 'receipt',
            ];
          switch($value->respuestaestado){
               case 'ACEPTADA':
                   $estado = 'Aceptada';
                    if ($value->cantidad_anulado > 0) {
                      $estadoAnulado = 'Anulado';
                      $opcionNotaCredito = [];
                      $opcionNotaDebito = [];
                      $opcionGuiaRemision = [];
                    }
              
                   if ($value->venta_tipodocumento == '01' && $value->cantidad_anulado <= 0) {
                      $opcionAnular = [
                        'nombre' => 'Anular',
                        'onclick' => '/'.$idtienda.'/facturacionboletafactura/'.$value->id.'/edit?view=anular_comunicacionbaja',
                        'icono' => 'receipt',
                      ];
                   }
              
                   if ($value->venta_tipodocumento == '03' && $value->cantidad_anulado <= 0) {
                     $opcionAnular = [
                        'nombre' => 'Anular',
                        'onclick' => '/'.$idtienda.'/facturacionboletafactura/'.$value->id.'/edit?view=anular_resumendiario',
                        'icono' => 'receipt',
                      ];
                   }
                    break;
               case 'OBSERVACIONES':
                   $estado  = 'Observaciones';
                   break;
              case 'RECHAZADA':
                   $estado  = 'Rechazada';
                   break;
              case 'EXCEPCION':
                   $estado  = 'Excepción';
                   break;
            default:
                   $estado  = 'No enviado';
          }
        
        $tabla[] = [
          'id'  => $value->id,
          'text'  => $serie_corre.' | '.$value->cliente_numerodocumento.' - '.$value->cliente_razonsocial,
          'fecha_emision' => $fecha_emi,
          'comprobante' => $comprobante,
          'serie_correlativo' => $serie_corre,
          'serie' => $value->venta_serie,
          'correlativo' => str_pad($value->venta_correlativo, 8, "0", STR_PAD_LEFT),
          'moneda' => $value->venta_tipomoneda=='PEN' ? 'PEN' : 'USD',
          'total' => $value->venta_montoimpuestoventa,
          'cliente' => $value->cliente_numerodocumento.' - '.$value->cliente_razonsocial,
          'emisor' => $value->emisor_ruc.' - '.$value->emisor_razonsocial,
          'codigo_venta' => ($value->idventa !="" ? str_pad($value->idventa, 8, "0", STR_PAD_LEFT) : ""),
          'estado_envio' => $estado,
          'estado_anulado' => $estadoAnulado,
          'nota_credito' => $nota_credito_doc,
          'nota_debito' => $nota_debito_doc,
          'opcion' => [
             [
              'nombre' => 'Comprobante',
              'onclick' => '/'.$idtienda.'/facturacionboletafactura/'.$value->id.'/edit?view=ticket',
              'icono' => 'receipt',
            ],
            $opcionAnular,
            $opcionGuiaRemision,
            $opcionNotaCredito,
            $opcionNotaDebito,
          ]
        ];
      }
      
      $nombre_json = "facturacionboletafactura_".$idsucursal;

      json_create($idtienda, $nombre_json, $tabla);
}*/
/*
function json_facturacionnotacredito($idtienda, $idsucursal, $idusuario){
      $tienda = DB::table('tienda')->whereId($idtienda)->first();

      $facturacionnotacredito = DB::table('s_facturacionnotacredito')
          ->leftJoin('users as responsable','responsable.id','s_facturacionnotacredito.idusuarioresponsable')
          ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacionnotacredito','s_facturacionnotacredito.id')
          ->where('s_facturacionnotacredito.idtienda', $tienda->id)
          ->where('s_facturacionnotacredito.idusuarioresponsable',$idusuario)
          ->where('s_facturacionnotacredito.idsucursal', $idsucursal)
          ->select(
              's_facturacionnotacredito.*',
              'responsable.nombre as responsablenombre',
              's_facturacionrespuesta.codigo as respuestacodigo',
              's_facturacionrespuesta.estado as respuestaestado',
          )
          ->selectRaw(
            "CASE 
             WHEN s_facturacionnotacredito.notacredito_tipodocafectado = '01' THEN (SELECT COUNT(id) AS cantidad_documento_comunicacion
              FROM s_facturacioncomunicacionbajadetalle 
              WHERE s_facturacioncomunicacionbajadetalle.idfacturacionnotacredito =  s_facturacionnotacredito.id)
             WHEN s_facturacionnotacredito.notacredito_tipodocafectado = '03' THEN (SELECT COUNT(id) AS cantidad_documento_resumendiario
              FROM s_facturacionresumendiariodetalle 
              WHERE s_facturacionresumendiariodetalle.idfacturacionnotacredito =  s_facturacionnotacredito.id)
             ELSE '0'
             END AS cantidad_anulado"
          )
          ->orderBy('s_facturacionnotacredito.id','desc')
          ->get();
      
      $tabla = [];
      foreach($facturacionnotacredito as $value){
          $fecha_emi  = date_format(date_create($value->notacredito_fechaemision), 'd/m/Y h:i:s A');
          $serie_corre = $value->notacredito_serie.' '.str_pad($value->notacredito_correlativo, 8, "0", STR_PAD_LEFT);
          $moneda = '';

          $estado='';
          $estadoAnulado='';
          $opcionAnular = [];
          switch($value->respuestaestado){
               case 'ACEPTADA':
                  $estado = 'Aceptada';
                  if ($value->cantidad_anulado > 0) {
                      $estadoAnulado = 'Anulado';
                      $estado = 'Anulado';
                      $opcionAnular = [];
                  }
                  if ($value->notacredito_tipodocafectado == '01' && $value->cantidad_anulado <= 0) {
                      $opcionAnular = [
                        'nombre' => 'Anular',
                        'onclick' => '/'.$idtienda.'/facturacionnotacredito/'.$value->id.'/edit?view=anular_comunicacionbaja',
                        'icono' => 'receipt',
                      ];
                   }

                   if ($value->notacredito_tipodocafectado == '03' && $value->cantidad_anulado <= 0) {
                     $opcionAnular = [
                        'nombre' => 'Anular',
                        'onclick' => '/'.$idtienda.'/facturacionnotacredito/'.$value->id.'/edit?view=anular_resumendiario',
                        'icono' => 'receipt',
                      ];
                   }
                   break;
               case 'OBSERVACIONES':
                   $estado  = 'Observaciones';
                   break;
              case 'RECHAZADA':
                   $estado  = 'Rechazada';
                   break;
              case 'EXCEPCION':
                   $estado  = 'Excepción';
                   break;
            default:
                   $estado  = 'No enviado';
          }
        
        
        
  
        $tabla[] = [
          'id'  => $value->id,
          'serie_correlativo' => $serie_corre,
          'total' => $value->notacredito_totalimpuestos,
          'fecha_emision' => $fecha_emi,
          'cliente' => $value->cliente_numerodocumento.' - '.$value->cliente_razonsocial,
          'emisor' => $value->emisor_ruc.' - '.$value->emisor_nombrecomercial,
          'documento_afectado' => $value->notacredito_numerodocumentoafectado,
          'motivo' => $value->notacredito_descripcionmotivo,
          'estado_envio' => $estado,
          'estado_anulado' => $estadoAnulado,
          'opcion' => [
             [
              'nombre' => 'Comprobantes',
               // 'onclick' => '/'.$idtienda.'/facturacionboletafactura/'.$value->id.'/edit?view=ticket',
              'onclick' => '/'.$idtienda.'/facturacionnotacredito/'.$value->id.'/edit?view=ticket',
              'icono' => 'receipt',
            ],
            $opcionAnular
          ],
          
        ];
      }
  
      $nombre_json = "facturacionnotacredito_{$idsucursal}_{$idusuario}";

      json_create($idtienda, $nombre_json, $tabla);
}
function json_facturacionnotadebito($idtienda,$idsucursal, $idusuario){
    $tienda = DB::table('tienda')->whereId($idtienda)->first();         
    $facturacionnotadebito = DB::table('s_facturacionnotadebito')
            ->where('s_facturacionnotadebito.idtienda', $tienda->id)
            ->leftJoin('users as responsable','responsable.id','s_facturacionnotadebito.idusuarioresponsable')
            ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacionnotadebito','s_facturacionnotadebito.id')
            ->select(
                's_facturacionnotadebito.*',
                'responsable.nombre as responsablenombre',
                's_facturacionrespuesta.estado as respuestaestado',
            )
            ->selectRaw(
              "CASE 
               WHEN s_facturacionnotadebito.notadebito_tipodocafectado = '01' THEN (SELECT COUNT(id) AS cantidad_documento_comunicacion
                FROM s_facturacioncomunicacionbajadetalle 
                WHERE s_facturacioncomunicacionbajadetalle.idfacturacionnotadebito =  s_facturacionnotadebito.id)
               WHEN s_facturacionnotadebito.notadebito_tipodocafectado = '03' THEN (SELECT COUNT(id) AS cantidad_documento_resumendiario
                FROM s_facturacionresumendiariodetalle 
                WHERE s_facturacionresumendiariodetalle.idfacturacionnotadebito =  s_facturacionnotadebito.id)
               ELSE '0'
               END AS cantidad_anulado"
            )
            ->orderBy('s_facturacionnotadebito.id','desc')
            ->get();
          $tabla = [];
    foreach($facturacionnotadebito as $value){
        $serie_corre = $value->notadebito_serie.' '.str_pad($value->notadebito_correlativo, 8, "0", STR_PAD_LEFT);
        $moneda =  $value->notadebito_tipomoneda=='PEN' ? 'SOLES' : 'DOLARES';
        $fecha_emi = date_format(date_create($value->notadebito_fechaemision), 'd/m/Y h:i:s A');
    
        $estado='';
        $estadoAnulado='';
        $opcionAnular = [];
        switch($value->respuestaestado){
            case  'ACEPTADA':
            $estado =  '<span><i class="fa fa-check"></i> Aceptada</span>';
            if ($value->cantidad_anulado > 0) {
                    $estadoAnulado = 'Anulado';
                    $estado = 'Anulado';
                $opcionAnular = [];
            }
            if ($value->notadebito_tipodocafectado == '01' && $value->cantidad_anulado <= 0) {
                $opcionAnular = [
                    'nombre' => 'Anular',
                    'onclick' => '/'.$idtienda.'/facturacionnotadebito/'.$value->id.'/edit?view=anular_comunicacionbaja',
                    'icono' => 'receipt',
                ];
                }

                if ($value->notadebito_tipodocafectado == '03' && $value->cantidad_anulado <= 0) {
                $opcionAnular = [
                    'nombre' => 'Anular',
                    'onclick' => '/'.$idtienda.'/facturacionnotadebito/'.$value->id.'/edit?view=anular_resumendiario',
                    'icono' => 'receipt',
                ];
                }
                break;
            case 'OBSERVACIONES':
                $estado = '<span><i class="fas fa-sync-alt"></i> Observaciones</span>';
                break;
            case 'RECHAZADA':
                $estado =  '<span><i class="fas fa-sync-alt"></i> Rechazada</span>';
                break;
            case 'EXCEPCION':
                $estado =  '<span><i class="fa fa-sync-alt"></i> Excepción</span>';
                break;
            default:
                $estado = '<span><i class="fa fa-sync-alt"></i> No enviado</span>';
        }
    
        $tabla[] = [
            'id'                  => $value->id,
            'serie_correlativo'   => $serie_corre,
            'total'               => $value->notadebito_totalimpuestos,
            'fecha_emision'       => $fecha_emi,
            'cliente'             => $value->cliente_numerodocumento.' - '.$value->cliente_razonsocial,
            'emisor'              => $value->emisor_ruc.' - '.$value->emisor_nombrecomercial,
            'documento_afectado'  => $value->notadebito_numerodocumentoafectado,
            'motivo'              => $value->notadebito_descripcionmotivo,
            'estado_envio'        => $estado,
            'opcion'              => [
                                        [
                                        'nombre' => 'Comprobantes',
                                        'onclick' => '/'.$idtienda.'/facturacionnotadebito/'.$value->id.'/edit?view=ticket',
                                        'icono' => 'receipt',
                                        ],
                                        $opcionAnular
            ],
        ];
      }
      $nombre_json = "facturacionnotadebito_{$idsucursal}_{$idusuario}";
      json_create($idtienda, $nombre_json, $tabla);
}
*/

function json_listacpevalido($idtienda, $idsucursal, $idusuario){

    $tienda = DB::table('tienda')->whereId($idtienda)->first();   
    $facturacionboletafactura = DB::table('s_facturacionboletafactura')
          ->join('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacionboletafactura','s_facturacionboletafactura.id')
          ->where('s_facturacionboletafactura.idtienda', $tienda->id)
          ->where('s_facturacionboletafactura.idusuarioresponsable',$idusuario)
          ->where('s_facturacionboletafactura.idsucursal', $idsucursal)
          ->where('s_facturacionrespuesta.estado', 'ACEPTADA')
          ->select(
              's_facturacionboletafactura.*',
              's_facturacionrespuesta.estado as respuestaestado'
          )
          ->orderBy('s_facturacionboletafactura.id','desc')
          ->get();
  
      $tabla = [];
      foreach($facturacionboletafactura as $value){
          $fecha_emi  = date_format(date_create($value->venta_fechaemision), 'd/m/Y h:i:s A');
          $comprobante = '';
          $serie_corre = $value->venta_serie.' '.str_pad($value->venta_correlativo, 8, "0", STR_PAD_LEFT);
        
          switch($value->venta_tipodocumento){
            case '01':
                $comprobante = 'FACTURA';
               break;
            case '03':
                $comprobante  = 'BOLETA';
                 break;
            default:
              $comprobante  = 'TICKET';
          }
          $estado='';
          $estadoAnulado='';
          $opcionAnular = [];
          switch($value->respuestaestado){
               case 'ACEPTADA':
                   $estado = 'Aceptada';
                   
                    break;
               case 'OBSERVACIONES':
                   $estado  = 'Observaciones';
                   break;
              case 'RECHAZADA':
                   $estado  = 'Rechazada';
                   break;
              case 'EXCEPCION':
                   $estado  = 'Excepción';
                   break;
            default:
                   $estado  = 'No enviado';
          }
        
        $tabla[] = [
          'id'  => $value->id,
          'fecha_emision' => $fecha_emi,
          'comprobante' => $comprobante,
          'serie_correlativo' => $serie_corre,
          'serie' => $value->venta_serie,
          'correlativo' => str_pad($value->venta_correlativo, 8, "0", STR_PAD_LEFT),
          'moneda' => $value->venta_tipomoneda=='PEN' ? 'PEN' : 'USD',
          'total' => $value->venta_montoimpuestoventa,
          'cliente' => $value->cliente_numerodocumento.' - '.$value->cliente_razonsocial,
          'emisor' => $value->emisor_ruc.' - '.$value->emisor_nombrecomercial,
          'codigo_venta' => ($value->idventa !="" ? str_pad($value->idventa, 8, "0", STR_PAD_LEFT) : ""),
          'estado_envio' => $estado,
          'estado_anulado' => $estadoAnulado,
          
        ];
      }
      
      $nombre_json = "listacpevalido_{$idsucursal}_{$idusuario}";

      json_create($idtienda, $nombre_json, $tabla);
}
/*
function json_facturacioncomunicacionbaja($idtienda, $idsucursal, $idusuario){
      $tienda = DB::table('tienda')->whereId($idtienda)->first();   
 
      $facturacioncomunicacionbaja = DB::table('s_facturacioncomunicacionbajadetalle')
            ->join('s_facturacioncomunicacionbaja','s_facturacioncomunicacionbaja.id','s_facturacioncomunicacionbajadetalle.idfacturacioncomunicacionbaja')
            ->join('users as responsable','responsable.id','s_facturacioncomunicacionbaja.idusuarioresponsable')
            ->leftJoin('users as cliente','cliente.id','s_facturacioncomunicacionbajadetalle.idusuariocliente')
            ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacioncomunicacionbaja','s_facturacioncomunicacionbaja.id')
            ->where('s_facturacioncomunicacionbaja.idtienda', $tienda->id)
            ->where('s_facturacioncomunicacionbaja.idusuarioresponsable',$idusuario)
            ->where('s_facturacioncomunicacionbaja.idsucursal', $idsucursal)
            ->select(
                's_facturacioncomunicacionbajadetalle.*',
                's_facturacioncomunicacionbaja.comunicacionbaja_fechageneracion as comunicacionbaja_fechageneracion',
                's_facturacioncomunicacionbaja.comunicacionbaja_correlativo as comunicacionbaja_correlativo',
                's_facturacioncomunicacionbaja.comunicacionbaja_fechacomunicacion as comunicacionbaja_fechacomunicacion',
                's_facturacioncomunicacionbaja.emisor_ruc as emisor_ruc',
                's_facturacioncomunicacionbaja.emisor_nombrecomercial as emisor_nombrecomercial',
                'responsable.nombre as responsablenombre',
                'cliente.identificacion as clienteidentificacion',
                DB::raw('IF(cliente.idtipopersona=1,
                CONCAT(cliente.nombrecompleto),
                CONCAT(cliente.nombrecompleto)) as cliente'),
                's_facturacionrespuesta.estado as respuestaestado',
            )
            ->orderBy('s_facturacioncomunicacionbaja.id','desc')
            ->get();
      $tabla = [];
      foreach($facturacioncomunicacionbaja as $value){
        // $comprobante = $value->tipodocumento=='01'?'FACTURA ':($value->tipodocumento=='07'?'NOTA DE CRÉDITO ':'---');
        $comprobante = '';
        switch($value->tipodocumento){
             case  '01':
                $comprobante = 'FACTURA';
                   break;
             case '07':
                  $comprobante = 'NOTA DE CRÉDITO';
                  break;
             case '08':
                 $comprobante =  'NOTA DE DÉBITO';
                   break;
           }
        
        $correlativo=  str_pad($value->comunicacionbaja_correlativo, 8, "0", STR_PAD_LEFT) ;
        $fecha_gene = date_format(date_create($value->comunicacionbaja_fechageneracion), 'd/m/Y h:i:s A');
        $fecha_resu =  date_format(date_create($value->comunicacionbaja_fechacomunicacion), 'd/m/Y h:i:s A');
        $sunat ='';
           switch($value->respuestaestado){
             case  'ACEPTADA':
                $sunat =  'Aceptada';
                   break;
             case 'OBSERVACIONES':
                  $sunat = 'Observaciones';
                  break;
             case 'RECHAZADA':
                 $sunat =  'Rechazada';
                   break;
             case 'EXCEPCION':
                  $sunat =  'Excepción';
                   break;
             default:
                  $sunat = 'No enviado';
           }
        
        $tabla[]=[
            'id'  => $value->id,
            'correlativo' => $correlativo,
            'fecha_generacion' => $fecha_gene,
            'fecha_comunicacion' => $fecha_resu,
            'documento_afectado' => $comprobante.'<br> '.$value->serie.'-'.$value->correlativo,
            'motivo' => $value->descripcionmotivobaja,
            'cliente' => $value->clienteidentificacion.'-'.$value->cliente,
            'emisor' => $value->emisor_ruc.'-'.$value->emisor_nombrecomercial,
            'estado_envio' => $sunat,
            'opcion' => [
               [
                'nombre' => 'Comprobantes',
                'onclick' => '/'.$idtienda.'/facturacioncomunicacionbaja/'.$value->idfacturacioncomunicacionbaja.'/edit?view=ticket',
                'icono' => 'receipt',
              ]
            ],
        ];
      }
  
      $nombre_json = "facturacioncomunicacionbaja_{$idsucursal}_{$idusuario}";

      json_create($idtienda, $nombre_json, $tabla);
}
*/
/*
function json_facturacionguiaremision($idtienda, $idsucursal){
  
      $tienda        = DB::table('tienda')->whereId($idtienda)->first();  
      $guiaremision  = DB::table('s_facturacionguiaremision')
        ->join('users as responsable', 'responsable.id', 's_facturacionguiaremision.idusuarioresponsable')
        ->leftJoin('users as transportista', 'transportista.id', 's_facturacionguiaremision.idusuariochofer')
        ->join('s_sunat_motivotraslado', 's_sunat_motivotraslado.codigo', 's_facturacionguiaremision.envio_modtraslado')
        ->where('s_facturacionguiaremision.idtienda', $tienda->id)
        // ->where('s_facturacionguiaremision.idusuarioresponsable',$idusuario)
        // ->where('s_facturacionguiaremision.idsucursal', $idsucursal)
        ->select(
            's_facturacionguiaremision.*',
            'responsable.nombre as responsablenombre',
            's_sunat_motivotraslado.nombre as motivotrasladonombre',
            DB::raw('IF(transportista.idtipopersona=1,
            CONCAT(transportista.nombrecompleto),
            CONCAT(transportista.nombrecompleto)) as transportista'),
        )
        ->orderBy('s_facturacionguiaremision.id','desc')
        ->get();
  
      $tabla = [];
  
      foreach($guiaremision as $value){
        $serie_corre= $value->despacho_serie.' - '.str_pad($value->despacho_correlativo, 8, "0", STR_PAD_LEFT);
        $fecha_emi = date_format(date_create($value->despacho_fechaemision),"d/m/Y h:i:s A");
        $fecha_tras = date_format(date_create($value->envio_fechatraslado),"d/m/Y");
        
        $tabla[]=[
            'id'  => $value->id,
            'serie' => $value->despacho_serie,
            'correlativo' => $value->despacho_correlativo,
            'fecha_emision' => $fecha_emi,
            'ruc_emisor' => $value->emisor_ruc,
            'razon_social_emisor' => $value->emisor_razonsocial,
            'ruc_destinatario' => $value->despacho_destinatario_numerodocumento,
            'razon_social_destinatario' => $value->despacho_destinatario_razonsocial,
            'descripcion_traslado' => $value->envio_descripciontraslado,
            'transporte_numerodocumento' => $value->transporte_numerodocumento,
            'transporte_razonsocial' => $value->transporte_razonsocial,
            'responsable' => $value->responsablenombre,
            'opcion' => [
               [
                'nombre' => 'Comprobantes',
                'onclick' => '/'.$idtienda.'/facturacionguiaremision/'.$value->id.'/edit?view=ticket',
                'icono' => 'receipt',
              ]
            ],
        ];
      }
  
     $nombre_json = "facturacionguiaremision{$idsucursal}";

     json_create($idtienda, $nombre_json, $tabla);
}
*/
/*
function json_facturacionresumendiario($idtienda, $idsucursal, $idusuario) {
  $tienda = DB::table('tienda')->whereId($idtienda)->first();   
  
  $facturacionresumendiario = DB::table('s_facturacionresumendiariodetalle')
            ->join('s_facturacionresumendiario','s_facturacionresumendiario.id','s_facturacionresumendiariodetalle.idfacturacionresumendiario')
            ->join('users as responsable','responsable.id','s_facturacionresumendiario.idusuarioresponsable')
            ->leftJoin('users as cliente','cliente.id','s_facturacionresumendiariodetalle.idusuariocliente')
            ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacionresumendiario.idfacturacionrespuesta')
            ->where('s_facturacionresumendiario.idtienda', $tienda->id)
            // ->where('s_facturacionresumendiario.idusuarioresponsable',$idusuario)
            ->where('s_facturacionresumendiario.idsucursal', $idsucursal)
            ->select(
                's_facturacionresumendiariodetalle.*',
                's_facturacionresumendiario.id as ids_facturacionresumendiario',
                's_facturacionresumendiario.resumen_fechageneracion as resumen_fechageneracion',
                's_facturacionresumendiario.resumen_correlativo as resumen_correlativo',
                's_facturacionresumendiario.resumen_fecharesumen as resumen_fecharesumen',
                's_facturacionresumendiario.emisor_ruc as emisor_ruc',
                's_facturacionresumendiario.emisor_nombrecomercial as emisor_nombrecomercial',
                's_facturacionresumendiario.emisor_razonsocial as emisor_razonsocial',
                's_facturacionrespuesta.codigo as respuestacodigo',
                'responsable.nombre as responsablenombre',
                DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(cliente.nombrecompleto),
                  CONCAT(cliente.nombrecompleto)) as cliente'),
                's_facturacionrespuesta.estado as respuestaestado',
            )
            ->orderBy('s_facturacionresumendiario.id','desc')
            ->get();
  
    $tabla = [];
  
    foreach ($facturacionresumendiario as $value) {
        $correlativo = date_format(date_create($value->resumen_fecharesumen), 'd-m-Y h:i:s A');
        $fecha_resumen = $value->resumen_fechageneracion;
        // $comprobante_afectado = $value->tipodocumento=='03' ? 'BOLETA' : ($value->tipodocumento=='07'?'NOTA DE CRÉDITO ':'---').' '.$value->serienumero;
        $comprobante_afectado = '';
        switch($value->tipodocumento){
             case  '03':
                $comprobante_afectado = 'FACTURA';
                   break;
             case '07':
                  $comprobante_afectado = 'NOTA DE CRÉDITO';
                  break;
             case '08':
                 $comprobante_afectado =  'NOTA DE DÉBITO';
                   break;
           }
        $estado_envio = '';
      
        if($value->respuestaestado=='ACEPTADA') {
          $estado_envio = 'Aceptada';          
        } elseif ($value->respuestaestado=='OBSERVACIONES') {
          $estado_envio = 'Observaciones';           
        } elseif ($value->respuestaestado=='RECHAZADA') {
          if($value->respuestacodigo=='2223') {
            $estado_envio = 'Aceptada';        
          } else {
            $estado_envio = 'Rechazada';        
          }
        } elseif ($value->respuestaestado=='EXCEPCION') {
          $estado_envio = 'Excepción';          
        } else {
          if($value->respuestacodigo=='0402') {
            $estado_envio = 'Aceptada';    
          } else {
            $estado_envio = 'No enviado';
          }
        }
      
        $tabla[] = [
          'id' => $value->id,
          'correlativo' => $correlativo,
          'fecha_generacion' => $fecha_resumen,
          'comprobante_afectado' => $comprobante_afectado.'<br>'.$value->serienumero,
          'total' => $value->total,
          'estado' => $value->estado==1?'Adicionado':($value->estado==2?'Modificado':($value->estado==3?'Anulado':'---')),
          'cliente' => $value->clientenumero.' - '.$value->cliente,
          'emisor' => $value->emisor_ruc.' - '.$value->emisor_razonsocial,
          'estado_envio' => $estado_envio,
          'opcion' => [
               [
                'nombre' => 'Comprobantes',
                'onclick' => '/'.$idtienda.'/facturacionresumendiario/'.$value->ids_facturacionresumendiario.'/edit?view=ticket',
                'icono' => 'receipt',
              ]
          ],
        ];
    }
  
    $nombre_json = "facturacionresumendiario_{$idsucursal}_{$idusuario}";

    json_create($idtienda, $nombre_json, $tabla);
}
*/
//REPORTES
function json_reporteproductos($idtienda,$name_modulo){
         $producto  = DB::table('s_producto')
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
         
         $marca      = DB::table('s_marca')->where('idtienda',$idtienda)->get();
         
         $categoria  = DB::table('s_categoria')
                ->where('s_categoria.idtienda',$idtienda)
                ->where('s_categoria.s_idcategoria',0)
                ->orderBy('s_categoria.nombre','asc')
                ->get();
         
          $tabla = [];
            foreach($producto as $value){
              
                $codigo = str_pad($value->codigo, 8, "0", STR_PAD_LEFT); 
                $estado='';
              
                 if($value->s_idestado==1){
                    $estado = '<span></i> Activado</span>';
                }elseif($value->s_idestado==2){
                    $estado = '<span><i class="fas fa-sync-alt"></i> Desactivado</span>';
                }
                    
                $tabla[] = [
                    'id' => $value->id,
                    'titulo' => 'CODIGO<br>NOMBRE<br>CATEGORIA<br>MARCA<br>',
                    'nombre' => ': '.$codigo.'<br>: '.$value->nombre.'<br>: '.$value->nombrecategoria.'<br>: '.$value->nombremarca.'<br>',
                    'titulo2' => 'U. MEDIDA<br>PRECIO<br>Estado<br>',
                    'nombre2' => ': '.$value->nombreummedida.'<br>: '.$value->precioalpublico.'<br>: '.$estado,
                    'option'    => '<div class="option3">
                                        <a href="javascript:;" onclick="table_modal('.$value->id.',\'Exportar Excel\',\'excel\')" class="btn-tabla"><div class="btn-tabla-edit"></div>Exportar Excel</a>
                                    </div>',
                ];
            }

             json_create($idtienda,$name_modulo,$tabla);
}
function json_reportecompra($idtienda,$name_modulo){
         $s_compra = DB::table('s_compra')
              ->join('users','users.id','s_compra.s_idusuarioproveedor')
              ->join('users as responsable','responsable.id','s_compra.s_idusuarioresponsable')
              ->join('s_tipocomprobante','s_tipocomprobante.id','s_compra.s_idcomprobante')
              ->where('s_compra.idtienda',$idtienda)
              ->select(
                  's_compra.*',
                  'users.nombre as nombreProveedor',
                  'users.nombrecompleto as apellidoProveedor',
                  's_tipocomprobante.nombre as nombreComprobante',
                  'responsable.nombre as responsablenombre'
              )
              ->orderBy('s_compra.id','desc')
              ->get();

              $tabla =[];
            foreach($s_compra as $value){
              
               $codigo = str_pad($value->codigo, 8, "0", STR_PAD_LEFT); 
               $estado='';
               $total = 0;
              
               $fecha_reg = date_format(date_create($value->fecharegistro), 'd/m/Y h:i:s A');
              
               if($value->totalredondeado==0){ 
                    $montototal = DB::table('s_compradetalle')->where('s_idcompra',$value->id)->sum('preciototal'); 
                    $total = number_format($montototal, 2, '.', '') ;
               }else{ 
                   $total = $value->totalredondeado;
               }
              
                if($value->s_idestado==1){
                    $estado = '<span><i class="fa fa-check"></i> Comprado</span>';
                }elseif($value->s_idestado==2){
                    $estado = '<span><i class="fas fa-sync-alt"></i> Pendiente</span>';
                }
                    
                $tabla[] = [
                    'id' => $value->id,
                    'titulo' => 'CODIGO<br>COMPROBANTE<br>CORRELATIVO<br>TOTAL<br>',
                    'nombre' => ': '.$codigo.'<br>: '.$value->nombreComprobante.'<br>: '.$value->seriecorrelativo.'<br>: '.$total.'<br>',
                    'titulo2' => 'PROVEEDOR<br>FECHA REGISTRO<br>RESPONSABLE<br>ESTADO<br>',
                    'nombre2' => ': '.$value->apellidoProveedor.'<br>: '.$fecha_reg.'<br>: '.$value->responsablenombre.'<br>: '.$estado,
                    'option'    => '<div class="option3">
                                        <a style="width:100px" href="javascript:;" onclick="table_modal('.$value->id.',\'Exportar Excel\',\'excel\')" class="btn-tabla"><div class="btn-tabla-edit"></div>Exportar Excel</a>
                                    </div>',
                ];
            }

             json_create($idtienda,$name_modulo,$tabla);
}
function json_reportecompraproducto($idtienda,$name_modulo){
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
  
           $s_compradetalle  =   DB::table('s_compradetalle')
            ->join('s_compra as compra','compra.id','s_compradetalle.s_idcompra')
            ->join('s_producto as producto','producto.id','s_compradetalle.s_idproducto')
            ->join('users as responsable','responsable.id','compra.s_idusuarioresponsable')
            ->join('users as proveedor','proveedor.id','compra.s_idusuarioproveedor')
            ->join('s_tipocomprobante','s_tipocomprobante.id','compra.s_idcomprobante')
            ->select(
                's_compradetalle.*',
                'compra.codigo as codigocompra',
                'compra.fechaconfirmacion as fechacompra',
                'compra.seriecorrelativo as seriecorrelativo',
                'producto.nombre as nombreproducto',
                'producto.codigo as codigo',
                'responsable.nombre as nombreresponsable',
                'responsable.apellidos as apellidosresponsable',
                'proveedor.nombre as nombreproveedor',
                'proveedor.apellidos as apellidoproveedor',
                's_tipocomprobante.nombre as nombreComprobante'
            )
            ->orderBy('compra.id','desc')
            ->get();
          
          $comprobante    = DB::table('s_tipocomprobante')->get();
      
              $tabla =[];
            foreach($s_compradetalle as $value){
              
                $codigo = str_pad($value->codigocompra, 8, "0", STR_PAD_LEFT); 
                $producto = str_pad($value->codigo, 13, "0", STR_PAD_LEFT).'-'.$value->nombreproducto;
                $serie_corre = str_pad($value->seriecorrelativo, 8, "0", STR_PAD_LEFT);
                $fecha_emi = date_format(date_create($value->fechacompra), 'd/m/Y h:i:s A');
              
                    
                $tabla[] = [
                    'id' => $value->id,
                    'titulo' => 'CODIGO<br>PRODUCTO<br>COMPROBANTE<br>SERIE CORRELATIVO<br>P. UNITARIO<br>',
                    'nombre' => ': '.$codigo.'<br>: '.$producto.'<br>: '.$value->nombreComprobante.'<br>: '.$serie_corre.'<br>: '.$value->preciounitario.'<br>',
                    'titulo2' => 'CANT.<br>TOTAL<br>PROVEEDOR<br>FECHA EMISION<br>RESPONSABLE<br>',
                    'nombre2' => ': '.$value->cantidad.'<br>: '.$value->preciototal.'<br>: '.$value->nombreproveedor.'<br>: '.$fecha_emi.'<br>: '. $value->apellidosresponsable.'-'.$value->nombreresponsable,
                    'option'    => '<div class="option3">
                                        <a style="width:100px" href="javascript:;" onclick="table_modal('.$value->id.',\'Exportar Excel\',\'excel\')" class="btn-tabla"><div class="btn-tabla-edit"></div>Exportar Excel</a>
                                    </div>',
                ];
            }

             json_create($idtienda,$name_modulo,$tabla);
}
function json_reporteventa($idtienda,$name_modulo){
       $s_venta = DB::table('s_venta')
              ->join('s_tipocomprobante','s_tipocomprobante.id','s_venta.s_idcomprobante')
              ->join('s_tipoentrega','s_tipoentrega.id','s_venta.s_idtipoentrega')
              ->join('users as responsable','responsable.id','s_venta.s_idusuarioresponsable')
              ->join('users as responsableregistro','responsableregistro.id','s_venta.s_idusuarioresponsableregistro')
              ->join('users as cliente','cliente.id','s_venta.s_idusuariocliente')
              ->where('s_venta.idtienda',$idtienda)
              ->select(
                  's_venta.*',
                  's_tipocomprobante.nombre as nombreComprobante',
                  's_tipoentrega.nombre as tipoentreganombre',
                  'responsable.nombre as responsablenombre',
                   'responsableregistro.nombre as responsableregistronombre',
                  'cliente.nombre as clientenombre'
              )
              ->orderBy('s_venta.id','desc')
              ->get();
      
           $tabla =[];
            foreach($s_venta as $value){
              
               $codigo = str_pad($value->codigo, 8, "0", STR_PAD_LEFT); 
               $estado='';
               $total = 0;
              
               $fecha_reg = ($value->s_idestado==2 or $value->s_idestado==3 or $value->s_idestado==4) ? date_format(date_create($value->fecharegistro),"d/m/Y h:i:s A") : '---';
               $fecha_vend  = ($value->s_idestado==3 or $value->s_idestado==4) ? date_format(date_create($value->fechaconfirmacion),"d/m/Y h:i:s A") : '---';
               if($value->totalredondeado==0){ 
                    $total = number_format($value->montoredondeado, 2, '.', '');
               }else{ 
                   $total = $value->totalredondeado;
               }
              
                if($value->s_idestado==1){
                    $estado = '<span><i class="fa fa-check"></i> Comprado</span>';
                }elseif($value->s_idestado==2){
                    $estado = '<span><i class="fas fa-sync-alt"></i> Pendiente</span>';
                }
              switch($value->s_idestado){
                  case 1:
                      $estado = '<span><i class="fas fa-sync-alt"></i> Pendiente</span>';
                       break;
                  case 2:
                        $estado = '<span><i class="fas fa-sync-alt"></i> Confirmado</span>';
                        break;
                   case 3:
                      $estado = '<span><i class="fa fa-check"></i> Vendido</span>';
                       break;
                   case 4:
                      $estado = '<span><i class="fa fa-ban"></i> Anulado</span>';
                        break;
              }
                  
                $tabla[] = [
                    'id' => $value->id,
                    'titulo' => 'CODIGO<br>COMPROBANTE<br>TOTAL<br>TIPO ENTREGA<br>CLIENTE<br>',
                    'nombre' => ': '.$codigo.'<br>: '.$value->nombreComprobante.'<br>: '.$total.'<br>'.$value->tipoentreganombre.'<br>: '.$value->clientenombre.'<br>',
                    'titulo2' => 'FECHA REGISTRO<br>FECHA VENDIDA<br>VENDEDOR<br>CAJA<br>ESTADO<br>',
                    'nombre2' => ': '.$fecha_reg.'<br>: '.$fecha_vend.'<br>: '.$value->responsableregistronombre.'<br>: '.$value->responsablenombre.'<br>: '.$estado,
                    'option'    => '<div class="option3">
                                        <a style="width:100px" href="javascript:;" onclick="table_modal('.$value->id.',\'Exportar Excel\',\'excel\')" class="btn-tabla"><div class="btn-tabla-edit"></div>Exportar Excel</a>
                                    </div>',
                ];
            }

             json_create($idtienda,$name_modulo,$tabla);
}
function json_reportefacturacionnotacredito($idtienda,$name_modulo){
        $facturacionnotacredito = DB::table('s_facturacionnotacredito')
            ->join('s_moneda','s_moneda.codigo','s_facturacionnotacredito.notacredito_tipomoneda')
            ->join('users as responsable','responsable.id','s_facturacionnotacredito.idusuarioresponsable')
            ->join('users as cliente','cliente.id','s_facturacionnotacredito.idusuariocliente')
            ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacionnotacredito','s_facturacionnotacredito.id')
            ->where('s_facturacionnotacredito.idtienda',$idtienda)
            ->select(
                's_facturacionnotacredito.*',
                's_facturacionrespuesta.estado as respuestaestado',
                'responsable.nombre as responsablenombre',
                'responsable.apellidos as responsableapellido'
            )
            ->orderBy('s_facturacionnotacredito.id','desc')
            ->get();
        
           $tabla =[];
            foreach($facturacionnotacredito as $value){
              
               $moneda = $value->notacredito_tipomoneda=='PEN' ? 'SOLES' : 'DOLARES';
               $serie_corre= $value->notacredito_serie.'-'.str_pad($value->notacredito_correlativo, 8, "0", STR_PAD_LEFT);
               $sunat = '';
               $fecha_emi = date_format(date_create($value->notacredito_fechaemision), 'd/m/Y h:i:s A');
              
              switch($value->respuestaestado){
                  case 'ACEPTADA':
                      $sunat = '<span><i class="fas fa-check"></i>Aceptada</span>';
                       break;
                  case 'OBSERVACIONES':
                        $sunat = '<span><i class="fas fa-sync-alt"></i> Observaciones</span>';
                        break;
                   case 'RECHAZADA':
                      $sunat = '<span><i class="fa fa-sync-alt"></i> Rechazada</span>';
                       break;
                   case 'EXCEPCION':
                      $sunat = '<span><i class="fa fa-sync-alt"></i> Excepción</span>';
                        break;
                default:
                      $sunat = '<span><i class="fa fa-sync-alt"></i> No enviado</span>';
              }
                $tabla[] = [
                    'id' => $value->id,
                    'titulo' => 'SERIE-CORRELATIVO<br>BASE IMP.<br>IGV<br>TOTAL<br>MONEDA<br>FECHA EMISION<br>DNI/RUC<br>',
                    'nombre' => ': '.$serie_corre.'<br>: '.$value->notacredito_valorventa.'<br>: '.$value->notacredito_totalimpuestos.'<br>'.$value->notacredito_montoimpuestoventa.'<br>: '.$moneda.'<br>: '.$fecha_emi.'<br>: '.$value->cliente_numerodocumento.'<br>',
                    'titulo2' => 'CLIENTE<br>RUC<br>EMISOR<br>RESPONSABLE<br>MODIFICADO<br>MOTIVO<br>SUNAT',
                    'nombre2' => ': '.$value->cliente_razonsocial.'<br>: '.$value->emisor_ruc.'<br>: '.$value->emisor_nombrecomercial.'<br>: '.$value->responsablenombre.'<br>: '.$value->notacredito_numerodocumentoafectado.'<br>: '.$value->notacredito_descripcionmotivo.'<br>: '.$sunat,
                    'option'    => '<div class="option3">
                                       <a style="width:100px" href="javascript:;" onclick="table_modal('.$value->id.',\'Exportar Excel\',\'excel\')" class="btn-tabla"><div class="btn-tabla-edit"></div>Exportar Excel</a>
                                        <a style="width:100px" href="javascript:;" onclick="table_modal('.$value->id.',\'Exportar SUNAT\',\'sunat\')" class="btn-tabla"><div class="btn-tabla-edit"></div>Exportar Excel SUNAT</a>

                                    </div>',
                ];
            }

             json_create($idtienda,$name_modulo,$tabla);
}
function json_reportefacturacionnotadebito($idtienda,$name_modulo){
        $facturacionnotadebito = DB::table('s_facturacionnotadebito')
            ->join('s_moneda','s_moneda.codigo','s_facturacionnotadebito.notadebito_tipomoneda')
            ->join('users as responsable','responsable.id','s_facturacionnotadebito.idusuarioresponsable')
            ->join('users as cliente','cliente.id','s_facturacionnotadebito.idusuariocliente')
           ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacionnotadebito','s_facturacionnotadebito.id')
            ->where('s_facturacionnotadebito.idtienda',$idtienda)
            ->select(
                's_facturacionnotadebito.*',
                'responsable.nombre as responsablenombre',
                'responsable.apellidos as responsableapellido',
                'cliente.nombre as clientenombre',
                'cliente.nombrecompleto as clienteapellido',
          's_facturacionrespuesta.estado as respuestaestado',
                
            )
            ->orderBy('s_facturacionnotadebito.id','desc')
            ->get();
           $tabla =[];
            foreach($facturacionnotadebito as $value){
              
               $moneda = $value->notadebito_tipomoneda=='PEN' ? 'SOLES' : 'DOLARES';
               $serie_corre = $value->notadebito_serie.'-'.str_pad($value->notadebito_correlativo, 8, "0", STR_PAD_LEFT); 
               $sunat = '';
               $fecha_emi = date_format(date_create($value->notadebito_fechaemision), 'd/m/Y h:i:s A');
              switch($value->respuestaestado){
                  case 'ACEPTADA':
                      $sunat = '<span><i class="fas fa-check"></i>Aceptada</span>';
                       break;
                  case 'OBSERVACIONES':
                        $sunat = '<span><i class="fas fa-sync-alt"></i> Observaciones</span>';
                        break;
                   case 'RECHAZADA':
                      $sunat = '<span><i class="fa fa-sync-alt"></i> Rechazada</span>';
                       break;
                   case 'EXCEPCION':
                      $sunat = '<span><i class="fa fa-sync-alt"></i> Excepción</span>';
                        break;
                default:
                      $sunat = '<span><i class="fa fa-sync-alt"></i> No enviado</span>';
              }
                $tabla[] = [
                    'id' => $value->id,
                    'titulo' => 'SERIE-CORRELATIVO<br>BASE IMP.<br>IGV<br>TOTAL<br>MONEDA<br>FECHA EMISION<br>DNI/RUC<br>',
                    'nombre' => ': '.$serie_corre.'<br>: '.$value->notadebito_valorventa.'<br>: '.$value->notadebito_totalimpuestos.'<br>'.$value->notadebito_montoimpuestoventa.'<br>: '.$moneda.'<br>: '.$fecha_emi.'<br>: '.$value->cliente_numerodocumento.'<br>',
                    'titulo2' => 'CLIENTE<br>RUC<br>EMISOR<br>RESPONSABLE<br>MODIFICADO<br>MOTIVO<br>SUNAT',
                    'nombre2' => ': '.$value->clienteapellido.','.$value->clientenombre.'<br>: '.$value->emisor_ruc.'<br>: '.$value->emisor_nombrecomercial.'<br>: '.$value->responsablenombre.'<br>: '.$value->notadebito_numerodocumentoafectado.'<br>: '.$value->notadebito_descripcionmotivo.'<br>: '.$sunat,
                    'option'    => '<div class="option3">
                                       <a style="width:100px" href="javascript:;" onclick="table_modal('.$value->id.',\'Exportar Excel\',\'excel\')" class="btn-tabla"><div class="btn-tabla-edit"></div>Exportar Excel</a>
                                    </div>',
                ];
            }

             json_create($idtienda,$name_modulo,$tabla);
}
function json_reportefacturacionguiaremision($idtienda,$name_modulo){
        $facturacionguiaremision = DB::table('s_facturacionguiaremision')
            ->join('users as responsable', 'responsable.id', 's_facturacionguiaremision.idusuarioresponsable')
            ->join('s_sunat_motivotraslado', 's_sunat_motivotraslado.codigo', 's_facturacionguiaremision.envio_modtraslado')
            ->leftJoin('users as transportista', 'transportista.id', 's_facturacionguiaremision.idusuariochofer')
            ->join('users','users.identificacion','s_facturacionguiaremision.despacho_destinatario_numerodocumento')
            ->join('users as destinatario','destinatario.id','users.id')
            ->where('s_facturacionguiaremision.idtienda',$idtienda)
            ->select(
                's_facturacionguiaremision.*',
                'responsable.nombre as responsablenombre',
                's_sunat_motivotraslado.nombre as motivotrasladonombre',
                 DB::raw('IF(transportista.idtipopersona=1,
                 CONCAT(transportista.apellidos,", ",transportista.nombre),
                 CONCAT(transportista.apellidos)) as transportista'),
                'users.identificacion'
              )
            ->orderBy('s_facturacionguiaremision.id','desc')
            ->get();
         $tabla =[];
            foreach($facturacionguiaremision as $value){
              
               $serie_corre =  $value->despacho_serie.'-'.str_pad($value->despacho_correlativo, 8, "0", STR_PAD_LEFT);
               $fecha_emi = date_format(date_create($value->despacho_fechaemision), 'd/m/Y h:i:s A');
                $tabla[] = [
                    'id' => $value->id,
                    'titulo' => 'SERIE-CORRELATIVO<br>FECHA EMISION<br>RUC/DNI<br>REMITENTE<br>RUC<br> DESTINATARIO<br>',
                    'nombre' => ': '.$serie_corre.'<br>: '.$fecha_emi.'<br>: '.$value->emisor_ruc.'<br>'.$value->emisor_nombrecomercial.'<br>: '.$value->despacho_destinatario_numerodocumento.'<br>: '.$value->despacho_destinatario_razonsocial.'<br>',
                    'titulo2' => 'MOTIVO<br>RUC<br>TRASLADO<br>RUC/DNI<br>TRANSPORTISTA<br>RESPONSABLE<br>',
                    'nombre2' => ': '.$value->motivotrasladonombre.','.$value->envio_fechatraslado.'<br>: '.$value->transporte_choferdocumento.'<br>: '.$value->transportista.'<br>: '.$value->responsablenombre,
                    'option'    => '<div class="option3">
                                       <a style="width:100px" href="javascript:;" onclick="table_modal('.$value->id.',\'Exportar Excel\',\'excel\')" class="btn-tabla"><div class="btn-tabla-edit"></div>Exportar Excel</a>
                                    </div>',
                ];
            }

             json_create($idtienda,$name_modulo,$tabla);
      
}
function json_repotefacturacionboletafactura($idtienda,$name_modulo){
       $facturacionboletafactura = DB::table('s_facturacionboletafactura')
            ->join('s_moneda','s_moneda.codigo','s_facturacionboletafactura.venta_tipomoneda')
            ->join('users as responsable','responsable.id','s_facturacionboletafactura.idusuarioresponsable')
            ->join('users as cliente','cliente.id','s_facturacionboletafactura.idusuariocliente')
            ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacionboletafactura','s_facturacionboletafactura.id')
            ->leftJoin('s_venta as venta','venta.id','s_facturacionboletafactura.idventa')
            ->where('s_facturacionboletafactura.idtienda',$idtienda)
            ->select(
                's_facturacionboletafactura.*',
                's_facturacionrespuesta.estado as respuestaestado',
                'responsable.nombre as responsablenombre',
                'responsable.apellidos as responsableapellido',
                'venta.codigo as ventacodigo'
                
            )
            ->orderBy('s_facturacionboletafactura.id','desc')
            ->get();
      
             $tabla =[];
            foreach($facturacionboletafactura as $value){
              
               $moneda = $value->venta_tipomoneda=='PEN' ? 'SOLES' : 'DOLARES';
               $comprobante = '';
               $serie_corre= $value->venta_serie.'-'.str_pad($value->venta_correlativo, 8, "0", STR_PAD_LEFT);
               $sunat = '';
               $fecha_emi = date_format(date_create($value->venta_fechaemision), 'd/m/Y h:i:s A');
               $cod_venta = $value->ventacodigo!=''?str_pad($value->ventacodigo, 8, "0", STR_PAD_LEFT):'---';
              if($value->venta_tipodocumento=='03'){ 
                      $comprobante=  'BOLETA';
                 }elseif($value->venta_tipodocumento=='01'){ 
                      $comprobante = 'FACTURA';
                 }else{ 
                      $comprobante  = 'TICKET';
                 }
              switch($value->respuestaestado){
                  case 'ACEPTADA':
                      $sunat = '<span><i class="fas fa-check"></i>Aceptada</span>';
                       break;
                  case 'OBSERVACIONES':
                        $sunat = '<span><i class="fas fa-sync-alt"></i> Observaciones</span>';
                        break;
                   case 'RECHAZADA':
                      $sunat = '<span><i class="fa fa-sync-alt"></i> Rechazada</span>';
                       break;
                   case 'EXCEPCION':
                      $sunat = '<span><i class="fa fa-sync-alt"></i> Excepción</span>';
                        break;
                default:
                      $sunat = '<span><i class="fa fa-sync-alt"></i> No enviado</span>';
              }
                $tabla[] = [
                    'id' => $value->id,
                    'titulo' => 'COMPROBANTE<br>SERIE-CORRELATIVO<br>BASE IMP.<br>IGV<br>TOTAL<br>MONEDA<br>FECHA EMISION<br>',
                    'nombre' => ': '.$comprobante.'<br>: '.$serie_corre.'<br>: '.$value->venta_valorventa.'<br>: '.$value->venta_totalimpuestos.'<br>'.$value->venta_montoimpuestoventa.'<br>: '.$moneda.'<br>: '.$fecha_emi.'<br>',
                    'titulo2' => 'DNI/RUC<br>CLIENTE<br>RUC<br>EMISOR<br>RESPONSABLE<br>COD. VENTA<br>SUNAT',
                    'nombre2' => ': '.$value->cliente_numerodocumento.'<br>: '.$value->cliente_razonsocial.'<br>: '.$value->emisor_ruc.'<br>: '.$value->emisor_nombrecomercial.'<br>: '.$value->responsablenombre.'<br>: '.$cod_venta.'<br>: '.$sunat,
                    'option'    => '<div class="option3">
                                       <a style="width:100px" href="javascript:;" onclick="table_modal('.$value->id.',\'Exportar Excel\',\'excel\')" class="btn-tabla"><div class="btn-tabla-edit"></div>Exportar Excel</a>
                                        <a style="width:100px" href="javascript:;" onclick="table_modal('.$value->id.',\'Exportar SUNAT\',\'sunat\')" class="btn-tabla"><div class="btn-tabla-edit"></div>Exportar Excel SUNAT</a>

                                    </div>',
                ];
            }

             json_create($idtienda,$name_modulo,$tabla);
}
