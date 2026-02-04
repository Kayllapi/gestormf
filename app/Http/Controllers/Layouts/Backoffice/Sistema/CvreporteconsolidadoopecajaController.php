<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class CvreporteconsolidadoopecajaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            
            $agencias = DB::table('tienda')->get();
            return view(sistema_view().'/cvreporteconsolidadoopecaja/tabla',[
                'tienda' => $tienda,
                'agencias' => $agencias,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
    }
  
    public function store(Request $request, $idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->input('view') == 'valid_registro_arqueocaja') {
            $rules = [
                'moneda_1' => 'required',          
                'moneda_2' => 'required',         
                'moneda_3' => 'required',         
                'moneda_4' => 'required',         
                'moneda_5' => 'required',         
                'moneda_6' => 'required',         
                'moneda_7' => 'required',         
                'moneda_8' => 'required',         
                'moneda_9' => 'required',         
                'moneda_10' => 'required',         
                'moneda_11' => 'required',              
            ];

            $messages = [
                'moneda_1.required' => 'La "Cantidad de Denominación de 0.10" es Obligatorio.',
                'moneda_2.required' => 'La "Cantidad de Denominación de 0.20" es Obligatorio.',
                'moneda_3.required' => 'La "Cantidad de Denominación de 0.50" es Obligatorio.',
                'moneda_4.required' => 'La "Cantidad de Denominación de 1.00" es Obligatorio.',
                'moneda_5.required' => 'La "Cantidad de Denominación de 2.00" es Obligatorio.',
                'moneda_6.required' => 'La "Cantidad de Denominación de 5.00" es Obligatorio.',
                'moneda_7.required' => 'La "Cantidad de Denominación de 10.00" es Obligatorio.',
                'moneda_8.required' => 'La "Cantidad de Denominación de 20.00" es Obligatorio.',
                'moneda_9.required' => 'La "Cantidad de Denominación de 50.00" es Obligatorio.',
                'moneda_10.required' => 'La "Cantidad de Denominación de 100.00" es Obligatorio.',
                'moneda_11.required' => 'La "Cantidad de Denominación de 200.00" es Obligatorio.',
            ];

            $this->validate($request,$rules,$messages);
          
            if($request->total_arqueocaja<=0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El TOTAL DE EFECTIVO FÍSICO EN CAJA AL ARQUEO debe ser mayor a S/. 0.00.'
                ]);
            }
            if($request->total_arqueocaja<$request->saldocaja_arqueocaja){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El TOTAL DE EFECTIVO FÍSICO EN CAJA AL ARQUEO debe ser mayor o igual a SALDO CONTABLE EN CAJA.'
                ]);
            }
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha validado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'submit_registro_arqueocaja'){
            $co = cvconsolidadooperaciones($tienda,$request->idagencia_arqueocaja,$request->corte_arqueocaja);

            $apertura_caja =  DB::table('cvmovimientointernodinero')
                ->where('cvmovimientointernodinero.idestadoeliminado',1)
                ->where('cvmovimientointernodinero.idfuenteretiro',1)
                ->where('cvmovimientointernodinero.idtipomovimientointerno',6)
                ->where('cvmovimientointernodinero.idresponsable','<>',0)
                ->where('cvmovimientointernodinero.fecharegistro','>=',$request->corte_arqueocaja.' 00:00:00')
                ->where('cvmovimientointernodinero.fecharegistro','<=',$request->corte_arqueocaja.' 23:59:59')
                ->where('cvmovimientointernodinero.idtienda',$request->idagencia_arqueocaja)
                ->first();

            if(is_null($apertura_caja)){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Falta confirmar apertura de caja.'
                ]);
            }

            $idarqueocaja = DB::table('cvarqueocaja')->insertGetId([
                'fecharegistro' => now(),
                'total' => $request->total_arqueocaja,
                'corte' => $request->corte_arqueocaja,

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

                'eliminado_idresponsable' => 0,
                'eliminado_idresponsable_permiso' => 0,
                'idcvmovimientointernodinero_apertura' => $apertura_caja->id,
                'idcvmovimientointernodinero_cierre' => 0,

                'idresponsable' => Auth::user()->id,
                'idresponsable_registro' => $request->idresponsable_registro,
                'idresponsable_registro_idpermiso' => $request->idresponsable_registro_idpermiso,
                'idagencia' => $request->idagencia_arqueocaja,
                'idtienda' => user_permiso()->idtienda,
                'idestadoeliminado' => 1,
                'idestado' => 1,
            ]);
          
            DB::table('cvarqueocaja_denominacion')->insert([
                'denominacion' => 0.10,
                'cantidad' => $request->moneda_1,
                'total' => $request->moneda_1*0.10,
                'tipo' => 1,
                'idarqueocaja' => $idarqueocaja,
                'idtienda' => user_permiso()->idtienda,
            ]);
          
            DB::table('cvarqueocaja_denominacion')->insert([
                'denominacion' => 0.20,
                'cantidad' => $request->moneda_2,
                'total' => $request->moneda_2*0.20,
                'tipo' => 1,
                'idarqueocaja' => $idarqueocaja,
                'idtienda' => user_permiso()->idtienda,
            ]);
          
            DB::table('cvarqueocaja_denominacion')->insert([
                'denominacion' => 0.50,
                'cantidad' => $request->moneda_3,
                'total' => $request->moneda_3*0.50,
                'tipo' => 1,
                'idarqueocaja' => $idarqueocaja,
                'idtienda' => user_permiso()->idtienda,
            ]);
          
            DB::table('cvarqueocaja_denominacion')->insert([
                'denominacion' => 1.00,
                'cantidad' => $request->moneda_4,
                'total' => $request->moneda_4*1.00,
                'tipo' => 1,
                'idarqueocaja' => $idarqueocaja,
                'idtienda' => user_permiso()->idtienda,
            ]);
          
            DB::table('cvarqueocaja_denominacion')->insert([
                'denominacion' => 2.00,
                'cantidad' => $request->moneda_5,
                'total' => $request->moneda_5*2.00,
                'tipo' => 1,
                'idarqueocaja' => $idarqueocaja,
                'idtienda' => user_permiso()->idtienda,
            ]);
          
            DB::table('cvarqueocaja_denominacion')->insert([
                'denominacion' => 5.00,
                'cantidad' => $request->moneda_6,
                'total' => $request->moneda_6*5.00,
                'tipo' => 1,
                'idarqueocaja' => $idarqueocaja,
                'idtienda' => user_permiso()->idtienda,
            ]);
          
            DB::table('cvarqueocaja_denominacion')->insert([
                'denominacion' => 10.00,
                'cantidad' => $request->moneda_7,
                'total' => $request->moneda_7*10.00,
                'tipo' => 2,
                'idarqueocaja' => $idarqueocaja,
                'idtienda' => user_permiso()->idtienda,
            ]);
          
            DB::table('cvarqueocaja_denominacion')->insert([
                'denominacion' => 20.00,
                'cantidad' => $request->moneda_8,
                'total' => $request->moneda_8*20.00,
                'tipo' => 2,
                'idarqueocaja' => $idarqueocaja,
                'idtienda' => user_permiso()->idtienda,
            ]);
          
            DB::table('cvarqueocaja_denominacion')->insert([
                'denominacion' => 50.00,
                'cantidad' => $request->moneda_9,
                'total' => $request->moneda_9*50.00,
                'tipo' => 2,
                'idarqueocaja' => $idarqueocaja,
                'idtienda' => user_permiso()->idtienda,
            ]);
          
            DB::table('cvarqueocaja_denominacion')->insert([
                'denominacion' => 100.00,
                'cantidad' => $request->moneda_10,
                'total' => $request->moneda_10*100.00,
                'tipo' => 2,
                'idarqueocaja' => $idarqueocaja,
                'idtienda' => user_permiso()->idtienda,
            ]);
          
            DB::table('cvarqueocaja_denominacion')->insert([
                'denominacion' => 200.00,
                'cantidad' => $request->moneda_11,
                'total' => $request->moneda_11*200.00,
                'tipo' => 2,
                'idarqueocaja' => $idarqueocaja,
                'idtienda' => user_permiso()->idtienda,
            ]);
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.',
              'corte'   => $request->corte_arqueocaja,
              'idagencia'   => $request->idagencia_arqueocaja,
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
    }

    public function edit(Request $request, $idtienda, $id)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->input('view') == 'pdf_reporte'){
            $co_actual = cvconsolidadooperaciones($tienda,$request->idagencia,$request->corte);

            $fechaCorte = Carbon::createFromFormat('Y-m-d', $request->corte);
            $fechaAnterior = $fechaCorte->copy()->subDay()->format('Y-m-d');
            $co_anterior = DB::table('cvarqueocaja')
                ->where('idagencia', $request->idagencia)
                ->where('corte', $fechaAnterior)
                ->orderByDesc('id')
                ->first();
            if (!$co_anterior) {
                $ultimo = DB::table('cvarqueocaja')
                    ->where('idagencia', $request->idagencia)
                    ->orderByDesc('id')
                    ->first();

                if ($ultimo && $ultimo->corte >= $request->corte) {
                    $co_anterior = DB::table('cvarqueocaja')
                        ->where('idagencia', $request->idagencia)
                        ->where('corte', '<', $request->corte)
                        ->orderByDesc('corte')
                        ->orderByDesc('id')
                        ->first();
                } else {
                    $co_anterior = $ultimo;
                }
            }
          $data_actual = DB::table('cvarqueocaja')
              ->where('idagencia',$request->idagencia)
              ->where('corte',$request->corte)
              ->first();
          $pdf = PDF::loadView(sistema_view().'/cvreporteconsolidadoopecaja/pdf_reporte',[
              'co_actual' => $co_actual,
              'co_anterior' => $co_anterior,
              'data_actual' => $data_actual,
          ]); 
          $pdf->setPaper('A4', 'landscape');
          return $pdf->stream('REPORTE_CONSOLIDADO_OPE_CAJA.pdf');
        }
        else if($request->input('view') == 'valid_registro_arqueocaja') {
          
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[2,4])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.id as idpermiso','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/cvreporteconsolidadoopecaja/valid_registro_arqueocaja',[
                'tienda' => $tienda,
                'usuarios' => $usuarios,
            ]);
        }
        else if($request->input('view') == 'arqueocaja') {
            $agencias = DB::table('tienda')->get();
            $agencia = DB::table('tienda')->whereId($request->idagencia)->first();
            $consolidadooperaciones = cvconsolidadooperaciones($tienda,$request->idagencia,$request->corte);
            $validacionArqueoCaja = validacionArqueoCaja($request->idagencia,$request->corte);
            $arqueocaja = DB::table('cvarqueocaja')->where('idagencia',$request->idagencia)->where('corte',$request->corte)->first();
            $resposanble = DB::table('users')->where('id',$arqueocaja?$arqueocaja->idresponsable:0)->first();
            return view(sistema_view().'/cvreporteconsolidadoopecaja/arqueocaja',[
                'tienda' => $tienda,
                'agencias' => $agencias,
                'agencia' => $agencia,
                'corte' => $request->corte,
                'consolidadooperaciones' => $consolidadooperaciones,
                'validacionArqueoCaja' => $validacionArqueoCaja,
                'arqueocaja' => $arqueocaja,
                'resposanble' => $resposanble,
            ]);
        }
        else if($request->input('view') == 'reporte_arqueocaja') {
          
            $agencias = DB::table('tienda')->get();
            $agencia = DB::table('tienda')->whereId($request->idagencia)->first();
            return view(sistema_view().'/cvreporteconsolidadoopecaja/reporte_arqueocaja',[
                'tienda' => $tienda,
                'agencias' => $agencias,
                'agencia' => $agencia,
            ]);
        }
        else if($request->input('view') == 'reporte_arqueocaja_pdf'){
            $agencia = DB::table('tienda')->whereId($request->idagencia_reporte_arqueocaja)->first();
            $arqueocaja = DB::table('cvarqueocaja')
                ->join('users','users.id','cvarqueocaja.idresponsable_registro')
                ->join('permiso','permiso.id','cvarqueocaja.idresponsable_registro_idpermiso')
                ->where('idagencia',$request->idagencia_reporte_arqueocaja)
                ->where('corte',$request->fecha_reporte_arqueocaja)
                ->select(
                    'cvarqueocaja.*',
                    'users.nombrecompleto as nombrecompleto_responsable',
                    'users.codigo as codigo_responsable',
                    'permiso.nombre as nombre_permiso',
                )
                ->first();
            $idarqueocaja = 0;
            if($arqueocaja){
                $idarqueocaja = $arqueocaja->id;
            }
            $arqueocaja_denominacion_1 = DB::table('cvarqueocaja_denominacion')
                ->where('idarqueocaja',$idarqueocaja)
                ->where('denominacion','0.10')
                ->first();
          
            $arqueocaja_denominacion_2 = DB::table('cvarqueocaja_denominacion')
                ->where('idarqueocaja',$idarqueocaja)
                ->where('denominacion','0.20')
                ->first();
          
            $arqueocaja_denominacion_3 = DB::table('cvarqueocaja_denominacion')
                ->where('idarqueocaja',$idarqueocaja)
                ->where('denominacion','0.50')
                ->first();
          
            $arqueocaja_denominacion_4 = DB::table('cvarqueocaja_denominacion')
                ->where('idarqueocaja',$idarqueocaja)
                ->where('denominacion','1.00')
                ->first();
          
            $arqueocaja_denominacion_5 = DB::table('cvarqueocaja_denominacion')
                ->where('idarqueocaja',$idarqueocaja)
                ->where('denominacion','2.00')
                ->first();
          
            $arqueocaja_denominacion_6 = DB::table('cvarqueocaja_denominacion')
                ->where('idarqueocaja',$idarqueocaja)
                ->where('denominacion','5.00')
                ->first();
          
            $arqueocaja_denominacion_7 = DB::table('cvarqueocaja_denominacion')
                ->where('idarqueocaja',$idarqueocaja)
                ->where('denominacion','10.00')
                ->first();
          
            $arqueocaja_denominacion_8 = DB::table('cvarqueocaja_denominacion')
                ->where('idarqueocaja',$idarqueocaja)
                ->where('denominacion','20.00')
                ->first();
          
            $arqueocaja_denominacion_9 = DB::table('cvarqueocaja_denominacion')
                ->where('idarqueocaja',$idarqueocaja)
                ->where('denominacion','50.00')
                ->first();
          
            $arqueocaja_denominacion_10 = DB::table('cvarqueocaja_denominacion')
                ->where('idarqueocaja',$idarqueocaja)
                ->where('denominacion','100.00')
                ->first();
          
            $arqueocaja_denominacion_11 = DB::table('cvarqueocaja_denominacion')
                ->where('idarqueocaja',$idarqueocaja)
                ->where('denominacion','200.00')
                ->first();
          
            $pdf = PDF::loadView(sistema_view().'/cvreporteconsolidadoopecaja/reporte_arqueocaja_pdf',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'corte' => $request->fecha_reporte_arqueocaja,
                'arqueocaja' => $arqueocaja,
                'arqueocaja_denominacion_1' => $arqueocaja_denominacion_1,
                'arqueocaja_denominacion_2' => $arqueocaja_denominacion_2,
                'arqueocaja_denominacion_3' => $arqueocaja_denominacion_3,
                'arqueocaja_denominacion_4' => $arqueocaja_denominacion_4,
                'arqueocaja_denominacion_5' => $arqueocaja_denominacion_5,
                'arqueocaja_denominacion_6' => $arqueocaja_denominacion_6,
                'arqueocaja_denominacion_7' => $arqueocaja_denominacion_7,
                'arqueocaja_denominacion_8' => $arqueocaja_denominacion_8,
                'arqueocaja_denominacion_9' => $arqueocaja_denominacion_9,
                'arqueocaja_denominacion_10' => $arqueocaja_denominacion_10,
                'arqueocaja_denominacion_11' => $arqueocaja_denominacion_11,
            ]); 
            //$pdf->setPaper('A4', 'landscape');
            return $pdf->stream('REPORTE_ARQUEO_CAJA.pdf');
        }
        else if($request->input('view') == 'valid_eliminar_arqueocaja') {
          
            $arqueocaja = DB::table('cvarqueocaja')->where('corte',$request->corte)->first();
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[2])
                ->where('users_permiso.idtienda',$request->idagencia)
                ->select('users.*','permiso.id as idpermiso','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/cvreporteconsolidadoopecaja/valid_eliminar_arqueocaja',[
                'tienda' => $tienda,
                'usuarios' => $usuarios,
                'arqueocaja' => $arqueocaja,
            ]);
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        if($request->input('view') == 'valid_registro_arqueocaja'){
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
        elseif($request->input('view') == 'valid_eliminar_arqueocaja'){
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
          
            $arqueocaja = DB::table('cvarqueocaja')->whereId($id)->first();
            DB::table('cvarqueocaja')->whereId($id)->delete();
            DB::table('cvarqueocaja_denominacion')->where('idarqueocaja',$id)->delete();
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha eliminado correctamente.',
              'idresponsable'   => $idresponsable,
              'corte'   => $arqueocaja->corte,
              'idagencia'   => $arqueocaja->idagencia,
            ]);
        }
    }


    public function destroy(Request $request, $idtienda, $id)
    {

    }
}
