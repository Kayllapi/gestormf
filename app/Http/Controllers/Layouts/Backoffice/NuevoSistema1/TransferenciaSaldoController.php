<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

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
       
      
      
//         $transferenciasaldos = DB::table('s_transferenciasaldo')
//             ->join('s_caja as cajaorigen', 'cajaorigen.id', 's_transferenciasaldo.idcajaorigen')
//             ->join('s_caja as cajadestino', 'cajadestino.id', 's_transferenciasaldo.idcajadestino')
//             ->join('users as responsableorigen', 'responsableorigen.id', 's_transferenciasaldo.idresponsableorigen')
//             ->leftjoin('users as responsabledestino', 'responsabledestino.id', 's_transferenciasaldo.idresponsabledestino')
//             ->where('s_transferenciasaldo.idtienda', $idtienda)
//             ->whereIn('s_transferenciasaldo.idestado', [1, 2])
//             ->select(
//                 's_transferenciasaldo.*',
//                 'cajaorigen.nombre as cajaorigen_nombre',
//                 'cajadestino.nombre as cajadestino_nombre',
//                 'responsableorigen.nombre as responsableorigen_nombre',
//                 'responsabledestino.nombre as responsabledestino_nombre'
//             )
//             ->orderBy('s_transferenciasaldo.id', 'desc')
//             ->paginate(10);
            json_transferenciasaldo($idtienda,$request->name_modulo);
      
        return view('layouts/backoffice/tienda/nuevosistema/transferenciasaldo/index', compact(
            'tienda',
        ));
    }

  
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $cajas = DB::table('s_caja')->where('idtienda', $idtienda)->get();
        return view('layouts/backoffice/tienda/nuevosistema/transferenciasaldo/create', compact(
            'cajas',
            'tienda'
        ));
    }

    
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
          
            // Generando codigo
            $transferenciasaldo = DB::table('s_transferenciasaldo')->orderBy('id', 'desc')->first();
            $codigo = 1;
            if (!is_null($transferenciasaldo)) {
                $codigo = (int)$transferenciasaldo->codigo + 1;
            }
            // fin

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

    public function show(Request $request, $idtienda,$id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      if ($id == 'show-moduloactualizar') {
           json_transferenciasaldo($idtienda,$request->name_modulo);

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
        $cajas = DB::table('s_caja')->where('idtienda', $idtienda)->get();
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
            return view('layouts/backoffice/tienda/nuevosistema/transferenciasaldo/confirmar', compact(
                'transferenciasaldo',
                'tienda'
            ));
        } else if ($request->input('view') == 'detalle') {
            return view('layouts/backoffice/tienda/nuevosistema/transferenciasaldo/detalle', compact(
                'transferenciasaldo',
                'tienda'
            ));
        } else if ($request->input('view') == 'editar') {
            return view('layouts/backoffice/tienda/nuevosistema/transferenciasaldo/editar', compact(
                'transferenciasaldo',
                'tienda',
                'cajas'
            ));
        } else if ($request->input('view') == 'anular') {
            return view('layouts/backoffice/tienda/nuevosistema/transferenciasaldo/anular', compact(
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
