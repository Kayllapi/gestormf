<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use App\User;
use Hash;

class InicioController extends Controller
{
    public function index(Request $request,$idtienda)
    {
                $tienda = DB::table('tienda')
                    ->where('tienda.id',$idtienda)
                    ->first();
      
   
        return view('layouts/backoffice/sistema/inicio/index',[
            'tienda' => $tienda,
        ]);
    }

    public function create(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        /*return view('layouts/backoffice/sistema/inicio/createhorario',[
               'tienda' => $tienda,
        ]);*/
      
        if($request->input('view')=='formapago'){
            return view('app/ajax/formapago',[
                'tienda' => $tienda,
                'request' => $request,
            ]);
        }
     }
  
    public function store(Request $request, $idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->input('view') == 'registrarusuario') {
            if($request->input('cliente_idtipopersona')==1){
                $rules = [
                    'cliente_dni' => 'required|numeric|digits:8',
                    'cliente_nombre' => 'required',
                    'cliente_apellidos' => 'required',
                    'cliente_idubigeo' => 'required',
                    'cliente_direccion' => 'required'
                ];
                $identificacion = $request->input('cliente_dni');
                $nombre = $request->input('cliente_nombre');
                $apellidos = $request->input('cliente_apellidos');
            }
            elseif ($request->input('cliente_idtipopersona') == 2) {
                $rules = [
                    'cliente_ruc' => 'required|numeric|digits:11',
                    'cliente_nombrecomercial' => 'required',
                    'cliente_razonsocial' => 'required',
                    'cliente_idubigeo' => 'required',
                    'cliente_direccion' => 'required'
                ];
                $identificacion = $request->input('cliente_ruc');
                $nombre = $request->input('cliente_nombrecomercial');
                $apellidos = $request->input('cliente_razonsocial');
            }
            elseif ($request->input('cliente_idtipopersona') == 3) {
                $rules = [
                    'cliente_carnetextranjeria' => 'required',
                    'cliente_nombre_carnetextranjeria' => 'required',
                    'cliente_apellidos_carnetextranjeria' => 'required',
                    'cliente_idubigeo' => 'required',
                    'cliente_direccion' => 'required'
                ];
                $identificacion = $request->input('cliente_carnetextranjeria');
                $nombre = $request->input('cliente_nombre_carnetextranjeria');
                $apellidos = $request->input('cliente_apellidos_carnetextranjeria');
            }
            $messages = [
                    'cliente_dni.required'   => 'El "DNI" es Obligatorio.',
                    'cliente_dni.numeric'   => 'El "DNI" debe ser Númerico.',
                    'cliente_dni.digits'   => 'El "DNI" debe ser de 8 Digitos.',
                    'cliente_nombre.required'   => 'El "Nombre" es Obligatorio.',
                    'cliente_apellidos.required'   => 'El "Apellidos" es Obligatorio.',
                    'cliente_ruc.required'   => 'El "RUC" es Obligatorio.',
                    'cliente_ruc.numeric'   => 'El "RUC" debe ser Númerico.',
                    'cliente_ruc.digits'   => 'El "RUC" debe ser de 11 Digitos.',
                    'cliente_nombrecomercial.required'   => 'El "Nombre Comercial" es Obligatorio.',
                    'cliente_razonsocial.required'   => 'El "Razón Social" es Obligatorio.',
                    'cliente_carnetextranjeria.required'   => 'El "Carnet Extranjería" es Obligatorio.',
                    'cliente_nombre_carnetextranjeria.required'   => 'El "Nombre" es Obligatorio.',
                    'cliente_apellidos_carnetextranjeria.required'   => 'El "Apellidos" es Obligatorio.',
                    'cliente_numerotelefono.required' => 'El "Número de Teléfono" es Obligatorio.',
                    'cliente_email.required'    => 'El "Correo Electrónico" es Obligatorio.',
                    'cliente_email.email'    => 'El "Correo Electrónico" es Incorrecto.',
                    'cliente_idubigeo.required'    => 'El "Ubicación (Ubigeo)" es Obligatorio.',
                    'cliente_direccion.required'    => 'La "Dirección" es Obligatorio.',
                    'cliente_idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $usuario = DB::table('users')
                ->where('identificacion',$identificacion)
                ->where('idtienda',$idtienda)
                ->where('idestado','<>',3)
                ->first();
            if($usuario!='' and $identificacion!=0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "DNI/RUC" ya existe, Ingrese Otro por favor.'
                ]);
            }
          
            $user = User::create([
                'nombre'            => $nombre,
                'apellidos'         => $apellidos!=null?$apellidos:'',
                'identificacion'    => $identificacion!=null?$identificacion:'',
                'email'             => $request->input('cliente_email')!=null ? $request->input('cliente_email') : '',
                'email_verified_at' => Carbon::now(),
                'usuario'           => Carbon::now()->format("Ymdhisu"),
                'clave'             => '123',
                'password'          => Hash::make('123'),
                'numerotelefono'    => $request->input('cliente_numerotelefono')!=null?$request->input('cliente_numerotelefono'):'',
                'direccion'         => $request->input('cliente_direccion'),
                'imagen'            => '',
                'iduserspadre'      => 0,
                'idubigeo'          => $request->input('cliente_idubigeo'),
                'idtipopersona'     => $request->input('cliente_idtipopersona'),
                'idtipousuario'     => 2,
                'idtienda'          => $idtienda,
                'idestadousuario'   => 2,
                'idestado'          => 1
            ]);
          
            // prestamo
            if($tienda->idcategoria==13){
                prestamo_registrar_tranferenciacartera($idtienda,Auth::user()->id,Auth::user()->id,$user->id);
            }
          
            $ubigeocliente = DB::table('ubigeo')->whereId($request->input('cliente_idubigeo'))->first();
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.',
              'cliente' => $user,
              'ubigeocliente' => $ubigeocliente
            ]);
        }
        elseif($request->input('view')=='registrarobservacionhorario') {
        
            DB::table('s_usuarioingresosalida')->whereId($request->input('idusuarioingresosalida'))->update([
								'observacion'    => $request->input('observacion'),
						]);
          
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje' => 'Se ha registrado correctamente.'
						]);
        }
      
    }

    public function show(Request $request, $idtienda, $id)
    {
        if($id == 'showlistarproducto'){
            $data_productos = [];
            if (is_numeric($request->input('buscar'))) {
                
            $datosProducto = DB::table('s_producto')
                ->join('tienda','tienda.id','s_producto.idtienda')
                ->where('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo',$request->input('buscar'))
                ->where('s_producto.s_idestado',1)
                ->orWhere('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo2',$request->input('buscar'))
                ->where('s_producto.s_idestado',1)
                ->orWhere('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo3',$request->input('buscar'))
                ->where('s_producto.s_idestado',1)
                ->orWhere('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo4',$request->input('buscar'))
                ->where('s_producto.s_idestado',1)
                ->orWhere('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo5',$request->input('buscar'))
                ->where('s_producto.s_idestado',1)
                ->orWhere('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo6',$request->input('buscar'))
                ->where('s_producto.s_idestado',1)
                ->orWhere('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo7',$request->input('buscar'))
                ->where('s_producto.s_idestado',1)
                ->orWhere('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo8',$request->input('buscar'))
                ->where('s_producto.s_idestado',1)
                ->orWhere('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo9',$request->input('buscar'))
                ->where('s_producto.s_idestado',1)
                ->orWhere('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo10',$request->input('buscar'))
                ->where('s_producto.s_idestado',1)
                ->select(
                    's_producto.id as id',
                )
                ->first();
                $presentaciones = producto_presentaciones($idtienda,$datosProducto->id);
                foreach($presentaciones as $valuepresentacion){  
                    $data_productos[] = [
                        'id' => $valuepresentacion['producto']->id,
                        'nivel' => 2,
                        'codigo' => $valuepresentacion['producto']->codigo,
                        'nombre' => $valuepresentacion['producto']->nombre,
                        'precioalpublico' => $valuepresentacion['producto']->precioalpublico,
                        'idestadodetalle' => $valuepresentacion['producto']->s_idestadodetalle,
                        'idestado' => $valuepresentacion['producto']->s_idestado,
                        'idestadotv' => $valuepresentacion['producto']->s_idestadotiendavirtual,
                        'unidadmedida' => $valuepresentacion['producto']->unidadmedida,
                        'text' => $valuepresentacion['producto']->text,
                        'idtienda' => $valuepresentacion['producto']->idtienda,
                        'tiendanombre' => $valuepresentacion['producto']->tiendanombre,
                        'categorianombre' => $valuepresentacion['producto']->categorianombre,
                        'imagen' => $valuepresentacion['producto']->imagen,
                    ];
                }
            }
            if (count($data_productos)==0) {
                $productos = DB::table('s_producto')
                    ->join('tienda','tienda.id','s_producto.idtienda')
                    ->leftJoin('s_categoria','s_categoria.id','s_producto.s_idcategoria1')
                    ->leftJoin('s_categoria as subcategoria','subcategoria.id','s_producto.s_idcategoria2')
                    ->leftJoin('s_marca','s_marca.id','s_producto.s_idmarca')
                    ->leftJoin('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                    ->where('s_producto.idtienda',$idtienda)
                    ->where('s_producto.nombre','LIKE','%'.$request->input('buscar').'%')
                    ->where('s_producto.s_idestadosistema',1)
                    ->where('s_producto.s_idestado',1)
                    ->orWhere('s_producto.idtienda',$idtienda)
                    ->where('s_producto.codigo',$request->input('buscar'))
                    ->where('s_producto.s_idestadosistema',1)
                    ->where('s_producto.s_idestado',1)
                    ->orWhere('s_producto.idtienda',$idtienda)
                    ->where('s_producto.codigo2',$request->input('buscar'))
                    ->where('s_producto.s_idestadosistema',1)
                    ->where('s_producto.s_idestado',1)
                    ->orWhere('s_producto.idtienda',$idtienda)
                    ->where('s_producto.codigo3',$request->input('buscar'))
                    ->where('s_producto.s_idestadosistema',1)
                    ->where('s_producto.s_idestado',1)
                    ->orWhere('s_producto.idtienda',$idtienda)
                    ->where('s_producto.codigo4',$request->input('buscar'))
                    ->where('s_producto.s_idestadosistema',1)
                    ->where('s_producto.s_idestado',1)
                    ->orWhere('s_producto.idtienda',$idtienda)
                    ->where('s_producto.codigo5',$request->input('buscar'))
                    ->where('s_producto.s_idestadosistema',1)
                    ->where('s_producto.s_idestado',1)
                    ->orWhere('s_producto.idtienda',$idtienda)
                    ->where('s_producto.codigo6',$request->input('buscar'))
                    ->where('s_producto.s_idestadosistema',1)
                    ->where('s_producto.s_idestado',1)
                    ->orWhere('s_producto.idtienda',$idtienda)
                    ->where('s_producto.codigo7',$request->input('buscar'))
                    ->where('s_producto.s_idestadosistema',1)
                    ->where('s_producto.s_idestado',1)
                    ->orWhere('s_producto.idtienda',$idtienda)
                    ->where('s_producto.codigo8',$request->input('buscar'))
                    ->where('s_producto.s_idestadosistema',1)
                    ->where('s_producto.s_idestado',1)
                    ->orWhere('s_producto.idtienda',$idtienda)
                    ->where('s_producto.codigo9',$request->input('buscar'))
                    ->where('s_producto.s_idestadosistema',1)
                    ->where('s_producto.s_idestado',1)
                    ->orWhere('s_producto.idtienda',$idtienda)
                    ->where('s_producto.codigo10',$request->input('buscar'))
                    ->where('s_producto.s_idestadosistema',1)
                    ->where('s_producto.s_idestado',1)
                    ->select(
                      's_producto.id as id',
                      's_producto.codigo as codigo',
                      's_producto.nombre as nombre',
                      's_producto.precioalpublico as precioalpublico',
                      's_producto.s_idestadodetalle as idestadodetalle',
                      's_producto.s_idestado as idestado',
                      's_producto.s_idestadotiendavirtual as idestadotv',
                       DB::raw('CONCAT(unidadmedida.nombre," x ",s_producto.por) as unidadmedida'),
                       DB::raw('CONCAT(s_producto.nombre," / ",s_producto.precioalpublico) as text'),
                       'tienda.id as idtienda',
                       'tienda.nombre as tiendanombre',
                       'tienda.link as tiendalink',
                       's_marca.nombre as marcanombre',
                       's_categoria.nombre as categorianombre',
                       DB::raw('(SELECT imagen FROM s_productogaleria WHERE s_idproducto=s_producto.id ORDER BY orden ASC LIMIT 1) as imagen')
                    )
                    ->limit(10)
                    ->get();

                $data_productos = [];
                foreach($productos as $value){
                    $data_productos[] = [
                        'id' => $value->id,
                        'nivel' => 1,
                        'codigo' => $value->codigo,
                        'nombre' => $value->nombre,
                        'precioalpublico' => $value->precioalpublico,
                        'idestadodetalle' => $value->idestadodetalle,
                        'idestado' => $value->idestado,
                        'idestadotv' => $value->idestadotv,
                        'unidadmedida' => $value->unidadmedida,
                        'text' => $value->text,
                        'idtienda' => $value->idtienda,
                        'tiendanombre' => $value->tiendanombre,
                        'categorianombre' => $value->categorianombre,
                        'imagen' => $value->imagen,
                    ];
                }
            }
                
            return $data_productos;
        }
        elseif($id=='showseleccionarproductocodigo'){
            if($request->input('codigoproducto')==''){
                return [ 
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Ingrese un codigo de Producto!!.',
                ];
            }
            $datosProducto = DB::table('s_producto')
                ->join('tienda','tienda.id','s_producto.idtienda')
                ->where('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo',$request->input('codigoproducto'))
                ->where('s_producto.s_idestado',1)
                ->orWhere('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo2',$request->input('codigoproducto'))
                ->where('s_producto.s_idestado',1)
                ->orWhere('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo3',$request->input('codigoproducto'))
                ->where('s_producto.s_idestado',1)
                ->orWhere('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo4',$request->input('codigoproducto'))
                ->where('s_producto.s_idestado',1)
                ->orWhere('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo5',$request->input('codigoproducto'))
                ->where('s_producto.s_idestado',1)
                ->orWhere('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo6',$request->input('codigoproducto'))
                ->where('s_producto.s_idestado',1)
                ->orWhere('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo7',$request->input('codigoproducto'))
                ->where('s_producto.s_idestado',1)
                ->orWhere('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo8',$request->input('codigoproducto'))
                ->where('s_producto.s_idestado',1)
                ->orWhere('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo9',$request->input('codigoproducto'))
                ->where('s_producto.s_idestado',1)
                ->orWhere('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo10',$request->input('codigoproducto'))
                ->where('s_producto.s_idestado',1)
                ->select(
                    's_producto.*',
                    'tienda.nombre as tiendanombre',
                    'tienda.link as tiendalink',
                    DB::raw('(SELECT imagen FROM s_productogaleria WHERE s_idproducto=s_producto.id ORDER BY orden ASC LIMIT 1) as imagen')
                )
                ->first();
            if($datosProducto==''){
                return [ 
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No existe el producto, ingrese otro código.',
                ];
            }
          
            /*$presentaciones = producto_presentaciones($idtienda,$datosProducto->id);
            $data_productos = [];
            foreach($presentaciones as $valuepresentacion){  
                 $data_productos[] = [
                     'id' => $valuepresentacion['producto']->id,
                     'codigo' => $valuepresentacion['producto']->codigo,
                     'nombre' => $valuepresentacion['producto']->nombre,
                     'precioalpublico' => $valuepresentacion['producto']->precioalpublico,
                     'idestadodetalle' => $valuepresentacion['producto']->s_idestadodetalle,
                     'idestado' => $valuepresentacion['producto']->s_idestado,
                     'idestadotv' => $valuepresentacion['producto']->s_idestadotiendavirtual,
                     'unidadmedida' => $valuepresentacion['producto']->unidadmedida,
                     'text' => $valuepresentacion['producto']->text,
                     'idtienda' => $valuepresentacion['producto']->idtienda,
                     'tiendanombre' => $valuepresentacion['producto']->tiendanombre,
                     'categorianombre' => $valuepresentacion['producto']->categorianombre,
                     'imagen' => $valuepresentacion['producto']->imagen,
                 ];
             }*/

            return [ 
              'producto' => $datosProducto,
              //'productopresentacioncantidad' => count($presentaciones),
              //'productopresentacion' => $data_productos,
              'stock' => productosaldo($idtienda,$datosProducto->id)['stock']
            ];
        }
        elseif($id == 'showlistarusuario'){
            $usuarios = DB::table('users')
                ->where('idtienda',$idtienda)
                ->where('users.nombre','LIKE','%'.$request->input('buscar').'%')
                ->where('idestado',1)
                ->orWhere('idtienda',$idtienda)
                ->where('users.apellidos','LIKE','%'.$request->input('buscar').'%')
                ->where('idestado',1)
                ->orWhere('idtienda',$idtienda)
                ->where('users.identificacion','LIKE','%'.$request->input('buscar').'%')
                ->where('idestado',1)
                ->select(
                  'users.id as id',
                  DB::raw('IF(users.idtipopersona = 1 || users.idtipopersona = 3,
                      CONCAT(users.identificacion, " - ", users.apellidos, ", ", users.nombre),
                      CONCAT(users.identificacion, " - ", users.apellidos)) as text')
                )
                ->get();
            return $usuarios;
        }
        elseif($id == 'showlistaracceso'){
            $usuarios = DB::table('users')
                ->join('role_user','role_user.user_id','users.id')
                ->join('roles','roles.id','role_user.role_id')
                ->where('idtienda',$idtienda)
                ->where('users.nombre','LIKE','%'.$request->input('buscar').'%')
                ->where('idestado',1)
                ->orWhere('idtienda',$idtienda)
                ->where('users.apellidos','LIKE','%'.$request->input('buscar').'%')
                ->where('idestado',1)
                ->orWhere('idtienda',$idtienda)
                ->where('users.identificacion','LIKE','%'.$request->input('buscar').'%')
                ->where('idestado',1)
                ->select(
                  'users.id as id',
                  DB::raw('IF(users.idtipopersona = 1 || users.idtipopersona = 3,
                      CONCAT(users.identificacion, " - ", users.apellidos, ", ", users.nombre),
                      CONCAT(users.identificacion, " - ", users.apellidos)) as text')
                )
                ->get();
            return $usuarios;
        }
        elseif($id == 'showlistarprestamousuario'){
            $where = [];
            if($request->input('idasesor')!=''){
                $where[] = ['s_prestamo_cartera.idasesordestino',$request->input('idasesor')];
            }
            $usuarios = DB::table('users')
                ->join('tipopersona','tipopersona.id','=','users.idtipopersona')
                ->join('s_prestamo_cartera','s_prestamo_cartera.id','=','users.idprestamocartera')
                ->join('users as asesor','asesor.id','=','s_prestamo_cartera.idasesordestino')
                ->where('users.idtienda',$idtienda)
                ->where('users.nombre','LIKE','%'.$request->input('buscar').'%')
                ->where('users.idestado',1)
                ->where($where)
                ->orWhere('users.idtienda',$idtienda)
                ->where('users.apellidos','LIKE','%'.$request->input('buscar').'%')
                ->where('users.idestado',1)
                ->where($where)
                ->orWhere('users.idtienda',$idtienda)
                ->where('users.identificacion','LIKE','%'.$request->input('buscar').'%')
                ->where('users.idestado',1)
                ->where($where)
                ->select(
                  'users.id as id',
                  'tipopersona.nombre as tipopersonanombre',
                  DB::raw('IF(users.idtipopersona = 1 || users.idtipopersona = 3,
                      CONCAT(users.identificacion, " - ", users.apellidos, ", ", users.nombre),
                      CONCAT(users.identificacion, " - ", users.apellidos)) as text'),
                  DB::raw('CONCAT( asesor.apellidos, ", ", asesor.nombre) as asesor')
                )
                ->get();
          
            return $usuarios;
        }
        elseif ($id == 'showlistarahorrousuario') {
            $where = [];
            if($request->input('idasesor')!=''){
                $where[] = ['s_prestamo_cartera.idasesordestino',$request->input('idasesor')];
            }
            $clientes = DB::table('s_prestamo_ahorro')
                ->join('users as cliente', 'cliente.id', 's_prestamo_ahorro.idcliente')
                ->join('s_prestamo_tipoahorro', 's_prestamo_tipoahorro.id', 's_prestamo_ahorro.idprestamo_tipoahorro')
                ->join('s_prestamo_cartera','s_prestamo_cartera.id','=','cliente.idprestamocartera')
                ->join('s_moneda', 's_moneda.id', 's_prestamo_ahorro.idmoneda')
                ->where('cliente.identificacion','LIKE', '%'.$request->buscar.'%')
                ->where('s_prestamo_ahorro.idestado', 1)
                ->where('s_prestamo_ahorro.idtienda', $idtienda)
                ->where('s_prestamo_ahorro.idestadoahorro', 4)
                ->where('s_prestamo_ahorro.idestadoconfirmacion', 1)
                ->where($where)

                ->orWhere('cliente.nombre','LIKE', '%'.$request->buscar.'%')
                ->where('s_prestamo_ahorro.idestado', 1)
                ->where('s_prestamo_ahorro.idtienda', $idtienda)
                ->where('s_prestamo_ahorro.idestadoahorro', 4)
                ->where('s_prestamo_ahorro.idestadoconfirmacion', 1)
                ->where($where)

                ->orWhere('cliente.apellidos','LIKE', '%'.$request->buscar.'%')
                ->where('s_prestamo_ahorro.idestado', 1)
                ->where('s_prestamo_ahorro.idtienda', $idtienda)
                ->where('s_prestamo_ahorro.idestadoahorro', 4)
                ->where('s_prestamo_ahorro.idestadoconfirmacion', 1)
                ->where($where)

                ->orWhere('s_prestamo_ahorro.codigo',$request->buscar)
                ->where('s_prestamo_ahorro.idestado', 1)
                ->where('s_prestamo_ahorro.idtienda', $idtienda)
                ->where('s_prestamo_ahorro.idestadoahorro', 4)
                ->where('s_prestamo_ahorro.idestadoconfirmacion', 1)
                ->where($where)
                ->select(
                      's_prestamo_ahorro.id as id',
                      DB::raw('IF(s_prestamo_ahorro.idestadorecaudacion = 1, "PENDIENTE", 
                                  IF(s_prestamo_ahorro.idestadorecaudacion = 2, "CANCELADO", "PENDIENTE")) as estado'),
                      's_prestamo_tipoahorro.nombre as tipoahorro',
                      'cliente.identificacion as clienteidentificacion',
                      'cliente.apellidos as clienteapellidos',
                      'cliente.nombre as clientenombre',
                      's_moneda.simbolo as monedasimbolo',
                      's_prestamo_ahorro.codigo as ahorrocodigo',
                      's_prestamo_ahorro.monto as ahorromonto',
                      's_prestamo_ahorro.fechaconfirmado as ahorrofechaconfirmado',
                      's_prestamo_ahorro.ahorrolibre_tiponombre as ahorrolibre_tiponombre',
                )
                ->orderBy('s_prestamo_ahorro.fechaconfirmado','desc')
                ->get();
            $dataclientes = [];
            foreach($clientes as $value){
                $dataclientes[] = [
                    'id' => $value->id,
                    'text' => $value->tipoahorro.' '.($value->ahorrolibre_tiponombre!=''?'('.$value->ahorrolibre_tiponombre.')':'').' <b>/</b> '.$value->estado.' <b>/</b> '.str_pad($value->ahorrocodigo, 8, "0", STR_PAD_LEFT).' <b>/</b> '.$value->clienteidentificacion.' - '.$value->clienteapellidos.', '.$value->clientenombre.' <b>/</b> '.$value->monedasimbolo.' '.$value->ahorromonto,
                    'tipoahorro' => $value->tipoahorro.' '.($value->ahorrolibre_tiponombre!=''?'('.$value->ahorrolibre_tiponombre.')':''),
                    'estado' => $value->estado,
                    'ahorrocodigo' => str_pad($value->ahorrocodigo, 8, "0", STR_PAD_LEFT),
                    'clienteidentificacion' => $value->clienteidentificacion,
                    'clienteapellidos' => $value->clienteapellidos,
                    'clientenombre' => $value->clientenombre,
                    'monedasimbolo' => $value->monedasimbolo,
                    'ahorromonto' => $value->ahorromonto,
                    'ahorrofechaconfirmado' => date_format(date_create($value->ahorrofechaconfirmado),"d-m-Y"),
                ];
            }
            return $dataclientes;
        }
        elseif ($id == 'showlistarcreditousuario') {
            $where = [];
            if($request->input('idasesor')!=''){
                $where[] = ['s_prestamo_cartera.idasesordestino',$request->input('idasesor')];
            }
            $clientes = DB::table('s_prestamo_credito')
                ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
                ->join('s_prestamo_cartera','s_prestamo_cartera.id','=','cliente.idprestamocartera')
                ->join('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
                ->where('cliente.identificacion','LIKE', '%'.$request->buscar.'%')
                ->where('s_prestamo_credito.idestado', 1)
                ->where('s_prestamo_credito.idtienda', $idtienda)
                ->where('s_prestamo_credito.idestadocredito', $request->idestadocredito)
                ->where('s_prestamo_credito.idestadodesembolso', $request->idestadodesembolso)
                ->where('s_prestamo_credito.idprestamo_estadocredito', $request->idprestamo_estadocredito)
                ->where($where)

                ->orWhere('cliente.nombre','LIKE', '%'.$request->buscar.'%')
                ->where('s_prestamo_credito.idestado', 1)
                ->where('s_prestamo_credito.idtienda', $idtienda)
                ->where('s_prestamo_credito.idestadocredito', $request->idestadocredito)
                ->where('s_prestamo_credito.idestadodesembolso', $request->idestadodesembolso)
                ->where('s_prestamo_credito.idprestamo_estadocredito', $request->idprestamo_estadocredito)
                ->where($where)

                ->orWhere('cliente.apellidos','LIKE', '%'.$request->buscar.'%')
                ->where('s_prestamo_credito.idestado', 1)
                ->where('s_prestamo_credito.idtienda', $idtienda)
                ->where('s_prestamo_credito.idestadocredito', $request->idestadocredito)
                ->where('s_prestamo_credito.idestadodesembolso', $request->idestadodesembolso)
                ->where('s_prestamo_credito.idprestamo_estadocredito', $request->idprestamo_estadocredito)
                ->where($where)

                ->orWhere('s_prestamo_credito.codigo',$request->buscar)
                ->where('s_prestamo_credito.idestado', 1)
                ->where('s_prestamo_credito.idtienda', $idtienda)
                ->where('s_prestamo_credito.idestadocredito', $request->idestadocredito)
                ->where('s_prestamo_credito.idestadodesembolso', $request->idestadodesembolso)
                ->where('s_prestamo_credito.idprestamo_estadocredito', $request->idprestamo_estadocredito)
                ->where($where)
                ->select(
                      's_prestamo_credito.id as id',
                      DB::raw('IF(s_prestamo_credito.idestadocobranza = 1, "PENDIENTE", 
                                  IF(s_prestamo_credito.idestadocobranza = 2, "CANCELADO", "PENDIENTE")) as estado'),
                      DB::raw('IF(s_prestamo_credito.idprestamo_tipocredito = 1, "NORMAL", 
                                  IF(s_prestamo_credito.idprestamo_tipocredito = 2, "REFINANCIADO",
                                      IF(s_prestamo_credito.idprestamo_tipocredito = 3, "REPROGRAMADO", "NINGUNO"))) as tipocredito'),
                      'cliente.identificacion as clienteidentificacion',
                      'cliente.apellidos as clienteapellidos',
                      'cliente.nombre as clientenombre',
                      's_moneda.simbolo as monedasimbolo',
                      's_prestamo_credito.codigo as creditocodigo',
                      's_prestamo_credito.monto as creditomonto',
                      's_prestamo_credito.fechadesembolsado as creditofechadesembolsado',
                )
                ->orderBy('s_prestamo_credito.idestadocobranza','asc')
                ->orderBy('s_prestamo_credito.fechadesembolsado','desc')
                ->get();
            $dataclientes = [];
            foreach($clientes as $value){
                $dataclientes[] = [
                    'id' => $value->id,
                    'text' => $value->tipocredito.' <b>/</b> '.$value->estado.' <b>/</b> '.str_pad($value->creditocodigo, 8, "0", STR_PAD_LEFT).' <b>/</b> '.$value->clienteidentificacion.' - '.$value->clienteapellidos.', '.$value->clientenombre.' <b>/</b> '.$value->monedasimbolo.' '.$value->creditomonto,
                    'tipocredito' => $value->tipocredito,
                    'estado' => $value->estado,
                    'creditocodigo' => str_pad($value->creditocodigo, 8, "0", STR_PAD_LEFT),
                    'clienteidentificacion' => $value->clienteidentificacion,
                    'clienteapellidos' => $value->clienteapellidos,
                    'clientenombre' => $value->clientenombre,
                    'monedasimbolo' => $value->monedasimbolo,
                    'creditomonto' => $value->creditomonto,
                    'creditofechadesembolsado' => date_format(date_create($value->creditofechadesembolsado),"d-m-Y")
                ];
            }
            return $dataclientes;
        }
        elseif($id == 'showbuscaridentificacion'){
            return consultaDniRuc($request->input('buscar_identificacion'), $request->input('tipo_persona'));
        }
        elseif($id == 'show-seleccionaridentificacion'){
            
            $user = DB::table('users')
              ->where('users.idtienda', $idtienda)
              ->where('users.id',Auth::user()->id)
              ->where('users.identificacion',$request->input('identificacion'))
              ->first();

            $idestado = 2;
            $resultado = 'ERROR';
            $mensaje = 'La Identificación ingresada es incorrecta, por favor vuelve a ingresar.';
          
            if($user != ''){
                $resultado = 'CORRECTO';
                $mensaje = 'Su registro de horario fue registrado correctamente.';
                $idestado = 1;
            }
            $fechaactual = Carbon::now();
            $idusuarioingresosalida = DB::table('s_usuarioingresosalida')->insertGetId([
                    'identificacioningresada'=> $request->input('identificacion'),
                    'observacion'            => $request->input('observacion'),
                    'fecharegistro'          => $fechaactual,
                    'idtienda'               => $idtienda,
                    'idusario'               => Auth::user()->id,
                    'idestado'               => $idestado,
            ]);
            
            return [
                  'resultado'              => $resultado,
                  'mensaje'                => $mensaje,
                  'fechaactual'            => date_format(date_create($fechaactual), 'd-m-Y h:i:s A'),
                  'idusuarioingresosalida' => $idusuarioingresosalida
            ];
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->input('view')=='editperfil'){
            $ubigeos = DB::table('ubigeo')->get();
            $usuario = DB::table('users')->whereId(Auth::user()->id)->first();
            return view('layouts/backoffice/sistema/inicio/editperfil',[
              'tienda' => $tienda,
              'ubigeos' => $ubigeos,
              'usuario' => $usuario
            ]);
        }
        elseif($request->input('view')=='editcambiarclave'){
            $ubigeos = DB::table('ubigeo')->get();
            $usuario = DB::table('users')->whereId(Auth::user()->id)->first();
            return view('layouts/backoffice/sistema/inicio/editpassword',[
              'tienda' => $tienda,
              'ubigeos' => $ubigeos,
              'usuario' => $usuario
            ]);
        }
        elseif($request->input('view')=='editmetodopago'){
            $ubigeos = DB::table('ubigeo')->get();
            $usuario = DB::table('users')->whereId(Auth::user()->id)->first();
            $bancos = DB::table('banco')->get();
            return view('layouts/backoffice/sistema/inicio/editmetodopago',[
              'tienda' => $tienda,
              'ubigeos' => $ubigeos,
              'usuario' => $usuario,
              'bancos' => $bancos
            ]);  
        }
      
        elseif($request->input('view')=='productovencido'){
            $productos = DB::table('s_producto')
              ->join('tienda','tienda.id','s_producto.idtienda')
              ->leftJoin('s_categoria','s_categoria.id','s_producto.s_idcategoria1')
              ->leftJoin('s_categoria as subcategoria','subcategoria.id','s_producto.s_idcategoria2')
              ->leftJoin('s_marca','s_marca.id','s_producto.s_idmarca')
              ->leftJoin('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
              ->where('s_producto.idtienda',$idtienda)
              ->where('s_producto.s_idestado',1)
              ->whereNotNull('s_producto.fechavencimiento')
              //->where('s_producto.alertavencimiento','>',0)
              ->select(
                's_producto.id as id',
                's_producto.codigo as codigo',
                's_producto.nombre as nombre',
                's_producto.precioalpublico as precioalpublico',
                's_producto.s_idestadodetalle as idestadodetalle',
                's_producto.s_idestado as idestado',
                's_producto.s_idestadotiendavirtual as idestadotv',
                's_producto.idproductopresentacion as idproductopresentacion',
                's_producto.alertavencimiento as alertavencimiento',
                's_producto.fechavencimiento as fechavencimiento',
                 DB::raw('TIMESTAMPDIFF(DAY, CURDATE(), s_producto.fechavencimiento) as diasfaltantevencimiento'),
                 DB::raw('CONCAT(unidadmedida.nombre," x ",s_producto.por) as unidadmedida'),
                 DB::raw('CONCAT(s_producto.nombre," / ",s_producto.precioalpublico) as text'),
                 'tienda.id as idtienda',
                 'tienda.nombre as tiendanombre',
                 'tienda.link as tiendalink',
                 's_marca.nombre as marcanombre',
                 's_categoria.nombre as categorianombre',
                 DB::raw('(SELECT imagen FROM s_productogaleria WHERE s_idproducto=s_producto.id ORDER BY orden ASC LIMIT 1) as imagen')
              )
              //->orderBy('s_producto.alertavencimiento','desc')
              ->orderBy('s_producto.fechavencimiento','asc')
              ->paginate(10);
          
            return view('layouts/backoffice/sistema/inicio/productovencido',[
              'tienda' => $tienda,
              'productos' => $productos,
            ]);
        }
      
     
    }

    public function update(Request $request, $idtienda, $idsuario)
    {
        if($request->input('view')=='editperfil') {
            $rules = [
								'nombre' => 'required',
								'apellidos' => 'required',
						];
						$messages = [
								'nombre.required' => 'El "Nombre" es Obligatorio.',
								'apellidos.required' => 'Los "Apellidos" es Obligatorio.',
						];
						$this->validate($request,$rules,$messages);
          
            $usuario = DB::table('users')->whereId($idsuario)->first();
            $imagen = uploadfile($usuario->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/'.$idtienda.'/sistema/');

            DB::table('users')->whereId($idsuario)->update([
								'nombre' => $request->input('nombre'),
                'apellidos' => $request->input('apellidos'),
                'identificacion' => $request->input('identificacion')!=null?$request->input('identificacion'):'',
                'email' => $request->input('email')!=null?$request->input('email'):'',
                'numerotelefono' => $request->input('numerotelefono')!=null?$request->input('numerotelefono'):'',
                'direccion' => $request->input('direccion')!=null?$request->input('direccion'):'',
                'imagen' => $imagen,
                'idubigeo' => $request->input('idubigeo')
						]);
          
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje' => 'Se ha registrado correctamente.'
						]);
        }elseif($request->input('view')=='editpassword') {
            $rules = [
								'antpassword' => 'required',
								'password' => 'required|string|min:3|confirmed',
								'password_confirmation' => 'required|required_with:passwordcsame:password|string|min:3',
						];
						$messages = [
								'antpassword.required' => 'La "Contraseña Actual" es Obligatorio.',
								'password.required' => 'La "Nueva Contraseña" es Obligatorio.',
								'password_confirmation.required' => 'El "Confirmar Nueva Contraseña" es Obligatorio.',
						];
						$this->validate($request,$rules,$messages);
          
            $user = DB::table('users')->whereId(Auth::user()->id)->where('clave',$request->input('antpassword'))->first();
            if($user==''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje' => 'la "Contraseña Actual" no es correcta.'
                ]);
            }

            DB::table('users')->whereId(Auth::user()->id)->update([
								'clave' => $request->input('password'),
                'password' => Hash::make($request->input('password')),
						]);
          
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje' => 'Se ha registrado correctamente.'
						]);
        }
      
    }

    public function destroy($id)
    {
        //
    }
}
