<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;

class PrestamoMorasolicitudController extends Controller
{
    public function index(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
      /*$moras = DB::table('s_prestamo_creditodetalle')
        ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_creditodetalle.idprestamo_credito')
        ->where('s_prestamo_creditodetalle.moradescuento','>', 0)
        ->where('s_prestamo_credito.idtienda',172)
        ->select(
          's_prestamo_creditodetalle.*',
          's_prestamo_credito.id as idcredito',
          's_prestamo_credito.idtienda as idtienda',
          's_prestamo_credito.fecharegistro as fecharegistro',
          's_prestamo_credito.idcliente as idcliente',
          's_prestamo_credito.idasesor as idasesor',
        )
        ->get();

      foreach($moras as $value){
        prestamo_registrar_mora($value->idtienda,$value->idcredito,$value->idasesor,$value->moradescuento,'');
      }
      
      dd(count($moras));*/
      
        $where = [];
        if($request->codigo!=''){ $where[] = ['s_prestamo_mora.codigo',$request->codigo]; }
        if($request->codigocredito!=''){ $where[] = ['credito.codigo',$request->codigocredito]; }
        $where[] = ['cliente.identificacion','LIKE','%'.$request->identificacion.'%'];
        $where[] = ['cliente.nombre','LIKE','%'.$request->cliente.'%'];
      
        $where1 = [];
        if($request->codigo!=''){ $where1[] = ['s_prestamo_mora.codigo',$request->codigo]; }
        if($request->codigocredito!=''){ $where1[] = ['credito.codigo',$request->codigocredito]; }
        $where1[] = ['cliente.identificacion','LIKE','%'.$request->identificacion.'%'];
        $where1[] = ['cliente.apellidos','LIKE','%'.$request->cliente.'%'];
      
      $moras = DB::table('s_prestamo_mora')
        ->join('users as cliente', 'cliente.id', 's_prestamo_mora.idcliente')
        ->join('s_prestamo_cartera','s_prestamo_cartera.id','=','cliente.idprestamocartera')
        ->join('s_prestamo_credito as credito', 'credito.id', 's_prestamo_mora.idprestamo_credito')
        ->where($where)
        ->where('s_prestamo_mora.idtienda', $idtienda)
        ->where('s_prestamo_mora.idestado', 1)
        ->where('s_prestamo_cartera.idasesordestino', Auth::user()->id)
        ->orWhere($where1)
        ->where('s_prestamo_mora.idtienda', $idtienda)
        ->where('s_prestamo_mora.idestado', 1)
        ->where('s_prestamo_cartera.idasesordestino', Auth::user()->id)
        ->select(
          's_prestamo_mora.*',
          'credito.codigo as creditocodigo',
          'credito.idestadocobranza as idestadocobranza',
          'cliente.identificacion as identificacion_cliente',
          'cliente.apellidos as apellidos_cliente',
          'cliente.nombre as nombre_cliente',
        )
        ->orderBy('credito.idestadocobranza','asc')
        ->orderBy('s_prestamo_mora.fecharegistro','desc')
        ->paginate(10);

      return view('layouts/backoffice/tienda/sistema/prestamomorasolicitud/index',[
        'tienda' => $tienda,
        'moras' => $moras
      ]);
    }
    public function create(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      if($request->view == 'create') {
          return view('layouts/backoffice/tienda/sistema/prestamomorasolicitud/create', compact(
            'tienda'
          ));
      }elseif($request->view == 'mora') {
          $s_prestamo_credito = DB::table('s_prestamo_credito')
              ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
              ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_credito.idcajero')
              ->leftJoin('ubigeo','ubigeo.id','cliente.idubigeo')
              ->where('s_prestamo_credito.id', $request->idcredito)
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
        
          return view('layouts/backoffice/tienda/sistema/prestamomorasolicitud/mora', [
            'tienda' => $tienda,
            's_prestamo_credito' => $s_prestamo_credito,
          ]);
      }
      elseif ($request->view == 'cuotapendiente') {
          $cronograma = prestamo_cobranza_cronograma($idtienda,$request->idcredito,$request->moradescuento,0,1,$request->hastacuota);
          return view('layouts/backoffice/tienda/sistema/prestamomorasolicitud/cuotapendiente', compact(
            'tienda',
            'cronograma',
          ));
      }
    }
    public function store(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      if ($request->view == 'registrar') {
        $rules = [
          'moradescuento' => 'required',
          'moradescuento_detalle' => 'required',
        ];
        $messages = [
          'moradescuento.required' => 'El "Descuento a Solicitar" es Obligatorio.',
          'moradescuento_detalle.required' => 'El "Motivo de Descuento" es Obligatorio.',
        ];
        $this->validate($request, $rules, $messages);
        
        $cronograma = prestamo_cobranza_cronograma($idtienda,$request->idprestamo_credito,$request->moradescuento,0,1,$request->hastacuota);
        
        // validaciones
        if ($request->moradescuento <= 0) {
            return response()->json([
              'resultado' => 'ERROR',
              'mensaje' => 'El "Descuento a Solicitar" debe ser mayor a cero.'
            ]);
        }
        if ($request->moradescuento > ($cronograma['select_mora']-$cronograma['morarestante'])) {
            return response()->json([
              'resultado' => 'ERROR',
              'mensaje' => 'El "Descuento a Solicitar" debe ser menor o igual a "Mora a Pagar"'
            ]);
        }
        
        /*if($request->file('imagendocumento') == null) {
            return response()->json([
                'resultado' => 'ERROR',
                'mensaje'   => 'El documento es obligatorio.'
            ]);
        }*/
        
        prestamo_registrar_mora($idtienda,$request->idprestamo_credito,Auth::user()->id,$request->moradescuento,$request->moradescuento_detalle,1);
        
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje' => 'Se ha registrado correctamente.'
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
      $s_prestamo_mora = DB::table('s_prestamo_mora')
          ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_mora.idprestamo_credito')
          ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
          ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
          ->join('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
          ->where('s_prestamo_mora.id', $id)
          ->select(
              's_prestamo_mora.*',
              DB::raw('IF(s_prestamo_credito.idestadocredito = 4 && s_prestamo_credito.idestado = 1, "PENDIENTE", 
              IF(s_prestamo_credito.idestadocredito = 5, "CANCELADO", "REFINANCIADO")) as estado'),
              'cliente.identificacion as clienteidentificacion',
              'cliente.apellidos as clienteapellidos',
              'cliente.nombre as clientenombre',
              'asesor.identificacion as asesoridentificacion',
              'asesor.apellidos as asesorapellidos',
              'asesor.nombre as asesornombre',
              's_moneda.simbolo as monedasimbolo',
              's_prestamo_credito.monto as creditomonto',
              's_prestamo_credito.codigo as creditocodigo',
              's_prestamo_credito.fechadesembolsado as creditofechadesembolsado',
              's_prestamo_credito.numerocuota as creditonumerocuota',
              's_prestamo_credito.idestadocobranza as idestadocobranza',
          )
          ->first();
      
      if ($request->view == 'confirmar') {
          $s_prestamo_moradetalles = DB::table('s_prestamo_moradetalle')
              ->where('s_prestamo_moradetalle.idtienda', $idtienda)
              ->where('s_prestamo_moradetalle.idestado', 1)
              ->where('s_prestamo_moradetalle.idprestamo_mora', $s_prestamo_mora->id)
              ->select(
                's_prestamo_moradetalle.*',
              )
              ->orderBy('s_prestamo_moradetalle.id', 'desc')
              ->get();
          return view('layouts/backoffice/tienda/sistema/prestamomorasolicitud/confirmar', [
              'tienda' => $tienda,
              's_prestamo_mora' => $s_prestamo_mora,
              's_prestamo_moradetalles' => $s_prestamo_moradetalles,
          ]);
      }
      elseif ($request->view == 'anular') {
          $s_prestamo_moradetalles = DB::table('s_prestamo_moradetalle')
              ->where('s_prestamo_moradetalle.idtienda', $idtienda)
              ->where('s_prestamo_moradetalle.idestado', 1)
              ->where('s_prestamo_moradetalle.idprestamo_mora', $s_prestamo_mora->id)
              ->select(
                's_prestamo_moradetalle.*',
              )
              ->orderBy('s_prestamo_moradetalle.id', 'desc')
              ->get();
          return view('layouts/backoffice/tienda/sistema/prestamomorasolicitud/anular', [
              'tienda' => $tienda,
              's_prestamo_mora' => $s_prestamo_mora,
              's_prestamo_moradetalles' => $s_prestamo_moradetalles,
          ]);
      }
      elseif ($request->view == 'detalle') {
          $s_prestamo_moradetalles = DB::table('s_prestamo_moradetalle')
              ->where('s_prestamo_moradetalle.idtienda', $idtienda)
              ->where('s_prestamo_moradetalle.idestado', 1)
              ->where('s_prestamo_moradetalle.idprestamo_mora', $s_prestamo_mora->id)
              ->select(
                's_prestamo_moradetalle.*',
              )
              ->orderBy('s_prestamo_moradetalle.id', 'desc')
              ->get();
          return view('layouts/backoffice/tienda/sistema/prestamomorasolicitud/detalle', [
              'tienda' => $tienda,
              's_prestamo_mora' => $s_prestamo_mora,
              's_prestamo_moradetalles' => $s_prestamo_moradetalles,
          ]);
      }
    }
    public function update(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if ($request->view == 'confirmar') {
          
          $moras = explode('/&/', $request->input('moras'));
          for($i = 1;$i <  count($moras);$i++){
                $item = explode('/,/', $moras[$i]);
                if($item[1]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Motivo es Obligatorio.'
                    ]);
                    break;
                }                     
          } 
          
          for($i = 1;$i <  count($moras);$i++){
                $item = explode('/,/', $moras[$i]);
            
                $s_prestamo_moradetalle = DB::table('s_prestamo_moradetalle')->whereId($item[0])->first();
                DB::table('s_prestamo_moradetalle')->whereId($item[0])->update([
                      'fechaconfirmado' => Carbon::now(),
                      'motivo' => $item[1],
                ]);                
          } 
          
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje' => 'Se ha confirmado correctamente.'
          ]);
        }
    }
    public function destroy(Request $request, $idtienda, $id)
    {
        
    }
}
