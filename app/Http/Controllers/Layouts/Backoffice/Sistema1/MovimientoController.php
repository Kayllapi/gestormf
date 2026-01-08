<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class MovimientoController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        $where = [];
        $where[] = ['s_movimiento.tipomovimientonombre','LIKE','%'.$request->input('concepto').'%'];
        $where[] = ['s_movimiento.concepto','LIKE','%'.$request->input('descripcion').'%'];
        $where[] = ['s_movimiento.monto','LIKE','%'.$request->input('monto').'%'];
        $where[] = ['s_movimiento.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];

        if(Auth::user()->idtienda!=0 && Auth::user()->idtipousuario!=1){
            $where[] = ['responsable.id',Auth::user()->id];
        }
       
        $s_movimientos  = DB::table('s_movimiento')
            ->join('s_moneda','s_moneda.id','s_movimiento.s_idmoneda')
            ->leftJoin('s_aperturacierre','s_aperturacierre.id','s_movimiento.s_idaperturacierre')
            ->leftJoin('s_caja','s_caja.id','s_aperturacierre.s_idcaja')
            ->join('users as responsable','responsable.id','s_movimiento.s_idusuario')
            ->where('s_movimiento.idtienda',$idtienda)
            ->where('s_movimiento.idestado',1)
            ->where($where)
            ->select(
                's_movimiento.*',
                's_caja.nombre as cajanombre',
                's_movimiento.tipomovimiento as conceptomovimientotipo',
                's_movimiento.tipomovimientonombre as conceptomovimientonombre',
                's_moneda.simbolo as monedasimbolo',
                'responsable.nombre as responsablenombre'
            )
            ->orderBy('s_movimiento.id','desc')
            ->paginate(10);
      
        // aperturacaja
        $caja = caja($idtienda,Auth::user()->id);
        $idaperturacierre = 0;
        if($caja['resultado']=='ABIERTO'){
            $idaperturacierre = $caja['apertura']->id;
        }
      
        return view('layouts/backoffice/tienda/sistema/movimiento/index',[
            'tienda' => $tienda,
            's_movimientos' => $s_movimientos,
            'idapertura' => $idaperturacierre,
        ]);
    }

    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $s_monedas = DB::table('s_moneda')->get();
        $caja = caja($idtienda,Auth::user()->id);
      
        return view('layouts/backoffice/tienda/sistema/movimiento/create',[
            'tienda' => $tienda,
            's_monedas' => $s_monedas,
            'caja' => $caja,
        ]);
        
    }

    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
          
            $apertura = caja($idtienda,Auth::user()->id)['apertura'];
            $rules = [
                'idconceptomovimiento' => 'required',
            ];
            
            $idmoneda=0;
            if($apertura->config_sistema_moneda_usar==1){
                $idmoneda=1;
            }elseif($apertura->config_sistema_moneda_usar==2){
                $idmoneda=2;
            }elseif($apertura->config_sistema_moneda_usar==3){
                $rules = array_merge($rules,[
                    'idmoneda' => 'required',
                ]);
                $idmoneda=$request->idmoneda;
            }
          
            $rules = array_merge($rules,[
                'monto' => 'required',
                'idresponsableentrega' => 'required',
                'concepto' => 'required',
            ]);
          
            $messages = [
                'idconceptomovimiento.required'   => 'El "Tipo" es Obligatorio.',
                'idmoneda.required'   => 'La "Moneda" es Obligatorio.',
                'monto.required'   => 'El "Monto" es Obligatorio.',
                'idresponsableentrega.required'   => 'El "Responsable a Entregar" es Obligatorio.',
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

            $tipo = explode('/-/',$request->input('idconceptomovimiento'));
    
            if($tipo[0]=='EGRESO'){
                $efectivo = efectivo($idtienda,$idaperturacierre,$idmoneda);
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
                'tipomovimiento'=> $tipo[0],         
                'tipomovimientonombre'=> $tipo[1],         
                's_idmoneda'=> $idmoneda,
                's_idaperturacierre'=> $idaperturacierre,
                's_idusuario'=> Auth::user()->id,
                'idresponsableentrega'=> $request->idresponsableentrega,
                'idestadomovimiento'=> 2,
                'idtienda'=> $idtienda,
                'idestado'=> 1
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show($id)
    {
       //
    }

    public function edit(Request $request, $idtienda, $idmovimiento)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')
            ->leftJoin('ubigeo', 'ubigeo.id', 'tienda.idubigeo')
            ->select(
                'tienda.*',
                'ubigeo.nombre as ubigeonombre',
            )
            ->where('tienda.id',$idtienda)
            ->first();
      
        $s_movimiento = DB::table('s_movimiento')
            ->join('users as responsable','responsable.id','s_movimiento.s_idusuario')
            ->join('users as responsableentregado','responsableentregado.id','s_movimiento.idresponsableentrega')
            ->join('s_moneda','s_moneda.id','s_movimiento.s_idmoneda')
            ->where('s_movimiento.id',$idmovimiento)
            ->select(
                's_movimiento.*',
                's_movimiento.tipomovimiento as tiponombre',
                's_movimiento.tipomovimientonombre as conceptonombre',
                'responsable.nombre as responsablenombre',
                'responsable.apellidos as responsableapellidos',
                'responsableentregado.identificacion as responsableentregadoidentificacion',
                'responsableentregado.nombre as responsableentregadonombre',
                'responsableentregado.apellidos as responsableentregadoapellidos',
                's_moneda.nombre as monedanombre',
                's_moneda.simbolo as monedasimbolo'
            )
            ->first();
      
        if($request->input('view') == 'ticket') {
            return view('layouts/backoffice/tienda/sistema/movimiento/ticket',[
                'tienda' => $tienda,
                's_movimiento' => $s_movimiento,
            ]);
        }
        elseif($request->input('view') == 'ticketpdf') {
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/movimiento/ticketpdf',[
                'tienda' => $tienda,
                's_movimiento' => $s_movimiento,
            ]);
            $ticket = 'Ticket_'.str_pad($s_movimiento->codigo, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');

        }
        elseif($request->input('view') == 'detalle') {
            return view('layouts/backoffice/tienda/sistema/movimiento/detalle',[
                'tienda' => $tienda,
                's_movimiento' => $s_movimiento,
            ]);
        }
        elseif($request->input('view') == 'anular') {
            return view('layouts/backoffice/tienda/sistema/movimiento/anular',[
                'tienda' => $tienda,
                's_movimiento' => $s_movimiento,
            ]);
        }
    }

    public function update(Request $request, $idtienda, $idmovimiento)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        /*if($request->input('view') == 'editar') {
            $rules = [
                'idconceptomovimiento' => 'required',
                'monto' => 'required',
                'idusuarioresponsable' => 'required',
                'concepto' => 'required',
            ];
            $messages = [
                'idconceptomovimiento.required'   => 'El "Tipo" es Obligatorio.',
                'monto.required'   => 'El "Monto" es Obligatorio.',
                'idusuarioresponsable.required'   => 'El "Responsable" es Obligatorio.',
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
                's_idusuarioresponsable'=> $request->input('idusuarioresponsable'),
                's_idusuario'=> Auth::user()->id
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }*/
        if($request->input('view') == 'anular') {

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
          
            // validar 
            $s_movimiento = DB::table('s_movimiento')->whereid($idmovimiento)->first();
            if($idaperturacierre!=$s_movimiento->s_idaperturacierre){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El movimiento no se puede anular, ya que no pertenece a esta caja aperturada.'
                ]);
            }

            DB::table('s_movimiento')->whereId($idmovimiento)->update([     
                'fechaanulacion'=> Carbon::now(),
                'idestadomovimiento'=> 3
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha anulado correctamente.'
            ]);
        }
        /*elseif($request->input('view') == 'confirmar') {

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

            $efectivo = efectivo($idtienda,$idaperturacierre);
            if($request->input('total')>$efectivo['total']){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No hay suficiente saldo en caja!.'
                ]);
            }

            DB::table('s_movimiento')->whereId($idmovimiento)->update([     
                'fechaconfirmacion'=> Carbon::now(),
                's_idaperturacierre'=> $idaperturacierre,
                'idestadomovimiento'=> 2
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha confirmado correctamente.'
            ]);
        }*/
    }

    public function destroy(Request $request, $idtienda, $s_idmovimiento)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        /*if($request->input('view') == 'eliminar') {
          
            DB::table('s_movimiento')
                ->where('s_movimiento.id',$s_idmovimiento)
                ->where('s_movimiento.idtienda',$idtienda)
                ->update([     
                'fechaeliminar'=> Carbon::now(),
                'idestado'=> 2
            ]);
          
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha eliminado correctamente.'
						]);
        }*/
    }
}
