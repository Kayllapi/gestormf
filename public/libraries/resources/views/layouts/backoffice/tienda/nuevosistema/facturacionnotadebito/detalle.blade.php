<div class="table-responsive">
  <table class="tabla-detalle">
    <tr>
        <th colspan="3" style="background-color: #afaeae;">GENERAL</th>
    </tr>
    <tr>
      <td>CLIENTE</td>
      <td width="1px">:</td>
      <td>{{$facturacionnotadebito->cliente_numerodocumento}} - {{$facturacionnotadebito->cliente_razonsocial}}</td>
    </tr>
    <tr>
      <td>DIRECCION</td>
      <td width="1px">:</td>
      <td>{{ $facturacionnotadebito->cliente_direccion }}</td>
    </tr>
    <tr>
      <td>UBIGEO</td>
      <td width="1px">:</td>
      <td>{{ $facturacionnotadebito->cliente_departamento}}/{{$facturacionnotadebito->cliente_provincia}}/{{$facturacionnotadebito->cliente_distrito }}</td>
    </tr>
    <tr>
      <td>AGENCIA</td>
      <td width="1px">:</td>
      <td>{{$facturacionnotadebito->emisor_ruc}}-{{$facturacionnotadebito->emisor_nombrecomercial}}</td>
    </tr>
    <tr>
      <td>MOTIVO</td>
      <td width="1px">:</td>
      <td>{{  $facturacionnotadebito->notadebito_descripcionmotivo }}</td>
    </tr>
    <tr>
      <td>MONEDA</td>
      <td width="1px">:</td>
      <td>{{  $facturacionnotadebito->notadebito_tipomoneda=='PEN' ? 'SOLES' : 'DOLARES' }}</td>
    </tr>
  
  </table>
  <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
            <tr>
                <th width="60px">Código</th>
                <th>Descripción de Producto</th>
                <th width="60px">Cantidad</th>
                <th width="110px">P. Unitario</th>
                <th width="110px">P. Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facturacionnotadebitodetalles as $value)
                <tr style="background-color: #a53b93;color: #fff;height: 40px;">
                    <td>{{$value->productocodigo}}</td>
                    <td>{{$value->productonombre}}</td>
                    <td>{{$value->cantidad}}</td>
                    <td>{{$value->montopreciounitario}}</td>
                    <td>{{ number_format($value->montopreciounitario*$value->cantidad, 2, '.', '') }}</td>  
                </tr>
            @endforeach 
        </tbody>
    </table>
  <table style="margin:auto; text-align:left;">
    <tr>
      <td>SUB TOTAL </td>
      <td width="1px">:</td>
      <td> {{ $facturacionnotadebito->notadebito_valorventa }}</td>
    </tr>
    <tr>
      <td>IGV </td>
      <td width="1px">:</td>
      <td> {{$facturacionnotadebito->notadebito_totalimpuestos}}</td>
    </tr>
    <tr>
      <td>TOTAL </td>
      <td width="1px">:</td>
      <td> {{$facturacionnotadebito->notadebito_totalimpuestos}}</td>
    </tr>
  </table>
</div>
</div>

      