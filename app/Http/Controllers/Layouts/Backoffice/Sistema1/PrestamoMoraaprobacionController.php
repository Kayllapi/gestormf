<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;

class PrestamoMoraaprobacionController extends Controller
{
    public function index(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
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
        ->join('s_prestamo_credito as credito', 'credito.id', 's_prestamo_mora.idprestamo_credito')
        ->where($where)
        ->where('s_prestamo_mora.idtienda', $idtienda)
        ->where('s_prestamo_mora.idestado', 1)
        ->orWhere($where1)
        ->where('s_prestamo_mora.idtienda', $idtienda)
        ->where('s_prestamo_mora.idestado', 1)
        ->select(
          's_prestamo_mora.*',
          'credito.codigo as creditocodigo',
          'credito.idestadocobranza as idestadocobranza',
          'cliente.identificacion as identificacion_cliente',
          'cliente.apellidos as apellidos_cliente',
          'cliente.nombre as nombre_cliente',
        )
        ->orderBy('credito.idestadocobranza','asc')
        ->orderBy('s_prestamo_mora.fechasolicitud', 'desc')
        ->paginate(10);

      return view('layouts/backoffice/tienda/sistema/prestamomoraaprobacion/index',[
        'tienda' => $tienda,
        'moras' => $moras
      ]);
    }

    public function create(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    public function store(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
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
      
      if ($request->view == 'aprobar') {
          $s_prestamo_moradetalles = DB::table('s_prestamo_moradetalle')
              ->where('s_prestamo_moradetalle.idtienda', $idtienda)
              ->where('s_prestamo_moradetalle.idestado', 1)
              ->where('s_prestamo_moradetalle.idprestamo_mora', $s_prestamo_mora->id)
              ->select(
                's_prestamo_moradetalle.*',
              )
              ->orderBy('s_prestamo_moradetalle.id', 'desc')
              ->get();
          return view('layouts/backoffice/tienda/sistema/prestamomoraaprobacion/aprobar', [
              'tienda' => $tienda,
              's_prestamo_mora' => $s_prestamo_mora,
              's_prestamo_moradetalles' => $s_prestamo_moradetalles,
          ]);
      }
      elseif ($request->view == 'rechazar') {
          return view('layouts/backoffice/tienda/sistema/prestamomoraaprobacion/rechazar', [
              'tienda' => $tienda,
              's_prestamo_mora' => $s_prestamo_mora,
          ]);
      }
      elseif ($request->view == 'detallesolicitud') {
          $s_prestamo_moradetalles = DB::table('s_prestamo_moradetalle')
              ->where('s_prestamo_moradetalle.idtienda', $idtienda)
              ->where('s_prestamo_moradetalle.idestado', 1)
              ->where('s_prestamo_moradetalle.idprestamo_mora', $s_prestamo_mora->id)
              ->select(
                's_prestamo_moradetalle.*',
              )
              ->orderBy('s_prestamo_moradetalle.id', 'desc')
              ->get();
          return view('layouts/backoffice/tienda/sistema/prestamomoraaprobacion/detallesolicitud', [
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
          return view('layouts/backoffice/tienda/sistema/prestamomoraaprobacion/detalle', [
              'tienda' => $tienda,
              's_prestamo_mora' => $s_prestamo_mora,
              's_prestamo_moradetalles' => $s_prestamo_moradetalles,
          ]);
      }
    }

    public function update(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if ($request->view == 'aprobar') {
          
          $s_prestamo_mora = DB::table('s_prestamo_mora')
              ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_mora.idprestamo_credito')
              ->where('s_prestamo_mora.id', $id)
              ->select(
                  's_prestamo_mora.*',
                  's_prestamo_credito.idestadocobranza as idestadocobranza',
              )
              ->first();
          
          if($s_prestamo_mora->idestadocobranza==2){
              return response()->json([
                'resultado' => 'ERROR',
                'mensaje' => 'El Crédito ya esta CANCELADA, no puede realizar el descuento.'
              ]);
          }
          
          $total_solicitado = 0;
          $moras = explode('/&/', $request->input('moras'));
          for($i = 1;$i <  count($moras);$i++){
                $item = explode('/,/', $moras[$i]);
                if($item[1]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Motivo es Obligatorio (Solicite que el asesor actualice el motivo).'
                    ]);
                    break;
                }      
                $total_solicitado = $total_solicitado+$item[4];              
          } 
          
          if($request->totaladescontar<0){
              return response()->json([
                'resultado' => 'ERROR',
                'mensaje' => 'El "Total Aprobado" debe ser mayor o igual 0.00.'
              ]);
          }
          if(number_format($request->totaladescontar, 2, '.', '')>number_format($total_solicitado, 2, '.', '')){
              return response()->json([
                'resultado' => 'ERROR',
                'mensaje' => 'El "Total Aprobado" debe ser menor o igual a "Total Solicitado".'
              ]);
          }
          
          for($i = 1;$i <  count($moras);$i++){
                $item = explode('/,/', $moras[$i]);
            
                $s_prestamo_moradetalle = DB::table('s_prestamo_moradetalle')->whereId($item[0])->first();
                DB::table('s_prestamo_moradetalle')->whereId($item[0])->update([
                      'fechaaprobado' => Carbon::now(),
                      'moradescontar' => $item[2],
                      'moradescuento' => $item[3],
                ]);              
          } 

          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje' => 'Se ha aprobado correctamente.'
          ]);
        }
        /*elseif ($request->view == 'rechazar') {
            DB::table('s_prestamo_mora')->whereId($id)->update([
                'fecharechazado' => Carbon::now(),
                'idsupervisor' => Auth::user()->id,
                'idestadoaprobacion' => 2,
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha rechazado correctamente.'
            ]);
        }*/
    }

    public function destroy(Request $request, $idtienda, $id)
    {
        
    }
}
