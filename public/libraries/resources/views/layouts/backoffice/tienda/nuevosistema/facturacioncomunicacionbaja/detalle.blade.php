<div class="table-responsive">
  <table class="tabla-detalle">
    <tr>
        <th colspan="3" style="background-color: #afaeae;">GENERAL</th>
    </tr>
    <tr>
      <td>CLIENTE</td>
      <td width="1px">:</td>
      <td>{{$facturacionnotacredito->cliente_numerodocumento}} - {{$facturacionnotacredito->cliente_razonsocial}}</td>
    </tr>
    <tr>
      <td>DIRECCION</td>
      <td width="1px">:</td>
      <td>{{ $facturacionnotacredito->cliente_direccion }}</td>
    </tr>
    <tr>
      <td>UBIGEO</td>
      <td width="1px">:</td>
      <td>{{ $facturacionnotacredito->cliente_departamento}}/{{$facturacionnotacredito->cliente_provincia}}/{{$facturacionnotacredito->cliente_distrito }}</td>
    </tr>
    <tr>
      <td>AGENCIA</td>
      <td width="1px">:</td>
      <td>{{$facturacionnotacredito->emisor_ruc}}-{{$facturacionnotacredito->emisor_nombrecomercial}}</td>
    </tr>
    <tr>
      <td>MOTIVO</td>
      <td width="1px">:</td>
      <td>{{  $facturacionnotacredito->notacredito_descripcionmotivo }}</td>
    </tr>
    <tr>
      <td>MONEDA</td>
      <td width="1px">:</td>
      <td>{{  $facturacionnotacredito->notacredito_tipomoneda=='PEN' ? 'SOLES' : 'DOLARES' }}</td>
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
            @foreach($facturacionnotacreditodetalles as $value)
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
      <td> {{ $facturacionnotacredito->notacredito_valorventa }}</td>
    </tr>
    <tr>
      <td>IGV </td>
      <td width="1px">:</td>
      <td> {{$facturacionnotacredito->notacredito_totalimpuestos}}</td>
    </tr>
    <tr>
      <td>TOTAL </td>
      <td width="1px">:</td>
      <td> {{$facturacionnotacredito->notacredito_montoimpuestoventa}}</td>
    </tr>
  </table>
</div>
</div>

<?php
    if ($facturacioncomunicacionbaja->idfacturacionboletafactura != 0) {

        $facturacionboletafacturadetalles = DB::table('s_facturacionboletafacturadetalle')
        ->whereId($facturacioncomunicacionbaja->idfacturacionboletafactura)
        ->get();
      
    }elseif($facturacioncomunicacionbaja->idfacturacionnotacredito != 2) {
      
        $facturacionboletafacturadetalles = DB::table('s_facturacionnotacreditodetalle')
        ->whereId($facturacioncomunicacionbaja->idfacturacionnotacredito)
        ->get();
      
    }
?>

 <div class="profile-edit-container">
     <div class="custom-form">
         <div class="row">
            <div class="col-md-6"> 
              <label>Cliente *</label>
                  <input type="text" id="cliente"  value="{{ $facturacioncomunicacionbaja->cliente_numerodocumento }} - {{ $facturacioncomunicacionbaja->cliente_razonsocial }}" disabled>
              <label>Comprobante *</label>
                  <input type="text" id="tipodocumento" value="FACTURA"  disabled> 
              <label>Descripcion de Motivo</label>
                  <input type="text" value="{{ $facturacioncomunicacionbaja->descripcionmotivobaja }}" disabled>
              <label>Correlativo</label>
                  <input  type="text"  value="{{ $facturacioncomunicacionbaja->comunicacionbaja_correlativo }}" disabled/>
           </div>
           <div class="col-md-6"> 
              <label>Agencia *</label>
                  <input type="text" value="{{ $facturacioncomunicacionbaja->emisor_ruc }} - {{ $facturacioncomunicacionbaja->emisor_razonsocial }}" disabled>
              <label>Moneda</label>
             @if($facturacioncomunicacionbaja->venta_tipomoneda=="PEN")
                  <input type="text" value="SOLES"  disabled>
             @elseif($facturacioncomunicacionbaja->venta_tipomoneda=="USD")
                 <input type="text" value="DOLARES" disabled>
             @endif
              <label>Fecha Emisión</label>
                  <input type="text" id="fechaemision"  value="{{ $facturacioncomunicacionbaja->venta_fechaemision }}" disabled>
              <label>Serie - Correlativo*</label>
                  <input type="text" id="seriecorrelativo"  value="{{ $facturacioncomunicacionbaja->serie }} - {{ $facturacioncomunicacionbaja->correlativo }}" disabled>  
            </div>
         </div>
         <div class="table-responsive">
                <table class="table" id="tabla-contenido">
                    <thead class="thead-dark">
                        <tr>
                            <th width="15%">Código</th>
                            <th>Producto</th>
                            <th width="60px">Cantidad</th>
                            <th width="110px">P. Unitario</th>
                            <th width="110px">P. Total</th> 
                            <th width="10px"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($facturacionboletafacturadetalles as $value)
                         <tr style="background-color: #a53b93;color: #fff;height: 40px;">
                            <td>{{ $value->codigoproducto }}</td>
                            <td>{{ $value->descripcion }}</td>
                            <td>{{ $value->cantidad }}</td>
                            <td>{{ $value->montopreciounitario }}</td>
                            <td>{{ number_format($value->montopreciounitario * $value->cantidad, 2, '.',  '') }} </td>  
                            <td></td>
                         </tr>
                     @endforeach
                     </tbody>
                </table>
         </div>
       <div class="row">
          <div class="col-md-4"></div>
          <div class="col-md-4"> 
              <label>Total</label>
                 <input class="form-control" type="text" id="totalventa" placeholder="0.00" disabled>
              </div>
          <div class="col-md-4"></div>
         </div> 
   </div>
</div>          
