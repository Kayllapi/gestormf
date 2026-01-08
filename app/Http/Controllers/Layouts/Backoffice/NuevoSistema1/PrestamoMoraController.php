<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;

class PrestamoMoraController extends Controller
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
      $moras = DB::table('s_prestamo_mora as mora')
        ->join('users as cliente', 'cliente.id', 'mora.idcliente')
        ->leftJoin('users as responsableregistro', 'responsableregistro.id', 'mora.idresponsableregistro')
        ->leftJoin('users as responsableconfirmacion', 'responsableconfirmacion.id', 'mora.idresponsableconfirmacion')
        ->join('s_prestamo_credito as credito', 'credito.id', 'mora.idprestamo_credito')
        ->where('mora.idtienda', $idtienda)
        ->select(
          'mora.*',
          'cliente.apellidos as apellidos_cliente',
          'cliente.nombre as nombre_cliente',
          'responsableregistro.nombre as nombre_responsableregistro',
          'responsableconfirmacion.nombre as nombre_responsableconfirmacion'
        )
        ->orderBy('mora.id', 'desc')
        ->paginate(10);

      return view('layouts/backoffice/tienda/sistema/prestamomora/index', compact(
        'tienda',
        'moras'
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
      return view('layouts/backoffice/tienda/sistema/prestamomora/create', compact(
        'tienda'
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
      if ($request->view == 'registrar-mora') {
        $rules = [
          'moradescuento' => 'required',
          'moradescuento_detalle' => 'required',
        ];
        $messages = [
          'moradescuento.required' => 'La "Mora Descuento" es Obligatorio.',
          'moradescuento_detalle.required' => 'El "Motivo de Descuento" es Obligatorio.',
        ];
        $this->validate($request, $rules, $messages);
        
        $prestamo_credito = DB::table('s_prestamo_credito')->whereId($request->idprestamo_credito)->first();
        
        // validaciones
        if ($request->moradescuento <= 0) {
          return response()->json([
            'resultado' => 'ERROR',
            'mensaje' => 'La Mora Descuento debe ser mayor a cero.'
          ]);
        }
        if ($request->moradescuento > $request->total_moraapagar) {
          return response()->json([
            'resultado' => 'ERROR',
            'mensaje' => "La Mora Descuento no puede ser mayor a $request->total_moraapagar"
          ]);
        }
        
        if($request->file('documento') == null) {
            return response()->json([
                'resultado' => 'ERROR',
                'mensaje'   => 'El documento es obligatorio.'
            ]);
        }
        // fin validaciones
        $documento = '';
        if ($request->file('documento')->isValid()) {
          $rutaarchivo = '/public/backoffice/tienda/'.$idtienda.'/prestamomora/';
          $documento = $request->file('documento')->getClientOriginalName();
          $request->file('documento')->move(getcwd().$rutaarchivo, $documento);
        }
        /* idestado
        * 1 = pendiente
        * 2 = confirmado
        * 3 = anulado
        */
        DB::table('s_prestamo_mora')->insert([
          'fecharegistro' => Carbon::now(),
          'monto' => $request->moradescuento,
          'motivo' => $request->moradescuento_detalle,
          'documento' => $documento,
          'idprestamo_credito' => $prestamo_credito->id,
          'idcliente' => $prestamo_credito->idcliente,
          'idresponsableregistro' => Auth::user()->id,
          'idresponsableconfirmacion' => 0,
          'idtienda' => $idtienda,
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

      if($request->view == 'mora') {
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
        
          return view('layouts/backoffice/tienda/sistema/prestamomora/mora', compact(
            's_prestamo_credito',
            'tienda',
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
        
          $morapagadas = $prestamo_morapagadas+$request->moradescuento;
          
          $ultima_cuota = DB::table('s_prestamo_creditodetalle')
                ->where('s_prestamo_creditodetalle.idprestamo_credito', $s_prestamo_credito->id)
                ->orderBy('s_prestamo_creditodetalle.numero', 'desc')
                ->first();
        
          $cronograma = prestamo_cobranza_cronograma(
              $idtienda,
              $s_prestamo_credito->id,
              $morapagadas,
              0,
              1,
              $ultima_cuota->numero
          );
          return view('layouts/backoffice/tienda/sistema/prestamomora/cuotapendiente', compact(
            's_prestamo_credito',
            'tienda',
            'cronograma',
            'request',
            'prestamo_morapagadas'
          ));
      }
      elseif ($request->view == 'editar') {
        $s_prestamo_mora = DB::table('s_prestamo_mora')
          ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_mora.idprestamo_credito')
          ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
          ->join('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
          ->where('s_prestamo_mora.id', $id)
          ->select(
              's_prestamo_mora.*',
              DB::raw('IF(s_prestamo_credito.idestadocredito = 4 && s_prestamo_credito.idestado = 1, "PENDIENTE", 
              IF(s_prestamo_credito.idestadocredito = 5, "CANCELADO", "REFINANCIADO")) as estado'),
              'cliente.identificacion as clienteidentificacion',
              'cliente.apellidos as clienteapellidos',
              'cliente.nombre as clientenombre',
              's_moneda.simbolo as monedasimbolo',
              's_prestamo_credito.monto as creditomonto',
              's_prestamo_credito.fechadesembolsado as creditofechadesembolsado'
          )
          ->first();
        return view('layouts/backoffice/tienda/sistema/prestamomora/edit', compact(
            'tienda',
            's_prestamo_mora',
          ));
      }
      elseif ($request->view == 'confirmar') {
        $s_prestamo_mora = DB::table('s_prestamo_mora')
          ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_mora.idprestamo_credito')
          ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
          ->join('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
          ->where('s_prestamo_mora.id', $id)
          ->select(
              's_prestamo_mora.*',
              DB::raw('IF(s_prestamo_credito.idestadocredito = 4 && s_prestamo_credito.idestado = 1, "PENDIENTE", 
              IF(s_prestamo_credito.idestadocredito = 5, "CANCELADO", "REFINANCIADO")) as estado'),
              'cliente.identificacion as clienteidentificacion',
              'cliente.apellidos as clienteapellidos',
              'cliente.nombre as clientenombre',
              's_moneda.simbolo as monedasimbolo',
              's_prestamo_credito.monto as creditomonto',
              's_prestamo_credito.fechadesembolsado as creditofechadesembolsado'
          )
          ->first();
        return view('layouts/backoffice/tienda/sistema/prestamomora/confirmar', compact(
            'tienda',
            's_prestamo_mora',
          ));
      }
      elseif ($request->view == 'anular') {
        $s_prestamo_mora = DB::table('s_prestamo_mora')
          ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_mora.idprestamo_credito')
          ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
          ->join('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
          ->where('s_prestamo_mora.id', $id)
          ->select(
              's_prestamo_mora.*',
              DB::raw('IF(s_prestamo_credito.idestadocredito = 4 && s_prestamo_credito.idestado = 1, "PENDIENTE", 
              IF(s_prestamo_credito.idestadocredito = 5, "CANCELADO", "REFINANCIADO")) as estado'),
              'cliente.identificacion as clienteidentificacion',
              'cliente.apellidos as clienteapellidos',
              'cliente.nombre as clientenombre',
              's_moneda.simbolo as monedasimbolo',
              's_prestamo_credito.monto as creditomonto',
              's_prestamo_credito.fechadesembolsado as creditofechadesembolsado'
          )
          ->first();
        return view('layouts/backoffice/tienda/sistema/prestamomora/anular', compact(
            'tienda',
            's_prestamo_mora',
          ));
      }
      elseif ($request->view == 'detalle') {
        $s_prestamo_mora = DB::table('s_prestamo_mora')
          ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_mora.idprestamo_credito')
          ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
          ->join('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
          ->where('s_prestamo_mora.id', $id)
          ->select(
              's_prestamo_mora.*',
              DB::raw('IF(s_prestamo_credito.idestadocredito = 4 && s_prestamo_credito.idestado = 1, "PENDIENTE", 
              IF(s_prestamo_credito.idestadocredito = 5, "CANCELADO", "REFINANCIADO")) as estado'),
              'cliente.identificacion as clienteidentificacion',
              'cliente.apellidos as clienteapellidos',
              'cliente.nombre as clientenombre',
              's_moneda.simbolo as monedasimbolo',
              's_prestamo_credito.monto as creditomonto',
              's_prestamo_credito.fechadesembolsado as creditofechadesembolsado'
          )
          ->first();
        return view('layouts/backoffice/tienda/sistema/prestamomora/detalle', compact(
            'tienda',
            's_prestamo_mora',
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
      $request->user()->authorizeRoles($request->path(),$idtienda);
      if ($request->view == 'editar-mora') {
        $rules = [
          'moradescuento' => 'required',
          'moradescuento_detalle' => 'required',
        ];
        $messages = [
          'moradescuento.required' => 'La "Mora Descuento" es Obligatorio.',
          'moradescuento_detalle.required' => 'El "Motivo de Descuento" es Obligatorio.',
        ];
        $this->validate($request, $rules, $messages);
        
        // validaciones
        if ($request->moradescuento <= 0) {
          return response()->json([
            'resultado' => 'ERROR',
            'mensaje' => 'La Mora Descuento debe ser mayor a cero.'
          ]);
        }
        if ($request->moradescuento > $request->total_moraapagar) {
          return response()->json([
            'resultado' => 'ERROR',
            'mensaje' => "La Mora Descuento no puede ser mayor a $request->total_moraapagar"
          ]);
        }
        // fin validaciones
        
        /* idestado
        * 1 = pendiente
        * 2 = confirmado
        * 3 = anulado
        */
        if($request->file('documento') != null) {
            $s_prestamo_mora = DB::table('s_prestamo_mora')->whereId($id)->first();
            $documento = $s_prestamo_mora->documento;
          
            if ($request->file('documento')->isValid()) {
              $rutaarchivo = '/public/backoffice/tienda/'.$idtienda.'/prestamomora/';
              uploadfile_eliminar($s_prestamo_mora->documento,$rutaarchivo);
              if(file_exists(getcwd().$rutaarchivo.$documento) && $documento!='') {
                  unlink(getcwd().$rutaarchivo.$documento);
              }
              $documento = $request->file('documento')->getClientOriginalName();
              $request->file('documento')->move(getcwd().$rutaarchivo, $documento);
            }
          
            DB::table('s_prestamo_mora')->whereId($id)->update([
              'monto' => $request->moradescuento,
              'motivo' => $request->moradescuento_detalle,
              'documento' => $documento,
            ]);
        }
        else {
            DB::table('s_prestamo_mora')->whereId($id)->update([
              'monto' => $request->moradescuento,
              'motivo' => $request->moradescuento_detalle,
            ]);
        }
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje' => 'Se ha actualizado correctamente.'
        ]);
      }
      elseif ($request->view == 'confirmar-mora') {
        $rules = [
          'moradescuento' => 'required',
          'moradescuento_detalle' => 'required',
          'documento' => 'required',
        ];
        $messages = [
          'moradescuento.required' => 'El "Mora a Descontar" es Obligatorio.',
          'moradescuento_detalle.required' => 'El "Motivo de Descuento" es Obligatorio.',
          'documento.required' => 'El "Documento" es Obligatorio.',
        ];
        $this->validate($request, $rules, $messages);
        
        if ($request->moradescuento > $request->total_moraapagar) {
          return response()->json([
            'resultado' => 'ERROR',
            'mensaje' => "La Mora Descuento no puede ser mayor a $request->total_moraapagar"
          ]);
        }
        
        DB::table('s_prestamo_mora')->whereId($id)->update([
          'fechaconfirmacion' => Carbon::now(),
          'idresponsableconfirmacion' => Auth::user()->id,
          'idestado' => 2
        ]);
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje' => 'Se ha confirnmado correctamente.'
        ]);
      }
      elseif ($request->view == 'anular-mora') {
        /* idestado
        * 1 = pendiente
        * 2 = confirmado
        * 3 = anulado
        */
        DB::table('s_prestamo_mora')->whereId($id)->update([
          'fechaanulacion' => Carbon::now(),
          'idestado' => 3
        ]);
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje' => 'Se ha anulado correctamente.'
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
      
    }
}
