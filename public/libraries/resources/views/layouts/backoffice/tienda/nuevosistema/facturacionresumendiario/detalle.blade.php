<div class="table-responsive">
  <table class="tabla-detalle">
    <tr>
        <th colspan="3" style="background-color: #afaeae;">GENERAL</th>
    </tr>
    <tr>
      <td>AGENCIA</td>
      <td width="1px">:</td>
      <td>{{$facturacionresumen->emisor_ruc}}-{{$facturacionresumen->emisor_nombrecomercial}}</td>
    </tr>
    <tr>
      <td>CORRELATIVO</td>
      <td width="1px">:</td>
      <td>{{  $facturacionresumen->resumen_correlativo }}</td>
    </tr>
   
  
  </table>
  <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
            <tr>
                <th width="15%">Serie</th> 
                <th>Documento-Cliente</th>
                <th width="60px">OP. Gravadas</th>
                <th width="110px">P. Unitario</th>
                <th width="110px">P. Total</th> 
                <th width="10px"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($facturacionresumendetalle as $value)
                 <?php
                $facturacion = DB::table('s_facturacionboletafactura')->where('s_facturacionboletafactura.id', $value->idfacturacionboletafactura)->first();
             ?>
                  <tr style="background-color: #a53b93;color: #fff;height: 40px;">
                        <td>{{ $value->serienumero }}</td>
                        <td>{{ $facturacion->cliente_numerodocumento }} - {{ $facturacion->cliente_razonsocial }}</td>
                        <td>{{ $value->operacionesgravadas }}</td>
                        <td>{{ $value->montoigv }}</td>
                        <td>{{ $value->total }}</td>
                        <td></td>
                 </tr>
            @endforeach 
        </tbody>
    </table>
 
</div>

 