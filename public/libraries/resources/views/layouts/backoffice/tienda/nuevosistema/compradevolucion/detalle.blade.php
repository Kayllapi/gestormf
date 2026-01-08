<table class="tabla-detalle">
      <tr>
        <th colspan="3" style="background-color: #afaeae;">GENERAL</th>
      </tr>
      <tr>
        <td width="10%">COD. COMPRA</td>
        <td width="1px">:</td>
        <td>{{ str_pad($compradevolucion->codigo, 8, "0", STR_PAD_LEFT) }}</td>
      </tr>
      <tr>
        <td>PROVEEDOR</td>
        <td>:</td>
        <td>{{$compradevolucion->apellidosproveedor}},{{$compradevolucion->nombreproveedor}}</td>
      </tr>
      <tr>
        <td>FECHA DE REGISTRO</td>
        <td>:</td>
        <td>{{ date_format(date_create($compradevolucion->fecharegistro),"d/m/Y h:i:s A") }} </td>
      </tr>
      <tr>
        <td>TIPO DE ENTREGA</td>
        <td>:</td>
        <td>{{ $compradevolucion->comprobantenombre }} </td>
      </tr>
      <tr>
        <td>MOTIVO DEVOLUCION</td>
        <td>:</td>
        <td>{{ $compradevolucion->motivo }} </td>
      </tr>
      
</table>
 <table class="table" id="tabla-contenido-compradevolucion">
                   <thead class="thead-dark">
                      <tr>
                          <th>Descripción de Producto</th>
                          <th width="60px">Cantidad</th>
                          <th width="110px">P. Unitario</th>
                          <th width="110px">P. Total</th>
                      </tr>
                   </thead>
                   <tbody>
                      @foreach($compradevoluciondetalles as $value)
                      <tr style="background-color: #008cea;color: #fff;height: 40px;">
                            <td>{{$value->concepto}}</td>
                            <td>{{$value->cantidad}}</td>
                            <td>{{$value->preciounitario}}</td>
                            <td>{{$value->preciototal}}</td>       
                       </tr>
                       @endforeach 
                     <?php $total = 0  ?>
              <?php
                    $subtotal = $total+number_format($compradevolucion->total/1.18, 2, '.', '');
                    $igv      = $compradevolucion->total-$subtotal
              ?>
                     
                    </tbody>
                 
</table>
<table class="tabla-detalle">
                    <tr>
                        <td width="10%">SUB TOTAL </td>
                        <td width="1px">:</td>
                        <td>{{$subtotal}}</td>
                     </tr>
                      <tr>
                        <td>IGV</td>
                        <td>:</td>
                        <td>{{$igv}}</td>
                     </tr>
                      <tr>
                        <td>TOTAL</td>
                        <td>:</td>
                        <td>{{$compradevolucion->total}}</td>
                     </tr>
</table>
