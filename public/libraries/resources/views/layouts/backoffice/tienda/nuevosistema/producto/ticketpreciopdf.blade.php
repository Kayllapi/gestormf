<!DOCTYPE html>
<html>
<head>
	<title>Ticket de Precio</title>
	<style>
		html, body {
			margin: 0px;
			padding: 13px;
			font-size: 10px;
      font-weight: bold;
		}
		.contenedor {
			width: 220px;
			text-align: center;
      /*background-color:red;*/
      border:3px solid #000
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
	<code>
	<table>
		<thead>
			<tr>
				<td colspan="4" class="titulo">{{$producto->nombre}}</td>
			</tr>
			<tr>
				<td colspan="4" class="precio">{{$producto->precioalpublico}}</td>
			</tr>
			<tr>
				<td colspan="4" class="precio">
      <img class="imagen"
           src='https://barcode.tec-it.com/barcode.ashx?data={{$producto->codigo}}&code=Code39FullASCII&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&codepage=&qunit=Mm&quiet=0'/></td>
			</tr>
      
		</thead>
	</table>
	</code>
</div>
</body>
</html>