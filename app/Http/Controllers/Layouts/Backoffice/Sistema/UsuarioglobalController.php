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

class UsuarioglobalController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->input('view') == 'tabla'){
            $agencias = DB::table('tienda')->get();
            return view(sistema_view().'/usuarioglobal/tabla',[
                'tienda'          => $tienda,
                'agencias' => $agencias,
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

            $idagencia = request('idagencia') ?? 0;

            $where = [];
            if($request->input('columns')[4]['search']['value']!=''){
                $where[] = ['tipopersona.id',$request->input('columns')[4]['search']['value']];
            }
            if($request->input('columns')[5]['search']['value']!=''){
                $where[] = ['s_users_prestamo.idtipodocumento',$request->input('columns')[5]['search']['value']];
            }

            if ($idagencia!=0) {
                $where[] = ['users.idtienda',$idagencia];
            }

            // ultimo credito generado (como cliente o aval, excluyendo eliminados)
            $ultimo_credito = '(select max(credito.fecha) from credito
                                where (credito.idcliente = users.id or credito.idaval = users.id)
                                and credito.estado != "ELIMINADO")';

            $usuarios = DB::table('users')
                ->join('tipopersona','tipopersona.id','=','users.idtipopersona')
                ->leftJoin('ubigeo','ubigeo.id','=','users.idubigeo')
                ->leftJoin('s_users_prestamo','s_users_prestamo.id_s_users','users.id')
                ->where('users.idestado',1)
                ->where('users.idtipousuario',2)
                ->where('users.idasesor',0)
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
                    DB::raw($ultimo_credito.' as ultimacredito'),
                )
                ->orderBy('users.id','desc')
                ->paginate($request->length,'*',null,($request->start/$request->length)+1);

            $tabla = [];
            foreach($usuarios as $value){

                // dias sin un credito generado (sin credito se cuenta desde el registro del cliente)
                $fecha_referencia = $value->ultimacredito!=null?$value->ultimacredito:$value->fechamodificacion;
                $dias_sin_credito = $fecha_referencia?Carbon::parse($fecha_referencia)->diffInDays(Carbon::now()):null;

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
                  'ultimacredito'   => $value->ultimacredito!=null?date_format(date_create($value->ultimacredito),'d/m/Y'):'',
                  'dias'            => $dias_sin_credito!==null?(int)round($dias_sin_credito):null,
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
        elseif ($id=='show_table_consulta'){
            // Usuarios (clientes/garantes) de la tienda asignados al asesor
            $usuarios = DB::table('users')
                ->where('users.idestado',1)
                ->where('users.idtipousuario',2)
                ->where('users.idtienda',$idtienda)
                ->where('users.idasesor',Auth::user()->id)
                ->select(
                    'users.id',
                    'users.codigo',
                    'users.identificacion',
                    'users.nombrecompleto',
                    'users.created_at',
                )
                ->orderBy('users.id','desc')
                ->get();

            // Ultimos creditos solicitados por cada usuario (como cliente o como aval)
            $idsusuarios = $usuarios->pluck('id');

            $creditos = DB::table('credito')
                ->where(function($query) use ($idsusuarios){
                    $query->whereIn('credito.idcliente',$idsusuarios)
                        ->orWhereIn('credito.idaval',$idsusuarios);
                })
                ->where('credito.estado','!=','ELIMINADO')
                ->select(
                    'credito.id',
                    'credito.idcliente',
                    'credito.idaval',
                    'credito.fecha',
                    'credito.estado',
                    'credito.monto_solicitado',
                    'credito.fecha_desembolso',
                )
                ->orderBy('credito.fecha','desc')
                ->get();

            // agrupar los creditos por cada usuario (cliente y aval)
            $creditos_usuario = [];
            foreach($creditos as $value){
                $participantes = array_unique(array_filter([$value->idcliente,$value->idaval]));
                foreach($participantes as $participante){
                    $creditos_usuario[$participante][] = $value;
                }
            }

            $listado = [];
            foreach($usuarios as $usuario){

                $creditos_usuario_ = collect($creditos_usuario[$usuario->id]??[])->take(3)->values();
                $ultimo_credito = $creditos_usuario_->first();

                // Dias sin un credito generado (sin credito se cuenta desde el registro del usuario)
                $fecha_referencia = $ultimo_credito!=null?$ultimo_credito->fecha:$usuario->created_at;

                $listado[] = [
                    'idusuario'          => $usuario->id,
                    'codigo'             => $usuario->codigo,
                    'identificacion'     => $usuario->identificacion,
                    'cliente'            => $usuario->nombrecompleto,
                    'ultimo_credito'     => $ultimo_credito,
                    'ultimos_creditos'   => $creditos_usuario_,
                    'dias_sin_credito'   => $fecha_referencia?Carbon::parse($fecha_referencia)->diffInDays(Carbon::now()):null,
                    'ultimo_desembolso'  => $ultimo_credito!=null?$ultimo_credito->fecha_desembolso:null,
                ];
            }

            return response()->json([
                'data' => $listado,
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
