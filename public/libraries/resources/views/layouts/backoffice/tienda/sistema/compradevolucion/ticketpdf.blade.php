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
  <div class="nombrecomercial">{{ strtoupper($compradevolucion->tiendanombre) }}</div>
      {{ strtoupper($compradevolucion->tiendadireccion) }}<br><br>
	<div class="datocomprobante">
    DEVOLUCIÓN: {{ str_pad($compradevolucion->codigo, 8, "0", STR_PAD_LEFT) }} <br>
    FECHA: {{$compradevolucion->fecharegistro}}<br>
    PROVEEDOR: {{$compradevolucion->apellidosproveedor}},{{$compradevolucion->nombreproveedor}} <br>
    RUC: {{$compradevolucion->identificacionproveedor}} <br>
    DIRECCIÓN: {{$compradevolucion->direccionproveedor}}<br>
	</div>
	<table class="table" style="margin-top:5px;">
		<thead>
			<tr>
				<td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
			<tr>
				<td style="white-space: nowrap;text-align: center;">CANT.</td>
				<td style="white-space: nowrap;text-align: center;width:60px;">P.UNIT.</td>
				<td style="white-space: nowrap;text-align: right;width:30px;">TOTAL</td>
			</tr>
			<tr>
				<td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
		</thead>
		<tbody>
			@foreach($compradevoluciondetalles as $value)
			<tr>
				<td colspan="3">{{ strtoupper($value->concepto) }}</td>
			</tr>
			<tr>
				<td style="white-space: nowrap;text-align: center;">{{ $value->cantidad }}</td>
				<td style="white-space: nowrap;text-align: center;">{{ $value->preciounitario }}</td>
				<td style="white-space: nowrap;text-align: right;">{{ $value->preciototal }}</td>
			</tr>
			@endforeach
      <?php $total = 0  ?>
      <?php
  
      $subtotal = $total+number_format($compradevolucion->total/1.18, 2, '.', '');
      $igv = $compradevolucion->total-$subtotal
        
      ?>
      <tr>
				<td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: right;">SUB TOTAL:</td>
				<td style="white-space: nowrap;text-align: right;">
            {{$subtotal}}
        </td>
			</tr>
      <tr>
        <td colspan="2" style="text-align: right;">IGV:</td>
				<td style="white-space: nowrap;text-align: right;">
          {{$igv}}
        </td>
			</tr>
      <tr>
        <td colspan="2" style="text-align: right;">TOTAL:</td>
				<td style="white-space: nowrap;text-align: right;">
        {{$compradevolucion->total}}
        </td>
			</tr>
		</tbody>
	</table>
  
	<div class="datofinal">
	¡GRACIAS POR SU COMPRA DE DEVOLUCIÓN!
	</div>
</div>
</body>
</html>