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
          width: 270px;
      }
      .cabecera {
          
      }
      .titulo {
        text-align: center;
        
      }
      .linea {
          width:100%;
          border-top:0.5px solid #000;
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
                <td style="width:48px;">
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
                    <b>Pago en</b>
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
                <td style="width:48px;">
                    <b>D. Venc.</b>
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
                    $es_acuenta    = ($credito_cobranzacuota->opcion_pago == 'PAGO_ACUENTA');
                    $es_anticipado = ($credito_cobranzacuota->opcion_pago == 'PAGO_ANTICIPADO');

                    if ($es_anticipado) {
                        // Pago Anticipado: se sigue mostrando desde los totales ya consolidados en
                        // credito_cobranzacuota (sin tocar, tiene su propio manejo especial de
                        // reduccion_cuota/reduccion_plazo que no pasa por credito_adelanto igual que
                        // el resto de opciones).
                        $t_tenencia      = (float) $credito_cobranzacuota->total_tenencia;
                        $t_penalidad     = (float) $credito_cobranzacuota->total_penalidad;
                        $t_compensatorio = (float) $credito_cobranzacuota->total_compensatorio;
                        $t_cuotapagado   = (float) $credito_cobranzacuota->total_totalcuota - $t_tenencia - $t_penalidad - $t_compensatorio;
                        $t_acuenta       = (float) $credito_cobranzacuota->total_adelanto;
                    } else {
                        // PAGO_CUOTA / PAGO_TOTAL / PAGO_ACUENTA: se reconstruye desde el desglose de
                        // credito_adelanto que quedo grabado con ESTA transaccion (idcredito_cobranzacuota
                        // = este pago), que nunca cambia despues aunque se reimprima el voucher mas tarde.
                        // Ese desglose ya viene neto de cualquier pago a cuenta anterior sobre la misma
                        // cuota (select_cronograma reparte sobre el saldo que quedaba, no sobre el
                        // importe integro), asi que sumandolo aqui se obtiene: lo realmente pendiente de
                        // la(s) cuota(s) que este pago cerro, y la mora generada desde el ultimo abono
                        // (no la mora acumulada historica, que ya se cobro en pagos anteriores).
                        // Las cuotas que este pago SI cerro estan en pago_cuota (fijo desde el momento
                        // del pago); cualquier otra cuota en el desglose sigue abierta y lo que se le
                        // aplico es un pago a cuenta que se traslada a la siguiente cobranza.
                        $cuotas_cerradas = array_filter(
                            array_map('trim', explode(',', (string) $credito_cobranzacuota->pago_cuota)),
                            fn($v) => $v !== ''
                        );

                        $credito_adelanto_pago = DB::table('credito_adelanto')
                            ->where('credito_adelanto.idcredito_cobranzacuota', $credito_cobranzacuota->id)
                            ->get();

                        $t_cuotapagado   = 0;
                        $t_acuenta       = 0;
                        $t_penalidad     = 0;
                        $t_tenencia      = 0;
                        $t_compensatorio = 0;
                        foreach ($credito_adelanto_pago as $valueadelanto) {
                            if (in_array((string) $valueadelanto->numerocuota, $cuotas_cerradas)) {
                                // Cuota cerrada por este pago: se desglosa en sus dos lineas
                                // (cuota / mora). Sumarlas por separado no pierde nada, ambas
                                // vienen de este mismo registro.
                                $t_cuotapagado   += (float) $valueadelanto->capital + (float) $valueadelanto->interes + (float) $valueadelanto->comision + (float) $valueadelanto->cargo;
                                $t_penalidad     += (float) $valueadelanto->penalidad;
                                $t_tenencia      += (float) $valueadelanto->tenencia;
                                $t_compensatorio += (float) $valueadelanto->compensatorio;
                            } else {
                                // Cuota que sigue abierta: su "total" (cuota+mora) va integro a
                                // "Pago a Cuenta". Sumar su mora tambien en la linea de mora
                                // la duplicaria, ya va incluida dentro de este total.
                                $t_acuenta += (float) $valueadelanto->total;
                            }
                        }
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
                <td style="border-top: 0.8px dashed #000;padding-top:5px;padding-bottom:5px;">
                    <b>Importe de Cuota(s)</b><br>
                    <b>Pago a Cuenta</b>
                    @if($es_acuenta && $t_acuenta>0)
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
                <td width="5px" style="border-top: 0.8px dashed #000;padding-top:5px;padding-bottom:5px;text-align:right;">
                  @if($es_acuenta && $t_acuenta>0)
                  &nbsp;
                  <br><b><?php if($credito_ultadelanto){ echo $credito_ultadelanto->numerocuota; } ?></b>
                  <br>&nbsp;
                  <br>&nbsp;
                  @endif
                </td>
                <td width="60px" style="border-top: 0.8px dashed #000;padding-top:5px;padding-bottom:5px;text-align:right;">
                    {{ number_format($t_cuotapagado, 2, '.', '') }}<br>
                    {{ number_format($t_acuenta, 2, '.', '') }}<br>
                    {{ number_format($t_penalidad+$t_tenencia+$t_compensatorio, 2, '.', '') }}<br>
                    {{ $credito_cobranzacuota->cobrar_cargo }}<br>
                </td>
            </tr>
            <tr>
              <td style="border-bottom: 0.8px dashed #000;border-top: 0.8px dashed #000;padding-top:5px;padding-bottom:5px;">
                <b>Total</b> 
              </td>
                <td width="5px" style="border-bottom: 0.8px dashed #000;border-top: 0.8px dashed #000;padding-top:5px;padding-bottom:5px;text-align:right;">
                    <b>S/.:</b>
                </td>
              <td style="border-bottom: 0.8px dashed #000;border-top: 0.8px dashed #000;padding-top:5px;padding-bottom:5px;text-align:right;">
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
          <table class="tabla_informativa" style="width:100%; margin-top:-8px;">
              <tr>
                  <td><b>Proximo Vencimiento:</b> {{ $credito_cobranzacuota->proximo_vencimiento }}</td>
              </tr>
          </table> 
          <table style="width:100%; margin-top:-8px;">
            <tr>
              <td style="border-bottom: 0.5px solid #000;padding-top:5px;padding-bottom:5px;">
                <b>Saldo Pend. de Pago</b> 
              </td>
                <td width="5px" style="border-bottom: 0.5px solid #000;padding-top:5px;padding-bottom:5px;text-align:right;">
                    <b>S/.:</b>
                </td>
              <td width="60px" style="border-bottom: 0.5px solid #000;padding-top:5px;padding-bottom:5px;text-align:right;">
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