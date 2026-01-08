<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;

class PrestamoRefinanciacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda = DB::table('tienda')->whereId($idtienda)->first();

      $refinanciaciones = DB::table('s_prestamo_credito')
        ->join('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_credito.idprestamo_frecuencia')
        ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
        ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
        ->where([
          ['s_prestamo_credito.idestadocredito', 2], // credito pre aprobado
          ['s_prestamo_credito.idtipocredito', 2], // credito refinanciado
          ['s_prestamo_credito.idtienda', $idtienda],
          ['s_prestamo_credito.idasesor', Auth::user()->id]
        ])
        ->select(
            's_prestamo_credito.*',
            's_prestamo_frecuencia.nombre as frecuencia_nombre',
            'asesor.nombre as asesor_nombre',
            DB::raw('IF(cliente.idtipopersona=1,
            CONCAT(cliente.apellidos,", ",cliente.nombre),
            CONCAT(cliente.apellidos)) as cliente'),
        )
        ->orderBy('s_prestamo_credito.id','desc')
        ->paginate(10);

      return view('layouts/backoffice/tienda/sistema/prestamorefinanciacion/index', compact(
        'tienda',
        'refinanciaciones'
      ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      return view('layouts/backoffice/tienda/sistema/prestamorefinanciacion/create', compact(
        'tienda',
      ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      if ($request->input('view') == 'registrar-refinanciacion') {
        $rules = [
          'idprestamo_credito' => 'required',
        ];
        if($request->input('check_idconyuge')=='on'){
          $rules = array_merge($rules,[
            'idconyuge' => 'required'
          ]);
        }

        $rules = array_merge($rules,[
          'monto' => 'required',
          'numerocuota' => 'required',
          'fechainicio' => 'required',
          'idfrecuencia' => 'required',
          'idtasa' => 'required',
          'tasa' => 'required',
        ]);
        $messages = [
          'idprestamo_credito.required' => 'El "Cliente" es Obligatorio.',
          'idconyuge.required' => 'El "Cónyuge" es Obligatorio.',
          'monto.required' => 'El "Monto" es Obligatorio.',
          'numerocuota.required' => 'El "Nro. de Cuota" es Obligatorio.',
          'fechainicio.required' => 'La "Fecha de Inicio" es Obligatorio.',
          'idfrecuencia.required' => 'La "Frecuencia" es Obligatorio.',
          'idtasa.required' => 'La "Tasa" es Obligatorio.',
          'tasa.required' => 'El "Interes" es Obligatorio.',
        ];
        $this->validate($request, $rules, $messages);
        
        $prestamo_credito = DB::table('s_prestamo_credito')
          ->join('users', 'users.id', 's_prestamo_credito.idcliente')
          ->where('s_prestamo_credito.id', $request->idprestamo_credito)
          ->select(
            's_prestamo_credito.*',
            'users.direccion as direccion_cliente',
            'users.idubigeo as idubigeo_cliente'
          )
          ->first();
        
        // inicio registro de mora
            if ($request->moradescuento <= 0) {
              return response()->json([
                'resultado' => 'ERROR',
                'mensaje' => 'La Mora Descuento debe ser mayor a cero.'
              ]);
            }
            if ($request->moradescuento > $request->moradescuento_maximo) {
              return response()->json([
                'resultado' => 'ERROR',
                'mensaje' => "La Mora Descuento no puede ser mayor a $request->moradescuento_maximo"
              ]);
            }

            /* idestado
            * 1 = pendiente
            * 2 = confirmado
            * 3 = anulado
            */
            DB::table('s_prestamo_mora')->insert([
              'fecharegistro' => Carbon::now(),
              'monto' => $request->moradescuento,
              'motivo' => $request->motivo ?? '',
              'documento' => '',
              'idprestamo_credito' => $prestamo_credito->id,
              'idcliente' => $prestamo_credito->idcliente,
              'idtienda' => $idtienda,
              'idestado' => 1
        ]);
        // fin registro de mora
        
        /* db=s_prestamo_credito
         idestadocredito
         * 1 = credito pendiente
         * 2 = credito pre aprobado
         * 3 = aprobado
         * 4 = desembolsado
         * 5 = cancelado
         * 6 = refinanciado
         idestado
         * 1 = correcto
         * 2 = anulado
         idmoneda
         * 1 = soles
         * 2 = dolares
         idtipocredito
         * 1 = normal
         * 2 = refinanciado
         * 3 = reprogramado
        */
        
        // realizando cobranza de credito
          $moradescuento = $request->moradescuento;
          $montocompleto = 0;
          $idtipopago = 1;
          $montorecibido = $request->monto;
          $hastacuota = $request->hastacuota;
        
          $cronograma = prestamo_cobranza_cronograma($idtienda, $request->idprestamo_credito, $moradescuento, $montocompleto, $idtipopago, $hastacuota);

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
        
          $idprestamocobranza = DB::table('s_prestamo_cobranza')->insertGetId([
              'fecharegistro' => Carbon::now(),
              'codigo' => Carbon::now()->format('ymdhms'),
              'cronograma_moradescuento' => $moradescuento,
              'cronograma_montocompleto' => $montocompleto,
              'cronograma_montorecibido' => $montorecibido,
              'cronograma_vuelto' => 0,
              'cronograma_idtipopago' => $idtipopago,
              'cronograma_hastacuota' => $hastacuota,
              'select_cuota' => $cronograma['select_cuota'],
              'select_atraso' => $cronograma['select_atraso'],
              'select_mora' => $cronograma['select_mora'],
              'select_moradescontado' => $cronograma['select_moradescontado'],
              'select_moraapagar' => $cronograma['select_moraapagar'],
              'select_cuotapago' => $cronograma['select_cuotapago'],
              'select_cuotaapagar' => $cronograma['select_cuotaapagar'],
              'select_cuotaapagarredondeado' => $cronograma['select_cuotaapagarredondeado'],
              'select_acuentacuotaapagar' => $cronograma['select_acuentacuotaapagar'],
              'select_acuentacuotaapagarredondeado' => $cronograma['select_acuentacuotaapagarredondeado'],
              'cliente_direccion' => $prestamo_credito->direccion_cliente,
              'cliente_idubigeo' => $prestamo_credito->idubigeo_cliente,
              's_idaperturacierre' => $idaperturacierre,
              'idprestamo_credito' => $request->idprestamo_credito,
              'idtipocomprobante' => 1,
              'idmoneda' => 1,
              'idagencia' => 0,
              'idcliente' => $prestamo_credito->idcliente,
              'idasesor' => $cronograma['creditosolicitud']->idasesor,
              'idcajero' => Auth::user()->id,
              'idtienda' => $idtienda,
              'idestado' => 1
          ]);

          foreach ($cronograma['cuotas_pendientes_seleccionados'] as $value) {
              DB::table('s_prestamo_cobranzadetalle')->insert([
                  'idprestamo_cobranza' => $idprestamocobranza,
                  'idprestamo_creditodetalle' => $value['idprestamo_creditodetalle'],
              ]);
              /* idestadocobranza
               * 1 = cuota pendiente
               * 2 = cuota cancelado
              */
              $idestadocobranza = 2;
              if($value['estado'] == 'ACUENTA'){
                  $idestadocobranza = 1;
              }
              DB::table('s_prestamo_creditodetalle')->whereId($value['idprestamo_creditodetalle'])->update([
                  'atraso' => $value['atraso'],
                  'moradescuento' => $value['moradescontado'],
                  'moraapagar' => $value['moraapagar'],
                  'cuotapago' => $value['cuotapago'],
                  'acuenta' => $value['acuenta'],
                  'cuotaapagar' => $value['cuotaapagar'],
                  'idestadocobranza' => $idestadocobranza
              ]);
          }
        
          // Actualizando credito a refinanciado
          DB::table('s_prestamo_credito')->whereId($request->idprestamo_credito)->update([
            'idestadocredito' => 6
          ]);
        // fin realizando cobranza de credito
        
        $cronograma = prestamo_cronograma(
          $idtienda,
          $request->monto,
          $request->numerocuota,
          $request->fechainicio,
          $request->idfrecuencia,
          $request->numerodias,
          $request->idtasa,
          $request->tasa,
          0,
          $request->excluirferiado,
          $request->excluirsabado,
          $request->excluirdomingo
        );
        
        // Registrando nuevo credito refinanciado
        $idprestamo_credito = DB::table('s_prestamo_credito')->insertGetId([
          'fecharegistro' => Carbon::now(),
          'codigo' => Carbon::now()->format('ymdhis').rand(100, 999),
          'monto' => $request->input('monto'),
          'numerocuota' => $request->input('numerocuota'),
          'fechainicio' => $request->input('fechainicio'),
          'numerodias' => $request->input('numerodias') ?? 0,
          'tasa' => $request->input('tasa'),
          'cuota' => $cronograma['cuota'],
          'excluirsabado' => $request->excluirsabado != 'undefined' ? $request->excluirsabado : '',
          'excluirdomingo' => $request->excluirdomingo != 'undefined' ? $request->excluirdomingo : '',
          'excluirferiado' => $request->excluirferiado != 'undefined' ? $request->excluirferiado : '',
          'total_amortizacion' => $cronograma['total_amortizacion'],
          'total_interes' => $cronograma['total_interes'],
          'total_cuota' => $cronograma['total_cuota'],
          'total_segurodesgravamen' => $cronograma['total_segurodesgravamen'],
          'total_cuotafinal' => $cronograma['total_cuotafinal'],
          'idmoneda' => 1,
          'idasesor' => Auth::user()->id,
          'idsupervisor' => 0,
          'idcajero' => 0,
          'idcliente' => $prestamo_credito->idcliente,
          'idconyuge' => $request->input('idconyuge')!='' ? $request->input('idconyuge') : 0,
          'idprestamo_frecuencia' => $request->input('idfrecuencia'),
          'idprestamo_tipotasa' => $request->idtasa,
          'idtienda' => $idtienda,
          'idtipocredito' => 2,
          'idsolicitudrefinanciacion' => $request->idprestamo_credito,
          'idestadocredito' => 2,
          'idestadogastoadministrativo' => 0,
          'total_gastoadministrativo' => 0,
          'idestado' => 1,
        ]);

        foreach($cronograma['cronograma'] as $value) {
          DB::table('s_prestamo_creditodetalle')->insert([
            'numero' => $value['numero'],
            'fechavencimiento' => $value['fechanormal'],
            'saldocapital' => $value['saldo'],
            'amortizacion' => $value['amortizacion'],
            'interes' => $value['interes'],
            'cuota' => $value['cuota'],
            'seguro' => $value['segurodesgravamen'],
            'gastoadministrativo' => 0,
            'total' => $value['cuotafinal'],
            'atraso' => 0,
            'moradescuento' => 0,
            'moraapagar' => 0,
            'cuotapago' => 0,
            'acuenta' => 0,
            'cuotaapagar' => 0,
            'idprestamo_credito' => $idprestamo_credito,
            'idestadocobranza' => 1,
            'idestado' => 1
          ]);
        }
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha registrado correctamente.'
        ]);
      }
        
      
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      if ($id == 'show-creditocliente') {
        $clientes = DB::table('s_prestamo_credito')
            ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
            ->join('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
            ->where('cliente.identificacion','LIKE', '%'.$request->buscar.'%')
            ->where('s_prestamo_credito.idestado', 1)
            ->where('s_prestamo_credito.idtienda', $idtienda)
            ->whereIn('s_prestamo_credito.idestadocredito', [4])
          
            ->orWhere('cliente.nombre','LIKE', '%'.$request->buscar.'%')
            ->where('s_prestamo_credito.idestado', 1)
            ->where('s_prestamo_credito.idtienda', $idtienda)
            ->whereIn('s_prestamo_credito.idestadocredito', [4])
          
            ->orWhere('cliente.apellidos','LIKE', '%'.$request->buscar.'%')
            ->where('s_prestamo_credito.idestado', 1)
            ->where('s_prestamo_credito.idtienda', $idtienda)
            ->whereIn('s_prestamo_credito.idestadocredito', [4])
          
            ->orWhere('s_prestamo_credito.codigo',$request->buscar)
            ->where('s_prestamo_credito.idestado', 1)
            ->where('s_prestamo_credito.idtienda', $idtienda)
            ->whereIn('s_prestamo_credito.idestadocredito', [4])
            ->select(
                    's_prestamo_credito.id as id',
                    DB::raw('IF(s_prestamo_credito.idestadocredito = 4 && s_prestamo_credito.idestado = 1, "PENDIENTE", 
                    IF(s_prestamo_credito.idestadocredito = 5, "CANCELADO", "REFINANCIADO")) as estado'),
                    'cliente.identificacion as clienteidentificacion',
                    'cliente.apellidos as clienteapellidos',
                    'cliente.nombre as clientenombre',
                    's_moneda.simbolo as monedasimbolo',
                    's_prestamo_credito.codigo as creditocodigo',
                    's_prestamo_credito.monto as creditomonto',
                    's_prestamo_credito.fechadesembolsado as creditofechadesembolsado',
            )
            ->orderBy('s_prestamo_credito.fechadesembolsado','desc')
            ->get();
        $dataclientes = [];
        foreach($clientes as $value){
            $dataclientes[] = [
                'id' => $value->id,
                'text' => $value->estado.' <b>/</b> '.$value->creditocodigo.' <b>/</b> '.$value->clienteidentificacion.' - '.$value->clienteapellidos.', '.$value->clientenombre.' <b>/</b> '.date_format(date_create($value->creditofechadesembolsado),"d-m-Y").' <b>/</b> '.$value->monedasimbolo.' '.$value->creditomonto,
                'estado' => $value->estado,
                'creditocodigo' => $value->creditocodigo,
                'clienteidentificacion' => $value->clienteidentificacion,
                'clienteapellidos' => $value->clienteapellidos,
                'clientenombre' => $value->clientenombre,
                'monedasimbolo' => $value->monedasimbolo,
                'creditomonto' => $value->creditomonto,
                'creditofechadesembolsado' => date_format(date_create($value->creditofechadesembolsado),"d-m-Y")
            ];
        }
        return $dataclientes;
      }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
              'ubigeo.id as idubigeo',
              'ubigeo.nombre as ubigeo',
              'asesor.nombre as asesor_nombre',
              'asesor.apellidos as asesor_apellidos',
              'cajero.nombre as cajero_nombre',
              'cajero.apellidos as cajero_apellidos',
              'conyuge.identificacion as conyugeidentificacion',
              'conyuge.nombre as conyugenombre',
              'conyuge.apellidos as conyugeapellidos',
              DB::raw('IF(cliente.idtipopersona=1,
              CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos,", ",cliente.nombre),
              CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos)) as cliente'),
              DB::raw('IF(asesor.idtipopersona = 1 || asesor.idtipopersona = 3,
                  CONCAT(asesor.identificacion, " - ", asesor.apellidos, ", ", asesor.nombre),
                  CONCAT(asesor.identificacion, " - ", asesor.apellidos)) as asesor_nombre'),
              DB::raw('IF(conyuge.idtipopersona = 1 || conyuge.idtipopersona = 3,
                  CONCAT(conyuge.identificacion, " - ", conyuge.apellidos, ", ", conyuge.nombre),
                  CONCAT(conyuge.identificacion, " - ", conyuge.apellidos)) as conyuge_nombre')
          )
          ->first();
      
      if ($request->view == 'detalle') {
          $s_prestamo_creditodetalle = DB::table('s_prestamo_creditodetalle')
            ->where('s_prestamo_creditodetalle.idprestamo_credito', $s_prestamo_credito->id)
            ->get();
          return view('layouts/backoffice/tienda/sistema/prestamorefinanciacion/detalle', compact(
            'tienda',
            's_prestamo_credito',
            's_prestamo_creditodetalle'
          ));
      }
      elseif($request->view == 'refinanciacion') {
          $ultima_cuota = DB::table('s_prestamo_creditodetalle')
                ->where('s_prestamo_creditodetalle.idprestamo_credito', $s_prestamo_credito->id)
                ->orderBy('s_prestamo_creditodetalle.numero', 'desc')
                ->first();
          $cronograma = prestamo_cobranza_cronograma($idtienda,$s_prestamo_credito->id,0,0,1,0);
          $agencias = DB::table('s_agencia')->where('s_agencia.idtienda', $idtienda)->get();
          $tipocomprobantes = DB::table('s_tipocomprobante')->get();
          $monedas = DB::table('s_moneda')->get();
          $configuracion = configuracion_prestamo($idtienda);
          $configuracion_facturacion = configuracion_facturacion($idtienda);
        
          // pestaña crédito
          $frecuencias  = DB::table('s_prestamo_frecuencia')->get();
        
          return view('layouts/backoffice/tienda/sistema/prestamorefinanciacion/refinanciacion', compact(
            's_prestamo_credito',
            'tienda',
            'cronograma',
            'agencias',
            'tipocomprobantes',
            'monedas',
            'configuracion',
            'configuracion_facturacion',
            'ultima_cuota',
            'frecuencias'
          ));
      }
      elseif ($request->view == 'cuotapendiente') {
          $cronograma = prestamo_cobranza_cronograma($idtienda,$s_prestamo_credito->id,$request->moradescuento,$request->montocompleto,$request->idtipopago,$request->hastacuota);
          return view('layouts/backoffice/tienda/sistema/prestamorefinanciacion/cuotapendiente', compact(
            's_prestamo_credito',
            'tienda',
            'cronograma',
            'request'
          ));
      }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idtienda, $id)
    {
      
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idtienda, $id)
    {
      
    }
}
