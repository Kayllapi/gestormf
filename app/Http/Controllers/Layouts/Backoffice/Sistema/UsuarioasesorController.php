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

class UsuarioasesorController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/usuarioasesor/tabla',[
                'tienda' => $tienda,
            ]);
        }
            
    }

    public function create(Request $request, $idtienda)
    {
        
    }

    public function store(Request $request, $idtienda)
    {
        
    }

    public function show(Request $request, $idtienda, $id)
    {
        if($id=='show_table'){
            $tienda = DB::table('tienda')->whereId($idtienda)->first(); 
          
            $where = [];
            if($request->input('columns')[4]['search']['value']!=''){
                $where[] = ['tipopersona.id',$request->input('columns')[4]['search']['value']];
            }
            if($request->input('columns')[5]['search']['value']!=''){
                $where[] = ['s_users_prestamo.idtipodocumento',$request->input('columns')[5]['search']['value']];
            }
            $usuarios = DB::table('users')
                ->join('tipopersona','tipopersona.id','=','users.idtipopersona')
                ->leftJoin('ubigeo','ubigeo.id','=','users.idubigeo')
                ->leftJoin('s_users_prestamo','s_users_prestamo.id_s_users','users.id')
                ->where('users.idestado',1)
                ->where('users.idtipousuario',2)
                ->where('users.idtienda',$idtienda)
                ->where('users.idasesor',Auth::user()->id)
                ->where('users.codigo','LIKE','%'.$request->input('columns')[0]['search']['value'].'%')
                ->where('users.identificacion','LIKE','%'.$request->input('columns')[1]['search']['value'].'%')
                ->where('users.nombrecompleto','LIKE','%'.$request->input('columns')[2]['search']['value'].'%')
                ->where('ubigeo.nombre','LIKE','%'.$request->input('columns')[3]['search']['value'].'%')
                ->where($where)
                ->select(
                    'users.*',
                    's_users_prestamo.db_idtipodocumento as tipodocumento_persona',
                    'tipopersona.nombre as tipopersonanombre',
                    'ubigeo.codigo as ubigeocodigo',
                    'ubigeo.nombre as ubigeonombre',
                )
                ->orderBy('users.id','desc')
                ->paginate($request->length,'*',null,($request->start/$request->length)+1);

            $tabla = [];
            foreach($usuarios as $value){
                
              $tabla[] = [
                  'id'              => $value->id,
                  'text'            => ($value->identificacion!=0?$value->identificacion.' - ':'').$value->nombrecompleto,
                  'codigo'          => $value->codigo,
                  'idtipopersona'   => $value->idtipopersona,
                  'tipodocumento'   => $value->tipodocumento_persona,
                  'persona'         => $value->tipopersonanombre,
                  'identificacion'  => $value->identificacion!=0?$value->identificacion:'',
                  'cliente'         => $value->nombrecompleto,
                  'telefono'        => $value->numerotelefono,
                  'direccion'       => $value->direccion,
                  'idubigeo'        => $value->idubigeo,
                  'ubigeo'          => $value->ubigeocodigo!=''?$value->ubigeocodigo.' - '.$value->ubigeonombre:'',
                  'opcion'          => [
                      [
                          'nombre'  => 'Editar',
                          'onclick' => '/'.$idtienda.'/usuario/'.$value->id.'/edit?view=editar',
                          'icono'   => 'edit'
                      ],
                      [
                          'nombre'  => 'Editar Ubicación',
                          'onclick' => '/'.$idtienda.'/usuario/'.$value->id.'/edit?view=ubicacion',
                          'icono'   => 'location-dot'
                      ],
                      [
                          'nombre'  => 'Ficha',
                          'onclick' => '/'.$idtienda.'/usuario/'.$value->id.'/edit?view=ficha',
                          'icono'   => 'list',
                            'size'    => 'modal-fullscreen'
                      ],
                      /*[
                          'nombre'  => 'Eliminar',
                          'onclick' => '/'.$idtienda.'/usuario/'.$value->id.'/edit?view=eliminar',
                          'icono'   => 'trash'
                      ]*/
                  ]
              ];
            }
            
            return response()->json([
                'start'           => $request->start,
                'draw'            => $request->draw,
                'recordsTotal'    => $request->length,
                'recordsFiltered' => $usuarios->total(),
                'data'            => $tabla,
            ]);
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {

    }

    public function update(Request $request, $idtienda, $idusuario)
    {

    }

    public function destroy(Request $request, $idtienda, $idusuario)
    {
 
       
    }
}
