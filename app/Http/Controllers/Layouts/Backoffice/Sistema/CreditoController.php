<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class CreditoController extends Controller
{
    public function __construct()
    {
        $this->tipo_credito = DB::table('tipo_credito')->get();
        $this->modalidad_credito = DB::table('modalidad_credito')->whereIn('id',[1,2,3])->get();
        $this->forma_credito = DB::table('forma_credito')->get();
        $this->tipo_operacion_credito = DB::table('tipo_operacion_credito')->get();
        $this->forma_pago_credito = DB::table('forma_pago_credito')->get();
        $this->tipo_destino_credito = DB::table('tipo_destino_credito')->get();
        $this->giro_economico_evaluacion = DB::table('giro_economico_evaluacion')->get();
        $this->tipo_giro_economico = DB::table('tipo_giro_economico')->get();
        $this->f_tiporeferencia = DB::table('f_tiporeferencia')->get();
        $this->unidadmedida_credito = DB::table('unidadmedida_credito')->get();
        $this->tipo_credito_evaluacion = DB::table('tipo_credito_evaluacion')->get();
        $this->calificacion_cliente = DB::table('calificacion_cliente')->get();
        $this->fenomenos = DB::table('fenomenos')->where('fenomenos.estado','HABILITADO')->get();
    }
  
    public function index(Request $request,$idtienda)
    {
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
          
            // ACTUALIZAR e eliminar durante el dia
            $creditos = DB::table('credito')
                ->whereIn('credito.estado',['PROCESO','APROBADO'])
                ->orderBy('credito.id','asc')
                ->get();
            $fecha = Carbon::now();
            foreach($creditos as $value){
                $ultimafecha = date_format(date_create($value->fecha_proceso),"Y-m-d").' 23:59:59';
                if($fecha>=$ultimafecha){
                    DB::table('credito')->whereId($value->id)->update([
                      'aprobacion_tipo_validacion' => '',
                      'aprobacion_nivel_validacion' => 0,
                      //'idadministrador' => Auth::user()->id,
                      'estado' => 'PENDIENTE',
                    ]);
                    DB::table('credito_aprobacion')->where('idcredito',$value->id)->delete();
                    DB::table('credito_formapago')->where('idcredito',$value->id)->delete();
                }
            }
            // FIN ACTUALIZAR 
          
            return view(sistema_view().'/credito/tabla',[
              'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->view == 'registrar') {
            return view(sistema_view().'/credito/create',[
              'tienda' => $tienda,
              'modalidad_credito' => $this->modalidad_credito,
              'tipo_operacion_credito' => $this->tipo_operacion_credito,
              'forma_credito' => $this->forma_credito,
              'tipo_destino_credito' => $this->tipo_destino_credito,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
      
        if($request->input('view') == 'registrar') {
            
            $rules = [
                'idcliente' => 'required',                 
                //'idaval' => 'required',                 
                'idcredito_prendatario' => 'required',                 
                'idtipo_operacion_credito' => 'required',                 
                'idforma_credito' => 'required',               
                'idtipo_destino_credito' => 'required',                   
                'idmodalidad_credito' => 'required',                     
            ];
          
            $messages = [
                'idcliente.required' => 'El Campo es Obligatorio.',
                'idaval.required' => 'El Campo es Obligatorio.',
                'idcredito_prendatario.required' => 'El Campo es Obligatorio.',
                'idtipo_operacion_credito.required' => 'El Campo es Obligatorio.',
                'idforma_credito.required' => 'El Campo es Obligatorio.',
                'idtipo_destino_credito.required' => 'El Campo es Obligatorio.',
                'idmodalidad_credito.required' => 'El Campo es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
              
            // ---- SELECCIONAR LOS DATOS FILTRADOS
            $cliente = DB::table('users')->whereId($request->input('idcliente'))->first();
            $clienteidentificacion = '';
            $clientenombrecompleto = '';
            if($cliente!=''){
                $clienteidentificacion = $cliente->identificacion;
                $clientenombrecompleto = $cliente->nombrecompleto;
            }
            $aval = DB::table('users')->whereId($request->input('idaval'))->first();
            $avalidentificacion = '';
            $avalnombrecompleto = '';
            if($aval!=''){
                $avalidentificacion = $aval->identificacion;
                $avalnombrecompleto = $aval->nombrecompleto;
            }
            $asesor = DB::table('users')->whereId(Auth::user()->id)->first();
            $asesoridentificacion = '';
            $asesornombrecompleto = '';
            if($asesor!=''){
                $asesoridentificacion = $asesor->identificacion;
                $asesornombrecompleto = $asesor->nombrecompleto;
            }
          
            $credito_prendatario = DB::table('credito_prendatario')->whereId($request->input('idcredito_prendatario'))->first();
            $tipo_operacion_credito = DB::table('tipo_operacion_credito')->whereId($request->input('idtipo_operacion_credito'))->first();
            $forma_credito = DB::table('forma_credito')->whereId($request->input('idforma_credito'))->first();
            $tipo_destino_credito = DB::table('tipo_destino_credito')->whereId($request->input('idtipo_destino_credito'))->first();
            $modalidad_credito = DB::table('modalidad_credito')->whereId($request->input('idmodalidad_credito'))->first();
            
            // ---- FIN SELECCIONAR LOS DATOS FILTRADOS
          
            $idcredito = DB::table('credito')->insertGetId([
              
              'clienteidentificacion'     => $clienteidentificacion,
              'clientenombrecompleto'     => $clientenombrecompleto,
              'avalidentificacion'        => $avalidentificacion,
              'avalnombrecompleto'        => $avalnombrecompleto,
              'credito_prendatario'       => $credito_prendatario->nombre,
              'tipo_operacion_credito'    => $tipo_operacion_credito->nombre,
              'forma_credito'             => $forma_credito->nombre,
              'tipo_destino_credito'      => $tipo_destino_credito->nombre,
              'modalidad_credito'         => $modalidad_credito->nombre,
              'asesoridentificacion'      => $asesoridentificacion,
              'asesornombrecompleto'      => $asesornombrecompleto,
              'participarconyugue_titular'=> $request->participarconyugue_titular,
              'participarconyugue_aval'   => $request->participarconyugue_aval,
              
              'idcliente'                 => $request->input('idcliente'),
              'idaval'                    => $request->input('idaval')!=''?$request->input('idaval'):0,
              'idcredito_prendatario'     => $request->input('idcredito_prendatario'),
              'idtipo_operacion_credito'  => $request->input('idtipo_operacion_credito'),
              'idforma_credito'           => $request->input('idforma_credito'),
              'idtipo_destino_credito'    => $request->input('idtipo_destino_credito'),
              'idforma_pago_credito'      => 1,
              'idmodalidad_credito'       => $request->input('idmodalidad_credito'),
              'fecha'                     => Carbon::now(),
              'idasesor'                  => Auth::user()->id,
              'estado'                    => 'PENDIENTE',
              'idevaluacion'              => 1,
              'idestadocredito'           => 1,
              'idtienda'                  => user_permiso()->idtienda,
            ]);
          
            //jalar ultimo registro
             
            $credito = DB::table('credito')
                //->where('credito.idevaluacion',1)
                ->where('credito.idcliente',$request->input('idcliente'))
                ->where('credito.estado','DESEMBOLSADO')
                ->whereIn('credito.idestadocredito',[1,2])
                ->orderBy('credito.idestadocredito','asc')
                ->orderBy('credito.fecha_desembolso','desc')
                ->limit(1)
                ->first();
            
            if($credito!=''){
              
                // Evaluación Cualiativa
              
                $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')->where('idcredito',$credito->id)->first();
                
                if($credito_evaluacion_cualitativa!=''){
                DB::table('credito_evaluacion_cualitativa')->insert([
                    'idcredito' => $idcredito,
                    'fecha' => Carbon::now(),
                    'descripcion_actividad' => $credito_evaluacion_cualitativa->descripcion_actividad,
                    'idtipo_giro_economico' => $credito_evaluacion_cualitativa->idtipo_giro_economico,
                    'idgiro_economico_evaluacion' => $credito_evaluacion_cualitativa->idgiro_economico_evaluacion,
                    'ejercicio_giro_economico' => $credito_evaluacion_cualitativa->ejercicio_giro_economico,

                    'referencia' => $credito_evaluacion_cualitativa->referencia,

                    'cantidad_cliente_natural' => $credito_evaluacion_cualitativa->cantidad_cliente_natural,
                    'cantidad_cliente_juridico' => $credito_evaluacion_cualitativa->cantidad_cliente_juridico,
                    'cantidad_pareja_natural' => $credito_evaluacion_cualitativa->cantidad_pareja_natural,
                    'cantidad_pareja_juridico' => $credito_evaluacion_cualitativa->cantidad_pareja_juridico,
                    'total_deuda' => $credito_evaluacion_cualitativa->total_deuda,
                    'experiencia_microempresa' => $credito_evaluacion_cualitativa->experiencia_microempresa,
                    'tiempo_mismo_local' => $credito_evaluacion_cualitativa->tiempo_mismo_local,
                    'instalacion_local' => $credito_evaluacion_cualitativa->instalacion_local,
                    'nro_trabajador_completo' => $credito_evaluacion_cualitativa->nro_trabajador_completo,
                    'nro_trabajador_parcal' => $credito_evaluacion_cualitativa->nro_trabajador_parcal,

                    'saladario_fijo' => $credito_evaluacion_cualitativa->saladario_fijo,
                    'otros_negocios' => $credito_evaluacion_cualitativa->otros_negocios,
                    'alquiler_local' => $credito_evaluacion_cualitativa->alquiler_local,
                    'no_tiene' => $credito_evaluacion_cualitativa->no_tiene,
                    'pensionista' => $credito_evaluacion_cualitativa->pensionista,
                    'registro_ventas_cuentas' => $credito_evaluacion_cualitativa->registro_ventas_cuentas,
                    'pago_impuestos_dia' => $credito_evaluacion_cualitativa->pago_impuestos_dia,
                    'pago_servicios_dia' => $credito_evaluacion_cualitativa->pago_servicios_dia,
                    'politica_orden' => $credito_evaluacion_cualitativa->politica_orden,
                    'normas_municipales' => $credito_evaluacion_cualitativa->normas_municipales,

                    'gasto_alimentacion' => $credito_evaluacion_cualitativa->gasto_alimentacion,
                    'gasto_educacion' => $credito_evaluacion_cualitativa->gasto_educacion,
                    'gasto_vestimenta' => $credito_evaluacion_cualitativa->gasto_vestimenta,
                    'gasto_transporte' => $credito_evaluacion_cualitativa->gasto_transporte,
                    'gasto_salud' => $credito_evaluacion_cualitativa->gasto_salud,
                    'gasto_vivienda' => $credito_evaluacion_cualitativa->gasto_vivienda,
                    'gasto_agua' => $credito_evaluacion_cualitativa->gasto_agua,
                    'gasto_luz' => $credito_evaluacion_cualitativa->gasto_luz,
                    'gasto_telefono_internet' => $credito_evaluacion_cualitativa->gasto_telefono_internet,
                    'gasto_celular' => $credito_evaluacion_cualitativa->gasto_celular,
                    'gasto_cable' => $credito_evaluacion_cualitativa->gasto_cable,
                    'total_servicios' => $credito_evaluacion_cualitativa->total_servicios,
                    'gasto_otros' => $credito_evaluacion_cualitativa->gasto_otros,
                    'gasto_total' => $credito_evaluacion_cualitativa->gasto_total,

                    'total_hijos' => $credito_evaluacion_cualitativa->total_hijos,
                    'total_hijos_dependientes' => $credito_evaluacion_cualitativa->total_hijos_dependientes,
                    'detalle_destino_prestamo' => $credito_evaluacion_cualitativa->detalle_destino_prestamo,
                    'fortalezas_negocio' => $credito_evaluacion_cualitativa->fortalezas_negocio,
                ]);
                }
              
                // Evaluación Cuantitiva
              
                $credito_evaluacion_cuantitativa = DB::table('credito_evaluacion_cuantitativa')->where('idcredito',$credito->id)->first();
                
                if($credito_evaluacion_cuantitativa!=''){
                DB::table('credito_evaluacion_cuantitativa')->insert([
                    'idcredito' => $idcredito,
                    'fecha' => Carbon::now(),
                    'evaluacion_meses' => $credito_evaluacion_cuantitativa->evaluacion_meses,
                    'margen_venta_calculado' => $credito_evaluacion_cuantitativa->margen_venta_calculado,
                    'balance_general' => $credito_evaluacion_cuantitativa->balance_general,
                    'ganancia_perdida' => $credito_evaluacion_cuantitativa->ganancia_perdida,

                    'dias_ventas_mensual' => $credito_evaluacion_cuantitativa->dias_ventas_mensual,
                    'dias_compras_mensual' => $credito_evaluacion_cuantitativa->dias_compras_mensual,

                    'credito_cobrando_venta_mensual' => $credito_evaluacion_cuantitativa->credito_cobrando_venta_mensual,
                    'credito_porcentaje_venta_mensual' => $credito_evaluacion_cuantitativa->credito_porcentaje_venta_mensual,
                    'contado_cobrando_venta_mensual' => $credito_evaluacion_cuantitativa->contado_cobrando_venta_mensual,
                    'contado_porcentaje_venta_mensual' => $credito_evaluacion_cuantitativa->contado_porcentaje_venta_mensual,
                    'credito_cobrando_compra_mensual' => $credito_evaluacion_cuantitativa->credito_cobrando_compra_mensual,
                    'credito_porcentaje_compra_mensual' => $credito_evaluacion_cuantitativa->credito_porcentaje_compra_mensual,
                    'contado_cobrando_compra_mensual' => $credito_evaluacion_cuantitativa->contado_cobrando_compra_mensual,
                    'contado_porcentaje_compra_mensual' => $credito_evaluacion_cuantitativa->contado_porcentaje_compra_mensual,

                    'ratio_re_negocio' => $credito_evaluacion_cuantitativa->ratio_re_negocio,
                    'ratio_re_unidadfamiliar' => $credito_evaluacion_cuantitativa->ratio_re_unidadfamiliar,
                    'ratio_re_patrimonial' => $credito_evaluacion_cuantitativa->ratio_re_patrimonial,
                    'ratio_re_activos' => $credito_evaluacion_cuantitativa->ratio_re_activos,
                    'ratio_re_ventas' => $credito_evaluacion_cuantitativa->ratio_re_ventas,

                    'ratio_re_prestamo' => $credito_evaluacion_cuantitativa->ratio_re_prestamo,
                    'ratio_re_capital' => $credito_evaluacion_cuantitativa->ratio_re_capital,
                    'ratio_re_liquidez' => $credito_evaluacion_cuantitativa->ratio_re_liquidez,
                    'ratio_re_liquidez_acida' => $credito_evaluacion_cuantitativa->ratio_re_liquidez_acida,
                    'ratio_re_endeudamiento_actual' => $credito_evaluacion_cuantitativa->ratio_re_endeudamiento_actual,
                    'ratio_re_endeudamiento_propuesta' => $credito_evaluacion_cuantitativa->ratio_re_endeudamiento_propuesta,
                    'ratio_re_rotacion_inventario' => $credito_evaluacion_cuantitativa->ratio_re_rotacion_inventario,
                    'ratio_re_promedio_cobranza' => $credito_evaluacion_cuantitativa->ratio_re_promedio_cobranza,
                    'ratio_re_primedio_pago' => $credito_evaluacion_cuantitativa->ratio_re_primedio_pago,
                    //'ratio_re_cuota_total' => $request->input('ratio_re_cuota_total'),
                    'excedente_antes_propuesta' => $credito_evaluacion_cuantitativa->excedente_antes_propuesta,
                    'excedente_propuesta_sin_deduccion' => $credito_evaluacion_cuantitativa->excedente_propuesta_sin_deduccion,
                    'excedente_propuesta_con_deduccion' => $credito_evaluacion_cuantitativa->excedente_propuesta_con_deduccion,
                    'estado_credito' => $credito_evaluacion_cuantitativa->estado_credito,

                    'comentario' => $credito_evaluacion_cuantitativa->comentario,
                ]);
                }
              
                // Deuda
              
                $credito_cuantitativa_deudas = DB::table('credito_cuantitativa_deudas')->where('idcredito',$credito->id)->first();
              
                if($credito_cuantitativa_deudas!=''){
                DB::table('credito_cuantitativa_deudas')->insert([
                    'idcredito' => $idcredito,
                    'fecha' => Carbon::now(),
                    'entidad_regulada' => $credito_cuantitativa_deudas->entidad_regulada,
                    'total_saldo_capital' => $credito_cuantitativa_deudas->total_saldo_capital,
                    'total_cuota' => $credito_cuantitativa_deudas->total_cuota,
                    'total_corto_plazo' => $credito_cuantitativa_deudas->total_corto_plazo,
                    'total_largo_plazo' => $credito_cuantitativa_deudas->total_largo_plazo,
                    'total_saldo_capital_deducciones' => $credito_cuantitativa_deudas->total_saldo_capital_deducciones,
                    'total_cuota_deducciones' => $credito_cuantitativa_deudas->total_cuota_deducciones,

                    'entidad_noregulada' => $credito_cuantitativa_deudas->entidad_noregulada,
                    'total_noregulada_saldo_capital' => $credito_cuantitativa_deudas->total_noregulada_saldo_capital,
                    'total_noregulada_cuota' => $credito_cuantitativa_deudas->total_noregulada_cuota,
                    'total_noregulada_corto_plazo' => $credito_cuantitativa_deudas->total_noregulada_corto_plazo,
                    'total_noregulada_largo_plazo' => $credito_cuantitativa_deudas->total_noregulada_largo_plazo,
                    'total_noregulada_saldo_capital_deducciones' => $credito_cuantitativa_deudas->total_noregulada_saldo_capital_deducciones,
                    'total_noregulada_cuota_deducciones' => $credito_cuantitativa_deudas->total_noregulada_cuota_deducciones,

                    'linea_credito' => $credito_cuantitativa_deudas->linea_credito,
                    'total_lc_linea_credito' => $credito_cuantitativa_deudas->total_lc_linea_credito,
                    'total_lc_cuotas' => $credito_cuantitativa_deudas->total_lc_cuotas,
                    'resumen' => $credito_cuantitativa_deudas->resumen,

                    'total_resumen_linea_credito' => $credito_cuantitativa_deudas->total_resumen_linea_credito,
                    'total_resumen_cuotas_linea_credito' => $credito_cuantitativa_deudas->total_resumen_cuotas_linea_credito,
                    'total_resumen_cuotas_linea_credito2' => $credito_cuantitativa_deudas->total_resumen_cuotas_linea_credito2,

                    'idforma_pago_credito' => $credito_cuantitativa_deudas->idforma_pago_credito,
                    'propuesta_cuotas' => $credito_cuantitativa_deudas->propuesta_cuotas,
                    'propuesta_monto' => $credito_cuantitativa_deudas->propuesta_monto,
                    'propuesta_tem' => $credito_cuantitativa_deudas->propuesta_tem,

                    'propuesta_servicio_otros' => $credito_cuantitativa_deudas->propuesta_servicio_otros,
                    'propuesta_cargos' => $credito_cuantitativa_deudas->propuesta_cargos,
                    'propuesta_total_pagar' => $credito_cuantitativa_deudas->propuesta_total_pagar,
                    'total_propuesta' => $credito_cuantitativa_deudas->total_propuesta,

                    'riesgo_proyectado_empresa' => $credito_cuantitativa_deudas->riesgo_proyectado_empresa,
                    'riesgo_proyectado_todos' => $credito_cuantitativa_deudas->riesgo_proyectado_todos,
                    /*'excedente_antes_propuesta' => $request->input('excedente_antes_propuesta'),
                    'excedente_propuesta_sin_deduccion' => $request->input('excedente_propuesta_sin_deduccion'),
                    'excedente_propuesta_con_deduccion' => $request->input('excedente_propuesta_con_deduccion'),*/
                    'estado_credito' => $credito_cuantitativa_deudas->estado_credito,
                ]);
                }
              
                // Control Limites
              
                $credito_cuantitativa_control_limites = DB::table('credito_cuantitativa_control_limites')->where('idcredito',$credito->id)->first();
          
                if($credito_cuantitativa_control_limites!=''){
                DB::table('credito_cuantitativa_control_limites')->insert([
                    'fecha' => Carbon::now(),
                    'idcredito' => $idcredito,
                    'vinculacion_deudor' => $credito_cuantitativa_control_limites->vinculacion_deudor,
                    'total_garantia_cliente' => $credito_cuantitativa_control_limites->total_garantia_cliente,
                    'cantidad_garante_natural' => $credito_cuantitativa_control_limites->cantidad_garante_natural,
                    'cantidad_garante_juridico' => $credito_cuantitativa_control_limites->cantidad_garante_juridico,
                    'cantidad_pareja_natural' => $credito_cuantitativa_control_limites->cantidad_pareja_natural,
                    'cantidad_pareja_juridico' => $credito_cuantitativa_control_limites->cantidad_pareja_juridico,
                    'total_deuda' => $credito_cuantitativa_control_limites->total_deuda,
                    'total_garantia_aval' => $credito_cuantitativa_control_limites->total_garantia_aval,
                    'total_vinculo_deudor' => $credito_cuantitativa_control_limites->total_vinculo_deudor,
                    'comentarios' => $credito_cuantitativa_control_limites->comentarios,

                    'saldo_noprendario_cliente' => $credito_cuantitativa_control_limites->saldo_noprendario_cliente,
                    'propuesta_noprendario_cliente' => $credito_cuantitativa_control_limites->propuesta_noprendario_cliente,
                    'saldo_noprendario_aval' => $credito_cuantitativa_control_limites->saldo_noprendario_aval,
                    'propuesta_noprendario_aval' => $credito_cuantitativa_control_limites->propuesta_noprendario_aval,

                    'reporte_institucional' => $credito_cuantitativa_control_limites->reporte_institucional,
                    'capital_asignado' => $credito_cuantitativa_control_limites->capital_asignado,

                    'total_financiado_deudor' => $credito_cuantitativa_control_limites->total_financiado_deudor,
                    'porcentaje_resultado' => $credito_cuantitativa_control_limites->porcentaje_resultado,
                    'estado_resultado' => $credito_cuantitativa_control_limites->estado_resultado,
                ]);
                }
              
                // ingreso adicional
              
                $credito_cuantitativa_ingreso_adicional = DB::table('credito_cuantitativa_ingreso_adicional')->where('idcredito',$credito->id)->first();
   
                if($credito_cuantitativa_ingreso_adicional!=''){
                DB::table('credito_cuantitativa_ingreso_adicional')->insert([
                    'idcredito' => $idcredito,
                    'fecha' => Carbon::now(),
                    'idtipo_giro_economico_adiccional' => $credito_cuantitativa_ingreso_adicional->idtipo_giro_economico_adiccional,
                    'idgiro_economico_evaluacion_adicional' => $credito_cuantitativa_ingreso_adicional->idgiro_economico_evaluacion_adicional,
                    'evaluacion_meses' => $credito_cuantitativa_ingreso_adicional->evaluacion_meses,
                    'margen_venta_calculado' => $credito_cuantitativa_ingreso_adicional->margen_venta_calculado,
                    'productos' => $credito_cuantitativa_ingreso_adicional->productos,
                    'total_venta' => $credito_cuantitativa_ingreso_adicional->total_venta,
                    'total_compra' => $credito_cuantitativa_ingreso_adicional->total_compra,
                    'porcentaje_margen' => $credito_cuantitativa_ingreso_adicional->porcentaje_margen,
                    'frecuencia_ventas' => $credito_cuantitativa_ingreso_adicional->frecuencia_ventas,
                    'dias' => $credito_cuantitativa_ingreso_adicional->dias,
                    'venta_total_dias' => $credito_cuantitativa_ingreso_adicional->venta_total_dias,
                    'numero_dias' => $credito_cuantitativa_ingreso_adicional->numero_dias,

                    'venta_mensual' => $credito_cuantitativa_ingreso_adicional->venta_mensual,
                    'recabo_dato_numero' => $credito_cuantitativa_ingreso_adicional->recabo_dato_numero,
                    'recabo_dato_dia' => $credito_cuantitativa_ingreso_adicional->recabo_dato_dia,
                    'recabo_dato_monto' => $credito_cuantitativa_ingreso_adicional->recabo_dato_monto,
                    'estado_muestra' => $credito_cuantitativa_ingreso_adicional->estado_muestra,
                    'margen_ventas' => $credito_cuantitativa_ingreso_adicional->margen_ventas,
                    'subproducto' => $credito_cuantitativa_ingreso_adicional->subproducto,
                    'productos_mensual' => $credito_cuantitativa_ingreso_adicional->productos_mensual,
                    'total_venta_mensual' => $credito_cuantitativa_ingreso_adicional->total_venta_mensual,
                    'total_compra_mensual' => $credito_cuantitativa_ingreso_adicional->total_compra_mensual,
                    'porcentaje_margen_mensual' => $credito_cuantitativa_ingreso_adicional->porcentaje_margen_mensual,
                    'semanas' => $credito_cuantitativa_ingreso_adicional->semanas,
                    'venta_total_mensual' => $credito_cuantitativa_ingreso_adicional->venta_total_mensual,
                    'estado_muestra_mensual' => $credito_cuantitativa_ingreso_adicional->estado_muestra_mensual,
                    'margen_ventas_mensual' => $credito_cuantitativa_ingreso_adicional->margen_ventas_mensual,
                    'subproductomensual' => $credito_cuantitativa_ingreso_adicional->subproductomensual,

                    'inventario' => $credito_cuantitativa_ingreso_adicional->inventario,
                    'total_inventario' => $credito_cuantitativa_ingreso_adicional->total_inventario,
                    'inmuebles' => $credito_cuantitativa_ingreso_adicional->inmuebles,
                    'total_inmuebles' => $credito_cuantitativa_ingreso_adicional->total_inmuebles,
                    'muebles' => $credito_cuantitativa_ingreso_adicional->muebles,
                    'total_muebles' => $credito_cuantitativa_ingreso_adicional->total_muebles,

                    'balance_general' => $credito_cuantitativa_ingreso_adicional->balance_general,
                    'ganancias_perdidas' => $credito_cuantitativa_ingreso_adicional->ganancias_perdidas,

                    'dias_ventas_mensual' => $credito_cuantitativa_ingreso_adicional->dias_ventas_mensual,
                    'dias_compras_mensual' => $credito_cuantitativa_ingreso_adicional->dias_compras_mensual,

                    'credito_cobrando_venta_mensual' => $credito_cuantitativa_ingreso_adicional->credito_cobrando_venta_mensual,
                    'credito_porcentaje_venta_mensual' => $credito_cuantitativa_ingreso_adicional->credito_porcentaje_venta_mensual,
                    'contado_cobrando_venta_mensual' => $credito_cuantitativa_ingreso_adicional->contado_cobrando_venta_mensual,
                    'contado_porcentaje_venta_mensual' => $credito_cuantitativa_ingreso_adicional->contado_porcentaje_venta_mensual,
                    'credito_cobrando_compra_mensual' => $credito_cuantitativa_ingreso_adicional->credito_cobrando_compra_mensual,
                    'credito_porcentaje_compra_mensual' => $credito_cuantitativa_ingreso_adicional->credito_porcentaje_compra_mensual,
                    'contado_cobrando_compra_mensual' => $credito_cuantitativa_ingreso_adicional->contado_cobrando_compra_mensual,
                    'contado_porcentaje_compra_mensual' => $credito_cuantitativa_ingreso_adicional->contado_porcentaje_compra_mensual,
                    'adicional_fijo' => $credito_cuantitativa_ingreso_adicional->adicional_fijo,
                    'total_ingreso_adicional' => $credito_cuantitativa_ingreso_adicional->total_ingreso_adicional,
                    'comentario' => $credito_cuantitativa_ingreso_adicional->comentario,
                ]);
                }
              
                // Margen de venta
              
                $credito_cuantitativa_margen_venta = DB::table('credito_cuantitativa_margen_venta')->where('idcredito',$credito->id)->first();
        
                if($credito_cuantitativa_margen_venta!=''){
                DB::table('credito_cuantitativa_margen_venta')->insert([
                    'fecha' => Carbon::now(),
                    'idcredito' => $idcredito,
                    'tipo_registro' => $credito_cuantitativa_margen_venta->tipo_registro,
                    'productos' => $credito_cuantitativa_margen_venta->productos,
                    'total_venta' => $credito_cuantitativa_margen_venta->total_venta,
                    'total_compra' => $credito_cuantitativa_margen_venta->total_compra,
                    'porcentaje_margen' => $credito_cuantitativa_margen_venta->porcentaje_margen,
                    'frecuencia_ventas' => $credito_cuantitativa_margen_venta->frecuencia_ventas,
                    'dias' => $credito_cuantitativa_margen_venta->dias,
                    'venta_total_dias' => $credito_cuantitativa_margen_venta->venta_total_dias,
                    'numero_dias' => $credito_cuantitativa_margen_venta->numero_dias,

                    'venta_mensual' => $credito_cuantitativa_margen_venta->venta_mensual,
                    'recabo_dato_numero' => $credito_cuantitativa_margen_venta->recabo_dato_numero,
                    'recabo_dato_dia' => $credito_cuantitativa_margen_venta->recabo_dato_dia,
                    'recabo_dato_monto' => $credito_cuantitativa_margen_venta->recabo_dato_monto,
                    'estado_muestra' => $credito_cuantitativa_margen_venta->estado_muestra,
                    'margen_ventas' => $credito_cuantitativa_margen_venta->margen_ventas,
                    'subproducto' => $credito_cuantitativa_margen_venta->subproducto,
                    'productos_mensual' => $credito_cuantitativa_margen_venta->productos_mensual,
                    'total_venta_mensual' => $credito_cuantitativa_margen_venta->total_venta_mensual,
                    'total_compra_mensual' => $credito_cuantitativa_margen_venta->total_compra_mensual,
                    'porcentaje_margen_mensual' => $credito_cuantitativa_margen_venta->porcentaje_margen_mensual,
                    'semanas' => $credito_cuantitativa_margen_venta->semanas,
                    'venta_total_mensual' => $credito_cuantitativa_margen_venta->venta_total_mensual,
                    'estado_muestra_mensual' => $credito_cuantitativa_margen_venta->estado_muestra_mensual,
                    'margen_ventas_mensual' => $credito_cuantitativa_margen_venta->margen_ventas_mensual,
                    'subproductomensual' => $credito_cuantitativa_margen_venta->subproductomensual,
                    'margen_venta_calculado' => $credito_cuantitativa_margen_venta->margen_venta_calculado,
                ]);
                }
              
                // Resumida
                
                $credito_evaluacion_resumida = DB::table('credito_evaluacion_resumida')->where('idcredito',$credito->id)->first();
          
                if($credito_evaluacion_resumida!=''){
                DB::table('credito_evaluacion_resumida')->insert([
                    'idcredito' => $idcredito,
                    'fecha' => Carbon::now(),
                    'descripcion_actividad' => $credito_evaluacion_resumida->descripcion_actividad,
                    'idtipo_giro_economico' => $credito_evaluacion_resumida->idtipo_giro_economico,
                    'idgiro_economico_evaluacion' => $credito_evaluacion_resumida->idgiro_economico_evaluacion,
                    'ejercicio_giro_economico' => $credito_evaluacion_resumida->ejercicio_giro_economico,

                    'cantidad_cliente_natural' => $credito_evaluacion_resumida->cantidad_cliente_natural,
                    'cantidad_cliente_juridico' => $credito_evaluacion_resumida->cantidad_cliente_juridico,
                    'cantidad_pareja_natural' => $credito_evaluacion_resumida->cantidad_pareja_natural,
                    'cantidad_pareja_juridico' => $credito_evaluacion_resumida->cantidad_pareja_juridico,
                    'total_deuda' => $credito_evaluacion_resumida->total_deuda,

                    'cantidad_garante_natural' => $credito_evaluacion_resumida->cantidad_garante_natural,
                    'cantidad_garante_juridico' => $credito_evaluacion_resumida->cantidad_garante_juridico,
                    'cantidad_garante_pareja_natural' => $credito_evaluacion_resumida->cantidad_garante_pareja_natural,
                    'cantidad_garante_pareja_juridico' => $credito_evaluacion_resumida->cantidad_garante_pareja_juridico,
                    'total_deuda_garante' => $credito_evaluacion_resumida->total_deuda_garante,

                    'experiencia_microempresa' => $credito_evaluacion_resumida->experiencia_microempresa,
                    'tiempo_mismo_local' => $credito_evaluacion_resumida->tiempo_mismo_local,
                    'instalacion_local' => $credito_evaluacion_resumida->instalacion_local,
                    'nro_trabajador_completo' => $credito_evaluacion_resumida->nro_trabajador_completo,
                    'nro_trabajador_parcal' => $credito_evaluacion_resumida->nro_trabajador_parcal,

                    'referencia' => $credito_evaluacion_resumida->referencia,

                    'venta_diaria' => $credito_evaluacion_resumida->venta_diaria,
                    'venta_total_dias' => $credito_evaluacion_resumida->venta_total_dias,
                    'venta_semanal' => $credito_evaluacion_resumida->venta_semanal,
                    'venta_total_mensual' => $credito_evaluacion_resumida->venta_total_mensual,

                    'ingresos_gastos' => $credito_evaluacion_resumida->ingresos_gastos,
                    'ingresos_op_total' => $credito_evaluacion_resumida->ingresos_op_total,

                    'gasto_alimentacion' => $credito_evaluacion_resumida->gasto_alimentacion,
                    'gasto_educacion' => $credito_evaluacion_resumida->gasto_educacion,
                    'gasto_vestimenta' => $credito_evaluacion_resumida->gasto_vestimenta,
                    'gasto_transporte' => $credito_evaluacion_resumida->gasto_transporte,
                    'gasto_salud' => $credito_evaluacion_resumida->gasto_salud,
                    'gasto_vivienda' => $credito_evaluacion_resumida->gasto_vivienda,
                    'total_servicios' => $credito_evaluacion_resumida->total_servicios,
                    'gasto_agua' => $credito_evaluacion_resumida->gasto_agua,
                    'gasto_luz' => $credito_evaluacion_resumida->gasto_luz,
                    'gasto_telefono_internet' => $credito_evaluacion_resumida->gasto_telefono_internet,
                    'gasto_celular' => $credito_evaluacion_resumida->gasto_celular,
                    'gasto_cable' => $credito_evaluacion_resumida->gasto_cable,
                    'gasto_otros' => $credito_evaluacion_resumida->gasto_otros,
                    'gasto_total' => $credito_evaluacion_resumida->gasto_total,

                    'idforma_pago_credito' => $credito_evaluacion_resumida->idforma_pago_credito,
                    'propuesta_cuotas' => $credito_evaluacion_resumida->propuesta_cuotas,
                    'propuesta_monto' => $credito_evaluacion_resumida->propuesta_monto,
                    'propuesta_tem' => $credito_evaluacion_resumida->propuesta_tem,

                    'propuesta_servicio_otros' => $credito_evaluacion_resumida->propuesta_servicio_otros,
                    'propuesta_cargos' => $credito_evaluacion_resumida->propuesta_cargos,
                    'propuesta_total_pagar' => $credito_evaluacion_resumida->propuesta_total_pagar,
                    'total_propuesta' => $credito_evaluacion_resumida->total_propuesta,

                    'detalle_destino_prestamo' => $credito_evaluacion_resumida->detalle_destino_prestamo,
                    'fortalezas_negocio' => $credito_evaluacion_resumida->fortalezas_negocio,

                    'relacion_cuota_venta_diaria' => $credito_evaluacion_resumida->relacion_cuota_venta_diaria,
                    'relacion_cuota_venta_semanal' => $credito_evaluacion_resumida->relacion_cuota_venta_semanal,
                    'relacion_cuota_venta_quincenal' => $credito_evaluacion_resumida->relacion_cuota_venta_quincenal,
                    'relacion_cuota_venta_mensual' => $credito_evaluacion_resumida->relacion_cuota_venta_mensual,

                    'estado_indicador_solvencia' => $credito_evaluacion_resumida->estado_indicador_solvencia,
                    'estado_indicador_cuota_ingreso' => $credito_evaluacion_resumida->estado_indicador_cuota_ingreso,
                    'estado_indicador_cuota_venta_diario' => $credito_evaluacion_resumida->estado_indicador_cuota_venta_diario,
                    'estado_indicador_cuota_venta_semanal' => $credito_evaluacion_resumida->estado_indicador_cuota_venta_semanal,
                    'estado_indicador_cuota_venta_quincenal' => $credito_evaluacion_resumida->estado_indicador_cuota_venta_quincenal,
                    'estado_indicador_cuota_venta_mensual' => $credito_evaluacion_resumida->estado_indicador_cuota_venta_mensual,
                    'estado_credito_general' => $credito_evaluacion_resumida->estado_credito_general,

                    'indicador_solvencia_excedente' => $credito_evaluacion_resumida->indicador_solvencia_excedente,
                    'indicador_solvencia_cuotas' => $credito_evaluacion_resumida->indicador_solvencia_cuotas,
                    'relacion_cuota_mensual' => $credito_evaluacion_resumida->relacion_cuota_mensual,
                ]);
                }
              
                // Flujo de caja
              
                $credito_flujo_caja = DB::table('credito_flujo_caja')->where('idcredito',$credito->id)->first();
          
                if($credito_flujo_caja!=''){
                DB::table('credito_flujo_caja')->insert([
                    'idcredito' => $idcredito,
                    'fecha' => Carbon::now(),
                    'encabezado' => $credito_flujo_caja->encabezado,
                    'evaluacion_meses' => $credito_flujo_caja->evaluacion_meses,
                    'flujo_caja' => $credito_flujo_caja->flujo_caja,
                    'entidad_reguladas' => $credito_flujo_caja->entidad_reguladas,
                    'linea_credito' => $credito_flujo_caja->linea_credito,
                    'entidad_noregulada' => $credito_flujo_caja->entidad_noregulada,
                    'comentarios' => $credito_flujo_caja->comentarios,
                ]);
                }
              
                // Evaluación
              
                $credito_formato_evaluacion = DB::table('credito_formato_evaluacion')->where('idcredito',$credito->id)->first();
          
                if($credito_formato_evaluacion!=''){
                DB::table('credito_formato_evaluacion')->insert([
                    'idcredito' => $idcredito,
                    'fecha' => Carbon::now(),
                    'remuneracion_total_cliente' => $credito_formato_evaluacion->remuneracion_total_cliente,
                    'remuneracion_variable' => $credito_formato_evaluacion->remuneracion_variable,
                    'remuneracion_pareja' => $credito_formato_evaluacion->remuneracion_pareja,
                    'adicional_ingreso_mensual' => $credito_formato_evaluacion->adicional_ingreso_mensual,
                    'total_ingresos_mensuales' => $credito_formato_evaluacion->total_ingresos_mensuales,
                    'numero_total_hijos' => $credito_formato_evaluacion->numero_total_hijos,
                    'total_hijos_dependientes' => $credito_formato_evaluacion->total_hijos_dependientes,

                    'pago_cuotas_deuda' => $credito_formato_evaluacion->pago_cuotas_deuda,
                    'monto_alimentacion' => $credito_formato_evaluacion->monto_alimentacion,
                    'monto_salud' => $credito_formato_evaluacion->monto_salud,
                    'monto_educacion' => $credito_formato_evaluacion->monto_educacion,
                    'monto_alquiler_vivienda' => $credito_formato_evaluacion->monto_alquiler_vivienda,
                    'monto_mobilidad' => $credito_formato_evaluacion->monto_mobilidad,
                    'monto_luz' => $credito_formato_evaluacion->monto_luz,
                    'monto_agua' => $credito_formato_evaluacion->monto_agua,
                    'monto_telefono' => $credito_formato_evaluacion->monto_telefono,
                    'monto_cable' => $credito_formato_evaluacion->monto_cable,
                    'otros_gastos_personales' => $credito_formato_evaluacion->otros_gastos_personales,
                    'monto_pension_alimentos' => $credito_formato_evaluacion->monto_pension_alimentos,
                    'adicional_egresos_mensual' => $credito_formato_evaluacion->adicional_egresos_mensual,
                    'total_egresos_mensuales' => $credito_formato_evaluacion->total_egresos_mensuales,
                    'excedente_mensual_disponible' => $credito_formato_evaluacion->excedente_mensual_disponible,

                    'deudas_financieras' => $credito_formato_evaluacion->deudas_financieras,
                    'saldo_capita_cliente' => $credito_formato_evaluacion->saldo_capita_cliente,
                    'couta_mensual_cliente' => $credito_formato_evaluacion->couta_mensual_cliente,
                    'cuota_ampliacion_cliente' => $credito_formato_evaluacion->cuota_ampliacion_cliente,
                    'saldo_capita_pareja' => $credito_formato_evaluacion->saldo_capita_pareja,
                    'couta_mensual_pareja' => $credito_formato_evaluacion->couta_mensual_pareja,
                    'cuota_ampliacion_pareja' => $credito_formato_evaluacion->cuota_ampliacion_pareja,
                    'total_saldo_capital' => $credito_formato_evaluacion->total_saldo_capital,
                    'total_couta_mensual' => $credito_formato_evaluacion->total_couta_mensual,
                    'total_couta_ampliacion' => $credito_formato_evaluacion->total_couta_ampliacion,
                    'entidad_financiera_cliente' => $credito_formato_evaluacion->entidad_financiera_cliente,
                    'entidad_financiera_pareja' => $credito_formato_evaluacion->entidad_financiera_pareja,
                    'entidad_financiera_total' => $credito_formato_evaluacion->entidad_financiera_total,

                    'idforma_pago_credito' => $credito_formato_evaluacion->idforma_pago_credito,
                    'propuesta_cuotas' => $credito_formato_evaluacion->propuesta_cuotas,
                    'propuesta_monto' => $credito_formato_evaluacion->propuesta_monto,
                    'propuesta_tem' => $credito_formato_evaluacion->propuesta_tem,
                    'propuesta_servicio_otros' => $credito_formato_evaluacion->propuesta_servicio_otros,
                    'propuesta_cargos' => $credito_formato_evaluacion->propuesta_cargos,
                    'propuesta_total_pagar' => $credito_formato_evaluacion->propuesta_total_pagar,
                    'total_propuesta' => $credito_formato_evaluacion->total_propuesta,

                    'resultado_cuota_excedente' => $credito_formato_evaluacion->resultado_cuota_excedente,
                    'estado_evaluacion' => $credito_formato_evaluacion->estado_evaluacion,

                    'referencia' => $credito_formato_evaluacion->referencia,

                    'comentario_centro_laboral' => $credito_formato_evaluacion->comentario_centro_laboral,
                    'comentario_capacidad_pago' => $credito_formato_evaluacion->comentario_capacidad_pago,
                    'sustento_historial_pago' => $credito_formato_evaluacion->sustento_historial_pago,
                    'sustento_destino_credito' => $credito_formato_evaluacion->sustento_destino_credito,
                ]);
                }
              
                // Propuesta
              
                $credito_propuesta = DB::table('credito_propuesta')->where('idcredito',$credito->id)->first();
              
                if($credito_propuesta!=''){
                DB::table('credito_propuesta')->insert([
                    'idcredito' => $idcredito,
                    'fecha' => Carbon::now(),
                    'monto_compra_deuda' => $credito_propuesta->monto_compra_deuda,

                    'idclasificacion_cliente' => $credito_propuesta->idclasificacion_cliente,
                    'idclasificacion_cliente_pareja' => $credito_propuesta->idclasificacion_cliente_pareja,
                    'idclasificacion_aval' => $credito_propuesta->idclasificacion_aval,
                    'idclasificacion_aval_pareja' => $credito_propuesta->idclasificacion_aval_pareja,

                    'detalle_monto_compra_deuda' => $credito_propuesta->detalle_monto_compra_deuda,
                    'neto_destino_credito' => $credito_propuesta->neto_destino_credito,
                    'fenomenos' => $credito_propuesta->fenomenos,

                    'rentabilidad_patrimonial_res_coment' => $credito_propuesta->rentabilidad_patrimonial_res_coment,
                    'rentabilidad_activos_res_coment' => $credito_propuesta->rentabilidad_activos_res_coment,
                    'solvencia_cuota_total_res_coment' => $credito_propuesta->solvencia_cuota_total_res_coment,
                    'solvencia_capital_trabajo_res_coment' => $credito_propuesta->solvencia_capital_trabajo_res_coment,
                    'limites_financiamiento_vru_res_coment' => $credito_propuesta->limites_financiamiento_vru_res_coment,
                    'limites_numero_entidades' => $credito_propuesta->limites_numero_entidades,
                    'limites_numero_entidades_res' => $credito_propuesta->limites_numero_entidades_res,
                    'limites_numero_entidades_res_coment' => $credito_propuesta->limites_numero_entidades_res_coment,
                    'res_solvencia_relacion_cuota_coment' => $credito_propuesta->res_solvencia_relacion_cuota_coment,
                    'res_ratios_tendencia_comportamiento_res_coment' => $credito_propuesta->res_ratios_tendencia_comportamiento_res_coment,
                ]);
                }
              
                // Inventarios activos
              
                $credito_cuantitativa_inventario = DB::table('credito_cuantitativa_inventario')->where('idcredito',$credito->id)->first();
          
                if($credito_cuantitativa_inventario){
                  DB::table('credito_cuantitativa_inventario')->insert([
                    'fecha' => Carbon::now(),
                    'idcredito' => $idcredito,
                    'inventario' => $credito_cuantitativa_inventario->inventario,
                    'total_inventario' => $credito_cuantitativa_inventario->total_inventario,
                    'inmuebles' => $credito_cuantitativa_inventario->inmuebles,
                    'total_inmuebles' => $credito_cuantitativa_inventario->total_inmuebles,
                    'muebles' => $credito_cuantitativa_inventario->muebles,
                    'total_muebles' => $credito_cuantitativa_inventario->total_muebles,
                  ]);
                }
          
            }
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'showtable'){
          $creditos = DB::table('credito')
                            ->join('users as cliente','cliente.id','credito.idcliente')
                            ->leftjoin('users as aval','aval.id','credito.idaval')
                            ->join('modalidad_credito','modalidad_credito.id','credito.idmodalidad_credito')
                            ->join('tipo_operacion_credito','tipo_operacion_credito.id','credito.idtipo_operacion_credito')
                            // ->join('tarifario','tarifario.id','credito.idtarifario')
                            ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                            ->where('credito.estado','PENDIENTE')
                            ->select(
                                'credito.*',
                                'cliente.nombrecompleto as nombrecliente',
                                'aval.nombrecompleto as nombreaval',
                                'credito_prendatario.nombre as nombreproductocredito' 
                            )
                            ->orderBy('credito.id','asc')
                            ->get();
          
          $html = '';
          foreach($creditos as $key => $value){
            
              $option_editarcredito = "<li>
                                    <a class='dropdown-item' href='javascript:;' onclick='btnEditar({$value->id})'>
                                      <i class='fa fa-edit'></i> Editar Crédito
                                    </a>
                                  </li>";
            
              if($value->idcredito_refinanciado!=0){
                  $option_editarcredito = "";
              }
            
              $html .= "<tr id='show_data_select'>
                            <td>".($key+1)."</td>
                            <td>{$value->nombrecliente}</td>
                            <td>{$value->nombreaval}</td>
                            <td>{$value->nombreproductocredito}</td>
                            <td>{$value->monto_solicitado}</td>
                            <td>{$value->estado}</td>
                            <td>{$value->fecha}</td>
                            <td>
                              <div class='dropdown' id='menu-opcion'>
                                <button class='btn btn-primary dropdown-toggle'  type='button' data-bs-toggle='dropdown' aria-expanded='false'>Opción</button>
                                <ul class='dropdown-menu dropdown-menu-end'>
                                  <li>
                                    <a class='dropdown-item' href='javascript:;' data-valor-columna='{$value->id}' onclick='show_data(this)'>
                                      <i class='fa fa-money-bill'></i> Garantia, Cronograma y Evaluación
                                    </a>
                                  </li>
                                  ".$option_editarcredito."
                                  <li>
                                    <a class='dropdown-item' href='javascript:;' onclick='btnEliminar({$value->id})'>
                                      <i class='fa fa-trash'></i> Eliminar Crédito
                                    </a>
                                  </li>
                                </ul>
                              </div>
                            </td>
                            
                        </tr>";
          }
          return array(
            'html' => $html
          );
          
        }
        else if($id == 'show_tipoevaluacion'){
          
          DB::table('credito')->whereId($request->idcredito)->update([
              'idevaluacion'              => $request->input('idevaluacion'),
            ]);
          
        }
        else if($id == 'show_producto_credito'){
          
          $producto_credito = DB::table('credito_prendatario')
                              ->where('credito_prendatario.idforma_credito',$request->input('tipo'))
                              ->where('credito_prendatario.estado','ACTIVO')
                              ->select('credito_prendatario.*')
                              ->orderBy('credito_prendatario.id', 'asc')
                              ->get();

          return $producto_credito;
          /*
          $tarifario_producto = DB::table('tarifario')
                            ->join('forma_pago_credito','forma_pago_credito.id','tarifario.idforma_pago_credito')
                            ->join('credito_prendatario','credito_prendatario.id','tarifario.idcredito_prendatario')
                            ->where('credito_prendatario.idforma_credito',$request->input('tipo_busqueda'))   
                            ->select(
                                'tarifario.*',
                                'credito_prendatario.nombre as nombrecredito'            
                            )
                            ->orderBy('tarifario.id','desc')
                            ->get();
          */
          return $tarifario_producto;
          
        }
        else if($id == 'show_verificarcliente'){
          
          $s_listanegra = DB::table('s_listanegra')->where('idcliente',$request->input('idcliente'))->first();
          $result = '';
          $motivo = '';
          if($s_listanegra!=''){
              $result = 'EN LISTA NEGRA';
              $motivo = $s_listanegra->motivo;
          }
          return [
              'resultado' => $result,
              'motivo' => $motivo,
          ];
          
        }
        else if($id == 'showgarantias'){
          $html = '';
          $credito = DB::table('credito')->whereId($request->input('idcredito'))->first();
          //$credito->idforma_credito
          $where_garantia_prendaria = [];
          $where_garantia_noprendaria = [];
          $garantia_prendaria = DB::table('garantias')
                                ->select(
                                  'garantias.id',
                                  'garantias.fecharegistro as fecharegistro',
                                  'garantias.idcliente as idclientegarantia',
                                  'garantias.descripcion',
                                  'garantias.valor_mercado',
                                  'garantias.valorcomercial as gvalor_comercial',
                                  'garantias.cobertura as gcobertura',
                                )
                            ->where('garantias.idestadoeliminado',1)
                                ->selectRaw("'Prendario' AS tipo_garantia");
          if($credito->idforma_credito == 2) {
            $garantia_prendaria->where('garantias.id', 0);
          }
          
          $garantia_noprendaria = DB::table('garantias_noprendarias')
                                  ->select(
                                    'garantias_noprendarias.id',
                                    'garantias_noprendarias.fecharegistro as fecharegistro',
                                    'garantias_noprendarias.idcliente as idclientegarantia',
                                    'garantias_noprendarias.descripcion',
                                    'garantias_noprendarias.valor_mercado',
                                    'garantias_noprendarias.valor_comercial as gvalor_comercial',
                                    'garantias_noprendarias.valor_realizacion as gcobertura',
                                  )
                            ->where('garantias_noprendarias.idestadoeliminado',1)
                                  ->selectRaw("'No Prendario' AS tipo_garantia");
          if($credito->idforma_credito == 1) {
            $garantia_noprendaria->where('garantias_noprendarias.id', 0);
          }
          
          
          
          $excluir_prendaria = DB::table('credito_garantia')
                                ->join('garantias','garantias.id','credito_garantia.idgarantias')
                                //->join('credito','credito.id','credito_garantia.idcredito')
                                ->where('credito_garantia.idcliente',$request->input('idcliente'))
                                ->select('garantias.id','garantias.descripcion')
                                //->where('credito_garantia.idcredito','<>',$request->input('idcredito'))
                                //->where('credito.idestadocredito',1)
                                ->where('credito_garantia.idestadoentrega',1)
                                //->whereIn('credito.estado',['PENDIENTE','PROCESO','APROBADO','DESEMBOLSADO'])
                                ->get();
          
          /*$excluir_noprendaria = DB::table('credito_garantia')
                                ->join('garantias_noprendarias','garantias_noprendarias.id','credito_garantia.idgarantias_noprendarias')
//                                 ->where('credito_garantia.idcliente',$request->input('idcliente'))
                                ->select('garantias_noprendarias.id','garantias_noprendarias.descripcion')
                                ->where('credito_garantia.idcredito','<>',$request->input('idcredito'))
                                ->get();*/
          
          
          $garantia_prendaria->where('garantias.idcliente',$request->input('idcliente'));
          //$garantia_prendaria->whereNotIn('garantias.id', $excluir_prendaria->pluck('id'));
          
          $garantia_noprendaria->where('garantias_noprendarias.idcliente',$request->input('idcliente'));
          //$garantia_noprendaria->whereNotIn('garantias_noprendarias.id', $excluir_noprendaria->pluck('id'));
          $garantias = $garantia_prendaria
                       ->union($garantia_noprendaria)
                       ->get();
          foreach($garantias as $value){
              $where_garantia = [];
              $where_garantia[] = ['credito_garantia.idcredito',$request->input('idcredito')];
              $where_garantia[] = ['credito_garantia.tipo',$request->input('tipo_garantia')];
            
              if($value->tipo_garantia == 'Prendario'){
                $where_garantia[] = ['credito_garantia.idgarantias',$value->id];
              }else{
                $where_garantia[] = ['credito_garantia.idgarantias_noprendarias',$value->id];
              }
            
              $credito_garantia = DB::table('credito_garantia')
                                    ->where($where_garantia)
                                    ->first();
            
              $check_garantia = $credito_garantia ? 'checked' : '';
              $disabled_garantia = $request->detalle=='false' ? 'disabled' : '';
              
              $idgarantia_prendaria = $value->tipo_garantia == 'Prendario' ? $value->id : 0;
              $idgarantia_noprendaria = $value->tipo_garantia == 'No Prendario' ? $value->id : 0;
            
              $descripcion = $value->descripcion;
              if(strlen($descripcion)>=100){
                  $descripcion = substr($descripcion, 0, 100).'...';    
              }
              
              // valid garantia
              $color_garantia = '';
              $est_garantia = '';
              if($credito->idforma_credito==1){
              $garantia_credito = DB::table('credito_garantia')
                  ->join('credito','credito.id','credito_garantia.idcredito')
                  ->where('credito_garantia.idgarantias',$value->id)
                  ->where('credito.idestadocredito',1)
                  ->where('credito_garantia.idcredito','<>',$request->input('idcredito'))
                  ->whereIn('credito.estado',['PENDIENTE','PROCESO','APROBADO','DESEMBOLSADO'])
                  ->first();
              $color_garantia = $garantia_credito ? 'style="background-color:#3cd48d !important;border-radius: 5px;padding: 3px 5px 3px 5px;white-space: nowrap;"' : '';
              $est_garantia = $garantia_credito ? 'c. Crédito' : '';
              if($garantia_credito==''){
                $garantia_credito = DB::table('credito_garantia')
                    ->join('credito','credito.id','credito_garantia.idcredito')
                    ->where('credito_garantia.idgarantias',$value->id)
                    ->where('credito.idestadocredito',2)
                    ->where('credito_garantia.idestadoentrega',1)
                    ->where('credito_garantia.idcredito','<>',$request->input('idcredito'))
                    ->whereIn('credito.estado',['PENDIENTE','PROCESO','APROBADO','DESEMBOLSADO'])
                    ->first();
                $color_garantia = $garantia_credito ? 'style="background-color:#6bc5ff !important;border-radius: 5px;padding: 3px 5px 3px 5px;white-space: nowrap;"' : '';
                $est_garantia = $garantia_credito ? 'x. Entreg.' : '';
              }
              }
              // fin valid garantia
              $dias = '--';
              if($value->fecharegistro!=''){
                $dias = "<div style='margin-top: 6px;
    background-color: #ffd100;
    float: left;
    padding-left: 3px;
    padding-right: 3px;
    border-radius: 3px;padding-top: 2px;
    padding-bottom: 2px;'>".calcularDiasPasados($value->fecharegistro).' DIA(S)</div>';
              }
              $html .= "<tr idgarantia='{$idgarantia_prendaria}' idgarantianoprendataria='{$idgarantia_noprendaria}' idcliente='{$value->idclientegarantia}'>
                          <td value='{$value->tipo_garantia}' tipo_garantia>{$value->tipo_garantia}</td>
                          <td value='{$value->descripcion}' descripcion>
                          <label 
                                  data-bs-toggle='popover' 
                                  data-bs-placement='right' 
                                  data-bs-content='{$value->descripcion}'>
                              ".substr($descripcion, 0, 100)."
                          </label>
                          </td>
                          <td>{$dias}</td>
                          <td value='{$value->valor_mercado}' valor_mercado>{$value->valor_mercado}</td>
                          <td value='{$value->gvalor_comercial}' valor_comercial>{$value->gvalor_comercial}</td>
                          <td value='{$value->gcobertura}' valor_realizacion>{$value->gcobertura}</td>
                          <td class='mx-td-input' estado_check onclick='suma_garantias();'>";
                          if($est_garantia!=''){
                              $html .= '<div '.$color_garantia.'><b>'.$est_garantia.'</b></div>';
                          }elseif($est_garantia==''){
                            $html .= '
                              <label class="chk">
                                  <input type="checkbox" '.$check_garantia.' '.$disabled_garantia.'>
                                  <span class="checkmark"></span>
                              </label>';
                          }
                 $html .= "</td>
                        </tr>";
            }
            return $html;
          
        }
        else if($id == 'showgarantiacliente'){
          $html = '';
                    $credito_garantia = DB::table('credito_garantia')
                                ->where('credito_garantia.idcredito',$request->input('idcredito'))
                                ->get();
          
          if(count($credito_garantia) > 0){
            foreach($credito_garantia as $value){
              
              $descripcion = $value->descripcion;
              if(strlen($descripcion)>=100){
                  $descripcion = substr($descripcion, 0, 100).'...';    
              }
              
              $html .= "<tr >
                            <td descripcion>
                          <label 
                                  data-bs-toggle='popover' 
                                  data-bs-placement='right' 
                                  data-bs-content='{$value->descripcion}'>
                              ".substr($descripcion, 0, 100)."
                          </label></td>
                            <td valor_mercado>{$value->valor_mercado}</td>
                            <td valor_comercial>{$value->valor_comercial}</td>
                            <td valor_realizacion>{$value->valor_realizacion}</td>
                            <td valor_realizacion>{$value->tipo}</td>
                          </tr>";
            }
            
          }
          return $html;
        }
        else if($id == 'showtasa'){
          //dd($request->input());
          $credito = DB::table('credito')
                  ->whereId($request->input('idcredito'))
                  ->first();
          $tasatarifario = DB::table('tarifario')
                ->where('tarifario.idcredito_prendatario',$credito->idcredito_prendatario)
                ->where('tarifario.idforma_pago_credito',$request->input('frecuencia'))
                ->where('tarifario.monto','>=',$request->input('monto'))
                ->where('tarifario.cuotas','>=',$request->input('numerocuota'))
                ->orderBy('tarifario.cuotas','asc')
                ->orderBy('tarifario.monto','asc')
                ->limit(1)
                ->first();
          $tasa_tem_minima = 0;
          $comision_cargo = 0;
          if($tasatarifario!=''){
              $tasa_tem_minima = $tasatarifario->tem;
              $comision_cargo = $tasatarifario->cargos_otros;
          }
          
          $frecuenciaDiasMap = [
            1 => 26,
            2 => 4,
            3 => 2,
            4 => 1,
          ];
          $dias = $frecuenciaDiasMap[$request->input('frecuencia')];
          $tasa_tip = number_format(($request->input('tasa') / $dias) * $request->input('numerocuota'), 2, '.', '');
          
          return array(
            'tasa_tem_minima' => $tasa_tem_minima,
            'tasa_tip' => $tasa_tip,
            'cargootros' => $comision_cargo,
          );
        }
        else if($id=='cronograma'){
          
          
          $credito = DB::table('credito')
                  ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                  ->where('credito.id',$request->input('idcredito'))
                  ->select(
                      'credito.*',
                      'credito_prendatario.modalidad as modalidad_calculo',
                  )
                  ->first();
          
          $montomaximo = DB::table('tarifario')
                ->where('tarifario.idcredito_prendatario',$credito->idcredito_prendatario)
                ->where('tarifario.idforma_pago_credito',$request->input('frecuencia'))
                ->orderBy('tarifario.monto','desc')
                ->limit(1)
                ->first();
          
          if($montomaximo!='' && $request->modalidad_credito != 'REFINANCIADO'){
              if($request->input('monto')>$montomaximo->monto){
                  return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'El monto máximo según el tarifario es '.$montomaximo->monto.'.',
                  ]);
              }
          }
          
          /*$cuotaminimo = DB::table('tarifario')
                ->where('tarifario.idcredito_prendatario',$credito->idcredito_prendatario)
                ->where('tarifario.idforma_pago_credito',$request->input('frecuencia'))
                ->orderBy('tarifario.cuotas','asc')
                ->limit(1)
                ->first();
          if($cuotaminimo!=''){
              if($request->input('numerocuota')<$cuotaminimo->cuotas){
                  return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'La cuota mínima según el tarifario es '.$cuotaminimo->cuotas.'.',
                  ]);
              }
          }*/
          
          $cuotamaximo = DB::table('tarifario')
                ->where('tarifario.idcredito_prendatario',$credito->idcredito_prendatario)
                ->where('tarifario.idforma_pago_credito',$request->input('frecuencia'))
                ->orderBy('tarifario.cuotas','desc')
                ->limit(1)
                ->first();
          if($cuotamaximo!=''){
              if($request->input('numerocuota')>$cuotamaximo->cuotas){
                  return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'La cuota máxima según el tarifario es '.$cuotamaximo->cuotas.'.',
                  ]);
              }
          }
          
          if($credito->idforma_credito == 1 && $request->modalidad_credito != 'REFINANCIADO'){
              if($request->input('monto') > $credito->monto_cobertura_garantia){
                  return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'El monto máximo según la cobertura es '.$credito->monto_cobertura_garantia.'.',
                  ]);
              }
          }
          
          
          $tasatarifario = DB::table('tarifario')
                ->where('tarifario.idcredito_prendatario',$credito->idcredito_prendatario)
                ->where('tarifario.idforma_pago_credito',$request->input('frecuencia'))
                ->where('tarifario.monto','>=',$request->input('monto'))
                ->where('tarifario.cuotas','>=',$request->input('numerocuota'))
                ->orderBy('tarifario.cuotas','asc')
                ->orderBy('tarifario.monto','asc')
                ->limit(1)
                ->first();
          
          $tasa_tem = $request->input('tasa');
          $tasa_tem_minima = 0;
          $comision_cargo = 0;
          if($tasatarifario!=''){
              $comision_cargo = $tasatarifario->cargos_otros;
              $tasa_tem_minima = $tasatarifario->tem;
              if($request->input('tasa')!='' && $request->input('tasa')>0 && $request->input('tasa') < $tasatarifario->tem){
                  if($request->modalidad_credito != 'REFINANCIADO'){
                  return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'El tasa mínima según el tarifario es '.$tasatarifario->tem.'.',
                  ]);
                  }
              }else{
                  if($request->input('tasa')=='' or $request->input('tasa')==0){
                      $tasa_tem = $tasatarifario->tem;
                  }
              }
          }else{
              if($request->modalidad_credito != 'REFINANCIADO'){
              return response()->json([
                  'resultado' => 'ERROR',
                  'mensaje'   => 'No se asignado ningún tarifario para esta frecuencia de pago!!.',
              ]);
              }
          }
          $frecuenciaDiasMap = [
            1 => 26,
            2 => 4,
            3 => 2,
            4 => 1,
          ];
          $dias = $frecuenciaDiasMap[$request->input('frecuencia')];
          $tasa_tip = number_format(($tasa_tem / $dias) * $request->input('numerocuota'), 2, '.', '');
          if($credito->modalidad_calculo == 'Interes Compuesto'){
              $tasa_tip = $tasa_tem;
          }
    
          $cronograma = genera_cronograma(
                $request->input('monto'),
                $request->input('numerocuota'),
                $request->input('fechainicio'),
                $request->input('frecuencia'),
                $tasa_tip,
                $request->input('tipotasa'),
                $request->input('dia_gracia'),
                $comision_cargo,
                $request->input('cargo')
          );
          
          $html = '';
          foreach($cronograma['cronograma'] as $value){
            
            $html .= '<tr>
                        <td>'.$value['numero'].'</td>
                        <td>'.$value['fecha'].'</td>
                        <td>'.$value['saldo'].'</td>
                        <td>'.$value['amortizacion'].'</td>
                        <td>'.$value['interes'].'</td>
                        <td>'.$value['comision'].'</td>
                        <td>'.$value['cargo'].'</td>
                        <td>'.$value['cuotafinal'].'</td>
                      </tr>';
          }
          $html .= '<tr>
                        <th></th>
                        <th></th>
                        <th>TOTAL</th>
                        <th>'.$cronograma['total_amortizacion'].'</th>
                        <th>'.$cronograma['total_interes'].'</th>
                        <th>'.$cronograma['total_comision'].'</th>
                        <th>'.$cronograma['total_cargo'].'</th>
                        <th>'.$cronograma['total_cuotafinal'].'</th>
                      </tr>';
          return array(
            'cronograma' => $html,
            'tasa_tem' => $tasa_tem,
            'tasa_tem_minima' => $tasa_tem_minima,
            'tasa_tip' => $tasa_tip,
            'tasa_tcem' => number_format($tasa_tem+$comision_cargo,2,'.',''),
            'cargootros' => $comision_cargo,
            'interes_total' => $cronograma['total_interes'],
            'cargo_total' => $cronograma['total_comisioncargo'],
            'total_pagar' => $cronograma['total_cuotafinal']
          );
        }
        else if($id == 'showgiroeconomico'){
          $giros = DB::table('giro_economico_evaluacion')
                          ->where('giro_economico_evaluacion.idtipo_giro_economico',$request->input('tipogiro'))
                          ->where('giro_economico_evaluacion.estado','HABILITADO')
                          ->select(
                              'giro_economico_evaluacion.*'
                          )
                          ->orderBy('giro_economico_evaluacion.id','asc')
                          ->get();
          return $giros;
        }
        else if($id == 'showgiroeconomico_giro'){
          $giros = DB::table('giro_economico_evaluacion')
                          ->where('giro_economico_evaluacion.id',$request->input('giro'))
                          ->where('giro_economico_evaluacion.estado','HABILITADO')
                          ->first();
          $maximo = 0;
          if($giros!=''){
              $maximo = $giros->porcentaje;
          }
          return $maximo;
        }
        else if($id == 'showpersona'){
          $persona = DB::table('users')
                      ->where('users.identificacion',$request->input('dni'))
                      ->select('users.nombrecompleto')
                      ->first();
          return $persona;
        }

    }

    public function edit(Request $request, $idtienda, $id)
    {
        
      
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
      $credito = DB::table('credito')
                  ->join('users as cliente','cliente.id','credito.idcliente')
                  ->join('users as asesor','asesor.id','credito.idasesor')
                  ->leftjoin('users as aval','aval.id','credito.idaval')
                  ->join('forma_credito','forma_credito.id','credito.idforma_credito')
                  ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
                  ->join('modalidad_credito','modalidad_credito.id','credito.idmodalidad_credito')
                  ->join('tipo_destino_credito','tipo_destino_credito.id','credito.idtipo_destino_credito')
                  ->join('tipo_operacion_credito','tipo_operacion_credito.id','credito.idtipo_operacion_credito')
                  /*->join('tarifario','tarifario.id','credito.idtarifario')
                  ->join('credito_prendatario','credito_prendatario.id','tarifario.idcredito_prendatario')*/
                  ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                  ->where('credito.id',$id)
        
                  ->select(
                      'credito.*',
                      'asesor.usuario as usuario_asesor',
                      'cliente.codigo as codigo_cliente',
                      'cliente.identificacion as docuementocliente',
                      'cliente.nombrecompleto as nombreclientecredito',
                      'aval.identificacion as documentoaval',
                      'aval.nombrecompleto as nombreavalcredito',
                      'forma_credito.nombre as forma_credito_nombre',
                      'tipo_operacion_credito.nombre as tipo_operacion_credito_nombre',
                      'modalidad_credito.nombre as modalidad_credito_nombre',
                      'forma_pago_credito.nombre as forma_pago_credito_nombre',
                      'tipo_destino_credito.nombre as tipo_destino_credito_nombre',
                      /*'tarifario.monto as monto_max_credito',
                      'tarifario.cuotas as coutas_max_credito',
                      'tarifario.tem as tem_producto',
                      'tarifario.tipo_producto_credito as tipo_producto_credito',*/
                      'credito_prendatario.nombre as nombreproductocredito',
                      'credito_prendatario.modalidad as modalidad_calculo',
                      'credito_prendatario.conevaluacion as conevaluacion',
                  )
                  ->orderBy('credito.id','desc')
                  ->first();
      
      
      
      $tarifario_producto = DB::table('tarifario')
                            ->join('forma_pago_credito','forma_pago_credito.id','tarifario.idforma_pago_credito')
                            ->where('tarifario.idcredito_prendatario',$credito->idcredito_prendatario)
                            ->select(
                                'tarifario.*',
                                'forma_pago_credito.nombre as nombreformapago',          
                            )
                            ->orderBy('tarifario.id','desc')
                            ->get();
      
      $usuario = DB::table('users')
            ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
            ->leftJoin('ubigeo as ubigeonacimiento','ubigeonacimiento.id','users.idubigeo_nacimiento')
            ->leftJoin('role_user','role_user.user_id','users.id')
            ->leftJoin('roles','roles.id','role_user.role_id')
            ->where('users.id', $credito->idcliente)
            ->select(
                'users.*',
                'roles.id as idroles',
                'roles.description as descriptionrole',
                'ubigeo.nombre as ubigeonombre',
                'ubigeonacimiento.nombre as ubigeonacimientonombre'
            )
            ->first();
      
      $usuario_aval = DB::table('users')
            ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
            ->leftJoin('ubigeo as ubigeonacimiento','ubigeonacimiento.id','users.idubigeo_nacimiento')
            ->leftJoin('role_user','role_user.user_id','users.id')
            ->leftJoin('roles','roles.id','role_user.role_id')
            ->where('users.id', $credito->idaval)
            ->select(
                'users.*',
                'roles.id as idroles',
                'roles.description as descriptionrole',
                'ubigeo.nombre as ubigeonombre',
                'ubigeonacimiento.nombre as ubigeonacimientonombre'
            )
            ->first();
      $users_prestamo = DB::table('s_users_prestamo')->where('s_users_prestamo.id_s_users',$credito->idcliente)->first();
      $users_prestamo_aval = DB::table('s_users_prestamo')->where('s_users_prestamo.id_s_users',$credito->idaval)->first();
      $evaluacion_mes = DB::table('evaluacion_mes')->get();
      if($request->input('view') == 'editar') {

        return view(sistema_view().'/credito/edit',[
          'tienda' => $tienda,
          'usuario' => $usuario,
          'modalidad_credito' => $this->modalidad_credito,
          'tipo_operacion_credito' => $this->tipo_operacion_credito,
          'forma_pago_credito' => $this->forma_pago_credito,
          'tipo_destino_credito' => $this->tipo_destino_credito,
          'tarifario_producto' => $tarifario_producto,
          'credito' => $credito,
           'forma_credito' => $this->forma_credito,
        ]);
      }
      else if( $request->input('view') == 'cronograma' ){
        
        $tipocredito = $credito->idforma_credito== 1 ? 'PRENDARIA' : 'NOPRENDARIA';
        $diasdegracia = DB::table('diasdegracia')->where('diasdegracia.nombre',$tipocredito)->first();
        
        return view(sistema_view().'/credito/cronograma',[
          'tienda' => $tienda,
          'usuario' => $usuario,
          'modalidad_credito' => $this->modalidad_credito,
          'tipo_operacion_credito' => $this->tipo_operacion_credito,
          'forma_pago_credito' => $this->forma_pago_credito,
          'tipo_destino_credito' => $this->tipo_destino_credito,
          'tarifario_producto' => $tarifario_producto,
          'credito' => $credito,
          'forma_credito' => $this->forma_credito,
          'diasdegracia' => $diasdegracia->dias,
          'view_detalle' => $request->detalle
        ]);
      }
      else if( $request->input('view') == 'opciones' ){
        return view(sistema_view().'/credito/opciones',[
          'tienda' => $tienda,
          'credito' => $credito,
          'usuario' => $usuario,
          'users_prestamo' => $users_prestamo,
        ]);
      }
      else if( $request->input('view') == 'eliminar' ){
        return view(sistema_view().'/credito/delete',[
          'tienda' => $tienda,
          'credito' => $credito,
          'usuario' => $usuario,
          
        ]);
      }
      else if( $request->input('view') == 'fuente_ingreso' ){
        return view(sistema_view().'/credito/fuente_ingreso',[
          'users_prestamo'    => $users_prestamo,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'view_detalle' => $request->detalle
        ]);
      }
      else if( $request->input('view') == 'evaluacion_cualitativa' ){
        
        $ejercicio_giro_economico = DB::table('ejercicio_giro_economico')->get();
        
        $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')->where('credito_evaluacion_cualitativa.idcredito',$id)->first();
        return view(sistema_view().'/credito/evaluacion_cualitativa',[
          'users_prestamo'    => $users_prestamo,
          'evaluacion_mes'    => $evaluacion_mes,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'credito_evaluacion_cualitativa' => $credito_evaluacion_cualitativa,
          'giro_economico_evaluacion' => $this->giro_economico_evaluacion,
          'tipo_giro_economico' => $this->tipo_giro_economico,
          'f_tiporeferencia' => $this->f_tiporeferencia,
          'ejercicio_giro_economico' => $ejercicio_giro_economico,
          'view_detalle' => $request->detalle
        ]);
        
      }
      else if( $request->input('view') == 'evaluacion_cuantitativa' ){
        
        
        $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_cualitativa.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_cualitativa.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_cualitativa.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_cualitativa.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion',
                                            'giro_economico_evaluacion.porcentaje as margen_giro_economico',
                                          )
                                          ->first();
        
        $credito_cuantitativa_margen_venta = DB::table('credito_cuantitativa_margen_venta')->where('credito_cuantitativa_margen_venta.idcredito',$id)->first();
        $credito_evaluacion_cuantitativa = DB::table('credito_evaluacion_cuantitativa')->where('credito_evaluacion_cuantitativa.idcredito',$id)->first();
        $credito_cuantitativa_inventario = DB::table('credito_cuantitativa_inventario')->where('credito_cuantitativa_inventario.idcredito',$id)->first();
        $credito_cuantitativa_deudas = DB::table('credito_cuantitativa_deudas')->where('credito_cuantitativa_deudas.idcredito',$id)->first();
        $credito_cuantitativa_ingreso_adicional = DB::table('credito_cuantitativa_ingreso_adicional')->where('credito_cuantitativa_ingreso_adicional.idcredito',$id)->first();
        
        // Evaluación Anterior
        
        $credito_anterior = DB::table('credito')
            //->where('credito.idestadocredito',1)
            ->where('credito.idcliente',$credito->idcliente)
            ->where('credito.estado','DESEMBOLSADO')
            ->whereIn('credito.idestadocredito',[1,2])
            ->orderBy('credito.idestadocredito','asc')
            ->orderBy('credito.fecha_desembolso','desc')
            ->limit(1)
            ->first();
        
        $idcredito_anterior = 0;
        if($credito_anterior){
            $idcredito_anterior = $credito_anterior->id;
        }
        
        $credito_evaluacion_cualitativa_anterior = DB::table('credito_evaluacion_cualitativa')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_cualitativa.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_cualitativa.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_cualitativa.idcredito',$idcredito_anterior)
                                          ->select(
                                            'credito_evaluacion_cualitativa.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion',
                                            'giro_economico_evaluacion.porcentaje as margen_giro_economico',
                                          )
                                          ->first();
        
        $credito_cuantitativa_margen_venta_anterior = DB::table('credito_cuantitativa_margen_venta')
            ->where('credito_cuantitativa_margen_venta.idcredito',$idcredito_anterior)
            ->first();
        
        
        $credito_evaluacion_cuantitativa_anterior = DB::table('credito_evaluacion_cuantitativa')
            ->where('credito_evaluacion_cuantitativa.idcredito',$idcredito_anterior)
            ->first();
        
        $credito_cuantitativa_inventario_anterior = DB::table('credito_cuantitativa_inventario')
            ->where('credito_cuantitativa_inventario.idcredito',$idcredito_anterior)
            ->first();
        
        $credito_cuantitativa_deudas_anterior = DB::table('credito_cuantitativa_deudas')
            ->where('credito_cuantitativa_deudas.idcredito',$idcredito_anterior)
            ->first();
        
        $credito_cuantitativa_ingreso_adicional_anterior = DB::table('credito_cuantitativa_ingreso_adicional')
            ->where('credito_cuantitativa_ingreso_adicional.idcredito',$idcredito_anterior)
            ->first();
        
        
        //dump($credito_anterior);
        
        return view(sistema_view().'/credito/evaluacion_cuantitativa',[
          'users_prestamo'    => $users_prestamo,
          'evaluacion_mes'    => $evaluacion_mes,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'credito_evaluacion_cualitativa' => $credito_evaluacion_cualitativa,
          'credito_evaluacion_cuantitativa' => $credito_evaluacion_cuantitativa,
          'giro_economico_evaluacion' => $this->giro_economico_evaluacion,
          'tipo_giro_economico' => $this->tipo_giro_economico,
          'f_tiporeferencia' => $this->f_tiporeferencia,
          'credito_cuantitativa_margen_venta' => $credito_cuantitativa_margen_venta,
          'credito_cuantitativa_inventario' => $credito_cuantitativa_inventario,
          'credito_cuantitativa_deudas' => $credito_cuantitativa_deudas,
          'credito_cuantitativa_ingreso_adicional' => $credito_cuantitativa_ingreso_adicional,
          'view_detalle' => $request->detalle,
          'credito_evaluacion_cualitativa_anterior' => $credito_evaluacion_cualitativa_anterior,
          'credito_evaluacion_cuantitativa_anterior' => $credito_evaluacion_cuantitativa_anterior,
          'credito_cuantitativa_margen_venta_anterior' => $credito_cuantitativa_margen_venta_anterior,
          'credito_cuantitativa_inventario_anterior' => $credito_cuantitativa_inventario_anterior,
          'credito_cuantitativa_deudas_anterior' => $credito_cuantitativa_deudas_anterior,
          'credito_cuantitativa_ingreso_adicional_anterior' => $credito_cuantitativa_ingreso_adicional_anterior,
          
        ]);
        
      }
      else if( $request->input('view') == 'control_limites' ){
        
        $credito_cuantitativa_control_limites = DB::table('credito_cuantitativa_control_limites')->where('credito_cuantitativa_control_limites.idcredito',$id)->first();
        $credito_cuantitativa_margen_venta = DB::table('credito_cuantitativa_margen_venta')->where('credito_cuantitativa_margen_venta.idcredito',$id)->first();
        $credito_evaluacion_cuantitativa = DB::table('credito_evaluacion_cuantitativa')->where('credito_evaluacion_cuantitativa.idcredito',$id)->first();
        $credito_cuantitativa_deudas = DB::table('credito_cuantitativa_deudas')->where('credito_cuantitativa_deudas.idcredito',$id)->first();
        $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_cualitativa.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_cualitativa.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_cualitativa.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_cualitativa.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion'
                                          )
                                          ->first();
        
          $credito_aval = DB::table('credito')->where('credito.idcliente',$credito->idaval)->first();
          $idcredito_aval_idcliente = 0;
          $idcredito_aval_idaval = 0;
          if($credito_aval){
              $idcredito_aval_idcliente = $credito_aval->idcliente;
              $idcredito_aval_idaval = $credito_aval->idaval;
          }
          //-------- GARANTIAS DE CLIENTE
        
          $credito_garantias_cliente = DB::table('credito_garantia')
              ->where('credito_garantia.idcredito',$credito->id)
              ->where('credito_garantia.idgarantias',0)
              ->where('credito_garantia.tipo','CLIENTE')
              ->select(
                  'credito_garantia.idgarantias_noprendarias as idgarantias_noprendarias',
                  'credito_garantia.idgarantias as idgarantias',
                  'credito_garantia.garantias_noprendarias_tipo_garantia_noprendaria as garantias_noprendarias_tipo_garantia_noprendaria',
                  'credito_garantia.descripcion as descripcion',
                  'credito_garantia.valor_mercado as valor_mercado',
                  'credito_garantia.valor_comercial as valor_comercial',
                  'credito_garantia.valor_realizacion as valor_realizacion',
              )
              ->get();
        
          $lista_credito_garantia_cliente_propio = DB::table('credito')
              ->join('credito_garantia','credito_garantia.idcredito','credito.id')
              ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
              ->where('credito_garantia.idcredito','<>',$credito->id)
              ->where('credito_garantia.idcliente',$credito->idcliente)
              ->where('credito_garantia.tipo','CLIENTE')
              ->where('credito.estado','DESEMBOLSADO')
              ->where('credito.idforma_credito',2)
              ->where('credito.idestadocredito',1)
              ->select(
                  'credito.id as idcredito',
                  'credito.idforma_credito as idforma_credito',
                  'credito.cuenta as credito_cuenta',
                  'credito_prendatario.modalidad as modalidadproductocredito',
                  DB::raw('CONCAT("PROPIO") as tipoprestamo')
              )
              ->distinct()
              ->get();
        
          $lista_credito_garantia_cliente_aval = DB::table('credito')
              ->join('credito_garantia','credito_garantia.idcredito','credito.id')
              ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
              ->where('credito_garantia.idcliente',$credito->idcliente)
              ->where('credito_garantia.tipo','AVAL')
              ->where('credito.estado','DESEMBOLSADO')
              ->where('credito.idforma_credito',2)
              ->where('credito.idestadocredito',1)
              ->select(
                  'credito.id as idcredito',
                  'credito.idforma_credito as idforma_credito',
                  'credito.cuenta as credito_cuenta',
                  'credito_prendatario.modalidad as modalidadproductocredito',
                  DB::raw('CONCAT("AVALADO") as tipoprestamo')
              )
              ->get();
        
          $credito_saldodeduda_cliente_propio = [];
          $credito_saldodeduda_cliente_aval = [];
        
          foreach($lista_credito_garantia_cliente_propio as $valuec){

              $credito_descuentocuotas = DB::table('credito_descuentocuota')
                    ->where('credito_descuentocuota.idcredito',$valuec->idcredito)
                    ->where('credito_descuentocuota.idestadocredito_descuentocuota',1)
                    ->first();
              $total_descuento_capital = 0; 
              $total_descuento_interes = 0; 
              $total_descuento_comision = 0; 
              $total_descuento_cargo = 0;  
              $total_descuento_penalidad = 0; 
              $total_descuento_tenencia = 0; 
              $total_descuento_compensatorio = 0; 
              $total_descuento_total = 0; 
              if($credito_descuentocuotas){
                  if(1000>=$credito_descuentocuotas->numerocuota_fin){
                      $total_descuento_capital = $credito_descuentocuotas->capital;
                      $total_descuento_interes = $credito_descuentocuotas->interes;
                      $total_descuento_comision = $credito_descuentocuotas->comision;
                      $total_descuento_cargo = $credito_descuentocuotas->cargo;
                      $total_descuento_penalidad = $credito_descuentocuotas->penalidad;
                      $total_descuento_tenencia = $credito_descuentocuotas->tenencia;
                      $total_descuento_compensatorio = $credito_descuentocuotas->compensatorio;
                      $total_descuento_total = $credito_descuentocuotas->total;
                  }
              }
              $cronograma = select_cronograma(
                  $tienda->id,
                  $valuec->idcredito,
                  $valuec->idforma_credito,
                  $valuec->modalidadproductocredito,
                  1000,
                  $total_descuento_capital,
                  $total_descuento_interes,
                  $total_descuento_comision,
                  $total_descuento_cargo,
                  $total_descuento_penalidad,
                  $total_descuento_tenencia,
                  $total_descuento_compensatorio
              );

              $credito_saldodeduda_cliente_propio[] = [
                  'idforma_credito' => $valuec->idforma_credito,
                  'modalidad' => $valuec->modalidadproductocredito,
                  'cuenta' => 'C'.str_pad($valuec->credito_cuenta, 8, "0", STR_PAD_LEFT),
                  'saldo_vigente' => $cronograma['saldo_capital'],
              ];
          }
          foreach($lista_credito_garantia_cliente_aval as $valuec){

              $credito_descuentocuotas = DB::table('credito_descuentocuota')
                    ->where('credito_descuentocuota.idcredito',$valuec->idcredito)
                    ->where('credito_descuentocuota.idestadocredito_descuentocuota',1)
                    ->first();
              $total_descuento_capital = 0; 
              $total_descuento_interes = 0; 
              $total_descuento_comision = 0; 
              $total_descuento_cargo = 0;  
              $total_descuento_penalidad = 0; 
              $total_descuento_tenencia = 0; 
              $total_descuento_compensatorio = 0; 
              $total_descuento_total = 0; 
              if($credito_descuentocuotas){
                  if(1000>=$credito_descuentocuotas->numerocuota_fin){
                      $total_descuento_capital = $credito_descuentocuotas->capital;
                      $total_descuento_interes = $credito_descuentocuotas->interes;
                      $total_descuento_comision = $credito_descuentocuotas->comision;
                      $total_descuento_cargo = $credito_descuentocuotas->cargo;
                      $total_descuento_penalidad = $credito_descuentocuotas->penalidad;
                      $total_descuento_tenencia = $credito_descuentocuotas->tenencia;
                      $total_descuento_compensatorio = $credito_descuentocuotas->compensatorio;
                      $total_descuento_total = $credito_descuentocuotas->total;
                  }
              }
              $cronograma = select_cronograma(
                  $tienda->id,
                  $valuec->idcredito,
                  $valuec->idforma_credito,
                  $valuec->modalidadproductocredito,
                  1000,
                  $total_descuento_capital,
                  $total_descuento_interes,
                  $total_descuento_comision,
                  $total_descuento_cargo,
                  $total_descuento_penalidad,
                  $total_descuento_tenencia,
                  $total_descuento_compensatorio
              );

              $credito_saldodeduda_cliente_aval[] = [
                  'idforma_credito' => $valuec->idforma_credito,
                  'modalidad' => $valuec->modalidadproductocredito,
                  'cuenta' => 'C'.str_pad($valuec->credito_cuenta, 8, "0", STR_PAD_LEFT),
                  'saldo_vigente' => $cronograma['saldo_capital'],
              ];
          }
          //-------- GARANTIAS DE AVAL
        
          $credito_garantias_aval = DB::table('credito_garantia')
              ->where('credito_garantia.idcredito',$credito->id)
              ->where('credito_garantia.idgarantias',0)
              ->where('credito_garantia.tipo','AVAL')
              ->select(
                  'credito_garantia.idgarantias_noprendarias as idgarantias_noprendarias',
                  'credito_garantia.idgarantias as idgarantias',
                  'credito_garantia.garantias_noprendarias_tipo_garantia_noprendaria as garantias_noprendarias_tipo_garantia_noprendaria',
                  'credito_garantia.descripcion as descripcion',
                  'credito_garantia.valor_mercado as valor_mercado',
                  'credito_garantia.valor_comercial as valor_comercial',
                  'credito_garantia.valor_realizacion as valor_realizacion',
              )
              ->get();
          //dump($idcredito_aval_idcliente);
          $lista_credito_garantia_aval_propio = DB::table('credito')
              ->join('credito_garantia','credito_garantia.idcredito','credito.id')
              ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
              ->where('credito_garantia.idcredito','<>',$credito->id)
              ->where('credito_garantia.idcliente',$idcredito_aval_idcliente)
              ->where('credito_garantia.tipo','CLIENTE')
              ->where('credito.estado','DESEMBOLSADO')
              ->where('credito.idforma_credito',2)
              ->where('credito.idestadocredito',1)
              ->select(
                  'credito.id as idcredito',
                  'credito.idforma_credito as idforma_credito',
                  'credito.cuenta as credito_cuenta',
                  'credito_prendatario.modalidad as modalidadproductocredito',
                  DB::raw('CONCAT("PROPIO") as tipoprestamo')
              )
              ->distinct()
              ->get();
        
          $lista_credito_garantia_aval_aval = DB::table('credito')
              ->join('credito_garantia','credito_garantia.idcredito','credito.id')
              ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
              ->where('credito_garantia.idcliente',$idcredito_aval_idcliente)
              ->where('credito_garantia.tipo','AVAL')
              ->where('credito.estado','DESEMBOLSADO')
              ->where('credito.idforma_credito',2)
              ->where('credito.idestadocredito',1)
              ->select(
                  'credito.id as idcredito',
                  'credito.idforma_credito as idforma_credito',
                  'credito.cuenta as credito_cuenta',
                  'credito_prendatario.modalidad as modalidadproductocredito',
                  DB::raw('CONCAT("AVALADO") as tipoprestamo')
              )
              ->get();
        
          $credito_saldodeduda_aval_propio = [];
          $credito_saldodeduda_aval_aval = [];
        
          foreach($lista_credito_garantia_aval_propio as $valuec){

              $credito_descuentocuotas = DB::table('credito_descuentocuota')
                    ->where('credito_descuentocuota.idcredito',$valuec->idcredito)
                    ->where('credito_descuentocuota.idestadocredito_descuentocuota',1)
                    ->first();
              $total_descuento_capital = 0; 
              $total_descuento_interes = 0; 
              $total_descuento_comision = 0; 
              $total_descuento_cargo = 0;  
              $total_descuento_penalidad = 0; 
              $total_descuento_tenencia = 0; 
              $total_descuento_compensatorio = 0; 
              $total_descuento_total = 0; 
              if($credito_descuentocuotas){
                  if(1000>=$credito_descuentocuotas->numerocuota_fin){
                      $total_descuento_capital = $credito_descuentocuotas->capital;
                      $total_descuento_interes = $credito_descuentocuotas->interes;
                      $total_descuento_comision = $credito_descuentocuotas->comision;
                      $total_descuento_cargo = $credito_descuentocuotas->cargo;
                      $total_descuento_penalidad = $credito_descuentocuotas->penalidad;
                      $total_descuento_tenencia = $credito_descuentocuotas->tenencia;
                      $total_descuento_compensatorio = $credito_descuentocuotas->compensatorio;
                      $total_descuento_total = $credito_descuentocuotas->total;
                  }
              }
              $cronograma = select_cronograma(
                  $tienda->id,
                  $valuec->idcredito,
                  $valuec->idforma_credito,
                  $valuec->modalidadproductocredito,
                  1000,
                  $total_descuento_capital,
                  $total_descuento_interes,
                  $total_descuento_comision,
                  $total_descuento_cargo,
                  $total_descuento_penalidad,
                  $total_descuento_tenencia,
                  $total_descuento_compensatorio
              );

              $credito_saldodeduda_aval_propio[] = [
                  'idforma_credito' => $valuec->idforma_credito,
                  'modalidad' => $valuec->modalidadproductocredito,
                  'cuenta' => 'C'.str_pad($valuec->credito_cuenta, 8, "0", STR_PAD_LEFT),
                  'saldo_vigente' => $cronograma['saldo_capital'],
              ];
          }
          foreach($lista_credito_garantia_aval_aval as $valuec){

              $credito_descuentocuotas = DB::table('credito_descuentocuota')
                    ->where('credito_descuentocuota.idcredito',$valuec->idcredito)
                    ->where('credito_descuentocuota.idestadocredito_descuentocuota',1)
                    ->first();
              $total_descuento_capital = 0; 
              $total_descuento_interes = 0; 
              $total_descuento_comision = 0; 
              $total_descuento_cargo = 0;  
              $total_descuento_penalidad = 0; 
              $total_descuento_tenencia = 0; 
              $total_descuento_compensatorio = 0; 
              $total_descuento_total = 0; 
              if($credito_descuentocuotas){
                  if(1000>=$credito_descuentocuotas->numerocuota_fin){
                      $total_descuento_capital = $credito_descuentocuotas->capital;
                      $total_descuento_interes = $credito_descuentocuotas->interes;
                      $total_descuento_comision = $credito_descuentocuotas->comision;
                      $total_descuento_cargo = $credito_descuentocuotas->cargo;
                      $total_descuento_penalidad = $credito_descuentocuotas->penalidad;
                      $total_descuento_tenencia = $credito_descuentocuotas->tenencia;
                      $total_descuento_compensatorio = $credito_descuentocuotas->compensatorio;
                      $total_descuento_total = $credito_descuentocuotas->total;
                  }
              }
              $cronograma = select_cronograma(
                  $tienda->id,
                  $valuec->idcredito,
                  $valuec->idforma_credito,
                  $valuec->modalidadproductocredito,
                  1000,
                  $total_descuento_capital,
                  $total_descuento_interes,
                  $total_descuento_comision,
                  $total_descuento_cargo,
                  $total_descuento_penalidad,
                  $total_descuento_tenencia,
                  $total_descuento_compensatorio
              );

              $credito_saldodeduda_aval_aval[] = [
                  'idforma_credito' => $valuec->idforma_credito,
                  'modalidad' => $valuec->modalidadproductocredito,
                  'cuenta' => 'C'.str_pad($valuec->credito_cuenta, 8, "0", STR_PAD_LEFT),
                  'saldo_vigente' => $cronograma['saldo_capital'],
              ];
          }

          $credito_evaluacion_resumida = DB::table('credito_evaluacion_resumida')
              ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_resumida.idgiro_economico_evaluacion')
              ->where('credito_evaluacion_resumida.idcredito',$id)
              ->select(
                'credito_evaluacion_resumida.*',
                'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion'
              )
              ->first();
        
          return view(sistema_view().'/credito/control_limites',[
              'users_prestamo'    => $users_prestamo,
              'users_prestamo_aval'    => $users_prestamo_aval,
              'evaluacion_mes'    => $evaluacion_mes,
              'tienda' => $tienda,
              'usuario' => $usuario,
              'credito' => $credito,
              'giro_economico_evaluacion' => $this->giro_economico_evaluacion,
              'tipo_giro_economico' => $this->tipo_giro_economico,
              'f_tiporeferencia' => $this->f_tiporeferencia,
              'credito_evaluacion_resumida' => $credito_evaluacion_resumida,
              'credito_cuantitativa_control_limites' => $credito_cuantitativa_control_limites,
              'credito_evaluacion_cualitativa' => $credito_evaluacion_cualitativa,
              'credito_cuantitativa_margen_venta' => $credito_cuantitativa_margen_venta,
              'credito_cuantitativa_deudas' => $credito_cuantitativa_deudas,
              'credito_evaluacion_cuantitativa' => $credito_evaluacion_cuantitativa,
              'credito_garantias_cliente' => $credito_garantias_cliente,
              'credito_garantias_aval' => $credito_garantias_aval,
              'credito_saldodeduda_cliente_propio' => $credito_saldodeduda_cliente_propio,
              'credito_saldodeduda_cliente_aval' => $credito_saldodeduda_cliente_aval,
              'credito_saldodeduda_aval_propio' => $credito_saldodeduda_aval_propio,
              'credito_saldodeduda_aval_aval' => $credito_saldodeduda_aval_aval,
              'view_detalle' => $request->detalle
          ]);
      }
      else if( $request->input('view') == 'deudas' ){
        
        $credito_cuantitativa_deudas = DB::table('credito_cuantitativa_deudas')->where('credito_cuantitativa_deudas.idcredito',$id)->first();
        $credito_cuantitativa_margen_venta = DB::table('credito_cuantitativa_margen_venta')->where('credito_cuantitativa_margen_venta.idcredito',$id)->first();
        $credito_evaluacion_cuantitativa = DB::table('credito_evaluacion_cuantitativa')->where('credito_evaluacion_cuantitativa.idcredito',$id)->first();
        
        $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_cualitativa.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_cualitativa.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_cualitativa.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_cualitativa.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion'
                                          )
                                          ->first();
        $tipocredito = $credito->idforma_credito== 1 ? 'PRENDARIA' : 'NOPRENDARIA';
        $diasdegracia = DB::table('diasdegracia')->where('diasdegracia.nombre',$tipocredito)->first();
        
        return view(sistema_view().'/credito/deudas',[
          'diasdegracia' => $diasdegracia->dias,
          'users_prestamo'    => $users_prestamo,
          'evaluacion_mes'    => $evaluacion_mes,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'forma_pago_credito' => $this->forma_pago_credito,
          'giro_economico_evaluacion' => $this->giro_economico_evaluacion,
          'tipo_giro_economico' => $this->tipo_giro_economico,
          'tipo_credito_evaluacion' => $this->tipo_credito_evaluacion,
          'f_tiporeferencia' => $this->f_tiporeferencia,
          'credito_cuantitativa_deudas' => $credito_cuantitativa_deudas,
          'credito_evaluacion_cuantitativa' => $credito_evaluacion_cuantitativa,
          'credito_evaluacion_cualitativa' => $credito_evaluacion_cualitativa,
          'credito_cuantitativa_margen_venta' => $credito_cuantitativa_margen_venta,
          'view_detalle' => $request->detalle
        ]);
        
      }
      else if( $request->input('view') == 'ingresos_adicionales' ){
        
        $credito_cuantitativa_deudas = DB::table('credito_cuantitativa_deudas')->where('credito_cuantitativa_deudas.idcredito',$id)->first();
        $credito_cuantitativa_margen_venta = DB::table('credito_cuantitativa_margen_venta')->where('credito_cuantitativa_margen_venta.idcredito',$id)->first();
        $credito_cuantitativa_ingreso_adicional = DB::table('credito_cuantitativa_ingreso_adicional')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_cuantitativa_ingreso_adicional.idgiro_economico_evaluacion_adicional')
                                          ->where('credito_cuantitativa_ingreso_adicional.idcredito',$id)
                                          ->select(
                                            'credito_cuantitativa_ingreso_adicional.*',
                                            'giro_economico_evaluacion.porcentaje as margen_giro_economico',
                                          )
                                          ->first();
        $credito_evaluacion_cuantitativa = DB::table('credito_evaluacion_cuantitativa')->where('credito_evaluacion_cuantitativa.idcredito',$id)->first();
        $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_cualitativa.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_cualitativa.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_cualitativa.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_cualitativa.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion',
                                            //'giro_economico_evaluacion.porcentaje as margen_giro_economico',
                                          )
                                          ->first();
        $tipocredito = $credito->idforma_credito== 1 ? 'PRENDARIA' : 'NOPRENDARIA';
        $diasdegracia = DB::table('diasdegracia')->where('diasdegracia.nombre',$tipocredito)->first();
        
        return view(sistema_view().'/credito/ingresos_adicionales',[
          'diasdegracia' => $diasdegracia->dias,
          'users_prestamo'    => $users_prestamo,
          'evaluacion_mes'    => $evaluacion_mes,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'forma_pago_credito' => $this->forma_pago_credito,
          'giro_economico_evaluacion' => $this->giro_economico_evaluacion,
          'tipo_giro_economico' => $this->tipo_giro_economico,
          'tipo_credito_evaluacion' => $this->tipo_credito_evaluacion,
          'f_tiporeferencia' => $this->f_tiporeferencia,
          'unidadmedida_credito' => $this->unidadmedida_credito,
          'credito_cuantitativa_deudas' => $credito_cuantitativa_deudas,
          'credito_evaluacion_cuantitativa' => $credito_evaluacion_cuantitativa,
          'credito_evaluacion_cualitativa' => $credito_evaluacion_cualitativa,
          'credito_cuantitativa_margen_venta' => $credito_cuantitativa_margen_venta,
          'credito_cuantitativa_ingreso_adicional' => $credito_cuantitativa_ingreso_adicional,
          'view_detalle' => $request->detalle
        ]);
      }
      else if( $request->input('view') == 'margen_ventas' ){
        $credito_cuantitativa_margen_venta = DB::table('credito_cuantitativa_margen_venta')->where('credito_cuantitativa_margen_venta.idcredito',$id)->first();
        
        
        $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_cualitativa.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_cualitativa.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_cualitativa.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_cualitativa.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion',
                                            'giro_economico_evaluacion.porcentaje as margen_giro_economico',
                                          )
                                          ->first();

        return view(sistema_view().'/credito/margen_ventas',[
          'users_prestamo'    => $users_prestamo,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'unidadmedida_credito' => $this->unidadmedida_credito,
          'credito_evaluacion_cualitativa' => $credito_evaluacion_cualitativa,
          'credito_cuantitativa_margen_venta' => $credito_cuantitativa_margen_venta,
          'view_detalle' => $request->detalle
        ]);
      }
      else if( $request->input('view') == 'inventario_activos' ){
        $credito_cuantitativa_inventario = DB::table('credito_cuantitativa_inventario')->where('credito_cuantitativa_inventario.idcredito',$id)->first();
        $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_cualitativa.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_cualitativa.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_cualitativa.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_cualitativa.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion'
                                          )
                                          ->first();
        return view(sistema_view().'/credito/inventario_activos',[
          'users_prestamo'    => $users_prestamo,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'unidadmedida_credito' => $this->unidadmedida_credito,
          'credito_evaluacion_cualitativa' => $credito_evaluacion_cualitativa,
          'credito_cuantitativa_inventario' => $credito_cuantitativa_inventario,
          'view_detalle' => $request->detalle
        ]);
      }
      else if( $request->input('view') == 'datos_cliente' ){
        
        return view(sistema_view().'/credito/datos_cliente',[
          'users_prestamo'    => $users_prestamo,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito
        ]);
      }
      else if( $request->input('view') == 'garantia_cliente' ){
        return view(sistema_view().'/credito/garantia_cliente',[
          'tienda' => $tienda,
          'credito' => $credito,
          'view_detalle' => $request->detalle
        ]);
      }
      else if( $request->input('view') == 'garantia_aval' ){
        return view(sistema_view().'/credito/garantia_aval',[
          'tienda' => $tienda,
          'credito' => $credito
        ]);
      }
      else if( $request->input('view') == 'solicitud' ){
        return view(sistema_view().'/credito/solicitud',[
          'tienda' => $tienda,
          'credito' => $credito
        ]);
      }
      else if( $request->input('view') == 'pdfsolicitud' ){

        $garantia_cliente = DB::table('credito_garantia')
                                ->where('credito_garantia.idcredito',$id)
                                ->where('credito_garantia.tipo','CLIENTE')
                                ->get();
        
        $garantia_aval = DB::table('credito_garantia')
                                ->where('credito_garantia.idcredito',$id)
                                ->where('credito_garantia.tipo','AVAL')
                                ->get();
        
        $pdf = PDF::loadView(sistema_view().'/credito/pdfsolicitud',[
            'users_prestamo'    => $users_prestamo,
            'users_prestamo_aval'    => $users_prestamo_aval,
            'garantia_cliente'  => $garantia_cliente,
            'garantia_aval'  => $garantia_aval,
            'tienda' => $tienda,
            'usuario' => $usuario,
            'usuario_aval' => $usuario_aval,
            'credito' => $credito
        ]); 
        $pdf->setPaper('A4');
        // $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('SOLICITUD DE CREDITO.pdf');
      }
      else if( $request->input('view') == 'solicitud_cualitativa' ){
        return view(sistema_view().'/credito/solicitud_cualitativa',[
          'tienda' => $tienda,
          'credito' => $credito
        ]);
      }
      else if( $request->input('view') == 'pdfsolicitud_cualitativa' ){

        $ejercicio_giro_economico = DB::table('ejercicio_giro_economico')->get();
        
        $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_cualitativa.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_cualitativa.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_cualitativa.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_cualitativa.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion'
                                          )
                                          ->first();
        
        $pdf = PDF::loadView(sistema_view().'/credito/pdfsolicitud_cualitativa',[
          'users_prestamo'    => $users_prestamo,
          'evaluacion_mes'    => $evaluacion_mes,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'credito_evaluacion_cualitativa' => $credito_evaluacion_cualitativa,
          'giro_economico_evaluacion' => $this->giro_economico_evaluacion,
          'tipo_giro_economico' => $this->tipo_giro_economico,
          'f_tiporeferencia' => $this->f_tiporeferencia,
          'ejercicio_giro_economico' => $ejercicio_giro_economico,
        ]); 
        $pdf->setPaper('A4');
        // $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('SOLICITUD DE EVALUACION CUALITATIVA.pdf');
      }
      else if( $request->input('view') == 'solicitud_margen_ventas' ){
        return view(sistema_view().'/credito/solicitud_margen_ventas',[
          'tienda' => $tienda,
          'credito' => $credito
        ]);
      }
      else if( $request->input('view') == 'pdfsolicitud_margen_ventas' ){

        $ejercicio_giro_economico = DB::table('ejercicio_giro_economico')->get();
        
        $credito_cuantitativa_margen_venta = DB::table('credito_cuantitativa_margen_venta')->where('credito_cuantitativa_margen_venta.idcredito',$id)->first();
        $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_cualitativa.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_cualitativa.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_cualitativa.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_cualitativa.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion'
                                          )
                                          ->first();
        $pdf = PDF::loadView(sistema_view().'/credito/pdfsolicitud_margen_ventas',[
          'users_prestamo'    => $users_prestamo,
          'evaluacion_mes'    => $evaluacion_mes,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'credito_cuantitativa_margen_venta' => $credito_cuantitativa_margen_venta,
          'credito_evaluacion_cualitativa' => $credito_evaluacion_cualitativa,
          'giro_economico_evaluacion' => $this->giro_economico_evaluacion,
          'tipo_giro_economico' => $this->tipo_giro_economico,
          'f_tiporeferencia' => $this->f_tiporeferencia,
          'ejercicio_giro_economico' => $ejercicio_giro_economico,
        ]); 
        $pdf->setPaper('A4');
        return $pdf->stream('SOLICITUD MARGEN DE VENTAS.pdf');
      }
      else if( $request->input('view') == 'solicitud_inventario_activos' ){
        return view(sistema_view().'/credito/solicitud_inventario_activos',[
          'tienda' => $tienda,
          'credito' => $credito
        ]);
      }
      else if( $request->input('view') == 'pdfsolicitud_inventario_activos' ){

        $credito_cuantitativa_inventario = DB::table('credito_cuantitativa_inventario')->where('credito_cuantitativa_inventario.idcredito',$id)->first();
        $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_cualitativa.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_cualitativa.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_cualitativa.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_cualitativa.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion'
                                          )
                                          ->first();
        
        $pdf = PDF::loadView(sistema_view().'/credito/pdfsolicitud_inventario_activos',[
          'users_prestamo'    => $users_prestamo,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'unidadmedida_credito' => $this->unidadmedida_credito,
          'credito_evaluacion_cualitativa' => $credito_evaluacion_cualitativa,
          'credito_cuantitativa_inventario' => $credito_cuantitativa_inventario,
        ]); 
        $pdf->setPaper('A4');
        return $pdf->stream('SOLICITUD INVENTARIOS Y ACTIVOS.pdf');
      }
      else if( $request->input('view') == 'solicitud_control_limites' ){
        return view(sistema_view().'/credito/solicitud_control_limites',[
          'tienda' => $tienda,
          'credito' => $credito
        ]);
      }
      else if( $request->input('view') == 'pdfsolicitud_control_limites' ){

        $credito_cuantitativa_control_limites = DB::table('credito_cuantitativa_control_limites')->where('credito_cuantitativa_control_limites.idcredito',$id)->first();
        $credito_cuantitativa_margen_venta = DB::table('credito_cuantitativa_margen_venta')->where('credito_cuantitativa_margen_venta.idcredito',$id)->first();
        $credito_evaluacion_cuantitativa = DB::table('credito_evaluacion_cuantitativa')->where('credito_evaluacion_cuantitativa.idcredito',$id)->first();
        $credito_cuantitativa_deudas = DB::table('credito_cuantitativa_deudas')->where('credito_cuantitativa_deudas.idcredito',$id)->first();
        $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_cualitativa.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_cualitativa.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_cualitativa.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_cualitativa.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion'
                                          )
                                          ->first();
        $credito_garantias_cliente = DB::table('credito_garantia')
                                  ->join('credito','credito.id','credito_garantia.idcredito')
                                  ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                                  ->join('garantias_noprendarias','garantias_noprendarias.id','credito_garantia.idgarantias_noprendarias')
                                  ->join('tipo_garantia_noprendaria','tipo_garantia_noprendaria.id','garantias_noprendarias.idtipo_garantia_noprendaria')
                                  ->where('credito_garantia.idcliente',$credito->idcliente)
                                  ->where('credito_garantia.idgarantias',0)
                                  ->where('credito_garantia.tipo','CLIENTE')
                                  ->where('credito.estado','DESEMBOLSADO')
                                  ->where('credito.idestadocredito',1)
                                  ->select(
                                      'credito_garantia.*',
                                      'credito.idforma_credito as idforma_credito',
                                      'credito_prendatario.modalidad as modalidadproductocredito',
                                      'garantias_noprendarias.descripcion as descripcion_garantia',
                                      'garantias_noprendarias.idtipo_garantia_noprendaria as tipo_garantia_no_prendaria',
                                      'garantias_noprendarias.valor_mercado as valor_mercado_garantia',
                                      'garantias_noprendarias.valor_comercial as valor_comercial_garantia',
                                      'garantias_noprendarias.valor_realizacion as valor_realizacion_garantia',
                                      'tipo_garantia_noprendaria.nombre as nombretipogarantia',
                                  )
                                 ->get();
        $credito_garantias_aval = DB::table('credito_garantia')
                                  ->join('credito','credito.id','credito_garantia.idcredito')
                                  ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                                  ->join('garantias_noprendarias','garantias_noprendarias.id','credito_garantia.idgarantias_noprendarias')
                                  ->join('tipo_garantia_noprendaria','tipo_garantia_noprendaria.id','garantias_noprendarias.idtipo_garantia_noprendaria')
                                  ->where('credito_garantia.idcliente',$credito->idaval)
                                  ->where('credito_garantia.idgarantias',0)
                                  ->where('credito_garantia.tipo','AVAL')
                                  ->where('credito.estado','DESEMBOLSADO')
                                  ->where('credito.idestadocredito',1)
                                  ->select(
                                      'credito_garantia.*',
                                      'garantias_noprendarias.descripcion as descripcion_garantia',
                                      'garantias_noprendarias.idtipo_garantia_noprendaria as tipo_garantia_no_prendaria',
                                      'garantias_noprendarias.valor_mercado as valor_mercado_garantia',
                                      'garantias_noprendarias.valor_comercial as valor_comercial_garantia',
                                      'garantias_noprendarias.valor_realizacion as valor_realizacion_garantia',
                                      'tipo_garantia_noprendaria.nombre as nombretipogarantia',
                                  )
                                 ->get();
        
        /*$credito_aval = DB::table('credito')->where('credito.idcliente',$credito->idaval)->first();
        $credito_garantias_aval_aval = DB::table('credito_garantia')
                                  ->join('credito','credito.id','credito_garantia.idcredito')
                                  ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                                  ->join('garantias_noprendarias','garantias_noprendarias.id','credito_garantia.idgarantias_noprendarias')
                                  ->join('tipo_garantia_noprendaria','tipo_garantia_noprendaria.id','garantias_noprendarias.idtipo_garantia_noprendaria')
                                  ->where('credito_garantia.idcliente',$credito->idaval)
                                  ->where('credito_garantia.idgarantias',0)
                                  ->where('credito_garantia.tipo','AVAL')
                                  ->where('credito.estado','DESEMBOLSADO')
                                  ->where('credito.idestadocredito',1)
                                  ->select(
                                      'credito_garantia.*',
                                      'garantias_noprendarias.descripcion as descripcion_garantia',
                                      'garantias_noprendarias.idtipo_garantia_noprendaria as tipo_garantia_no_prendaria',
                                      'garantias_noprendarias.valor_mercado as valor_mercado_garantia',
                                      'garantias_noprendarias.valor_comercial as valor_comercial_garantia',
                                      'garantias_noprendarias.valor_realizacion as valor_realizacion_garantia',
                                      'tipo_garantia_noprendaria.nombre as nombretipogarantia',
                                  )
                                 ->get();*/
        
       $credito_evaluacion_resumida = DB::table('credito_evaluacion_resumida')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_resumida.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_resumida.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_resumida.*',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion'
                                          )
                                          ->first();
        
        $pdf = PDF::loadView(sistema_view().'/credito/pdfsolicitud_control_limites',[
          'users_prestamo'    => $users_prestamo,
          'users_prestamo_aval'    => $users_prestamo_aval,
          'evaluacion_mes'    => $evaluacion_mes,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'giro_economico_evaluacion' => $this->giro_economico_evaluacion,
          'tipo_giro_economico' => $this->tipo_giro_economico,
          'f_tiporeferencia' => $this->f_tiporeferencia,
          'credito_evaluacion_resumida' => $credito_evaluacion_resumida,
          'credito_cuantitativa_control_limites' => $credito_cuantitativa_control_limites,
          'credito_evaluacion_cualitativa' => $credito_evaluacion_cualitativa,
          'credito_cuantitativa_margen_venta' => $credito_cuantitativa_margen_venta,
          'credito_cuantitativa_deudas' => $credito_cuantitativa_deudas,
          'credito_evaluacion_cuantitativa' => $credito_evaluacion_cuantitativa,
          'credito_garantias_cliente' => $credito_garantias_cliente,
          'credito_garantias_aval' => $credito_garantias_aval,
        ]); 
        $pdf->setPaper('A4');
        return $pdf->stream('SOLICITUD CONTROL Y LIMITES.pdf');
      }
      else if( $request->input('view') == 'solicitud_deudas' ){
        return view(sistema_view().'/credito/solicitud_deudas',[
          'tienda' => $tienda,
          'credito' => $credito
        ]);
      }
      else if( $request->input('view') == 'pdfsolicitud_deudas' ){
        $credito_cuantitativa_deudas = DB::table('credito_cuantitativa_deudas')->where('credito_cuantitativa_deudas.idcredito',$id)->first();
        $credito_cuantitativa_margen_venta = DB::table('credito_cuantitativa_margen_venta')->where('credito_cuantitativa_margen_venta.idcredito',$id)->first();
        $credito_evaluacion_cuantitativa = DB::table('credito_evaluacion_cuantitativa')->where('credito_evaluacion_cuantitativa.idcredito',$id)->first();
        $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_cualitativa.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_cualitativa.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_cualitativa.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_cualitativa.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion'
                                          )
                                          ->first();
        $tipocredito = $credito->idforma_credito== 1 ? 'PRENDARIA' : 'NOPRENDARIA';
        $diasdegracia = DB::table('diasdegracia')->where('diasdegracia.nombre',$tipocredito)->first();
        $pdf = PDF::loadView(sistema_view().'/credito/pdfsolicitud_deudas',[
          'diasdegracia' => $diasdegracia->dias,
          'users_prestamo'    => $users_prestamo,
          'evaluacion_mes'    => $evaluacion_mes,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'forma_pago_credito' => $this->forma_pago_credito,
          'giro_economico_evaluacion' => $this->giro_economico_evaluacion,
          'tipo_giro_economico' => $this->tipo_giro_economico,
          'tipo_credito_evaluacion' => $this->tipo_credito_evaluacion,
          'f_tiporeferencia' => $this->f_tiporeferencia,
          'credito_cuantitativa_deudas' => $credito_cuantitativa_deudas,
          'credito_evaluacion_cuantitativa' => $credito_evaluacion_cuantitativa,
          'credito_evaluacion_cualitativa' => $credito_evaluacion_cualitativa,
          'credito_cuantitativa_margen_venta' => $credito_cuantitativa_margen_venta,
        ]); 
        $pdf->setPaper('A4');
        return $pdf->stream('SOLICITUD DEUDAS.pdf');
      }
      else if( $request->input('view') == 'solicitud_cuantitativa' ){
        return view(sistema_view().'/credito/solicitud_cuantitativa',[
          'tienda' => $tienda,
          'credito' => $credito
        ]);
      }
      else if( $request->input('view') == 'pdfsolicitud_cuantitativa' ){
        $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_cualitativa.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_cualitativa.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_cualitativa.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_cualitativa.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion'
                                          )
                                          ->first();
        $credito_cuantitativa_margen_venta = DB::table('credito_cuantitativa_margen_venta')->where('credito_cuantitativa_margen_venta.idcredito',$id)->first();
        $credito_evaluacion_cuantitativa = DB::table('credito_evaluacion_cuantitativa')->where('credito_evaluacion_cuantitativa.idcredito',$id)->first();
        $credito_cuantitativa_inventario = DB::table('credito_cuantitativa_inventario')->where('credito_cuantitativa_inventario.idcredito',$id)->first();
        $credito_cuantitativa_deudas = DB::table('credito_cuantitativa_deudas')->where('credito_cuantitativa_deudas.idcredito',$id)->first();
        $credito_cuantitativa_ingreso_adicional = DB::table('credito_cuantitativa_ingreso_adicional')->where('credito_cuantitativa_ingreso_adicional.idcredito',$id)->first();
        
        // Evaluación Anterior
        
        $credito_anterior = DB::table('credito')
            //->where('credito.idestadocredito',1)
            ->where('credito.idcliente',$credito->idcliente)
            ->where('credito.estado','DESEMBOLSADO')
            ->whereIn('credito.idestadocredito',[1,2])
            ->orderBy('credito.idestadocredito','asc')
            ->orderBy('credito.fecha_desembolso','desc')
            ->limit(1)
            ->first();
        
        $idcredito_anterior = 0;
        if($credito_anterior){
            $idcredito_anterior = $credito_anterior->id;
        }
        
        $credito_evaluacion_cualitativa_anterior = DB::table('credito_evaluacion_cualitativa')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_cualitativa.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_cualitativa.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_cualitativa.idcredito',$idcredito_anterior)
                                          ->select(
                                            'credito_evaluacion_cualitativa.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion',
                                            'giro_economico_evaluacion.porcentaje as margen_giro_economico',
                                          )
                                          ->first();
        
        $credito_cuantitativa_margen_venta_anterior = DB::table('credito_cuantitativa_margen_venta')
            ->where('credito_cuantitativa_margen_venta.idcredito',$idcredito_anterior)
            ->first();
        
        
        $credito_evaluacion_cuantitativa_anterior = DB::table('credito_evaluacion_cuantitativa')
            ->where('credito_evaluacion_cuantitativa.idcredito',$idcredito_anterior)
            ->first();
        
        $credito_cuantitativa_inventario_anterior = DB::table('credito_cuantitativa_inventario')
            ->where('credito_cuantitativa_inventario.idcredito',$idcredito_anterior)
            ->first();
        
        $credito_cuantitativa_deudas_anterior = DB::table('credito_cuantitativa_deudas')
            ->where('credito_cuantitativa_deudas.idcredito',$idcredito_anterior)
            ->first();
        
        $credito_cuantitativa_ingreso_adicional_anterior = DB::table('credito_cuantitativa_ingreso_adicional')
            ->where('credito_cuantitativa_ingreso_adicional.idcredito',$idcredito_anterior)
            ->first();
        
        $pdf = PDF::loadView(sistema_view().'/credito/pdfsolicitud_cuantitativa',[
          'users_prestamo'    => $users_prestamo,
          'evaluacion_mes'    => $evaluacion_mes,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'credito_evaluacion_cualitativa' => $credito_evaluacion_cualitativa,
          'credito_evaluacion_cuantitativa' => $credito_evaluacion_cuantitativa,
          'giro_economico_evaluacion' => $this->giro_economico_evaluacion,
          'tipo_giro_economico' => $this->tipo_giro_economico,
          'f_tiporeferencia' => $this->f_tiporeferencia,
          'credito_cuantitativa_margen_venta' => $credito_cuantitativa_margen_venta,
          'credito_cuantitativa_inventario' => $credito_cuantitativa_inventario,
          'credito_cuantitativa_deudas' => $credito_cuantitativa_deudas,
          'credito_cuantitativa_ingreso_adicional' => $credito_cuantitativa_ingreso_adicional,
          'credito_evaluacion_cualitativa_anterior' => $credito_evaluacion_cualitativa_anterior,
          'credito_evaluacion_cuantitativa_anterior' => $credito_evaluacion_cuantitativa_anterior,
          'credito_cuantitativa_margen_venta_anterior' => $credito_cuantitativa_margen_venta_anterior,
          'credito_cuantitativa_inventario_anterior' => $credito_cuantitativa_inventario_anterior,
          'credito_cuantitativa_deudas_anterior' => $credito_cuantitativa_deudas_anterior,
          'credito_cuantitativa_ingreso_adicional_anterior' => $credito_cuantitativa_ingreso_adicional_anterior,
        ]); 
        $pdf->setPaper('A4');
        return $pdf->stream('SOLICITUD CUANTITATIVA.pdf');
      }
      else if( $request->input('view') == 'solicitud_ingresoadicional' ){
        return view(sistema_view().'/credito/solicitud_ingresoadicional',[
          'tienda' => $tienda,
          'credito' => $credito
        ]);
      }
      else if( $request->input('view') == 'pdfsolicitud_ingresoadicional' ){
        
        $credito_cuantitativa_deudas = DB::table('credito_cuantitativa_deudas')->where('credito_cuantitativa_deudas.idcredito',$id)->first();
        $credito_cuantitativa_margen_venta = DB::table('credito_cuantitativa_margen_venta')->where('credito_cuantitativa_margen_venta.idcredito',$id)->first();
        $credito_cuantitativa_ingreso_adicional = DB::table('credito_cuantitativa_ingreso_adicional')
                                                    ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_cuantitativa_ingreso_adicional.idgiro_economico_evaluacion_adicional')
                                                    ->where('credito_cuantitativa_ingreso_adicional.idcredito',$id)
                                                    ->select(
                                                      'credito_cuantitativa_ingreso_adicional.*',
                                                      'giro_economico_evaluacion.nombre as nombreingresoadicional'
                                                    )
                                                    ->first();
        $credito_evaluacion_cuantitativa = DB::table('credito_evaluacion_cuantitativa')->where('credito_evaluacion_cuantitativa.idcredito',$id)->first();
        $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_cualitativa.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_cualitativa.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_cualitativa.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_cualitativa.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion'
                                          )
                                          ->first();
        $tipocredito = $credito->idforma_credito== 1 ? 'PRENDARIA' : 'NOPRENDARIA';
        $diasdegracia = DB::table('diasdegracia')->where('diasdegracia.nombre',$tipocredito)->first();
        $pdf = PDF::loadView(sistema_view().'/credito/pdfsolicitud_ingresoadicional',[
          'diasdegracia' => $diasdegracia->dias,
          'users_prestamo'    => $users_prestamo,
          'evaluacion_mes'    => $evaluacion_mes,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'forma_pago_credito' => $this->forma_pago_credito,
          'giro_economico_evaluacion' => $this->giro_economico_evaluacion,
          'tipo_giro_economico' => $this->tipo_giro_economico,
          'tipo_credito_evaluacion' => $this->tipo_credito_evaluacion,
          'f_tiporeferencia' => $this->f_tiporeferencia,
          'unidadmedida_credito' => $this->unidadmedida_credito,
          'credito_cuantitativa_deudas' => $credito_cuantitativa_deudas,
          'credito_evaluacion_cuantitativa' => $credito_evaluacion_cuantitativa,
          'credito_evaluacion_cualitativa' => $credito_evaluacion_cualitativa,
          'credito_cuantitativa_margen_venta' => $credito_cuantitativa_margen_venta,
          'credito_cuantitativa_ingreso_adicional' => $credito_cuantitativa_ingreso_adicional,
        ]); 
        $pdf->setPaper('A4');
        return $pdf->stream('ING ADIC-MES Y FIJOS.pdf');
      }
      
      else if( $request->input('view') == 'evaluacion_resumida' ){
        $ejercicio_giro_economico = DB::table('ejercicio_giro_economico')->get();
        $credito_cuantitativa_control_limites = DB::table('credito_cuantitativa_control_limites')->where('credito_cuantitativa_control_limites.idcredito',$id)->first();
        
        $credito_evaluacion_resumida = DB::table('credito_evaluacion_resumida')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_resumida.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_resumida.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_resumida.*',
                                            'giro_economico_evaluacion.porcentaje as margen_venta'
                                          )
                                          ->first();
        $tipocredito = $credito->idforma_credito== 1 ? 'PRENDARIA' : 'NOPRENDARIA';

        $diasdegracia = DB::table('diasdegracia')->where('diasdegracia.nombre',$tipocredito)->first();
        return view(sistema_view().'/credito/evaluacion_resumida',[
          'diasdegracia' => $diasdegracia->dias,
          'users_prestamo'    => $users_prestamo,
          'users_prestamo_aval'    => $users_prestamo_aval,
          'evaluacion_mes'    => $evaluacion_mes,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'credito_cuantitativa_control_limites' => $credito_cuantitativa_control_limites,
          'credito_evaluacion_resumida' => $credito_evaluacion_resumida,
          'forma_pago_credito' => $this->forma_pago_credito,
          'giro_economico_evaluacion' => $this->giro_economico_evaluacion,
          'tipo_giro_economico' => $this->tipo_giro_economico,
          'f_tiporeferencia' => $this->f_tiporeferencia,
          'ejercicio_giro_economico' => $ejercicio_giro_economico,
          'view_detalle' => $request->detalle
        ]);
      }
      else if( $request->input('view') == 'solicitud_resumida' ){
        return view(sistema_view().'/credito/solicitud_resumida',[
          'tienda' => $tienda,
          'credito' => $credito
        ]);
      }
      else if( $request->input('view') == 'pdfsolicitud_resumida' ){
        $ejercicio_giro_economico = DB::table('ejercicio_giro_economico')->get();
        
        $credito_cuantitativa_control_limites = DB::table('credito_cuantitativa_control_limites')->where('credito_cuantitativa_control_limites.idcredito',$id)->first();
        
        $credito_evaluacion_resumida = DB::table('credito_evaluacion_resumida')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_resumida.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_resumida.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_resumida.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_resumida.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion'
                                          )
                                          ->first();
        
        $tipocredito = $credito->idforma_credito== 1 ? 'PRENDARIA' : 'NOPRENDARIA';
        $diasdegracia = DB::table('diasdegracia')->where('diasdegracia.nombre',$tipocredito)->first();
        $pdf = PDF::loadView(sistema_view().'/credito/pdfsolicitud_resumida',[
          'diasdegracia' => $diasdegracia->dias,
          'users_prestamo'    => $users_prestamo,
          'evaluacion_mes'    => $evaluacion_mes,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'credito_cuantitativa_control_limites' => $credito_cuantitativa_control_limites,
          'credito_evaluacion_resumida' => $credito_evaluacion_resumida,
          'forma_pago_credito' => $this->forma_pago_credito,
          'giro_economico_evaluacion' => $this->giro_economico_evaluacion,
          'tipo_giro_economico' => $this->tipo_giro_economico,
          'f_tiporeferencia' => $this->f_tiporeferencia,
          'ejercicio_giro_economico' => $ejercicio_giro_economico,
          'users_prestamo_aval'    => $users_prestamo_aval,
        ]); 
        $pdf->setPaper('A4');
        return $pdf->stream('EVALUACION RESUMIDA.pdf');
      }
      else if( $request->input('view') == 'solicitud_checklist' ){
        return view(sistema_view().'/credito/solicitud_checklist',[
          'tienda' => $tienda,
          'credito' => $credito
        ]);
      }
      else if( $request->input('view') == 'pdfsolicitud_checklist' ){
        $ejercicio_giro_economico = DB::table('ejercicio_giro_economico')->get();
        
        $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_cualitativa.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_cualitativa.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_cualitativa.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_cualitativa.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion'
                                          )
                                          ->first();
        $pdf = PDF::loadView(sistema_view().'/credito/pdfsolicitud_checklist',[
          'users_prestamo'    => $users_prestamo,
          'evaluacion_mes'    => $evaluacion_mes,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'credito_evaluacion_cualitativa' => $credito_evaluacion_cualitativa,
          'giro_economico_evaluacion' => $this->giro_economico_evaluacion,
          'tipo_giro_economico' => $this->tipo_giro_economico,
          'f_tiporeferencia' => $this->f_tiporeferencia,
          'ejercicio_giro_economico' => $ejercicio_giro_economico,
        ]); 
        $pdf->setPaper('A4');
        return $pdf->stream('CHECK LIST.pdf');
      }
      else if( $request->input('view') == 'flujo_caja' ){
        $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_cualitativa.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_cualitativa.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_cualitativa.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_cualitativa.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion',
                                            'giro_economico_evaluacion.porcentaje as margen_giro_economico',
                                          )
                                          ->first();
        $credito_evaluacion_cuantitativa = DB::table('credito_evaluacion_cuantitativa')->where('credito_evaluacion_cuantitativa.idcredito',$id)->first();
        $credito_cuantitativa_ingreso_adicional = DB::table('credito_cuantitativa_ingreso_adicional')->where('credito_cuantitativa_ingreso_adicional.idcredito',$id)->first();
        $credito_cuantitativa_deudas = DB::table('credito_cuantitativa_deudas')->where('credito_cuantitativa_deudas.idcredito',$id)->first();
        $credito_flujo_caja = DB::table('credito_flujo_caja')->where('credito_flujo_caja.idcredito',$id)->first();
        
        
        return view(sistema_view().'/credito/flujo_caja',[
          'credito_evaluacion_cualitativa' => $credito_evaluacion_cualitativa,
          'credito_evaluacion_cuantitativa' => $credito_evaluacion_cuantitativa,
          'credito_cuantitativa_ingreso_adicional' => $credito_cuantitativa_ingreso_adicional,
          'credito_cuantitativa_deudas' => $credito_cuantitativa_deudas,
          'credito_flujo_caja' => $credito_flujo_caja,
          'users_prestamo'    => $users_prestamo,
          'users_prestamo_aval'    => $users_prestamo_aval,
          'evaluacion_mes'    => $evaluacion_mes,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'forma_pago_credito' => $this->forma_pago_credito,
          'giro_economico_evaluacion' => $this->giro_economico_evaluacion,
          'tipo_giro_economico' => $this->tipo_giro_economico,
          'f_tiporeferencia' => $this->f_tiporeferencia,
          'view_detalle' => $request->detalle
        ]);
      }
      else if( $request->input('view') == 'solicitud_flujocaja' ){
        return view(sistema_view().'/credito/solicitud_flujocaja',[
          'tienda' => $tienda,
          'credito' => $credito
        ]);
      }
      else if( $request->input('view') == 'pdfsolicitud_flujocaja' ){
        $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_cualitativa.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_cualitativa.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_cualitativa.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_cualitativa.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion',
                                            'giro_economico_evaluacion.porcentaje as margen_giro_economico',
                                          )
                                          ->first();
        $credito_evaluacion_cuantitativa = DB::table('credito_evaluacion_cuantitativa')->where('credito_evaluacion_cuantitativa.idcredito',$id)->first();
        $credito_cuantitativa_ingreso_adicional = DB::table('credito_cuantitativa_ingreso_adicional')->where('credito_cuantitativa_ingreso_adicional.idcredito',$id)->first();
        $credito_cuantitativa_deudas = DB::table('credito_cuantitativa_deudas')->where('credito_cuantitativa_deudas.idcredito',$id)->first();
        $credito_flujo_caja = DB::table('credito_flujo_caja')->where('credito_flujo_caja.idcredito',$id)->first();
       
        $pdf = PDF::loadView(sistema_view().'/credito/pdfsolicitud_flujocaja',[
          'credito_evaluacion_cualitativa' => $credito_evaluacion_cualitativa,
          'credito_evaluacion_cuantitativa' => $credito_evaluacion_cuantitativa,
          'credito_cuantitativa_ingreso_adicional' => $credito_cuantitativa_ingreso_adicional,
          'credito_cuantitativa_deudas' => $credito_cuantitativa_deudas,
          'credito_flujo_caja' => $credito_flujo_caja,
          'users_prestamo'    => $users_prestamo,
          'users_prestamo_aval'    => $users_prestamo_aval,
          'evaluacion_mes'    => $evaluacion_mes,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'forma_pago_credito' => $this->forma_pago_credito,
          'giro_economico_evaluacion' => $this->giro_economico_evaluacion,
          'tipo_giro_economico' => $this->tipo_giro_economico,
          'f_tiporeferencia' => $this->f_tiporeferencia,
        ]); 
        $pdf->setPaper('A4');
        //$pdf->setPaper('A4', 'landscape');
        return $pdf->stream('SOLICITUD DE CREDITO.pdf');
      }
      else if( $request->input('view') == 'formato_evaluacion' ){
        $ejercicio_giro_economico = DB::table('ejercicio_giro_economico')->get();
        
        $credito_formato_evaluacion = DB::table('credito_formato_evaluacion')->where('credito_formato_evaluacion.idcredito',$id)->first();
        
        $tipocredito = $credito->idforma_credito== 1 ? 'PRENDARIA' : 'NOPRENDARIA';

        $diasdegracia = DB::table('diasdegracia')->where('diasdegracia.nombre',$tipocredito)->first();
        
        return view(sistema_view().'/credito/formato_evaluacion',[
          'diasdegracia' => $diasdegracia->dias,
          'users_prestamo'    => $users_prestamo,
          'evaluacion_mes'    => $evaluacion_mes,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'forma_pago_credito' => $this->forma_pago_credito,
          'credito_formato_evaluacion' => $credito_formato_evaluacion,
          'giro_economico_evaluacion' => $this->giro_economico_evaluacion,
          'tipo_giro_economico' => $this->tipo_giro_economico,
          'f_tiporeferencia' => $this->f_tiporeferencia,
          'ejercicio_giro_economico' => $ejercicio_giro_economico,
          'view_detalle' => $request->detalle
        ]);
      }
      else if( $request->input('view') == 'solicitud_formato_evaluacion' ){
        return view(sistema_view().'/credito/solicitud_formato_evaluacion',[
          'tienda' => $tienda,
          'credito' => $credito
        ]);
      }
      else if( $request->input('view') == 'pdfsolicitud_formato_evaluacion' ){
        $ejercicio_giro_economico = DB::table('ejercicio_giro_economico')->get();
        
        $credito_formato_evaluacion = DB::table('credito_formato_evaluacion')->where('credito_formato_evaluacion.idcredito',$id)->first();
        
        $tipocredito = $credito->idforma_credito== 1 ? 'PRENDARIA' : 'NOPRENDARIA';

        $diasdegracia = DB::table('diasdegracia')->where('diasdegracia.nombre',$tipocredito)->first();
       
        $pdf = PDF::loadView(sistema_view().'/credito/pdfsolicitud_formato_evaluacion',[
          'diasdegracia' => $diasdegracia->dias,
          'users_prestamo'    => $users_prestamo,
          'evaluacion_mes'    => $evaluacion_mes,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'forma_pago_credito' => $this->forma_pago_credito,
          'credito_formato_evaluacion' => $credito_formato_evaluacion,
          'giro_economico_evaluacion' => $this->giro_economico_evaluacion,
          'tipo_giro_economico' => $this->tipo_giro_economico,
          'f_tiporeferencia' => $this->f_tiporeferencia,
          'ejercicio_giro_economico' => $ejercicio_giro_economico,
        ]); 
        $pdf->setPaper('A4');
        return $pdf->stream('FORMATO DE EVALUACION.pdf');
      }
      else if( $request->input('view') == 'propuesta_credito' ){
        
        $credito_propuesta = DB::table('credito_propuesta')->where('credito_propuesta.idcredito',$id)->first();
        $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_cualitativa.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_cualitativa.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_cualitativa.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_cualitativa.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion',
                                            'giro_economico_evaluacion.porcentaje as margen_giro_economico',
                                          )
                                          ->first();
        $credito_cuantitativa_deudas = DB::table('credito_cuantitativa_deudas')
                                        ->join('forma_pago_credito','forma_pago_credito.id','credito_cuantitativa_deudas.idforma_pago_credito')
                                        ->where('credito_cuantitativa_deudas.idcredito',$id)
                                        ->select(
                                          'credito_cuantitativa_deudas.*',
                                          'forma_pago_credito.nombre as nombre_forma_pago_credito',
                                        )
                                        ->first();
        $credito_evaluacion_cuantitativa = DB::table('credito_evaluacion_cuantitativa')->where('credito_evaluacion_cuantitativa.idcredito',$id)->first();
        $credito_cuantitativa_control_limites = DB::table('credito_cuantitativa_control_limites')->where('credito_cuantitativa_control_limites.idcredito',$id)->first();
        $credito_evaluacion_resumida = DB::table('credito_evaluacion_resumida')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_resumida.idtipo_giro_economico')
                                          ->join('forma_pago_credito','forma_pago_credito.id','credito_evaluacion_resumida.idforma_pago_credito')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_resumida.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_resumida.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_resumida.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion',
                                            'giro_economico_evaluacion.porcentaje as margen_venta',
                                            'forma_pago_credito.nombre as nombre_forma_pago_credito',
                                          )
                                          ->first();
        
        $credito_garantias_cliente = DB::table('credito_garantia')
                                      ->where('credito_garantia.idcredito',$id)
                                      ->where('credito_garantia.idgarantias',0)
                                      ->where('credito_garantia.tipo','CLIENTE')
                                      ->select(
                                          'credito_garantia.*',
                                      )
                                     ->get();
        $credito_garantias_aval = DB::table('credito_garantia')
                                  ->where('credito_garantia.idcredito',$id)
                                  ->where('credito_garantia.idgarantias',0)
                                  ->where('credito_garantia.tipo','AVAL')
                                  ->select(
                                      'credito_garantia.*',
                                  )
                                 ->get();
        
        
        $tipocredito = $credito->idforma_credito== 1 ? 'PRENDARIA' : 'NOPRENDARIA';

        $diasdegracia = DB::table('diasdegracia')->where('diasdegracia.nombre',$tipocredito)->first();
        
        $credito_formato_evaluacion = DB::table('credito_formato_evaluacion')->where('credito_formato_evaluacion.idcredito',$id)->first();
        
        return view(sistema_view().'/credito/propuesta_credito',[
          'credito_propuesta' => $credito_propuesta,
          'credito_evaluacion_cualitativa' => $credito_evaluacion_cualitativa,
          'credito_cuantitativa_deudas' => $credito_cuantitativa_deudas,
          'credito_evaluacion_cuantitativa' => $credito_evaluacion_cuantitativa,
          'credito_cuantitativa_control_limites' => $credito_cuantitativa_control_limites,
          'credito_evaluacion_resumida' => $credito_evaluacion_resumida,
          'credito_garantias_cliente' => $credito_garantias_cliente,
          'credito_garantias_aval' => $credito_garantias_aval,
          'credito_formato_evaluacion' => $credito_formato_evaluacion,
          'diasdegracia' => $diasdegracia->dias,
          'users_prestamo'    => $users_prestamo,
          'users_prestamo_aval'    => $users_prestamo_aval,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'forma_pago_credito' => $this->forma_pago_credito,
          'giro_economico_evaluacion' => $this->giro_economico_evaluacion,
          'tipo_giro_economico' => $this->tipo_giro_economico,
          'f_tiporeferencia' => $this->f_tiporeferencia,
          'fenomenos' => $this->fenomenos,
          'calificacion_cliente' => $this->calificacion_cliente,
          'view_detalle' => $request->detalle,
        ]);
      }
      else if( $request->input('view') == 'aprobar_propuesta' ){
        $credito_evaluacion_resumida = DB::table('credito_evaluacion_resumida')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_resumida.idtipo_giro_economico')
                                          ->join('forma_pago_credito','forma_pago_credito.id','credito_evaluacion_resumida.idforma_pago_credito')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_resumida.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_resumida.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_resumida.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion',
                                            'giro_economico_evaluacion.porcentaje as margen_venta',
                                            'forma_pago_credito.nombre as nombre_forma_pago_credito',
                                          )
                                          ->first();
        
        //$credito_cronograma = DB::table('credito_cronograma')->where('idcredito',$id)->count();
        $credito_evaluacion_cuantitativa = DB::table('credito_evaluacion_cuantitativa')->where('credito_evaluacion_cuantitativa.idcredito',$id)->first();  
        $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_cualitativa.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_cualitativa.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_cualitativa.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_cualitativa.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion'
                                          )
                                          ->first();
        
        $credito_formato_evaluacion = DB::table('credito_formato_evaluacion')->where('credito_formato_evaluacion.idcredito',$id)->first();
        
        return view(sistema_view().'/credito/aprobar_propuesta',[
          'users_prestamo'    => $users_prestamo,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'unidadmedida_credito' => $this->unidadmedida_credito,
          'credito_evaluacion_cualitativa' => $credito_evaluacion_cualitativa,
          'credito_evaluacion_cuantitativa' => $credito_evaluacion_cuantitativa,
          'credito_evaluacion_resumida' => $credito_evaluacion_resumida,
          'credito_formato_evaluacion' => $credito_formato_evaluacion,
          //'credito_cronograma' => $credito_cronograma,
        ]);
      }
      else if( $request->input('view') == 'solicitudpropuesta_credito' ){
        return view(sistema_view().'/credito/solicitudpropuesta_credito',[
          'tienda' => $tienda,
          'credito' => $credito
        ]);
      }
      else if( $request->input('view') == 'pdfsolicitudpropuesta_credito'){
        
        $credito_propuesta = DB::table('credito_propuesta')->where('credito_propuesta.idcredito',$id)->first();
        $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_cualitativa.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_cualitativa.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_cualitativa.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_cualitativa.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion',
                                            'giro_economico_evaluacion.porcentaje as margen_giro_economico',
                                          )
                                          ->first();
        $credito_cuantitativa_deudas = DB::table('credito_cuantitativa_deudas')
                                        ->join('forma_pago_credito','forma_pago_credito.id','credito_cuantitativa_deudas.idforma_pago_credito')
                                        ->where('credito_cuantitativa_deudas.idcredito',$id)
                                        ->select(
                                          'credito_cuantitativa_deudas.*',
                                          'forma_pago_credito.nombre as nombre_forma_pago_credito',
                                        )
                                        ->first();
        $credito_evaluacion_cuantitativa = DB::table('credito_evaluacion_cuantitativa')->where('credito_evaluacion_cuantitativa.idcredito',$id)->first();
        $credito_cuantitativa_control_limites = DB::table('credito_cuantitativa_control_limites')->where('credito_cuantitativa_control_limites.idcredito',$id)->first();
        $credito_evaluacion_resumida = DB::table('credito_evaluacion_resumida')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_resumida.idtipo_giro_economico')
                                          ->join('forma_pago_credito','forma_pago_credito.id','credito_evaluacion_resumida.idforma_pago_credito')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_resumida.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_resumida.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_resumida.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.porcentaje as margen_venta',
                                            'forma_pago_credito.nombre as nombre_forma_pago_credito',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion',
                                          )
                                          ->first();
        
        $credito_garantias_cliente = DB::table('credito_garantia')
                                      
                                      ->join('garantias_noprendarias','garantias_noprendarias.id','credito_garantia.idgarantias_noprendarias')
                                      ->join('tipo_garantia_noprendaria','tipo_garantia_noprendaria.id','garantias_noprendarias.idtipo_garantia_noprendaria')
                                      ->where('credito_garantia.idcredito',$id)
                                      ->where('credito_garantia.idgarantias',0)
                                      ->where('credito_garantia.tipo','CLIENTE')
                                      ->select(
                                          'credito_garantia.*',
                                          'garantias_noprendarias.descripcion as descripcion_garantia',
                                          'garantias_noprendarias.idtipo_garantia_noprendaria as tipo_garantia_no_prendaria',
                                          'garantias_noprendarias.valor_mercado as valor_mercado_garantia',
                                          'garantias_noprendarias.valor_comercial as valor_comercial_garantia',
                                          'garantias_noprendarias.valor_realizacion as valor_realizacion_garantia',
                                          'tipo_garantia_noprendaria.nombre as nombretipogarantia',
                                      )
                                     ->get();
        $credito_garantias_aval = DB::table('credito_garantia')
                                  ->join('garantias_noprendarias','garantias_noprendarias.id','credito_garantia.idgarantias_noprendarias')
                                  ->join('tipo_garantia_noprendaria','tipo_garantia_noprendaria.id','garantias_noprendarias.idtipo_garantia_noprendaria')
                                  ->where('credito_garantia.idcredito',$id)
                                  ->where('credito_garantia.idgarantias',0)
                                  ->where('credito_garantia.tipo','AVAL')
                                  ->select(
                                      'credito_garantia.*',
                                      'garantias_noprendarias.descripcion as descripcion_garantia',
                                      'garantias_noprendarias.idtipo_garantia_noprendaria as tipo_garantia_no_prendaria',
                                      'garantias_noprendarias.valor_mercado as valor_mercado_garantia',
                                      'garantias_noprendarias.valor_comercial as valor_comercial_garantia',
                                      'garantias_noprendarias.valor_realizacion as valor_realizacion_garantia',
                                      'tipo_garantia_noprendaria.nombre as nombretipogarantia',
                                  )
                                 ->get();
        
        $tipocredito = $credito->idforma_credito== 1 ? 'PRENDARIA' : 'NOPRENDARIA';

        $diasdegracia = DB::table('diasdegracia')->where('diasdegracia.nombre',$tipocredito)->first();
        
        $credito_formato_evaluacion = DB::table('credito_formato_evaluacion')->where('credito_formato_evaluacion.idcredito',$id)->first();
        
        $pdf = PDF::loadView(sistema_view().'/credito/pdfsolicitudpropuesta_credito',[
          'credito_propuesta' => $credito_propuesta,
          'credito_evaluacion_cualitativa' => $credito_evaluacion_cualitativa,
          'credito_cuantitativa_deudas' => $credito_cuantitativa_deudas,
          'credito_evaluacion_cuantitativa' => $credito_evaluacion_cuantitativa,
          'credito_cuantitativa_control_limites' => $credito_cuantitativa_control_limites,
          'credito_evaluacion_resumida' => $credito_evaluacion_resumida,
          'credito_garantias_cliente' => $credito_garantias_cliente,
          'credito_garantias_aval' => $credito_garantias_aval,
          'credito_formato_evaluacion' => $credito_formato_evaluacion,
          'diasdegracia' => $diasdegracia->dias,
          'users_prestamo'    => $users_prestamo,
          'users_prestamo_aval'    => $users_prestamo_aval,
          'tienda' => $tienda,
          'usuario' => $usuario,
          'credito' => $credito,
          'forma_pago_credito' => $this->forma_pago_credito,
          'giro_economico_evaluacion' => $this->giro_economico_evaluacion,
          'tipo_giro_economico' => $this->tipo_giro_economico,
          'f_tiporeferencia' => $this->f_tiporeferencia,
          'fenomenos' => $this->fenomenos,
          'calificacion_cliente' => $this->calificacion_cliente,
          'tipo' => $request->tipo,
        ]); 
        
        $pdf->setPaper('A4');
        
        return $pdf->stream('PROPUESTA DE CREDITO.pdf');
      }
      
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        if($request->input('view') == 'editar') {
  
            $rules = [
                'idcliente' => 'required',                  
                'idcredito_prendatario' => 'required',                 
                'idtipo_operacion_credito' => 'required',                
                'idforma_credito' => 'required',                   
                'idtipo_destino_credito' => 'required',               
                'idmodalidad_credito' => 'required',                 
            ];
          
            $messages = [
                'idcliente.required' => 'El Campo es Obligatorio.',
                'idcredito_prendatario.required' => 'El Campo es Obligatorio.',
                'idforma_credito.required' => 'El Campo es Obligatorio.',
                'idtipo_operacion_credito.required' => 'El Campo es Obligatorio.',
                'idtipo_destino_credito.required' => 'El Campo es Obligatorio.',
                'idmodalidad_credito.required' => 'El Campo es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
              
              
            // ---- SELECCIONAR LOS DATOS FILTRADOS
            $cliente = DB::table('users')->whereId($request->input('idcliente'))->first();
            $clienteidentificacion = '';
            $clientenombrecompleto = '';
            if($cliente!=''){
                $clienteidentificacion = $cliente->identificacion;
                $clientenombrecompleto = $cliente->nombrecompleto;
            }
            $aval = DB::table('users')->whereId($request->input('idaval'))->first();
            $avalidentificacion = '';
            $avalnombrecompleto = '';
            if($aval!=''){
                $avalidentificacion = $aval->identificacion;
                $avalnombrecompleto = $aval->nombrecompleto;
            }
            $asesor = DB::table('users')->whereId(Auth::user()->id)->first();
            $asesoridentificacion = '';
            $asesornombrecompleto = '';
            if($asesor!=''){
                $asesoridentificacion = $asesor->identificacion;
                $asesornombrecompleto = $asesor->nombrecompleto;
            }
          
            $credito_prendatario = DB::table('credito_prendatario')->whereId($request->input('idcredito_prendatario'))->first();
            $tipo_operacion_credito = DB::table('tipo_operacion_credito')->whereId($request->input('idtipo_operacion_credito'))->first();
            $forma_credito = DB::table('forma_credito')->whereId($request->input('idforma_credito'))->first();
            $tipo_destino_credito = DB::table('tipo_destino_credito')->whereId($request->input('idtipo_destino_credito'))->first();
            $modalidad_credito = DB::table('modalidad_credito')->whereId($request->input('idmodalidad_credito'))->first();
            
            // ---- FIN SELECCIONAR LOS DATOS FILTRADOS
          
            DB::table('credito')->whereId($id)->update([
              
              'clienteidentificacion'     => $clienteidentificacion,
              'clientenombrecompleto'     => $clientenombrecompleto,
              'avalidentificacion'        => $avalidentificacion,
              'avalnombrecompleto'        => $avalnombrecompleto,
              'credito_prendatario'       => $credito_prendatario->nombre,
              'tipo_operacion_credito'    => $tipo_operacion_credito->nombre,
              'forma_credito'             => $forma_credito->nombre,
              'tipo_destino_credito'      => $tipo_destino_credito->nombre,
              'modalidad_credito'         => $modalidad_credito->nombre,
              'asesoridentificacion'      => $asesoridentificacion,
              'asesornombrecompleto'      => $asesornombrecompleto,
              'participarconyugue_titular'=> $request->participarconyugue_titular,
              'participarconyugue_aval'   => $request->participarconyugue_aval,
              
              'idcliente'                 => $request->input('idcliente'),
              'idaval'                    => $request->input('idaval')!='null'?$request->input('idaval'):0,
              'idcredito_prendatario'     => $request->input('idcredito_prendatario'),
              'idtipo_operacion_credito'  => $request->input('idtipo_operacion_credito'),
              'idforma_credito'           => $request->input('idforma_credito'),
              'idmodalidad_credito'       => $request->input('idmodalidad_credito'),
              'idtipo_destino_credito'    => $request->input('idtipo_destino_credito'),
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'credito_garantia') {
  
            $rules = [
                'monto_solicitado' => 'required',                
                'idforma_pago_credito' => 'required',                 
                'cuotas' => 'required',                
                'tasa_tem' => 'required',                   
                'dia_gracia' => 'required',               
                'fecha_desembolso' => 'required',                   
            ];
          
            $messages = [
                'monto_solicitado.required' => 'El Campo es Obligatorio.',
                'idforma_pago_credito.required' => 'El Campo es Obligatorio.',
                'cuotas.required' => 'El Campo es Obligatorio.',
                'tasa_tem.required' => 'El Campo es Obligatorio.',
                'dia_gracia.required' => 'El Campo es Obligatorio.',
                'fecha_desembolso.required' => 'El Campo es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            if($request->input('tasa_tem')<=0 || $request->input('tasa_tem')==''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'TEM debe ser mayor a 0.00.'
                ]);
            }
            if($request->input('monto_solicitado')<=0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Monto de Prestamo debe ser mayor a 0.00.'
                ]);
            }
            if($request->input('cuotas')<=0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Número de Cuotas debe ser mayor a 0.'
                ]);
            }

            if($request->input('dia_gracia')<0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El día de gracia debe ser mayor o igual a 0!!.'
                ]);
            }
          
            //------- validar cronograma
            $credito = DB::table('credito')
                  ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                  ->where('credito.id',$id)
                  ->select(
                      'credito.*',
                      'credito_prendatario.modalidad as modalidad_calculo',
                  )
                  ->first();

            $montomaximo = DB::table('tarifario')
                  ->where('tarifario.idcredito_prendatario',$credito->idcredito_prendatario)
                  ->where('tarifario.idforma_pago_credito',$request->input('idforma_pago_credito'))
                  ->orderBy('tarifario.monto','desc')
                  ->limit(1)
                  ->first();

            if($montomaximo!=''){
                if($request->input('monto_solicitado')>$montomaximo->monto){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El monto máximo según el tarifario es '.$montomaximo->monto.'.',
                    ]);
                }
            }

            $cuotamaximo = DB::table('tarifario')
                  ->where('tarifario.idcredito_prendatario',$credito->idcredito_prendatario)
                  ->where('tarifario.idforma_pago_credito',$request->input('idforma_pago_credito'))
                  ->orderBy('tarifario.cuotas','desc')
                  ->limit(1)
                  ->first();
            if($cuotamaximo!=''){
                if($request->input('cuotas')>$cuotamaximo->cuotas){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cuota máxima según el tarifario es '.$cuotamaximo->cuotas.'.',
                    ]);
                }
            }

            if($credito->idforma_credito == 1){
                if($request->input('monto_solicitado') > $credito->monto_cobertura_garantia){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El monto máximo según la cobertura es '.$credito->monto_cobertura_garantia.'.',
                    ]);
                }
            }


            $tasatarifario = DB::table('tarifario')
                  ->where('tarifario.idcredito_prendatario',$credito->idcredito_prendatario)
                  ->where('tarifario.idforma_pago_credito',$request->input('idforma_pago_credito'))
                  ->where('tarifario.monto','>=',$request->input('monto_solicitado'))
                  ->where('tarifario.cuotas','>=',$request->input('cuotas'))
                  ->orderBy('tarifario.cuotas','asc')
                  ->orderBy('tarifario.monto','asc')
                  ->limit(1)
                  ->first();
            $tasa_tem_minima = 0;
            $comision_cargo = 0;
            if($tasatarifario!=''){
                $tasa_tem_minima = $tasatarifario->tem;
                $comision_cargo = $tasatarifario->cargos_otros;
                if($request->input('tasa_tem') < $tasa_tem_minima){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El tasa mínima según el tarifario es '.$tasa_tem_minima.'.',
                    ]);
                }
            }else{
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No se asignado ningún tarifario para esta frecuencia de pago!!.',
                ]);
            }

            $frecuenciaDiasMap = [
              1 => 26,
              2 => 4,
              3 => 2,
              4 => 1,
            ];
            $dias = $frecuenciaDiasMap[$request->input('idforma_pago_credito')];
            $tasa_tip = number_format(($request->input('tasa_tem') / $dias) * $request->input('cuotas'), 2, '.', '');
            if($credito->modalidad_calculo == 'Interes Compuesto'){
                $tasa_tip = $request->input('tasa_tem');
            }
            
        

            $cronograma = genera_cronograma(
                  $request->input('monto_solicitado'),
                  $request->input('cuotas'),
                  $request->input('fecha_desembolso'),
                  $request->input('idforma_pago_credito'),
                  $tasa_tip,
                  $request->input('tipotasa'),
                  $request->input('dia_gracia'),
                  $comision_cargo,
                  $request->input('cargo')
            );
            //-------- fin validar cronograma
          
            // ---- SELECCIONAR LOS DATOS FILTRADOS
          
            $forma_pago_credito = DB::table('forma_pago_credito')->whereId($request->input('idforma_pago_credito'))->first();
            
            // ---- FIN SELECCIONAR LOS DATOS FILTRADOS
            
          
            DB::table('credito')->whereId($id)->update([
                'forma_pago_credito'        => $forma_pago_credito->nombre,
                'saldo_pendientepago'       => $request->input('monto_solicitado'),
                'cuota_pago'                => $cronograma['cuota_pago'],
                'fecha_primerpago'          => $cronograma['fechainicio'],
                'fecha_ultimopago'          => $cronograma['ultimafecha'],
                'fecha'                     => $request->input('fecha_desembolso'),
                'monto_solicitado'          => $request->input('monto_solicitado'),
                'idforma_pago_credito'      => $request->input('idforma_pago_credito'),
                'cuotas'                    => $request->input('cuotas'),
                'dia_gracia'                => $request->input('dia_gracia'),
                'tasa_tem'                  => $request->input('tasa_tem'),
                'tasa_tem_minima'           => $request->input('tasa_tem_minima'),
                'tasa_tip'                  => $request->input('tasa_tip'),
                'tasa_tcem'                 => $request->input('tasa_tcem'),
                'interes_total'             => $request->input('interes_total'),
                'total_pagar'               => $request->input('total_pagar'),
                'total_propuesta'           => $cronograma['total_propuesta'],
                'comision'                  => $request->input('comision'),
                'cargo'                     => $request->input('cargo'),
                'cuota_comision'            => $cronograma['cuota_comision'],
                'cuota_cargo'               => $cronograma['cuota_cargo'],
                'cuota_comisioncargo'       => $cronograma['cuota_comisioncargo'],
                'total_comision'            => $cronograma['total_comision'],
                'total_cargo'               => $cronograma['total_cargo'],
                'total_comisioncargo'       => $cronograma['total_comisioncargo'],
            ]);
             
            //$tipotasa    = $credito->modalidad_calculo == 'Interes Simple' ? 1 : 2;
          
            DB::table('credito_cronograma')->where('idcredito',$id)->delete();

            foreach($cronograma['cronograma'] as $value){
                DB::table('credito_cronograma')->insert([
                  'numerocuota'     => $value['numero'],
                  'fechapago'       => $value['fechanormal'],
                  'capital'         => $value['saldo'],
                  'amortizacion'    => $value['amortizacion'],
                  'interes'         => $value['interes'],
                  'cuotapagar'      => 0,
                  'cuota_real'      => $value['cuotafinal'],
                  'resto_redondeo'  => 0,
                  'comision'        => $value['comision'],
                  'cargo'           => $value['cargo'],
                  'comision_cargo'  => $value['comisioncargo'],
                  'idestadocredito_cronograma' => 1,
                  'idcredito'       => $id,
                ]);
            }
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha guardado correctamente.'
            ]);
        }
        else if( $request->input('view') == 'garantia_cliente' ){

            $credito = DB::table('credito')->whereId($id)->first();
          
            $select_garantias = json_decode($request->input('garantias'),true);
            $select_garantias_aval = json_decode($request->input('garantias_aval'),true);
          
            DB::table('credito')->whereId($id)->update([
              'monto_cobertura_garantia' =>  $request->input('monto_cobertura_garantia')
            ]);
            
            
            DB::table('credito_garantia')
                    ->where('credito_garantia.idcredito',$id)
                    ->where('credito_garantia.tipo','CLIENTE')
                    ->delete();
            foreach($select_garantias as $value){
              
              
              $garantias = DB::table('garantias')->whereId($value['idgarantia'])->first();
              
              $garantias_codigo = '';
              $garantias_tipogarantia = '';
              $garantias_descripcion = '';
              $garantias_serie_motor_partida = '';
              $garantias_chasis = '';
              $garantias_modelo_tipo = '';
              $garantias_otros = '';
              $garantias_color = '';
              $garantias_fabricacion = '';
              $garantias_compra = '';
              $garantias_placa = '';
              $garantias_accesorio_doc = '';
              $garantias_detalle_garantia = '';
              $garantias_idestado_garantia = 0;
              $garantias_idestado_garantia_ref = '';
              $garantias_cobertura = '';
              $garantias_valorcomercial = '';
              $garantias_metodo_valorizacion = '';
              $garantias_tipo_joyas = '';
              $garantias_tarifario_joya = '';
              $garantias_descuento_joya = '';
              $garantias_valorizacion_descuento = '';
              
              $garantias_valor_mercado = '';
              $garantias_valor_comercial = '';
              $garantias_valor_realizacion = '';

              $clienteidentificacion = '';
              $clientenombrecompleto = '';
              
              if($garantias){
                  $tipo_garantia = DB::table('tipo_garantia')->whereId($garantias->idtipogarantia)->first();
                  $metodo_valorizacion = DB::table('metodo_valorizacion')->whereId($garantias->idmetodo_valorizacion)->first();
                  $tipo_joyas = DB::table('tipo_joyas')->whereId($garantias->idtipo_joyas)->first();
                  $tarifario_joyas = DB::table('tarifario_joyas')->whereId($garantias->idtarifario_joya)->first();
                  $descuento_joya = DB::table('descuento_joya')->whereId($garantias->iddescuento_joya)->first();
                  $valorizacion_descuento = DB::table('valorizacion_descuento')->whereId($garantias->idvalorizacion_descuento)->first();
                
                  if($credito->idforma_credito==1){
                      $garantias_codigo = 'GP'.str_pad($garantias->id, 8, '0', STR_PAD_LEFT);
                  }
                  $garantias_tipogarantia = $tipo_garantia->nombre;
                  $garantias_descripcion = $garantias->descripcion;
                  $garantias_serie_motor_partida = $garantias->serie_motor_partida;
                  $garantias_chasis = $garantias->chasis;
                  $garantias_modelo_tipo = $garantias->modelo_tipo;
                  $garantias_otros = $garantias->otros;
                  $garantias_color = $garantias->color;
                  $garantias_fabricacion = $garantias->fabricacion;
                  $garantias_compra = $garantias->compra;
                  $garantias_placa = $garantias->placa;
                  $garantias_accesorio_doc = $garantias->accesorio_doc;
                  $garantias_detalle_garantia = $garantias->detalle_garantia;
                  $garantias_idestado_garantia = $garantias->idestado_garantia;
                  $garantias_idestado_garantia_ref = $garantias->idestado_garantia_ref;
                  $garantias_cobertura = $garantias->cobertura;
                  $garantias_valorcomercial = $garantias->valorcomercial;
                  $garantias_metodo_valorizacion = $metodo_valorizacion?$metodo_valorizacion->nombre:'';
                  $garantias_tipo_joyas = $tipo_joyas?$tipo_joyas->nombre:'';
                  $garantias_tarifario_joya = $tarifario_joyas?$tarifario_joyas->tipo:'';
                  $garantias_descuento_joya = $descuento_joya?$descuento_joya->nombre:'';
                  $garantias_valorizacion_descuento = $descuento_joya?$descuento_joya->nombre:'';
                
                  $garantias_valor_mercado = $garantias->valor_mercado;
                  $garantias_valor_comercial = $garantias->valorcomercial;
                  $garantias_valor_realizacion = $garantias->cobertura;
              
                  $cliente = DB::table('users')->whereId($garantias->idcliente)->first();
                  if($cliente!=''){
                      $clienteidentificacion = $cliente->identificacion;
                      $clientenombrecompleto = $cliente->nombrecompleto;
                  }
                
              }
              
              
              $garantias_noprendarias = DB::table('garantias_noprendarias')->whereId($value['idgarantianoprendataria'])->first();
              $idtipo_garantia_noprendaria = 0;
              $garantias_noprendarias_tipo_garantia_noprendaria = '';
              $garantias_noprendarias_subtipo_garantia_noprendaria = '';
              $garantias_noprendarias_subtipo_garantia_noprendaria_ii = '';
              $garantias_noprendarias_descripcion = '';
              if($garantias_noprendarias){
                  $tipo_garantia_noprendaria = DB::table('tipo_garantia_noprendaria')->whereId($garantias_noprendarias->idtipo_garantia_noprendaria)->first();
                  $subtipo_garantia_noprendaria = DB::table('subtipo_garantia_noprendaria')->whereId($garantias_noprendarias->idsubtipo_garantia_noprendaria)->first();
                  $subtipo_garantia_noprendaria_ii = DB::table('subtipo_garantia_noprendaria_ii')->whereId($garantias_noprendarias->idsubtipo_garantia_noprendaria_ii)->first();
                  $idtipo_garantia_noprendaria = $garantias_noprendarias->idtipo_garantia_noprendaria;
                  $garantias_noprendarias_tipo_garantia_noprendaria = $tipo_garantia_noprendaria->nombre;
                  $garantias_noprendarias_subtipo_garantia_noprendaria = $subtipo_garantia_noprendaria->nombre;
                  $garantias_noprendarias_subtipo_garantia_noprendaria_ii = $subtipo_garantia_noprendaria_ii->nombre;
                  $garantias_noprendarias_descripcion = $garantias_noprendarias->descripcion;
                
                  if($garantias_noprendarias->idtipo_garantia_noprendaria==2){
                      $garantias_valor_comercial = $garantias_noprendarias->valor_mercado;
                  }else{
                      $garantias_valor_mercado = $garantias_noprendarias->valor_mercado;
                      $garantias_valor_comercial = $garantias_noprendarias->valor_comercial;
                      $garantias_valor_realizacion = $garantias_noprendarias->valor_realizacion;
                  }
                
                  $cliente = DB::table('users')->whereId($garantias_noprendarias->idcliente)->first();
                  if($cliente!=''){
                      $clienteidentificacion = $cliente->identificacion;
                      $clientenombrecompleto = $cliente->nombrecompleto;
                  }
              }
              
              DB::table('credito_garantia')->insertGetId([
                //'propioavalado'                 => 'PROPIO',
                'garantias_codigo'              => $garantias_codigo,
                'garantias_tipogarantia'        => $garantias_tipogarantia,
                //'garantias_descripcion'         => $garantias_descripcion,
                'garantias_serie_motor_partida' => $garantias_serie_motor_partida,
                'garantias_chasis'              => $garantias_chasis,
                'garantias_modelo_tipo'         => $garantias_modelo_tipo,
                'garantias_otros'               => $garantias_otros,
                'garantias_idestado_garantia'          => $garantias_idestado_garantia,
                'garantias_color'               => $garantias_color,
                'garantias_fabricacion'         => $garantias_fabricacion,
                'garantias_compra'              => $garantias_compra,
                'garantias_placa'               => $garantias_placa,
                'garantias_accesorio_doc'       => $garantias_accesorio_doc,
                'garantias_detalle_garantia'    => $garantias_detalle_garantia,
                'garantias_idestado_garantia_ref'          => $garantias_idestado_garantia_ref,
                'garantias_metodo_valorizacion' => $garantias_metodo_valorizacion,
                'garantias_tipo_joyas'          => $garantias_tipo_joyas,
                'garantias_tarifario_joya'      => $garantias_tarifario_joya,
                'garantias_descuento_joya'      => $garantias_descuento_joya,
                'garantias_cobertura'      => $garantias_cobertura,
                'garantias_valorcomercial'      => $garantias_valorcomercial,
                'garantias_valorizacion_descuento'  => $garantias_valorizacion_descuento,
                'clienteidentificacion'     => $clienteidentificacion,
                'clientenombrecompleto'     => $clientenombrecompleto,
                
                'garantias_noprendarias_tipo_garantia_noprendaria'  => $garantias_noprendarias_tipo_garantia_noprendaria,
                'garantias_noprendarias_subtipo_garantia_noprendaria'  => $garantias_noprendarias_subtipo_garantia_noprendaria,
                'garantias_noprendarias_subtipo_garantia_noprendaria_ii'  => $garantias_noprendarias_subtipo_garantia_noprendaria_ii,
                //'garantias_noprendarias_descripcion'  => $garantias_noprendarias_descripcion,
                
                'idtipo_garantia_noprendaria'  => $idtipo_garantia_noprendaria,
                
                'idcredito'                 => $id,
                'idgarantias'               => $value['idgarantia'],
                'idcliente'                 => $value['idcliente'],
                'idgarantias_noprendarias'  => $value['idgarantianoprendataria'],
                'descripcion'               => $value['descripcion'],
                'valor_mercado'             => $value['valor_mercado'],
                'valor_comercial'           => $value['valor_comercial'],
                'valor_realizacion'         => $value['valor_realizacion'],
                'tipo'                      => 'CLIENTE',
                'idestadoentrega'           => 1,
              ]);
            }
            DB::table('credito_garantia')
                    ->where('credito_garantia.idcredito',$id)
                    ->where('credito_garantia.tipo','AVAL')
                    ->delete();
            foreach($select_garantias_aval as $value){
              
              $garantias = DB::table('garantias')->whereId($value['idgarantia'])->first();
              
              $garantias_codigo = '';
              $garantias_tipogarantia = '';
              $garantias_descripcion = '';
              $garantias_serie_motor_partida = '';
              $garantias_chasis = '';
              $garantias_modelo_tipo = '';
              $garantias_otros = '';
              $garantias_color = '';
              $garantias_fabricacion = '';
              $garantias_compra = '';
              $garantias_placa = '';
              $garantias_accesorio_doc = '';
              $garantias_detalle_garantia = '';
              $garantias_idestado_garantia_ref = '';
              $garantias_cobertura = '';
              $garantias_valorcomercial = '';
              $garantias_metodo_valorizacion = '';
              $garantias_tipo_joyas = '';
              $garantias_tarifario_joya = '';
              $garantias_descuento_joya = '';
              $garantias_valorizacion_descuento = '';
              
              $garantias_valor_mercado = '';
              $garantias_valor_comercial = '';
              $garantias_valor_realizacion = '';
              
              $clienteidentificacion = '';
              $clientenombrecompleto = '';
              
              if($garantias){
                  $tipo_garantia = DB::table('tipo_garantia')->whereId($garantias->idtipogarantia)->first();
                  $metodo_valorizacion = DB::table('metodo_valorizacion')->whereId($garantias->idmetodo_valorizacion)->first();
                  $tipo_joyas = DB::table('tipo_joyas')->whereId($garantias->idtipo_joyas)->first();
                  $tarifario_joyas = DB::table('tarifario_joyas')->whereId($garantias->idtarifario_joya)->first();
                  $descuento_joya = DB::table('descuento_joya')->whereId($garantias->iddescuento_joya)->first();
                  $valorizacion_descuento = DB::table('valorizacion_descuento')->whereId($garantias->idvalorizacion_descuento)->first();
                
                  if($credito->idforma_credito==1){
                      $garantias_codigo = 'GP'.str_pad($garantias->id, 8, '0', STR_PAD_LEFT);
                  }
                  $garantias_tipogarantia = $tipo_garantia->nombre;
                  $garantias_descripcion = $garantias->descripcion;
                  $garantias_serie_motor_partida = $garantias->serie_motor_partida;
                  $garantias_chasis = $garantias->chasis;
                  $garantias_modelo_tipo = $garantias->modelo_tipo;
                  $garantias_otros = $garantias->otros;
                  $garantias_color = $garantias->color;
                  $garantias_fabricacion = $garantias->fabricacion;
                  $garantias_compra = $garantias->compra;
                  $garantias_placa = $garantias->placa;
                  $garantias_accesorio_doc = $garantias->accesorio_doc;
                  $garantias_detalle_garantia = $garantias->detalle_garantia;
                  $garantias_idestado_garantia_ref = $garantias->idestado_garantia_ref;
                  $garantias_cobertura = $garantias->cobertura;
                  $garantias_valorcomercial = $garantias->valorcomercial;
                  $garantias_metodo_valorizacion = $metodo_valorizacion->nombre;
                  $garantias_tipo_joyas = $tipo_joyas?$tipo_joyas->nombre:'';
                  $garantias_tarifario_joya = $tarifario_joyas?$tarifario_joyas->nombre:'';
                  $garantias_descuento_joya = $descuento_joya?$descuento_joya->nombre:'';
                  $garantias_valorizacion_descuento = $descuento_joya?$descuento_joya->detalle_descuento:'';
              
                  $garantias_valor_mercado = $garantias->valor_mercado;
                  $garantias_valor_comercial = $garantias->valorcomercial;
                  $garantias_valor_realizacion = $garantias->cobertura;
                
                  $cliente = DB::table('users')->whereId($garantias->idcliente)->first();
                  if($cliente!=''){
                      $clienteidentificacion = $cliente->identificacion;
                      $clientenombrecompleto = $cliente->nombrecompleto;
                  }
                
              }
              
              
              $garantias_noprendarias = DB::table('garantias_noprendarias')->whereId($value['idgarantianoprendataria'])->first();
              $idtipo_garantia_noprendaria = 0;
              $garantias_noprendarias_tipo_garantia_noprendaria = '';
              $garantias_noprendarias_subtipo_garantia_noprendaria = '';
              $garantias_noprendarias_subtipo_garantia_noprendaria_ii = '';
              $garantias_noprendarias_descripcion = '';
              if($garantias_noprendarias){
                  $tipo_garantia_noprendaria = DB::table('tipo_garantia_noprendaria')->whereId($garantias_noprendarias->idtipo_garantia_noprendaria)->first();
                  $subtipo_garantia_noprendaria = DB::table('subtipo_garantia_noprendaria')->whereId($garantias_noprendarias->idsubtipo_garantia_noprendaria)->first();
                  $subtipo_garantia_noprendaria_ii = DB::table('subtipo_garantia_noprendaria_ii')->whereId($garantias_noprendarias->idsubtipo_garantia_noprendaria_ii)->first();
                  $idtipo_garantia_noprendaria = $garantias_noprendarias->idtipo_garantia_noprendaria;
                  $garantias_noprendarias_tipo_garantia_noprendaria = $tipo_garantia_noprendaria->nombre;
                  $garantias_noprendarias_subtipo_garantia_noprendaria = $subtipo_garantia_noprendaria->nombre;
                  $garantias_noprendarias_subtipo_garantia_noprendaria_ii = $subtipo_garantia_noprendaria_ii->nombre;
                  $garantias_noprendarias_descripcion = $garantias_noprendarias->descripcion;
                
                  if($garantias_noprendarias->idtipo_garantia_noprendaria==2){
                      $garantias_valor_comercial = $garantias_noprendarias->valor_mercado;
                  }else{
                      $garantias_valor_mercado = $garantias_noprendarias->valor_mercado;
                      $garantias_valor_comercial = $garantias_noprendarias->valor_comercial;
                      $garantias_valor_realizacion = $garantias_noprendarias->valor_realizacion;
                  }
                
                  $cliente = DB::table('users')->whereId($garantias_noprendarias->idcliente)->first();
                  if($cliente!=''){
                      $clienteidentificacion = $cliente->identificacion;
                      $clientenombrecompleto = $cliente->nombrecompleto;
                  }
              }
              
              DB::table('credito_garantia')->insertGetId([
                //'propioavalado'                 => 'AVALADO',
                'garantias_codigo'              => $garantias_codigo,
                'garantias_tipogarantia'        => $garantias_tipogarantia,
                //'garantias_descripcion'         => $garantias_descripcion,
                'garantias_serie_motor_partida' => $garantias_serie_motor_partida,
                'garantias_chasis'              => $garantias_chasis,
                'garantias_modelo_tipo'         => $garantias_modelo_tipo,
                'garantias_otros'               => $garantias_otros,
                'garantias_color'               => $garantias_color,
                'garantias_fabricacion'         => $garantias_fabricacion,
                'garantias_compra'              => $garantias_compra,
                'garantias_placa'               => $garantias_placa,
                'garantias_accesorio_doc'       => $garantias_accesorio_doc,
                'garantias_detalle_garantia'    => $garantias_detalle_garantia,
                'garantias_metodo_valorizacion' => $garantias_metodo_valorizacion,
                'garantias_tipo_joyas'          => $garantias_tipo_joyas,
                'garantias_tarifario_joya'      => $garantias_tarifario_joya,
                'garantias_descuento_joya'      => $garantias_descuento_joya,
                'garantias_valorizacion_descuento'  => $garantias_valorizacion_descuento,
                'clienteidentificacion'     => $clienteidentificacion,
                'clientenombrecompleto'     => $clientenombrecompleto,
                
                'garantias_noprendarias_tipo_garantia_noprendaria'  => $garantias_noprendarias_tipo_garantia_noprendaria,
                'garantias_noprendarias_subtipo_garantia_noprendaria'  => $garantias_noprendarias_subtipo_garantia_noprendaria,
                'garantias_noprendarias_subtipo_garantia_noprendaria_ii'  => $garantias_noprendarias_subtipo_garantia_noprendaria_ii,
                //'garantias_noprendarias_descripcion'  => $garantias_noprendarias_descripcion,
                
                'idtipo_garantia_noprendaria'  => $idtipo_garantia_noprendaria,
                
                'idcredito'                 => $id,
                'idgarantias'               => $value['idgarantia'],
                'idcliente'                 => $value['idcliente'],
                'idgarantias_noprendarias'  => $value['idgarantianoprendataria'],
                'descripcion'               => $value['descripcion'],
                //'valor_mercado'             => $garantias_valor_mercado,
                //'valor_comercial'           => $garantias_valor_comercial,
                //'valor_realizacion'         => $garantias_valor_realizacion,
                'valor_mercado'             => $value['valor_mercado'],
                'valor_comercial'           => $value['valor_comercial'],
                'valor_realizacion'         => $value['valor_realizacion'],
                'tipo'                      => 'AVAL',
                'idestadoentrega'           => 1,
              ]);
            }
            
            
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
          
        }
        else if( $request->input('view') == 'evaluacion_cualitativa'){
          $rules = [
              'idtipo_giro_economico' => 'required',                      
              'idgiro_economico_evaluacion' => 'required',                      
          ];

          $messages = [
              'idtipo_giro_economico.required' => 'El Campo es Obligatorio.',
              'idgiro_economico_evaluacion.required' => 'El Campo es Obligatorio.',
          ];
          $this->validate($request,$rules,$messages);
          $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')->where('credito_evaluacion_cualitativa.idcredito',$id)->first();
          
          
          if($credito_evaluacion_cualitativa){
            DB::table('credito_evaluacion_cualitativa')->whereId($credito_evaluacion_cualitativa->id)->update([
              'fecha' => Carbon::now(),
              'descripcion_actividad' => $request->input('descripcion_actividad'),
              'idtipo_giro_economico' => $request->input('idtipo_giro_economico'),
              'idgiro_economico_evaluacion' => $request->input('idgiro_economico_evaluacion'),
              'ejercicio_giro_economico' => $request->input('ejercicio_giro_economico'),
              
              'referencia' => $request->input('referencia'),
              
              'cantidad_cliente_natural' => $request->input('cantidad_cliente_natural'),
              'cantidad_cliente_juridico' => $request->input('cantidad_cliente_juridico'),
              'cantidad_pareja_natural' => $request->input('cantidad_pareja_natural'),
              'cantidad_pareja_juridico' => $request->input('cantidad_pareja_juridico'),
              'total_deuda' => $request->input('total_deuda'),
              'experiencia_microempresa' => $request->input('experiencia_microempresa'),
              'tiempo_mismo_local' => $request->input('tiempo_mismo_local'),
              'instalacion_local' => $request->input('instalacion_local'),
              'nro_trabajador_completo' => $request->input('nro_trabajador_completo'),
              'nro_trabajador_parcal' => $request->input('nro_trabajador_parcal'),
              
              'saladario_fijo' => $request->input('saladario_fijo') ? 'SI' : 'NO',
              'otros_negocios' => $request->input('otros_negocios') ? 'SI' : 'NO',
              'alquiler_local' => $request->input('alquiler_local') ? 'SI' : 'NO',
              'no_tiene' => $request->input('no_tiene') ? 'SI' : 'NO',
              'pensionista' => $request->input('pensionista') ? 'SI' : 'NO',
              'registro_ventas_cuentas' => $request->input('registro_ventas_cuentas') ? 'SI' : 'NO',
              'pago_impuestos_dia' => $request->input('pago_impuestos_dia') ? 'SI' : 'NO',
              'pago_servicios_dia' => $request->input('pago_servicios_dia') ? 'SI' : 'NO',
              'politica_orden' => $request->input('politica_orden') ? 'SI' : 'NO',
              'normas_municipales' => $request->input('normas_municipales') ? 'SI' : 'NO',
              
              'gasto_alimentacion' => $request->input('gasto_alimentacion'),
              'gasto_educacion' => $request->input('gasto_educacion'),
              'gasto_vestimenta' => $request->input('gasto_vestimenta'),
              'gasto_transporte' => $request->input('gasto_transporte'),
              'gasto_salud' => $request->input('gasto_salud'),
              'gasto_vivienda' => $request->input('gasto_vivienda'),
              'gasto_agua' => $request->input('gasto_agua'),
              'gasto_luz' => $request->input('gasto_luz'),
              'gasto_telefono_internet' => $request->input('gasto_telefono_internet'),
              'gasto_celular' => $request->input('gasto_celular'),
              'gasto_cable' => $request->input('gasto_cable'),
              'total_servicios' => $request->input('total_servicios'),
              'gasto_otros' => $request->input('gasto_otros'),
              'gasto_total' => $request->input('gasto_total'),
              
              'total_hijos' => $request->input('total_hijos'),
              'total_hijos_dependientes' => $request->input('total_hijos_dependientes'),
              'detalle_destino_prestamo' => $request->input('detalle_destino_prestamo'),
              'fortalezas_negocio' => $request->input('fortalezas_negocio'),
              'cantidad_update' => $credito_evaluacion_cualitativa->cantidad_update + 1,
              
            ]);
          }else{
            DB::table('credito_evaluacion_cualitativa')->insert([
              'idcredito' => $id,
              'fecha' => Carbon::now(),
              'descripcion_actividad' => $request->input('descripcion_actividad'),
              'idtipo_giro_economico' => $request->input('idtipo_giro_economico'),
              'idgiro_economico_evaluacion' => $request->input('idgiro_economico_evaluacion'),
              'ejercicio_giro_economico' => $request->input('ejercicio_giro_economico'),
              
              'referencia' => $request->input('referencia'),
              
              'cantidad_cliente_natural' => $request->input('cantidad_cliente_natural'),
              'cantidad_cliente_juridico' => $request->input('cantidad_cliente_juridico'),
              'cantidad_pareja_natural' => $request->input('cantidad_pareja_natural'),
              'cantidad_pareja_juridico' => $request->input('cantidad_pareja_juridico'),
              'total_deuda' => $request->input('total_deuda'),
              'experiencia_microempresa' => $request->input('experiencia_microempresa'),
              'tiempo_mismo_local' => $request->input('tiempo_mismo_local'),
              'instalacion_local' => $request->input('instalacion_local'),
              'nro_trabajador_completo' => $request->input('nro_trabajador_completo'),
              'nro_trabajador_parcal' => $request->input('nro_trabajador_parcal'),
              
              'saladario_fijo' => $request->input('saladario_fijo') ? 'SI' : 'NO',
              'otros_negocios' => $request->input('otros_negocios') ? 'SI' : 'NO',
              'alquiler_local' => $request->input('alquiler_local') ? 'SI' : 'NO',
              'no_tiene' => $request->input('no_tiene') ? 'SI' : 'NO',
              'pensionista' => $request->input('pensionista') ? 'SI' : 'NO',
              'registro_ventas_cuentas' => $request->input('registro_ventas_cuentas') ? 'SI' : 'NO',
              'pago_impuestos_dia' => $request->input('pago_impuestos_dia') ? 'SI' : 'NO',
              'pago_servicios_dia' => $request->input('pago_servicios_dia') ? 'SI' : 'NO',
              'politica_orden' => $request->input('politica_orden') ? 'SI' : 'NO',
              'normas_municipales' => $request->input('normas_municipales') ? 'SI' : 'NO',
              
              'gasto_alimentacion' => $request->input('gasto_alimentacion'),
              'gasto_educacion' => $request->input('gasto_educacion'),
              'gasto_vestimenta' => $request->input('gasto_vestimenta'),
              'gasto_transporte' => $request->input('gasto_transporte'),
              'gasto_salud' => $request->input('gasto_salud'),
              'gasto_vivienda' => $request->input('gasto_vivienda'),
              'gasto_agua' => $request->input('gasto_agua'),
              'gasto_luz' => $request->input('gasto_luz'),
              'gasto_telefono_internet' => $request->input('gasto_telefono_internet'),
              'gasto_celular' => $request->input('gasto_celular'),
              'gasto_cable' => $request->input('gasto_cable'),
              'total_servicios' => $request->input('total_servicios'),
              'gasto_otros' => $request->input('gasto_otros'),
              'gasto_total' => $request->input('gasto_total'),
              
              'total_hijos' => $request->input('total_hijos'),
              'total_hijos_dependientes' => $request->input('total_hijos_dependientes'),
              'detalle_destino_prestamo' => $request->input('detalle_destino_prestamo'),
              'fortalezas_negocio' => $request->input('fortalezas_negocio'),
            ]);
          }
          DB::table('credito')->whereId($id)->update([
            'fecha' => Carbon::now(),
          ]);
          
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha actualizado correctamente.'
          ]);
          
        }
        else if( $request->input('view') == 'evaluacion_cuantitativa' ){
          $credito_evaluacion_cuantitativa = DB::table('credito_evaluacion_cuantitativa')->where('credito_evaluacion_cuantitativa.idcredito',$id)->first();
          
          
          if($credito_evaluacion_cuantitativa){
            DB::table('credito_evaluacion_cuantitativa')->whereId($credito_evaluacion_cuantitativa->id)->update([
              'fecha' => Carbon::now(),
              'evaluacion_meses' => $request->input('evaluacion_meses'),
              'margen_venta_calculado' => $request->input('margen_venta_calculado'),
              'balance_general' => $request->input('balance_general'),
              'ganancia_perdida' => $request->input('ganancia_perdida'),
              
              'dias_ventas_mensual' => $request->input('dias_ventas_mensual')!=''?$request->input('dias_ventas_mensual'):0,
              'dias_compras_mensual' => $request->input('dias_compras_mensual')!=''?$request->input('dias_compras_mensual'):0,
              
              'credito_cobrando_venta_mensual' => $request->input('credito_cobrando_venta_mensual'),
              'credito_porcentaje_venta_mensual' => $request->input('credito_porcentaje_venta_mensual'),
              'contado_cobrando_venta_mensual' => $request->input('contado_cobrando_venta_mensual'),
              'contado_porcentaje_venta_mensual' => $request->input('contado_porcentaje_venta_mensual'),
              'credito_cobrando_compra_mensual' => $request->input('credito_cobrando_compra_mensual'),
              'credito_porcentaje_compra_mensual' => $request->input('credito_porcentaje_compra_mensual'),
              'contado_cobrando_compra_mensual' => $request->input('contado_cobrando_compra_mensual'),
              'contado_porcentaje_compra_mensual' => $request->input('contado_porcentaje_compra_mensual'),
              
              'ratio_re_negocio' => $request->input('ratio_re_negocio'),
              'ratio_re_unidadfamiliar' => $request->input('ratio_re_unidadfamiliar'),
              'ratio_re_patrimonial' => $request->input('ratio_re_patrimonial'),
              'ratio_re_activos' => $request->input('ratio_re_activos'),
              'ratio_re_ventas' => $request->input('ratio_re_ventas'),
              
              'ratio_re_prestamo' => $request->input('ratio_re_prestamo'),
              'ratio_re_capital' => $request->input('ratio_re_capital'),
              'ratio_re_liquidez' => $request->input('ratio_re_liquidez'),
              'ratio_re_liquidez_acida' => $request->input('ratio_re_liquidez_acida'),
              'ratio_re_endeudamiento_actual' => $request->input('ratio_re_endeudamiento_actual'),
              'ratio_re_endeudamiento_propuesta' => $request->input('ratio_re_endeudamiento_propuesta'),
              'ratio_re_rotacion_inventario' => $request->input('ratio_re_rotacion_inventario'),
              'ratio_re_promedio_cobranza' => $request->input('ratio_re_promedio_cobranza'),
              'ratio_re_primedio_pago' => $request->input('ratio_re_primedio_pago'),
              //'ratio_re_cuota_total' => $request->input('ratio_re_cuota_total'),
              'excedente_antes_propuesta' => $request->input('excedente_antes_propuesta'),
              'excedente_propuesta_sin_deduccion' => $request->input('excedente_propuesta_sin_deduccion'),
              'excedente_propuesta_con_deduccion' => $request->input('excedente_propuesta_con_deduccion'),
              'estado_credito' => $request->input('estado_credito'),
              
              'comentario' => $request->input('comentario'),
              'cantidad_update' => $credito_evaluacion_cuantitativa->cantidad_update + 1,
              
            ]);
            
          }else{
            DB::table('credito_evaluacion_cuantitativa')->insert([
              'idcredito' => $id,
              'fecha' => Carbon::now(),
              'evaluacion_meses' => $request->input('evaluacion_meses'),
              'margen_venta_calculado' => $request->input('margen_venta_calculado'),
              'balance_general' => $request->input('balance_general'),
              'ganancia_perdida' => $request->input('ganancia_perdida'),
              
              'dias_ventas_mensual' => $request->input('dias_ventas_mensual')!=''?$request->input('dias_ventas_mensual'):0,
              'dias_compras_mensual' => $request->input('dias_compras_mensual')!=''?$request->input('dias_compras_mensual'):0,
              
              'credito_cobrando_venta_mensual' => $request->input('credito_cobrando_venta_mensual'),
              'credito_porcentaje_venta_mensual' => $request->input('credito_porcentaje_venta_mensual'),
              'contado_cobrando_venta_mensual' => $request->input('contado_cobrando_venta_mensual'),
              'contado_porcentaje_venta_mensual' => $request->input('contado_porcentaje_venta_mensual'),
              'credito_cobrando_compra_mensual' => $request->input('credito_cobrando_compra_mensual'),
              'credito_porcentaje_compra_mensual' => $request->input('credito_porcentaje_compra_mensual'),
              'contado_cobrando_compra_mensual' => $request->input('contado_cobrando_compra_mensual'),
              'contado_porcentaje_compra_mensual' => $request->input('contado_porcentaje_compra_mensual'),
              
              'ratio_re_negocio' => $request->input('ratio_re_negocio'),
              'ratio_re_unidadfamiliar' => $request->input('ratio_re_unidadfamiliar'),
              'ratio_re_patrimonial' => $request->input('ratio_re_patrimonial'),
              'ratio_re_activos' => $request->input('ratio_re_activos'),
              'ratio_re_ventas' => $request->input('ratio_re_ventas'),
              
              'ratio_re_prestamo' => $request->input('ratio_re_prestamo'),
              'ratio_re_capital' => $request->input('ratio_re_capital'),
              'ratio_re_liquidez' => $request->input('ratio_re_liquidez'),
              'ratio_re_liquidez_acida' => $request->input('ratio_re_liquidez_acida'),
              'ratio_re_endeudamiento_actual' => $request->input('ratio_re_endeudamiento_actual'),
              'ratio_re_endeudamiento_propuesta' => $request->input('ratio_re_endeudamiento_propuesta'),
              'ratio_re_rotacion_inventario' => $request->input('ratio_re_rotacion_inventario'),
              'ratio_re_promedio_cobranza' => $request->input('ratio_re_promedio_cobranza'),
              'ratio_re_primedio_pago' => $request->input('ratio_re_primedio_pago'),
              //'ratio_re_cuota_total' => $request->input('ratio_re_cuota_total'),
              'excedente_antes_propuesta' => $request->input('excedente_antes_propuesta'),
              'excedente_propuesta_sin_deduccion' => $request->input('excedente_propuesta_sin_deduccion'),
              'excedente_propuesta_con_deduccion' => $request->input('excedente_propuesta_con_deduccion'),
              'estado_credito' => $request->input('estado_credito'),
              
              'comentario' => $request->input('comentario'),
            ]);
            
          }
          DB::table('credito')->whereId($id)->update([
            'fecha' => Carbon::now(),
          ]);
          
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha actualizado correctamente.'
          ]);
        }
        else if( $request->input('view') == 'deudas'){
          $credito_cuantitativa_deudas = DB::table('credito_cuantitativa_deudas')->where('credito_cuantitativa_deudas.idcredito',$id)->first();
          
          
          if($credito_cuantitativa_deudas){
            DB::table('credito_cuantitativa_deudas')->whereId($credito_cuantitativa_deudas->id)->update([
              'fecha' => Carbon::now(),
              'entidad_regulada' => $request->input('entidad_regulada'),
              'total_saldo_capital' => $request->input('total_saldo_capital'),
              'total_cuota' => $request->input('total_cuota'),
              'total_corto_plazo' => $request->input('total_corto_plazo'),
              'total_largo_plazo' => $request->input('total_largo_plazo'),
              'total_saldo_capital_deducciones' => $request->input('total_saldo_capital_deducciones'),
              'total_cuota_deducciones' => $request->input('total_cuota_deducciones'),
              
              'entidad_noregulada' => $request->input('entidad_noregulada'),
              'total_noregulada_saldo_capital' => $request->input('total_noregulada_saldo_capital'),
              'total_noregulada_cuota' => $request->input('total_noregulada_cuota'),
              'total_noregulada_corto_plazo' => $request->input('total_noregulada_corto_plazo'),
              'total_noregulada_largo_plazo' => $request->input('total_noregulada_largo_plazo'),
              'total_noregulada_saldo_capital_deducciones' => $request->input('total_noregulada_saldo_capital_deducciones'),
              'total_noregulada_cuota_deducciones' => $request->input('total_noregulada_cuota_deducciones'),
              
              'linea_credito' => $request->input('linea_credito'),
              'total_lc_linea_credito' => $request->input('total_lc_linea_credito'),
              'total_lc_cuotas' => $request->input('total_lc_cuotas'),
              'resumen' => $request->input('resumen'),
              
              'total_resumen_linea_credito' => $request->input('total_resumen_linea_credito'),
              'total_resumen_cuotas_linea_credito' => $request->input('total_resumen_cuotas_linea_credito'),
              'total_resumen_cuotas_linea_credito2' => $request->input('total_resumen_cuotas_linea_credito2'),
              
              'idforma_pago_credito' => $request->input('idforma_pago_credito'),
              'propuesta_cuotas' => $request->input('propuesta_cuotas'),
              'propuesta_monto' => $request->input('propuesta_monto'),
              'propuesta_tem' => $request->input('propuesta_tem'),
              
              'propuesta_servicio_otros' => $request->input('propuesta_servicio_otros'),
              'propuesta_cargos' => $request->input('propuesta_cargos'),
              'propuesta_total_pagar' => $request->input('propuesta_total_pagar'),
              'total_propuesta' => $request->input('total_propuesta'),
              
              'riesgo_proyectado_empresa' => $request->input('riesgo_proyectado_empresa'),
              'riesgo_proyectado_todos' => $request->input('riesgo_proyectado_todos'),
              /*'excedente_antes_propuesta' => $request->input('excedente_antes_propuesta'),
              'excedente_propuesta_sin_deduccion' => $request->input('excedente_propuesta_sin_deduccion'),
              'excedente_propuesta_con_deduccion' => $request->input('excedente_propuesta_con_deduccion'),*/
              'estado_credito' => $request->input('estado_credito'),
              
            ]);
          }else{
            DB::table('credito_cuantitativa_deudas')->insert([
              'idcredito' => $id,
              'fecha' => Carbon::now(),
              'entidad_regulada' => $request->input('entidad_regulada'),
              'total_saldo_capital' => $request->input('total_saldo_capital'),
              'total_cuota' => $request->input('total_cuota'),
              'total_corto_plazo' => $request->input('total_corto_plazo'),
              'total_largo_plazo' => $request->input('total_largo_plazo'),
              'total_saldo_capital_deducciones' => $request->input('total_saldo_capital_deducciones'),
              'total_cuota_deducciones' => $request->input('total_cuota_deducciones'),
              
              'entidad_noregulada' => $request->input('entidad_noregulada'),
              'total_noregulada_saldo_capital' => $request->input('total_noregulada_saldo_capital'),
              'total_noregulada_cuota' => $request->input('total_noregulada_cuota'),
              'total_noregulada_corto_plazo' => $request->input('total_noregulada_corto_plazo'),
              'total_noregulada_largo_plazo' => $request->input('total_noregulada_largo_plazo'),
              'total_noregulada_saldo_capital_deducciones' => $request->input('total_noregulada_saldo_capital_deducciones'),
              'total_noregulada_cuota_deducciones' => $request->input('total_noregulada_cuota_deducciones'),
              
              'linea_credito' => $request->input('linea_credito'),
              'total_lc_linea_credito' => $request->input('total_lc_linea_credito'),
              'total_lc_cuotas' => $request->input('total_lc_cuotas'),
              'resumen' => $request->input('resumen'),
              
              'total_resumen_linea_credito' => $request->input('total_resumen_linea_credito'),
              'total_resumen_cuotas_linea_credito' => $request->input('total_resumen_cuotas_linea_credito'),
              'total_resumen_cuotas_linea_credito2' => $request->input('total_resumen_cuotas_linea_credito2'),
              
              'idforma_pago_credito' => $request->input('idforma_pago_credito'),
              'propuesta_cuotas' => $request->input('propuesta_cuotas'),
              'propuesta_monto' => $request->input('propuesta_monto'),
              'propuesta_tem' => $request->input('propuesta_tem'),
              
              'propuesta_servicio_otros' => $request->input('propuesta_servicio_otros'),
              'propuesta_cargos' => $request->input('propuesta_cargos'),
              'propuesta_total_pagar' => $request->input('propuesta_total_pagar'),
              'total_propuesta' => $request->input('total_propuesta'),
              
              'riesgo_proyectado_empresa' => $request->input('riesgo_proyectado_empresa'),
              'riesgo_proyectado_todos' => $request->input('riesgo_proyectado_todos'),
              /*'excedente_antes_propuesta' => $request->input('excedente_antes_propuesta'),
              'excedente_propuesta_sin_deduccion' => $request->input('excedente_propuesta_sin_deduccion'),
              'excedente_propuesta_con_deduccion' => $request->input('excedente_propuesta_con_deduccion'),*/
              'estado_credito' => $request->input('estado_credito'),
              
              
            ]);
          }
          DB::table('credito')->whereId($id)->update([
            'fecha' => Carbon::now(),
          ]);
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha actualizado correctamente.'
          ]);
        }
        else if( $request->input('view') == 'control_limites' ){

          $credito_cuantitativa_control_limites = DB::table('credito_cuantitativa_control_limites')->where('credito_cuantitativa_control_limites.idcredito',$id)->first();
       
          
          if($credito_cuantitativa_control_limites){
    
            DB::table('credito_cuantitativa_control_limites')->whereId($credito_cuantitativa_control_limites->id)->update([
              'fecha' => Carbon::now(),
              'vinculacion_deudor' => $request->input('vinculacion_deudor'),
              
              'cliente_saldo_vigente_cliente_det' => $request->input('cliente_saldo_vigente_cliente_det') ? $request->input('cliente_saldo_vigente_cliente_det') : '',
              'cliente_saldo_vigente_aval_det' => $request->input('cliente_saldo_vigente_aval_det') ? $request->input('cliente_saldo_vigente_aval_det') : '',
              
              'credito_saldodeduda_cliente_propio_det' => $request->input('credito_saldodeduda_cliente_propio_det') ? $request->input('credito_saldodeduda_cliente_propio_det') : '',
              'credito_saldodeduda_cliente_aval_det' => $request->input('credito_saldodeduda_cliente_aval_det') ? $request->input('credito_saldodeduda_cliente_aval_det') : '',
              'credito_saldodeduda_aval_propio_det' => $request->input('credito_saldodeduda_aval_propio_det') ? $request->input('credito_saldodeduda_aval_propio_det') : '',
              'credito_saldodeduda_aval_aval_det' => $request->input('credito_saldodeduda_aval_aval_det') ? $request->input('credito_saldodeduda_aval_aval_det') : '',
              
              'total_garantia_cliente' => $request->input('total_garantia_cliente'),
              'cantidad_garante_natural' => $request->input('cantidad_garante_natural') ? $request->input('cantidad_garante_natural') : 0,
              'cantidad_garante_juridico' => $request->input('cantidad_garante_juridico') ? $request->input('cantidad_garante_juridico') : 0,
              'cantidad_pareja_natural' => $request->input('cantidad_pareja_natural') ? $request->input('cantidad_pareja_natural') : 0,
              'cantidad_pareja_juridico' => $request->input('cantidad_pareja_juridico') ? $request->input('cantidad_pareja_juridico') : 0,
              'total_deuda' => $request->input('total_deuda') ? $request->input('total_deuda') : 0,
              'total_garantia_aval' => $request->input('total_garantia_aval'),
              'total_vinculo_deudor' => $request->input('total_vinculo_deudor'),
              'comentarios' => $request->input('comentarios'),
              
              
              'saldo_noprendario_cliente' => $request->input('saldo_noprendario_cliente') ? $request->input('saldo_noprendario_cliente') : 0,
              'propuesta_noprendario_cliente' => $request->input('propuesta_noprendario_cliente') ? $request->input('propuesta_noprendario_cliente') : 0,
              'saldo_noprendario_aval' => $request->input('saldo_noprendario_aval') ? $request->input('saldo_noprendario_aval') : 0,
              'propuesta_noprendario_aval' => $request->input('propuesta_noprendario_aval') ? $request->input('propuesta_noprendario_aval') : 0,
              
              'reporte_institucional' => $request->input('reporte_institucional'),
              'capital_asignado' => $request->input('capital_asignado'),
              
              'total_financiado_deudor' => $request->input('total_financiado_deudor'),
              'porcentaje_resultado' => $request->input('porcentaje_resultado'),
              'estado_resultado' => $request->input('estado_resultado'),
            ]);
          }else{
            DB::table('credito_cuantitativa_control_limites')->insert([
              'fecha' => Carbon::now(),
              'idcredito' => $id,
              'vinculacion_deudor' => $request->input('vinculacion_deudor'),
              
              'cliente_saldo_vigente_cliente_det' => $request->input('cliente_saldo_vigente_cliente_det') ? $request->input('cliente_saldo_vigente_cliente_det') : '',
              'cliente_saldo_vigente_aval_det' => $request->input('cliente_saldo_vigente_aval_det') ? $request->input('cliente_saldo_vigente_aval_det') : '',
              
              'credito_saldodeduda_cliente_propio_det' => $request->input('credito_saldodeduda_cliente_propio_det') ? $request->input('credito_saldodeduda_cliente_propio_det') : '',
              'credito_saldodeduda_cliente_aval_det' => $request->input('credito_saldodeduda_cliente_aval_det') ? $request->input('credito_saldodeduda_cliente_aval_det') : '',
              'credito_saldodeduda_aval_propio_det' => $request->input('credito_saldodeduda_aval_propio_det') ? $request->input('credito_saldodeduda_aval_propio_det') : '',
              'credito_saldodeduda_aval_aval_det' => $request->input('credito_saldodeduda_aval_aval_det') ? $request->input('credito_saldodeduda_aval_aval_det') : '',
              
              'total_saldodeuda_cliente_propio' => $request->input('total_saldodeuda_cliente_propio') ? $request->input('total_saldodeuda_cliente_propio') : '',
              'total_saldodeuda_cliente_aval' => $request->input('total_saldodeuda_cliente_aval') ? $request->input('total_saldodeuda_cliente_aval') : '',
              'total_saldodeuda_aval_propio' => $request->input('total_saldodeuda_aval_propio') ? $request->input('total_saldodeuda_aval_propio') : '',
              'total_saldodeuda_aval_aval' => $request->input('total_saldodeuda_aval_aval') ? $request->input('total_saldodeuda_aval_aval') : '',
              
              'total_garantia_cliente' => $request->input('total_garantia_cliente'),
              'cantidad_garante_natural' => $request->input('cantidad_garante_natural') ? $request->input('cantidad_garante_natural') : 0,
              'cantidad_garante_juridico' => $request->input('cantidad_garante_juridico') ? $request->input('cantidad_garante_juridico') : 0,
              'cantidad_pareja_natural' => $request->input('cantidad_pareja_natural') ? $request->input('cantidad_pareja_natural') : 0,
              'cantidad_pareja_juridico' => $request->input('cantidad_pareja_juridico') ? $request->input('cantidad_pareja_juridico') : 0,
              'total_deuda' => $request->input('total_deuda') ? $request->input('total_deuda') : 0,
              'total_garantia_aval' => $request->input('total_garantia_aval'),
              'total_vinculo_deudor' => $request->input('total_vinculo_deudor'),
              'comentarios' => $request->input('comentarios'),
              
              'saldo_noprendario_cliente' => $request->input('saldo_noprendario_cliente') ? $request->input('saldo_noprendario_cliente') : 0,
              'propuesta_noprendario_cliente' => $request->input('propuesta_noprendario_cliente') ? $request->input('propuesta_noprendario_cliente') : 0,
              'saldo_noprendario_aval' => $request->input('saldo_noprendario_aval') ? $request->input('saldo_noprendario_aval') : 0,
              'propuesta_noprendario_aval' => $request->input('propuesta_noprendario_aval') ? $request->input('propuesta_noprendario_aval') : 0,
              
              'reporte_institucional' => $request->input('reporte_institucional'),
              'capital_asignado' => $request->input('capital_asignado'),
              
              'total_financiado_deudor' => $request->input('total_financiado_deudor'),
              'porcentaje_resultado' => $request->input('porcentaje_resultado'),
              'estado_resultado' => $request->input('estado_resultado'),
              
            ]);
          }
          DB::table('credito')->whereId($id)->update([
            'fecha' => Carbon::now(),
          ]);
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha actualizado correctamente.'
          ]);
        }
        else if( $request->input('view') == 'ingresos_adicionales' ){
          $credito_cuantitativa_ingreso_adicional = DB::table('credito_cuantitativa_ingreso_adicional')->where('credito_cuantitativa_ingreso_adicional.idcredito',$id)->first();
          
          
          if($credito_cuantitativa_ingreso_adicional){
            DB::table('credito_cuantitativa_ingreso_adicional')->whereId($credito_cuantitativa_ingreso_adicional->id)->update([
              'fecha' => Carbon::now(),
              'idtipo_giro_economico_adiccional' => $request->input('idtipo_giro_economico_adiccional') ? $request->input('idtipo_giro_economico_adiccional') : 0,
              'idgiro_economico_evaluacion_adicional' => $request->input('idgiro_economico_evaluacion_adicional') ? $request->input('idgiro_economico_evaluacion_adicional') : 0,
              'evaluacion_meses' => $request->input('evaluacion_meses'),
              'margen_venta_calculado' => $request->input('margen_venta_calculado'),
              'productos' => $request->input('productos'),
              'total_venta' => $request->input('total_venta'),
              'total_compra' => $request->input('total_compra'),
              'porcentaje_margen' => $request->input('porcentaje_margen'),
              'frecuencia_ventas' => $request->input('frecuencia_ventas'),
              'dias' => $request->input('dias'),
              'venta_total_dias' => $request->input('venta_total_dias'),
              'numero_dias' => $request->input('numero_dias'),
              
              'venta_mensual' => $request->input('venta_mensual'),
              'recabo_dato_numero' => $request->input('recabo_dato_numero'),
              'recabo_dato_dia' => $request->input('recabo_dato_dia'),
              'recabo_dato_monto' => $request->input('recabo_dato_monto') ? $request->input('recabo_dato_monto') : 0,
              'estado_muestra' => $request->input('estado_muestra'),
              'margen_ventas' => $request->input('margen_ventas'),
              'margen_ventas_mensual' => $request->input('margen_ventas_mensual'),
              'subproducto' => $request->input('subproducto'),
              'productos_mensual' => $request->input('productos_mensual'),
              'total_venta_mensual' => $request->input('total_venta_mensual'),
              'total_compra_mensual' => $request->input('total_compra_mensual'),
              'porcentaje_margen_mensual' => $request->input('porcentaje_margen_mensual'),
              'semanas' => $request->input('semanas'),
              'venta_total_mensual' => $request->input('venta_total_mensual'),
              'estado_muestra_mensual' => $request->input('estado_muestra_mensual'),
              'subproductomensual' => $request->input('subproductomensual'),
              
              'inventario' => $request->input('inventario'),
              'total_inventario' => $request->input('total-inventario-producto'),
              'inmuebles' => $request->input('inmuebles'),
              'total_inmuebles' => $request->input('total-activos-inmuebles'),
              'muebles' => $request->input('muebles'),
              'total_muebles' => $request->input('total-activos-muebles'),
              
              'balance_general' => $request->input('balance_general'),
              'ganancias_perdidas' => $request->input('ganancias_perdidas'),
              
              'dias_ventas_mensual' => $request->input('dias_ventas_mensual')!=''?$request->input('dias_ventas_mensual'):0,
              'dias_compras_mensual' => $request->input('dias_compras_mensual')!=''?$request->input('dias_compras_mensual'):0,
              
              'credito_cobrando_venta_mensual' => $request->input('credito_cobrando_venta_mensual'),
              'credito_porcentaje_venta_mensual' => $request->input('credito_porcentaje_venta_mensual'),
              'contado_cobrando_venta_mensual' => $request->input('contado_cobrando_venta_mensual'),
              'contado_porcentaje_venta_mensual' => $request->input('contado_porcentaje_venta_mensual'),
              'credito_cobrando_compra_mensual' => $request->input('credito_cobrando_compra_mensual'),
              'credito_porcentaje_compra_mensual' => $request->input('credito_porcentaje_compra_mensual'),
              'contado_cobrando_compra_mensual' => $request->input('contado_cobrando_compra_mensual'),
              'contado_porcentaje_compra_mensual' => $request->input('contado_porcentaje_compra_mensual'),
              'adicional_fijo' => $request->input('adicional_fijo'),
              'total_ingreso_adicional' => $request->input('total_ingreso_adicional'),
              'comentario' => $request->input('comentario'),
              'cantidad_update' => $credito_cuantitativa_ingreso_adicional->cantidad_update + 1,
             
            ]);
          }else{
            DB::table('credito_cuantitativa_ingreso_adicional')->insert([
              'idcredito' => $id,
              'fecha' => Carbon::now(),
              'idtipo_giro_economico_adiccional' => $request->input('idtipo_giro_economico_adiccional') ? $request->input('idtipo_giro_economico_adiccional') : 0,
              'idgiro_economico_evaluacion_adicional' => $request->input('idgiro_economico_evaluacion_adicional') ? $request->input('idgiro_economico_evaluacion_adicional') : 0,
              'evaluacion_meses' => $request->input('evaluacion_meses'),
              'margen_venta_calculado' => $request->input('margen_venta_calculado'),
              'productos' => $request->input('productos'),
              'total_venta' => $request->input('total_venta'),
              'total_compra' => $request->input('total_compra'),
              'porcentaje_margen' => $request->input('porcentaje_margen'),
              'frecuencia_ventas' => $request->input('frecuencia_ventas'),
              'dias' => $request->input('dias'),
              'venta_total_dias' => $request->input('venta_total_dias'),
              'numero_dias' => $request->input('numero_dias'),
              
              'venta_mensual' => $request->input('venta_mensual'),
              'recabo_dato_numero' => $request->input('recabo_dato_numero'),
              'recabo_dato_dia' => $request->input('recabo_dato_dia'),
              'recabo_dato_monto' => $request->input('recabo_dato_monto') ? $request->input('recabo_dato_monto') : 0,
              'estado_muestra' => $request->input('estado_muestra'),
              'margen_ventas' => $request->input('margen_ventas'),
              'subproducto' => $request->input('subproducto'),
              'productos_mensual' => $request->input('productos_mensual'),
              'total_venta_mensual' => $request->input('total_venta_mensual'),
              'total_compra_mensual' => $request->input('total_compra_mensual'),
              'porcentaje_margen_mensual' => $request->input('porcentaje_margen_mensual'),
              'semanas' => $request->input('semanas'),
              'venta_total_mensual' => $request->input('venta_total_mensual'),
              'estado_muestra_mensual' => $request->input('estado_muestra_mensual'),
              'margen_ventas_mensual' => $request->input('margen_ventas_mensual'),
              'subproductomensual' => $request->input('subproductomensual'),
              
              'inventario' => $request->input('inventario'),
              'total_inventario' => $request->input('total-inventario-producto'),
              'inmuebles' => $request->input('inmuebles'),
              'total_inmuebles' => $request->input('total-activos-inmuebles'),
              'muebles' => $request->input('muebles'),
              'total_muebles' => $request->input('total-activos-muebles'),
              
              'balance_general' => $request->input('balance_general'),
              'ganancias_perdidas' => $request->input('ganancias_perdidas'),
              
              'dias_ventas_mensual' => $request->input('dias_ventas_mensual')!=''?$request->input('dias_ventas_mensual'):0,
              'dias_compras_mensual' => $request->input('dias_compras_mensual')!=''?$request->input('dias_compras_mensual'):0,
              
              'credito_cobrando_venta_mensual' => $request->input('credito_cobrando_venta_mensual'),
              'credito_porcentaje_venta_mensual' => $request->input('credito_porcentaje_venta_mensual'),
              'contado_cobrando_venta_mensual' => $request->input('contado_cobrando_venta_mensual'),
              'contado_porcentaje_venta_mensual' => $request->input('contado_porcentaje_venta_mensual'),
              'credito_cobrando_compra_mensual' => $request->input('credito_cobrando_compra_mensual'),
              'credito_porcentaje_compra_mensual' => $request->input('credito_porcentaje_compra_mensual'),
              'contado_cobrando_compra_mensual' => $request->input('contado_cobrando_compra_mensual'),
              'contado_porcentaje_compra_mensual' => $request->input('contado_porcentaje_compra_mensual'),
              'adicional_fijo' => $request->input('adicional_fijo'),
              'total_ingreso_adicional' => $request->input('total_ingreso_adicional'),
              'comentario' => $request->input('comentario'),
            ]);
          }
          DB::table('credito')->whereId($id)->update([
            'fecha' => Carbon::now(),
          ]);
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha actualizado correctamente.'
          ]);
        }
        else if( $request->input('view') == 'margen_ventas'){
          $credito_cuantitativa_margen_venta = DB::table('credito_cuantitativa_margen_venta')->where('credito_cuantitativa_margen_venta.idcredito',$id)->first();
          
          /*if($request->estado_error_margen_venta=='ERROR'){
            
              return response()->json([
                'resultado' => 'ERROR',
                'mensaje'   => 'EL MARGEN DE VENTA CALCULADO NO PUEDE SER SUPERIOR AL DEL GIRO ECONÓMICO.'
              ]);
          }*/
          
          if($credito_cuantitativa_margen_venta){
            DB::table('credito_cuantitativa_margen_venta')->whereId($credito_cuantitativa_margen_venta->id)->update([
              'fecha' => Carbon::now(),
              'tipo_registro' => $request->input('tipo_registro'),
              'productos' => $request->input('productos'),
              'total_venta' => $request->input('total_venta'),
              'total_compra' => $request->input('total_compra'),
              'porcentaje_margen' => $request->input('porcentaje_margen'),
              'frecuencia_ventas' => $request->input('frecuencia_ventas'),
              'dias' => $request->input('dias'),
              'venta_total_dias' => $request->input('venta_total_dias'),
              'numero_dias' => $request->input('numero_dias'),
              
              'venta_mensual' => $request->input('venta_mensual'),
              'recabo_dato_numero' => $request->input('recabo_dato_numero'),
              'recabo_dato_dia' => $request->input('recabo_dato_dia'),
              'recabo_dato_monto' => $request->input('recabo_dato_monto'),
              'estado_muestra' => $request->input('estado_muestra'),
              'margen_ventas' => $request->input('margen_ventas'),
              'subproducto' => $request->input('subproducto'),
              'productos_mensual' => $request->input('productos_mensual'),
              'total_venta_mensual' => $request->input('total_venta_mensual'),
              'total_compra_mensual' => $request->input('total_compra_mensual'),
              'porcentaje_margen_mensual' => $request->input('porcentaje_margen_mensual'),
              'semanas' => $request->input('semanas'),
              'venta_total_mensual' => $request->input('venta_total_mensual'),
              'estado_muestra_mensual' => $request->input('estado_muestra_mensual'),
              'margen_ventas_mensual' => $request->input('margen_ventas_mensual'),
              'subproductomensual' => $request->input('subproductomensual'),
              'cantidad_update' => $credito_cuantitativa_margen_venta->cantidad_update + 1,
              'margen_venta_calculado' => $request->input('margen_venta_calculado'),
            ]);
            /*DB::table('credito_evaluacion_cuantitativa')->where('credito_evaluacion_cuantitativa.idcredito', $id)->update([
              'margen_venta_calculado' => $request->input('margen_venta_calculado'),
            ]);*/
          }else{
            DB::table('credito_cuantitativa_margen_venta')->insert([
              'fecha' => Carbon::now(),
              'idcredito' => $id,
              'tipo_registro' => $request->input('tipo_registro'),
              'productos' => $request->input('productos'),
              'total_venta' => $request->input('total_venta'),
              'total_compra' => $request->input('total_compra'),
              'porcentaje_margen' => $request->input('porcentaje_margen'),
              'frecuencia_ventas' => $request->input('frecuencia_ventas'),
              'dias' => $request->input('dias'),
              'venta_total_dias' => $request->input('venta_total_dias'),
              'numero_dias' => $request->input('numero_dias'),
              
              'venta_mensual' => $request->input('venta_mensual'),
              'recabo_dato_numero' => $request->input('recabo_dato_numero'),
              'recabo_dato_dia' => $request->input('recabo_dato_dia'),
              'recabo_dato_monto' => $request->input('recabo_dato_monto'),
              'estado_muestra' => $request->input('estado_muestra'),
              'margen_ventas' => $request->input('margen_ventas'),
              'subproducto' => $request->input('subproducto'),
              'productos_mensual' => $request->input('productos_mensual'),
              'total_venta_mensual' => $request->input('total_venta_mensual'),
              'total_compra_mensual' => $request->input('total_compra_mensual'),
              'porcentaje_margen_mensual' => $request->input('porcentaje_margen_mensual'),
              'semanas' => $request->input('semanas'),
              'venta_total_mensual' => $request->input('venta_total_mensual'),
              'estado_muestra_mensual' => $request->input('estado_muestra_mensual'),
              'margen_ventas_mensual' => $request->input('margen_ventas_mensual'),
              'subproductomensual' => $request->input('subproductomensual'),
              'margen_venta_calculado' => $request->input('margen_venta_calculado'),
            ]);
          }
          DB::table('credito')->whereId($id)->update([
            'fecha' => Carbon::now(),
          ]);
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha actualizado correctamente.'
          ]);
        }
        else if( $request->input('view') == 'inventario_activos' ){
          
          $credito_cuantitativa_inventario = DB::table('credito_cuantitativa_inventario')->where('credito_cuantitativa_inventario.idcredito',$id)->first();
          
          
          if($credito_cuantitativa_inventario){
            DB::table('credito_cuantitativa_inventario')->whereId($credito_cuantitativa_inventario->id)->update([
              'fecha' => Carbon::now(),
              'inventario' => $request->input('inventario'),
              'total_inventario' => $request->input('total-inventario-producto'),
              'inmuebles' => $request->input('inmuebles'),
              'total_inmuebles' => $request->input('total-activos-inmuebles'),
              'muebles' => $request->input('muebles'),
              'total_muebles' => $request->input('total-activos-muebles'),
            ]);
          }else{
            DB::table('credito_cuantitativa_inventario')->insert([
              'fecha' => Carbon::now(),
              'idcredito' => $id,
              'inventario' => $request->input('inventario'),
              'total_inventario' => $request->input('total-inventario-producto'),
              'inmuebles' => $request->input('inmuebles'),
              'total_inmuebles' => $request->input('total-activos-inmuebles'),
              'muebles' => $request->input('muebles'),
              'total_muebles' => $request->input('total-activos-muebles'),
            ]);
          }
          DB::table('credito')->whereId($id)->update([
            'fecha' => Carbon::now(),
          ]);
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha actualizado correctamente.'
          ]);
        }
        else if( $request->input('view') == 'evaluacion_resumida' ){
          $rules = [
              'idtipo_giro_economico' => 'required',                      
              'idgiro_economico_evaluacion' => 'required',                      
          ];

          $messages = [
              'idtipo_giro_economico.required' => 'El Campo es Obligatorio.',
              'idgiro_economico_evaluacion.required' => 'El Campo es Obligatorio.',
          ];
          $this->validate($request,$rules,$messages);
          $credito_evaluacion_resumida = DB::table('credito_evaluacion_resumida')->where('credito_evaluacion_resumida.idcredito',$id)->first();
          
          
          if($credito_evaluacion_resumida){
            DB::table('credito_evaluacion_resumida')->whereId($credito_evaluacion_resumida->id)->update([
              'fecha' => Carbon::now(),
              'descripcion_actividad' => $request->input('descripcion_actividad'),
              'idtipo_giro_economico' => $request->input('idtipo_giro_economico'),
              'idgiro_economico_evaluacion' => $request->input('idgiro_economico_evaluacion'),
              'ejercicio_giro_economico' => $request->input('ejercicio_giro_economico'),
              
              'cantidad_cliente_natural' => $request->input('cantidad_cliente_natural'),
              'cantidad_cliente_juridico' => $request->input('cantidad_cliente_juridico'),
              'cantidad_pareja_natural' => $request->input('cantidad_pareja_natural'),
              'cantidad_pareja_juridico' => $request->input('cantidad_pareja_juridico'),
              'total_deuda' => $request->input('total_deuda'),
              
              'cantidad_garante_natural' => $request->input('cantidad_garante_natural'),
              'cantidad_garante_juridico' => $request->input('cantidad_garante_juridico'),
              'cantidad_garante_pareja_natural' => $request->input('cantidad_garante_pareja_natural'),
              'cantidad_garante_pareja_juridico' => $request->input('cantidad_garante_pareja_juridico'),
              'total_deuda_garante' => $request->input('total_deuda_garante'),
              
              'experiencia_microempresa' => $request->input('experiencia_microempresa'),
              'tiempo_mismo_local' => $request->input('tiempo_mismo_local'),
              'instalacion_local' => $request->input('instalacion_local'),
              'nro_trabajador_completo' => $request->input('nro_trabajador_completo'),
              'nro_trabajador_parcal' => $request->input('nro_trabajador_parcal'),
              
              'referencia' => $request->input('referencia'),
              
              'venta_diaria' => $request->input('dias'),
              'venta_total_dias' => $request->input('venta_total_dias'),
              'venta_semanal' => $request->input('semanas'),
              'venta_total_mensual' => $request->input('venta_total_mensual'),
              
              'ingresos_gastos' => $request->input('ingresos_gastos'),
              'ingresos_op_total' => $request->input('ingresos_op_total'),
              
              'gasto_alimentacion' => $request->input('gasto_alimentacion'),
              'gasto_educacion' => $request->input('gasto_educacion'),
              'gasto_vestimenta' => $request->input('gasto_vestimenta'),
              'gasto_transporte' => $request->input('gasto_transporte'),
              'gasto_salud' => $request->input('gasto_salud'),
              'gasto_vivienda' => $request->input('gasto_vivienda'),
              'total_servicios' => $request->input('total_servicios'),
              'gasto_agua' => $request->input('gasto_agua'),
              'gasto_luz' => $request->input('gasto_luz'),
              'gasto_telefono_internet' => $request->input('gasto_telefono_internet'),
              'gasto_celular' => $request->input('gasto_celular'),
              'gasto_cable' => $request->input('gasto_cable'),
              'gasto_otros' => $request->input('gasto_otros'),
              'gasto_total' => $request->input('gasto_total'),
              
              'idforma_pago_credito' => $request->input('idforma_pago_credito'),
              'propuesta_cuotas' => $request->input('propuesta_cuotas'),
              'propuesta_monto' => $request->input('propuesta_monto'),
              'propuesta_tem' => $request->input('propuesta_tem'),
              
              'propuesta_servicio_otros' => $request->input('propuesta_servicio_otros'),
              'propuesta_cargos' => $request->input('propuesta_cargos'),
              'propuesta_total_pagar' => $request->input('propuesta_total_pagar'),
              'total_propuesta' => $request->input('total_propuesta'),
              
              'detalle_destino_prestamo' => $request->input('detalle_destino_prestamo'),
              'fortalezas_negocio' => $request->input('fortalezas_negocio'),
              
              'relacion_cuota_venta_diaria' => $request->input('relacion_cuota_venta_diaria'),
              'relacion_cuota_venta_semanal' => $request->input('relacion_cuota_venta_semanal'),
              'relacion_cuota_venta_quincenal' => $request->input('relacion_cuota_venta_quincenal'),
              'relacion_cuota_venta_mensual' => $request->input('relacion_cuota_venta_mensual'),
              
              'estado_indicador_solvencia' => $request->input('estado_indicador_solvencia'),
              'estado_indicador_cuota_ingreso' => $request->input('estado_indicador_cuota_ingreso'),
              'estado_indicador_cuota_venta_diario' => $request->input('estado_indicador_cuota_venta_diario'),
              'estado_indicador_cuota_venta_semanal' => $request->input('estado_indicador_cuota_venta_semanal'),
              'estado_indicador_cuota_venta_quincenal' => $request->input('estado_indicador_cuota_venta_quincenal'),
              'estado_indicador_cuota_venta_mensual' => $request->input('estado_indicador_cuota_venta_mensual'),
              'estado_credito_general' => $request->input('estado_credito_general'),
              
              'indicador_solvencia_excedente' => $request->input('indicador_solvencia_excedente'),
              'indicador_solvencia_cuotas' => $request->input('indicador_solvencia_cuotas'),
              'relacion_cuota_mensual' => $request->input('relacion_cuota_mensual'),
              'cantidad_update' => $credito_evaluacion_resumida->cantidad_update + 1,
              
            ]);
          }else{
            DB::table('credito_evaluacion_resumida')->insert([
              'idcredito' => $id,
              'fecha' => Carbon::now(),
              'descripcion_actividad' => $request->input('descripcion_actividad'),
              'idtipo_giro_economico' => $request->input('idtipo_giro_economico'),
              'idgiro_economico_evaluacion' => $request->input('idgiro_economico_evaluacion'),
              'ejercicio_giro_economico' => $request->input('ejercicio_giro_economico'),
              
              'cantidad_cliente_natural' => $request->input('cantidad_cliente_natural'),
              'cantidad_cliente_juridico' => $request->input('cantidad_cliente_juridico'),
              'cantidad_pareja_natural' => $request->input('cantidad_pareja_natural'),
              'cantidad_pareja_juridico' => $request->input('cantidad_pareja_juridico'),
              'total_deuda' => $request->input('total_deuda'),
              
              'cantidad_garante_natural' => $request->input('cantidad_garante_natural'),
              'cantidad_garante_juridico' => $request->input('cantidad_garante_juridico'),
              'cantidad_garante_pareja_natural' => $request->input('cantidad_garante_pareja_natural'),
              'cantidad_garante_pareja_juridico' => $request->input('cantidad_garante_pareja_juridico'),
              'total_deuda_garante' => $request->input('total_deuda_garante'),
              
              'experiencia_microempresa' => $request->input('experiencia_microempresa'),
              'tiempo_mismo_local' => $request->input('tiempo_mismo_local'),
              'instalacion_local' => $request->input('instalacion_local'),
              'nro_trabajador_completo' => $request->input('nro_trabajador_completo'),
              'nro_trabajador_parcal' => $request->input('nro_trabajador_parcal'),
              
              'referencia' => $request->input('referencia'),
              
              'venta_diaria' => $request->input('dias'),
              'venta_total_dias' => $request->input('venta_total_dias'),
              'venta_semanal' => $request->input('semanas'),
              'venta_total_mensual' => $request->input('venta_total_mensual'),
              
              'ingresos_gastos' => $request->input('ingresos_gastos'),
              'ingresos_op_total' => $request->input('ingresos_op_total'),
              
              'gasto_alimentacion' => $request->input('gasto_alimentacion'),
              'gasto_educacion' => $request->input('gasto_educacion'),
              'gasto_vestimenta' => $request->input('gasto_vestimenta'),
              'gasto_transporte' => $request->input('gasto_transporte'),
              'gasto_salud' => $request->input('gasto_salud'),
              'gasto_vivienda' => $request->input('gasto_vivienda'),
              'total_servicios' => $request->input('total_servicios'),
              'gasto_agua' => $request->input('gasto_agua'),
              'gasto_luz' => $request->input('gasto_luz'),
              'gasto_telefono_internet' => $request->input('gasto_telefono_internet'),
              'gasto_celular' => $request->input('gasto_celular'),
              'gasto_cable' => $request->input('gasto_cable'),
              'gasto_otros' => $request->input('gasto_otros'),
              'gasto_total' => $request->input('gasto_total'),
              
              'idforma_pago_credito' => $request->input('idforma_pago_credito'),
              'propuesta_cuotas' => $request->input('propuesta_cuotas'),
              'propuesta_monto' => $request->input('propuesta_monto'),
              'propuesta_tem' => $request->input('propuesta_tem'),
              
              'propuesta_servicio_otros' => $request->input('propuesta_servicio_otros'),
              'propuesta_cargos' => $request->input('propuesta_cargos'),
              'propuesta_total_pagar' => $request->input('propuesta_total_pagar'),
              'total_propuesta' => $request->input('total_propuesta'),
              
              'detalle_destino_prestamo' => $request->input('detalle_destino_prestamo'),
              'fortalezas_negocio' => $request->input('fortalezas_negocio'),
              
              'relacion_cuota_venta_diaria' => $request->input('relacion_cuota_venta_diaria'),
              'relacion_cuota_venta_semanal' => $request->input('relacion_cuota_venta_semanal'),
              'relacion_cuota_venta_quincenal' => $request->input('relacion_cuota_venta_quincenal'),
              'relacion_cuota_venta_mensual' => $request->input('relacion_cuota_venta_mensual'),
              
              'estado_indicador_solvencia' => $request->input('estado_indicador_solvencia'),
              'estado_indicador_cuota_ingreso' => $request->input('estado_indicador_cuota_ingreso'),
              'estado_indicador_cuota_venta_diario' => $request->input('estado_indicador_cuota_venta_diario'),
              'estado_indicador_cuota_venta_semanal' => $request->input('estado_indicador_cuota_venta_semanal'),
              'estado_indicador_cuota_venta_quincenal' => $request->input('estado_indicador_cuota_venta_quincenal'),
              'estado_indicador_cuota_venta_mensual' => $request->input('estado_indicador_cuota_venta_mensual'),
              'estado_credito_general' => $request->input('estado_credito_general'),
              
              'indicador_solvencia_excedente' => $request->input('indicador_solvencia_excedente'),
              'indicador_solvencia_cuotas' => $request->input('indicador_solvencia_cuotas'),
              'relacion_cuota_mensual' => $request->input('relacion_cuota_mensual'),
            ]);
          }
          DB::table('credito')->whereId($id)->update([
            'fecha' => Carbon::now(),
          ]);
          
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha actualizado correctamente.'
          ]);
        }
        else if( $request->input('view') == 'flujo_caja' ){
             
          $credito_flujo_caja = DB::table('credito_flujo_caja')->where('credito_flujo_caja.idcredito',$id)->first();
          if($credito_flujo_caja){
            DB::table('credito_flujo_caja')->whereId($credito_flujo_caja->id)->update([
              'fecha' => Carbon::now(),
              'encabezado' => $request->input('encabezado'),
              'evaluacion_meses' => $request->input('evaluacion_meses'),
              'flujo_caja' => $request->input('json_flujo_caja'),
              'entidad_reguladas' => $request->input('entidad_regulada'),
              'linea_credito' => $request->input('linea_credito'),
              'entidad_noregulada' => $request->input('entidad_noregulada'),
              'comentarios' => $request->input('comentarios'),
              
            ]);
          }else{
            DB::table('credito_flujo_caja')->insert([
              'idcredito' => $id,
              'fecha' => Carbon::now(),
              'encabezado' => $request->input('encabezado'),
              'evaluacion_meses' => $request->input('evaluacion_meses'),
              'flujo_caja' => $request->input('json_flujo_caja'),
              'entidad_reguladas' => $request->input('entidad_regulada'),
              'linea_credito' => $request->input('linea_credito'),
              'entidad_noregulada' => $request->input('entidad_noregulada'),
              'comentarios' => $request->input('comentarios'),
            ]);
          }
          
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha actualizado correctamente.'
          ]);
        }
        else if( $request->input('view') == 'formato_evaluacion'){
          $credito_formato_evaluacion = DB::table('credito_formato_evaluacion')->where('credito_formato_evaluacion.idcredito',$id)->first();
          if($credito_formato_evaluacion){
            DB::table('credito_formato_evaluacion')->whereId($credito_formato_evaluacion->id)->update([
              'fecha' => Carbon::now(),
              'remuneracion_total_cliente' => $request->input('remuneracion_total_cliente'),
              'remuneracion_variable' => $request->input('remuneracion_variable'),
              'remuneracion_pareja' => $request->input('remuneracion_pareja'),
              'adicional_ingreso_mensual' => $request->input('adicional_ingreso_mensual'),
              'total_ingresos_mensuales' => $request->input('total_ingresos_mensuales'),
              'numero_total_hijos' => $request->input('numero_total_hijos'),
              'total_hijos_dependientes' => $request->input('total_hijos_dependientes'),
              
              'pago_cuotas_deuda' => $request->input('pago_cuotas_deuda'),
              'monto_alimentacion' => $request->input('monto_alimentacion'),
              'monto_salud' => $request->input('monto_salud'),
              'monto_educacion' => $request->input('monto_educacion'),
              'monto_alquiler_vivienda' => $request->input('monto_alquiler_vivienda'),
              'monto_mobilidad' => $request->input('monto_mobilidad'),
              'monto_luz' => $request->input('monto_luz'),
              'monto_agua' => $request->input('monto_agua'),
              'monto_telefono' => $request->input('monto_telefono'),
              'monto_cable' => $request->input('monto_cable'),
              'otros_gastos_personales' => $request->input('otros_gastos_personales'),
              'monto_pension_alimentos' => $request->input('monto_pension_alimentos'),
              'adicional_egresos_mensual' => $request->input('adicional_egresos_mensual'),
              'total_egresos_mensuales' => $request->input('total_egresos_mensuales'),
              'excedente_mensual_disponible' => $request->input('excedente_mensual_disponible'),
              
              'deudas_financieras' => $request->input('deudas_financieras'),
              'saldo_capita_cliente' => $request->input('saldo_capita_cliente'),
              'couta_mensual_cliente' => $request->input('couta_mensual_cliente'),
              'cuota_ampliacion_cliente' => $request->input('cuota_ampliacion_cliente'),
              'saldo_capita_pareja' => $request->input('saldo_capita_pareja'),
              'couta_mensual_pareja' => $request->input('couta_mensual_pareja'),
              'cuota_ampliacion_pareja' => $request->input('cuota_ampliacion_pareja'),
              'total_saldo_capital' => $request->input('total_saldo_capital'),
              'total_couta_mensual' => $request->input('total_couta_mensual'),
              'total_couta_ampliacion' => $request->input('total_couta_ampliacion'),
              'entidad_financiera_cliente' => $request->input('entidad_financiera_cliente'),
              'entidad_financiera_pareja' => $request->input('entidad_financiera_pareja'),
              'entidad_financiera_total' => $request->input('entidad_financiera_total'),
              
              'idforma_pago_credito' => $request->input('idforma_pago_credito'),
              'propuesta_cuotas' => $request->input('propuesta_cuotas'),
              'propuesta_monto' => $request->input('propuesta_monto'),
              'propuesta_tem' => $request->input('propuesta_tem'),
              'propuesta_servicio_otros' => $request->input('propuesta_servicio_otros'),
              'propuesta_cargos' => $request->input('propuesta_cargos'),
              'propuesta_total_pagar' => $request->input('propuesta_total_pagar'),
              'total_propuesta' => $request->input('total_propuesta'),
              
              'resultado_cuota_excedente' => $request->input('resultado_cuota_excedente'),
              'estado_evaluacion' => $request->input('estado_evaluacion'),
              
              'referencia' => $request->input('referencia'),
              
              'comentario_centro_laboral' => $request->input('comentario_centro_laboral'),
              'comentario_capacidad_pago' => $request->input('comentario_capacidad_pago'),
              'sustento_historial_pago' => $request->input('sustento_historial_pago'),
              'sustento_destino_credito' => $request->input('sustento_destino_credito'),
              
            ]);
          }else{
            DB::table('credito_formato_evaluacion')->insert([
              'idcredito' => $id,
              'fecha' => Carbon::now(),
              'remuneracion_total_cliente' => $request->input('remuneracion_total_cliente'),
              'remuneracion_variable' => $request->input('remuneracion_variable'),
              'remuneracion_pareja' => $request->input('remuneracion_pareja'),
              'adicional_ingreso_mensual' => $request->input('adicional_ingreso_mensual'),
              'total_ingresos_mensuales' => $request->input('total_ingresos_mensuales'),
              'numero_total_hijos' => $request->input('numero_total_hijos'),
              'total_hijos_dependientes' => $request->input('total_hijos_dependientes'),
              
              'pago_cuotas_deuda' => $request->input('pago_cuotas_deuda'),
              'monto_alimentacion' => $request->input('monto_alimentacion'),
              'monto_salud' => $request->input('monto_salud'),
              'monto_educacion' => $request->input('monto_educacion'),
              'monto_alquiler_vivienda' => $request->input('monto_alquiler_vivienda'),
              'monto_mobilidad' => $request->input('monto_mobilidad'),
              'monto_luz' => $request->input('monto_luz'),
              'monto_agua' => $request->input('monto_agua'),
              'monto_telefono' => $request->input('monto_telefono'),
              'monto_cable' => $request->input('monto_cable'),
              'otros_gastos_personales' => $request->input('otros_gastos_personales'),
              'monto_pension_alimentos' => $request->input('monto_pension_alimentos'),
              'adicional_egresos_mensual' => $request->input('adicional_egresos_mensual'),
              'total_egresos_mensuales' => $request->input('total_egresos_mensuales'),
              'excedente_mensual_disponible' => $request->input('excedente_mensual_disponible'),
              
              'deudas_financieras' => $request->input('deudas_financieras'),
              'saldo_capita_cliente' => $request->input('saldo_capita_cliente'),
              'couta_mensual_cliente' => $request->input('couta_mensual_cliente'),
              'cuota_ampliacion_cliente' => $request->input('cuota_ampliacion_cliente'),
              'saldo_capita_pareja' => $request->input('saldo_capita_pareja'),
              'couta_mensual_pareja' => $request->input('couta_mensual_pareja'),
              'cuota_ampliacion_pareja' => $request->input('cuota_ampliacion_pareja'),
              'total_saldo_capital' => $request->input('total_saldo_capital'),
              'total_couta_mensual' => $request->input('total_couta_mensual'),
              'total_couta_ampliacion' => $request->input('total_couta_ampliacion'),
              'entidad_financiera_cliente' => $request->input('entidad_financiera_cliente'),
              'entidad_financiera_pareja' => $request->input('entidad_financiera_pareja'),
              'entidad_financiera_total' => $request->input('entidad_financiera_total'),
              
              'idforma_pago_credito' => $request->input('idforma_pago_credito'),
              'propuesta_cuotas' => $request->input('propuesta_cuotas'),
              'propuesta_monto' => $request->input('propuesta_monto'),
              'propuesta_tem' => $request->input('propuesta_tem'),
              'propuesta_servicio_otros' => $request->input('propuesta_servicio_otros'),
              'propuesta_cargos' => $request->input('propuesta_cargos'),
              'propuesta_total_pagar' => $request->input('propuesta_total_pagar'),
              'total_propuesta' => $request->input('total_propuesta'),
              
              'resultado_cuota_excedente' => $request->input('resultado_cuota_excedente'),
              'estado_evaluacion' => $request->input('estado_evaluacion'),
              
              'referencia' => $request->input('referencia'),
              
              'comentario_centro_laboral' => $request->input('comentario_centro_laboral'),
              'comentario_capacidad_pago' => $request->input('comentario_capacidad_pago'),
              'sustento_historial_pago' => $request->input('sustento_historial_pago'),
              'sustento_destino_credito' => $request->input('sustento_destino_credito'),
            ]);
          }
          
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha actualizado correctamente.'
          ]);
        }
        else if( $request->input('view') == 'propuesta_credito' ){

          $credito = DB::table('credito')->whereId($id)->first();
          
          $saldo_prestamo_vigente_propio = DB::table('credito')
              ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
              ->where('credito.idcliente',$credito->idcliente)
              ->where('credito.estado','DESEMBOLSADO')
              ->where('credito.idestadocredito',1)
              ->select(
                  'credito.*',
                  'credito_prendatario.nombre as nombreproductocredito',
                  'credito_prendatario.modalidad as modalidadproductocredito',
              )
              ->distinct()
              ->get();
          
          if($credito->idmodalidad_credito == 2 && count(json_decode($request->input('monto_compra_deuda_det'))) == 0 && count($saldo_prestamo_vigente_propio)>0){
              return response()->json([
                'resultado' => 'ERROR',
                'mensaje'   => 'Es Obligatorio seleccionar mínimo una ampliación de deuda	.'
              ]);
          }
          
          $credito_propuesta = DB::table('credito_propuesta')->where('credito_propuesta.idcredito',$id)->first();
          
          if($credito_propuesta){
            DB::table('credito_propuesta')->whereId($credito_propuesta->id)->update([
              'fecha' => Carbon::now(),
              'monto_compra_deuda' => $request->input('monto_compra_deuda'),
              'monto_compra_deuda_det' => $request->input('monto_compra_deuda_det'),
              
              'idclasificacion_cliente' => $request->input('idclasificacion_cliente'),
              'idclasificacion_cliente_pareja' => $request->input('idclasificacion_cliente_pareja'),
              'idclasificacion_aval' => $request->input('idclasificacion_aval'),
              'idclasificacion_aval_pareja' => $request->input('idclasificacion_aval_pareja'),
              
              'detalle_monto_compra_deuda' => $request->input('detalle_monto_compra_deuda'),
              'neto_destino_credito' => $request->input('neto_destino_credito'),
              'fenomenos' => $request->input('fenomenos'),
              
              'rentabilidad_patrimonial_res_coment' => $request->input('rentabilidad_patrimonial_res_coment'),
              'rentabilidad_activos_res_coment' => $request->input('rentabilidad_activos_res_coment'),
              'solvencia_cuota_total_res_coment' => $request->input('solvencia_cuota_total_res_coment'),
              'solvencia_capital_trabajo_res_coment' => $request->input('solvencia_capital_trabajo_res_coment'),
              'limites_financiamiento_vru_res_coment' => $request->input('limites_financiamiento_vru_res_coment'),
              'limites_numero_entidades' => $request->input('limites_numero_entidades')!=null?$request->input('limites_numero_entidades'):0,
              'limites_numero_entidades_res' => $request->input('limites_numero_entidades_res'),
              'limites_numero_entidades_res_coment' => $request->input('limites_numero_entidades_res_coment'),
              'res_solvencia_relacion_cuota_coment' => $request->input('res_solvencia_relacion_cuota_coment'),
              'res_ratios_tendencia_comportamiento_res_coment' => $request->input('res_ratios_tendencia_comportamiento_res_coment'),
            ]);
          }else{
            DB::table('credito_propuesta')->insert([
              'idcredito' => $id,
              'fecha' => Carbon::now(),
              'monto_compra_deuda' => $request->input('monto_compra_deuda'),
              'monto_compra_deuda_det' => $request->input('monto_compra_deuda_det'),
              
              'idclasificacion_cliente' => $request->input('idclasificacion_cliente'),
              'idclasificacion_cliente_pareja' => $request->input('idclasificacion_cliente_pareja'),
              'idclasificacion_aval' => $request->input('idclasificacion_aval'),
              'idclasificacion_aval_pareja' => $request->input('idclasificacion_aval_pareja'),
              
              'detalle_monto_compra_deuda' => $request->input('detalle_monto_compra_deuda'),
              'neto_destino_credito' => $request->input('neto_destino_credito'),
              'fenomenos' => $request->input('fenomenos'),
              
              'rentabilidad_patrimonial_res_coment' => $request->input('rentabilidad_patrimonial_res_coment'),
              'rentabilidad_activos_res_coment' => $request->input('rentabilidad_activos_res_coment'),
              'solvencia_cuota_total_res_coment' => $request->input('solvencia_cuota_total_res_coment'),
              'solvencia_capital_trabajo_res_coment' => $request->input('solvencia_capital_trabajo_res_coment'),
              'limites_financiamiento_vru_res_coment' => $request->input('limites_financiamiento_vru_res_coment'),
              'limites_numero_entidades' => $request->input('limites_numero_entidades')!=null?$request->input('limites_numero_entidades'):0,
              'limites_numero_entidades_res' => $request->input('limites_numero_entidades_res'),
              'limites_numero_entidades_res_coment' => $request->input('limites_numero_entidades_res_coment'),
              'res_solvencia_relacion_cuota_coment' => $request->input('res_solvencia_relacion_cuota_coment'),
              'res_ratios_tendencia_comportamiento_res_coment' => $request->input('res_ratios_tendencia_comportamiento_res_coment'),
            ]);
          }
          
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha actualizado correctamente.'
          ]);
        }
        else if( $request->input('view') == 'aprobar_propuesta' ){
          
           DB::table('credito')->whereId($id)->update([
              'estado' => 'PROCESO',
              'fecha_proceso' => Carbon::now(),
              'cuenta' => 0,
              'config_dias_tolerancia' => configuracion($idtienda,'dias_tolerancia')['valor'],
              'config_dias_tolerancia_garantia' => configuracion($idtienda,'dias_tolerancia_garantia')['valor'],
              'config_dias_maximo_penalidad' => configuracion($idtienda,'dias_maximo_penalidad')['valor'],
              'config_penalidad_couta_simple' => configuracion($idtienda,'penalidad_couta_simple')['valor'],
              'config_penalidad_couta_compuesto' => configuracion($idtienda,'penalidad_couta_compuesto')['valor'],
              'config_penalidad_couta_simple_noprendaria' => configuracion($idtienda,'penalidad_couta_simple_noprendaria')['valor'],
              'config_penalidad_couta_compuesto_noprendaria' => configuracion($idtienda,'penalidad_couta_compuesto_noprendaria')['valor'],
              'config_tasa_moratoria' => configuracion($idtienda,'tasa_moratoria')['valor'],
            ]);
            
            //actualizar cronograma
             
            $credito = DB::table('credito')
                  ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                  ->where('credito.id',$id)
                  ->select(
                      'credito.*',
                      'credito_prendatario.modalidad as modalidad_calculo',
                  )
                  ->first();
              
            $tasatarifario = DB::table('tarifario')
                  ->where('tarifario.idcredito_prendatario',$credito->idcredito_prendatario)
                  ->where('tarifario.idforma_pago_credito',$credito->idforma_pago_credito)
                  ->where('tarifario.monto','>=',$credito->monto_solicitado)
                  ->where('tarifario.cuotas','>=',$credito->cuotas)
                  ->orderBy('tarifario.cuotas','asc')
                  ->orderBy('tarifario.monto','asc')
                  ->limit(1)
                  ->first();
          
            $comision_cargo = 0;
            if($tasatarifario!=''){
                $comision_cargo = $tasatarifario->cargos_otros;
            }
          
            $fechaactual = Carbon::now()->format('Y-m-d');
            $cronograma = genera_cronograma(
                  $credito->monto_solicitado,
                  $credito->cuotas,
                  $fechaactual,
                  $credito->idforma_pago_credito,
                  $credito->tasa_tip,
                  $credito->modalidad_calculo == 'Interes Simple' ? 1 : 2,
                  $credito->dia_gracia,
                  $comision_cargo,
                  $credito->cargo
            );
          
          
            DB::table('credito')->whereId($id)->update([
                'fecha'                     => $fechaactual,
                'cuota_pago'                => $cronograma['cuota_pago'],
                'fecha_primerpago'          => $cronograma['fechainicio'],
                'fecha_ultimopago'          => $cronograma['ultimafecha'],
                'total_propuesta'           => $cronograma['total_propuesta'],
                'cuota_comision'            => $cronograma['cuota_comision'],
                'cuota_cargo'               => $cronograma['cuota_cargo'],
                'cuota_comisioncargo'       => $cronograma['cuota_comisioncargo'],
                'total_comision'            => $cronograma['total_comision'],
                'total_cargo'               => $cronograma['total_cargo'],
                'total_comisioncargo'       => $cronograma['total_comisioncargo'],
            ]);
          
            DB::table('credito_cronograma')->where('idcredito',$id)->delete();

            foreach($cronograma['cronograma'] as $value){
                DB::table('credito_cronograma')->insert([
                  'numerocuota'     => $value['numero'],
                  'fechapago'       => $value['fechanormal'],
                  'capital'         => $value['saldo'],
                  'amortizacion'    => $value['amortizacion'],
                  'interes'         => $value['interes'],
                  'cuotapagar'      => 0,
                  'cuota_real'      => $value['cuotafinal'],
                  'resto_redondeo'  => 0,
                  'comision'        => $value['comision'],
                  'cargo'           => $value['cargo'],
                  'comision_cargo'  => $value['comisioncargo'],
                  'idestadocredito_cronograma' => 1,
                  'idcredito'       => $id,
                ]);
            }
          
            
           
            if($credito->idforma_credito==1){
                
                $cliente = DB::table('users')->whereId($credito->idcliente)->first();
                //depositario
                DB::table('credito')->whereId($credito->id)->update([
                  'custodiagarantia_id' => $cliente->custodiagarantia_id,
                  'custodiagarantia_nombre' => $cliente->custodiagarantia_nombre,
                  'gd_nombre' => $cliente->gd_nombre,
                  'gd_doeruc' => $cliente->gd_doeruc,
                  'gd_direccion' => $cliente->gd_direccion,
                  'gd_representante_doeruc' => $cliente->gd_representante_doeruc,
                  'gd_representante_nombre' => $cliente->gd_representante_nombre,
                  'constituciongarantia_id' => $cliente->constituciongarantia_id,
                  'constituciongarantia_nombre' => $cliente->constituciongarantia_nombre,
                ]);

                //poliza de seguros
                $credito_polizaseguro = DB::table('credito_polizaseguro')->where('id_cliente',$credito->idcliente)->get();
                DB::table('credito_polizaseguro_prestamo')->where('id_credito',$credito->id)->delete();
                foreach($credito_polizaseguro as $value){
                    DB::table('credito_polizaseguro_prestamo')
                        ->insert([
                            'numero_poliza' => $value->numero_poliza,
                            'aseguradora' => $value->aseguradora,
                            'prima_recio' => $value->prima_recio,
                            'beneficiario' => $value->beneficiario,
                            'asegurado' => $value->asegurado,
                            'tomador' => $value->tomador,
                            'vigencia_desde' => $value->vigencia_desde,
                            'vigencia_hasta' => $value->vigencia_hasta,
                            'id_credito' => $credito->id,
                        ]);
                }
                
                // representante comun
                $credito_representantecomun = DB::table('credito_representantecomun')->where('estado_id',1)->get();
                DB::table('credito_representantecomun_prestamo')->where('id_credito',$credito->id)->delete();
                foreach($credito_representantecomun as $value){
                    DB::table('credito_representantecomun_prestamo')
                        ->insert([
                            'nombre' => $value->nombre,
                            'doi' => $value->doi,
                            'direccion' => $value->direccion,
                            'ubigeo_id' => $value->ubigeo_id,
                            'ubigeo_nombre' => $value->ubigeo_nombre,
                            'estado_id' => $value->estado_id,
                            'estado_nombre' => $value->estado_nombre,
                            'id_credito' => $credito->id,
                        ]);
                }
              
            }else{

                DB::table('credito')->whereId($credito->id)->update([
                  'custodiagarantia_id' => 0,
                  'custodiagarantia_nombre' => '',
                  'gd_nombre' => '',
                  'gd_doeruc' => '',
                  'gd_direccion' => '',
                  'gd_representante_doeruc' => '',
                  'gd_representante_nombre' => '',
                  'constituciongarantia_id' => 0,
                  'constituciongarantia_nombre' => '',
                ]);
              
                DB::table('credito_polizaseguro_prestamo')->where('id_credito',$credito->id)->delete();
                DB::table('credito_representantecomun_prestamo')->where('id_credito',$credito->id)->delete();
            }
          
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
    
    }


    public function destroy(Request $request, $idtienda, $id)
    {
      
      if( $request->input('view') == 'eliminar' ){
        DB::table('credito')->whereId($id)->delete();
        DB::table('credito_garantia')->where('idcredito',$id)->delete();
        DB::table('credito_cronograma')->where('idcredito',$id)->delete();
       
        DB::table('credito_evaluacion_cualitativa')->where('idcredito',$id)->delete();
        DB::table('credito_evaluacion_cuantitativa')->where('idcredito',$id)->delete();
        DB::table('credito_cuantitativa_deudas')->where('idcredito',$id)->delete();
        DB::table('credito_cuantitativa_control_limites')->where('idcredito',$id)->delete();
        DB::table('credito_cuantitativa_ingreso_adicional')->where('idcredito',$id)->delete();
        DB::table('credito_cuantitativa_margen_venta')->where('idcredito',$id)->delete();
        DB::table('credito_evaluacion_resumida')->where('idcredito',$id)->delete();
        DB::table('credito_flujo_caja')->where('idcredito',$id)->delete();
        DB::table('credito_formato_evaluacion')->where('idcredito',$id)->delete();
        DB::table('credito_propuesta')->where('idcredito',$id)->delete();
        
        
        DB::table('credito_polizaseguro_prestamo')->where('id_credito',$id)->delete();
        
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha elimino correctamente.'
        ]);
      }
      
    
    }
}
