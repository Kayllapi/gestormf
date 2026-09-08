<table style="width:100%">
    <thead>
        <tr></tr>
        <tr>
            <th></th>
            <th style="font-weight: 900; background-color:#31353d; color: #ffffff; text-align: center; font-size: 12px; "
                colspan="17">
              {{ $titulo }}
            </th>
        </tr>

        <tr>
            <th></th>
            <th style="font-weight: 900;">Agencia:</th>
            <th style="font-weight: 900;" colspan="16">{{ $agencia?$agencia->nombreagencia:'TODA LAS AGENCIAS' }}</th>
        </tr>
        <tr>
            <th></th>
            <th style="font-weight: 900;">Periodo:</th>
            <th style="font-weight: 900;">{{ $fechainicio }}</th>
            <th style="font-weight: 900;">AL:</th>
            <th style="font-weight: 900;" colspan="14">{{ $fechafin }}</th>
        </tr>
        <tr></tr>
        <tr>
                  <td></td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">N°</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Cuenta</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">F.C.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Apellidos y Nombres</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">RUC/DNI/CE</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Fecha/Hora</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Cuotas</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Capital</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Interés</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Carg. x Cust. G./Ot.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Ss. Recau.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">CxC</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">P. Cust.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Int. Comp.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Int. Morat.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Total (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">F/L. PAGO</td>
        </tr>
    </thead>
    <tbody>

          <?php
          $html = '';

          $total_amortizacion = 0;
          $total_interes = 0;
          $total_cargo = 0;
          $total_comision = 0;
          $cobrar_cargo = 0;
          $total_tenencia = 0;
          $total_penalidad = 0;
          $total_compensatorio = 0;
          $total_totalcuota = 0;

          $total_caja = 0;
          $total_banco = 0;


          foreach($credito_cobranzacuotas as $key => $value){

              $operacionen1 = '';

              if($value->idformapago==0){ $operacionen1 = 'TRANSITORIO'; }
              if($value->idformapago==1){ $operacionen1 = 'CAJA'; }
              if($value->idformapago==2){ $operacionen1 = 'BANCO'; }

              $cuotas = str_replace(',',', ',$value->pago_cuota);

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
              $fecharegistro = date_format(date_create($value->fecharegistro),"d-m-Y H:i:s A");
              $html .= "<tr>
                            <td></td>
                            <td>".($key+1)."</td>
                            <td>C{$value->cuentacredito}</td>
                            <td>{$cp}</td>
                            <td>{$value->nombrecliente}</td>
                            <td>{$value->identificacion}</td>
                            <td>{$fecharegistro}</td>
                            <td>{$cuotas}</td>
                            <td>{$value->total_amortizacion}</td>
                            <td>{$value->total_interes}</td>
                            <td>{$value->total_cargo}</td>
                            <td>{$value->total_comision}</td>
                            <td>{$value->cobrar_cargo}</td>
                            <td>{$value->total_tenencia}</td>
                            <td>{$value->total_penalidad}</td>
                            <td>{$value->total_compensatorio}</td>
                            <td>{$value->total_totalcuota}</td>
                            <td>{$operacionen1}</td>
                        </tr>";


              $total_amortizacion += $value->total_amortizacion;
              $total_interes += $value->total_interes;
              $total_cargo += $value->total_comision;
              $total_comision += $value->total_cargo;
              $cobrar_cargo += $value->cobrar_cargo;
              $total_tenencia += $value->total_tenencia;
              $total_penalidad += $value->total_penalidad;
              $total_compensatorio += $value->total_compensatorio;
              $total_totalcuota += $value->total_totalcuota;

              if($value->idformapago==1){
                  $total_caja = $total_caja+$value->total_totalcuota;
              }
              if($value->idformapago==2){
                  $total_banco = $total_banco+$value->total_totalcuota;
              }
          }
          if(count($credito_cobranzacuotas)==0){
              $html.= '<tr><td></td><td colspan="17" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
          }
              $html .= '
                <tr>
                  <td></td>
                  <td colspan="7" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">TOTAL S/.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_amortizacion, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_interes, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_comision, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_cargo, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($cobrar_cargo, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_tenencia, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_penalidad, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_compensatorio, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_totalcuota, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;"></td>
                </tr>
                <tr></tr>
                <tr>
                  <td></td>
                  <td colspan="7"></td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">RESUMEN:</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">CAJA</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_caja, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">BANCO</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_banco, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">TRANSIT.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">0.00</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">T. EFE. (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_caja+$total_banco, 2, '.', '').'</td>
                  <td colspan="2"></td>
                </tr>';
            echo $html;
              ?>
    </tbody>
</table>
