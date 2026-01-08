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
			width: <?php echo  $configuracion_facturacion['anchoticket']!=null?($configuracion_facturacion['anchoticket']-1):'6.62' ?>cm;
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
      margin-top:-10px;
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
      @if($agencia->logo!='')
      <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$agencia->logo) }}" height="60px">
      @endif
      <div class="nombrecomercial">{{ strtoupper($agencia->nombrecomercial) }}</div>
      RUC: {{ $agencia->ruc }}<br>
      {{ strtoupper($agencia->direccion) }}<br>
      {{ strtoupper($agencia->ubigeonombre) }}<br><br>
    @else
     <div class="nombrecomercial"> {{ strtoupper($tienda->nombre) }}</div>
      {{ strtoupper($tienda->direccion) }}<br><br>
    @endif
	<div class="datocomprobante">
    VENTA: {{ str_pad($venta->codigo, 8, "0", STR_PAD_LEFT) }}<br>
    FECHA: {{ date_format(date_create($venta->fechaconfirmacion),"d/m/Y h:i:s A") }}<br>
    MONEDA: {{ $venta->monedanombre }}<br>
    
    @if (!is_null($comida_venta))
    NRO MESA: {{ strtoupper($comida_venta->mesa_numero_mesa) }}<br>
    @endif
    
    CLIENTE: {{ strtoupper($cliente->idtipopersona==1?$cliente->nombre.', '.$cliente->apellidos:$cliente->apellidos) }}<br>
    @if($cliente->identificacion!='')
    @if($cliente->idtipopersona==1)
    DNI/RUC: {{ $cliente->identificacion }}<br>
    @else
    RUC: {{ $cliente->identificacion }}<br>
    @endif
    @endif
    @if($cliente->direccion!='')
    DIRECCIÓN: {{ strtoupper($cliente->direccion) }} {{ strtoupper($cliente->ubigeonombre) }}<br>
    @endif
    @if($venta->s_idtipoentrega==2)
    ENTREGA: {{ strtoupper($venta->tipoentreganombre) }}<br>
    @endif
    
    @if (!is_null($comida_venta))
    MESERO: {{ strtoupper($comida_venta->mesero_nombre) }}<br>
    @endif
    
    VENTANILLA: {{ strtoupper($vendedor->nombre) }}<br>
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
			@foreach($s_ventadetalles as $value)
			<?php $subtotal = number_format($value->cantidad*$value->preciounitario, 2, '.', '') ?>
			<tr>
				<td colspan="3"> <!--{{ $value->productocodigo }}:--> {{ strtoupper($value->productonombre) }}</td>
			</tr>
			<tr>
				<td style="white-space: nowrap;text-align: center;">{{ $value->cantidad }}</td>
				<td style="white-space: nowrap;text-align: center;">{{ $value->preciounitario }}</td>
				<td style="white-space: nowrap;text-align: right;">{{ $value->total }}</td>
			</tr>
			@endforeach
			<tr>
				<td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
      @if($configuracion['estadodescuento']==1 && $venta->totaldescuento>0)
			<tr>
				<td colspan="2" style="text-align: right;">Total Venta:</td>
				<td style="white-space: nowrap;text-align: right;">
          {{ $venta->totalventa }}
        </td>
			</tr>
			<tr>
				<td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
      @foreach($ventadescuentos as $value)
        <?php 
        $ventadescuentodetalles = DB::table('s_ventadescuentodetalle')
            ->join('s_producto','s_producto.id','s_ventadescuentodetalle.s_idproducto')
            ->where('s_ventadescuentodetalle.s_idventadescuento',$value->id)
            ->select(
              's_producto.nombre as productonombre',
              DB::raw('COUNT(s_producto.nombre) as cantidadrepetido')
            )
            ->groupBy('s_producto.nombre')
            ->orderBy('s_ventadescuentodetalle.id','asc')
            ->get();
        ?>
        <tr>
          <td colspan="2">
            @foreach($ventadescuentodetalles as $descvalue)
            ({{$descvalue->cantidadrepetido}}) {{strtoupper($descvalue->productonombre)}}<br>
            @endforeach 
          </td>
          <td style="white-space: nowrap;text-align: right;">-{{$value->montodescuento}}</td>   
        </tr>
      @endforeach 
			<tr>
				<td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: right;">TOTAL DESCUENTO:</td>
				<td style="white-space: nowrap;text-align: right;">
          -{{ $venta->totaldescuento }}
        </td>
			</tr>
			<tr>
				<td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
      @endif
      @if($venta->s_idtipoentrega==2)
			<tr>
				<td colspan="2" style="text-align: right;">SUB TOTAL:</td>
				<td style="white-space: nowrap;text-align: right;">
          {{ $venta->subtotal }}
        </td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: right;">COSTO DE ENVIO:</td>
				<td style="white-space: nowrap;text-align: right;">{{ number_format($venta->envio, 2, '.', '') }}</td>
			</tr>
      @endif
			<tr>
				<td colspan="2" style="text-align: right;">TOTAL:</td>
				<td style="white-space: nowrap;text-align: right;">
          {{ $venta->total }}
        </td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: right;">TOTAL REDONDEADO:</td>
				<td style="white-space: nowrap;text-align: right;">
          {{ $venta->totalredondeado }}
        </td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: right;">MONTO PAGADO:</td>
				<td style="white-space: nowrap;text-align: right;">
          {{ $venta->montorecibido }}
        </td>
			</tr>
      @if($venta->vuelto!=0)
			<tr>
				<td colspan="2" style="text-align: right;">VUELTO:</td>
				<td style="white-space: nowrap;text-align: right;">
          {{ $venta->vuelto }}
        </td>
			</tr>
      @endif
			<tr>
				<td colspan="3" style="text-align: center;"</td>
			</tr>
		</tbody>
	</table>
	<div class="datofinal">
	¡GRACIAS POR SU COMPRA!
	</div>
</div>
</body>
</html>