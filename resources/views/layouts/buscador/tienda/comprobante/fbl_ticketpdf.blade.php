<!DOCTYPE html>
<html>
<head>
	<title>Comprobante</title>
	<style>
		html, body {
			margin: 0px;
			padding: 15px;
			font-size: 11px;
      font-weight: bold;
      font-family: Courier;
		}
		.contenedor {
			width: <?php echo  $configuracion['anchoticket']!=null?($configuracion['anchoticket']-1):'6.62' ?>cm;
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
            width: 50px;
            height: 50px;
        }
	</style>
</head>
<body>
  <div class="contenedor">
    <div class="nombrecomercial">
      @if($facturacionboletafactura->agencialogo!='')
          <img class="logo" src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$facturacionboletafactura->agencialogo) }}" height=50px><br>
      @endif
      {{ strtoupper($facturacionboletafactura->emisor_nombrecomercial) }}
      <br>
    </div>
      RUC: {{ $facturacionboletafactura->emisor_ruc }}<br>
           {{ strtoupper($facturacionboletafactura->emisor_direccion) }}<br>
           {{ strtoupper($facturacionboletafactura->emisor_departamento.'/'.$facturacionboletafactura->emisor_provincia.'/'.$facturacionboletafactura->emisor_distrito) }}<br><br>
    
      <div class="datocomprobante">   
        @if($facturacionboletafactura->venta_tipodocumento==3)
           BOLETA: {{$facturacionboletafactura->venta_serie}}-{{ str_pad($facturacionboletafactura->venta_correlativo, 8, "0", STR_PAD_LEFT) }}
        @elseif($facturacionboletafactura->venta_tipodocumento==1)
           FACTURA: {{$facturacionboletafactura->venta_serie}}-{{ str_pad($facturacionboletafactura->venta_correlativo, 8, "0", STR_PAD_LEFT) }}
        @endif<br>
        EMISIÓN: {{ date_format(date_create($facturacionboletafactura->venta_fechaemision),"d/m/Y h:i:s A") }}<br>
        @if($facturacionboletafactura->venta_tipomoneda=='PEN')
        MONEDA: SOLES<br>
        @else($facturacionboletafactura->venta_tipomoneda=='USD')
        MONEDA: DOLARES<br>
        @endif
        CLIENTE: {{ strtoupper($facturacionboletafactura->cliente_razonsocial) }}<br>
        DNI/RUC: {{ $facturacionboletafactura->cliente_numerodocumento }}<br>
        DIRECCIÓN: {{ strtoupper($facturacionboletafactura->cliente_direccion) }}<br>
        {{ strtoupper($facturacionboletafactura->cliente_departamento.'/'.$facturacionboletafactura->cliente_provincia.'/'.$facturacionboletafactura->cliente_distrito) }}<br>
        RESPONSABLE: {{ strtoupper($facturacionboletafactura->responsablenombre) }}<br>
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
				<td colspan="2" style="text-align: right;">SUB TOTAL:</td>
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
    @if($respuesta['facturacionrespuesta']!='')
	<div class="qr">
    <img src="<?php echo $respuesta['facturacionrespuesta']->qr ?>" width="100px"><br>
	</div>
	<div class="autorizacion">
      Representación impresa del comprobante electrónico, esta puede ser consultada en: <br><b>kayllapi.com/{{$tienda->link}}/comprobante</b><br><br>
  </div>
  @endif

	<div class="datofinal">
	¡GRACIAS POR SU COMPRA!
	</div>
</div>
</body>
</html>

