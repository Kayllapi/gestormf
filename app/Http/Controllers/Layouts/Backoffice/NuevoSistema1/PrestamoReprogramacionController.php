<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;

class PrestamoReprogramacionController extends Controller
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
      $reprogramaciones = DB::table('s_prestamo_credito')
        ->join('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_credito.idprestamo_frecuencia')
        ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
        ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
        ->where([
          // ['s_prestamo_credito.idestadocredito', 2], // credito pre aprobado
          ['s_prestamo_credito.idtipocredito', 3], // credito reprogramado
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

      return view('layouts/backoffice/tienda/sistema/prestamoreprogramacion/index', compact(
        'tienda',
        'reprogramaciones'
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
      return view('layouts/backoffice/tienda/sistema/prestamoreprogramacion/create', compact(
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
      if ($request->view == 'registrar') {
        $rules = [
          'idcliente' => 'required',
        ];
        $messages = [
          'idcliente.required' => 'El "Cliente" es Obligatorio.',
        ];
        $this->validate($request, $rules, $messages);
        
        DB::table('s_prestamo_reprogramacion')->insert([
          'fecharegistro' => Carbon::now(),
          'idcliente' => $request->idcliente,
          'idtienda' => $request->idtienda,
          'idestado' => 1
        ]);
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje' => 'Se ha registrado correctamente.'
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
          ->whereIn('s_prestamo_credito.idestadocredito', [4])
          ->orWhere('cliente.nombre','LIKE', '%'.$request->buscar.'%')
          ->where('s_prestamo_credito.idestado', 1)
          ->whereIn('s_prestamo_credito.idestadocredito', [4])
          ->orWhere('cliente.apellidos','LIKE', '%'.$request->buscar.'%')
          ->where('s_prestamo_credito.idestado', 1)
          ->whereIn('s_prestamo_credito.idestadocredito', [4])
          ->select(
                  's_prestamo_credito.id as id',
                  DB::raw('IF(s_prestamo_credito.idestadocredito = 4 && s_prestamo_credito.idestado = 1, "PENDIENTE", 
                  IF(s_prestamo_credito.idestadocredito = 5, "CANCELADO", "REFINANCIADO")) as estado'),
                  'cliente.identificacion as clienteidentificacion',
                  'cliente.apellidos as clienteapellidos',
                  'cliente.nombre as clientenombre',
                  's_moneda.simbolo as monedasimbolo',
                  's_prestamo_credito.monto as creditomonto',
                  's_prestamo_credito.fechadesembolsado as creditofechadesembolsado',
          )
          ->get();
        $dataclientes = [];
        foreach($clientes as $value){
            $dataclientes[] = [
                'id' => $value->id,
                'text' => $value->estado.' <b>/</b> '.$value->clienteidentificacion.' - '.$value->clienteapellidos.', '.$value->clientenombre.' <b>/</b> '.date_format(date_create($value->creditofechadesembolsado),"d-m-Y").' <b>/</b> '.$value->monedasimbolo.' '.$value->creditomonto,
                'estado' => $value->estado,
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
      
      if($request->view == 'reprogramacion') {
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
        
          return view('layouts/backoffice/tienda/sistema/prestamoreprogramacion/reprogramacion', compact(
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
          return view('layouts/backoffice/tienda/sistema/prestamoreprogramacion/cuotapendiente', compact(
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
