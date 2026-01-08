<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

class PrestamoAprobacionController extends Controller
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
        $where1[] = ['cliente.apellidos','LIKE','%'.$request->cliente.'%'];
        if($request->frecuencia!=''){ $where1[] = ['s_prestamo_credito.idprestamo_frecuencia',$request->frecuencia]; }
      
        $prestamocreditos = DB::table('s_prestamo_credito')
              ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
              ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->join('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_credito.idprestamo_frecuencia')
              ->join('s_prestamo_tipocredito', 's_prestamo_tipocredito.id', 's_prestamo_credito.idprestamo_tipocredito')
              ->join('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
              ->where($where)
              ->where('s_prestamo_credito.idtienda', $idtienda)
              ->where('s_prestamo_credito.idestado', 1)
              ->whereIn('s_prestamo_credito.idestadocredito', [3,4])
              ->where('s_prestamo_credito.idsupervisor', Auth::user()->id)
              ->orWhere($where1)
              ->where('s_prestamo_credito.idtienda', $idtienda)
              ->where('s_prestamo_credito.idestado', 1)
              ->whereIn('s_prestamo_credito.idestadocredito', [3,4])
              ->where('s_prestamo_credito.idsupervisor', Auth::user()->id)
              ->orWhere($where)
              ->where('s_prestamo_credito.idtienda', $idtienda)
              ->where('s_prestamo_credito.idestado', 1)
              ->whereIn('s_prestamo_credito.idestadocredito', [2])
              ->orWhere($where1)
              ->where('s_prestamo_credito.idtienda', $idtienda)
              ->where('s_prestamo_credito.idestado', 1)
              ->whereIn('s_prestamo_credito.idestadocredito', [2])
              ->select(
                  's_prestamo_credito.id as id',
                  's_prestamo_credito.codigo as codigo',
                  's_prestamo_credito.monto as monto',
                  's_prestamo_credito.numerocuota as numerocuota',
                  's_prestamo_credito.fechadesembolsado as fechadesembolsado',
                  's_prestamo_credito.idestadoaprobacion as idestadoaprobacion',
                  's_prestamo_credito.idestadodesembolso as idestadodesembolso',
                  's_prestamo_credito.idestadocredito as idestadocredito',
                  's_prestamo_credito.fechaaprobado as fechaaprobado',
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
              ->orderBy('s_prestamo_credito.idestadodesembolso','asc')
              ->orderBy('s_prestamo_credito.idestadocredito','asc')
              ->orderBy('s_prestamo_credito.fechapreaprobado','desc')
              ->paginate(10);
      
        return view('layouts/backoffice/tienda/sistema/prestamoaprobacion/index',[
            'tienda' => $tienda,
            'prestamocreditos' => $prestamocreditos
        ]);
    }

    public function create(Request $request,$idtienda)
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
      $prestamocredito = DB::table('s_prestamo_credito')
          ->where('s_prestamo_credito.id', $id)
          ->first();

      if ($request->input('view') == 'aprobar') {
        return view('layouts/backoffice/tienda/sistema/prestamoaprobacion/aprobar', compact(
          'tienda',
          'prestamocredito',
        ));
      }
      elseif ($request->input('view') == 'detalle') {
        return view('layouts/backoffice/tienda/sistema/prestamoaprobacion/detalle', compact(
          'tienda',
          'prestamocredito',
        ));
      }
    }

    public function update(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(), $idtienda);

        if ($request->input('view') == 'aprobar') {
            /*$rules = [
                'observacionsupervisor_aprobar' => 'required',
            ];
            $messages = [
                'observacionsupervisor_aprobar.required' => 'El "Comentario/Observación" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);*/
          
            $prestamocredito = DB::table('s_prestamo_credito')
                ->where('s_prestamo_credito.id', $id)
                ->first();
          
            $comentariosupervisor = '';
            if($prestamocredito->comentariosupervisor!=''){
                if($request->observacionsupervisor_aprobar!=''){
                    $comentariosupervisor = $prestamocredito->comentariosupervisor.'/&/comentario/&/'.$request->observacionsupervisor_aprobar;
                }
            }else{
                $comentariosupervisor = $request->observacionsupervisor_aprobar!=''?$request->observacionsupervisor_aprobar:'';
            }
          
            DB::table('s_prestamo_credito')->whereId($id)->update([
                'fechaaprobado' => Carbon::now(),
                'comentariosupervisor' => $comentariosupervisor,
                'idsupervisor' => Auth::user()->id,
                'idestadocredito' => 3, // 1=pendiente,2=preaprobado,3=aprobado,4=desembolsado
                'idestadoaprobacion' => 1 // 1=aprobado,2=rechazado,3=denegado
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha Aprobado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'rechazar') {
            $rules = [
                'observacionsupervisor_rechazar' => 'required',
            ];
            $messages = [
                'observacionsupervisor_rechazar.required' => 'El "Comentario/Observación" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);
          
            $prestamocredito = DB::table('s_prestamo_credito')
                ->where('s_prestamo_credito.id', $id)
                ->first();
          
            $comentariosupervisor = '';
            if($prestamocredito->comentariosupervisor!=''){
                 $comentariosupervisor = $prestamocredito->comentariosupervisor.'/&/comentario/&/'.$request->observacionsupervisor_rechazar;
            }else{
                 $comentariosupervisor = $request->observacionsupervisor_rechazar;
            }
          
            DB::table('s_prestamo_credito')->whereId($id)->update([
                'fecharechazado' => Carbon::now(),
                'comentariosupervisor' => $comentariosupervisor,
                'idsupervisor' => Auth::user()->id,
                'idestadocredito' => 1,
                'idestadoaprobacion' => 2
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha Rechazado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'denegar') {
            $rules = [
                'observacionsupervisor_denegar' => 'required',
            ];
            $messages = [
                'observacionsupervisor_denegar.required' => 'El "Comentario/Observación" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);
          
            $prestamocredito = DB::table('s_prestamo_credito')
                ->where('s_prestamo_credito.id', $id)
                ->first();
          
            $comentariosupervisor = '';
            if($prestamocredito->comentariosupervisor!=''){
                 $comentariosupervisor = $prestamocredito->comentariosupervisor.'/&/comentario/&/'.$request->observacionsupervisor_denegar;
            }else{
                 $comentariosupervisor = $request->observacionsupervisor_denegar;
            }
          
            DB::table('s_prestamo_credito')->whereId($id)->update([
                'fechadenegado' => Carbon::now(),
                'comentariosupervisor' => $request->observacionsupervisor_denegar,
                'idsupervisor' => Auth::user()->id,
                'idestadoaprobacion' => 3
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha Denegado correctamente.'
            ]);
        }
    }

    public function destroy(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }
}
