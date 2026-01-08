<?php

namespace App\Http\Controllers\Layouts\Buscador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF; 

class TiendaController extends Controller
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

        /*if(isset($_GET['user'])&&isset($_GET['referencia'])){
            validar_linkpuntoskay($_GET['user'],$_GET['referencia'],$tienda->id);
        }*/
        if($valid_tienda==1){
            // Informaciòn
            $recomendaciones = DB::table('recomendacion')
                  ->where('idtienda',$tienda->id)
                  ->where('idtiporecomendacion',1)
                  ->count();
            $s_categorias = DB::table('s_categoria')
                  ->where('idtienda',$tienda->id)
                  ->where('s_idcategoria',0)
                  ->orderBy('s_categoria.nombre','asc')
                  //->orderBy('orden','asc')
                  ->get();
            if($request->input('pagina')=='informacion'){
                $tiendagalerias = DB::table('tiendagaleria')
                    ->where('idtienda',$tienda->id)
                    ->orderBy('fecharegistro','desc')
                    ->get();
                $tiendavideos = DB::table('tiendavideo')
                    ->where('idtienda', $tienda->id)
                    ->get();
                return view('layouts/buscador/tienda/informacion',[
                    'tienda' => $tienda,
                    'url_link' => $url_link,
                    'recomendaciones' => $recomendaciones,
                    's_categorias' => $s_categorias,
                    'tiendagalerias' => $tiendagalerias,
                    'tiendavideos' => $tiendavideos,
                ]);
            }elseif($request->input('pagina')=='comentario'){
                $tiendacomentarios = DB::table('tiendacomentario')
                  ->join('users','users.id','=','tiendacomentario.idusers')
                  ->where('tiendacomentario.idtienda',$tienda->id)
                  ->select(
                      'tiendacomentario.*',
                      'users.nombre as usersnombre',
                      'users.apellidos as usersapellidos',
                      'users.apellidos as usersapellidos',
                      'users.imagen as usersimagen'
                  )
                  ->orderBy('tiendacomentario.fechaaprobacion','desc')
                  ->get();
                return view('layouts/buscador/tienda/comentario',[
                    'tienda' => $tienda,
                    'url_link' => $url_link,
                    'recomendaciones' => $recomendaciones,
                    'tiendacomentarios' => $tiendacomentarios,
                    's_categorias' => $s_categorias,
                ]);
            }
            elseif($request->input('pagina')=='comprobante'){
                return view('layouts/buscador/tienda/comprobante',[
                    'tienda' => $tienda,
                    'url_link' => $url_link,
                    'recomendaciones' => $recomendaciones,
                    's_categorias' => $s_categorias,
                ]);
            }else{
                $s_productos = DB::table('s_producto')
                        ->join('s_categoria','s_categoria.id','=','s_producto.s_idcategoria1')
                        ->where('s_categoria.idtienda',$tienda->id)
                        ->where('s_producto.s_idestadotiendavirtual',1)
                        ->select('s_producto.*')
                        ->orderBy('s_producto.id','desc')
                        ->limit(6)
                        ->get();
                $s_ecommerceportada = DB::table('s_ecommerceportada')
                  ->where('idtienda',$tienda->id)
                  ->orderBy('orden','asc')
                  ->get();
                return view('layouts/buscador/tienda/index',[
                    'tienda' => $tienda,
                    'url_link' => $url_link,
                    'recomendaciones' => $recomendaciones,
                    's_categorias' => $s_categorias,
                    's_ecommerceportada' => $s_ecommerceportada,
                    's_productos' => $s_productos,
                ]);
            }  
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
    public function create(Request $request,$linktienda,$data0,$data1=0,$data2=0)
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
            $recomendaciones = DB::table('recomendacion')
                ->where('idtienda',$tienda->id)
                ->where('idtiporecomendacion',1)
                ->count();
            $s_categorias = DB::table('s_categoria')
                ->where('idtienda',$tienda->id)
                ->where('s_idcategoria',0)
                ->orderBy('s_categoria.nombre','asc')
                //->orderBy('orden','asc')
                ->get();
            $marcas = DB::table('s_marca')->where('idtienda',$tienda->id)->get();
            //------------ productos
           
            if($data1==0){
                $menucategoria = '';
                if($data0=='searchtienda'){
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
                        ->where($where)
                        ->select('s_producto.*')
                        ->orderBy($orderbyname,$orderbyorder)
                        ->paginate(12);
                    if($request->input('search')!=''){
                        $menucategoria = $menucategoria.' / '.$request->input('search');
                    }else{
                        $menucategoria = $menucategoria.' / Todo los productos';
                    }
                }
                else{
                    $where = [];
                    $where[] = ['s_producto.idtienda',$tienda->id];
                    if($data0!=''){
                        $where[] = ['categoria1.nombre',str_replace('-',' ',$data0)];
                        $menucategoria = $menucategoria.' / '.str_replace('-',' ',ucfirst(mb_strtolower($data0)));
                    }
                    if($data1!=''){
                        $where[] = ['categoria2.nombre',str_replace('-',' ',$data1)];
                        $menucategoria = $menucategoria.' / '.str_replace('-',' ',ucfirst(mb_strtolower($data1)));
                    }
                    if($data2!=''){
                        $where[] = ['categoria3.nombre',str_replace('-',' ',$data2)];
                        $menucategoria = $menucategoria.' / '.str_replace('-',' ',ucfirst(mb_strtolower($data2)));
                    }
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
                        ->join('s_categoria as categoria1','categoria1.id','=','s_producto.s_idcategoria1')
                        ->leftJoin('s_categoria as categoria2','categoria2.id','=','s_producto.s_idcategoria2')
                        ->leftJoin('s_categoria as categoria3','categoria3.id','=','s_producto.s_idcategoria3')
                        ->leftJoin('s_marca','s_marca.id','=','s_producto.s_idmarca')
                        ->where('s_producto.s_idestadotiendavirtual',1)
                        ->where($where)
                        ->select('s_producto.*')
                        ->orderBy($orderbyname,$orderbyorder)
                        ->paginate(12);
                }
                
                return view('layouts/buscador/tienda/createproducto',[
                    'tienda' => $tienda,
                    'url_link' => $url_link,
                    'marcas' => $marcas,
                    'recomendaciones' => $recomendaciones,
                    'menucategoria' => $menucategoria,
                    's_categorias' => $s_categorias,
                    's_productos' => $s_productos
                ]);
            }else{
                //------------ detalle de producto
                if($request->input('view') == 'selectproducto'){
                    $s_producto = DB::table('s_producto')
                      ->where('s_producto.s_idestadotiendavirtual',1)
                      ->whereId($data1)
                      ->first();
                    return view('layouts/buscador/tienda/createproductodetalle',[
                        'tienda' => $tienda,
                        'url_link' => $url_link,
                        'marcas' => $marcas,
                        'recomendaciones' => $recomendaciones,
                        's_categorias' => $s_categorias,
                        's_producto' => $s_producto
                    ]);
                //------------- carrito de compra
                }elseif($request->input('view') == 'selectcarritocompra'){
                    $tipopersonas = DB::table('tipopersona')->get();
                    $comprobantes = DB::table('s_tipocomprobante')->where('id',2)->orWhere('id',3)->get();
                    return view('layouts/buscador/tienda/createcarritocompra',[
                        'tienda' => $tienda,
                        'url_link' => $url_link,
                        'recomendaciones' => $recomendaciones,
                        'tipopersonas' => $tipopersonas,
                        'comprobantes' => $comprobantes
                    ]);
                }else{
                    //return abort(404); 
                    $where = [];
                    $where[] = ['s_producto.idtienda',$tienda->id];
                    $menucategoria = '';
                    if($data0!=''){
                        $where[] = ['categoria1.nombre',$data0];
                        $menucategoria = $menucategoria.' / '.str_replace('-',' ',ucfirst(mb_strtolower($data0)));
                    }
                    if($data1!=''){
                        $where[] = ['categoria2.nombre',$data1];
                        $menucategoria = $menucategoria.' / '.str_replace('-',' ',ucfirst(mb_strtolower($data1)));
                    }
                    if($data2!=''){
                        $where[] = ['categoria3.nombre',$data2];
                        $menucategoria = $menucategoria.' / '.str_replace('-',' ',ucfirst(mb_strtolower($data2)));
                    }
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
                        ->join('s_categoria as categoria1','categoria1.id','=','s_producto.s_idcategoria1')
                        ->leftJoin('s_categoria as categoria2','categoria2.id','=','s_producto.s_idcategoria2')
                        ->leftJoin('s_categoria as categoria3','categoria3.id','=','s_producto.s_idcategoria3')
                        ->leftJoin('s_marca','s_marca.id','=','s_producto.s_idmarca')
                        ->where('s_producto.s_idestadotiendavirtual',1)
                        ->where($where)
                        ->select('s_producto.*')
                        ->orderBy($orderbyname,$orderbyorder)
                        ->paginate(12);
                  
                    return view('layouts/buscador/tienda/createproducto',[
                        'tienda' => $tienda,
                        'url_link' => $url_link,
                        'marcas' => $marcas,
                        'recomendaciones' => $recomendaciones,
                        'menucategoria' => $menucategoria,
                        's_categorias' => $s_categorias,
                        's_productos' => $s_productos
                    ]);
                }
            }
        }else{
            //return redirect('/');
            return abort(404);
        }     
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$linktienda)
    {
        if($request->input('view') == 'registrar'){
            // listar carrito de compra
            $tiendas = DB::table('tienda')
                ->join('s_carritocompra','s_carritocompra.idtienda','=','tienda.id')
                ->where('s_idusuariocliente',Auth::user()->id)
                ->select(
                    'tienda.id as idtienda',
                    'tienda.nombre as tiendanombre'
                )
                ->orderBy('tienda.nombre','asc')
                ->distinct()
                ->get();
          
            if(count($tiendas)==0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Debe ingresar mínimo un producto a su carrito de compra.'
                ]);
            }
          
            $rules = [
                'delivery_idestadoenvio' => 'required',
                'delivery_fecha' => 'required',
                'delivery_hora' => 'required',
                'delivery_personanombre' => 'required',
                'delivery_numerocelular' => 'required',
                'delivery_direccion' => 'required',
                'delivery_mapa_ubicacion_lat' => 'required',
                'delivery_facturacionidentificacion' => 'required|numeric|digits_between:8,11',
                'delivery_facturacioncliente' => 'required',
                'delivery_facturaciondireccion' => 'required',
                'delivery_facturacionidubigeo' => 'required',
            ];
            $messages = [
                'delivery_idestadoenvio.required' => 'El "Estado de envio" es Obligatorio.',
                'delivery_fecha.required' => 'La "Fecha de entrega" es Obligatorio.',
                'delivery_hora.required' => 'La "Hora de entrega" es Obligatorio.',
                'delivery_personanombre.required' => 'El "Nombre de persona a entregar" es Obligatorio.',
                'delivery_numerocelular.required' => 'El "Número de celular de entrega" es Obligatorio.',
                'delivery_direccion.required' => 'La "Dirección de entrega" es Obligatorio.',
                'delivery_facturacionidentificacion.required'   => 'El "DNI/RUC" es Obligatorio.',
                'delivery_facturacionidentificacion.numeric'   => 'El "DNI/RUC" debe ser Númerico.',
                'delivery_facturacionidentificacion.digits_between'   => 'El "DNI/RUC" debe ser entre 8 a 11 Digitos.',
                'delivery_facturacioncliente.required'   => 'El "Cliente" es Obligatorio.',
                'delivery_mapa_ubicacion_lat.required' => 'La "Ubicación de entrega" es Obligatorio.',
                'delivery_facturaciondireccion.required' => 'La "Dirección" es Obligatorio.',
                'delivery_facturacionidubigeo.required' => 'El "Departamento/Provincia/Distrito" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            if(strlen($request->input('delivery_facturacionidentificacion'))==8 or strlen($request->input('delivery_facturacionidentificacion'))==11){
              
            }else{
                return response()->json([
                  'resultado' => 'ERROR',
                  'mensaje' => 'El "DNI/RUC" es incorrecto, ingrese otro por favor.'
                ]);
            }
          
            foreach($tiendas as $value){
              
                $s_carritocompras = DB::table('s_carritocompra')
                    ->join('s_producto','s_producto.id','=','s_carritocompra.s_idproducto')
                    ->where('s_producto.s_idestadotiendavirtual',1)
                    ->where('s_carritocompra.s_idusuariocliente',Auth::user()->id)
                    //->where('s_carritocompra.idtienda',$value->idtienda)
                    ->select(
                          's_carritocompra.*',
                          's_producto.nombre as productonombre'
                    )
                    ->orderBy('s_producto.nombre','asc')
                    ->get();
              
                $valid = 0;
                $resultado = '';
                $mensaje = '';
                foreach($s_carritocompras as $cvalue){
                    if($cvalue->cantidad<=0){
                        $resultado = 'ERROR';
                        $mensaje = 'La cantidad minímo es 1.';
                        $valid = 1;
                        break;
                    }elseif($cvalue->preciounitario<0){
                        $resultado = 'ERROR';
                        $mensaje = 'La Precio minímo es 0.00.';
                        $valid = 1;
                        break;
                    }elseif($cvalue->descuento<0){
                        $resultado = 'ERROR';
                        $mensaje = 'El Descuento minímo es 0.00.';
                        $valid = 1;
                        break;
                    }
                }
                if($valid==1){
                    return response()->json([
                        'resultado' => $resultado,
                        'mensaje'   => $mensaje
                    ]);
                    break; 
                }else{
                    // obtener ultimo código
                    $s_venta = DB::table('s_venta')
                        ->where('s_venta.idtienda',$value->idtienda)
                        ->orderBy('s_venta.codigo','desc')
                        ->limit(1)
                        ->first();
                    $codigo = 1;
                    if($s_venta!=''){
                        $codigo = $s_venta->codigo+1;
                    }
                    // fin obtener ultimo código

                    // registrar pedidos
                    $idventa = DB::table('s_venta')->insertGetId([
                       'codigo' => $codigo,
                       'fecharegistro' => Carbon::now(),
                       'fechapedido' => Carbon::now(),
                       'fechaconfirmacion' => Carbon::now(),
                       'montorecibido' => '0.00',
                       'descuento' => '0.00',
                       'envio' => '0.00',
                       'vuelto' => '0.00',
                       's_idaperturacierre' => 0,
                       's_idusuarioresponsable' => Auth::user()->id,
                       's_idusuariocliente' => Auth::user()->id,
                       's_idagencia' =>  0,
                       's_idcomprobante' =>  1, // ticket
                       's_idtipoentrega' => 2, // delivery
                       's_idestado' => 1,
                       'idtienda' => $value->idtienda,
                    ]);
                  
                    foreach($s_carritocompras as $cvalue){
                        DB::table('s_ventadetalle')->insert([
                          'cantidad' => $cvalue->cantidad,
                          'preciounitario' => $cvalue->preciounitario,
                          'descuento' => '0.00',
                          's_idproducto' => $cvalue->s_idproducto,
                          's_idventa' => $idventa,
                        ]);
                    }

                    // registrar delivery
                    DB::table('s_ventadelivery')->insertGetId([
                       'fecha' => $request->input('delivery_fecha'),
                       'hora' => $request->input('delivery_hora'),
                       'nombre' => $request->input('delivery_personanombre'),
                       'telefono' => $request->input('delivery_numerocelular'),
                       'direccion' => $request->input('delivery_direccion'),
                       'mapa_ubicacion_lat' => $request->input('delivery_mapa_ubicacion_lat'),
                       'mapa_ubicacion_lng' => $request->input('delivery_mapa_ubicacion_lng'),
                       's_idestadoenvio' => $request->input('delivery_idestadoenvio'),
                       's_idventa' => $idventa,
                    ]);
                  
                  
                    // facturacion
                    $cliente =  DB::table('users')->whereId(Auth::user()->id)->first();
                    $ubigeo =  DB::table('ubigeo')->whereId($request->input('delivery_facturacionidubigeo'))->first();
                    DB::table('s_facturacion')->insertGetId([
                       'cliente_identificacion' => $request->input('delivery_facturacionidentificacion'),
                       'cliente_nombre' => $request->input('delivery_facturacioncliente'),
                       'cliente_direccion' => $request->input('delivery_facturaciondireccion'),
                       'cliente_ubigeo' => $ubigeo->nombre,
                       'cliente_ubigeocodigo' => $ubigeo->codigo,
                       's_idventa' => $idventa,
                    ]);
                    //--guardar
                    if($request->input('check-cliente')=='1'){
                        $users = DB::table('s_usuariofacturacion')
                            ->where('s_usuariofacturacion.idusers',Auth::user()->id)
                            ->limit(1)
                            ->first();
                        if($users!=''){
                            DB::table('s_usuariofacturacion')->whereId($users->id)->update([
                               'facturacion_identificacion' => $request->input('delivery_facturacionidentificacion'),
                               'facturacion_nombre' => $request->input('delivery_facturacioncliente'),
                               'facturacion_direccion' => $request->input('delivery_facturaciondireccion'),
                               'facturacion_ubigeo' => $ubigeo->nombre,
                               'facturacion_ubigeocodigo' => $ubigeo->codigo
                            ]);
                        }else{
                            DB::table('s_usuariofacturacion')->insert([
                               'facturacion_identificacion' => $request->input('delivery_facturacionidentificacion'),
                               'facturacion_nombre' => $request->input('delivery_facturacioncliente'),
                               'facturacion_direccion' => $request->input('delivery_facturaciondireccion'),
                               'facturacion_ubigeo' => $ubigeo->nombre,
                               'facturacion_ubigeocodigo' => $ubigeo->codigo,
                               'idusers' => Auth::user()->id,
                            ]);
                        }
                    }
                    // fin facturacion
                }  
            }
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje' => 'Se ha registrado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'consultasunat'){
            $rules = [
               'documento_ruc'           => 'required',
               'tipo_comprobante'        => 'required',
               'facturador_serie'        => 'required',
               'facturador_correlativo'  => 'required',
               'facturador_fechaemision' => 'required',
            ];
            $messages = [
               'documento_ruc.required'           => 'El "Numero de RUC" es Obligatorio.',
               'tipo_comprobante.required'        => 'El "Tipo de Comprobante" es Obligatorio.',
               'facturador_serie.required'        => 'El "Numero de Serie" es Obligatorio.',
               'facturador_correlativo.required'  => 'El "Numero de Correlativo" es Obligatorio.',
               'facturador_fechaemision.required' => 'El "Fecha de Emision" es Obligatorio.',
            ];

            $this->validate($request,$rules,$messages);

            $tienda = DB::table('tienda')
                  ->where('tienda.link',$linktienda)
                  ->first();

            if($request->input('tipo_comprobante')=='01' or $request->input('tipo_comprobante')=='03'){ // factura y boleta
                $comprobante = DB::table('s_facturacionboletafactura')
                    ->where([
                      ['s_facturacionboletafactura.idtienda', $tienda->id],
                      ['s_facturacionboletafactura.cliente_numerodocumento', $request->input('documento_ruc')],
                      ['s_facturacionboletafactura.venta_tipodocumento', $request->input('tipo_comprobante')],
                      ['s_facturacionboletafactura.venta_serie', $request->input('facturador_serie')],
                      ['s_facturacionboletafactura.venta_correlativo', $request->input('facturador_correlativo')],
                      ['s_facturacionboletafactura.venta_fechaemision', 'LIKE', '%'.$request->input('facturador_fechaemision').'%']
                    ])
                    ->first();
            }elseif($request->input('tipo_comprobante')=='07'){ // nota de credito
                $comprobante = DB::table('s_facturacionnotacredito')
                    ->where([
                      ['s_facturacionnotacredito.idtienda', $tienda->id],
                      ['s_facturacionnotacredito.cliente_numerodocumento', $request->input('documento_ruc')],
                      ['s_facturacionnotacredito.notacredito_tipodocumento', $request->input('tipo_comprobante')],
                      ['s_facturacionnotacredito.notacredito_serie', $request->input('facturador_serie')],
                      ['s_facturacionnotacredito.notacredito_correlativo', $request->input('facturador_correlativo')],
                      ['s_facturacionnotacredito.notacredito_fechaemision', 'LIKE', '%'.$request->input('facturador_fechaemision').'%']
                    ])
                    ->first();
            }elseif($request->input('tipo_comprobante')=='08'){ // nota de debito
                $comprobante = DB::table('s_facturacionnotadebito')
                    ->where([
                      ['s_facturacionnotadebito.idtienda', $tienda->id],
                      ['s_facturacionnotadebito.cliente_numerodocumento', $request->input('documento_ruc')],
                      ['s_facturacionnotadebito.notadebito_tipodocumento', $request->input('tipo_comprobante')],
                      ['s_facturacionnotadebito.notadebito_serie', $request->input('facturador_serie')],
                      ['s_facturacionnotadebito.notadebito_correlativo', $request->input('facturador_correlativo')],
                      ['s_facturacionnotadebito.notadebito_fechaemision', 'LIKE', '%'.$request->input('facturador_fechaemision').'%']
                    ])
                    ->first();
            }elseif($request->input('tipo_comprobante')=='09'){ // guiare mision
                $comprobante = DB::table('s_facturacionguiaremision')
                    ->where([
                      ['s_facturacionguiaremision.idtienda', $tienda->id],
                      ['s_facturacionguiaremision.despacho_destinatario_numerodocumento', $request->input('documento_ruc')],
                      ['s_facturacionguiaremision.despacho_tipodocumento', $request->input('tipo_comprobante')],
                      ['s_facturacionguiaremision.despacho_serie', $request->input('facturador_serie')],
                      ['s_facturacionguiaremision.despacho_correlativo', $request->input('facturador_correlativo')],
                      ['s_facturacionguiaremision.despacho_fechaemision', 'LIKE', '%'.$request->input('facturador_fechaemision').'%']
                    ])
                    ->first();
            }

            $idcomprobante = 0;
            $idtienda = 0;
            if(!is_null($comprobante)){
                $idcomprobante = $comprobante->id;
                $idtienda = $comprobante->idtienda;

            }
            return response()->json([
                  'resultado' => 'CORRECTO',
                  'mensaje'   => 'La consulta ha finalizado!!.',
                  'tipo_comprobante'   => $request->input('tipo_comprobante'),
                  'idcomprobante'   => $idcomprobante,
                  'idtienda'   => $idtienda
             ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$linktienda, $id)
    {
        if($id == 'showcarritocompra'){
            DB::table('s_carritocompra')->where('s_idusuariocliente',Auth::user()->id)->delete();
          
            if($request->input('productos')!=''){
                foreach($request->input('productos') as $value){
                    $idcarritocompra = DB::table('s_carritocompra')->insertGetId([
                        'cantidad' => $value['producto_cantidad'],
                        'preciounitario' => $value['producto_precioalpublico'],
                        'descuento' => '0.00',
                        's_idproducto' => $value['idproducto'],
                        's_idusuariocliente' => Auth::user()->id,
                        'idtienda' => $value['idtienda'],
                    ]);
                }
            }
          
            // listar carrito de compra
            $tiendas = DB::table('tienda')
                ->join('s_carritocompra','s_carritocompra.idtienda','=','tienda.id')
                ->where('s_idusuariocliente',Auth::user()->id)
                ->select(
                    'tienda.id as idtienda',
                    'tienda.nombre as tiendanombre'
                )
                ->orderBy('tienda.nombre','asc')
                ->distinct()
                ->get();
            $html = '';
            $carrito_total = 0;
            foreach($tiendas as $value){
                $s_carritocompras = DB::table('s_carritocompra')
                    ->join('s_producto','s_producto.id','=','s_carritocompra.s_idproducto')
                    ->where('s_carritocompra.s_idusuariocliente',Auth::user()->id)
                    ->where('s_carritocompra.idtienda',$value->idtienda)
                    ->select(
                          's_carritocompra.*',
                          's_producto.nombre as productonombre'
                    )
                    ->orderBy('s_producto.nombre','asc')
                    ->get();
                $html = $html.'<b>'.$value->tiendanombre.'</b><br>';
                $tienda_total = 0;
                $item = 1;
                foreach($s_carritocompras as $cvalue){
                    $tienda_subtotal = number_format(($cvalue->cantidad*$cvalue->preciounitario), 2, '.', '');
                    $html = $html.'<b>'.str_pad($item, 2, "0", STR_PAD_LEFT).'</b> - ('.$cvalue->cantidad.') '.$cvalue->productonombre.' - '.$tienda_subtotal.'<br>';
                    $tienda_total = $tienda_total+$tienda_subtotal;
                    $item++;
                }
                $html = $html.'<b>Total de compra: '.number_format($tienda_total, 2, '.', '').'</b><br>';
                //$html = $html.'<b>Costo de envio: 0.00</b><br>';
                $html = $html.'<b>Fecha de entrega: 2020-05-10 04:30:15</b><br>';
                $html = $html.'------------------------------------------------<br>';
                $carrito_total = $carrito_total+number_format($tienda_total, 2, '.', '');
            }
            $html = $html.'<b>TOTAL: '.number_format($carrito_total, 2, '.', '').'</b> <!--select id="delivery_idmetodopago1"><option value="1">Pagar con Visa o Mastercard</option><option value="2">Pagar cuando reciba el producto</option></select><button type="submit" id="pagacontraentrega1" style="float: none;background-color: #343a40;" class="price-link"> Solicitar Pedido</button--><br>';
            return $html;
        }
        elseif($id == 'showmostrarcomprobante') {
          
            $html = '<div class="mensaje-warning">
                  <i class="fa fa-warning"></i> El Comprobante no existe!!
                </div>';
            if($request->input('tipo_comprobante')=='01' or $request->input('tipo_comprobante')=='03'){
                $facturacionrespuesta= DB::table('s_facturacionrespuesta')
                    ->where('s_facturacionrespuesta.s_idfacturacionboletafactura',$request->input('idcomprobante'))
                    ->orderBy('s_facturacionrespuesta.id','desc')
                    ->limit(1)
                    ->first();
                if(isset($facturacionrespuesta)){
                    if($facturacionrespuesta->estado=='ACEPTADA'){
                        $html = '<div class="mensaje-info">
                                  '.$facturacionrespuesta->mensaje.'<br>
                                </div>
                                <div class="custom-form" style="margin-bottom: 5px;">
                                    <a href="'.url('public/backoffice/tienda/'.$request->input('idtienda').'/sunat/produccion/boletafactura/'.$facturacionrespuesta->nombre.'.xml').'" download class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;">
                                    <i class="fa fa-download"></i> Descargar XML</a>
                                    <a href="'.url('public/backoffice/tienda/'.$request->input('idtienda').'/sunat/produccion/boletafactura/R-'.$facturacionrespuesta->nombre.'.zip').'" download class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;">
                                    <i class="fa fa-download"></i> Descargar CDR</a>
                                    <a href="'.url('backoffice/tienda/sistema/'.$request->input('idtienda').'/facturacionboletafactura/'.$request->input('idcomprobante').'/edit?view=ticketpdf').'" download class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;">
                                    <i class="fa fa-download"></i> Descargar PDF</a>
                                </div>
                                <iframe src="'.url($linktienda).'/'.$request->input('idcomprobante').'/edit?view=ticketpdf_facturaboleta#zoom=130" frameborder="0" width="100%" height="600px"></iframe>';
                    }else{
                        if($facturacionrespuesta->estado=='OBSERVACIONES'){
                            $html = '<div class="mensaje-info">
                                      El Comprobante tiene Observaciones:<br>
                                      {{$facturacionrespuesta->mensaje}}<br>
                                      <b>¿Deseas reenviar el comprobante?</b><br>
                                    </div>';
                        }
                        elseif($facturacionrespuesta->estado=='RECHAZADA'){
                            $html = '<div class="mensaje-info">
                                        El Comprobante fue rechazado:<br>
                                        {{$facturacionrespuesta->mensaje}}<br>
                                        <b>¿Deseas reenviar el comprobante?</b><br>
                                      </div>';
                        }
                        elseif($facturacionrespuesta->estado=='EXCEPCION'){
                            $html = '<div class="mensaje-info">
                                      El CDR es inválido debe tratarse de un error-excepción:<br>
                                      {{$facturacionrespuesta->mensaje}}<br>
                                      <b>¿Deseas reenviar el comprobante?</b><br>
                                    </div>';
                        }else{
                            $html = '<div class="mensaje-info">
                                      El envio del comprobante tiene un error!!<br>
                                      <b>¿Deseas reenviar el comprobante?</b><br>
                                    </div>';
                        }
                    }
                }
            }
            elseif($request->input('tipo_comprobante')=='07'){
                $facturacionrespuesta= DB::table('s_facturacionrespuesta')
                    ->where('s_facturacionrespuesta.s_idfacturacionnotacredito',$request->input('idcomprobante'))
                    ->orderBy('s_facturacionrespuesta.id','desc')
                    ->limit(1)
                    ->first();
                if(isset($facturacionrespuesta)){
                    if($facturacionrespuesta->estado=='ACEPTADA'){
                        $html = '<div class="mensaje-info">
                                  '.$facturacionrespuesta->mensaje.'<br>
                                </div>
                                <div class="custom-form" style="margin-bottom: 5px;">
                                    <a href="'.url('public/backoffice/tienda/'.$request->input('idtienda').'/sunat/produccion/notacredito/'.$facturacionrespuesta->nombre.'.xml').'" download class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;">
                                    <i class="fa fa-download"></i> Descargar XML</a>
                                    <a href="'.url('public/backoffice/tienda/'.$request->input('idtienda').'/sunat/produccion/notacredito/R-'.$facturacionrespuesta->nombre.'.zip').'" download class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;">
                                    <i class="fa fa-download"></i> Descargar CDR</a>
                                    <a href="'.url('backoffice/tienda/sistema/'.$request->input('idtienda').'/facturacionnotacredito/'.$request->input('idcomprobante').'/edit?view=ticketpdf').'" download class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;">
                                    <i class="fa fa-download"></i> Descargar PDF</a>
                                </div>
                                <iframe src="'.url($linktienda).'/'.$request->input('idcomprobante').'/edit?view=ticketpdf_notacredito#zoom=130" frameborder="0" width="100%" height="600px"></iframe>';
                    }else{
                        if($facturacionrespuesta->estado=='OBSERVACIONES'){
                            $html = '<div class="mensaje-info">
                                      El Comprobante tiene Observaciones:<br>
                                      {{$facturacionrespuesta->mensaje}}<br>
                                      <b>¿Deseas reenviar el comprobante?</b><br>
                                    </div>';
                        }
                        elseif($facturacionrespuesta->estado=='RECHAZADA'){
                            $html = '<div class="mensaje-info">
                                        El Comprobante fue rechazado:<br>
                                        {{$facturacionrespuesta->mensaje}}<br>
                                        <b>¿Deseas reenviar el comprobante?</b><br>
                                      </div>';
                        }
                        elseif($facturacionrespuesta->estado=='EXCEPCION'){
                            $html = '<div class="mensaje-info">
                                      El CDR es inválido debe tratarse de un error-excepción:<br>
                                      {{$facturacionrespuesta->mensaje}}<br>
                                      <b>¿Deseas reenviar el comprobante?</b><br>
                                    </div>';
                        }else{
                            $html = '<div class="mensaje-info">
                                      El envio del comprobante tiene un error!!<br>
                                      <b>¿Deseas reenviar el comprobante?</b><br>
                                    </div>';
                        }
                    }
                }
            }
            elseif($request->input('tipo_comprobante')=='08'){
                $facturacionrespuesta= DB::table('s_facturacionrespuesta')
                    ->where('s_facturacionrespuesta.s_idfacturacionnotadebito',$request->input('idcomprobante'))
                    ->orderBy('s_facturacionrespuesta.id','desc')
                    ->limit(1)
                    ->first();
                if(isset($facturacionrespuesta)){
                    if($facturacionrespuesta->estado=='ACEPTADA'){
                        $html = '<div class="mensaje-info">
                                  '.$facturacionrespuesta->mensaje.'<br>
                                </div>
                                <div class="custom-form" style="margin-bottom: 5px;">
                                    <a href="'.url('public/backoffice/tienda/'.$request->input('idtienda').'/sunat/produccion/notadebito/'.$facturacionrespuesta->nombre.'.xml').'" download class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;">
                                    <i class="fa fa-download"></i> Descargar XML</a>
                                    <a href="'.url('public/backoffice/tienda/'.$request->input('idtienda').'/sunat/produccion/notadebito/R-'.$facturacionrespuesta->nombre.'.zip').'" download class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;">
                                    <i class="fa fa-download"></i> Descargar CDR</a>
                                    <a href="'.url('backoffice/tienda/sistema/'.$request->input('idtienda').'/facturacionnotadebito/'.$request->input('idcomprobante').'/edit?view=ticketpdf').'" download class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;">
                                    <i class="fa fa-download"></i> Descargar PDF</a>
                                </div>
                                <iframe src="'.url($linktienda).'/'.$request->input('idcomprobante').'/edit?view=ticketpdf_notadebito#zoom=130" frameborder="0" width="100%" height="600px"></iframe>';
                    }else{
                        if($facturacionrespuesta->estado=='OBSERVACIONES'){
                            $html = '<div class="mensaje-info">
                                      El Comprobante tiene Observaciones:<br>
                                      {{$facturacionrespuesta->mensaje}}<br>
                                      <b>¿Deseas reenviar el comprobante?</b><br>
                                    </div>';
                        }
                        elseif($facturacionrespuesta->estado=='RECHAZADA'){
                            $html = '<div class="mensaje-info">
                                        El Comprobante fue rechazado:<br>
                                        {{$facturacionrespuesta->mensaje}}<br>
                                        <b>¿Deseas reenviar el comprobante?</b><br>
                                      </div>';
                        }
                        elseif($facturacionrespuesta->estado=='EXCEPCION'){
                            $html = '<div class="mensaje-info">
                                      El CDR es inválido debe tratarse de un error-excepción:<br>
                                      {{$facturacionrespuesta->mensaje}}<br>
                                      <b>¿Deseas reenviar el comprobante?</b><br>
                                    </div>';
                        }else{
                            $html = '<div class="mensaje-info">
                                      El envio del comprobante tiene un error!!<br>
                                      <b>¿Deseas reenviar el comprobante?</b><br>
                                    </div>';
                        }
                    }
                }
            }
            elseif($request->input('tipo_comprobante')=='09'){
                $facturacionrespuesta= DB::table('s_facturacionrespuesta')
                    ->where('s_facturacionrespuesta.s_idfacturacionguiaremision',$request->input('idcomprobante'))
                    ->orderBy('s_facturacionrespuesta.id','desc')
                    ->limit(1)
                    ->first();
                if(isset($facturacionrespuesta)){
                    if($facturacionrespuesta->estado=='ACEPTADA'){
                        $html = '<div class="mensaje-info">
                                  '.$facturacionrespuesta->mensaje.'<br>
                                </div>
                                <div class="custom-form" style="margin-bottom: 5px;">
                                    <a href="'.url('public/backoffice/tienda/'.$request->input('idtienda').'/sunat/produccion/guiaremision/'.$facturacionrespuesta->nombre.'.xml').'" download class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;">
                                    <i class="fa fa-download"></i> Descargar XML</a>
                                    <a href="'.url('public/backoffice/tienda/'.$request->input('idtienda').'/sunat/produccion/guiaremision/R-'.$facturacionrespuesta->nombre.'.zip').'" download class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;">
                                    <i class="fa fa-download"></i> Descargar CDR</a>
                                    <a href="'.url('backoffice/tienda/sistema/'.$request->input('idtienda').'/facturacionguiaremision/'.$request->input('idcomprobante').'/edit?view=ticketpdf').'" download class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;">
                                    <i class="fa fa-download"></i> Descargar PDF</a>
                                </div>
                                <iframe src="'.url($linktienda).'/'.$request->input('idcomprobante').'/edit?view=ticketpdf_guiaremision#zoom=130" frameborder="0" width="100%" height="600px"></iframe>';
                    }else{
                        if($facturacionrespuesta->estado=='OBSERVACIONES'){
                            $html = '<div class="mensaje-info">
                                      El Comprobante tiene Observaciones:<br>
                                      {{$facturacionrespuesta->mensaje}}<br>
                                      <b>¿Deseas reenviar el comprobante?</b><br>
                                    </div>';
                        }
                        elseif($facturacionrespuesta->estado=='RECHAZADA'){
                            $html = '<div class="mensaje-info">
                                        El Comprobante fue rechazado:<br>
                                        {{$facturacionrespuesta->mensaje}}<br>
                                        <b>¿Deseas reenviar el comprobante?</b><br>
                                      </div>';
                        }
                        elseif($facturacionrespuesta->estado=='EXCEPCION'){
                            $html = '<div class="mensaje-info">
                                      El CDR es inválido debe tratarse de un error-excepción:<br>
                                      {{$facturacionrespuesta->mensaje}}<br>
                                      <b>¿Deseas reenviar el comprobante?</b><br>
                                    </div>';
                        }else{
                            $html = '<div class="mensaje-info">
                                      El envio del comprobante tiene un error!!<br>
                                      <b>¿Deseas reenviar el comprobante?</b><br>
                                    </div>';
                        }
                    }
                }
            }

            return $html;
          
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $linktienda, $id)
    {
        if($request->input('view') == 'ticketpdf_facturaboleta') {
            $tienda = DB::table('tienda')
                ->where('tienda.link',$linktienda)
                ->first();
            $facturacionboletafactura = DB::table('s_facturacionboletafactura as facturaboleta')
                ->join('users as responsable','responsable.id','facturaboleta.idusuarioresponsable')
                ->join('s_agencia','s_agencia.id','facturaboleta.idagencia')
                ->where('facturaboleta.id',$id)
                ->select(
                    'facturaboleta.*',
                    'responsable.nombre as responsablenombre',
                    's_agencia.logo as agencialogo',
                     DB::raw('CONCAT(facturaboleta.cliente_numerodocumento," - ",facturaboleta.cliente_razonsocial) as cliente'),
                     DB::raw('CONCAT(facturaboleta.cliente_departamento, " , ", facturaboleta.cliente_provincia, " , ", facturaboleta.cliente_distrito) as ubigeo'),
                     DB::raw('CONCAT(facturaboleta.emisor_ruc, " - ", facturaboleta.emisor_nombrecomercial) as agencia')
                  )
                ->first();
            $boletafacturadetalle     = DB::table('s_facturacionboletafacturadetalle')
                ->join('s_producto','s_producto.id','s_facturacionboletafacturadetalle.idproducto')
                ->where('s_facturacionboletafacturadetalle.idfacturacionboletafactura',$facturacionboletafactura->id)
                ->select(
                    's_facturacionboletafacturadetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->orderBy('s_facturacionboletafacturadetalle.id','asc')
                ->get();
            $facturacionrespuesta = DB::table('s_facturacionrespuesta')
                  ->where('s_facturacionrespuesta.s_idfacturacionboletafactura',$facturacionboletafactura->id)
                  ->first();
            $configuracion = tienda_configuracion($tienda->id);
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionboletafactura/ticketpdf',[
                'tienda'                   => $tienda,
                'facturacionboletafactura' => $facturacionboletafactura,
                'boletafacturadetalle'     => $boletafacturadetalle,
                'configuracion'            => $configuracion,
                'respuesta'                => $facturacionrespuesta
            ]);
            $ticket = 'Ticket_'.str_pad($facturacionboletafactura->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');
        }
        elseif($request->input('view') == 'ticketpdf_notacredito') {
            $tienda = DB::table('tienda')
                ->where('tienda.link',$linktienda)
                ->first();

            $facturacionnotacredito = DB::table('s_facturacionnotacredito')
                ->join('users as responsable','responsable.id','s_facturacionnotacredito.idusuarioresponsable')
                ->join('s_agencia','s_agencia.id','s_facturacionnotacredito.idagencia')
                ->where('s_facturacionnotacredito.id',$id)  
                ->select(
                    's_facturacionnotacredito.*',
                    'responsable.nombre as responsablenombre',
                    's_agencia.logo as agencialogo',
                )
                ->first();
          
            $facturacionnotacreditodetalles = DB::table('s_facturacionnotacreditodetalle')
                ->join('s_producto','s_producto.id','s_facturacionnotacreditodetalle.idproducto')
                ->where('s_facturacionnotacreditodetalle.idfacturacionnotacredito',$facturacionnotacredito->id)
                ->select(
                    's_facturacionnotacreditodetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->orderBy('s_facturacionnotacreditodetalle.id','asc')
                ->get();
          
            $facturacionrespuesta = DB::table('s_facturacionrespuesta')
                  ->where('s_facturacionrespuesta.s_idfacturacionnotacredito',$facturacionnotacredito->id)
                  ->first();
          
            $configuracion = tienda_configuracion($tienda->id);
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionnotacredito/ticketpdf',[
                'tienda' => $tienda,
                'facturacionnotacredito' => $facturacionnotacredito,
                'notacreditodetalle' => $facturacionnotacreditodetalles,
                'configuracion' => $configuracion,
                'respuesta' => $facturacionrespuesta
            ]);
            $ticket = 'Ticket_'.str_pad($facturacionnotacredito->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');
        }
        elseif($request->input('view') == 'ticketpdf_notadebito') {
            $tienda = DB::table('tienda')
                ->where('tienda.link',$linktienda)
                ->first();
          
            $facturacionnotadebito = DB::table('s_facturacionnotadebito')
                ->join('users as responsable','responsable.id','s_facturacionnotadebito.idusuarioresponsable')
                ->join('s_agencia','s_agencia.id','s_facturacionnotadebito.idagencia')
                ->where('s_facturacionnotadebito.id',$id)  
                ->select(
                    's_facturacionnotadebito.*',
                    'responsable.nombre as responsablenombre',
                    's_agencia.logo as agencialogo',
                )
                ->first();
            $facturacionnotadebitodetalles = DB::table('s_facturacionnotadebitodetalle')
                ->join('s_producto','s_producto.id','s_facturacionnotadebitodetalle.idproducto')
                ->where('s_facturacionnotadebitodetalle.idfacturacionnotadebito',$facturacionnotadebito->id)
                ->select(
                    's_facturacionnotadebitodetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->orderBy('s_facturacionnotadebitodetalle.id','asc')
                ->get();
          
            $facturacionrespuesta = DB::table('s_facturacionrespuesta')
                  ->where('s_facturacionrespuesta.s_idfacturacionnotadebito',$facturacionnotadebito->id)
                  ->first();
          
            $configuracion = tienda_configuracion($tienda->id);
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionnotadebito/ticketpdf',[
                'tienda' => $tienda,
                'facturacionnotadebito' => $facturacionnotadebito,
                'notadebitodetalle' => $facturacionnotadebitodetalles,
                'configuracion' => $configuracion,
                'respuesta' => $facturacionrespuesta
            ]);
            $ticket = 'Ticket_'.str_pad($facturacionnotadebito->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');
        }
        elseif($request->input('view') == 'ticketpdf_guiaremision') {
            $tienda = DB::table('tienda')
                ->where('tienda.link',$linktienda)
                ->first();
          
            $facturacionguiaremision = DB::table('s_facturacionguiaremision as guiaremision')
                ->join('users as responsable','responsable.id','guiaremision.idusuarioresponsable')
                ->join('s_agencia','s_agencia.id','guiaremision.idagencia')
                ->join('ubigeo as llegadaubigeo','llegadaubigeo.codigo','guiaremision.envio_direccionllegadacodigoubigeo')
                ->join('ubigeo as partidaubigeo','partidaubigeo.codigo','guiaremision.envio_direccionpartidacodigoubigeo')
                ->leftJoin('users as transportista', 'transportista.id', 'guiaremision.idusuariochofer')
                ->where('guiaremision.id', $id)
                ->select(
                    'guiaremision.*',
                    'responsable.nombre as responsablenombre',
                    's_agencia.logo as agencialogo',
                    DB::raw('CONCAT(guiaremision.emisor_ruc, " - ", guiaremision.emisor_razonsocial) as agencia'),
                    DB::raw('CONCAT(guiaremision.despacho_destinatario_numerodocumento, " - ", guiaremision.despacho_destinatario_razonsocial) as destinatario'),
                    DB::raw('IF(transportista.idtipopersona=1,
                    CONCAT(transportista.apellidos,", ",transportista.nombre),
                    CONCAT(transportista.apellidos)) as transportista'),
                    'llegadaubigeo.nombre as llegadaubigeonombre',
                    'partidaubigeo.nombre as partidaubigeonombre',
                )
                ->first();
            $facturacionguiaremisiondetalles = DB::table('s_facturacionguiaremisiondetalle')
                ->where('s_facturacionguiaremisiondetalle.idfacturacionguiaremision', $facturacionguiaremision->id)
                ->orderBy('s_facturacionguiaremisiondetalle.id', 'asc')
                ->get();
            $ubigeo_partida = DB::table('ubigeo')->where('ubigeo.codigo', $facturacionguiaremision->envio_direccionpartidacodigoubigeo)->first();
            $ubigeo_llegada = DB::table('ubigeo')->where('ubigeo.codigo', $facturacionguiaremision->envio_direccionllegadacodigoubigeo)->first();
            $transportista  = DB::table('users')
                ->where('identificacion', $facturacionguiaremision->transporte_choferdocumento)
                ->select(
                    'users.*',
                    DB::raw('CONCAT(users.identificacion, " - ", users.apellidos, ", ", users.nombre) as transportista')
                )
                ->first();
            $facturacionrespuesta = DB::table('s_facturacionrespuesta')
                  ->where('s_facturacionrespuesta.s_idfacturacionguiaremision',$facturacionguiaremision->id)
                  ->first();
            $configuracion = tienda_configuracion($tienda->id);
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionguiaremision/ticketpdf',[
                'tienda'                          => $tienda,
                'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles,
                'facturacionguiaremision'         => $facturacionguiaremision,
                'ubigeo_partida'                  => $ubigeo_partida,
                'ubigeo_llegada'                  => $ubigeo_llegada,
                'transportista'                   => $transportista,
                'configuracion'                   => $configuracion,
                'respuesta'                       => $facturacionrespuesta
            ]);
            $ticket = 'Ticket_'.str_pad($facturacionguiaremision->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');
        } 
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
