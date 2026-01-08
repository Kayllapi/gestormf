<?php
use Carbon\Carbon;
#######################################
############# NUEVO SISTEMA DE CREDITOS
#######################################
function genera_cronograma($montosolicitado,$numerocuota,$fechainicio,$frecuencia,$tasa,$tipotasa,$dia_gracia,$comision,$cargo){
 
        //dia de gracia
        $fechacero = $fechainicio;
        $fechainicio = date_create($fechainicio);
        date_add($fechainicio, date_interval_create_from_date_string($dia_gracia.' day'));
        $fechainicio = date_format($fechainicio, 'Y-m-d');
        //fecha inicio y fin
        $cronograma_fechainicio = '0000-00-00';
        $cronograma_ultimafecha = '0000-00-00';
        $cronograma_cuotapago = 0;

        $cronograma = [];

        $feriados = DB::table('feriados')->get();
        $db_frecuencia = DB::table('forma_pago_credito')->whereId($frecuencia)->first();

        $cuota_amortizacion = 0;
        $cuota_interes = 0;
        $cuota_comisioncargo = 0;
        $cuota_cargo = 0;
        $cuota = 0;
        $cuotafinal = 0;
        $total_amortizacion = number_format($montosolicitado, 2, '.', '');
        $total_interes = 0;
        $total_comisioncargo = 0;
        $total_cargo = 0;
        $total_cuota = 0;
        $total_cuotafinal = 0;
      
      
        $frecuenciaDiasMap = [
          1 => 26,
          2 => 4,
          3 => 2,
          4 => 1,
        ];
        $dias = $frecuenciaDiasMap[$frecuencia];
        $tasa_tip_comision = number_format(($comision / $dias) * $numerocuota, 2, '.', '');
        $tasa_tip_interes = number_format(($tasa / $dias) * $numerocuota, 2, '.', '');
  
        $cuota_comision = number_format(round((($montosolicitado*$tasa_tip_comision)/100)/$numerocuota, 1), 2, '.', '');
  
        $cuota_cargo = number_format(round($cargo/$numerocuota, 1), 2, '.', '');
  
        $total_comision = number_format(round((($montosolicitado*$tasa_tip_comision)/100), 1), 2, '.', '');
  
        $total_cargo = number_format($cargo, 2, '.', '');
        
        $cuota_comisioncargo = number_format($cuota_comision+$cuota_cargo, 2, '.', '');
        $total_comisioncargo = number_format($total_comision+$total_cargo, 2, '.', '');
  
        $cuota_comision1 = number_format($cuota_comision, 2, '.', '');
        $cuota_cargo1 = number_format($cuota_cargo, 2, '.', '');
        $cuota_comisioncargo1 = number_format($cuota_comision+$cuota_cargo, 2, '.', '');
  
        $total_comision1 = number_format($total_comision, 2, '.', '');
  
        $total_cargo1 = number_format($total_cargo, 2, '.', '');
        $total_comisioncargo1 = number_format($total_comision1+$total_cargo1, 2, '.', '');
        
        if($tipotasa==2){
            if($frecuencia==2){
                $interes_diaria = pow(1+($tasa/100), $db_frecuencia->dias/30.416)-1;
                $interes_comision = pow(1+($comision/100), $db_frecuencia->dias/30.416)-1;
            }else{
                if($frecuencia==4){
                    $interes_comision = $comision/100;
                    $interes_diaria = $tasa/100;
                }else{
                    $interes_comision = pow(1+($comision/100), $db_frecuencia->dias/30)-1;
                    $interes_diaria = pow(1+($tasa/100), $db_frecuencia->dias/30)-1;
                }
            }
            //dd($tasa_tip_interes);
            $cuota = number_format(round($montosolicitado*(($interes_diaria*pow(1+$interes_diaria,$numerocuota))/(pow(1+$interes_diaria,$numerocuota)-1)),1), 2, '.', '');
            $total_cuota = number_format(round($cuota*$numerocuota, 1), 2, '.', '');
            $total_interes = number_format(round($total_cuota-$montosolicitado, 1), 2, '.', '');
            $total_cuotafinal = number_format(round($total_cuota+$total_comisioncargo, 1), 2, '.', '');
        }else{
            $cuota_amortizacion = number_format(round($montosolicitado/$numerocuota, 1), 2, '.', '');
            $cuota_interes = number_format(round((($montosolicitado*$tasa)/100)/$numerocuota, 1), 2, '.', '');
            $total_interes = number_format(round((($montosolicitado*$tasa)/100), 1), 2, '.', '');
        }
  
  
  
        //dd($interes_diaria);
        $saldo = $montosolicitado;
        $suma_amortizacion = 0;
        $suma_interes = 0;
        $suma_cuota = 0;
        $suma_comisioncargo = 0;
        $suma_cuotafinal = 0;
  
        $suma_comision1 = 0;
        $suma_cargo1 = 0;
        
        /*    array_push($cronograma,[
                'numero' => 0,
                'fechanormal' => '',
                'fecha' => '',
                'saldo' => number_format($saldo, 2, '.', ''),
                'amortizacion' => '',
                'interes' => '',
                'cuota' => '',
                'comisioncargo' => '',
                'cuotafinal' => $cuota,
            ]);
        $saldo = $saldo-$cuota;*/
            
        for ($i=1; $i < ($numerocuota+1); $i++){
             
            $fecha = cronograma_fecha($frecuencia,$fechainicio,$feriados);
            $fechainicio = $fecha['fecha_inicio'];
             
            if($tipotasa==1){
                if($i == $numerocuota){
                    $cuota_amortizacion = number_format($total_amortizacion-$suma_amortizacion, 2, '.', '');
                    $cuota_interes = number_format($total_interes-$suma_interes, 2, '.', '');
                    $cuota_comisioncargo = number_format($total_comisioncargo1-$suma_comisioncargo, 2, '.', '');
                    $cuotafinal = number_format($total_cuotafinal-$suma_cuotafinal, 2, '.', '');
                  
                    $cuota_comision1 = number_format($total_comision1-$suma_comision1, 2, '.', '');
                    $cuota_cargo1 = number_format($total_cargo1-$suma_cargo1, 2, '.', '');
                }else{
                    
                }
                $cuotafinal = number_format($cuota_amortizacion+$cuota_interes+$cuota_comisioncargo, 2, '.', '');
            }elseif($tipotasa==2){
              
                $cuota_interes = number_format(round($saldo * $interes_diaria, 1), 2, '.', '');
              
                if($i == $numerocuota){
                    $cuota_amortizacion = number_format($total_amortizacion-$suma_amortizacion, 2, '.', '');
                    //$cuota_interes = number_format($total_interes-$suma_interes, 2, '.', '');
                    $cuota_comisioncargo = number_format($cuota_comision1-$cuota_cargo1, 2, '.', '');
                    //$cuotafinal = number_format($total_cuotafinal-$suma_cuotafinal, 2, '.', '');
                  
                    //$cuota_comision1 = number_format($total_comision1-$suma_comision1, 2, '.', '');
                    $cuota_cargo1 = number_format($total_cargo1-$suma_cargo1, 2, '.', '');
                }else{
                    $cuota_amortizacion = number_format(round($cuota-$cuota_interes, 1), 2, '.', '');
                  
                }
                $cuota_comision1 = number_format(round($saldo*$interes_comision, 1), 2, '.', '');
                $cuotafinal = number_format($cuota+$cuota_comision1+$cuota_cargo1, 2, '.', '');
                //$cuota_amortizacion = number_format(round($cuota-$cuota_interes, 1), 2, '.', '');
            }
            
              
            array_push($cronograma,[
                //'numero' => str_pad($i, 2, "0", STR_PAD_LEFT),
                'numero' => $i,
                'fechanormal' => $fecha['fecha_normal'],
                'fecha' => $fecha['credito_fecha'],
                'saldo' => number_format($saldo, 2, '.', ''),
                'amortizacion' => $cuota_amortizacion,
                'interes' => $cuota_interes,
                'cuota' => $cuota,
                'comision' => $cuota_comision1,
                'cargo' => $cuota_cargo1,
                'comisioncargo' => $cuota_comisioncargo,
                'cuotafinal' => $cuotafinal,
            ]);

            if($i==1){
                $cronograma_fechainicio = $fecha['fecha_normal'];
                $cronograma_cuotapago = $cuotafinal;
            }
            if($i==$numerocuota){
                $cronograma_ultimafecha = $fecha['fecha_normal'];
            }

            $saldo = $saldo-$cuota_amortizacion;
            $suma_amortizacion = number_format($suma_amortizacion+$cuota_amortizacion, 2, '.', '');
            $suma_interes = number_format($suma_interes+$cuota_interes, 2, '.', '');
            $suma_cuota = number_format($suma_cuota+$cuota, 2, '.', '');
            $suma_comisioncargo = number_format($suma_comisioncargo+($cuota_comision1+$cuota_cargo1), 2, '.', '');
            $suma_cuotafinal = number_format($suma_cuotafinal+$cuotafinal, 2, '.', '');
          
            $suma_comision1 = number_format($suma_comision1+$cuota_comision1, 2, '.', '');
            $suma_cargo1 = number_format($suma_cargo1+$cuota_cargo1, 2, '.', '');
        }
  
  
        $total_propuesta = 0;
        if ($frecuencia == 1) {
          $total_propuesta = $cronograma_cuotapago * 26;
        }
        else if ( $frecuencia == 2) {
          $total_propuesta = $cronograma_cuotapago * 4;
        }
        else if ( $frecuencia == 3) {
          $total_propuesta = $cronograma_cuotapago * 2;
        }
        else if ( $frecuencia == 4) {
          $total_propuesta = $cronograma_cuotapago;
        }
  
        return ([
            'cronograma' => $cronograma,
            'tipotasa' => $tipotasa,
            'fechainicio' => $cronograma_fechainicio,
            'ultimafecha' => $cronograma_ultimafecha,
            'cuota_pago' => $cronograma_cuotapago,
            'total_amortizacion' => $suma_amortizacion,
            'total_interes' => $suma_interes,
            'total_cuota' => $suma_cuota,
            'cuota_comision' => number_format($cuota_comision, 2, '.', ''),
            'cuota_cargo' => number_format($cuota_cargo, 2, '.', ''),
            'cuota_comisioncargo' => number_format($cuota_comision+$cuota_cargo, 2, '.', ''),
            'total_comision' => $suma_comision1,
            'total_cargo' => $suma_cargo1,
            'total_comisioncargo' => $suma_comisioncargo,
            'total_cuotafinal' => $suma_cuotafinal,
            'total_propuesta' => $total_propuesta,
        ]);
}


function cronograma_fecha($frecuencia,$fechainicio,$feriados){
  
    $nuevafecha = date_create($fechainicio);
                  
    if($frecuencia == 1){
        date_add($nuevafecha, date_interval_create_from_date_string('1 day'));
    }
    elseif($frecuencia == 2){
        date_add($nuevafecha, date_interval_create_from_date_string('1 weeks'));
    }
    elseif($frecuencia == 3){
        date_add($nuevafecha, date_interval_create_from_date_string('2 weeks'));
    }
    elseif($frecuencia == 4){
        date_add($nuevafecha, date_interval_create_from_date_string('1 months'));
    }
    
          
  
    $fechainicio = date_format($nuevafecha, 'd-m-Y');
    $fechaferiado = date_format($nuevafecha, 'd/m');
    $creditofecha = date_format($nuevafecha, 'd/m/Y');
    $fechanormal = date_format($nuevafecha, 'Y-m-d'); 
 
    foreach($feriados as $value) {
        $dia_feriado = date_format(date_create($value->fecha_feriado),'d');
        $mes_feriado = date_format(date_create($value->fecha_feriado),'m');
        $dia_mes = str_pad($dia_feriado, 2, "0", STR_PAD_LEFT).'/'.str_pad($mes_feriado, 2, "0", STR_PAD_LEFT);
        if( $dia_mes == $fechaferiado){
          $nuevafecha = strtotime('+1day',strtotime($fechainicio));
          $creditofecha = date('d/m/Y',$nuevafecha);
          $fechainicio = date('d-m-Y',$nuevafecha);
          $fechaferiado = date('d/m',$nuevafecha);
          $fechanormal = date('Y-m-d',$nuevafecha); 
        }
    } 
//     if(date('l', strtotime($fechainicio))=='Saturday' && $excluirsabado=='on') {
//         $nuevafecha = strtotime('+1day',strtotime($fechainicio));
//         $creditofecha = date('d/m/Y',$nuevafecha);
//         $fechainicio = date('d-m-Y',$nuevafecha);
//         $fechaferiado = date('d/m',$nuevafecha);
//         $fechanormal = date('Y-m-d',$nuevafecha);
//     }
    if(date('l', strtotime($fechainicio))=='Sunday') {
        $nuevafecha = strtotime('+1day',strtotime($fechainicio));
        $creditofecha = date('d/m/Y',$nuevafecha);
        $fechainicio = date('d-m-Y',$nuevafecha);
        $fechaferiado = date('d/m',$nuevafecha);
        $fechanormal = date('Y-m-d',$nuevafecha);
      
        foreach($feriados as $value) {
            $dia_feriado = date_format(date_create($value->fecha_feriado),'d');
            $mes_feriado = date_format(date_create($value->fecha_feriado),'m');
            $dia_mes = str_pad($dia_feriado, 2, "0", STR_PAD_LEFT).'/'.str_pad($mes_feriado, 2, "0", STR_PAD_LEFT);
            if( $dia_mes == $fechaferiado){
              $nuevafecha = strtotime('+1day',strtotime($fechainicio));
              $creditofecha = date('d/m/Y',$nuevafecha);
              $fechainicio = date('d-m-Y',$nuevafecha);
              $fechaferiado = date('d/m',$nuevafecha);
              $fechanormal = date('Y-m-d',$nuevafecha); 
            }
        } 
    }
  
    return [
        'nueva_fecha' => $nuevafecha,
        'credito_fecha' => $creditofecha,
        'fecha_inicio' => $fechainicio,
        'fecha_feriado' => $fechaferiado,
        'fecha_normal' => $fechanormal,
    ];
}



#########################################
############# ANTIGUO SISTEMA DE CREDITOS
#########################################

function ahorro_cronograma($idtienda,$tipoahorro,$monto,$numerocuota,$fechainicio,$frecuencia,$numerodias,$tasa,$excluirferiado,$excluirsabado,$excluirdomingo){
          
            
            $tipotasa = configuracion($idtienda,'prestamo_ahorro_tasapordefecto')['valor']!=''?configuracion($idtienda,'prestamo_ahorro_tasapordefecto')['valor']:1;
     
            $resultado = '';
            $mensaje = ''; 
    
            if($tipoahorro==1){
            $monto = $monto/$numerocuota;
            }
            $fechainicio_ahorro = $fechainicio;
            $fecharetiro = '';
            $cronograma_fechainicio = '0000-00-00';
            $cronograma_ultimafecha = '0000-00-00';
  
            $total_cuota = 0;
            $total_interesganado = 0;
            $total_total = 0;
            $cronograma = [];

            if ($monto <= 0) {
                $resultado = 'ERROR';
                $mensaje = 'El "Monto" debe ser mayor a Cero "0".';
            }elseif ($numerocuota < 1) {
                $resultado = 'ERROR';
                $mensaje = 'El "Número de Cuota" mínima debe ser 1 Cuota.';
            }elseif ($tasa <= 0) {
                $resultado = 'ERROR';
                $mensaje = 'La "Tasa" debe ser mayor a Cero "0".';
            }
            else{
                $feriados = DB::table('s_prestamo_diaferiado')->where('idtienda',$idtienda)->get();
                $db_frecuencia = DB::table('s_prestamo_frecuencia')->whereId($frecuencia)->first();
              
                $saldocapital = 0;
                $interesganado = 0;
                if($tipotasa==2){
                    $tasaefectiva = pow(1+($tasa/100), 360/$db_frecuencia->dias)-1;
                    $interesganado =  (pow(1+$tasaefectiva, $db_frecuencia->dias/360)-1) * $monto;
                }else{
                    $interesganado = (($monto*$tasa)/100);
                } 

                for ($i=1; $i < ($numerocuota+1); $i++) { 
                  
                    /*$fechanormal = '';
                    $fecha = '';
                    if($tipoahorro==1){*/
                    /*}elseif($tipoahorro==2){
                        $fecha = prestamo_cronograma_fecha($feriados,$frecuencia,$fechainicio,$excluirsabado,$excluirdomingo,$excluirferiado,$numerodias);
                        $fechainicio = $fecha['fecha_inicio'];
                        $fechanormal = $fecha['fecha_normal'];
                        $fecha = $fecha['credito_fecha'];
                    }elseif($tipoahorro==3){

                    }*/
                  
                    if($tipoahorro==1){
                        $fecha = prestamo_cronograma_fecha($feriados,$frecuencia,$fechainicio,$excluirsabado,$excluirdomingo,$excluirferiado,$numerodias);
                        $fechainicio = $fecha['fecha_inicio'];
                    }
                  
                    $fechanormal = date_format(date_create($fechainicio), 'Y-m-d');
                    $fecha = date_format(date_create($fechainicio), 'd/m/Y');
                  
                    $total = $monto+$interesganado;
                    $saldocapital = $total+$saldocapital;
                      
                    array_push($cronograma,[
                        'numero' => str_pad($i, 2, "0", STR_PAD_LEFT),
                        'fechanormal' => $fechanormal,
                        'fecha' => $fecha,
                        'saldocapital' => number_format($saldocapital, 2, '.', ''),
                        'cuota' => number_format($monto, 2, '.', ''),
                        'interesganado' => number_format($interesganado, 2, '.', ''),
                        'total' => number_format($total, 2, '.', ''),
                    ]);
                  
                    if($tipoahorro==2){
                        $fecha = prestamo_cronograma_fecha($feriados,$frecuencia,$fechainicio,$excluirsabado,$excluirdomingo,$excluirferiado,$numerodias);
                        $fechainicio = $fecha['fecha_inicio'];
                    }
                  
                    if($i==1){
                        $cronograma_fechainicio = $fechanormal;
                    }
                    if($i==$numerocuota){
                        $cronograma_ultimafecha = $fechanormal;
                    }
                  
                    $total_cuota = $total_cuota+$monto;
                    $total_interesganado = $total_interesganado+$interesganado;
                    $total_total = $total_total+$total;
                }
              
                /*if($tipoahorro==1){
                    $fecharetiro = date_create($fechainicio_ahorro);
                    date_add($fecharetiro, date_interval_create_from_date_string($tiempocobro.' months'));
                    $fecharetiro = date_format($fecharetiro, 'Y-m-d');
                }elseif($tipoahorro==2){*/
                    $fecharetiro = $fechanormal;  
                /*}elseif($tipoahorro==3){
                    $fecharetiro = $fecha['fecha_normal']; 
                }*/
              
                $total_cuota = number_format($total_cuota, 2, '.', '');
                $total_interesganado = number_format($total_interesganado, 2, '.', '');
                $total_total = number_format($total_total, 2, '.', '');
              
                $resultado = 'CORRECTO';
                $mensaje = 'Se ha cargado correctamente.';
              
              
            }

            return ([
                'resultado' => $resultado,
                'mensaje' => $mensaje,
                'cronograma' => $cronograma,
                'tipotasa' => $tipotasa,
                'fechainicio' => $cronograma_fechainicio,
                'fecharetiro' => $fecharetiro,
                'ultimafecha' => $cronograma_ultimafecha,
                'total_cuota' => $total_cuota,
                'total_interesganado' => $total_interesganado,
                'total_total' => $total_total,
            ]);
}
function ahorro_recaudacion_cronograma_fijo($idtienda,$idprestamo_ahorro){
  
            $ahorrosolicitud = DB::table('s_prestamo_ahorro')
                ->leftJoin('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_ahorro.idprestamo_frecuencia')
                ->where([
                    ['s_prestamo_ahorro.id', $idprestamo_ahorro],
                    ['s_prestamo_ahorro.idtienda', $idtienda]
                ])
                ->select(
                    's_prestamo_ahorro.*',
                    's_prestamo_frecuencia.nombre as frecuencianombre'
                )
                ->first();
          
            $ahorrosolicituddetalle = DB::table('s_prestamo_ahorrofijodetalle')
                ->where('s_prestamo_ahorrofijodetalle.idprestamo_ahorro', $ahorrosolicitud->id)
                ->orderBy('s_prestamo_ahorrofijodetalle.numero', 'asc')
                ->get();
          
            $total_activa_cuota = '0.00';
            $total_activa_interesganado = '0.00';
            $total_activa_total = '0.00';
            $total_activa_retirar = '0.00';
  
            $total_restante_cuota = '0.00';
            $total_restante_interesganado = '0.00';
            $total_restante_total = '0.00';
  
            $total_pendiente_cuota = '0.00';
            $total_pendiente_interesganado = '0.00';
            $total_pendiente_total = '0.00';
            $total_pendiente_retirar = '0.00';
  
            $total_cancelada_cuota = '0.00';
            $total_cancelada_interesganado = '0.00';
            $total_cancelada_total = '0.00';
            
            $cuotas_pendientes = [];
            $cuotas_canceladas = [];
            $i = 0;
            $ii = 0;
            $ioption = 0;
            foreach ($ahorrosolicituddetalle as $value) {
                if($value->idestadorecaudacion == 1){
                    $estado = '';
                    if($ahorrosolicitud->idestadocobrarganancia == 1){
                        $colorTr = 'background-color: #2ecc71;';
                        
                        $estado = 'ACTIVA';
                        $total_activa_cuota = $total_activa_cuota+$value->cuota;
                        $total_activa_interesganado = $total_activa_interesganado+$value->interesganado;
                        $total_activa_total = $total_activa_total+$value->total;
                        $total_activa_retirar = $total_activa_retirar+$value->interesganado;
                        $retirar = $value->interesganado;

                        $total_pendiente_cuota = $total_pendiente_cuota+$value->cuota;
                        $total_pendiente_interesganado = $total_pendiente_interesganado+$value->interesganado;
                        $total_pendiente_total = $total_pendiente_total+$value->total;
                        $total_pendiente_retirar = $total_pendiente_retirar+$value->interesganado;
                    }
                    elseif($ahorrosolicitud->idestadocobrarganancia == 2){
                        // Dias de atraso
                        $datetime1 = date_create($value->fechaahorro);
                        $datetime2 = date_create(date('Y-m-d'));
                        $contador  = date_diff($datetime2, $datetime1);
                        $differenceFormat = '%a';
                        if ($datetime2 <= $datetime1) {
                            $atraso  = $contador->format($differenceFormat) * -1;
                        }else{
                            $atraso = $contador->format($differenceFormat);
                        }

                        $colorTr = 'background-color: #2ecc71;';

                        if($atraso==0){
                            $colorTr = 'background-color: #51b9ff;';
                        }elseif($atraso<0){
                            $colorTr = '';
                        }

                        $retirar = 0;
                        if($atraso >= 0){
                            $estado = 'ACTIVA';
                            $total_activa_cuota = $total_activa_cuota+$value->cuota;
                            $total_activa_interesganado = $total_activa_interesganado+$value->interesganado;
                            $total_activa_total = $total_activa_total+$value->total;
                            $total_activa_retirar = $total_activa_retirar+$value->interesganado;
                            $retirar = $value->interesganado;
                        }else{
                            $estado = 'RESTANTE';
                            $total_restante_cuota = $total_restante_cuota+$value->cuota;
                            $total_restante_interesganado = $total_restante_interesganado+$value->interesganado;
                            $total_restante_total = $total_restante_total+$value->total;
                        }

                        $total_pendiente_cuota = $total_pendiente_cuota+$value->cuota;
                        $total_pendiente_interesganado = $total_pendiente_interesganado+$value->interesganado;
                        $total_pendiente_total = $total_pendiente_total+$value->total;
                        $total_pendiente_retirar = $total_pendiente_retirar+$value->interesganado;
                    }
                    elseif($ahorrosolicitud->idestadocobrarganancia == 3){
                      
                    }
                        
                  

                        $cuotas_pendientes[] = [
                            'estado' => $estado,
                            'idprestamo_ahorrodetalle' => $value->id,
                            'tabla_colortr' => $colorTr,
                            'tabla_numero' => str_pad($value->numero, 2, "0", STR_PAD_LEFT),
                            'tabla_fechaahorro' => date_format(date_create($value->fechaahorro),"d/m/Y"),
                            'tabla_cuota' => $value->cuota,
                            'tabla_interesganado' => $value->interesganado,
                            'tabla_total' => $value->total,
                            'tabla_retirar' => $retirar,
                        ];
                }
                elseif($value->idestadorecaudacion == 2 or $value->idestadorecaudacion == 3){
                  
                    $cuotas_canceladas[] = [
                        'idprestamo_ahorrodetalle' => $value->id,
                        'tabla_numero' => str_pad($value->numero, 2, "0", STR_PAD_LEFT),
                        'tabla_fechaahorro' => date_format(date_create($value->fechaahorro),"d/m/Y"),
                        'tabla_cuota' => $value->cuota,
                        'tabla_interesganado' => $value->interesganado,
                        'tabla_total' => $value->total,
                        'tabla_retirar' => $value->retirar,
                    ];
                  
                    $total_cancelada_cuota = $total_cancelada_cuota+$value->cuota;
                    $total_cancelada_interesganado = $total_cancelada_interesganado+$value->interesganado;
                    $total_cancelada_total = $total_cancelada_total+$value->total;
                }  
                $i++;
            }
            
          
            $total_activa_cuota = number_format($total_activa_cuota, 2, '.', '');
            $total_activa_interesganado = number_format($total_activa_interesganado, 2, '.', '');
            $total_activa_total = number_format($total_activa_total, 2, '.', '');
            $total_activa_retirar = number_format($total_activa_retirar, 2, '.', '');
            $total_activa_retirar_redondeado = number_format(round($total_activa_retirar, 1), 2, '.', '');
  
            $total_restante_cuota = number_format($total_restante_cuota, 2, '.', '');
            $total_restante_interesganado = number_format($total_restante_interesganado, 2, '.', '');
            $total_restante_total = number_format($total_restante_total, 2, '.', '');
  
            $total_pendiente_cuota = number_format($total_pendiente_cuota, 2, '.', '');
            $total_pendiente_interesganado = number_format($total_pendiente_interesganado, 2, '.', '');
            $total_pendiente_total = number_format($total_pendiente_total, 2, '.', '');
            $total_pendiente_retirar = number_format($total_pendiente_retirar, 2, '.', '');
  
            $total_cancelada_cuota = number_format($total_cancelada_cuota, 2, '.', '');
            $total_cancelada_interesganado = number_format($total_cancelada_interesganado, 2, '.', '');
            $total_cancelada_total = number_format($total_cancelada_total, 2, '.', '');
  
    return [
        'ahorrosolicitud' => $ahorrosolicitud,
        'select_cuota' => 0,
      
        'total_cancelada_cuota' => $total_cancelada_cuota,
        'total_cancelada_interesganado' => $total_cancelada_interesganado,
        'total_cancelada_total' => $total_cancelada_total,
      
        'total_activa_cuota' => $total_activa_cuota,
        'total_activa_interesganado' => $total_activa_interesganado,
        'total_activa_total' => $total_activa_total,
        'total_activa_retirar' => $total_activa_retirar,
        'total_activa_retirar_redondeado' => $total_activa_retirar_redondeado,
      
        'total_restante_cuota' => $total_restante_cuota,
        'total_restante_interesganado' => $total_restante_interesganado,
        'total_restante_total' => $total_restante_total,
      
        'total_pendiente_cuota' => $total_pendiente_cuota,
        'total_pendiente_interesganado' => $total_pendiente_interesganado,
        'total_pendiente_total' => $total_pendiente_total,
        'total_pendiente_retirar' => $total_pendiente_retirar,
      
        'cuotas_canceladas' => $cuotas_canceladas,
        'cuotas_pendientes' => $cuotas_pendientes,
    ];
}
function ahorro_recaudacion_cronograma($idtienda,$idprestamo_ahorro,$moradescuento,$montocompleto,$idtipopago,$hastacuota,$fechainicio_alterado=''){
  
            $ahorrosolicitud = DB::table('s_prestamo_ahorro')
                ->leftJoin('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_ahorro.idprestamo_frecuencia')
                ->where([
                    ['s_prestamo_ahorro.id', $idprestamo_ahorro],
                    ['s_prestamo_ahorro.idtienda', $idtienda]
                ])
                ->select(
                    's_prestamo_ahorro.*',
                    's_prestamo_frecuencia.nombre as frecuencianombre'
                )
                ->first();
          
            $ahorrosolicituddetalle = DB::table('s_prestamo_ahorrodetalle')
                ->where('s_prestamo_ahorrodetalle.idprestamo_ahorro', $ahorrosolicitud->id)
                ->orderBy('s_prestamo_ahorrodetalle.numero', 'asc')
                ->get();
  
            
            $feriados = DB::table('s_prestamo_diaferiado')->get();
            $credito_excluirsabado = $ahorrosolicitud->excluirsabado;
            $credito_excluirdomingo = $ahorrosolicitud->excluirdomingo;
            $credito_excluirferiado = $ahorrosolicitud->excluirferiado;
            $credito_idfrecuencia = $ahorrosolicitud->idprestamo_frecuencia;
            $numerodias = $ahorrosolicitud->numerodias;
            
            $html_cuotasrestantes = "<option></option>";
            $html_cuotasrestantes_selected = null;
            $ultima_cuota_vencida = 0;
            $proximo_vencimiento = '';
            $hastacuota_completo = 0;
            $primeratraso = 0;
            $cuotas_seleccionadas = 0;
            $montorecibido = $montocompleto;
  
            $select_interesrestante = '0.00';
            $select_interes = '0.00';
            $select_cuota = '0.00';
            $select_atrasorestante = 0;
            $select_atraso = 0;
            $select_mora = '0.00';
            $select_moradescontado = '0.00';
            $select_moraapagar = '0.00';
            $select_cuotapago = '0.00';
            $select_acuentaanterior = '0.00';
            $select_acuentaproxima = '0.00';
            $select_cuotaapagar = '0.00';
            $select_ultimaacuenta = '0.00';
            $select_ultimonumerocuota = 0;
            
            $select_moradescontadoasesor = '0.00';
          
            $total_vencida_cuota = '0.00';
            $total_vencida_atraso = 0;
            $total_vencida_mora = '0.00';
            $total_vencida_moradescontado = '0.00';
            $total_vencida_moraapagar = '0.00';
            $total_vencida_cuotapago = '0.00';
            $total_vencida_acuenta = '0.00';
            $total_vencida_cuotaapagar = '0.00';
  
            $total_restante_cuota = '0.00';
            $total_restante_atraso = 0;
            $total_restante_mora = '0.00';
            $total_restante_moradescontado = '0.00';
            $total_restante_moraapagar = '0.00';
            $total_restante_cuotapago = '0.00';
            $total_restante_acuenta = '0.00';
            $total_restante_cuotaapagar = '0.00';
  
            $total_pendiente_cuota = '0.00';
            $total_pendiente_atraso = 0;
            $total_pendiente_mora = '0.00';
            $total_pendiente_moradescontado = '0.00';
            $total_pendiente_moraapagar = '0.00';
            $total_pendiente_cuotapago = '0.00';
            $total_pendiente_acuenta = '0.00';
            $total_pendiente_cuotaapagar = '0.00';
  
            $total_cancelada_cuota = '0.00';
            $total_cancelada_atraso = 0;
            $total_cancelada_mora = '0.00';
            $total_cancelada_moradescontado = '0.00';
            $total_cancelada_moraapagar = '0.00';
            $total_cancelada_cuotapago = '0.00';
            $total_cancelada_acuenta = '0.00';
            $total_cancelada_cuotaapagar = '0.00';
  
            // mora
            $mora_pordia = 0;
            
            if(configuracion($idtienda,'prestamo_ahorro_morapordefecto')['valor']==1){
                if(configuracion($idtienda,'prestamo_ahorro_moratipo')['valor']==1){ // por frecuencia de pagos

                    if($credito_idfrecuencia==1){
                        $mora_pordia = configuracion($idtienda,'prestamo_ahorro_mora_diario')['valor'];
                    }elseif($credito_idfrecuencia==2){
                        $mora_pordia = configuracion($idtienda,'prestamo_ahorro_mora_semanal')['valor'];
                    }elseif($credito_idfrecuencia==3){
                        $mora_pordia = configuracion($idtienda,'prestamo_ahorro_mora_quincenal')['valor'];
                    }elseif($credito_idfrecuencia==4){
                        $mora_pordia = configuracion($idtienda,'prestamo_ahorro_mora_mensual')['valor'];
                    }elseif($credito_idfrecuencia==5){
                        $mora_pordia = configuracion($idtienda,'prestamo_ahorro_mora_programado')['valor'];
                    }

                }
                elseif(configuracion($idtienda,'prestamo_ahorro_moratipo')['valor']==2){ // por rango de montos

                    $morarangos = json_decode(configuracion($idtienda,'prestamo_ahorro_morarango')['valor']);
                    foreach($morarangos as $value){
                        if($ahorrosolicitud->monto<=$value->morarango){
                            $mora_pordia = $value->morarangomonto;
                            break;
                        }
                    } 

                }
            }
            elseif(configuracion($idtienda,'prestamo_ahorro_morapordefecto')['valor']==2){
                    if($credito_idfrecuencia==1){
                        $mora_pordia = configuracion($idtienda,'prestamo_ahorro_mora_diario_efectiva')['valor'];
                    }elseif($credito_idfrecuencia==2){
                        $mora_pordia = configuracion($idtienda,'prestamo_ahorro_mora_semanal_efectiva')['valor'];
                    }elseif($credito_idfrecuencia==3){
                        $mora_pordia = configuracion($idtienda,'prestamo_ahorro_mora_quincenal_efectiva')['valor'];
                    }elseif($credito_idfrecuencia==4){
                        $mora_pordia = configuracion($idtienda,'prestamo_ahorro_mora_mensual_efectiva')['valor'];
                    }elseif($credito_idfrecuencia==5){
                        $mora_pordia = configuracion($idtienda,'prestamo_ahorro_mora_programado_efectiva')['valor'];
                    }
            }    
           
  
            $morapendiente = DB::table('s_prestamo_ahorromora')
                ->where('s_prestamo_ahorromora.idprestamo_ahorro', $ahorrosolicitud->id)
                ->where('s_prestamo_ahorromora.idestado', 1)
                ->where('s_prestamo_ahorromora.idestadoaprobacion', 1)
                ->whereIn('s_prestamo_ahorromora.idestadomora', [1,2])
                ->sum('s_prestamo_ahorromora.total_moradescuento');
  
            $moraaprobado = DB::table('s_prestamo_ahorromora')
                ->where('s_prestamo_ahorromora.idprestamo_ahorro', $ahorrosolicitud->id)
                ->where('s_prestamo_ahorromora.idestado', 1)
                ->where('s_prestamo_ahorromora.idestadoaprobacion', 1)
                ->where('s_prestamo_ahorromora.idestadomora', 3)
                ->sum('s_prestamo_ahorromora.total_moradescuento');
  
            $recaudacionmoradescontado = DB::table('s_prestamo_ahorrorecaudacion')
                ->where('s_prestamo_ahorrorecaudacion.idprestamo_ahorro', $ahorrosolicitud->id)
                ->where('s_prestamo_ahorrorecaudacion.idestado', 1)
                ->where('s_prestamo_ahorrorecaudacion.idestadorecaudacion', 2)
                ->sum('s_prestamo_ahorrorecaudacion.cronograma_moradescuento');
            // fin mora
        
            $moraadescontar = $morapendiente+$moraaprobado-$recaudacionmoradescontado;
            //dd($recaudacionmoradescontado);
            if($moraadescontar>0 && $idtipopago==1){
                $mora_descontado_total = $moraadescontar;
            }else{
                $mora_descontado_total = $moradescuento;
            }
            //dd($total_prestamo_mora_descontada);
            //$mora_pediente = $total_prestamo_mora_descontada;
            $mora_pediente = 0;
            $montocompleto_total = $montocompleto; //number_format(round($montocompleto, 1), 2, '.', '');
            
            $cuotas_pendientes = [];
            $cuotas_pendientes_seleccionados = [];
            $cuotas_canceladas = [];
            $cuotas_vencidas = [];
            $cuotas_restantes = [];
            $i = 0;
            $ii = 0;
            $ioption = 0;
            foreach ($ahorrosolicituddetalle as $value) {
                
                // cuotas
                if ($value->idestadorecaudacion == 1) {
                    if($ioption==0){
                        $html_cuotasrestantes_selected = $value->numero;
                    }
                    $html_cuotasrestantes .= '<option value="'.$value->numero.'">'.str_pad($value->numero, 2, "0", STR_PAD_LEFT).'</option>';
                    $ioption++;
                }
              
          
                $fechaahorro = $value->fechaahorro;
            
                // Dias de atraso
                $datetime1 = date_create($fechaahorro);
                $datetime2 = date_create(date('Y-m-d'));
                $contador  = date_diff($datetime2, $datetime1);
                $differenceFormat = '%a';
                if ($datetime2 <= $datetime1) {
                    $atraso  = $contador->format($differenceFormat) * -1;
                    $colorTr = '';
                    if ($datetime2 == $datetime1) {
                        $colorTr = 'background-color: #b0ffbc;';
                        $ultima_cuota_vencida = $value->numero;
                    }
                } else {
                    $atraso = $contador->format($differenceFormat);
                    $colorTr = 'background-color: #ffb0b0;';
                }
              
                // Calculando la mora
                $mora = 0;
                if ($atraso > 0) {
                    if(configuracion($idtienda,'prestamo_ahorro_morapordefecto')['valor']==1){
                        $mora = number_format(($mora_pordia*$atraso)+$mora_pediente, 2, '.', '');
                    }elseif(configuracion($idtienda,'prestamo_ahorro_morapordefecto')['valor']==2){
                        $mora = number_format(($value->total*(pow(1+$mora_pordia,($atraso/360))-1))+$mora_pediente, 2, '.', '');
                    }
                }
              
                /*if($i==0){
                    $montocompleto_total = $montocompleto_total+$value->acuenta;
                }*/
                $cuota_pago = number_format($value->total+$mora, 2, '.', '');
                $mora_apagar = '0.00';
                $mora_descontado = '0.00';
                $cuota_apagar = '0.00';
                $montocompleto = '0.00';
                $acuenta = '0.00';
                $tabla_acuenta = '';
                $interesrestante = 0;
              
                $class = '';
              
                //dump($montocompleto_total);
              
                $acuenta = $value->acuenta;
                $tabla_acuenta = $value->acuenta;
              
                
               
                if($idtipopago==1 && $value->idestadorecaudacion == 1){
                    //dd($hastacuota);
                    if(isset($hastacuota)){
                        if($value->numero <= $hastacuota){
                            $cuotas_seleccionadas = $cuotas_seleccionadas+1;
                            $class = 'class="mx-tableselect"';
                            $colorTr = 'background-color: #ec8585; background-image: url('.url('public/backoffice/sistema/text3.png').') !important;';
                          
                            // DESCUENTO MORA
                            if($mora_descontado_total>=$mora){
                                $mora_descontado_total = $mora_descontado_total-$mora;
                                $mora_descontado = $mora;
                                $colorTr = 'background-color: #ec8585; background-image: url('.url('public/backoffice/sistema/text3.png').') !important;';
                            }
                            elseif($mora_descontado_total<$mora && $mora_descontado_total>0){
                                $mora_descontado = $mora_descontado_total;
                                $mora_descontado_total = 0;
                            }
                            
                            if($atraso==0){
                                $colorTr = 'background-color: #51b9ff;background-image: url('.url('public/backoffice/sistema/text3.png').') !important;';
                            }elseif($atraso<0){
                                $colorTr = 'background-color: #2ecc71;background-image: url('.url('public/backoffice/sistema/text3.png').') !important;';
                                $select_atrasorestante = $select_atrasorestante+$atraso;
                                $select_interesrestante = $select_interesrestante+$value->interesganado;
                                $interesrestante = $value->interesganado;
                            }
                            
                            $acuenta = $value->acuenta;
                            $tabla_acuenta = $value->acuenta;
                            
                            $mora_descontado = number_format($mora_descontado, 2, '.', '');
                            $cuota_pago = number_format($cuota_pago-$mora_descontado, 2, '.', '');
                            $mora_apagar = number_format($mora-$mora_descontado, 2, '.', '');
                
                            $cuota_apagar = number_format($value->total+$mora_apagar-$acuenta, 2, '.', '');
            
                            $select_interes = $select_interes+$value->interesganado;
                            $select_cuota = $select_cuota+$value->total;
                            $select_atraso = $select_atraso+$atraso;
                            $select_mora = $select_mora+$mora;
                            $select_moradescontado = $select_moradescontado+$mora_descontado;
                            $select_moraapagar = $select_moraapagar+$mora_apagar;
                            $select_cuotapago = $select_cuotapago+$cuota_pago;
                            $select_cuotaapagar = $select_cuotaapagar+$cuota_apagar;
                            $select_ultimonumerocuota = $value->numero;
                          
                         
                          
                            $cuotas_pendientes_seleccionados[] = [
                                'estado' => 'CANCELADO',
                                'idprestamo_ahorrodetalle' => $value->id,
                                'numero' => $value->numero,
                                'fechaahorro' => $fechaahorro,
                                'cuota' => $value->total,
                                'atraso' => $atraso,
                                'mora' => $mora,
                                'moradescontado' => $mora_descontado,
                                'moraapagar' => $mora_apagar,
                                'cuotapago' => $cuota_pago,
                                'acuenta' => $acuenta,
                                'cuotaapagar' => $cuota_apagar,
                                'interes' => $value->interesganado,
                                'interesrestante' => $interesrestante
                            ];
                        }
                        elseif($value->numero == ($hastacuota+1)){
                            $proximo_vencimiento = $fechaahorro;
                        }
                    }
                }
                elseif($idtipopago==2 && $value->idestadorecaudacion == 1){
                    $montocompleto_total = $montocompleto_total+$value->acuenta;
                    if($montocompleto_total>0){
                        $class = 'class="mx-tableselect"';
                        $colorTr = 'background-color: #ec8585;background-image: url('.url('public/backoffice/sistema/text3.png').') !important;';
                      
                        if($atraso==0){
                            $colorTr = 'background-color: #51b9ff;background-image: url('.url('public/backoffice/sistema/text3.png').') !important;';
                        }elseif($atraso<0){
                            $colorTr = 'background-color: #2ecc71;background-image: url('.url('public/backoffice/sistema/text3.png').') !important;';
                            $select_atrasorestante = $select_atrasorestante+$atraso;
                        }
                      

                        // DESCUENTO MORA
                        if($mora_descontado_total>=$mora){
                            $mora_descontado_total = $mora_descontado_total-$mora;
                            $mora_descontado = $mora;
                        }
                        elseif($mora_descontado_total<$mora && $mora_descontado_total>0){
                            $mora_descontado = $mora_descontado_total;
                            $mora_descontado_total = 0;
                        }
                      
                        $mora_descontado = number_format($mora_descontado, 2, '.', '');
                        $cuota_pago = number_format($cuota_pago-$mora_descontado, 2, '.', '');
                      
                        $cuota_apagartotal = 0;
                        $cuota_pago_new = $cuota_pago-$value->acuenta;
                        $estado = 'NINGUNO';
                        if(intval($montocompleto_total)>=intval($cuota_pago_new)){
                            $montocompleto_total = $montocompleto_total-$cuota_pago_new;
                            $montocompleto = $cuota_pago_new;
                            $est = 1;
                          
                            $estado = 'CANCELADO';
                            $acuenta = $value->acuenta;
                            $tabla_acuenta = $value->acuenta;
                            $hastacuota_completo = $value->numero;
                            $cuotas_seleccionadas = $cuotas_seleccionadas+1;
                            $select_ultimonumerocuota = $value->numero;
                            
                            // seleccionar
                            $mora_apagar = number_format($mora-$mora_descontado, 2, '.', '');
                            $cuota_apagar = number_format($montocompleto, 2, '.', '');
                            $select_interes = $select_interes+$value->interesganado;
                            $select_cuota = $select_cuota+$value->total;
                            $select_atraso = $select_atraso+$atraso;
                            $select_mora = $select_mora+$mora;
                            $select_moradescontado = $select_moradescontado+$mora_descontado;
                            $select_moraapagar = $select_moraapagar+$mora_apagar;
                            $select_cuotapago = $select_cuotapago+$cuota_pago;
                            $select_cuotaapagar = $select_cuotaapagar+$cuota_apagar;
                        }
                        elseif($montocompleto_total<$cuota_pago_new && $montocompleto_total>0){
                            $montocompleto = $montocompleto_total;
                            $montocompleto_total = 0;
                          
                            $estado = 'ACUENTA';
                            $class = '';
                            $colorTr = 'background-color: #fff9b0;';
                            $acuenta = number_format($montocompleto, 2, '.', '');
                            $tabla_acuenta = number_format($montocompleto, 2, '.', '');
                            $select_ultimaacuenta = $acuenta;
                            $select_acuentaproxima = $select_acuentaproxima+$acuenta;
                            if($value->acuenta>0){
                                $acuenta = number_format($value->acuenta+$montocompleto, 2, '.', '');
                                $tabla_acuenta = $value->acuenta.' + '.number_format($montocompleto, 2, '.', '');
                            }
                            $montocompleto = '0.00';
                        }
                        
                        if($value->numero == ($hastacuota_completo+1)){
                            $proximo_vencimiento = $fechaahorro;
                        } 
                      
                            
                      
                      
                        $cuotas_pendientes_seleccionados[] = [
                            'estado' => $estado,
                            'idprestamo_ahorrodetalle' => $value->id,
                            'numero' => $value->numero,
                            'fechaahorro' => $fechaahorro,
                            'cuota' => $value->total,
                            'atraso' => $atraso,
                            'mora' => $mora,
                            'moradescontado' => $mora_descontado,
                            'moraapagar' => $mora_apagar,
                            'cuotapago' => $cuota_pago,
                            'acuenta' => $acuenta,
                            'cuotaapagar' => $cuota_apagar,
                            'interes' => $value->interesganado,
                            'interesrestante' => 0
                        ];
                    }
                    elseif($value->numero == ($hastacuota_completo+1)){
                        $proximo_vencimiento = $fechaahorro;
                    }
                }
              
                if($value->idestadorecaudacion == 1){
                    $estado = '';
                    if($atraso > 0){
                        $estado = 'VENCIDO';
                        $total_vencida_cuota = $total_vencida_cuota+$value->total;
                        $total_vencida_atraso = $total_vencida_atraso+$atraso;
                        $total_vencida_mora = $total_vencida_mora+$mora;
                        $total_vencida_moradescontado = $total_vencida_moradescontado+$mora_descontado;
                        $total_vencida_moraapagar = $total_vencida_moraapagar+$mora_apagar;
                        $total_vencida_cuotapago = $total_vencida_cuotapago+$cuota_pago;
                        $total_vencida_acuenta = $total_vencida_acuenta+$acuenta;
                        $total_vencida_cuotaapagar = $total_vencida_cuotaapagar+$cuota_apagar;
                    }else{
                        $estado = 'RESTANTE';
                        $total_restante_cuota = $total_restante_cuota+$value->total;
                        $total_restante_atraso = $total_restante_atraso+$atraso;
                        $total_restante_mora = $total_restante_mora+$mora;
                        $total_restante_moradescontado = $total_restante_moradescontado+$mora_descontado;
                        $total_restante_moraapagar = $total_restante_moraapagar+$mora_apagar;
                        $total_restante_cuotapago = $total_restante_cuotapago+$cuota_pago;
                        $total_restante_acuenta = $total_restante_acuenta+$acuenta;
                        $total_restante_cuotaapagar = $total_restante_cuotaapagar+$cuota_apagar;
                    }
                  
                    if($ii==0){
                        $primeratraso = $atraso;
                        $ii++;
                    }
                  
                    $tabla_style_mora = "background-color: #ff1f43;color: white;";
                    if($mora_descontado > 0){
                        $tabla_style_mora = "background-color:#0077ff;color: white;";
                    }
                  
                    $select_acuentaanterior = $select_acuentaanterior+$value->acuenta;
                  
                    //alterar nueva fecha
                    if($fechainicio_alterado!=''){
                        $fecha = prestamo_cronograma_fecha($feriados,$credito_idfrecuencia,$fechainicio_alterado,$credito_excluirsabado,$credito_excluirdomingo,$credito_excluirferiado,$numerodias);
                        $fechainicio_alterado = $fecha['fecha_inicio'];
                        $fechaahorro = $fecha['fecha_normal'];
                    }
                    //fin alterar nueva fecha
                  
                    if($atraso >= 0){
                        $cuotas_vencidas[] = [
                            'estado' => $estado,
                            'idprestamo_ahorrodetalle' => $value->id,
                            'tabla_colortr' => $colorTr,
                            'tabla_class' => $class,
                            'tabla_style_mora' => $tabla_style_mora,
                            'tabla_numero' => str_pad($value->numero, 2, "0", STR_PAD_LEFT),
                            'tabla_fechaahorro' => date_format(date_create($fechaahorro),"d/m/Y"),
                            'tabla_fvencimiento' => $fechaahorro,
                            'tabla_cuota' => $value->total,
                            'tabla_atraso' => $atraso,
                            'tabla_mora' => $mora,
                            'tabla_moradescontado' => $mora_descontado,
                            'tabla_moraapagar' => $mora_apagar,
                            'tabla_cuotatotal' => $cuota_pago,
                            'tabla_acuenta' => $tabla_acuenta,
                            'tabla_cuotaapagar' => $cuota_apagar,
                        ];
                    }else{
                        $cuotas_restantes[] = [
                            'estado' => $estado,
                            'idprestamo_ahorrodetalle' => $value->id,
                            'tabla_colortr' => $colorTr,
                            'tabla_class' => $class,
                            'tabla_style_mora' => $tabla_style_mora,
                            'tabla_numero' => str_pad($value->numero, 2, "0", STR_PAD_LEFT),
                            'tabla_fechaahorro' => date_format(date_create($fechaahorro),"d/m/Y"),
                            'tabla_fvencimiento' => $fechaahorro,
                            'tabla_cuota' => $value->total,
                            'tabla_atraso' => $atraso,
                            'tabla_mora' => $mora,
                            'tabla_moradescontado' => $mora_descontado,
                            'tabla_moraapagar' => $mora_apagar,
                            'tabla_cuotatotal' => $cuota_pago,
                            'tabla_acuenta' => $tabla_acuenta,
                            'tabla_cuotaapagar' => $cuota_apagar,
                        ];
                    }
                  
                        $cuotas_pendientes[] = [
                            'estado' => $estado,
                            'idprestamo_ahorrodetalle' => $value->id,
                            'tabla_colortr' => $colorTr,
                            'tabla_class' => $class,
                            'tabla_style_mora' => $tabla_style_mora,
                            'tabla_numero' => str_pad($value->numero, 2, "0", STR_PAD_LEFT),
                            'tabla_fechaahorro' => date_format(date_create($fechaahorro),"d/m/Y"),
                            'tabla_fvencimiento' => $fechaahorro,
                            'tabla_cuota' => $value->total,
                            'tabla_atraso' => $atraso,
                            'tabla_mora' => $mora,
                            'tabla_moradescontado' => $mora_descontado,
                            'tabla_moraapagar' => $mora_apagar,
                            'tabla_cuotatotal' => $cuota_pago,
                            'tabla_acuenta' => $tabla_acuenta,
                            'tabla_cuotaapagar' => $cuota_apagar,
                        ];
                        
                  
                    $total_pendiente_cuota = $total_pendiente_cuota+$value->total;
                    $total_pendiente_atraso = $total_pendiente_atraso+$atraso;
                    $total_pendiente_mora = $total_pendiente_mora+$mora;
                    $total_pendiente_moradescontado = $total_pendiente_moradescontado+$mora_descontado;
                    $total_pendiente_moraapagar = $total_pendiente_moraapagar+$mora_apagar;
                    $total_pendiente_cuotapago = $total_pendiente_cuotapago+$cuota_pago;
                    $total_pendiente_acuenta = $total_pendiente_acuenta+$acuenta;
                    $total_pendiente_cuotaapagar = $total_pendiente_cuotaapagar+$cuota_apagar;
                  
                }
                elseif($value->idestadorecaudacion == 2 or $value->idestadorecaudacion == 3){
                  
                    $tabla_style_mora = "background-color: #ff1f43;color: white;";
                    if($value->moradescuento > 0){
                        $tabla_style_mora = "background-color:#0077ff;color: white;";
                    }
                  
                    
                  
                    $cuotas_canceladas[] = [
                        'idprestamo_ahorrodetalle' => $value->id,
                        'tabla_colortr' => $colorTr,
                        'tabla_class' => $class,
                        'tabla_style_mora' => $tabla_style_mora,
                        'tabla_numero' => str_pad($value->numero, 2, "0", STR_PAD_LEFT),
                        'tabla_fechaahorro' => date_format(date_create($fechaahorro),"d/m/Y"),
                        'tabla_fvencimiento' => $fechaahorro,
                        'tabla_cuota' => $value->total,
                        'tabla_atraso' => $value->atraso,
                        'tabla_mora' => $value->mora,
                        'tabla_moradescontado' => $value->moradescuento,
                        'tabla_moraapagar' => $value->moraapagar,
                        'tabla_cuotatotal' => $value->cuotapago,
                        'tabla_acuenta' => $value->acuenta,
                        'tabla_cuotaapagar' => $value->cuotaapagar,
                    ];
                  
                    $total_cancelada_cuota = $total_cancelada_cuota+$value->total;
                    $total_cancelada_atraso = $total_cancelada_atraso+$value->atraso;
                    $total_cancelada_mora = $total_cancelada_mora+$mora;
                    $total_cancelada_moradescontado = $total_cancelada_moradescontado+$value->moradescuento;
                    $total_cancelada_moraapagar = $total_cancelada_moraapagar+$value->moraapagar;
                    $total_cancelada_cuotapago = $total_cancelada_cuotapago+$value->cuotapago;
                    $total_cancelada_acuenta = $total_cancelada_acuenta+$value->acuenta;
                    $total_cancelada_cuotaapagar = $total_cancelada_cuotaapagar+$value->cuotaapagar;
                }  
                $i++;
            }
            
            // mora pendiente - se cobra en la ultima cuota, si no se ha sustentando
            $ultimacuota = '';
            $morapendientefinal = 0;
            if($ahorrosolicitud->numerocuota==$hastacuota){
                $morapendientefinal = $morapendiente;
                $ultimacuota = 'ok';
            }
  
            $select_interesrestante = number_format($select_interesrestante, 3, '.', '');
            $select_interes = number_format($select_interes, 3, '.', '');
            $select_cuota = number_format($select_cuota, 2, '.', '');
            $select_atrasorestante = number_format($select_atrasorestante, 2, '.', '');
            $select_atraso = number_format($select_atraso, 2, '.', '');
            $select_mora = number_format($select_mora, 2, '.', '');
            $select_moradescontado = number_format($select_moradescontado, 2, '.', '');
            $select_moraapagar = number_format($select_moraapagar, 2, '.', '');
            $select_cuotapago = number_format($select_cuotapago, 2, '.', '');
            $select_acuentaanterior = number_format($select_acuentaanterior, 2, '.', '');
            $select_acuentaproxima = number_format($select_acuentaproxima, 2, '.', '');
            //$select_acuentatotal = number_format($select_acuentaanterior+$select_acuentaproxima, 2, '.', '');
            $select_cuotaapagar = number_format($morapendientefinal+$select_cuotaapagar, 2, '.', '');
            $select_cuotaapagarredondeado = number_format(round($select_cuotaapagar, 1), 2, '.', '');
            $select_acuentacuotaapagar = number_format($select_ultimaacuenta+$select_cuotaapagar, 2, '.', '');
            $select_acuentacuotaapagarredondeado = number_format($select_acuentacuotaapagar, 2, '.', '');
          
            $total_vencida_cuota = number_format($total_vencida_cuota, 2, '.', '');
            $total_vencida_atraso = $total_vencida_atraso;
            $total_vencida_mora = number_format($total_vencida_mora, 2, '.', '');
            $total_vencida_moradescontado = number_format($total_vencida_moradescontado, 2, '.', '');
            $total_vencida_moraapagar = number_format($total_vencida_moraapagar, 2, '.', '');
            $total_vencida_cuotapago = number_format($total_vencida_cuotapago, 2, '.', '');
            $total_vencida_acuenta = number_format($total_vencida_acuenta, 2, '.', '');
            $total_vencida_cuotaapagar = number_format($total_vencida_cuotaapagar, 2, '.', '');
  
            $total_restante_cuota = number_format($total_restante_cuota, 2, '.', '');
            $total_restante_atraso = $total_restante_atraso;
            $total_restante_mora = number_format($total_restante_mora, 2, '.', '');
            $total_restante_moradescontado = number_format($total_restante_moradescontado, 2, '.', '');
            $total_restante_moraapagar = number_format($total_restante_moraapagar, 2, '.', '');
            $total_restante_cuotapago = number_format($total_restante_cuotapago, 2, '.', '');
            $total_restante_acuenta = number_format($total_restante_acuenta, 2, '.', '');
            $total_restante_cuotaapagar = number_format($total_restante_cuotaapagar, 2, '.', '');
  
            $total_pendiente_cuota = number_format($total_pendiente_cuota, 2, '.', '');
            $total_pendiente_atraso = $total_pendiente_atraso;
            $total_pendiente_mora = number_format($total_pendiente_mora, 2, '.', '');
            $total_pendiente_moradescontado = number_format($total_pendiente_moradescontado, 2, '.', '');
            $total_pendiente_moraapagar = number_format($total_pendiente_moraapagar, 2, '.', '');
            $total_pendiente_cuotapago = number_format($total_pendiente_cuotapago, 2, '.', '');
            $total_pendiente_acuenta = number_format($total_pendiente_acuenta, 2, '.', '');
            $total_pendiente_cuotaapagar = number_format($total_pendiente_cuotaapagar, 2, '.', '');
  
            $total_cancelada_cuota = number_format($total_cancelada_cuota, 2, '.', '');
            $total_cancelada_atraso = $total_cancelada_atraso;
            $total_cancelada_mora = number_format($total_cancelada_mora, 2, '.', '');
            $total_cancelada_moradescontado = number_format($total_cancelada_moradescontado, 2, '.', '');
            $total_cancelada_moraapagar = number_format($total_cancelada_moraapagar, 2, '.', '');
            $total_cancelada_cuotapago = number_format($total_cancelada_cuotapago, 2, '.', '');
            $total_cancelada_acuenta = number_format($total_cancelada_acuenta, 2, '.', '');
            $total_cancelada_cuotaapagar = number_format($total_cancelada_cuotaapagar, 2, '.', '');
  
    return [
        'ahorrosolicitud' => $ahorrosolicitud,
        'html_cuotasrestantes' =>$html_cuotasrestantes,
        'html_cuotasrestantes_selected' =>$html_cuotasrestantes_selected,
        'ultima_cuota_vencida' =>$ultima_cuota_vencida,
        'proximo_vencimiento' =>$proximo_vencimiento,
        'primeratraso' =>$primeratraso,
        'morapendiente' => number_format($morapendiente, 2, '.', ''),
        'moraaprobado' => number_format($moraaprobado, 2, '.', ''),
        'moraadescontar' => number_format($morapendiente+$moraaprobado, 2, '.', ''),
        'cuotas_seleccionadas' => $cuotas_seleccionadas,
        'ultimacuota' => $ultimacuota,
        'montorecibido' => $montorecibido,
      
        'select_interesrestante' => $select_interesrestante,
        'select_interes' => $select_interes,
        'select_cuota' => $select_cuota,
        'select_atrasorestante' => $select_atrasorestante,
        'select_atraso' => $select_atraso,
        'select_mora' => $select_mora,
        'select_moradescontado' => $select_moradescontado,
        'select_moraapagar' => $select_moraapagar,
        'select_cuotapago' => $select_cuotapago,
        'select_acuentaanterior' => $select_acuentaanterior,
        'select_acuentaproxima' => $select_acuentaproxima,
        //'select_acuentatotal' => $select_acuentatotal,
        'select_cuotaapagar' => $select_cuotaapagar,
        'select_cuotaapagarredondeado' => $select_cuotaapagarredondeado,
        'select_acuentacuotaapagar' => $select_acuentacuotaapagar,
        'select_acuentacuotaapagarredondeado' => $select_acuentacuotaapagarredondeado,
        'select_ultimonumerocuota' => $select_ultimonumerocuota,
      
        'total_cancelada_cuota' => $total_cancelada_cuota,
        'total_cancelada_atraso' => $total_cancelada_atraso,
        'total_cancelada_mora' => $total_cancelada_mora,
        'total_cancelada_moradescontado' => $total_cancelada_moradescontado,
        'total_cancelada_moraapagar' => $total_cancelada_moraapagar,
        'total_cancelada_cuotapago' => $total_cancelada_cuotapago,
        'total_cancelada_acuenta' => $total_cancelada_acuenta,
        'total_cancelada_cuotaapagar' => $total_cancelada_cuotaapagar,
      
        'total_vencida_cuota' => $total_vencida_cuota,
        'total_vencida_atraso' => $total_vencida_atraso,
        'total_vencida_mora' => $total_vencida_mora,
        'total_vencida_moradescontado' => $total_vencida_moradescontado,
        'total_vencida_moraapagar' => $total_vencida_moraapagar,
        'total_vencida_cuotapago' => $total_vencida_cuotapago,
        'total_vencida_acuenta' => $total_vencida_acuenta,
        'total_vencida_cuotaapagar' => $total_vencida_cuotaapagar,
      
        'total_restante_cuota' => $total_restante_cuota,
        'total_restante_atraso' => $total_restante_atraso,
        'total_restante_mora' => $total_restante_mora,
        'total_restante_moradescontado' => $total_restante_moradescontado,
        'total_restante_moraapagar' => $total_restante_moraapagar,
        'total_restante_cuotapago' => $total_restante_cuotapago,
        'total_restante_acuenta' => $total_restante_acuenta,
        'total_restante_cuotaapagar' => $total_restante_cuotaapagar,
      
        'total_pendiente_cuota' => $total_pendiente_cuota,
        'total_pendiente_atraso' => $total_pendiente_atraso,
        'total_pendiente_mora' => $total_pendiente_mora,
        'total_pendiente_moradescontado' => $total_pendiente_moradescontado,
        'total_pendiente_moraapagar' => $total_pendiente_moraapagar,
        'total_pendiente_cuotapago' => $total_pendiente_cuotapago,
        'total_pendiente_acuenta' => $total_pendiente_acuenta,
        'total_pendiente_cuotaapagar' => $total_pendiente_cuotaapagar,
      
        'cuotas_pendientes' => $cuotas_pendientes,
        'cuotas_vencidas' => $cuotas_vencidas,
        'cuotas_restantes' => $cuotas_restantes,
        'cuotas_pendientes_seleccionados' => $cuotas_pendientes_seleccionados,
        'cuotas_canceladas' => $cuotas_canceladas,
    ];
}
function prestamo_cronograma($idtienda,$monto,$numerocuota,$fechainicio,$frecuencia,$numerodias,$tasa,$gastoadministrativo,$excluirferiado,$excluirsabado,$excluirdomingo,$abono=0){
          
            $resultado = '';
            $mensaje = ''; 
  
            $cronograma_fechainicio = '0000-00-00';
            $cronograma_ultimafecha = '0000-00-00';
  
  
            $total_amortizacion = 0;
            $total_interes = 0;
            $total_cuota = 0;
            $total_segurodesgravamen = 0;
            $total_gastoadministrativo = 0;
            $total_acumulado = 0;
            $total_acumuladofinal = 0;
            $total_cuotanormal = 0;
            $total_cuotafinal = 0;
            $total_abono = 0;
            $total_cuotafinaltotal = 0;
            $cronograma = [];
            
            /*$fecha = Carbon::now()->format('Y-m-d');
            $fecha1= new DateTime($fecha);
            $fecha2= new DateTime($fechainicio);
            $diff = $fecha1->diff($fecha2);*/
            $cuota_final = 0;
                
            $tipotasa = configuracion($idtienda,'prestamo_tasapordefecto')['valor']!=''?configuracion($idtienda,'prestamo_tasapordefecto')['valor']:1;
            $estadoacumulado = configuracion($idtienda,'prestamo_estadoacumulado')['valor'];
  
            if ($monto <= 0) {
                $resultado = 'ERROR';
                $mensaje = 'El "Monto" debe ser mayor a Cero "0".';
            }elseif ($numerocuota < 1) {
                $resultado = 'ERROR';
                $mensaje = 'El "Número de Cuota" mínima debe ser 1 Cuota.';
            }elseif ($tasa <= 0) {
                $resultado = 'ERROR';
                $mensaje = 'La "Tasa" debe ser mayor a Cero "0".';
            }
            /*elseif ($fecha > $fechainicio) {
                $resultado = 'ERROR';
                $mensaje = 'La "Fecha de Inicio" debe ser mayor o igual a la fecha Actual.';
            }*/
            else{
                //$fechainicio = date('d-m-Y',strtotime('+'.$sdiasgracia.'day',strtotime(date_format(date_create($fechainicio), 'd-m-Y'))));
                
                // Seguro degravamen
                $sdesgravamen = 0;
                if(configuracion($idtienda,'prestamo_estadoseguro_degravamen')['valor']=='on'){
                    if($frecuencia == 1){
                        $sdesgravamen = configuracion($idtienda,'prestamo_seguro_degravamen_diario')['valor'];               
                    }elseif($frecuencia == 2){
                        $sdesgravamen = configuracion($idtienda,'prestamo_seguro_degravamen_semanal')['valor'];          
                    }elseif($frecuencia == 3){
                        $sdesgravamen = configuracion($idtienda,'prestamo_seguro_degravamen_quincenal')['valor']; 
                    }elseif($frecuencia == 4){
                        $sdesgravamen = configuracion($idtienda,'prestamo_seguro_degravamen_mensual')['valor'];  
                    }elseif($frecuencia == 5){
                        $sdesgravamen = configuracion($idtienda,'prestamo_seguro_degravamen_programado')['valor']; 
                    }
                }
              
                $feriados = DB::table('s_prestamo_diaferiado')->where('idtienda',$idtienda)->get();
                $db_frecuencia = DB::table('s_prestamo_frecuencia')->whereId($frecuencia)->first();
  
                $saldocapital = $monto;
                $cuota_amortizacion = 0;
                $cuota_interes = 0;
                if($tipotasa==2){
                    $tasaefectiva_anual = pow(1+($tasa/100), 360/$db_frecuencia->dias)-1;
                    $cuota = $saldocapital*( (($tasa/100)*pow(1+($tasa/100), $numerocuota))/(pow(1+($tasa/100), $numerocuota)-1) );
                    $segurodesgravamen = number_format((($saldocapital*$sdesgravamen)/100)/$numerocuota, 3, '.', '');
                }else{
                    $cuota_amortizacion = $saldocapital/$numerocuota;
                    $cuota_interes = (($saldocapital*$tasa)/100)/$numerocuota;
                    $cuota = $cuota_amortizacion+$cuota_interes;
                    $segurodesgravamen = $sdesgravamen/$numerocuota;
              //dump($segurodesgravamen);
                }
                $gastoadministrativocuota = number_format($gastoadministrativo/$numerocuota, 2, '.', '');
                $abonocuota = number_format($abono/$numerocuota, 2, '.', '');
                $acumuladocuota = 0;
                $cuotanormal = number_format($cuota+$segurodesgravamen+$gastoadministrativocuota, 2, '.', '');
                $cuotafinal = number_format($cuota+$segurodesgravamen+$gastoadministrativocuota, 2, '.', '');
                $saldomontototal = $cuotanormal*$numerocuota;

                for ($i=1; $i < ($numerocuota+1); $i++) { 
                  
                    // establecer fecha de credito
                    $fecha = prestamo_cronograma_fecha($feriados,$frecuencia,$fechainicio,$excluirsabado,$excluirdomingo,$excluirferiado,$numerodias);
                    $fechainicio = $fecha['fecha_inicio'];
                    // fin establecer fecha de credito
           
                    if($tipotasa==2){
                        $interes =  (pow(1+$tasaefectiva_anual, $db_frecuencia->dias/360)-1) * $saldocapital;
                        $amortizacion = $cuota - $interes;
                    }else{
                        $interes = $cuota_interes;
                        $amortizacion = $cuota_amortizacion;
                    }
                  
         
                    if($estadoacumulado==1){
          
                        if($i==$numerocuota){ // ultima cuota
                            $cuotafinal = $cuotanormal-$total_acumulado;
                            $acumuladocuota = -$total_acumulado;
                            $total_acumuladofinal = $total_acumulado;
                        }else{
                            $cuotafinal = number_format(round_mayor($cuotafinal,1), 2, '.', '');
                            $acumuladocuota = number_format($cuotafinal-($cuota+$segurodesgravamen+$gastoadministrativocuota), 2, '.', '');
                        }
                      
                    }else{
                        $cuotafinal = number_format(round($cuotafinal,1), 2, '.', '');
                    }
                    $cuotafinaltotal = $cuotafinal+$abonocuota;
                  
                      
                    array_push($cronograma,[
                        'numero' => str_pad($i, 2, "0", STR_PAD_LEFT),
                        'fechanormal' => $fecha['fecha_normal'],
                        'fecha' => $fecha['credito_fecha'],
                        'saldo' => number_format($saldocapital, 2, '.', ''),
                        'saldototal' => number_format($saldomontototal, 2, '.', ''),
                        'amortizacion' => number_format($amortizacion, 2, '.', ''),
                        'interes' => number_format($interes, 3, '.', ''),
                        'cuota' => number_format($cuota, 2, '.', ''),
                        'segurodesgravamen' => number_format($segurodesgravamen, 3, '.', ''),
                        'gastoadministrativo' => number_format($gastoadministrativocuota, 2, '.', ''),
                        'cuotanormal' => number_format($cuotanormal, 2, '.', ''),
                        'acumulado' => number_format($acumuladocuota, 2, '.', ''),
                        'cuotafinal' => number_format($cuotafinal, 2, '.', ''),
                        'abono' => number_format($abonocuota, 2, '.', ''),
                        'cuotafinaltotal' => number_format($cuotafinaltotal, 2, '.', ''),
                    ]);
                  
                    if($i==1){
                        $cuota_final = $cuotafinal;
                        $cronograma_fechainicio = $fecha['fecha_normal'];
                    }
                    if($i==$numerocuota){
                        $cronograma_ultimafecha = $fecha['fecha_normal'];
                    }
                  
                    $saldocapital = $saldocapital-$amortizacion;
                    $saldomontototal = $saldomontototal-$cuotafinal;
                  
                  
                    $total_amortizacion = $total_amortizacion+number_format($amortizacion, 2, '.', '');
                    $total_interes = $total_interes+number_format($interes, 3, '.', '');
                    $total_cuota = $total_cuota+number_format($cuota, 2, '.', '');
                    $total_segurodesgravamen = $total_segurodesgravamen+$segurodesgravamen;
                    $total_gastoadministrativo = $total_gastoadministrativo+number_format($gastoadministrativocuota, 2, '.', '');
                    $total_cuotanormal = $total_acumulado+number_format($cuotanormal, 2, '.', '');
                    $total_acumulado = $total_acumulado+number_format($acumuladocuota+$total_acumuladofinal, 2, '.', '');
                    $total_cuotafinal = $total_cuotafinal+number_format($cuotafinal, 2, '.', '');
                    $total_abono = $total_abono+number_format($abonocuota, 2, '.', '');
                    $total_cuotafinaltotal = $total_cuotafinaltotal+number_format($cuotafinaltotal, 2, '.', '');
                }
              
                $total_amortizacion = number_format($total_amortizacion, 2, '.', '');
                $total_interes = number_format($total_interes, 3, '.', '');
                $total_cuota = number_format($total_cuota, 2, '.', '');
                $total_segurodesgravamen = number_format($total_segurodesgravamen, 2, '.', '');
                $total_gastoadministrativo = number_format($total_gastoadministrativo, 2, '.', '');
                $total_cuotanormal = number_format($total_cuotanormal, 2, '.', '');
                $total_acumulado = number_format($total_acumulado, 2, '.', '');
                $total_cuotafinal = number_format($total_cuotafinal, 2, '.', '');
                $total_abono = number_format($total_abono, 2, '.', '');
                $total_cuotafinaltotal = number_format($total_cuotafinaltotal, 2, '.', '');
              
                $resultado = 'CORRECTO';
                $mensaje = 'Se ha cargado correctamente.';
              
              
            }
          
                
            return ([
                'resultado' => $resultado,
                'mensaje' => $mensaje,
                'cronograma' => $cronograma,
                'tipotasa' => $tipotasa,
                'cuota' => $cuota_final,
                'fechainicio' => $cronograma_fechainicio,
                'ultimafecha' => $cronograma_ultimafecha,
                'total_amortizacion' => $total_amortizacion,
                'total_interes' => $total_interes,
                'total_cuota' => $total_cuota,
                'total_segurodesgravamen' => $total_segurodesgravamen,
                'total_gastoadministrativo' => $total_gastoadministrativo,
                'total_cuotanormal' => $total_cuotanormal,
                'total_acumulado' => $total_acumulado,
                'total_cuotafinal' => $total_cuotafinal,
                'total_abono' => $total_abono,
                'total_cuotafinaltotal' => $total_cuotafinaltotal,
            ]);
}
function prestamo_cobranza_cronograma($idtienda,$idprestamo_credito,$moradescuento,$montocompleto,$idtipopago,$hastacuota,$fechainicio_alterado='',$descuentointeres=0){
          
            $descuentointeres = $descuentointeres!='undefined'?$descuentointeres:0;

            $creditosolicitud = DB::table('s_prestamo_credito')
                ->leftJoin('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_credito.idprestamo_frecuencia')
                ->where([
                    ['s_prestamo_credito.id', $idprestamo_credito],
                    ['s_prestamo_credito.idtienda', $idtienda]
                ])
                ->select(
                    's_prestamo_credito.*',
                    's_prestamo_frecuencia.nombre as frecuencianombre'
                )
                ->first();
          
            $creditosolicituddetalle = DB::table('s_prestamo_creditodetalle')
                ->where('s_prestamo_creditodetalle.idprestamo_credito', $creditosolicitud->id)
                ->orderBy('s_prestamo_creditodetalle.numero', 'asc')
                ->get();
  
            
            $feriados = DB::table('s_prestamo_diaferiado')->get();
            $credito_excluirsabado = $creditosolicitud->excluirsabado;
            $credito_excluirdomingo = $creditosolicitud->excluirdomingo;
            $credito_excluirferiado = $creditosolicitud->excluirferiado;
            $credito_idfrecuencia = $creditosolicitud->idprestamo_frecuencia;
            $numerodias = $creditosolicitud->numerodias;
            
            $html_cuotasrestantes = "<option></option>";
            $html_cuotasrestantes_selected = null;
            $ultima_cuota_vencida = 0;
            $proximo_vencimiento = '';
            $hastacuota_completo = 0;
            $primeratraso = 0;
            $cuotas_seleccionadas = 0;
            $montorecibido = $montocompleto;
  
            $select_amortizacionrestante = '0.00';
            $select_amortizacion = '0.00';
            $select_interesrestante = '0.00';
            //$select_interesdescuento = $descuentointeres;
            $select_interes = '0.00';
            $select_cuota = '0.00';
            $select_atrasorestante = 0;
            $select_atraso = 0;
            $select_mora = '0.00';
            $select_moradescontado = '0.00';
            $select_moraapagar = '0.00';
            $select_cuotapago = '0.00';
            $select_acuentaanterior = '0.00';
            $select_acuentaproxima = '0.00';
            $select_cuotaapagar = '0.00';
            $select_abono = '0.00';
            $select_cuotaapagartotal = '0.00';
            $select_ultimaacuenta = '0.00';
            $select_ultimonumerocuota = 0;
            
            $select_moradescontadoasesor = '0.00';
          
            $total_vencida_cuota = '0.00';
            $total_vencida_atraso = 0;
            $total_vencida_mora = '0.00';
            $total_vencida_moradescontado = '0.00';
            $total_vencida_moraapagar = '0.00';
            $total_vencida_cuotapago = '0.00';
            $total_vencida_acuenta = '0.00';
            $total_vencida_cuotaapagar = '0.00';
            $total_vencida_abono = '0.00';
            $total_vencida_cuotaapagartotal = '0.00';
  
            $total_restante_cuota = '0.00';
            $total_restante_atraso = 0;
            $total_restante_mora = '0.00';
            $total_restante_moradescontado = '0.00';
            $total_restante_moraapagar = '0.00';
            $total_restante_cuotapago = '0.00';
            $total_restante_acuenta = '0.00';
            $total_restante_cuotaapagar = '0.00';
            $total_restante_abono = '0.00';
            $total_restante_cuotaapagartotal = '0.00';
  
            $total_pendiente_cuota = '0.00';
            $total_pendiente_atraso = 0;
            $total_pendiente_mora = '0.00';
            $total_pendiente_moradescontado = '0.00';
            $total_pendiente_moraapagar = '0.00';
            $total_pendiente_cuotapago = '0.00';
            $total_pendiente_acuenta = '0.00';
            $total_pendiente_cuotaapagar = '0.00';
            $total_pendiente_abono = '0.00';
            $total_pendiente_cuotaapagartotal = '0.00';
  
  
            $total_cancelada_cuota = '0.00';
            $total_cancelada_atraso = 0;
            $total_cancelada_mora = '0.00';
            $total_cancelada_moradescontado = '0.00';
            $total_cancelada_moraapagar = '0.00';
            $total_cancelada_cuotapago = '0.00';
            $total_cancelada_acuenta = '0.00';
            $total_cancelada_cuotaapagar = '0.00';
            $total_cancelada_abono = '0.00';
            $total_cancelada_cuotaapagartotal = '0.00';
  
            // mora
            $mora_pordia = 0;
            
            if(configuracion($idtienda,'prestamo_morapordefecto')['valor']==1){
                if(configuracion($idtienda,'prestamo_moratipo')['valor']==1){ // por frecuencia de pagos

                    if($credito_idfrecuencia==1){
                        $mora_pordia = configuracion($idtienda,'prestamo_mora_diario')['valor'];
                    }elseif($credito_idfrecuencia==2){
                        $mora_pordia = configuracion($idtienda,'prestamo_mora_semanal')['valor'];
                    }elseif($credito_idfrecuencia==3){
                        $mora_pordia = configuracion($idtienda,'prestamo_mora_quincenal')['valor'];
                    }elseif($credito_idfrecuencia==4){
                        $mora_pordia = configuracion($idtienda,'prestamo_mora_mensual')['valor'];
                    }elseif($credito_idfrecuencia==5){
                        $mora_pordia = configuracion($idtienda,'prestamo_mora_programado')['valor'];
                    }

                }
                elseif(configuracion($idtienda,'prestamo_moratipo')['valor']==2){ // por rango de montos

                    $morarangos = json_decode(configuracion($idtienda,'prestamo_morarango')['valor']);
                    foreach($morarangos as $value){
                        if($creditosolicitud->monto<=$value->morarango){
                            $mora_pordia = $value->morarangomonto;
                            break;
                        }
                    } 

                }
            }
            elseif(configuracion($idtienda,'prestamo_morapordefecto')['valor']==2){
                    if($credito_idfrecuencia==1){
                        $mora_pordia = configuracion($idtienda,'prestamo_mora_diario_efectiva')['valor'];
                    }elseif($credito_idfrecuencia==2){
                        $mora_pordia = configuracion($idtienda,'prestamo_mora_semanal_efectiva')['valor'];
                    }elseif($credito_idfrecuencia==3){
                        $mora_pordia = configuracion($idtienda,'prestamo_mora_quincenal_efectiva')['valor'];
                    }elseif($credito_idfrecuencia==4){
                        $mora_pordia = configuracion($idtienda,'prestamo_mora_mensual_efectiva')['valor'];
                    }elseif($credito_idfrecuencia==5){
                        $mora_pordia = configuracion($idtienda,'prestamo_mora_programado_efectiva')['valor'];
                    }
            }    
           
            /*$moradetalles = DB::table('s_prestamo_moradetalle')
                ->where('s_prestamo_moradetalle.idprestamo_credito', $creditosolicitud->id)
                ->where('s_prestamo_moradetalle.idestado', 1)
                ->get();
            
            $morasolicitado = 0;
            $moraaprobado = 0;
            $morapendiente = 0;
            foreach($moradetalles as $value){
                if($value->idprocedencia==1){
                    $morasolicitado = $morasolicitado+$value->morapagar;
                    $moraaprobado = $moraaprobado+$value->moradescontar;
                    $morapendiente = $morapendiente+$value->moradescuento;
                }
                elseif($value->idprocedencia==2){
                    $morasolicitado = $morasolicitado+$value->morapagar;
                    $moraaprobado = $moraaprobado+$value->moradescontar;
                    $morapendiente = $morapendiente+$value->moradescuento;
                }
            }*/
  
            
          
            $total_mora = DB::table('s_prestamo_creditodetalle')
                ->where('s_prestamo_creditodetalle.idprestamo_credito', $creditosolicitud->id)
                ->whereIn('s_prestamo_creditodetalle.idestadocobranza', [2,3])
                ->sum('s_prestamo_creditodetalle.moradescuento');
  
            $morasolicitado = DB::table('s_prestamo_moradetalle')
                ->where('s_prestamo_moradetalle.idprestamo_credito', $creditosolicitud->id)
                ->where('s_prestamo_moradetalle.idestado', 1)
                //->where('s_prestamo_moradetalle.idprocedencia', 2)
                ->sum('s_prestamo_moradetalle.morapagar');
  
            $moraaprobado = DB::table('s_prestamo_moradetalle')
                ->where('s_prestamo_moradetalle.idprestamo_credito', $creditosolicitud->id)
                ->where('s_prestamo_moradetalle.idestado', 1)
                //->where('s_prestamo_moradetalle.idprocedencia', 2)
                ->sum('s_prestamo_moradetalle.moradescontar');
  
            $morapendiente = DB::table('s_prestamo_moradetalle')
                ->where('s_prestamo_moradetalle.idprestamo_credito', $creditosolicitud->id)
                ->where('s_prestamo_moradetalle.idestado', 1)
                //->where('s_prestamo_moradetalle.idprocedencia', 2)
                ->sum('s_prestamo_moradetalle.moradescuento');
  
  
            /*$cobranzamoradescontado = DB::table('s_prestamo_cobranza')
                ->where('s_prestamo_cobranza.idprestamo_credito', $creditosolicitud->id)
                ->where('s_prestamo_cobranza.idestado', 1)
                ->where('s_prestamo_cobranza.idestadocobranza', 2)
                ->sum('s_prestamo_cobranza.cronograma_moradescuento');*/
            // fin mora
            //$moraadescontar = $morapendiente/*+$moraaprobado-$cobranzamoradescontado*/;
            //dd($cobranzamoradescontado);
            /*if($morapendiente>0 && $idtipopago==1){
                $mora_descontado_total = $morapendiente;
            }else{*/
            $morarestante = $morasolicitado-$total_mora;
            if($morarestante>=0){
                $mora_descontado_total = $moradescuento+$morarestante;
            }else{
                
            }
  //dd($morarestante);
            //}
            //dd($total_prestamo_mora_descontada);
            //$mora_pediente = $total_prestamo_mora_descontada;
            $mora_pediente = 0;
            $montocompleto_total = $montocompleto; //number_format(round($montocompleto, 1), 2, '.', '');
            
            $cuotas_pendientes = [];
            $cuotas_pendientes_seleccionados = [];
            $cuotas_canceladas = [];
            $cuotas_vencidas = [];
            $cuotas_restantes = [];
            $i = 0;
            $ii = 0;
            $ioption = 0;
            foreach ($creditosolicituddetalle as $value) {
                
                // cuotas
                if ($value->idestadocobranza == 1) {
                    if($ioption==0){
                        $html_cuotasrestantes_selected = $value->numero;
                    }
                    $html_cuotasrestantes .= '<option value="'.$value->numero.'">'.str_pad($value->numero, 2, "0", STR_PAD_LEFT).'</option>';
                    $ioption++;
                }
              
          
                $fechavencimiento = $value->fechavencimiento;
            
                // Dias de atraso
                $datetime1 = date_create($fechavencimiento);
                $datetime2 = date_create(date('Y-m-d'));
                $contador  = date_diff($datetime2, $datetime1);
                $differenceFormat = '%a';
                if ($datetime2 <= $datetime1) {
                    $atraso  = $contador->format($differenceFormat) * -1;
                    $colorTr = '';
                    if ($datetime2 == $datetime1) {
                        $colorTr = 'background-color: #b0ffbc;';
                        $ultima_cuota_vencida = $value->numero;
                    }
                } else {
                    $atraso = $contador->format($differenceFormat);
                    $colorTr = 'background-color: #ffb0b0;';
                }
              
                // Calculando la mora
                $mora = 0;
                if ($atraso > 0) {
                    if(configuracion($idtienda,'prestamo_morapordefecto')['valor']==1){
                        $mora = number_format(($mora_pordia*$atraso)+$mora_pediente, 2, '.', '');
                    }elseif(configuracion($idtienda,'prestamo_morapordefecto')['valor']==2){
                        $mora = number_format(($value->total*(pow(1+$mora_pordia,($atraso/360))-1))+$mora_pediente, 2, '.', '');
                    }
                }
              
                /*if($i==0){
                    $montocompleto_total = $montocompleto_total+$value->acuenta;
                }*/
                $cuota_pago = number_format($value->total+$mora, 2, '.', '');
                $mora_apagar = '0.00';
                $mora_descontado = '0.00';
                $cuota_apagar = '0.00';
                $montocompleto = '0.00';
                $acuenta = '0.00';
                $tabla_acuenta = '';
                $interesrestante = 0;
              
                $class = '';
              
                //dump($montocompleto_total);
              
                $acuenta = $value->acuenta;
                $tabla_acuenta = $value->acuenta;
              
                
               
                if($idtipopago==1 && $value->idestadocobranza == 1){
                    //dd($hastacuota);
                    if(isset($hastacuota)){
                        if($value->numero <= $hastacuota){
                            $cuotas_seleccionadas = $cuotas_seleccionadas+1;
                            $class = 'class="mx-tableselect"';
                            $colorTr = 'background-color: #ec8585; background-image: url('.url('public/backoffice/sistema/text3.png').') !important;';
                          
                            // DESCUENTO MORA
                            if($mora_descontado_total>=$mora){
                                $mora_descontado_total = $mora_descontado_total-$mora;
                                $mora_descontado = $mora;
                                $colorTr = 'background-color: #ec8585; background-image: url('.url('public/backoffice/sistema/text3.png').') !important;';
                            }
                            elseif($mora_descontado_total<$mora && $mora_descontado_total>0){
                                $mora_descontado = $mora_descontado_total;
                                $mora_descontado_total = 0;
                            }
                            
                            if($atraso==0){
                                $colorTr = 'background-color: #51b9ff;background-image: url('.url('public/backoffice/sistema/text3.png').') !important;';
                            }elseif($atraso<0){
                                $colorTr = 'background-color: #2ecc71;background-image: url('.url('public/backoffice/sistema/text3.png').') !important;';
                                $select_amortizacionrestante = $select_amortizacionrestante+$value->amortizacion;
                                $select_atrasorestante = $select_atrasorestante+$atraso;
                            }
                            
                            $acuenta = $value->acuenta;
                            $tabla_acuenta = $value->acuenta;
                            
                            $mora_descontado = number_format($mora_descontado, 2, '.', '');
                            $cuota_pago = number_format($cuota_pago-$mora_descontado, 2, '.', '');
                            $mora_apagar = number_format($mora-$mora_descontado, 2, '.', '');
                
                            /*if(configuracion($idtienda,'prestamo_estadodescuentointeres')['valor']==1 && $atraso<0){
                                $cuota_apagar = number_format($value->total+$mora_apagar-$acuenta, 2, '.', '');
                                $select_interesrestante = $select_interesrestante+$value->interes;
                                $interesrestante = $value->interes;
                            }else{
                                $cuota_apagar = number_format($value->total+$mora_apagar-$acuenta, 2, '.', '');
                            }*/
                            $cuota_apagar = number_format($value->total+$mora_apagar-$acuenta, 2, '.', '');
                            $select_interesrestante = $select_interesrestante+$value->interes;
                            $interesrestante = $value->interes;
                            
                            $cuota_apagartotal = $cuota_apagar+$value->abono;
                              
                            $select_amortizacion = $select_amortizacion+$value->amortizacion;
                            $select_interes = $select_interes+$value->interes;
                            $select_cuota = $select_cuota+$value->total;
                            $select_atraso = $select_atraso+$atraso;
                            $select_mora = $select_mora+$mora;
                            $select_moradescontado = $select_moradescontado+$mora_descontado;
                            $select_moraapagar = $select_moraapagar+$mora_apagar;
                            $select_cuotapago = $select_cuotapago+$cuota_pago;
                            $select_cuotaapagar = $select_cuotaapagar+$cuota_apagar;
                            $select_ultimonumerocuota = $value->numero;
                          
                          
                            $select_abono = $select_abono+$value->abono;
                            $select_cuotaapagartotal = $select_cuotaapagartotal+$cuota_apagartotal;
                          
                            $cuotas_pendientes_seleccionados[] = [
                                'estado' => 'CANCELADO',
                                'idprestamo_creditodetalle' => $value->id,
                                'numero' => $value->numero,
                                'fechavencimiento' => $fechavencimiento,
                                'cuota' => $value->total,
                                'atraso' => $atraso,
                                'mora' => $mora,
                                'moradescontado' => $mora_descontado,
                                'moraapagar' => $mora_apagar,
                                'cuotapago' => $cuota_pago,
                                'acuenta' => $acuenta,
                                'cuotaapagar' => $cuota_apagar,
                                'abono' => $value->abono,
                                'cuotaapagartotal' => $cuota_apagartotal,
                                'interes' => $value->interes,
                                'interesrestante' => $interesrestante
                            ];
                        }
                        elseif($value->numero == ($hastacuota+1)){
                            $proximo_vencimiento = $fechavencimiento;
                        }
                    }
                }
                elseif($idtipopago==2 && $value->idestadocobranza == 1){
                    $montocompleto_total = $montocompleto_total+$value->acuenta;
                    if($montocompleto_total>0){
                        $class = 'class="mx-tableselect"';
                        $colorTr = 'background-color: #ec8585;background-image: url('.url('public/backoffice/sistema/text3.png').') !important;';
                      
                        if($atraso==0){
                            $colorTr = 'background-color: #51b9ff;background-image: url('.url('public/backoffice/sistema/text3.png').') !important;';
                        }elseif($atraso<0){
                            $colorTr = 'background-color: #2ecc71;background-image: url('.url('public/backoffice/sistema/text3.png').') !important;';
                            $select_amortizacionrestante = $select_amortizacionrestante+$value->amortizacion;
                            $select_atrasorestante = $select_atrasorestante+$atraso;
                        }
                      

                        // DESCUENTO MORA
                        if($mora_descontado_total>=$mora){
                            $mora_descontado_total = $mora_descontado_total-$mora;
                            $mora_descontado = $mora;
                        }
                        elseif($mora_descontado_total<$mora && $mora_descontado_total>0){
                            $mora_descontado = $mora_descontado_total;
                            $mora_descontado_total = 0;
                        }
                      
                        $mora_descontado = number_format($mora_descontado, 2, '.', '');
                        $cuota_pago = number_format($cuota_pago-$mora_descontado, 2, '.', '');
                      
                        $cuota_apagartotal = 0;
                        $cuota_pago_new = $cuota_pago;
                        $estado = 'NINGUNO';
                        if(intval($montocompleto_total)>=intval($cuota_pago_new)){
                            $montocompleto_total = $montocompleto_total-$cuota_pago_new;
                            $montocompleto = $cuota_pago_new-$value->acuenta;
                            $est = 1;
                          
                            $estado = 'CANCELADO';
                            $acuenta = $value->acuenta;
                            $tabla_acuenta = $value->acuenta;
                            $hastacuota_completo = $value->numero;
                            $cuotas_seleccionadas = $cuotas_seleccionadas+1;
                            $select_ultimonumerocuota = $value->numero;
                            
                            // seleccionar
                            $mora_apagar = number_format($mora-$mora_descontado, 2, '.', '');
                            $cuota_apagar = number_format($montocompleto, 2, '.', '');
                            $cuota_apagartotal = $cuota_apagar+$value->abono;

                            $select_amortizacion = $select_amortizacion+$value->amortizacion;
                            $select_interes = $select_interes+$value->interes;
                            $select_cuota = $select_cuota+$value->total;
                            $select_atraso = $select_atraso+$atraso;
                            $select_mora = $select_mora+$mora;
                            $select_moradescontado = $select_moradescontado+$mora_descontado;
                            $select_moraapagar = $select_moraapagar+$mora_apagar;
                            $select_cuotapago = $select_cuotapago+$cuota_pago;
                            $select_cuotaapagar = $select_cuotaapagar+$cuota_apagar;

                            $select_abono = $select_abono+$value->abono;
                            $select_cuotaapagartotal = $select_cuotaapagartotal+$cuota_apagartotal;
                        }
                        elseif($montocompleto_total<$cuota_pago_new && $montocompleto_total>0){
                            $montocompleto = $montocompleto_total;
                            $montocompleto_total = 0;
                          
                            $estado = 'ACUENTA';
                            $class = '';
                            $colorTr = 'background-color: #fff9b0;';
                            $acuenta = number_format($montocompleto, 2, '.', '');
                            $tabla_acuenta = number_format($montocompleto, 2, '.', '');
                            $select_ultimaacuenta = $acuenta;
                            $select_acuentaproxima = $select_acuentaproxima+$acuenta;
                            if($value->acuenta>0){
                                $acuenta = number_format($montocompleto, 2, '.', '');
                                $tabla_acuenta = number_format($acuenta, 2, '.', '');
                            }
                            $montocompleto = '0.00';
                        }
                        
                        if($value->numero == ($hastacuota_completo+1)){
                            $proximo_vencimiento = $fechavencimiento;
                        } 
                      
                            
                      
                      
                        $cuotas_pendientes_seleccionados[] = [
                            'estado' => $estado,
                            'idprestamo_creditodetalle' => $value->id,
                            'numero' => $value->numero,
                            'fechavencimiento' => $fechavencimiento,
                            'cuota' => $value->total,
                            'atraso' => $atraso,
                            'mora' => $mora,
                            'moradescontado' => $mora_descontado,
                            'moraapagar' => $mora_apagar,
                            'cuotapago' => $cuota_pago,
                            'acuenta' => $acuenta,
                            'cuotaapagar' => $cuota_apagar,
                            'abono' => $value->abono,
                            'cuotaapagartotal' => $cuota_apagartotal,
                            'interes' => $value->interes,
                            'interesrestante' => 0
                        ];
                    }
                    elseif($value->numero == ($hastacuota_completo+1)){
                        $proximo_vencimiento = $fechavencimiento;
                    }
                }
              
                if($value->idestadocobranza == 1){
                  
                    
                  
                    $estado = '';
                    if($atraso > 0){
                        $estado = 'VENCIDO';
                        $total_vencida_cuota = $total_vencida_cuota+$value->total;
                        $total_vencida_atraso = $total_vencida_atraso+$atraso;
                        $total_vencida_mora = $total_vencida_mora+$mora;
                        $total_vencida_moradescontado = $total_vencida_moradescontado+$mora_descontado;
                        $total_vencida_moraapagar = $total_vencida_moraapagar+$mora_apagar;
                        $total_vencida_cuotapago = $total_vencida_cuotapago+$cuota_pago;
                        $total_vencida_acuenta = $total_vencida_acuenta+$acuenta;
                        $total_vencida_cuotaapagar = $total_vencida_cuotaapagar+$cuota_apagar;
                        $total_vencida_abono = $total_vencida_abono+$value->abono;
                        $total_vencida_cuotaapagartotal = $total_vencida_cuotaapagartotal+($cuota_apagar+$value->abono);
                    }else{
                        $estado = 'RESTANTE';
                        $total_restante_cuota = $total_restante_cuota+$value->total;
                        $total_restante_atraso = $total_restante_atraso+$atraso;
                        $total_restante_mora = $total_restante_mora+$mora;
                        $total_restante_moradescontado = $total_restante_moradescontado+$mora_descontado;
                        $total_restante_moraapagar = $total_restante_moraapagar+$mora_apagar;
                        $total_restante_cuotapago = $total_restante_cuotapago+$cuota_pago;
                        $total_restante_acuenta = $total_restante_acuenta+$acuenta;
                        $total_restante_cuotaapagar = $total_restante_cuotaapagar+$cuota_apagar;
                        $total_restante_abono = $total_restante_abono+$value->abono;
                        $total_restante_cuotaapagartotal = $total_restante_cuotaapagartotal+($cuota_apagar+$value->abono);
                    }
                  
                    if($ii==0){
                        $primeratraso = $atraso;
                        $ii++;
                    }
                  
                    $tabla_style_mora = "background-color: #ff1f43;color: white;";
                    if($mora_descontado > 0){
                        $tabla_style_mora = "background-color:#0077ff;color: white;";
                    }
                  
                    $select_acuentaanterior = $select_acuentaanterior+$value->acuenta;
                  
                    //alterar nueva fecha
                    if($fechainicio_alterado!=''){
                        $fecha = prestamo_cronograma_fecha($feriados,$credito_idfrecuencia,$fechainicio_alterado,$credito_excluirsabado,$credito_excluirdomingo,$credito_excluirferiado,$numerodias);
                        $fechainicio_alterado = $fecha['fecha_inicio'];
                        $fechavencimiento = $fecha['fecha_normal'];
                    }
                    //fin alterar nueva fecha
                  
                    if($atraso >= 0){
                        $cuotas_vencidas[] = [
                            'estado' => $estado,
                            'idprestamo_creditodetalle' => $value->id,
                            'tabla_colortr' => $colorTr,
                            'tabla_class' => $class,
                            'tabla_style_mora' => $tabla_style_mora,
                            'tabla_numero' => str_pad($value->numero, 2, "0", STR_PAD_LEFT),
                            'tabla_fechavencimiento' => date_format(date_create($fechavencimiento),"d/m/Y"),
                            'tabla_fvencimiento' => $fechavencimiento,
                            'tabla_cuota' => $value->total,
                            'tabla_atraso' => $atraso,
                            'tabla_mora' => $mora,
                            'tabla_moradescontado' => $mora_descontado,
                            'tabla_moraapagar' => $mora_apagar,
                            'tabla_cuotatotal' => $cuota_pago,
                            'tabla_acuenta' => $tabla_acuenta,
                            'tabla_cuotaapagar' => $cuota_apagar,
                            'tabla_abono' => $value->abono,
                            'tabla_cuotaapagartotal' => $cuota_apagar+$value->abono,
                        ];
                    }else{
                        $cuotas_restantes[] = [
                            'estado' => $estado,
                            'idprestamo_creditodetalle' => $value->id,
                            'tabla_colortr' => $colorTr,
                            'tabla_class' => $class,
                            'tabla_style_mora' => $tabla_style_mora,
                            'tabla_numero' => str_pad($value->numero, 2, "0", STR_PAD_LEFT),
                            'tabla_fechavencimiento' => date_format(date_create($fechavencimiento),"d/m/Y"),
                            'tabla_fvencimiento' => $fechavencimiento,
                            'tabla_cuota' => $value->total,
                            'tabla_atraso' => $atraso,
                            'tabla_mora' => $mora,
                            'tabla_moradescontado' => $mora_descontado,
                            'tabla_moraapagar' => $mora_apagar,
                            'tabla_cuotatotal' => $cuota_pago,
                            'tabla_acuenta' => $tabla_acuenta,
                            'tabla_cuotaapagar' => $cuota_apagar,
                            'tabla_abono' => $value->abono,
                            'tabla_cuotaapagartotal' => $cuota_apagar+$value->abono,
                        ];
                    }
                  
                    
                        // interes descuento
                        /*if(configuracion($idtienda,'prestamo_estadodescuentointeres')['valor']==1 && $atraso<0){
                            $totalcuota = $value->total.' (-'.($value->total-$value->amortizacion).')';
                        }else{
                            $totalcuota = $value->total;
                        }*/
                  
                        $cuotas_pendientes[] = [
                            'estado' => $estado,
                            'idprestamo_creditodetalle' => $value->id,
                            'tabla_colortr' => $colorTr,
                            'tabla_class' => $class,
                            'tabla_style_mora' => $tabla_style_mora,
                            'tabla_numero' => str_pad($value->numero, 2, "0", STR_PAD_LEFT),
                            'tabla_fechavencimiento' => date_format(date_create($fechavencimiento),"d/m/Y"),
                            'tabla_fvencimiento' => $fechavencimiento,
                            'tabla_cuota' => $value->total,
                            'tabla_interes' => $value->interes,
                            'tabla_atraso' => $atraso,
                            'tabla_mora' => $mora,
                            'tabla_moradescontado' => $mora_descontado,
                            'tabla_moraapagar' => $mora_apagar,
                            'tabla_cuotatotal' => $cuota_pago,
                            'tabla_acuenta' => $tabla_acuenta,
                            'tabla_cuotaapagar' => $cuota_apagar,
                            'tabla_abono' => $value->abono,
                            'tabla_cuotaapagartotal' => $cuota_apagar+$value->abono,
                        ];
                        
                  
                    $total_pendiente_cuota = $total_pendiente_cuota+$value->total;
                    $total_pendiente_atraso = $total_pendiente_atraso+$atraso;
                    $total_pendiente_mora = $total_pendiente_mora+$mora;
                    $total_pendiente_moradescontado = $total_pendiente_moradescontado+$mora_descontado;
                    $total_pendiente_moraapagar = $total_pendiente_moraapagar+$mora_apagar;
                    $total_pendiente_cuotapago = $total_pendiente_cuotapago+$cuota_pago;
                    $total_pendiente_acuenta = $total_pendiente_acuenta+$acuenta;
                    $total_pendiente_cuotaapagar = $total_pendiente_cuotaapagar+$cuota_apagar;
                    $total_pendiente_abono = $total_pendiente_abono+$value->abono;
                    $total_pendiente_cuotaapagartotal = $total_pendiente_cuotaapagartotal+($cuota_apagar+$value->abono);
                  
                }
                elseif($value->idestadocobranza == 2 or $value->idestadocobranza == 3){
                  
                    $tabla_style_mora = "background-color: #ff1f43;color: white;";
                    if($value->moradescuento > 0){
                        $tabla_style_mora = "background-color:#0077ff;color: white;";
                    }
                  
                    
                  
                    $cuotas_canceladas[] = [
                        'idprestamo_creditodetalle' => $value->id,
                        'tabla_colortr' => $colorTr,
                        'tabla_class' => $class,
                        'tabla_style_mora' => $tabla_style_mora,
                        'tabla_numero' => str_pad($value->numero, 2, "0", STR_PAD_LEFT),
                        'tabla_fechavencimiento' => date_format(date_create($fechavencimiento),"d/m/Y"),
                        'tabla_fvencimiento' => $fechavencimiento,
                        'tabla_cuota' => $value->total,
                        'tabla_atraso' => $value->atraso,
                        'tabla_mora' => $value->mora,
                        'tabla_moradescontado' => $value->moradescuento,
                        'tabla_moraapagar' => $value->moraapagar,
                        'tabla_cuotatotal' => $value->cuotapago,
                        'tabla_acuenta' => $value->acuenta,
                        'tabla_cuotaapagar' => $value->cuotaapagar,
                        'tabla_abono' => $value->abono,
                        'tabla_cuotaapagartotal' => $value->cuotaapagartotal,
                    ];
                  
                    $total_cancelada_cuota = $total_cancelada_cuota+$value->total;
                    $total_cancelada_atraso = $total_cancelada_atraso+$value->atraso;
                    $total_cancelada_mora = $total_cancelada_mora+$value->mora;
                    $total_cancelada_moradescontado = $total_cancelada_moradescontado+$value->moradescuento;
                    $total_cancelada_moraapagar = $total_cancelada_moraapagar+$value->moraapagar;
                    $total_cancelada_cuotapago = $total_cancelada_cuotapago+$value->cuotapago;
                    $total_cancelada_acuenta = $total_cancelada_acuenta+$value->acuenta;
                    $total_cancelada_cuotaapagar = $total_cancelada_cuotaapagar+$value->cuotaapagar;
                    $total_cancelada_abono = $total_cancelada_abono+$value->abono;
                    $total_cancelada_cuotaapagartotal = $total_cancelada_cuotaapagartotal+$value->cuotaapagartotal;
                }  
                $i++;
            }
            
            // mora pendiente - se cobra en la ultima cuota, si no se ha sustentando
            $ultimacuota = '';
            $morapendientefinal = 0;
            if($creditosolicitud->numerocuota==$hastacuota){
                $morapendientefinal = $morapendiente;
                $ultimacuota = 'ok';
            }
  
            
            $total_interesdescuento = DB::table('s_prestamo_cobranza')
                ->where('s_prestamo_cobranza.idprestamo_credito', $creditosolicitud->id)
                ->sum('s_prestamo_cobranza.cronograma_interesdescuento');
            
  
            $select_amortizacionrestante = number_format($select_amortizacionrestante, 2, '.', '');
            $select_amortizacion = number_format($select_amortizacion, 2, '.', '');
            $select_interesrestante = number_format($select_interesrestante, 3, '.', '');
            //$select_interesdescuento = number_format($select_interesdescuento, 3, '.', '');
            $select_interes = number_format($select_interes, 3, '.', '');
            $select_cuota = number_format($select_cuota, 2, '.', '');
            $select_atrasorestante = number_format($select_atrasorestante, 2, '.', '');
            $select_atraso = number_format($select_atraso, 2, '.', '');
            $select_mora = number_format($select_mora, 2, '.', '');
            $select_moradescontado = number_format($select_moradescontado, 2, '.', '');
            $select_moraapagar = number_format($select_moraapagar, 2, '.', '');
            $select_cuotapago = number_format($select_cuotapago, 2, '.', '');
            $select_acuentaanterior = number_format($select_acuentaanterior, 2, '.', '');
            $select_acuentaproxima = number_format($select_acuentaproxima, 2, '.', '');
            //$select_acuentatotal = number_format($select_acuentaanterior+$select_acuentaproxima, 2, '.', '');
            $select_cuotaapagar = number_format($morapendientefinal+$select_cuotaapagar-$descuentointeres, 2, '.', '');
            if(configuracion($idtienda,'prestamo_redondeoefectivo')['valor']==1){
                $select_cuotaapagarredondeado = number_format(round_menor($select_cuotaapagar,1), 2, '.', '');
            }elseif(configuracion($idtienda,'prestamo_redondeoefectivo')['valor']==3){
                $select_cuotaapagarredondeado = number_format(round_mayor($select_cuotaapagar,1), 2, '.', '');
            }else{
                $select_cuotaapagarredondeado = number_format(round($select_cuotaapagar,1), 2, '.', '');
            }
            //$select_cuotaapagarredondeado = number_format(round($select_cuotaapagar, 1), 2, '.', '');
            $select_acuentacuotaapagar = number_format($select_ultimaacuenta+$select_cuotaapagar, 2, '.', '');
            $select_acuentacuotaapagarredondeado = number_format($select_acuentacuotaapagar, 2, '.', '');
            $select_abono = number_format($select_abono, 2, '.', '');
            $select_cuotaapagartotal = number_format($select_cuotaapagartotal, 2, '.', '');
          
            $total_vencida_cuota = number_format($total_vencida_cuota, 2, '.', '');
            $total_vencida_atraso = $total_vencida_atraso;
            $total_vencida_mora = number_format($total_vencida_mora, 2, '.', '');
            $total_vencida_moradescontado = number_format($total_vencida_moradescontado, 2, '.', '');
            $total_vencida_moraapagar = number_format($total_vencida_moraapagar, 2, '.', '');
            $total_vencida_cuotapago = number_format($total_vencida_cuotapago, 2, '.', '');
            $total_vencida_acuenta = number_format($total_vencida_acuenta, 2, '.', '');
            $total_vencida_cuotaapagar = number_format($total_vencida_cuotaapagar, 2, '.', '');
            $total_vencida_abono = number_format($total_vencida_abono, 2, '.', '');
            $total_vencida_cuotaapagartotal = number_format($total_vencida_cuotaapagartotal, 2, '.', '');
  
            $total_restante_cuota = number_format($total_restante_cuota, 2, '.', '');
            $total_restante_atraso = $total_restante_atraso;
            $total_restante_mora = number_format($total_restante_mora, 2, '.', '');
            $total_restante_moradescontado = number_format($total_restante_moradescontado, 2, '.', '');
            $total_restante_moraapagar = number_format($total_restante_moraapagar, 2, '.', '');
            $total_restante_cuotapago = number_format($total_restante_cuotapago, 2, '.', '');
            $total_restante_acuenta = number_format($total_restante_acuenta, 2, '.', '');
            $total_restante_cuotaapagar = number_format($total_restante_cuotaapagar, 2, '.', '');
            $total_restante_abono = number_format($total_restante_abono, 2, '.', '');
            $total_restante_cuotaapagartotal = number_format($total_restante_cuotaapagartotal, 2, '.', '');
  
            $total_pendiente_cuota = number_format($total_pendiente_cuota, 2, '.', '');
            $total_pendiente_atraso = $total_pendiente_atraso;
            $total_pendiente_mora = number_format($total_pendiente_mora, 2, '.', '');
            $total_pendiente_moradescontado = number_format($total_pendiente_moradescontado, 2, '.', '');
            $total_pendiente_moraapagar = number_format($total_pendiente_moraapagar, 2, '.', '');
            $total_pendiente_cuotapago = number_format($total_pendiente_cuotapago, 2, '.', '');
            $total_pendiente_acuenta = number_format($total_pendiente_acuenta, 2, '.', '');
            $total_pendiente_cuotaapagar = number_format($total_pendiente_cuotaapagar, 2, '.', '');
            $total_pendiente_abono = number_format($total_pendiente_abono, 2, '.', '');
            $total_pendiente_cuotaapagartotal = number_format($total_pendiente_cuotaapagartotal, 2, '.', '');
  
            $total_cancelada_cuota = number_format($total_cancelada_cuota, 2, '.', '');
            $total_cancelada_atraso = $total_cancelada_atraso;
            $total_cancelada_mora = number_format($total_cancelada_mora, 2, '.', '');
            $total_cancelada_moradescontado = number_format($total_cancelada_moradescontado, 2, '.', '');
            $total_cancelada_moraapagar = number_format($total_cancelada_moraapagar, 2, '.', '');
            $total_cancelada_cuotapago = number_format($total_cancelada_cuotapago-$total_interesdescuento, 2, '.', '');
            $total_cancelada_acuenta = number_format($total_cancelada_acuenta, 2, '.', '');
            $total_cancelada_cuotaapagar = number_format($total_cancelada_cuotaapagar, 2, '.', '');
            $total_cancelada_abono = number_format($total_cancelada_abono, 2, '.', '');
            $total_cancelada_cuotaapagartotal = number_format($total_cancelada_cuotaapagartotal, 2, '.', '');
  
    return [
        'creditosolicitud' => $creditosolicitud,
        'html_cuotasrestantes' =>$html_cuotasrestantes,
        'html_cuotasrestantes_selected' =>$html_cuotasrestantes_selected,
        'ultima_cuota_vencida' =>$ultima_cuota_vencida,
        'proximo_vencimiento' =>$proximo_vencimiento,
        'primeratraso' =>$primeratraso,
        'morasolicitado' => number_format($morasolicitado, 2, '.', ''),
        'moraaprobado' => number_format($moraaprobado, 2, '.', ''),
        'morapendiente' => number_format($morapendiente, 2, '.', ''),
        'morarestante' => number_format($morarestante, 2, '.', ''),
        //'moraadescontar' => number_format($morapendiente+$moraaprobado, 2, '.', ''),
        'cuotas_seleccionadas' => $cuotas_seleccionadas,
        'ultimacuota' => $ultimacuota,
        'montorecibido' => $montorecibido,
        'morapendientefinal' => number_format($morapendientefinal, 2, '.', ''),
        'interesdescontado' => number_format($total_interesdescuento, 2, '.', ''),
      
        'select_amortizacionrestante' => $select_amortizacionrestante,
        'select_amortizacion' => $select_amortizacion,
        'select_interesrestante' => $select_interesrestante,
        'select_interesdescuento' => number_format($descuentointeres, 2, '.', ''),
        'select_interes' => $select_interes,
        'select_cuota' => $select_cuota,
        'select_atrasorestante' => $select_atrasorestante,
        'select_atraso' => $select_atraso,
        'select_mora' => $select_mora,
        'select_moradescontado' => $select_moradescontado,
        'select_moraapagar' => $select_moraapagar,
        'select_cuotapago' => $select_cuotapago,
        'select_acuentaanterior' => $select_acuentaanterior,
        'select_acuentaproxima' => $select_acuentaproxima,
        //'select_acuentatotal' => $select_acuentatotal,
        'select_cuotaapagar' => $select_cuotaapagar,
        'select_cuotaapagarredondeado' => $select_cuotaapagarredondeado,
        'select_acuentacuotaapagar' => $select_acuentacuotaapagar,
        'select_acuentacuotaapagarredondeado' => $select_acuentacuotaapagarredondeado,
        'select_abono' => $select_abono,
        'select_cuotaapagartotal' => $select_cuotaapagartotal,
        'select_ultimonumerocuota' => $select_ultimonumerocuota,
      
        'total_cancelada_cuota' => $total_cancelada_cuota,
        'total_cancelada_atraso' => $total_cancelada_atraso,
        'total_cancelada_mora' => $total_cancelada_mora,
        'total_cancelada_moradescontado' => $total_cancelada_moradescontado,
        'total_cancelada_moraapagar' => $total_cancelada_moraapagar,
        'total_cancelada_cuotapago' => $total_cancelada_cuotapago,
        'total_cancelada_acuenta' => $total_cancelada_acuenta,
        'total_cancelada_cuotaapagar' => $total_cancelada_cuotaapagar,
        'total_cancelada_abono' => $total_cancelada_abono,
        'total_cancelada_cuotaapagartotal' => $total_cancelada_cuotaapagartotal,
      
        'total_vencida_cuota' => $total_vencida_cuota,
        'total_vencida_atraso' => $total_vencida_atraso,
        'total_vencida_mora' => $total_vencida_mora,
        'total_vencida_moradescontado' => $total_vencida_moradescontado,
        'total_vencida_moraapagar' => $total_vencida_moraapagar,
        'total_vencida_cuotapago' => $total_vencida_cuotapago,
        'total_vencida_acuenta' => $total_vencida_acuenta,
        'total_vencida_cuotaapagar' => $total_vencida_cuotaapagar,
        'total_vencida_abono' => $total_vencida_abono,
        'total_vencida_cuotaapagartotal' => $total_vencida_cuotaapagartotal,
      
        'total_restante_cuota' => $total_restante_cuota,
        'total_restante_atraso' => $total_restante_atraso,
        'total_restante_mora' => $total_restante_mora,
        'total_restante_moradescontado' => $total_restante_moradescontado,
        'total_restante_moraapagar' => $total_restante_moraapagar,
        'total_restante_cuotapago' => $total_restante_cuotapago,
        'total_restante_acuenta' => $total_restante_acuenta,
        'total_restante_cuotaapagar' => $total_restante_cuotaapagar,
        'total_restante_abono' => $total_restante_abono,
        'total_restante_cuotaapagartotal' => $total_restante_cuotaapagartotal,
      
        'total_pendiente_cuota' => $total_pendiente_cuota,
        'total_pendiente_atraso' => $total_pendiente_atraso,
        'total_pendiente_mora' => $total_pendiente_mora,
        'total_pendiente_moradescontado' => $total_pendiente_moradescontado,
        'total_pendiente_moraapagar' => $total_pendiente_moraapagar,
        'total_pendiente_cuotapago' => $total_pendiente_cuotapago,
        'total_pendiente_acuenta' => $total_pendiente_acuenta,
        'total_pendiente_cuotaapagar' => $total_pendiente_cuotaapagar,
        'total_pendiente_abono' => $total_pendiente_abono,
        'total_pendiente_cuotaapagartotal' => $total_pendiente_cuotaapagartotal,
      
        'cuotas_pendientes' => $cuotas_pendientes,
        'cuotas_vencidas' => $cuotas_vencidas,
        'cuotas_restantes' => $cuotas_restantes,
        'cuotas_pendientes_seleccionados' => $cuotas_pendientes_seleccionados,
        'cuotas_canceladas' => $cuotas_canceladas,
    ];
}
function prestamo_cronograma_fecha($feriados,$frecuencia,$fechainicio,$excluirsabado,$excluirdomingo,$excluirferiado,$numerodias=0){
  
    $nuevafecha = date_create($fechainicio);
                  
    if($frecuencia == 1){
        date_add($nuevafecha, date_interval_create_from_date_string('1 day'));
    }
    elseif($frecuencia == 2){
        date_add($nuevafecha, date_interval_create_from_date_string('1 weeks'));
    }
    elseif($frecuencia == 3){
        date_add($nuevafecha, date_interval_create_from_date_string('2 weeks'));
    }
    elseif($frecuencia == 4){
        date_add($nuevafecha, date_interval_create_from_date_string('1 months'));
    }
    elseif($frecuencia == 5){
        date_add($nuevafecha, date_interval_create_from_date_string('+'.$numerodias.' day'));
    }
          
  
    $fechainicio = date_format($nuevafecha, 'd-m-Y');
    $fechaferiado = date_format($nuevafecha, 'd/m');
    $creditofecha = date_format($nuevafecha, 'd/m/Y');
    $fechanormal = date_format($nuevafecha, 'Y-m-d'); 
 
    foreach($feriados as $value) {
        $dia_mes = str_pad($value->dia, 2, "0", STR_PAD_LEFT).'/'.str_pad($value->mes, 2, "0", STR_PAD_LEFT);
        if( $dia_mes == $fechaferiado && $excluirferiado == 'on'){
          $nuevafecha = strtotime('+1day',strtotime($fechainicio));
          $creditofecha = date('d/m/Y',$nuevafecha);
          $fechainicio = date('d-m-Y',$nuevafecha);
          $fechaferiado = date('d/m',$nuevafecha);
          $fechanormal = date('Y-m-d',$nuevafecha); 
        }
    } 
    if(date('l', strtotime($fechainicio))=='Saturday' && $excluirsabado=='on') {
        $nuevafecha = strtotime('+1day',strtotime($fechainicio));
        $creditofecha = date('d/m/Y',$nuevafecha);
        $fechainicio = date('d-m-Y',$nuevafecha);
        $fechaferiado = date('d/m',$nuevafecha);
        $fechanormal = date('Y-m-d',$nuevafecha);
    }
    if(date('l', strtotime($fechainicio))=='Sunday' && $excluirdomingo=='on') {
        $nuevafecha = strtotime('+1day',strtotime($fechainicio));
        $creditofecha = date('d/m/Y',$nuevafecha);
        $fechainicio = date('d-m-Y',$nuevafecha);
        $fechaferiado = date('d/m',$nuevafecha);
        $fechanormal = date('Y-m-d',$nuevafecha);
      
        foreach($feriados as $value) {
            $dia_mes = str_pad($value->dia, 2, "0", STR_PAD_LEFT).'/'.str_pad($value->mes, 2, "0", STR_PAD_LEFT);
            if( $dia_mes == $fechaferiado && $excluirferiado == 'on'){
              $nuevafecha = strtotime('+1day',strtotime($fechainicio));
              $creditofecha = date('d/m/Y',$nuevafecha);
              $fechainicio = date('d-m-Y',$nuevafecha);
              $fechaferiado = date('d/m',$nuevafecha);
              $fechanormal = date('Y-m-d',$nuevafecha); 
            }
        } 
    }
  
    return [
        'nueva_fecha' => $nuevafecha,
        'credito_fecha' => $creditofecha,
        'fecha_inicio' => $fechainicio,
        'fecha_feriado' => $fechaferiado,
        'fecha_normal' => $fechanormal,
    ];
}
function prestamo_resultado_solicitud($idtienda,$idcredito){
            $prestamocredito = DB::table('s_prestamo_credito')
                ->leftJoin('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_credito.idprestamo_frecuencia')
                ->leftJoin('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
                ->where([
                  ['s_prestamo_credito.id', $idcredito],
                  ['s_prestamo_credito.idtienda', $idtienda]
                ])
                ->select(
                  's_prestamo_credito.*',
                  's_prestamo_frecuencia.nombre as frecuencia_nombre',
                  's_prestamo_frecuencia.id as idprestamo_frecuencia',
                  's_moneda.simbolo as monedasimbolo',
                )
                ->first();
            $s_prestamo_creditolaboral = DB::table('s_prestamo_creditolaboral')
                ->where('s_prestamo_creditolaboral.idtienda', $idtienda)
                ->where('s_prestamo_creditolaboral.idprestamo_credito', $idcredito)
                ->get();
            $venta = 0;
            $ingreso = 0;
            $servicio = 0;
            $ingresototal = 0;
            $compra = 0;
            $utilidad_bruta = 0;
            $egresogasto = 0;
            $utilidad_operativa = 0;
            $egresopago = 0;
            $utilidad_neta = 0;
            $otroingreso = 0;
            $otrogasto = 0;
            $egresogastofamiliar = 0;
            $excedentenetomensual = 0;
            foreach($s_prestamo_creditolaboral as $value){
                $venta = $venta+$value->venta;
                $ingreso = $ingreso+$value->ingreso;
                $servicio = $servicio+$value->servicio;
                $ingresototal = $ingresototal+$value->ingresototal;
                $compra = $compra+$value->compra;
                $utilidad_bruta = $utilidad_bruta+$value->utilidad_bruta;
                $egresogasto = $egresogasto+$value->egresogasto;
                $utilidad_operativa = $utilidad_operativa+$value->utilidad_operativa;
                $egresopago = $egresopago+$value->egresopago;
                $utilidad_neta = $utilidad_neta+$value->utilidad_neta;
                $otroingreso = $otroingreso+$value->otroingreso;
                $otrogasto = $otrogasto+$value->otrogasto;
                $egresogastofamiliar = $egresogastofamiliar+$value->egresogastofamiliar;
                $excedentenetomensual = $excedentenetomensual+$value->ingresomensual;
            }
  
            $cuotamensualizada = 0;
            if($prestamocredito->idprestamo_frecuencia==1){
                if($prestamocredito->numerocuota<30){
                    $cuotamensualizada = $prestamocredito->cuota*$prestamocredito->numerocuota;
                }else{
                    $cuotamensualizada = $prestamocredito->cuota*30;
                }
            }
            elseif($prestamocredito->idprestamo_frecuencia==2){
                if($prestamocredito->numerocuota<4){
                    $cuotamensualizada = $prestamocredito->cuota*$prestamocredito->numerocuota;
                }else{
                    $cuotamensualizada = $prestamocredito->cuota*4;
                }
            }
            elseif($prestamocredito->idprestamo_frecuencia==3){
                if($prestamocredito->numerocuota<2){
                    $cuotamensualizada = $prestamocredito->cuota*$prestamocredito->numerocuota;
                }else{
                    $cuotamensualizada = $prestamocredito->cuota*2;
                }
            }
            elseif($prestamocredito->idprestamo_frecuencia==4){
                $cuotamensualizada = $prestamocredito->cuota;
            }
            elseif($prestamocredito->idprestamo_frecuencia==5){
                if($prestamocredito->numerocuota<30){
                    $cuotamensualizada = $prestamocredito->cuota*$prestamocredito->numerocuota;
                }else{
                    $cuotamensualizada = $prestamocredito->cuota*30;
                }
                $cuotamensualizada = $prestamocredito->cuota*30;
            }
  
            $resultado = 'DESAPROBADO';
            if($cuotamensualizada<$excedentenetomensual){
                $resultado = 'APROBADO';
            }
  
            return [
                'resultado' => $resultado,
                'prestamocredito' => $prestamocredito,
                'cuotamensualizada' => number_format($cuotamensualizada, 2, '.', ''),
                'total_laboralventa' => number_format($venta, 2, '.', ''),
                'total_laboralingreso' => number_format($ingreso, 2, '.', ''),
                'total_laboralservicio' => number_format($servicio, 2, '.', ''),
                'total_laboralcompra' => number_format($compra, 2, '.', ''),
                'total_laboralingresototal' => number_format($ingresototal, 2, '.', ''),
                'total_laboralutilidad_bruta' => number_format($utilidad_bruta, 2, '.', ''),
                'total_laboralegresogasto' => number_format($egresogasto, 2, '.', ''),
                'total_laboralutilidad_operativa' => number_format($utilidad_operativa, 2, '.', ''),
                'total_laboralegresopago' => number_format($egresopago, 2, '.', ''),
                'total_laboralutilidad_neta' => number_format($utilidad_neta, 2, '.', ''),
                'total_laboralotroingreso' => number_format($otroingreso, 2, '.', ''),
                'total_laboralotrogasto' => number_format($otrogasto, 2, '.', ''),
                'total_laboralegresogastofamiliares' => number_format($egresogastofamiliar, 2, '.', ''),
                'total_laboralexcedentenetomensual' => number_format($excedentenetomensual, 2, '.', '')
            ]; 
}
function prestamo_registrar_tranferenciacartera($idtienda,$idorigen,$iddestino,$idcliente,$estado=1){
    $idprestamocartera = DB::table('s_prestamo_cartera')->insertGetId([
        'fecharegistro' => Carbon::now(),
        'idasesororigen' => $idorigen,
        'idasesordestino' => $iddestino,
        'iduserscliente' => $idcliente,
        'idestadotransferenciacartera' => $estado, // 1=registro,2=transferencia
        'idtienda' => $idtienda,
        'idestado' => 1
    ]);
    DB::table('users')->whereId($idcliente)->update([
        'idprestamocartera' => $idprestamocartera
    ]);
}

function prestamo_registrar_mora($idtienda,$idcredito,$idresponsable,$moradescuento,$moradescuentodetalle,$idprocedencia){
        $prestamo_mora = DB::table('s_prestamo_mora')
            ->where('s_prestamo_mora.idtienda',$idtienda)
            ->where('idprestamo_credito',$idcredito)
            ->first();
        $idprestamo_mora = 0;
        if($prestamo_mora!=''){
            $idprestamo_mora = $prestamo_mora->id;
        }else{
            $prestamo_credito = DB::table('s_prestamo_credito')
                ->where('s_prestamo_credito.idtienda',$idtienda)
                ->whereId($idcredito)
                ->first();

            //$imagendocumento = subir_archivo('/public/backoffice/tienda/'.$idtienda.'/prestamomora/',$request->file('imagendocumento'),'','');

            // obtener ultimo código
            $prestamomora = DB::table('s_prestamo_mora')
                ->where('s_prestamo_mora.idtienda',$idtienda)
                ->orderBy('s_prestamo_mora.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($prestamomora!=''){
                $codigo = $prestamomora->codigo+1;
            }
            // fin obtener ultimo código
          
            $idprestamo_mora = DB::table('s_prestamo_mora')->insertGetId([
                'fecharegistro' => Carbon::now(),
                'codigo' => $codigo,
                'idmoneda' => 1,
                'idcliente' => $prestamo_credito->idcliente,
                'idasesor' => $prestamo_credito->idasesor,
                'idprestamo_credito' => $idcredito,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
        }
        $motivo = '';
        if($moradescuentodetalle!=''){
            $motivo = $moradescuentodetalle;
        }
        DB::table('s_prestamo_moradetalle')->insert([
            'fecharegistro' => Carbon::now(),
            'morapagar' => $moradescuento,
            'moradescontar' => 0,
            'moradescuento' => $moradescuento,
            'motivo' => $motivo,
            'idresponsable' => $idresponsable,
            'idsupervisor' => 0,
            'idprestamo_mora' => $idprestamo_mora,
            'idprestamo_credito' => $idcredito,
            'idprocedencia' => $idprocedencia, // 1 = desde descuento mora, 2 = desde cobranza
            'idtienda' => $idtienda,
            'idestado' => 1
        ]);
}

function prestamo_importar_ultimocredito($idtienda,$idcredito){   
        $prestamocredito_act = DB::table('s_prestamo_credito')
            ->where('s_prestamo_credito.idtienda', $idtienda)
            ->where('s_prestamo_credito.id',$idcredito)
            ->first();
        if($prestamocredito_act->estadoexpediente=='no'){
            $ultimoprestamocredito = DB::table('s_prestamo_credito')
                ->where('s_prestamo_credito.idestado', 1)
                ->where('s_prestamo_credito.idtienda', $idtienda)
                ->where('s_prestamo_credito.id','<>', $idcredito)
                //->where('s_prestamo_credito.estadoexpediente','si')
                ->where('s_prestamo_credito.codigo','<', $prestamocredito_act->codigo)
                ->where('s_prestamo_credito.idcliente',$prestamocredito_act->idcliente)
                ->orderBy('s_prestamo_credito.codigo','desc')
                ->first();
            if($ultimoprestamocredito!=''){
                // DOMICILIO
                $prestamodomicilio = DB::table('s_prestamo_creditodomicilio')
                    ->join('ubigeo', 'ubigeo.id', 's_prestamo_creditodomicilio.idubigeo')
                    ->where('s_prestamo_creditodomicilio.idprestamo_credito',  $ultimoprestamocredito->id)
                    ->where('s_prestamo_creditodomicilio.idtienda',  $idtienda)
                    ->select(
                        's_prestamo_creditodomicilio.*',
                        'ubigeo.nombre as nombre_ubigeo',
                        DB::raw('CONCAT(ubigeo.distrito, ", ", ubigeo.provincia, ", ", ubigeo.departamento) as ubigeoubicacion'),
                    )
                    ->orderBy('s_prestamo_creditodomicilio.id','desc')
                    ->first();
                if($prestamodomicilio!=''){
                  
                    $imagensuministro = duplicar_fichero('/public/backoffice/tienda/'.$idtienda.'/creditodomicilio/',$prestamodomicilio->imagensuministro);
                    $imagenfachada = duplicar_fichero('/public/backoffice/tienda/'.$idtienda.'/creditodomicilio/',$prestamodomicilio->imagenfachada);
                  
                    DB::table('s_prestamo_creditodomicilio')->insert([
                        'fecharegistro' => Carbon::now(),
                        'direccion' => $prestamodomicilio->direccion,
                        'reside_desdemes' => $prestamodomicilio->reside_desdemes,
                        'reside_desdeanio' => $prestamodomicilio->reside_desdeanio,
                        'horaubicacion_de' => $prestamodomicilio->horaubicacion_de,
                        'horaubicacion_hasta' => $prestamodomicilio->horaubicacion_hasta,
                        'mapa_latitud' => $prestamodomicilio->mapa_latitud,
                        'mapa_longitud' => $prestamodomicilio->mapa_longitud,
                        'referencia' => $prestamodomicilio->referencia,
                        'imagensuministro' => $imagensuministro,
                        'imagenfachada' => $imagenfachada,
                        'idubigeo' => $prestamodomicilio->idubigeo,
                        'idtipopropiedad' => $prestamodomicilio->idtipopropiedad,
                        'iddeudapagoservicio' => $prestamodomicilio->iddeudapagoservicio,
                        'idprestamo_credito' => $idcredito,
                        'idtienda' => $idtienda,
                        'idestado' => 1
                    ]);
                }

                $relaciones = DB::table('s_prestamo_creditorelacion')
                    ->join('s_prestamo_tiporelacion', 's_prestamo_tiporelacion.id', 's_prestamo_creditorelacion.idprestamo_tiporelacion')
                    ->where([
                        ['s_prestamo_creditorelacion.idprestamo_credito', $ultimoprestamocredito->id],
                        ['s_prestamo_creditorelacion.idtienda', $idtienda],
                    ])
                    ->select(
                        's_prestamo_creditorelacion.*',
                        's_prestamo_tiporelacion.nombre as nombre_tiporelacion'
                    )
                    ->orderBy('s_prestamo_creditorelacion.id','asc')
                    ->get();

                foreach($relaciones as $value){
                    DB::table('s_prestamo_creditorelacion')->insert([
                        'numerotelefono' => $value->numerotelefono,
                        'comentario' => $value->comentario,
                        'personanombre' => $value->personanombre,
                        'idprestamo_tiporelacion' => $value->idprestamo_tiporelacion,
                        'idprestamo_credito' => $idcredito,
                        'idtienda' => $idtienda,
                    ]);
                }

                $prestamodomicilioimagen = DB::table('s_prestamo_creditodomicilioimagen')
                    ->where('s_prestamo_creditodomicilioimagen.idprestamo_credito', $ultimoprestamocredito->id)
                    ->where('s_prestamo_creditodomicilioimagen.idtienda',  $idtienda)
                    ->orderBy('s_prestamo_creditodomicilioimagen.id','asc')
                    ->get();

                foreach($prestamodomicilioimagen as $value){
                    
                    $imagen = duplicar_fichero('/public/backoffice/tienda/'.$idtienda.'/creditodomicilio/',$value->imagen);
                  
                    DB::table('s_prestamo_creditodomicilioimagen')->insert([
                      'fecharegistro'     => Carbon::now(),
                      'orden'             => $value->orden,
                      'imagen'            => $imagen,
                      'idprestamo_credito'=> $idcredito,
                      'idtienda'          => $idtienda,
                      'idestado'          => 1,
                    ]);
                }

                // LABORAL
                $prestamolaboral = DB::table('s_prestamo_creditolaboral')
                    ->leftJoin('s_prestamo_giro', 's_prestamo_giro.id', 's_prestamo_creditolaboral.idprestamo_giro')
                    ->leftJoin('ubigeo', 'ubigeo.id', 's_prestamo_creditolaboral.idubigeo')
                    ->where('s_prestamo_creditolaboral.idprestamo_credito', $ultimoprestamocredito->id)
                    ->where('s_prestamo_creditolaboral.idtienda', $idtienda)
                    ->select(
                        's_prestamo_creditolaboral.*',
                        's_prestamo_giro.nombre as nombre_giro',
                        'ubigeo.nombre as nombre_ubigeo',
                        DB::raw('IF(s_prestamo_creditolaboral.idfuenteingreso = 1,
                            "Dependiente", "Independiente") as fuenteingreso')
                    )
                    ->orderBy('s_prestamo_creditolaboral.id','desc')
                    ->first();
                if($prestamolaboral!=''){
                  
                    $imagensuministro = duplicar_fichero('/public/backoffice/tienda/'.$idtienda.'/creditolaboral/',$prestamolaboral->imagensuministro);
                    $imagenfachada = duplicar_fichero('/public/backoffice/tienda/'.$idtienda.'/creditolaboral/',$prestamolaboral->imagenfachada);
                  
                    $idprestamocreditolaboral = DB::table('s_prestamo_creditolaboral')->insertGetId([
                            'fecharegistro' => Carbon::now(),
                            'venta' => $prestamolaboral->venta,
                            'ingreso' => $prestamolaboral->ingreso,
                            'servicio' => $prestamolaboral->servicio,
                            'ingresototal' => $prestamolaboral->ingresototal,
                            'compra' => $prestamolaboral->compra,
                            'utilidad_bruta' => $prestamolaboral->utilidad_bruta,
                            'egresogasto' => $prestamolaboral->egresogasto,
                            'utilidad_operativa' => $prestamolaboral->utilidad_operativa,
                            'egresopago' => $prestamolaboral->egresopago,
                            'utilidad_neta' => $prestamolaboral->utilidad_neta,
                            'otroingreso' => $prestamolaboral->otroingreso,
                            'otrogasto' => $prestamolaboral->otrogasto,
                            'egresogastofamiliar' => $prestamolaboral->egresogastofamiliar,
                            'ingresomensual' => $prestamolaboral->ingresomensual,
                            'actividad' => $prestamolaboral->actividad,
                            'direccion' => $prestamolaboral->direccion,
                            'labora_desdemes' => $prestamolaboral->labora_desdemes,
                            'labora_desdeanio' => $prestamolaboral->labora_desdeanio,
                            'labora_lunes' => $prestamolaboral->labora_lunes,
                            'labora_martes' => $prestamolaboral->labora_martes,
                            'labora_miercoles' => $prestamolaboral->labora_miercoles,
                            'labora_jueves' => $prestamolaboral->labora_jueves,
                            'labora_viernes' => $prestamolaboral->labora_viernes,
                            'labora_sabados' => $prestamolaboral->labora_sabados,
                            'labora_domingos' => $prestamolaboral->labora_domingos,
                            'mapa_latitud' => $prestamolaboral->mapa_latitud,
                            'mapa_longitud' => $prestamolaboral->mapa_longitud,
                            'referencia' => $prestamolaboral->referencia,
                            'imagensuministro' => $imagensuministro,
                            'imagenfachada' => $imagenfachada,
                            'nombrenegocio' => $prestamolaboral->nombrenegocio,
                            'estadoficharuc' => $prestamolaboral->estadoficharuc,
                            'rucficharuc' => $prestamolaboral->rucficharuc,
                            'emisioncomprobante' => $prestamolaboral->emisioncomprobante,
                            'estadolicenciafuncionamiento' => $prestamolaboral->estadolicenciafuncionamiento,
                            'codigolicenciafuncionamiento' => $prestamolaboral->codigolicenciafuncionamiento,
                            'estadoreciboagua' => $prestamolaboral->estadoreciboagua,
                            'codigoreciboagua' => $prestamolaboral->codigoreciboagua,
                            'estadoreciboluz' => $prestamolaboral->estadoreciboluz,
                            'codigoreciboluz' => $prestamolaboral->codigoreciboluz,
                            'estadoboletacompra' => $prestamolaboral->estadoboletacompra,
                            'estadocontratoalquiler' => $prestamolaboral->estadocontratoalquiler,
                            'duenocontratoalquiler' => $prestamolaboral->duenocontratoalquiler,
                            'idubigeo' => $prestamolaboral->idubigeo,
                            'idprestamo_giro' => $prestamolaboral->idprestamo_giro,
                            'idfuenteingreso' => $prestamolaboral->idfuenteingreso,
                            'idprestamo_credito' => $idcredito,
                            'idtienda' => $idtienda,
                            'idestado' => 1
                        ]);

                    $total_laboralventa = DB::table('s_prestamo_creditolaboralventa')
                        ->where('s_prestamo_creditolaboralventa.s_idprestamo_creditolaboral', $prestamolaboral->id)
                        ->orderBy('s_prestamo_creditolaboralventa.id','asc')
                        ->get();
                    $total_laboralcompra = DB::table('s_prestamo_creditolaboralcompra')
                        ->where('s_prestamo_creditolaboralcompra.s_idprestamo_creditolaboral', $prestamolaboral->id)
                        ->orderBy('s_prestamo_creditolaboralcompra.id','asc')
                        ->get();
                    $total_laboralingreso = DB::table('s_prestamo_creditolaboralingreso')
                        ->where('s_prestamo_creditolaboralingreso.s_idprestamo_creditolaboral', $prestamolaboral->id)
                        ->orderBy('s_prestamo_creditolaboralingreso.id','asc')
                        ->get();
                    $total_laboralegresogasto = DB::table('s_prestamo_creditolaboralegresogasto')
                        ->where('s_prestamo_creditolaboralegresogasto.s_idprestamo_creditolaboral', $prestamolaboral->id)
                        ->orderBy('s_prestamo_creditolaboralegresogasto.id','asc')
                        ->get();
                    $total_laboralegresogastofamiliar = DB::table('s_prestamo_creditolaboralegresogastofamiliar')
                        ->where('s_prestamo_creditolaboralegresogastofamiliar.s_idprestamo_creditolaboral', $prestamolaboral->id)
                        ->orderBy('s_prestamo_creditolaboralegresogastofamiliar.id','asc')
                        ->get();
                    $total_laboralegresopago = DB::table('s_prestamo_creditolaboralegresopago')
                        ->where('s_prestamo_creditolaboralegresopago.s_idprestamo_creditolaboral', $prestamolaboral->id)
                        ->orderBy('s_prestamo_creditolaboralegresopago.id','asc')
                        ->get();
                    $total_laboralotroingreso = DB::table('s_prestamo_creditolaboralotroingreso')
                        ->where('s_prestamo_creditolaboralotroingreso.s_idprestamo_creditolaboral', $prestamolaboral->id)
                        ->orderBy('s_prestamo_creditolaboralotroingreso.id','asc')
                        ->get();
                    $total_laboralotrogasto = DB::table('s_prestamo_creditolaboralotrogasto')
                        ->where('s_prestamo_creditolaboralotrogasto.s_idprestamo_creditolaboral', $prestamolaboral->id)
                        ->orderBy('s_prestamo_creditolaboralotrogasto.id','asc')
                        ->get();
                    $total_laboralservicio = DB::table('s_prestamo_creditolaboralservicio')
                        ->where('s_prestamo_creditolaboralservicio.s_idprestamo_creditolaboral', $prestamolaboral->id)
                        ->orderBy('s_prestamo_creditolaboralservicio.id','asc')
                        ->get();
                    foreach($total_laboralingreso as $value){
                            DB::table('s_prestamo_creditolaboralingreso')->insert([
                              'fecharegistro' => Carbon::now(),
                              'monto' => $value->monto,
                              'conceptoingreso' => $value->conceptoingreso,
                              's_idprestamo_creditolaboral' => $idprestamocreditolaboral,
                              'idtienda' => $idtienda,
                              'idestado' => 1
                            ]);
                    }
                    foreach($total_laboralventa as $value){
                            DB::table('s_prestamo_creditolaboralventa')->insert([
                              'fecharegistro' => Carbon::now(),
                              'cantidad' => $value->cantidad,
                              'preciounitario' => $value->preciounitario,
                              'preciototal' => $value->preciototal,
                              'preciototal_semanal' => $value->preciototal_semanal,
                              'preciototal_quincenal' => $value->preciototal_quincenal,
                              'preciototal_mensual' => $value->preciototal_mensual,
                              'producto' => $value->producto,
                              's_idprestamo_creditolaboral' => $idprestamocreditolaboral,
                              'idtienda' => $idtienda,
                              'idestado' => 1
                            ]);
                    }
                    foreach($total_laboralcompra as $value){
                            DB::table('s_prestamo_creditolaboralcompra')->insert([
                              'fecharegistro' => Carbon::now(),
                              'cantidad' => $value->cantidad,
                              'preciounitario' => $value->preciounitario,
                              'preciototal' => $value->preciototal,
                              'preciototal_semanal' => $value->preciototal_semanal,
                              'preciototal_quincenal' => $value->preciototal_quincenal,
                              'preciototal_mensual' => $value->preciototal_mensual,
                              'producto' => $value->producto,
                              's_idprestamo_creditolaboral' => $idprestamocreditolaboral,
                              'idtienda' => $idtienda,
                              'idestado' => 1
                            ]);
                    }
                    foreach($total_laboralservicio as $value){
                            DB::table('s_prestamo_creditolaboralservicio')->insert([
                              'fecharegistro' => Carbon::now(),
                              'bueno' => $value->bueno,
                              'regular' => $value->regular,
                              'malo' => $value->malo,
                              'promedio' => $value->promedio,
                              'semanal' => $value->semanal,
                              'quincenal' => $value->quincenal,
                              'mensual' => $value->mensual,
                              's_idprestamo_creditolaboral' => $idprestamocreditolaboral,
                              'idtienda' => $idtienda,
                              'idestado' => 1
                            ]);
                    }
                    foreach($total_laboralegresogasto as $value){
                        DB::table('s_prestamo_creditolaboralegresogasto')->insert([
                          'fecharegistro' => Carbon::now(),
                          'monto' => $value->monto,
                          'concepto' => $value->concepto,
                          's_idprestamo_creditolaboral' => $idprestamocreditolaboral,
                          'idtienda' => $idtienda,
                          'idestado' => 1
                        ]);
                    }
                    foreach($total_laboralegresogastofamiliar as $value){
                        DB::table('s_prestamo_creditolaboralegresogastofamiliar')->insert([
                          'fecharegistro' => Carbon::now(),
                          'monto' => $value->monto,
                          'concepto' => $value->concepto,
                          's_idprestamo_creditolaboral' => $idprestamocreditolaboral,
                          'idtienda' => $idtienda,
                          'idestado' => 1
                        ]);
                    }
                    foreach($total_laboralegresopago as $value){
                        DB::table('s_prestamo_creditolaboralegresopago')->insert([
                          'fecharegistro' => Carbon::now(),
                          'monto' => $value->monto,
                          'conceptoegresopago' => $value->conceptoegresopago,
                          's_idprestamo_creditolaboral' => $idprestamocreditolaboral,
                          'idtienda' => $idtienda,
                          'idestado' => 1
                        ]);
                    }
                    foreach($total_laboralotroingreso as $value){
                        DB::table('s_prestamo_creditolaboralotroingreso')->insert([
                          'fecharegistro' => Carbon::now(),
                          'monto' => $value->monto,
                          'conceptootroingreso' => $value->conceptootroingreso,
                          's_idprestamo_creditolaboral' => $idprestamocreditolaboral,
                          'idtienda' => $idtienda,
                          'idestado' => 1
                        ]);
                    }
                    foreach($total_laboralotrogasto as $value){
                        DB::table('s_prestamo_creditolaboralotrogasto')->insert([
                          'fecharegistro' => Carbon::now(),
                          'monto' => $value->monto,
                          'conceptootrogasto' => $value->conceptootrogasto,
                          's_idprestamo_creditolaboral' => $idprestamocreditolaboral,
                          'idtienda' => $idtienda,
                          'idestado' => 1
                        ]);
                    }
                }




                $prestamolaboralnegocioimagen = DB::table('s_prestamo_creditolaboralnegocioimagen')
                    ->where('s_prestamo_creditolaboralnegocioimagen.idprestamo_credito', $ultimoprestamocredito->id)
                    ->where('s_prestamo_creditolaboralnegocioimagen.idtienda',  $idtienda)
                    ->orderBy('s_prestamo_creditolaboralnegocioimagen.orden','asc')
                    ->get();

                foreach($prestamolaboralnegocioimagen as $value){
                    $imagen = duplicar_fichero('/public/backoffice/tienda/'.$idtienda.'/creditolaboral/',$value->imagen);
                    DB::table('s_prestamo_creditolaboralnegocioimagen')->insert([
                      'fecharegistro'     => Carbon::now(),
                      'orden'             => $value->orden,
                      'imagen'            => $imagen,
                      'idprestamo_credito'=> $idcredito,
                      'idtienda'          => $idtienda,
                      'idestado'          => 1,
                    ]);
                }

                $prestamolaboralimagen = DB::table('s_prestamo_creditolaborallicenciafuncionamientoimagen')
                  ->where('s_prestamo_creditolaborallicenciafuncionamientoimagen.idprestamo_credito', $ultimoprestamocredito->id)
                  ->orderBy('s_prestamo_creditolaborallicenciafuncionamientoimagen.orden','asc')
                  ->get();
                foreach($prestamolaboralimagen as $value) {
                    $imagen = duplicar_fichero('/public/backoffice/tienda/'.$idtienda.'/creditolaboral/',$value->imagen);
                    DB::table('s_prestamo_creditolaborallicenciafuncionamientoimagen')->insert([
                      'fecharegistro'     => Carbon::now(),
                      'orden'             => $value->orden,
                      'imagen'            => $imagen,
                      'idprestamo_credito'=> $idcredito,
                      'idtienda'          => $idtienda,
                      'idestado'          => 1,
                    ]);
                }

                $prestamolaboralimagen = DB::table('s_prestamo_creditolaboralcontratoalquilerimagen')
                  ->where('s_prestamo_creditolaboralcontratoalquilerimagen.idprestamo_credito', $ultimoprestamocredito->id)
                  ->orderBy('s_prestamo_creditolaboralcontratoalquilerimagen.orden','asc')
                  ->get();
                foreach($prestamolaboralimagen as $value) {
                    $imagen = duplicar_fichero('/public/backoffice/tienda/'.$idtienda.'/creditolaboral/',$value->imagen);
                    DB::table('s_prestamo_creditolaboralcontratoalquilerimagen')->insert([
                      'fecharegistro'     => Carbon::now(),
                      'orden'             => $value->orden,
                      'imagen'            => $imagen,
                      'idprestamo_credito'=> $idcredito,
                      'idtienda'          => $idtienda,
                      'idestado'          => 1,
                    ]);
                }

                $prestamolaboralimagen = DB::table('s_prestamo_creditolaboralficharucimagen')
                  ->where('s_prestamo_creditolaboralficharucimagen.idprestamo_credito', $ultimoprestamocredito->id)
                  ->orderBy('s_prestamo_creditolaboralficharucimagen.orden','asc')
                  ->get();
                foreach($prestamolaboralimagen as $value) {
                    $imagen = duplicar_fichero('/public/backoffice/tienda/'.$idtienda.'/creditolaboral/',$value->imagen);
                    DB::table('s_prestamo_creditolaboralficharucimagen')->insert([
                      'fecharegistro'     => Carbon::now(),
                      'orden'             => $value->orden,
                      'imagen'            => $imagen,
                      'idprestamo_credito'=> $idcredito,
                      'idtienda'          => $idtienda,
                      'idestado'          => 1,
                    ]);
                }

                $prestamolaboralimagen = DB::table('s_prestamo_creditolaboralreciboaguaimagen')
                  ->where('s_prestamo_creditolaboralreciboaguaimagen.idprestamo_credito', $ultimoprestamocredito->id)
                  ->orderBy('s_prestamo_creditolaboralreciboaguaimagen.orden','asc')
                  ->get();
                foreach($prestamolaboralimagen as $value) {
                    $imagen = duplicar_fichero('/public/backoffice/tienda/'.$idtienda.'/creditolaboral/',$value->imagen);
                    DB::table('s_prestamo_creditolaboralreciboaguaimagen')->insert([
                      'fecharegistro'     => Carbon::now(),
                      'orden'             => $value->orden,
                      'imagen'            => $imagen,
                      'idprestamo_credito'=> $idcredito,
                      'idtienda'          => $idtienda,
                      'idestado'          => 1,
                    ]);
                }

                $prestamolaboralimagen = DB::table('s_prestamo_creditolaboralreciboluzimagen')
                  ->where('s_prestamo_creditolaboralreciboluzimagen.idprestamo_credito', $ultimoprestamocredito->id)
                  ->orderBy('s_prestamo_creditolaboralreciboluzimagen.orden','asc')
                  ->get();
                foreach($prestamolaboralimagen as $value) {
                    $imagen = duplicar_fichero('/public/backoffice/tienda/'.$idtienda.'/creditolaboral/',$value->imagen);
                    DB::table('s_prestamo_creditolaboralreciboluzimagen')->insert([
                      'fecharegistro'     => Carbon::now(),
                      'orden'             => $value->orden,
                      'imagen'            => $imagen,
                      'idprestamo_credito'=> $idcredito,
                      'idtienda'          => $idtienda,
                      'idestado'          => 1,
                    ]);
                }

                $prestamolaboralimagen = DB::table('s_prestamo_creditolaboralboletacompraimagen')
                  ->where('s_prestamo_creditolaboralboletacompraimagen.idprestamo_credito', $ultimoprestamocredito->id)
                  ->orderBy('s_prestamo_creditolaboralboletacompraimagen.orden','asc')
                  ->get();
                foreach($prestamolaboralimagen as $value) {
                    $imagen = duplicar_fichero('/public/backoffice/tienda/'.$idtienda.'/creditolaboral/',$value->imagen);
                    DB::table('s_prestamo_creditolaboralboletacompraimagen')->insert([
                      'fecharegistro'     => Carbon::now(),
                      'orden'             => $value->orden,
                      'imagen'            => $imagen,
                      'idprestamo_credito'=> $idcredito,
                      'idtienda'          => $idtienda,
                      'idestado'          => 1,
                    ]);
                }

                // GARANTIA
                /*$prestamobien = DB::table('s_prestamo_creditobien')
                    ->where('s_prestamo_creditobien.idprestamo_credito', $ultimoprestamocredito->id)
                    ->orderBy('s_prestamo_creditobien.id','asc')
                    ->get();
                foreach($prestamobien as $value) {
                  $idcreditobien = DB::table('s_prestamo_creditobien')->insertGetId([
                    'fecharegistro' => Carbon::now(),
                    'producto' => $value->producto,
                    'descripcion' => $value->descripcion,
                    'valorestimado' => $value->valorestimado,
                    'idprestamo_documento' => $value->idprestamo_documento,
                    'idprestamo_credito' => $idcredito,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                  ]);
                  $prestamolaboralimagen = DB::table('s_prestamo_creditobienimagen')
                    ->where('s_prestamo_creditobienimagen.idprestamo_creditobien', $value->id)
                    ->orderBy('s_prestamo_creditobienimagen.id','asc')
                    ->get();
                  foreach($prestamolaboralimagen as $valueimagen) {
                      $imagen = duplicar_fichero('/public/backoffice/tienda/'.$idtienda.'/creditobien/',$valueimagen->imagen);
                      DB::table('s_prestamo_creditobienimagen')->insert([
                        'fecharegistro'     => Carbon::now(),
                        'orden'             => $valueimagen->orden,
                        'imagen'            => $imagen,
                        'idprestamo_creditobien'=> $idcreditobien,
                        'idtienda'          => $idtienda,
                      ]);
                  }
                }*/

                // SUSTENTO
                $sustento = DB::table('s_prestamo_creditosustento')
                    ->where('idprestamo_credito', $ultimoprestamocredito->id)
                    ->orderBy('s_prestamo_creditosustento.id','desc')
                    ->first();
                if($sustento!=''){
                  DB::table('s_prestamo_creditosustento')->insert([
                      'fecharegistro' => Carbon::now(),
                      'comentarioasesor' => $sustento->comentarioasesor,
                      'destinocredito' => $sustento->destinocredito,
                      'riesgonegocio' => $sustento->riesgonegocio,
                      'destinoexcendete' => $sustento->destinoexcendete,
                      'sustentopropuesta' =>$sustento->sustentopropuesta,
                      'idprestamo_credito' => $idcredito,
                      'idprestamo_calificacion' => $sustento->idprestamo_calificacion,
                      'idprestamo_experienciacredito' => $sustento->idprestamo_experienciacredito,
                      'idprestamo_endeudamientosistema' => $sustento->idprestamo_endeudamientosistema,
                      'idprestamo_inventario' => $sustento->idprestamo_inventario,
                      'idtienda' => $idtienda,
                      'idestado' => 1
                  ]);
                }
            }


                // si con expediente
                DB::table('s_prestamo_credito')->whereId($idcredito)->update([
                    'estadoexpediente' => 'si',
                ]);
        }   
}

function round_menor($zahl,$decimals=2){   
     return floor($zahl*pow(10,$decimals))/pow(10,$decimals);
}
function round_mayor($nb, $decimals=2) {
    return ceil(round($nb *pow(10, $decimals), 1)) /pow(10, $decimals);
}