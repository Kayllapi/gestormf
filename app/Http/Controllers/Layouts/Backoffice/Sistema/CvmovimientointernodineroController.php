<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use PDF;
use Carbon\Carbon;

class CvmovimientointernodineroController extends Controller
{
    public function __construct()
    {
       
    }
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $apertura_caja = cvapertura($idtienda);
        $arqueocaja = cvarqueocaja($idtienda);

        $validacionDiaria = validacionDiaria($idtienda);

        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/cvmovimientointernodinero/tabla', compact(
                'tienda',
                'apertura_caja',
                'arqueocaja',
                'validacionDiaria'
            ));
        }
    }
  
    public function create(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        if($request->view == 'registrar_retiro1') {
            $bancos = DB::table('banco')->where('estado','ACTIVO')->get();
            $fuenteretiros= DB::table('credito_fuenteretiro')
              ->where('idtipo',2)
              ->whereIn('id',[6,7,8,9])
              ->get();
            $apertura_caja = cvapertura($idtienda);
            return view(sistema_view().'/cvmovimientointernodinero/create_retiro1',[
                'tienda' => $tienda,
                'bancos' => $bancos,
                'fuenteretiros' => $fuenteretiros,
                'apertura_caja' => $apertura_caja,
            ]);
        }
        elseif($request->view == 'registrar_deposito1') {
            $bancos = DB::table('banco')->where('estado','ACTIVO')->get();
            $fuenteretiros= DB::table('credito_fuenteretiro')
              ->where('idtipo',1)
              ->whereIn('id',[1,2,3,4])
              ->get();
            return view(sistema_view().'/cvmovimientointernodinero/create_deposito1',[
                'tienda' => $tienda,
                'bancos' => $bancos,
                'fuenteretiros' => $fuenteretiros,
            ]);
        }
        elseif($request->view == 'registrar_retiro3') {
            $bancos = DB::table('banco')->where('estado','ACTIVO')->get();
            $fuenteretiros= DB::table('credito_fuenteretiro')
                ->where('idtipo',2)
                ->whereIn('id',[6,8])
                ->get();
            return view(sistema_view().'/cvmovimientointernodinero/create_retiro3',[
                'tienda' => $tienda,
                'bancos' => $bancos,
                'fuenteretiros' => $fuenteretiros,
            ]);
        }
        elseif($request->view == 'registrar_deposito3') {
            $bancos = DB::table('banco')->where('estado','ACTIVO')->get();
            $fuenteretiros= DB::table('credito_fuenteretiro')
              ->where('idtipo',1)
              ->whereIn('id',[1,3])
              ->get();
            return view(sistema_view().'/cvmovimientointernodinero/create_deposito3',[
                'tienda' => $tienda,
                'bancos' => $bancos,
                'fuenteretiros' => $fuenteretiros,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->input('view') == 'registrar_retiro1') {
            $rules = [
                'idfuenteretiro_retiro1' => 'required', 
                'monto_retiro1' => 'required', 
                'descripcion_retiro1' => 'required',        
            ];
          
            $messages = [
                'idfuenteretiro_retiro1.required' => 'La "Fuente de Retiro" es Obligatorio.',
                'monto_retiro1.required' => 'El "Monto" es Obligatorio.',
                'descripcion_retiro1.required' => 'La "Descricpión" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            if($request->idtipodestino==2){
                $rules = [
                    'idbanco_retiro1' => 'required',                  
                    'numerooperacion_retiro1' => 'required',                       
                ];

                $messages = [
                    'idbanco_retiro1.required' => 'El Campo Banco es Obligatorio.',
                    'numerooperacion_retiro1.required' => 'El Campo Número de Operación es Obligatorio.',
                ];
                $this->validate($request,$rules,$messages);
            }
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha validado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'registrar_retiro1_insert') {
            // --- RETIRO
            $consolidadooperaciones = cvconsolidadooperaciones($tienda,$idtienda,now()->format('Y-m-d'));
            if($request->idfuenteretiro_retiro1==6){
                if($consolidadooperaciones['saldos_reserva']<$request->monto_retiro1){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'No hay saldo suficiente en RESERVA CF.<br><b>Saldo Actual: S/. '.$consolidadooperaciones['saldos_reserva'].'.</b>.'
                    ]);
                }
            }
            elseif($request->idfuenteretiro_retiro1==7){
                foreach($consolidadooperaciones['saldos_cuentabanco_bancos'] as $value){
                    if($value['banco_id']==$request->idbanco_retiro1 && $value['banco']<$request->monto_retiro1){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay saldo suficiente en Cuenta Bancaria.<br><b>Saldo Actual: S/. '.$value['banco'].'.</b>'
                        ]);
                    }
                }  
            }
            elseif($request->idfuenteretiro_retiro1==8 || $request->idfuenteretiro_retiro1==9){
                if($consolidadooperaciones['saldos_caja']<$request->monto_retiro1){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'No hay saldo suficiente en CAJA.<br><b>Saldo Actual: S/. '.$consolidadooperaciones['saldos_caja'].'.</b>.'
                    ]);
                }
            }

            $bancoo = DB::table('banco')->where('banco.id',$request->idbanco_retiro1)->first();
          
            $movimientointernodinero = DB::table('cvmovimientointernodinero')
                ->where('cvmovimientointernodinero.idtipomovimientointerno',1)
                ->orderBy('cvmovimientointernodinero.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($movimientointernodinero!=''){
                $codigo = $movimientointernodinero->codigo+1;
            }
          
            $banco = '';
            $cuenta = '';
            if($bancoo!=''){
                $banco = $bancoo->nombre;
                $cuenta = $bancoo->cuenta;
            }
          
            $idmovimientointernodinero = DB::table('cvmovimientointernodinero')->insertGetId([
                'fecharegistro' => now(),
                'codigoprefijo' => 'MRV',
                'codigo' => $codigo,
                'monto' => $request->input('monto_retiro1'),
                'descripcion' => $request->input('descripcion_retiro1'),
                'numerooperacion' => $request->numerooperacion_retiro1!=''?$request->numerooperacion_retiro1:'',
                'banco' => $banco,
                'cuenta' => $cuenta,
                'idbanco' => $request->idbanco_retiro1!=''?$request->idbanco_retiro1:0,
                'idfuenteretiro' => $request->idfuenteretiro_retiro1,
                'idresponsable' => $request->idresponsable_retiro1,
                'idresponsable_permiso' => $request->idresponsable_permiso_retiro1,
                'idtipomovimientointerno' => 1, 
                'idtienda' => user_permiso()->idtienda,
                'idestadoeliminado' => 1,
                'idestado' => 1,
            ]);
          
            // DEPOSITO
            $movimientointernodinero = DB::table('cvmovimientointernodinero')
                ->where('cvmovimientointernodinero.idtipomovimientointerno',2)
                ->orderBy('cvmovimientointernodinero.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($movimientointernodinero!=''){
                $codigo = $movimientointernodinero->codigo+1;
            }
          
            DB::table('cvmovimientointernodinero')->insert([
                'fecharegistro' => now(),
                'codigoprefijo' => 'MDV',
                'codigo' => $codigo,
                'monto' => $request->input('monto_retiro1'),
                'descripcion' => $request->input('descripcion_retiro1'),
                'numerooperacion' => $request->numerooperacion_retiro1!=''?$request->numerooperacion_retiro1:'',
                'banco' => $banco,
                'cuenta' => $cuenta,
                'idbanco' => $request->idbanco_retiro1!=''?$request->idbanco_retiro1:0,
                'idfuenteretiro' => $request->idfuenteretiro_retiro1-5,
                'idtipomovimientointerno' => 2, 
                'idcvmovimientointernodinero' => $idmovimientointernodinero, 
                'idtienda' => user_permiso()->idtienda,
                'idestadoeliminado' => 1,
                'idestado' => 1,
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        /*elseif($request->input('view') == 'registrar_deposito1') {
            $rules = [
                'idfuenteretiro_deposito1' => 'required', 
                'monto_deposito1' => 'required', 
                'descripcion_deposito1' => 'required',        
            ];
          
            $messages = [
                'idfuenteretiro_deposito1.required' => 'La "Fuente de Retiro" es Obligatorio.',
                'monto_deposito1.required' => 'El "Monto" es Obligatorio.',
                'descripcion_deposito1.required' => 'La "Descricpión" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            if($request->idtipodestino==2){
                $rules = [
                    'idbanco_deposito1' => 'required',                  
                    'numerooperacion_deposito1' => 'required',                       
                ];

                $messages = [
                    'idbanco_deposito1.required' => 'El Campo Banco es Obligatorio.',
                    'numerooperacion_deposito1.required' => 'El Campo Número de Operación es Obligatorio.',
                ];
                $this->validate($request,$rules,$messages);
            } 
          
            DB::table('movimientointernodinero')->whereId()->insert([
                'fecharegistro' => now(),
                'idresponsable' => Auth::user()->id,
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha validado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'registrar_deposito1_insert') {

            $movimientointernodinero = DB::table('movimientointernodinero')
                ->where('movimientointernodinero.idtipomovimientointerno',2)
                ->orderBy('movimientointernodinero.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($movimientointernodinero!=''){
                $codigo = $movimientointernodinero->codigo+1;
            }
          
            $bancoo = DB::table('banco')->where('banco.id',$request->idbanco_deposito1)->first();

            $banco = '';
            $cuenta = '';
            if($bancoo!=''){
                $banco = $bancoo->nombre;
                $cuenta = $bancoo->cuenta;
            }
          
            DB::table('movimientointernodinero')->insert([
                'codigoprefijo' => 'OMD',
                'codigo' => $codigo,
                'monto' => $request->input('monto_deposito1'),
                'descripcion' => $request->input('descripcion_deposito1'),
                'numerooperacion' => $request->numerooperacion_deposito1!=''?$request->numerooperacion_deposito1:'',
                'banco' => $banco,
                'cuenta' => $cuenta,
                'idbanco' => $request->idbanco_deposito1!=''?$request->idbanco_deposito1:0,
                'idfuenteretiro' => $request->idfuenteretiro_deposito1,
                'idresponsable' => Auth::user()->id,
                'idtipomovimientointerno' => 2, 
                'idtienda' => user_permiso()->idtienda,
                'idestadoeliminado' => 1,
                'idestado' => 1,
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }*/
        elseif($request->input('view') == 'registrar_retiro3') {
            $rules = [
                'idfuenteretiro_retiro3' => 'required', 
                'monto_retiro3' => 'required',  
                'descripcion_retiro3' => 'required',        
            ];
          
            $messages = [
                'idfuenteretiro_retiro3.required' => 'La "Fuente de Retiro" es Obligatorio.',
                'monto_retiro3.required' => 'El "Monto" es Obligatorio.',
                'descripcion_retiro3.required' => 'La "Descricpión" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            // validar si hay una apertura de caja o cierre de caja en el mismo día
            $fecharegularizacion = now();
            if ($request->fecharegularizacion!='') {
                $fecharegularizacion = Carbon::parse($request->fecharegularizacion)->setTime(now()->hour, now()->minute, now()->second);
            }

            $apertura_existe = DB::table('cvmovimientointernodinero')
                ->where('idtienda',user_permiso()->idtienda)
                ->where('idfuenteretiro', 6)
                ->where('idtipomovimientointerno', 5)
                ->where('idestadoeliminado', 1)
                ->whereBetween('fecharegistro', [
                    $fecharegularizacion->copy()->startOfDay(),
                    $fecharegularizacion->copy()->endOfDay()
                ])
                ->exists();
            if ($request->idfuenteretiro_retiro3 == 8 && !$apertura_existe) {
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No puede cerrar caja porque no hay apertura de caja.'
                ]);
            }

            if($request->idfuenteretiro_retiro3 == 8){
                $datenow = now()->format('Y-m-d');
                $tienda = DB::table('tienda')->whereId(user_permiso()->idtienda)->first();
                $co = cvconsolidadooperaciones($tienda,user_permiso()->idtienda,$datenow);

                if ($request->monto_retiro3 < $co['saldos_caja']) {
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El saldo contable a retirar para reserva CF es S/. ' . $co['saldos_caja']
                    ]);
                }
            }


            $dt = DB::table('cvmovimientointernodinero')
                ->where('idtienda',user_permiso()->idtienda)
                ->where('idfuenteretiro', $request->idfuenteretiro_retiro3)
                ->where('idtipomovimientointerno', 5)
                ->where('idestadoeliminado', 1)
                ->where('fecharegistro', '>=', now()->format('Y-m-d 00:00:00'))
                ->where('fecharegistro', '<=', now()->format('Y-m-d 23:59:59'))
                ->first();
            if($dt!=''){
                if ($request->idfuenteretiro_retiro3 == 6) {
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Ya existe operación de Apertura de Caja .'
                    ]);
                }
                elseif ($request->idfuenteretiro_retiro3 == 8) {
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Ya existe operación de Cierre de Caja.'
                    ]);
                }
            }

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha validado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'registrar_retiro3_insert') {
            $validacionDiaria = validacionDiaria($idtienda);
            // --- RETIRO
            $consolidadooperaciones = cvconsolidadooperaciones($tienda,$idtienda,now()->format('Y-m-d'));
            if($request->idfuenteretiro_retiro3==6){
                if (!$validacionDiaria['cierre_caja']) {
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Falta cerrar caja '.$validacionDiaria['fechacorte'].'!!'
                    ]);
                }
                if($consolidadooperaciones['saldos_reserva']<$request->monto_retiro3){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'No hay saldo suficiente en RESERVA CF.<br><b>Saldo Actual: S/. '.$consolidadooperaciones['saldos_reserva'].'.</b>'
                    ]);
                }
            }
            elseif($request->idfuenteretiro_retiro3==8){
                if (!$validacionDiaria['arqueocaja']) {
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Falta arquear caja '.$validacionDiaria['fechacorte'].'!!'
                    ]);
                }
                if($consolidadooperaciones['saldos_caja']<$request->monto_retiro3){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'No hay saldo suficiente en CAJA.<br><b>Saldo Actual: S/. '.$consolidadooperaciones['saldos_caja'].'.</b>'
                    ]);
                }
            }
          
            $movimientointernodinero = DB::table('cvmovimientointernodinero')
                ->where('cvmovimientointernodinero.idtipomovimientointerno',5)
                ->orderBy('cvmovimientointernodinero.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($movimientointernodinero!=''){
                $codigo = $movimientointernodinero->codigo+1;
            }

            $fecharegistro = now();
            $fecharegularizacion = null;
            if ($request->fecharegularizacion!='') {
                $fecharegistro = Carbon::parse($request->fecharegularizacion)->setTime(now()->hour, now()->minute, now()->second);
                $fecharegularizacion = now();
            }

            $idmovimientointernodinero = DB::table('cvmovimientointernodinero')->insertGetId([
                'fecharegistro' => $fecharegistro,
                'fecharegularizacion' => $fecharegularizacion,
                'codigoprefijo' => 'ACRV',
                'codigo' => $codigo,
                'monto' => $request->input('monto_retiro3'),
                'descripcion' => $request->input('descripcion_retiro3'),
                'numerooperacion' => $request->numerooperacion_retiro3!=''?$request->numerooperacion_retiro3:'',
                'banco' => '',
                'cuenta' => '',
                'idbanco' => 0,
                'idfuenteretiro' => $request->idfuenteretiro_retiro3,
                'idresponsable' => $request->idresponsable_retiro3,
                'idresponsable_permiso' => $request->idresponsable_permiso_retiro3,
                'idtipomovimientointerno' => 5, 
                'idtienda' => user_permiso()->idtienda,
                'idestadoeliminado' => 1,
                'idestado' => 1,
            ]);
          
            // --- DEPOSITO
            $movimientointernodinero = DB::table('cvmovimientointernodinero')
                ->where('cvmovimientointernodinero.idtipomovimientointerno',6)
                ->orderBy('cvmovimientointernodinero.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($movimientointernodinero!=''){
                $codigo = $movimientointernodinero->codigo+1;
            }

            DB::table('cvmovimientointernodinero')->insert([
                'fecharegistro' => $fecharegistro,
                'fecharegularizacion' => $fecharegularizacion,
                'codigoprefijo' => 'ACDV',
                'codigo' => $codigo,
                'monto' => $request->input('monto_retiro3'),
                'descripcion' => $request->input('descripcion_retiro3'),
                'numerooperacion' => $request->numerooperacion_retiro3!=''?$request->numerooperacion_retiro3:'',
                'banco' => '',
                'cuenta' => '',
                'idbanco' => 0,
                'idfuenteretiro' => $request->idfuenteretiro_retiro3-5,
                'idtipomovimientointerno' => 6, 
                'idcvmovimientointernodinero' => $idmovimientointernodinero, 
                'idtienda' => user_permiso()->idtienda,
                'idestadoeliminado' => 1,
                'idestado' => 1,
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        /*elseif($request->input('view') == 'registrar_deposito3') {
            $rules = [
                'idfuenteretiro_deposito3' => 'required', 
                'monto_deposito3' => 'required',   
                'descripcion_deposito3' => 'required',        
            ];
          
            $messages = [
                'idfuenteretiro_deposito3.required' => 'La "Fuente de Retiro" es Obligatorio.',
                'monto_deposito3.required' => 'El "Monto" es Obligatorio.',
                'descripcion_deposito3.required' => 'La "Descricpión" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha validado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'registrar_deposito3_insert') {

            $movimientointernodinero = DB::table('movimientointernodinero')
                ->where('movimientointernodinero.idtipomovimientointerno',6)
                ->orderBy('movimientointernodinero.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($movimientointernodinero!=''){
                $codigo = $movimientointernodinero->codigo+1;
            }

            DB::table('movimientointernodinero')->insert([
                'codigoprefijo' => 'OACD',
                'codigo' => $codigo,
                'monto' => $request->input('monto_deposito3'),
                'descripcion' => $request->input('descripcion_deposito3'),
                'numerooperacion' => $request->numerooperacion_deposito3!=''?$request->numerooperacion_deposito3:'',
                'banco' => '',
                'cuenta' => '',
                'idbanco' => 0,
                'idfuenteretiro' => $request->idfuenteretiro_deposito3,
                'idresponsable' => Auth::user()->id,
                'idtipomovimientointerno' => 6, 
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
        if($id=='show_table_retiro1'){
            $where = [];
            if($request->input('fechainicio') != ''){
                $where[] = ['cvmovimientointernodinero.fecharegistro','>=',$request->fechainicio.' 00:00:00'];
            }
            if($request->input('fechafin') != ''){
                $where[] = ['cvmovimientointernodinero.fecharegistro','<=',$request->fechafin.' 23:59:59']; 
            }
            $movimientointernodinero = DB::table('cvmovimientointernodinero')
                ->join('credito_fuenteretiro','credito_fuenteretiro.id','cvmovimientointernodinero.idfuenteretiro')
                ->join('users as responsable','responsable.id','cvmovimientointernodinero.idresponsable')
                ->join('tienda','tienda.id','cvmovimientointernodinero.idtienda')
                ->where('cvmovimientointernodinero.idestadoeliminado',1)
                ->where('cvmovimientointernodinero.idtipomovimientointerno',1)
                ->where($where)
                ->select(
                    'cvmovimientointernodinero.*',
                    'credito_fuenteretiro.nombre as credito_fuenteretironombre',
                    'tienda.nombreagencia as tiendanombre',
                    'responsable.codigo as codigo_responsable',
                )
                ->orderBy('cvmovimientointernodinero.id','ASC')
                ->get();
  
            $total = 0;
            $html = '';
            foreach($movimientointernodinero as $key => $value){
                $fecharegistro = date_format(date_create($value->fecharegistro),"d-m-Y H:i:s A");
                $cuenta = $value->banco!=''?$value->banco.' - ***'.substr($value->cuenta, -4):'';
                $numerooperacion = $value->banco!=''?$value->numerooperacion:'';
                $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data_retiro1(this)'>
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
                $html.= '<tr><td colspan="8" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
            }
               
            $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <td colspan="2" style="background-color: #c2c0c2 !important; font-weight: bold; text-align:right;">Total Retiros (S/.)</td>
                  <td style="background-color: #c2c0c2 !important; font-weight: bold; text-align:right;">'.number_format($total, 2, '.', '').'</td>
                  <td colspan="5" style="background-color: #c2c0c2 !important; font-weight: bold; text-align:right;"></td>
                </tr>'; 
          
            return array(
                'html' => $html
            );
        }
        elseif($id=='show_table_deposito1'){
            $where = [];
            if($request->input('fechainicio') != ''){
                $where[] = ['cvmovimientointernodinero.fecharegistro','>=',$request->fechainicio.' 00:00:00'];
            }
            if($request->input('fechafin') != ''){
                $where[] = ['cvmovimientointernodinero.fecharegistro','<=',$request->fechafin.' 23:59:59']; 
            }
            $movimientointernodinero = DB::table('cvmovimientointernodinero')
                ->join('credito_fuenteretiro','credito_fuenteretiro.id','cvmovimientointernodinero.idfuenteretiro')
                ->leftJoin('users as responsable','responsable.id','cvmovimientointernodinero.idresponsable')
                ->join('tienda','tienda.id','cvmovimientointernodinero.idtienda')
                ->where('cvmovimientointernodinero.idestadoeliminado',1)
                ->where('cvmovimientointernodinero.idtipomovimientointerno',2)
                ->where($where)
                ->select(
                    'cvmovimientointernodinero.*',
                    'credito_fuenteretiro.nombre as credito_fuenteretironombre',
                    'tienda.nombreagencia as tiendanombre',
                    'responsable.codigo as codigo_responsable',
                )
                ->orderBy('cvmovimientointernodinero.id','ASC')
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
                $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data_deposito1(this)' ".$bgcolor.">
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
                $html.= '<tr><td colspan="8" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
            }
               
            $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <td colspan="2" style="background-color: #c2c0c2 !important; font-weight: bold; text-align:right;">Total Depósitos (S/.)</td>
                  <td style="background-color: #c2c0c2 !important; font-weight: bold; text-align:right;">'.number_format($total, 2, '.', '').'</td>
                  <td colspan="5" style="background-color: #c2c0c2 !important; font-weight: bold; text-align:right;"></td>
                </tr>'; 
          
            return array(
                'html' => $html
            );
        }
        elseif($id=='show_table_retiro3'){
            $where = [];
            if($request->input('fechainicio') != ''){
                $where[] = ['cvmovimientointernodinero.fecharegistro','>=',$request->fechainicio.' 00:00:00'];
            }
            if($request->input('fechafin') != ''){
                $where[] = ['cvmovimientointernodinero.fecharegistro','<=',$request->fechafin.' 23:59:59']; 
            }
            $movimientointernodinero = DB::table('cvmovimientointernodinero')
                ->join('credito_fuenteretiro','credito_fuenteretiro.id','cvmovimientointernodinero.idfuenteretiro')
                ->join('users as responsable','responsable.id','cvmovimientointernodinero.idresponsable')
                ->join('tienda','tienda.id','cvmovimientointernodinero.idtienda')
                ->where('cvmovimientointernodinero.idestadoeliminado',1)
                ->where('cvmovimientointernodinero.idtipomovimientointerno',5)
                ->where($where)
                ->select(
                    'cvmovimientointernodinero.*',
                    'credito_fuenteretiro.nombre as credito_fuenteretironombre',
                    'tienda.nombreagencia as tiendanombre',
                    'responsable.codigo as codigo_responsable',
                )
                ->orderBy('cvmovimientointernodinero.id','ASC')
                ->get();
  
            $total = 0;
            $html = '';
            foreach($movimientointernodinero as $key => $value){
                $fecharegistro = date_format(date_create($value->fecharegistro),"d-m-Y H:i:s A");
                $fecharegularizacion = $value->fecharegularizacion!='' ? date_format(date_create($value->fecharegularizacion),"d-m-Y H:i:s A") : '---';
                $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data_retiro3(this)'>
                              <td style='white-space: nowrap;'>{$value->codigoprefijo}{$value->codigo}</td>
                              <td style='white-space: nowrap;'>{$value->credito_fuenteretironombre}</td>
                              <td style='white-space: nowrap;text-align:right;'>{$value->monto}</td>
                              <td style='white-space: nowrap;'>{$value->descripcion}</td>
                              <td style='white-space: nowrap;'>{$fecharegistro}</td>
                              <td style='white-space: nowrap;'>{$fecharegularizacion}</td>
                              <td style='white-space: nowrap;'>{$value->codigo_responsable}</td>
                          </tr>";
                $total = $total+$value->monto;
            }
          
            if(count($movimientointernodinero)==0){
                $html.= '<tr><td colspan="8" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
            }
               
            $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <td colspan="2" style="background-color: #c2c0c2 !important; font-weight: bold; text-align:right;">Total Retiros (S/.)</td>
                  <td style="background-color: #c2c0c2 !important; font-weight: bold; text-align:right;">'.number_format($total, 2, '.', '').'</td>
                  <td colspan="4" style="background-color: #c2c0c2 !important; font-weight: bold; text-align:right;"></td>
                </tr>'; 
          
            return array(
                'html' => $html
            );
        }
        elseif($id=='show_table_deposito3'){
            $where = [];
            if($request->input('fechainicio') != ''){
                $where[] = ['cvmovimientointernodinero.fecharegistro','>=',$request->fechainicio.' 00:00:00'];
            }
            if($request->input('fechafin') != ''){
                $where[] = ['cvmovimientointernodinero.fecharegistro','<=',$request->fechafin.' 23:59:59']; 
            }
            $movimientointernodinero = DB::table('cvmovimientointernodinero')
                ->join('credito_fuenteretiro','credito_fuenteretiro.id','cvmovimientointernodinero.idfuenteretiro')
                ->leftJoin('users as responsable','responsable.id','cvmovimientointernodinero.idresponsable')
                ->join('tienda','tienda.id','cvmovimientointernodinero.idtienda')
                ->where('cvmovimientointernodinero.idestadoeliminado',1)
                ->where('cvmovimientointernodinero.idtipomovimientointerno',6)
                ->where($where)
                ->select(
                    'cvmovimientointernodinero.*',
                    'credito_fuenteretiro.nombre as credito_fuenteretironombre',
                    'tienda.nombreagencia as tiendanombre',
                    'responsable.codigo as codigo_responsable',
                )
                ->orderBy('cvmovimientointernodinero.id','ASC')
                ->get();
  
            $total = 0;
            $html = '';
            foreach($movimientointernodinero as $key => $value){
                $fecharegistro = date_format(date_create($value->fecharegistro),"d-m-Y H:i:s A");
                $fecharegularizacion = $value->fecharegularizacion!='' ? date_format(date_create($value->fecharegularizacion),"d-m-Y H:i:s A") : '---';
                $bgcolor = '';
                if($value->idresponsable==0){
                    $bgcolor = 'style="background-color: #ffb8b8;"';
                }
                $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data_deposito3(this)' ".$bgcolor.">
                              <td style='white-space: nowrap;'>{$value->codigoprefijo}{$value->codigo}</td>
                              <td style='white-space: nowrap;'>{$value->credito_fuenteretironombre}</td>
                              <td style='white-space: nowrap;text-align:right;'>{$value->monto}</td>
                              <td style='white-space: nowrap;'>{$value->descripcion}</td>
                              <td style='white-space: nowrap;'>{$fecharegistro}</td>
                              <td style='white-space: nowrap;'>{$fecharegularizacion}</td>
                              <td style='white-space: nowrap;'>{$value->codigo_responsable}</td>
                          </tr>";
                $total = $total+$value->monto;
            }
          
            if(count($movimientointernodinero)==0){
                $html.= '<tr><td colspan="7" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
            }
               
            $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <td colspan="2" style="background-color: #c2c0c2 !important; font-weight: bold; text-align:right;">Total Depósitos (S/.)</td>
                  <td style="background-color: #c2c0c2 !important; font-weight: bold; text-align:right;">'.number_format($total, 2, '.', '').'</td>
                  <td colspan="4" style="background-color: #c2c0c2 !important; font-weight: bold; text-align:right;"></td>
                </tr>'; 
          
            return array(
                'html' => $html
            );
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $movimientointernodinero = DB::table('cvmovimientointernodinero')
                      ->where('cvmovimientointernodinero.id',$id)
                      ->select(
                          'cvmovimientointernodinero.*',
                      )
                      ->orderBy('cvmovimientointernodinero.id','desc')
                      ->first();
      
        if($request->input('view') == 'editar_retiro1'){
            $bancos = DB::table('banco')->where('estado','ACTIVO')->get();
            $fuenteretiros= DB::table('credito_fuenteretiro')
              ->where('idtipo',2)
              ->whereIn('id',[6,7,8,9])
              ->get();
            return view(sistema_view().'/cvmovimientointernodinero/edit_retiro1',[
                'tienda' => $tienda,
                'bancos' => $bancos,
                'fuenteretiros' => $fuenteretiros,
                'movimientointernodinero' => $movimientointernodinero,
            ]);
        } 
        elseif($request->input('view') == 'eliminar_retiro1'){
          
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();
            $apertura_caja = cvapertura($idtienda);
            return view(sistema_view().'/cvmovimientointernodinero/delete_retiro1',[
                'tienda' => $tienda,
                'movimientointernodinero' => $movimientointernodinero,
                'usuarios' => $usuarios,
                'apertura_caja' => $apertura_caja,
            ]);
        }
        elseif($request->input('view') == 'editar_deposito1'){
            $bancos = DB::table('banco')->where('estado','ACTIVO')->get();
            $fuenteretiros= DB::table('credito_fuenteretiro')
              ->where('idtipo',1)
              ->whereIn('id',[1,2,3,4])
              ->get();
            return view(sistema_view().'/cvmovimientointernodinero/edit_deposito1',[
                'tienda' => $tienda,
                'bancos' => $bancos,
                'fuenteretiros' => $fuenteretiros,
                'movimientointernodinero' => $movimientointernodinero,
            ]);
        } 
        elseif($request->input('view') == 'eliminar_deposito1'){
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/cvmovimientointernodinero/delete_deposito1',[
              'tienda' => $tienda,
              'movimientointernodinero' => $movimientointernodinero,
              'usuarios' => $usuarios,
            ]);
        }
        elseif($request->input('view') == 'editar_retiro3'){
            $bancos = DB::table('banco')->where('estado','ACTIVO')->get();
            $fuenteretiros= DB::table('credito_fuenteretiro')
              ->where('idtipo',2)
              ->whereIn('id',[6,8])
              ->get();
            return view(sistema_view().'/cvmovimientointernodinero/edit_retiro3',[
                'tienda' => $tienda,
                'bancos' => $bancos,
                'fuenteretiros' => $fuenteretiros,
                'movimientointernodinero' => $movimientointernodinero,
            ]);
        } 
        elseif($request->input('view') == 'eliminar_retiro3'){
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/cvmovimientointernodinero/delete_retiro3',[
                'tienda' => $tienda,
                'movimientointernodinero' => $movimientointernodinero,
                'usuarios' => $usuarios,
            ]);
        }
        elseif($request->input('view') == 'editar_deposito3'){
            $bancos = DB::table('banco')->where('estado','ACTIVO')->get();
            $fuenteretiros= DB::table('credito_fuenteretiro')
              ->where('idtipo',1)
              ->whereIn('id',[1,3])
              ->get();
            return view(sistema_view().'/cvmovimientointernodinero/edit_deposito3',[
                'tienda' => $tienda,
                'bancos' => $bancos,
                'fuenteretiros' => $fuenteretiros,
                'movimientointernodinero' => $movimientointernodinero,
            ]);
        } 
        elseif($request->input('view') == 'eliminar_deposito3'){
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/cvmovimientointernodinero/delete_deposito3',[
              'tienda' => $tienda,
              'movimientointernodinero' => $movimientointernodinero,
              'usuarios' => $usuarios,
            ]);
        }
        elseif($request->input('view') == 'valid_registro_retiro1') {
            $where = [];
            if($request->idfuenteretiro_retiro1==6){
                $where = [1,2];
            }
            elseif($request->idfuenteretiro_retiro1==7){
                $where = [2];
            }
            elseif($request->idfuenteretiro_retiro1==8){
                $where = [2,4];
            }
            elseif($request->idfuenteretiro_retiro1==9){
                $where = [2,4];
            }
            elseif($request->idfuenteretiro_retiro1==10){
                $where = [2];
            }
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',$where)
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.id as idpermiso','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/cvmovimientointernodinero/valid_registro_retiro1',[
                'tienda' => $tienda,
                'usuarios' => $usuarios,
            ]);
        }
        elseif($request->input('view') == 'valid_registro_retiro3') {
            $where = [];
            if($request->idfuenteretiro_retiro3==6){
                $where = [1,2];
            }
            elseif($request->idfuenteretiro_retiro3==7){
                $where = [2];
            }
            elseif($request->idfuenteretiro_retiro3==8){
                $where = [2,4];
            }
            elseif($request->idfuenteretiro_retiro3==9){
                $where = [2,4];
            }
            elseif($request->idfuenteretiro_retiro3==10){
                $where = [2];
            }
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',$where)
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.id as idpermiso','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/cvmovimientointernodinero/valid_registro_retiro3',[
                'tienda' => $tienda,
                'usuarios' => $usuarios,
            ]);
        }
        elseif($request->input('view') == 'valid_registro_deposito1') {
            $where = [];
            if($request->idfuenteretiro_deposito1==1){
                $where = [2,4];
            }
            elseif($request->idfuenteretiro_deposito1==2){
                $where = [2,4];
            }
            elseif($request->idfuenteretiro_deposito1==3){
                $where = [1,2];
            }
            elseif($request->idfuenteretiro_deposito1==4){
                $where = [1,2];
            }
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',$where)
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.id as idpermiso','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/cvmovimientointernodinero/valid_registro_deposito1',[
                'tienda' => $tienda,
                'usuarios' => $usuarios,
                'idmovimientointernodinero' => $id,
            ]);
        }
        elseif($request->input('view') == 'valid_registro_deposito3') {
            $where = [];
            if($request->idfuenteretiro_deposito3==1){
                $where = [2,4];
            }
            elseif($request->idfuenteretiro_deposito3==3){
                $where = [1,2];
            }
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',$where)
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.id as idpermiso','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/cvmovimientointernodinero/valid_registro_deposito3',[
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
            return view(sistema_view().'/cvmovimientointernodinero/valid_reporte',[
                'tienda' => $tienda,
                'usuarios' => $usuarios,
            ]);
        }
        elseif($request->input('view') == 'exportar') {
            return view(sistema_view().'/cvmovimientointernodinero/exportar',[
                'tienda' => $tienda,
                'fechainicio' => $request->fechainicio,
                'fechafin' => $request->fechafin,
            ]);
        }
        elseif( $request->input('view') == 'exportar_pdf' ){
              
            $where = [];
            if($request->input('fechainicio') != ''){
                $where[] = ['cvmovimientointernodinero.fecharegistro','>=',$request->fechainicio.' 00:00:00'];
            }
            if($request->input('fechafin') != ''){
                $where[] = ['cvmovimientointernodinero.fecharegistro','<=',$request->fechafin.' 23:59:59']; 
            }
            $movimientointernodinero_retiro1 = DB::table('cvmovimientointernodinero')
                ->join('credito_fuenteretiro','credito_fuenteretiro.id','cvmovimientointernodinero.idfuenteretiro')
                ->join('users as responsable','responsable.id','cvmovimientointernodinero.idresponsable')
                ->join('tienda','tienda.id','cvmovimientointernodinero.idtienda')
                ->where('cvmovimientointernodinero.idestadoeliminado',1)
                ->where('cvmovimientointernodinero.idtipomovimientointerno',1)
                ->where($where)
                ->select(
                    'cvmovimientointernodinero.*',
                    'credito_fuenteretiro.nombre as credito_fuenteretironombre',
                    'tienda.nombreagencia as tiendanombre',
                    'responsable.codigo as codigo_responsable',
                )
                ->orderBy('cvmovimientointernodinero.id','ASC')
                ->get();
          
            
            $movimientointernodinero_retiro2 = DB::table('cvmovimientointernodinero')
                ->join('credito_fuenteretiro','credito_fuenteretiro.id','cvmovimientointernodinero.idfuenteretiro')
                ->join('users as responsable','responsable.id','cvmovimientointernodinero.idresponsable')
                ->join('tienda','tienda.id','cvmovimientointernodinero.idtienda')
                ->where('cvmovimientointernodinero.idestadoeliminado',1)
                ->where('cvmovimientointernodinero.idtipomovimientointerno',3)
                ->where($where)
                ->select(
                    'cvmovimientointernodinero.*',
                    'credito_fuenteretiro.nombre as credito_fuenteretironombre',
                    'tienda.nombreagencia as tiendanombre',
                    'responsable.codigo as codigo_responsable',
                )
                ->orderBy('cvmovimientointernodinero.id','ASC')
                ->get();

            $movimientointernodinero_retiro3 = DB::table('cvmovimientointernodinero')
                ->join('credito_fuenteretiro','credito_fuenteretiro.id','cvmovimientointernodinero.idfuenteretiro')
                ->join('users as responsable','responsable.id','cvmovimientointernodinero.idresponsable')
                ->join('tienda','tienda.id','cvmovimientointernodinero.idtienda')
                ->where('cvmovimientointernodinero.idestadoeliminado',1)
                ->where('cvmovimientointernodinero.idtipomovimientointerno',5)
                ->where($where)
                ->select(
                    'cvmovimientointernodinero.*',
                    'credito_fuenteretiro.nombre as credito_fuenteretironombre',
                    'tienda.nombreagencia as tiendanombre',
                    'responsable.codigo as codigo_responsable',
                )
                ->orderBy('cvmovimientointernodinero.id','ASC')
                ->get();
          
            $movimientointernodinero_deposito1 = DB::table('cvmovimientointernodinero')
                ->join('credito_fuenteretiro','credito_fuenteretiro.id','cvmovimientointernodinero.idfuenteretiro')
                ->join('users as responsable','responsable.id','cvmovimientointernodinero.idresponsable')
                ->join('tienda','tienda.id','cvmovimientointernodinero.idtienda')
                ->where('cvmovimientointernodinero.idestadoeliminado',1)
                ->where('cvmovimientointernodinero.idtipomovimientointerno',2)
                ->where($where)
                ->select(
                    'cvmovimientointernodinero.*',
                    'credito_fuenteretiro.nombre as credito_fuenteretironombre',
                    'tienda.nombreagencia as tiendanombre',
                    'responsable.codigo as codigo_responsable',
                )
                ->orderBy('cvmovimientointernodinero.id','ASC')
                ->get();
            
            $movimientointernodinero_deposito2 = DB::table('cvmovimientointernodinero')
                ->join('credito_fuenteretiro','credito_fuenteretiro.id','cvmovimientointernodinero.idfuenteretiro')
                ->join('users as responsable','responsable.id','cvmovimientointernodinero.idresponsable')
                ->join('tienda','tienda.id','cvmovimientointernodinero.idtienda')
                ->where('cvmovimientointernodinero.idestadoeliminado',1)
                ->where('cvmovimientointernodinero.idtipomovimientointerno',4)
                ->where($where)
                ->select(
                    'cvmovimientointernodinero.*',
                    'credito_fuenteretiro.nombre as credito_fuenteretironombre',
                    'tienda.nombreagencia as tiendanombre',
                    'responsable.codigo as codigo_responsable',
                )
                ->orderBy('cvmovimientointernodinero.id','ASC')
                ->get();

            $movimientointernodinero_deposito3 = DB::table('cvmovimientointernodinero')
                ->join('credito_fuenteretiro','credito_fuenteretiro.id','cvmovimientointernodinero.idfuenteretiro')
                ->join('users as responsable','responsable.id','cvmovimientointernodinero.idresponsable')
                ->join('tienda','tienda.id','cvmovimientointernodinero.idtienda')
                ->where('cvmovimientointernodinero.idestadoeliminado',1)
                ->where('cvmovimientointernodinero.idtipomovimientointerno',6)
                ->where($where)
                ->select(
                    'cvmovimientointernodinero.*',
                    'credito_fuenteretiro.nombre as credito_fuenteretironombre',
                    'tienda.nombreagencia as tiendanombre',
                    'responsable.codigo as codigo_responsable',
                )
                ->orderBy('cvmovimientointernodinero.id','ASC')
                ->get();
          
            $agencia = DB::table('tienda')->whereId(user_permiso()->idtienda)->first();
        
            $pdf = PDF::loadView(sistema_view().'/cvmovimientointernodinero/exportar_pdf',[
                'tienda' => $tienda,
                'fechainicio' => $request->fechainicio,
                'fechafin' => $request->fechafin,
                'movimientointernodinero_retiro1' => $movimientointernodinero_retiro1,
                'movimientointernodinero_retiro2' => $movimientointernodinero_retiro2,
                'movimientointernodinero_retiro3' => $movimientointernodinero_retiro3,
                'movimientointernodinero_deposito1' => $movimientointernodinero_deposito1,
                'movimientointernodinero_deposito2' => $movimientointernodinero_deposito2,
                'movimientointernodinero_deposito3' => $movimientointernodinero_deposito3,
                'agencia' => $agencia,
            ]); 
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('OPERACIONES_DE_MOVIMIENTO_INTERNO.pdf');
        } 
        /*elseif($request->input('view') == 'valid_reporte_deposito') {
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/cvmovimientointernodinero/valid_reporte_deposito',[
                'tienda' => $tienda,
                'usuarios' => $usuarios,
            ]);
        }
        elseif($request->input('view') == 'exportar_deposito') {
            return view(sistema_view().'/cvmovimientointernodinero/exportar_deposito',[
                'tienda' => $tienda,
                'fechainicio' => $request->fechainicio,
                'fechafin' => $request->fechafin,
            ]);
        }
        elseif( $request->input('view') == 'exportar_pdf_deposito' ){          
            $where = [];
            if($request->input('fechainicio') != ''){
                $where[] = ['movimientointernodinero.fecharegistro','>=',$request->fechainicio.' 00:00:00'];
            }
            if($request->input('fechafin') != ''){
                $where[] = ['movimientointernodinero.fecharegistro','<=',$request->fechafin.' 23:59:59']; 
            }
            $movimientointernodinero_deposito1 = DB::table('movimientointernodinero')
                ->join('credito_fuenteretiro','credito_fuenteretiro.id','movimientointernodinero.idfuenteretiro')
                ->join('users as responsable','responsable.id','movimientointernodinero.idresponsable')
                ->join('tienda','tienda.id','movimientointernodinero.idtienda')
                ->where('movimientointernodinero.idestadoeliminado',1)
                ->where('movimientointernodinero.idtipomovimientointerno',2)
                ->where($where)
                ->select(
                    'movimientointernodinero.*',
                    'credito_fuenteretiro.nombre as credito_fuenteretironombre',
                    'tienda.nombreagencia as tiendanombre',
                    'responsable.codigo as codigo_responsable',
                )
                ->orderBy('movimientointernodinero.id','ASC')
                ->get();
            
            $movimientointernodinero_deposito2 = DB::table('movimientointernodinero')
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

            $movimientointernodinero_deposito3 = DB::table('movimientointernodinero')
                ->join('credito_fuenteretiro','credito_fuenteretiro.id','movimientointernodinero.idfuenteretiro')
                ->join('users as responsable','responsable.id','movimientointernodinero.idresponsable')
                ->join('tienda','tienda.id','movimientointernodinero.idtienda')
                ->where('movimientointernodinero.idestadoeliminado',1)
                ->where('movimientointernodinero.idtipomovimientointerno',2)
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
        
            $pdf = PDF::loadView(sistema_view().'/cvmovimientointernodinero/exportar_pdf_deposito',[
                'tienda' => $tienda,
                'fechainicio' => $request->fechainicio,
                'fechafin' => $request->fechafin,
                'movimientointernodinero_deposito1' => $movimientointernodinero_deposito1,
                'movimientointernodinero_deposito2' => $movimientointernodinero_deposito2,
                'movimientointernodinero_deposito3' => $movimientointernodinero_deposito3,
                'agencia' => $agencia,
            ]); 
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('OPERACIONES_DE_MOVIMIENTO_INTERNO.pdf');
        } */
    }

    public function update(Request $request, $idtienda, $id)
    {
        if($request->input('view') == 'valid_registro_retiro1'){
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
        elseif($request->input('view') == 'valid_registro_retiro3'){
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
        elseif($request->input('view') == 'valid_registro_deposito1'){
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
          
            DB::table('cvmovimientointernodinero')->whereId($id)->update([
                'idresponsable' => $request->idresponsable,
                'idresponsable_permiso' => $request->idresponsable_permiso,
            ]);
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha validado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'valid_registro_deposito3'){
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

            DB::table('cvmovimientointernodinero')->whereId($id)->update([
                'idresponsable' => $request->idresponsable,
                'idresponsable_permiso' => $request->idresponsable_permiso,
            ]);

            $arqueo = DB::table('cvarqueocaja')
                ->where('idcvmovimientointernodinero_cierre', 0)
                ->orderByDesc('id')
                ->first();

            // DB::table('cvmovimientointernodinero')->whereId($id)->update([
            //     'idcvarqueocaja_cierre' => $arqueo->id,
            // ]);
            updatearqueocaja($arqueo->id);

            if ($arqueo) {
                $tienda = DB::table('tienda')->whereId($arqueo->idagencia)->first();
                $ret_reservacf_caja_total = DB::table('cvmovimientointernodinero')
                    ->where('cvmovimientointernodinero.idestadoeliminado',1)
                    ->where('cvmovimientointernodinero.idfuenteretiro',6)
                    ->where('cvmovimientointernodinero.idtipomovimientointerno',5)
                    ->where('cvmovimientointernodinero.fecharegistro','>=',$arqueo->corte.' 00:00:00')
                    ->where('cvmovimientointernodinero.fecharegistro','<=',$arqueo->corte.' 23:59:59')
                    ->where('cvmovimientointernodinero.idtienda',$arqueo->idagencia)
                    ->first();
                $ret_caja_reservacf_total = DB::table('cvmovimientointernodinero')
                    ->where('cvmovimientointernodinero.idestadoeliminado',1)
                    ->where('cvmovimientointernodinero.idfuenteretiro',8)
                    ->where('cvmovimientointernodinero.idtipomovimientointerno',5)
                    ->where('cvmovimientointernodinero.fecharegistro','>=',$arqueo->corte.' 00:00:00')
                    ->where('cvmovimientointernodinero.fecharegistro','<=',$arqueo->corte.' 23:59:59')
                    ->where('cvmovimientointernodinero.idtienda',$arqueo->idagencia)
                    ->first();

                if($ret_reservacf_caja_total && $ret_caja_reservacf_total){
                    $co = cvconsolidadooperaciones($tienda,$arqueo->idagencia,$arqueo->corte);
                    DB::table('cvarqueocaja')->where('id',$arqueo->id)->update([
                        'ingresoyegresocaja_ingreso_ventas' => $co['ingresoyegresocaja_ingreso_cvventa'],
                        'ingresoyegresocaja_ingreso_incrementocapital' => $co['ingresoyegresocaja_ingreso_incrementocapital'],
                        'ingresoyegresocaja_ingreso_ingresosextraordinarios' => $co['ingresoyegresocaja_ingreso_ingresosextraordinarios'],
                        'ingresoyegresocaja_egreso_compras' => $co['ingresoyegresocaja_egreso_cvcompra'],
                        'ingresoyegresocaja_egreso_reduccioncapital' => $co['ingresoyegresocaja_egreso_reduccioncapital'],
                        'ingresoyegresocaja_egreso_gastosadministrativosyoperativos' => $co['ingresoyegresocaja_egreso_gastosadministrativosyoperativos'],

                        'ingresoyegresobanco_ingreso_ventas' => $co['ingresoyegresobanco_ingreso_cvventa'],
                        'ingresoyegresobanco_ingreso_ventas_bancos' => json_encode($co['ingresoyegresobanco_ingreso_cvventas']),
                        'ingresoyegresobanco_ingreso_ventas_validacion' => $co['ingresoyegresobanco_ingreso_cvventa_validacion'],
                        'ingresoyegresobanco_ingreso_ventas_validacion_cantidad' => $co['ingresoyegresobanco_ingreso_cvventa_validacion_cantidad'],
                        'ingresoyegresobanco_ingreso_incrementocapital' => $co['ingresoyegresobanco_ingreso_incrementocapital'],
                        'ingresoyegresobanco_ingreso_incrementocapital_bancos' => json_encode($co['ingresoyegresobanco_ingreso_incrementocapital_bancos']),
                        'ingresoyegresobanco_ingreso_incrementocapital_validacion' => $co['ingresoyegresobanco_ingreso_incrementocapital_validacion'],
                        'ingresoyegresobanco_ingreso_incrementocapital_validacion_cantida' => $co['ingresoyegresobanco_ingreso_incrementocapital_validacion_cantidad'],
                        'ingresoyegresobanco_ingreso_ingresosextraordinarios' => $co['ingresoyegresobanco_ingreso_ingresosextraordinarios'],
                        'ingresoyegresobanco_ingreso_ingresosextraordinarios_bancos' => json_encode($co['ingresoyegresobanco_ingreso_ingresosextraordinarios_bancos']),
                        'ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion' => $co['ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion'],
                        'ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion_c' => $co['ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion_cantidad'],

                        'ingresoyegresobanco_egreso_compras' => $co['ingresoyegresobanco_egreso_cvcompra'],
                        'ingresoyegresobanco_egreso_compras_bancos' => json_encode($co['ingresoyegresobanco_egreso_cvcompras']),
                        'ingresoyegresobanco_egreso_compras_validacion' => $co['ingresoyegresobanco_egreso_cvcompra_validacion'],
                        'ingresoyegresobanco_egreso_compras_validacion_cantidad' => $co['ingresoyegresobanco_egreso_cvcompra_validacion_cantidad'],
                        'ingresoyegresobanco_egreso_reduccioncapital' => $co['ingresoyegresobanco_egreso_reduccioncapital'],
                        'ingresoyegresobanco_egreso_reduccioncapital_bancos' => json_encode($co['ingresoyegresobanco_egreso_reduccioncapital_bancos']),
                        'ingresoyegresobanco_egreso_reduccioncapital_validacion' => $co['ingresoyegresobanco_egreso_reduccioncapital_validacion'],
                        'ingresoyegresobanco_egreso_reduccioncapital_validacion_cantidad' => $co['ingresoyegresobanco_egreso_reduccioncapital_validacion_cantidad'],
                        'ingresoyegresobanco_egreso_gastosadministrativosyoperativos' => $co['ingresoyegresobanco_egreso_gastosadministrativosyoperativos'],
                        'ingresoyegresobanco_egreso_gastosadministrativosyoperativos_banc' => json_encode($co['ingresoyegresobanco_egreso_gastosadministrativosyoperativos_bancos']),
                        'ingresoyegresobanco_egreso_gastosadministrativosyoperativos_vali' => $co['ingresoyegresobanco_egreso_gastosadministrativosyoperativos_validacion'],
                        'ingresoyegresobanco_egreso_gastosadministrativosyoperativos_cant' => $co['ingresoyegresobanco_egreso_gastosadministrativosyoperativos_validacion_cantidad'],

                        'dep_caja_banco' => $co['dep_caja_banco'],
                        'dep_caja_banco_bancos' => json_encode($co['ret_banco_caja_bancos']),
                        'dep_reservacf_caja' => $co['dep_reservacf_caja'],
                        'dep_banco_caja' => $co['dep_banco_caja'],
                        'dep_banco_caja_bancos' => json_encode($co['ret_caja_banco_bancos']),
                        'dep_reservacf_banco' => $co['dep_reservacf_banco'],
                        'dep_reservacf_banco_bancos' => json_encode($co['ret_banco_reservacf_bancos']),
                        'dep_caja_reservacf_total' => $co['dep_caja_reservacf_total'],
                        'dep_reservacf_caja_total' => $co['dep_reservacf_caja_total'],

                        'habilitacion_gestion_liquidez1' => $co['habilitacion_gestion_liquidez1'],
                        'habilitacion_gestion_liquidez2' => $co['habilitacion_gestion_liquidez2'],
                        'cierre_caja_apertura' => $co['cierre_caja_apertura'],
                        'saldos_capitalasignada' => $co['saldos_capitalasignada'],
                        'saldos_cuentabanco' => $co['saldos_cuentabanco'],
                        'saldos_cuentabanco_bancos' => json_encode($co['saldos_cuentabanco_bancos']),
                        'saldos_reserva' => $co['saldos_reserva'],
                        'saldos_caja' => $co['saldos_caja'],
                        'arqueo_caja' => $co['arqueo_caja'],
                        'saldos_bienescomprados' => $co['saldos_bienescomprados'],

                        'ret_reservacf_caja' => $co['ret_reservacf_caja_sum'],
                        'ret_banco_caja' => $co['ret_banco_caja_sum'],
                        'ret_banco_caja_bancos' => json_encode($co['ret_banco_caja_bancos']),
                        'ret_caja_reservacf' => $co['ret_caja_reservacf_sum'],
                        'ret_caja_banco' => $co['ret_caja_banco_sum'],
                        'ret_caja_banco_bancos' => json_encode($co['ret_caja_banco_bancos']),
                        'ret_banco_reservacf' => $co['ret_banco_reservacf'],
                        'ret_banco_reservacf_bancos' => json_encode($co['ret_banco_reservacf_bancos']),
                        'ret_reservacf_caja_total' => $co['ret_reservacf_caja_total'],
                        'ret_caja_reservacf_total' => $co['ret_caja_reservacf_total'],

                        'dep_caja_reservacf' => $co['dep_caja_reservacf'],
                        'total_efectivo_ejercicio' => $co['total_efectivo_ejercicio'],
                        'incremental_capital_asignado' => $co['incremental_capital_asignado'],
                        'indicador_reserva_legal' => $co['indicador_reserva_legal'],
                        'validacion_operaciones_cuenta_banco' => $co['validacion_operaciones_cuenta_banco'],
                        'efectivo_caja_corte' => $co['efectivo_caja_corte'],
                        'efectivo_caja_arqueo' => $co['efectivo_caja_arqueo'],
                        'resultado' => $co['resultado'],

                        'idcvmovimientointernodinero_cierre' => $id,
                    ]);
                }
            }

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
              'mensaje'   => 'Se ha validado correctamente.'
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
              'mensaje'   => 'Se ha validado correctamente.'
            ]);
        }
    }


    public function destroy(Request $request, $idtienda, $id)
    {
        if($request->input('view') == 'eliminar_retiro1'){
            $rules = [
                'idresponsable_retiro1' => 'required',          
                'responsableclave_retiro1' => 'required',              
            ];

            $messages = [
                'idresponsable_retiro1.required' => 'El "Responsable" es Obligatorio.',
                'responsableclave_retiro1.required' => 'La "Contraseña" es Obligatorio.',
            ];

            $this->validate($request,$rules,$messages);

            $usuario = DB::table('users')
                ->where('users.id',$request->idresponsable_retiro1)
                ->where('users.clave',$request->responsableclave_retiro1)
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

            DB::table('cvmovimientointernodinero')->whereId($id)->update([
               'fecha_eliminado' => now(),
               'idestadoeliminado' => 2,
               'idresponsble_eliminado' => $idresponsable,
            ]);

            /* $movimientointernodinero = DB::table('cvmovimientointernodinero')
                ->whereId($id)
                ->first(); */
          
            DB::table('cvmovimientointernodinero')->where('idcvmovimientointernodinero',$id)->update([
                'fecha_eliminado' => now(),
                'idestadoeliminado' => 2,
                'idresponsble_eliminado' => $idresponsable,
            ]);
            // DB::table('cvmovimientointernodinero')->whereId($id)->delete();
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha elimino correctamente.'
            ]);
        }
        /*elseif($request->input('view') == 'eliminar_deposito1'){
            $rules = [
                'idresponsable_deposito1' => 'required',          
                'responsableclave_deposito1' => 'required',              
            ];

            $messages = [
                'idresponsable_deposito1.required' => 'El "Responsable" es Obligatorio.',
                'responsableclave_deposito1.required' => 'La "Contraseña" es Obligatorio.',
            ];

            $this->validate($request,$rules,$messages);

            $usuario = DB::table('users')
                ->where('users.id',$request->idresponsable_deposito1)
                ->where('users.clave',$request->responsableclave_deposito1)
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

            DB::table('movimientointernodinero')->whereId($id)->delete();
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha elimino correctamente.'
            ]);
        }*/
        elseif($request->input('view') == 'eliminar_retiro3'){
            $rules = [
                'idresponsable_retiro3' => 'required',          
                'responsableclave_retiro3' => 'required',              
            ];

            $messages = [
                'idresponsable_retiro3.required' => 'El "Responsable" es Obligatorio.',
                'responsableclave_retiro3.required' => 'La "Contraseña" es Obligatorio.',
            ];

            $this->validate($request,$rules,$messages);

            $usuario = DB::table('users')
                ->where('users.id',$request->idresponsable_retiro3)
                ->where('users.clave',$request->responsableclave_retiro3)
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

            DB::table('cvmovimientointernodinero')->whereId($id)->update([
               'fecha_eliminado' => now(),
               'idestadoeliminado' => 2,
               'idresponsble_eliminado' => $idresponsable,
            ]);

            /* $movimientointernodinero = DB::table('cvmovimientointernodinero')
                ->whereId($id)
                ->first(); */

            DB::table('cvmovimientointernodinero')->where('idcvmovimientointernodinero',$id)->update([
                'fecha_eliminado' => now(),
                'idestadoeliminado' => 2,
                'idresponsble_eliminado' => $idresponsable,
            ]);
            // DB::table('cvmovimientointernodinero')->whereId($id)->delete();
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha elimino correctamente.'
            ]);
        }
        elseif($request->input('view') == 'eliminar_deposito3'){
            $rules = [
                'idresponsable_deposito3' => 'required',          
                'responsableclave_deposito3' => 'required',              
            ];

            $messages = [
                'idresponsable_deposito3.required' => 'El "Responsable" es Obligatorio.',
                'responsableclave_deposito3.required' => 'La "Contraseña" es Obligatorio.',
            ];

            $this->validate($request,$rules,$messages);

            $usuario = DB::table('users')
                ->where('users.id',$request->idresponsable_deposito3)
                ->where('users.clave',$request->responsableclave_deposito3)
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

            DB::table('cvmovimientointernodinero')->whereId($id)->update([
               'fecha_eliminado' => now(),
               'idestadoeliminado' => 2,
               'idresponsble_eliminado' => $idresponsable,
            ]);

            // DB::table('cvmovimientointernodinero')->whereId($id)->delete();
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha elimino correctamente.'
            ]);
        }
    
    }
}
