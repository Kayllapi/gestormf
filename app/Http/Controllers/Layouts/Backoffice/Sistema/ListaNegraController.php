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

class ListaNegraController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/listanegra/tabla',[
                'tienda' => $tienda,
            ]);
        }
            
    }

    public function create(Request $request, $idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->view == 'registrar') {

            return view(sistema_view().'/listanegra/create',[
                'tienda' => $tienda,
            ]);
        }
        else if($request->view == 'reporte'){
            return view(sistema_view().'/listanegra/reporte',[
                'tienda' => $tienda,
            ]);
        }
    }

    public function store(Request $request, $idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if ($request->input('view') == 'registrar') {
            
            $rules['idcliente']     =  'required';
            $rules['motivo']        =  'required';
            $messages['idcliente.required'] = 'El campo "Cliente" es obligatorio.';
            $messages['motivo.required']    = 'El campo "Motivo" es obligatorio.';

            $this->validate($request,$rules,$messages);
            $cliente = DB::table('users')->whereId($request->input('idcliente'))->first();
            DB::table('s_listanegra')->insert([
                'idcliente'         => $request->input('idcliente'),
                'motivo'            => $request->input('motivo'),
                'idresponsable'     => Auth::user()->id,
                'fecharegistro'     => Carbon::now(),
                'idtienda'          => $idtienda,
                'idestado'          => '1',
                'db_cliente'        => $cliente ? $cliente->nombrecompleto : ''
            ]);

            return response()->json([
                'resultado'           => 'CORRECTO',
                'mensaje'             => 'Cliente registrado en lista negra.'
            ]);

        } 
    
    }

    public function show(Request $request, $idtienda, $id)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        if($id=='show_table'){
            $tienda = DB::table('tienda')->whereId($idtienda)->first(); 
            $idsucursal = Auth::user()->idsucursal;
 
            /*$where = [];
            if($request->input('columns')[4]['search']['value']!=''){
                $where[] = ['tipopersona.id',$request->input('columns')[4]['search']['value']];
            }
            if($request->input('columns')[5]['search']['value']!=''){
                $where[] = ['s_users_prestamo.idtipodocumento',$request->input('columns')[5]['search']['value']];
            }*/
          
            $listanegra = DB::table('s_listanegra')
                    ->join('users', 'users.id','s_listanegra.idcliente')
                    ->join('users as usuario', 'usuario.id','s_listanegra.idresponsable')
                    ->where('users.identificacion','LIKE','%'.$request->input('columns')[0]['search']['value'].'%')
                    ->where('users.nombrecompleto','LIKE','%'.$request->input('columns')[1]['search']['value'].'%')
                    ->where('s_listanegra.motivo','LIKE','%'.$request->input('columns')[2]['search']['value'].'%')
                    ->where('s_listanegra.fecharegistro','LIKE','%'.$request->input('columns')[4]['search']['value'].'%')
                    ->where('usuario.codigo','LIKE','%'.$request->input('columns')[3]['search']['value'].'%')
                    //->where($where)
                    ->select(
                        's_listanegra.*',
                        'users.identificacion',
                        'usuario.codigo as usuariocodigo'
                    )
                    ->orderBy('s_listanegra.id','desc')
                    ->paginate($request->length,'*',null,($request->start/$request->length)+1);

            $tabla = [];
            foreach($listanegra as $value){
                
                $estado = '';
                switch($value->idestado){
                    case '1':
                        $estado = 'EN LISTA';
                            break;
                    case '2':
                            $estado = 'FUERA DE LISTA';
                            break;
                }
            
              
              $tabla[]=[
                  'id'              => $value->id,
                  'identificacion'  => $value->identificacion,
                  'cliente'         => $value->db_cliente,
                  'motivo'          => $value->motivo,
                  'fecha'           => date_format(date_create($value->fecharegistro),'d-m-Y'),
                  'estado'          => $estado,
                  'usuariocodigo'          => $value->usuariocodigo,
                  'opcion' => [
                     [
                      'nombre' => 'Retirar de Lista Negra',
                      'onclick' => '/'.$idtienda.'/listanegra/'.$value->id.'/edit?view=editar',
                      'size' => 'modal-sm',
                      'icono' => 'refresh',
                    ]
                  ],
              ];
            }
            return response()->json([
                'start'           => $request->start,
                'draw'            => $request->draw,
                'recordsTotal'    => $request->length,
                'recordsFiltered' => $listanegra->total(),
                'data'            => $tabla,
            ]);
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        

        if($request->input('view') == 'editar') {
            
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[1,2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();
                
            $listanegra = DB::table('s_listanegra')
                    ->join('users', 'users.id','s_listanegra.idcliente')
                    ->where('s_listanegra.idtienda', $idtienda)
                    ->where('s_listanegra.id',$id)
                    ->select(
                        's_listanegra.*',
                        'users.identificacion'
                    )
                    ->first();
             $responsable = DB::table('users')->whereId($listanegra->idresponsable)->first();

            return view(sistema_view().'/listanegra/edit',[
                'listanegra' => $listanegra,
                'tienda'     => $tienda,
                'usuarios'     => $usuarios,
                'responsable'     => $responsable,
            ]);
          
        } 
        else if( $request->input('view') == 'reportepdf'){
            $listanegra = DB::table('s_listanegra')
                    ->join('users', 'users.id','s_listanegra.idresponsable')
                    ->where('s_listanegra.idtienda', $idtienda)
                    ->select(
                        's_listanegra.*',
                        'users.identificacion',
                        'users.codigo'
                    )
                    ->orderBy('s_listanegra.id','desc')
                    ->get();

            $pdf = PDF::loadView(sistema_view().'/listanegra/reportepdf',[
                'listanegra' => $listanegra,
                'tienda'     => $tienda
            ]); 
            $pdf->setPaper('A4','LANDSCAPE');
            return $pdf->stream('LISTA_NEGRA.pdf');
        }
    }

    public function update(Request $request, $idtienda, $idusuario)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        if ($request->input('view') == 'editar') {

            // $rules['idestado']     =  'required';
            $rules['motivo']        =  'required';
            $rules['idresponsable']        =  'required';
            $rules['responsableclave']        =  'required';
            // $messages['idestado.required'] = 'El campo "Estado" es obligatorio.';
            $messages['motivo.required']    = 'El campo "Motivo" es obligatorio.';
            $messages['idresponsable.required']    = 'El campo "Responsable" es obligatorio.';
            $messages['responsableclave.required']    = 'El campo "Contraseña" es obligatorio.';

            $this->validate($request,$rules,$messages);
            
            
            $usuario = DB::table('users')
                ->where('users.id',$request->idresponsable)
                ->where('users.clave',$request->responsableclave)
                ->first();
            $idresponsable = 0;
            if($usuario==''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El usuario y/o la contraseña es incorrecta!!.'
                ]);
                $idresponsable = $usuario->id;
            }
            
            if( !is_null($request->input('idestado')) ){

                DB::table('s_listanegra')->whereId($idusuario)->delete();

                // DB::table('s_listanegra')->whereId($idusuario)->update([
                //     'motivo'        => $request->input('motivo'),
                //     'fecharegistro' => Carbon::now(),
                //     'idestado'      => $request->input('idestado')
                // ]);
            }else{
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Debe marcar la casilla de "QUITAR DE LISTA"'
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
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
       
    }
}
