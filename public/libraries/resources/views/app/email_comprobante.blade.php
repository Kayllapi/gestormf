<?php
  $color = $tienda->ecommerce_color != '' ? $tienda->ecommerce_color : '#409dc9';
  $documento = '';
  $serie_comprobante = $facturacionboletafactura->venta_serie.' - '.str_pad($facturacionboletafactura->venta_correlativo, 8, "0", STR_PAD_LEFT);
  $fecha_emision = date_format(date_create($facturacionboletafactura->venta_fechaemision),"d/m/Y h:i:s A");
  $cliente = strtoupper($facturacionboletafactura->cliente_razonsocial);
  $subtotal = $facturacionboletafactura->venta_valorventa.' '.$facturacionboletafactura->venta_tipomoneda;
  $igv = $facturacionboletafactura->venta_totalimpuestos.' '.$facturacionboletafactura->venta_tipomoneda;
  $total = $facturacionboletafactura->venta_montoimpuestoventa.' '.$facturacionboletafactura->venta_tipomoneda;
  $link = url($tienda->link.'/pagina/comprobante');
  $link_data = url($tienda->link.'/pagina/comprobante?numerodocumento='.
                   $facturacionboletafactura->cliente_numerodocumento.'&tipodocumento='.
                   $facturacionboletafactura->venta_tipodocumento.'&serie='.
                   $facturacionboletafactura->venta_serie.'&correlativo='.
                   $facturacionboletafactura->venta_correlativo.'&fechaemision='.
                   date_format(date_create($facturacionboletafactura->venta_fechaemision),"Y-m-d"));
  $agencia = strtoupper($facturacionboletafactura->emisor_nombrecomercial);
  $agencia_ruc = $facturacionboletafactura->emisor_ruc;
  $agencia_email = $tienda->correo;
  $agencia_telefono = $tienda->numerotelefono;
  $agencia_direccion = strtoupper($facturacionboletafactura->emisor_direccion);
  $agencia_ubigeo = strtoupper($facturacionboletafactura->emisor_departamento.'/'.$facturacionboletafactura->emisor_provincia.'/'.$facturacionboletafactura->emisor_distrito);
  $agencia_web = url($tienda->link);

  $nro_pedido = $facturacionboletafactura->venta_tipooperacion;

  if ($facturacionboletafactura->venta_tipodocumento == 3) {
    $documento = 'BOLETA ELECTRÓNICA';
  }
  elseif ($facturacionboletafactura->venta_tipodocumento == 1) {
    $documento = 'FACTURA ELECTRÓNICA';
  }
?>
<table width="100%" style="
    text-align: left;
    font-family: -webkit-pictograph;">
  <thead>
    <tr>
      <th>
        <h2 style="color: {{ $color }}; font-weight: bold; font-size: 20px;">{{ $documento }} {{ $serie_comprobante }}</h2>
        <div class="line-divider" style="margin-bottom: 15px; margin-top: 10px; width: 100%; height: 2px; background-color: {{ $color }};"></div>
      </th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>
        <div class="container-cuerpo">
          <span>Hola {{ $cliente }}</span> <br>
          <span>Adjuntamos en este email una {{ $documento }}</span> <br>
          <span><b>El comprobante ya fue pagada.</b></span> <br>
          <ul>
            <li><b>{{ $documento }}</b> {{ $serie_comprobante }}</li>
            <li><b>Fecha de emisión:</b> {{ $fecha_emision }}</li>
            <li><b>Subtotal:</b> {{ $subtotal }}</li>
            <li><b>Igv:</b> {{ $igv }}</li>
            <li><b>Total:</b> {{ $total }}</li>
          </ul>
          <span>
            @if ($nro_pedido != '')
            Generado automáticamente a partir del PEDIDO N° {{ $nro_pedido }}.
            @endif
          </span> <br>
          <a href="{{ $link_data }}" style=" display: inline-block;
                    border-radius: 15px;
                    background-color: {{ $color }};
                    border: none;
                    color: #ffffff;
                    text-align: center;
                    font-size: 20px;
                    padding: 10px;
                    width: 50%;
                    cursor: pointer;
                    margin: 5px;
                    text-decoration: none;">Ver Factura Electrónica</a> <br>
          <span>Si el link no funciona, usa el siguiente enlace en tu navegador: {{ strval($link_data) }}</span> <br>
          <span>También se adjunta el XML, PDF A4 y Ticket en el mismo documento. Que puede ser impresa y usada como una Factura emitida de manera tradicional.</span> <br>
        </div>
        <br>
        <div class="container-despedida">
          <span>Saludos,</span> <br>
          <span>Equipo {{ $agencia }}</span> <br>
        </div>
        <div class="line-divider" style="margin-bottom: 15px; margin-top: 10px; width: 100%; height: 2px; background-color: {{ $color }};"></div>
        <div id="container-footer">
          @if($facturacionboletafactura->agencialogo != '')
              <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$facturacionboletafactura->agencialogo) }}" height=50px><br>
          @endif
          <span style="color: #474545;">{{ $agencia }}</span> <br>
          <span style="color: #474545;"><b>RUC:</b> {{ $agencia_ruc }}</span> <br>
          @if ($agencia_email != '')
          <span style="color: #474545;">
            <b>Correo Electrónico: </b>{{ strval($agencia_email) }}
          </span> <br>
          @endif
          @if ($agencia_telefono != '')
          <span style="color: #474545;">
            <b>Teléfono: </b>{{ $agencia_telefono }}
          </span> <br>
          @endif
          <span style="color: #474545;"><b>Dirección: </b>{{ $agencia_direccion }}</span> <br>
          <span style="color: #474545;"><b>Ubigeo: </b>{{ $agencia_ubigeo }}</span> <br>
          <span style="color: #474545;"><b>Tienda Virtual: </b><a href="{{ $agencia_web }}" style="color: #474545;">{{ $agencia_web }}</a></span> <br>
          <span style="color: #474545;">Genera confianza y ganarás antes de haber empezado</span>
        </div>
      </td>
    </tr>
  </tbody>
</table>