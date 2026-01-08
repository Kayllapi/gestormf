<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use PDF;
use Carbon\Carbon;

class MovimientointernodineroinstiController extends Controller
{
    public function __construct()
    {
       
    }
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/movimientointernodineroinsti/tabla',[
              'tienda' => $tienda
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        if($request->view == 'registrar_retiro2') {
            $bancos = DB::table('banco')->where('estado','ACTIVO')->get();
            $fuenteretiros= DB::table('credito_fuenteretiro')
              ->where('idtipo',2)
              ->whereIn('id',[10])
              ->get();
            return view(sistema_view().'/movimientointernodineroinsti/create_retiro2',[
                'tienda' => $tienda,
                'bancos' => $bancos,
                'fuenteretiros' => $fuenteretiros,
            ]);
        }
        elseif($request->view == 'registrar_deposito2') {
            $bancos = DB::table('banco')->where('estado','ACTIVO')->get();
            $fuenteretiros= DB::table('credito_fuenteretiro')
              ->where('idtipo',1)
              ->whereIn('id',[5])
              ->get();
            return view(sistema_view().'/movimientointernodineroinsti/create_deposito2',[
                'tienda' => $tienda,
                'bancos' => $bancos,
                'fuenteretiros' => $fuenteretiros,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->input('view') == 'registrar_retiro2') {
            $rules = [
                'idfuenteretiro_retiro2' => 'required', 
                'monto_retiro2' => 'required',   
                'descripcion_retiro2' => 'required',        
            ];
          
            $messages = [
                'idfuenteretiro_retiro2.required' => 'La "Fuente de Retiro" es Obligatorio.',
                'monto_retiro2.required' => 'El "Monto" es Obligatorio.',
                'descripcion_retiro2.required' => 'La "Descricpión" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            if($request->idtipodestino==2){
                $rules = [
                    'idbanco_retiro2' => 'required',                  
                    'numerooperacion_retiro2' => 'required',                       
                ];

                $messages = [
                    'idbanco_retiro2.required' => 'El Campo Banco es Obligatorio.',
                    'numerooperacion_retiro2.required' => 'El Campo Número de Operación es Obligatorio.',
                ];
                $this->validate($request,$rules,$messages);
            }
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha validado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'registrar_retiro2_insert') {
            // --- RETIRO
            $consolidadooperaciones = consolidadooperaciones($tienda,$idtienda,now()->format('Y-m-d'));
            if($request->idfuenteretiro_retiro2==10){
                foreach($consolidadooperaciones['saldos_cuentabanco_bancos'] as $value){
                    if($value['banco_id']==$request->idbanco_retiro2 && $value['banco']<$request->monto_retiro2){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay saldo suficiente en Cuenta Bancaria.<br><b>Saldo Actual: S/. '.$value['banco'].'.</b>'
                        ]);
                    }
                }  
            }

            $movimientointernodinero = DB::table('movimientointernodinero')
                ->where('movimientointernodinero.idtipomovimientointerno',3)
                ->orderBy('movimientointernodinero.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($movimientointernodinero!=''){
                $codigo = $movimientointernodinero->codigo+1;
            }

            $bancoo = DB::table('banco')->where('banco.id',$request->idbanco_retiro2)->first();

            $banco = '';
            $cuenta = '';
            if($bancoo!=''){
                $banco = $bancoo->nombre;
                $cuenta = $bancoo->cuenta;
            }
          
            $idmovimientointernodinero = DB::table('movimientointernodinero')->insertGetId([
                'fecharegistro' => now(),
                'codigoprefijo' => 'OMIR',
                'codigo' => $codigo,
                'monto' => $request->input('monto_retiro2'),
                'descripcion' => $request->input('descripcion_retiro2'),
                'numerooperacion' => $request->numerooperacion_retiro2!=''?$request->numerooperacion_retiro2:'',
                'banco' => $banco,
                'cuenta' => $cuenta,
                'idbanco' => $request->idbanco_retiro2!=''?$request->idbanco_retiro2:0,
                'idfuenteretiro' => $request->idfuenteretiro_retiro2,
                'idresponsable' => $request->idresponsable_retiro2,
                'idresponsable_permiso' => $request->idresponsable_permiso_retiro2,
                'idtipomovimientointerno' => 3, 
                'idtienda' => user_permiso()->idtienda,
                'idestadoeliminado' => 1,
                'idestado' => 1,
            ]);
          
            // DEPOSITO
            $movimientointernodinero = DB::table('movimientointernodinero')
                ->where('movimientointernodinero.idtipomovimientointerno',4)
                ->orderBy('movimientointernodinero.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($movimientointernodinero!=''){
                $codigo = $movimientointernodinero->codigo+1;
            }
          
            DB::table('movimientointernodinero')->insert([
                'fecharegistro' => now(),
                'codigoprefijo' => 'OMID',
                'codigo' => $codigo,
                'monto' => $request->input('monto_retiro2'),
                'descripcion' => $request->input('descripcion_retiro2'),
                'numerooperacion' => $request->numerooperacion_retiro2!=''?$request->numerooperacion_retiro2:'',
                'banco' => $banco,
                'cuenta' => $cuenta,
                'idbanco' => $request->idbanco_retiro2!=''?$request->idbanco_retiro2:0,
                'idfuenteretiro' => $request->idfuenteretiro_retiro2-5,
                'idtipomovimientointerno' => 4, 
                'idmovimientointernodinero' => $idmovimientointernodinero, 
                'idtienda' => user_permiso()->idtienda,
                'idestadoeliminado' => 1,
                'idestado' => 1,
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'registrar_deposito2') {
            $rules = [
                'idfuenteretiro_deposito2' => 'required', 
                'monto_deposito2' => 'required', 
                'descripcion_deposito2' => 'required',        
            ];
          
            $messages = [
                'idfuenteretiro_deposito2.required' => 'La "Fuente de Retiro" es Obligatorio.',
                'monto_deposito2.required' => 'El "Monto" es Obligatorio.',
                'descripcion_deposito2.required' => 'La "Descricpión" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            if($request->idtipodestino==2){
                $rules = [
                    'idbanco_deposito2' => 'required',                  
                    'numerooperacion_deposito2' => 'required',                       
                ];

                $messages = [
                    'idbanco_deposito2.required' => 'El Campo Banco es Obligatorio.',
                    'numerooperacion_deposito2.required' => 'El Campo Número de Operación es Obligatorio.',
                ];
                $this->validate($request,$rules,$messages);
            }
            
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha validado correctamente.'
            ]);
        }
        /*elseif($request->input('view') == 'registrar_deposito2_insert') {
          
            $movimientointernodinero = DB::table('movimientointernodinero')
                ->where('movimientointernodinero.idtipomovimientointerno',4)
                ->orderBy('movimientointernodinero.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($movimientointernodinero!=''){
                $codigo = $movimientointernodinero->codigo+1;
            }
          
            $bancoo = DB::table('banco')->where('banco.id',$request->idbanco_deposito2)->first();

            $banco = '';
            $cuenta = '';
            if($bancoo!=''){
                $banco = $bancoo->nombre;
                $cuenta = $bancoo->cuenta;
            }
          
            DB::table('movimientointernodinero')->insert([
                'codigoprefijo' => 'OMID',
                'codigo' => $codigo,
                'monto' => $request->input('monto_deposito2'),
                'descripcion' => $request->input('descripcion_deposito2'),
                'numerooperacion' => $request->numerooperacion_deposito2!=''?$request->numerooperacion_deposito2:'',
                'banco' => $banco,
                'cuenta' => $cuenta,
                'idbanco' => $request->idbanco_deposito2!=''?$request->idbanco_deposito2:0,
                'idfuenteretiro' => $request->idfuenteretiro_deposito2,
                'idresponsable' => Auth::user()->id,
                'idtipomovimientointerno' => 4, 
                'idtienda' => user_permiso()->idtienda,
                'idestadoeliminado' => 1,
                'idestado' => 1,
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }*/
    }

    public function show(Request $request, $idtienda, $id)
    {
        if($id=='show_table_retiro2'){
            $where = [];
            if($request->input('fechainicio') != ''){
                $where[] = ['movimientointernodinero.fecharegistro','>=',$request->fechainicio.' 00:00:00'];
            }
            if($request->input('fechafin') != ''){
                $where[] = ['movimientointernodinero.fecharegistro','<=',$request->fechafin.' 23:59:59']; 
            }
            $movimientointernodinero = DB::table('movimientointernodinero')
                ->join('credito_fuenteretiro','credito_fuenteretiro.id','movimientointernodinero.idfuenteretiro')
                ->join('users as responsable','responsable.id','movimientointernodinero.idresponsable')
                ->join('tienda','tienda.id','movimientointernodinero.idtienda')
                ->where('movimientointernodinero.idestadoeliminado',1)
                ->where('movimientointernodinero.idtipomovimientointerno',3)
                ->where($where)
                ->select(
                    'movimientointernodinero.*',
                    'credito_fuenteretiro.nombre as credito_fuenteretironombre',
                    'tienda.nombreagencia as tiendanombre',
                    'responsable.codigo as codigo_responsable',
                )
                ->orderBy('movimientointernodinero.id','ASC')
                ->get();
  
            $total = 0;
            $html = '';
            foreach($movimientointernodinero as $key => $value){
                $fecharegistro = date_format(date_create($value->fecharegistro),"d-m-Y H:i:s A");
                $cuenta = $value->banco!=''?$value->banco.' - ***'.substr($value->cuenta, -4):'';
                $numerooperacion = $value->banco!=''?$value->numerooperacion:'';
                $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data_retiro2(this)'>
                              <td style='white-space: nowrap;'>{$value->codigoprefijo}{$value->codigo}</td>
                              <td style='white-space: nowrap;'>{$value->credito_fuenteretironombre}</td>
                              <td style='white-space: nowrap;text-align:right;'>{$value->monto}</td>
                              <td style='white-space: nowrap;'>{$cuenta}</td>
                              <td style='white-space: nowrap;'>{$numerooperacion}</td>
                              <td style='white-space: nowrap;'>{$value->descripcion}</td>
                              <td style='white-space: nowrap;'>{$fecharegistro}</td>
                              <td style='white-space: nowrap;'>{$value->codigo_responsable}</td>
                          </tr>";
                $total = $total+$value->monto;
            }
          
            if(count($movimientointernodinero)==0){
                $html.= '<tr><td colspan="7" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
            }
               
            $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <td colspan="2" style="background-color: #144081 !important;text-align:right;color:#fff !important;">Total Retiros (S/.)</td>
                  <td style="background-color: #144081 !important;text-align:right;color:#fff !important;">'.number_format($total, 2, '.', '').'</td>
                  <td colspan="5" style="background-color: #144081 !important;text-align:right;color:#fff !important;"></td>
                </tr>'; 
          
            return array(
                'html' => $html
            );
        }
        elseif($id=='show_table_deposito2'){
            $where = [];
            if($request->input('fechainicio') != ''){
                $where[] = ['movimientointernodinero.fecharegistro','>=',$request->fechainicio.' 00:00:00'];
            }
            if($request->input('fechafin') != ''){
                $where[] = ['movimientointernodinero.fecharegistro','<=',$request->fechafin.' 23:59:59']; 
            }
            $movimientointernodinero = DB::table('movimientointernodinero')
                ->join('credito_fuenteretiro','credito_fuenteretiro.id','movimientointernodinero.idfuenteretiro')
                ->leftJoin('users as responsable','responsable.id','movimientointernodinero.idresponsable')
                ->join('tienda','tienda.id','movimientointernodinero.idtienda')
                ->where('movimientointernodinero.idestadoeliminado',1)
                ->where('movimientointernodinero.idtipomovimientointerno',4)
                ->where($where)
                ->select(
                    'movimientointernodinero.*',
                    'credito_fuenteretiro.nombre as credito_fuenteretironombre',
                    'tienda.nombreagencia as tiendanombre',
                    'responsable.codigo as codigo_responsable',
                )
                ->orderBy('movimientointernodinero.id','ASC')
                ->get();
  
            $total = 0;
            $html = '';
            foreach($movimientointernodinero as $key => $value){
                $fecharegistro = date_format(date_create($value->fecharegistro),"d-m-Y H:i:s A");
                $cuenta = $value->banco!=''?$value->banco.' - ***'.substr($value->cuenta, -4):'';
                $numerooperacion = $value->banco!=''?$value->numerooperacion:'';
                $bgcolor = '';
                if($value->idresponsable==0){
                    $bgcolor = 'style="background-color: #ffb8b8;"';
                }
                $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data_deposito2(this)' ".$bgcolor.">
                              <td style='white-space: nowrap;'>{$value->codigoprefijo}{$value->codigo}</td>
                              <td style='white-space: nowrap;'>{$value->credito_fuenteretironombre}</td>
                              <td style='white-space: nowrap;text-align:right;'>{$value->monto}</td>
                              <td style='white-space: nowrap;'>{$cuenta}</td>
                              <td style='white-space: nowrap;'>{$numerooperacion}</td>
                              <td style='white-space: nowrap;'>{$value->descripcion}</td>
                              <td style='white-space: nowrap;'>{$fecharegistro}</td>
                              <td style='white-space: nowrap;'>{$value->codigo_responsable}</td>
                          </tr>";
                $total = $total+$value->monto;
            }
          
            if(count($movimientointernodinero)==0){
                $html.= '<tr><td colspan="7" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
            }
               
            $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <td colspan="2" style="background-color: #144081 !important;text-align:right;color:#fff !important;">Total Depósitos (S/.)</td>
                  <td style="background-color: #144081 !important;text-align:right;color:#fff !important;">'.number_format($total, 2, '.', '').'</td>
                  <td colspan="5" style="background-color: #144081 !important;text-align:right;color:#fff !important;"></td>
                </tr>'; 
          
            return array(
                'html' => $html
            );
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $movimientointernodinero = DB::table('movimientointernodinero')
                      ->where('movimientointernodinero.id',$id)
                      ->select(
                          'movimientointernodinero.*',
                      )
                      ->orderBy('movimientointernodinero.id','desc')
                      ->first();
      
        if($request->input('view') == 'editar_retiro2'){
            $bancos = DB::table('banco')->where('estado','ACTIVO')->get();
            $fuenteretiros= DB::table('credito_fuenteretiro')
              ->where('idtipo',2)
              ->whereIn('id',[10])
              ->get();
            return view(sistema_view().'/movimientointernodineroinsti/edit_retiro2',[
                'tienda' => $tienda,
                'bancos' => $bancos,
                'fuenteretiros' => $fuenteretiros,
                'movimientointernodinero' => $movimientointernodinero,
            ]);
        } 
        elseif($request->input('view') == 'eliminar_retiro2'){
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/movimientointernodineroinsti/delete_retiro2',[
              'tienda' => $tienda,
              'movimientointernodinero' => $movimientointernodinero,
              'usuarios' => $usuarios,
            ]);
        }
        elseif($request->input('view') == 'editar_deposito2'){
            $bancos = DB::table('banco')->where('estado','ACTIVO')->get();
            $fuenteretiros= DB::table('credito_fuenteretiro')
              ->where('idtipo',1)
              ->whereIn('id',[5])
              ->get();
            return view(sistema_view().'/movimientointernodineroinsti/edit_deposito2',[
                'tienda' => $tienda,
                'bancos' => $bancos,
                'fuenteretiros' => $fuenteretiros,
                'movimientointernodinero' => $movimientointernodinero,
            ]);
        } 
        elseif($request->input('view') == 'eliminar_deposito2'){
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/movimientointernodineroinsti/delete_deposito2',[
              'tienda' => $tienda,
              'movimientointernodinero' => $movimientointernodinero,
              'usuarios' => $usuarios,
            ]);
        }
        elseif($request->input('view') == 'valid_registro_retiro2') {
            $where = [];
            if($request->idfuenteretiro_retiro2==10){
                $where = [2];
            }
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',$where)
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.id as idpermiso','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/movimientointernodineroinsti/valid_registro_retiro2',[
                'tienda' => $tienda,
                'usuarios' => $usuarios,
            ]);
        }
        elseif($request->input('view') == 'valid_registro_deposito2') {
            $where = [];
            if($request->idfuenteretiro_deposito2==5){
                $where = [1,2];
            }
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',$where)
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.id as idpermiso','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/movimientointernodineroinsti/valid_registro_deposito2',[
                'tienda' => $tienda,
                'usuarios' => $usuarios,
                'idmovimientointernodinero' => $id,
            ]);
        }
        elseif($request->input('view') == 'valid_reporte') {
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/movimientointernodineroinsti/valid_reporte',[
                'tienda' => $tienda,
                'usuarios' => $usuarios,
            ]);
        }
        elseif($request->input('view') == 'exportar') {
            return view(sistema_view().'/movimientointernodineroinsti/exportar',[
                'tienda' => $tienda,
                'fechainicio' => $request->fechainicio,
                'fechafin' => $request->fechafin,
            ]);
        }
        elseif($request->input('view') == 'exportar_pdf'){
              
            $where = [];
            if($request->input('fechainicio') != ''){
                $where[] = ['movimientointernodinero.fecharegistro','>=',$request->fechainicio.' 00:00:00'];
            }
            if($request->input('fechafin') != ''){
                $where[] = ['movimientointernodinero.fecharegistro','<=',$request->fechafin.' 23:59:59']; 
            }
            $movimientointernodinero_retiro2 = DB::table('movimientointernodinero')
                ->join('credito_fuenteretiro','credito_fuenteretiro.id','movimientointernodinero.idfuenteretiro')
                ->join('users as responsable','responsable.id','movimientointernodinero.idresponsable')
                ->join('tienda','tienda.id','movimientointernodinero.idtienda')
                ->where('movimientointernodinero.idestadoeliminado',1)
                ->where('movimientointernodinero.idtipomovimientointerno',3)
                ->where($where)
                ->select(
                    'movimientointernodinero.*',
                    'credito_fuenteretiro.nombre as credito_fuenteretironombre',
                    'tienda.nombreagencia as tiendanombre',
                    'responsable.codigo as codigo_responsable',
                )
                ->orderBy('movimientointernodinero.id','ASC')
                ->get();
          
            $movimientointernodinero_deposito3 = DB::table('movimientointernodinero')
                ->join('credito_fuenteretiro','credito_fuenteretiro.id','movimientointernodinero.idfuenteretiro')
                ->join('users as responsable','responsable.id','movimientointernodinero.idresponsable')
                ->join('tienda','tienda.id','movimientointernodinero.idtienda')
                ->where('movimientointernodinero.idestadoeliminado',1)
                ->where('movimientointernodinero.idtipomovimientointerno',4)
                ->where($where)
                ->select(
                    'movimientointernodinero.*',
                    'credito_fuenteretiro.nombre as credito_fuenteretironombre',
                    'tienda.nombreagencia as tiendanombre',
                    'responsable.codigo as codigo_responsable',
                )
                ->orderBy('movimientointernodinero.id','ASC')
                ->get();
          
            $agencia = DB::table('tienda')->whereId(user_permiso()->idtienda)->first();
        
            $pdf = PDF::loadView(sistema_view().'/movimientointernodineroinsti/exportar_pdf',[
                'tienda' => $tienda,
                'fechainicio' => $request->fechainicio,
                'fechafin' => $request->fechafin,
                'movimientointernodinero_retiro2' => $movimientointernodinero_retiro2,
                'movimientointernodinero_deposito3' => $movimientointernodinero_deposito3,
                'agencia' => $agencia,
            ]); 
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('OPERACIONES_DE_MOVIMIENTO_INTERNO.pdf');
        } 
    }

    public function update(Request $request, $idtienda, $id)
    {
        if($request->input('view') == 'valid_registro_retiro2'){
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
        elseif($request->input('view') == 'valid_registro_deposito2'){
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
          
            DB::table('movimientointernodinero')->whereId($id)->update([
                'idresponsable' => $request->idresponsable,
                'idresponsable_permiso' => $request->idresponsable_permiso,
            ]);
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha validado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'valid_reporte_retiro'){
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
        elseif($request->input('view') == 'valid_reporte_deposito'){
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
    }


    public function destroy(Request $request, $idtienda, $id)
    {
        if($request->input('view') == 'eliminar_retiro2'){
            $rules = [
                'idresponsable_retiro2' => 'required',          
                'responsableclave_retiro2' => 'required',              
            ];

            $messages = [
                'idresponsable_retiro2.required' => 'El "Responsable" es Obligatorio.',
                'responsableclave_retiro2.required' => 'La "Contraseña" es Obligatorio.',
            ];

            $this->validate($request,$rules,$messages);

            $usuario = DB::table('users')
                ->where('users.id',$request->idresponsable_retiro2)
                ->where('users.clave',$request->responsableclave_retiro2)
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

            /*DB::table('movimientointernodinero')->whereId($id)->update([
               'fecha_eliminado' => now(),
               'idestadoeliminado' => 2,
               'idresponsble_eliminado' => Auth::user()->id,
            ]);*/


            $movimientointernodinero = DB::table('movimientointernodinero')
                ->whereId($id)
                ->first();

            DB::table('movimientointernodinero')->where('idmovimientointernodinero',$movimientointernodinero->id)->delete();
            DB::table('movimientointernodinero')->whereId($id)->delete();
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha elimino correctamente.'
            ]);
        }
        elseif($request->input('view') == 'eliminar_deposito2'){
            $rules = [
                'idresponsable_deposito2' => 'required',          
                'responsableclave_deposito2' => 'required',              
            ];

            $messages = [
                'idresponsable_deposito2.required' => 'El "Responsable" es Obligatorio.',
                'responsableclave_deposito2.required' => 'La "Contraseña" es Obligatorio.',
            ];

            $this->validate($request,$rules,$messages);

            $usuario = DB::table('users')
                ->where('users.id',$request->idresponsable_deposito2)
                ->where('users.clave',$request->responsableclave_deposito2)
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

            /*DB::table('movimientointernodinero')->whereId($id)->update([
               'fecha_eliminado' => now(),
               'idestadoeliminado' => 2,
               'idresponsble_eliminado' => Auth::user()->id,
            ]);*/

            DB::table('movimientointernodinero')->whereId($id)->delete();
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha elimino correctamente.'
            ]);
        }
    
    }
}
