<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Prestamo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

class AhorroAprobacionController extends Controller
{
    public function index(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $where = [];
        if($request->tipoahorro!=''){ $where[] = ['s_prestamo_tipoahorro.id',$request->tipoahorro]; }
        $where[] = ['s_prestamo_ahorro.codigo','LIKE','%'.$request->codigocredito.'%'];
        $where[] = ['cliente.identificacion','LIKE','%'.$request->identificacion.'%'];
        $where[] = ['cliente.nombre','LIKE','%'.$request->cliente.'%'];
      
        $where1 = [];
        if($request->tipoahorro!=''){ $where1[] = ['s_prestamo_tipoahorro.id',$request->tipoahorro]; }
        $where1[] = ['s_prestamo_ahorro.codigo','LIKE','%'.$request->codigocredito.'%'];
        $where1[] = ['cliente.identificacion','LIKE','%'.$request->identificacion.'%'];
        $where1[] = ['cliente.nombre','LIKE','%'.$request->cliente.'%'];
      
        $prestamoahorros = DB::table('s_prestamo_ahorro')
              ->join('users as asesor', 'asesor.id', 's_prestamo_ahorro.idasesor')
              ->join('users as cliente', 'cliente.id', 's_prestamo_ahorro.idcliente')
              ->join('s_prestamo_tipoahorro', 's_prestamo_tipoahorro.id', 's_prestamo_ahorro.idprestamo_tipoahorro')
              ->join('s_moneda', 's_moneda.id', 's_prestamo_ahorro.idmoneda')
              ->where($where)
              ->where('s_prestamo_ahorro.idtienda', $idtienda)
              ->where('s_prestamo_ahorro.idestado', 1)
              ->whereIn('s_prestamo_ahorro.idestadoahorro', [3,4])
              ->where('s_prestamo_ahorro.idsupervisor', Auth::user()->id)
              ->orWhere($where1)
              ->where('s_prestamo_ahorro.idtienda', $idtienda)
              ->where('s_prestamo_ahorro.idestado', 1)
              ->whereIn('s_prestamo_ahorro.idestadoahorro', [3,4])
              ->where('s_prestamo_ahorro.idsupervisor', Auth::user()->id)
              ->orWhere($where)
              ->where('s_prestamo_ahorro.idtienda', $idtienda)
              ->where('s_prestamo_ahorro.idestado', 1)
              ->whereIn('s_prestamo_ahorro.idestadoahorro', [2])
              ->orWhere($where1)
              ->where('s_prestamo_ahorro.idtienda', $idtienda)
              ->where('s_prestamo_ahorro.idestado', 1)
              ->whereIn('s_prestamo_ahorro.idestadoahorro', [2])
              ->select(
                  's_prestamo_ahorro.*',
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  's_prestamo_tipoahorro.nombre as tipocreditonombre',
                  'cliente.identificacion as clienteidentificacion',
                  's_moneda.simbolo as monedasimbolo',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente'),
              )
              ->orderBy('s_prestamo_ahorro.id','desc')
              ->paginate(10);
      
        return view('layouts/backoffice/tienda/sistema/prestamo/ahorroaprobacion/index', [
            'tienda' => $tienda,
            'prestamoahorros' => $prestamoahorros,
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
      $prestamoahorro = DB::table('s_prestamo_ahorro')
            ->leftjoin('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_ahorro.idprestamo_frecuencia')
            ->join('s_moneda', 's_moneda.id', 's_prestamo_ahorro.idmoneda')
            ->join('users as asesor', 'asesor.id', 's_prestamo_ahorro.idasesor')
            ->join('users as cliente', 'cliente.id', 's_prestamo_ahorro.idcliente')
            ->leftjoin('users as beneficiario', 'beneficiario.id', 's_prestamo_ahorro.idbeneficiario')
            ->leftjoin('ubigeo as clienteubigeo', 'clienteubigeo.id', 'cliente.idubigeo')
            ->leftjoin('ubigeo as beneficiarioubigeo', 'beneficiarioubigeo.id', 'beneficiario.idubigeo')
            ->join('tienda', 'tienda.id', 's_prestamo_ahorro.idtienda')
            ->leftjoin('users as conyuge', 'conyuge.id', 's_prestamo_ahorro.idconyuge')
            ->leftJoin('s_prestamo_tipoahorro', 's_prestamo_tipoahorro.id', 's_prestamo_ahorro.idprestamo_tipoahorro')
            ->where([
              ['s_prestamo_ahorro.id', $id],
              ['s_prestamo_ahorro.idtienda', $idtienda]
            ])
            ->select(
              's_prestamo_ahorro.*',
              's_prestamo_frecuencia.nombre as frecuencia_nombre',
              's_prestamo_frecuencia.id as idprestamo_frecuencia',
              'tienda.nombre as tiendanombre',
              'cliente.identificacion as clienteidentificacion',
              'cliente.nombre as clientenombre',
              'cliente.apellidos as clienteapellidos',
              'cliente.direccion as clientedireccion',
              'cliente.referencia as clientereferencia',
              'clienteubigeo.id as clienteidubigeo',
              'clienteubigeo.nombre as clienteubigeonombre',
              DB::raw('CONCAT(clienteubigeo.distrito, ", ", clienteubigeo.provincia, ", ", clienteubigeo.departamento) as clienteubigeoubicacion'),
              'conyuge.identificacion as conyugeidentificacion',
              'conyuge.nombre as conyugenombre',
              'conyuge.apellidos as conyugeapellidos',
              'beneficiario.identificacion as beneficiarioidentificacion',
              'beneficiario.nombre as beneficiarionombre',
              'beneficiario.apellidos as beneficiarioapellidos',
              'beneficiario.direccion as beneficiariodireccion',
              'beneficiario.referencia as beneficiarioreferencia',
              'beneficiarioubigeo.nombre as beneficiarioubigeonombre',
              'asesor.identificacion as asesoridentificacion',
              'asesor.nombre as asesornombre',
              'asesor.apellidos as asesorapellidos',
              's_moneda.simbolo as monedasimbolo',
              's_prestamo_tipoahorro.nombre as tipoahorronombre',
              DB::raw('IF(asesor.idtipopersona = 1 || asesor.idtipopersona = 3,
                  CONCAT(asesor.identificacion, " - ", asesor.apellidos, ", ", asesor.nombre),
                  CONCAT(asesor.identificacion, " - ", asesor.apellidos)) as asesor_nombre'),
              DB::raw('IF(cliente.idtipopersona = 1 || cliente.idtipopersona = 3,
                  CONCAT(cliente.identificacion, " - ", cliente.apellidos, ", ", cliente.nombre),
                  CONCAT(cliente.identificacion, " - ", cliente.apellidos)) as cliente_nombre'),
              DB::raw('IF(conyuge.idtipopersona = 1 || conyuge.idtipopersona = 3,
                  CONCAT(conyuge.identificacion, " - ", conyuge.apellidos, ", ", conyuge.nombre),
                  CONCAT(conyuge.identificacion, " - ", conyuge.apellidos)) as conyuge_nombre'),
              DB::raw('IF(beneficiario.idtipopersona = 1 || beneficiario.idtipopersona = 3,
                  CONCAT(beneficiario.identificacion, " - ", beneficiario.apellidos, ", ", beneficiario.nombre),
                  CONCAT(beneficiario.identificacion, " - ", beneficiario.apellidos)) as beneficiario_nombre')
            )
            ->first();

      if ($request->input('view') == 'aprobar') {
        return view('layouts/backoffice/tienda/sistema/prestamo/ahorroaprobacion/aprobar', compact(
          'tienda',
          'prestamoahorro',
        ));
      }
      elseif ($request->input('view') == 'detalle') {
        return view('layouts/backoffice/tienda/sistema/prestamo/ahorroaprobacion/detalle', compact(
          'tienda',
          'prestamoahorro',
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
          
            DB::table('s_prestamo_ahorro')->whereId($id)->update([
                'fechaaprobado' => Carbon::now(),
                'comentariosupervisor' => $request->observacionsupervisor_aprobar!=''?$request->observacionsupervisor_aprobar:'',
                'idsupervisor' => Auth::user()->id,
                'idestadoahorro' => 3, // 1=pendiente,2=preaprobado,3=aprobado,4=confirmado
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
            DB::table('s_prestamo_ahorro')->whereId($id)->update([
                'fecharechazado' => Carbon::now(),
                'comentariosupervisor' => $request->observacionsupervisor_rechazar,
                'idsupervisor' => Auth::user()->id,
                'idestadoahorro' => 1,
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
            DB::table('s_prestamo_ahorro')->whereId($id)->update([
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
