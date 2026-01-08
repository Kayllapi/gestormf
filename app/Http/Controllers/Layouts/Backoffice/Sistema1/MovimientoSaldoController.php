<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;

class MovimientoSaldoController extends Controller
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
        /* idestado
         * 1 = pendiente
         * 2 = confirmado
         * 3 = anulado
         */
        $movimientosaldos = DB::table('s_movimientosaldo')
            ->join('s_caja', 's_caja.id', 's_movimientosaldo.idcaja')
            ->join('users', 'users.id', 's_movimientosaldo.idresponsable')
            ->where('s_movimientosaldo.idtienda', $idtienda)
            ->where('s_movimientosaldo.idestado', 1)
            ->select(
                's_movimientosaldo.*',
                's_caja.nombre as cajanombre',
                'users.nombre as responsablenombre',
            )
            ->orderBy('s_movimientosaldo.id', 'desc')
            ->paginate(10);
      
        return view('layouts/backoffice/tienda/sistema/movimientosaldo/index', [
            'tienda' => $tienda,
            'movimientosaldos' => $movimientosaldos,
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
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $cajas = DB::table('s_caja')
            ->where('idtienda', $idtienda)
            ->where('idestado', 1)
            ->get();
        $tipomovimientos = DB::table('s_tipomovimiento')->get();
        $monedas = DB::table('s_moneda')->get();
        return view('layouts/backoffice/tienda/sistema/movimientosaldo/create', [
            'tienda' => $tienda,
            'cajas' => $cajas,
            'tipomovimientos' => $tipomovimientos,
            'monedas' => $monedas,
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
            $rules    = [
                'idtipomovimiento' => 'required',
                'idcaja' => 'required',
                'idmoneda' => 'required',
                'monto' => 'required',
                'motivo' => 'required',
            ];
            $messages = [
                'idtipomovimiento.required' => 'El "Tipo Movimiento" es Obligatorio.',
                'idcaja.required' => 'La "Caja" es Obligatorio.',
                'idmoneda.required' => 'La "Moneda" es Obligatorio.',
                'monto.required' => 'El "Monto" es Obligatorio.',
                'motivo.required' => 'El "Motivo" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            if ((float)$request->monto <= 0) {
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "Monto" debe ser mayor a cero.'
                ]);
            }
          
            // obtener ultimo código
            $movimientosaldo = DB::table('s_movimientosaldo')
                ->where('s_movimientosaldo.idtienda',$idtienda)
                ->orderBy('s_movimientosaldo.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($movimientosaldo!=''){
                $codigo = $movimientosaldo->codigo+1;
            }
            // fin obtener ultimo código

            DB::table('s_movimientosaldo')->insert([
                'fecharegistro' => Carbon::now(),
                'codigo'        => $codigo,
                'monto'         => $request->input('monto'),
                'motivo'        => $request->input('motivo'),
                'idmoneda'      => $request->input('idmoneda'),
                'idcaja'        => $request->input('idcaja'),
                'idresponsable' => Auth::user()->id,
                'idtipomovimiento' => $request->input('idtipomovimiento'),
                'idestadomovimientosaldo' => 1,
                'idtienda'      => $idtienda,
                'idestado'      => 1
            ]);
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
    public function show(Request $request, $idtienda,$id)
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
        $movimientosaldo = DB::table('s_movimientosaldo')
            ->join('s_caja', 's_caja.id', 's_movimientosaldo.idcaja')
            ->join('s_moneda', 's_moneda.id', 's_movimientosaldo.idmoneda')
            ->join('s_tipomovimiento', 's_tipomovimiento.id', 's_movimientosaldo.idtipomovimiento')
            ->join('users', 'users.id', 's_movimientosaldo.idresponsable')
            ->where('s_movimientosaldo.idtienda', $idtienda)
            ->where('s_movimientosaldo.idestado', 1)
            ->where('s_movimientosaldo.id', $id)
            ->select(
                's_movimientosaldo.*',
                's_caja.nombre as cajanombre',
                's_moneda.nombre as monedanombre',
                's_tipomovimiento.nombre as tipomovimientonombre',
                'users.nombre as responsablenombre',
            )
            ->first();
      
        if ($request->input('view') == 'confirmar') {
            $cajas = DB::table('s_caja')
              ->where('idtienda', $idtienda)
              ->where('idestado', 1)
              ->get();
            $tipomovimientos = DB::table('s_tipomovimiento')->get();
            $monedas = DB::table('s_moneda')->get();
            return view('layouts/backoffice/tienda/sistema/movimientosaldo/confirmar', [
                'tienda' => $tienda,
                'cajas' => $cajas,
                'tipomovimientos' => $tipomovimientos,
                'monedas' => $monedas,
                'movimientosaldo' => $movimientosaldo,
            ]);
        } else if ($request->input('view') == 'detalle') {
            $cajas = DB::table('s_caja')
              ->where('idtienda', $idtienda)
              ->where('idestado', 1)
              ->get();
            $tipomovimientos = DB::table('s_tipomovimiento')->get();
            $monedas = DB::table('s_moneda')->get();
            return view('layouts/backoffice/tienda/sistema/movimientosaldo/detalle', [
                'tienda' => $tienda,
                'cajas' => $cajas,
                'tipomovimientos' => $tipomovimientos,
                'monedas' => $monedas,
                'movimientosaldo' => $movimientosaldo,
            ]);
        } else if ($request->input('view') == 'editar') {
            $cajas = DB::table('s_caja')
              ->where('idtienda', $idtienda)
              ->where('idestado', 1)
              ->get();
            $tipomovimientos = DB::table('s_tipomovimiento')->get();
            $monedas = DB::table('s_moneda')->get();
            return view('layouts/backoffice/tienda/sistema/movimientosaldo/edit', [
                'tienda' => $tienda,
                'cajas' => $cajas,
                'tipomovimientos' => $tipomovimientos,
                'monedas' => $monedas,
                'movimientosaldo' => $movimientosaldo,
            ]);
        } else if ($request->input('view') == 'eliminar') {
            $cajas = DB::table('s_caja')
              ->where('idtienda', $idtienda)
              ->where('idestado', 1)
              ->get();
            $tipomovimientos = DB::table('s_tipomovimiento')->get();
            $monedas = DB::table('s_moneda')->get();
            return view('layouts/backoffice/tienda/sistema/movimientosaldo/eliminar', [
                'tienda' => $tienda,
                'cajas' => $cajas,
                'tipomovimientos' => $tipomovimientos,
                'monedas' => $monedas,
                'movimientosaldo' => $movimientosaldo,
            ]);
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
      
      if ($request->input('view') == 'editar') {
            $rules    = [
                'idtipomovimiento' => 'required',
                'idcaja' => 'required',
                'idmoneda' => 'required',
                'monto' => 'required',
                'motivo' => 'required',
            ];
            $messages = [
                'idtipomovimiento.required' => 'El "Tipo Movimiento" es Obligatorio.',
                'idcaja.required' => 'La "Caja" es Obligatorio.',
                'idmoneda.required' => 'La "Moneda" es Obligatorio.',
                'monto.required' => 'El "Monto" es Obligatorio.',
                'motivo.required' => 'El "Motivo" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            if ((float)$request->monto <= 0) {
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "Monto" debe ser mayor a cero.'
                ]);
            }
          
            DB::table('s_movimientosaldo')->whereId($id)->update([
                'monto'         => $request->input('monto'),
                'motivo'        => $request->input('motivo'),
                'idmoneda'      => $request->input('idmoneda'),
                'idcaja'        => $request->input('idcaja'),
                'idtipomovimiento' => $request->input('idtipomovimiento'),
            ]);     
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        } elseif ($request->input('view') == 'confirmar') {
            DB::table('s_movimientosaldo')->whereId($id)->update([
                'fechaconfirmado' => Carbon::now(),
                'idestadomovimientosaldo' => 2
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha confirmado correctamente.'
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
      
        if ($request->input('view') == 'eliminar') {
            DB::table('s_movimientosaldo')->whereId($id)->delete();
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
       
    }
}
