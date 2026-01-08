<!DOCTYPE html>
<html>
<head>
	<title>REPORTE</title>
	<style>
		html, body {
			margin: 0px;
			padding: 25px;
      padding-top:30px;
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
	</style>
</head>
<body>
<div class="contenedor">
    <div class="nombrecomercial"> REPORTE DE HOJA DE ALMACÉN <br><br></div>
    <div class="datocomprobante">
      FECHA INICIO: {{ date_format(date_create($fechainicio),"d/m/Y") }}<br>
      FECHA FIN: {{ date_format(date_create($fechafin),"d/m/Y") }}<br>
    </div>
    <table class="table" style="margin-top:5px;">
      <thead class="thead-dark">
        <tr>
          <td colspan="2" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
        </tr>
        <tr>
          <th style="white-space: nowrap;text-align: center;">Producto</th>
          <th style="white-space: nowrap;text-align: center;width:10px;">Cant.</th>
        </tr><tr>
          <td colspan="2" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
        </tr>
      </thead>
      <tbody>
         @foreach($s_ventadetalles as $value)
          <tr>
            <td style="text-align: left;">{{ $value->nombreproducto }}</td>
            <td style="white-space: nowrap;text-align: right;">{{ $value->cantidad }}</td>
          </tr>
         @endforeach
          <tr>
            <td colspan="2" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
          </tr>
      </tbody>
    </table>
</div>
</body>
</html>