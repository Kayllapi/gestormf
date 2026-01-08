<!DOCTYPE html>
<html>
<head>
	<title>Comprobante</title>
	<style>
		html, body {
          margin: 0px;
          padding: 0px;
          font-size: 10px;
          font-weight: bold;
      font-family: <?php echo  configuracion($tienda->id,'facturacion_tipoletra')['resultado']=='CORRECTO'?configuracion($tienda->id,'facturacion_tipoletra')['valor']:'Courier' ?>,sans-serif;
		}
		.contenedor {
			padding: 15px;
			width: <?php echo  configuracion($tienda->id,'facturacion_anchoticket')['resultado']=='CORRECTO'?(configuracion($tienda->id,'facturacion_anchoticket')['valor']-1):'7' ?>cm;
			text-align: center;
      /*background-color:red;*/
		}
		.table {
			width: 100%;
      margin:0px;
      padding:0px;
			font-size: 11px;
		}
		.nombrecomercial {
			font-size: 14px;
		}
		.datocomprobante {
			text-align: left;
		}
		.datofinal {
			text-align: center;
		}
		.qr {
			  text-align: center;
        margin-top:5px;
        margin-bottom:10px;
		}
    .autorizacion{
        text-align:center;
        font-size:9px;
    }
    .logo {
            height: 60px;
        }
	</style>
</head>
<body>
  <div class="contenedor">
    <div class="nombrecomercial">
      @if($facturacionboletafactura->agencialogo!='')
          <img class="logo" src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$facturacionboletafactura->agencialogo) }}"><br>
      @endif
      {{ strtoupper($facturacionboletafactura->emisor_nombrecomercial) }}
      <br>
    </div>
      RUC: {{ $facturacionboletafactura->emisor_ruc }}<br>
           {{ strtoupper($facturacionboletafactura->emisor_direccion) }}<br>
           {{ strtoupper($facturacionboletafactura->emisor_distrito.' - '.$facturacionboletafactura->emisor_provincia.' - '.$facturacionboletafactura->emisor_departamento) }}<br><br>
    
      <div class="datocomprobante">   
        @if($facturacionboletafactura->venta_tipodocumento==3)
           BOLETA ELECTRÓNICA: {{$facturacionboletafactura->venta_serie}}-{{ str_pad($facturacionboletafactura->venta_correlativo, 6, "0", STR_PAD_LEFT) }}
        @elseif($facturacionboletafactura->venta_tipodocumento==1)
           FACTURA ELECTRÓNICA: {{$facturacionboletafactura->venta_serie}}-{{ str_pad($facturacionboletafactura->venta_correlativo, 6, "0", STR_PAD_LEFT) }}
        @endif<br>
        FECHA DE EMISIÓN: {{ date_format(date_create($facturacionboletafactura->venta_fechaemision),"d/m/Y h:i:s A") }}<br>
        @if($facturacionboletafactura->venta_tipomoneda=='PEN')
        MONEDA: SOLES<br>
        
        @if($comida_venta!='')
        NRO MESA: {{ strtoupper($comida_venta->numeromesa) }}<br>
        @endif
        
        @else($facturacionboletafactura->venta_tipomoneda=='USD')
        MONEDA: DOLARES<br>
        @endif
        CLIENTE: {{ strtoupper($facturacionboletafactura->cliente_razonsocial) }}<br>
        DNI/RUC: {{ $facturacionboletafactura->cliente_numerodocumento }}<br>
        DIRECCIÓN: {{ strtoupper($facturacionboletafactura->cliente_direccion) }}<br>
        {{ strtoupper($facturacionboletafactura->cliente_distrito.' - '.$facturacionboletafactura->cliente_provincia.' - '.$facturacionboletafactura->cliente_departamento) }}<br>
        
        @if($comida_venta!='')
        MESERO: {{ strtoupper($comida_venta->mesero_nombre) }}<br>
        @endif
        VENTANILLA: {{ strtoupper($facturacionboletafactura->responsablenombre) }}<br>
    </div>
   	<table class="table" style="margin-top:5px;">
		<thead>
			<tr>
				<td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
			<tr>
        <td style="white-space: nowrap;text-align: center;">CANT</td>
				<td style="white-space: nowrap;text-align: center;width:60px;">P.UNIT.</td>
				<td style="white-space: nowrap;text-align: right;width:30px;">TOTAL</td>
			</tr>
			<tr>
				<td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
		</thead>
		<tbody>
			@foreach($boletafacturadetalle as $value)
			<tr>
				<td colspan="3">{{ strtoupper($value->descripcion) }}</td>
			</tr>
			<tr>
				<td style="white-space: nowrap;text-align: center;">{{ $value->cantidad }}</td>
				<td style="white-space: nowrap;text-align: center;">{{ $value->montopreciounitario }}</td>
				<td style="white-space: nowrap;text-align: right;">{{number_format($value->cantidad*$value->montopreciounitario, 2, '.', '') }}</td>
			</tr>
			@endforeach
			<tr>
				<td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
     <tr>
				<td colspan="2" style="text-align: right;">OP. GRAV:</td>
				<td style="white-space: nowrap;text-align: right;">
          {{ $facturacionboletafactura->venta_valorventa }}
        </td>
			</tr>
      <tr>
				<td colspan="2" style="text-align: right;">IGV:</td>
				<td style="white-space: nowrap;text-align: right;">
          {{ $facturacionboletafactura->venta_totalimpuestos }}
        </td>
			</tr>
      <tr>
				<td colspan="2" style="text-align: right;">TOTAL:</td>
				<td style="white-space: nowrap;text-align: right;">
          {{ $facturacionboletafactura->venta_montoimpuestoventa }}
        </td>
			</tr>
			<tr>
				<td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
			<tr>
				<td colspan="3" style="text-align: center;height:5px;">{{ $facturacionboletafactura->leyenda_value }}</td>
			</tr>
			<tr>
				<td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
		</tbody>
	</table>
	<div class="qr">
    <img src="<?php echo $facturacionboletafactura->respuestaqr ?>" width="100px"><br>
	</div>
	<div class="autorizacion">
    Autorizado mediante Resolución de Intendencia Nº 034-005-0005315.<br>
      Representación impresa del comprobante electrónico, esta puede ser consultada en: <br><b>kayllapi.com/{{$tienda->link}}/comprobante</b><br><br>
  </div>

	<div class="datofinal">
	¡GRACIAS POR SU COMPRA, QUE DIOS LE BENDIGA!
	</div>
</div>
</body>
</html>

