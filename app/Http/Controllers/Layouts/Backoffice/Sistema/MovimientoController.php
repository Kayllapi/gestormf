<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class MovimientoController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/movimiento/tabla',[
                'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->view == 'registrar') {
            return view(sistema_view().'/movimiento/create',[
                'tienda' => $tienda
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
            /* ----- VALIDAR CAMPOS ----- */
            $rules = [
                'idconceptomovimiento' => 'required',
                'idmoneda' => 'required',
                'monto' => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0', 
                'idresponsableentrega' => 'required',
                'concepto' => 'required',
            ];
          
            $messages = [
                'idconceptomovimiento.required'   => 'El "Tipo" es Obligatorio.',
                'idmoneda.required'   => 'La "Moneda" es Obligatorio.',
                'monto.required'   => 'El "Monto" es Obligatorio.',
                'monto.numeric'   => 'El "Monto", debe ser númerico.',
                'monto.regex'     => 'El "Monto", debe ser máximo de 2 decimales.',
                'monto.gte'       => 'El "Monto", debe ser mayor ó igual 0.',
                'idresponsableentrega.required'   => 'El "Responsable a Entregar" es Obligatorio.',
                'concepto.required'   => 'El "Concepto" es Obligatorio.',
            ];
      
            $this->validate($request,$rules,$messages);
            // aperturacaja
            $apertura = sistema_apertura([
                'idtienda'          => $idtienda,
                'idsucursal'        => Auth::user()->idsucursal,
                'idusersrecepcion'  => Auth::user()->id,
            ]);
            if($apertura['resultado']!='ABIERTO'){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La Caja debe estar Aperturada.'
                ]);
            }
            $idaperturacierre = $apertura['idapertura'];
            // fin aperturacaja
            /* ----- FIN VALIDAR CAMPOS ----- */


            $tipo = explode('/-/',$request->input('idconceptomovimiento'));
    
            if($tipo[0]=='EGRESO'){
                
                $efectivo_soles = sistema_efectivo([
                    'idtienda'    => $idtienda,
                    'idsucursal'  => Auth::user()->idsucursal,
                    'idapertura'  => $apertura['idapertura'],
                    'idmoneda'    => $request->input('idmoneda'),
                ]);

                if($request->input('monto')>$efectivo_soles['total']){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'No hay suficiente saldo en caja!.'
                    ]);
                }
            };
              
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
            $moneda = DB::table('s_moneda')->whereId($request->input('idmoneda'))->first();
            $usuario = DB::table('users')->whereId(Auth::user()->id)->first();
            $responsable_entrega = DB::table('users')->whereId($request->idresponsableentrega)->first();

            DB::table('s_movimiento')->insert([
                'fecharegistro'=> Carbon::now(),   
                'fechaconfirmacion'=> Carbon::now(),         
                'codigo'=> $codigo,         
                'monto'=> $request->input('monto'),         
                'concepto'=> $request->input('concepto')!=null ? $request->input('concepto') : '',
                'tipomovimiento'=> $tipo[0],         
                'tipomovimientonombre'=> $tipo[1],     

                'db_idmoneda' => $moneda->nombre,
                'db_idusuario' => $usuario->nombrecompleto,
                'db_idresponsableentrega' => $responsable_entrega->nombrecompleto,
                'db_idestadomovimiento' => 'CONFIRMADO', 

                's_idmoneda'=> $request->input('idmoneda'),
                's_idaperturacierre'=> $idaperturacierre,
                's_idusuario'=> Auth::user()->id,
                'idresponsableentrega'=> $request->idresponsableentrega,
                'idestadomovimiento'=> 2, // 1 = PENDIENTE | 2 = CONFIRMADO | 3 = ANULADO
                'idsucursal'=> Auth::user()->idsucursal,
                'idtienda'=> $idtienda,
                'idestado'=> 1
            ]);
            
            // json_movimiento($idtienda,Auth::user()->idsucursal,Auth::user()->id);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        if($id == 'show_table'){
            $idsucursal = Auth::user()->idsucursal;
            $idusuario = Auth::user()->id;
            $idapertura = sistema_apertura([
                'idtienda'          => $idtienda,
                'idsucursal'        => $idsucursal,
                'idusersrecepcion'  => $idusuario,
            ])['idapertura'];
          
           $s_movimientos  = DB::table('s_movimiento')
                ->join('s_moneda','s_moneda.id','s_movimiento.s_idmoneda')
                ->leftJoin('s_aperturacierre','s_aperturacierre.id','s_movimiento.s_idaperturacierre')
                ->leftJoin('s_caja','s_caja.id','s_aperturacierre.s_idcaja')
                ->join('users as responsable','responsable.id','s_movimiento.s_idusuario')
                ->where('s_movimiento.idtienda',$idtienda)
                ->where('s_movimiento.idsucursal',$idsucursal)
                ->where('s_movimiento.idestado',1)
                ->where('s_movimiento.s_idaperturacierre',$idapertura)
                ->where('responsable.id',$idusuario)
                ->where('s_movimiento.codigo','LIKE','%'.$request['columns'][0]['search']['value'].'%')
                ->where('s_movimiento.tipomovimiento','LIKE','%'.$request['columns'][1]['search']['value'].'%')
                ->where('s_movimiento.tipomovimientonombre','LIKE','%'.$request['columns'][2]['search']['value'].'%')
                ->where('s_movimiento.concepto','LIKE','%'.$request['columns'][3]['search']['value'].'%')
                ->where('s_movimiento.fecharegistro','LIKE','%'.$request['columns'][5]['search']['value'].'%')
                ->where('s_movimiento.db_idestadomovimiento','LIKE','%'.$request['columns'][6]['search']['value'].'%')
                ->select(
                    's_movimiento.*',
                    's_caja.nombre as cajanombre',
                    's_movimiento.tipomovimiento as conceptomovimientotipo',
                    's_movimiento.tipomovimientonombre as conceptomovimientonombre',
                    's_moneda.simbolo as monedasimbolo',
                    'responsable.nombre as responsablenombre'
                )
                ->orderBy('s_movimiento.id','desc')
                ->paginate($request->length,'*',null,($request->start/$request->length)+1);
          
            $tabla = [];
          
            foreach($s_movimientos as $value){
                $opcion = [];
                $estadomovimiento = '';
                $style = '';
                if($value->idestadomovimiento==1){
                    $estadomovimiento = 'PENDIENTE';
                }elseif($value->idestadomovimiento==2){
                    $opcion[] = [
                        'nombre'  => 'Ticket',
                        'onclick' => '/'.$idtienda.'/movimiento/'.$value->id.'/edit?view=ticket',
                        'icono'   => 'file-text'
                    ];
                    $opcion[] = [
                        'nombre'  => 'Anular',
                        'onclick' => '/'.$idtienda.'/movimiento/'.$value->id.'/edit?view=anular',
                        'icono'   => 'ban'
                    ];
                    $estadomovimiento = 'CONFIRMADO';
                }elseif($value->idestadomovimiento==3){
                    $estadomovimiento = 'ANULADO';
                    $style = 'table-mark-anulado';
                }
        
                $tabla[] = [
                    'id' => $value->id,
                    'style' => $style,
                    'codigo' => str_pad($value->codigo, 8, "0", STR_PAD_LEFT),
                    'conceptomovimientotipo' => $value->conceptomovimientotipo,
                    'conceptomovimientonombre' => $value->conceptomovimientonombre,
                    'concepto' => $value->concepto,
                    'monto' => $value->monedasimbolo.' '.$value->monto,
                    'fecharegistro' => date_format(date_create($value->fecharegistro),"d/m/Y h:i A"),
                    'estadomovimiento' => $estadomovimiento,
                    'opcion' => $opcion
                ];
            }
            return response()->json([
                'start'           => $request->start,
                'draw'            => $request->draw,
                'recordsTotal'    => $request->length,
                'recordsFiltered' => $s_movimientos->total(),
                'data'            => $tabla,
            ]);  
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $s_movimiento = DB::table('s_movimiento')
            ->join('users as responsable','responsable.id','s_movimiento.s_idusuario')
            ->join('users as responsableentregado','responsableentregado.id','s_movimiento.idresponsableentrega')
            ->join('s_moneda','s_moneda.id','s_movimiento.s_idmoneda')
            ->where('s_movimiento.id',$id)
            ->select(
                's_movimiento.*',
                's_movimiento.tipomovimiento as tiponombre',
                's_movimiento.tipomovimientonombre as conceptonombre',
                'responsable.nombrecompleto as responsablenombrecompleto',
                'responsableentregado.identificacion as responsableentregadoidentificacion',
                'responsableentregado.nombrecompleto as responsableentregadonombrecompleto',
                's_moneda.nombre as monedanombre',
                's_moneda.simbolo as monedasimbolo'
            )
            ->first();
      
        if($request->input('view') == 'ticket') {
          $ticket = new \stdClass();
          //DATOS EMISOR
          $ticket->tipo_fuente = configuracion($tienda->id,'sistema_tipoletra')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_tipoletra')['valor']:'Helvetica';
          $ticket->ancho_ticket = configuracion($tienda->id,'sistema_anchoticket')['resultado']=='CORRECTO'?(configuracion($tienda->id,'sistema_anchoticket')['valor']-1):'8'.'cm';
          $ticket->logotipo = url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$tienda->sucursal_imagen_logo);
          
            return view(sistema_view().'/movimiento/ticket',[
              'ticket' => $ticket,
              'tienda' => $tienda,
              's_movimiento' => $s_movimiento
            ]);
        }

        elseif($request->input('view') == 'anular') {
            return view(sistema_view().'/movimiento/anular',[
              'tienda' => $tienda,
              's_movimiento' => $s_movimiento,
            ]);
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'anular') {
            // aperturacaja
            $apertura = sistema_apertura([
                'idtienda'          => $idtienda,
                'idsucursal'        => Auth::user()->idsucursal,
                'idusersrecepcion'  => Auth::user()->id,
            ]);
            if($apertura['resultado']!='ABIERTO'){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La Caja debe estar Aperturada.'
                ]);
            }
            $idaperturacierre = $apertura['idapertura'];
            // fin aperturacaja
          
            $s_movimiento = DB::table('s_movimiento')->whereid($id)->first();
            if($idaperturacierre!=$s_movimiento->s_idaperturacierre){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El movimiento no se puede anular, ya que no pertenece a esta caja aperturada.'
                ]);
            }

            DB::table('s_movimiento')->whereId($id)->update([     
                'fechaanulacion'=> Carbon::now(),
                'db_idestadomovimiento' => 'ANULADO', 
                'idestadomovimiento'=> 3
            ]);
          
            json_movimiento($idtienda,Auth::user()->idsucursal,Auth::user()->id);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha anulado correctamente.'
            ]);
        }
            
    }


    public function destroy(Request $request, $idtienda, $id)
    {
        //
    }
}
