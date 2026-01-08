<!DOCTYPE html>
<html>
<head>
	<title>Nota de Débito</title>
	<style>
		html, body {
			margin: 0px;
			padding: 15px;
			font-size: 11px;
      font-weight: bold;
      font-family: <?php echo  configuracion($tienda->id,'facturacion_tipoletra')['resultado']=='CORRECTO'?configuracion($tienda->id,'facturacion_tipoletra')['valor']:'Courier' ?>,sans-serif;
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
			font-size:11px;
		}
		.nombrecomercial {
			font-size: 11px;
		}
		.datocomprobante {
			text-align: left;
      padding: 5px;
		}
		.datofinal {
			text-align: center;
		}
		.datodetalle {
			text-align: left;
		}
     .datofacturaoboleta{
      text-align: center;
      padding:5px;
      font-weight:bold;
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
      @if($facturacionnotadebito->agencialogo!='')
          <img class="logo" src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$facturacionnotadebito->agencialogo) }}" height=50px><br>
      @endif
      {{ strtoupper($facturacionnotadebito->emisor_nombrecomercial) }}
      <br>    
    </div>
        RUC:  {{ $facturacionnotadebito->emisor_ruc }}<br>
              {{ strtoupper($facturacionnotadebito->emisor_direccion) }}<br>
              {{ strtoupper($facturacionnotadebito->emisor_departamento.'/'.$facturacionnotadebito->emisor_provincia.'/'.$facturacionnotadebito->emisor_distrito) }}<br><br>
    <div class="datocomprobante">
        NOTA DE DÉBITO: {{$facturacionnotadebito->notadebito_serie}}-{{ str_pad($facturacionnotadebito->notadebito_correlativo, 8, "0", STR_PAD_LEFT) }}<br>
        EMISIÓN: {{ date_format(date_create($facturacionnotadebito->notadebito_fechaemision),"d/m/Y h:i:s A") }}<br>
        @if($facturacionnotadebito->	notadebito_tipomoneda=='PEN')
        MONEDA: SOLES<br>
        @else($facturacionnotadebito->	notadebito_tipomoneda=='USD')
        MONEDA: DOLARES<br>
        @endif
        CLIENTE: {{ strtoupper($facturacionnotadebito->cliente_razonsocial) }}<br>
        DNI/RUC: {{ $facturacionnotadebito->cliente_numerodocumento }}<br>
        DIRECCIÓN: {{ strtoupper($facturacionnotadebito->cliente_direccion) }}<br>
        {{ strtoupper($facturacionnotadebito->cliente_departamento.'/'.$facturacionnotadebito->cliente_provincia.'/'.$facturacionnotadebito->cliente_distrito) }}<br>
        RESPONSABLE: {{ strtoupper($facturacionnotadebito->responsablenombre) }}<br><br>
    </div>
    <?php 
    $facturacionboletafactura = DB::table('s_facturacionboletafactura')
        ->where('s_facturacionboletafactura.id',$facturacionnotadebito->idfacturacionboletafactura)
        ->first(); 
    ?>
        COMPROBANTE MODIFICADO<br>
    <div class="datocomprobante">
        @if($facturacionboletafactura->venta_tipodocumento==3)
           BOLETA: {{$facturacionboletafactura->venta_serie}}-{{ str_pad($facturacionboletafactura->venta_correlativo, 8, "0", STR_PAD_LEFT) }}
        @elseif($facturacionboletafactura->venta_tipodocumento==1)
           FACTURA: {{$facturacionboletafactura->venta_serie}}-{{ str_pad($facturacionboletafactura->venta_correlativo, 8, "0", STR_PAD_LEFT) }}
        @endif<br>
        EMISIÓN: {{ date_format(date_create($facturacionboletafactura->venta_fechaemision),"d/m/Y h:i:s A") }}<br>
        MOTIVO: {{strtoupper($facturacionnotadebito->notadebito_descripcionmotivo) }}
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
			@foreach($notadebitodetalle as $value)
			<tr>
				<td colspan="3"> {{ strtoupper($value->descripcion) }}</td>
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
          {{ $facturacionnotadebito->notadebito_valorventa }}
        </td>
			</tr>
      <tr>
				<td colspan="2" style="text-align: right;">IGV:</td>
				<td style="white-space: nowrap;text-align: right;">
          {{ $facturacionnotadebito->notadebito_totalimpuestos }}
        </td>
			</tr>
      <tr>
				<td colspan="2" style="text-align: right;">TOTAL:</td>
				<td style="white-space: nowrap;text-align: right;">
          {{ $facturacionnotadebito->notadebito_montoimpuestoventa }}
        </td>
			</tr>
			<tr>
				<td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
			<tr>
				<td colspan="3" style="text-align: center;height:5px;">{{ $facturacionnotadebito->leyenda_value }}</td>
			</tr>
			<tr>
				<td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
		</tbody>
	</table>
    @if($respuesta!='')
	<div class="qr">
    <img src="{{$respuesta->qr}}" width="100px"><br>
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
