<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class SucursalController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/sucursal/tabla',[
                'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->view == 'registrar') {
            return view(sistema_view().'/sucursal/create',[
                'tienda' => $tienda,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
            $rules = [
                'nombreagencia' => 'required',    
                'nombre' => 'required',    
                'idubigeo' => 'required',   
                'direccion' => 'required',          
            ];
          
            $messages = [
                'nombreagencia.required' => 'La "Agencia" es Obligatorio.',
                'nombre.required' => 'El "Nombre Comercial" es Obligatorio.',
                'direccion.required' => 'La "Dirección" es Obligatorio.',
                'idubigeo.required' => 'El "Ubicación (Ubigeo)" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('tienda')->insert([
               'fecharegistro'      => Carbon::now(),
               'nombreagencia'      => $request->input('nombreagencia'),
               'nombre'             => $request->input('nombre'),
               'representante'      => $request->input('representante'),
               'ruc'                => $request->input('ruc'),
               'tipo_empresa'       => $request->input('tipo_empresa'),
               'password_agencia'   => '',
               'direccion'          => $request->input('direccion'),
               'numerotelefono'     => $request->input('telefono'),
               'paginaweb'          => $request->input('paginaweb')!=''?$request->input('paginaweb'):'',
               'idubigeo'           => $request->input('idubigeo'),
               'password_compraventa' => '',
               'idestado'           => 1,
            ]);

            
            json_sucursal($idtienda);
          
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
    }

    public function edit(Request $request, $idtienda, $id)
    {
        
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($id)->first();
        if($request->input('view') == 'editar') {
            
            return view(sistema_view().'/sucursal/edit',[
              'tienda' => $tienda,
              'idtienda' => $idtienda,
            ]);
        }
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        if($request->input('view') == 'editar') {

            $rules = [
                'nombreagencia' => 'required',    
                'nombre' => 'required',    
                'idubigeo' => 'required',   
                'direccion' => 'required',  
                'compra_limitemaximo_caja' => 'required',  
                'compra_limitemaximo_reserva' => 'required',       
                'credito_limitemaximo_caja' => 'required',       
                'credito_limitemaximo_reserva' => 'required',                   
            ];
          
            $messages = [
                'nombreagencia.required' => 'La "Agencia" es Obligatorio.',
                'nombre.required' => 'El "Nombre Comercial" es Obligatorio.',
                'direccion.required' => 'La "Dirección" es Obligatorio.',
                'idubigeo.required' => 'El "Ubicación (Ubigeo)" es Obligatorio.',
                'compra_limitemaximo_caja.required' => 'El "Limites de fondo de Créditos para caja" es Obligatorio.',
                'compra_limitemaximo_reserva.required' => 'El "Limites de fondo de Créditos para reserva" es Obligatorio.',
                'credito_limitemaximo_caja.required' => 'El "Limites de fondo de Ventas para caja" es Obligatorio.',
                'credito_limitemaximo_reserva.required' => 'El "Limites de fondo de Ventas para reserva" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
            $tienda = DB::table('tienda')->whereId($request->idtienda)->first();
            $firma = uploadfile($tienda->firma,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/tienda/'.$request->idtienda.'/sistema/');
            $logo = uploadfile($tienda->imagen,$request->input('imagen-logoant'),$request->file('imagen-logo'),'/public/backoffice/tienda/'.$request->idtienda.'/sistema/');
            DB::table('tienda')->whereId($request->idtienda)->update([
                'nombre'             => $request->input('nombre'),
                'nombreagencia'      => $request->input('nombreagencia'),
                'representante'      => $request->input('representante'),
                'ruc'                => $request->input('ruc'),
                'tipo_empresa'       => $request->input('tipo_empresa'),
                'firma'              => $firma,
                'imagen'             => $logo,
                'password_agencia'   => '',
                'direccion'          => $request->input('direccion'),
                'numerotelefono'     => $request->input('telefono'),
                'paginaweb'          => $request->input('paginaweb')!=''?$request->input('paginaweb'):'',
                'password_compraventa' => '',
                'idubigeo'           => $request->input('idubigeo'),
                'compra_limitemaximo_caja'          => $request->input('compra_limitemaximo_caja'),
                'compra_limitemaximo_reserva'          => $request->input('compra_limitemaximo_reserva'),
                'credito_limitemaximo_caja'          => $request->input('credito_limitemaximo_caja'),
                'credito_limitemaximo_reserva'          => $request->input('credito_limitemaximo_reserva'),
            ]);
            
          
            json_sucursal($idtienda); 
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
    
    }


    public function destroy(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
    
    }
}
