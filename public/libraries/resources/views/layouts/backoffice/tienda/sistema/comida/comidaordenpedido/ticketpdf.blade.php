<!DOCTYPE html>
<html>
<head>
	<title>Ticket</title>
	<style>
		html, body {
          margin: 0px;
          padding: 0px;
          font-size: 10px;
          font-weight: bold;
          font-family: <?php echo  configuracion($tienda->id,'facturacion_tipoletra')['resultado']=='CORRECTO'?configuracion($tienda->id,'facturacion_tipoletra')['valor']:'Courier' ?>,sans-serif;
		}
		.contenedor {
			padding: 15px;
			width: <?php echo  configuracion($tienda->id,'facturacion_anchoticket')['resultado']=='CORRECTO'?(configuracion($tienda->id,'facturacion_anchoticket')['valor']-1):'7' ?>cm;
			text-align: center;
		}
		.table {
			width: 100%;
      margin:0px;
      padding:0px;
			font-size: 11px;
		}
		.nombrecomercial {
			font-size: 15px;
      margin-top:0px;
		}
		.datocomprobante {
			text-align: left;
		}
		.datofinal {
			text-align: center;
      margin-top:10px;
		}
		.datodetalle {
			text-align: left;
		}
	</style>
</head>
<body>
    <div class="ticket_contenedor">
      <div class="contenedor">
          @include('app.pdf_headerticket',[
              'logo'=>$tienda->imagen,
              'tienda'=>$tienda,
          ])
          <div class="titulo">ORDEN DE PEDIDO</div>
          <div class="espacio"></div>
          <table class="tabla_informativa">
              <tr>
                  <td class="tabla_informativa_subtitulo" style="width:65px;">CÓDIGO</td>
                  <td class="tabla_informativa_punto" style="width:5px;">:</td>
                  <td class="tabla_informativa_descripcion">{{ str_pad($ordenpedido->codigo, 6, "0", STR_PAD_LEFT) }}</td>
              </tr>
              <tr>
                  <td class="tabla_informativa_subtitulo">FECHA</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{ date_format(date_create($ordenpedido->fecharegistro),"d/m/Y h:i A") }}</td>
              </tr>
              <tr>
                  <td class="tabla_informativa_subtitulo" style="width:65px;">NRO MESA</td>
                  <td class="tabla_informativa_punto" style="width:5px;">:</td>
                  <td class="tabla_informativa_descripcion">{{ str_pad($ordenpedido->numeromesa, 2, "0", STR_PAD_LEFT) }}</td>
              </tr>
              <tr>
                  <td class="tabla_informativa_subtitulo" style="width:65px;">MESERO</td>
                  <td class="tabla_informativa_punto" style="width:5px;">:</td>
                  <td class="tabla_informativa_descripcion">{{ strtoupper($ordenpedido->mesero_apellidos) }}, {{ strtoupper($ordenpedido->mesero_nombre) }}</td>
              </tr>
        </table>

        <table class="table">
            <tr>
              <td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
            </tr>
            <tr>
              <td style="white-space: nowrap;text-align: right;">CANT.</td>
              <td style="white-space: nowrap;text-align: right;">PRECIO</td>
              <td style="white-space: nowrap;text-align: right;">TOTAL</td>
            </tr>
            <tr>
              <td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
            </tr>
            @foreach($ordenpedidodetalles as $value)
            <tr>
              <td colspan="3">{{ strtoupper($value->productonombre) }}</td>
            </tr>
            <tr>
              <td style="white-space: nowrap;text-align: right;">{{ $value->cantidad }}</td>
              <td style="white-space: nowrap;text-align: right;">{{ $value->precio }}</td>
              <td style="white-space: nowrap;text-align: right;">{{ $value->total }}</td>
            </tr>
            @endforeach
            <tr>
              <td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
            </tr>
            <tr>
              <td colspan="2" style="text-align: right;">TOTAL</td>
              <td style="white-space: nowrap;text-align: right;">{{ $ordenpedido->total }}</td>
            </tr>
        </table>   
    </div>
    </div>
</body>
</html>