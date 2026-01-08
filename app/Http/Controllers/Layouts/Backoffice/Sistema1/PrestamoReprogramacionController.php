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
      
      
      
      
      $reprogramaciones = DB::table('s_prestamo_reprogramacion')
        ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_reprogramacion.idprestamo_credito')
        ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
        ->where([
          //['s_prestamo_credito.idprestamo_tipocredito', 3], // credito reprogramado
          ['s_prestamo_credito.idtienda', $idtienda],
          //['s_prestamo_credito.idasesor', Auth::user()->id]
        ])
        ->select(
            's_prestamo_reprogramacion.*',
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
          'idprestamo_credito' => 'required',
          'fechainicio' => 'required',
          'reprogramar_motivo' => 'required',
        ];
        $messages = [
          'idprestamo_credito.required' => 'El "Cliente" es Obligatorio.',
          'fechainicio.required' => 'La "Fecha de Inicio" es Obligatorio.',
          'reprogramar_motivo.required' => 'El "Motivo" es Obligatorio.',
        ];
        $this->validate($request, $rules, $messages);
        
        if($request->file('reprogramar_documento') == null) {
            return response()->json([
                'resultado' => 'ERROR',
                'mensaje'   => 'La Foto de sustento.'
            ]);
        }
        
        $documento = uploadfile('','',$request->file('reprogramar_documento'),'/public/backoffice/tienda/'.$idtienda.'/prestamoreprogramacion/');
        
        DB::table('s_prestamo_reprogramacion')->insert([
          'fecharegistro' => Carbon::now(),
          'fechainicio' => $request->fechainicio,
          'motivo' => $request->reprogramar_motivo,
          'documento' => $documento,
          'idprestamo_credito' => $request->idprestamo_credito,
          'idresponsableregistro' => Auth::user()->id,
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
              //->leftJoin('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              //->leftJoin('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
              ->where([
                ['s_prestamo_credito.id', $id],
                ['s_prestamo_credito.idtienda', $idtienda]
              ])
              ->select(
                  's_prestamo_credito.*',
                  /*'cliente.id as idcliente',
                  'cliente.identificacion as cliente_identificacion',
                  'cliente.nombre as cliente_nombre',
                  'cliente.apellidos as cliente_apellidos',
                  'cliente.direccion as cliente_direccion',
                  's_moneda.simbolo as monedasimbolo',
                  DB::raw('IF(s_prestamo_credito.idestadocredito = 4, "PENDIENTE", 
                                IF(s_prestamo_credito.idestadocredito = 5, "CANCELADO",
                                    IF(s_prestamo_credito.idestadocredito = 6, "REFINANCIADO", 
                                        IF(s_prestamo_credito.idestadocredito = 7, "REPROGRAMADO", "")))) as estado'),
                  DB::raw('IF(cliente.idtipopersona=1 || cliente.idtipopersona = 3,
                      CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos,", ",cliente.nombre),
                      CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos)) as cliente_nombre'),*/

              )
              ->first();
        
          // pestaña crédito
          $frecuencias = DB::table('s_prestamo_frecuencia')->get();
        
          return view('layouts/backoffice/tienda/sistema/prestamoreprogramacion/reprogramacion', compact(
            's_prestamo_credito',
            'tienda',
            'frecuencias'
          ));
      }
      elseif($request->view == 'editar') {
        
          $s_prestamo_credito = DB::table('s_prestamo_credito')
              ->join('s_prestamo_reprogramacion', 's_prestamo_reprogramacion.idprestamo_credito', 's_prestamo_credito.id')
              ->leftJoin('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->leftJoin('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
              ->where([
                ['s_prestamo_reprogramacion.id', $id],
                ['s_prestamo_reprogramacion.idtienda', $idtienda]
              ])
              ->select(
                  's_prestamo_credito.*',
                  'cliente.id as idcliente',
                  'cliente.identificacion as cliente_identificacion',
                  'cliente.nombre as cliente_nombre',
                  'cliente.apellidos as cliente_apellidos',
                  'cliente.direccion as cliente_direccion',
                  's_moneda.simbolo as monedasimbolo',
                  's_prestamo_reprogramacion.id as idprestamo_reprogramacion',
                  's_prestamo_reprogramacion.fechainicio as fechainicio',
                  's_prestamo_reprogramacion.motivo as motivo',
                  's_prestamo_reprogramacion.documento as documento',
                    DB::raw('IF(s_prestamo_credito.idestadocredito = 4, "PENDIENTE", 
                                IF(s_prestamo_credito.idestadocredito = 5, "CANCELADO", "NINGUNO")) as estado'),
                    DB::raw('IF(s_prestamo_credito.idprestamo_tipocredito = 1, "NORMAL", 
                                IF(s_prestamo_credito.idprestamo_tipocredito = 2, "REFINANCIADO",
                                    IF(s_prestamo_credito.idprestamo_tipocredito = 3, "REPROGRAMADO", "NINGUNO"))) as tipocredito'),
                  DB::raw('IF(cliente.idtipopersona=1 || cliente.idtipopersona = 3,
                      CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos,", ",cliente.nombre),
                      CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos)) as cliente_nombre'),

              )
              ->first();
     
          // pestaña crédito
          $frecuencias = DB::table('s_prestamo_frecuencia')->get();
        
          return view('layouts/backoffice/tienda/sistema/prestamoreprogramacion/edit', compact(
            's_prestamo_credito',
            'tienda',
            'frecuencias'
          ));
      }
      elseif($request->view == 'confirmar') {
        
          $s_prestamo_credito = DB::table('s_prestamo_credito')
              ->join('s_prestamo_reprogramacion', 's_prestamo_reprogramacion.idprestamo_credito', 's_prestamo_credito.id')
              ->leftJoin('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->leftJoin('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
              ->where([
                ['s_prestamo_reprogramacion.id', $id],
                ['s_prestamo_reprogramacion.idtienda', $idtienda]
              ])
              ->select(
                  's_prestamo_credito.*',
                  'cliente.id as idcliente',
                  'cliente.identificacion as cliente_identificacion',
                  'cliente.nombre as cliente_nombre',
                  'cliente.apellidos as cliente_apellidos',
                  'cliente.direccion as cliente_direccion',
                  's_moneda.simbolo as monedasimbolo',
                  's_prestamo_reprogramacion.id as idprestamo_reprogramacion',
                  's_prestamo_reprogramacion.fechainicio as fechainicio',
                  's_prestamo_reprogramacion.motivo as motivo',
                  's_prestamo_reprogramacion.documento as documento',
                    DB::raw('IF(s_prestamo_credito.idestadocredito = 4, "PENDIENTE", 
                                IF(s_prestamo_credito.idestadocredito = 5, "CANCELADO", "NINGUNO")) as estado'),
                    DB::raw('IF(s_prestamo_credito.idprestamo_tipocredito = 1, "NORMAL", 
                                IF(s_prestamo_credito.idprestamo_tipocredito = 2, "REFINANCIADO",
                                    IF(s_prestamo_credito.idprestamo_tipocredito = 3, "REPROGRAMADO", "NINGUNO"))) as tipocredito'),
                  DB::raw('IF(cliente.idtipopersona=1 || cliente.idtipopersona = 3,
                      CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos,", ",cliente.nombre),
                      CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos)) as cliente_nombre'),

              )
              ->first();
     
          // pestaña crédito
          $frecuencias = DB::table('s_prestamo_frecuencia')->get();
        
          return view('layouts/backoffice/tienda/sistema/prestamoreprogramacion/confirmar', compact(
            's_prestamo_credito',
            'tienda',
            'frecuencias'
          ));
      }
      elseif($request->view == 'detalle') {
        
          $s_prestamo_credito = DB::table('s_prestamo_credito')
              ->join('s_prestamo_reprogramacion', 's_prestamo_reprogramacion.idprestamo_credito', 's_prestamo_credito.id')
              ->leftJoin('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->leftJoin('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
              ->where([
                ['s_prestamo_reprogramacion.id', $id],
                ['s_prestamo_reprogramacion.idtienda', $idtienda]
              ])
              ->select(
                  's_prestamo_credito.*',
                  'cliente.id as idcliente',
                  'cliente.identificacion as cliente_identificacion',
                  'cliente.nombre as cliente_nombre',
                  'cliente.apellidos as cliente_apellidos',
                  'cliente.direccion as cliente_direccion',
                  's_moneda.simbolo as monedasimbolo',
                  's_prestamo_reprogramacion.id as idprestamo_reprogramacion',
                  's_prestamo_reprogramacion.fechainicio as fechainicio',
                  's_prestamo_reprogramacion.motivo as motivo',
                  's_prestamo_reprogramacion.documento as documento',
                    DB::raw('IF(s_prestamo_credito.idestadocredito = 4, "PENDIENTE", 
                                IF(s_prestamo_credito.idestadocredito = 5, "CANCELADO", "NINGUNO")) as estado'),
                    DB::raw('IF(s_prestamo_credito.idprestamo_tipocredito = 1, "NORMAL", 
                                IF(s_prestamo_credito.idprestamo_tipocredito = 2, "REFINANCIADO",
                                    IF(s_prestamo_credito.idprestamo_tipocredito = 3, "REPROGRAMADO", "NINGUNO"))) as tipocredito'),
                  DB::raw('IF(cliente.idtipopersona=1 || cliente.idtipopersona = 3,
                      CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos,", ",cliente.nombre),
                      CONCAT(IF(cliente.identificacion="","",CONCAT(cliente.identificacion, " - ")),cliente.apellidos)) as cliente_nombre'),

              )
              ->first();
     
          // pestaña crédito
          $frecuencias = DB::table('s_prestamo_frecuencia')->get();
        
          return view('layouts/backoffice/tienda/sistema/prestamoreprogramacion/detalle', compact(
            's_prestamo_credito',
            'tienda',
            'frecuencias'
          ));
      }
      elseif ($request->view == 'credito_reprogramado') {
          
          $s_prestamo_credito = DB::table('s_prestamo_credito')
              ->where([
                ['s_prestamo_credito.id', $id],
                ['s_prestamo_credito.idtienda', $idtienda]
              ])
              ->first();
        
          $cronograma = prestamo_cobranza_cronograma($idtienda,$s_prestamo_credito->id,0,0,1,0,$request->fechainicio);
          return view('layouts/backoffice/tienda/sistema/prestamoreprogramacion/credito_reprogramado', compact(
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
      $request->user()->authorizeRoles($request->path(),$idtienda);
      if ($request->view == 'editar') {
        $rules = [
          'idprestamo_credito' => 'required',
          'fechainicio' => 'required',
          'reprogramar_motivo' => 'required',
        ];
        $messages = [
          'idprestamo_credito.required' => 'El "Cliente" es Obligatorio.',
          'fechainicio.required' => 'La "Fecha de Inicio" es Obligatorio.',
          'reprogramar_motivo.required' => 'El "Motivo" es Obligatorio.',
        ];
        $this->validate($request, $rules, $messages);

        $creditoreprogramacion = DB::table('s_prestamo_reprogramacion')->whereId($id)->first();
        $documento = uploadfile($creditoreprogramacion->documento, $request->reprogramar_documento_anterior, $request->file('reprogramar_documento'), '/public/backoffice/tienda/'.$idtienda.'/prestamoreprogramacion/');

        DB::table('s_prestamo_reprogramacion')->whereId($id)->update([
          'fecharegistro' => Carbon::now(),
          'fechainicio' => $request->fechainicio,
          'motivo' => $request->reprogramar_motivo,
          'documento' => $documento,
          'idprestamo_credito' => $request->idprestamo_credito,
          'idresponsableregistro' => Auth::user()->id,
          'idtienda' => $idtienda,
          'idestado' => 1
        ]);
        
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje' => 'Se ha registrado correctamente.'
        ]);
      }
      elseif ($request->view == 'confirmar') {
        $cronograma = prestamo_cobranza_cronograma($idtienda,$request->idprestamo_credito,0,0,1,0,$request->fechainicio);
        DB::table('s_prestamo_reprogramacion')->whereId($id)->update([
          'fechaconfirmado' => Carbon::now(),
          'idestado' => 2
        ]);
        
        DB::table('s_prestamo_credito')->whereId($request->idprestamo_credito)->update([
          'fechareprogramado' => Carbon::now(),
          'idprestamo_tipocredito' => 3 // credito reprogramado
        ]);
        
        foreach($cronograma['cuotas_pendientes'] as $value) {
              DB::table('s_prestamo_creditodetalle')->whereId($value['idprestamo_creditodetalle'])->update([
                'fechavencimiento' => $value['tabla_fvencimiento'],
              ]);
        }
        
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje' => 'Se ha confirmado correctamente.'
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
