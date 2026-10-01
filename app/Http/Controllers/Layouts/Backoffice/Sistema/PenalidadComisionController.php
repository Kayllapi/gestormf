<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class PenalidadComisionController extends Controller
{
    /**
     * Parametros (s_config) que administra esta pantalla y que si existen POR AGENCIA.
     *
     * Todo lo configurable por agencia vive en s_config (s_config.idtienda).
     *
     * Las penalidades por tipo de garantia (tipo_garantia.penalidad y
     * subtipo_garantia_noprendaria_ii.penalidad) ya NO son un unico valor global: esas dos
     * tablas siguen guardando el valor COMUN y la diferencia por agencia esta en
     * tipo_garantia_penalidad / subtipo_garantia_noprendaria_ii_penalidad (ver
     * database/sql/01102026120000create_tipo_garantia_penalidad.sql). Si la agencia no tiene
     * fila propia se usa el valor comun, asi que las agencias que nunca se configuraron
     * siguen viendo exactamente los mismos numeros que antes del cambio.
     */
    const PARAMETROS = [
        'dias_maximo_penalidad',
        'cargo_custodia_garantia',
        'penalidad_couta_simple',
        'penalidad_couta_compuesto',
        'dias_tolerancia',
        'penalidad_couta_simple_noprendaria',
        'penalidad_couta_compuesto_noprendaria',
        'dias_tolerancia_garantia',
        'tasa_moratoria',
        'tipo_cambio_dolar',
        'comision_gestion_garantia_cargo',
        'comision_gestion_garantia_convenio',
        'porcentaje_descuento_liquidacion',
        'porcentaje_precio_liquidacion',
    ];

    public function __construct()
    {
        $this->tipo_credito = DB::table('tipo_credito')->get();
    }
    public function index(Request $request,$idtienda)
    {
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

            return view(sistema_view().'/penalidadcomision/tabla',[
              'tienda' => $tienda,
              'agencias' => $agencias,
              'idtienda_seleccionada' => $idtienda_seleccionada,
            ]);
        }
            
    }

    /**
     * Agencias sobre las que el usuario puede ver y editar esta configuracion.
     *
     * La fuente de verdad es users_permiso: el usuario accede a una agencia si tiene ahi
     * un permiso activo (idestado=1), que es el mismo criterio con el que el sistema arma
     * el menu y el selector de agencia del MasterController. Un usuario puede tener permiso
     * en varias agencias a la vez (users_permiso.idsession solo marca cual esta activa),
     * por eso se toman todas y no solo la de la sesion.
     *
     * Un usuario sin ningun permiso registrado (tipicamente el admin del sistema, que no
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
     * Agencia activa del usuario (users_permiso.idsession=2): la que el sistema tiene
     * seleccionada. Es la que debe verse por defecto en el filtro.
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
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->view == 'registrar') {
            return view(sistema_view().'/creditoordinario/create',[
                'tienda' => $tienda,
                'tipo_credito' => $this->tipo_credito
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {

    }

    public function show(Request $request, $idtienda, $id)
    {



    }

    public function edit(Request $request, $idtienda, $id)
    {
        
      
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
      
      if($request->input('view') == 'editar') {
        $agencia = $this->agencia_seleccionada($request,$idtienda);
        abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');

        // No se llama authorizeRoles() a proposito: en esta base de datos el modelo de
        // roles esta roto (role_user apunta a un rol inexistente y rolesmodulo solo tiene
        // filas del rol 4), por lo que hasRole() da false para todos y la pantalla
        // quedaria con 401 siempre. El control de agencia lo hace agencias_permitidas().

        $tipo_garantia = DB::table('tipo_garantia')->get();
        $tipo_garantia_noprendaria = DB::table('subtipo_garantia_noprendaria_ii')
            ->join('subtipo_garantia_noprendaria','subtipo_garantia_noprendaria.id','subtipo_garantia_noprendaria_ii.idsubtipo_garantia_noprendaria')
            ->join('tipo_garantia_noprendaria','tipo_garantia_noprendaria.id','subtipo_garantia_noprendaria.idtipo_garantia_noprendaria')
            ->select(
              'subtipo_garantia_noprendaria_ii.*',
              'subtipo_garantia_noprendaria.nombre as subtipo_garantia_nombre',
              'tipo_garantia_noprendaria.nombre as tipo_garantia_nombre',
            )
            ->get();

        // Cada tipo de garantia lleva dos campos para la vista:
        //   - penalidad:       el valor que se usa para esta agencia (propio, o el comun si no tiene fila propia)
        //   - penalidad_comun: el valor comun (tipo_garantia.penalidad), que se muestra como
        //                       referencia para saber que valor se esta sobrescribiendo.
        // propio_agencia = 1 si la agencia tiene fila en tipo_garantia_penalidad.
        $penalidades    = tipo_garantia_penalidad($agencia->id);
        $overrides      = tipo_garantia_penalidad_overrides($agencia->id);
        foreach($tipo_garantia as $value){
            $value->penalidad_comun = $value->penalidad;
            $value->propio_agencia  = isset($overrides[$value->id]) ? 1 : 0;
            $value->penalidad       = $penalidades[$value->id]??$value->penalidad;
        }

        $penalidades_noprendaria   = subtipo_garantia_noprendaria_ii_penalidad($agencia->id);
        $overrides_noprendaria     = subtipo_garantia_noprendaria_ii_penalidad_overrides($agencia->id);
        foreach($tipo_garantia_noprendaria as $value){
            $value->penalidad_comun = $value->penalidad;
            $value->propio_agencia  = isset($overrides_noprendaria[$value->id]) ? 1 : 0;
            $value->penalidad       = $penalidades_noprendaria[$value->id]??$value->penalidad;
        }

        return view(sistema_view().'/penalidadcomision/edit',[
          'tienda' => $tienda,
          'agencia' => $agencia,
          'tipo_garantia' => $tipo_garantia,
          'tipo_garantia_noprendaria' => $tipo_garantia_noprendaria,
        ]);
      }

      if($request->input('view') == 'copiar') {
        $agencias = $this->agencias_permitidas();

        if($agencias->count()<2){
            abort(403,'Se necesitan al menos dos agencias administrables para copiar la configuración.');
        }

        return view(sistema_view().'/penalidadcomision/copiar',[
          'tienda' => $tienda,
          'agencias' => $agencias,
        ]);
      }
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        if($request->input('view') == 'editar') {
  
            $agencia = $this->agencia_seleccionada($request,$idtienda);
            abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');

            // A partir de aqui la configuracion se guarda sobre la agencia seleccionada
            // y no sobre la agencia de la ruta.
            $idtienda_config = $agencia->id;

            $cargo_custodia_garantia = $request->cargo_custodia_garantia ? 1 : 0;

            configuracion_update($idtienda_config,'dias_maximo_penalidad',$request->dias_maximo_penalidad);
            configuracion_update($idtienda_config,'cargo_custodia_garantia',$cargo_custodia_garantia);
            configuracion_update($idtienda_config,'penalidad_couta_simple',$request->penalidad_couta_simple);
            configuracion_update($idtienda_config,'penalidad_couta_compuesto',$request->penalidad_couta_compuesto);
            configuracion_update($idtienda_config,'dias_tolerancia',$request->dias_tolerancia);
            configuracion_update($idtienda_config,'penalidad_couta_simple_noprendaria',$request->penalidad_couta_simple_noprendaria);
            configuracion_update($idtienda_config,'penalidad_couta_compuesto_noprendaria',$request->penalidad_couta_compuesto_noprendaria);
            configuracion_update($idtienda_config,'dias_tolerancia_garantia',$request->dias_tolerancia_garantia);
            configuracion_update($idtienda_config,'tasa_moratoria',$request->tasa_moratoria);
            configuracion_update($idtienda_config,'tipo_cambio_dolar',$request->tipo_cambio_dolar);
            configuracion_update($idtienda_config,'comision_gestion_garantia_cargo',$request->comision_gestion_garantia_cargo);
            configuracion_update($idtienda_config,'comision_gestion_garantia_convenio',$request->comision_gestion_garantia_convenio);
            configuracion_update($idtienda_config,'porcentaje_descuento_liquidacion',$request->porcentaje_descuento_liquidacion);
            configuracion_update($idtienda_config,'porcentaje_precio_liquidacion',$request->porcentaje_precio_liquidacion);

            // Penalidad por tipo de garantia: se guarda como override de la agencia
            // (tipo_garantia_penalidad). Un input vacio borra el override y la agencia vuelve
            // al valor comun de tipo_garantia.penalidad. Se recorre el catalogo y no el
            // request para no crear filas de tipos que la agencia no tiene.
            //
            // Cada input viaja con la clave 'penalidad_tipogarantia'.$id del CATALOGO porque
            // forminput() (public/libraries/app/js/app.js) arma el FormData usando el atributo
            // id de cada input como nombre del campo, no el name. El id no lleva el idtienda
            // adentro a proposito: el idtienda sale de la agencia ya validada arriba.
            foreach(DB::table('tipo_garantia')->get() as $value){
                tipo_garantia_penalidad_update(
                    $idtienda_config,
                    $value->id,
                    $request->input('penalidad_tipogarantia'.$value->id)
                );
            }

            // Sub Tipo II de garantia no prendaria (mismo criterio, otra tabla).
            foreach(DB::table('subtipo_garantia_noprendaria_ii')->get() as $value){
                subtipo_garantia_noprendaria_ii_penalidad_update(
                    $idtienda_config,
                    $value->id,
                    $request->input('penalidad_subtipogarantia'.$value->id)
                );
            }

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente la configuración de '.$agencia->nombreagencia.'.'
            ]);
        }

        if($request->input('view') == 'copiar') {
            return $this->copiar_configuracion($request);
        }
    
    }

    /**
     * Copia la configuracion (s_config) de una agencia a otra, y tambien las penalidades por
     * tipo de garantia (tipo_garantia_penalidad / subtipo_garantia_noprendaria_ii_penalidad).
     *
     * Por cada parametro: si la agencia destino ya lo tiene lo actualiza, si no lo tiene
     * lo registra. Los parametros que la agencia origen no tenga configurado se omiten,
     * para no crear filas vacias en el destino.
     *
     * Igual con las penalidades por tipo de garantia: se copian solo las que la agencia
     * origen tenga como valor PROPIO, porque las demas ya valen el valor comun en destino.
     * Los overrides que solo existen en destino no se tocan.
     *
     * s_config no tiene indice unico en (nombre, idtienda), asi que la busqueda del
     * destino y el insert se hacen dentro de la misma transaccion.
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

        // Origen y destino ya se validé contra agencias_permitidas() arriba, asi que un
        // usuario sin el rol 1 no puede copiar hacia o desde una agencia ajena.

        $valores_origen = DB::table('s_config')
            ->where('idtienda',$idorigen)
            ->whereNull('idusers')
            ->whereIn('nombre',self::PARAMETROS)
            ->pluck('valor','nombre');

        $insertados   = 0;
        $actualizados = 0;
        $omitidos     = 0;

        DB::transaction(function () use ($valores_origen,$idorigen,$iddestino,&$insertados,&$actualizados,&$omitidos) {

            foreach(self::PARAMETROS as $nombre){

                $valor = $valores_origen->get($nombre);

                if($valor===null || $valor===''){
                    $omitidos++;
                    continue;
                }

                $existe = DB::table('s_config')
                    ->where('idtienda',$iddestino)
                    ->where('nombre',$nombre)
                    ->whereNull('idusers')
                    ->first();

                if($existe){
                    DB::table('s_config')->whereId($existe->id)->update(['valor'=>$valor]);
                    $actualizados++;
                }else{
                    DB::table('s_config')->insert([
                        'nombre'   => $nombre,
                        'valor'    => $valor,
                        'idusers'  => null,
                        'idtienda' => $iddestino,
                    ]);
                    $insertados++;
                }
            }

            // Penalidades por tipo de garantia: se replican los overrides de la agencia
            // origen. Ambas tablas tienen indice unico en (idtienda, id<tipo>), asi que el
            // insert del destino no puede duplicar fila.
            foreach([
                ['tabla'=>'tipo_garantia_penalidad','columna'=>'idtipo_garantia'],
                ['tabla'=>'subtipo_garantia_noprendaria_ii_penalidad','columna'=>'idsubtipo_garantia_noprendaria_ii'],
            ] as $penalidades_tipo_garantia){

                $overrides_origen = DB::table($penalidades_tipo_garantia['tabla'])
                    ->where('idtienda',$idorigen)
                    ->pluck('penalidad',$penalidades_tipo_garantia['columna']);

                foreach($overrides_origen as $idtipo=>$penalidad){

                    $existe = DB::table($penalidades_tipo_garantia['tabla'])
                        ->where('idtienda',$iddestino)
                        ->where($penalidades_tipo_garantia['columna'],$idtipo)
                        ->first();

                    if($existe){
                        DB::table($penalidades_tipo_garantia['tabla'])->whereId($existe->id)->update(['penalidad'=>$penalidad]);
                    }else{
                        DB::table($penalidades_tipo_garantia['tabla'])->insert([
                            'idtienda'                          => $iddestino,
                            $penalidades_tipo_garantia['columna'] => $idtipo,
                            'penalidad'                         => $penalidad,
                        ]);
                    }
                }
            }
        });

        return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se copiaron '.$insertados.' parametro(s) nuevos y se actualizaron '.$actualizados.' en '
                            .$destino->nombreagencia.'.'
        ]);
    }


    public function destroy(Request $request, $idtienda, $id)
    {
      
      if( $request->input('view') == 'eliminar' ){
        // acotado a la agencia: desde esta pantalla no se deben tocar los
        // productos de otras agencias
        DB::table('credito_prendatario')->where('id',$id)->where('idtienda',idtienda_actual())->delete();
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha elimino correctamente.'
        ]);
      }
      
    
    }
}
