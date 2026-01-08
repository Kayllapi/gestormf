<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class AgenciaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/agencia/tabla',[
                'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->view == 'registrar') {
            return view(sistema_view().'/agencia/create',[
                'tienda' => $tienda,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
            
            $rules = [
                'nombrecomercial' => 'required',   
                'telefono' => 'required',     
                'idubigeo' => 'required',   
                'direccion' => 'required',         
            ];
          
            $messages = [
                'nombrecomercial.required' => 'El "Nombre Comercial" es Obligatorio.',
                'telefono.required' => 'El "Teléfono" es Obligatorio.',
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
 
              
            DB::table('s_agencia')->insert([
               'fecharegistro' => Carbon::now(),
               'ruc' => '',
               'nombrecomercial' => $request->input('nombrecomercial'),
               'razonsocial' => '',
               'logo' => '',
               'direccion' => $request->input('direccion'),
               'representante_dni' => '',
               'representante_nombre' => '',
               'representante_apellidos' => '',
               'representante_cargo' => '',
               'facturacion_usuario' => '',
               'facturacion_clave' => '',
               'facturacion_certificado' => '',
               'idestadofacturacion' => 1,
               'idubigeo' => $request->input('idubigeo'),
               'idtienda' => $idtienda,
               'telefono' => $request->input('telefono'),
               'idestado' => 1,
            ]);
            
            json_agencia($idtienda);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($id == 'showbuscaridentificacion'){
            return consultaDniRuc($request->input('buscar_identificacion'), $request->input('tipo_persona'));
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $s_agencia = DB::table('s_agencia')
            ->leftJoin('ubigeo','ubigeo.id','s_agencia.idubigeo')
            ->where('s_agencia.id',$id)
            ->select(
              's_agencia.*',
              'ubigeo.nombre as ubigeonombre'
            )
            ->first();
      
        if($request->input('view') == 'editar') {
            return view(sistema_view().'/agencia/edit',[
              'tienda' => $tienda,
              's_agencia' => $s_agencia,
            ]);
        }
        elseif($request->input('view') == 'facturacion') {
            return view(sistema_view().'/agencia/facturacion',[
              'tienda' => $tienda,
              's_agencia' => $s_agencia,
            ]);
        }
        elseif($request->input('view') == 'eliminar') {
            return view(sistema_view().'/agencia/delete',[
              'tienda' => $tienda,
              's_agencia' => $s_agencia,
            ]);
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'editar') {
            $rules = [
                'nombrecomercial' => 'required',   
                'telefono' => 'required',     
                'idubigeo' => 'required',   
                'direccion' => 'required',         
            ];
          
            $messages = [
                'nombrecomercial.required' => 'El "Nombre Comercial" es Obligatorio.',
                'telefono.required' => 'El "Teléfono" es Obligatorio.',
                'direccion.required' => 'La "Dirección" es Obligatorio.',
                'idubigeo.required' => 'El "Ubicación (Ubigeo)" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $agencia = DB::table('s_agencia')->whereId($id)->first();
          
            
          

            DB::table('s_agencia')->whereId($id)->update([
                'nombrecomercial' => $request->input('nombrecomercial'),
                'direccion' => $request->input('direccion'),
                'telefono' => $request->input('telefono'),
                'idubigeo' => $request->input('idubigeo'),
            ]);

          
            json_agencia($idtienda); 
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        else if($request->input('view') == 'facturacion'){
            
            if($request->input('idestadofacturacion')==1){
                $rules = [
                    'facturacion_usuario' => 'required', 
                    'facturacion_clave' => 'required',          
                ];
                $messages = [
                    'facturacion_usuario.required' => 'El "Usuario" es Obligatorio.',
                    'facturacion_clave.required' => 'El "Clave" es Obligatorio.',
                ];
                $this->validate($request,$rules,$messages);
            }
          
            
          
            $agencia = DB::table('s_agencia')->whereId($id)->first();
          
            if($request->input('idestadofacturacion')==1){
                

                if($agencia->facturacion_certificado==''){
                    if($request->file('facturacion_certificado')==null) {
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El certificado es obligatorio.'
                        ]);
                    }
                }
            }
    
          
            if($request->input('idestadofacturacion')==1){
                $facturacion_certificado = $agencia->facturacion_certificado;
                if($request->facturacion_certificado!=null){
                    
                    foreach($request->file('facturacion_certificado') as $value){
                        if ($value->isValid()) { 
                            if($request->input('facturacion_usuario')=='MODDATOS' && $request->input('facturacion_clave')=='moddatos'){
                                $rutaarchivo = '/public/backoffice/tienda/'.$idtienda.'/sunat/produccion/certificado/';
                            }else{
                                $rutaarchivo = '/public/backoffice/tienda/'.$idtienda.'/sunat/produccion/certificado/';
                            }

                            uploadfile_eliminar($agencia->facturacion_certificado,$rutaarchivo);

                            if(file_exists(getcwd().$rutaarchivo.$facturacion_certificado) && $facturacion_certificado!='') {
                                unlink(getcwd().$rutaarchivo.$facturacion_certificado);
                            }

                            $facturacion_certificado =  'certificado_'.$agencia->ruc.'.pem';
                            $value->move(getcwd().$rutaarchivo, $facturacion_certificado);
                        }
                    } 
                }
                    
                DB::table('s_agencia')->whereId($id)->update([
                
                    'facturacion_usuario' => $request->input('facturacion_usuario'),
                    'facturacion_clave' => $request->input('facturacion_clave'),
                    'facturacion_certificado' => $facturacion_certificado,
                    'idestadofacturacion' => $request->input('idestadofacturacion'),
                    
                    'id_token' => $request->input('id_token'),
                    'clave_token' => $request->input('clave_token'),
                    'validation_id_token'       => $request->input('validation_id_token'),
                    'validation_clave_token'    => $request->input('validation_clave_token')

                ]);
            }else{
                uploadfile_eliminar($agencia->facturacion_certificado,'/public/backoffice/tienda/'.$idtienda.'/sunat/produccion/certificado/');
                uploadfile_eliminar($agencia->facturacion_certificado,'/public/backoffice/tienda/'.$idtienda.'/sunat/beta/certificado/');
                DB::table('s_agencia')->whereId($id)->update([
                    
                    'facturacion_usuario' => '',
                    'facturacion_clave' => '',
                    'facturacion_certificado' => '',
                    'idestadofacturacion' => 2,
                ]);
              
            }
          
            json_agencia($idtienda); 
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
            
    }


    public function destroy(Request $request, $idtienda, $id)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
            
            DB::table('s_agencia')
                ->where('id',$id)
                ->where('idtienda',$idtienda)
                ->update([
                  'fechaeliminado' => Carbon::now(),
                  'idestado'=>2
                ]);
          
            json_agencia($idtienda); 
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
