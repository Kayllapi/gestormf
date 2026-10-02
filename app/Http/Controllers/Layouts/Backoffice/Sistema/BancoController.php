<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class BancoController extends Controller
{
    public function __construct()
    {
        
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

            return view(sistema_view().'/banco/tabla',[
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

            return view(sistema_view().'/banco/create',[
                'tienda' => $tienda,
                'agencia' => $agencia,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
      
        if($request->input('view') == 'registrar') {
            $rules = [                
                'nombre' => 'required',                 
                'cuenta' => 'required',                 
            ];
          
            $messages = [
                'nombre.required' => 'El Campo es Obligatorio.',
                'cuenta.required' => 'El Campo es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            // el banco es por agencia: se guarda sobre la agencia seleccionada en el
            // filtro, no sobre la de la ruta ni la activa del usuario.
            $agencia = $this->agencia_seleccionada($request,$idtienda);
            abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');
              
            DB::table('banco')->insertGetId([
              'idtienda' => (int) $agencia->id,
              'nombre'  => $request->input('nombre'),
              'cuenta' => $request->input('cuenta'),
                'estado' => 'ACTIVO'
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente en '.$agencia->nombreagencia.'.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'showbanco'){
          $agencia = $this->agencia_seleccionada($request,$idtienda);
          abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');
          
          $banco = DB::table('banco')
                            ->where('banco.idtienda',(int) $agencia->id)
                            ->select(
                                'banco.*'          
                            )
                            ->orderBy('banco.nombre','asc')
                            ->get();
          
          $html = '';
          foreach($banco as $key => $value){
              
              $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data(this)'>
                            <td>".($key+1)."</td>
                            <td>{$value->nombre}</td>
                            <td>{$value->cuenta}</td>
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

      // La pantalla de copia no trabaja sobre un banco concreto: la URL llega con
      // id=0 como marcador, asi que se resuelve ANTES de buscar el banco (si se
      // buscara, el 404 saltaria siempre).
      if($request->input('view') == 'copiar'){
        $agencias = $this->agencias_permitidas();

        if($agencias->count()<2){
            abort(403,'Se necesitan al menos dos agencias administrables para copiar los bancos.');
        }

        return view(sistema_view().'/banco/copiar',[
          'tienda' => $tienda,
          'agencias' => $agencias,
        ]);
      }

      $feriado = DB::table('banco')
                            ->where('banco.idtienda',(int) $agencia->id)
                            ->where('banco.id',$id)
                            ->select(
                                'banco.*'          
                            )
                            ->first();

      // un banco que no es de la agencia seleccionada no se abre
      abort_unless($feriado,404,'El banco no existe en '.$agencia->nombreagencia.'.');
  
      if($request->input('view') == 'editar') {

        return view(sistema_view().'/banco/edit',[
          'tienda' => $tienda,
          'agencia' => $agencia,
          'feriado' => $feriado,
        ]);
      }
      else if($request->input('view') == 'eliminar'){
        return view(sistema_view().'/banco/delete',[
          'tienda' => $tienda,
          'agencia' => $agencia,
          'feriado' => $feriado,
          
        ]);
      }
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        if($request->input('view') == 'editar') {
          $rules = [                
                'nombre' => 'required',                 
                'cuenta' => 'required',                 
            ];
          
            $messages = [
                'nombre.required' => 'El Campo es Obligatorio.',
                'cuenta.required' => 'El Campo es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            $agencia = $this->agencia_seleccionada($request,$idtienda);
            abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');

            // no se puede editar un banco de otra agencia
            if(!DB::table('banco')->where('id',$id)->where('idtienda',(int) $agencia->id)->exists()){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El banco no pertenece a '.$agencia->nombreagencia.'.'
                ]);
            }

            DB::table('banco')->where('id',$id)->where('idtienda',(int) $agencia->id)->update([
              'nombre'  => $request->input('nombre'),
              'cuenta' => $request->input('cuenta'),
                'estado' => $request->input('estado')
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
     * Agencias sobre las que el usuario puede ver y editar esta configuracion.
     *
     * Mismo criterio que en Penalidades y Comisiones, Tasas Activas y Giro
     * Economico: se accede a una agencia si el usuario tiene ahi un permiso
     * activo (users_permiso.idestado=1). Un usuario puede tener permiso en varias
     * agencias a la vez (idsession solo marca cual esta activa), asi que se toman
     * todas.
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
     * Copia los bancos de una agencia a otra.
     *
     * Un banco se empareja por NOMBRE + CUENTA, que es lo que lo identifica en la
     * pantalla (no tiene codigo propio). Ese par no se repite dentro de una
     * agencia, asi que la correspondencia es univoca.
     *
     * Por cada banco de origen: si el destino ya tiene ese par, se actualiza el
     * estado; si no, se registra. Agrega y actualiza, no reemplaza.
     *
     * Un banco NUNCA se borra: credito_formapago, credito_cobranzacuota,
     * movimientointernodinero, asignacioncapital, gastoadministrativooperativo e
     * ingresoextraordinario guardan el id del banco de operaciones ya emitidas.
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

            $bancos_origen = DB::table('banco')
                                ->where('idtienda',$idorigen)
                                ->orderBy('id')
                                ->get();

            foreach($bancos_origen as $banco_origen){

                $banco_destino = DB::table('banco')
                                    ->where('idtienda',$iddestino)
                                    ->where('nombre',$banco_origen->nombre)
                                    ->where('cuenta',$banco_origen->cuenta)
                                    ->first();

                if($banco_destino){
                    DB::table('banco')->where('id',$banco_destino->id)->update([
                        'estado' => $banco_origen->estado,
                    ]);
                    $actualizados++;
                    continue;
                }

                DB::table('banco')->insert([
                    'idtienda' => $iddestino,
                    'nombre'   => $banco_origen->nombre,
                    'cuenta'   => $banco_origen->cuenta,
                    'estado'   => $banco_origen->estado,
                ]);
                $insertados++;
            }
        });

        return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se copiaron '.$insertados.' banco(s) nuevo(s) y se actualizaron '.$actualizados.' en '
                            .$destino->nombreagencia.'.'
        ]);
    }

    /**
     * Tablas que guardan el banco de una operacion ya emitida.
     *
     * credito_cobranzacuota y credito_formapago son las que tienen datos hoy; el
     * resto se lista por si se activan mas adelante.
     */
    const TABLAS_CON_BANCO = [
        'credito_formapago',
        'credito_cobranzacuota',
        'movimientointernodinero',
        'asignacioncapital',
        'gastoadministrativooperativo',
        'ingresoextraordinario',
    ];

    /**
     * Elimina un banco SOLO si ninguna operacion emitida lo referencia.
     *
     * banco esta referenciada por credito_formapago, credito_cobranzacuota,
     * movimientointernodinero, asignacioncapital, gastoadministrativooperativo e
     * ingresoextraordinario. Borrarla dejaria movimientos de caja apuntando a un
     * banco inexistente, asi que en ese caso se rechaza y se sugiere marcarlo
     * INACTIVO. Un banco sin movimientos si se puede borrar, que es el caso de un
     // alta equivocada.
     */
    public function destroy(Request $request, $idtienda, $id)
    {
      
      if( $request->input('view') == 'eliminar' ){
        $agencia = $this->agencia_seleccionada($request,$idtienda);
        abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');

        // no se puede borrar un banco de otra agencia
        $banco = DB::table('banco')
                        ->where('id',$id)
                        ->where('idtienda',(int) $agencia->id)
                        ->first();

        abort_unless($banco,404,'El banco no existe en '.$agencia->nombreagencia.'.');

        $movimientos = 0;
        foreach(self::TABLAS_CON_BANCO as $tabla){
            $movimientos += DB::table($tabla)->where('idbanco',$id)->count();
        }

        if($movimientos>0){
            return response()->json([
              'resultado' => 'ERROR',
              'mensaje'   => 'El banco '.$banco->nombre.' tiene '.$movimientos.' movimiento(s) de caja asociado(s), '
                          .'por eso no se puede eliminar. Marquelo como INACTIVO en su lugar.'
            ]);
        }

        DB::table('banco')->where('id',$id)->where('idtienda',(int) $agencia->id)->delete();
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha elimino correctamente.'
        ]);
      }
      
    
    }
}
