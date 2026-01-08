<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        json_usuario($idtienda,$request->name_modulo);
      
        return view('layouts/backoffice/tienda/nuevosistema/usuario/index', [
            'tienda' => $tienda
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if ($request->view == 'registrar') {
            $tipopersonas = DB::table('tipopersona')->get();
            $estadocivil = DB::table('s_prestamo_estadocivil')->where('s_prestamo_estadocivil.idtienda', $idtienda)->get();
            $nivelestudio = DB::table('s_prestamo_nivelestudio')->where('s_prestamo_nivelestudio.idtienda', $idtienda)->get();
            $modulo_prestamo = DB::table('role_user')
                ->join('roles','roles.id','role_user.role_id')
                ->where('role_user.user_id',Auth::user()->id)
                ->where('roles.idcategoria',13) // prestamo
                ->first();
            return view('layouts/backoffice/tienda/nuevosistema/usuario/create', compact(
                'tienda',
                'tipopersonas',
                'estadocivil',
                'nivelestudio',
                'modulo_prestamo',
            ));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if ($request->input('view') == 'registrar') {
            if ($request->input('idtipopersona') == 1) {
                $rules = [
                    'dni' => 'required|numeric|digits:8',
                    'nombre' => 'required',
                    'apellidos' => 'required',
                ];
                if($request->input('estado_numtelefono')=='required'){
                    $rules = array_merge($rules,[
                        'numerotelefono' => 'required',
                    ]);
                }
                $rules = array_merge($rules,[
                    'idubigeo' => 'required',
                    'direccion' => 'required',
                ]);
                $identificacion = $request->input('dni');
                $nombre = $request->input('nombre');
                $apellidos = $request->input('apellidos');
            }
            elseif ($request->input('idtipopersona') == 2) {
                $rules = [
                    'ruc' => 'required|numeric|digits:11',
                    'nombrecomercial' => 'required',
                    'razonsocial' => 'required',
                ];
                if($request->input('estado_numtelefono')=='required'){
                    $rules = array_merge($rules,[
                        'numerotelefono' => 'required',
                    ]);
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
                    'apellidos_carnetextranjeria' => 'required',
                ];
                if($request->input('estado_numtelefono')=='required'){
                    $rules = array_merge($rules,[
                        'numerotelefono' => 'required',
                    ]);
                }
                $rules = array_merge($rules,[
                    'idubigeo' => 'required',
                    'direccion' => 'required',
                ]);
                $identificacion = $request->input('carnetextranjeria');
                $nombre = $request->input('nombre_carnetextranjeria');
                $apellidos = $request->input('apellidos_carnetextranjeria');
            }
            $messages = [
                    'dni.required' => 'El "DNI" es Obligatorio.',
                    'dni.numeric'   => 'El "DNI" debe ser Númerico.',
                    'dni.digits'   => 'El "DNI" debe ser de 8 Digitos.',
                    'nombre.required' => 'El "Nombre" es Obligatorio.',
                    'apellidos.required' => 'El "Apellidos" es Obligatorio.',
                    'ruc.required' => 'El "RUC" es Obligatorio.',
                    'ruc.numeric'   => 'El "RUC" debe ser Númerico.',
                    'ruc.digits'   => 'El "RUC" debe ser de 11 Digitos.',
                    'nombrecomercial.required' => 'El "Nombre Comercial" es Obligatorio.',
                    'razonsocial.required' => 'El "Razón Social" es Obligatorio.',
                    'numerotelefono.required' => 'El "Número de Teléfono" es Obligatorio.',
                    'idubigeo.required' => 'El "Ubicación (Ubigeo)" es Obligatorio.',
                    'direccion.required' => 'La "Dirección" es Obligatorio.',
                    'carnetextranjeria.required' => 'El "Carnet Extranjería" es Obligatorio.',
                    'nombre_carnetextranjeria.required' => 'El "Nombre" es Obligatorio.',
                    'apellidos_carnetextranjeria.required' => 'El "Apellidos" es Obligatorio.',
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
                    'mensaje'   => 'El "RUC/DNI" ya existe, Ingrese Otro por favor.'
                ]);
            }

            $imagen = uploadfile('','',$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/sistema/');
            $newusuario = Carbon::now()->format("Ymdhisu").'@'.$idtienda.'.com';
            $user = User::create([
                'idtipopersona'  => $request->input('idtipopersona'),
                'identificacion' => $identificacion!=null?$identificacion:'',
                'nombre'         => $nombre,
                'apellidos'      => $apellidos!=null?$apellidos:'',
              
                'imagen'         => $imagen,
                'fechanacimiento' => $request->fechanacimiento,
                'idubigeo_nacimiento' => $request->idubigeo_nacimiento != 'null' ? $request->idubigeo_nacimiento : 0,
                'idgenero' => $request->idgenero != 'null' ? $request->idgenero : 0,
                'idestadocivil' => $request->idestadocivil != 'null' ? $request->idestadocivil : 0,
                'idnivelestudio' => $request->idnivelestudio != 'null' ? $request->idnivelestudio : 0,
                'ocupacion' => $request->ocupacion,
                
                'numerotelefono' => $request->input('numerotelefono')!=null?$request->input('numerotelefono'):'',
                'email'          => $request->input('email')!=null ? $request->input('email') : '',
                'referencia' => $request->referencia,
                'idubigeo'       => $request->idubigeo != 'null' ? $request->idubigeo : 0,
                'direccion'      => $request->input('direccion')!=null?$request->input('direccion'):'',
                'mapa_latitud' => $request->domicilio_mapa_latitud,
                'mapa_longitud' => $request->domicilio_mapa_longitud,
              
                'email_verified_at' => Carbon::now(),
                'usuario'        => $newusuario,
                'clave'          => '123',
                'password'       => Hash::make('123'),
                'iduserspadre'   => 0,
                'idtipousuario'  => 2,
                'idtienda'       => $idtienda,
                'idestado'       => 2
            ]);
          
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);

        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
                                        <td width='10px' style='padding: 0px;'>
                                            <img src='".url('public/backoffice/sistema/modulosistema/flecha_derecha.png')."' style='height:18px;'>
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
                                            <td width='10px' style='padding: 0px;'>
                                                <img src='".url('public/backoffice/sistema/modulosistema/check.png')."' style='height:18px;'>
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
        elseif ($id == 'show-moduloactualizar'){
            json_usuario($idtienda,$request->name_modulo);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $usuario = DB::table('users')->where('users.id', $id)
            ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
            ->leftJoin('ubigeo as ubigeonacimiento','ubigeonacimiento.id','users.idubigeo_nacimiento')
            ->leftJoin('role_user','role_user.user_id','users.id')
            ->leftJoin('roles','roles.id','role_user.role_id')
            ->leftJoin('tipopersona','tipopersona.id','users.idtipopersona')
            ->leftJoin('s_prestamo_estadocivil','s_prestamo_estadocivil.id','users.idestadocivil')
            ->leftJoin('s_prestamo_nivelestudio','s_prestamo_nivelestudio.id','users.idnivelestudio')
            ->select(
                'users.*',
                'roles.id as idroles',
                'roles.description as descriptionrole',
                'ubigeo.nombre as ubigeonombre',
                'ubigeonacimiento.nombre as ubigeonacimientonombre',
                'tipopersona.nombre as tipopersonanombre',
                's_prestamo_estadocivil.nombre as estadocivilnombre',
                's_prestamo_nivelestudio.nombre as nivelestudionombre'
            )
            ->first();

        if ($request->input('view') == 'editar') {
          
            $tipopersonas = DB::table('tipopersona')->get();
            $estadocivil = DB::table('s_prestamo_estadocivil')->where('s_prestamo_estadocivil.idtienda', $idtienda)->get();
            $nivelestudio = DB::table('s_prestamo_nivelestudio')->where('s_prestamo_nivelestudio.idtienda', $idtienda)->get();

            $modulo_prestamo = DB::table('role_user')
                ->join('roles','roles.id','role_user.role_id')
                ->where('role_user.user_id',Auth::user()->id)
                ->where('roles.idcategoria',13) // prestamo
                ->first();
          
            return view('layouts/backoffice/tienda/nuevosistema/usuario/edit', compact(
                'usuario', 
                'tienda',
                'tipopersonas',
                'estadocivil',
                'nivelestudio',
                'modulo_prestamo',
            ));
          
        } 
        elseif ($request->input('view') == 'detalle') {
          
            return view('layouts/backoffice/tienda/nuevosistema/usuario/detalle', compact(
                'usuario', 
                'tienda',
            ));
          
        } 
        elseif ($request->input('view')=='permiso') {
            $roles = DB::table('roles')
                ->where('idcategoria',$tienda->idcategoria)
                ->orderBy('roles.description','asc')
                ->get();
            return view('layouts/backoffice/tienda/nuevosistema/usuario/permiso',[
                'usuario' => $usuario,
                'tienda' => $tienda,
                'roles' => $roles
            ]);  
        } 
        elseif ($request->input('view')=='eliminar') {
            $tipopersonas = DB::table('tipopersona')->get();
            $estadocivil = DB::table('s_prestamo_estadocivil')->where('s_prestamo_estadocivil.idtienda', $idtienda)->get();
            $nivelestudio = DB::table('s_prestamo_nivelestudio')->where('s_prestamo_nivelestudio.idtienda', $idtienda)->get();

            $modulo_prestamo = DB::table('role_user')
                ->join('roles','roles.id','role_user.role_id')
                ->where('role_user.user_id',Auth::user()->id)
                ->where('roles.idcategoria',13) // prestamo
                ->first();
          
            return view('layouts/backoffice/tienda/nuevosistema/usuario/delete', compact(
                'usuario', 
                'tienda',
                'tipopersonas',
                'estadocivil',
                'nivelestudio',
                'modulo_prestamo',
            ));
        } 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idtienda, $idusuario)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if ($request->input('view') == 'editar') {

            if ($request->input('idtipopersona') == 1) {
                $rules = [
                    'dni' => 'required',
                    'nombre' => 'required',
                    'apellidos' => 'required',
                ];
                if($request->input('estado_numtelefono')=='required'){
                    $rules = array_merge($rules,[
                        'numerotelefono' => 'required',
                    ]);
                }
                $rules = array_merge($rules,[
                    'idubigeo' => 'required',
                    'direccion' => 'required',
                ]);
                $identificacion = $request->input('dni');
                $nombre = $request->input('nombre');
                $apellidos = $request->input('apellidos');
            }
            elseif ($request->input('idtipopersona') == 2) {
                $rules = [
                    'ruc' => 'required',
                    'nombrecomercial' => 'required',
                    'razonsocial' => 'required',
                ];
                if($request->input('estado_numtelefono')=='required'){
                    $rules = array_merge($rules,[
                        'numerotelefono' => 'required',
                    ]);
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
                    'apellidos_carnetextranjeria' => 'required',
                ];
                if($request->input('estado_numtelefono')=='required'){
                    $rules = array_merge($rules,[
                        'numerotelefono' => 'required',
                    ]);
                }
                $rules = array_merge($rules,[
                    'idubigeo' => 'required',
                    'direccion' => 'required',
                ]);
                $identificacion = $request->input('carnetextranjeria');
                $nombre = $request->input('nombre_carnetextranjeria');
                $apellidos = $request->input('apellidos_carnetextranjeria');
            }
            $messages = [
                    'dni.required' => 'El "DNI" es Obligatorio.',
                    'nombre.required' => 'El "Nombre" es Obligatorio.',
                    'apellidos.required' => 'El "Apellidos" es Obligatorio.',
                    'ruc.required' => 'El "RUC" es Obligatorio.',
                    'nombrecomercial.required' => 'El "Nombre Comercial" es Obligatorio.',
                    'razonsocial.required' => 'El "Razón Social" es Obligatorio.',
                    'numerotelefono.required' => 'El "Número de Teléfono" es Obligatorio.',
                    'idubigeo.required' => 'El "Ubicación (Ubigeo)" es Obligatorio.',
                    'direccion.required' => 'La "Dirección" es Obligatorio.',
                    'carnetextranjeria.required' => 'El "Carnet Extranjería" es Obligatorio.',
                    'nombre_carnetextranjeria.required' => 'El "Nombre" es Obligatorio.',
                    'apellidos_carnetextranjeria.required' => 'El "Apellidos" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            $usuario = DB::table('users')
                ->where('id','<>',$idusuario)
                ->where('identificacion',$identificacion)
                ->where('idtienda',$idtienda)
                ->where('idestado','<>',3)
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
                'idtipopersona'  => $request->input('idtipopersona'),
                'identificacion' => $identificacion!=null?$identificacion:'',
                'nombre'         => $nombre,
                'apellidos'      => $apellidos!=null?$apellidos:'',
              
                'imagen'         => $imagen,
                'fechanacimiento' => $request->fechanacimiento,
                'idubigeo_nacimiento' => $request->idubigeo_nacimiento != 'null' ? $request->idubigeo_nacimiento : 0,
                'idgenero' => $request->idgenero != 'null' ? $request->idgenero : 0,
                'idestadocivil' => $request->idestadocivil != 'null' ? $request->idestadocivil : 0,
                'idnivelestudio' => $request->idnivelestudio != 'null' ? $request->idnivelestudio : 0,
                'ocupacion' => $request->ocupacion,
                
                'numerotelefono' => $request->input('numerotelefono')!=null?$request->input('numerotelefono'):'',
                'email'          => $request->input('email')!=null ? $request->input('email') : '',
                'referencia' => $request->referencia,
                'idubigeo'       => $request->input('idubigeo')!='null'?$request->input('idubigeo'):0,
                'direccion'      => $request->input('direccion')!=null?$request->input('direccion'):'',
                //'mapa_latitud' => $request->domicilio_mapa_latitud,
                //'mapa_longitud' => $request->domicilio_mapa_longitud,
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);

        } 
        elseif ($request->input('view') == 'editpermiso') {
          
            $idestado = 2;
            $rules = [];
            $messages = [];
            if($request->input('estadoacceso')=='on'){
                $rules = [
                    'usuario' => 'required|alpha_dash',
                    'idrol' => 'required',
                    'estadoacceso' => 'required',
                ];
                $messages = [
                    'usuario.required' => 'El "Usuario" es Obligatorio.',
                    'usuario.alpha_dash' => 'El "Usuario" solo debe tener letras y/o número.',
                    'idrol.required' => 'El "Permiso" es Obligatorio.',
                ];
                $idestado = 1;
            }

            $this->validate($request,$rules,$messages);
            $newusuario = $request->input('usuario').'@'.$idtienda.'.com';

            $users = DB::table('users')->where('id','<>',$idusuario)->where('usuario',  )->first();
            if($users!=''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "Usuario" ya existe, ingreso otro.'
                ]);
            }

            if($request->input('password')!=''){
                DB::table('users')->whereId($idusuario)->update([
                    'usuario' => $newusuario,
                    'clave' => $request->input('password'),
                    'password' => Hash::make($request->input('password')),
                    'idestado' => $idestado,
                ]);
            }else{
                DB::table('users')->whereId($idusuario)->update([
                    'usuario' => $newusuario,
                    'idestado' => $idestado,
                ]);
            }

            if ( $request->input('idrol') != '') {

                $role_user = DB::table('role_user')->where('user_id',$idusuario)->limit(1)->first();

                if($role_user!=''){
                    DB::table('role_user')->where('role_user.user_id', $idusuario)->update([
                        'role_id' => $request->input('idrol'),
                    ]);
                }else{
                    DB::table('role_user')->insert([
                        'role_id' => $request->input('idrol'),
                        'user_id' => $idusuario,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                } 
            }

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idtienda, $idusuario)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if ($request->input('view') == 'eliminar') {
            /*$usuario = DB::table('users')->whereId($idusuario)->first();
            uploadfile_eliminar($usuario->imagen,'/public/backoffice/tienda/'.$idtienda.'/sistema/');
            */
            /*DB::table('role_user')
                ->where('user_id',$idusuario)
                ->delete();*/
            /*DB::table('users')
                ->where('id',$idusuario)
                ->where('idtienda',$idtienda)
                ->delete();*/
            DB::table('users')
                ->where('id',$idusuario)
                ->where('idtienda',$idtienda)
                ->update(['idestado'=>3]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                    'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
       
    }
}
