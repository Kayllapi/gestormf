<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;

class PrestamoAmpliacionController extends Controller
{
    public function index(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        $where = [];
        if($request->tipocredito!=''){ $where[] = ['s_prestamo_tipocredito.id',$request->tipocredito]; }
        $where[] = ['s_prestamo_credito.codigo','LIKE','%'.$request->codigocredito.'%'];
        $where[] = ['cliente.identificacion','LIKE','%'.$request->identificacion.'%'];
        $where[] = ['cliente.nombre','LIKE','%'.$request->cliente.'%'];
        if($request->frecuencia!=''){ $where[] = ['s_prestamo_credito.idprestamo_frecuencia',$request->frecuencia]; }
      
        $where1 = [];
        if($request->tipocredito!=''){ $where1[] = ['s_prestamo_tipocredito.id',$request->tipocredito]; }
        $where1[] = ['s_prestamo_credito.codigo','LIKE','%'.$request->codigocredito.'%'];
        $where1[] = ['cliente.identificacion','LIKE','%'.$request->identificacion.'%'];
        $where1[] = ['cliente.nombre','LIKE','%'.$request->cliente.'%'];
        if($request->frecuencia!=''){ $where1[] = ['s_prestamo_credito.idprestamo_frecuencia',$request->frecuencia]; }
      
        $prestamocreditos = DB::table('s_prestamo_credito')
              ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
              ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->join('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_credito.idprestamo_frecuencia')
              ->join('s_prestamo_tipocredito', 's_prestamo_tipocredito.id', 's_prestamo_credito.idprestamo_tipocredito')
              ->join('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
              ->where($where)
              ->where('s_prestamo_credito.idtienda', $idtienda)
              ->where('s_prestamo_credito.idasesor', Auth::user()->id)
              ->where('s_prestamo_credito.idprestamo_tipocredito', 4)
              ->orWhere($where1)
              ->where('s_prestamo_credito.idtienda', $idtienda)
              ->where('s_prestamo_credito.idasesor', Auth::user()->id)
              ->where('s_prestamo_credito.idprestamo_tipocredito', 4)
              ->select(
                  's_prestamo_credito.*',
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  's_prestamo_frecuencia.nombre as frecuencianombre',
                  's_prestamo_tipocredito.nombre as tipocreditonombre',
                  'cliente.identificacion as clienteidentificacion',
                  's_moneda.simbolo as monedasimbolo',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente'),
              )
              ->orderBy('s_prestamo_credito.id','desc')
              ->paginate(10);

      return view('layouts/backoffice/tienda/sistema/prestamoampliacion/index', compact(
        'tienda',
        'prestamocreditos'
      ));
    }

    public function create(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      return view('layouts/backoffice/tienda/sistema/prestamoampliacion/create', compact(
        'tienda',
      ));
    }

    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if ($request->input('view') == 'registrar-ampliacion') { 
      
            $rules = [
                'idprestamo_credito' => 'required',
                'monto' => 'required',
                'numerocuota' => 'required',
                'fechainicio' => 'required',
                'idfrecuencia' => 'required',
                'tasa' => 'required',
            ];
            $messages = [
                'idprestamo_credito.required' => 'El "Cliente" es Obligatorio.',
                'monto.required' => 'El "Monto" es Obligatorio.',
                'numerocuota.required' => 'El "Nro. de Cuota" es Obligatorio.',
                'fechainicio.required' => 'La "Fecha de Inicio" es Obligatorio.',
                'idfrecuencia.required' => 'La "Frecuencia" es Obligatorio.',
                'tasa.required' => 'El "Interes" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);
            
            $prestamo_credito = DB::table('s_prestamo_credito')
                ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
                ->where('s_prestamo_credito.id', $request->idprestamo_credito)
                ->select(
                    's_prestamo_credito.*',
                    'cliente.direccion as cliente_direccion',
                    'cliente.idubigeo as cliente_idubigeo',
                )
                ->first();
          
            

          
            $cronograma = prestamo_cobranza_cronograma($idtienda, $request->idprestamo_credito, 0, 0, 1, $prestamo_credito->numerocuota);
  
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

            $idprestamocobranza = DB::table('s_prestamo_cobranza')->insertGetId([
                'fecharegistro' => Carbon::now(),
                'codigo' => $codigo,
                'cuota' => $cronograma['select_cuota'],
                'proximo_vencimiento' => '',
                'interesdescontado' => 0,
                'cronograma_idtipopago' => 1,
                'cronograma_hastacuota' => $prestamo_credito->numerocuota,
                'cronograma_totalcuota' => $cronograma['select_cuota'],
                'cronograma_acuentaanterior' => $cronograma['select_acuentaanterior'],
                'cronograma_acuentaproxima' => $cronograma['select_acuentaproxima'],
                'cronograma_moratotal' => $cronograma['select_mora'],
                'cronograma_moradescuento' => $cronograma['select_moradescontado'],
                'cronograma_morapagar' => $cronograma['select_moraapagar'],
                'cronograma_total' => $cronograma['select_cuotaapagar']-$cronograma['total_cancelada_abono'],
                'cronograma_totalredondeado' => $cronograma['select_cuotaapagarredondeado']-$cronograma['total_cancelada_abono'],
                'cronograma_abono' => 0,
                'cronograma_montorecibido' => $cronograma['select_cuotaapagarredondeado']-$cronograma['total_cancelada_abono'],
                'cronograma_pagado' => $cronograma['select_cuotaapagarredondeado']-$cronograma['total_cancelada_abono'],
                'cronograma_vuelto' => 0,
                'cronograma_deposito' => 0,
                'cronograma_ultimonumerocuota' => $cronograma['select_ultimonumerocuota'],
                'cliente_direccion' => $prestamo_credito->cliente_direccion,
                'cliente_idubigeo' => $prestamo_credito->cliente_idubigeo,
                's_idaperturacierre' => 0,
                'idprestamo_credito' => $request->idprestamo_credito,
                'idtipocomprobante' => 0,
                'idmoneda' => 0,
                'idagencia' => 0,
                'idcliente' => $prestamo_credito->idcliente,
                'idasesor' => Auth::user()->id,
                'idcajero' => 0,
                'idestadocobranza' => 1,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);

            foreach ($cronograma['cuotas_pendientes_seleccionados'] as $value) {

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
                        'interesdescontado' => $value['interesrestante'],
                        'montorefinanciado' => 0,
                        'idestadocobranza' => 2
                    ]);
                  
                    DB::table('s_prestamo_cobranza')->whereId($idprestamocobranza)->update([
                        'cronograma_acuentaanterior' => 0,
                    ]); 
            }

            DB::table('s_prestamo_credito')->whereId($request->idprestamo_credito)->update([
                'fechaampliado' => Carbon::now(),
                'idestadocobranza' => 2,
            ]);
          
            // tipo credito
            $clientes = DB::table('s_prestamo_credito')
                ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
                ->join('s_prestamo_cartera','s_prestamo_cartera.id','=','cliente.idprestamocartera')
                ->join('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
                ->where('s_prestamo_credito.idcliente',$prestamo_credito->idcliente)
                ->where('s_prestamo_credito.idestado', 1)
                ->where('s_prestamo_credito.idtienda', $idtienda)
                ->whereIn('s_prestamo_credito.idestadocredito', [2,3,4])
                ->whereIn('s_prestamo_credito.idestadodesembolso', [0,1])
                ->where('s_prestamo_credito.idestadocobranza', 1)
                ->get();
          
            $tipocreditogenerado = 'CRÉDITO PRINCIPAL';
            if(count($clientes)>0){
                $tipocreditogenerado = 'CRÉDITO PARALELO';   
            }
          
            // REGISTRAR CREDITO AMPLIADO
          
            $cronograma = prestamo_cronograma(
              $idtienda,
              $request->monto,
              $request->numerocuota,
              $request->fechainicio,
              $request->idfrecuencia,
              $request->numerodias,
              $request->tasa,
              0,
              $request->excluirferiado,
              $request->excluirsabado,
              $request->excluirdomingo,
              $request->abono
            );
          
            // obtener ultimo código
            $prestamocredito = DB::table('s_prestamo_credito')
                ->where('s_prestamo_credito.idtienda',$idtienda)
                ->orderBy('s_prestamo_credito.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($prestamocredito!=''){
                $codigo = $prestamocredito->codigo+1;
            }
            // fin obtener ultimo código
            
            $idprestamo_credito = DB::table('s_prestamo_credito')->insertGetId([
              'fecharegistro' => Carbon::now(),
              'fechapreaprobado' => Carbon::now(),
              'codigo' => $codigo,
              'monto' => $request->input('monto'),
              'numerocuota' => $request->input('numerocuota'),
              'fechainiciocero' => $request->fechainicio,
              'fechainicio' => $cronograma['fechainicio'],
              'ultimafecha' => $cronograma['ultimafecha'],
              'numerodias' => $request->input('numerodias') ?? 0,
              'tasa' => $request->input('tasa'),
              'cuota' => $cronograma['cuota'],
              'comentariosupervisor' => '',
              'tipocredito' => 'CRÉDITO NORMAL',
              'tipocreditogenerado' => $tipocreditogenerado,
              'excluirsabado' => $request->input('excluirsabado')!='undefined' ? $request->input('excluirsabado'):'',
              'excluirdomingo' => $request->input('excluirdomingo')!='undefined' ? $request->input('excluirdomingo'):'',
              'excluirferiado' => $request->input('excluirferiado')!='undefined' ? $request->input('excluirferiado'):'',
              'total_amortizacion' => $cronograma['total_amortizacion'],
              'total_interes' => $cronograma['total_interes'],
              'total_cuota' => $cronograma['total_cuota'],
              'total_gastoadministrativo' => $cronograma['total_gastoadministrativo'],
              'total_segurodesgravamen' => $cronograma['total_segurodesgravamen'],
              'total_cuotafinal' => $cronograma['total_cuotafinal'],
              'total_abono' => $cronograma['total_abono'],
              'estadoexpediente' => 'no',
              'total_cuotafinaltotal' => $cronograma['total_cuotafinaltotal'],
              'facturacion_montorecibido' => 0,
              'facturacion_vuelto' => 0,
              'facturacion_cliente_identificacion' => '',
              'facturacion_cliente_nombre' => '',
              'facturacion_cliente_apellidos' => '',
              'facturacion_cliente_direccion' => '',
              'facturacion_idagencia' => 0,
              'facturacion_idtipocomprobante' => 0,
              'facturacion_idubigeo' => 0,
              'facturacion_idaperturacierre' => 0,
              'cronograma_primeratraso' => 0,
              'cronograma_total_cancelada_atraso' => 0,
              'cronograma_total_cancelada_cuota' => 0,
              'cronograma_total_cancelada_mora' => 0,
              'cronograma_total_cancelada_moradescontado' => 0,
              'cronograma_total_cancelada_moraapagar' => 0,
              'cronograma_total_cancelada_acuenta' => 0,
              'cronograma_total_cancelada_cuotapago' => 0,
              'cronograma_total_vencida_atraso' => 0,
              'cronograma_total_vencida_cuota' => 0,
              'cronograma_total_vencida_mora' => 0,
              'cronograma_total_vencida_moradescontado' => 0,
              'cronograma_total_vencida_moraapagar' => 0,
              'cronograma_total_vencida_acuenta' => 0,
              'cronograma_total_vencida_cuotapago' => 0,
              'cronograma_total_restante_atraso' => 0,
              'cronograma_total_restante_cuota' => 0,
              'cronograma_total_restante_mora' => 0,
              'cronograma_total_restante_moradescontado' => 0,
              'cronograma_total_restante_moraapagar' => 0,
              'cronograma_total_restante_acuenta' => 0,
              'cronograma_total_restante_cuotapago' => 0,
              'cronograma_total_pendiente_atraso' => 0,
              'cronograma_total_pendiente_cuota' => 0,
              'cronograma_total_pendiente_mora' => 0,
              'cronograma_total_pendiente_moradescontado' => 0,
              'cronograma_total_pendiente_moraapagar' => 0,
              'cronograma_total_pendiente_acuenta' => 0,
              'cronograma_total_pendiente_cuotapago' => 0,
              'idmoneda' => 1,
              'idasesor' => Auth::user()->id,
              'idcajero' => 0,
              'idsupervisor' => 0,
              'idcliente' => $prestamo_credito->idcliente,
              'idconyuge' => $prestamo_credito->idconyuge,
              'idgarante' => $prestamo_credito->idgarante,
              'idprestamo_frecuencia' => $request->input('idfrecuencia'),
              'idprestamo_tipotasa' => $cronograma['tipotasa'],
              'idprestamo_tipocredito' => 4, // 1=NORMAL, 2=REFINANCIADO,3=REPROGRAMADO,4=AMPLIADO, 5=CAMPAÑA
              'idprestamo_estadocredito' => 1, // 1=NORMAL, 2=GRUPAL
              'idprestamo_creditorefinanciado' => 0,
              'idprestamo_creditoreprogramado' => 0,
              'idprestamo_creditoampliado' => 0,
              'idestadocobranza' => 1, // 1 = PENDIENTE, 2 = CANCELADO
              'idestadocredito' => 2, // pre aprobado
              'idestadoaprobacion' => 0,
              'idestadodesembolso' => 0,
              'idestadogastoadministrativo' => 0,
              'idtienda' => $idtienda,
              'idestado' => 1,
            ]);

            foreach($cronograma['cronograma'] as $value) {
                  DB::table('s_prestamo_creditodetalle')->insert([
                    'numero' => $value['numero'],
                    'fechavencimiento' => $value['fechanormal'],
                    'saldocapital' => $value['saldo'],
                    'saldomontototal' => $value['saldototal'],
                    'amortizacion' => $value['amortizacion'],
                    'interes' => $value['interes'],
                    'cuota' => $value['cuota'],
                    'seguro' => $value['segurodesgravamen'],
                    'gastoadministrativo' => $value['gastoadministrativo'],
                    'total' => $value['cuotafinal'],
                    'abono' => $value['abono'],
                    'totalfinal' => $value['cuotafinaltotal'],
                    'atraso' => 0,
                    'mora' => 0,
                    'moradescuento' => 0,
                    'moraapagar' => 0,
                    'cuotapago' => 0,
                    'acuenta' => 0,
                    'cuotaapagar' => 0,
                    'cuotaapagartotal' => 0,
                    'montorefinanciado' => 0,
                    'interesdescontado' => 0,
                    'idprestamo_credito' => $idprestamo_credito,
                    'idestadocobranza' => 1,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                  ]);
            }
         
            prestamo_importar_ultimocredito($idtienda,$idprestamo_credito);
          
            // Actualizar pagos de creditos
            $cronograma = prestamo_cobranza_cronograma($idtienda,$idprestamo_credito,0,0,1,0);
            $idestadocobranza = 1; // PENDIENTE
            if(count($cronograma['cuotas_pendientes'])==0){
                $idestadocobranza = 2; // CANCELADO
            }
            DB::table('s_prestamo_credito')->whereId($idprestamo_credito)->update([
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
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    public function edit(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      $s_prestamo_credito = DB::table('s_prestamo_credito')
          ->join('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_credito.idprestamo_frecuencia')
          ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
          ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
          ->leftjoin('users as conyuge', 'conyuge.id', 's_prestamo_credito.idconyuge')
          ->join('tienda', 'tienda.id', 's_prestamo_credito.idtienda')
          ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_credito.idcajero')
          ->leftJoin('ubigeo','ubigeo.id','cliente.idubigeo')
          ->where([
            ['s_prestamo_credito.id', $id],
            ['s_prestamo_credito.idtienda', $idtienda]
          ])
          ->select(
              's_prestamo_credito.*',
              's_prestamo_frecuencia.nombre as frecuencia_nombre',
              's_prestamo_frecuencia.id as idprestamo_frecuencia',
              'tienda.nombre as tiendanombre',
              'cliente.id as idcliente',
              'cliente.direccion as cliente_direccion',
              'ubigeo.id as cliente_idubigeo',
              'ubigeo.nombre as cliente_ubigeonombre',
              'asesor.nombre as asesor_nombre',
              'asesor.apellidos as asesor_apellidos',
              'cajero.nombre as cajero_nombre',
              'cajero.apellidos as cajero_apellidos',
              'conyuge.identificacion as conyugeidentificacion',
              'conyuge.nombre as conyugenombre',
              'conyuge.apellidos as conyugeapellidos',
              DB::raw('IF(cliente.idtipopersona=1 || cliente.idtipopersona = 3,
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos,", ",cliente.nombre),
                  CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos)) as cliente_nombre'),
              DB::raw('IF(asesor.idtipopersona = 1 || asesor.idtipopersona = 3,
                  CONCAT(asesor.identificacion, " - ", asesor.apellidos, ", ", asesor.nombre),
                  CONCAT(asesor.identificacion, " - ", asesor.apellidos)) as asesor_nombre'),
              DB::raw('IF(conyuge.idtipopersona = 1 || conyuge.idtipopersona = 3,
                  CONCAT(conyuge.identificacion, " - ", conyuge.apellidos, ", ", conyuge.nombre),
                  CONCAT(conyuge.identificacion, " - ", conyuge.apellidos)) as conyuge_nombre')
          )
          ->first();
      
      
      if($request->view == 'ampliacion') {

          $cronograma = prestamo_cobranza_cronograma($idtienda,$s_prestamo_credito->id,0,0,1,$s_prestamo_credito->numerocuota);
        
          // pestaña crédito
          $frecuencias  = DB::table('s_prestamo_frecuencia')->get();
        
          return view('layouts/backoffice/tienda/sistema/prestamoampliacion/ampliacion', compact(
            's_prestamo_credito',
            'tienda',
            'cronograma',
            'frecuencias'
          ));
      }
      elseif ($request->view == 'detalle') {
          $s_prestamo_creditodetalle = DB::table('s_prestamo_creditodetalle')
            ->where('s_prestamo_creditodetalle.idprestamo_credito', $s_prestamo_credito->id)
            ->get();
          return view('layouts/backoffice/tienda/sistema/prestamoampliacion/detalle', compact(
            'tienda',
            's_prestamo_credito',
            's_prestamo_creditodetalle'
          ));
      }
    }

    public function update(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    public function destroy(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
    }
}
