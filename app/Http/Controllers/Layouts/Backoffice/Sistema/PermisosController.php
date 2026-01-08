<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use Auth;
use Hash;
use DB;
use Image;
use PDF;

class PermisosController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/permisos/tabla',[
                'tienda' => $tienda,
            ]);
        }
            
    }

    public function create(Request $request, $idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->view == 'registrar') {

            return view(sistema_view().'/permisos/create',[
                'tienda' => $tienda,
            ]);
        }
        else if($request->view == 'reporte'){
            return view(sistema_view().'/permisos/reporte',[
                'tienda' => $tienda,
            ]);
        }
    }

    public function store(Request $request, $idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if ($request->input('view') == 'registrar') {
            
            $rules['rango']        =  'required';
            $rules['nombre']        =  'required';
            $messages['rango.required']    = 'El campo "Rango" es obligatorio.';
            $messages['nombre.required']    = 'El campo "Nombre" es obligatorio.';

            $this->validate($request,$rules,$messages);
            
            DB::table('permiso')->insert([
                'rango'    => $request->input('rango'),
                'nombre'    => $request->input('nombre'),
                'idtienda'  => $idtienda,
            ]);

            return response()->json([
                'resultado'           => 'CORRECTO',
                'mensaje'             => 'El Permiso fue registrado correctamente'
            ]);

        } 
    
    }

    public function show(Request $request, $idtienda, $id)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        if($id=='show_table'){
            $tienda = DB::table('tienda')->whereId($idtienda)->first(); 

            $permisos = DB::table('permiso')
                    // ->where('permiso.idtienda', $idtienda)
                    ->select(
                        'permiso.*'
                    )
                    ->orderBy('permiso.rango','asc')
                    ->paginate($request->length,'*',null,($request->start/$request->length)+1);

            $tabla = [];
            foreach($permisos as $value){
                
              $tabla[]=[
                  'id'      => $value->id,
                  'rango'  => $value->rango,
                  'nombre'  => $value->nombre,
                  'opcion' => [
                     [
                      'nombre' => 'Editar',
                      'onclick' => '/'.$idtienda.'/permisos/'.$value->id.'/edit?view=editar',
                      'icono' => 'edit',
                    ]
                  ],
              ];
            }
            
            return response()->json([
                'start'           => $request->start,
                'draw'            => $request->draw,
                'recordsTotal'    => $request->length,
                'recordsFiltered' => $permisos->total(),
                'data'            => $tabla,
            ]);
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        

        if($request->input('view') == 'editar') {
            $permiso = DB::table('permiso')
                    // ->where('permiso.idtienda', $idtienda)
                    ->where('permiso.id',$id)
                    ->select(
                        'permiso.*'
                    )
                    ->first();

            return view(sistema_view().'/permisos/edit',[
                'permiso'  => $permiso,
                'tienda'    => $tienda,
            ]);
          
        } 
        
    }

    public function update(Request $request, $idtienda, $idpermiso)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        if ($request->input('view') == 'editar') {

            $rules['rango']        =  'required';
            $rules['nombre']        =  'required';
            $messages['rango.required']    = 'El campo "Rango" es obligatorio.';
            $messages['nombre.required']    = 'El campo "Nombre" es obligatorio.';
          
            DB::table('permiso')->whereId($idpermiso)->update([
                'rango'    => $request->input('rango'),
                'nombre'    => $request->input('nombre'),
            ]);

            DB::table('permisoacceso')
                ->where('idpermiso',$idpermiso)
                ->delete();

            $list = explode(',',$request->input('idmodulos'));
            //$idmodulos = '';
            for ($i=1; $i < count($list); $i++) { 
                //$idmodulos = $idmodulos.$list[$i];
                DB::table('permisoacceso')->insert([
                    'idpermiso' => $idpermiso,
                    'idmodulo'  => $list[$i]
                ]);
            }

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Cliente actualizado de lista negra.'
            ]);
        }
        
    }

    public function destroy(Request $request, $idtienda, $idusuario)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
      
       
    }
}
