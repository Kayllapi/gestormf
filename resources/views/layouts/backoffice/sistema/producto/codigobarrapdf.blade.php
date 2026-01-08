<!DOCTYPE html>
<html>
<head>
	<title>Ticket de Precio</title>
	<style>
		html, body {
			margin: 0px;
			padding: 0px;
			font-size: 10px;
      font-weight: bold;
      background-color:red;
			width: <?php echo  configuracion($tienda->id,'facturacion_anchoticket')['resultado']=='CORRECTO'?(configuracion($tienda->id,'facturacion_anchoticket')['valor']-1):'7' ?>cm;
      font-family: <?php echo  configuracion($tienda->id,'facturacion_tipoletra')['resultado']=='CORRECTO'?configuracion($tienda->id,'facturacion_tipoletra')['valor']:'Courier' ?>,sans-serif;
		}
		.contenedor {
			margin: 15px;
			text-align: center;
      /*border:3px solid #000*/
		}
		table {
			width: 100%;
      margin:0px;
       padding:0px;
		}
		.titulo {
			font-size: 11px;
      text-align: center;
		}
		.precio {
			font-size: 20px;
      white-space: nowrap;
      text-align: center;
		}
		.imagen {
			height: 40px;
      width: 95%;
		}
	</style>
</head>
<body>
<div class="contenedor">
	<table>
		<thead>
			<tr>
				<td colspan="4" class="titulo">{{$producto->nombre}}</td>
			</tr>
			<tr>
				<td colspan="4" class="precio">
      <img class="imagen"
           src='https://barcode.tec-it.com/barcode.ashx?data={{$producto->codigo}}&code=Code39FullASCII&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&codepage=&qunit=Mm&quiet=0'/></td>
			</tr>
      
		</thead>
	</table>
</div>
</body>
</html>