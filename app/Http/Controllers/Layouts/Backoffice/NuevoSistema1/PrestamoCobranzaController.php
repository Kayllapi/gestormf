<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;

class PrestamoCobranzaController extends Controller
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
        $prestamocobranzas = DB::table('s_prestamo_cobranza')
          ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_cobranza.idprestamo_credito')
          ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
          ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
          ->where([
            ['s_prestamo_cobranza.idtienda', $idtienda]
          ])
          ->select(
            's_prestamo_cobranza.*',
            'cliente.nombre as cliente_nombre',
            'asesor.nombre as asesor_nombre',
            DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente')
          )
          ->orderBy('s_prestamo_cobranza.id','desc')
          ->paginate(10);
      
        return view('layouts/backoffice/tienda/sistema/prestamocobranza/index', [
            'tienda'      => $tienda,
            'prestamocobranzas' => $prestamocobranzas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda   = DB::table('tienda')->whereId($idtienda)->first();
        return view('layouts/backoffice/tienda/sistema/prestamocobranza/create', [
            'tienda' => $tienda
        ]);
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
      
        if($request->input('view') == 'registrar') {
          
            $rules = [
                'idprestamo_credito' => 'required',
                'idtipopago' => 'required',
            ];
            if($request->input('idtipopago')==1){
                $rules = array_merge($rules,[
                    'hastacuota' => 'required',
                    'montorecibido' => 'required'
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
          
            $messages = [
                'idprestamo_credito.required' => 'El "Cliente" es Obligatorio.',
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
            ];
            $this->validate($request, $rules, $messages);
          
            $prestamo_morapagadas = DB::table('s_prestamo_mora')
                ->where('s_prestamo_mora.idprestamo_credito', $request->idprestamo_credito)
                ->where('s_prestamo_mora.idestado', 2)
                ->sum('s_prestamo_mora.monto');

            $cronograma = prestamo_cobranza_cronograma($idtienda,$request->idprestamo_credito,$request->moradescuento+$prestamo_morapagadas,$request->montocompleto,$request->idtipopago,$request->hastacuota);
          
            $moradescuento = 0;
            if($request->input('check_moradescuento')=='on'){
                if($cronograma['select_mora'] < $request->moradescuento) {
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El "Descontar Mora" debe ser menor o igual que al "Total de Moras".'
                    ]);
                }
                $moradescuento = $request->moradescuento;
            }
          
            $montocompleto = 0;
            $montorecibido = 0;
            $hastacuota = 0;
            if($request->idtipopago == 1) {
                if($cronograma['select_cuotaapagarredondeado'] > $request->montorecibido) {
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El "Monto Recibido" debe ser mayor o igual que el Redondeado.'
                    ]);
                }
                $montorecibido = $request->montorecibido;
                $hastacuota = $request->hastacuota;
            }elseif($request->idtipopago == 2) {
                $montocompleto = $request->montocompleto;
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
          
            $idprestamocobranza = DB::table('s_prestamo_cobranza')->insertGetId([
                'fecharegistro' => Carbon::now(),
                'codigo' => Carbon::now()->format('ymdhms'),
                'cronograma_moradescuento' => $moradescuento,
                'cronograma_montocompleto' => $montocompleto,
                'cronograma_montorecibido' => $montorecibido,
                'cronograma_vuelto' => $request->vuelto!=''?$request->vuelto:0,
                'cronograma_idtipopago' => $request->idtipopago,
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
          
            if($request->input('check_moradescuento')=='on'){
                DB::table('s_prestamo_mora')->insert([
                  'fecharegistro' => Carbon::now(),
                  'monto' => $request->moradescuento,
                  'motivo' => '',
                  'documento' => '',
                  'idprestamo_credito' => $request->idprestamo_credito,
                  'idcliente' => $request->facturacion_idcliente,
                  'idresponsableregistro' => Auth::user()->id,
                  'idresponsableconfirmacion' => 0,
                  'idtienda' => $idtienda,
                  'idestado' => 1
                ]);
              
                
            }
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
            ->whereIn('s_prestamo_credito.idestadocredito', [4, 5, 6])
          
            ->orWhere('cliente.nombre','LIKE', '%'.$request->buscar.'%')
            ->where('s_prestamo_credito.idestado', 1)
            ->where('s_prestamo_credito.idtienda', $idtienda)
            ->whereIn('s_prestamo_credito.idestadocredito', [4, 5, 6])
          
            ->orWhere('cliente.apellidos','LIKE', '%'.$request->buscar.'%')
            ->where('s_prestamo_credito.idestado', 1)
            ->where('s_prestamo_credito.idtienda', $idtienda)
            ->whereIn('s_prestamo_credito.idestadocredito', [4, 5, 6])
          
            ->orWhere('s_prestamo_credito.codigo',$request->buscar)
            ->where('s_prestamo_credito.idestado', 1)
            ->where('s_prestamo_credito.idtienda', $idtienda)
            ->whereIn('s_prestamo_credito.idestadocredito', [4, 5, 6])
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
      elseif($id=='show-index'){
          $prestamocobranzas = DB::table('s_prestamo_cobranza')
              ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_cobranza.idprestamo_credito')
              ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
              ->where([
                ['s_prestamo_cobranza.idtienda', $idtienda],
                ['s_prestamo_cobranza.codigo','LIKE','%'.$request->input('columns')[0]['search']['value'].'%'],
                ['s_prestamo_cobranza.fecharegistro','LIKE','%'.$request->input('columns')[1]['search']['value'].'%'],
                ['cliente.apellidos','LIKE','%'.$request->input('columns')[3]['search']['value'].'%'],
                ['asesor.nombre','LIKE','%'.$request->input('columns')[4]['search']['value'].'%']
              ])
              ->orWhere([
                ['s_prestamo_cobranza.idtienda', $idtienda],
                ['s_prestamo_cobranza.codigo','LIKE','%'.$request->input('columns')[0]['search']['value'].'%'],
                ['s_prestamo_cobranza.fecharegistro','LIKE','%'.$request->input('columns')[1]['search']['value'].'%'],
                ['cliente.nombre','LIKE','%'.$request->input('columns')[3]['search']['value'].'%'],
                ['asesor.nombre','LIKE','%'.$request->input('columns')[4]['search']['value'].'%']
              ])
              ->select(
                's_prestamo_cobranza.*',
                'cliente.nombre as cliente_nombre',
                'asesor.nombre as asesor_nombre',
                DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente')
              )
              ->orderBy('s_prestamo_cobranza.id','desc')
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
                if($value->idestado==1){
                    $estado = '<span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Correcto</span>';
                }elseif($value->idestado==2){
                    $estado = '<span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span>';
                }
              
                $classname = '';
                $btn_anular = '';
                if($idaperturacierre==$value->s_idaperturacierre){
                    $classname = 'mx-table-warning';
                    $btn_anular = '<li><a href="'.url('backoffice/tienda/sistema/'.$idtienda.'/prestamocobranza/'.$value->id.'/edit?view=anular').'"><i class="fa fa-ban"></i> Anular</a></li>';
                }
              
                $opcion = '<li><a href="'.url('backoffice/tienda/sistema/'.$idtienda.'/prestamocobranza/'.$value->id.'/edit?view=ticket').'"><i class="fa fa-receipt"></i> Ticket</a></li>
                                  <li><a href="'.url('backoffice/tienda/sistema/'.$idtienda.'/prestamocobranza/'.$value->id.'/edit?view=detalle').'"><i class="fa fa-list"></i> Detalle</a></li>
                                  '.$btn_anular;
              
                $tabla[] = [
                    'idcobranza' => $value->id,
                    'codigo' => $value->codigo,
                    'fechapago' => date_format(date_create($value->fecharegistro), "d/m/Y h:i:s A"),
                    'monto' => $value->select_cuotaapagarredondeado,
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
      elseif($id=='show-indexpagorealizado'){
          $prestamocobranzas = DB::table('s_prestamo_cobranza')
              ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_cobranza.idprestamo_credito')
              ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->join('users as cajero', 'cajero.id', 's_prestamo_credito.idcajero')
              ->where([
                ['s_prestamo_cobranza.idprestamo_credito', $request->idcredito],
                ['s_prestamo_cobranza.idtienda', $idtienda],
                ['s_prestamo_cobranza.codigo','LIKE','%'.$request->input('columns')[0]['search']['value'].'%'],
                ['s_prestamo_cobranza.fecharegistro','LIKE','%'.$request->input('columns')[1]['search']['value'].'%'],
                ['cliente.apellidos','LIKE','%'.$request->input('columns')[3]['search']['value'].'%'],
                ['cajero.nombre','LIKE','%'.$request->input('columns')[4]['search']['value'].'%']
              ])
              ->orWhere([
                ['s_prestamo_cobranza.idprestamo_credito', $request->idcredito],
                ['s_prestamo_cobranza.idtienda', $idtienda],
                ['s_prestamo_cobranza.codigo','LIKE','%'.$request->input('columns')[0]['search']['value'].'%'],
                ['s_prestamo_cobranza.fecharegistro','LIKE','%'.$request->input('columns')[1]['search']['value'].'%'],
                ['cliente.nombre','LIKE','%'.$request->input('columns')[3]['search']['value'].'%'],
                ['cajero.nombre','LIKE','%'.$request->input('columns')[4]['search']['value'].'%']
              ])
              ->select(
                's_prestamo_cobranza.*',
                'cajero.nombre as cajero_nombre'
              )
              ->orderBy('s_prestamo_cobranza.id','desc')
              ->paginate($request->input('length'), ['*'], 'page', (($request->input('start')/$request->input('length'))+1));
        
            // aperturacaja
            $caja = caja($idtienda,Auth::user()->id);
            $idaperturacierre = 0;
            if($caja['resultado']=='ABIERTO'){
                $idaperturacierre = $caja['apertura']->id;
            }
        
            $tabla = [];
            $i = 0;
            foreach($prestamocobranzas as $value){
              
                $estado = '';
                if($value->idestado==1){
                    $estado = '<span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Correcto</span>';
                }elseif($value->idestado==2){
                    $estado = '<span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span>';
                }
                
                $classname = '';
                $btn_anular = '';
                if($idaperturacierre==$value->s_idaperturacierre){
                    $classname = 'mx-table-warning';
                    if($i==0 && $value->idestado==1){
                        $btn_anular = '<li><a href="javascript:;" onclick="anular_pagorealizado('.$value->id.')"><i class="fa fa-ban"></i> Anular</a></li>';
                        $i++;
                    }
                }
              
                $opcion = '<li><a href="javascript:;" onclick="ticket_pagorealizado('.$value->id.')"><i class="fa fa-receipt"></i> Ticket</a></li>
                                  <li><a href="javascript:;" onclick="detalle_pagorealizado('.$value->id.')"><i class="fa fa-list"></i> Detalle</a></li>
                                  '.$btn_anular;
              
                $tabla[] = [
                    'idcobranza' => $value->id,
                    'codigo' => $value->codigo,
                    'fechapago' => date_format(date_create($value->fecharegistro), "d/m/Y h:i:s A"),
                    'monto' => $value->select_acuentacuotaapagarredondeado,
                    'responsable' => $value->cajero_nombre,
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
          
          $prestamo_morapagadas = DB::table('s_prestamo_mora')
                ->where('s_prestamo_mora.idprestamo_credito', $s_prestamo_credito->id)
                ->where('s_prestamo_mora.idestado', 2)
                ->sum('s_prestamo_mora.monto');
        
          $cronograma = prestamo_cobranza_cronograma($idtienda,$s_prestamo_credito->id,0,$prestamo_morapagadas,1,0);
          $agencias = DB::table('s_agencia')->where('s_agencia.idtienda', $idtienda)->get();
          $tipocomprobantes = DB::table('s_tipocomprobante')->get();
          $monedas = DB::table('s_moneda')->get();
          $configuracion = configuracion_prestamo($idtienda);
          $configuracion_facturacion = configuracion_facturacion($idtienda);
          return view('layouts/backoffice/tienda/sistema/prestamocobranza/cobranza', compact(
            's_prestamo_credito',
            'tienda',
            'cronograma',
            'agencias',
            'tipocomprobantes',
            'monedas',
            'configuracion',
            'configuracion_facturacion'
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
        
          $prestamo_morapagadas = DB::table('s_prestamo_mora')
                ->where('s_prestamo_mora.idprestamo_credito', $s_prestamo_credito->id)
                ->where('s_prestamo_mora.idestado', 2)
                ->sum('s_prestamo_mora.monto');
        
          $cronograma = prestamo_cobranza_cronograma($idtienda,$s_prestamo_credito->id,$request->moradescuento+$prestamo_morapagadas,$request->montocompleto,$request->idtipopago,$request->hastacuota);
          return view('layouts/backoffice/tienda/sistema/prestamocobranza/cuotapendiente', compact(
            's_prestamo_credito',
            'tienda',
            'cronograma',
            'request',
            'prestamo_morapagadas'
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
          return view('layouts/backoffice/tienda/sistema/prestamocobranza/pagorealizado', compact(
            's_prestamo_credito',
            'tienda'
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
          $configuracion_facturacion = configuracion_facturacion($idtienda);
          $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamocobranza/pagorealizadoticketpdf', compact(
              's_prestamo_credito',
              'cobranza',
              'cobranzadetalle',
              'agencia',
              'tienda',
              'configuracion_facturacion'
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
                  's_moneda.simbolo as monedasimbolo'
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
          $configuracion_facturacion = configuracion_facturacion($idtienda);
          $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamocobranza/ticketpdf', compact(
              'cobranza',
              'cobranzadetalle',
              'agencia',
              'tienda',
              'configuracion_facturacion'
          ));
          return $pdf->stream('Ticket Cobranza.pdf');
      }
      elseif ($request->view == 'detalle') {
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
          $cobranzadetalle = DB::table('s_prestamo_cobranzadetalle')
              ->where('s_prestamo_cobranzadetalle.idprestamo_cobranza', $id)
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
      elseif ($request->view == 'anular') {
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
          $cobranzadetalle = DB::table('s_prestamo_cobranzadetalle')
              ->where('s_prestamo_cobranzadetalle.idprestamo_cobranza', $id)
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
        $request->user()->authorizeRoles($request->path(), $idtienda);
        if ($request->input('view') == 'editar') {
          $rules = [
            'dia' => 'required',
            'mes' => 'required',
            'motivo' => 'required'
          ];
          $messages = [
            'dia.required' => 'El "Dia" es Obligatorio.',
            'mes.required' => 'El "Mes" es Obligatorio.',
            'motivo.required' => 'El "Motivo" es Obligatorio.'
          ];
          $this->validate($request, $rules, $messages);

          DB::table('s_prestamo_cobranza')->whereId($id)->update([
            'dia' => $request->input('dia'),
            'mes' => $request->input('mes'),
            'motivo' => $request->input('motivo')
          ]);
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha actualizado correctamente.'
          ]);
        }
        elseif ($request->input('view') == 'anular_pagorealizado') {
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
          
            $s_prestamo_cobranza = DB::table('s_prestamo_cobranza')->whereId($id)->first();
          
            if($idaperturacierre!=$s_prestamo_cobranza->s_idaperturacierre){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La Apertura no coincide con la apertura actual.'
                ]);
            }
          
            $s_prestamo_creditodetalle = DB::table('s_prestamo_creditodetalle')
                ->where('s_prestamo_creditodetalle.idprestamo_credito',$s_prestamo_cobranza->idprestamo_credito)
                ->where('s_prestamo_creditodetalle.acuenta','<>','0.00')
                ->orWhere('s_prestamo_creditodetalle.idprestamo_credito',$s_prestamo_cobranza->idprestamo_credito)
                ->where('s_prestamo_creditodetalle.cuotaapagar','<>','0.00')
                //->where('s_prestamo_creditodetalle.idestadocobranza',2)
                ->orderBy('s_prestamo_creditodetalle.numero','desc')
                ->get();
            
            $total_cuotaapagar = $s_prestamo_cobranza->select_acuentacuotaapagar; //300
            foreach($s_prestamo_creditodetalle as $value){
                  if(($value->cuotaapagar+$value->acuenta) <= $total_cuotaapagar && $total_cuotaapagar > 0){
                    $total_cuotaapagar = $total_cuotaapagar-($value->cuotaapagar+$value->acuenta);
                    $acuenta = 0;
                    $cuotaapagar = 0;
                    /*if($value->idestadocobranza==1){ // acuenta
                        $acuenta = 0;
                        $cuotaapagar = 0;
                    }elseif($value->idestadocobranza==2){ // cancelado
                        $acuenta = 0;
                        $cuotaapagar = $total_cuotaapagar;
                    }*/
                    DB::table('s_prestamo_creditodetalle')->whereId($value->id)->update([
                        'atraso' => 0,
                        'moradescuento' => 0,
                        'moraapagar' => 0,
                        'cuotapago' => 0,
                        'acuenta' => $acuenta,
                        'cuotaapagar' => $cuotaapagar,
                        'idestadocobranza' => 1
                    ]);
                }elseif(($value->cuotaapagar+$value->acuenta) > $total_cuotaapagar && $total_cuotaapagar > 0){
                    $acuenta = ($value->cuotaapagar+$value->acuenta)-$total_cuotaapagar;
                    $total_cuotaapagar = 0;
                    DB::table('s_prestamo_creditodetalle')->whereId($value->id)->update([
                        'atraso' => 0,
                        'moradescuento' => 0,
                        'moraapagar' => 0,
                        'cuotapago' => 0,
                        'acuenta' => $acuenta,
                        'cuotaapagar' => $total_cuotaapagar,
                        'idestadocobranza' => 1
                    ]);
                }
            }
          
            DB::table('s_prestamo_cobranza')->whereId($id)->update([
                'fechaanulacion' => Carbon::now(),
                'idestado' => 2,
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha anulado correctamente.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
