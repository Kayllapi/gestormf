<?php $color = '#409dc9';?>
<style>
  .table-email {
    text-align: left;
    font-family: -webkit-pictograph;
  }
  .line-divider {
    margin-bottom: 15px;
    margin-top: 10px;
    width: 100%;
    height: 2px;
    background-color: {{ $color }};
  }
  
/* TITULOS */
  h2 {
    color: {{ $color }};
    font-weight: bold;
    font-size: 20px;
  }
  
/* BOTON  */
  .button {
    display: inline-block;
    border-radius: 15px;
    background-color: {{ $color }};
    border: none;
    color: #ffffff;
    text-align: center;
    font-size: 24px;
    padding: 20px;
    width: 100%;
    transition: all 0.5s;
    cursor: pointer;
    margin: 5px;
    box-shadow: 0 8px 10px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
  }

  .button span {
    cursor: pointer;
    display: inline-block;
    position: relative;
    transition: 0.5s;
  }

  .button span:after {
    content: '\00bb';
    position: absolute;
    opacity: 0;
    top: 0;
    right: -20px;
    transition: 0.5s;
  }

  .button:hover span {
    padding-right: 25px;
  }

  .button:hover span:after {
    opacity: 1;
    right: 0;
  }
</style>

<?php
  $documento = '';
  $serie_comprobante = $facturacionboletafactura->venta_serie.' - '.str_pad($facturacionboletafactura->id, 8, "0", STR_PAD_LEFT);
  $fecha_emision = date_format(date_create($facturacionboletafactura->venta_fechaemision),"d/m/Y h:i:s A");
  $cliente = strtoupper($facturacionboletafactura->cliente_razonsocial);
  $subtotal = $facturacionboletafactura->venta_valorventa;
  $igv = $facturacionboletafactura->venta_totalimpuestos;
  $total = $facturacionboletafactura->venta_montoimpuestoventa;
  $link = "https://llama.pe/see_invoice/c7acd25d-eca0-4602-ad31-bdac724fac4e";
  $agencia = strtoupper($facturacionboletafactura->emisor_nombrecomercial);
  $agencia_ruc = $facturacionboletafactura->emisor_ruc;
  $agencia_email = '';
  $agencia_telefono = '';
  $agencia_direccion = strtoupper($facturacionboletafactura->emisor_direccion);
  $agencia_ubigeo = strtoupper($facturacionboletafactura->emisor_departamento.'/'.$facturacionboletafactura->emisor_provincia.'/'.$facturacionboletafactura->emisor_distrito);
  $agencia_horario = '';
  $agencia_web = '';

  if ($facturacionboletafactura->venta_tipodocumento == 3) {
    $documento = 'BOLETA ELECTRÓNICA';
  }
  elseif ($facturacionboletafactura->venta_tipodocumento == 1) {
    $documento = 'FACTURA ELECTRÓNICA';
  }
?>
<table width="100%" class="table-email">
  <thead>
    <tr>
      <th>
        <h2>{{ $documento }} {{ $serie_comprobante }}</h2>
        <div class="line-divider"></div>
      </th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>
        <div class="container-cuerpo">
          <span>Hola {{ $cliente }},</span> <br>
          <span>Adjuntamos en este email una {{ $documento }}</span> <br>
          <span><b>El comprobante ya fue pagada.</b></span> <br>
          <ul>
            <li>{{ $documento }} {{ $serie_comprobante }}</li>
            <li><b>Fecha de emisión:</b> {{ $$fecha_emision }}</li>
            <li><b>Subtotal:</b> {{ $subtotal }}</li>
            <li><b>Igv:</b> {{ $igv }}</li>
            <li><b>Total:</b> {{ $total }}</li>
          </ul>
          <span>Generado automáticamente a partir del PEDIDO N°PD211298478. y ORDEN DE PAGO N°108590</span> <br>
          <button class="button" style="vertical-align:middle"><span>Ver Factura Electrónica </span></button>
          <span>Si el link no funciona, usa el siguiente enlace en tu navegador: <a href="{{ $link }}">{{ $link }}</a></span> <br>
          <span>También se adjunta en XML, PDF A4 y Ticket en el mismo documento. Que puede ser impresa y usada como una Factura emitida de manera tradicional.</span> <br>
        </div>
        <div class="container-despedida">
          <span>Saludos,</span> <br>
          <span>Equipo de {{ $agencia }}</span> <br>
        </div>
        <div class="line-divider"></div>
        <div class="container-footer">
          @if($facturacionboletafactura->agencialogo != '')
              <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$facturacionboletafactura->agencialogo) }}" height=50px><br>
          @endif
          <span>{{ $agencia }}</span> <br>
          <span>Entidad de Certificación acreditado en INDECOPI</span> <br>
          <span>RUC {{ $agencia_ruc }}</span> <br>
          <span>Email: <a href="javascript:;">{{ $agencia_email }}</a> | Teléfono: {{ $agencia_telefono }} | Móvil o WhatsApp: {{ $agencia_telefono }}</span> <br>
          <span>{{ $agencia_direccion }}</span> <br>
          <span>{{ $agencia_ubigeo }}</span> <br>
          <span>{{ $agencia_horario }}</span> <br>
          <span><a href="javascript:;">{{ $agencia_web }}</a></span> <br>
          <span>Genera confianza y ganarás antes de haber empezado</span>
        </div>
      </td>
    </tr>
  </tbody>
</table>
