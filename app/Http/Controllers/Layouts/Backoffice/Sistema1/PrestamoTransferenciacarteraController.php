<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;

class PrestamoTransferenciacarteraController extends Controller
{
    public function index(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        $where = [];
        if($request->fecharegistro!=''){ $where[] = ['s_prestamo_cartera.fecharegistro','LIKE','%'.$request->fecharegistro.'%']; }
        if($request->tipo!=''){ $where[] = ['s_prestamo_cartera.idestadotransferenciacartera',$request->tipo]; }
      
        $transferenciacarteras = DB::table('s_prestamo_cartera')
            ->join('users as cliente', 'cliente.id', 's_prestamo_cartera.iduserscliente')
            ->join('users as asesororigen', 'asesororigen.id', 's_prestamo_cartera.idasesororigen')
            ->join('users as asesordestino', 'asesordestino.id', 's_prestamo_cartera.idasesordestino')
            ->where('s_prestamo_cartera.idtienda', $idtienda)
            ->where('s_prestamo_cartera.idestado', 1)
            ->where('cliente.idestado', 1)
            ->where($where)
            ->whereRaw('CONCAT(asesororigen.apellidos,", ",asesororigen.nombre) LIKE "%'.$request->responsableregistro.'%"')
            ->whereRaw('CONCAT(asesordestino.apellidos,", ",asesordestino.nombre) LIKE "%'.$request->responsablerecepcion.'%"')
            ->whereRaw('CONCAT(cliente.apellidos,", ",cliente.nombre) LIKE "%'.$request->cliente.'%"')
            //->where('asesororigen.id',Auth::user()->id)
            ->select(
                's_prestamo_cartera.*',
                DB::raw('IF(asesororigen.idtipopersona=1,
                CONCAT(asesororigen.apellidos,", ",asesororigen.nombre),
                CONCAT(asesororigen.apellidos)) as asesororigen'),
                DB::raw('IF(asesordestino.idtipopersona=1,
                CONCAT(asesordestino.apellidos,", ",asesordestino.nombre),
                CONCAT(asesordestino.apellidos)) as asesordestino'),
                DB::raw('IF(cliente.idtipopersona=1,
                CONCAT(cliente.apellidos,", ",cliente.nombre),
                CONCAT(cliente.apellidos)) as cliente'),
            )
            ->orderBy('s_prestamo_cartera.id','desc')
            ->paginate(10);

        return view('layouts/backoffice/tienda/sistema/prestamotransferenciacartera/index', compact(
            'tienda',
            'transferenciacarteras'
        ));
    }

    public function create(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      return view('layouts/backoffice/tienda/sistema/prestamotransferenciacartera/create', compact(
        'tienda',
      ));
    }

    public function store(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      if ($request->view == 'registrar') {
        $rules = [
          'idcliente' => 'required',
          'idasesordestino' => 'required',
        ];
        $messages = [
          'idcliente.required' => 'El "Crédito del Cliente" es Obligatorio.',
          'idasesordestino.required' => 'El "Destino (Asesor)" es Obligatorio.',
        ];
        $this->validate($request, $rules, $messages);
        
        
        $usuario = DB::table('users')
            ->join('s_prestamo_cartera','s_prestamo_cartera.id','=','users.idprestamocartera')
            ->join('users as asesor','asesor.id','=','s_prestamo_cartera.idasesordestino')
            ->where('users.id',$request->idcliente)
            ->select(
              'asesor.id as idasesor',
            )
            ->first();
        
        prestamo_registrar_tranferenciacartera($idtienda,$usuario->idasesor,$request->idasesordestino,$request->idcliente,2);
        
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

      $s_prestamo_cartera = DB::table('s_prestamo_cartera')
        ->join('users as cliente', 'cliente.id', 's_prestamo_cartera.iduserscliente')
        ->join('users as asesororigen', 'asesororigen.id', 's_prestamo_cartera.idasesororigen')
        ->join('users as asesordestino', 'asesordestino.id', 's_prestamo_cartera.idasesordestino')
              ->where([
                ['s_prestamo_cartera.id', $id],
                ['s_prestamo_cartera.idtienda', $idtienda]
              ])
              ->select(
                   's_prestamo_cartera.*',
                   DB::raw('CONCAT(cliente.identificacion," - ",cliente.apellidos,", ",cliente.nombre) as cliente'),
                   DB::raw('CONCAT(asesororigen.identificacion," - ",asesororigen.apellidos,", ",asesororigen.nombre) as asesororigen'),
                   DB::raw('CONCAT(asesordestino.identificacion," - ",asesordestino.apellidos,", ",asesordestino.nombre) as asesordestino'),

              )
              ->first();
      
      if($request->view == 'detalle') {
        
          return view('layouts/backoffice/tienda/sistema/prestamotransferenciacartera/detalle', compact(
            'tienda',
            's_prestamo_cartera'
          ));
      }
    }

    public function update(Request $request, $idtienda, $id)
    {
      /*$request->user()->authorizeRoles($request->path(),$idtienda);
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

        $documento = $request->anterior_reprogramar_documento;
        

            if($request->anterior_reprogramar_documento==''){
                if($request->file('reprogramar_documento') == null) {
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El documento es obligatorio.'
                    ]);
                }
                $documento = uploadfile('','',$request->file('reprogramar_documento'),'/public/backoffice/tienda/'.$idtienda.'/prestamotransferenciacartera/');
            }else{

                if($request->file('reprogramar_documento')!=null){
                    $documento = uploadfile($request->anterior_reprogramar_documento,'',$request->file('reprogramar_documento'),'/public/backoffice/tienda/'.$idtienda.'/prestamotransferenciacartera/');
                }
            }
        DB::table('s_prestamo_transferenciacartera')->whereId($id)->update([
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
        DB::table('s_prestamo_transferenciacartera')->whereId($id)->update([
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
      }*/
    }

    public function destroy(Request $request, $idtienda, $id)
    {
      
    }
}
