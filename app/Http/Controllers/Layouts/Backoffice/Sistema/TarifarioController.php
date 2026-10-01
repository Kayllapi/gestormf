<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class TarifarioController extends Controller
{
    public function __construct()
    {
        $this->tipo_credito = DB::table('tipo_credito')->get();
        $this->forma_pago_credito = DB::table('forma_pago_credito')->get();
        $this->forma_credito = DB::table('forma_credito')->get();
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

            return view(sistema_view().'/tarifario/tabla',[
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

            return view(sistema_view().'/tarifario/create',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'forma_pago_credito' => $this->forma_pago_credito,
                'forma_credito' => $this->forma_credito
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
      
        if($request->input('view') == 'registrar') {
            
            $rules = [                
                'idforma_credito' => 'required',                 
                'idcredito_prendatario' => 'required',                 
                'idforma_pago_credito' => 'required',                 
                'monto' => 'required',                 
                'cuotas' => 'required',                 
                'tem' => 'required',                 
                'cargos_otros' => 'required',                 
            ];
          
            $messages = [
                'idforma_credito.required' => 'El Campo es Obligatorio.',
                'idcredito_prendatario.required' => 'El Campo es Obligatorio.',
                'idforma_pago_credito.required' => 'El Campo es Obligatorio.',
                'monto.required' => 'El Campo es Obligatorio.',
                'cuotas.required' => 'El Campo es Obligatorio.',
                'tem.required' => 'El Campo es Obligatorio.',
                'cargos_otros.required' => 'El Campo es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            // el tarifario es por agencia: se guarda sobre la agencia seleccionada
            // en el filtro, no sobre la de la ruta ni la activa del usuario.
            $agencia = $this->agencia_seleccionada($request,$idtienda);
            abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');
            $idtienda_destino = (int) $agencia->id;

            // el producto tiene que ser de la misma agencia que la tasa
            if($error = $this->valida_producto_de_la_agencia($request->input('idcredito_prendatario'),$idtienda_destino)){
                return $error;
            }
              
            $idtarifario = DB::table('tarifario')->insertGetId([
              'idtienda'              => $idtienda_destino,
              'idforma_credito'       => $request->input('idforma_credito'),
              'idcredito_prendatario' => $request->input('idcredito_prendatario'),
              'idforma_pago_credito'  => $request->input('idforma_pago_credito'),
              'monto'                 => $request->input('monto'),
              'cuotas'                => $request->input('cuotas'),
              'tem'                   => $request->input('tem'),
              'cargos_otros'          => $request->input('cargos_otros')
            ]);
            $codCredito = str_pad($idtarifario, 4, '0', STR_PAD_LEFT);
            DB::table('tarifario')->where('id', $idtarifario)->update(['codigo' => 'T'.$codCredito]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente en '.$agencia->nombreagencia.'.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        $agencia = $this->agencia_seleccionada($request,$idtienda);
        abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');
        $idtienda_destino = (int) $agencia->id;

        if($id == 'showtarifario'){
          $where = [];
          if($request->input('tipo') != ''){
            $where[] = ['tarifario.idforma_credito', $request->input('tipo')];  
          }
          if($request->input('idcredito_prendatario') != ''){
            $where[] = ['tarifario.idcredito_prendatario', $request->input('idcredito_prendatario')];  
          }
          if($request->input('idforma_pago_credito') != ''){
            $where[] = ['tarifario.idforma_pago_credito', $request->input('idforma_pago_credito')];  
          }
          
          $creditos = DB::table('tarifario')
                            ->join('forma_pago_credito','forma_pago_credito.id','tarifario.idforma_pago_credito')
                            ->join('forma_credito','forma_credito.id','tarifario.idforma_credito')
                            ->join('credito_prendatario','credito_prendatario.id','tarifario.idcredito_prendatario')
                            ->where('tarifario.idtienda',$idtienda_destino)
                            ->where($where)
                            ->select(
                                'tarifario.*',
                                'forma_pago_credito.nombre as nombreformapago',
                                'forma_credito.nombre as nombreformacredito',
                                'credito_prendatario.nombre as nombreproductocredito'            
                            )
                            ->orderBy('tarifario.id','asc')
                            ->get();
          
          $html = '';
          foreach($creditos as $key => $value){
              
              $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data(this)'>
                            <td>".($key+1)."</td>
                            <td>{$value->codigo}</td>
                            <td>{$value->monto}</td>
                            <td>{$value->cuotas}</td>
                            <td>{$value->tem}</td>
                            <td>{$value->cargos_otros}</td>
                            <td>{$value->nombreformapago}</td>
                            <td>{$value->nombreformacredito}</td>
                            <td>{$value->nombreproductocredito}</td>
                        </tr>";
          }
          return array(
            'html' => $html
          );
          
        }
        else if($id == 'showproductocredito'){
          
          $producto_credito = DB::table('credito_prendatario')
                              ->where('credito_prendatario.idtienda',$idtienda_destino)
                              ->where('credito_prendatario.idforma_credito',$request->input('tipo'))
                              ->select('credito_prendatario.*')
                              ->orderBy('credito_prendatario.id', 'asc')
                              ->get();

          return $producto_credito;
        }

    }

    public function edit(Request $request, $idtienda, $id)
    {
        
      
      $tienda = DB::table('tienda')->whereId($idtienda)->first();

      $agencia = $this->agencia_seleccionada($request,$idtienda);
      abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');

      // La pantalla de copia no trabaja sobre un tarifario concreto: la URL llega
      // con id=0 como marcador, asi que se resuelve antes de buscar la tasa (si se
      // buscara, el 404 saltaria siempre).
      if($request->input('view') == 'copiar'){
        $agencias = $this->agencias_permitidas();

        if($agencias->count()<2){
            abort(403,'Se necesitan al menos dos agencias administrables para copiar el tarifario.');
        }

        return view(sistema_view().'/tarifario/copiar',[
          'tienda' => $tienda,
          'agencias' => $agencias,
        ]);
      }

      $idtienda_destino = (int) $agencia->id;
      
      // el tarifario tiene que ser de la agencia seleccionada: el id viaja en la URL
      // y se valida aqui, no solo al guardar.
      $tarifario = DB::table('tarifario')
                            ->join('forma_pago_credito','forma_pago_credito.id','tarifario.idforma_pago_credito')
                            ->join('credito_prendatario','credito_prendatario.id','tarifario.idcredito_prendatario')
                            ->where('tarifario.idtienda',$idtienda_destino)
                            ->where('tarifario.id',$id)
                            ->select(
                                'tarifario.*',
                                'forma_pago_credito.nombre as nombreformapago',
                                'credito_prendatario.nombre as nombrecredito'            
                            )
                            ->orderBy('tarifario.id','desc')
                            ->first();

      abort_unless($tarifario,404,'El tarifario no existe en '.$agencia->nombreagencia.'.');
      
      if($request->input('view') == 'editar') {

        return view(sistema_view().'/tarifario/edit',[
          'tienda' => $tienda,
          'agencia' => $agencia,
          'tarifario' => $tarifario,
          'forma_pago_credito' => $this->forma_pago_credito,
          'forma_credito' => $this->forma_credito
        ]);
      }
      else if($request->input('view') == 'eliminar'){
        return view(sistema_view().'/tarifario/delete',[
          'tienda' => $tienda,
          'agencia' => $agencia,
          'tarifario' => $tarifario
          
        ]);
      }
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        if($request->input('view') == 'editar') {
  
            $rules = [
                'idforma_credito' => 'required',                   
                'idcredito_prendatario' => 'required',                 
                'idforma_pago_credito' => 'required',                 
                'monto' => 'required',                 
                'cuotas' => 'required',                 
                'tem' => 'required',                 
                'cargos_otros' => 'required',                 
            ];
          
            $messages = [
                'idforma_credito.required' => 'El Campo es Obligatorio.',
                'idcredito_prendatario.required' => 'El Campo es Obligatorio.',
                'idforma_pago_credito.required' => 'El Campo es Obligatorio.',
                'monto.required' => 'El Campo es Obligatorio.',
                'cuotas.required' => 'El Campo es Obligatorio.',
                'tem.required' => 'El Campo es Obligatorio.',
                'cargos_otros.required' => 'El Campo es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            $agencia = $this->agencia_seleccionada($request,$idtienda);
            abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');
            $idtienda_destino = (int) $agencia->id;

            // no se puede editar un tarifario de otra agencia
            if(!DB::table('tarifario')->where('id',$id)->where('idtienda',$idtienda_destino)->exists()){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El tarifario no pertenece a '.$agencia->nombreagencia.'.'
                ]);
            }

            // el producto tampoco puede ser de otra agencia
            if($error = $this->valida_producto_de_la_agencia($request->input('idcredito_prendatario'),$idtienda_destino)){
                return $error;
            }

            $idtarifario = DB::table('tarifario')->where('id',$id)->where('idtienda',$idtienda_destino)->update([
              'idforma_credito'       => $request->input('idforma_credito'),
              'idcredito_prendatario' => $request->input('idcredito_prendatario'),
              'idforma_pago_credito'  => $request->input('idforma_pago_credito'),
              'monto'                 => $request->input('monto'),
              'cuotas'                => $request->input('cuotas'),
              'tem'                   => $request->input('tem'),
              'cargos_otros'          => $request->input('cargos_otros'),
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
     * El producto elegido tiene que existir y ser de la misma agencia que el
     * tarifario: si no, el tarifario quedaria apuntando al catalogo de otra
     * agencia y esa agencia no veria nunca su propia tasa.
     * Devuelve la respuesta de error si el producto no sirve, o null si todo esta
     * bien. Los llamadores deben hacer:
     *     if($error = $this->valida_producto_de_la_agencia(...)){ return $error; }
     */
    protected function valida_producto_de_la_agencia($idproducto,$idtienda)
    {
        $ok = DB::table('credito_prendatario')
                    ->where('id',$idproducto)
                    ->where('idtienda',$idtienda)
                    ->exists();

        if($ok){
            return null;
        }

        return response()->json([
            'resultado' => 'ERROR',
            'mensaje'   => 'El producto seleccionado no pertenece a la agencia elegida.'
        ]);
    }

    /**
     * Agencias sobre las que el usuario puede ver y editar el tarifario.
     *
     * Mismo criterio que en Penalidades y Comisiones: se accede a una agencia si
     * el usuario tiene ahi un permiso activo (users_permiso.idestado=1). Un
     * usuario puede tener permiso en varias agencias a la vez (idsession solo
     * marca cual esta activa), asi que se toman todas.
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
     * envio, la activa. Una agencia enviada explicitamente que el usuario no
     * tiene permiso se rechaza en lugar de cair silenciosamente en la suya.
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
     * Copia el tarifario de una agencia a otra.
     *
     * A diferencia de Penalidades y Comisiones (que guarda diferencias en tablas
     * override), aqui el tarifario es una COPIA real por agencia y cada fila
     * apunta al producto (credito_prendatario) de SU agencia. Por eso la copia
     * tiene que resolver primero los productos:
     *
     *   - Si la agencia destino ya tiene un producto con el mismo nombre, se
     *     reutiliza ese id y no se crea nada nuevo.
     *   - Si no lo tiene, se crea la copia del producto en destino. Los productos
     *     NO se borran nunca porque credito.idcredito_prendatario los referencia
     *     (historico de creditos emitidos).
     *
     * Despues se copian las tasas. La que define si una tasa "ya existe" en
     * destino es el combo (producto, forma de pago, monto, cuotas), que es lo
     * que las consultas del simulador seek: si el combo ya esta, se actualizan
     * TEM y Ss. Recaudo; si no, se registra.
     *
     * Las filas de destino que no esten en el origen NO se tocan: la copia
     * agrega y actualiza, no reemplaza.
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

        $productos_creados = 0;
        $insertados        = 0;
        $actualizados      = 0;
        $omitidos          = 0;

        DB::transaction(function () use ($idorigen,$iddestino,&$productos_creados,&$insertados,&$actualizados,&$omitidos) {

            // 1. mapa de productos: id en origen => id en destino, emparejados por nombre
            $mapa = [];
            $productos_origen = DB::table('credito_prendatario')
                                    ->where('idtienda',$idorigen)
                                    ->orderBy('id')
                                    ->get();

            foreach($productos_origen as $producto_origen){

                $producto_destino = DB::table('credito_prendatario')
                                        ->where('idtienda',$iddestino)
                                        ->where('nombre',$producto_origen->nombre)
                                        ->first();

                if(!$producto_destino){
                    // se crea el producto en destino con el prefijo que le
                    // corresponde a su forma de credito (CP prendario / CO ordinario)
                    $prefijo = ((int) $producto_origen->idforma_credito === 1) ? 'CP' : 'CO';

                    $nuevo_id = DB::table('credito_prendatario')->insertGetId([
                        'idtienda'            => $iddestino,
                        'nombre'              => $producto_origen->nombre,
                        'idtipo_credito'      => $producto_origen->idtipo_credito,
                        'modalidad'           => $producto_origen->modalidad,
                        'garantiaprendatario' => $producto_origen->garantiaprendatario,
                        'estado'              => $producto_origen->estado,
                        'conevaluacion'       => $producto_origen->conevaluacion,
                        'idforma_credito'     => $producto_origen->idforma_credito,
                    ]);

                    DB::table('credito_prendatario')->where('id',$nuevo_id)->update([
                        'codigo' => $prefijo.str_pad($nuevo_id,4,'0',STR_PAD_LEFT),
                    ]);

                    $mapa[(int) $producto_origen->id] = (int) $nuevo_id;
                    $productos_creados++;
                    continue;
                }

                $mapa[(int) $producto_origen->id] = (int) $producto_destino->id;
            }

            // 2. copy de las tasas, remapeando el producto al de destino
            $tarifarios_origen = DB::table('tarifario')
                                    ->where('idtienda',$idorigen)
                                    ->orderBy('id')
                                    ->get();

            foreach($tarifarios_origen as $tarifario_origen){

                $idproducto_destino = $mapa[(int) $tarifario_origen->idcredito_prendatario] ?? null;

                if($idproducto_destino === null){
                    $omitidos++;
                    continue;
                }

                $existe = DB::table('tarifario')
                            ->where('idtienda',$iddestino)
                            ->where('idcredito_prendatario',$idproducto_destino)
                            ->where('idforma_pago_credito',$tarifario_origen->idforma_pago_credito)
                            ->where('monto',$tarifario_origen->monto)
                            ->where('cuotas',$tarifario_origen->cuotas)
                            ->first();

                if($existe){
                    DB::table('tarifario')->where('id',$existe->id)->update([
                        'idforma_credito' => $tarifario_origen->idforma_credito,
                        'tem'             => $tarifario_origen->tem,
                        'cargos_otros'    => $tarifario_origen->cargos_otros,
                    ]);
                    $actualizados++;
                    continue;
                }

                $nuevo_id = DB::table('tarifario')->insertGetId([
                    'idtienda'              => $iddestino,
                    'idforma_credito'       => $tarifario_origen->idforma_credito,
                    'idcredito_prendatario' => $idproducto_destino,
                    'idforma_pago_credito'  => $tarifario_origen->idforma_pago_credito,
                    'monto'                 => $tarifario_origen->monto,
                    'cuotas'                => $tarifario_origen->cuotas,
                    'tem'                   => $tarifario_origen->tem,
                    'cargos_otros'          => $tarifario_origen->cargos_otros,
                ]);

                DB::table('tarifario')->where('id',$nuevo_id)->update([
                    'codigo' => 'T'.str_pad($nuevo_id,4,'0',STR_PAD_LEFT),
                ]);

                $insertados++;
            }
        });

        $mensaje = 'Se copiaron '.$insertados.' tasa(s) nueva(s) y se actualizaron '.$actualizados.' en '
                    .$destino->nombreagencia.'.';

        if($productos_creados>0){
            $mensaje .= ' Se crearon '.$productos_creados.' producto(s) que le faltaban.';
        }
        if($omitidos>0){
            $mensaje .= ' Se omitieron '.$omitidos.' tasa(s) sin producto equivalente.';
        }

        return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => $mensaje
        ]);
    }

    public function destroy(Request $request, $idtienda, $id)
    {
      
      if( $request->input('view') == 'eliminar' ){
        $agencia = $this->agencia_seleccionada($request,$idtienda);
        abort_unless($agencia,403,'No tiene permisos sobre la agencia seleccionada.');

        // no se puede borrar un tarifario de otra agencia
        DB::table('tarifario')->where('id',$id)->where('idtienda',(int) $agencia->id)->delete();
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha elimino correctamente.'
        ]);
      }
      
    
    }
}
