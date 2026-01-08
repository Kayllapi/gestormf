<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;
use Image;
use Intervention\Image\ImageManager;

class PrestamoCobranzaController extends Controller
{
    public function index(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        return view('layouts/backoffice/tienda/sistema/prestamocobranza/index', [
            'tienda'      => $tienda,
        ]);
    }

    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda   = DB::table('tienda')->whereId($idtienda)->first();
        return view('layouts/backoffice/tienda/sistema/prestamocobranza/create', [
            'tienda' => $tienda
        ]);
    }

    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {

            $rules = [
                'idprestamo_credito' => 'required',
                'idtipopago' => 'required',
                'acuenta' => 'required',
            ];
            if($request->input('idtipopago')==1){
                $rules = array_merge($rules,[
                    'hastacuota' => 'required',
                    //'montorecibido' => 'required'
                ]);
                
            }
            if($request->input('check_moradescuento')=='on'){
                $rules = array_merge($rules,[
                    'moradescuento' => 'required',
                    'moradescuento_detalle' => 'required'
                ]);
            }
            if($request->input('idtipopago')==2){
                $rules = array_merge($rules,[
                    'montocompleto' => 'required'
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
          
            
            $messages = [];
            //$monto = 0;
            $monto_deposito = 0;
          
            if(isset($request->seleccionar_formapago)){
            for($i=0;$i<count($request->seleccionar_formapago);$i++){
                $num = $request->seleccionar_formapago[$i];

                    if($request->input('formapago_idcuentabancaria'.$num)==''){
                        $rules = array_merge($rules,[
                            'formapago_idcuentabancaria'.$num => 'required'
                        ]);
                        $messages = array_merge($messages,[
                            'formapago_idcuentabancaria'.$num.'.required' => 'La "Cuenta Bancaria" es Obligatorio.',
                        ]);
                    }
                    if($request->input('formapago_numerooperacion'.$num)==''){
                        $rules = array_merge($rules,[
                            'formapago_numerooperacion'.$num => 'required'
                        ]);
                        $messages = array_merge($messages,[
                            'formapago_numerooperacion'.$num.'.required' => 'El "Número de Operación" es Obligatorio.',
                        ]);
                    }
                    if($request->input('formapago_fecha'.$num)==''){
                        $rules = array_merge($rules,[
                            'formapago_fecha'.$num => 'required'
                        ]);
                        $messages = array_merge($messages,[
                            'formapago_fecha'.$num.'.required' => 'La "Fecha" es Obligatorio.',
                        ]);
                    }
                    if($request->input('formapago_hora'.$num)==''){
                        $rules = array_merge($rules,[
                            'formapago_hora'.$num => 'required'
                        ]);
                        $messages = array_merge($messages,[
                            'formapago_hora'.$num.'.required' => 'La "Hora" es Obligatorio.',
                        ]);
                    }
                    if($request->input('formapago_montodeposito'.$num)==''){
                        $rules = array_merge($rules,[
                            'formapago_montodeposito'.$num => 'required'
                        ]);
                        $messages = array_merge($messages,[
                            'formapago_montodeposito'.$num.'.required' => 'El "Monto" es Obligatorio.',
                        ]);
                    }
                    if($request->input('formapago_voucher'.$num)==''){
                        $rules = array_merge($rules,[
                            'formapago_voucher'.$num => 'required'
                        ]);
                        $messages = array_merge($messages,[
                            'formapago_voucher'.$num.'.required' => 'El "Voucher" es Obligatorio.',
                        ]);
                    }
                    //$monto = $monto+$request->input('formapago_montocontado'.$num);
                    $monto_deposito = $monto_deposito+$request->input('formapago_montodeposito'.$num);
            }
            }
            
            $messages = array_merge($messages,[
                'idprestamo_credito.required' => 'El "Cliente" es Obligatorio.',
                'idtipopago.required' => 'El "Tipo de Pago" es Obligatorio.',
                'acuenta.required' => 'El "A Cuenta" es Obligatorio.',
                'idtipopago.required' => 'El "Tipo de Pago" es Obligatorio.',
                'moradescuento.required' => 'El "Total de Moras" es Obligatorio.',
                'moradescuento_detalle.required' => 'El "Motivo de descuento" es Obligatorio.',
                'montocompleto.required' => 'El "Monto Completo" es Obligatorio.',
                'hastacuota.required' => 'El "Hasta Cuota" es Obligatorio.',
                'montorecibido.required' => 'El "Monto Recibido" es Obligatorio.',
                'facturacion_idcliente.required' => 'El "Cliente" es Obligatorio.',
                'facturacion_direccion.required' => 'La "Dirección" es Obligatorio.',
                'facturacion_idubigeo.required' => 'El "Ubigeo" es Obligatorio.',
                'facturacion_idagencia.required' => 'La "Agencia" es Obligatorio.',
                'facturacion_idmoneda.required' => 'La "Moneda" es Obligatorio.',
                'facturacion_idtipocomprobante.required' => 'El "Monto Recibido" es Obligatorio.',
                'seleccionar_formapago.required' => 'La "Forma de Pago" es Obligatorio.',
            ]);
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
          
            $prestamo_cobranza = DB::table('s_prestamo_cobranza')
                ->where('s_prestamo_cobranza.idprestamo_credito', $request->idprestamo_credito)
                ->where('s_prestamo_cobranza.idestadocobranza', 1)
                ->limit(1)
                ->first();
          
            if($prestamo_cobranza!=''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Hay una cobranza pendiente de este Crédito!!.'
                ]);
            }
          

            $cronograma = prestamo_cobranza_cronograma($idtienda,$request->idprestamo_credito,$request->moradescuento,$request->montocompleto,$request->idtipopago,$request->hastacuota,'',$request->descuentointeres);
            if(configuracion($idtienda,'prestamo_estadodescuentointeres')['valor']==1 && configuracion($idtienda,'usuario_estadodescuentointeres',Auth::user()->id)['valor']==1){
                if($request->descuentointeres>$cronograma['select_interesrestante']){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El "Descontar Interes" debe ser menor o igual que al "Total Interes".'
                    ]);
              
                }
            }
            // actvar descuento
            if($request->input('check_moradescuento')=='on'){
                if(($cronograma['select_mora']-$cronograma['morarestante']) < $request->moradescuento) {
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El "Descontar Mora" debe ser menor o igual que al "Total de Moras".'
                    ]);
                }
            }
          
            // tipo de pago
            $monto_efectivo = 0;
            $hastacuota = 0;
            $montorecibido = 0;
            $vuelto = 0;
            if($request->idtipopago == 1) {
                $monto_efectivo = $cronograma['select_cuotaapagarredondeado']+$cronograma['select_abono']-$monto_deposito;
                $mont_recibido = $request->montorecibido!=''?$request->montorecibido:0;
                if($monto_efectivo > $mont_recibido) { // $cronograma['select_cuotaapagarredondeado']
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El "Monto Recibido" debe ser mayor o igual que al "EFECTIVO".'
                    ]);
                }
                $hastacuota = $request->hastacuota;
                $montorecibido = $mont_recibido;
                $vuelto = $montorecibido-$monto_efectivo;
            }elseif($request->idtipopago == 2) {
                $monto_efectivo = $request->montocompleto+$cronograma['select_abono']-$monto_deposito;
                $montorecibido = $request->montocompleto;
                $vuelto = 0;
            }
          
            if($monto_efectivo < 0) { 
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "EFECTIVO" debe ser mayor o igual "0.00".'
                ]);
            }
          
            // descontar interes
            $interesdescontado = 0;
            if($request->input('check_interesdescuento')=='on'){
                $interesdescontado = $cronograma['select_interesrestante'];
            }
          
            // obtener ultimo código
            $prestamocobranza = DB::table('s_prestamo_cobranza')
                ->where('s_prestamo_cobranza.idtienda',$idtienda)
                ->orderBy('s_prestamo_cobranza.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($prestamocobranza!=''){
                $codigo = $prestamocobranza->codigo+1;
            }
            // fin obtener ultimo código
            //dd($mont_recibido);
            $idprestamocobranza = DB::table('s_prestamo_cobranza')->insertGetId([
                'fecharegistro' => Carbon::now(),
                'codigo' => $codigo,
                'cuota' => $cronograma['select_cuota'],
                'proximo_vencimiento' => $cronograma['proximo_vencimiento'],
                'interesdescontado' => $interesdescontado,
                'cronograma_idtipopago' => $request->idtipopago,
                'cronograma_hastacuota' => $hastacuota,
                'cronograma_totalcuota' => $cronograma['select_cuota'],
                'cronograma_acuentaanterior' => $cronograma['select_acuentaanterior'],
                'cronograma_acuentaproxima' => $cronograma['select_acuentaproxima'],
                'cronograma_moratotal' => $cronograma['select_mora'],
                'cronograma_moradescuento' => $cronograma['select_moradescontado'],
                'cronograma_morapagar' => $cronograma['select_moraapagar'],
                'cronograma_total' => $cronograma['select_cuotaapagar'],
                'cronograma_totalredondeado' => $cronograma['select_cuotaapagarredondeado'],
                'cronograma_abono' => $cronograma['select_abono'],
                'cronograma_interesdescuento' => $cronograma['select_interesdescuento'],
                'cronograma_montorecibido' => $montorecibido,
                'cronograma_pagado' => $monto_efectivo,
                'cronograma_vuelto' => $vuelto,
                'cronograma_deposito' => $monto_deposito,
                'cronograma_ultimonumerocuota' => $cronograma['select_ultimonumerocuota'],
                'cronograma_morapendientefinal' => $cronograma['morapendientefinal'],
                'cliente_direccion' => $request->facturacion_direccion,
                'cliente_idubigeo' => $request->facturacion_idubigeo,
                's_idaperturacierre' => $idaperturacierre,
                'idprestamo_credito' => $request->idprestamo_credito,
                'idtipocomprobante' => $request->facturacion_idtipocomprobante,
                'idmoneda' => $request->facturacion_idmoneda,
                'idagencia' => $request->facturacion_idagencia,
                'idcliente' => $request->facturacion_idcliente,
                'idasesor' => $cronograma['creditosolicitud']->idasesor,
                'idcajero' => Auth::user()->id,
                'idestadocobranza' => 2,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
       
            foreach ($cronograma['cuotas_pendientes_seleccionados'] as $value) {
        
                if($value['estado'] == 'CANCELADO'){
                    DB::table('s_prestamo_cobranzadetalle')->insert([
                        'idprestamo_cobranza' => $idprestamocobranza,
                        'idprestamo_creditodetalle' => $value['idprestamo_creditodetalle'],
                        'idtienda' => $idtienda,
                        'idestado' => 1,
                    ]);
                    
                    DB::table('s_prestamo_creditodetalle')->whereId($value['idprestamo_creditodetalle'])->update([
                        'atraso' => $value['atraso'],
                        'mora' => $value['mora'],
                        'moradescuento' => $value['moradescontado'],
                        'moraapagar' => $value['moraapagar'],
                        'cuotapago' => $value['cuotapago'],
                        'acuenta' => $value['acuenta'],
                        'cuotaapagar' => $value['cuotaapagar'],
                        'interesdescontado' => 0,
                        'montorefinanciado' => 0,
                        'idestadocobranza' => 2
                    ]);
                  
                    DB::table('s_prestamo_cobranza')->whereId($idprestamocobranza)->update([
                        'cronograma_acuentaanterior' => 0,
                    ]);
                }
                elseif($value['estado'] == 'ACUENTA'){
                    DB::table('s_prestamo_cobranzadetalle')->insert([
                        'idprestamo_cobranza' => $idprestamocobranza,
                        'idprestamo_creditodetalle' => $value['idprestamo_creditodetalle'],
                        'idtienda' => $idtienda,
                        'idestado' => 1,
                    ]);
                    DB::table('s_prestamo_creditodetalle')->whereId($value['idprestamo_creditodetalle'])->update([
                        'acuenta' => $value['acuenta'],
                    ]);
                }
                    
            }
          
            // PAGOS EN DEPOSITO
            if(isset($request->seleccionar_formapago)){
                $monto_deposito = 0;
                for($i=0;$i<count($request->seleccionar_formapago);$i++){
                    $num = $request->seleccionar_formapago[$i];

                    $imagen = uploadfile('','',$request->file('formapago_voucher'.$num),'/public/backoffice/tienda/'.$idtienda.'/cobranza/');
                    DB::table('s_formapagodetalle')->insert([
                        'fecharegistro' => Carbon::now(),
                        'numerocuenta' => $request->input('formapago_numerocuenta'.$num)!=''?$request->input('formapago_numerocuenta'.$num):'',
                        'numerooperacion' => $request->input('formapago_numerooperacion'.$num)!=''?$request->input('formapago_numerooperacion'.$num):'',
                        'banco' => $request->input('formapago_banco'.$num)!=''?$request->input('formapago_banco'.$num):'',
                        'fecha' => $request->input('formapago_fecha'.$num)!=''?$request->input('formapago_fecha'.$num):'',
                        'hora' => $request->input('formapago_hora'.$num)!=''?$request->input('formapago_hora'.$num):'',
                        'monto' => $request->input('formapago_montodeposito'.$num),
                        'voucher' => $imagen,
                        's_idcuentabancaria' => $request->input('formapago_idcuentabancaria'.$num)!=''?$request->input('formapago_idcuentabancaria'.$num):0,
                        's_idprestamo_cobranza' => $idprestamocobranza,
                        'idmoneda' => $request->facturacion_idmoneda,
                        //'s_idaperturacierre' => $idaperturacierre,
                        'idtienda' => $idtienda,
                        'idestado' => 1,
                    ]);
                    $monto_deposito = $monto_deposito+$request->input('formapago_montodeposito'.$num);
                }
                DB::table('s_prestamo_cobranza')->whereId($idprestamocobranza)->update([
                    'cronograma_deposito' => $monto_deposito,
                ]);
            }
          
            // DESCUENTO MORA DESDE COBRANZA
            if($request->input('check_moradescuento')=='on'){
                prestamo_registrar_mora($idtienda,$request->idprestamo_credito,Auth::user()->id,$cronograma['select_moradescontado'],'',2);
            }
        
            // Descuento de mora pendiente por tipo de pago
            /*if($request->idtipopago==1){
                $prestamo_credito = DB::table('s_prestamo_credito')
                    ->whereId($request->idprestamo_credito)
                    ->first();
                if($prestamo_credito->numerocuota==$request->hastacuota && $cronograma['morapendiente']>0){
                    DB::table('s_prestamo_moradetalle')
                        ->where('s_prestamo_moradetalle.idprestamo_credito', $request->idprestamo_credito)
                        ->where('s_prestamo_moradetalle.idestado', 1)
                        ->whereIn('s_prestamo_moradetalle.idestadomoradetalle', [1,2])
                        ->update([
                        'fechaanulado' => Carbon::now(),
                        'idestadomoradetalle' => 4,
                    ]);
                }
            }
            elseif($request->idtipopago==2){
                    DB::table('s_prestamo_moradetalle')
                        ->where('s_prestamo_moradetalle.idprestamo_credito', $request->idprestamo_credito)
                        ->where('s_prestamo_moradetalle.idestado', 1)
                        ->whereIn('s_prestamo_moradetalle.idestadomoradetalle', [1,2])
                        ->update([
                        'fechaanulado' => Carbon::now(),
                        'idestadomoradetalle' => 4,
                    ]);
            }*/
          
            // Actualizar pagos de creditos
            $cronograma = prestamo_cobranza_cronograma($idtienda,$cronograma['creditosolicitud']->id,0,0,1,0);
            $idestadocobranza = 1; // PENDIENTE
            if(count($cronograma['cuotas_pendientes'])==0){
                $idestadocobranza = 2; // CANCELADO
            }
            DB::table('s_prestamo_credito')->whereId($cronograma['creditosolicitud']->id)->update([
                'cronograma_primeratraso' => $cronograma['primeratraso'],
                'cronograma_total_cancelada_atraso' => $cronograma['total_cancelada_atraso'],
                'cronograma_total_cancelada_cuota' => $cronograma['total_cancelada_cuota'],
                'cronograma_total_cancelada_mora' => $cronograma['total_cancelada_mora'],
                'cronograma_total_cancelada_moradescontado' => $cronograma['total_cancelada_moradescontado'],
                'cronograma_total_cancelada_moraapagar' => $cronograma['total_cancelada_moraapagar'],
                'cronograma_total_cancelada_acuenta' => $cronograma['total_cancelada_acuenta'],
                'cronograma_total_cancelada_cuotapago' => $cronograma['total_cancelada_cuotapago'],
                'cronograma_total_vencida_atraso' => $cronograma['total_vencida_atraso'],
                'cronograma_total_vencida_cuota' => $cronograma['total_vencida_cuota'],
                'cronograma_total_vencida_mora' => $cronograma['total_vencida_mora'],
                'cronograma_total_vencida_moradescontado' => $cronograma['total_vencida_moradescontado'],
                'cronograma_total_vencida_moraapagar' => $cronograma['total_vencida_moraapagar'],
                'cronograma_total_vencida_acuenta' => $cronograma['total_vencida_acuenta'],
                'cronograma_total_vencida_cuotapago' => $cronograma['total_vencida_cuotapago'],
                'cronograma_total_restante_atraso' => $cronograma['total_restante_atraso'],
                'cronograma_total_restante_cuota' => $cronograma['total_restante_cuota'],
                'cronograma_total_restante_mora' => $cronograma['total_restante_mora'],
                'cronograma_total_restante_moradescontado' => $cronograma['total_restante_moradescontado'],
                'cronograma_total_restante_moraapagar' => $cronograma['total_restante_moraapagar'],
                'cronograma_total_restante_acuenta' => $cronograma['total_restante_acuenta'],
                'cronograma_total_restante_cuotapago' => $cronograma['total_restante_cuotapago'],
                'cronograma_total_pendiente_atraso' => $cronograma['total_pendiente_atraso'],
                'cronograma_total_pendiente_cuota' => $cronograma['total_pendiente_cuota'],
                'cronograma_total_pendiente_mora' => $cronograma['total_pendiente_mora'],
                'cronograma_total_pendiente_moradescontado' => $cronograma['total_pendiente_moradescontado'],
                'cronograma_total_pendiente_moraapagar' => $cronograma['total_pendiente_moraapagar'],
                'cronograma_total_pendiente_acuenta' => $cronograma['total_pendiente_acuenta'],
                'cronograma_total_pendiente_cuotapago' => $cronograma['total_pendiente_cuotapago'],
                'idestadocobranza' => $idestadocobranza,
            ]);
          
            // Emitir Comprobante
            /*if($request->input('facturacion_idtipocomprobante')==2 or $request->input('facturacion_idtipocomprobante')==3){
                $result = facturar_venta(
                    $idtienda,
                    $request->input('facturacion_idtipocomprobante'),
                    $request->input('facturacion_idagencia'),
                    $idventa
                );
            }*/
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.',
              'idprestamocobranza' => $idprestamocobranza,
              'idestadocobranza' => $idestadocobranza
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      if($id=='show-index'){
          
        $where = [];
        $where[] = ['s_prestamo_cobranza.idtienda', $idtienda];
        $where[] = ['s_prestamo_cobranza.idcajero',Auth::user()->id];
        if($request->input('columns')[0]['search']['value']!=''){ $where[] = ['s_prestamo_cobranza.codigo','LIKE','%'.$request->input('columns')[0]['search']['value'].'%']; }
        if($request->input('columns')[1]['search']['value']!=''){ $where[] = ['s_prestamo_credito.codigo','LIKE','%'.$request->input('columns')[1]['search']['value'].'%']; }
        if($request->input('columns')[2]['search']['value']!=''){ $where[] = ['s_prestamo_cobranza.fecharegistro','LIKE','%'.$request->input('columns')[2]['search']['value'].'%']; }
        if($request->input('columns')[4]['search']['value']!=''){ $where[] = ['cliente.apellidos','LIKE','%'.$request->input('columns')[4]['search']['value'].'%']; }
        if($request->input('columns')[5]['search']['value']!=''){ $where[] = ['asesor.nombre','LIKE','%'.$request->input('columns')[5]['search']['value'].'%']; }
        $where1 = [];
        $where1[] = ['s_prestamo_cobranza.idtienda', $idtienda];
        $where1[] = ['s_prestamo_cobranza.idcajero',Auth::user()->id];
        if($request->input('columns')[0]['search']['value']!=''){ $where1[] = ['s_prestamo_cobranza.codigo','LIKE','%'.$request->input('columns')[0]['search']['value'].'%']; }
        if($request->input('columns')[1]['search']['value']!=''){ $where1[] = ['s_prestamo_credito.codigo','LIKE','%'.$request->input('columns')[1]['search']['value'].'%']; }
        if($request->input('columns')[2]['search']['value']!=''){ $where1[] = ['s_prestamo_cobranza.fecharegistro','LIKE','%'.$request->input('columns')[2]['search']['value'].'%']; }
        if($request->input('columns')[4]['search']['value']!=''){ $where1[] = ['cliente.apellidos','LIKE','%'.$request->input('columns')[4]['search']['value'].'%']; }
        if($request->input('columns')[5]['search']['value']!=''){ $where1[] = ['asesor.nombre','LIKE','%'.$request->input('columns')[5]['search']['value'].'%']; }
          
          $prestamocobranzas_refinanciacion = DB::table('s_prestamo_cobranza')
              ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_cobranza.idprestamo_credito')
              ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
              ->where('s_prestamo_cobranza.idcajero',0)
              ->where('s_prestamo_cobranza.idtienda',$idtienda)
              ->select(
                's_prestamo_cobranza.id as id',
                's_prestamo_cobranza.fecharegistro as fecharegistro',
                's_prestamo_cobranza.idestadocobranza as idestadocobranza',
                's_prestamo_cobranza.s_idaperturacierre as s_idaperturacierre',
                's_prestamo_cobranza.cronograma_idtipopago as cronograma_idtipopago',
                's_prestamo_cobranza.cronograma_totalredondeado as cronograma_totalredondeado',
                's_prestamo_cobranza.cronograma_pagado as cronograma_pagado',
                's_prestamo_cobranza.cronograma_abono as cronograma_abono',
                's_prestamo_cobranza.codigo as codigo',
                's_prestamo_cobranza.idcajero as idcajero',
                's_prestamo_credito.codigo as creditocodigo',
                'cliente.nombre as cliente_nombre',
                'asesor.nombre as asesor_nombre',
                DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente')
              );
        
          $prestamocobranzas = DB::table('s_prestamo_cobranza')
              ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_cobranza.idprestamo_credito')
              ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
              ->union($prestamocobranzas_refinanciacion)
              ->where($where)
              ->orWhere($where1)
              ->select(
                's_prestamo_cobranza.id as id',
                's_prestamo_cobranza.fecharegistro as fecharegistro',
                's_prestamo_cobranza.idestadocobranza as idestadocobranza',
                's_prestamo_cobranza.s_idaperturacierre as s_idaperturacierre',
                's_prestamo_cobranza.cronograma_idtipopago as cronograma_idtipopago',
                's_prestamo_cobranza.cronograma_totalredondeado as cronograma_totalredondeado',
                's_prestamo_cobranza.cronograma_pagado as cronograma_pagado',
                's_prestamo_cobranza.cronograma_abono as cronograma_abono',
                's_prestamo_cobranza.codigo as codigo',
                's_prestamo_cobranza.idcajero as idcajero',
                's_prestamo_credito.codigo as creditocodigo',
                'cliente.nombre as cliente_nombre',
                'asesor.nombre as asesor_nombre',
                DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente')
              )
              ->orderBy('idcajero','asc')
              ->orderBy('id','desc')
              ->paginate($request->input('length'), ['*'], 'page', (($request->input('start')/$request->input('length'))+1));

            // aperturacaja
            $caja = caja($idtienda,Auth::user()->id);
            $idaperturacierre = 0;
            if($caja['resultado']=='ABIERTO'){
                $idaperturacierre = $caja['apertura']->id;
            }
        
            $tabla = [];
            foreach($prestamocobranzas as $value){
              
                $estado = '';
                if($value->idestadocobranza==1){
                    $estado = '<span class="badge badge-pill badge-info"><i class="fa fa-sync"></i> Pendiente</span>';
                }elseif($value->idestadocobranza==2){
                    $estado = '<span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Correcto</span>';
                }elseif($value->idestadocobranza==3){
                    $estado = '<span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span>';
                }
              
                $opcion = '';
                $classname = '';
                
                if($value->idestadocobranza==1){
                    $opcion = '<li><a href="'.url('backoffice/tienda/sistema/'.$idtienda.'/prestamocobranza/'.$value->id.'/edit?view=confirmar').'"><i class="fa fa-check"></i> Confirmar</a></li>';
                }elseif($value->idestadocobranza==2 or $value->idestadocobranza==3){
                    $opcion = '<li><a href="'.url('backoffice/tienda/sistema/'.$idtienda.'/prestamocobranza/'.$value->id.'/edit?view=ticket').'"><i class="fa fa-receipt"></i> Ticket</a></li>
                          <li><a href="'.url('backoffice/tienda/sistema/'.$idtienda.'/prestamocobranza/'.$value->id.'/edit?view=detalle').'"><i class="fa fa-list"></i> Detalle</a></li>';
                }
              
                $classname = '';
                if($idaperturacierre==$value->s_idaperturacierre){
                    $classname = 'mx-table-warning';
                    //$opcion = $opcion.'<li><a href="'.url('backoffice/tienda/sistema/'.$idtienda.'/prestamocobranza/'.$value->id.'/edit?view=anular').'"><i class="fa fa-ban"></i> Anular</a></li>';
                }
              
                $monto = 0;
                  
                if($value->cronograma_idtipopago==1){
                    $monto = $value->cronograma_totalredondeado;
                }
                elseif($value->cronograma_idtipopago==2){
                    $monto = $value->cronograma_pagado;
                }
              
                $tabla[] = [
                    'idcobranza' => $value->id,
                    'codigo' => str_pad($value->codigo, 8, "0", STR_PAD_LEFT),
                    'codigocredito' => str_pad($value->creditocodigo, 8, "0", STR_PAD_LEFT),
                    'fechapago' => date_format(date_create($value->fecharegistro), "d/m/Y h:i:s A"),
                    'monto' => number_format($monto+$value->cronograma_abono, 2, '.', ''),
                    'cliente' => $value->cliente,
                    'responsable' => $value->asesor_nombre,
                    'estado' => $estado,
                    'idtienda' => $idtienda,
                    'classname' => $classname,
                    'opcion' => $opcion
                ];
            }
          
            return json_encode([
                'draw' => $request->input('draw'),
                'recordsTotal' => $prestamocobranzas->total(),
                'recordsFiltered' => $prestamocobranzas->total(),
                'data' => $tabla
            ]);
      }
    }

    public function edit(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda = DB::table('tienda')->whereId($idtienda)->first();

      if($request->view == 'cobranza') {
          $s_prestamo_credito = DB::table('s_prestamo_credito')
              ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
              ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_credito.idcajero')
              ->leftJoin('ubigeo','ubigeo.id','cliente.idubigeo')
              ->where('s_prestamo_credito.id', $id)
              ->select(
                  's_prestamo_credito.*',
                  'cliente.id as idcliente',
                  'cliente.direccion as cliente_direccion',
                  'ubigeo.id as idubigeo',
                  'ubigeo.nombre as ubigeo',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos,", ",cliente.nombre),
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos)) as cliente'),
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos'
              )
              ->first();
        
          $cronograma = prestamo_cobranza_cronograma($idtienda,$s_prestamo_credito->id,0,0,1,0);
          $agencias = DB::table('s_agencia')->where('s_agencia.idtienda', $idtienda)->get();
          $tipocomprobantes = DB::table('s_tipocomprobante')->get();
          $monedas = DB::table('s_moneda')->get();
          $cuentabancarias = DB::table('s_cuentabancaria')
              ->where('s_cuentabancaria.idtienda', $idtienda)
              ->where('s_cuentabancaria.idestado', 1)
              ->get();
          return view('layouts/backoffice/tienda/sistema/prestamocobranza/cobranza', compact(
            's_prestamo_credito',
            'tienda',
            'cronograma',
            'agencias',
            'tipocomprobantes',
            'monedas',
            'cuentabancarias',
          ));
      }
      elseif ($request->view == 'cuotapendiente') {
          $s_prestamo_credito = DB::table('s_prestamo_credito')
              ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
              ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_credito.idcajero')
              ->leftJoin('ubigeo','ubigeo.id','cliente.idubigeo')
              ->where('s_prestamo_credito.id', $id)
              ->select(
                  's_prestamo_credito.*',
                  'cliente.id as idcliente',
                  'cliente.direccion as cliente_direccion',
                  'ubigeo.id as idubigeo',
                  'ubigeo.nombre as ubigeo',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos,", ",cliente.nombre),
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos)) as cliente'),
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos',
              )
              ->first();
         
          $cronograma = prestamo_cobranza_cronograma($idtienda,$s_prestamo_credito->id,$request->moradescuento,$request->montocompleto,$request->idtipopago,$request->hastacuota,'',$request->descuentointeres);
        
          return view('layouts/backoffice/tienda/sistema/prestamocobranza/cuotapendiente', compact(
            's_prestamo_credito',
            'tienda',
            'cronograma',
            'request',
            //'morapendiente',
          ));
      }
      elseif ($request->view == 'cuotacancelada') {
          $s_prestamo_credito = DB::table('s_prestamo_credito')
              ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
              ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_credito.idcajero')
              ->leftJoin('ubigeo','ubigeo.id','cliente.idubigeo')
              ->where('s_prestamo_credito.id', $id)
              ->select(
                  's_prestamo_credito.*',
                  'cliente.id as idcliente',
                  'cliente.direccion as cliente_direccion',
                  'ubigeo.id as idubigeo',
                  'ubigeo.nombre as ubigeo',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos,", ",cliente.nombre),
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos)) as cliente'),
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos',
              )
              ->first();
          $cronograma = prestamo_cobranza_cronograma($idtienda,$s_prestamo_credito->id,$request->moradescuento,$request->montocompleto,$request->idtipopago,$request->hastacuota);
          return view('layouts/backoffice/tienda/sistema/prestamocobranza/cuotacancelada', compact(
            's_prestamo_credito',
            'tienda',
            'cronograma'
          ));
      }
      elseif ($request->view == 'garantias') {
          $bienes = DB::table('s_prestamo_creditobien')
                ->where([
                    ['s_prestamo_creditobien.idprestamo_credito', $id],
                    ['s_prestamo_creditobien.idtienda', $idtienda],
                    ['s_prestamo_creditobien.idestado', 1]
                ])
                ->orderBy('s_prestamo_creditobien.id','desc')
                ->get();
          return view('layouts/backoffice/tienda/sistema/prestamocobranza/garantias', compact(
            'tienda',
            'bienes',
          ));
      }
      elseif ($request->view == 'garantiasdevolver') {
          $prestamobien = DB::table('s_prestamo_creditobien')
                ->where('s_prestamo_creditobien.idtienda', $idtienda)
                ->where('s_prestamo_creditobien.id', $request->idbien)
                ->first();
          return view('layouts/backoffice/tienda/sistema/prestamocobranza/garantiasdevolver', compact(
            'tienda',
            'prestamobien',
          ));
      }
      elseif ($request->view == 'garantiasrematar') {
          $prestamobien = DB::table('s_prestamo_creditobien')
                ->where('s_prestamo_creditobien.idtienda', $idtienda)
                ->where('s_prestamo_creditobien.id', $request->idbien)
                ->first();
          $categorias = DB::table('s_categoria')
                ->where('idestado',1)
                ->where('s_categoria.idtienda',$idtienda)
                ->where('s_categoria.s_idcategoria',0)
                ->orderBy('s_categoria.nombre','asc')
                ->get();
          return view('layouts/backoffice/tienda/sistema/prestamocobranza/garantiasrematar', compact(
            'tienda',
            'prestamobien',
            'categorias',
          ));
      }
      elseif ($request->view == 'pagorealizado') {
          $s_prestamo_credito = DB::table('s_prestamo_credito')
              ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
              ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_credito.idcajero')
              ->leftJoin('ubigeo','ubigeo.id','cliente.idubigeo')
              ->where('s_prestamo_credito.id', $id)
              ->select(
                  's_prestamo_credito.*',
                  'cliente.id as idcliente',
                  'cliente.direccion as cliente_direccion',
                  'ubigeo.id as idubigeo',
                  'ubigeo.nombre as ubigeo',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos,", ",cliente.nombre),
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos)) as cliente'),
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos',
              )
              ->first();

          $prestamocobranzas = DB::table('s_prestamo_cobranza')
              ->join('users as cliente', 'cliente.id', 's_prestamo_cobranza.idcliente')
              ->join('users as cajero', 'cajero.id', 's_prestamo_cobranza.idcajero')
              ->where([
                ['s_prestamo_cobranza.idprestamo_credito', $id],
                ['s_prestamo_cobranza.idtienda', $idtienda],
              ])
              ->orWhere([
                ['s_prestamo_cobranza.idprestamo_credito', $id],
                ['s_prestamo_cobranza.idtienda', $idtienda],
              ])
              ->select(
                's_prestamo_cobranza.*',
                'cajero.nombre as cajero_nombre'
              )
              ->orderBy('s_prestamo_cobranza.id','desc')
              ->get();

            // aperturacaja
            $caja = caja($idtienda,Auth::user()->id);
            $idaperturacierre = 0;
            if($caja['resultado']=='ABIERTO'){
                $idaperturacierre = $caja['apertura']->id;
            }
          return view('layouts/backoffice/tienda/sistema/prestamocobranza/pagorealizado', compact(
            's_prestamo_credito',
            'tienda',
            'prestamocobranzas',
            'idaperturacierre'
          ));
      }
      elseif ($request->view == 'pagorealizadodetalle') {
          $s_prestamo_credito = DB::table('s_prestamo_credito')
              ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
              ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_credito.idcajero')
              ->leftJoin('ubigeo','ubigeo.id','cliente.idubigeo')
              ->where('s_prestamo_credito.id', $id)
              ->select(
                  's_prestamo_credito.*',
                  'cliente.id as idcliente',
                  'cliente.direccion as cliente_direccion',
                  'ubigeo.id as idubigeo',
                  'ubigeo.nombre as ubigeo',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos,", ",cliente.nombre),
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos)) as cliente'),
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos'
              )
              ->first();
          $cobranza = DB::table('s_prestamo_cobranza')
              ->join('users as cliente', 'cliente.id', 's_prestamo_cobranza.idcliente')
              ->leftJoin('users as asesor', 'asesor.id', 's_prestamo_cobranza.idasesor')
              ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_cobranza.idcajero')
              ->leftJoin('ubigeo','ubigeo.id','s_prestamo_cobranza.cliente_idubigeo')
              ->leftJoin('s_agencia', 's_agencia.id', 's_prestamo_cobranza.idagencia')
              ->where('s_prestamo_cobranza.id', $request->idcobranza)
              ->select(
                  's_prestamo_cobranza.*',
                  'ubigeo.nombre as ubigeo',
                  'cliente.identificacion as cliente_identificacion',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente'),
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos',
                  's_agencia.id as idagencia',
                  's_agencia.nombrecomercial as agencia_nombre'
              )
              ->first();
          $cobranzadetalle = DB::table('s_prestamo_cobranzadetalle')
              ->where('s_prestamo_cobranzadetalle.idprestamo_cobranza', $request->idcobranza)
              ->get();
          $agencia = DB::table('s_agencia')
              ->leftJoin('ubigeo','ubigeo.id','s_agencia.idubigeo')
              ->where('s_agencia.id', $cobranza->idagencia)
              ->select(
                's_agencia.*',
                'ubigeo.nombre as ubigeonombre'
              )
              ->first();
          return view('layouts/backoffice/tienda/sistema/prestamocobranza/pagorealizadodetalle', compact(
              's_prestamo_credito',
              'cobranza',
              'cobranzadetalle',
              'agencia',
              'tienda'
          ));
      }
      elseif ($request->view == 'pagorealizadoticket') {
          $s_prestamo_credito = DB::table('s_prestamo_credito')
              ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
              ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_credito.idcajero')
              ->leftJoin('ubigeo','ubigeo.id','cliente.idubigeo')
              ->where('s_prestamo_credito.id', $id)
              ->select(
                  's_prestamo_credito.*',
                  'cliente.id as idcliente',
                  'cliente.direccion as cliente_direccion',
                  'ubigeo.id as idubigeo',
                  'ubigeo.nombre as ubigeo',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos,", ",cliente.nombre),
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos)) as cliente'),
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos'
              )
              ->first();
          $cobranza = DB::table('s_prestamo_cobranza')
              ->join('users as cliente', 'cliente.id', 's_prestamo_cobranza.idcliente')
              ->leftJoin('users as asesor', 'asesor.id', 's_prestamo_cobranza.idasesor')
              ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_cobranza.idcajero')
              ->leftJoin('ubigeo','ubigeo.id','s_prestamo_cobranza.cliente_idubigeo')
              ->leftJoin('s_agencia', 's_agencia.id', 's_prestamo_cobranza.idagencia')
              ->where('s_prestamo_cobranza.id', $request->idcobranza)
              ->select(
                  's_prestamo_cobranza.*',
                  'ubigeo.nombre as ubigeo',
                  'cliente.identificacion as cliente_identificacion',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente'),
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos',
                  's_agencia.id as idagencia',
                  's_agencia.nombrecomercial as agencia_nombre'
              )
              ->first();
        
          return view('layouts/backoffice/tienda/sistema/prestamocobranza/pagorealizadoticket', compact(
              's_prestamo_credito',
              'cobranza',
              'tienda'
          ));
      }
      elseif ($request->view == 'pagorealizadoticketpdf') { 
          $s_prestamo_credito = DB::table('s_prestamo_credito')
              ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
              ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_credito.idcajero')
              ->leftJoin('ubigeo','ubigeo.id','cliente.idubigeo')
              ->where('s_prestamo_credito.id', $id)
              ->select(
                  's_prestamo_credito.*',
                  'cliente.id as idcliente',
                  'cliente.direccion as cliente_direccion',
                  'ubigeo.id as idubigeo',
                  'ubigeo.nombre as ubigeo',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos,", ",cliente.nombre),
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos)) as cliente'),
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos'
              )
              ->first();
          $cobranza = DB::table('s_prestamo_cobranza')
              ->join('users as cliente', 'cliente.id', 's_prestamo_cobranza.idcliente')
              ->leftJoin('users as asesor', 'asesor.id', 's_prestamo_cobranza.idasesor')
              ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_cobranza.idcajero')
              ->leftJoin('ubigeo','ubigeo.id','s_prestamo_cobranza.cliente_idubigeo')
              ->leftJoin('s_agencia', 's_agencia.id', 's_prestamo_cobranza.idagencia')
              ->where('s_prestamo_cobranza.id', $request->idcobranza)
              ->select(
                  's_prestamo_cobranza.*',
                  'ubigeo.nombre as ubigeo',
                  'cliente.identificacion as cliente_identificacion',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente'),
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos',
                  's_agencia.id as idagencia',
                  's_agencia.nombrecomercial as agencia_nombre'
              )
              ->first();
          $cobranzadetalle = DB::table('s_prestamo_cobranzadetalle')
              ->where('s_prestamo_cobranzadetalle.idprestamo_cobranza', $request->idcobranza)
              ->get();
          $agencia = DB::table('s_agencia')
              ->leftJoin('ubigeo','ubigeo.id','s_agencia.idubigeo')
              ->where('s_agencia.id', $cobranza->idagencia)
              ->select(
                's_agencia.*',
                'ubigeo.nombre as ubigeonombre'
              )
              ->first();
          $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamocobranza/pagorealizadoticketpdf', compact(
              's_prestamo_credito',
              'cobranza',
              'cobranzadetalle',
              'agencia',
              'tienda',
          ));
          return $pdf->stream('Ticket Cobranza.pdf');
      }
      elseif ($request->view == 'pagorealizadoanular') {
          $s_prestamo_credito = DB::table('s_prestamo_credito')
              ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
              ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_credito.idcajero')
              ->leftJoin('ubigeo','ubigeo.id','cliente.idubigeo')
              ->where('s_prestamo_credito.id', $id)
              ->select(
                  's_prestamo_credito.*',
                  'cliente.id as idcliente',
                  'cliente.direccion as cliente_direccion',
                  'ubigeo.id as idubigeo',
                  'ubigeo.nombre as ubigeo',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos,", ",cliente.nombre),
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos)) as cliente'),
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos'
              )
              ->first();
          $cobranza = DB::table('s_prestamo_cobranza')
              ->join('users as cliente', 'cliente.id', 's_prestamo_cobranza.idcliente')
              ->leftJoin('users as asesor', 'asesor.id', 's_prestamo_cobranza.idasesor')
              ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_cobranza.idcajero')
              ->leftJoin('ubigeo','ubigeo.id','s_prestamo_cobranza.cliente_idubigeo')
              ->leftJoin('s_agencia', 's_agencia.id', 's_prestamo_cobranza.idagencia')
              ->where('s_prestamo_cobranza.id', $request->idcobranza)
              ->select(
                  's_prestamo_cobranza.*',
                  'ubigeo.nombre as ubigeo',
                  'cliente.identificacion as cliente_identificacion',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente'),
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos',
                  's_agencia.id as idagencia',
                  's_agencia.nombrecomercial as agencia_nombre'
              )
              ->first();
          $cobranzadetalle = DB::table('s_prestamo_cobranzadetalle')
              ->where('s_prestamo_cobranzadetalle.idprestamo_cobranza', $request->idcobranza)
              ->get();
          $agencia = DB::table('s_agencia')
              ->leftJoin('ubigeo','ubigeo.id','s_agencia.idubigeo')
              ->where('s_agencia.id', $cobranza->idagencia)
              ->select(
                's_agencia.*',
                'ubigeo.nombre as ubigeonombre'
              )
              ->first();
          return view('layouts/backoffice/tienda/sistema/prestamocobranza/pagorealizadoanular', compact(
              's_prestamo_credito',
              'cobranza',
              'cobranzadetalle',
              'agencia',
              'tienda'
          ));
      }
      elseif ($request->view == 'ticket') {
        $cobranza = DB::table('s_prestamo_cobranza')
              ->where('s_prestamo_cobranza.id', $id)
              ->select(
                  's_prestamo_cobranza.*'
              )
              ->first();
        return view('layouts/backoffice/tienda/sistema/prestamocobranza/ticket', compact(
          'cobranza',
          'tienda'
        ));
      }
      elseif ($request->view == 'ticketpdf') {
        
          
          $cobranza = DB::table('s_prestamo_cobranza')
              ->join('users as cliente', 'cliente.id', 's_prestamo_cobranza.idcliente')
              ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_cobranza.idprestamo_credito')
              ->join('s_moneda', 's_moneda.id', 's_prestamo_cobranza.idmoneda')
              ->leftJoin('users as asesor', 'asesor.id', 's_prestamo_cobranza.idasesor')
              ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_cobranza.idcajero')
              ->leftJoin('ubigeo','ubigeo.id','s_prestamo_cobranza.cliente_idubigeo')
              ->leftJoin('s_agencia', 's_agencia.id', 's_prestamo_cobranza.idagencia')
              ->where('s_prestamo_cobranza.id', $request->idcobranza)
              ->select(
                  's_prestamo_cobranza.*',
                  'ubigeo.nombre as ubigeo',
                  'cliente.identificacion as cliente_identificacion',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente'),
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos',
                  's_agencia.id as idagencia',
                  's_agencia.nombrecomercial as agencia_nombre',
                  's_moneda.simbolo as monedasimbolo',
                  's_moneda.nombre as monedanombre',
                  's_prestamo_credito.codigo as creditocodigo',
                  's_prestamo_credito.numerocuota as numerocuota'
              )
              ->first();
      
          $cobranzadetalle = DB::table('s_prestamo_cobranzadetalle')
              ->join('s_prestamo_creditodetalle', 's_prestamo_creditodetalle.id', 's_prestamo_cobranzadetalle.idprestamo_creditodetalle')
              ->where('s_prestamo_cobranzadetalle.idprestamo_cobranza', $request->idcobranza)
              ->select('s_prestamo_creditodetalle.*')
              ->orderBy('s_prestamo_cobranzadetalle.id','asc')
              ->get();
          $agencia = DB::table('s_agencia')
              ->leftJoin('ubigeo','ubigeo.id','s_agencia.idubigeo')
              ->where('s_agencia.id', $cobranza->idagencia)
              ->select(
                's_agencia.*',
                'ubigeo.nombre as ubigeonombre'
              )
              ->first();
          $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamocobranza/ticketpdf', compact(
              'cobranza',
              'cobranzadetalle',
              'agencia',
              'tienda',
          ));
          return $pdf->stream('Ticket Cobranza.pdf');
      }
      elseif ($request->view == 'detalle') {
          $cobranza = DB::table('s_prestamo_cobranza')
              ->join('users as cliente', 'cliente.id', 's_prestamo_cobranza.idcliente')
              ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_cobranza.idprestamo_credito')
              ->join('s_moneda', 's_moneda.id', 's_prestamo_cobranza.idmoneda')
              ->leftJoin('users as asesor', 'asesor.id', 's_prestamo_cobranza.idasesor')
              ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_cobranza.idcajero')
              ->leftJoin('ubigeo','ubigeo.id','s_prestamo_cobranza.cliente_idubigeo')
              ->leftJoin('s_agencia', 's_agencia.id', 's_prestamo_cobranza.idagencia')
              ->where('s_prestamo_cobranza.id', $id)
              ->select(
                  's_prestamo_cobranza.*',
                  'ubigeo.nombre as ubigeo',
                  'cliente.identificacion as cliente_identificacion',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente'),
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos',
                  's_agencia.id as idagencia',
                  's_agencia.nombrecomercial as agencia_nombre',
                  's_moneda.simbolo as monedasimbolo',
                  's_moneda.nombre as monedanombre',
                  's_prestamo_credito.codigo as creditocodigo'
              )
              ->first();
          $cobranzadetalle = DB::table('s_prestamo_cobranzadetalle')
              ->join('s_prestamo_creditodetalle', 's_prestamo_creditodetalle.id', 's_prestamo_cobranzadetalle.idprestamo_creditodetalle')
              ->where('s_prestamo_cobranzadetalle.idprestamo_cobranza', $id)
              ->select('s_prestamo_creditodetalle.*')
              ->get();
          $agencia = DB::table('s_agencia')
              ->leftJoin('ubigeo','ubigeo.id','s_agencia.idubigeo')
              ->where('s_agencia.id', $cobranza->idagencia)
              ->select(
                's_agencia.*',
                'ubigeo.nombre as ubigeonombre'
              )
              ->first();
          return view('layouts/backoffice/tienda/sistema/prestamocobranza/detalle', compact(
              'cobranza',
              'cobranzadetalle',
              'agencia',
              'tienda'
          ));
      }
      elseif ($request->view == 'confirmar') {
        
          $cobranza = DB::table('s_prestamo_cobranza')
              ->join('users as cliente', 'cliente.id', 's_prestamo_cobranza.idcliente')
              ->leftJoin('users as asesor', 'asesor.id', 's_prestamo_cobranza.idasesor')
              ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_cobranza.idcajero')
              ->leftJoin('ubigeo','ubigeo.id','s_prestamo_cobranza.cliente_idubigeo')
              ->leftJoin('s_agencia', 's_agencia.id', 's_prestamo_cobranza.idagencia')
              ->where('s_prestamo_cobranza.id', $id)
              ->select(
                  's_prestamo_cobranza.*',
                  'ubigeo.nombre as ubigeo',
                  'cliente.identificacion as cliente_identificacion',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente'),
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos',
                  's_agencia.id as idagencia',
                  's_agencia.nombrecomercial as agencia_nombre'
              )
              ->first();
          $s_prestamo_credito = DB::table('s_prestamo_credito')
              ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
              ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_credito.idcajero')
              ->leftJoin('ubigeo','ubigeo.id','cliente.idubigeo')
              ->where('s_prestamo_credito.id', $cobranza->idprestamo_credito)
              ->select(
                  's_prestamo_credito.*',
                  'cliente.id as idcliente',
                  'cliente.direccion as cliente_direccion',
                  'ubigeo.id as idubigeo',
                  'ubigeo.nombre as ubigeo',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos,", ",cliente.nombre),
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos)) as cliente'),
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos'
              )
              ->first();
        
          $creditosolicituddetalle = DB::table('s_prestamo_creditodetalle')
                ->join('s_prestamo_cobranzadetalle', 's_prestamo_cobranzadetalle.idprestamo_creditodetalle', 's_prestamo_creditodetalle.id')
                //->where('s_prestamo_creditodetalle.idprestamo_credito', $cobranza->idprestamo_credito)
                ->where('s_prestamo_cobranzadetalle.idprestamo_cobranza', $id)
                ->orderBy('s_prestamo_creditodetalle.numero', 'asc')
                ->get();

          //$cronograma = prestamo_cobranza_cronograma($idtienda,$cobranza->idprestamo_credito,$cobranza->cronograma_moradescuento+$prestamo_morapagadas,0,1,$cobranza->cronograma_hastacuota);
          $agencias = DB::table('s_agencia')->where('s_agencia.idtienda', $idtienda)->get();
          $tipocomprobantes = DB::table('s_tipocomprobante')->get();
          $monedas = DB::table('s_moneda')->get();
        
          return view('layouts/backoffice/tienda/sistema/prestamocobranza/confirmar', compact(
            's_prestamo_credito',
            'cobranza',
            'tienda',
            'creditosolicituddetalle',
            'agencias',
            'tipocomprobantes',
            'monedas',
          ));
      }
      elseif ($request->view == 'anular') {
          $cobranza = DB::table('s_prestamo_cobranza')
              ->join('users as cliente', 'cliente.id', 's_prestamo_cobranza.idcliente')
              ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_cobranza.idprestamo_credito')
              ->join('s_moneda', 's_moneda.id', 's_prestamo_cobranza.idmoneda')
              ->leftJoin('users as asesor', 'asesor.id', 's_prestamo_cobranza.idasesor')
              ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_cobranza.idcajero')
              ->leftJoin('ubigeo','ubigeo.id','s_prestamo_cobranza.cliente_idubigeo')
              ->leftJoin('s_agencia', 's_agencia.id', 's_prestamo_cobranza.idagencia')
              ->where('s_prestamo_cobranza.id', $id)
              ->select(
                  's_prestamo_cobranza.*',
                  'ubigeo.nombre as ubigeo',
                  'cliente.identificacion as cliente_identificacion',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente'),
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  'cajero.nombre as cajero_nombre',
                  'cajero.apellidos as cajero_apellidos',
                  's_agencia.id as idagencia',
                  's_agencia.nombrecomercial as agencia_nombre',
                  's_moneda.simbolo as monedasimbolo',
                  's_moneda.nombre as monedanombre',
                  's_prestamo_credito.codigo as creditocodigo'
              )
              ->first();
          $cobranzadetalle = DB::table('s_prestamo_cobranzadetalle')
              ->join('s_prestamo_creditodetalle', 's_prestamo_creditodetalle.id', 's_prestamo_cobranzadetalle.idprestamo_creditodetalle')
              ->where('s_prestamo_cobranzadetalle.idprestamo_cobranza', $id)
              ->select('s_prestamo_creditodetalle.*')
              ->get();
          $agencia = DB::table('s_agencia')
              ->leftJoin('ubigeo','ubigeo.id','s_agencia.idubigeo')
              ->where('s_agencia.id', $cobranza->idagencia)
              ->select(
                's_agencia.*',
                'ubigeo.nombre as ubigeonombre'
              )
              ->first();
          return view('layouts/backoffice/tienda/sistema/prestamocobranza/anular', compact(
              'cobranza',
              'cobranzadetalle',
              'agencia',
              'tienda'
          ));
      }
      elseif ($request->view == 'documento') {
          return view('layouts/backoffice/tienda/sistema/prestamocobranza/documento',[
              'tienda' => $tienda,
              'idprestamocredito' => $id,
          ]);  
      } 
    }

    public function update(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(), $idtienda);
        if ($request->input('view') == 'anular_pagorealizado') {
            // aperturacaja
            /*$caja = caja($idtienda,Auth::user()->id);
            $idaperturacierre = 0;
            if($caja['resultado']!='ABIERTO'){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La Caja debe estar Aperturada.'
                ]);
            }
          
            $idaperturacierre = $caja['apertura']->id;
          
            $s_prestamo_cobranza = DB::table('s_prestamo_cobranza')->whereId($id)->first();
          
            if($idaperturacierre!=$s_prestamo_cobranza->s_idaperturacierre){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La Apertura no coincide con la apertura actual.'
                ]);
            }*/
          
            //dd('---');
          
           $s_prestamo_cobranza = DB::table('s_prestamo_cobranza')
                ->where('s_prestamo_cobranza.idtienda',$idtienda)
                ->where('s_prestamo_cobranza.id',$id)
                ->first();
          
            if($s_prestamo_cobranza->cronograma_idtipopago==1){
                $s_prestamo_cobranzadetalle = DB::table('s_prestamo_cobranzadetalle')
                    ->where('s_prestamo_cobranzadetalle.idtienda',$idtienda)
                    ->where('s_prestamo_cobranzadetalle.idprestamo_cobranza',$id)
                    ->get();
                foreach($s_prestamo_cobranzadetalle as $value){
                    DB::table('s_prestamo_creditodetalle')
                        ->where('s_prestamo_creditodetalle.idtienda',$idtienda)
                        ->where('s_prestamo_creditodetalle.id',$value->idprestamo_creditodetalle)
                        ->update([
                        'atraso' => 0,
                        'mora' => 0,
                        'moradescuento' => 0,
                        'moraapagar' => 0,
                        'cuotapago' => 0,
                        //'acuenta' => 0,
                        'cuotaapagar' => 0,
                        'abono' => 0,
                        'cuotaapagartotal' => 0,
                        'montorefinanciado' => 0,
                        'interesdescontado' => 0,
                        'idestadocobranza' => 1
                    ]);
                }

                /*DB::table('s_prestamo_cobranzadetalle')
                    ->where('s_prestamo_cobranzadetalle.idtienda',$idtienda)
                    ->where('s_prestamo_cobranzadetalle.idprestamo_cobranza',$id)
                    ->delete();*/
                DB::table('s_prestamo_cobranza')
                    ->where('s_prestamo_cobranza.idtienda',$idtienda)
                    ->where('s_prestamo_cobranza.id',$id)
                    ->update([
                        'idestadocobranza' => 3,
                    ]);
            }
            elseif($s_prestamo_cobranza->cronograma_idtipopago==2){
                $s_prestamo_creditodetalle = DB::table('s_prestamo_creditodetalle')
                    ->where('s_prestamo_creditodetalle.idtienda',$idtienda)
                    ->where('s_prestamo_creditodetalle.idprestamo_credito',$s_prestamo_cobranza->idprestamo_credito)
                    ->where('s_prestamo_creditodetalle.idestadocobranza',1)
                    ->orderBy('s_prestamo_creditodetalle.numero','asc')
                    ->first();
              
                $cuenta_actualizado = 0;
                if($s_prestamo_creditodetalle!=''){
                    if($s_prestamo_creditodetalle->acuenta>0){
                        if($s_prestamo_cobranza->cronograma_montorecibido<$s_prestamo_creditodetalle->acuenta){
                            $cuenta_actualizado = $s_prestamo_creditodetalle->acuenta-$s_prestamo_cobranza->cronograma_montorecibido;
                        }else{
                            $cuenta_actualizado = 0;
                        }
                    }
                //dd($cuenta_actualizado);
                    DB::table('s_prestamo_creditodetalle')
                        ->where('s_prestamo_creditodetalle.idtienda',$idtienda)
                        ->where('s_prestamo_creditodetalle.id',$s_prestamo_creditodetalle->id)
                        ->update([
                        'acuenta' => $cuenta_actualizado,
                    ]);
                }
              
                //dd('--');
              
                $s_prestamo_cobranzadetalle = DB::table('s_prestamo_cobranzadetalle')
                    ->where('s_prestamo_cobranzadetalle.idtienda',$idtienda)
                    ->where('s_prestamo_cobranzadetalle.idprestamo_cobranza',$id)
                    ->get();
                foreach($s_prestamo_cobranzadetalle as $value){
                    DB::table('s_prestamo_creditodetalle')
                        ->where('s_prestamo_creditodetalle.idtienda',$idtienda)
                        ->where('s_prestamo_creditodetalle.id',$value->idprestamo_creditodetalle)
                        ->update([
                        'atraso' => 0,
                        'mora' => 0,
                        'moradescuento' => 0,
                        'moraapagar' => 0,
                        'cuotapago' => 0,
                        'cuotaapagar' => 0,
                        'abono' => 0,
                        'cuotaapagartotal' => 0,
                        'montorefinanciado' => 0,
                        'interesdescontado' => 0,
                        'idestadocobranza' => 1
                    ]);
                }
                /*DB::table('s_prestamo_cobranzadetalle')
                    ->where('s_prestamo_cobranzadetalle.idtienda',$idtienda)
                    ->where('s_prestamo_cobranzadetalle.idprestamo_cobranza',$id)
                    ->delete();*/
                DB::table('s_prestamo_cobranza')
                    ->where('s_prestamo_cobranza.idtienda',$idtienda)
                    ->where('s_prestamo_cobranza.id',$id)
                    ->update([
                        'idestadocobranza' => 3,
                    ]);
            }
          
            // poner en pendiente
            DB::table('s_prestamo_credito')->whereId($s_prestamo_cobranza->idprestamo_credito)->update([
                'idestadocobranza' => 1,
            ]);    
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha anulado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'entregar_garantias') {

            DB::table('s_prestamo_creditobien')->whereId($id)->update([
                'fechaentrega' => Carbon::now(),
                'idestadoentrega' => 2, // 2 = entregado
            ]);    
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha entregado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'rematar_garantias') {
            $rules = [
                'nombre' => 'required', 
                'precioalpublico'    => 'required', 
                'descripcion'    => 'required', 
                'idcategoria' => 'required',
            ];
          
            $messages = [
                'nombre.required'   => 'El "Nombre" es Obligatorio.',
                'precioalpublico.required'   => 'El "Precio al Público" es Obligatorio.',
                'descripcion.required'   => 'La "Descripción" es Obligatorio.',
                'idcategoria.required'   => 'La "Categoría" es Obligatorio.',
            ];
      
            $this->validate($request,$rules,$messages);
          
                $idproducto = DB::table('s_producto')->insertGetId([
                    'fecharegistro'   => Carbon::now(),
                    'orden'           => 0,
                    'codigo'          => '',
                    'nombre'          => $request->input('nombre'),
                    'descripcion'     => $request->input('descripcion'),
                    'preciopormayor'  => '0.00',
                    'precioalpublico' => $request->input('precioalpublico'),
                    'por'             => 1,
                    'stockminimo'     => 0,
                    'alertavencimiento'=> 0,
                    's_idproducto'    => 0,
                    's_idcategoria1'  => $request->input('idcategoria'),
                    's_idcategoria2'  => 0,
                    's_idcategoria3'  => 0,
                    's_idmarca'       => 0,
                    's_idestadodetalle' => 2,
                    's_idestado'      => 1,
                    's_idestadotiendavirtual' => 1,
                    's_idestadosistema'     => 1,
                    'idunidadmedida'  => 1,
                    'idproductopresentacion'  => 0,
                    'idtienda'     => $idtienda
                ]);
          
          
                  $prestamolaboralimagen = DB::table('s_prestamo_creditobienimagen')
                    ->where('s_prestamo_creditobienimagen.idprestamo_creditobien', $id)
                    ->orderBy('s_prestamo_creditobienimagen.id','asc')
                    ->get();
                  foreach($prestamolaboralimagen as $valueimagen) {
                      $imagen = duplicar_fichero('/public/backoffice/tienda/'.$idtienda.'/creditobien/',$valueimagen->imagen,'/public/backoffice/tienda/'.$idtienda.'/producto/');
                    
                      $countproductogaleria = DB::table('s_productogaleria')->where('s_idproducto',$idproducto)->count();
                      if($countproductogaleria==0){
                          $make = getcwd().'/public/backoffice/tienda/'.$idtienda.'/producto/'.$imagen;
                          resize_img($make,null,40,getcwd().'/public/backoffice/tienda/'.$idtienda.'/producto/40/',$imagen);
                          resize_img($make,null,250,getcwd().'/public/backoffice/tienda/'.$idtienda.'/producto/250/',$imagen);
                      }
                    
                      DB::table('s_productogaleria')->insert([
                          'fecharegistro' => Carbon::now(),
                          'orden' => $countproductogaleria+1,
                          'imagen' => $imagen,
                          's_idproducto' => $idproducto,
                          'idtienda' => $idtienda,
                          's_idestado' => 1
                      ]);
                  }
          
            DB::table('s_prestamo_creditobien')->whereId($id)->update([
                'fechaentrega' => Carbon::now(),
                'idestadoentrega' => 3, // 2 = rematado
                'idproductoentrega' => $idproducto,
            ]);    
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha entregado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'confirmar') {
            $rules = [
              'facturacion_idcliente' => 'required',
              'facturacion_direccion' => 'required',
              'facturacion_idubigeo' => 'required',
              'facturacion_idagencia' => 'required',
              'facturacion_idmoneda' => 'required',
              'facturacion_idtipocomprobante' => 'required',
            ];
            $messages = [
              'facturacion_idcliente.required' => 'El "Cliente" es Obligatorio.',
              'facturacion_direccion.required' => 'La "Dirección" es Obligatorio.',
              'facturacion_idubigeo.required' => 'El "Ubigeo" es Obligatorio.',
              'facturacion_idagencia.required' => 'La "Agencia" es Obligatorio.',
              'facturacion_idmoneda.required' => 'La "Moneda" es Obligatorio.',
              'facturacion_idtipocomprobante.required' => 'El "Tipo de comprobante" es Obligatorio.'
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

            DB::table('s_prestamo_cobranza')->whereId($id)->update([
                'fechaconfirmado' => Carbon::now(),
                'cliente_direccion' => $request->facturacion_direccion,
                'cliente_idubigeo' => $request->facturacion_idubigeo,
                's_idaperturacierre' => $idaperturacierre,
                'idtipocomprobante' => $request->facturacion_idtipocomprobante,
                'idmoneda' => $request->facturacion_idmoneda,
                'idagencia' => $request->facturacion_idagencia,
                'idcliente' => $request->facturacion_idcliente,
                'idcajero' => Auth::user()->id,
                'idestadocobranza' => 2
            ]);

            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha actualizado correctamente.',
              'idprestamocobranza'   => $id
            ]);
        }
    }

    public function destroy(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if($request->input('view') == 'eliminar') {
            DB::table('s_prestamo_cobranza')->where('idtienda',$idtienda)->where('id',$id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
