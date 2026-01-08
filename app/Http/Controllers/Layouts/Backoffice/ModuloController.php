<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class ModuloController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$request->user()->authorizeRoles($request->path());
      
        $where = [];
        $where[] = ['modulo.nombre','LIKE','%'.$request->input('modulonombre').'%'];
        $where[] = ['modulo.idmodulo',0];
      
        $modulos = DB::table('modulo')
            ->where($where)
            ->select(
                'modulo.*'
            )
            ->orderBy('orden','asc')
            ->get();
      
        return view('layouts/backoffice/modulo/index',[
            'modulos' => $modulos
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //$request->user()->authorizeRoles($request->path());

        if($request->input('view')=='registrar'){
            return view('layouts/backoffice/modulo/create');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$request->user()->authorizeRoles($request->path());

        if($request->input('view')=='create') {
            $rules = [
                'nombre' => 'required',
                'orden' => 'required',
                'idestado' => 'required',
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'orden.required' => 'El "Orden" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('modulo')->insertGetId([
                'orden' => $request->input('orden'),
                'icono' => $request->input('icono')!=null?$request->input('icono'):'',
                'nombre' => $request->input('nombre'),
                'imagen' => '',
                'vista' => '',
                'controlador' => '',
                'idmodulo' => 0,
                'idestado' => $request->input('idestado'),
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha registrado correctamente.'
            ]);
        }
        elseif($request->input('view')=='createsubmodulo') {
          
            $rules = [
                'nombre' => 'required',
                'orden' => 'required',
                'idestado' => 'required',
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'orden.required' => 'El "Orden" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('modulo')->insertGetId([
                'orden' => $request->input('orden'),
                'icono' => $request->input('icono')!=null?$request->input('icono'):'',
                'nombre' => $request->input('nombre'),
                'imagen' => '',
                'vista' => $request->input('vista')!=null?$request->input('vista'):'',
                'controlador' => $request->input('controlador')!=null?$request->input('controlador'):'',
                'idmodulo' => $request->input('idmodulo'),
                'idestado' => $request->input('idestado'),
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha registrado correctamente.'
            ]);
        }
        elseif($request->input('view')=='createsubsubmodulo') {
          
            $rules = [
                'nombre' => 'required',
                'orden' => 'required',
                'idestado' => 'required',
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'orden.required' => 'El "Orden" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $imagen = uploadfile('','',$request->file('imagen'),'/public/backoffice/sistema/modulo/',140,140);
          
            DB::table('modulo')->insertGetId([
                'orden' => $request->input('orden'),
                'icono' => $request->input('icono')!=null?$request->input('icono'):'',
                'nombre' => $request->input('nombre'),
                'imagen' => '$imagen',
                'vista' => $request->input('vista')!=null?$request->input('vista'):'',
                'controlador' => $request->input('controlador')!=null?$request->input('controlador'):'',
                'idmodulo' => $request->input('idmodulo'),
                'idestado' => $request->input('idestado'),
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha registrado correctamente.'
            ]);
        }
        elseif($request->input('view')=='createsistemamodulo') {
          
            $rules = [
                'nombre' => 'required',
                'orden' => 'required',
                'idestado' => 'required',
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'orden.required' => 'El "Orden" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $imagen = uploadfile('','',$request->file('imagen'),'/public/backoffice/sistema/modulo/',140,140);
          
            DB::table('modulo')->insertGetId([
                'orden' => $request->input('orden'),
                'icono' => $request->input('icono')!=null?$request->input('icono'):'',
                'nombre' => $request->input('nombre'),
                'imagen' => $imagen,
                'vista' => $request->input('vista')!=null?$request->input('vista'):'',
                'controlador' => $request->input('controlador')!=null?$request->input('controlador'):'',
                'idmodulo' => $request->input('idmodulo'),
                'idestado' => $request->input('idestado'),
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha registrado correctamente.'
            ]);
        }
        elseif($request->input('view')=='createsistemamoduloopcion') {
          
            $rules = [
                'nombre' => 'required',
                'orden' => 'required',
                'opcion' => 'required',
                'idestado' => 'required',
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'orden.required' => 'El "Orden" es Obligatorio.',
                'opcion.required' => 'La "Opción" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $imagen = uploadfile('','',$request->file('imagen'),'/public/backoffice/sistema/modulo/',140,140);
          
            DB::table('modulo')->insertGetId([
                'orden' => $request->input('orden'),
                'icono' => $request->input('icono')!=null?$request->input('icono'):'',
                'nombre' => $request->input('nombre'),
                'imagen' => $imagen,
                'opcion' => $request->input('opcion'),
                'vista' => $request->input('vista'),
                'controlador' => '',
                'idmodulo' => $request->input('idmodulo'),
                'idestado' => $request->input('idestado'),
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha registrado correctamente.'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //$request->user()->authorizeRoles($request->path());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        //$request->user()->authorizeRoles($request->path());

        if($request->input('view')=='editar'){
            $modulos = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/edit',[
                'modulo' => $modulos
            ]);
        }elseif($request->input('view')=='registrarsubmodulo'){
            $modulo = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/createsubmodulo',[
                'modulo' => $modulo
            ]);
        }elseif($request->input('view')=='registrarsubsubmodulo'){
            $modulo = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/createsubsubmodulo',[
                'modulo' => $modulo
            ]);
        }elseif($request->input('view')=='registrarsistemamodulo'){
            $modulo = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/createsistemamodulo',[
                'modulo' => $modulo
            ]);
        }elseif($request->input('view')=='registrarsistemamoduloopcion'){
            $modulo = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/createsistemamoduloopcion',[
                'modulo' => $modulo
            ]);
        }elseif($request->input('view')=='editarsubmodulo'){
            $modulos = DB::table('modulo')
                ->where('idmodulo',0)
                ->orderBy('orden','asc')
                ->get();
            $modulo = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/editsubmodulo',[
                'modulos' => $modulos,
                'modulo' => $modulo
            ]);
        }elseif($request->input('view')=='editarsubsubmodulo'){
            $modulos = DB::table('modulo')
                ->where('idmodulo',0)
                ->orderBy('orden','asc')
                ->get();
            $modulo = DB::table('modulo')->whereId($id)->first();
            $categorias = DB::table('categoria')->get();
            return view('layouts/backoffice/modulo/editsubsubmodulo',[
                'modulos' => $modulos,
                'modulo' => $modulo,
                'categorias' => $categorias,
            ]);
        }elseif($request->input('view')=='editarsistemamodulo'){
            $modulos = DB::table('modulo')
                ->where('idmodulo',0)
                ->orderBy('orden','asc')
                ->get();
            $modulo = DB::table('modulo')->whereId($id)->first();
            $categorias = DB::table('categoria')->get();
          
            return view('layouts/backoffice/modulo/editsistemamodulo',[
                'modulos' => $modulos,
                'modulo' => $modulo,
                'categorias' => $categorias,
            ]);
        }elseif($request->input('view')=='editarsistemamoduloopcion'){
            $modulos = DB::table('modulo')
                ->where('idmodulo',0)
                ->orderBy('orden','asc')
                ->get();
            $modulo = DB::table('modulo')->whereId($id)->first();
            $categorias = DB::table('categoria')->get();
          
            return view('layouts/backoffice/modulo/editsistemamoduloopcion',[
                'modulos' => $modulos,
                'modulo' => $modulo,
                'categorias' => $categorias,
            ]);
        }else if ($request->input('view')=='eliminar') {
            $modulos = DB::table('modulo')
                ->where('idmodulo',0)
                ->orderBy('orden','asc')
                ->get();
            $modulo = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/delete',[
                'modulos' => $modulos,
                'modulo' => $modulo
            ]);
        }else if ($request->input('view')=='eliminarsubmodulo') {
            $modulos = DB::table('modulo')
                ->where('idmodulo',0)
                ->orderBy('orden','asc')
                ->get();
            $modulo = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/deletesubmodulo',[
                'modulos' => $modulos,
                'modulo' => $modulo
            ]);
        }else if ($request->input('view')=='eliminarsubsubmodulo') {
            $modulos = DB::table('modulo')
                ->where('idmodulo',0)
                ->orderBy('orden','asc')
                ->get();
            $modulo = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/deletesubsubmodulo',[
                'modulos' => $modulos,
                'modulo' => $modulo
            ]);
        }else if ($request->input('view')=='eliminarsistemamodulo') {
            $modulos = DB::table('modulo')
                ->where('idmodulo',0)
                ->orderBy('orden','asc')
                ->get();
            $modulo = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/deletesistemamodulo',[
                'modulos' => $modulos,
                'modulo' => $modulo
            ]);
        }else if ($request->input('view')=='eliminarsistemamoduloopcion') {
            $modulos = DB::table('modulo')
                ->where('idmodulo',0)
                ->orderBy('orden','asc')
                ->get();
            $modulo = DB::table('modulo')->whereId($id)->first();
            return view('layouts/backoffice/modulo/deletesistemamoduloopcion',[
                'modulos' => $modulos,
                'modulo' => $modulo
            ]);
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
        //$request->user()->authorizeRoles($request->path());

        if($request->input('view')=='edit') {
            $rules = [
                'nombre' => 'required',
                'orden' => 'required',
                'idestado' => 'required',
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'orden.required' => 'El "Orden" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('modulo')->whereId($id)->update([
                'orden' => $request->input('orden'),
                'icono' => $request->input('icono')!=null?$request->input('icono'):'',
                'nombre' => $request->input('nombre'),
                'idestado' => $request->input('idestado'),
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif($request->input('view')=='editsubmodulo') {
            $rules = [
                'idmodulo' => 'required',
                'nombre' => 'required',
                'orden' => 'required',
                'idestado' => 'required',
            ];
            $messages = [
                'idmodulo.required' => 'El "Módulo" es Obligatorio.',
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'orden.required' => 'El "Orden" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('modulo')->whereId($id)->update([
                'orden' => $request->input('orden'),
                'icono' => $request->input('icono')!=null?$request->input('icono'):'',
                'nombre' => $request->input('nombre'),
                'vista' => $request->input('vista')!=null?$request->input('vista'):'',
                'controlador' => $request->input('controlador')!=null?$request->input('controlador'):'',
                'idmodulo' => $request->input('idmodulo'),
                'idestado' => $request->input('idestado'),
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif($request->input('view')=='editsubsubmodulo') {
            $rules = [
                'idmodulo' => 'required',
                'nombre' => 'required',
                'orden' => 'required',
                'idestado' => 'required',
            ];
            $messages = [
                'idmodulo.required' => 'El "Módulo" es Obligatorio.',
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'orden.required' => 'El "Orden" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $modulo = DB::table('modulo')->whereId($id)->first();
            $imagen = uploadfile($modulo->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/sistema/modulo/',140,140);
          
            DB::table('modulo')->whereId($id)->update([
                'orden'       => $request->input('orden'),
                'icono'       => $request->input('icono')!=null?$request->input('icono'):'',
                'nombre'      => $request->input('nombre'),
                'imagen'      => $imagen,
                'vista' => $request->input('vista')!=null?$request->input('vista'):'',
                'controlador' => $request->input('controlador')!=null?$request->input('controlador'):'',
                'idmodulo'    => $request->input('idmodulo'),
                'idestado'    => $request->input('idestado'),
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif($request->input('view')=='editsistemamodulo') {
            $rules = [
                'idmodulo' => 'required',
                'nombre' => 'required',
                'orden' => 'required',
                'idestado' => 'required',
            ];
            $messages = [
                'idmodulo.required' => 'El "Módulo" es Obligatorio.',
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'orden.required' => 'El "Orden" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $modulo = DB::table('modulo')->whereId($id)->first();
            $imagen = uploadfile($modulo->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/sistema/modulo/',140,140);
          
            DB::table('modulo')->whereId($id)->update([
                'orden' => $request->input('orden'),
                'icono' => $request->input('icono')!=null?$request->input('icono'):'',
                'nombre' => $request->input('nombre'),
                'imagen' => $imagen,
                'vista' => $request->input('vista')!=null?$request->input('vista'):'',
                'controlador' => $request->input('controlador')!=null?$request->input('controlador'):'',
                'idmodulo' => $request->input('idmodulo'),
                'idestado' => $request->input('idestado'),
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif($request->input('view')=='editsistemamoduloopcion') {
            $rules = [
                'idmodulo' => 'required',
                'nombre' => 'required',
                'orden' => 'required',
                'opcion' => 'required',
                'idestado' => 'required',
            ];
            $messages = [
                'idmodulo.required' => 'El "Módulo" es Obligatorio.',
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'orden.required' => 'El "Orden" es Obligatorio.',
                'opcion.required' => 'La "Opción" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $modulo = DB::table('modulo')->whereId($id)->first();
            $imagen = uploadfile($modulo->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/sistema/modulo/',140,140);
          
            DB::table('modulo')->whereId($id)->update([
                'orden' => $request->input('orden'),
                'icono' => $request->input('icono')!=null?$request->input('icono'):'',
                'nombre' => $request->input('nombre'),
                'imagen' => $imagen,
                'vista' => $request->input('vista'),
                'opcion' => $request->input('opcion'),
                'idmodulo' => $request->input('idmodulo'),
                'idestado' => $request->input('idestado'),
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha actualizado correctamente.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //$request->user()->authorizeRoles($request->path());
        
        if($request->input('view')=='deletemodulo'){
            $modulo = DB::table('modulo')->whereId($id)->first();
            uploadfile_eliminar($modulo->imagen,'/public/backoffice/sistema/modulo/');
            DB::table('modulo')->whereId($id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.'
            ]);
        }elseif($request->input('view')=='deletesubmodulo'){
            $modulo = DB::table('modulo')->whereId($id)->first();
            uploadfile_eliminar($modulo->imagen,'/public/backoffice/sistema/modulo/');
            DB::table('modulo')->whereId($id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.'
            ]);
        }elseif($request->input('view')=='deletesubsubmodulo'){
            $modulo = DB::table('modulo')->whereId($id)->first();
            uploadfile_eliminar($modulo->imagen,'/public/backoffice/sistema/modulo/');
            DB::table('modulo')->whereId($id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.'
            ]);
        }elseif($request->input('view')=='deletesistemamodulo'){
            $modulo = DB::table('modulo')->whereId($id)->first();
            uploadfile_eliminar($modulo->imagen,'/public/backoffice/sistema/modulo/');
            DB::table('modulo')->whereId($id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.'
            ]);
        }elseif($request->input('view')=='deletesistemamodulo'){
            $modulo = DB::table('modulo')->whereId($id)->first();
            uploadfile_eliminar($modulo->imagen,'/public/backoffice/sistema/modulo/');
            DB::table('modulo')->whereId($id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.'
            ]);
        }elseif($request->input('view')=='deletesistemamoduloopcion'){
            DB::table('modulo')->whereId($id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.'
            ]);
        }
            
    }
}
