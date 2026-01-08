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
        width: <?php echo  configuracion($tienda->id,'facturacion_anchoticket')['resultado']=='CORRECTO'?configuracion($tienda->id,'facturacion_anchoticket')['valor']:'7' ?>cm;
        font-family: <?php echo  configuracion($tienda->id,'facturacion_tipoletra')['resultado']=='CORRECTO'?configuracion($tienda->id,'facturacion_tipoletra')['valor']:'Courier' ?>,sans-serif;
		}
		.contenedor {
        margin: 15px;
        width: 100%;
        text-align: center;
        border:3px solid #000
		}
		.tabla {
        width: 100%;
        margin:0px;
        padding:0px;
		}
		.titulo {
        font-size: 9px;
        text-align: left;
        padding-left:5px;
		}
		.precio {
        font-size: 50px;
        text-align: center;
		}
	</style>
</head>
<body>
<div class="contenedor">
	<table class="tabla">
			<tr>
				<td class="titulo">{{strtoupper($producto->nombre)}}</td>
				<td class="precio">{{$producto->precioalpublico}}</td>
			</tr>
	</table>
</div>
</body>
</html>