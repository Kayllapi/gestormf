<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;

class TransferenciaSaldoController extends Controller
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
        $transferenciasaldos = DB::table('s_transferenciasaldo')
            ->join('s_caja as cajaorigen', 'cajaorigen.id', 's_transferenciasaldo.idcajaorigen')
            ->join('s_caja as cajadestino', 'cajadestino.id', 's_transferenciasaldo.idcajadestino')
            ->join('users as responsableorigen', 'responsableorigen.id', 's_transferenciasaldo.idresponsableorigen')
            ->leftjoin('users as responsabledestino', 'responsabledestino.id', 's_transferenciasaldo.idresponsabledestino')
            ->where('s_transferenciasaldo.idtienda', $idtienda)
            ->whereIn('s_transferenciasaldo.idestado', [1, 2])
            ->select(
                's_transferenciasaldo.*',
                'cajaorigen.nombre as cajaorigen_nombre',
                'cajadestino.nombre as cajadestino_nombre',
                'responsableorigen.nombre as responsableorigen_nombre',
                'responsabledestino.nombre as responsabledestino_nombre'
            )
            ->orderBy('s_transferenciasaldo.id', 'desc')
            ->paginate(10);
      
        return view('layouts/backoffice/tienda/sistema/transferenciasaldo/index', compact(
            'tienda',
            'transferenciasaldos'
        ));
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
        return view('layouts/backoffice/tienda/sistema/transferenciasaldo/create', compact(
            'tienda',
            'cajas'
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
      
        if($request->input('view') == 'registrar') {
            $rules    = [
                'idcajaorigen' => 'required',
                'idcajadestino' => 'required',
                'monto' => 'required',
                'motivo' => 'required',
            ];
            $messages = [
                'idcajaorigen.required' => 'El campo "De" es Obligatorio.',
                'idcajadestino.required' => 'El campo "Para" es Obligatorio.',
                'monto.required' => 'El campo "Monto" es Obligatorio.',
                'motivo.required' => 'El campo "Motivo" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            // Validaciones
            if ($request->idcajaorigen == $request->idcajadestino) {
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El campo "De" y el campo "Para" no pueden ser iguales.'
                ]);
            }
            if ((float)$request->monto <= 0) {
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El campo "Monto" debe ser mayor a cero.'
                ]);
            }
            // fin
          
            // obtener ultimo código
            $transferenciasaldo = DB::table('s_transferenciasaldo')
                ->where('s_transferenciasaldo.idtienda',$idtienda)
                ->orderBy('s_transferenciasaldo.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($transferenciasaldo!=''){
                $codigo = $transferenciasaldo->codigo+1;
            }
            // fin obtener ultimo código

            /* idestado
             * 1 = pendiente
             * 2 = confirmado
             * 3 = anulado
             */
            DB::table('s_transferenciasaldo')->insert([
                'fecharegistro'         => Carbon::now(),
                'fechasolicitud'        => Carbon::now(),
                'codigo'                => $codigo,
                'monto'                 => $request->input('monto'),
                'motivo'                => $request->input('motivo') ?? '',
                'idmoneda'              => 1,
                'idcajaorigen'          => $request->input('idcajaorigen'),
                'idcajadestino'         => $request->input('idcajadestino'),
                'idresponsableorigen'   => Auth::user()->id,
                'idresponsabledestino'  => 0,
                'idtienda'              => $idtienda,
                'idestado'              => 1
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
        $cajas = DB::table('s_caja')
            ->where('idtienda', $idtienda)
            ->where('idestado', 1)
            ->get();
        $transferenciasaldo = DB::table('s_transferenciasaldo')
            ->join('s_caja as cajaorigen', 'cajaorigen.id', 's_transferenciasaldo.idcajaorigen')
            ->join('s_caja as cajadestino', 'cajadestino.id', 's_transferenciasaldo.idcajadestino')
            ->join('users as responsableorigen', 'responsableorigen.id', 's_transferenciasaldo.idresponsableorigen')
            ->leftjoin('users as responsabledestino', 'responsabledestino.id', 's_transferenciasaldo.idresponsabledestino')
            ->where('s_transferenciasaldo.id', $id)
            ->select(
                's_transferenciasaldo.*',
                'cajaorigen.nombre as cajaorigen_nombre',
                'cajadestino.nombre as cajadestino_nombre',
                'responsableorigen.nombre as responsableorigen_nombre',
                'responsabledestino.nombre as responsabledestino_nombre'
            )
            ->first();
      
        if ($request->input('view') == 'confirmar') {
            return view('layouts/backoffice/tienda/sistema/transferenciasaldo/confirmar', compact(
                'transferenciasaldo',
                'tienda'
            ));
        } else if ($request->input('view') == 'detalle') {
            return view('layouts/backoffice/tienda/sistema/transferenciasaldo/detalle', compact(
                'transferenciasaldo',
                'tienda'
            ));
        } else if ($request->input('view') == 'editar') {
            return view('layouts/backoffice/tienda/sistema/transferenciasaldo/edit', compact(
                'transferenciasaldo',
                'tienda',
                'cajas'
            ));
        } else if ($request->input('view') == 'anular') {
            return view('layouts/backoffice/tienda/sistema/transferenciasaldo/anular', compact(
                'transferenciasaldo',
                'tienda'
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
      
      if ($request->input('view') == 'editar') {
            $rules    = [
                'idcajaorigen' => 'required',
                'idcajadestino' => 'required',
                'monto' => 'required',
                'motivo' => 'required',
            ];
            $messages = [
                'idcajaorigen.required' => 'El campo "De" es Obligatorio.',
                'idcajadestino.required' => 'El campo "Para" es Obligatorio.',
                'monto.required' => 'El campo "Monto" es Obligatorio.',
                'motivo.required' => 'El campo "Motivo" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            // Validaciones
            if ($request->idcajaorigen == $request->idcajadestino) {
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El campo "De" y el campo "Para" no pueden ser iguales.'
                ]);
            }
            if ((float)$request->monto <= 0) {
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El campo "Monto" debe ser mayor a cero.'
                ]);
            }
            // fin

            /* idestado
             * 1 = pendiente
             * 2 = confirmado
             * 3 = anulado
             */
            DB::table('s_transferenciasaldo')->whereId($id)->update([
                'monto'         => $request->input('monto'),
                'motivo'        => $request->input('motivo') ?? '',
                'idcajaorigen'  => $request->input('idcajaorigen'),
                'idcajadestino' => $request->input('idcajadestino'),
            ]);     
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        } elseif ($request->input('view') == 'confirmar') {
            /* idestado
             * 1 = pendiente
             * 2 = confirmado
             * 3 = anulado
             */
            DB::table('s_transferenciasaldo')->whereId($id)->update([
                'fecharecepcion' => Carbon::now(),
                'idestado' => 2
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
      
        if ($request->input('view') == 'anular') {
            DB::table('s_transferenciasaldo')->whereId($id)->update([
                'fechaanulacion' => Carbon::now(),
                'idestado'       => 3,
            ]);
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
       
    }
}
