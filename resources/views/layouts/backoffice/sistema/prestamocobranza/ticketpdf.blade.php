<!DOCTYPE html>
<html>
<head>
    <title>TICKET DE COBRANZA</title>
    @include('app.pdf_style',['idtienda'=>$tienda->id])
</head>
<body>
    <div class="ticket_contenedor">
      <div class="contenedor">
          @include('app.pdf_headerticket',[
              'logo'=>$agencia->logo,
              'nombrecomercial'=>$agencia->nombrecomercial,
              'ruc'=>$agencia->ruc,
              'direccion'=>$agencia->direccion,
              'ubigeo'=>$agencia->ubigeonombre,
              'tienda'=>$tienda,
          ])
          <table class="tabla_informativa">
              <tr>
                <td class="tabla_informativa_subtitulo">FECHA</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ date_format(date_create($cobranza->fecharegistro), "d/m/Y h:i A") }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo" style="width:80px;">COD. COBRANZA</td>
                <td class="tabla_informativa_punto" style="width:5px;">:</td>
                <td class="tabla_informativa_descripcion">{{ str_pad($cobranza->codigo, 8, "0", STR_PAD_LEFT) }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">COD. CRÉDITO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ str_pad($cobranza->creditocodigo, 8, "0", STR_PAD_LEFT) }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">DNI</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $cobranza->cliente_identificacion }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">CLIENTE</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $cobranza->cliente }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">VENTANILLA</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $cobranza->cajero_apellidos }}, {{ $cobranza->cajero_nombre }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">MONEDA</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $cobranza->monedanombre }}</td>
              </tr>
              @if($cobranza->proximo_vencimiento!='')
              <tr>
                <td class="tabla_informativa_subtitulo">PROX. VCTO.</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{date_format(date_create($cobranza->proximo_vencimiento), "d/m/Y")}} <span style="float:right;">{{ $cobranza->cronograma_ultimonumerocuota }}/{{ $cobranza->numerocuota }}</span></td>
              </tr>
              @endif

              <?php
                    $cobranzabcancaria = DB::table('s_formapagodetalle')
                      ->where('s_formapagodetalle.s_idprestamo_cobranza',$cobranza->id)->get();
              ?>
              <!--tr>
                <td>T. DE PAGO <div style="float:right">:</div></td>
                <td>
                  @if($cobranza->cronograma_idtipopago==1)
                      POR CUOTAS
                  @elseif($cobranza->cronograma_idtipopago==2)
                      COMPLETO
                  @endif
                </td>
              </tr>
              @if(count($cobranzabcancaria)>0)
              <tr>
                <td>F. PAGO <div style="float:right">:</div></td>
                <td>
                  DEPOSITO
                </td>
              </tr>
              @else
              <tr>
                <td>F. PAGO <div style="float:right">:</div></td>
                <td>
                  CONTADO
                </td>
              </tr>
              @endif
              -->
          </table>
          <div class="espacio"></div>
        
        
          @if(configuracion($tienda->id,'prestamo_formatoticket')['valor']==2) 
          <table class="tabla_informativa">
              <?php
              $capital = 0;
              $interes = 0;
              $cuota = 0;
              $moraapagar = 0;
              $total = 0;
              $acuenta = 0;
              $totalacumulado = 0;
              $totalpagado = $cobranza->cronograma_pagado;
              ?>
              @foreach ($cobranzadetalle as $value)
                  <?php
                  $totalacumulado = $totalacumulado+($value->total+$value->moraapagar);
                  ?>
                  @if($totalpagado>$totalacumulado)
                  <?php
                  $totalpagado = $totalpagado-$totalacumulado;
                  ?>
                  @elseif($totalpagado<=$totalacumulado&&$totalpagado>=0)
                  <?php
                  $totalpagado = 0;
                  ?>
                  @endif
                  @if($value->acuenta>0)
                  <?php
                  $acuenta = $acuenta+$value->acuenta;
                  ?>
                  @else
                  <?php
                  $capital = $capital+$value->amortizacion;
                  $interes = $interes+$value->interes;
                  $cuota = $cuota+$value->total;
                  $moraapagar = $moraapagar+$value->moraapagar;
                  //$acuenta = $acuenta+$value->acuenta;
                  $total = $total+($value->total+$value->moraapagar);
                  ?>
                  @endif
              @endforeach
              @if($capital==0 && $interes==0 && $cuota==0)
              @else
              <tr>
                <td colspan="5"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo" style="text-align: right;">CAPITAL</td>
                <td class="tabla_informativa_subtitulo" style="text-align: right;">INTERES</td>
                <td class="tabla_informativa_subtitulo" style="text-align: right;">CUOTA</td>
                <td class="tabla_informativa_subtitulo" style="text-align: right;">MORA</td>
                <td class="tabla_informativa_subtitulo" style="text-align: right;">TOTAL</td>
              </tr>
              <tr>
                <td colspan="5"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
              </tr>
              <tr>
                <td style="text-align: right;">{{number_format($capital, 2, '.', '')}}</td>
                <td style="text-align: right;">{{number_format($interes, 2, '.', '')}}</td>
                <td style="text-align: right;">{{number_format($cuota, 2, '.', '')}}</td>
                <td style="text-align: right;">{{number_format($moraapagar, 2, '.', '')}}</td>
                <td style="text-align: right;">{{number_format($total, 2, '.', '')}}</td>
              </tr>
              @endif
              <tr>
                <td colspan="5"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
              </tr>
              @if($cobranza->cronograma_acuentaanterior>0)
              <tr>
                <td class="tabla_informativa_subtitulo" colspan="4" style="text-align: right;">A CUENTA (ANTERIOR) 1</td>
                <td class="tabla_informativa_descripcion" style="text-align: right;width:40px;;">{{ $cobranza->cronograma_acuentaanterior }}</td>
              </tr>
              @elseif($acuenta>0)
              <tr>
                <td class="tabla_informativa_subtitulo" colspan="4" style="text-align: right;">A CUENTA (ANTERIOR) 2</td>
                <td class="tabla_informativa_descripcion" style="text-align: right;width:40px;;">{{number_format($acuenta-$cobranza->cronograma_acuentaproxima, 2, '.', '')}}</td>
              </tr>
              @endif
              <tr>
                <td class="tabla_informativa_subtitulo" colspan="4" style="text-align: right">TOTAL PAGADO</td>
                <td class="tabla_informativa_descripcion" style="text-align: right;;width:40px;">{{ $cobranza->cronograma_pagado }}</td>
              </tr>
              @if($cobranza->cronograma_acuentaproxima>0)
              <tr>
                <td class="tabla_informativa_subtitulo" colspan="4" style="text-align: right;">A CUENTA (PRÓXIMA)</td>
                <td class="tabla_informativa_descripcion" style="text-align: right;width:40px;;">{{ $cobranza->cronograma_acuentaproxima }}</td>
              </tr>
              @endif
              <tr>
                <td colspan="5"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
              </tr>
          </table>
          @else
          <table class="tabla_informativa">
              <tr>
                <td colspan="3"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
              </tr>
              @if(count($cobranzadetalle)>0)
              @foreach ($cobranzadetalle as $value)
              <tr>
                <td class="tabla_informativa_subtitulo" style="width:120px;">NRO. DE CUOTA</td>
                <td style="width:5px;">:</td>
                <td class="tabla_informativa_descripcion">{{ str_pad($value->numero, 2, "0", STR_PAD_LEFT) }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">CUOTA</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $value->total }}</td>
              </tr>
              @if($value->moradescuento>0)
              <tr>
                <td class="tabla_informativa_subtitulo">MORA (+)</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $value->mora }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">DSCTO. MORA (-)</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $value->moradescuento }}</td>
              </tr>
              @endif
              @if($value->moraapagar>0)
              <tr>
                <td class="tabla_informativa_subtitulo">MORA A PAGAR (+)</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $value->moraapagar }}</td>
              </tr>
              @endif
              @if($value->acuenta>0)
              <tr>
                <td class="tabla_informativa_subtitulo">A CUENTA (-)</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $value->acuenta }}</td>
              </tr>
              @endif
              @if($value->abono>0)
              <tr>
                <td class="tabla_informativa_subtitulo">ABONO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $value->abono }}</td>
              </tr>
              @endif
              <tr>
                <td colspan="3" style="text-align: center; height:10px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
              </tr>
              @if(count($cobranzadetalle)>1)
              <!--tr>
                <td class="tabla_informativa_subtitulo">TOTAL</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $value->cuotaapagar }}</td>
              </tr-->
              @endif
              @endforeach
              @if(count($cobranzadetalle)==1)
              <!--tr>
                <td colspan="3" style="text-align: center; height:10px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
              </tr-->
              @endif
              @if($cobranza->cronograma_moradescuento>0)
              <!--tr>
                <td class="tabla_informativa_subtitulo">TOTAL DSCTO. MORA</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $cobranza->cronograma_moradescuento }}</td>
              </tr-->
              @endif
              <!--tr>
                <td class="tabla_informativa_subtitulo">TOTAL CUOTAS</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $cobranza->cronograma_total }}</td>
              </tr-->
              @if($cobranza->cronograma_total!=$cobranza->cronograma_totalredondeado)
              <tr>
                <td class="tabla_informativa_subtitulo">TOTAL REDONDEADO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $cobranza->cronograma_totalredondeado }}</td>
              </tr>
              @endif
              @if($cobranza->cronograma_totalredondeado+$cobranza->cronograma_abono>0)
              <!--tr>
                <td class="tabla_informativa_subtitulo">TOTAL ABONOS</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $cobranza->cronograma_abono }}</td>
              </tr>
              <tr>
                <td colspan="3" style="text-align: center; height:10px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
              </tr-->
              @if($cobranza->cronograma_morapendientefinal>0)
              <tr>
                <td class="tabla_informativa_subtitulo">MORA ACUMULADO (+)</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $cobranza->cronograma_morapendientefinal }}</td>
              </tr>
              @endif
              @if($cobranza->cronograma_interesdescuento>0)
              <tr>
                <td class="tabla_informativa_subtitulo">DESC. INTERES (-)</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $cobranza->cronograma_interesdescuento }}</td>
              </tr>
              @endif
              <tr>
                <td class="tabla_informativa_subtitulo">TOTAL A PAGAR</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ number_format($cobranza->cronograma_totalredondeado+$cobranza->cronograma_abono, 2, '.', '') }}</td>
              </tr>
              <tr>
                <td colspan="3" style="text-align: center; height:10px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
              </tr>
              @endif
              @endif
              @if($cobranza->cronograma_acuentaanterior>0)
              <tr>
                <td class="tabla_informativa_subtitulo">A CUENTA (ANTERIOR)</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $cobranza->cronograma_acuentaanterior }}</td>
              </tr>
              @endif
              @if($cobranza->cronograma_deposito>0)
              <tr>
                <td class="tabla_informativa_subtitulo">PAGO DEPÓSITO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $cobranza->cronograma_deposito }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">PAGO EFECTIVO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $cobranza->cronograma_pagado }}</td>
              </tr>
              <tr>
                <td colspan="3" style="text-align: center; height:10px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
              </tr>
              <!--tr>
                <td class="tabla_informativa_subtitulo">TOTAL PAGADO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ number_format($cobranza->cronograma_pagado+$cobranza->cronograma_deposito, 2, '.', '') }}</td>
              </tr-->
              @endif
              <tr>
                <td class="tabla_informativa_subtitulo">MONTO PAGADO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $cobranza->cronograma_montorecibido }}</td>
              </tr>
              @if($cobranza->cronograma_vuelto>0)
              <tr>
                <td class="tabla_informativa_subtitulo">VUELTO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $cobranza->cronograma_vuelto }}</td>
              </tr>
              @endif
              @if($cobranza->cronograma_acuentaproxima>0)
              <tr>
                <td class="tabla_informativa_subtitulo">A CUENTA (PRÓXIMA)</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $cobranza->cronograma_acuentaproxima }}</td>
              </tr>
              @endif
          </table>
          @endif
        
          <div class="espacio"></div>
  
        
          @if(configuracion($tienda->id,'prestamo_mensajeadicionalticket_1')['resultado']=='CORRECTO')
          <div class="dato_adicional">
            {{ configuracion($tienda->id,'prestamo_mensajeadicionalticket_1')['valor'] }}
          </div>
          <div class="espacio"></div>
          @endif
          @if(configuracion($tienda->id,'prestamo_mensajeadicionalticket_2')['resultado']=='CORRECTO')
          <div class="dato_adicional">
            {{ configuracion($tienda->id,'prestamo_mensajeadicionalticket_2')['valor'] }}
          </div>
          <div class="espacio"></div>
          @endif
          @if(configuracion($tienda->id,'prestamo_mensajeadicionalticket_3')['resultado']=='CORRECTO')
          <div class="dato_adicional">
            {{ configuracion($tienda->id,'prestamo_mensajeadicionalticket_3')['valor'] }}
          </div>
          <div class="espacio"></div>
          @endif
      </div>
    </body>
</html>