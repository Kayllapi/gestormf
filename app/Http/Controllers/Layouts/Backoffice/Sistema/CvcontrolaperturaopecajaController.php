<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class CvcontrolaperturaopecajaController extends Controller
{
    public function __construct()
    {
        //
    }
    public function index(Request $request,$idtienda)
    {
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[1])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.id as idpermiso','permiso.nombre as nombrepermiso')
                ->get();
          
            $tiendas = DB::table('tienda')
                ->orderBy('tienda.id','asc')
                ->get();

            $valid_apertura = 0;
            $valid_arqueo = 0;
            $valid_cierre = 0;
            foreach($tiendas as $value){
                $ret_reservacf_caja_total = DB::table('cvmovimientointernodinero')
                    ->where('cvmovimientointernodinero.idestadoeliminado',1)
                    ->where('cvmovimientointernodinero.idfuenteretiro',6)
                    ->where('cvmovimientointernodinero.idtipomovimientointerno',5)
                    ->where('cvmovimientointernodinero.fecharegistro','>=',$request->fecha_corte.' 00:00:00')
                    ->where('cvmovimientointernodinero.fecharegistro','<=',$request->fecha_corte.' 23:59:59')
                    ->where('cvmovimientointernodinero.idtienda',$value->id)
                    ->first();

                if($ret_reservacf_caja_total){
                    $cierre_insitucionaldetalle_1 = DB::table('cvcierre_insitucionaldetalle')
                        ->where('cvcierre_insitucionaldetalle.idaperturacaja',$ret_reservacf_caja_total->id)
                        ->first();
                    if(!$cierre_insitucionaldetalle_1){
                        $valid_apertura++; 
                    }           
                }

                $arqueocaja = DB::table('cvarqueocaja')
                    ->where('idagencia',$value->id)
                    ->where('corte',$request->fecha_corte)
                    ->first();

                if($arqueocaja && $ret_reservacf_caja_total){
                    $cierre_insitucionaldetalle_2 = DB::table('cvcierre_insitucionaldetalle')
                        ->where('cvcierre_insitucionaldetalle.idarqueocaja',$arqueocaja->id)
                        ->first();
                    if(!$cierre_insitucionaldetalle_2){
                        $valid_arqueo++; 
                    }
                }

                $ret_caja_reservacf_total = DB::table('cvmovimientointernodinero')
                    ->where('cvmovimientointernodinero.idestadoeliminado',1)
                    ->where('cvmovimientointernodinero.idfuenteretiro',8)
                    ->where('cvmovimientointernodinero.idtipomovimientointerno',5)
                    ->where('cvmovimientointernodinero.fecharegistro','>=',$request->fecha_corte.' 00:00:00')
                    ->where('cvmovimientointernodinero.fecharegistro','<=',$request->fecha_corte.' 23:59:59')
                    ->where('cvmovimientointernodinero.idtienda',$value->id)
                    ->first();

                if($ret_caja_reservacf_total && $ret_reservacf_caja_total){
                    $cierre_insitucionaldetalle_3 = DB::table('cvcierre_insitucionaldetalle')
                        ->where('cvcierre_insitucionaldetalle.idcierrecaja',$ret_caja_reservacf_total->id)
                        ->first();
                    if(!$cierre_insitucionaldetalle_3){
                        $valid_cierre++; 
                    }
                }    
            }
          
            $estado_cierre_institucional = '';
            if($valid_apertura>0 && $valid_cierre==0){
                $estado_cierre_institucional = 'PENDIENTE';
            }elseif($valid_apertura>0 && $valid_arqueo==0){
                $estado_cierre_institucional = 'PENDIENTE';
            }elseif($valid_apertura==0){
                $estado_cierre_institucional = 'NOEXISTE';
            }
            
            $agencias = DB::table('tienda')->get();
          
            return view(sistema_view().'/cvcontrolaperturaopecaja/tabla',[
              'tienda' => $tienda,
              'agencias' => $agencias,
              'estado_cierre_institucional' => $estado_cierre_institucional,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
    }
  
    public function store(Request $request, $idtienda)
    {
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'showtable'){

          $tiendas = DB::table('tienda')
              ->orderBy('tienda.id','asc')
              ->get();
          
          $html = '<table class="table table-hover table-bordered" id="table-lista-credito">
              <thead class="table-dark" style="position: sticky;top: 0;">
                <tr>
                  <td rowspan="2" style="text-align:center; font-weight: bold;">N°</td>
                  <td rowspan="2" style="text-align:center; font-weight: bold;">Agencia</td>
                  <td colspan="3" style="text-align:center; font-weight: bold;">APERTURA DE CAJA</td>
                  <td colspan="2" style="text-align:center; font-weight: bold;">ARQUEO DE CAJA</td>
                  <td colspan="3" style="text-align:center; font-weight: bold;">CIERRE DE CAJA</td>
                  <td rowspan="2" style="text-align:center; font-weight: bold;">Usuario</td>
                </tr>
                <tr>
                  <td style="text-align:center; font-weight: bold;">Estado</td>
                  <td style="text-align:center; font-weight: bold;">Efectivo (S/.)</td>
                  <td style="text-align:center; font-weight: bold;">Fecha y Hora</td>
                  <td style="text-align:center; font-weight: bold;">Efectivo (S/.)</td>
                  <td style="text-align:center; font-weight: bold;">Fecha y Hora</td>
                  <td style="text-align:center; font-weight: bold;">Estado</td>
                  <td style="text-align:center; font-weight: bold;">Fecha y Hora</td>
                  <td style="text-align:center; font-weight: bold;">Administrador de Agencia  (A. y N.)</td>
                </tr>
              </thead>
              <tbody>';
          $i = 1;
          foreach($tiendas as $value){
            
              $ret_reservacf_caja_total = DB::table('cvmovimientointernodinero')
                  ->where('cvmovimientointernodinero.idestadoeliminado',1)
                  ->where('cvmovimientointernodinero.idfuenteretiro',6)
                  ->where('cvmovimientointernodinero.idtipomovimientointerno',5)
                  ->where('cvmovimientointernodinero.fecharegistro','>=',$request->fecha.' 00:00:00')
                  ->where('cvmovimientointernodinero.fecharegistro','<=',$request->fecha.' 23:59:59')
                  ->where('cvmovimientointernodinero.idtienda',$value->id)
                  ->first();
            
              $estado_apertura = 'NO ABIERTO';
              $efectivo_apertura = 0;
              $fechahora_apertura = '';
              if($ret_reservacf_caja_total){
                  $estado_apertura = 'ABIERTO';    
                  $efectivo_apertura = $ret_reservacf_caja_total->monto;
                  $fechahora_apertura = date_format(date_create($ret_reservacf_caja_total->fecharegistro),"d-m-Y h:i:s A");            
              }
            
              $arqueocaja = DB::table('cvarqueocaja')
                  ->where('idagencia',$value->id)
                  ->where('corte',$request->fecha)
                  ->first();
            
              $efectivo_arqueocaja = 0;
              $fechahora_arqueocaja = '';
              if($arqueocaja && $ret_reservacf_caja_total){
                  $efectivo_arqueocaja = $arqueocaja->total;
                  $fechahora_arqueocaja = date_format(date_create($arqueocaja->fecharegistro),"d-m-Y h:i:s A");
              }
            
              $ret_caja_reservacf_total = DB::table('cvmovimientointernodinero')
                ->join('users as responsable','responsable.id','cvmovimientointernodinero.idresponsable')
                  ->where('cvmovimientointernodinero.idestadoeliminado',1)
                  ->where('cvmovimientointernodinero.idfuenteretiro',8)
                  ->where('cvmovimientointernodinero.idtipomovimientointerno',5)
                  ->where('cvmovimientointernodinero.fecharegistro','>=',$request->fecha.' 00:00:00')
                  ->where('cvmovimientointernodinero.fecharegistro','<=',$request->fecha.' 23:59:59')
                  ->where('cvmovimientointernodinero.idtienda',$value->id)
                  ->select(
                      'cvmovimientointernodinero.*',
                      'responsable.nombrecompleto as nombrecompleto_responsable',
                      'responsable.codigo as usuario_responsable',
                  )
                  ->first();
            
              $fechahora_cierre = '';
              $responsable_cierre = '';
              $usuario_cierre = '';
              if($ret_caja_reservacf_total && $ret_reservacf_caja_total){
                  $responsable_cierre = $ret_caja_reservacf_total->nombrecompleto_responsable;
                  $usuario_cierre = $ret_caja_reservacf_total->usuario_responsable;
                  $fechahora_cierre = date_format(date_create($ret_caja_reservacf_total->fecharegistro),"d-m-Y h:i:s A");
              }
            
              $estado_cierre = '';
              if(!$ret_reservacf_caja_total){
                    $color = "#fce092";
                    $estado_cierre = 'SIN APERTURA';
              }
              elseif(($ret_reservacf_caja_total && !$arqueocaja) || 
                     ($ret_reservacf_caja_total && !$ret_caja_reservacf_total)){
                    $color = "#ffc9ca";
                    $estado_cierre = 'PENDIENTE';
              }
              elseif($ret_reservacf_caja_total && $arqueocaja && $ret_caja_reservacf_total){
                    $color = "#3cd48d";
                    $estado_cierre = 'CERRADA';
              }
            
              $html .= '<tr id="show_data_select" idtienda="{$value->id}">
                            <td style="text-align:center;">'.$i.'</td>
                            <td>'.$value->nombreagencia.'</td>
                            <td>'.$estado_apertura.'</td>
                            <td style="text-align:right;">'.number_format($efectivo_apertura,2,'.','').'</td>
                            <td style="text-align:center;">'.$fechahora_apertura.'</td>
                            <td style="text-align:right;">'.number_format($efectivo_arqueocaja,2,'.','').'</td>
                            <td style="text-align:center;">'.$fechahora_arqueocaja.'</td>
                            <td style="background-color: '.$color.'">'.$estado_cierre.'</td>
                            <td style="text-align:center;">'.$fechahora_cierre.'</td>
                            <td>'.$responsable_cierre.'</td>
                            <td>'.$usuario_cierre.'</td>
                        </tr>';
             $i++;     
          }
          if(count($tiendas)==0){
              $html.= '<tr><td colspan="11" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
          }
              $html .= '</tbody>
            </table>';
          return array(
            'html' => $html,
            'html1' => ''
          );
          
        }

    }

    public function edit(Request $request, $idtienda, $id)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first(); 

        if($request->input('view') == 'cierre') {
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[1])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.id as idpermiso','permiso.nombre as nombrepermiso')
                ->get();
          
            $tiendas = DB::table('tienda')
                ->orderBy('tienda.id','asc')
                ->get();

            $valid_apertura = 0;
            $valid_arqueo = 0;
            $valid_cierre = 0;
            foreach($tiendas as $value){
                $ret_reservacf_caja_total = DB::table('cvmovimientointernodinero')
                    ->where('cvmovimientointernodinero.idestadoeliminado',1)
                    ->where('cvmovimientointernodinero.idfuenteretiro',6)
                    ->where('cvmovimientointernodinero.idtipomovimientointerno',5)
                    ->where('cvmovimientointernodinero.fecharegistro','>=',$request->fecha_corte.' 00:00:00')
                    ->where('cvmovimientointernodinero.fecharegistro','<=',$request->fecha_corte.' 23:59:59')
                    ->where('cvmovimientointernodinero.idtienda',$value->id)
                    ->first();

                if($ret_reservacf_caja_total){
                    $cierre_insitucionaldetalle_1 = DB::table('cvcierre_insitucionaldetalle')
                        ->where('cvcierre_insitucionaldetalle.idaperturacaja',$ret_reservacf_caja_total->id)
                        ->first();
                    if(!$cierre_insitucionaldetalle_1){
                        $valid_apertura++; 
                    }           
                }

                $arqueocaja = DB::table('cvarqueocaja')
                    ->where('idagencia',$value->id)
                    ->where('corte',$request->fecha_corte)
                    ->first();

                if($arqueocaja && $ret_reservacf_caja_total){
                    $cierre_insitucionaldetalle_2 = DB::table('cvcierre_insitucionaldetalle')
                        ->where('cvcierre_insitucionaldetalle.idarqueocaja',$arqueocaja->id)
                        ->first();
                    if(!$cierre_insitucionaldetalle_2){
                        $valid_arqueo++; 
                    }
                }

                $ret_caja_reservacf_total = DB::table('cvmovimientointernodinero')
                    ->where('cvmovimientointernodinero.idestadoeliminado',1)
                    ->where('cvmovimientointernodinero.idfuenteretiro',8)
                    ->where('cvmovimientointernodinero.idtipomovimientointerno',5)
                    ->where('cvmovimientointernodinero.fecharegistro','>=',$request->fecha_corte.' 00:00:00')
                    ->where('cvmovimientointernodinero.fecharegistro','<=',$request->fecha_corte.' 23:59:59')
                    ->where('cvmovimientointernodinero.idtienda',$value->id)
                    ->first();

                if($ret_caja_reservacf_total && $ret_reservacf_caja_total){
                    $cierre_insitucionaldetalle_3 = DB::table('cvcierre_insitucionaldetalle')
                        ->where('cvcierre_insitucionaldetalle.idcierrecaja',$ret_caja_reservacf_total->id)
                        ->first();
                    if(!$cierre_insitucionaldetalle_3){
                        $valid_cierre++; 
                    }
                }    
            }
          
            $estado_cierre_institucional = '';
            if($valid_apertura>0 && $valid_cierre==0){
                $estado_cierre_institucional = 'PENDIENTE';
            }elseif($valid_apertura>0 && $valid_arqueo==0){
                $estado_cierre_institucional = 'PENDIENTE';
            }elseif($valid_apertura==0){
                $estado_cierre_institucional = 'NOEXISTE';
            }
            return view(sistema_view().'/cvcontrolaperturaopecaja/cierre',[
                'tienda' => $tienda,
                'usuarios' => $usuarios,
                'fecha_corte' => $request->fecha_corte,
                'estado_cierre_institucional' => $estado_cierre_institucional,
            ]);
        }
        else if($request->input('view') == 'reporte') {
            return view(sistema_view().'/cvcontrolaperturaopecaja/reporte',[
                'tienda' => $tienda,
                'fecha_corte' => $request->fecha_corte_reporte,
            ]);
        }
        else if( $request->input('view') == 'reporte_pdf' ){
            $cierre_insitucional = DB::table('cvcierre_insitucional')
                ->join('users as responsable','responsable.id','cvcierre_insitucional.validar_responsable')
                ->join('permiso','permiso.id','cvcierre_insitucional.validar_responsable_permiso')
                ->where('cvcierre_insitucional.fechacorte',$request->fecha_corte_reporte)
                ->select(
                    'cvcierre_insitucional.*',
                    'responsable.nombrecompleto as nombrecompleto_responsable',
                    'responsable.codigo as codigo_responsable',
                    'permiso.nombre as nombre_permiso',
                )
                ->first();

            $pdf = PDF::loadView(sistema_view().'/cvcontrolaperturaopecaja/reporte_pdf',[
                'tienda' => $tienda,
                'fecha_corte' => $request->fecha_corte_reporte,
                'cierre_insitucional' => $cierre_insitucional,
            ]); 
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('REPORTE_CONTROL_APERTURA_CIERRE_CAJA.pdf');
        }  
    }

    public function update(Request $request, $idtienda, $id)
    {
        if($request->input('view') == 'validar'){
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

            $idcierre_insitucional = DB::table('cvcierre_insitucional')->insertGetId([
                'fecharegistro' => now(),
                'fechacorte' => $request->fecha_corte,
                'validar_responsable' => $request->idresponsable,
                'validar_responsable_permiso' => $request->idresponsable_permiso,
                'idtienda' => user_permiso()->idtienda,
                'idestado' => 1
            ]);
          
            $tiendas = DB::table('tienda')
              ->orderBy('tienda.id','asc')
              ->get();
          
            foreach($tiendas as $value){

                $ret_reservacf_caja_total = DB::table('cvmovimientointernodinero')
                    ->where('cvmovimientointernodinero.idestadoeliminado',1)
                    ->where('cvmovimientointernodinero.idfuenteretiro',6)
                    ->where('cvmovimientointernodinero.idtipomovimientointerno',5)
                    ->where('cvmovimientointernodinero.fecharegistro','>=',$request->fecha_corte.' 00:00:00')
                    ->where('cvmovimientointernodinero.fecharegistro','<=',$request->fecha_corte.' 23:59:59')
                    ->where('cvmovimientointernodinero.idtienda',$value->id)
                    ->first();

                $estado_apertura = 'NO ABIERTO';
                $id_apertura = 0;
                $efectivo_apertura = 0;
                $fechahora_apertura = '';
                if($ret_reservacf_caja_total){
                    $estado_apertura = 'ABIERTO';   
                    $id_apertura = $ret_reservacf_caja_total->id; 
                    $efectivo_apertura = $ret_reservacf_caja_total->monto;
                    $fechahora_apertura = $ret_reservacf_caja_total->fecharegistro;    
                }
              
                $arqueocaja = DB::table('cvarqueocaja')
                    ->where('idagencia',$value->id)
                    ->where('corte',$request->fecha_corte)
                    ->first();

                $id_arquecaja = 0;
                $efectivo_arqueocaja = 0;
                $fechahora_arqueocaja = '';
                if($arqueocaja && $ret_reservacf_caja_total){
                    $id_arquecaja = $arqueocaja->id;
                    $efectivo_arqueocaja = $arqueocaja->total;
                    $fechahora_arqueocaja = $arqueocaja->fecharegistro;
                }

                $ret_caja_reservacf_total = DB::table('cvmovimientointernodinero')
                    ->where('cvmovimientointernodinero.idestadoeliminado',1)
                    ->where('cvmovimientointernodinero.idfuenteretiro',8)
                    ->where('cvmovimientointernodinero.idtipomovimientointerno',5)
                    ->where('cvmovimientointernodinero.fecharegistro','>=',$request->fecha_corte.' 00:00:00')
                    ->where('cvmovimientointernodinero.fecharegistro','<=',$request->fecha_corte.' 23:59:59')
                    ->where('cvmovimientointernodinero.idtienda',$value->id)
                    ->first();

                $id_cierre = 0;
                $efectivo_cierre = 0;
                $fechahora_cierre = '';
                if($ret_caja_reservacf_total && $ret_reservacf_caja_total){
                    $id_cierre = $ret_caja_reservacf_total->id;
                    $efectivo_cierre = $ret_caja_reservacf_total->monto;
                    $fechahora_cierre = $ret_caja_reservacf_total->fecharegistro;
                }
            
                $estado_cierre = '';
                if(!$ret_reservacf_caja_total){
                    $estado_cierre = 'SIN APERTURA';
                }
                elseif(($ret_reservacf_caja_total && !$arqueocaja) || 
                       ($ret_reservacf_caja_total && !$ret_caja_reservacf_total)){
                    $estado_cierre = 'PENDIENTE';
                }
                elseif($ret_reservacf_caja_total && $arqueocaja && $ret_caja_reservacf_total){
                    $estado_cierre = 'CERRADA';
                }

                if($ret_caja_reservacf_total && $arqueocaja && $ret_reservacf_caja_total){

                DB::table('cvcierre_insitucionaldetalle')->insert([
                    'idagencia' => $value->id,
                    'apertura_estado' => $estado_apertura,
                    'apertura_efectivo' => number_format($efectivo_apertura,2,'.',''),
                    'apertura_fecha' => $fechahora_apertura,
                    'arqueo_efectivo' => number_format($efectivo_arqueocaja,2,'.',''),
                    'arqueo_fecha' => $fechahora_arqueocaja,
                    'cierre_estado' => $estado_cierre,
                    'cierre_fecha' => $fechahora_cierre,
                    'idaperturacaja' => $id_apertura,
                    'idarqueocaja' => $id_arquecaja,
                    'idcierrecaja' => $id_cierre,
                    'idresponsable' => $request->idresponsable,
                    'idcvcierre_insitucional' => $idcierre_insitucional,
                    'idtienda' => user_permiso()->idtienda,
                    'idestado' => 1
                ]); 
                  
                }
            }
          
            /*DB::table('movimientointernodinero')
                ->where('movimientointernodinero.idestadoeliminado',1)
                ->whereIn('movimientointernodinero.idfuenteretiro',[6,8])
                ->where('movimientointernodinero.idtipomovimientointerno',5)
                ->where('movimientointernodinero.fecharegistro','>=',$request->fecha_corte.' 00:00:00')
                ->where('movimientointernodinero.fecharegistro','<=',$request->fecha_corte.' 23:59:59')
                ->update([
                'idcierre_insitucional' => $idcierre_insitucional,
            ]);
          
            DB::table('arqueocaja')
                ->where('arqueocaja.corte',$request->fecha_corte)
                ->update([
                'idcierre_insitucional' => $idcierre_insitucional,
            ]);*/
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha validado correctamente.',
              'idresponsable'   => $idresponsable
            ]);
        }
    }

    public function destroy(Request $request, $idtienda, $id)
    {
      
    }
}
