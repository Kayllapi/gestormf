<!DOCTYPE html>
<html>
<head>
    <title>PAGO DE CRÉDITO</title>
    <style>
      *{
        font-family:helvetica;
        font-size:12px;
      }
      @page {
          margin: 15px;
      }
      .ticket_contenedor {
          width: 300px;
      }
      .cabecera {
          
      }
      .titulo {
        text-align: center;
        
      }
      .linea {
          width:100%;
          border-top:1px solid #000;
      }
    </style>
</head>
<body>
    <div class="ticket_contenedor">
          <div class="cabecera"><b>{{ $tienda->nombre }} - {{ $tienda->nombreagencia }}</b></div>
          <div class="linea"></div>
          <br>
          <div class="titulo"><b>PAGO DE CRÉDITO</b></div>  
          <table style="width:100%;">
            <tr>
                <td style="width:65px;">
                    <b>Fecha</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>
                    {{ date_format(date_create($credito_cobranzacuota->fecharegistro),'d-m-Y h:i:s A') }}
                </td>
            </tr>
            <tr>
                <td>
                    <b>Cliente</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>
                   {{ $usuario->nombrecompleto }}
                </td>
            </tr>
            <tr>
                <td>
                    <b>Cuenta</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>
                    C{{ str_pad($credito_cobranzacuota->creditocuenta, 8, "0", STR_PAD_LEFT) }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  <b>Ope.:</b> OP{{ str_pad($credito_cobranzacuota->codigo, 10, "0", STR_PAD_LEFT) }}
                </td>
            </tr>
            <tr>
                <td>
                    <b>Pagado en</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>
                    {{ $idformapago==1?'CAJA':'BANCO' }}
                </td>
            </tr>
              @if($idformapago==2)
            <tr>
                <td>
                    <b>Banco</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>
                    {{ $banco }} ***{{ substr($bancocuenta, -5) }}
                </td>
            </tr>
            <tr>
                <td>
                    <b>N° Op./Dt.</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>
                    {{ $numerooperacion }}
                </td>
            </tr>
              @endif
            <tr>
                <td>
                    <b>Cuota</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>
                    {{ $pago_cuota }}
                </td>
            </tr>
          </table>  
          <div class="linea"></div>
          <table style="width:100%;">
            <tr>
                <td style="width:65px;">
                    <b>Dias atraso</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>
                    {{ $pago_diasatraso }}
                </td>
            </tr>
          </table>   
     
                    <?php
                    $credito_adelanto = DB::table('credito_adelanto')->where('credito_adelanto.idcredito_cobranzacuota',$credito_cobranzacuota->id)->get();
              
                    $t_cuotapagado = 0;
                    $t_acuenta = 0;
                    $t_penalidad = 0;
                    $t_tenencia = 0;
                    $t_compensatorio = 0;

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
                    }
                  
                    ?>
          <table style="width:100%;">
            <tr>
                <td>
                    <b>Moto Recibido (Soles):</b>
                </td>
                <td width="5px" style="padding-top:5px;padding-bottom:5px;text-align:right;">
                    <b>S/.:</b>
                </td>
                <td width="60px" style="padding-top:5px;padding-bottom:5px;text-align:right;">
                    {{ number_format($credito_cobranzacuota->total_recibido+$credito_cobranzacuota->cobrar_cargo, 2, '.', '') }}
                </td>
            </tr>
            <tr>
                <td style="border-top: 1px dashed #000;padding-top:5px;padding-bottom:5px;">
                    <b>Importe de Cuota(s)</b><br>
                    <b>Pago a Cuenta</b> 
                    @if($t_acuenta>0 && ($t_penalidad+$t_tenencia+$t_compensatorio)>0)
                    <div style="text-align:right;float:right;">Cuota</div>
                    @endif
                    <br>
                    <b>Cust., I. Comp. y I. Morat.</b><br>
                    <b>Cuenta x C.</b>
                </td>
                    <?php
                    $credito_ultadelanto = DB::table('credito_adelanto')
                        ->whereIn('credito_adelanto.idestadocredito_adelanto',[1,2])
                        ->where('credito_adelanto.idcredito',$credito_cobranzacuota->idcredito)
                        ->orderBy('credito_adelanto.id','desc')
                        ->first();
                    ?>
                <td width="5px" style="border-top: 1px dashed #000;padding-top:5px;padding-bottom:5px;text-align:right;">
                  @if($t_acuenta>0 && ($t_penalidad+$t_tenencia+$t_compensatorio)>0)
                  &nbsp;
                  <br><b><?php if($credito_ultadelanto){ echo $credito_ultadelanto->numerocuota; } ?></b>
                  <br>&nbsp;
                  <br>&nbsp;
                  @endif
                </td>
                <td width="60px" style="border-top: 1px dashed #000;padding-top:5px;padding-bottom:5px;text-align:right;">
                    {{ number_format($t_cuotapagado, 2, '.', '') }}<br>
                    {{ number_format($t_acuenta, 2, '.', '') }}<br>
                    {{ number_format($t_penalidad+$t_tenencia+$t_compensatorio, 2, '.', '') }}<br>
                    {{ $credito_cobranzacuota->cobrar_cargo }}<br>
                </td>
            </tr>
            <tr>
              <td style="border-bottom: 1px dashed #000;border-top: 1px dashed #000;padding-top:5px;padding-bottom:5px;">
                <b>Total</b> 
              </td>
                <td width="5px" style="border-bottom: 1px dashed #000;border-top: 1px dashed #000;padding-top:5px;padding-bottom:5px;text-align:right;">
                    <b>S/.:</b>
                </td>
              <td style="border-bottom: 1px dashed #000;border-top: 1px dashed #000;padding-top:5px;padding-bottom:5px;text-align:right;">
                    {{ number_format($credito_cobranzacuota->total_pagar+$credito_cobranzacuota->cobrar_cargo, 2, '.', '') }}
              </td>
            </tr>
            <tr>
              <td style="padding-top:5px;padding-bottom:5px;">
                <b>Vuelto</b> 
              </td>
                <td width="5px" style="padding-top:5px;padding-bottom:5px;text-align:right;">
                    <b>S/.:</b>
                </td>
              <td style="padding-top:5px;padding-bottom:5px;text-align:right;">
                {{ $credito_cobranzacuota->vuelto }}
              </td>
            </tr>
          </table>     
          <table class="tabla_informativa">
              <tr>
                  <td><b>Proximo Vencimiento:</b> {{ $credito_cobranzacuota->proximo_vencimiento }}</td>
              </tr>
          </table> 
          <table style="width:100%;">
            <tr>
              <td style="border-bottom: 1px solid #000;padding-top:5px;padding-bottom:5px;">
                <b>Saldo Pend. de Pago</b> 
              </td>
                <td width="5px" style="border-bottom: 1px solid #000;padding-top:5px;padding-bottom:5px;text-align:right;">
                    <b>S/.:</b>
                </td>
              <td width="60px" style="border-bottom: 1px solid #000;padding-top:5px;padding-bottom:5px;text-align:right;">
                {{ $total_pendientepago }}
              </td>
            </tr>
          </table> 
          <table class="tabla_informativa">
              <tr>
                  <td><b>//{{ strtoupper($cajero->codigo) }}</b></td>
              </tr>
          </table>  
          @if($count_credito_cronograma==0 && $count_creditopendiente>0 && $credito->idforma_credito==1)
          <table class="tabla_informativa">
              <tr>
                  <td><b>GARANTÍA PENDIENTE DE ENTREGA</b></td>
              </tr>
          </table>  
          @endif
    </div>
</body>
</html>