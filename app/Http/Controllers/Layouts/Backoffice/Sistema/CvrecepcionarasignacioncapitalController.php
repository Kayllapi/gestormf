<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use PDF;
use Carbon\Carbon;

class CvrecepcionarasignacioncapitalController extends Controller
{
    public function __construct()
    {
       
    }
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            
            return view(sistema_view().'/cvrecepcionarasignacioncapital/tabla',[
              'tienda' => $tienda
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
        if($id=='show_table'){
            /*$where = [];
            $where[] = ['asignacioncapital.fecharegistro','>=',now()->format('Y-m-d').' 00:00:00'];
            $where[] = ['asignacioncapital.fecharegistro','<=',now()->format('Y-m-d').' 23:59:59']; */
          
            $asignacioncapital = DB::table('cvasignacioncapital')
                ->join('credito_tipooperacion','credito_tipooperacion.id','cvasignacioncapital.idtipooperacion')
                ->leftJoin('credito_tipodestino','credito_tipodestino.id','cvasignacioncapital.idtipodestino')
                ->join('users as responsable','responsable.id','cvasignacioncapital.idresponsable')
                ->leftJoin('users as responsable_recfinal','responsable_recfinal.id','cvasignacioncapital.idresponsable_recfinal')
                ->join('tienda','tienda.id','cvasignacioncapital.idtienda')
                ->where('cvasignacioncapital.idestadoeliminado',1)
                ->where('cvasignacioncapital.idresponsable_recfinal',0)
                //->where($where)
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
                $html .= "<tr data-valor-columna='{$value->id}' idresponsable_recfinal='{$value->idresponsable_recfinal}' onclick='show_data(this)'>
                              <td>{$value->tiendanombre}</td>
                              <td>{$fecharegistro}</td>
                              <td>{$value->codigoprefijo}{$value->codigo}</td>
                              <td><span style='{$subrayado_sincolor}'>{$value->credito_tipooperacionnombre}</span></td>
                              <td>{$value->credito_tipodestinonombre}</td>
                              <td style='text-align: right;{$style}'><span style='{$subrayado}'>{$signo}{$value->monto}</span></td>
                              <td>{$cuenta}</td>
                              <td>{$numerooperacion}</td>
                              <td>{$value->descripcion}</td>
                              <td>{$value->codigo_responsable}</td>
                              <td>{$value->codigo_responsable_recfinal}</td>
                          </tr>";
                $total = $total+$value->monto;
                if($value->idtipooperacion==1 or $value->idtipooperacion==4){
                    $total_suma = $total_suma+$value->monto;
                }elseif($value->idtipooperacion==2 or $value->idtipooperacion==3){
                    $total_resta = $total_resta+$value->monto;
                }
            }
          
            if(count($asignacioncapital)==0){
                $html.= '<tr><td colspan="11" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
            }
               
              $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <td colspan="5" style="background-color: #c2c0c2 !important;text-align:right; font-weight: bold;">Total (S/.)</td>
                  <td style="background-color: #c2c0c2 !important;text-align:right; font-weight: bold;">'.number_format($total_suma-$total_resta, 2, '.', '').'</td>
                  <td colspan="5" style="background-color: #c2c0c2 !important;text-align:right; font-weight: bold;"></td>
                </tr>'; 
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
      
        if($request->input('view') == 'recepcionar') {
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
            return view(sistema_view().'/cvrecepcionarasignacioncapital/recepcionar',[
                'tienda' => $tienda,
                'usuarios' => $usuarios,
                'asignacioncapital' => $asignacioncapital,
            ]);
        }
        else if($request->input('view') == 'voucher') {
            return view(sistema_view().'/cvrecepcionarasignacioncapital/voucher',[
              'tienda' => $tienda,
              'idasignacioncapital' => $id,
            ]);
        }
        else if( $request->input('view') == 'voucher_pdf' ){
              
            $asignacioncapital = DB::table('cvasignacioncapital')
                ->join('credito_tipooperacion','credito_tipooperacion.id','cvasignacioncapital.idtipooperacion')
                ->join('credito_tipodestino','credito_tipodestino.id','cvasignacioncapital.idtipodestino')
                ->join('users as responsable','responsable.id','cvasignacioncapital.idresponsable')
                ->join('users_permiso as users_permiso_responsable','users_permiso_responsable.idusers','responsable.id')
                ->join('permiso as permiso_responsable','permiso_responsable.id','users_permiso_responsable.idpermiso')
                ->leftJoin('users as responsable_recfinal','responsable_recfinal.id','cvasignacioncapital.idresponsable_recfinal')
                ->join('users_permiso as users_permiso_responsable_recfinal','users_permiso_responsable_recfinal.idusers','responsable_recfinal.id')
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
          
          
            $pdf = PDF::loadView(sistema_view().'/cvrecepcionarasignacioncapital/voucher_pdf',[
                'tienda' => $tienda,
                'asignacioncapital' => $asignacioncapital,
            ]); 
            $pdf->setPaper('A4');
            return $pdf->stream('VOUCHER.pdf');
        }   
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        if($request->input('view') == 'recepcionar'){
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
    }


    public function destroy(Request $request, $idtienda, $id)
    {
    
    }
}
