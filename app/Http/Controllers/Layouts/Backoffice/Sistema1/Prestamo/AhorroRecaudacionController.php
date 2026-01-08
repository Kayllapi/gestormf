<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Prestamo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;

class AhorroRecaudacionController extends Controller
{
    public function index(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        return view('layouts/backoffice/tienda/sistema/prestamo/ahorrorecaudacion/index', [
            'tienda'      => $tienda,
        ]);
    }

    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda   = DB::table('tienda')->whereId($idtienda)->first();
        return view('layouts/backoffice/tienda/sistema/prestamo/ahorrorecaudacion/create', [
            'tienda' => $tienda
        ]);
    }

    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        /*if($request->view == 'registrar') {

            $rules = [];
          
            if($request->idprestamo_tipoahorro==1){
                $rules = array_merge($rules,[
                    'idprestamo_ahorro' => 'required',
                    'retirarganancia' => 'required',
                ]);
            }elseif($request->idprestamo_tipoahorro==3){
                $rules = array_merge($rules,[
                    'cuotas_totalredondeado' => 'required'
                ]);
            }
          
            $rules = array_merge($rules,[
                'facturacion_idcliente' => 'required',
                'facturacion_direccion' => 'required',
                'facturacion_idubigeo' => 'required',
                'facturacion_idagencia' => 'required',
                'facturacion_idmoneda' => 'required',
                'facturacion_idtipocomprobante' => 'required'
            ]);
          
            $messages = [
                'idprestamo_ahorro.required' => 'El "Cliente" es Obligatorio.',
                'retirarganancia.required' => 'El "Retirar Ganancia" es Obligatorio.',
                'cuotas_totalredondeado.required' => 'El "Monto a Ahorrar" es Obligatorio.',
                'facturacion_idcliente.required' => 'El "Cliente" es Obligatorio.',
                'facturacion_direccion.required' => 'La "Dirección" es Obligatorio.',
                'facturacion_idubigeo.required' => 'El "Ubigeo" es Obligatorio.',
                'facturacion_idagencia.required' => 'La "Agencia" es Obligatorio.',
                'facturacion_idmoneda.required' => 'La "Moneda" es Obligatorio.',
                'facturacion_idtipocomprobante.required' => 'El "Monto Recibido" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);
          
            // aperturacaja
            $caja = caja($idtienda, Auth::user()->id);
            if($caja['resultado']!='ABIERTO'){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La Caja debe estar Aperturada.'
                ]);
            }
            $idaperturacierre = $caja['apertura']->id;
            // fin aperturacaja
          
            $cronograma = ahorro_recaudacion_cronograma_fijo($idtienda,$request->idprestamo_ahorro);
          
          
            
            if($request->idprestamo_tipoahorro==1){
           
            }
            elseif($request->idprestamo_tipoahorro==2){
                // obtener ultimo código
                $prestamorecaudacion = DB::table('s_prestamo_ahorrorecaudacion')
                    ->where('s_prestamo_ahorrorecaudacion.idtienda',$idtienda)
                    ->orderBy('s_prestamo_ahorrorecaudacion.codigo','desc')
                    ->limit(1)
                    ->first();
                $codigo = 1;
                if($prestamorecaudacion!=''){
                    $codigo = $prestamorecaudacion->codigo+1;
                }
                // fin obtener ultimo código
                $idprestamorecaudacion = DB::table('s_prestamo_ahorrorecaudacion')->insertGetId([
                    'fecharegistro' => Carbon::now(),
                    'codigo' => $codigo,
                    'cuota' => $cronograma['select_cuota'],
                    'cliente_direccion' => $request->facturacion_direccion,
                    'cliente_idubigeo' => $request->facturacion_idubigeo,
                    's_idaperturacierre' => $idaperturacierre,
                    'idprestamo_ahorro' => $request->idprestamo_ahorro,
                    'idtipocomprobante' => $request->facturacion_idtipocomprobante,
                    'idmoneda' => $request->facturacion_idmoneda,
                    'idagencia' => $request->facturacion_idagencia,
                    'idcliente' => $request->facturacion_idcliente,
                    'idasesor' => $cronograma['ahorrosolicitud']->idasesor,
                    'idcajero' => Auth::user()->id,
                    'idestadorecaudacion' => 2,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                foreach ($cronograma['cuotas_pendientes_seleccionados'] as $value) {

                    if($value['estado'] == 'CANCELADO'){
                        DB::table('s_prestamo_ahorrorecaudaciondetalle')->insert([
                            'idprestamo_recaudacion' => $idprestamorecaudacion,
                            'idprestamo_ahorrodetalle' => $value['idprestamo_ahorrodetalle'],
                            'idtienda' => $idtienda,
                            'idestado' => 1,
                        ]);

                        DB::table('s_prestamo_ahorrodetalle')->whereId($value['idprestamo_ahorrodetalle'])->update([
                            'atraso' => $value['atraso'],
                            'mora' => $value['mora'],
                            'moradescuento' => $value['moradescontado'],
                            'moraapagar' => $value['moraapagar'],
                            'cuotapago' => $value['cuotapago'],
                            'acuenta' => $value['acuenta'],
                            'cuotaapagar' => $value['cuotaapagar'],
                            'interesdescontado' => $value['interesrestante'],
                            'idestadorecaudacion' => 2
                        ]);

                        DB::table('s_prestamo_ahorrorecaudacion')->whereId($idprestamorecaudacion)->update([
                            'cronograma_acuentaanterior' => 0,
                        ]);
                    }
                    elseif($value['estado'] == 'ACUENTA'){
                        DB::table('s_prestamo_ahorrodetalle')->whereId($value['idprestamo_ahorrodetalle'])->update([
                            'acuenta' => $value['acuenta'],
                        ]);
                    }

                }
                // DESCUENTO MORA DESDE COBRANZA
                if($request->input('check_moradescuento')=='on'){
                    $idprestamo_mora = DB::table('s_prestamo_ahorromora')->insertGetId([
                        'fecharegistro' => Carbon::now(),
                        'codigo' => Carbon::now()->format('ymdhms'),
                        'total_mora' => $cronograma['select_mora'],
                        'total_moradescuento' => $cronograma['select_moradescontado'],
                        'total_moraapagar' => $cronograma['select_moraapagar'],
                        'motivo' => '',
                        'documento' => '',
                        'idmoneda' => 1,
                        'idcliente' => $request->facturacion_idcliente,
                        'idasesor' => $cronograma['creditosolicitud']->idasesor,
                        'idcajero' => Auth::user()->id,
                        'idsupervisor' => 0,
                        'idestadomora' => 1,
                        'idestadoaprobacion' => 1,
                        'idprestamo_ahorro' => $request->idprestamo_ahorro,
                        'idtienda' => $idtienda,
                        'idestado' => 1
                    ]);
                }
                // Descuento de mora pendiente por tipo de pago
                if($request->idtipopago==1){
                    $prestamo_credito = DB::table('s_prestamo_ahorro')
                        ->whereId($request->idprestamo_ahorro)
                        ->first();
                    if($prestamo_credito->numerocuota==$request->hastacuota && $cronograma['morapendiente']>0){
                        DB::table('s_prestamo_ahorromora')
                            ->where('s_prestamo_ahorromora.idprestamo_ahorro', $request->idprestamo_ahorro)
                            ->where('s_prestamo_ahorromora.idestado', 1)
                            ->where('s_prestamo_ahorromora.idestadoaprobacion', 1)
                            ->whereIn('s_prestamo_ahorromora.idestadomora', [1,2])
                            ->update([
                            'fechaanulado' => Carbon::now(),
                            'idcajero' => Auth::user()->id,
                            'idestadoaprobacion' => 3,
                            'idestadomora' => 1,
                        ]);
                    }
                }
                elseif($request->idtipopago==2){
                        DB::table('s_prestamo_ahorromora')
                            ->where('s_prestamo_ahorromora.idprestamo_ahorro', $request->idprestamo_ahorro)
                            ->where('s_prestamo_ahorromora.idestado', 1)
                            ->where('s_prestamo_ahorromora.idestadoaprobacion', 1)
                            ->whereIn('s_prestamo_ahorromora.idestadomora', [1,2])
                            ->update([
                            'fechaanulado' => Carbon::now(),
                            'idcajero' => Auth::user()->id,
                            'idestadoaprobacion' => 3,
                            'idestadomora' => 1,
                        ]);
                }
                // Actualizar pagos de ahorros
                $cronograma = ahorro_recaudacion_cronograma($idtienda,$cronograma['ahorrosolicitud']->id,0,0,1,0);
                $idestadorecaudacion = 1; // PENDIENTE
                if(count($cronograma['cuotas_pendientes'])==0){
                    $idestadorecaudacion = 2; // CANCELADO
                }
                DB::table('s_prestamo_ahorro')->whereId($cronograma['ahorrosolicitud']->id)->update([
                    'idestadorecaudacion' => $idestadorecaudacion,
                ]);
            }
            elseif($request->idprestamo_tipoahorro==3){
               
                // obtener ultimo código
                $prestamorecaudacionlibre = DB::table('s_prestamo_ahorrorecaudacionlibre')
                    ->where('s_prestamo_ahorrorecaudacionlibre.idtienda',$idtienda)
                    ->orderBy('s_prestamo_ahorrorecaudacionlibre.codigo','desc')
                    ->limit(1)
                    ->first();
                $codigo = 1;
                if($prestamorecaudacionlibre!=''){
                    $codigo = $prestamorecaudacionlibre->codigo+1;
                }
                // fin obtener ultimo código
                $idprestamorecaudacion = DB::table('s_prestamo_ahorrorecaudacionlibre')->insertGetId([
                    'fecharegistro' => Carbon::now(),
                    'codigo' => $codigo,
                    'monto_efectivo' => $request->cuotas_totalredondeado,
                    'monto_deposito' => 0,
                    'cliente_direccion' => $request->facturacion_direccion,
                    'cliente_idubigeo' => $request->facturacion_idubigeo,
                    's_idaperturacierre' => $idaperturacierre,
                    'idprestamo_ahorro' => $request->idprestamo_ahorro,
                    'idtipocomprobante' => $request->facturacion_idtipocomprobante,
                    'idmoneda' => $request->facturacion_idmoneda,
                    'idagencia' => $request->facturacion_idagencia,
                    'idcliente' => $request->facturacion_idcliente,
                    'idcajero' => Auth::user()->id,
                    'idestadorecaudacion' => 2,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
            }
          
            // PAGOS EN DEPOSITO
            if(isset($request->seleccionar_formapago)){
                $monto_deposito = 0;
                for($i=0;$i<count($request->seleccionar_formapago);$i++){
                    $num = $request->seleccionar_formapago[$i];

                    $imagen = uploadfile('','',$request->file('formapago_voucher'.$num),'/public/backoffice/tienda/'.$idtienda.'/recaudacion/');
                    DB::table('s_prestamo_ahorrorecaudacioncuentabancaria')->insert([
                        'fecharegistro' => Carbon::now(),
                        'numerocuenta' => $request->input('formapago_numerocuenta'.$num)!=''?$request->input('formapago_numerocuenta'.$num):'',
                        'numerooperacion' => $request->input('formapago_numerooperacion'.$num)!=''?$request->input('formapago_numerooperacion'.$num):'',
                        'banco' => $request->input('formapago_banco'.$num)!=''?$request->input('formapago_banco'.$num):'',
                        'fecha' => $request->input('formapago_fecha'.$num)!=''?$request->input('formapago_fecha'.$num):'',
                        'hora' => $request->input('formapago_hora'.$num)!=''?$request->input('formapago_hora'.$num):'',
                        'monto' => $request->input('formapago_montodeposito'.$num),
                        'voucher' => $imagen,
                        's_idcuentabancaria' => $request->input('formapago_idcuentabancaria'.$num)!=''?$request->input('formapago_idcuentabancaria'.$num):0,
                        's_idprestamo_recaudacion' => $idprestamorecaudacion,
                        'idmoneda' => $request->facturacion_idmoneda,
                        's_idaperturacierre' => $idaperturacierre,
                        'idtienda' => $idtienda,
                        'idestado' => 1,
                    ]);
                    $monto_deposito = $monto_deposito+$request->input('formapago_montodeposito'.$num);
                }
                
                if($request->idprestamo_tipoahorro==1){
                }elseif($request->idprestamo_tipoahorro==2){
                    DB::table('s_prestamo_ahorrorecaudacion')->whereId($idprestamorecaudacion)->update([
                        'cronograma_deposito' => $monto_deposito,
                    ]);
                }elseif($request->idprestamo_tipoahorro==3){
                    DB::table('s_prestamo_ahorrorecaudacionlibre')->whereId($idprestamorecaudacion)->update([
                        'monto_deposito' => $monto_deposito,
                    ]);
                }
                    
            } 
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.',
              'idprestamorecaudacion' => $idprestamorecaudacion,
              'idprestamo_tipoahorro' => $request->idprestamo_tipoahorro,
            ]);
        }*/
        if($request->view == 'registrar_ahorrolibre') {
            $rules = [
                'monto_efectivo' => 'required'
            ];
          
            $messages = [];
          
            $monto_deposito = 0;
            if(isset($request->formapago_contado_seleccionar)){
                foreach(json_decode($request->formapago_contado_seleccionar) as $value){
                    if($request->input('formapago_idcuentabancaria'.$value->num)==''){
                        $rules = array_merge($rules,[
                            'formapago_idcuentabancaria'.$value->num => 'required',
                        ]);
                    }
                    if($request->input('formapago_montodeposito'.$value->num)==''){
                        $rules = array_merge($rules,[
                            'formapago_montodeposito'.$value->num => 'required',
                        ]);
                    }
                    if($request->input('formapago_voucher'.$value->num)==''){
                        $rules = array_merge($rules,[
                            'formapago_voucher'.$value->num => 'required',
                        ]);
                    }
                    $messages = array_merge($messages,[
                        'formapago_idcuentabancaria'.$value->num.'.required' => 'La "Cuenta Bancaria" es Obligatorio.',
                        'formapago_montodeposito'.$value->num.'.required' => 'El "Monto" es Obligatorio.',
                        'formapago_voucher'.$value->num.'.required' => 'El "Voucher" es Obligatorio.',
                    ]);
                    $monto_deposito = $monto_deposito+$request->input('formapago_montodeposito'.$value->num);
                }
            }
          
            $messages = array_merge($messages,[
                'monto_efectivo.required' => 'El "Monto a Ahorrar" es Obligatorio.',
            ]);
          
            $this->validate($request, $rules, $messages);
            $monto_efectivo = $request->monto_efectivo-$monto_deposito;
            if($monto_efectivo<0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Efectivo no puede ser negativo.'
                ]);
            }
          
            // aperturacaja
            $caja = caja($idtienda, Auth::user()->id);
            if($caja['resultado']!='ABIERTO'){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La Caja debe estar Aperturada.'
                ]);
            }
            $idaperturacierre = $caja['apertura']->id;
            // fin aperturacaja
               
            // obtener ultimo código
            $prestamorecaudacionlibre = DB::table('s_prestamo_ahorrorecaudacionlibre')
                ->where('s_prestamo_ahorrorecaudacionlibre.idtienda',$idtienda)
                ->orderBy('s_prestamo_ahorrorecaudacionlibre.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($prestamorecaudacionlibre!=''){
                $codigo = $prestamorecaudacionlibre->codigo+1;
            }
            // fin obtener ultimo código
          
            $s_prestamo_ahorro = DB::table('s_prestamo_ahorro')
                ->where('s_prestamo_ahorro.id', $request->idprestamo_ahorro)
                ->first();
          
            $idprestamo_ahorrorecaudacionlibre = DB::table('s_prestamo_ahorrorecaudacionlibre')->insertGetId([
                'fecharegistro' => Carbon::now(),
                'codigo' => $codigo,
                'monto_efectivo' => $monto_efectivo,
                'monto_deposito' => $monto_deposito,
                'idaperturacierre' => $idaperturacierre,
                'idprestamo_ahorro' => $request->idprestamo_ahorro,
                'idmoneda' => 1,
                'idcliente' => $s_prestamo_ahorro->idcliente,
                'idcajero' => Auth::user()->id,
                'idestadorecaudacion' => 2,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
          
            // REGISTRAR DEPOSITO
            if(isset($request->formapago_contado_seleccionar)){
                $formadepago = formadepago(
                    $idtienda,
                    $request,
                    's_idprestamo_ahorrorecaudacionlibre',
                    $idprestamo_ahorrorecaudacionlibre,
                );
            }
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.',
                'idprestamo_ahorrorecaudacionlibre' => $idprestamo_ahorrorecaudacionlibre,
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      if($id=='show-index'){
        
          $prestamorecaudacions = DB::table('s_prestamo_ahorrorecaudacionlibre')
              ->join('s_prestamo_ahorro', 's_prestamo_ahorro.id', 's_prestamo_ahorrorecaudacionlibre.idprestamo_ahorro')
              ->join('users as cliente', 'cliente.id', 's_prestamo_ahorrorecaudacionlibre.idcliente')
              ->join('users as cajero', 'cajero.id', 's_prestamo_ahorrorecaudacionlibre.idcajero')
              ->where([
                ['s_prestamo_ahorrorecaudacionlibre.idprestamo_ahorro', $id],
                ['s_prestamo_ahorrorecaudacionlibre.idtienda', $idtienda],
              ])
              ->where([
                ['s_prestamo_ahorrorecaudacionlibre.idtienda', $idtienda],
                ['s_prestamo_ahorrorecaudacionlibre.codigo','LIKE','%'.$request->input('columns')[0]['search']['value'].'%'],
                ['s_prestamo_ahorro.codigo','LIKE','%'.$request->input('columns')[1]['search']['value'].'%'],
                ['s_prestamo_ahorrorecaudacionlibre.fecharegistro','LIKE','%'.$request->input('columns')[2]['search']['value'].'%'],
                ['cliente.apellidos','LIKE','%'.$request->input('columns')[5]['search']['value'].'%'],
                ['cajero.nombre','LIKE','%'.$request->input('columns')[6]['search']['value'].'%']
              ])
              ->orWhere([
                ['s_prestamo_ahorrorecaudacionlibre.idtienda', $idtienda],
                ['s_prestamo_ahorrorecaudacionlibre.codigo','LIKE','%'.$request->input('columns')[0]['search']['value'].'%'],
                ['s_prestamo_ahorro.codigo','LIKE','%'.$request->input('columns')[1]['search']['value'].'%'],
                ['s_prestamo_ahorrorecaudacionlibre.fecharegistro','LIKE','%'.$request->input('columns')[2]['search']['value'].'%'],
                ['cliente.nombre','LIKE','%'.$request->input('columns')[5]['search']['value'].'%'],
                ['cajero.nombre','LIKE','%'.$request->input('columns')[6]['search']['value'].'%']
              ])
              ->select(
                's_prestamo_ahorrorecaudacionlibre.*',
                's_prestamo_ahorro.codigo as ahorrocodigo',
                'cajero.nombre as cajero_nombre',
                DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente'),
              )
              ->orderBy('s_prestamo_ahorrorecaudacionlibre.id','desc')
              ->paginate($request->input('length'), ['*'], 'page', (($request->input('start')/$request->input('length'))+1));
        
          /*$prestamorecaudacions = DB::table('s_prestamo_ahorrorecaudacion')
              ->join('s_prestamo_ahorro', 's_prestamo_ahorro.id', 's_prestamo_ahorrorecaudacion.idprestamo_ahorro')
              ->join('users as cliente', 'cliente.id', 's_prestamo_ahorro.idcliente')
              ->join('users as asesor', 'asesor.id', 's_prestamo_ahorro.idasesor')
              ->where([
                ['s_prestamo_ahorrorecaudacion.idtienda', $idtienda],
                ['s_prestamo_ahorrorecaudacion.codigo','LIKE','%'.$request->input('columns')[0]['search']['value'].'%'],
                ['s_prestamo_ahorro.codigo','LIKE','%'.$request->input('columns')[1]['search']['value'].'%'],
                ['s_prestamo_ahorrorecaudacion.fecharegistro','LIKE','%'.$request->input('columns')[2]['search']['value'].'%'],
                ['cliente.apellidos','LIKE','%'.$request->input('columns')[4]['search']['value'].'%'],
                ['asesor.nombre','LIKE','%'.$request->input('columns')[5]['search']['value'].'%']
              ])
              ->orWhere([
                ['s_prestamo_ahorrorecaudacion.idtienda', $idtienda],
                ['s_prestamo_ahorrorecaudacion.codigo','LIKE','%'.$request->input('columns')[0]['search']['value'].'%'],
                ['s_prestamo_ahorro.codigo','LIKE','%'.$request->input('columns')[1]['search']['value'].'%'],
                ['s_prestamo_ahorrorecaudacion.fecharegistro','LIKE','%'.$request->input('columns')[2]['search']['value'].'%'],
                ['cliente.nombre','LIKE','%'.$request->input('columns')[4]['search']['value'].'%'],
                ['asesor.nombre','LIKE','%'.$request->input('columns')[5]['search']['value'].'%']
              ])
              ->select(
                's_prestamo_ahorrorecaudacion.*',
                's_prestamo_ahorro.codigo as creditocodigo',
                'cliente.nombre as cliente_nombre',
                'asesor.nombre as asesor_nombre',
                DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente')
              )
              ->orderBy('s_prestamo_ahorrorecaudacion.id','desc')
              ->paginate($request->input('length'), ['*'], 'page', (($request->input('start')/$request->input('length'))+1));*/

            // aperturacaja
            $caja = caja($idtienda,Auth::user()->id);
            $idaperturacierre = 0;
            if($caja['resultado']=='ABIERTO'){
                $idaperturacierre = $caja['apertura']->id;
            }
        
            $tabla = [];
            foreach($prestamorecaudacions as $value){
              
                $estado = '';
                if($value->idestadorecaudacion==1){
                    $estado = '<span class="badge badge-pill badge-info"><i class="fa fa-sync"></i> Pendiente</span>';
                }elseif($value->idestadorecaudacion==2){
                    $estado = '<span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Correcto</span>';
                }elseif($value->idestadorecaudacion==3){
                    $estado = '<span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span>';
                }
              
                $classname = '';
                if($idaperturacierre==$value->idaperturacierre){
                    $classname = 'mx-table-warning';
                   
                }
   
              
                $tabla[] = [
                    'idrecaudacion' => $value->id,
                    'codigo' => str_pad($value->codigo, 8, "0", STR_PAD_LEFT),
                    'codigoahorro' => str_pad($value->ahorrocodigo, 8, "0", STR_PAD_LEFT),
                    'fechapago' => date_format(date_create($value->fecharegistro), "d/m/Y h:i:s A"),
                    'monto_efectivo' => $value->monto_efectivo,
                    'monto_deposito' => $value->monto_deposito,
                    'cliente' => $value->cliente,
                    'cajero_nombre' => $value->cajero_nombre,
                    'estado' => $estado,
                    'idtienda' => $idtienda,
                    'classname' => $classname,
                ];
            }
          
            return json_encode([
                'draw' => $request->input('draw'),
                'recordsTotal' => $prestamorecaudacions->total(),
                'recordsFiltered' => $prestamorecaudacions->total(),
                'data' => $tabla
            ]);
      }
    }

    public function edit(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda = DB::table('tienda')
            ->leftJoin('ubigeo', 'ubigeo.id', 'tienda.idubigeo')
            ->select(
                'tienda.*',
                'ubigeo.nombre as ubigeonombre',
            )
            ->where('tienda.id',$idtienda)
            ->first();
      
      $s_prestamo_ahorro = DB::table('s_prestamo_ahorro')
              ->join('users as cliente', 'cliente.id', 's_prestamo_ahorro.idcliente')
              ->join('users as asesor', 'asesor.id', 's_prestamo_ahorro.idasesor')
              ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_ahorro.idcajero')
              ->leftJoin('ubigeo','ubigeo.id','cliente.idubigeo')
              ->where('s_prestamo_ahorro.id', $id)
              ->select(
                  's_prestamo_ahorro.*',
                  /*'cliente.id as idcliente',
                  'cliente.direccion as cliente_direccion',
                  'ubigeo.id as idubigeo',
                  'ubigeo.nombre as ubigeo',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos,", ",cliente.nombre),
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos)) as cliente'),
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos'*/
              )
              ->first();

      if($request->view == 'recaudacion') {
          if($s_prestamo_ahorro->idprestamo_tipoahorro==1){ 
          }
          elseif($s_prestamo_ahorro->idprestamo_tipoahorro==2){
          }
          elseif($s_prestamo_ahorro->idprestamo_tipoahorro==3){
              return view('layouts/backoffice/tienda/sistema/prestamo/ahorrorecaudacion/recaudacion_libre',[
                  'tienda' => $tienda,
                  's_prestamo_ahorro' => $s_prestamo_ahorro,
              ]);
          }
              
      }
      elseif ($request->view == 'pagolibre_index') {
          $s_prestamo_ahorrorecaudacionlibre = DB::table('s_prestamo_ahorrorecaudacionlibre')
              ->join('users as cliente', 'cliente.id', 's_prestamo_ahorrorecaudacionlibre.idcliente')
              ->join('users as cajero', 'cajero.id', 's_prestamo_ahorrorecaudacionlibre.idcajero')
              ->where([
                ['s_prestamo_ahorrorecaudacionlibre.idprestamo_ahorro', $id],
                ['s_prestamo_ahorrorecaudacionlibre.idtienda', $idtienda],
              ])
              ->select(
                's_prestamo_ahorrorecaudacionlibre.*',
                'cajero.nombre as cajero_nombre'
              )
              ->orderBy('s_prestamo_ahorrorecaudacionlibre.id','desc')
              ->get();

          // aperturacaja
          $caja = caja($idtienda,Auth::user()->id);
          $idaperturacierre = 0;
          if($caja['resultado']=='ABIERTO'){
              $idaperturacierre = $caja['apertura']->id;
          }
          return view('layouts/backoffice/tienda/sistema/prestamo/ahorrorecaudacion/pagolibre_index',[
              'tienda' => $tienda,
              's_prestamo_ahorro' => $s_prestamo_ahorro,
              's_prestamo_ahorrorecaudacionlibre' => $s_prestamo_ahorrorecaudacionlibre,
              'idaperturacierre' => $idaperturacierre,
          ]);
      }
      elseif ($request->view == 'pagolibre_ticket') {
          return view('layouts/backoffice/tienda/sistema/prestamo/ahorrorecaudacion/pagolibre_ticket',[
              'tienda' => $tienda,
              's_prestamo_ahorro' => $s_prestamo_ahorro,
              'idprestamo_ahorrorecaudacionlibre' => $request->idprestamo_ahorrorecaudacionlibre,
          ]);
      }
      elseif ($request->view == 'pagolibre_anular') {
          $s_prestamo_ahorrorecaudacionlibre = DB::table('s_prestamo_ahorrorecaudacionlibre')
              ->join('users as cliente', 'cliente.id', 's_prestamo_ahorrorecaudacionlibre.idcliente')
              ->join('users as cajero', 'cajero.id', 's_prestamo_ahorrorecaudacionlibre.idcajero')
              ->where('s_prestamo_ahorrorecaudacionlibre.id', $request->idprestamo_ahorrorecaudacionlibre)
              ->select(
                  's_prestamo_ahorrorecaudacionlibre.*',
                  'cliente.identificacion as cliente_identificacion',
                  'cliente.nombre as cliente_nombre',
                  'cliente.apellidos as cliente_apellidos',
                  'cajero.identificacion as cajero_identificacion',
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos',
              )
              ->first();
          return view('layouts/backoffice/tienda/sistema/prestamo/ahorrorecaudacion/pagolibre_anular',[
              'tienda' => $tienda,
              's_prestamo_ahorro' => $s_prestamo_ahorro,
              's_prestamo_ahorrorecaudacionlibre' => $s_prestamo_ahorrorecaudacionlibre,
          ]);
      }
      elseif ($request->view == 'ticketpdf_pagolibre') {
          $prestamo_ahorrorecaudacionlibre = DB::table('s_prestamo_ahorrorecaudacionlibre')
              ->join('users as cliente', 'cliente.id', 's_prestamo_ahorrorecaudacionlibre.idcliente')
              ->join('s_prestamo_ahorro', 's_prestamo_ahorro.id', 's_prestamo_ahorrorecaudacionlibre.idprestamo_ahorro')
              ->join('s_moneda', 's_moneda.id', 's_prestamo_ahorrorecaudacionlibre.idmoneda')
              ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_ahorrorecaudacionlibre.idcajero')
              ->leftJoin('ubigeo as clienteubigeo','clienteubigeo.id','cliente.idubigeo')
              ->where('s_prestamo_ahorrorecaudacionlibre.id', $request->idprestamo_ahorrorecaudacionlibre)
              ->select(
                  's_prestamo_ahorrorecaudacionlibre.*',
                  'cliente.identificacion as cliente_identificacion',
                  'clienteubigeo.nombre as ubigeo',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente'),
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos',
                  's_moneda.simbolo as monedasimbolo',
                  's_moneda.nombre as monedanombre',
                  's_prestamo_ahorro.codigo as ahorrocodigo'
              )
              ->first();
        
          $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamo/ahorrorecaudacion/ticketpdf_pagolibre', [
              'tienda' => $tienda,
              'prestamo_ahorrorecaudacionlibre' => $prestamo_ahorrorecaudacionlibre,
          ]);
          return $pdf->stream('Ticket Ahorro.pdf');
      }
      elseif ($request->view == 'retirolibre_index') {
          $s_prestamo_ahorroretirolibre = DB::table('s_prestamo_ahorroretirolibre')
              ->join('users as cliente', 'cliente.id', 's_prestamo_ahorroretirolibre.idcliente')
              ->join('users as cajero', 'cajero.id', 's_prestamo_ahorroretirolibre.idcajero')
              ->where([
                ['s_prestamo_ahorroretirolibre.idprestamo_ahorro', $id],
                ['s_prestamo_ahorroretirolibre.idtienda', $idtienda],
              ])
              ->select(
                's_prestamo_ahorroretirolibre.*',
                'cajero.nombre as cajero_nombre'
              )
              ->orderBy('s_prestamo_ahorroretirolibre.id','desc')
              ->get();

          // aperturacaja
          $caja = caja($idtienda,Auth::user()->id);
          $idaperturacierre = 0;
          if($caja['resultado']=='ABIERTO'){
              $idaperturacierre = $caja['apertura']->id;
          }
        
          return view('layouts/backoffice/tienda/sistema/prestamo/ahorrorecaudacion/retirolibre_index',[
              'tienda' => $tienda,
              's_prestamo_ahorro' => $s_prestamo_ahorro,
              's_prestamo_ahorroretirolibre' => $s_prestamo_ahorroretirolibre,
              'idaperturacierre' => $idaperturacierre,
          ]);
      }
      elseif ($request->view == 'retirolibre_registrar') {
          return view('layouts/backoffice/tienda/sistema/prestamo/ahorrorecaudacion/retirolibre_registrar',[
              'tienda' => $tienda,
              's_prestamo_ahorro' => $s_prestamo_ahorro,
              'idprestamo_ahorro' => $request->idprestamo_ahorro,
          ]);
      }
      elseif ($request->view == 'retirolibre_ticket') {
          return view('layouts/backoffice/tienda/sistema/prestamo/ahorrorecaudacion/retirolibre_ticket',[
              'tienda' => $tienda,
              's_prestamo_ahorro' => $s_prestamo_ahorro,
              'idprestamo_ahorroretirolibre' => $request->idprestamo_ahorroretirolibre,
          ]);
      }
      elseif ($request->view == 'retirolibre_anular') {
          $s_prestamo_ahorroretirolibre = DB::table('s_prestamo_ahorroretirolibre')
              ->join('users as cliente', 'cliente.id', 's_prestamo_ahorroretirolibre.idcliente')
              ->join('users as cajero', 'cajero.id', 's_prestamo_ahorroretirolibre.idcajero')
              ->where('s_prestamo_ahorroretirolibre.id', $request->idprestamo_ahorroretirolibre)
              ->select(
                  's_prestamo_ahorroretirolibre.*',
                  'cliente.identificacion as cliente_identificacion',
                  'cliente.nombre as cliente_nombre',
                  'cliente.apellidos as cliente_apellidos',
                  'cajero.identificacion as cajero_identificacion',
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos',
              )
              ->first();
          return view('layouts/backoffice/tienda/sistema/prestamo/ahorrorecaudacion/retirolibre_anular',[
              'tienda' => $tienda,
              's_prestamo_ahorro' => $s_prestamo_ahorro,
              's_prestamo_ahorroretirolibre' => $s_prestamo_ahorroretirolibre,
          ]);
      }
      elseif ($request->view == 'ticketpdf_retirolibre') {
          $prestamo_ahorroretirolibre = DB::table('s_prestamo_ahorroretirolibre')
              ->join('users as cliente', 'cliente.id', 's_prestamo_ahorroretirolibre.idcliente')
              ->join('s_prestamo_ahorro', 's_prestamo_ahorro.id', 's_prestamo_ahorroretirolibre.idprestamo_ahorro')
              ->join('s_moneda', 's_moneda.id', 's_prestamo_ahorroretirolibre.idmoneda')
              ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_ahorroretirolibre.idcajero')
              ->leftJoin('ubigeo as clienteubigeo','clienteubigeo.id','cliente.idubigeo')
              ->where('s_prestamo_ahorroretirolibre.id', $request->idprestamo_ahorroretirolibre)
              ->select(
                  's_prestamo_ahorroretirolibre.*',
                  'cliente.identificacion as cliente_identificacion',
                  'clienteubigeo.nombre as ubigeo',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente'),
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos',
                  's_moneda.simbolo as monedasimbolo',
                  's_moneda.nombre as monedanombre',
                  's_prestamo_ahorro.codigo as ahorrocodigo'
              )
              ->first();
        
          $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamo/ahorrorecaudacion/ticketpdf_retirolibre', [
              'tienda' => $tienda,
              'prestamo_ahorroretirolibre' => $prestamo_ahorroretirolibre,
          ]);
          return $pdf->stream('Ticket Ahorro.pdf');
      }
      elseif ($request->view == 'retirolibre_index') {
          $s_prestamo_ahorroretirolibre = DB::table('s_prestamo_ahorroretirolibre')
              ->join('users as cliente', 'cliente.id', 's_prestamo_ahorroretirolibre.idcliente')
              ->join('users as cajero', 'cajero.id', 's_prestamo_ahorroretirolibre.idcajero')
              ->where([
                ['s_prestamo_ahorroretirolibre.idprestamo_ahorro', $id],
                ['s_prestamo_ahorroretirolibre.idtienda', $idtienda],
              ])
              ->select(
                's_prestamo_ahorroretirolibre.*',
                'cajero.nombre as cajero_nombre'
              )
              ->orderBy('s_prestamo_ahorroretirolibre.id','desc')
              ->get();

          // aperturacaja
          $caja = caja($idtienda,Auth::user()->id);
          $idaperturacierre = 0;
          if($caja['resultado']=='ABIERTO'){
              $idaperturacierre = $caja['apertura']->id;
          }
        
          return view('layouts/backoffice/tienda/sistema/prestamo/ahorrorecaudacion/retirolibre_index',[
              'tienda' => $tienda,
              's_prestamo_ahorro' => $s_prestamo_ahorro,
              's_prestamo_ahorroretirolibre' => $s_prestamo_ahorroretirolibre,
              'idaperturacierre' => $idaperturacierre,
          ]);
      }
      elseif ($request->view == 'resumen_index') {
        
          $total_recaudacion_efectivo = DB::table('s_prestamo_ahorrorecaudacionlibre')
              ->where('s_prestamo_ahorrorecaudacionlibre.idtienda', $idtienda)
              ->where('s_prestamo_ahorrorecaudacionlibre.idprestamo_ahorro', $s_prestamo_ahorro->id)
              ->where('s_prestamo_ahorrorecaudacionlibre.idestadorecaudacion', 2)
              ->sum('s_prestamo_ahorrorecaudacionlibre.monto_efectivo');
          $total_recaudacion_deposito = DB::table('s_prestamo_ahorrorecaudacionlibre')
              ->where('s_prestamo_ahorrorecaudacionlibre.idtienda', $idtienda)
              ->where('s_prestamo_ahorrorecaudacionlibre.idprestamo_ahorro', $s_prestamo_ahorro->id)
              ->where('s_prestamo_ahorrorecaudacionlibre.idestadorecaudacion', 2)
              ->sum('s_prestamo_ahorrorecaudacionlibre.monto_deposito');
        
          $total_retiro_efectivo = DB::table('s_prestamo_ahorroretirolibre')
              ->where('s_prestamo_ahorroretirolibre.idtienda', $idtienda)
              ->where('s_prestamo_ahorroretirolibre.idprestamo_ahorro', $s_prestamo_ahorro->id)
              ->where('s_prestamo_ahorroretirolibre.idestadoahorroretirolibre', 2)
              ->sum('s_prestamo_ahorroretirolibre.monto_efectivo');
          $total_retiro_deposito = DB::table('s_prestamo_ahorroretirolibre')
              ->where('s_prestamo_ahorroretirolibre.idtienda', $idtienda)
              ->where('s_prestamo_ahorroretirolibre.idprestamo_ahorro', $s_prestamo_ahorro->id)
              ->where('s_prestamo_ahorroretirolibre.idestadoahorroretirolibre', 2)
              ->sum('s_prestamo_ahorroretirolibre.monto_deposito');
        
          $total_saldo = ($total_recaudacion_efectivo+$total_recaudacion_deposito)-($total_retiro_efectivo+$total_retiro_deposito);
        
          return view('layouts/backoffice/tienda/sistema/prestamo/ahorrorecaudacion/resumen_index',[
              'tienda' => $tienda,
              's_prestamo_ahorro' => $s_prestamo_ahorro,
              'total_recaudacion' => number_format($total_recaudacion_efectivo+$total_recaudacion_deposito, 2, '.', ''),
              'total_retiro' => number_format($total_retiro_efectivo+$total_retiro_deposito, 2, '.', ''),
              'total_saldo' => number_format($total_saldo, 2, '.', ''),
          ]);
      }

    }

    public function update(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(), $idtienda);
        if ($request->input('view') == 'pagolibre_anular') {
            // aperturacaja
            $caja = caja($idtienda,Auth::user()->id);
            $idaperturacierre = 0;
            if($caja['resultado']!='ABIERTO'){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La Caja debe estar Aperturada.'
                ]);
            }
            $idaperturacierre = $caja['apertura']->id;
            // fin aperturacaja
          
            $s_prestamo_ahorrorecaudacion = DB::table('s_prestamo_ahorrorecaudacionlibre')->whereId($id)->first();
          
            if($idaperturacierre!=$s_prestamo_ahorrorecaudacion->idaperturacierre){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La Apertura no coincide con la apertura actual.'
                ]);
            }
          
            DB::table('s_prestamo_ahorrorecaudacionlibre')->whereId($id)->update([
                'fechaanulado' => Carbon::now(),
                'idestadorecaudacion' => 3
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha anulado correctamente.'
            ]);
        }
        elseif($request->view == 'retirolibre_registrar'){
            $rules = [
                'montoretiro' => 'required',
            ];
          
            $messages = [
                'montoretiro.required' => 'El "Monto a Retirar" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);
          
            // aperturacaja
            $caja = caja($idtienda, Auth::user()->id);
            if($caja['resultado']!='ABIERTO'){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La Caja debe estar Aperturada.'
                ]);
            }
            $idaperturacierre = $caja['apertura']->id;
            // fin aperturacaja
          
            $s_prestamo_ahorro = DB::table('s_prestamo_ahorro')->whereId($id)->first();
          
            // obtener ultimo código
            $prestamoahorroretirolibre = DB::table('s_prestamo_ahorroretirolibre')
                ->where('s_prestamo_ahorroretirolibre.idtienda',$idtienda)
                ->orderBy('s_prestamo_ahorroretirolibre.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($prestamoahorroretirolibre!=''){
                $codigo = $prestamoahorroretirolibre->codigo+1;
            }
            // fin obtener ultimo código
            $idprestamorecaudacion = DB::table('s_prestamo_ahorroretirolibre')->insertGetId([
                'fecharegistro' => Carbon::now(),
                'fecharetiro' => Carbon::now(),
                'codigo' => $codigo,
                'monto_efectivo' => $request->montoretiro,
                'monto_deposito' => 0,
                's_idaperturacierre' => $idaperturacierre,
                'idmoneda' => 1,
                'idcajero' => Auth::user()->id,
                'idcliente' => $s_prestamo_ahorro->idcliente,
                'idprestamo_ahorro' => $id,
                'idestadoahorroretirolibre' => 2, // 1=pendiente,2=confirmado,3=anulado
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
        }
        elseif ($request->input('view') == 'retirolibre_anular') {
            // aperturacaja
            $caja = caja($idtienda,Auth::user()->id);
            $idaperturacierre = 0;
            if($caja['resultado']!='ABIERTO'){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La Caja debe estar Aperturada.'
                ]);
            }
            $idaperturacierre = $caja['apertura']->id;
            // fin aperturacaja
          
          
            $s_prestamo_ahorroretiro = DB::table('s_prestamo_ahorroretirolibre')->whereId($id)->first();

          
            if($idaperturacierre!=$s_prestamo_ahorroretiro->s_idaperturacierre){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La Apertura no coincide con la apertura actual.'
                ]);
            }
          
            DB::table('s_prestamo_ahorroretirolibre')->whereId($id)->update([
                'fechaanulado' => Carbon::now(),
                'idestadoahorroretirolibre' => 3
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha anulado correctamente.'
            ]);
        }
    }

    public function destroy(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if($request->input('view') == 'eliminar') {
            DB::table('s_prestamo_ahorrorecaudacion')->where('idtienda',$idtienda)->where('id',$id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
