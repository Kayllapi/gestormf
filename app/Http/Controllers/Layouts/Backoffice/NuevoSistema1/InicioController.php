<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use App\User;
use Hash;

class InicioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$idtienda)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        /*return view('layouts/backoffice/tienda/nuevosistema/inicio/createhorario',[
               'tienda' => $tienda,
        ]);*/
      
     }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $idtienda)
    {
        /*if($request->input('view') == 'registrarusuario') {
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
                'nombre'         => $nombre,
                'apellidos'      => $apellidos!=null?$apellidos:'',
                'identificacion' => $identificacion!=null?$identificacion:'',
                'email'          => $request->input('cliente_email')!=null ? $request->input('cliente_email') : '',
                'email_verified_at' => Carbon::now(),
                'usuario'        => Carbon::now()->format("Ymdhisu"),
                'clave'          => '123',
                'password'       => Hash::make('123'),
                'numerotelefono' => $request->input('cliente_numerotelefono')!=null?$request->input('cliente_numerotelefono'):'',
                'direccion'      => $request->input('cliente_direccion'),
                'imagen'         => '',
                'iduserspadre'=> 0,
                'idubigeo'       => $request->input('cliente_idubigeo'),
                'idtipopersona'  => $request->input('cliente_idtipopersona'),
                'idtipousuario'  => 2,
                'idtienda'       => $idtienda,
                'idestado'       => 2
            ]);
          
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
        }*/
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $idtienda, $id)
    {
        /*if($id == 'showlistarproducto'){
            $productos = DB::table('s_producto')
                ->join('tienda','tienda.id','s_producto.idtienda')
                ->leftJoin('s_categoria','s_categoria.id','s_producto.s_idcategoria1')
                ->leftJoin('s_categoria as subcategoria','subcategoria.id','s_producto.s_idcategoria2')
                ->leftJoin('s_marca','s_marca.id','s_producto.s_idmarca')
                ->leftJoin('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                ->where('s_producto.idtienda',$idtienda)
                ->where('s_producto.nombre','LIKE','%'.$request->input('buscar').'%')
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
                ->limit(20)
                ->get();
            return $productos;
        }
        elseif($id == 'showlistarusuario'){
            $usuarios = DB::table('users')
                ->where('idtienda',$idtienda)
                ->where('users.nombre','LIKE','%'.$request->input('buscar').'%')
                ->where('idestado','<>',3)
                ->orWhere('idtienda',$idtienda)
                ->where('users.apellidos','LIKE','%'.$request->input('buscar').'%')
                ->where('idestado','<>',3)
                ->orWhere('idtienda',$idtienda)
                ->where('users.identificacion','LIKE','%'.$request->input('buscar').'%')
                ->where('idestado','<>',3)
                ->select(
                  'users.id as id',
                   DB::raw('CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre) as text')
                )
                ->get();
            return $usuarios;
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
        }*/
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idtienda, $id)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->input('view')=='editarperfil'){
   
            $usuario = DB::table('users')
                ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
                ->where('users.id',Auth::user()->id)
                ->select(
                    'users.*',
                    'ubigeo.nombre as ubigeonombre',
                )
                ->first();
            return view('layouts/backoffice/tienda/nuevosistema/inicio/editperfil',[
              'tienda' => $tienda,
              'usuario' => $usuario
            ]);
        }
        elseif($request->input('view')=='editarcambiarclave'){
            $usuario = DB::table('users')
                ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
                ->where('users.id',Auth::user()->id)
                ->select(
                    'users.*',
                    'ubigeo.nombre as ubigeonombre',
                )
                ->first();
            return view('layouts/backoffice/tienda/nuevosistema/inicio/editpassword',[
              'tienda' => $tienda,
              'usuario' => $usuario
            ]);
        }
        /*elseif($request->input('view')=='editmetodopago'){
            $usuario = DB::table('users')->whereId(Auth::user()->id)->first();
            $bancos = DB::table('banco')->get();
            return view('layouts/backoffice/tienda/nuevosistema/inicio/editmetodopago',[
              'tienda' => $tienda,
              'usuario' => $usuario,
              'bancos' => $bancos
            ]);  
        }*/
     
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
            $imagen = uploadfile($usuario->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/nuevosistema/');

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
