<div class="table-responsive">
  <table class="tabla-detalle">
    <tr>
        <th colspan="3" style="background-color: #afaeae;">GENERAL</th>
    </tr>
    <tr>
      <td>CLIENTE</td>
      <td width="1px">:</td>
      <td>{{ strtoupper($facturacionboletafactura->cliente) }}</td>
    </tr>
    <tr>
      <td>DIRECCION</td>
      <td width="1px">:</td>
      <td>{{ strtoupper($facturacionboletafactura->cliente_direccion) }}</td>
    </tr>
    <tr>
      <td>UBIGEO</td>
      <td width="1px">:</td>
      <td>{{ strtoupper($facturacionboletafactura->ubigeo) }}</td>
    </tr>
    <tr>
      <td>AGENCIA</td>
      <td width="1px">:</td>
      <td>{{ strtoupper($facturacionboletafactura->agencia) }}</td>
    </tr>
    <tr>
      <td>MONEDA</td>
      <td width="1px">:</td>
      <td>{{$facturacionboletafactura->venta_tipomoneda=='PEN' ? 'SOLES' : 'DOLARES'}}</td>
    </tr>
    <tr>
      <td>COMPROBANTE</td>
      <td width="1px">:</td>
       @if($facturacionboletafactura->venta_tipodocumento=='03')
          <td>BOLETA</td>
       @elseif($facturacionboletafactura->venta_tipodocumento=='01')
           <td>FACTURA</td>
        @elseif($facturacionboletafactura->venta_tipodocumento=='00')
           <td>TICKET</td>
       @endif
      <td></td>
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
            <?php $total = 0 ?>
            @foreach($boletafacturadetalle as $value)
                <?php $total = $total+number_format($value->cantidad*$value->montopreciounitario, 2, '.', '') ?>
                <tr style="background-color: #a53b93;color: #fff;height: 40px;">
                    <td>{{$value->productocodigo}}</td>
                    <td>{{$value->productonombre}}</td>
                    <td>{{$value->cantidad}}</td>
                    <td>{{$value->montopreciounitario}}</td>
                    <td>{{number_format($value->cantidad*$value->montopreciounitario, 2, '.', '') }}</td>  
                </tr>
            @endforeach 
        </tbody>
    </table>
  <table style="margin:auto; text-align:left;">
    <tr>
      <td>SUB TOTAL </td>
      <td width="1px">:</td>
      <td> {{$facturacionboletafactura->venta_valorventa}}</td>
    </tr>
    <tr>
      <td>IGV </td>
      <td width="1px">:</td>
      <td> {{$facturacionboletafactura->venta_totalimpuestos}}</td>
    </tr>
    <tr>
      <td>TOTAL </td>
      <td width="1px">:</td>
      <td> {{$facturacionboletafactura->venta_montoimpuestoventa}}</td>
    </tr>
  </table>
</div>
