<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class AgenciaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        json_agencia($idtienda,$request->name_modulo);
        return view('layouts/backoffice/tienda/nuevosistema/agencia/index',[
            'tienda' => $tienda,
        ]);
    }
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        return view('layouts/backoffice/tienda/nuevosistema/agencia/create',[
            'tienda' => $tienda
        ]);
    }
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
            $rules = [
                'ruc' => 'required|numeric|digits:11',   
                'nombrecomercial' => 'required',   
                'razonsocial' => 'required',     
                'idubigeo' => 'required',   
                'direccion' => 'required',            
            ];
            $messages = [
                'ruc.required' => 'El "RUC" es Obligatorio.',
                'ruc.numeric'   => 'El "RUC" debe ser Númerico.',
                'ruc.digits'   => 'El "RUC" debe ser de 11 Digitos.',
                'nombrecomercial.required' => 'El "Nombre Comercial" es Obligatorio.',
                'razonsocial.required' => 'La "Razón Comercial" es Obligatorio.',
                'direccion.required' => 'La "Dirección" es Obligatorio.',
                'idubigeo.required' => 'El "Ubicación (Ubigeo)" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $agencia = DB::table('s_agencia')
                ->where('ruc',$request->input('ruc'))
                ->where('idtienda',$idtienda)
                ->first();
            if($agencia!='' and $request->input('ruc')!=0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "RUC" ya existe, Ingrese Otro por favor.'
                ]);
            }
  
            $imagen = uploadfile('','',$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/sistema/');
          
            DB::table('s_agencia')->insert([
               'fecharegistro' => Carbon::now(),
               'ruc' => $request->input('ruc'),
               'nombrecomercial' => $request->input('nombrecomercial'),
               'razonsocial' => $request->input('razonsocial'),
               'logo' => $imagen,
               'direccion' => $request->input('direccion'),
               'facturacion_serie' => 0,
               'facturacion_correlativoinicial' => 0,
               'facturacion_usuario' => '',
               'facturacion_clave' => '',
               'facturacion_certificado' => '',
               'idestadofacturacion' => 2,
               'idubigeo' => $request->input('idubigeo'),
               'idtienda' => $idtienda,
               'idestado' => 1,
            ]);
            
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if($id == 'showbuscaridentificacion'){
            return consultaDniRuc($request->input('buscar_identificacion'), $request->input('tipo_persona'));
        }
        elseif ($id == 'show-moduloactualizar'){
            json_agencia($idtienda,$request->name_modulo);
        }
    }

    public function edit(Request $request, $idtienda, $idagencia)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $s_agencia = DB::table('s_agencia')
            ->leftJoin('ubigeo','ubigeo.id','s_agencia.idubigeo')
            ->where('s_agencia.id',$idagencia)
            ->select(
              's_agencia.*',
              'ubigeo.nombre as ubigeonombre'
            )
            ->first();
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'editar') {
          
          return view('layouts/backoffice/tienda/nuevosistema/agencia/edit',[
            's_agencia' => $s_agencia,
            'tienda' => $tienda
          ]);
          
        }
        elseif($request->input('view') == 'detalle') {
          return view('layouts/backoffice/tienda/nuevosistema/agencia/detalle',[
            'tienda' => $tienda,
            's_agencia' => $s_agencia,
          ]);
          
        }
        elseif($request->input('view') == 'eliminar') {
          
          return view('layouts/backoffice/tienda/nuevosistema/agencia/delete',[
            'tienda' => $tienda,
            's_agencia' => $s_agencia,
          ]);
          
        }
    }

    public function update(Request $request, $idtienda, $s_idagencia)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'editar') {
            $rules = [
                'ruc' => 'required',   
                'nombrecomercial' => 'required',   
                'razonsocial' => 'required',       
                'idubigeo' => 'required', 
                'direccion' => 'required',    
                'idestadofacturacion' => 'required',           
            ];
          
            if($request->input('idestadofacturacion')==1){
                $rules = array_merge($rules,[
                    'facturacion_serie' => 'required',    
                    'facturacion_correlativoinicial' => 'required',  
                    'facturacion_usuario' => 'required', 
                    'facturacion_clave' => 'required', 
                    'facturacion_certificado' => 'required', 
                ]);
            }
          
            $messages = [
                'ruc.required' => 'El "RUC" es Obligatorio.',
                'nombrecomercial.required' => 'El "Nombre Comercial" es Obligatorio.',
                'razonsocial.required' => 'La "Razón Comercial" es Obligatorio.',
                'direccion.required' => 'La "Dirección" es Obligatorio.',
                'idestadofacturacion.required' => 'El "Estado de Facturación" es Obligatorio.',
                'facturacion_serie.required' => 'La "Serie" es Obligatorio.',
                'facturacion_correlativoinicial.required' => 'El "Correlativo" es Obligatorio.',
                'facturacion_usuario.required' => 'El "Usuario" es Obligatorio.',
                'facturacion_clave.required' => 'El "Clave" es Obligatorio.',
                'facturacion_certificado.required' => 'El "Certificado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $agencia = DB::table('s_agencia')->whereId($s_idagencia)->first();
          
            if($request->input('idestadofacturacion')==1){
                if($request->input('facturacion_serie')<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Serie debe ser mayor a 0.'
                    ]);
                }
                elseif($request->input('facturacion_correlativoinicial')<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Correlativo debe ser mayor a 0.'
                    ]);
                }

                if($agencia->facturacion_certificado==''){
                    if($request->file('facturacion_certificado')==null) {
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El certificado es obligatorio.'
                        ]);
                    }
                }
            }
          
            $imagen = uploadfile($agencia->logo,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/sistema/');
              

            if($request->input('idestadofacturacion')==1){
              
                $facturacion_certificado = $agencia->facturacion_certificado;
                if($request->file('facturacion_certificado')!='') {
                    if($request->file('facturacion_certificado')->isValid()) {
                      
                        if($request->input('facturacion_usuario')=='MODDATOS' && $request->input('facturacion_clave')=='moddatos'){
                            $rutaarchivo = '/public/backoffice/tienda/'.$idtienda.'/sunat/produccion/certificado/';
                        }else{
                            $rutaarchivo = '/public/backoffice/tienda/'.$idtienda.'/sunat/produccion/certificado/';
                        }
                      
                        uploadfile_eliminar($agencia->facturacion_certificado,$rutaarchivo);
                      
                        if(file_exists(getcwd().$rutaarchivo.$facturacion_certificado) && $facturacion_certificado!='') {
                            unlink(getcwd().$rutaarchivo.$facturacion_certificado);
                        }
                      
                        $facturacion_certificado = $request->file('facturacion_certificado')->getClientOriginalName();
                        $request->file('facturacion_certificado')->move(getcwd().$rutaarchivo, $facturacion_certificado);
                    }
                }
              
                //$facturacion_certificado = uploadfile($agencia->facturacion_certificado,$request->input('facturacion_certificadoant'),$request->file('facturacion_certificado'),'/public/backoffice/tienda/'.$idtienda.'/sunat/produccion/certificado/');
                DB::table('s_agencia')->whereId($s_idagencia)->update([
                    'ruc' => $request->input('ruc'),
                    'nombrecomercial' => $request->input('nombrecomercial'),
                    'razonsocial' => $request->input('razonsocial'),
                    'logo' => $imagen,
                    'direccion' => $request->input('direccion'),
                    'facturacion_serie' => $request->input('facturacion_serie'),
                    'facturacion_correlativoinicial' => $request->input('facturacion_correlativoinicial'),
                    'facturacion_usuario' => $request->input('facturacion_usuario'),
                    'facturacion_clave' => $request->input('facturacion_clave'),
                    'facturacion_certificado' => $facturacion_certificado,
                    'idestadofacturacion' => $request->input('idestadofacturacion'),
                    'idubigeo' => $request->input('idubigeo'),
                ]);
            }else{
                uploadfile_eliminar($agencia->facturacion_certificado,'/public/backoffice/tienda/'.$idtienda.'/sunat/produccion/certificado/');
                uploadfile_eliminar($agencia->facturacion_certificado,'/public/backoffice/tienda/'.$idtienda.'/sunat/beta/certificado/');
                DB::table('s_agencia')->whereId($s_idagencia)->update([
                    'ruc' => $request->input('ruc'),
                    'nombrecomercial' => $request->input('nombrecomercial'),
                    'razonsocial' => $request->input('razonsocial'),
                    'logo' => $imagen,
                    'direccion' => $request->input('direccion'),
                    'facturacion_serie' => 0,
                    'facturacion_correlativoinicial' => 0,
                    'facturacion_usuario' => '',
                    'facturacion_clave' => '',
                    'facturacion_certificado' => '',
                    'idestadofacturacion' => 2,
                    'idubigeo' => $request->input('idubigeo'),
                ]);
              
            }
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
            
    }


    public function destroy(Request $request, $idtienda, $s_idagencia)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
            /*$s_agencia = DB::table('s_agencia')->whereId($s_idagencia)->first();
            uploadfile_eliminar($s_agencia->logo,'/public/backoffice/tienda/'.$idtienda.'/nuevosistema/');
            DB::table('s_agencia')
                ->where('idtienda',$idtienda)
                ->whereId($s_idagencia)
                ->delete();*/
            
            
            DB::table('s_agencia')
                ->where('id',$s_idagencia)
                ->where('idtienda',$idtienda)
                ->update([
                  'fechaeliminado' => Carbon::now(),
                  'idestado'=>3
                ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
