<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class GestionDepositarioController extends Controller
{
    public function __construct()
    {
        //$this->tipo_credito = DB::table('tipo_credito')->get();
    }
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        $agencias = $this->agencias_permitidas();

        // A diferencia de los otros modulos, aqui NO hay un listado por ajax: las
        // tres tablas se dibujan en el index. Por eso el idagencia del selector se
        // tiene que resolver aqui, si no cambiar la agencia recarga la pagina pero
        // vuelve a pintar los datos de la agencia activa.
        $agencia = $this->agencia_seleccionada($request,$idtienda);
        abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');
        $idtienda_seleccionada = (int) $agencia->id;

        // Los tres grupos de la pantalla se leen de la agencia seleccionada. Antes
        // se leian sin filtro, asi que las tres agencias editaban la misma lista.
        $credito_gestiondepositario1 = DB::table('credito_gestiondepositario')
                                            ->where('idtienda',$idtienda_seleccionada)
                                            ->where('constituciongarantia_id',1)
                                            ->get();
        $credito_gestiondepositario2 = DB::table('credito_gestiondepositario')
                                            ->where('idtienda',$idtienda_seleccionada)
                                            ->where('constituciongarantia_id',2)
                                            ->get();
        $credito_representantecomun = DB::table('credito_representantecomun')
                                            ->where('idtienda',$idtienda_seleccionada)
                                            ->get();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/gestiondepositario/tabla',[
              'tienda' => $tienda,
              'agencias' => $agencias,
              'idtienda_seleccionada' => $idtienda_seleccionada,
              'agencia' => $agencia,
              'credito_gestiondepositario1' => $credito_gestiondepositario1,
              'credito_gestiondepositario2' => $credito_gestiondepositario2,
              'credito_representantecomun' => $credito_representantecomun,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
      
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

        $agencia = $this->agencia_seleccionada($request,$idtienda);
        abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');

        // La pantalla de copia no trabaja sobre un registro concreto: la URL llega
        // con id=0 como marcador, asi que se resuelve antes de buscar nada.
        if($request->input('view') == 'copiar'){
          $agencias = $this->agencias_permitidas();

          if($agencias->count()<2){
            abort(403,'Se necesitan al menos dos agencias administrables para copiar la configuracion.');
          }

          return view(sistema_view().'/gestiondepositario/copiar',[
            'tienda' => $tienda,
            'agencias' => $agencias,
          ]);
        }
    }

    /**
     * Guarda los tres grupos de la pantalla sobre la agencia seleccionada.
     *
     * El formulario representa el estado COMPLETO de la agencia (borrar todo y
     * volver a insertar lo que se ve), asi que el borrado va acotado a la agencia.
     * Antes era un delete() sin filtro: al hacerlo por agencia, un guardado desde
     * una agencia habria vaciado los depositarios de las otras dos.
     */
    public function update(Request $request, $idtienda, $id)
    {
        if($request->input('view') == 'editar') {

            $agencia = $this->agencia_seleccionada($request,$idtienda);
            abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');
            $idtienda_destino = (int) $agencia->id;

            $conposicion   = json_decode($request->seleccionar_conentregaposesion)   ?? [];
            $sinposicion   = json_decode($request->seleccionar_sinentregaposesion)   ?? [];
            $representante = json_decode($request->seleccionar_representantecomun)   ?? [];

            // se valida TODO antes de borrar nada: si algo esta mal, la agencia
            // conserva su configuracion anterior en vez de quedar a medias.
            foreach($conposicion as $value){
                if($value->custodiagarantia_id==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Custodia de Garantía Obligatorio!!.'
                    ]);
                }
                if($value->estado_id==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Estado Obligatorio!!.'
                    ]);
                }
            }

            foreach($sinposicion as $value){
                if($value->custodiagarantia_id==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Custodia de Garantía Obligatorio!!.'
                    ]);
                }
                if($value->estado_id==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Estado Obligatorio!!.'
                    ]);
                }
            }

            foreach($representante as $value){
                if($value->nombre==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Nombre y Apellidos	es Obligatorio!!.'
                    ]);
                }
                if($value->estado_id==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Estado Obligatorio!!.'
                    ]);
                }
            }

            DB::transaction(function () use ($conposicion,$sinposicion,$representante,$idtienda_destino) {

                // borrado ACOTADO a la agencia
                DB::table('credito_gestiondepositario')->where('idtienda',$idtienda_destino)->delete();
                DB::table('credito_representantecomun')->where('idtienda',$idtienda_destino)->delete();

                foreach(array_merge($conposicion,$sinposicion) as $value){
                    DB::table('credito_gestiondepositario')->insert([
                        'idtienda'                 => $idtienda_destino,
                        'custodiagarantia_id'      => $value->custodiagarantia_id,
                        'custodiagarantia_nombre'  => $value->custodiagarantia_nombre,
                        'nombre'                   => $value->nombre,
                        'doeruc'                   => $value->doeruc,
                        'direccion'                => $value->direccion,
                        'representante_doeruc'     => $value->representante_doeruc,
                        'representante_nombre'     => $value->representante_nombre,
                        'estado_id'                => $value->estado_id,
                        'estado_nombre'            => $value->estado_nombre,
                        'constituciongarantia_id'  => $value->constituciongarantia_id,
                        'constituciongarantia_nombre' => $value->constituciongarantia_nombre,
                    ]);
                }

                foreach($representante as $value){
                    DB::table('credito_representantecomun')->insert([
                        'idtienda'        => $idtienda_destino,
                        'nombre'          => $value->nombre,
                        'doi'             => $value->doi,
                        'direccion'       => $value->direccion,
                        'ubigeo_id'       => $value->ubigeo_id,
                        'ubigeo_nombre'   => $value->ubigeo_nombre,
                        'estado_id'       => $value->estado_id,
                        'estado_nombre'   => $value->estado_nombre,
                    ]);
                }
            });

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
     * Mismo criterio que en Penalidades y Comisiones, Tasas Activas, Giro Economico
     * y Bancos: se accede a una agencia si el usuario tiene ahi un permiso activo
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
     * Copia los depositarios y el representante comun de una agencia a otra.
     *
     * Un depositario se empareja por Constitucion + Custodia + Nombre + RUC. Se
     * incluye la custodia porque hay filas de "Sin entrega de posesion" sin nombre
     * ni RUC (las del tipo "Otro" y "EL/LOS PRESTATARIO(S)"), y sin ese dato dos
     * filas vacias se emparejarian entre si.
     *
     * El representante comun se empareja por Nombre + DOI.
     *
     * Por cada fila de origen: si el destino ya tiene ese equivalente, se actualiza
     * (estado y datos); si no, se registra. Agrega y actualiza, no reemplaza: lo
     * que el destino tenga y el origen no, se queda. A diferencia del guardado de
     * la pantalla, la copia NO borra: es una operacion de agregar, no de sustituir
     * el catalogo entero.
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

        $insertados_depositarios   = 0;
        $actualizados_depositarios = 0;
        $insertados_representantes = 0;
        $actualizados_representantes = 0;

        DB::transaction(function () use ($idorigen,$iddestino,&$insertados_depositarios,&$actualizados_depositarios,&$insertados_representantes,&$actualizados_representantes) {

            // --- depositarios (con y sin entrega de posesion)
            $depositarios_origen = DB::table('credito_gestiondepositario')
                                    ->where('idtienda',$idorigen)
                                    ->orderBy('id')
                                    ->get();

            foreach($depositarios_origen as $d_origen){

                $d_destino = DB::table('credito_gestiondepositario')
                                ->where('idtienda',$iddestino)
                                ->where('constituciongarantia_id',$d_origen->constituciongarantia_id)
                                ->where('custodiagarantia_id',$d_origen->custodiagarantia_id)
                                ->where('nombre',$d_origen->nombre)
                                ->where('doeruc',$d_origen->doeruc)
                                ->first();

                $datos = [
                    'custodiagarantia_nombre'     => $d_origen->custodiagarantia_nombre,
                    'direccion'                   => $d_origen->direccion,
                    'representante_doeruc'        => $d_origen->representante_doeruc,
                    'representante_nombre'        => $d_origen->representante_nombre,
                    'estado_id'                   => $d_origen->estado_id,
                    'estado_nombre'               => $d_origen->estado_nombre,
                    'constituciongarantia_nombre' => $d_origen->constituciongarantia_nombre,
                ];

                if($d_destino){
                    DB::table('credito_gestiondepositario')->where('id',$d_destino->id)->update($datos);
                    $actualizados_depositarios++;
                    continue;
                }

                DB::table('credito_gestiondepositario')->insert($datos + [
                    'idtienda'                => $iddestino,
                    'custodiagarantia_id'     => $d_origen->custodiagarantia_id,
                    'nombre'                  => $d_origen->nombre,
                    'doeruc'                  => $d_origen->doeruc,
                    'constituciongarantia_id' => $d_origen->constituciongarantia_id,
                ]);
                $insertados_depositarios++;
            }

            // --- representante comun
            $representantes_origen = DB::table('credito_representantecomun')
                                    ->where('idtienda',$idorigen)
                                    ->orderBy('id')
                                    ->get();

            foreach($representantes_origen as $r_origen){

                $r_destino = DB::table('credito_representantecomun')
                                ->where('idtienda',$iddestino)
                                ->where('nombre',$r_origen->nombre)
                                ->where('doi',$r_origen->doi)
                                ->first();

                $datos = [
                    'direccion'     => $r_origen->direccion,
                    'ubigeo_id'     => $r_origen->ubigeo_id,
                    'ubigeo_nombre' => $r_origen->ubigeo_nombre,
                    'estado_id'     => $r_origen->estado_id,
                    'estado_nombre' => $r_origen->estado_nombre,
                ];

                if($r_destino){
                    DB::table('credito_representantecomun')->where('id',$r_destino->id)->update($datos);
                    $actualizados_representantes++;
                    continue;
                }

                DB::table('credito_representantecomun')->insert($datos + [
                    'idtienda' => $iddestino,
                    'nombre'   => $r_origen->nombre,
                    'doi'      => $r_origen->doi,
                ]);
                $insertados_representantes++;
            }
        });

        $mensaje = 'Se copiaron '.$insertados_depositarios.' depositario(s) y '.$insertados_representantes.' representante(s) nuevos, '
                    .'y se actualizaron '.$actualizados_depositarios.' depositario(s) y '.$actualizados_representantes.' representante(s) en '
                    .$destino->nombreagencia.'.';

        return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => $mensaje
        ]);
    }

    /**
     * Esta pantalla no borra registros uno por uno: se editan en el formulario y se
     * guardan todos juntos. El destroy queda sin efecto a proposito.
     *
     * Antes hacia DELETE sobre credito_prendatario: un producto de credito ajeno a
     * esta pantalla. Desde esta ruta se alcanzaba a borrar el tarifario de un
     * producto de otra agencia, asi que se elimino ese borrado.
     */
    public function destroy(Request $request, $idtienda, $id)
    {
        return response()->json([
            'resultado' => 'ERROR',
            'mensaje'   => 'En esta pantalla no se eliminan registros: edite la fila y guarde los cambios.'
        ]);
    }
}
