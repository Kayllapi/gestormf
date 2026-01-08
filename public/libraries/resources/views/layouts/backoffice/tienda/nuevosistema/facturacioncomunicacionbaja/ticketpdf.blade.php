<!DOCTYPE html>
<html>
<head>
	<title>Comunicación de Baja</title>
	<style>
		html, body {
			margin: 0px;
			padding: 15px;
			font-size: 11px;
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
      @if($facturacioncomunicacionbaja->agencialogo!='')
          <img class="logo" src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$facturacioncomunicacionbaja->agencialogo) }}" height=50px><br>
      @endif
      {{ strtoupper($facturacioncomunicacionbaja->emisor_nombrecomercial) }}
      <br>    
    </div>
        RUC:  {{ $facturacioncomunicacionbaja->emisor_ruc }}<br>
              {{ strtoupper($facturacioncomunicacionbaja->emisor_direccion) }}<br>
              {{ strtoupper($facturacioncomunicacionbaja->emisor_departamento.'/'.$facturacioncomunicacionbaja->emisor_provincia.'/'.$facturacioncomunicacionbaja->emisor_distrito) }}<br><br>
    <div class="datocomprobante">
        COMUNICACIÓN DE BAJA: {{ str_pad($facturacioncomunicacionbaja->comunicacionbaja_correlativo, 8, "0", STR_PAD_LEFT) }}<br>
        GENERACIÓN: {{ date_format(date_create($facturacioncomunicacionbaja->comunicacionbaja_fechageneracion),"d/m/Y h:i:s A") }}<br>
        COMUNICACIÓN: {{ date_format(date_create($facturacioncomunicacionbaja->comunicacionbaja_fechacomunicacion),"d/m/Y h:i:s A") }}<br>
        RESPONSABLE: {{ strtoupper($facturacioncomunicacionbaja->responsablenombre) }}<br>
    </div>
   	<table class="table" style="margin-top:5px;">
		<tbody>
			<tr>
				<td style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
      @foreach($facturacioncomunicacionbajadetalle as $value)
          <tr>
            <td>
              RUC/DNI: {{ $value->clienteidentificacion }} <br>
              CLIENTE: {{ strtoupper($value->cliente) }}<br>
              SERIE-CORRELATIVO: {{ $value->serie }}-{{ $value->correlativo }}<br>
              MOTIVO: {{ $value->descripcionmotivobaja }}<br>
            </td>
          </tr>
			<tr>
				<td style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
      @endforeach
			<tr>
				<td style="text-align: center;"</td>
			</tr>
		</tbody>
	</table>
    @if($respuesta!='')
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
