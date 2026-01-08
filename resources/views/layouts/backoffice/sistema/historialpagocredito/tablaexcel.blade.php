<table style="width:100%">
    <thead>
        <tr></tr>
        <tr>
            <th></th>
            <th style="font-weight: 900; background-color:#31353d; color: #ffffff; text-align: center; font-size: 12px; " 
                colspan="16">
              {{ $titulo }}
            </th>
        </tr>
    
        <tr>
            <th></th>
            <th style="font-weight: 900;">Agencia:</th>
            <th style="font-weight: 900;" colspan="17">{{ $agencia?$agencia->nombreagencia:'TODA LAS AGENCIAS' }}</th>
        </tr>
        <tr>
            <th></th>
            <th style="font-weight: 900;">Periodo:</th>
            <th style="font-weight: 900;" colspan="17">{{ $fechainicio }}</th>
        </tr>
        <tr>
            <th></th>
            <th style="font-weight: 900;">AL:</th>
            <th style="font-weight: 900;" colspan="17">{{ $fechafin }}</th>
        </tr>
        <tr></tr>
        <tr>
                  <td></td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">N°</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Cuenta</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">T. Cred.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Apellidos y Nombres</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">DOI/RUC</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Fecha/Hora</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Cuotas</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Capital</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Interés</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">C. SS /Desgrav.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Cargo</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Cust.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">I. Comp.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">I. Morat.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Total (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">F/L. PAGO</td>
        </tr>
    </thead>
    <tbody>
       
          <?php  
          $html = '';    
          $total_cuotapagado = 0;
          $total_acuenta = 0;
          $total_interes_compensatorio = 0;
          $total_interes_moratorio = 0;
          $total_custodia = 0;
          $total_cuentaxcobrar = 0;
          $total_total = 0;
          $total_caja = 0;
          $total_banco = 0;
      
          $total_capital = 0;
          $total_interes = 0;
          $total_comision = 0;
          
          
          foreach($credito_cobranzacuotas as $key => $value){
            
              $credito_adelanto = DB::table('credito_adelanto')->where('credito_adelanto.idcredito_cobranzacuota',$value->id)->get();
              
              $t_cuotapagado = 0;
              $t_acuenta = 0;
              $t_penalidad = 0;
              $t_tenencia = 0;
              $t_compensatorio = 0;
              $t_cuentaxcobrar = 0;
              $t_capital = 0;
              $t_interes = 0;
              $t_comision = 0;
              //$t_total = 0;
            
              foreach($credito_adelanto as $valueadelanto){
                  $credito_cronograma = DB::table('credito_cronograma')->where('credito_cronograma.id',$valueadelanto->idcredito_cronograma)->first();
                  if($credito_cronograma){
                      if($credito_cronograma->idestadocredito_cronograma==2){
                          $t_cuotapagado = $t_cuotapagado+$valueadelanto->total;
                      }else{
                          if($t_cuotapagado>0){
                              $t_acuenta = $t_acuenta+$valueadelanto->total;
                          }else{
                              $t_acuenta = $t_acuenta+$valueadelanto->capital+$valueadelanto->comision+$valueadelanto->cargo+$valueadelanto->interes;
                          }
                      }
                  }
                  $t_penalidad = $t_penalidad+$valueadelanto->penalidad;
                  $t_tenencia = $t_tenencia+$valueadelanto->tenencia;
                  $t_compensatorio = $t_compensatorio+$valueadelanto->compensatorio;
                  $t_capital = $t_capital+$valueadelanto->capital;
                  $t_interes = $t_interes+$valueadelanto->interes;
                  $t_comision = $t_comision+$valueadelanto->comision;
                  //$t_total = $t_total+$valueadelanto->total;
              }
            
              $t_cuotapagado = number_format($t_cuotapagado, 2, '.', '');
              $t_acuenta = number_format($t_acuenta, 2, '.', '');
              $t_penalidad = number_format($t_penalidad, 2, '.', '');
              $t_tenencia = number_format($t_tenencia, 2, '.', '');
              $t_compensatorio = number_format($t_compensatorio, 2, '.', '');
              $t_cuentaxcobrar = number_format($value->cobrar_cargo, 2, '.', '');
              $t_total = number_format($value->total_pagar+$t_cuentaxcobrar, 2, '.', '');
              $t_capital = number_format($t_capital, 2, '.', '');
              $t_interes = number_format($t_interes, 2, '.', '');
              $t_comision = number_format($t_comision, 2, '.', '');
            
              $operacionen1 = '';
       
              if($value->idformapago==0){ $operacionen1 = 'TRANSITORIO'; }
              if($value->idformapago==1){ $operacionen1 = 'CAJA'; }
              if($value->idformapago==2){ $operacionen1 = 'BANCO'; }
            
              $coutas = str_replace(',',', ',$value->pago_cuota);
              //$num_operacion =  'OP'.str_pad($value->codigo, 10, "0", STR_PAD_LEFT);
            
              $cp = '';
              if($value->idforma_credito==1){
                  $cp = 'CP';
              }
              elseif($value->idforma_credito==2){
                  $cp = 'CNP';
              }
              elseif($value->idforma_credito==3){
                  $cp = 'CC';
              }
              $fecharegistro = $value->fecharegistro;
              $html .= "<tr>
                            <td></td>
                            <td>".($key+1)."</td>
                            <td>C{$value->cuentacredito}</td>
                            <td>{$cp}</td>
                            <td>{$value->nombrecliente}</td>
                            <td>{$value->identificacion}</td>
                            <td>{$fecharegistro}</td>
                            <td>{$coutas}</td>
                            
                            <td>{$t_capital}</td>
                            <td>{$t_interes}</td>
                            <td>{$t_comision}</td>
                            
                            <td>{$t_cuentaxcobrar}</td>
                            <td>{$t_tenencia}</td>
                            <td>{$t_penalidad}</td>
                            <td>{$t_compensatorio}</td>
                            <td>{$t_total}</td>
                            <td>{$operacionen1}</td>
                        </tr>";
                        
                    
              //$total_cuotapagado = $total_cuotapagado+$t_cuotapagado;
              //$total_acuenta = $total_acuenta+$t_acuenta;
              $total_interes_compensatorio = $total_interes_compensatorio+$t_penalidad;
              $total_interes_moratorio = $total_interes_moratorio+$t_compensatorio;
              $total_custodia = $total_custodia+$t_tenencia;
              $total_cuentaxcobrar = $total_cuentaxcobrar+$t_cuentaxcobrar;
              $total_total = $total_total+$t_total;
            
              $total_capital = $total_capital+$t_capital;
              $total_interes = $total_interes+$t_interes;
              $total_comision = $total_comision+$t_comision;
            
              if($value->idformapago==1){
                  $total_caja = $total_caja+$t_total;
              }
              if($value->idformapago==2){
                  $total_banco = $total_banco+$t_total;
              }
          }
          if(count($credito_cobranzacuotas)==0){
              $html.= '<tr><td colspan="18" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
          }
              $html .= '
                <tr>
                  <td></td>
                  <td colspan="7" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right">TOTAL S/.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right">'.number_format($total_capital, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right">'.number_format($total_interes, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right">'.number_format($total_comision, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right">'.number_format($total_cuentaxcobrar, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right">'.number_format($total_custodia, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right">'.number_format($total_interes_compensatorio, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right">'.number_format($total_interes_moratorio, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right">'.number_format($total_total, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;"></td>
                </tr>
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>
                <tr>
                  <td></td>
                  <td colspan="7"></td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;width:70px;">RESUMEN:</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;width:70px;">CAJA</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;width:70px;">'.number_format($total_caja, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;width:70px;">BANCO</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;width:70px;">'.number_format($total_banco, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;width:70px;">TRANSIT.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;width:70px;">0.00</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;width:70px;">T. EFE. (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;width:70px;">'.number_format($total_caja+$total_banco, 2, '.', '').'</td>
                  <td style="" colspan="4"></td>
                </tr>';
            echo $html;
              ?>
    </tbody>
</table>