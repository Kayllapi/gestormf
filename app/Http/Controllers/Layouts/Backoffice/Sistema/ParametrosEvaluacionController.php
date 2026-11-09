<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class ParametrosEvaluacionController extends Controller
{
    /**
     * Parametros (s_config) que administra esta pantalla y que existen POR AGENCIA.
     * Mismo criterio que en Penalidades y Comisiones: todo lo configurable por agencia
     * vive en s_config (s_config.idtienda).
     *
     * El capital asignado a cada agencia (tienda.capital_agencia, tabla de abajo en el
     * formulario) no es un parametro de este catalogo: se edita directo sobre cada
     * agencia y no se copia entre agencias.
     */
    const PARAMETROS = [
        'provision_gastos_familiares',
        'porcentaje_min_muestra',
        'reporte_institucional',
        'capital_asignado',
        'rango_menor',
        'rango_tope',
        'ciclo_negocio_maximo',
        'tope_vinculacion_riesgo',
        'relacion_couta_ingreso',
        'relacion_cuota_venta',
        'rango_diferencia',
        'rango_tope_dependiente',
        'entidades_maxima',
        'activar_tem',
        'activar_tcem',
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

            return view(sistema_view().'/parametrosevaluacion/tabla',[
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

      // Estas views no trabajan sobre una agencia ya resuelta por agencia_seleccionada():
      // se resuelven antes, igual que en Penalidades y Comisiones y Giro Economico.
      if($request->input('view') == 'copiar') {
        $agencias = $this->agencias_permitidas();

        if($agencias->count()<2){
            abort(403,'Se necesitan al menos dos agencias administrables para copiar la configuración.');
        }

        return view(sistema_view().'/parametrosevaluacion/copiar',[
          'tienda' => $tienda,
          'agencias' => $agencias,
        ]);
      }

      $agencia = $this->agencia_seleccionada($request,$idtienda);
      abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');

      // La asignacion de capital es un valor propio de cada agencia (no un parametro
      // de este catalogo), por eso la tabla siempre lista TODAS las agencias y no
      // se filtra por la agencia seleccionada en el filtro de arriba.
      $agencias = DB::table('tienda')
        ->orderBy('tienda.nombre','ASC')
        ->get();


      if($request->input('view') == 'editar') {


        return view(sistema_view().'/parametrosevaluacion/edit',[
          'tienda' => $tienda,
          'agencia' => $agencia,
          'agencias' => $agencias,
        ]);
      }

    }

    public function update(Request $request, $idtienda, $id)
    {
        if($request->input('view') == 'editar') {
            $agencia = $this->agencia_seleccionada($request,$idtienda);
            abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');
            $idtienda_config = $agencia->id;

            $capital = json_decode($request->input('capital'), true);
            foreach($capital as $value){
              DB::table('tienda')->whereId($value['id'])->update([
                'capital_agencia' => $value['capital']
              ]);
            }

            $activar_tem = $request->activar_tem ? 1 : 0;
            $activar_tcem = $request->activar_tcem ? 1 : 0;

            configuracion_update($idtienda_config,'provision_gastos_familiares',$request->provision_gastos_familiares);
            configuracion_update($idtienda_config,'porcentaje_min_muestra',$request->porcentaje_min_muestra);
            configuracion_update($idtienda_config,'reporte_institucional',$request->reporte_institucional);
            configuracion_update($idtienda_config,'capital_asignado',$request->capital_asignado);
            configuracion_update($idtienda_config,'rango_menor',$request->rango_menor);
            configuracion_update($idtienda_config,'rango_tope',$request->rango_tope);
            configuracion_update($idtienda_config,'ciclo_negocio_maximo',$request->ciclo_negocio_maximo);
            configuracion_update($idtienda_config,'tope_vinculacion_riesgo',$request->tope_vinculacion_riesgo);
            configuracion_update($idtienda_config,'relacion_couta_ingreso',$request->relacion_couta_ingreso);
            configuracion_update($idtienda_config,'relacion_cuota_venta',$request->relacion_cuota_venta);
            configuracion_update($idtienda_config,'rango_diferencia',$request->rango_diferencia);
            configuracion_update($idtienda_config,'rango_tope_dependiente',$request->rango_tope_dependiente);
            configuracion_update($idtienda_config,'entidades_maxima',$request->entidades_maxima);
            configuracion_update($idtienda_config,'activar_tem',$activar_tem);
            configuracion_update($idtienda_config,'activar_tcem',$activar_tcem);

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
     * Agencias sobre las que el usuario puede ver y editar esta configuracion.
     *
     * Mismo criterio que en Penalidades y Comisiones, Giro Economico y Tasas Activas:
     * se accede a una agencia si el usuario tiene ahi un permiso activo
     * (users_permiso.idestado=1). Un usuario sin ningun permiso registrado
     * (tipicamente superadmin) ve todas las agencias.
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
            // $query->whereIn('id',$idtiendas);
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
     * permiso se rechaza en lugar de caer silenciosamente en la suya.
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
     * Copia los Parametros de Evaluacion (s_config) de una agencia a otra.
     *
     * Por cada parametro: si la agencia destino ya lo tiene lo actualiza, si no lo
     * tiene lo registra. Los parametros que la agencia origen no tenga configurado
     * se omiten, para no crear filas vacias en el destino. El capital asignado a la
     * agencia (tienda.capital_agencia) no se copia: ver nota en PARAMETROS.
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

        $valores_origen = DB::table('s_config')
            ->where('idtienda',$idorigen)
            ->whereNull('idusers')
            ->whereIn('nombre',self::PARAMETROS)
            ->pluck('valor','nombre');

        $insertados   = 0;
        $actualizados = 0;
        $omitidos     = 0;

        DB::transaction(function () use ($valores_origen,$iddestino,&$insertados,&$actualizados,&$omitidos) {

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
        });

        return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se copiaron '.$insertados.' parametro(s) nuevo(s) y se actualizaron '.$actualizados.' en '
                            .$destino->nombreagencia.'.'
        ]);
    }


    public function destroy(Request $request, $idtienda, $id)
    {
    }
}
