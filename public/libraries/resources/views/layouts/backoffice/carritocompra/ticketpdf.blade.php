<!DOCTYPE html>
<html>
<head>
	<title>Ticket</title>
	<style>
		html, body {
			margin: 0px;
			padding: 10px;
			font-size: 10px;
		}
		.contenedor {
			width: 250px;
			text-align: center;
		}
		table {
			width: 100%;
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
	</style>
</head>
<body>
<div class="contenedor">
	<code>
  @if($agencia!='')
	<div class="nombrecomercial">{{ $agencia->nombrecomercial }}</div>
	RUC: {{ $agencia->ruc }}<br>
	{{ $agencia->direccion }}<br><br>
  @else
	<div class="nombrecomercial">{{ $venta->tiendanombre }}</div>
	{{ $venta->tiendadireccion }}<br><br>
  @endif
	<div class="datocomprobante">
	{{ strtoupper($venta->comprobantenombre) }} - {{ str_pad($venta->codigo, 8, "0", STR_PAD_LEFT) }}<br>	
	FECHA: {{ $venta->fechaconfirmacion }}<br>
	CLIENTE: {{ $cliente->nombre }}<br>
	@if($cliente->identificacion!='')
	DNI/RUC: {{ $cliente->identificacion }}<br>
	@endif
	@if($cliente->direccion!='')
	DIRECCIÓN: {{ $cliente->direccion }} {{ $cliente->ubigeonombre }}<br>
	@endif
	VENDEDOR: {{ $vendedor->nombre }}<br><br>
	</div>
	<table>
		<thead>
			<tr>
				<td colspan="4" style="text-align: center;">---------------------------------------</td>
			</tr>
			<tr>
				<td style="white-space: nowrap;text-align: center;">Cant.</td>
				<td width="100%" style="white-space: nowrap;text-align: center;">Producto</td>
				<td style="white-space: nowrap;text-align: center;">P.Unit.</td>
				<td style="white-space: nowrap;text-align: center;">Total</td>
			</tr>
			<tr>
				<td colspan="4" style="text-align: center;">---------------------------------------</td>
			</tr>
		</thead>
		<tbody>
			<?php $total = 0 ?>
			<?php $decuento = 0 ?>
			@foreach($s_ventadetalles as $value)
			<?php $subtotal = number_format($value->cantidad*$value->preciounitario, 2, '.', '') ?>
			<tr>
				<td style="white-space: nowrap;text-align: center;">{{ $value->cantidad }}</td>
				<td>{{ $value->productocodigo }}:{{ $value->productonombre }}</td>
				<td style="white-space: nowrap;text-align: right;">{{ $value->preciounitario }}</td>
				<td style="white-space: nowrap;text-align: right;">{{ $subtotal }}</td>
			</tr>
			@if($value->descuento>0)
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td style="white-space: nowrap;text-align: right;">-{{ $value->descuento }}</td>
			</tr>
			@endif
			<?php $total = $total+$subtotal ?>
			<?php $decuento = $decuento+$value->descuento ?>
			@endforeach
			<tr>
				<td colspan="4" style="text-align: center;">---------------------------------------</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td>Sub Total:</td>
				<td style="white-space: nowrap;text-align: right;">{{ number_format($total, 2, '.', '') }}</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td>Descuento:</td>
				<td style="white-space: nowrap;text-align: right;">{{ number_format($decuento, 2, '.', '') }}</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td>Total:</td>
				<td style="white-space: nowrap;text-align: right;">{{ number_format($total-$decuento, 2, '.', '') }}</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td colspan="2" style="text-align: center;">------------------</td>
			</tr>
		</tbody>
	</table>
	<div class="datofinal">
	¡Gracias por su compra!
	</div>
	</code>
</div>
</body>
</html>