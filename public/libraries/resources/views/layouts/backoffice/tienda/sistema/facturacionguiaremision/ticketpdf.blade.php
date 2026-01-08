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
			font-size: 11px;
		}
		.nombrecomercial {
			font-size: 11px;
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
      
    @if($facturacionguiaremision->agencialogo!='')
          <img class="logo" src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$facturacionguiaremision->agencialogo) }}" height=50px><br>
      @endif
      {{ strtoupper($facturacionguiaremision->emisor_nombrecomercial) }}
      <br>
    </div>
      RUC: {{ $facturacionguiaremision->emisor_ruc }}<br>
           {{ strtoupper($facturacionguiaremision->emisor_direccion) }}<br>
           {{ strtoupper($facturacionguiaremision->emisor_departamento.'/'.$facturacionguiaremision->emisor_provincia.'/'.$facturacionguiaremision->emisor_distrito) }}<br><br>
 
    <div class="datocomprobante">
      GUIA DE REMISIÓN: {{$facturacionguiaremision->despacho_serie}}-{{ str_pad($facturacionguiaremision->despacho_correlativo, 8, "0", STR_PAD_LEFT) }}<br>
      EMISIÓN: {{date_format(date_create($facturacionguiaremision->despacho_fechaemision),"d/m/Y h:i:s A") }}<br><br>
    </div>  
        REMITENTE<br>
    <div class="datocomprobante">
      RUC: {{ strtoupper($facturacionguiaremision->emisor_ruc)}}<br>
      RAZÓN SOCIAL: {{ strtoupper($facturacionguiaremision->emisor_razonsocial)}}<br>
      PARTIDA: {{ $facturacionguiaremision->envio_direccionpartida }}<br>
      UBIGEO: {{ $facturacionguiaremision->partidaubigeonombre }}<br><br>
    </div>  
        DESTINATARIO<br>
    <div class="datocomprobante">
      RUC/DNI: {{ strtoupper($facturacionguiaremision->despacho_destinatario_numerodocumento)}}<br>
      RAZÓN SOCIAL: {{strtoupper($facturacionguiaremision->despacho_destinatario_razonsocial)}}<br>
      LLEDADA: {{ $facturacionguiaremision->envio_direccionllegada }}<br>
      UBIGEO: {{ $facturacionguiaremision->llegadaubigeonombre }}<br><br>
    </div>  
        TRANSPORTISTA<br>
    <div class="datocomprobante">
      RUC/DNI: {{ strtoupper($facturacionguiaremision->transporte_choferdocumento )}}<br>
      RAZÓN SOCIAL: {{ strtoupper($facturacionguiaremision->transportista )}}<br><br>
    </div>  
        DETALLE DE ENVIO<br>
    <div class="datocomprobante">
      MOTIVO: {{ $facturacionguiaremision->envio_descripciontraslado }}<br>
      F. TRASLADO: {{date_format(date_create($facturacionguiaremision->envio_fechatraslado),"d/m/Y") }}<br>
      OBSERVACIÓN: {{ strtoupper($facturacionguiaremision->despacho_observacion )}}<br>
      RESPONSABLE: {{ strtoupper($facturacionguiaremision->responsablenombre) }}<br>
    </div>
   	<table class="table" style="margin-top:5px;">
		<thead>
			<tr>
				<td colspan="2" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
			<tr>
				<td style="white-space: nowrap;text-align: center;">CANT.</td>
				<td style="white-space: nowrap;text-align: center;width:60px;">UNIDAD</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
		</thead>
		<tbody>
        <?php $total = 0; ?>
        @foreach($facturacionguiaremisiondetalles as $value)
          <tr>
            <td colspan="2"> {{ strtoupper($value->descripcion) }}</td>
          </tr>
          <tr>
              <td style="white-space: nowrap;text-align: center;">{{ $value->cantidad }}</td>
              <td style="white-space: nowrap;text-align: center;">{{ $value->unidad }}</td>
          </tr>
      @endforeach
			<tr>
				<td colspan="2" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: center;"</td>
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
