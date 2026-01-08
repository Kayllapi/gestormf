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
          </table>
          <div class="espacio"></div>
          <table class="tabla_informativa">
              <tr>
                <td colspan="3"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
              </tr>
              @if($recaudacion->monto_efectivo>0)
              <tr>
                <td class="tabla_informativa_subtitulo" style="width:90px;">TOTAL EFECTIVO</td>
                <td style="width:5px;">:</td>
                <td class="tabla_informativa_descripcion">{{ $recaudacion->monto_efectivo }}</td>
              </tr>
              @endif
              @if($recaudacion->monto_deposito>0)
              <tr>
                <td class="tabla_informativa_subtitulo" style="width:90px;">TOTAL DEPÓSITO</td>
                <td style="width:5px;">:</td>
                <td class="tabla_informativa_descripcion">{{ $recaudacion->monto_deposito }}</td>
              </tr>
              @endif
              <tr>
                <td colspan="3"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
              </tr>
          </table>
      </div>
    </body>
</html>