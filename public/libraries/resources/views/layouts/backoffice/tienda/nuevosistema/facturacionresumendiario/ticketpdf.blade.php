<!DOCTYPE html>
<html>
<head>
	<title>Resumen Diario</title>
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
      @if($facturacionresumen->agencialogo!='')
          <img class="logo" src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$facturacionresumen->agencialogo) }}" height=50px><br>
      @endif
      {{ strtoupper($facturacionresumen->emisor_nombrecomercial) }}
      <br>    
    </div>
        RUC:  {{ $facturacionresumen->emisor_ruc }}<br>
              {{ strtoupper($facturacionresumen->emisor_direccion) }}<br>
              {{ strtoupper($facturacionresumen->emisor_departamento.'/'.$facturacionresumen->emisor_provincia.'/'.$facturacionresumen->emisor_distrito) }}<br><br>
    <div class="datocomprobante">
        RESUMEN DIARIO: {{ str_pad($facturacionresumen->resumen_correlativo, 8, "0", STR_PAD_LEFT) }}<br>
        GENERACIÓN: {{ date_format(date_create($facturacionresumen->resumen_fechageneracion),"d/m/Y h:i:s A") }}<br>
        RESUMEN: {{ date_format(date_create($facturacionresumen->resumen_fecharesumen),"d/m/Y h:i:s A") }}<br>
        RESPONSABLE: {{ strtoupper($facturacionresumen->responsablenombre) }}<br>
    </div>
   	<table class="table" style="margin-top:5px;">
		<tbody>
			<tr>
				<td style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
      @foreach($facturacionresumendetalle as $value)
          <tr>
            <td>
              RUC/DNI: {{ $value->clientenumero }} <br>
              CLIENTE: {{ strtoupper($value->cliente) }}<br>
              SERIE-CORRELATIVO: {{ $value->serienumero }}<br>
              OP. GRAVADA: {{ $value->operacionesgravadas }}<br>
              IGV: {{ $value->montoigv }}<br>
              TOTAL: {{ $value->total }}<br>
              ESTADO: {{ $value->estado==1?'ADICIONADO':($value->estado==2?'MODIFICADO':($value->estado==3?'ANULADO':'---')) }}<br>
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
