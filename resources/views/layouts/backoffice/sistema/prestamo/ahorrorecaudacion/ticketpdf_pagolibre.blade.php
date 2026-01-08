<!DOCTYPE html>
<html>
<head>
    <title>TICKET DE PAGO LIBRE</title>
    @include('app.pdf_style',['idtienda'=>$tienda->id])
</head>
<body>
    <div class="ticket_contenedor">
      <div class="contenedor">
          @include('app.pdf_headerticket',[
              'logo'=>$tienda->imagen,
              'nombrecomercial'=>$tienda->nombre,
              'direccion'=>$tienda->direccion,
              'ubigeo'=>$tienda->ubigeonombre,
              'tienda'=>$tienda,
          ])
          <table class="tabla_informativa">
              <tr>
                <td class="tabla_informativa_subtitulo">FECHA</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ date_format(date_create($prestamo_ahorrorecaudacionlibre->fechaconfirmado), "d/m/Y h:i A") }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo" style="width:80px;">CÓDIGO</td>
                <td class="tabla_informativa_punto" style="width:5px;">:</td>
                <td class="tabla_informativa_descripcion">{{ str_pad($prestamo_ahorrorecaudacionlibre->codigo, 8, "0", STR_PAD_LEFT) }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">COD. RECAUDACIÓN</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ str_pad($prestamo_ahorrorecaudacionlibre->ahorrocodigo, 8, "0", STR_PAD_LEFT) }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">DNI</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $prestamo_ahorrorecaudacionlibre->cliente_identificacion }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">CLIENTE</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $prestamo_ahorrorecaudacionlibre->cliente }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">VENTANILLA</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $prestamo_ahorrorecaudacionlibre->cajero_apellidos }}, {{ $prestamo_ahorrorecaudacionlibre->cajero_nombre }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">MONEDA</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $prestamo_ahorrorecaudacionlibre->monedanombre }}</td>
              </tr>
          </table>
          <div class="espacio"></div>
          <table class="tabla_informativa">
              <tr>
                <td colspan="3"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
              </tr>
              @if($prestamo_ahorrorecaudacionlibre->monto_efectivo>0)
              <tr>
                <td class="tabla_informativa_subtitulo" style="width:80px;">EFECTIVO</td>
                <td style="width:5px;">:</td>
                <td class="tabla_informativa_descripcion">{{ $prestamo_ahorrorecaudacionlibre->monto_efectivo }}</td>
              </tr>
              @endif
              @if($prestamo_ahorrorecaudacionlibre->monto_deposito>0)
              <tr>
                <td class="tabla_informativa_subtitulo" style="width:80px;">DEPÓSITO</td>
                <td style="width:5px;">:</td>
                <td class="tabla_informativa_descripcion">{{ $prestamo_ahorrorecaudacionlibre->monto_deposito }}</td>
              </tr>
              @endif
              <tr>
                <td colspan="3"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
              </tr>
              @if($prestamo_ahorrorecaudacionlibre->monto_efectivo>0 && $prestamo_ahorrorecaudacionlibre->monto_deposito>0)
              <tr>
                <td class="tabla_informativa_subtitulo" style="width:80px;">TOTAL</td>
                <td style="width:5px;">:</td>
                <td class="tabla_informativa_descripcion">{{ number_format($prestamo_ahorrorecaudacionlibre->monto_efectivo+$prestamo_ahorrorecaudacionlibre->monto_deposito, 2, '.', '') }}</td>
              </tr>
              @endif
          </table>
      </div>
    </body>
</html>