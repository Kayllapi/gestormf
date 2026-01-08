<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;

class PrestamoDesembolsoController extends Controller
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

      return view('layouts/backoffice/tienda/sistema/prestamodesembolso/index',[
        'tienda' => $tienda,
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
      if ($id == 'show-creditodesembolsado') {
        $buscar_fechainicio = $request->input('columns')[3]['search']['value'];
        $buscar_idprestamo_frecuencia = $request->input('columns')[4]['search']['value'];
        $buscar_idprestamo_tipotasa = $request->input('columns')[5]['search']['value'];
        $buscar_asesor_nombre = $request->input('columns')[7]['search']['value'];
        $buscar_cliente = $request->input('columns')[8]['search']['value'];

        $where = [];
        if($buscar_fechainicio!=''){
            $where[] = ['s_prestamo_credito.fechainicio',$buscar_fechainicio];
        }
        if($buscar_idprestamo_frecuencia!=''){
            $where[] = ['s_prestamo_frecuencia.id',$buscar_idprestamo_frecuencia];
        }
        if($buscar_idprestamo_tipotasa!=''){
            $where[] = ['s_prestamo_credito.idprestamo_tipotasa',$buscar_idprestamo_tipotasa];
        }
        $where[] = ['asesor.nombre','LIKE','%'.$buscar_asesor_nombre.'%'];
        $where[] = ['cliente.nombre','LIKE','%'.$buscar_cliente.'%'];

        if($request->input('view')=='pendiente'){
            $where[] = ['s_prestamo_credito.idestadocredito', 1];
        }elseif($request->input('view')=='preaprobado'){
            $where[] = ['s_prestamo_credito.idestadocredito', 2];
        }elseif($request->input('view')=='aprobado'){
            $where[] = ['s_prestamo_credito.idestadocredito', 3];
        }elseif($request->input('view')=='desembolsado'){
            $where[] = ['s_prestamo_credito.idestadocredito', 4];
        }

        $where1 = [];
        if($buscar_fechainicio!=''){
            $where1[] = ['s_prestamo_credito.fechainicio',$buscar_fechainicio];
        }
        if($buscar_idprestamo_frecuencia!=''){
            $where1[] = ['s_prestamo_frecuencia.id',$buscar_idprestamo_frecuencia];
        }
        if($buscar_idprestamo_tipotasa!=''){
            $where1[] = ['s_prestamo_credito.idprestamo_tipotasa',$buscar_idprestamo_tipotasa];
        }
        $where1[] = ['asesor.nombre','LIKE','%'.$buscar_asesor_nombre.'%'];
        $where1[] = ['cliente.apellidos','LIKE','%'.$buscar_cliente.'%'];
        if($request->input('view')=='pendiente'){
            $where1[] = ['s_prestamo_credito.idestadocredito', 1];
        }elseif($request->input('view')=='preaprobado'){
            $where1[] = ['s_prestamo_credito.idestadocredito', 2];
        }elseif($request->input('view')=='aprobado'){
            $where1[] = ['s_prestamo_credito.idestadocredito', 3];
        }elseif($request->input('view')=='desembolsado'){
            $where1[] = ['s_prestamo_credito.idestadocredito', 4];
        }

      $prestamocreditos_desembolsados = DB::table('s_prestamo_credito')
          ->join('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_credito.idprestamo_frecuencia')
          ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
          ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
          ->where($where)
          ->where('s_prestamo_credito.idtienda', $idtienda)
          ->where('s_prestamo_credito.idasesor', Auth::user()->id)
          ->orWhere($where1)
          ->where('s_prestamo_credito.idtienda', $idtienda)
          ->where('s_prestamo_credito.idasesor', Auth::user()->id)
          ->select(
              's_prestamo_credito.id as idcredito',
              's_prestamo_credito.idtienda as idtienda',
              's_prestamo_credito.fechapreaprobado as fechapreaprobado',
              's_prestamo_credito.fechaaprobado as fechaaprobado',
              's_prestamo_credito.fechadesembolsado as fechadesembolsado',
              's_prestamo_credito.monto as monto',
              's_prestamo_credito.numerocuota as numerocuota',
              's_prestamo_credito.fechainicio as fechainicio',
              's_prestamo_credito.idprestamo_tipotasa as idprestamo_tipotasa',
              's_prestamo_credito.tasa as tasa',
              's_prestamo_credito.idestadocredito as idestadocredito',
              's_prestamo_credito.idestado as idestado',
              's_prestamo_frecuencia.nombre as frecuencia_nombre',
              'asesor.nombre as asesor_nombre',
              DB::raw('IF(cliente.idtipopersona=1,
              CONCAT(cliente.apellidos,", ",cliente.nombre),
              CONCAT(cliente.apellidos)) as cliente'),
          )
          ->orderBy('s_prestamo_credito.id','desc')
          ->paginate($request->input('length'), ['*'], 'page', (($request->input('start')/$request->input('length'))+1));

        
            $tabla = [];
            foreach($prestamocreditos_desembolsados as $value){
                $tipotasa = '';
                if($value->idprestamo_tipotasa==1){
                    $tipotasa = 'Fija';
                }elseif($value->idprestamo_tipotasa==2){
                    $tipotasa = 'Efectiva';
                }
              
                $estado = '';
                if($value->idestado==1){
                    $estado = '<span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Activado</span>';
                }elseif($value->idestado==2){
                    $estado = '<span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span>';
                }
              
                $fecharegistro = $value->fechapreaprobado;
                if($value->idestadocredito==3){
                $opcion = '<li><a href="javascript:;" onclick="desembolsar_aprobado('.$idtienda.','.$value->idcredito.')"><i class="fa fa-check"></i> Desembolsar</a></li>
                            <li><a href="javascript:;" onclick="remover_aprobado('.$idtienda.','.$value->idcredito.')"><i class="fa fa-ban"></i> Anular</a></li>
                            <li><a href="javascript:;" onclick="detalle_aprobado('.$idtienda.','.$value->idcredito.')"><i class="fa fa-list"></i> Detalle</a></li>';
                $fecharegistro = $value->fechaaprobado;
                }elseif($value->idestadocredito==4){
                $opcion = '<li><a href="javascript:;" onclick="anular_desembolsado('.$idtienda.','.$value->idcredito.')"><i class="fa fa-ban"></i> Anular</a></li>
                            <li><a href="javascript:;" onclick="ticket_desembolsado('.$idtienda.','.$value->idcredito.')"><i class="fa fa-ticket-alt"></i> Ticket</a></li>
                            <li><a href="javascript:;" onclick="cronograma_desembolsado('.$idtienda.','.$value->idcredito.')"><i class="fa fa-calendar-check"></i> Cronograma</a></li>
                            <li><a href="javascript:;" onclick="tarjeta_desembolsado('.$idtienda.','.$value->idcredito.')"><i class="fa fa-credit-card"></i> Tarjeta de Pago</a></li>
                            <li><a href="javascript:;" onclick="documento_desembolsado('.$idtienda.','.$value->idcredito.')"><i class="fa fa-folder-open"></i> Documentos</a></li>
                            <li><a href="javascript:;" onclick="detalle_desembolsado('.$idtienda.','.$value->idcredito.')"><i class="fa fa-list"></i> Detalle</a></li>';
                $fecharegistro = $value->fechadesembolsado;
                }
              
                $tabla[] = [
                    'fecharegistro' => date_format(date_create($fecharegistro), "d/m/Y h:i:s A"),
                    'monto' => $value->monto,
                    'numerocuota' => $value->numerocuota,
                    'fechainicio' => date_format(date_create($value->fechainicio), "d/m/Y"),
                    'frecuencia_nombre' => $value->frecuencia_nombre,
                    'tipotasa' => $tipotasa,
                    'tasa' => $value->tasa,
                    'asesor_nombre' => $value->asesor_nombre,
                    'cliente' => $value->cliente,
                    'estado' => $estado,
                    'opcion' => $opcion
                ];
            }
        
            return json_encode([
                'draw' => $request->input('draw'),
                'recordsTotal' => $prestamocreditos_desembolsados->total(),
                'recordsFiltered' => $prestamocreditos_desembolsados->total(),
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
        $configuracion_prestamo = configuracion_prestamo($idtienda);
        $prestamodesembolso = DB::table('s_prestamo_credito')
            ->join('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_credito.idprestamo_frecuencia')
            ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
            ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
            ->join('ubigeo', 'ubigeo.id', 'cliente.idubigeo')
            ->join('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
            ->leftjoin('users as conyuge', 'conyuge.id', 's_prestamo_credito.idconyuge')
            ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_credito.idcajero')
            ->where('s_prestamo_credito.id', $id)
            ->select(
                's_prestamo_credito.*',
                's_prestamo_frecuencia.nombre as frecuencia_nombre',
                'cliente.nombre as cliente',
                'cliente.identificacion as cliente_identificacion',
                'cliente.apellidos as cliente_apellido',
                'cliente.direccion as cliente_direccion',
                'ubigeo.id as cliente_idubigeo',
                'ubigeo.nombre as cliente_ubigeonombre',
                'ubigeo.codigo as cliente_ubigeocodigo',
                's_moneda.simbolo as monedasimbolo',
                's_moneda.nombre as monedanombre',
                DB::raw('IF(cajero.idtipopersona = 1 || cajero.idtipopersona = 3,
                    CONCAT(cajero.apellidos, ", ", cajero.nombre),
                    CONCAT(cajero.apellidos)) as cajero_nombre'),
                DB::raw('IF(asesor.idtipopersona = 1 || asesor.idtipopersona = 3,
                    CONCAT(asesor.apellidos, ", ", asesor.nombre),
                    CONCAT(asesor.apellidos)) as asesor_nombre'),
                DB::raw('IF(cliente.idtipopersona = 1 || cliente.idtipopersona = 3,
                    CONCAT(cliente.apellidos, ", ", cliente.nombre),
                    CONCAT(cliente.apellidos)) as cliente_nombre'),
                DB::raw('IF(conyuge.idtipopersona = 1 || conyuge.idtipopersona = 3,
                    CONCAT(conyuge.identificacion, " - ", conyuge.apellidos, ", ", conyuge.nombre),
                    CONCAT(conyuge.identificacion, " - ", conyuge.apellidos)) as conyuge_nombre')
            )
            ->first();
        $prestamodesembolsodetalle = DB::table('s_prestamo_creditodetalle')
            ->where('s_prestamo_creditodetalle.idprestamo_credito', $prestamodesembolso->id)
            ->orderBy('s_prestamo_creditodetalle.numero','asc')
            ->get();
      
        if ($request->input('view') == 'desembolsar') {
          $agencias = DB::table('s_agencia')->where('s_agencia.idtienda', $idtienda)->get();
          $ubigeo = DB::table('ubigeo')->get();
          $tipocomprobante = DB::table('s_tipocomprobante')->get();
          $monedas = DB::table('s_moneda')->get();
          $configuracion_facturacion = configuracion_facturacion($idtienda);
          return view('layouts/backoffice/tienda/sistema/prestamodesembolso/desembolsar', compact(
            'tienda',
            'prestamodesembolso',
            'prestamodesembolsodetalle',
            'configuracion_prestamo',
            'agencias',
            'tipocomprobante',
            'ubigeo',
            'monedas',
            'configuracion_facturacion'
          ));
        }
        elseif ($request->input('view') == 'remover') {
          return view('layouts/backoffice/tienda/sistema/prestamodesembolso/remover', compact(
            'tienda',
            'prestamodesembolso',
            'prestamodesembolsodetalle',
            'configuracion_prestamo',
          ));
        }
        elseif ($request->input('view') == 'detalle') {
          return view('layouts/backoffice/tienda/sistema/prestamodesembolso/detalle', compact(
            'tienda',
            'prestamodesembolso',
            'prestamodesembolsodetalle',
            'configuracion_prestamo'
          ));
        }
        elseif ($request->input('view') == 'detalledesembolso') {
          $facturacion = DB::table('s_prestamo_facturacion')
            ->leftJoin('s_moneda', 's_moneda.id', 's_prestamo_facturacion.idmoneda')
            ->leftJoin('s_tipocomprobante', 's_tipocomprobante.id', 's_prestamo_facturacion.idtipocomprobante')
            ->leftJoin('users as cliente', 'cliente.id', 's_prestamo_facturacion.idcliente')
            ->leftJoin('ubigeo as clienteubigeo', 'clienteubigeo.id', 's_prestamo_facturacion.idubigeo')
            ->leftJoin('s_agencia', 's_agencia.id', 's_prestamo_facturacion.idagencia')
            ->where('s_prestamo_facturacion.idprestamo_credito', $prestamodesembolso->id)
            ->select(
                's_prestamo_facturacion.*',
                's_moneda.nombre as monedanombre',
                's_agencia.ruc as agenciaruc',
                's_agencia.razonsocial as agenciarazonsocial',
                'clienteubigeo.nombre as clienteubigeonombre',
                'cliente.nombre as clientenombre',
                'cliente.apellidos as clienteapellidos',
                's_tipocomprobante.nombre as tipocomprobantenombre',
            )
            ->limit(1)
            ->first();
          return view('layouts/backoffice/tienda/sistema/prestamodesembolso/detalledesembolso', compact(
            'tienda',
            'prestamodesembolso',
            'prestamodesembolsodetalle',
            'configuracion_prestamo',
            'facturacion',
          ));
        }
        elseif ($request->input('view') == 'anular') {
          $facturacion = DB::table('s_prestamo_facturacion')
            ->leftJoin('s_moneda', 's_moneda.id', 's_prestamo_facturacion.idmoneda')
            ->leftJoin('s_tipocomprobante', 's_tipocomprobante.id', 's_prestamo_facturacion.idtipocomprobante')
            ->leftJoin('users as cliente', 'cliente.id', 's_prestamo_facturacion.idcliente')
            ->leftJoin('ubigeo as clienteubigeo', 'clienteubigeo.id', 's_prestamo_facturacion.idubigeo')
            ->leftJoin('s_agencia', 's_agencia.id', 's_prestamo_facturacion.idagencia')
            ->where('s_prestamo_facturacion.idprestamo_credito', $prestamodesembolso->id)
            ->select(
                's_prestamo_facturacion.*',
                's_moneda.nombre as monedanombre',
                's_agencia.ruc as agenciaruc',
                's_agencia.razonsocial as agenciarazonsocial',
                'clienteubigeo.nombre as clienteubigeonombre',
                'cliente.nombre as clientenombre',
                'cliente.apellidos as clienteapellidos',
                's_tipocomprobante.nombre as tipocomprobantenombre',
            )
            ->limit(1)
            ->first();
          return view('layouts/backoffice/tienda/sistema/prestamodesembolso/anular', compact(
            'tienda',
            'prestamodesembolso',
            'prestamodesembolsodetalle',
            'configuracion_prestamo',
            'facturacion',
          ));
        }
        elseif ($request->input('view') == 'ticket') {
          return view('layouts/backoffice/tienda/sistema/prestamodesembolso/ticket', compact(
            'tienda',
            'prestamodesembolso',
          ));
        }
        elseif ($request->input('view') == 'ticketpdf') {
          $configuracion_facturacion = configuracion_facturacion($idtienda);
          $facturacion = DB::table('s_prestamo_facturacion')
            ->leftJoin('users as cliente', 'cliente.id', 's_prestamo_facturacion.idcliente')
            ->leftJoin('ubigeo as clienteubigeo', 'clienteubigeo.id', 's_prestamo_facturacion.idubigeo')
            ->leftJoin('s_agencia', 's_agencia.id', 's_prestamo_facturacion.idagencia')
            ->leftJoin('ubigeo as agenciaubigeo', 'agenciaubigeo.id', 's_agencia.idubigeo')
            ->where('s_prestamo_facturacion.idprestamo_credito', $prestamodesembolso->id)
            ->select(
                's_prestamo_facturacion.*',
                's_agencia.ruc as agenciaruc',
                's_agencia.razonsocial as agenciarazonsocial',
                's_agencia.nombrecomercial as agencianombrecomercial',
                's_agencia.logo as agencialogo',
                's_agencia.direccion as agenciadireccion',
                'agenciaubigeo.nombre as agenciaubigeonombre',
                'cliente.nombre as clientenombre',
                'cliente.apellidos as clienteapellidos',
                'clienteubigeo.nombre as clienteubigeonombre',
            )
            ->limit(1)
            ->first();
          $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamodesembolso/ticketpdf', compact(
            'tienda',
            'prestamodesembolso',
            'configuracion_prestamo',
            'configuracion_facturacion',
            'facturacion',
          ));
          return $pdf->stream('Ticket.pdf');
        }
        elseif ($request->input('view') == 'documento') {
          $documentos = DB::table('s_prestamo_documento')->where('s_prestamo_documento.idtienda', $idtienda)->get();
          return view('layouts/backoffice/tienda/sistema/prestamodesembolso/documento',  compact(
            'tienda',
            'prestamodesembolso',
            'documentos',
          ));
        }
        elseif ($request->input('view') == 'documentopdf') {
          $prestamodocumento = DB::table('s_prestamo_documento')
            ->where([
              ['s_prestamo_documento.idtienda', $idtienda],
              ['s_prestamo_documento.id', $request->iddocumento]
            ])
            ->first();
            $facturacion = DB::table('s_prestamo_facturacion')
                ->leftJoin('users as cliente', 'cliente.id', 's_prestamo_facturacion.idcliente')
                ->leftJoin('ubigeo as clienteubigeo', 'clienteubigeo.id', 's_prestamo_facturacion.idubigeo')
                ->leftJoin('s_agencia', 's_agencia.id', 's_prestamo_facturacion.idagencia')
                ->leftJoin('ubigeo as agenciaubigeo', 'agenciaubigeo.id', 's_agencia.idubigeo')
                ->where('s_prestamo_facturacion.idprestamo_credito', $prestamodesembolso->id)
                ->select(
                    's_prestamo_facturacion.*',
                    's_agencia.ruc as agenciaruc',
                    's_agencia.razonsocial as agenciarazonsocial',
                    's_agencia.nombrecomercial as agencianombrecomercial',
                    's_agencia.logo as agencialogo',
                    's_agencia.direccion as agenciadireccion',
                    'agenciaubigeo.nombre as agenciaubigeonombre',
                    'cliente.nombre as clientenombre',
                    'cliente.apellidos as clienteapellidos',
                    'clienteubigeo.nombre as clienteubigeonombre',
                )
                ->limit(1)
                ->first();

          $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamodesembolso/documentopdf', compact(
            'tienda',
            'prestamodesembolso',
            'prestamodocumento',
            'facturacion'
          ));
          return $pdf->stream('Documento.pdf');
        }
        elseif ($request->input('view') == 'cronograma') {
          return view('layouts/backoffice/tienda/sistema/prestamodesembolso/cronograma', compact(
            'tienda',
            'prestamodesembolso'
          ));
        }
        elseif ($request->input('view') == 'cronogramapdf') {
            $facturacion = DB::table('s_prestamo_facturacion')
                ->leftJoin('users as cliente', 'cliente.id', 's_prestamo_facturacion.idcliente')
                ->leftJoin('ubigeo as clienteubigeo', 'clienteubigeo.id', 's_prestamo_facturacion.idubigeo')
                ->leftJoin('s_agencia', 's_agencia.id', 's_prestamo_facturacion.idagencia')
                ->leftJoin('ubigeo as agenciaubigeo', 'agenciaubigeo.id', 's_agencia.idubigeo')
                ->where('s_prestamo_facturacion.idprestamo_credito', $prestamodesembolso->id)
                ->select(
                    's_prestamo_facturacion.*',
                    's_agencia.ruc as agenciaruc',
                    's_agencia.razonsocial as agenciarazonsocial',
                    's_agencia.nombrecomercial as agencianombrecomercial',
                    's_agencia.logo as agencialogo',
                    's_agencia.direccion as agenciadireccion',
                    'agenciaubigeo.nombre as agenciaubigeonombre',
                    'cliente.nombre as clientenombre',
                    'cliente.apellidos as clienteapellidos',
                    'clienteubigeo.nombre as clienteubigeonombre',
                )
                ->limit(1)
                ->first();
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamodesembolso/cronogramapdf', [
                'prestamodesembolso' => $prestamodesembolso,
                'prestamodesembolsodetalle' => $prestamodesembolsodetalle,
                'facturacion' => $facturacion,
                'tienda' => $tienda
            ]);
            return $pdf->stream('Cronograma.pdf');
        }
        elseif ($request->input('view') == 'tarjeta') {
            return view('layouts/backoffice/tienda/sistema/prestamodesembolso/tarjeta', [
                'prestamodesembolso' => $prestamodesembolso,
                'tienda' => $tienda
            ]);
        }
        elseif ($request->input('view') == 'tarjetapdf') {
            $facturacion = DB::table('s_prestamo_facturacion')
                ->leftJoin('users as cliente', 'cliente.id', 's_prestamo_facturacion.idcliente')
                ->leftJoin('ubigeo as clienteubigeo', 'clienteubigeo.id', 's_prestamo_facturacion.idubigeo')
                ->leftJoin('s_agencia', 's_agencia.id', 's_prestamo_facturacion.idagencia')
                ->leftJoin('ubigeo as agenciaubigeo', 'agenciaubigeo.id', 's_agencia.idubigeo')
                ->where('s_prestamo_facturacion.idprestamo_credito', $prestamodesembolso->id)
                ->select(
                    's_prestamo_facturacion.*',
                    's_agencia.ruc as agenciaruc',
                    's_agencia.razonsocial as agenciarazonsocial',
                    's_agencia.nombrecomercial as agencianombrecomercial',
                    's_agencia.logo as agencialogo',
                    's_agencia.direccion as agenciadireccion',
                    'agenciaubigeo.nombre as agenciaubigeonombre',
                    'cliente.nombre as clientenombre',
                    'cliente.apellidos as clienteapellidos',
                    'clienteubigeo.nombre as clienteubigeonombre',
                )
                ->limit(1)
                ->first();
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamodesembolso/tarjetapdf', [
                'prestamodesembolso' => $prestamodesembolso,
                'prestamodesembolsodetalle' => $prestamodesembolsodetalle,
                'facturacion' => $facturacion,
                'tienda' => $tienda
            ]);
            return $pdf->stream('Cronograma.pdf');
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

        /*
         idestadocredito
         * 1 = credito pendiente
         * 2 = credito pre aprobado
         * 3 = aprobado
         * 4 = desembolsado
         idestado
         * 1 = correcto
         * 2 = anulado
        */
        if ($request->input('view') == 'desembolsar') {
            $rules = [
                'cliente_direccion' => 'required',
                'idubigeo' => 'required',
                'idagencia' => 'required',
                'idmoneda' => 'required',
                'idtipocomprobante' => 'required',
            ];
            
            if($request->input('check_gastoadministrativo')!='on'){
              $rules = array_merge($rules,[
                'facturacion_montorecibido' => 'required'
              ]);
            }
          
            $messages = [
                'cliente_direccion.required' => 'La "Dirección" es Obligatorio.',
                'idubigeo.required' => 'El "Ubigeo" es Obligatorio.',
                'idagencia.required' => 'La "Agencia" es Obligatorio.',
                'idtipocomprobante.required' => 'El "Tipo de Comprobante" es Obligatorio.',
                'facturacion_montorecibido.required' => 'El "Monto recibido" es Obligatorio.',
                'idmoneda.required' => 'La "Moneda" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);

            // aperturacaja
            $idaperturacierre = 0;
            $caja = caja($idtienda, Auth::user()->id);
            if($caja['resultado'] != 'ABIERTO'){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La Caja debe estar Aperturada.'
                ]);
            }
            $idaperturacierre = $caja['apertura']->id;
            // fin aperturacaja

          
            $cliente = DB::table('users')
              ->leftjoin('ubigeo', 'ubigeo.id', 'users.idubigeo')
              ->where('users.id', $request->idcliente)
              ->select(
                'users.*',
                'ubigeo.nombre as ubigeo_nombre',
                'ubigeo.codigo as ubigeo_codigo'
              )
              ->first();
          
            $credito = DB::table('s_prestamo_credito')
              ->whereId($id)
              ->first();

            $cronograma = prestamo_cronograma(
                $idtienda,
                $credito->monto,
                $credito->numerocuota,
                $credito->fechainicio,
                $credito->idprestamo_frecuencia,
                $credito->numerodias,
                $credito->idprestamo_tipotasa,
                $credito->tasa,
                $request->input('total_gastoadministrativo'),
                $credito->excluirferiado,
                $credito->excluirsabado,
                $credito->excluirdomingo
            );

            // Registrando Facturación del prestamo
            DB::table('s_prestamo_facturacion')->insert([
                'fecharegistro' => Carbon::now(),
                'totalapagar' => $request->input('total_gastoadministrativo'),
                'montorecibido' => $request->input('facturacion_montorecibido')!=null?$request->input('facturacion_montorecibido'):0,
                'vuelto' => $request->input('facturacion_vuelto')!=null?$request->input('facturacion_vuelto'):0,
                'cliente_identificacion' => $cliente->identificacion,
                'cliente_nombre' => $cliente->nombre,
                'cliente_apellidos' => $cliente->apellidos,
                'cliente_direccion' => $request->cliente_direccion,
                'idcliente' => $request->idcliente,
                'idcajero' => Auth::user()->id,
                'idaperturacierre' => $idaperturacierre,
                'idagencia' => $request->idagencia,
                'idmoneda' => $request->idmoneda,
                'idtipocomprobante' => $request->idtipocomprobante,
                'idubigeo' => $request->idubigeo,
                'idprestamo_credito' => $id,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            // Fin registrando Facturación del prestamo
          
            
            if($request->input('check_gastoadministrativo')=='on'){
                DB::table('s_prestamo_credito')->whereId($id)->update([
                    'idcajero' => Auth::user()->id,
                    'total_gastoadministrativo' => $request->input('total_gastoadministrativo'),
                    'idestadogastoadministrativo' => 2
                ]);
                DB::table('s_prestamo_creditodetalle')->where('idprestamo_credito',$id)->delete();
                foreach($cronograma['cronograma'] as $value) {
                  DB::table('s_prestamo_creditodetalle')->insert([
                    'numero' => $value['numero'],
                    'fechavencimiento' => $value['fechanormal'],
                    'saldocapital' => $value['saldo'],
                    'amortizacion' => $value['amortizacion'],
                    'interes' => $value['interes'],
                    'cuota' => $value['cuota'],
                    'seguro' => $value['segurodesgravamen'],
                    'gastoadministrativo' => $value['gastoadministrativo'],
                    'total' => $value['cuotafinal'],
                    'atraso' => 0,
                    'moradescuento' => 0,
                    'moraapagar' => 0,
                    'cuotapago' => 0,
                    'acuenta' => 0,
                    'cuotaapagar' => 0,
                    'idprestamo_credito' => $id,
                    'idestadocobranza' => 1,
                    'idestado' => 1
                  ]);
                }
            }else{
                DB::table('s_prestamo_credito')->whereId($id)->update([
                    'idcajero' => Auth::user()->id,
                    'idestadogastoadministrativo' => 1
                ]);
            }
          
            
            DB::table('s_prestamo_credito')->whereId($id)->update([
                'fechadesembolsado' => Carbon::now(),
                'idestadocredito' => 4
            ]);
                
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'remover') {
            DB::table('s_prestamo_credito')->whereId($id)->update([
                'fechaanulado' => Carbon::now(),
                'idestado' => 2
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'anular') {
            DB::table('s_prestamo_credito')->whereId($id)->update([
                'fechaanulado' => Carbon::now(),
                'idestado' => 2
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
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
    }
}
