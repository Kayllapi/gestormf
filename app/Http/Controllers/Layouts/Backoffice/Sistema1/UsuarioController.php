<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Role;
use App\User;
use Auth;
use Hash;
use DB;
use Image;
use Intervention\Image\ImageManager;

class UsuarioController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        $where = [];
        $where[] = ['tipopersona.nombre','LIKE','%'.$request->input('tipo').'%'];
        $where[] = ['users.identificacion','LIKE','%'.$request->input('identificacion').'%'];
        $where[] = ['users.nombre','LIKE','%'.$request->input('cliente').'%'];
        $where[] = ['users.numerotelefono','LIKE','%'.$request->input('telefono').'%'];
        $where[] = ['ubigeo.nombre','LIKE','%'.$request->input('ubigeo').'%'];
      
        $where1 = [];
        $where1[] = ['tipopersona.nombre','LIKE','%'.$request->input('tipo').'%'];
        $where1[] = ['users.identificacion','LIKE','%'.$request->input('identificacion').'%'];
        $where1[] = ['users.apellidos','LIKE','%'.$request->input('cliente').'%'];
        $where1[] = ['users.numerotelefono','LIKE','%'.$request->input('telefono').'%'];
        $where1[] = ['ubigeo.nombre','LIKE','%'.$request->input('ubigeo').'%'];
      
       
      
        $usuarios = DB::table('users')
            ->join('tipopersona','tipopersona.id','=','users.idtipopersona')
            ->leftJoin('ubigeo','ubigeo.id','=','users.idubigeo')
            ->where($where)
            ->where('users.idestado',1)
            ->where('users.idtienda',$idtienda)
            ->orWhere($where1)
            ->where('users.idestado',1)
            ->where('users.idtienda',$idtienda)
            ->select(
                'users.*',
                'tipopersona.nombre as tipopersonanombre',
                'ubigeo.codigo as ubigeocodigo',
                'ubigeo.nombre as ubigeonombre'
            )
            ->orderBy('users.id','desc')
            ->paginate(10);
      
        // prestamo
        if($tienda->idcategoria==13){
            $usuario = DB::table('users')
                ->join('role_user','role_user.user_id','users.id')
                ->join('roles','roles.id','role_user.role_id')
                ->where('users.id',Auth::user()->id)
                ->where('roles.name','administrador')
                ->first();
            if($usuario==''){
                $usuarios = DB::table('users')
                    ->join('tipopersona','tipopersona.id','=','users.idtipopersona')
                    ->join('s_prestamo_cartera','s_prestamo_cartera.id','=','users.idprestamocartera')
                    ->leftJoin('ubigeo','ubigeo.id','=','users.idubigeo')
                    ->where($where)
                    ->where('users.idestado',1)
                    ->where('users.idtienda',$idtienda)
                    ->where('s_prestamo_cartera.idasesordestino',Auth::user()->id)
                    ->orWhere($where1)
                    ->where('users.idestado',1)
                    ->where('users.idtienda',$idtienda)
                    ->where('s_prestamo_cartera.idasesordestino',Auth::user()->id)
                    ->select(
                        'users.*',
                        'tipopersona.nombre as tipopersonanombre',
                        'ubigeo.codigo as ubigeocodigo',
                        'ubigeo.nombre as ubigeonombre'
                    )
                    ->orderBy('users.id','desc')
                    ->paginate(10);
            }
        }

        return view('layouts/backoffice/tienda/sistema/usuario/index', [
            'tienda' => $tienda,
            'usuarios' => $usuarios
        ]);
    }

    public function create(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if ($request->view == 'registrar') {
            $tipopersonas = DB::table('tipopersona')->get();
            return view('layouts/backoffice/tienda/sistema/usuario/create', compact(
                'tienda',
                'tipopersonas',
            ));
        }
    }

    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if ($request->input('view') == 'registrar') {
            $numerotelefono = '';
            $apellidopaterno = '';
            $apellidomaterno = '';
            if ($request->input('idtipopersona') == 1) {
                $rules = [
                    'dni' => 'required|numeric|digits:8',
                    'nombre' => 'required',
                    //'apellidos' => 'required',
                    'apellidopaterno' => 'required',
                    'apellidomaterno' => 'required',
                ];
                if($tienda->idcategoria==13){
                    $rules = array_merge($rules,[
                        'numerotelefono' => 'required|digits:9',
                    ]);
                    $numerotelefono = $request->input('numerotelefono');
                }
                $rules = array_merge($rules,[
                    'idubigeo' => 'required',
                    'direccion' => 'required',
                ]);
                $identificacion = $request->input('dni');
                $nombre = $request->input('nombre');
                $apellidos = $request->input('apellidopaterno').' '.$request->input('apellidomaterno');;
                $apellidopaterno = $request->input('apellidopaterno')!=null ? $request->input('apellidopaterno') : '';
                $apellidomaterno = $request->input('apellidomaterno')!=null ? $request->input('apellidomaterno') : '';
            }
            elseif ($request->input('idtipopersona') == 2) {
                $rules = [
                    'ruc' => 'required|numeric|digits:11',
                    'nombrecomercial' => 'required',
                    'razonsocial' => 'required',
                ];
                if($tienda->idcategoria==13){
                    $rules = array_merge($rules,[
                        'numerotelefono' => 'required|digits:9',
                    ]);
                    $numerotelefono = $request->input('numerotelefono');
                }
                $rules = array_merge($rules,[
                    'idubigeo' => 'required',
                    'direccion' => 'required',
                ]);
                $identificacion = $request->input('ruc');
                $nombre = $request->input('nombrecomercial');
                $apellidos = $request->input('razonsocial');
            }
            elseif ($request->input('idtipopersona') == 3) {
                $rules = [
                    'carnetextranjeria' => 'required',
                    'nombre_carnetextranjeria' => 'required',
                    //'apellidos_carnetextranjeria' => 'required',
                    'apellidopaterno_carnetextranjeria' => 'required',
                    'apellidomaterno_carnetextranjeria' => 'required',
                ];
                if($tienda->idcategoria==13){
                    $rules = array_merge($rules,[
                        'numerotelefono' => 'required|digits:9',
                    ]);
                    $numerotelefono = $request->input('numerotelefono');
                }
                $rules = array_merge($rules,[
                    'idubigeo' => 'required',
                    'direccion' => 'required',
                ]);
                $identificacion = $request->input('carnetextranjeria');
                $nombre = $request->input('nombre_carnetextranjeria');
                $apellidos = $request->input('apellidopaterno_carnetextranjeria').' '.$request->input('apellidomaterno_carnetextranjeria');
                $apellidopaterno = $request->input('apellidopaterno_carnetextranjeria')!=null ? $request->input('apellidopaterno_carnetextranjeria') : '';
                $apellidomaterno = $request->input('apellidomaterno_carnetextranjeria')!=null ? $request->input('apellidomaterno_carnetextranjeria') : '';
            }
            $messages = [
                    'dni.required' => 'El "DNI" es Obligatorio.',
                    'nombre.required' => 'El "Nombre" es Obligatorio.',
                    'apellidos.required' => 'El "Apellidos" es Obligatorio.',
                    'apellidopaterno.required' => 'El "Apellido Paterno" es Obligatorio.',
                    'apellidomaterno.required' => 'El "Apellido Materno" es Obligatorio.',
                    'ruc.required' => 'El "RUC" es Obligatorio.',
                    'nombrecomercial.required' => 'El "Nombre Comercial" es Obligatorio.',
                    'razonsocial.required' => 'El "Razón Social" es Obligatorio.',
                    'numerotelefono.required' => 'El "Número de Teléfono" es Obligatorio.',
                    'numerotelefono.digits' => 'El "Número de Teléfono" debe ser de 9 números.',
                    'idubigeo.required' => 'El "Ubicación (Ubigeo)" es Obligatorio.',
                    'direccion.required' => 'La "Dirección" es Obligatorio.',
                    'carnetextranjeria.required' => 'El "Carnet Extranjería" es Obligatorio.',
                    'nombre_carnetextranjeria.required' => 'El "Nombre" es Obligatorio.',
                    'apellidos_carnetextranjeria.required' => 'El "Apellidos" es Obligatorio.',
                    'apellidopaterno_carnetextranjeria.required' => 'El "Apellido Paterno" es Obligatorio.',
                    'apellidomaterno_carnetextranjeria.required' => 'El "Apellido Materno" es Obligatorio.',
                    'domicilio_mapa_latitud.required' => 'La "Ubicación" es Obligatorio.<br>(Mover el marcador del mapa para seleccionar una ubicación)',
                    'domicilio_mapa_longitud.required' => '',
            ];
            $this->validate($request,$rules,$messages);
          
            $usuario = DB::table('users')
                ->where('identificacion',$identificacion)
                ->where('idtienda',$idtienda)
                ->where('idestado',1)
                ->first();
            if($usuario!='' and $identificacion!=0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "RUC/DNI" ya existe, Ingrese Otro por favor.'
                ]);
            }

            $user = User::create([
                'idtipopersona'       => $request->input('idtipopersona'),
                'identificacion'      => $identificacion!=null?$identificacion:'',
                'nombre'              => $nombre,
                'apellidos'           => $apellidos!=null?$apellidos:'',
                'apellidopaterno'     => $apellidopaterno,
                'apellidomaterno'     => $apellidomaterno,
                'imagen'              => '',
                'fechanacimiento'     => $request->fechanacimiento,
                'idubigeo_nacimiento' => $request->idubigeo_nacimiento != 'null' ? $request->idubigeo_nacimiento : 0,
                'idgenero'            => $request->idgenero != 'null' ? $request->idgenero : 0,
                'idestadocivil'       => $request->idestadocivil != 'null' ? $request->idestadocivil : 0,
                'idnivelestudio'      => $request->idnivelestudio != 'null' ? $request->idnivelestudio : 0,
                'ocupacion'           => $request->ocupacion,
                'numerotelefono'      => $numerotelefono,
                'email'               => $request->input('email')!=null ? $request->input('email') : '',
                'referencia'          => $request->referencia,
                'idubigeo'            => $request->idubigeo != 'null' ? $request->idubigeo : 0,
                'direccion'           => $request->input('direccion')!=null?$request->input('direccion'):'',
                'mapa_latitud'        => '',
                'mapa_longitud'       => '',
              
                'email_verified_at'   => Carbon::now(),
                'usuario'             => Carbon::now()->format("Ymdhisu").'@'.$idtienda.'.com',
                'clave'               => '123',
                'password'            => Hash::make('123'),
                'iduserspadre'        => 0,
                'idtipousuario'       => 2, // 3=usuario sistema
                'idtienda'            => $idtienda,
                'idestadousuario'     => 2,
                'idestado'            => 1
            ]);
          
            // prestamo
            if($tienda->idcategoria==13){
                prestamo_registrar_tranferenciacartera($idtienda,Auth::user()->id,Auth::user()->id,$user->id);
            }
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);

        } 
    }

    public function show(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if ($id == 'show-detalle-permiso') {

            $permiso = DB::table('roles')->whereId($request->idrol)->first();
            $modulos = DB::table('modulo')
                ->where('idmodulo','0')
                ->where('idestado',1)
                ->orderBy('orden','asc')
                ->get();
            $html = "<table class='table' id='tabla-contenido'>
                        <thead class='thead-dark'>
                            <tr>
                                <th colspan='4'>Módulos</th>
                            </tr>
                        </thead>  
                        <tbody>";
            foreach ($modulos as $value) {

                $submodulos = DB::table('modulo')
                    ->where('idmodulo',$value->id)
                    ->where('idestado',1)
                    ->orderBy('orden','asc')
                    ->get();

                foreach ($submodulos as $subvalue) {

                    $subsubmodulos = DB::table('modulo')
                        ->where('idmodulo',$subvalue->id)
                        ->orderBy('orden','asc')
                        ->where('idestado',1)
                        ->get();

                    foreach($subsubmodulos as $subsubvalue) {
                        $rolesmodulo = DB::table('rolesmodulo')->where('idroles',$permiso->id)->where('idmodulo',$subsubvalue->id)->limit(1)->first(); 
                        if ($rolesmodulo != '') {
                            $html .= "<tr>
                                        <td width='10px' style='padding: 6px;'>
                                            <i class='fas fa-circle' style='font-size:10px;'></i>
                                        </td>
                                        <td colspan='2'>$subsubvalue->nombre</td>
                                    </tr>";
                        }

                        $sistemamodulos = DB::table('modulo')
                            ->where('idmodulo',$subsubvalue->id)
                            ->orderBy('orden','asc')
                            ->where('idestado',1)
                            ->get();

                        foreach($sistemamodulos as $sistemavalue) {
                            $rolesmodulo = DB::table('rolesmodulo')->where('idroles',$permiso->id)->where('idmodulo',$sistemavalue->id)->limit(1)->first(); 
                            if ($rolesmodulo != '') {
                                $html .= "<tr>
                                            <td></td>
                                            <td width='10px' style='padding: 6px;'>
                                                <i class='fas fa-check' style='font-size:10px;'></i>
                                            </td>
                                            <td>$sistemavalue->nombre</td>
                                        </tr>";
                            }
                        }
                    }
                }
            }
            $html .= "</tbody>
                  </table>";
            return [
                'html' => $html
            ];

        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $usuario = DB::table('users')->where('users.id', $id)
            ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
            ->leftJoin('ubigeo as ubigeonacimiento','ubigeonacimiento.id','users.idubigeo_nacimiento')
            ->leftJoin('role_user','role_user.user_id','users.id')
            ->leftJoin('roles','roles.id','role_user.role_id')
            ->select(
                'users.*',
                'roles.id as idroles',
                'roles.description as descriptionrole',
                'ubigeo.nombre as ubigeonombre',
                DB::raw('CONCAT(ubigeo.distrito, ", ", ubigeo.provincia, ", ", ubigeo.departamento) as ubigeoubicacion'),
                'ubigeonacimiento.nombre as ubigeonacimientonombre'
            )
            ->first();

        if ($request->input('view') == 'editar') {
            $tipopersonas = DB::table('tipopersona')->get();
            $estadocivil = DB::table('s_prestamo_estadocivil')->get();
            $nivelestudio = DB::table('s_prestamo_nivelestudio')->get();
            $logisticarutas       = DB::table('s_logisticaruta')->where('idtienda',$idtienda)->where('idestado',1)->get();
  
            return view('layouts/backoffice/tienda/sistema/usuario/edit', compact(
                'usuario', 
                'tienda',
                'tipopersonas',
                'estadocivil',
                'nivelestudio',
                'logisticarutas',
            ));
          
        } 
        elseif ($request->input('view')=='eliminar') {
            $tipopersonas = DB::table('tipopersona')->get();
            return view('layouts/backoffice/tienda/sistema/usuario/delete',[
                'usuario' => $usuario,
                'tienda' => $tienda,
                'tipopersonas' => $tipopersonas
            ]);
        } 
        elseif ($request->view == 'bienimportar') {
          
            $bienes = DB::table('s_prestamo_creditobien')
                ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_creditobien.idprestamo_credito')
                ->where([
                    ['s_prestamo_credito.idcliente', $id],
                    ['s_prestamo_creditobien.idtienda', $idtienda],
                    ['s_prestamo_creditobien.idestado', 1]
                ])
                ->select('s_prestamo_creditobien.*')
                ->orderBy('s_prestamo_creditobien.id','desc')
                ->get();
          
            return view('layouts/backoffice/tienda/sistema/usuario/bienimportar',[
                'tienda' => $tienda,
                'bienes' => $bienes,
            ]);  
        }
    }

    public function update(Request $request, $idtienda, $idusuario)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if ($request->input('view') == 'editar') {
            $numerotelefono = '';
            $apellidopaterno = '';
            $apellidomaterno = '';
            if ($request->input('idtipopersona') == 1) {
                $rules = [
                    'dni' => 'required',
                    'nombre' => 'required',
                    //'apellidos' => 'required',
                    'apellidopaterno' => 'required',
                    'apellidomaterno' => 'required',
                ];
                if($tienda->idcategoria==13){
                    $rules = array_merge($rules,[
                        'numerotelefono' => 'required|digits:9',
                    ]);
                    $numerotelefono = $request->input('numerotelefono');
                }
                $rules = array_merge($rules,[
                    'idubigeo' => 'required',
                    'direccion' => 'required',
                ]);
                $identificacion = $request->input('dni');
                $nombre = $request->input('nombre');
                $apellidos = $request->input('apellidopaterno').' '.$request->input('apellidomaterno');;
                $apellidopaterno = $request->input('apellidopaterno')!=null ? $request->input('apellidopaterno') : '';
                $apellidomaterno = $request->input('apellidomaterno')!=null ? $request->input('apellidomaterno') : '';
            }
            elseif ($request->input('idtipopersona') == 2) {
                $rules = [
                    'ruc' => 'required',
                    'nombrecomercial' => 'required',
                    'razonsocial' => 'required',
                ];
                if($tienda->idcategoria==13){
                    $rules = array_merge($rules,[
                        'numerotelefono' => 'required|digits:9',
                    ]);
                    $numerotelefono = $request->input('numerotelefono');
                }
                $rules = array_merge($rules,[
                    'idubigeo' => 'required',
                    'direccion' => 'required',
                ]);
                $identificacion = $request->input('ruc');
                $nombre = $request->input('nombrecomercial');
                $apellidos = $request->input('razonsocial');
            }
            elseif ($request->input('idtipopersona') == 3) {
                $rules = [
                    'carnetextranjeria' => 'required',
                    'nombre_carnetextranjeria' => 'required',
                    //'apellidos_carnetextranjeria' => 'required',
                    'apellidopaterno_carnetextranjeria' => 'required',
                    'apellidomaterno_carnetextranjeria' => 'required',
                ];
                if($tienda->idcategoria==13){
                    $rules = array_merge($rules,[
                        'numerotelefono' => 'required|digits:9',
                    ]);
                    $numerotelefono = $request->input('numerotelefono');
                }
                $rules = array_merge($rules,[
                    'idubigeo' => 'required',
                    'direccion' => 'required',
                ]);
                $identificacion = $request->input('carnetextranjeria');
                $nombre = $request->input('nombre_carnetextranjeria');
                $apellidos = $request->input('apellidopaterno_carnetextranjeria').' '.$request->input('apellidomaterno_carnetextranjeria');
                $apellidopaterno = $request->input('apellidopaterno_carnetextranjeria')!=null ? $request->input('apellidopaterno_carnetextranjeria') : '';
                $apellidomaterno = $request->input('apellidomaterno_carnetextranjeria')!=null ? $request->input('apellidomaterno_carnetextranjeria') : '';
            }
            $messages = [
                    'dni.required' => 'El "DNI" es Obligatorio.',
                    'nombre.required' => 'El "Nombre" es Obligatorio.',
                    'apellidos.required' => 'El "Apellidos" es Obligatorio.',
                    'apellidopaterno.required' => 'El "Apellido Paterno" es Obligatorio.',
                    'apellidomaterno.required' => 'El "Apellido Materno" es Obligatorio.',
                    'ruc.required' => 'El "RUC" es Obligatorio.',
                    'nombrecomercial.required' => 'El "Nombre Comercial" es Obligatorio.',
                    'razonsocial.required' => 'El "Razón Social" es Obligatorio.',
                    'numerotelefono.required' => 'El "Número de Teléfono" es Obligatorio.',
                    'numerotelefono.digits' => 'El "Número de Teléfono" debe ser de 9 números.',
                    'idubigeo.required' => 'El "Ubicación (Ubigeo)" es Obligatorio.',
                    'direccion.required' => 'La "Dirección" es Obligatorio.',
                    'carnetextranjeria.required' => 'El "Carnet Extranjería" es Obligatorio.',
                    'nombre_carnetextranjeria.required' => 'El "Nombre" es Obligatorio.',
                    'apellidos_carnetextranjeria.required' => 'El "Apellidos" es Obligatorio.',
                    'apellidopaterno_carnetextranjeria.required' => 'El "Apellido Paterno" es Obligatorio.',
                    'apellidomaterno_carnetextranjeria.required' => 'El "Apellido Materno" es Obligatorio.',
                    'domicilio_mapa_latitud.required' => 'La "Ubicación" es Obligatorio.<br>(Mover el marcador del mapa para seleccionar una ubicación)',
                    'domicilio_mapa_longitud.required' => '',
            ];
            $this->validate($request,$rules,$messages);

            $usuario = DB::table('users')
                ->where('id','<>',$idusuario)
                ->where('identificacion',$identificacion)
                ->where('idtienda',$idtienda)
                ->where('idestado',1)
                ->first();
            if($usuario!='' and $identificacion!=0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "RUC/DNI" ya existe, Ingrese Otro por favor.'
                ]);
            }

            $usuario = DB::table('users')->whereId($idusuario)->first();
            $imagen = uploadfile($usuario->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/sistema/');
            DB::table('users')->whereId($idusuario)->update([
                'idtipopersona'   => $request->input('idtipopersona'),
                'identificacion'  => $identificacion!=null?$identificacion:'',
                'nombre'          => $nombre,
                'apellidos'       => $apellidos!=null?$apellidos:'',
                'apellidopaterno' => $apellidopaterno,
                'apellidomaterno' => $apellidomaterno,
              
                'imagen'          => $imagen,
                'fechanacimiento' => $request->fechanacimiento,
                'idubigeo_nacimiento' => $request->idubigeo_nacimiento != 'null' ? $request->idubigeo_nacimiento : 0,
                'idgenero'        => $request->idgenero != 'null' ? $request->idgenero : 0,
                'idestadocivil'   => $request->idestadocivil != 'null' ? $request->idestadocivil : 0,
                'idnivelestudio'  => $request->idnivelestudio != 'null' ? $request->idnivelestudio : 0,
                'ocupacion'       => $request->ocupacion,
                
                'numerotelefono'  => $numerotelefono,
                'email'           => $request->input('email')!=null ? $request->input('email') : '',
                'referencia'      => $request->referencia,
                'idubigeo'        => $request->input('idubigeo')!='null'?$request->input('idubigeo'):0,
                'direccion'       => $request->input('direccion')!=null?$request->input('direccion'):'',
                'mapa_latitud'    => $request->domicilio_mapa_latitud,
                'mapa_longitud'   => $request->domicilio_mapa_longitud,
                'idlogisticaruta'          => $request->input('idlogisticaruta')!=null ? $request->input('idlogisticaruta') : 0,
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);

        } 
    }

    public function destroy(Request $request, $idtienda, $idusuario)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if ($request->input('view') == 'eliminar') {
            /*$usuario = DB::table('users')->whereId($idusuario)->first();
            uploadfile_eliminar($usuario->imagen,'/public/backoffice/tienda/'.$idtienda.'/sistema/');
            */
            /*DB::table('users')
                ->where('id',$idusuario)
                ->where('idtienda',$idtienda)
                ->delete();*/
            
            $usuario = DB::table('role_user')->where('user_id',$idusuario)->first();
            if($usuario!=''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No se puede eliminar, ya que tiene un acceso.'
                ]);
            }
          
            DB::table('users')
                ->where('id',$idusuario)
                ->where('idtienda',$idtienda)
                ->update(['idestado'=>2]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
       
    }
}
