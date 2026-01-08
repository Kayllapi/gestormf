<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class MovimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        json_movimiento($idtienda,$request->name_modulo);   

        return view('layouts/backoffice/tienda/nuevosistema/movimiento/index',[
            'tienda' => $tienda,
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
        $s_conceptomovimientos = DB::table('s_conceptomovimiento')->get();
        $s_monedas = DB::table('s_moneda')->get();
        return view('layouts/backoffice/tienda/nuevosistema/movimiento/create',[
          'tienda' => $tienda,
          's_conceptomovimientos' => $s_conceptomovimientos,
          's_monedas' => $s_monedas,
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
            $rules = [
                'idconceptomovimiento' => 'required',
                'idmoneda' => 'required',
                'monto' => 'required',
                'concepto' => 'required',
            ];
            $messages = [
                'idconceptomovimiento.required'   => 'El "Tipo" es Obligatorio.',
                'idmoneda.required'   => 'La "Moneda" es Obligatorio.',
                'monto.required'   => 'El "Monto" es Obligatorio.',
                'concepto.required'   => 'El "Concepto" es Obligatorio.',
            ];
      
            $this->validate($request,$rules,$messages);

            if($request->input('monto')<=0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Monto debe ser mayor o igual a 0.'
                ]);
            }

            // aperturacaja
            $caja = caja($idtienda,Auth::user()->id);
            if($caja['resultado']!='ABIERTO'){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La Caja debe estar Aperturada.'
                ]);
            }
            $idaperturacierre = $caja['apertura']->id;
            // fin aperturacaja

            $s_conceptomovimiento = DB::table('s_conceptomovimiento')
                ->where('s_conceptomovimiento.id',$request->input('idconceptomovimiento'))
                ->first();
            if($s_conceptomovimiento->tipo=='Egreso'){
                $efectivo = efectivo($idtienda,$idaperturacierre);
                if($request->input('monto')>$efectivo['total']){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'No hay suficiente saldo en caja!.'
                    ]);
                }
            };
            
            // validar decimales
            $listmonto = explode('.',$request->input('monto'));
            if(count($listmonto)>1){
               if(strlen($listmonto[1])>2){
                  return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'Solo puedes utilizar 2 decimales en el monto.'
                  ]);
               }elseif(substr($listmonto[1], 1, 1)>0){
                  return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'Los decimales del monto, debe ser redondeado.'
                  ]);
               }
            }
              
            // obtener ultimo código
            $s_movimiento = DB::table('s_movimiento')
                ->where('s_movimiento.idtienda',$idtienda)
                ->orderBy('s_movimiento.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($s_movimiento!=''){
                $codigo = $s_movimiento->codigo+1;
            }
            // fin obtener ultimo código
          
          
            DB::table('s_movimiento')->insert([
                'fecharegistro'=> Carbon::now(),   
                'fechaconfirmacion'=> Carbon::now(),         
                'codigo'=> $codigo,         
                'monto'=> $request->input('monto'),         
                'concepto'=> $request->input('concepto')!=null ? $request->input('concepto') : '',
                's_idmoneda'=> $request->input('idmoneda'),
                's_idaperturacierre'=> $idaperturacierre,
                's_idconceptomovimiento'=> $request->input('idconceptomovimiento'),
                's_idusuario'=> Auth::user()->id,
                's_idestado'=> 2,
                'idtienda'=> $idtienda
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if ($id == 'show-moduloactualizar') {
             json_movimiento($idtienda,$request->name_modulo);   
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idtienda, $idmovimiento)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $s_movimiento = DB::table('s_movimiento')->whereId($idmovimiento)->first();
        $s_conceptomovimientos = DB::table('s_conceptomovimiento')->get();
        $s_monedas = DB::table('s_moneda')->get();
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'editar') {
            return view('layouts/backoffice/tienda/nuevosistema/movimiento/edit',[
                'tienda' => $tienda,
                's_movimiento' => $s_movimiento,
                's_conceptomovimientos' => $s_conceptomovimientos,
                's_monedas' => $s_monedas,
            ]);
        }
        elseif($request->input('view') == 'detalle') {
          
           $s_movimiento = DB::table('s_movimiento')
            ->join('s_conceptomovimiento','s_conceptomovimiento.id','s_movimiento.s_idconceptomovimiento')
            ->join('s_moneda','s_moneda.id','s_movimiento.s_idmoneda')
            ->join('users as responsable','responsable.id','s_movimiento.s_idusuario')
            ->where('s_movimiento.id',$idmovimiento)
            ->select(
                's_movimiento.*',
                's_conceptomovimiento.nombre as conceptomovimientonombre',
                's_moneda.nombre as monedanombre',
                's_conceptomovimiento.tipo as tipo',
                'responsable.nombre as responsablenombre'
            )
            ->first();
            return view('layouts/backoffice/tienda/nuevosistema/movimiento/detalle',[
                's_movimiento' => $s_movimiento,
            ]);
            
        }
        elseif($request->input('view') == 'eliminar') {
          $s_movimiento = DB::table('s_movimiento')
            ->join('s_conceptomovimiento','s_conceptomovimiento.id','s_movimiento.s_idconceptomovimiento')
            ->join('s_moneda','s_moneda.id','s_movimiento.s_idmoneda')
            ->join('users as responsable','responsable.id','s_movimiento.s_idusuario')
            ->where('s_movimiento.id',$idmovimiento)
            ->select(
                's_movimiento.*',
                's_conceptomovimiento.nombre as conceptomovimientonombre',
                's_moneda.nombre as monedanombre',
                's_conceptomovimiento.tipo as tipo',
                'responsable.nombre as responsablenombre'
            )
            ->first();
            return view('layouts/backoffice/tienda/nuevosistema/movimiento/delete',[
                'tienda' => $tienda,
                's_movimiento' => $s_movimiento,
                's_conceptomovimientos' => $s_conceptomovimientos,
                's_monedas' => $s_monedas,
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
    public function update(Request $request, $idtienda, $idmovimiento)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'editar') {
            $rules = [
                'idconceptomovimiento' => 'required',
                'monto' => 'required',
                'concepto' => 'required',
            ];
            $messages = [
                'idconceptomovimiento.required'   => 'El "Tipo" es Obligatorio.',
                'monto.required'   => 'El "Monto" es Obligatorio.',
                'concepto.required'   => 'El "Concepto" es Obligatorio.',
            ];
      
            $this->validate($request,$rules,$messages);

            if($request->input('monto')<=0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Monto debe ser mayor o igual a 0.'
                ]);
            }

            DB::table('s_movimiento')->whereId($idmovimiento)->update([     
                'monto'=> $request->input('monto'),         
                'concepto'=> $request->input('concepto')!=null ? $request->input('concepto') : '',
                's_idconceptomovimiento'=> $request->input('idconceptomovimiento'),
                's_idusuario'=> Auth::user()->id
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idtienda, $s_idmovimiento)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
            // aperturacaja
            $caja = caja($idtienda,Auth::user()->id);
            if($caja['resultado']!='ABIERTO'){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La Caja debe estar Aperturada.'
                ]);
            }
            $idaperturacierre = $caja['apertura']->id;
            // fin aperturacaja
          
            $s_movimiento = DB::table('s_movimiento')->whereid($s_idmovimiento)->first();
            if($idaperturacierre!=$s_movimiento->s_idaperturacierre){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El movimiento no se puede anular, ya que no pertenece a esta caja aperturada.'
                ]);
            }

            DB::table('s_movimiento')->whereId($s_idmovimiento)->update([     
                'fechaanulacion'=> Carbon::now(),
                's_idestado'=> 3
            ]);
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha eliminado correctamente.'
						]);
        }
    }
}
