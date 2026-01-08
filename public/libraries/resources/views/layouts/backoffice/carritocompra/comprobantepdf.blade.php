<!DOCTYPE html>
<html>
<head>
	<title>Comprobante</title>
	<style>
		html, body {
			margin: 0px;
			padding: 10px;
			font-size: 10px;
			font-family: Arial, sans-serif;
		}
		.table {
			width: 937px;
			border-collapse: collapse;
    		border-spacing: 0;

		}
		.table td {
			border:1px solid #ccc;
			padding: 5px;
		}

		.trcabacera {
			background-color: #31353d;
			color: #fff;
		}
		.table-sinborde {
			width: 100%;
			border-collapse: collapse;
    	border-spacing: 0;
		}
		.table-sinborde td {	
			padding: 1px;
		}
	</style>
</head>
<body>
	
  @if($agencia!='')
	<table class="table-sinborde">
		<tr>
			<td rowspan="3" style="padding:0px;text-align: center;">
						<?php
                        $rutaimagen = getcwd().'/public/backoffice/tienda/'.$agencia->idtienda.'/sistema/'.$agencia->logo;
                        $logo = url('public/backoffice/sistema/sin_imagen_cuadrado.png');
                        if(file_exists($rutaimagen) && $agencia->logo!=''){
                            $logo =  url('/public/backoffice/tienda/'.$agencia->idtienda.'/sistema/'.$agencia->logo);
                        }
                        ?>
                <img src="{{ $logo }}" height="60px">
			</td>
			<td style="white-space: nowrap;text-align: right;width: 250px;font-size: 15px;">{{ $agencia->nombrecomercial }}</td>
		</tr>
		<tr>
			<td style="white-space: nowrap;text-align: right;font-size: 12px;">{{ $agencia->ruc }}</td>
		</tr>
		<tr>
			<td style="white-space: nowrap;text-align: right;font-size: 12px;">{{ $agencia->direccion }}</td>
		</tr>
	</table>
	@else
	<table class="table-sinborde">
		<tr>
			<td rowspan="2" style="padding:0px;text-align: center;">
						<?php
                        $rutaimagen = getcwd().'/public/backoffice/tienda/'.$venta->idtienda.'/logo/'.$venta->tiendaimagen;
                        $logo = url('public/backoffice/sistema/sin_imagen_cuadrado.png');
                        if(file_exists($rutaimagen) && $venta->tiendaimagen!=''){
                            $logo =  url('/public/backoffice/tienda/'.$venta->idtienda.'/logo/'.$venta->tiendaimagen);
                        }
                        ?>
                <img src="{{ $logo }}" height="60px">
			</td>
			<td style="white-space: nowrap;text-align: right;width: 250px;font-size: 15px;">{{ $venta->tiendanombre }}</td>
		</tr>
		<tr>
			<td style="white-space: nowrap;text-align: right;font-size: 12px;">{{ $venta->tiendadireccion }}</td>
		</tr>
	</table>
  @endif
	<br> 
	<table class="table-sinborde">
		<tr>
      <td><b>Cliente:</b> {{ $cliente->nombre }}</td>
			<td rowspan="2" style="white-space: nowrap;text-align: right;width: 200px;font-size: 15px;">{{ strtoupper($venta->comprobantenombre) }} - {{ str_pad($venta->codigo, 8, "0", STR_PAD_LEFT) }}</td>
		</tr>
		<tr>
			<td><b>DNI/RUC:</b> {{ $cliente->identificacion }}</td>
		</tr>
		<tr>
			<td><b>Dirección:</b> {{ $cliente->direccion }} {{ $cliente->ubigeonombre }}</td>
			<td style="white-space: nowrap;text-align: right;"><b>Fecha de emisión:</b> {{ $venta->fechaconfirmacion }}</td>
		</tr>
		<tr>
			<td><b>Vendedor:</b> {{ $vendedor->nombre }}</td>
			<td></td>
		</tr>
	</table>
	<br>
	<table class="table">
			<tr class="trcabacera">
				<td style="white-space: nowrap;text-align: center;width: 30px;">Cant.</td>
				<td style="white-space: nowrap;text-align: center;width: 100%;">Producto</td>
				<td style="white-space: nowrap;text-align: center;width: 50px;">P.Unit.</td>
				<td style="white-space: nowrap;text-align: center;width: 50px;">Total</td>
			</tr>
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
				<td colspan="3"></td>
				<td style="white-space: nowrap;text-align: right;">-{{ $value->descuento }}</td>
			</tr>
			@endif
			<?php $total = $total+$subtotal ?>
			<?php $decuento = $decuento+$value->descuento ?>
			@endforeach
			<tr>
				<td colspan="3" style="border-left: 0px;border-bottom: : 0px;border-top: : 0px;white-space: nowrap;text-align: right;">Sub Total:</td>
				<td style="white-space: nowrap;text-align: right;">{{ number_format($total, 2, '.', '') }}</td>
			</tr>
			<tr>
				<td colspan="3" style="border-left: 0px;border-bottom: : 0px;border-top: : 0px;white-space: nowrap;text-align: right;">Descuento:</td>
				<td style="white-space: nowrap;text-align: right;">{{ number_format($decuento, 2, '.', '') }}</td>
			</tr>
			<tr>
				<td colspan="2" style="border: 0px;"></td>
				<td style="border-left: 0px;border-bottom: : 0px;border-top: : 0px;white-space: nowrap;text-align: right;">Total:</td>
				<td style="white-space: nowrap;text-align: right;">{{ number_format($total-$decuento, 2, '.', '') }}</td>
			</tr>
		</tbody>
	</table>
  @if($s_ventadelivery!='')
  <b>ENTREGA:</b>
  <table class="table-sinborde">
		<tr>
			<td width="200px"><b>Fecha y Hora:</b> {{ $s_ventadelivery->fecha }} {{ $s_ventadelivery->hora }}</td>
		</tr>
		<tr>
			<td><b>Nombre de Persona:</b> {{ $s_ventadelivery->nombre }}</td>
		</tr>
		<tr>
			<td><b>Número de Celular:</b> {{ $s_ventadelivery->telefono }}</td>
		</tr>
		<tr>
			<td><b>Dirección:</b> {{ $s_ventadelivery->direccion }}</td>
		</tr>
	</table>
  @endif
  <div style="text-align: center;">¡Gracias por su compra!</div>
  
</body>
</html>