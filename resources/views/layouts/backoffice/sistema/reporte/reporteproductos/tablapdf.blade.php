<!DOCTYPE html>
<html>
<head>
    <title>REPORTE DE PRODUCTOS</title>
    @include('app.pdf_style',['idtienda'=>$tienda->id])
</head>
<body>
    @include('app.pdf_headerfooter',[
        'logo'=>$tienda->imagen,
        'nombrecomercial'=>$tienda->nombre,
        'direccion'=>$tienda->direccion,
        'ubigeo'=>$tienda->ubigeonombre,
        'tienda'=>$tienda,
    ])
    <div class="titulo">REPORTE DE PRODUCTOS</div>
    
    <div class="content">
      <div class="espacio"></div>
      @if(count($productos)==0)
      <div class="mensaje_alerta">No tiene ningún registro!!</div>
      @endif
      <table class="tabla">
          <tr class="tabla_cabera">
              <td width="50px" style="text-align:center;">CODIGO</td>
              <td width="90px" style="text-align:center;">NOMBRE</td>
              <td width="60px" style="text-align:center;">CATEGORIA</td>
              <td width="80px" style="text-align:center;">MARCA</td>
              <td width="30px" style="text-align:center;">U. MEDIDA</td>
              <td width="40px" style="text-align:center;">PRECIO</td>
              <td width="40px" style="text-align:center;">ESTADO</td>
          </tr>
          @foreach($productos as $value)
          <tr>
              <td width="50px" style="text-align:center;">{{ $value['codigo'] }}</td>
              <td width="90px" style="text-align:center;">{{ $value['nombre'] }}</td>
              <td width="60px" style="text-align:center;">{{ $value['categoria'] }}</td>
              <td width="80px" style="text-align:center;">{{ $value['marca'] }}</td>
              <td width="30px" style="text-align:center;">{{ $value['unidad_medida'] }}</td>
              <td width="40px" style="text-align:right;">{{ $value['precio'] }}</td>
              <td width="40px" style="text-align:right;">{{ $value['estado'] }}</td>
          </tr>
          @endforeach
      </table>
    </div>
</body>
</html>