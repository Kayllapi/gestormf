<!DOCTYPE html>
<html>
<head>
	<title>Ticket</title>
	<style>
		html, body {
			margin: 0px;
			padding: 15px;
			font-size: 12px;
      font-weight: bold;
      font-family: Courier;
		}
		.contenedor {
    width: <?php echo  $configuracion!=''?($configuracion->venta_anchoticket-1):'6.62' ?>cm;
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
			font-size: 15px;
		}
		.datocomprobante {
			text-align: left;
		}
		.datofinal {
			text-align: center;
		}
		.datodetalle {
			text-align: left;
		}
	</style>
</head>
<body>
<div class="contenedor">
  
    @if($agencia!='')
      <div class="nombrecomercial">{{ strtoupper($agencia->nombrecomercial) }}</div>
      RUC: {{ $agencia->ruc }}<br>
      {{ strtoupper($agencia->direccion) }}<br>
      {{ strtoupper($agencia->ubigeonombre) }}<br><br>
    @else
     <div class="nombrecomercial"> {{ strtoupper($tienda->nombre) }}</div>
      {{ strtoupper($tienda->direccion) }}<br><br>
    @endif
	<div class="datocomprobante">
    CODIGO: {{ str_pad($productotransferencia->codigo, 8, "0", STR_PAD_LEFT) }}<br>
   
    TIENDA ORIGEN: {{ $productotransferencia->tienda_origen_nombre }}<br>
    TIENDA DESTINO: {{ $productotransferencia->tienda_destino_nombre }}<br>
    RESPONSABLE ORIGEN: {{ $productotransferencia->idusersorigen!=0?''.$productotransferencia->user_origen_nombre.'':'' }}<br>
    RESPONSABLE DESTINO: {{ $productotransferencia->idusersdestino!=0?''.$productotransferencia->user_destino_nombre.'':'' }}<br>
    F. SOLICITUD: {{ date_format(date_create($productotransferencia->fechasolicitud),"d/m/Y h:i:s A") }}<br>
    F. ENVIO: {{ date_format(date_create($productotransferencia->fechaenvio),"d/m/Y h:i:s A") }}<br>
    F. RECEPCIÓN: {{ date_format(date_create($productotransferencia->fecharecepcion),"d/m/Y h:i:s A") }}<br>
   
	</div>
	<table class="table" style="margin-top:5px;">
		<thead>
			<tr>
				<td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
			<tr>
				<td style="white-space: nowrap;text-align: center;">CANT.</td>
				<td style="white-space: nowrap;text-align: center;width:60px;">CANT.ENV.</td>
				<td style="white-space: nowrap;text-align: right;width:30px;">CANT.RECEPCIÓN</td>
			</tr>
			<tr>
				<td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
		</thead>
		<tbody>
			@foreach($transferenciadetalle as $value)
			<tr>
				<td colspan="3"> {{ $value->productonombre }}</td>
			</tr>
			<tr>
				<td style="white-space: nowrap;text-align: center;">{{ $value->cantidad }}</td>
				<td style="white-space: nowrap;text-align: center;">{{ $value->cantidadenviado }}</td>
				<td style="white-space: nowrap;text-align: right;">{{ $value->cantidadrecepcion }}</td>
			</tr>
			@endforeach
<!-- 			<tr>
				<td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr> -->
<!--    
   
			<tr>
				<td colspan="3" style="text-align: center;"</td>
			</tr> -->
		</tbody>
	</table>
<!-- 	<div class="datofinal">
	¡GRACIAS POR SU COMPRA!
	</div> -->
</div>
</body>
</html>