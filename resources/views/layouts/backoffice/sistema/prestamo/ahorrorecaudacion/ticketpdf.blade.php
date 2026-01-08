<!DOCTYPE html>
<html>
<head>
    <title>TICKET DE AHORRO</title>
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
                <td class="tabla_informativa_descripcion">{{ date_format(date_create($recaudacion->fecharegistro), "d/m/Y h:i A") }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo" style="width:80px;">COD. VENTA</td>
                <td class="tabla_informativa_punto" style="width:5px;">:</td>
                <td class="tabla_informativa_descripcion">{{ str_pad($recaudacion->codigo, 8, "0", STR_PAD_LEFT) }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">COD. CRÉDITO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ str_pad($recaudacion->creditocodigo, 8, "0", STR_PAD_LEFT) }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">DNI</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $recaudacion->cliente_identificacion }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">CLIENTE</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $recaudacion->cliente }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">VENTANILLA</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $recaudacion->cajero_apellidos }}, {{ $recaudacion->cajero_nombre }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">MONEDA</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $recaudacion->monedanombre }}</td>
              </tr>
              @if($prestamoahorro->idprestamo_tipoahorro==2)
              @if($recaudacion->proximo_vencimiento!='')
              <tr>
                <td class="tabla_informativa_subtitulo">PROX. VCTO.</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{date_format(date_create($recaudacion->proximo_vencimiento), "d/m/Y")}} <span style="float:right;">{{ $recaudacion->cronograma_ultimonumerocuota }}/{{ $recaudacion->numerocuota }}</span></td>
              </tr>
              @endif
              @endif

              <?php
                    $recaudacionbcancaria = DB::table('s_prestamo_cobranzacuentabancaria')
                      ->where('s_prestamo_cobranzacuentabancaria.s_idprestamo_cobranza',$recaudacion->id)->get();
              ?>
          </table>
          <div class="espacio"></div>
          <table class="tabla_informativa">
              <tr>
                <td colspan="3"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
              </tr>
              @if(count($recaudaciondetalle)>0)
              @foreach ($recaudaciondetalle as $value)
              <tr>
                <td class="tabla_informativa_subtitulo" style="width:120px;">NRO. DE CUOTA</td>
                <td style="width:5px;">:</td>
                <td class="tabla_informativa_descripcion">{{ str_pad($value->numero, 2, "0", STR_PAD_LEFT) }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">CUOTA</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $value->cuota }}</td>
              </tr>
              @if($value->interesdescontado>0)
              <tr>
                <td class="tabla_informativa_subtitulo">INTERES DESC. (-)</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $value->interesdescontado }}</td>
              </tr>
              @endif
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
                <td class="tabla_informativa_subtitulo">A CUENTA (ANTERIOR)</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $value->acuenta }}</td>
              </tr>
              @endif
              @if(count($recaudaciondetalle)>1)
              <tr>
                <td class="tabla_informativa_subtitulo">TOTAL</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $value->cuotaapagar }}</td>
              </tr>
              <tr>
                <td colspan="3" style="text-align: center; height:10px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
              </tr>
              @endif
              @endforeach
              @if(count($recaudaciondetalle)==1)
              <tr>
                <td colspan="3" style="text-align: center; height:10px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
              </tr>
              @endif
              @if($recaudacion->cronograma_moradescuento>0)
              <tr>
                <td class="tabla_informativa_subtitulo">TOTAL DSCTO. MORA</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $recaudacion->cronograma_moradescuento }}</td>
              </tr>
              @endif
              <tr>
                <td class="tabla_informativa_subtitulo">TOTAL PAGO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ number_format($recaudacion->cronograma_total, 2, '.', '') }}</td>
              </tr>
              @if($recaudacion->cronograma_total!=$recaudacion->cronograma_totalredondeado)
              <tr>
                <td class="tabla_informativa_subtitulo">TOTAL REDONDEADO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ number_format($recaudacion->cronograma_totalredondeado, 2, '.', '') }}</td>
              </tr>
              @endif
              @endif
              @if($prestamoahorro->idprestamo_tipoahorro==2)
              @if($recaudacion->cronograma_acuentaanterior>0)
              <tr>
                <td class="tabla_informativa_subtitulo">A CUENTA (ANTERIOR)</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $recaudacion->cronograma_acuentaanterior }}</td>
              </tr>
              @endif
              @if($recaudacion->cronograma_idtipopago==1)
              <tr>
                <td class="tabla_informativa_subtitulo">MONTO PAGADO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $recaudacion->cronograma_montorecibido }}</td>
              </tr>
              @elseif($recaudacion->cronograma_idtipopago==2)
              <tr>
                <td class="tabla_informativa_subtitulo">MONTO RECIBIDO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $recaudacion->cronograma_pagado }}</td>
              </tr>
              @endif
              @if($recaudacion->cronograma_vuelto>0)
              <tr>
                <td class="tabla_informativa_subtitulo">VUELTO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $recaudacion->cronograma_vuelto }}</td>
              </tr>
              @endif
              @if($recaudacion->cronograma_acuentaproxima>0)
              <tr>
                <td class="tabla_informativa_subtitulo">A CUENTA (PRÓXIMA)</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $recaudacion->cronograma_acuentaproxima }}</td>
              </tr>
              @endif
              @endif
          </table>
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