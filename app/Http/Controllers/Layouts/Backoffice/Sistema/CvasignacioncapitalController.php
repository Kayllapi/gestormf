<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use PDF;
use Carbon\Carbon;

class CvasignacioncapitalController extends Controller
{
    public function __construct()
    {
       
    }
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            
            return view(sistema_view().'/cvasignacioncapital/tabla',[
              'tienda' => $tienda
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        if($request->view == 'registrar') {
            
            $agencias = DB::table('tienda')->get();
            $bancos = DB::table('banco')->where('estado','ACTIVO')->get();
            $tipooperacions= DB::table('credito_tipooperacion')->get();
            $tipodestinos = DB::table('credito_tipodestino')->get();
          
            return view(sistema_view().'/cvasignacioncapital/create',[
                'tienda' => $tienda,
                'agencias' => $agencias,
                'bancos' => $bancos,
                'tipooperacions' => $tipooperacions,
                'tipodestinos' => $tipodestinos,
            ]);
        }
        /*else if($request->input('view') == 'reporte_saldocapitalasignado') {
          
            $asignacioncapital = DB::table('asignacioncapital')
                ->join('credito_tipooperacion','credito_tipooperacion.id','asignacioncapital.idtipooperacion')
                ->join('credito_tipodestino','credito_tipodestino.id','asignacioncapital.idtipodestino')
                ->join('users as responsable','responsable.id','asignacioncapital.idresponsable')
                ->join('tienda','tienda.id','asignacioncapital.idtienda')
                ->where('asignacioncapital.idestadoeliminado',1)
                ->where('asignacioncapital.idtienda',$request->idtienda)
                ->select(
                    'asignacioncapital.*',
                    'credito_tipooperacion.nombre as credito_tipooperacionnombre',
                    'credito_tipodestino.nombre as credito_tipodestinonombre',
                    'tienda.nombre as tiendanombre',
                    'responsable.codigo as codigo_responsable',
                )
                ->orderBy('asignacioncapital.id','ASC')
                ->get();
  
            $total = 0;
            $total_caja = 0;
            $total_banco = 0;
            $html = '';
            foreach($asignacioncapital as $key => $value){
                $fecharegistro = date_format(date_create($value->fecharegistro),"d-m-Y H:i:s A");
                $cuenta = $value->banco!=''?$value->banco.' - ***'.substr($value->cuenta, -5):'';
                $numerooperacion = $value->banco!=''?'('.$value->numerooperacion.')':'';
                $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data(this)'>
                              <td>{$value->tiendanombre}</td>
                              <td>{$fecharegistro}</td>
                              <td>{$value->codigoprefijo}{$value->codigo}</td>
                              <td>{$value->credito_tipooperacionnombre}</td>
                              <td>{$value->credito_tipodestinonombre}</td>
                              <td style='text-align: right;'>{$value->monto}</td>
                              <td>{$cuenta}</td>
                              <td>{$numerooperacion}</td>
                              <td>{$value->descripcion}</td>
                              <td>{$value->codigo_responsable}</td>
                          </tr>";
                $total = $total+$value->monto;
                if($value->idformapago==1){
                    $total_caja = $total_caja+$value->monto;
                }elseif($value->idformapago==2){
                    $total_banco = $total_banco+$value->monto;
                }
            }
          
            if(count($asignacioncapital)==0){
                $html.= '<tr><td colspan="10" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
            }
          
            return view(sistema_view().'/asignacioncapital/reporte_saldocapitalasignado',[
                'tienda' => $tienda,
                'html' => $html,
            ]);
        }*/
    }
  
    public function store(Request $request, $idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'registrar') {
            $rules = [
                'idagencia' => 'required', 
                'idtipooperacion' => 'required', 
                'monto' => 'required', 
                'descripcion' => 'required',        
            ];
          
            $messages = [
                'idagencia.required' => 'La "Agencia" es Obligatorio.',
                'idtipooperacion.required' => 'El "Tipo de Operación" es Obligatorio.',
                'monto.required' => 'El "Monto" es Obligatorio.',
                'descripcion.required' => 'La "Descricpión" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
          
            if($request->idtipooperacion==1 || $request->idtipooperacion==2 || $request->idtipooperacion==4){
                $rules = [
                    'idtipodestino' => 'required',                       
                ];

                $messages = [
                    'idtipodestino.required' => 'El "Destino/Fuente Depósito/Retiro" es Obligatorio.',
                ];
                $this->validate($request,$rules,$messages);
            }
          
            if($request->idtipodestino==3){
                $rules = [
                    'idbanco' => 'required',                  
                    'numerooperacion' => 'required',                       
                ];

                $messages = [
                    'idbanco.required' => 'El Campo Banco es Obligatorio.',
                    'numerooperacion.required' => 'El Campo Número de Operación es Obligatorio.',
                ];
                $this->validate($request,$rules,$messages);
            }
          
            if(0>=$request->monto){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Monto minímo debe ser 0.00.'
                ]);
            }
          
            $cvconsolidadooperaciones = cvconsolidadooperaciones($tienda,$idtienda,now()->format('Y-m-d'));
            if($request->idtipooperacion==2){
                if($request->idtipodestino==1){
                    if($cvconsolidadooperaciones['saldos_caja']<$request->monto){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay saldo suficiente en CAJA.<br><b>Saldo Actual: S/. '.$cvconsolidadooperaciones['saldos_caja'].'.</b>'
                        ]);
                    }
                }
                elseif($request->idtipodestino==2){
                    if($cvconsolidadooperaciones['saldos_reserva']<$request->monto){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay saldo suficiente en RESERVA CF.<br><b>Saldo Actual: S/. '.$cvconsolidadooperaciones['saldos_reserva'].'.</b>'
                        ]);
                    }
                }
                elseif($request->idtipodestino==3){
                    foreach($cvconsolidadooperaciones['saldos_cuentabanco_bancos'] as $value){
                        if($value['banco_id']==$request->idbanco && $value['banco']<$request->monto){
                            return response()->json([
                                'resultado' => 'ERROR',
                                'mensaje'   => 'No hay saldo suficiente en Cuenta Bancaria.<br><b>Saldo Actual: S/. '.$value['banco'].'.</b>'
                            ]);
                        }
                    }  
                }
            }
            elseif($request->idtipooperacion==3){
                if($cvconsolidadooperaciones['saldos_capitalasignada']<$request->monto){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'No hay saldo suficiente en Capital Asignado.<br><b>Saldo Actual: S/. '.$cvconsolidadooperaciones['saldos_capitalasignada'].'.</b>'
                    ]);
                }
            }
            
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha validado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'registrar_insert') {
            
            if($request->idtipooperacion==3){
                $request->idtipodestino = 0;
            }
          
            $asignacioncapital = DB::table('cvasignacioncapital')
                ->orderBy('cvasignacioncapital.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($asignacioncapital!=''){
                $codigo = $asignacioncapital->codigo+1;
            }

            $bancoo = DB::table('banco')->where('banco.id',$request->idbanco)->first();

            $banco = '';
            $cuenta = '';
            if($bancoo!=''){
                $banco = $bancoo->nombre;
                $cuenta = $bancoo->cuenta;
            }
          
            DB::table('cvasignacioncapital')->insert([
                'fecharegistro' => now(),
                'codigoprefijo' => 'OV',
                'codigo' => $codigo,
                'monto' => $request->input('monto'),
                'descripcion' => $request->input('descripcion'),
              
                'numerooperacion' => $request->numerooperacion!=''?$request->numerooperacion:'',
                'banco' => $banco,
                'cuenta' => $cuenta,
              
                'idbanco' => $request->idbanco!=''?$request->idbanco:0,
                'idtipooperacion' => $request->idtipooperacion,
                'idtipodestino' => $request->idtipodestino,
                'idresponsable' => $request->idresponsable_registro,
                'idresponsable_permiso' => $request->idresponsable_registro_idpermiso,
                'idtienda' => $request->idagencia,
                'idestadoeliminado' => 1,
                'idestado' => 1,
            ]);

          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        if($id=='show_table'){
            $where = [];
            if($request->input('fechainicio') != ''){
                $where[] = ['cvasignacioncapital.fecharegistro','>=',$request->fechainicio.' 00:00:00'];
            }
            if($request->input('fechafin') != ''){
                $where[] = ['cvasignacioncapital.fecharegistro','<=',$request->fechafin.' 23:59:59']; 
            }
          
            $asignacioncapital = DB::table('cvasignacioncapital')
                ->join('credito_tipooperacion','credito_tipooperacion.id','cvasignacioncapital.idtipooperacion')
                ->leftJoin('credito_tipodestino','credito_tipodestino.id','cvasignacioncapital.idtipodestino')
                ->join('users as responsable','responsable.id','cvasignacioncapital.idresponsable')
                ->leftJoin('users as responsable_recfinal','responsable_recfinal.id','cvasignacioncapital.idresponsable_recfinal')
                ->join('tienda','tienda.id','cvasignacioncapital.idtienda')
                ->where('cvasignacioncapital.idestadoeliminado',1)
                //->where('cvasignacioncapital.idresponsable_recfinal'.'<>',0)
                ->where($where)
                ->select(
                    'cvasignacioncapital.*',
                    'credito_tipooperacion.nombre as credito_tipooperacionnombre',
                    'credito_tipodestino.nombre as credito_tipodestinonombre',
                    'tienda.nombreagencia as tiendanombre',
                    'responsable.codigo as codigo_responsable',
                    'responsable_recfinal.codigo as codigo_responsable_recfinal',
                )
                ->orderBy('cvasignacioncapital.id','ASC')
                ->get();
  
            $total = 0;
            $total_suma = 0;
            $total_resta = 0;
            $html = '';
            foreach($asignacioncapital as $key => $value){
              
                $style = '';
                $subrayado = '';
                $subrayado_sincolor = '';
                $signo = '';
              
                if($value->idtipooperacion==2 or $value->idtipooperacion==3){
                    $style = 'color:#b71c1b;font-weight: bold;';
                    $subrayado = 'border-bottom: 3px double #b71c1b;font-weight: bold;';
                    $subrayado_sincolor = 'border-bottom: 3px double black;font-weight: bold;';
                    $signo = '-';
                }
              
                $fecharegistro = date_format(date_create($value->fecharegistro),"d-m-Y H:i:s A");
                $cuenta = $value->banco!=''?$value->banco.' - ***'.substr($value->cuenta, -5):'';
                $numerooperacion = $value->banco!=''?$value->numerooperacion:'';
                   
                $btn_validar = '';
                if($value->idtipodestino==3){
                    $btn_validar = "<button type='button' class='btn btn-success' onclick='validar({$value->id})'><i class='fa-solid fa-check'></i> Validar</button>";
                    if($value->validar_estado==1){
                        $users = DB::table('users')->whereId($value->validar_responsable)->first();
                        $btn_validar = "<i class='fa-solid fa-check'></i> (".$users->codigo.")";
                    }
                } 
              
                $html .= "<tr data-valor-columna='{$value->id}' idresponsable_recfinal='{$value->idresponsable_recfinal}' onclick='show_data(this)'>
                              <td>{$value->tiendanombre}</td>
                              <td>{$fecharegistro}</td>
                              <td>{$value->codigoprefijo}{$value->codigo}</td>
                              <td><span style='{$subrayado_sincolor}'>{$value->credito_tipooperacionnombre}</span></td>
                              <td>{$value->credito_tipodestinonombre}</td>
                              <td style='text-align: right;{$style}'><span style='{$subrayado}'>{$signo}{$value->monto}</span></td>
                              <td>{$cuenta}</td>
                              <td style='width: 100px;'>{$btn_validar}</td>
                              <td>{$numerooperacion}</td>
                              <td>{$value->descripcion}</td>
                              <td>{$value->codigo_responsable}</td>
                              <td style='".($value->codigo_responsable_recfinal == '' ? 'background-color: #ffd374ff !important;' : '')."'>{$value->codigo_responsable_recfinal}</td>
                          </tr>";
                $total = $total+$value->monto;
                if($value->idtipooperacion==1 or $value->idtipooperacion==4){
                    $total_suma = $total_suma+$value->monto;
                }elseif($value->idtipooperacion==2 or $value->idtipooperacion==3){
                    $total_resta = $total_resta+$value->monto;
                }
            }
          
            if(count($asignacioncapital)==0){
                $html.= '<tr><td colspan="12" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
            }
               
              $html .= '
                <tr style="bottom: 0;">
                  <td colspan="5" style="background-color: #c2c0c2 !important;text-align:right; font-weight: bold;">Total (S/.)</td>
                  <td style="background-color: #c2c0c2 !important;text-align:right; font-weight: bold;">'.number_format($total_suma-$total_resta, 2, '.', '').'</td>
                  <td colspan="6" style="background-color: #c2c0c2 !important;text-align:right; font-weight: bold;"></td>
                </tr>'; 
            return array(
              'html' => $html
            );
        }
        elseif($id=='show_saldocapitalasignado'){

            $where = [];
            if($request->input('fechainicio') != ''){
                $where[] = ['cvasignacioncapital.fecharegistro','>=',$request->fechainicio.' 00:00:00'];
            }
            if($request->input('fechafin') != ''){
                $where[] = ['cvasignacioncapital.fecharegistro','<=',$request->fechafin.' 23:59:59']; 
            }
          
            $tiendas = DB::table('tienda')
                ->orderBy('tienda.nombre','ASC')
                ->get();
  
            $html = '';
            foreach($tiendas as $key => $value){
              
                $monto_suma = DB::table('cvasignacioncapital')
                    ->where('cvasignacioncapital.idestadoeliminado',1)
                    ->whereIn('cvasignacioncapital.idtipooperacion',[1,4])
                    ->where('cvasignacioncapital.idtienda',$value->id)
                    ->where('cvasignacioncapital.idresponsable_recfinal','<>',0)
                    //->where($where)
                    ->sum('cvasignacioncapital.monto');
              
                $monto_resta = DB::table('cvasignacioncapital')
                    ->where('cvasignacioncapital.idestadoeliminado',1)
                    ->whereIn('cvasignacioncapital.idtipooperacion',[2,3])
                    ->where('cvasignacioncapital.idtienda',$value->id)
                    ->where('cvasignacioncapital.idresponsable_recfinal','<>',0)
                    //->where($where)
                    ->sum('cvasignacioncapital.monto');
                
                $monto = number_format($monto_suma-$monto_resta, 2, '.', '');  
              
                $html .= "<tr>
                              <td>{$value->nombreagencia}</td>
                              <td style='text-align: right;'>{$monto}</td>
                              <td><button type='button' class='btn btn-primary' onclick='reporte_saldocapitalasignado({$value->id})' 
                              style='font-weight: bold;'>
                              <i class='fa-solid fa-list'></i> Reporte</button></td>
                          </tr>";
            }
            return array(
              'html' => $html
            );
        }
        
    }

    public function edit(Request $request, $idtienda, $id)
    {
        
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        
        $asignacioncapital = DB::table('cvasignacioncapital')
                      ->where('cvasignacioncapital.id',$id)
                      ->select(
                          'cvasignacioncapital.*',
                      )
                      ->orderBy('cvasignacioncapital.id','desc')
                      ->first();
      
        if($request->input('view') == 'editar'){

              $agencias = DB::table('tienda')->get();
              $bancos = DB::table('banco')->where('estado','ACTIVO')->get();
              $tipooperacions= DB::table('credito_tipooperacion')->get();
              $tipodestinos = DB::table('credito_tipodestino')->get();

              return view(sistema_view().'/cvasignacioncapital/edit',[
                  'tienda' => $tienda,
                  'agencias' => $agencias,
                  'bancos' => $bancos,
                  'tipooperacions' => $tipooperacions,
                  'tipodestinos' => $tipodestinos,
                  'asignacioncapital' => $asignacioncapital,
              ]);
        } 
        elseif($request->input('view') == 'eliminar'){
            
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();
        
        return view(sistema_view().'/cvasignacioncapital/delete',[
          'tienda' => $tienda,
          'asignacioncapital' => $asignacioncapital,
          'usuarios' => $usuarios,
        ]);
      }
        elseif($request->input('view') == 'valid_registro') {
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->where('users_permiso.idpermiso',2)
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.id as idpermiso','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/cvasignacioncapital/valid_registro',[
                'tienda' => $tienda,
                'usuarios' => $usuarios,
            ]);
        }
        elseif($request->input('view') == 'recepcionar') {
            //dd($asignacioncapital->idtipooperacion);
            $where = [];
            if($asignacioncapital->idtipooperacion==1){
                if($asignacioncapital->idtipodestino==1){
                    $where = [4];
                }
                elseif($asignacioncapital->idtipodestino==2){
                    $where = [1];
                }
                elseif($asignacioncapital->idtipodestino==3){
                    $where = [2];
                }
            }
            elseif($asignacioncapital->idtipooperacion==2){
                $where = [2];
            }
            elseif($asignacioncapital->idtipooperacion==3){
                $where = [2];
            }
            elseif($asignacioncapital->idtipooperacion==4){
                if($asignacioncapital->idtipodestino==1){
                    $where = [4];
                }
                elseif($asignacioncapital->idtipodestino==2){
                    $where = [1];
                }
                elseif($asignacioncapital->idtipodestino==3){
                    $where = [2];
                }
            }
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',$where)
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.id as idpermiso','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/cvasignacioncapital/recepcionar',[
                'tienda' => $tienda,
                'usuarios' => $usuarios,
                'asignacioncapital' => $asignacioncapital,
            ]);
        }
        else if($request->input('view') == 'exportar') {
            return view(sistema_view().'/cvasignacioncapital/exportar',[
                'tienda' => $tienda,
                'fechainicio' => $request->fechainicio,
                'fechafin' => $request->fechafin,
            ]);
        }
        else if( $request->input('view') == 'exportar_pdf' ){
              
            if($request->input('fechainicio') != ''){
                $where[] = ['cvasignacioncapital.fecharegistro','>=',$request->fechainicio.' 00:00:00'];
            }
            if($request->input('fechafin') != ''){
                $where[] = ['cvasignacioncapital.fecharegistro','<=',$request->fechafin.' 23:59:59']; 
            }
          
            $asignacioncapital = DB::table('cvasignacioncapital')
                ->join('credito_tipooperacion','credito_tipooperacion.id','cvasignacioncapital.idtipooperacion')
                ->join('credito_tipodestino','credito_tipodestino.id','cvasignacioncapital.idtipodestino')
                ->join('users as responsable','responsable.id','cvasignacioncapital.idresponsable')
                ->join('tienda','tienda.id','cvasignacioncapital.idtienda')
                ->where('cvasignacioncapital.idestadoeliminado',1)
                ->where($where)
                ->select(
                    'cvasignacioncapital.*',
                    'credito_tipooperacion.nombre as credito_tipooperacionnombre',
                    'credito_tipodestino.nombre as credito_tipodestinonombre',
                    'tienda.nombre as tiendanombre',
                    'responsable.codigo as codigo_responsable',
                )
                ->orderBy('cvasignacioncapital.id','ASC')
                ->get();
        
            $pdf = PDF::loadView(sistema_view().'/cvasignacioncapital/exportar_pdf',[
                'tienda' => $tienda,
                'asignacioncapital' => $asignacioncapital,
                'fechainicio' => $request->fechainicio,
                'fechafin' => $request->fechafin,
            ]); 
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('GESTION_COBRANZA.pdf');
        } 
        else if( $request->input('view') == 'reporte_saldocapitalasignado') {
          
            return view(sistema_view().'/cvasignacioncapital/reporte_saldocapitalasignado',[
                'tienda' => $tienda,
                'idagencia' => $request->idagencia,
            ]);
        }
        else if( $request->input('view') == 'reporte_saldocapitalasignado_pdf' ){
              
            $where = [];
          
            if($request->input('idagencia') != ''){
                $where[] = ['cvasignacioncapital.idtienda',$request->idagencia];
            }
          
            $asignacioncapital = DB::table('cvasignacioncapital')
                ->join('credito_tipooperacion','credito_tipooperacion.id','cvasignacioncapital.idtipooperacion')
                ->leftJoin('credito_tipodestino','credito_tipodestino.id','cvasignacioncapital.idtipodestino')
                ->join('users as responsable','responsable.id','cvasignacioncapital.idresponsable')
                ->leftJoin('users as responsable_recfinal','responsable_recfinal.id','cvasignacioncapital.idresponsable_recfinal')
                ->join('tienda','tienda.id','cvasignacioncapital.idtienda')
                ->where('cvasignacioncapital.idestadoeliminado',1)
                ->where($where)
                ->select(
                    'cvasignacioncapital.*',
                    'credito_tipooperacion.nombre as credito_tipooperacionnombre',
                    'credito_tipodestino.nombre as credito_tipodestinonombre',
                    'tienda.nombreagencia as tiendanombre',
                    'responsable.codigo as codigo_responsable',
                    'responsable_recfinal.codigo as codigo_responsable_recfinal',
                )
                ->orderBy('cvasignacioncapital.id','ASC')
                ->get();
          
            $agencia = DB::table('tienda')->whereId($request->idagencia)->first();
        
            $pdf = PDF::loadView(sistema_view().'/cvasignacioncapital/reporte_saldocapitalasignado_pdf',[
                'tienda' => $tienda,
                'asignacioncapital' => $asignacioncapital,
                'agencia' => $agencia,
            ]); 
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('REPORTE.pdf');
        } 

        else if($request->input('view') == 'voucher') {
            return view(sistema_view().'/cvasignacioncapital/voucher',[
              'tienda' => $tienda,
              'idasignacioncapital' => $id,
            ]);
        }
        else if( $request->input('view') == 'voucher_pdf' ){
              
            $asignacioncapital = DB::table('cvasignacioncapital')
                ->join('credito_tipooperacion','credito_tipooperacion.id','cvasignacioncapital.idtipooperacion')
                ->leftJoin('credito_tipodestino','credito_tipodestino.id','cvasignacioncapital.idtipodestino')
                ->join('users as responsable','responsable.id','cvasignacioncapital.idresponsable')
                //->join('users_permiso as users_permiso_responsable','users_permiso_responsable.idusers','responsable.id')
                ->join('permiso as permiso_responsable','permiso_responsable.id','cvasignacioncapital.idresponsable_permiso')
                ->leftJoin('users as responsable_recfinal','responsable_recfinal.id','cvasignacioncapital.idresponsable_recfinal')
                //->join('users_permiso as users_permiso_responsable_recfinal','users_permiso_responsable_recfinal.idusers','responsable_recfinal.id')
                ->join('permiso as permiso_responsable_recfinal','permiso_responsable_recfinal.id','cvasignacioncapital.idresponsable_permiso_recfinal')
                ->where('cvasignacioncapital.id',$id)
                ->select(
                    'cvasignacioncapital.*',
                    'credito_tipooperacion.nombre as credito_tipooperacionnombre',
                    'credito_tipodestino.nombre as credito_tipodestinonombre',
                    'responsable.nombrecompleto as nombrecompleto_responsable',
                    'responsable.codigo as codigo_responsable',
                    'permiso_responsable.nombre as nombrepermiso_responsable',
                    'responsable_recfinal.nombrecompleto as nombrecompleto_responsable_recfinal',
                    'responsable_recfinal.codigo as codigo_responsable_recfinal',
                    'permiso_responsable_recfinal.nombre as nombrepermiso_responsable_recfinal',
                )
                ->first();
          
          
            $pdf = PDF::loadView(sistema_view().'/cvasignacioncapital/voucher_pdf',[
                'tienda' => $tienda,
                'asignacioncapital' => $asignacioncapital,
            ]); 
            $pdf->setPaper('A4');
            return $pdf->stream('VOUCHER.pdf');
        }   
        elseif($request->input('view') == 'validar') {

            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[1,4])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.id as idpermiso','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/cvasignacioncapital/validar',[
                'tienda' => $tienda,
                'usuarios' => $usuarios,
                'idasignacioncapital' => $id,
            ]);
        }
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        if($request->input('view') == 'editar') {

            $rules = [
                'nombre' => 'required',               
            ];
          
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('cvasignacioncapital')->whereId($id)->update([
               'nombre' => $request->input('nombre'),
               'estado' => $request->input('estado'),
            ]);
          
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'valid_registro'){
            $rules = [
                'idresponsable' => 'required',          
                'responsableclave' => 'required',              
            ];

            $messages = [
                'idresponsable.required' => 'El "Responsable" es Obligatorio.',
                'responsableclave.required' => 'La "Contraseña" es Obligatorio.',
            ];

            $this->validate($request,$rules,$messages);

            $usuario = DB::table('users')
                ->where('users.id',$request->idresponsable)
                ->where('users.clave',$request->responsableclave)
                ->first();
            $idresponsable = 0;
            if($usuario!=''){
                $idresponsable = $usuario->id;
            }else{
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El usuario y/o la contraseña es incorrecta!!.'
                ]);
            }
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha validado correctamente.',
              'idresponsable'   => $idresponsable
            ]);
        }
        elseif($request->input('view') == 'recepcionar'){
            $rules = [
                'idresponsable' => 'required',          
                'responsableclave' => 'required',              
            ];

            $messages = [
                'idresponsable.required' => 'El "Responsable" es Obligatorio.',
                'responsableclave.required' => 'La "Contraseña" es Obligatorio.',
            ];

            $this->validate($request,$rules,$messages);

            $usuario = DB::table('users')
                ->where('users.id',$request->idresponsable)
                ->where('users.clave',$request->responsableclave)
                ->first();
            $idresponsable = 0;
            if($usuario!=''){
                $idresponsable = $usuario->id;
            }else{
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El usuario y/o la contraseña es incorrecta!!.'
                ]);
            }
          
            
            DB::table('cvasignacioncapital')->whereId($id)->update([
               'idresponsable_recfinal' => $idresponsable,
               'idresponsable_permiso_recfinal' => $request->idpermiso,
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha recepcionado correctamente.',
            ]);
        }
      
        elseif($request->input('view') == 'validar'){
            $rules = [
                'idresponsable' => 'required',          
                'responsableclave' => 'required',              
            ];

            $messages = [
                'idresponsable.required' => 'El "Responsable" es Obligatorio.',
                'responsableclave.required' => 'La "Contraseña" es Obligatorio.',
            ];

            $this->validate($request,$rules,$messages);

            $usuario = DB::table('users')
                ->where('users.id',$request->idresponsable)
                ->where('users.clave',$request->responsableclave)
                ->first();
            $idresponsable = 0;
            if($usuario!=''){
                $idresponsable = $usuario->id;
            }else{
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El usuario y/o la contraseña es incorrecta!!.'
                ]);
            }
          
            DB::table('cvasignacioncapital')->whereId($id)->update([
                'validar_estado' => 1,
                'validar_responsable' => $request->idresponsable,
                'validar_responsable_permiso' => $request->idresponsable_permiso,
                'validar_fecha' => now(),
            ]);
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha validado correctamente.',
              'idresponsable'   => $idresponsable
            ]);
        }
    }


    public function destroy(Request $request, $idtienda, $id)
    {
        if( $request->input('view') == 'eliminar' ){
            $rules = [
                'idresponsable' => 'required',
                'responsableclave' => 'required',
            ];
            $messages = [
                'idresponsable.required' => 'El "Responsable" es Obligatorio.',
                'responsableclave.required' => 'La "Contraseña" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            $usuario = DB::table('users')
                ->where('users.id',$request->idresponsable)
                ->where('users.clave',$request->responsableclave)
                ->first();
            $idresponsable = 0;
            if($usuario!=''){
                $idresponsable = $usuario->id;
            }else{
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El usuario y/o la contraseña es incorrecta!!.'
                ]);
            }

            DB::table('cvasignacioncapital')->whereId($id)->update([
               'fecha_eliminado' => now(),
               'idestadoeliminado' => 2,
               'idresponsble_eliminado' => $idresponsable,
            ]);

            // DB::table('cvasignacioncapital')->whereId($id)->delete();
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha elimino correctamente.'
            ]);
        }
    }
}
