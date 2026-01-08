<?php
function select_cronograma(
    $idtienda,
    $idcredito,
    $idforma_credito,
    $modalidadproductocredito,
    $numerocuota,
    $descuento_amortizacion = 0,
    $descuento_interes = 0,
    $descuento_comision = 0,
    $descuento_cargo = 0,
    $descuento_penalidad = 0,
    $descuento_tenencia = 0,
    $descuento_compensatorio = 0,
    $pago_acuenta = 0,
    $estadocargo = 1,
    $detallecobranza = '') {
  
    //dd($detallecobranza);
  
    $credito = DB::table('credito')
        ->whereId($idcredito)
        ->first();
  
    /*$total_adelantos = DB::table('credito_adelanto')
        ->where('credito_adelanto.idestadocredito_adelanto',1)
        ->where('credito_adelanto.idcredito',$idcredito)
        ->sum('credito_adelanto.total');
  
    $pago_acuenta = $pago_acuenta+$total_adelantos;*/
  
    $total_cronogramaultimo = DB::table('credito_cronograma')
              ->where('credito_cronograma.idcredito',$idcredito)
              ->where('credito_cronograma.idestadocredito_cronograma',1)
              ->orderBy('credito_cronograma.numerocuota','asc')
              ->limit(1)
              ->first();
            
    $numerocuota_ultimo = 0;
    if($total_cronogramaultimo!=''){
        $numerocuota_ultimo = $total_cronogramaultimo->numerocuota;
    }
  
    $credito_cronograma = DB::table('credito_cronograma')
        ->where('credito_cronograma.idcredito',$idcredito)
        ->orderBy('credito_cronograma.numerocuota','asc')
        ->get();
    
    $penalidad_descuento = 0;
    $tenencia_descuento = 0;

    if($idforma_credito==1){ // Prendaria
        if($modalidadproductocredito=='Interes Simple'){
            $penalidad_descuento = configuracion($idtienda,'penalidad_couta_simple')['valor'];
        }
        elseif($modalidadproductocredito=='Interes Compuesto'){
            $penalidad_descuento = configuracion($idtienda,'penalidad_couta_compuesto')['valor'];
        }

        $garantias = DB::table('credito_garantia')
            ->leftJoin('garantias','garantias.id','credito_garantia.idgarantias')
            ->where('credito_garantia.idcredito', $idcredito)
            ->where('credito_garantia.tipo', 'CLIENTE')
            ->select(
              'credito_garantia.id as id',
              'garantias.idtipogarantia as idtipogarantia'
            )
            ->get();
        foreach($garantias as $value){
            $tipo_garantia = DB::table('tipo_garantia')
            ->where('tipo_garantia.estado', 'ACTIVO')
              ->get();
            foreach($tipo_garantia as $valuetipogarantia){
                if($value->idtipogarantia==$valuetipogarantia->id){
                    $tenencia_descuento = $tenencia_descuento+$valuetipogarantia->penalidad;
                }
            }
        }

    }
    elseif($idforma_credito==2){ // No prendaria
        if($modalidadproductocredito=='Interes Simple'){
            $penalidad_descuento = configuracion($idtienda,'penalidad_couta_simple_noprendaria')['valor'];
        }
        elseif($modalidadproductocredito=='Interes Compuesto'){
            $penalidad_descuento = configuracion($idtienda,'penalidad_couta_compuesto_noprendaria')['valor'];
        }
    }
          
    $tasa_moratoria = configuracion($idtienda,'tasa_moratoria')['valor']!=''?configuracion($idtienda,'tasa_moratoria')['valor']:0;
    $dias_tolerancia_garantia = configuracion($idtienda,'dias_tolerancia_garantia')['valor'];
    //dd($idtienda);
    $dias_maximo_penalidad = configuracion($idtienda,'dias_maximo_penalidad')['valor'];
    $fecha_hoy = new DateTime(Carbon\Carbon::now()->format('Y-m-d'));
  
    $i = 0;
    $ic = 0;
    
    $proximo_vencimiento = '';
    //$saldo_pendientepago = 0;
  
    $select_numerocuota_inicio = 0; 
    $select_numerocuota_fin = 0; 
    $select_numerocuota = 0; 
    $select_ultimacuotacancelada = 0;
  
  
    $select_amortizacion = 0; 
    $select_interes = 0; 
    $select_comision = 0; 
    $select_cargo = 0; 
    $select_cuota = 0; 
    $select_penalidad = 0; 
    $select_tenencia = 0; 
    $select_compensatorio = 0; 
    $select_totalcuota = 0; 
    $select_adelanto = 0; 
  
    $select_pagar_amortizacion = 0; 
    $select_pagar_interes = 0; 
    $select_pagar_comision = 0; 
    $select_pagar_cargo = 0; 
    $select_pagar_cuota = 0; 
    $select_pagar_penalidad = 0; 
    $select_pagar_tenencia = 0; 
    $select_pagar_compensatorio = 0; 
    $select_pagar_totalcuota = 0; 
  
    $select_descontar_amortizacion = 0; 
    $select_descontar_interes = 0; 
    $select_descontar_comision = 0; 
    $select_descontar_cargo = 0; 
    $select_descontar_cuota = 0; 
    $select_descontar_penalidad = 0; 
    $select_descontar_tenencia = 0; 
    $select_descontar_compensatorio = 0; 
    $select_descontar_totalcuota = 0; 
    
    $pagar_amortizacion = 0; 
    $pagar_interes = 0; 
    $pagar_comision = 0; 
    $pagar_cargo = 0; 
    $pagar_cuota = 0; 
    $pagar_penalidad = 0; 
    $pagar_tenencia = 0; 
    $pagar_compensatorio = 0; 
    $pagar_totalcuota = 0; 
    $pagar_acuenta = 0; 
    
    $descontar_amortizacion = 0; 
    $descontar_interes = 0; 
    $descontar_comision = 0; 
    $descontar_cargo = 0; 
    $descontar_cuota = 0; 
    $descontar_penalidad = 0; 
    $descontar_tenencia = 0; 
    $descontar_compensatorio = 0; 
    $descontar_totalcuota = 0; 
    
    $total_amortizacion = 0; 
    $total_interes = 0; 
    $total_comision = 0; 
    $total_cargo = 0; 
    $total_cuota = 0; 
    $total_penalidad = 0; 
    $total_tenencia = 0; 
    $total_compensatorio = 0;   
    $total_penalidad1 = 0; 
    $total_tenencia1 = 0; 
    $total_compensatorio1 = 0;
    $total_totalcuota = 0; 
    $total_acuenta = 0; 
    $total_pagoacuenta = 0;
    
    $total_pagar_amortizacion = 0; 
    $total_pagar_interes = 0; 
    $total_pagar_comision = 0; 
    $total_pagar_cargo = 0; 
    $total_pagar_cuota = 0; 
    $total_pagar_penalidad = 0; 
    $total_pagar_tenencia = 0; 
    $total_pagar_compensatorio = 0; 
    $total_pagar_totalcuota = 0; 
    
    $total_descontar_amortizacion = 0; 
    $total_descontar_interes = 0; 
    $total_descontar_comision = 0; 
    $total_descontar_cargo = 0; 
    $total_descontar_cuota = 0; 
    $total_descontar_penalidad = 0; 
    $total_descontar_tenencia = 0; 
    $total_descontar_compensatorio = 0; 
    $total_descontar_totalcuota = 0; 
    
    $numero_cuota_cancelada = 0;
    $numero_cuota_pendiente = 0;
    $numero_cuota_vencida = 0;
    $cuota_pagada = 0;
    $cuota_pendiente = 0;
    $cuota_vencida = 0;
    $saldo_capital = 0;
  
    $pagocuota_adelantado = 0;
    $pagocuota_puntual = 0;
    $pagocuota_vencido = 0;
  
    $total_atraso = 0;
    $total_moracredito = 0;
    $numero_moracredito = 0;
  
    $ultimo_atraso = 0;
    $ultimo_atraso_valid = 0;
  
    $si = 0;
  
    $data = [];
    foreach($credito_cronograma as $value){
      
        
        
        // adelanto
        $credito_adelanto = DB::table('credito_adelanto')
            ->whereIn('credito_adelanto.idestadocredito_adelanto',[1,2])
            ->where('credito_adelanto.numerocuota',$value->numerocuota)
            ->where('credito_adelanto.idcredito',$idcredito)
            ->orderBy('credito_adelanto.id','desc')
            ->first();
        
        $amortizacion_delanto_pagado  = 0;
      
        $amortizacion_delanto  = 0;
        $interes_delanto  = 0;
        $comision_delanto  = 0;
        $cargo_delanto  = 0;
        $cuota_delanto  = 0;
        $atraso_dias_adelanto = 0;
          
        if($credito_adelanto!=''){
            $amortizacion_delanto_pagado  = $amortizacion_delanto_pagado+$credito_adelanto->capital;
            $amortizacion_delanto  = $amortizacion_delanto+$credito_adelanto->total_capital;
            $interes_delanto  = $interes_delanto+$credito_adelanto->total_interes;
            $comision_delanto  = $comision_delanto+$credito_adelanto->total_comision;
            $cargo_delanto  = $cargo_delanto+$credito_adelanto->total_cargo;
            $cuota_delanto  = $cuota_delanto+($amortizacion_delanto+$interes_delanto+$comision_delanto+$cargo_delanto);
            //$atraso_dias_adelanto  = $atraso_dias_adelanto+$credito_adelanto->atraso;
            //$pago_acuenta = $pago_acuenta+$credito_adelanto->total;
            
        }
      
        
        $total_adelanto_numcuota = DB::table('credito_adelanto')
            ->whereIn('credito_adelanto.idestadocredito_adelanto',[1,2])
            ->where('credito_adelanto.numerocuota',$value->numerocuota)
            ->where('credito_adelanto.idcredito',$idcredito)
            ->sum('credito_adelanto.total');
      
        
      
        // fin adelanto
      
        $fecha = date_format(date_create($value->fechapago),'d-m-Y');
        $total_penalidad = 0;
        $total_compensatorio = 0;
        $total_tenencia = 0;
        $total_penalidad_real = 0;
        $total_compensatorio_real = 0;
        $total_tenencia_real = 0;
        $checked = '';
        $style = '';
        $disabled = '';

        //$totalcuota  = $value->cuota_real;

        $select = '';
      
        if(($ic==0 && $value->idestadocredito_cronograma==1) or ($ic==0 && $value->idestadocredito_cronograma==3)){
            $select_numerocuota_inicio = $value->numerocuota;
            $ic++;
        }
        
        
        // ---
        
        if($credito_adelanto!='' && $detallecobranza=='detalle_cobranza'){
            $amortizacion  = $amortizacion_delanto;
            $interes       = $interes_delanto;
            $comision      = $comision_delanto;
            $cargo         = $cargo_delanto;
            $cuota         = $cuota_delanto;
        }else{
            $amortizacion  = $value->amortizacion;
            $interes       = $value->interes;
            $comision      = $value->comision;
            $cargo         = $value->cargo;
            $cuota         = $value->cuota_real;
        }
      
        // ---
        $fechapago = new DateTime($value->fechapago);
        // ---
        $interval = $fecha_hoy->diff($fechapago);
        $atraso_dias_real = 0;
        if($fechapago<=$fecha_hoy){
            $atraso_dias = $interval->format('%a');
            if($credito->idestado_congelarcredito==2){
                $atraso_dias = $value->atraso_dias;
            }
            $atraso_dias_real = $atraso_dias;
            // jalar dias descontados
            //if($detallecobranza=='detalle_cobranza'){
            $atraso_dias  = $atraso_dias_adelanto>0?$atraso_dias-$atraso_dias_adelanto:$atraso_dias;
            //}
            
            // interes moratorio
            if($modalidadproductocredito=='Interes Simple' && $atraso_dias>$dias_tolerancia_garantia){
                $interes_diario = ($tasa_moratoria/100)/30;
                $total_compensatorio = $interes_diario*$amortizacion*$atraso_dias;
            }
            elseif($modalidadproductocredito=='Interes Compuesto' && $atraso_dias>$dias_tolerancia_garantia){
                $interes_diario = (pow(1+($tasa_moratoria/100), 1/30))-1;
                $total_compensatorio = $interes_diario*$amortizacion*$atraso_dias;
            }
          
            //real
            if($modalidadproductocredito=='Interes Simple' && $atraso_dias_real>$dias_tolerancia_garantia){
                $interes_diario_real = ($tasa_moratoria/100)/30;
                $total_compensatorio_real = $interes_diario_real*$value->amortizacion*$atraso_dias_real;
            }
            elseif($modalidadproductocredito=='Interes Compuesto' && $atraso_dias_real>$dias_tolerancia_garantia){
                $interes_diario_real = (pow(1+($tasa_moratoria/100), 1/30))-1;
                $total_compensatorio_real = $interes_diario_real*$value->amortizacion*$atraso_dias_real;
            }
          
            /*if($atraso_dias>$dias_tolerancia_garantia){
                $total_penalidad = number_format(($value->cuota_real*($penalidad_descuento/100)), 2, '.', '');
            }*/
          
            // interes compensatorio
            $tasacompensatorio = $credito->tasa_tip;
            if($credito->cuotas>1){
                $tasacompensatorio = $credito->tasa_tem;
            }
          
            if($modalidadproductocredito=='Interes Simple' && $atraso_dias>$dias_tolerancia_garantia){
                $com_interes_diario = ($tasacompensatorio/100)/30;
                $total_penalidad = $com_interes_diario*($value->amortizacion+$value->interes)*$atraso_dias;
            }
            elseif($modalidadproductocredito=='Interes Compuesto' && $atraso_dias>$dias_tolerancia_garantia){
                $com_interes_diario = (pow(1+($tasacompensatorio/100), 1/30))-1;
                $total_penalidad = $com_interes_diario*($value->amortizacion+$value->interes)*$atraso_dias;
            }
          
            $atraso_dias_tenencia = $atraso_dias;
            if($atraso_dias_tenencia>$dias_maximo_penalidad){
              $atraso_dias_tenencia = $dias_maximo_penalidad;
            }
          
            if($atraso_dias_tenencia>$dias_tolerancia_garantia && $atraso_dias_tenencia<=$dias_maximo_penalidad){
                $total_tenencia = number_format($tenencia_descuento*$atraso_dias_tenencia, 2, '.', '');
            }
          
            // interes compensatorio real          
            if($modalidadproductocredito=='Interes Simple' && $atraso_dias_real>$dias_tolerancia_garantia){
                $com_interes_diario_real = ($tasacompensatorio/100)/30;
                $total_penalidad_real = $com_interes_diario_real*($value->amortizacion+$value->interes)*$atraso_dias_real;
            }
            elseif($modalidadproductocredito=='Interes Compuesto' && $atraso_dias_real>$dias_tolerancia_garantia){
                $com_interes_diario_real = (pow(1+($tasacompensatorio/100), 1/30))-1;
                $total_penalidad_real = $com_interes_diario_real*($value->amortizacion+$value->interes)*$atraso_dias_real;
            }
          
            $atraso_dias_tenencia_real = $atraso_dias_real;
            if($atraso_dias_tenencia_real>$dias_maximo_penalidad){
              $atraso_dias_tenencia_real = $dias_maximo_penalidad;
            }
          
            if($atraso_dias_tenencia_real>$dias_tolerancia_garantia && $atraso_dias_tenencia_real<=$dias_maximo_penalidad){
                $total_tenencia_real = number_format($tenencia_descuento*$atraso_dias_tenencia_real, 2, '.', '');
            }
          
          
        }else{
            $atraso_dias = '-'.$interval->format('%a');
        }
      
      
        //amortizacion
        if($descuento_amortizacion>=$amortizacion && $descuento_amortizacion>0){
            $pagar_amortizacion = 0;
            $descuento_amortizacion = $descuento_amortizacion-$amortizacion;
            $descontar_amortizacion = $amortizacion;
        }elseif($descuento_amortizacion<$amortizacion && $descuento_amortizacion>0){
            $pagar_amortizacion = $amortizacion-$descuento_amortizacion;
            $descontar_amortizacion = $descuento_amortizacion;
            $descuento_amortizacion = 0;
        }else{
            $pagar_amortizacion = $amortizacion;
            $descontar_amortizacion = $descuento_amortizacion;
        }
      
        //comision
        if($descuento_comision>=$comision && $descuento_comision>0){
            $pagar_comision = 0;
            $descontar_comision = $descuento_comision-$comision;
            $descontar_comision= $comision;
        }elseif($descuento_comision<$comision && $descuento_comision>0){
            $pagar_comision = $comision-$descuento_comision;
            $descontar_comision = $descuento_comision;
            $descuento_comision = 0;
        }else{
            $pagar_comision = $comision;
            $descontar_comision = $descuento_comision;
        }
      
        //cargo
        if($descuento_cargo>=$cargo && $descuento_cargo>0){
            $pagar_cargo = 0;
            $descuento_cargo = $descuento_cargo-$cargo;
            $descontar_cargo = $cargo;
        }elseif($descuento_cargo<$cargo && $descuento_cargo>0){
            $pagar_cargo = $cargo-$descuento_cargo;
            $descontar_cargo = $descuento_cargo;
            $descuento_cargo = 0;
        }else{
            $pagar_cargo = $cargo;
            $descontar_cargo = $descuento_cargo;
        }
      
      
        //interes
        if($descuento_interes>=$interes && $descuento_interes>0){
            $pagar_interes = 0;
            $descuento_interes = $descuento_interes-$interes;
            $descontar_interes = $interes;
        }elseif($descuento_interes<$interes && $descuento_interes>0){
            $pagar_interes = $interes-$descuento_interes;
            $descontar_interes = $descuento_interes;
            $descuento_interes = 0;
        }else{
            $pagar_interes = $interes;
            $descontar_interes = $descuento_amortizacion;
        }
        
      
        $penalidad     = $total_penalidad;
        $tenencia      = $total_tenencia;
        $compensatorio = $total_compensatorio;
      
        $cuota         = number_format($amortizacion+$comision+$cargo+$interes, 2, '.', '');
        $totalcuota    = number_format($cuota+$penalidad+$tenencia+$compensatorio, 2, '.', '');
      
        $acuenta       = 0;

        if($value->idestadocredito_cronograma==1 or $value->idestadocredito_cronograma==3){
        
    
            //penalidad
            if($descuento_penalidad>=$total_penalidad && $descuento_penalidad>0){
                $pagar_penalidad = 0;
                $descuento_penalidad = $descuento_penalidad-$total_penalidad;
                $descontar_penalidad = $total_penalidad;
            }elseif($descuento_penalidad<$total_penalidad && $descuento_penalidad>0){
                $pagar_penalidad = $total_penalidad-$descuento_penalidad;
                $descontar_penalidad = $descuento_penalidad;
                $descuento_penalidad = 0;
            }else{
                $pagar_penalidad = $total_penalidad;
                $descontar_penalidad = $descuento_penalidad;
            }
            //tenencia
            if($descuento_tenencia>=$total_tenencia && $descuento_tenencia>0){
                $pagar_tenencia = 0;
                $descuento_tenencia = $descuento_tenencia-$total_tenencia;
                $descontar_tenencia = $total_tenencia;
            }elseif($descuento_tenencia<$total_tenencia && $descuento_tenencia>0){
                $pagar_tenencia = $total_tenencia-$descuento_tenencia;
                $descontar_tenencia = $descuento_tenencia;
                $descuento_tenencia = 0;
            }else{
                $pagar_tenencia = $total_tenencia;
                $descontar_tenencia = $descuento_tenencia;
            }
            //compensatorio
            if($descuento_compensatorio>=$total_compensatorio && $descuento_compensatorio>0){
                $pagar_compensatorio = 0;
                $descuento_compensatorio = $descuento_compensatorio-$total_compensatorio;
                $descontar_compensatorio = $total_compensatorio;
            }elseif($descuento_compensatorio<$total_compensatorio && $descuento_compensatorio>0){
                $pagar_compensatorio = $total_compensatorio-$descuento_compensatorio;
                $descontar_compensatorio = $descuento_compensatorio;
                $descuento_compensatorio = 0;
            }else{
                $pagar_compensatorio = $total_compensatorio;
                $descontar_compensatorio = $descuento_compensatorio;
            } 
            
        
            $pagar_cuota  = number_format(($pagar_amortizacion+$pagar_comision+$pagar_cargo+$pagar_interes), 2, '.', '');
            $pagar_totalcuota  = number_format(($pagar_cuota+$pagar_penalidad+$pagar_tenencia+$pagar_compensatorio), 2, '.', '');
        
            $descontar_cuota  = number_format(($descontar_amortizacion+$descontar_comision+$descontar_cargo+$descontar_interes), 2, '.', '');
            $descontar_totalcuota  = number_format(($descontar_cuota+$descontar_penalidad+$descontar_tenencia+$descontar_compensatorio), 2, '.', '');
            //$pago_acuenta = $pago_acuenta+$value->acuenta;
            //$pago_acuenta = $value->acuenta;
          
            $pago_acuenta = number_format($pago_acuenta+$total_adelanto_numcuota, 2, '.', ''); // 0+10
          
            $total_pagoacuenta = $total_pagoacuenta+$pago_acuenta; 
          
            
            //dd($pago_acuenta.'>='.$pagar_totalcuota);
          
            $total_totalcuotareal = $value->cuota_real+$total_penalidad_real+$total_tenencia_real+$total_compensatorio_real;
            //dd($total_totalcuotareal);
            if($pago_acuenta>=$total_totalcuotareal && $pago_acuenta>0){
                $pago_acuenta = $pago_acuenta-$total_totalcuotareal;
                $pagar_acuenta = $total_totalcuotareal;
                //$select = 'selected';
            }elseif($pago_acuenta<$total_totalcuotareal && $pago_acuenta>0){
                $pagar_acuenta = $pago_acuenta;
                $pago_acuenta = 0;
            }else{
                $pagar_acuenta = $pago_acuenta;
            } 
                
            if($value->numerocuota<=$numerocuota){
        
                $select = 'selected';
                $select_numerocuota = $select_numerocuota+1;
                $select_numerocuota_fin = $value->numerocuota;
                
                $select_amortizacion = $select_amortizacion+number_format($amortizacion, 2, '.', ''); 
                $select_interes = $select_interes+number_format($interes, 2, '.', ''); 
                $select_comision = $select_comision+number_format($comision, 2, '.', ''); 
                $select_cargo = $select_cargo+number_format($cargo, 2, '.', ''); 
                $select_cuota = $select_cuota+number_format($cuota, 2, '.', ''); 
                $select_penalidad = $select_penalidad+number_format($penalidad, 2, '.', ''); 
                $select_tenencia = $select_tenencia+number_format($tenencia, 2, '.', ''); 
                $select_compensatorio = $select_compensatorio+number_format($compensatorio, 2, '.', ''); 
                $select_totalcuota = $select_totalcuota+number_format($totalcuota, 2, '.', ''); 
                $select_adelanto = $select_adelanto+number_format($pagar_acuenta-$total_adelanto_numcuota, 2, '.', ''); 
                
                $select_pagar_amortizacion = $select_pagar_amortizacion+number_format($pagar_amortizacion, 2, '.', ''); 
                $select_pagar_interes = $select_pagar_interes+number_format($pagar_interes, 2, '.', ''); 
                $select_pagar_comision = $select_pagar_comision+number_format($pagar_comision, 2, '.', ''); 
                $select_pagar_cargo = $select_pagar_cargo+number_format($pagar_cargo, 2, '.', ''); 
                $select_pagar_cuota = $select_pagar_cuota+number_format($pagar_cuota, 2, '.', ''); 
                $select_pagar_penalidad = $select_pagar_penalidad+number_format($pagar_penalidad, 2, '.', ''); 
                $select_pagar_tenencia = $select_pagar_tenencia+number_format($pagar_tenencia, 2, '.', ''); 
                $select_pagar_compensatorio = $select_pagar_compensatorio+number_format($pagar_compensatorio, 2, '.', ''); 
                $select_pagar_totalcuota = $select_pagar_totalcuota+number_format($pagar_totalcuota, 2, '.', ''); 
                
                $select_descontar_amortizacion = $select_descontar_amortizacion+number_format($descontar_amortizacion, 2, '.', ''); 
                $select_descontar_interes = $select_descontar_interes+number_format($descontar_interes, 2, '.', ''); 
                $select_descontar_comision = $select_descontar_comision+number_format($descontar_comision, 2, '.', ''); 
                $select_descontar_cargo = $select_descontar_cargo+number_format($descontar_cargo, 2, '.', ''); 
                $select_descontar_cuota = $select_descontar_cuota+number_format($descontar_cuota, 2, '.', ''); 
                $select_descontar_penalidad = $select_descontar_penalidad+number_format($descontar_penalidad, 2, '.', ''); 
                $select_descontar_tenencia = $select_descontar_tenencia+number_format($descontar_tenencia, 2, '.', ''); 
                $select_descontar_compensatorio = $select_descontar_compensatorio+number_format($descontar_compensatorio, 2, '.', ''); 
                $select_descontar_totalcuota = $select_descontar_totalcuota+number_format($descontar_totalcuota, 2, '.', ''); 
                
                if($ultimo_atraso_valid==0){
                    $ultimo_atraso = $atraso_dias;
                    $ultimo_atraso_valid++;
                }
      
                //$total_moracredito = $total_moracredito+$amortizacion;
                if($compensatorio>0){
                    $numero_moracredito ++;
                }
              
            }else{
                // proximo vencimiento
                if($si == 0){
                    $proximo_vencimiento = $fecha;
                    $si++;
                }
            }
            
            //---
            $numero_cuota_pendiente++;
            $cuota_pendiente = $cuota_pendiente+$totalcuota;
            $saldo_capital = $saldo_capital+$amortizacion; //($value->acuenta>0?($totalcuota-$value->acuenta-$value->acuenta):0)
            
            if($atraso_dias>=0){
                //$style = 'box-shadow: inset 0 0 0 9999px rgb(244 172 172) !important;';
                
                $numero_cuota_vencida++;
                $cuota_vencida = $cuota_vencida+$totalcuota-$value->acuenta;
            }
            
            
            //$saldo_pendientepago = $saldo_pendientepago+$totalcuota;
        }
        elseif($value->idestadocredito_cronograma==2){
            
            $amortizacion  = $value->amortizacion;
            $interes       = $value->interes;
            $comision      = $value->comision;
            $cargo         = $value->cargo;
            $cuota         = $value->cuota_real;
            $atraso_dias   = $value->atraso_dias;
            $penalidad     = $value->penalidad;
            $tenencia      = $value->tenencia;
            $compensatorio = $value->compensatorio;
            $totalcuota    = $value->totalcuota;
            $pagar_acuenta = $value->acuenta;
            
            //---
            $disabled = 'disabled';
            if($value->atraso_dias>0){
                $style = 'box-shadow: inset 0 0 0 9999px rgb(244 172 172) !important;';
            }else{
                $style = 'box-shadow: inset 0 0 0 9999px rgb(172 244 172) !important;';
            }
            
            $checked = 'checked';
          
            $numero_cuota_cancelada++;
            $cuota_pagada = $cuota_pagada+$totalcuota;
          
            $select_ultimacuotacancelada = $value->numerocuota;
          
            // forma de pago de cuota
            if($value->atraso_dias>0){
                $pagocuota_vencido = $pagocuota_vencido+1;
            }elseif($value->atraso_dias==0){
                $pagocuota_puntual = $pagocuota_puntual+1;
            }elseif($value->atraso_dias<0){
                $pagocuota_adelantado = $pagocuota_adelantado+1;
            }
        }
      
        if($select=='selected'){
            $checked = 'checked';
        }
        
        $seleccionar = '';
        if($value->numerocuota==$numerocuota){
            $seleccionar = 'seleccionar';
        }
      
      
        // acuenta
        $acuenta_amortizacion = 0;
        $acuenta_interes = 0;
        $acuenta_comision = 0;
        $acuenta_cargo = 0;
        $acuenta_penalidad = 0;
        $acuenta_tenencia = 0;
        $acuenta_compensatorio = 0;
      
        $tot_amortizacion = 0;
        $tot_interes = 0;
        $tot_comision = 0;
        $tot_cargo = 0;
        $tot_penalidad = 0;
        $tot_tenencia = 0;
        $tot_compensatorio = 0;
      
      
      
        if($pagar_acuenta>0){
          
            $adelanto_pagar_acuenta = $pagar_acuenta-$total_adelanto_numcuota; 
          
            $tot_amortizacion  = $value->amortizacion;
            $tot_interes       = $value->interes;
            $tot_comision      = $value->comision;
            $tot_cargo         = $value->cargo;
            $tot_penalidad     = $value->penalidad+$penalidad;
            $tot_tenencia      = $value->tenencia+$tenencia;
            $tot_compensatorio = $value->compensatorio+$compensatorio;
            if($credito_adelanto!=''){
                $tot_amortizacion  = $amortizacion_delanto;
                $tot_interes       = $interes_delanto;
                $tot_comision      = $comision_delanto;
                $tot_cargo         = $cargo_delanto;
                $tot_cuota         = $cuota_delanto;
            }
          
            //$tot_amortizacion = number_format($amortizacion, 2, '.', '');
            //$tot_interes = number_format($interes, 2, '.', '');
            //$tot_comision = number_format($comision, 2, '.', '');
            //$tot_cargo = number_format($cargo, 2, '.', '');
            //$tot_penalidad = number_format($penalidad, 2, '.', '');
            //$tot_tenencia = number_format($tenencia, 2, '.', '');
            //$tot_compensatorio = number_format($compensatorio, 2, '.', '');
          
            if($adelanto_pagar_acuenta>$tot_compensatorio && $adelanto_pagar_acuenta>0){
                $adelanto_pagar_acuenta = $adelanto_pagar_acuenta-$tot_compensatorio;
                $acuenta_compensatorio = number_format($tot_compensatorio, 2, '.', '');
                $tot_compensatorio = 0;
            }
            elseif($adelanto_pagar_acuenta<=$tot_compensatorio && $adelanto_pagar_acuenta>0){
                $acuenta_compensatorio = number_format($adelanto_pagar_acuenta, 2, '.', '');
                $tot_compensatorio = $tot_compensatorio-$adelanto_pagar_acuenta;
                $adelanto_pagar_acuenta = 0;
            }
          
            if($adelanto_pagar_acuenta>$tot_tenencia && $adelanto_pagar_acuenta>0){
                $adelanto_pagar_acuenta = $adelanto_pagar_acuenta-$tot_tenencia;
                $acuenta_tenencia = number_format($tot_tenencia, 2, '.', '');
                $tot_tenencia = 0;
            }
            elseif($adelanto_pagar_acuenta<=$tot_tenencia && $adelanto_pagar_acuenta>0){
                $acuenta_tenencia = number_format($adelanto_pagar_acuenta, 2, '.', '');
                $tot_tenencia = $tot_tenencia-$adelanto_pagar_acuenta;
                $adelanto_pagar_acuenta = 0;
            }
          
            if($adelanto_pagar_acuenta>$tot_penalidad && $adelanto_pagar_acuenta>0){
                $adelanto_pagar_acuenta = $adelanto_pagar_acuenta-$tot_penalidad;
                $acuenta_penalidad = number_format($tot_penalidad, 2, '.', '');
                $tot_penalidad = 0;
            }
            elseif($adelanto_pagar_acuenta<=$tot_penalidad && $adelanto_pagar_acuenta>0){
                $acuenta_penalidad = number_format($adelanto_pagar_acuenta, 2, '.', '');
                $tot_penalidad = $tot_penalidad-$adelanto_pagar_acuenta;
                $adelanto_pagar_acuenta = 0;
            }
          
            if($adelanto_pagar_acuenta>$tot_cargo && $adelanto_pagar_acuenta>0){
                $adelanto_pagar_acuenta = $adelanto_pagar_acuenta-$tot_cargo;
                $acuenta_cargo = number_format($tot_cargo, 2, '.', '');
                $tot_cargo = 0;
            }
            elseif($adelanto_pagar_acuenta<=$tot_cargo && $adelanto_pagar_acuenta>0){
                $acuenta_cargo = number_format($adelanto_pagar_acuenta, 2, '.', '');
                $tot_cargo = $tot_cargo-$adelanto_pagar_acuenta;
                $adelanto_pagar_acuenta = 0;
            }
          
            if($adelanto_pagar_acuenta>$tot_comision && $adelanto_pagar_acuenta>0){
                $adelanto_pagar_acuenta = $adelanto_pagar_acuenta-$tot_comision;
                $acuenta_comision = number_format($tot_comision, 2, '.', '');
                $tot_comision = 0;
            }
            elseif($adelanto_pagar_acuenta<=$tot_comision && $adelanto_pagar_acuenta>0){
                $acuenta_comision = number_format($adelanto_pagar_acuenta, 2, '.', '');
                $tot_comision = $tot_comision-$adelanto_pagar_acuenta;
                $adelanto_pagar_acuenta = 0;
            }
          
            if($adelanto_pagar_acuenta>$tot_interes && $adelanto_pagar_acuenta>0){
                $adelanto_pagar_acuenta = $adelanto_pagar_acuenta-$tot_interes;
                $acuenta_interes = number_format($tot_interes, 2, '.', '');
                $tot_interes = 0;
            }
            elseif($adelanto_pagar_acuenta<=$tot_interes && $adelanto_pagar_acuenta>0){
                $acuenta_interes = number_format($adelanto_pagar_acuenta, 2, '.', '');
                $tot_interes = $tot_interes-$adelanto_pagar_acuenta;
                $adelanto_pagar_acuenta = 0;
            }
            
            if($adelanto_pagar_acuenta>$tot_amortizacion && $adelanto_pagar_acuenta>0){    // 12.80>14.50 && 12.80>0
                $adelanto_pagar_acuenta = $adelanto_pagar_acuenta-$tot_amortizacion;
                $acuenta_amortizacion = number_format($tot_amortizacion, 2, '.', '');
                $tot_amortizacion = 0;
            }
            elseif($adelanto_pagar_acuenta<=$tot_amortizacion && $adelanto_pagar_acuenta>0){   // 12.80<=14.50 && 12.80>0
                $acuenta_amortizacion = number_format($adelanto_pagar_acuenta, 2, '.', '');
                $tot_amortizacion = $tot_amortizacion-$adelanto_pagar_acuenta;
                $adelanto_pagar_acuenta = 0;
            }
        }
       
        $data[] = [
            'id' => $value->id,
            'selected' => $select,
            'disabled' => $disabled,
            'checked' => $checked,
            'style' => $style,
            'seleccionar' => $seleccionar,
            'numerocuota' => $value->numerocuota,
            'fecha' => $fecha,
          
            'acuenta_amortizacion' => number_format($acuenta_amortizacion, 2, '.', ''),
            'acuenta_interes' => number_format($acuenta_interes, 2, '.', ''),
            'acuenta_comision' => number_format($acuenta_comision, 2, '.', ''),
            'acuenta_cargo' => number_format($acuenta_cargo, 2, '.', ''),
            'acuenta_penalidad' => number_format($acuenta_penalidad, 2, '.', ''),
            'acuenta_tenencia' => number_format($acuenta_tenencia, 2, '.', ''),
            'acuenta_compensatorio' => number_format($acuenta_compensatorio, 2, '.', ''),
          
            'acuenta_total_amortizacion' => number_format($tot_amortizacion, 2, '.', ''),
            'acuenta_total_interes' => number_format($tot_interes, 2, '.', ''),
            'acuenta_total_comision' => number_format($tot_comision, 2, '.', ''),
            'acuenta_total_cargo' => number_format($tot_cargo, 2, '.', ''),
            'acuenta_total_penalidad' => number_format($tot_penalidad, 2, '.', ''),
            'acuenta_total_tenencia' => number_format($tot_tenencia, 2, '.', ''),
            'acuenta_total_compensatorio' => number_format($tot_compensatorio, 2, '.', ''),
          
            'amortizacion' => number_format($amortizacion, 2, '.', ''),
            'interes' => number_format($interes, 2, '.', ''),
            'comision' => number_format($comision, 2, '.', ''),
            'cargo' => number_format($cargo, 2, '.', ''),
            'cuota' => number_format($cuota, 2, '.', ''),
            'atraso_dias' => $atraso_dias,
            'atraso_dias_real' => $atraso_dias_real,
            'penalidad' => number_format($penalidad, 2, '.', ''),
            'tenencia' => number_format($tenencia, 2, '.', ''),
            'compensatorio' => number_format($compensatorio, 2, '.', ''),
            'totalcuota' => number_format($totalcuota, 2, '.', ''),
          
            'acuenta' => number_format($pagar_acuenta, 2, '.', ''),
            'adelanto' => number_format($pagar_acuenta-$total_adelanto_numcuota, 2, '.', ''),
            
            'pagar_amortizacion' => number_format($pagar_amortizacion, 2, '.', ''),
            'pagar_interes' => number_format($pagar_interes, 2, '.', ''),
            'pagar_comision' => number_format($pagar_comision, 2, '.', ''),
            'pagar_cargo' => number_format($pagar_cargo, 2, '.', ''),
            'pagar_cuota' => number_format($pagar_cuota, 2, '.', ''),
            'pagar_penalidad' => number_format($pagar_penalidad, 2, '.', ''),
            'pagar_tenencia' => number_format($pagar_tenencia, 2, '.', ''),
            'pagar_compensatorio' => number_format($pagar_compensatorio, 2, '.', ''),
            'pagar_totalcuota' => number_format($pagar_totalcuota, 2, '.', ''),
            
            'descontar_amortizacion' => number_format($descontar_amortizacion, 2, '.', ''),
            'descontar_interes' => number_format($descontar_interes, 2, '.', ''),
            'descontar_comision' => number_format($descontar_comision, 2, '.', ''),
            'descontar_cargo' => number_format($descontar_cargo, 2, '.', ''),
            'descontar_cuota' => number_format($descontar_cuota, 2, '.', ''),
            'descontar_penalidad' => number_format($descontar_penalidad, 2, '.', ''),
            'descontar_tenencia' => number_format($descontar_tenencia, 2, '.', ''),
            'descontar_compensatorio' => number_format($descontar_compensatorio, 2, '.', ''),
            'descontar_totalcuota' => number_format($descontar_totalcuota, 2, '.', ''),
            
            'idcredito_cobranzacuota' => $value->idcredito_cobranzacuota,
            'idestadocredito_cronograma' => $value->idestadocredito_cronograma,
        ];
            
        $total_amortizacion = $total_amortizacion+number_format($amortizacion, 2, '.', ''); 
        $total_interes = $total_interes+number_format($interes, 2, '.', ''); 
        $total_comision = $total_comision+number_format($comision, 2, '.', '');
        $total_cargo = $total_cargo+number_format($cargo, 2, '.', '');
        $total_cuota = $total_cuota+number_format($cuota, 2, '.', ''); 
        $total_penalidad1 = $total_penalidad1+number_format($penalidad, 2, '.', ''); 
        $total_tenencia1 = $total_tenencia1+number_format($tenencia, 2, '.', ''); 
        $total_compensatorio1 = $total_compensatorio1+number_format($compensatorio, 2, '.', ''); 
        $total_totalcuota = $total_totalcuota+number_format($totalcuota, 2, '.', ''); 
        $total_acuenta = $total_acuenta+number_format($pagar_acuenta, 2, '.', ''); 
            
        $total_pagar_amortizacion = $total_pagar_amortizacion+number_format($pagar_amortizacion, 2, '.', ''); 
        $total_pagar_interes = $total_pagar_interes+number_format($pagar_interes, 2, '.', ''); 
        $total_pagar_comision = $total_pagar_comision+number_format($pagar_comision, 2, '.', '');
        $total_pagar_cargo = $total_pagar_cargo+number_format($pagar_cargo, 2, '.', '');
        $total_pagar_cuota = $total_pagar_cuota+number_format($pagar_cuota, 2, '.', ''); 
        $total_pagar_penalidad = $total_pagar_penalidad+number_format($pagar_penalidad, 2, '.', ''); 
        $total_pagar_tenencia = $total_pagar_tenencia+number_format($pagar_tenencia, 2, '.', ''); 
        $total_pagar_compensatorio = $total_pagar_compensatorio+number_format($pagar_compensatorio, 2, '.', ''); 
        $total_pagar_totalcuota = $total_pagar_totalcuota+number_format($pagar_totalcuota, 2, '.', ''); 
            
        $total_descontar_amortizacion = $total_descontar_amortizacion+number_format($descontar_amortizacion, 2, '.', ''); 
        $total_descontar_interes = $total_descontar_interes+number_format($descontar_interes, 2, '.', ''); 
        $total_descontar_comision = $total_descontar_comision+number_format($descontar_comision, 2, '.', '');
        $total_descontar_cargo = $total_descontar_cargo+number_format($descontar_cargo, 2, '.', '');
        $total_descontar_cuota = $total_descontar_cuota+number_format($descontar_cuota, 2, '.', ''); 
        $total_descontar_penalidad = $total_descontar_penalidad+number_format($descontar_penalidad, 2, '.', ''); 
        $total_descontar_tenencia = $total_descontar_tenencia+number_format($descontar_tenencia, 2, '.', ''); 
        $total_descontar_compensatorio = $total_descontar_compensatorio+number_format($descontar_compensatorio, 2, '.', ''); 
        $total_descontar_totalcuota = $total_descontar_totalcuota+number_format($descontar_totalcuota, 2, '.', ''); 
      
        $i++;
    }
  //dd($cuota_pendiente);
    return [
        'cronograma' => $data,
        'total_moracredito' => number_format($total_moracredito, 2, '.', ''),
        'numero_moracredito' => $numero_moracredito,
        'ultimo_atraso' => $ultimo_atraso,
      
        'select_numerocuota_inicio' => $select_numerocuota_inicio,
        'select_numerocuota_fin' => $select_numerocuota_fin,
        'select_numerocuota' => $select_numerocuota,
        'select_ultimacuotacancelada' => $select_ultimacuotacancelada,
      
        'pagocuota_adelantado' => $pagocuota_adelantado,
        'pagocuota_vencido' => $pagocuota_vencido,
        'pagocuota_puntual' => $pagocuota_puntual,
      
        'numero_cuota_cancelada' => $numero_cuota_cancelada, 
        'numero_cuota_pendiente' => $numero_cuota_pendiente, 
        'numero_cuota_vencida' => $numero_cuota_vencida, 
        'cuota_pagada' => number_format($cuota_pagada+$pago_acuenta, 2, '.', ''), 
        'cuota_pendiente' => number_format($cuota_pendiente, 2, '.', ''), 
        'cuota_vencida' => number_format($cuota_vencida, 2, '.', ''),
        'saldo_capital' => number_format($saldo_capital, 2, '.', ''),
        'proximo_vencimiento' => $proximo_vencimiento,
        //'saldo_pendientepago' => number_format($saldo_pendientepago, 2, '.', ''),
      
        'select_amortizacion' => number_format($select_amortizacion, 2, '.', ''), 
        'select_interes' => number_format($select_interes, 2, '.', ''), 
        'select_comision' => number_format($select_comision, 2, '.', ''), 
        'select_cargo' => number_format($select_cargo, 2, '.', ''), 
        'select_cuota' => number_format($select_cuota, 2, '.', ''), 
        'select_penalidad' => number_format($select_penalidad, 2, '.', ''), 
        'select_tenencia' => number_format($select_tenencia, 2, '.', ''), 
        'select_compensatorio' => number_format($select_compensatorio, 2, '.', ''), 
        'select_totalcuota' => number_format($select_totalcuota, 2, '.', ''), 
        'select_adelanto' => number_format($select_adelanto, 2, '.', ''), 
      
        'select_pagar_amortizacion' => number_format($select_pagar_amortizacion, 2, '.', ''), 
        'select_pagar_interes' => number_format($select_pagar_interes, 2, '.', ''), 
        'select_pagar_comision' => number_format($select_pagar_comision, 2, '.', ''), 
        'select_pagar_cargo' => number_format($select_pagar_cargo, 2, '.', ''), 
        'select_pagar_cuota' => number_format($select_pagar_cuota, 2, '.', ''), 
        'select_pagar_penalidad' => number_format($select_pagar_penalidad, 2, '.', ''), 
        'select_pagar_tenencia' => number_format($select_pagar_tenencia, 2, '.', ''), 
        'select_pagar_compensatorio' => number_format($select_pagar_compensatorio, 2, '.', ''), 
        'select_pagar_totalcuota' => number_format($select_pagar_totalcuota, 2, '.', ''), 
      
        'select_descontar_amortizacion' => number_format($select_descontar_amortizacion, 2, '.', ''), 
        'select_descontar_interes' => number_format($select_descontar_interes, 2, '.', ''), 
        'select_descontar_comision' => number_format($select_descontar_comision, 2, '.', ''), 
        'select_descontar_cargo' => number_format($select_descontar_cargo, 2, '.', ''), 
        'select_descontar_cuota' => number_format($select_descontar_cuota, 2, '.', ''), 
        'select_descontar_penalidad' => number_format($select_descontar_penalidad, 2, '.', ''), 
        'select_descontar_tenencia' => number_format($select_descontar_tenencia, 2, '.', ''), 
        'select_descontar_compensatorio' => number_format($select_descontar_compensatorio, 2, '.', ''), 
        'select_descontar_totalcuota' => number_format($select_descontar_totalcuota, 2, '.', ''), 
      
        'total_amortizacion' => number_format($total_amortizacion, 2, '.', ''), 
        'total_interes' => number_format($total_interes, 2, '.', ''), 
        'total_comision' => number_format($total_comision, 2, '.', ''), 
        'total_cargo' => number_format($total_cargo, 2, '.', ''), 
        'total_cuota' => number_format($total_cuota, 2, '.', ''), 
        'total_penalidad' => number_format($total_penalidad1, 2, '.', ''), 
        'total_tenencia' => number_format($total_tenencia1, 2, '.', ''), 
        'total_compensatorio' => number_format($total_compensatorio1, 2, '.', ''), 
        'total_totalcuota' => number_format($total_totalcuota, 2, '.', ''),
        'total_acuenta' => number_format($total_acuenta, 2, '.', ''),
        'total_pagoacuenta' => number_format($total_pagoacuenta, 2, '.', ''),
      
        'total_pagar_amortizacion' => number_format($total_pagar_amortizacion, 2, '.', ''), 
        'total_pagar_interes' => number_format($total_pagar_interes, 2, '.', ''), 
        'total_pagar_comision' => number_format($total_pagar_comision, 2, '.', ''), 
        'total_pagar_cargo' => number_format($total_pagar_cargo, 2, '.', ''), 
        'total_pagar_cuota' => number_format($total_pagar_cuota, 2, '.', ''), 
        'total_pagar_penalidad' => number_format($total_pagar_penalidad, 2, '.', ''), 
        'total_pagar_tenencia' => number_format($total_pagar_tenencia, 2, '.', ''), 
        'total_pagar_compensatorio' => number_format($total_pagar_compensatorio, 2, '.', ''), 
        'total_pagar_totalcuota' => number_format($total_pagar_totalcuota, 2, '.', ''),
      
        'total_descontar_amortizacion' => number_format($total_descontar_amortizacion, 2, '.', ''), 
        'total_descontar_interes' => number_format($total_descontar_interes, 2, '.', ''), 
        'total_descontar_comision' => number_format($total_descontar_comision, 2, '.', ''), 
        'total_descontar_cargo' => number_format($total_descontar_cargo, 2, '.', ''), 
        'total_descontar_cuota' => number_format($total_descontar_cuota, 2, '.', ''), 
        'total_descontar_penalidad' => number_format($total_descontar_penalidad, 2, '.', ''), 
        'total_descontar_tenencia' => number_format($total_descontar_tenencia, 2, '.', ''), 
        'total_descontar_compensatorio' => number_format($total_descontar_compensatorio, 2, '.', ''), 
        'total_descontar_totalcuota' => number_format($total_descontar_totalcuota, 2, '.', ''),
    ];
}

function encontrar_valor($idBuscado, $datos) {
    foreach ($datos as $dato) {
        if ($dato->id === $idBuscado) {
            return $dato->valor ?? '0.00';
        }
    }
    return '0.00';
}
function generarTabla($fechaInicio) {
    echo '<thead>';
    echo '<tr>';
    echo '<th rowspan="2" width="100px" style="color: #000 !important;">MESES</th>';

    $meses_abreviados = array('OCT', 'NOV', 'DIC', 'ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEPT');
    foreach ($meses_abreviados as $mes) {
        echo '<th class="text-center" style="color: #000 !important;">' . $mes . '</th>';
    }

    echo '</tr>';
    echo '<tr>';

    $fechaActual = new DateTime($fechaInicio);
    for ($i = 0; $i < 12; $i++) {
        $mesNumero = $fechaActual->format('m');
        echo '<th class="text-center" style="color: #000 !important;">' . $mesNumero . '</th>';
        $fechaActual->modify('+1 month');
    }

    echo '</tr>';
    echo '</thead>';
}
function val_acceso_especial($accesos_especial){
  $submodulos = DB::table('permisoacceso')
                                  ->join('modulo','modulo.id','permisoacceso.idmodulo')
                                  ->where('permisoacceso.idpermiso',user_permiso()->idpermiso)
                                  ->where('modulo.idestado',1)
                                  ->where('modulo.vista','SOLO-ACCESO')
                                  ->where('modulo.controlador',$accesos_especial)
                                  ->first();

  return $submodulos ? true : false;
}
function user_permiso(){
    $tienda_permiso = DB::table('users_permiso')
                                  ->join('permiso','permiso.id','users_permiso.idpermiso')
                                  ->join('tienda','tienda.id','users_permiso.idtienda')
                                  ->where('users_permiso.idusers',Auth::user()->id)
                                  ->where('users_permiso.idsession',2)
                                  ->select(
                                    'users_permiso.*',
                                    'permiso.nombre as nombrepermiso',
                                    'tienda.nombre as nombretienda'
                                  )
                                  ->first();
    if(!$tienda_permiso){
        $tienda_permiso = DB::table('users_permiso')
            ->join('permiso','permiso.id','users_permiso.idpermiso')
            ->join('tienda','tienda.id','users_permiso.idtienda')
            ->where('users_permiso.idusers',Auth::user()->id)
            ->select(
            'users_permiso.*',
            'permiso.nombre as nombrepermiso',
            'tienda.nombre as nombretienda'
            )
            ->first(); 
    }

    return $tienda_permiso;
}
function configuracion($idtienda,$nombre,$idusers=''){
  
    if($idusers!=''){
        $configuracion = DB::table('s_config')
            ->where('s_config.nombre',$nombre)
            ->where('s_config.idtienda',$idtienda)
            ->where('s_config.idusers',$idusers)
            ->first();
    }else{
        $configuracion = DB::table('s_config')
            ->where('s_config.nombre',$nombre)
            ->where('s_config.idtienda',$idtienda)
            ->first();
    }

    $resultado = 'ERROR';
    $nombre = '';
    $valor = '';
  
    if($configuracion!=''){
        $resultado = 'CORRECTO';
        $nombre = $configuracion->nombre;
        $valor = $configuracion->valor;
    }
    
    return [
        'resultado' => $resultado,
        'nombre' => $nombre,
        'valor' => $valor,
    ];
}
function configuracion_update($idtienda,$nombre,$valor,$idusers=''){
    if($valor!=''){
        if($idusers!=''){
        $config = DB::table('s_config')
            ->where('s_config.idtienda',$idtienda)
            ->where('s_config.nombre',$nombre)
            ->where('s_config.idusers',$idusers)
            ->limit(1)
            ->first();
        }else{
        $config = DB::table('s_config')
            ->where('s_config.idtienda',$idtienda)
            ->where('s_config.nombre',$nombre)
            ->limit(1)
            ->first();
        }
        if($config!=''){
            DB::table('s_config')->whereId($config->id)->update([
               'valor' => $valor,
            ]);
        } else {
            if($idusers!=''){
                DB::table('s_config')->insert([
                   'nombre' => $nombre,
                   'valor' => $valor,
                   'idusers' => $idusers,
                   'idtienda' => $idtienda,
                ]);
            }else{
                DB::table('s_config')->insert([
                   'nombre' => $nombre,
                   'valor' => $valor,
                   'idtienda' => $idtienda,
                ]);
            }
        }
    }else{
        configuracion_delete($idtienda,$nombre,$idusers);
    }
}
function configuracion_delete($idtienda,$nombre,$idusers=''){
    if($idusers!=''){
    DB::table('s_config')
        ->where('s_config.idtienda',$idtienda)
        ->where('s_config.nombre',$nombre)
        ->where('s_config.idusers',$idusers)
        ->delete();
    }else{
    DB::table('s_config')
        ->where('s_config.idtienda',$idtienda)
        ->where('s_config.nombre',$nombre)
        ->delete();
    }
}
function sistema_view($idtienda=0){
    if($idtienda==0){
        $idtienda = Auth::user()->idtienda;
    }
    $sistema_plantilla = configuracion($idtienda,'sistema_plantilla')['valor'];
    $view = '';
    if($sistema_plantilla==2){
        $view = 'layouts/backoffice/nuevosistema';
    }else{
        $view = 'layouts/backoffice/sistema';
    }
    return $view;
}
function sistema_modulo($param){
    $usersrolesmodulo = DB::table('usersrolesmodulo')
        ->join('modulo','modulo.id','usersrolesmodulo.idmodulo')
        ->where('usersrolesmodulo.idtienda',$param['idtienda'])
        ->where('usersrolesmodulo.idusers',$param['idusuario'])
        ->where('modulo.opcion',$param['opcion'])
        ->where('modulo.idestado',1)
        ->first();
  
    $resultado = 'ERROR';
    if($usersrolesmodulo!=''){
        $resultado = 'CORRECTO';
    }
    
    return [
        'resultado' => $resultado,
    ];
}
function sistema_inventario($param){
    /* =================================================  SELECCIONANDO ULTIMO SALDO */
    $s_inventario = DB::table('s_inventario')
        ->where('s_inventario.idtienda',$param['idtienda'])
        ->where('s_inventario.idsucursal',$param['idsucursal'])
        ->where('s_inventario.s_idproducto',$param['idproducto'])
        ->orderBy('s_inventario.id','desc')
        ->limit(1)
        ->first();
  
    $saldo_cantidad = 0;
    $saldo_precio = 0;
    $saldo_total = 0;
    if($s_inventario!=''){
        $saldo_cantidad = $s_inventario->saldo_cantidad;
        $saldo_precio = $param['precio'];
        $saldo_total = $param['total'];
    }

    $cantidadpor = $param['cantidad']*$param['por'];
    $entrada_cantidad = 0;
    $entrada_precio = 0;
    $entrada_total = 0;
    $salida_cantidad = 0;
    $salida_precio = 0;
    $salida_total = 0;
    if($param['tipo']=='ENTRADA'){
        $saldo_cantidad = $saldo_cantidad+$cantidadpor;
        $entrada_cantidad = $cantidadpor;
        $entrada_precio = $param['precio'];
        $entrada_total = $param['total'];
    }
    elseif($param['tipo']=='SALIDA'){
        $saldo_cantidad = $saldo_cantidad-$cantidadpor;
        $salida_cantidad = $cantidadpor;
        $salida_precio = $param['precio'];
        $salida_total = $param['total'];
    }
  
    /* =================================================  REGISTRANDO ULTIMO SALDO */
    DB::table('s_inventario')->insert([
        'fecharegistro'     => Carbon\Carbon::now(),
        'responsable'       => $param['responsable'],
        'tipo'              => $param['tipo'],
        'referencia'        => $param['referencia'],
        'concepto'          => $param['concepto'],
        'cantidad'          => $param['cantidad'],
        'entrada_cantidad'  => $entrada_cantidad,
        'entrada_precio'    => $entrada_precio,
        'entrada_total'     => $entrada_total,
        'salida_cantidad'   => $salida_cantidad,
        'salida_precio'     => $salida_precio,
        'salida_total'      => $salida_total,
        'saldo_cantidad'    => $saldo_cantidad,
        'saldo_precio'      => $saldo_precio,
        'saldo_total'       => $saldo_total,
        's_idproducto'      => $param['idproducto'],
        'idsucursal'        => $param['idsucursal'],
        'idtienda'          => $param['idtienda'],
        'idestado'          => 1,
    ]);
                
    /* =================================================  ACTUALIZANDO STOCK */
    sistema_productostock([
        'idtienda'      => $param['idtienda'],
        'idproducto'    => $param['idproducto'],
    ]);       
}
function sistema_productostock($param){           
    /* ######  PRINCIPAL */
    $stock_principal = sistema_stock([
        'idtienda'      => $param['idtienda'],
        'idsucursal'    => 0,
        'idproducto'    => $param['idproducto'],
    ]);
    $tienda = DB::table('tienda')->whereId($param['idtienda'])->first();
    $db_presentacion_sucursal = [];
    $db_presentacion_sucursal[] = [
        'orden'         => 1,
        'sucursal'      => $tienda->nombre,
        'idsucursal'    => 0,
        'stock'         => $stock_principal,
    ];
    /* ######  SUCURSAL */
    $sucursals = DB::table('s_sucursal')
        ->where('s_sucursal.idtienda',$param['idtienda'])
        ->where('s_sucursal.idestado',1)
        ->orderBy('s_sucursal.id','asc')
        ->get();

    $i=2;
    foreach($sucursals as $value){
        $stock = sistema_stock([
            'idtienda'    => $param['idtienda'],
            'idsucursal'  => $value->id,
            'idproducto'  => $param['idproducto'],
        ]);
        $db_presentacion_sucursal[] = [
            'orden'       => $i,
            'sucursal'    => $value->nombre,
            'idsucursal'  => $value->id,
            'stock'       => $stock,
        ];
        $i++;
    }
    DB::table('s_producto')->whereId($param['idproducto'])->update([
        'db_stock'  => json_encode($db_presentacion_sucursal),
    ]);
}
function sistema_stock($param){
    $s_inventario = DB::table('s_inventario')
        ->where('s_inventario.idtienda',$param['idtienda'])
        ->where('s_inventario.idsucursal',$param['idsucursal'])
        ->where('s_inventario.s_idproducto',$param['idproducto'])
        ->orderBy('s_inventario.id','desc')
        ->limit(1)
        ->first();

    $stockactual = 0;
    if($s_inventario!=''){
        $stockactual = $s_inventario->saldo_cantidad;
    }
    return $stockactual;
}
function sistema_apertura($param){
    $apertura = DB::table('s_aperturacierre')
        ->join('s_caja','s_caja.id','s_aperturacierre.s_idcaja')
        ->where('s_aperturacierre.idtienda',$param['idtienda'])
        ->where('s_aperturacierre.idsucursal',$param['idsucursal'])
        ->where('s_aperturacierre.s_idusersrecepcion',$param['idusersrecepcion'])
        ->select('s_aperturacierre.*','s_caja.nombre as cajanombre')
        ->orderBy('s_aperturacierre.id','desc')
        ->limit(1)
        ->first();
  
    $idapertura = 0;
    $resultado = 'CERRADO';
    if($apertura!=''){
        $idapertura = $apertura->id;
        if($apertura->s_idestadoaperturacierre==1 && $apertura->s_idusersrecepcion==Auth::user()->id){
            $resultado = 'PROCESO';
        }elseif($apertura->s_idestadoaperturacierre==2 && $apertura->s_idusersrecepcion==Auth::user()->id && $apertura->fechaconfirmacion==''){
            $resultado = 'PENDIENTE';
        }elseif($apertura->s_idestadoaperturacierre==2 && $apertura->s_idusersrecepcion==Auth::user()->id && $apertura->fechaconfirmacion!=''){
            $resultado = 'ABIERTO';
        }
    }
  
    return [
        'resultado' => $resultado,
        'idapertura'  => $idapertura,
        'apertura'  => $apertura,
    ];
}
function sistema_efectivo($param){
    // INGRESO 
    //--> apertura
    $apertura = DB::table('s_aperturacierre')->whereId($param['idapertura'])->first();

    $total_apertura = 0;
    if($param['idmoneda'] == 1){
        $total_apertura = $apertura->montoasignar;
    }elseif($param['idmoneda'] == 2){
        $total_apertura = $apertura->montoasignar_dolares;
    }
  
    //--> apertura caja auxiliar
    $ingresoaperturaauxiliares = DB::table('s_aperturacierre')
        ->where('s_aperturacierre.s_idaperturacierre',$param['idapertura'])
        ->where('s_aperturacierre.s_idestadoaperturacierre',3)
        ->whereNotNull('s_aperturacierre.fechacierreconfirmacion')
        ->get();
    
    $total_ingresoapertura_auxiliar = 0;
    if($param['idmoneda'] == 1){
        $total_ingresoapertura_auxiliar = $ingresoaperturaauxiliares->sum('montocierre');
    }elseif($param['idmoneda'] == 2){
        $total_ingresoapertura_auxiliar = $ingresoaperturaauxiliares->sum('montocierre_dolares');
    }
  
    //--> movimiento
    $ingresosdiversos  = DB::table('s_movimiento')
        ->where('s_movimiento.tipomovimiento','INGRESO')
        ->where('s_movimiento.idtienda',$param['idtienda'])
        ->where('s_movimiento.s_idmoneda',$param['idmoneda'])
        ->where('s_movimiento.s_idaperturacierre',$param['idapertura'])
        ->where('s_movimiento.idestadomovimiento',2)
        ->where('s_movimiento.idestado',1)
        ->select(
            's_movimiento.*',
            's_movimiento.tipomovimientonombre as conceptomovimientonombre'
        )
        ->orderBy('s_movimiento.id','desc')
        ->get();
  
    $total_ingresosdiversos = $ingresosdiversos->sum('monto');
   
    //--> venta
    $ventas = DB::table('s_venta')
                ->where('s_venta.idtienda',$param['idtienda'])
                ->whereIn('s_venta.s_idestadoventa',[2,3])
                ->where('s_venta.s_idmoneda',$param['idmoneda'])
                ->where('s_venta.s_idaperturacierre',$param['idapertura'])
                ->orderBy('s_venta.id','desc')
                ->get();
  
    $total_ventas = $ventas->sum('totalredondeado');
    
     //--> compradevolucion
    $compradevoluciones = DB::table('s_compradevolucion')
        ->where('s_compradevolucion.idtienda',$param['idtienda'])
        ->where('s_compradevolucion.s_idestado',2)
        ->where('s_compradevolucion.s_idmoneda',$param['idmoneda'])
        ->where('s_compradevolucion.s_idaperturacierre',$param['idapertura'])
        ->orderBy('s_compradevolucion.id','desc')
        ->get();
  
    $total_compradevoluciones = $compradevoluciones->sum('totalredondeado');
    
    // EGRESO
    //--> apertura caja auxiliar
    $egresoaperturaauxiliares = DB::table('s_aperturacierre')
        ->where('s_aperturacierre.s_idaperturacierre',$param['idapertura'])
        ->whereNotNull('s_aperturacierre.fechaconfirmacion')
        ->get();
  
    $total_egresoapertura_auxiliar = 0;
    if ($param['idmoneda']==1) {
      $total_egresoapertura_auxiliar = $egresoaperturaauxiliares->sum('montoasignar');
    }elseif ($param['idmoneda']==2) {
      $total_egresoapertura_auxiliar = $egresoaperturaauxiliares->sum('montoasignar_dolares');
    }
        
    //--> movimiento
    $egresosdiversos  = DB::table('s_movimiento')
        ->where('s_movimiento.tipomovimiento','EGRESO')
        ->where('s_movimiento.idtienda',$param['idtienda'])
        ->where('s_movimiento.s_idmoneda',$param['idmoneda'])
        ->where('s_movimiento.s_idaperturacierre',$param['idapertura'])
        ->where('s_movimiento.idestadomovimiento',2)
        ->where('s_movimiento.idestado',1)
        ->select(
            's_movimiento.*',
            's_movimiento.tipomovimientonombre as conceptomovimientonombre'
        )
        ->orderBy('s_movimiento.id','desc')
        ->get();
    
    $total_egresosdiversos = $egresosdiversos->sum('monto');
    
    //--> compra  
    $compras = DB::table('s_compra')
                ->where('s_compra.idtienda',$param['idtienda'])
                ->where('s_compra.s_idestado',2)
                ->where('s_compra.s_idmoneda',$param['idmoneda'])
                ->where('s_compra.s_idaperturacierre',$param['idapertura'])
                ->orderBy('s_compra.id','desc')
                ->get();
  
    $total_compras = $compras->sum('total');
  
    //--> ventadevolucion
    $ventadevoluciones = DB::table('s_ventadevolucion')
        ->where('s_ventadevolucion.idtienda',$param['idtienda'])
        ->where('s_ventadevolucion.idestado',1)
        ->where('s_ventadevolucion.idestadoventadevolucion',2)
        ->where('s_ventadevolucion.idmoneda',$param['idmoneda'])
        //->where('s_ventadevolucion.s_idaperturacierre',$param['idapertura'])
        ->orderBy('s_ventadevolucion.id','desc')
        ->get();
  
    $total_ventadevoluciones = $ventadevoluciones->sum('totalredondeado');
  
    // total
    $total_egresos = $total_egresoapertura_auxiliar+$total_compras+$total_egresosdiversos+$total_ventadevoluciones;
    $total_ingresos = $total_apertura+$total_ingresoapertura_auxiliar+$total_ingresosdiversos+$total_ventas+$total_compradevoluciones;
    $total = $total_ingresos-$total_egresos;
  
  
    return [
        'apertura' => $apertura,
        'ingresoaperturaauxiliares' => $ingresoaperturaauxiliares,
        'egresoaperturaauxiliares' => $egresoaperturaauxiliares,
        'ingresosdiversos' => $ingresosdiversos,
        'egresosdiversos' => $egresosdiversos,
        'compras' => $compras,
        'compradevoluciones' => $compradevoluciones,
        'ventas' => $ventas,
        'ventadevoluciones' => $ventadevoluciones,
        'total_apertura' => number_format($total_apertura, 2, '.', ''),
        'total_egresoapertura_auxiliar' => number_format($total_egresoapertura_auxiliar, 2, '.', ''),
        'total_ingresoapertura_auxiliar' => number_format($total_ingresoapertura_auxiliar, 2, '.', ''),
        'total_ventas' => number_format($total_ventas, 2, '.', ''),
        'total_ventadevoluciones' => number_format($total_ventadevoluciones, 2, '.', ''),
        'total_ingresosdiversos' => number_format($total_ingresosdiversos, 2, '.', ''),
        'total_compras' => number_format($total_compras, 2, '.', ''),
        'total_compradevoluciones' => number_format($total_compradevoluciones, 2, '.', ''),
        'total_egresosdiversos' => number_format($total_egresosdiversos, 2, '.', ''),
        'total_ingresos' => number_format($total_ingresos, 2, '.', ''),
        'total_egresos' => number_format($total_egresos, 2, '.', ''),
        'total' => number_format($total, 2, '.', ''),
    ];
}
  
// SISTEMA

function modulo($idtienda,$idusers,$opcion){
    $usersrolesmodulo = DB::table('usersrolesmodulo')
                ->join('modulo','modulo.id','usersrolesmodulo.idmodulo')
                ->where('usersrolesmodulo.idtienda',$idtienda)
                ->where('usersrolesmodulo.idusers',$idusers)
                ->where('modulo.idestado',1)
                ->where('modulo.opcion',$opcion)
                ->first();
  
    $resultado = 'ERROR';
    if($usersrolesmodulo!=''){
        $resultado = 'CORRECTO';
    }
    
    return [
        'resultado' => $resultado,
    ];
}
function caja($idtienda,$idusersrecepcion){
    $apertura = DB::table('s_aperturacierre')
        ->join('s_caja','s_caja.id','s_aperturacierre.s_idcaja')
        ->where('s_caja.idtienda',$idtienda)
        ->where('s_aperturacierre.idusersrecepcion',$idusersrecepcion)
        //->where('s_aperturacierre.s_idestado',2)
        //->where('s_aperturacierre.fechaconfirmacion','<>','')
        ->select('s_aperturacierre.*','s_caja.nombre as cajanombre')
        ->orderBy('s_aperturacierre.id','desc')
        ->limit(1)
        ->first();
  
    $resultado = 'CERRADO';
    if($apertura!=''){
        if($apertura->s_idestadoaperturacierre==1 && $apertura->idusersrecepcion==Auth::user()->id){
            $resultado = 'PROCESO';
        }elseif($apertura->s_idestadoaperturacierre==2 && $apertura->idusersrecepcion==Auth::user()->id && $apertura->fechaconfirmacion==''){
            $resultado = 'PENDIENTE';
        }elseif($apertura->s_idestadoaperturacierre==2 && $apertura->idusersrecepcion==Auth::user()->id && $apertura->fechaconfirmacion!=''){
            $resultado = 'ABIERTO';
        }
    }
  
    return [
        'apertura'  => $apertura,
        'resultado' => $resultado,
    ];
}
function efectivo($idtienda,$idapertura,$idmoneda=1){
  
    // INGRESO 
    //--> apertura
  
    $apertura = DB::table('s_aperturacierre')
        ->whereId($idapertura)
        ->limit(1)
        ->first();
    $total_apertura = 0;
    if($idmoneda==1){
        $total_apertura = $apertura->montoasignar;
    }elseif($idmoneda==2){
        $total_apertura = $apertura->montoasignar_dolares;
    }
  
    //--> apertura caja auxiliar
    $ingresoaperturaauxiliares = DB::table('s_aperturacierre')
        // ->join('users as usersrecepcion','usersrecepcion.id','s_aperturacierre.idusersrecepcion')
        ->where('s_aperturacierre.s_idaperturacierre',$idapertura)
        ->where('s_aperturacierre.s_idestadoaperturacierre',3)
        ->whereNotNull('s_aperturacierre.fechacierreconfirmacion')
        ->select(
            's_aperturacierre.*',
            // 'usersrecepcion.nombre as usersrecepcionnombre',
            // 'usersrecepcion.apellidos as usersrecepcionapellidos',
        )
        ->get();
    $total_ingresoapertura_auxiliar = 0;
    foreach($ingresoaperturaauxiliares as $value){
        if($idmoneda==1){
            $total_ingresoapertura_auxiliar = $total_ingresoapertura_auxiliar+$value->montocierre;
        }elseif($idmoneda==2){
            $total_ingresoapertura_auxiliar = $total_ingresoapertura_auxiliar+$value->montocierre_dolares;
        }
    }
  
    //--> movimiento
    $ingresosdiversos  = DB::table('s_movimiento')
        ->where('s_movimiento.tipomovimiento','INGRESO')
        ->where('s_movimiento.idtienda',$idtienda)
        ->where('s_movimiento.s_idmoneda',$idmoneda)
        ->where('s_movimiento.s_idaperturacierre',$idapertura)
        ->where('s_movimiento.idestadomovimiento',2)
        ->where('s_movimiento.idestado',1)
        ->select(
            's_movimiento.*',
            's_movimiento.tipomovimientonombre as conceptomovimientonombre'
        )
        ->orderBy('s_movimiento.id','desc')
        ->get();
  
    $total_ingresosdiversos = 0;
    foreach($ingresosdiversos as $value){
        $total_ingresosdiversos = $total_ingresosdiversos+$value->monto;
    }
         
    //--> venta
    $ventas = DB::table('s_venta')
        // ->join('users as cliente','cliente.id','s_venta.s_idusuariocliente')
        ->where('s_venta.idtienda',$idtienda)
        ->whereIn('s_venta.s_idestadoventa',[2,3])
        // ->where('s_venta.s_idestadoventa',3)
        ->where('s_venta.s_idmoneda',$idmoneda)
        ->where('s_venta.s_idaperturacierre',$idapertura)
        ->select(
            's_venta.*',
            // DB::raw('IF(cliente.idtipopersona=1,
            // CONCAT(cliente.apellidos,", ",cliente.nombre),
            // CONCAT(cliente.apellidos)) as cliente')
        )
        ->orderBy('s_venta.id','desc')
        ->get();
  
    $total_ventas = 0;
    foreach($ventas as $value){
        $total_ventas = $total_ventas+$value->totalredondeado;
    }
  
    //--> compradevolucion
    $compradevoluciones = DB::table('s_compradevolucion')
        ->join('s_compra','s_compra.id','s_compradevolucion.idcompra')
        ->where('s_compradevolucion.idtienda',$idtienda)
        ->where('s_compradevolucion.s_idestado',2)
        ->where('s_compradevolucion.s_idmoneda',$idmoneda)
        ->where('s_compradevolucion.s_idaperturacierre',$idapertura)
        ->select(
            's_compradevolucion.*',
            's_compra.codigo as compracodigo'
        )
        ->orderBy('s_compradevolucion.id','desc')
        ->get();
  
    $total_compradevoluciones = 0;
    foreach($compradevoluciones as $value){
        $total_compradevoluciones = $total_compradevoluciones+$value->totalredondeado;
    }
  
    // EGRESO
    //--> apertura caja auxiliar
    $egresoaperturaauxiliares = DB::table('s_aperturacierre')
        // ->join('users as usersrecepcion','usersrecepcion.id','s_aperturacierre.idusersrecepcion')
        ->where('s_aperturacierre.s_idaperturacierre',$idapertura)
        ->whereNotNull('s_aperturacierre.fechaconfirmacion')
        ->select(
            's_aperturacierre.*',
            // 'usersrecepcion.nombre as usersrecepcionnombre',
            // 'usersrecepcion.apellidos as usersrecepcionapellidos',
        )
        ->get();
    $total_egresoapertura_auxiliar = 0;
    foreach($egresoaperturaauxiliares as $value){
        if($idmoneda==1){
            $total_egresoapertura_auxiliar = $total_egresoapertura_auxiliar+$value->montoasignar;
        }elseif($idmoneda==2){
            $total_egresoapertura_auxiliar = $total_egresoapertura_auxiliar+$value->montoasignar_dolares;
        }
    }
        
    //--> movimiento
    $egresosdiversos  = DB::table('s_movimiento')
        ->where('s_movimiento.tipomovimiento','EGRESO')
        ->where('s_movimiento.idtienda',$idtienda)
        ->where('s_movimiento.s_idmoneda',$idmoneda)
        ->where('s_movimiento.s_idaperturacierre',$idapertura)
        ->where('s_movimiento.idestadomovimiento',2)
        ->where('s_movimiento.idestado',1)
        ->select(
            's_movimiento.*',
            's_movimiento.tipomovimientonombre as conceptomovimientonombre'
        )
        ->orderBy('s_movimiento.id','desc')
        ->get();
    
    $total_egresosdiversos = 0;
    foreach($egresosdiversos as $value){
        $total_egresosdiversos = $total_egresosdiversos+$value->monto;
    }
        
    //--> compra  
    $compras = DB::table('s_compra')
        // ->join('users as proveedor','proveedor.id','s_compra.s_idusuarioproveedor')
        ->where('s_compra.idtienda',$idtienda)
        ->where('s_compra.s_idestado',2)
        ->where('s_compra.s_idmoneda',$idmoneda)
        ->where('s_compra.s_idaperturacierre',$idapertura)
        ->select(
            's_compra.*',
            // DB::raw('IF(proveedor.idtipopersona=1,
            // CONCAT(proveedor.apellidos,", ",proveedor.nombre),
            // CONCAT(proveedor.apellidos)) as proveedor')
        )
        ->orderBy('s_compra.id','desc')
        ->get();
  
    $total_compras = 0;
    foreach($compras as $value){
        $total_compras = $total_compras+$value->totalredondeado;
    }
  
    //--> ventadevolucion
    $ventadevoluciones = DB::table('s_ventadevolucion')
        ->join('s_venta','s_venta.id','s_ventadevolucion.idventa')
        ->where('s_ventadevolucion.idtienda',$idtienda)
        ->where('s_ventadevolucion.idestado',1)
        ->where('s_ventadevolucion.idestadoventadevolucion',2)
        ->where('s_ventadevolucion.idmoneda',$idmoneda)
        ->where('s_ventadevolucion.idaperturacierre',$idapertura)
        ->select(
            's_ventadevolucion.*',
            's_venta.codigo as ventacodigo'
        )
        ->orderBy('s_ventadevolucion.id','desc')
        ->get();
  
    $total_ventadevoluciones = 0;
    foreach($ventadevoluciones as $value){
        $total_ventadevoluciones = $total_ventadevoluciones+$value->totalredondeado;
    }
  
    // PRESTAMO
    // Ingreso
    //--> cobranza  
    $prestamocobranzas = DB::table('s_prestamo_cobranza')
        // ->join('users as cliente','cliente.id','s_prestamo_cobranza.idcliente')
        ->where('s_prestamo_cobranza.idtienda',$idtienda)
        ->where('s_prestamo_cobranza.idmoneda',$idmoneda)
        ->where('s_prestamo_cobranza.s_idaperturacierre',$idapertura)
        ->where('s_prestamo_cobranza.idestadocobranza',2)
        ->where('s_prestamo_cobranza.idestado',1)
        ->select(
            's_prestamo_cobranza.*',
            // DB::raw('IF(cliente.idtipopersona=1,
            // CONCAT(cliente.apellidos,", ",cliente.nombre),
            // CONCAT(cliente.apellidos)) as cliente')
        )
        ->orderBy('s_prestamo_cobranza.fecharegistro','desc')
        ->get();
  
    $total_prestamocobranzas = 0;
    foreach($prestamocobranzas as $value){
        /*if($value->cronograma_idtipopago==1){
            $total_prestamocobranzas = $total_prestamocobranzas+$value->cronograma_totalredondeado;
        }
        elseif($value->cronograma_idtipopago==2){*/
            $total_prestamocobranzas = $total_prestamocobranzas+$value->cronograma_pagado;
        //}
    }
    //--> desembolsos anulados
    $prestamodesembolsos_anulado = DB::table('s_prestamo_credito')
        // ->join('users as cliente','cliente.id','s_prestamo_credito.idcliente')
        ->where('s_prestamo_credito.idtienda', $idtienda)
        ->where('s_prestamo_credito.idmoneda',$idmoneda)
        ->where('s_prestamo_credito.facturacion_idaperturacierre',$idapertura)
        ->where('s_prestamo_credito.idestadodesembolso',3)
        ->where('s_prestamo_credito.idestadocredito',4)
        ->where('s_prestamo_credito.idestado',1)
        ->select(
            's_prestamo_credito.*',
            // DB::raw('IF(cliente.idtipopersona=1,
            // CONCAT(cliente.apellidos,", ",cliente.nombre),
            // CONCAT(cliente.apellidos)) as cliente')
        )
        ->orderBy('s_prestamo_credito.fechadesembolsado','desc')
        ->get();
    $total_prestamo_desembolsos_anulado = 0;
    $total_prestamo_gastosadministrativos_anulado = 0;
    foreach($prestamodesembolsos_anulado as $value){
        $total_prestamo_desembolsos_anulado = $total_prestamo_desembolsos_anulado+$value->monto;
        $total_prestamo_gastosadministrativos_anulado = $total_prestamo_gastosadministrativos_anulado+number_format($value->facturacion_montorecibido-$value->facturacion_vuelto, 2, '.', '');
    }
  
    //--> recaudaciones
    // $ahorrorecaudaciones = DB::table('s_prestamo_ahorrorecaudacionlibre')
    //     ->join('users as cliente', 'cliente.id', 's_prestamo_ahorrorecaudacionlibre.idcliente')
    //     ->where('s_prestamo_ahorrorecaudacionlibre.idtienda', $idtienda)
    //     ->where('s_prestamo_ahorrorecaudacionlibre.idmoneda',$idmoneda)
    //     ->where('s_prestamo_ahorrorecaudacionlibre.s_idaperturacierre',$idapertura)
    //     ->where('s_prestamo_ahorrorecaudacionlibre.idestadorecaudacion',2)
    //     ->where('s_prestamo_ahorrorecaudacionlibre.idestado',1)
    //     ->select(
    //         's_prestamo_ahorrorecaudacionlibre.*',
    //         DB::raw('IF(cliente.idtipopersona=1,
    //         CONCAT(cliente.apellidos,", ",cliente.nombre),
    //         CONCAT(cliente.apellidos)) as cliente')
    //     )
    //     ->orderBy('s_prestamo_ahorrorecaudacionlibre.fecharegistro','desc')
    //     ->get();
    $ahorrorecaudaciones = [];
    $total_ahorrorecaudaciones = 0;
    foreach($ahorrorecaudaciones as $value){
        $total_ahorrorecaudaciones = $total_ahorrorecaudaciones+$value->monto_efectivo;
    }
  
    // Egreso
    //--> desembolsar credito  
    $prestamodesembolsos = DB::table('s_prestamo_credito')
        // ->join('users as cliente','cliente.id','s_prestamo_credito.idcliente')
        ->where('s_prestamo_credito.idtienda', $idtienda)
        ->where('s_prestamo_credito.idmoneda',$idmoneda)
        ->where('s_prestamo_credito.facturacion_idaperturacierre',$idapertura)
        ->whereIn('s_prestamo_credito.idestadodesembolso',[1,3])
        ->where('s_prestamo_credito.idestadocredito',4)
        ->where('s_prestamo_credito.idestado',1)
        ->select(
            's_prestamo_credito.*',
            // DB::raw('IF(cliente.idtipopersona=1,
            // CONCAT(cliente.apellidos,", ",cliente.nombre),
            // CONCAT(cliente.apellidos)) as cliente')
        )
        ->orderBy('s_prestamo_credito.fechadesembolsado','desc')
        ->get();
    $total_prestamo_desembolsos = 0;
    $total_prestamo_gastosadministrativos = 0;
    foreach($prestamodesembolsos as $value){
        $total_prestamo_desembolsos = $total_prestamo_desembolsos+$value->monto;
        $total_prestamo_gastosadministrativos = $total_prestamo_gastosadministrativos+number_format($value->facturacion_montorecibido-$value->facturacion_vuelto, 2, '.', '');
    }
  
    // --> Retiros
    // $ahorroretiros = DB::table('s_prestamo_ahorroretirolibre')
    //     ->join('users as cliente', 'cliente.id', 's_prestamo_ahorroretirolibre.idcliente')
    //     ->where('s_prestamo_ahorroretirolibre.idtienda', $idtienda)
    //     ->where('s_prestamo_ahorroretirolibre.idmoneda',$idmoneda)
    //     ->where('s_prestamo_ahorroretirolibre.s_idaperturacierre',$idapertura)
    //     ->where('s_prestamo_ahorroretirolibre.idestadoahorroretirolibre',2)
    //     ->where('s_prestamo_ahorroretirolibre.idestado',1)
    //     ->select(
    //         's_prestamo_ahorroretirolibre.*',
    //         DB::raw('IF(cliente.idtipopersona=1,
    //         CONCAT(cliente.apellidos,", ",cliente.nombre),
    //         CONCAT(cliente.apellidos)) as cliente')
    //     )
    //     ->orderBy('s_prestamo_ahorroretirolibre.fecharegistro','desc')
    //     ->get();
    $ahorroretiros = [];
    $total_ahorroretiros = 0;
    foreach($ahorroretiros as $value){
        $total_ahorroretiros = $total_ahorroretiros+$value->monto_efectivo;
    }
  
    $prestamo_total_ingresos = $total_prestamocobranzas+$total_prestamo_gastosadministrativos+$total_prestamo_desembolsos_anulado+$total_ahorrorecaudaciones;
    $prestamo_total_egresos = $total_prestamo_gastosadministrativos_anulado+$total_prestamo_desembolsos+$total_ahorroretiros;

    // total
    $total_egresos = $total_egresoapertura_auxiliar+$total_compras+$total_egresosdiversos+$total_ventadevoluciones+$prestamo_total_egresos;
    $total_ingresos = $total_apertura+$total_ingresoapertura_auxiliar+$total_ingresosdiversos+$total_ventas+$total_compradevoluciones+$prestamo_total_ingresos;
    $total = $total_ingresos-$total_egresos;
  
  
    // DEPOSITO
  
    //--> cobranzas
    // $prestamocobranzacuentabancarias = DB::table('s_formapagodetalle')
    //     ->join('s_prestamo_cobranza','s_prestamo_cobranza.id','s_formapagodetalle.s_idprestamo_cobranza')
    //     ->join('users as cliente','cliente.id','s_prestamo_cobranza.idcliente')
    //     ->where('s_formapagodetalle.idtienda',$idtienda)
    //     ->where('s_formapagodetalle.idmoneda',$idmoneda)
    //     ->where('s_prestamo_cobranza.s_idaperturacierre',$idapertura)
    //     ->where('s_formapagodetalle.idestado',1)
    //     ->select(
    //         's_formapagodetalle.*',
    //         's_prestamo_cobranza.codigo as codigo',
    //         DB::raw('IF(cliente.idtipopersona=1,
    //         CONCAT(cliente.apellidos,", ",cliente.nombre),
    //         CONCAT(cliente.apellidos)) as cliente')
    //     )
    //     ->orderBy('s_formapagodetalle.fecharegistro','desc')
    //     ->get();
    $prestamocobranzacuentabancarias = [];
    $total_prestamocobranzacuentabancarias = 0;
    foreach($prestamocobranzacuentabancarias as $value){
        $total_prestamocobranzacuentabancarias = $total_prestamocobranzacuentabancarias+$value->monto;
    }
  
    //--> recaudaciones
    // $ahorrorecaudacioncuentabancarias = DB::table('s_prestamo_ahorrorecaudacionlibre')
    //     ->join('users as cliente', 'cliente.id', 's_prestamo_ahorrorecaudacionlibre.idcliente')
    //     ->where('s_prestamo_ahorrorecaudacionlibre.idtienda', $idtienda)
    //     ->where('s_prestamo_ahorrorecaudacionlibre.idmoneda',$idmoneda)
    //     ->where('s_prestamo_ahorrorecaudacionlibre.idaperturacierre',$idapertura)
    //     ->where('s_prestamo_ahorrorecaudacionlibre.idestadorecaudacion',2)
    //     ->where('s_prestamo_ahorrorecaudacionlibre.idestado',1)
    //     ->select(
    //         's_prestamo_ahorrorecaudacionlibre.*',
    //         DB::raw('IF(cliente.idtipopersona=1,
    //         CONCAT(cliente.apellidos,", ",cliente.nombre),
    //         CONCAT(cliente.apellidos)) as cliente')
    //     )
    //     ->orderBy('s_prestamo_ahorrorecaudacionlibre.fecharegistro','desc')
    //     ->get();
    $ahorrorecaudacioncuentabancarias = [];
    $total_ahorrorecaudacioncuentabancarias = 0;
    foreach($ahorrorecaudacioncuentabancarias as $value){
        $total_ahorrorecaudacioncuentabancarias = $total_ahorrorecaudacioncuentabancarias+$value->monto_deposito;
    }
  
    $totaldeposito = $total_prestamocobranzacuentabancarias+$total_ahorrorecaudacioncuentabancarias;
  
    return [
        'apertura' => $apertura,
        'ingresoaperturaauxiliares' => $ingresoaperturaauxiliares,
        'egresoaperturaauxiliares' => $egresoaperturaauxiliares,
        'ingresosdiversos' => $ingresosdiversos,
        'egresosdiversos' => $egresosdiversos,
        'compras' => $compras,
        'compradevoluciones' => $compradevoluciones,
        'ventas' => $ventas,
        'ventadevoluciones' => $ventadevoluciones,
        'prestamo_desembolsos' => $prestamodesembolsos,
        'prestamo_desembolsos_anulado' => $prestamodesembolsos_anulado,
        'prestamo_cobranzas' => $prestamocobranzas,
        'prestamo_cobranzacuentabancarias' => $prestamocobranzacuentabancarias,
        'ahorro_recaudaciones' => $ahorrorecaudaciones,
        'ahorro_recaudacioncuentabancarias' => $ahorrorecaudacioncuentabancarias,
        'ahorro_retiros' => $ahorroretiros,
        'total_apertura' => number_format($total_apertura, 2, '.', ''),
        'total_egresoapertura_auxiliar' => number_format($total_egresoapertura_auxiliar, 2, '.', ''),
        'total_ingresoapertura_auxiliar' => number_format($total_ingresoapertura_auxiliar, 2, '.', ''),
        'total_ventas' => number_format($total_ventas, 2, '.', ''),
        'total_ventadevoluciones' => number_format($total_ventadevoluciones, 2, '.', ''),
        'total_ingresosdiversos' => number_format($total_ingresosdiversos, 2, '.', ''),
        'total_compras' => number_format($total_compras, 2, '.', ''),
        'total_compradevoluciones' => number_format($total_compradevoluciones, 2, '.', ''),
        'total_egresosdiversos' => number_format($total_egresosdiversos, 2, '.', ''),
        'total_prestamo_cobranzas' => number_format($total_prestamocobranzas, 2, '.', ''),
        'total_prestamo_desembolsos' => number_format($total_prestamo_desembolsos, 2, '.', ''),
        'total_prestamo_gastosadministrativos' => number_format($total_prestamo_gastosadministrativos, 2, '.', ''),
        'total_prestamo_desembolsos_anulado' => number_format($total_prestamo_desembolsos_anulado, 2, '.', ''),
        'total_prestamo_gastosadministrativos_anulado' => number_format($total_prestamo_gastosadministrativos_anulado, 2, '.', ''),
        'total_ahorro_recaudaciones' => number_format($total_ahorrorecaudaciones, 2, '.', ''),
        'total_ahorro_retiros' => number_format($total_ahorroretiros, 2, '.', ''),
        'total_ingresos' => number_format($total_ingresos, 2, '.', ''),
        'total_egresos' => number_format($total_egresos, 2, '.', ''),
        'total' => number_format($total, 2, '.', ''),
        'total_prestamo_cobranzacuentabancarias' => number_format($total_prestamocobranzacuentabancarias, 2, '.', ''),
        'total_ahorro_recaudacioncuentabancarias' => number_format($total_ahorrorecaudacioncuentabancarias, 2, '.', ''),
        'totaldeposito' => number_format($totaldeposito, 2, '.', ''),
    ];
}
function efectivocaja($idtienda,$idcaja,$idmoneda){
  
    $sum_apertura = '';
    $sum_cierre = '';
    if($idmoneda==1){
      $sum_apertura = 's_aperturacierre.montoasignar';
      $sum_cierre = 's_aperturacierre.montocierre';
    }elseif($idmoneda==2){
      $sum_apertura = 's_aperturacierre.montoasignar_dolares';
      $sum_cierre = 's_aperturacierre.montocierre_dolares';
    }
    // INGRESO 
    $ingreso_aperturacierres = DB::table('s_aperturacierre')
            //->join('s_caja','s_caja.id','s_aperturacierre.s_idcaja')
            ->where('s_aperturacierre.s_idcaja',$idcaja)
            ->where('s_aperturacierre.idtienda',$idtienda)
            ->where('s_aperturacierre.s_idestadoaperturacierre',3)
            ->where('s_aperturacierre.idaperturacierre',0)
            ->sum($sum_cierre);
  
    $ingreso_transferenciasaldos = DB::table('s_transferenciasaldo')
            ->where('s_transferenciasaldo.idcajadestino',$idcaja)
            ->where('s_transferenciasaldo.idtienda', $idtienda)
            ->where('s_transferenciasaldo.idmoneda', $idmoneda)
            ->sum('s_transferenciasaldo.monto');
  
    $ingreso_movimientosaldos = DB::table('s_movimientosaldo')
            ->where('s_movimientosaldo.idcaja',$idcaja)
            ->where('s_movimientosaldo.idtienda', $idtienda)
            ->where('s_movimientosaldo.idmoneda', $idmoneda)
            ->where('s_movimientosaldo.idtipomovimiento', 1)
            ->sum('s_movimientosaldo.monto');
  
    // EGRESO
    $egreso_aperturacierres = DB::table('s_aperturacierre')
            //->join('s_caja','s_caja.id','s_aperturacierre.s_idcaja')
            //->where('s_aperturacierre.fechaconfirmacion','<>','')
            //->where('s_aperturacierre.s_idestadoaperturacierre',2)
            //->whereNull('s_aperturacierre.fechacierreconfirmacion')
            ->where('s_aperturacierre.idaperturacierre',0)
            ->where('s_aperturacierre.s_idcaja',$idcaja)
            ->where('s_aperturacierre.idtienda',$idtienda)
            ->sum($sum_apertura);
  
    //dump($egreso_aperturacierres);
  
    $egreso_transferenciasaldos = DB::table('s_transferenciasaldo')
            ->where('s_transferenciasaldo.idcajaorigen',$idcaja)
            ->where('s_transferenciasaldo.idtienda', $idtienda)
            ->where('s_transferenciasaldo.idmoneda', $idmoneda)
            ->sum('s_transferenciasaldo.monto');
  
    $egreso_movimientosaldos = DB::table('s_movimientosaldo')
            ->where('s_movimientosaldo.idcaja',$idcaja)
            ->where('s_movimientosaldo.idtienda', $idtienda)
            ->where('s_movimientosaldo.idmoneda', $idmoneda)
            ->where('s_movimientosaldo.idtipomovimiento', 2)
            ->sum('s_movimientosaldo.monto');
  
    $ingresos = $ingreso_aperturacierres+$ingreso_transferenciasaldos+$ingreso_movimientosaldos;
    $egresos = $egreso_aperturacierres+$egreso_transferenciasaldos+$egreso_movimientosaldos;
    $total = $ingresos-$egresos;
    return [
        'total' => number_format($total, 2, '.', '')
    ];
}
function descuento_producto($idtienda,$idproducto=0){
  
        if($idproducto==0){
            $producto = DB::table('s_producto')
                ->where('idtienda',$idtienda)
                ->get();
        }else{
            $producto = DB::table('s_producto')
                ->where('idtienda',$idtienda)
                ->where('id',$idproducto)
                ->get();
        }
        $data = [];
        foreach($producto as $producvalue){
  
            $productodescuentos = DB::table('s_productodescuento')
                ->join('s_productodescuentodetalle','s_productodescuentodetalle.s_idproductodescuento','s_productodescuento.id')
                ->where('s_productodescuentodetalle.s_idproductoasociado',$producvalue->id)
                ->select(
                    's_productodescuento.*'
                )
                ->orderBy('s_productodescuento.id','asc')
                ->distinct()
                ->get();
          
            $descuento = [];
            foreach($productodescuentos as $value){
                $productodescuentodetalles = DB::table('s_productodescuentodetalle')
                    ->join('s_producto as producto','producto.id','s_productodescuentodetalle.s_idproductoasociado')
                    ->where('s_productodescuentodetalle.s_idproductodescuento',$value->id)
                    ->select(
                        's_productodescuentodetalle.id as id',
                        'producto.id as idproducto',
                        'producto.codigo as productocodigo',
                        'producto.nombre as productonombre',
                        'producto.precioalpublico as productoprecioalpublico'
                    )
                    ->orderBy('s_productodescuentodetalle.id','asc')
                    ->get();
                $descuentodetalle = [];
                $descuentodetalle[] = [
                    'idproducto' => $producvalue->id,
                    'productocodigo' => $producvalue->codigo,
                    'productonombre' => $producvalue->nombre,
                    'precioalpublico' => $producvalue->precioalpublico,
                    'estado' => 'disabled'
                ];
                $i = 0;
                $num = 1;
                foreach($productodescuentodetalles as $valuedetalle){
                    if($valuedetalle->idproducto == $producvalue->id && $i==0){
                        $i++;
                    }else{
                        $descuentodetalle[] = [
                            'idproducto' => $valuedetalle->idproducto,
                            'productocodigo' => $valuedetalle->productocodigo,
                            'productonombre' => $valuedetalle->productonombre,
                            'precioalpublico' => $valuedetalle->productoprecioalpublico,
                            'estado' => ''
                        ];
                    }
                    $num++;
                }
                $descuento[] = [
                    'codigo' => $value->codigo,
                    'total' => $value->total,
                    'montodescuento' => $value->montodescuento,
                    'totalpack' => $value->totalpack,
                    'detalle' => $descuentodetalle
                ];
            }
            $data[] = [
                'producto' => $producto,
                'lista_descuento' => $descuento
            ];
        }
            
    return [
        'data' => $data
    ];
}
function producto_presentaciones_mostrar($idtienda,$idproducto,$productos=[],$nivel=1,$unidad=1){
    $datosProducto1 = DB::table('s_producto')
        ->join('tienda','tienda.id','s_producto.idtienda')
        ->leftJoin('s_categoria','s_categoria.id','s_producto.s_idcategoria1')
        ->leftJoin('s_categoria as subcategoria','subcategoria.id','s_producto.s_idcategoria2')
        ->leftJoin('s_marca','s_marca.id','s_producto.s_idmarca')
        ->leftJoin('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
        ->where('s_producto.idtienda',$idtienda)
        ->where('s_producto.id',$idproducto)
        ->select(
          's_producto.*',
          'unidadmedida.nombre as unidadmedidanombre',
          DB::raw('CONCAT(unidadmedida.nombre," x ",s_producto.por) as unidadmedida'),
          DB::raw('CONCAT(s_producto.nombre," / ",s_producto.precioalpublico) as text'),
          'tienda.id as idtienda',
          'tienda.nombre as tiendanombre',
          's_marca.nombre as marcanombre',
          's_categoria.nombre as categorianombre',
          DB::raw('(SELECT imagen FROM s_productogaleria WHERE s_idproducto=s_producto.id ORDER BY orden ASC LIMIT 1) as imagen')
        )
        ->first();
    if($datosProducto1!=''){
        //$cantidad = $cantidad/$datosProducto1->por;
        $productosaldo = productosaldo($idtienda,$datosProducto1->id);
        $productos[] = [
            'nivel' => $nivel,
            'producto' => $datosProducto1,
            'idproducto' => $datosProducto1->id,
            'text' => $datosProducto1->text,
            'por' => $datosProducto1->por,
            'productopor' => $datosProducto1->por*$unidad,
            'productocodigo' => $datosProducto1->codigo,
            'productonombre' => $datosProducto1->nombre,
            'productounidadmedida' => $datosProducto1->idunidadmedida,
            'unidadmedidanombre' => $datosProducto1->unidadmedidanombre,
            'precioalpublico' => $datosProducto1->precioalpublico,
            //'cantidad' => round($cantidad, 3)
            'cantidad' => $productosaldo['stock'],
            //'idreferencia' => $productosaldo['idreferencia'],
          
        ];
        $unidad = $datosProducto1->por;
        return producto_presentaciones_mostrar($idtienda,$datosProducto1->s_idproducto,$productos,$nivel+1,$unidad);
    }else{
        return $productos;
    }
}
function producto_presentaciones($idtienda,$idproducto){
    $datosProducto1 = DB::table('s_producto')
        ->where('s_producto.idtienda',$idtienda)
        ->where('s_producto.s_idproducto',$idproducto)
        ->first();
    if($datosProducto1!=''){
        return producto_presentaciones($idtienda,$datosProducto1->id);
    }else{
        return producto_presentaciones_mostrar($idtienda,$idproducto);
    }
}
function productosaldo($idtienda,$idproducto){
    $s_productosaldo = DB::table('s_productosaldos')
        ->where('s_productosaldos.idtienda',$idtienda)
        ->where('s_productosaldos.idproducto',$idproducto)
        ->orderBy('s_productosaldos.id','desc')
        ->limit(1)
        ->first();
    $stockactual = 0;
    //$stockactualpor = 0;
    //$idreferencia = 0;
    //$i=0;
    if($s_productosaldo!=''){
        //if($i==0){
        //    $cantidad = $s_productosaldo->saldo_cantidad;
        //}
        $stockactual = $s_productosaldo->saldo_cantidad;
        //$stockactualpor = number_format($cantidad/$s_productosaldo->productopor, 3, '.', '');
        //$idreferencia = $s_productosaldo->idreferencia;
        //$cantidad = number_format($cantidad/$s_productosaldo->productopor, 3, '.', '');
        //$i++;
    }
    return [
        'stock' => $stockactual,
        //'stockpor' => $stockactualpor,
        //'idreferencia' => $idreferencia,
    ];
}
function productosaldo_calcular($stock_agrupado,$productopor){
    $stock_agrupado = $stock_agrupado*100;
    $stock_agrupadopor2 = $productopor*100;
    $stock_agrupadoporvalid2 = $stock_agrupadopor2;
    $stock_agrupadoactual2 = 0;
    $stock_agrupadoactualrestante2 = 0;
    for($i=1;$i<=$stock_agrupado;$i++){
      if($i==$stock_agrupadoporvalid2){
          $stock_agrupadoactual2++;
          $stock_agrupadoporvalid2=$stock_agrupadoporvalid2+$stock_agrupadopor2;
          $stock_agrupadoactualrestante2 = 0;
      }else{
          $stock_agrupadoactualrestante2++;
      }
    }
    $stock_agrupadoactualrestante2=$stock_agrupadoactualrestante2/100;
    return [
        'stock' => $stock_agrupadoactual2,
        'stock_restante' => $stock_agrupadoactualrestante2
    ];
}
//function productosaldo_actualizar($idtienda,$idproducto,$concepto,$idunidadmedida=1,$por=1,$cantidad=0,$preciounitario=0,$preciototal=0,$idreferencia=0){
function productosaldo_actualizar($idtienda,$idproducto,$concepto,$cantidad=0,$por=1,$idunidadmedida=1,$idreferencia=0){  
            
        $producto = DB::table('s_producto')
            ->whereId($idproducto)
            ->first();
        if($concepto=='COMPRA' or 
           $concepto=='VENTA' or 
           $concepto=='MOVIMIENTO INGRESO' or 
           $concepto=='MOVIMIENTO SALIDA' or 
           $concepto=='DEVOLUCION VENTA' or 
           $concepto=='DEVOLUCION COMPRA'){
            $presentaciones = producto_presentaciones($idtienda,$idproducto);
            //$presentaciones = collect($presentaciones)->sortByDesc('nivel');
                $porunidadmayor = 1;
                foreach($presentaciones as $value){
                    if($value['idproducto']==$idproducto){
                        $porunidadmayor = $value['productopor']; 
                    }   
                }
          
                $saldo_salida = $porunidadmayor*$cantidad;
                foreach($presentaciones as $value){
                        $s_productosaldo = DB::table('s_productosaldos')
                            ->where('s_productosaldos.idtienda',$idtienda)
                            ->where('s_productosaldos.idproducto',$value['idproducto'])
                            ->orderBy('s_productosaldos.id','desc')
                            ->limit(1)
                            ->first();
                        if($s_productosaldo==''){
                            DB::table('s_productosaldos')->insert([
                                'fecharegistro' => Carbon\Carbon::now(),
                                'concepto' => 'SALDO INICIAL',
                                'cantidad' => 0,
                                'saldo_cantidad' => 0,
                                'idunidadmedida' => 0,
                                'idproducto' => $value['idproducto'],
                                'idproductoreferencia' => $idproducto,
                                'idtienda' => $idtienda,
                                'idestado' => 1,
                            ]);
                        }
                        $porductopor = ($value['por']==0?1:$value['por']);
                        $saldo_salida = $saldo_salida/$porductopor;
                    
                        $tabla = '';
                        $idtabla = 0;
                        if($concepto=='COMPRA'){
                            $tabla = 's_compradetalle';
                            $idtabla = 'idcompradetalle';
                            $saldo_cantidad = ($s_productosaldo!=''?$s_productosaldo->saldo_cantidad:0)+$saldo_salida;
                        }
                        elseif($concepto=='VENTA'){
                            $tabla = 's_ventadetalle';
                            $idtabla = 'idventadetalle';
                            $saldo_cantidad = ($s_productosaldo!=''?$s_productosaldo->saldo_cantidad:0)-$saldo_salida;
                        }
                        elseif($concepto=='DEVOLUCION COMPRA'){
                            $tabla = 's_compradevoluciondetalle';
                            $idtabla = 'idcompradevoluciondetalle';
                            $saldo_cantidad = ($s_productosaldo!=''?$s_productosaldo->saldo_cantidad:0)-$saldo_salida;
                        }
                        elseif($concepto=='DEVOLUCION VENTA'){
                            $tabla = 's_ventadevoluciondetalle';
                            $idtabla = 'idventadevoluciondetalle';
                            $saldo_cantidad = ($s_productosaldo!=''?$s_productosaldo->saldo_cantidad:0)+$saldo_salida;
                        }
                        elseif($concepto=='MOVIMIENTO INGRESO'){
                            $tabla = 's_productomovimiento';
                            $idtabla = 'idmovimiento';
                            $saldo_cantidad = ($s_productosaldo!=''?$s_productosaldo->saldo_cantidad:0)+$saldo_salida;
                        } 
                        elseif($concepto=='MOVIMIENTO SALIDA'){
                            $tabla = 's_productomovimiento';
                            $idtabla = 'idmovimiento';
                            $saldo_cantidad = ($s_productosaldo!=''?$s_productosaldo->saldo_cantidad:0)-$saldo_salida;
                        } 

                        $idproductosaldos = DB::table('s_productosaldos')->insertGetId([
                            'fecharegistro' => Carbon\Carbon::now(),
                            'concepto' => $concepto,
                            'cantidad' => $cantidad,
                            'saldo_cantidad' => $saldo_cantidad,
                            'idunidadmedida' => $idunidadmedida,
                            'idproducto' => $value['idproducto'],
                            'idproductoreferencia' => $idproducto,
                            $idtabla => $idreferencia,
                            'idtienda' => $idtienda,
                            'idestado' => 1,
                        ]);
                  
                        DB::table('s_producto')->whereId($value['idproducto'])->update([
                            's_idproductosaldos' => $idproductosaldos,
                        ]); 
                }
            
        } 
        elseif($concepto=='SALDO AGRUPADO'){

            $stockproducto = productosaldo($idtienda,$idreferencia)['stock'];
          
            $cantidad_producto = 0;
            if($stockproducto>0){
                $cantidad_producto = number_format(($stockproducto/$por), 3, '.', '');
            }
          
            $idproductosaldos = DB::table('s_productosaldos')->insertGetId([
                'fecharegistro' => Carbon\Carbon::now(),
                'concepto' => 'SALDO AGRUPADO',
                'cantidad' => 0,
                'saldo_cantidad' => $cantidad_producto,
                'idunidadmedida' => 0,
                'idproducto' => $idproducto,
                'idproductoreferencia' => $idreferencia,
                'idtienda' => $idtienda,
                'idestado' => 1,
            ]);
                  
            DB::table('s_producto')->whereId($idproducto)->update([
                's_idproductosaldos' => $idproductosaldos,
            ]); 
        }
        elseif($concepto=='SALDO DESAGRUPADO'){
          
            $idproductosaldos = DB::table('s_productosaldos')->insertGetId([
                'fecharegistro' => Carbon\Carbon::now(),
                'concepto' => 'SALDO DESAGRUPADO',
                'cantidad' => 0,
                'saldo_cantidad' => 0,
                'idunidadmedida' => 0,
                'idproducto' => $idproducto,
                'idproductoreferencia' => $idreferencia,
                'idtienda' => $idtienda,
                'idestado' => 1,
            ]);
                  
            DB::table('s_producto')->whereId($idproducto)->update([
                's_idproductosaldos' => $idproductosaldos,
            ]); 

        }
}
function alerta_fechavencimiento($idtienda){
      $productos = DB::table('s_producto')
              ->leftJoin('s_categoria','s_categoria.id','s_producto.s_idcategoria1')
              ->leftJoin('s_marca','s_marca.id','s_producto.s_idmarca')
              ->leftJoin('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
              ->where('s_producto.idtienda',$idtienda)
              ->where('s_producto.s_idestado',1)
              ->whereNotNull('s_producto.fechavencimiento')
              //->where('s_producto.alertavencimiento','>',0)
              ->select(
                's_producto.id as productoid',
                's_producto.codigo as productocodigo',
                's_producto.nombre as productonombre',
                 's_categoria.nombre as categorianombre',
                 's_marca.nombre as marcanombre',
                 DB::raw('CONCAT(unidadmedida.nombre," x ",s_producto.por) as unidadmedida'),
                's_producto.precioalpublico as precioalpublico',
                's_producto.fechavencimiento as fechavencimiento',
                's_producto.alertavencimiento as alertavencimiento',
                 DB::raw('TIMESTAMPDIFF(DAY, CURDATE(), s_producto.fechavencimiento) as diasfaltantevencimiento')
              )
              ->orderBy('s_producto.id','desc')
              ->get();
  
    $data = [];
    $cantidad = 0;
    foreach($productos as $value){
      
        /*$firstDate  = new DateTime(Carbon\Carbon::now()->format("Y-m-d"));
        $secondDate = new DateTime($value->fechavencimiento);
        $intvl = $firstDate->diff($secondDate);
        $ano = $intvl->y>0 ? $intvl->y.($intvl->y==1 ? " año".($intvl->m==0 ?" y ":", "):" años".($intvl->m==0 ?" y ":", ")):'';
        $mes = $intvl->m>0 ? $intvl->m.($intvl->m==1 ? " mes y ":" meses y "):'';
        $dia = $intvl->d>0 ? $intvl->d.($intvl->d==1 ? " día ":" días "):'';
      
        $data[] = [
            'productoid' => $value->productoid,
            'productocodigo' => $value->productocodigo,
            'productonombre' => $value->productonombre,
            'categorianombre' => $value->categorianombre,
            'marcanombre' => $value->marcanombre,
            'unidadmedida' => $value->unidadmedida,
            'precioalpublico' => $value->precioalpublico,
            'fechavencimiento' => date_format(date_create($value->fechavencimiento),"d/m/Y"),
            'tiempofaltante' => $ano.$mes.$dia,
            'alertavencimiento' => $value->alertavencimiento,
            'style' => $value->diasfaltantevencimiento<=$value->alertavencimiento?'class="select_vencimiento"':'',
        ];*/
      
            $fechaactual  = new DateTime(Carbon\Carbon::now()->format("Y-m-d"));
            // actualizar fecha vencimeinto, rentando con alerta de vencimiento
            $alertavencimiento = date("Y-m-d",strtotime(date($value->fechavencimiento)."- ".$value->alertavencimiento." days")); 
            $alertavencimiento = new DateTime($alertavencimiento);
     
            if($fechaactual>$alertavencimiento){
                $cantidad++;
            }
      
    }
  
    return [
        'data' => $data,
        'cantidad' => $cantidad,
    ];
}
function producto($idtienda,$idproducto){
    $producto = DB::table('s_producto')
        ->join('tienda','tienda.id','s_producto.idtienda')
        ->where('s_producto.idtienda',$idtienda)
        ->where('s_producto.id',$idproducto)
        ->where('s_producto.s_idestado',1)
        ->select(
                's_producto.id as id',
                's_producto.codigo as codigo',
                's_producto.nombre as nombre',
                's_producto.precioalpublico as precioalpublico',
                's_producto.s_idestadodetalle as idestadodetalle',
                's_producto.s_idestado as idestado',
                's_producto.s_idestadotiendavirtual as idestadotv',
                'tienda.id as idtienda',
                'tienda.nombre as tiendanombre',
                'tienda.link as tiendalink',
                 DB::raw('(SELECT imagen FROM s_productogaleria WHERE s_idproducto=s_producto.id ORDER BY orden ASC LIMIT 1) as imagen')
        )
        ->first();
  
    //stock
    $stock_producto = productosaldo($idtienda,$idproducto);

    return [
        'producto' => $producto,
        'stock' => $stock_producto['stock']
    ];
}
function productoimagenes($idtienda,$idproducto){
    $productos = DB::table('s_productogaleria')
        ->join('s_producto','s_producto.id','s_productogaleria.s_idproducto')
        ->where('s_producto.idtienda',$idtienda)
        ->where('s_producto.id',$idproducto)
        ->select(
          's_productogaleria.*'
        )
        ->get();
    return $productos;
}

function formadepago($idtienda,$request,$campo,$idcampo,$idmoneda=1){
    $total_deposito = 0;
    foreach(json_decode($request->formapago_contado_seleccionar) as $value){
        $imagen = uploadfile('','',$request->file('formapago_voucher'.$value->num),'/public/backoffice/tienda/'.$idtienda.'/recaudacion/');
        DB::table('s_formapagodetalle')->insert([
            'fecharegistro' => Carbon\Carbon::now(),
            'numerocuenta' => $request->input('formapago_numerocuenta'.$value->num)!=''?$request->input('formapago_numerocuenta'.$value->num):'',
            'numerooperacion' => $request->input('formapago_numerooperacion'.$value->num)!=''?$request->input('formapago_numerooperacion'.$value->num):'',
            'banco' => $request->input('formapago_banco'.$value->num)!=''?$request->input('formapago_banco'.$value->num):'',
            'fecha' => $request->input('formapago_fecha'.$value->num)!=''?$request->input('formapago_fecha'.$value->num):'',
            'hora' => $request->input('formapago_hora'.$value->num)!=''?$request->input('formapago_hora'.$value->num):'',
            'monto' => $request->input('formapago_montodeposito'.$value->num),
            'voucher' => $imagen,
            's_idcuentabancaria' => $request->input('formapago_idcuentabancaria'.$value->num)!=''?$request->input('formapago_idcuentabancaria'.$value->num):0,
            $campo => $idcampo,
            'idmoneda' => $idmoneda,
            'idtienda' => $idtienda,
            'idestado' => 1,
        ]);
        $total_deposito = $total_deposito+$request->input('formapago_montodeposito'.$value->num);
    }
    return [
        'total_deposito' => $total_deposito
    ];
}

#FUNCIONES
/*function validar_usuario_duplicado($idtienda,$identificacion){
    $usuario = DB::table('users')
        ->where('identificacion',$identificacion)
        ->where('idtienda',$idtienda)
        ->where('idestado','<>',3)
        ->first();
    return $productos;
}*/
function sistema_order_array($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            //$new_array[$k] = $array[$k];
            $new_array[] = $array[$k];
        }
    }
  
    return $new_array;
  
    // pasar a array 
    /*$array_new = '';
    $temp_array = []; 
    foreach($new_array as $value){
        if (!in_array($value['idtienda'], $temp_array)){
            $array_new = $array_new.'/&&&/'.$value['idtienda'].'/---/'.$value['tiendanombre'].'/---/';
            $temp_array[] = $value['idtienda'];
        }
        $array_new = $array_new.'/###/'.$value['s_idproducto'].'/,,,/'.$value['productonombre'].'/,,,/'.$value['productocantidad'].'/,,,/'.$value['productopreciounitario'];
    }
    $array_carrito = [];
    $productosarray = explode('/&&&/',$array_new);
    for($i = 1;$i <  count($productosarray);$i++){
        $productosarray1 = explode('/---/', $productosarray[$i]);
        $productosarraydetalle = explode('/###/', $productosarray[$i]);
        $array_carrito_detalle = [];
        for($x = 1;$x <  count($productosarraydetalle);$x++){
            $productosarraydet = explode('/,,,/', $productosarraydetalle[$x]);   
            $array_carrito_detalle[] = [
                'idproducto' => $productosarraydet[0],
                'productonombre' => $productosarraydet[1],
                'productocantidad' => $productosarraydet[2],
                'productopreciounitario' => $productosarraydet[3]
            ];
        }
        $array_carrito[] = [
            'idtienda' => $productosarray1[0],
            'tiendanombre' => $productosarray1[1],
            'productos' => $array_carrito_detalle
        ];
    }

    return $array_carrito;*/
}
function getTipoDocumento( $codigo ){
  switch( $codigo ){
     case '3':
         $documento  = 'BOLETA ELECTRÓNICA';
         break;
    case '1':
         $documento  = 'FACTURA ELECTRÓNICA';
         break;
    case '07':
         $documento  = 'NOTA DE CRÉDITO';
         break;
    case '08':
         $documento  = 'NOTA DE DÉBITO';
         break;
    case '09':
         $documento  = 'GUIA REMISION REMITENTE';
         break;
    default:
           $documento  = 'SIN DOC';
  } 
  return $documento;
}
function getTipoDocumentoCompra( $codigo ){
  switch( $codigo ){
     case '2':
         $documento  = 'BOLETA';
         break;
    case '1':
         $documento  = 'FACTURA';
         break;
    default:
           $documento  = 'SIN DOC';
  } 
  return $documento;
}
function getTipoMoneda( $tipo ){
  switch( $tipo ){
     case 'PEN':
     case 1:
         $moneda  = 'SOLES';
         break;
    case 'USD':
    case 2:
         $moneda  = 'DOLARES';
         break;
    default:
           $moneda  = '';
  }
  return $moneda;
}
function createTokenSunat($id_token, $clave_token){
    if($id_token != '' && $clave_token != ''){
        $sunatUri = "https://api-seguridad.sunat.gob.pe/v1/clientesextranet/{$id_token}/oauth2/token/";
        $params = [
            'grant_type'    => 'client_credentials',
            'scope'         => 'https://api.sunat.gob.pe/v1/contribuyente/contribuyentes',
            'client_id'     => $id_token,
            'client_secret' => $clave_token
        ];
  
        // Inicializar la sesión de cURL
        $ch = curl_init();
  
        // Configurar las opciones de cURL
        curl_setopt($ch, CURLOPT_URL, $sunatUri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
  
        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($ch);
  
        // Verificar si hay errores
        if(curl_errno($ch)){
            $error_msg = curl_error($ch);
        }
  
        // Cerrar la sesión de cURL
        curl_close($ch);
  
        // Procesar la respuesta y devolverla
        if(isset($error_msg)){
            return ['success' => false, 'response' => $error_msg];
        }else{
            $response = json_decode($response, true);
            return $response;
        }
  
    }
    return ['success' => false, 'response' => 'tokens inválidos'];
  }

  function calcularDiasPasados($fecha) {
      // Convertimos la fecha recibida a un objeto DateTime
      $fecha_inicial = new DateTime($fecha);

      // Obtenemos la fecha de hoy
      $hoy = new DateTime();

      // Calculamos la diferencia entre las dos fechas
      $diferencia = $hoy->diff($fecha_inicial);

      // Obtenemos la cantidad de días pasados
      $dias_pasados = $diferencia->days;

      return $dias_pasados;
  }
  