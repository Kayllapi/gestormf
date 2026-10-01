<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use PDF;
use Carbon\Carbon;

class GiroEconomicoController extends Controller
{
    public function __construct()
    {
        // tipo_giro_economico (Comercio / Servicio / Produccion) es un catalogo
        // GLOBAL de 3 filas: clasifica el giro y lo referencian los creditos ya
        // emitidos, asi que no se replica por agencia.
        $this->tipo_giro_economico = DB::table('tipo_giro_economico')->get();
    }
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            $agencias = $this->agencias_permitidas();

            // Por defecto se muestra la agencia activa del usuario; si no esta entre las
            // suyas se cae a la de la ruta. Si tampoco, a la primera disponible.
            $idtienda_seleccionada = $this->idtienda_activa();

            if(!$agencias->firstWhere('id',$idtienda_seleccionada)){
                $idtienda_seleccionada = (int) $tienda->id;
            }
            if(!$agencias->firstWhere('id',$idtienda_seleccionada)){
                $idtienda_seleccionada = (int) ($agencias->first()->id ?? 0);
            }

            return view(sistema_view().'/giroeconomico/tabla',[
              'tienda' => $tienda,
              'agencias' => $agencias,
              'idtienda_seleccionada' => $idtienda_seleccionada,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        if($request->view == 'registrar') {
            $agencia = $this->agencia_seleccionada($request,$idtienda);

            return view(sistema_view().'/giroeconomico/create',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'tipo_giro_economico' => $this->tipo_giro_economico,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
            $rules = [
                'nombre' => 'required',         
                'porcentaje' => 'required',        
                'idtipo_giro_economico' => 'required',        
            ];
          
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'porcentaje.required' => 'El "Porcentaje" es Obligatorio.',
                'idtipo_giro_economico.required' => 'El "Tipo de Giro Economico" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            // el giro es por agencia: se guarda sobre la agencia seleccionada en el
            // filtro, no sobre la de la ruta ni la activa del usuario.
            $agencia = $this->agencia_seleccionada($request,$idtienda);
            abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');
          
            DB::table('giro_economico_evaluacion')->insert([
               'idtienda'   => (int) $agencia->id,
               'idtipo_giro_economico' => $request->input('idtipo_giro_economico'),
               'nombre'        => $request->input('nombre'),
               'porcentaje'   => $request->input('porcentaje'),
               'estado'   => $request->input('estado'),
            ]);

          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente en '.$agencia->nombreagencia.'.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id=='show_table'){
            $agencia = $this->agencia_seleccionada($request,$idtienda);
            abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');

            // se inicializa siempre: si no se manda ningun filtro el where se
            // armaba sobre una variable inexistente y la consulta reventaba.
            $where = [];
            if($request->input('idtipo_giro_economico') != ''){
              $where[] = ['giro_economico_evaluacion.idtipo_giro_economico', $request->input('idtipo_giro_economico')];  
            }
            if($request->input('estado') != ''){
              $where[] = ['giro_economico_evaluacion.estado', $request->input('estado')];  
            }
            $giro = DB::table('giro_economico_evaluacion')
                            ->join('tipo_giro_economico','tipo_giro_economico.id','giro_economico_evaluacion.idtipo_giro_economico')
                            ->where('giro_economico_evaluacion.idtienda',(int) $agencia->id)
                            ->where($where)
                            ->select(
                                'giro_economico_evaluacion.*',
                                'tipo_giro_economico.nombre as nombretipogiroeconomico'
              
                            )
                            ->orderBy('giro_economico_evaluacion.id','ASC')
                            ->get();
  
            $html = '';
            foreach($giro as $key => $value){

                $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data(this)'>
                              <td>".($key+1)."</td>
                              <td>{$value->nombretipogiroeconomico}</td>
                              <td>{$value->nombre}</td>
                              <td>{$value->porcentaje}</td>
                              <td>{$value->estado}</td>
                          </tr>";
            }
            return array(
              'html' => $html
            );
        }
        
    }

    public function edit(Request $request, $idtienda, $id)
    {
        
      
      $tienda = DB::table('tienda')->whereId($idtienda)->first();

      $agencia = $this->agencia_seleccionada($request,$idtienda);
      abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');
      $idtienda_destino = (int) $agencia->id;

      // Estas tres vistas no trabajan sobre un giro concreto: la URL llega con
      // id=0 como marcador, asi que se resuelven ANTES de buscar el giro (si se
      // buscara, el 404 saltaria siempre).
      if($request->input('view') == 'copiar'){
        $agencias = $this->agencias_permitidas();

        if($agencias->count()<2){
          abort(403,'Se necesitan al menos dos agencias administrables para copiar el catalogo de giros.');
        }

        return view(sistema_view().'/giroeconomico/copiar',[
          'tienda' => $tienda,
          'agencias' => $agencias,
        ]);
      }
      else if( $request->input('view') == 'container' ){
        return view(sistema_view().'/giroeconomico/container',[
          'tienda' => $tienda,
          'agencia' => $agencia,
          'idtienda' => $idtienda,
          'tipo_giro_economico' => $this->tipo_giro_economico,
        ]);
      }
      else if( $request->input('view') == 'pdf' ){

        // ver la nota de $where mas arriba: sin filtros la consulta reventaba.
        $where = [];
        if($request->input('idtipo_giro_economico') != ''){
          $where[] = ['giro_economico_evaluacion.idtipo_giro_economico', $request->input('idtipo_giro_economico')];  
        }
        if($request->input('estado') != ''){
          $where[] = ['giro_economico_evaluacion.estado', $request->input('estado')];  
        }
        $giros = DB::table('giro_economico_evaluacion')
                        ->join('tipo_giro_economico','tipo_giro_economico.id','giro_economico_evaluacion.idtipo_giro_economico')
                        ->where('giro_economico_evaluacion.idtienda',$idtienda_destino)
                        ->where($where)
                        ->select(
                            'giro_economico_evaluacion.*',
                            'tipo_giro_economico.nombre as nombretipogiroeconomico'

                        )
                        ->orderBy('giro_economico_evaluacion.id','ASC')
                        ->get();
        $pdf = PDF::loadView(sistema_view().'/giroeconomico/pdf',[
          'tienda' => $tienda,
          'agencia' => $agencia,
          'giros' => $giros,
          'idtienda' => $idtienda,
          'tipo_giro_economico' => $this->tipo_giro_economico,
        ]); 
        $pdf->setPaper('A4');
        // $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('GIRO ECONOMICO.pdf');
      }
  
      // el giro tiene que ser de la agencia seleccionada: el id viaja en la URL
      // y se valida aqui, no solo al guardar.
      $giro = DB::table('giro_economico_evaluacion')
                      ->where('giro_economico_evaluacion.idtienda',$idtienda_destino)
                      ->where('giro_economico_evaluacion.id',$id)
                      ->select(
                          'giro_economico_evaluacion.*',
                      )
                      ->orderBy('giro_economico_evaluacion.id','desc')
                      ->first();

      abort_unless($giro,404,'El giro economico no existe en '.$agencia->nombreagencia.'.');
  
      if($request->input('view') == 'editar') {

        return view(sistema_view().'/giroeconomico/edit',[
          'tienda' => $tienda,
          'agencia' => $agencia,
          'giro' => $giro,
          'idtienda' => $idtienda,
          'tipo_giro_economico' => $this->tipo_giro_economico,
        ]);
      }
      else if($request->input('view') == 'eliminar'){
        return view(sistema_view().'/giroeconomico/delete',[
          'tienda' => $tienda,
          'agencia' => $agencia,
          'giro' => $giro,
          'idtienda' => $idtienda,
        ]);
      }
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        if($request->input('view') == 'editar') {

            $rules = [
                'nombre' => 'required',         
                'porcentaje' => 'required',        
                'idtipo_giro_economico' => 'required',        
            ];
          
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'porcentaje.required' => 'El "Porcentaje" es Obligatorio.',
                'idtipo_giro_economico.required' => 'El "Tipo de Giro Economico" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            $agencia = $this->agencia_seleccionada($request,$idtienda);
            abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');
            $idtienda_destino = (int) $agencia->id;

            // no se puede editar un giro de otra agencia
            if(!DB::table('giro_economico_evaluacion')->where('id',$id)->where('idtienda',$idtienda_destino)->exists()){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El giro economico no pertenece a '.$agencia->nombreagencia.'.'
                ]);
            }
          
            DB::table('giro_economico_evaluacion')->where('id',$id)->where('idtienda',$idtienda_destino)->update([
               'idtipo_giro_economico' => $request->input('idtipo_giro_economico'),
               'nombre'        => $request->input('nombre'),
               'porcentaje'   => $request->input('porcentaje'),
               'estado'        => $request->input('estado'),
            ]);
          
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente en '.$agencia->nombreagencia.'.'
            ]);
        }

        if($request->input('view') == 'copiar') {
            return $this->copiar_configuracion($request);
        }
    
    }

    /**
     * Agencias sobre las que el usuario puede ver y editar este catalogo.
     *
     * Mismo criterio que en Penalidades y Comisiones y en Tasas Activas: se accede
     * a una agencia si el usuario tiene ahi un permiso activo
     * (users_permiso.idestado=1). Un usuario puede tener permiso en varias agencias
     * a la vez (idsession solo marca cual esta activa), asi que se toman todas.
     *
     * Un usuario sin ningun permiso registrado (tipicamente superadmin, que no
     * tiene filas en users_permiso) ve todas las agencias.
     */
    protected function agencias_permitidas()
    {
        $idtiendas = DB::table('users_permiso')
            ->where('idusers',Auth::id())
            ->where('idestado',1)
            ->distinct()
            ->pluck('idtienda');

        $query = DB::table('tienda')->where('idestado',1);

        if($idtiendas->isNotEmpty()){
            $query->whereIn('id',$idtiendas);
        }

        return $query->orderBy('nombreagencia')->get();
    }

    /**
     * Agencia activa del usuario (users_permiso.idsession=2): la que el sistema
     * tiene seleccionada. Es la que debe verse por defecto en el filtro.
     */
    protected function idtienda_activa()
    {
        $permiso = user_permiso();

        return $permiso ? (int) $permiso->idtienda : 0;
    }

    /**
     * Agencia objetivo de la operacion: la que se envio en idagencia o, si no se
     * envio, la activa. Una agencia enviada explicitamente que el usuario no tiene
     * permiso se rechaza en lugar de cair silenciosamente en la suya.
     */
    protected function agencia_seleccionada(Request $request,$idtienda)
    {
        $agencias = $this->agencias_permitidas();
        $idagencia = (int) $request->input('idagencia');

        if($idagencia>0){
            $agencia = $agencias->firstWhere('id',$idagencia);
            abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');
            return $agencia;
        }

        $agencia = $agencias->firstWhere('id',$this->idtienda_activa())
                ?: $agencias->firstWhere('id',(int) $idtienda);

        return $agencia ?: $agencias->first();
    }

    /**
     * Copia el catalogo de giros de una agencia a otra.
     *
     * Un giro se empareja por TIPO + NOMBRE, que es lo que lo identifica en la
     * pantalla (el catalogo no tiene codigo propio). Ese par no se repite dentro
     * de una agencia, asi que la correspondencia es univoca.
     *
     * Por cada giro de origen: si el destino ya tiene ese par, se actualizan el
     * margen y el estado; si no, se registra. Agrega y actualiza, no reemplaza: los
     * giros que solo existan en destino no se tocan.
     *
     * Un giro NUNCA se borra: credito_evaluacion_cualitativa,
     * credito_evaluacion_resumida y credito_cuantitativa_ingreso_adicional guardan
     * el id del giro que se eligio en el credito.
     */
    protected function copiar_configuracion(Request $request)
    {
        $agencias = $this->agencias_permitidas();

        $idorigen  = (int) $request->input('idorigen');
        $iddestino = (int) $request->input('iddestino');

        if($idorigen<=0 || $iddestino<=0){
            return response()->json([
                'resultado' => 'ERROR',
                'mensaje'   => 'Debe seleccionar la agencia origen y la agencia destino.'
            ]);
        }

        if($idorigen==$iddestino){
            return response()->json([
                'resultado' => 'ERROR',
                'mensaje'   => 'La agencia origen y la agencia destino deben ser distintas.'
            ]);
        }

        $origen  = $agencias->firstWhere('id',$idorigen);
        $destino = $agencias->firstWhere('id',$iddestino);

        if(!$origen || !$destino){
            abort(403,'No tiene permisos sobre alguna de las agencias seleccionadas.');
        }

        $insertados   = 0;
        $actualizados = 0;

        DB::transaction(function () use ($idorigen,$iddestino,&$insertados,&$actualizados) {

            $giros_origen = DB::table('giro_economico_evaluacion')
                                ->where('idtienda',$idorigen)
                                ->orderBy('id')
                                ->get();

            foreach($giros_origen as $giro_origen){

                $giro_destino = DB::table('giro_economico_evaluacion')
                                    ->where('idtienda',$iddestino)
                                    ->where('idtipo_giro_economico',$giro_origen->idtipo_giro_economico)
                                    ->where('nombre',$giro_origen->nombre)
                                    ->first();

                if($giro_destino){
                    DB::table('giro_economico_evaluacion')->where('id',$giro_destino->id)->update([
                        'porcentaje' => $giro_origen->porcentaje,
                        'estado'     => $giro_origen->estado,
                    ]);
                    $actualizados++;
                    continue;
                }

                DB::table('giro_economico_evaluacion')->insert([
                    'idtienda'              => $iddestino,
                    'idtipo_giro_economico' => $giro_origen->idtipo_giro_economico,
                    'nombre'                => $giro_origen->nombre,
                    'porcentaje'            => $giro_origen->porcentaje,
                    'estado'                => $giro_origen->estado,
                ]);
                $insertados++;
            }
        });

        return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se copiaron '.$insertados.' giro(s) nuevo(s) y se actualizaron '.$actualizados.' en '
                            .$destino->nombreagencia.'.'
        ]);
    }

    public function destroy(Request $request, $idtienda, $id)
    {
//         $request->user()->authorizeRoles($request->path(),$idtienda);
      if( $request->input('view') == 'eliminar' ){
        $agencia = $this->agencia_seleccionada($request,$idtienda);
        abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');

        // no se puede borrar un giro de otra agencia
        DB::table('giro_economico_evaluacion')->where('id',$id)->where('idtienda',(int) $agencia->id)->delete();
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha elimino correctamente.'
        ]);
      }
      
    
    }
}
