<!DOCTYPE html>
<html>
<head>
    <title>REPORTE DE MARGEN DE GANANCIA</title>
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
    <div class="titulo">REPORTE DE MARGEN DE GANANCIA</div>
    
    <div class="content">
        <table class="tabla_informativa">
            <tr>
                <td style="text-align:right;width:49%;"><b>FECHA INICIO:</b> {{$fechainicio!=''?date_format(date_create($fechainicio),"d/m/Y"):''}}</td>
                <td style="text-align:center;width:2%;"><b>|</b></td>
                <td style="text-align:left;width:49%;"><b>FECHA FIN:</b> {{$fechafin!=''?date_format(date_create($fechafin),"d/m/Y"):''}}</td>
            </tr>
        </table>
        <div class="espacio"></div>
        @if(count($productos)==0)
          <div class="mensaje_alerta">No tiene ningún registro!!</div>
        @else
          <table class="tabla">
                  <tr class="tabla_cabera">
                      <td style="text-align:center;">PRODUCTO</td>
                      <td width="9%" style="text-align:center;">TOTAL COMPRA</td>
                      <td width="9%" style="text-align:center;">TOTAL VENTA</td>
                      <td width="9%" style="text-align:center;">GANANCIA</td>
                      <td width="9%" style="text-align:center;">GANANCIA (%)</td>
                  </tr>
                  @foreach($productos as $value)
                  <tr>
                      <td class="tabla_subtitulo" style="text-align:left;">{{ $value['nombre'] }}</td>
                      <td class="tabla_subtitulo" style="text-align:right;">{{ $value['preciocompra'] }}</td>
                      <td class="tabla_subtitulo" style="text-align:right;">{{ $value['precioventa'] }}</td>
                      <td class="tabla_subtitulo" style="text-align:right;">{{ $value['ganancia'] }}</td>
                      <td class="tabla_subtitulo" style="text-align:right;">{{ $value['gananciapor'] }} %</td>
                  </tr>
                  @endforeach
          </table>
        @endif
    </div>
</body>
</html>