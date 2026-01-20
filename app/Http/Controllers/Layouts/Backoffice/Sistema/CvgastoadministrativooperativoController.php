<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use PDF;
use Carbon\Carbon;

class CvgastoadministrativooperativoController extends Controller
{
    public function __construct()
    {
       
    }
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/cvgastoadministrativooperativo/tabla',[
              'tienda' => $tienda
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        if($request->view == 'registrar') {
            
            $bancos = DB::table('banco')->where('estado','ACTIVO')->get();
            $s_sustento_comprobante = DB::table('s_sustento_comprobante')->where('idtipo',2)->get();
            $credito_tipoformapago = DB::table('credito_tipoformapago')->get();
          
            return view(sistema_view().'/cvgastoadministrativooperativo/create',[
                'tienda' => $tienda,
                'bancos' => $bancos,
                's_sustento_comprobante' => $s_sustento_comprobante,
                'credito_tipoformapago' => $credito_tipoformapago,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'registrar') {
            $rules = [
                'monto' => 'required', 
                //'fechapago' => 'required', 
                'descripcion' => 'required',  
                'sustento_comprobante' => 'required',  
                'sustento_descripcion' => 'required',          
            ];
          
            $messages = [
                'monto.required' => 'El "Monto" es Obligatorio.',
                //'fechapago.required' => 'La "Fecha de Pago" es Obligatorio.',
                'descripcion.required' => 'La "Descricpión" es Obligatorio.',
                'sustento_comprobante.required' => 'El "Comprobante" es Obligatorio.',
                'sustento_descripcion.required' => 'La "Descricpión" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            if($request->idformapago==2){
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
          
            $consolidadooperaciones = consolidadooperaciones($tienda,$idtienda,now()->format('Y-m-d'));
            /*if($request->idformapago==1){
                if($consolidadooperaciones['saldos_caja']<$request->monto){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'No hay saldo suficiente en CAJA.<br><b>Saldo Actual: S/. '.$consolidadooperaciones['saldos_caja'].'.</b>'
                    ]);
                }
            }
            elseif($request->idformapago==2){
                foreach($consolidadooperaciones['saldos_cuentabanco_bancos'] as $value){
                    if($value['banco_id']==$request->idbanco && $value['banco']<$request->monto){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay saldo suficiente en Cuenta Bancaria.'
                        ]);
                    }
                }  
            }*/
          
            $gastoadministrativooperativo = DB::table('cvgastoadministrativooperativo')
                ->orderBy('cvgastoadministrativooperativo.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($gastoadministrativooperativo!=''){
                $codigo = $gastoadministrativooperativo->codigo+1;
            }

            $bancoo = DB::table('banco')->where('banco.id',$request->idbanco)->first();

            $banco = '';
            $cuenta = '';
            if($bancoo!=''){
                $banco = $bancoo->nombre;
                $cuenta = $bancoo->cuenta;
            }
          
            DB::table('cvgastoadministrativooperativo')->insert([
                'fecharegistro' => now(),
                'codigoprefijo' => 'GV',
                'codigo' => $codigo,
                //'fechapago' => now(),
                'monto' => $request->input('monto'),
                'descripcion' => $request->input('descripcion'),
                's_idsustento_comprobante' => $request->input('sustento_comprobante'),
                'sustento_descripcion' => $request->input('sustento_descripcion'),
              
                'numerooperacion' => $request->numerooperacion!=''?$request->numerooperacion:'',
                'banco' => $banco,
                'cuenta' => $cuenta,
                'idbanco' => $request->idbanco!=''?$request->idbanco:0,
                'idformapago' => $request->idformapago,
                'idresponsable' => Auth::user()->id,
                'idtienda' => user_permiso()->idtienda,
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
            if($request->fechainicio != ''){
                $where[] = ['cvgastoadministrativooperativo.fecharegistro','>=',$request->fechainicio.' 00:00:00'];
            }
            if($request->fechafin != ''){
                $where[] = ['cvgastoadministrativooperativo.fecharegistro','<=',$request->fechafin.' 23:59:59']; 
            }
          
            $gastoadministrativooperativo = DB::table('cvgastoadministrativooperativo')
                ->join('s_sustento_comprobante','s_sustento_comprobante.id','cvgastoadministrativooperativo.s_idsustento_comprobante')
                ->join('credito_tipoformapago','credito_tipoformapago.id','cvgastoadministrativooperativo.idformapago')
                ->join('users as responsable','responsable.id','cvgastoadministrativooperativo.idresponsable')
                ->where('cvgastoadministrativooperativo.idestadoeliminado',1)
                ->where($where)
                ->select(
                    'cvgastoadministrativooperativo.*',
                    's_sustento_comprobante.nombre as s_sustento_comprobantenombre',
                    'credito_tipoformapago.nombre as credito_tipoformapagonombre',
                    'responsable.codigo as codigo_responsable',
                )
                ->orderBy('cvgastoadministrativooperativo.id','ASC')
                ->get();
  
            $total = 0;
            $total_caja = 0;
            $total_banco = 0;
            $html = '';
            foreach($gastoadministrativooperativo as $key => $value){
                $fechapago = date_format(date_create($value->fechapago),"d-m-Y H:i:s A");
                $cuenta = $value->banco!=''?$value->banco.' - ***'.substr($value->cuenta, -5).' ('.$value->numerooperacion.')':'';
                
                $btn_validar = '';
                if($value->idformapago==2){
                    $btn_validar = "<button type='button' class='btn btn-success' onclick='validar({$value->id})'><i class='fa-solid fa-check'></i> Validar</button>";
                    if($value->validar_estado==1){
                        $users = DB::table('users')->whereId($value->validar_responsable)->first();
                        $btn_validar = "<i class='fa-solid fa-check'></i> (".$users->codigo.")";
                    }
                } 
              
                $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data(this)'>
                              <td>".($key+1)."</td>
                              <td>{$value->codigoprefijo}{$value->codigo}</td>
                              <td style='text-align: right;'>{$value->monto}</td>
                              <td>{$fechapago}</td>
                              <td>{$value->descripcion}</td>
                              <td>{$value->s_sustento_comprobantenombre}</td>
                              <td>{$value->sustento_descripcion}</td>
                              <td>{$value->credito_tipoformapagonombre}</td>
                              <td>{$cuenta}</td>
                              <td style='width: 100px;'>{$btn_validar}</td>
                              <td>{$value->codigo_responsable}</td>
                          </tr>";
                $total = $total+$value->monto;
                if($value->idformapago==1){
                    $total_caja = $total_caja+$value->monto;
                }elseif($value->idformapago==2){
                    $total_banco = $total_banco+$value->monto;
                }
            }
          
          if(count($gastoadministrativooperativo)==0){
              $html.= '<tr><td colspan="11" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
          }
              $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <td colspan="2" style="background-color: #c2c0c2 !important;text-align:right; font-weight: bold;">Total (S/.)</td>
                  <td style="background-color: #c2c0c2 !important;text-align:right; font-weight: bold;">'.number_format($total, 2, '.', '').'</td>
                  <td style="background-color: #c2c0c2 !important;text-align:right; font-weight: bold;">Caja (S/.)</td>
                  <td style="background-color: #c2c0c2 !important;text-align:right; font-weight: bold;">'.number_format($total_caja, 2, '.', '').'</td>
                  <td style="background-color: #c2c0c2 !important;text-align:right; font-weight: bold;">Banco (S/.)</td>
                  <td style="background-color: #c2c0c2 !important;text-align:right; font-weight: bold;">'.number_format($total_banco, 2, '.', '').'</td>
                  <td colspan="4" style="background-color: #c2c0c2 !important;text-align:right; font-weight: bold;"></td>
                </tr>';
            return array(
              'html' => $html
            );
        }
        
    }

    public function edit(Request $request, $idtienda, $id)
    {
        
      
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
  
      
      $gastoadministrativooperativo = DB::table('cvgastoadministrativooperativo')
                      ->where('cvgastoadministrativooperativo.id',$id)
                      ->select(
                          'cvgastoadministrativooperativo.*',
                      )
                      ->orderBy('cvgastoadministrativooperativo.id','desc')
                      ->first();
      
      if($request->input('view') == 'editar'){
            
            $bancos = DB::table('banco')->where('estado','ACTIVO')->get();
            $s_sustento_comprobante = DB::table('s_sustento_comprobante')->where('idtipo',2)->get();
            $credito_tipoformapago = DB::table('credito_tipoformapago')->get();
        
        return view(sistema_view().'/cvgastoadministrativooperativo/edit',[
          'tienda' => $tienda,
          'gastoadministrativooperativo' => $gastoadministrativooperativo,
          'idtienda' => $idtienda,
                'bancos' => $bancos,
                's_sustento_comprobante' => $s_sustento_comprobante,
                'credito_tipoformapago' => $credito_tipoformapago,
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
        
        return view(sistema_view().'/cvgastoadministrativooperativo/delete',[
          'tienda' => $tienda,
          'gastoadministrativooperativo' => $gastoadministrativooperativo,
          'usuarios' => $usuarios,
        ]);
      }
        else if($request->input('view') == 'exportar') {
            return view(sistema_view().'/cvgastoadministrativooperativo/exportar',[
                'tienda' => $tienda,
                'fechainicio' => $request->fechainicio,
                'fechafin' => $request->fechafin,
            ]);
        }
        else if( $request->input('view') == 'exportar_pdf' ){
              
            if($request->input('fechainicio') != ''){
                $where[] = ['cvgastoadministrativooperativo.fecharegistro','>=',$request->fechainicio.' 00:00:00'];
            }
            if($request->input('fechafin') != ''){
                $where[] = ['cvgastoadministrativooperativo.fecharegistro','<=',$request->fechafin.' 23:59:59']; 
            }
          
            $gastoadministrativooperativo = DB::table('cvgastoadministrativooperativo')
                ->join('s_sustento_comprobante','s_sustento_comprobante.id','cvgastoadministrativooperativo.s_idsustento_comprobante')
                ->join('credito_tipoformapago','credito_tipoformapago.id','cvgastoadministrativooperativo.idformapago')
                ->join('users as responsable','responsable.id','cvgastoadministrativooperativo.idresponsable')
                ->where('cvgastoadministrativooperativo.idestadoeliminado',1)
                ->where($where)
                ->select(
                    'cvgastoadministrativooperativo.*',
                    's_sustento_comprobante.nombre as s_sustento_comprobantenombre',
                    'credito_tipoformapago.nombre as credito_tipoformapagonombre',
                    'responsable.codigo as codigo_responsable',
                )
                ->orderBy('cvgastoadministrativooperativo.id','ASC')
                ->get();
        
            $pdf = PDF::loadView(sistema_view().'/cvgastoadministrativooperativo/exportar_pdf',[
                'tienda' => $tienda,
                'gastoadministrativooperativo' => $gastoadministrativooperativo,
                'fechainicio' => $request->fechainicio,
                'fechafin' => $request->fechafin,
            ]); 
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('GESTION_COBRANZA.pdf');
        } 

        elseif($request->input('view') == 'validar') {

            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[1,2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.id as idpermiso','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/cvgastoadministrativooperativo/validar',[
                'tienda' => $tienda,
                'usuarios' => $usuarios,
                'idgastoadministrativooperativo' => $id,
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
          
            DB::table('cvgastoadministrativooperativo')->whereId($id)->update([
               'nombre' => $request->input('nombre'),
               'estado' => $request->input('estado'),
            ]);
          
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
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
          
            DB::table('cvgastoadministrativooperativo')->whereId($id)->update([
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
            
                DB::table('cvgastoadministrativooperativo')->whereId($id)->update([
                'fecha_eliminado' => now(),
                'idestadoeliminado' => 2,
                'idresponsble_eliminado' => Auth::user()->id,
                ]);
            
                //DB::table('cvgastoadministrativooperativo')->whereId($id)->delete();
                return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha elimino correctamente.'
                ]);
        }
    }
}
