<!DOCTYPE html>
<html>
<head>
    <title>REPORTE DE VENTAS</title>
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
    <div class="titulo">REPORTE DE VENTAS</div>
    
    <div class="content">
        <table class="tabla_informativa">
            <tr>
                <td style="text-align:right;width:49%;"><b>FECHA INICIO:</b> {{$fechainicio!=''?date_format(date_create($fechainicio),"d/m/Y"):''}}</td>
                <td style="text-align:center;width:2%;"><b>|</b></td>
                <td style="text-align:left;width:49%;"><b>FECHA FIN:</b> {{$fechafin!=''?date_format(date_create($fechafin),"d/m/Y"):''}}</td>
            </tr>
        </table>
        <div class="espacio"></div>
        @if(count($ventas)==0)
        <div class="mensaje_alerta">No tiene ningún registro!!</div>
        @endif
        @if($listarpor==1)
          @if(count($ventas)>0)
            <table class="tabla">
                    <tr class="tabla_cabera">
                        <td width="60px" style="text-align:center;">CODIGO</td>
                        <td width="85px" style="text-align:center;">COMPROBANTE</td>
                        <td style="text-align:center;">CLIENTE</td>
                        <td style="text-align:center;">VENDEDOR</td>
                        <td style="text-align:center;">CAJERO</td>
                        <td width="130px" style="text-align:center;">FECHA DE VENTA</td>
                        <td width="40px" style="text-align:center;">TOTAL</td>
                    </tr>
                    @foreach($ventas as $value)
                    <tr>
                        <td>{{ $value['codigo'] }}</td>
                        <td>{{ $value['comprobante'] }}</td>
                        <td>{{ $value['cliente'] }}</td>
                        <td>{{ $value['vendedor'] }}</td>
                        <td>{{ $value['cajero'] }}</td>
                        <td style="text-align:center;">{{ $value['fechaconfirmacion'] }}</td>
                        <td style="text-align:right;">{{ $value['total'] }}</td>
                    </tr>
                    @endforeach
                    <tr class="tabla_cabera">
                        <td colspan="6" style="text-align:right;">TOTAL</td>
                        <td style="text-align:right;">{{$total}}</td>
                    </tr>
            </table>
          @endif
        @elseif($listarpor==2)
          @foreach($ventas as $value)
          <table class="tabla">
                  <tr class="tabla_cabera">
                      <td style="text-align:center;">CLIENTE: {{$value['cliente_identificacion']}} - {{$value['cliente']}}</td>
                  </tr>
          </table>
          <table class="tabla">
                  <tr class="tabla_cabera">
                      <td width="60px" style="text-align:center;">CODIGO</td>
                      <td width="85px" style="text-align:center;">COMPROBANTE</td>
                      <td style="text-align:center;">VENDEDOR</td>
                      <td style="text-align:center;">CAJERO</td>
                      <td width="130px" style="text-align:center;">FECHA DE VENTA</td>
                  </tr>
                  @foreach($value['detalle'] as $valuedetalle)
                  <tr>
                      <td class="tabla_subtitulo" style="text-align:center;">{{ $valuedetalle['codigo'] }}</td>
                      <td class="tabla_subtitulo">{{ $valuedetalle['comprobante'] }}</td>
                      <td class="tabla_subtitulo">{{ $valuedetalle['vendedor'] }}</td>
                      <td class="tabla_subtitulo">{{ $valuedetalle['cajero'] }}</td>
                      <td class="tabla_subtitulo" style="text-align:center;">{{ $valuedetalle['fechaconfirmacion'] }}</td>
                  </tr>
                  <tr>
                      <td style="text-align:left;padding:0px;" colspan="5">
                         <table class="tabla_informativa">
                            <tr>
                                <td class="tabla_titulo" style="text-align:center;width:1%;">NRO</td>
                                <td class="tabla_titulo" style="text-align:center;">PRODUCTO</td>
                                <td class="tabla_titulo" style="text-align:center;width:8%;">PRECIO</td>
                                <td class="tabla_titulo" style="text-align:center;width:8%;">CANT.</td>
                                <td class="tabla_titulo" style="text-align:center;width:8%;">TOTAL</td>
                            </tr>
                            @foreach($valuedetalle['detalleproducto'] as $valuedetalleproducto)
                            <tr>
                                <td style="text-align:center;">{{ $valuedetalleproducto['numero'] }}</td>
                                <td style="text-align:left;">{{ $valuedetalleproducto['producto'] }}</td>
                                <td style="text-align:right;">{{ $valuedetalleproducto['precio'] }}</td>
                                <td style="text-align:right;">{{ $valuedetalleproducto['cantidad'] }}</td>
                                <td style="text-align:right;">{{ $valuedetalleproducto['total'] }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td style="text-align:right;font-weight: bold;" colspan="4">TOTAL</td>
                                <td style="text-align:right;">{{ $valuedetalle['total'] }}</td>
                            </tr>
                        </table>
                      </td>
                  </tr>
                  @endforeach
          </table>
          <table class="tabla">
                  <tr class="tabla_cabera">
                      <td style="text-align:right;font-weight: bold;" colspan="4">TOTAL</td>
                      <td style="text-align:right;;width:8%;">{{ $total }}</td>
                  </tr>
          </table>
          <div class="espacio"></div>
          @endforeach
        @elseif($listarpor==3)
          @foreach($ventas as $value)
          <table class="tabla">
                  <tr class="tabla_cabera">
                      <td style="text-align:center;">VENDEDOR: {{$value['vendedor_identificacion']}} - {{$value['vendedor']}}</td>
                  </tr>
          </table>
          <table class="tabla">
                  <tr class="tabla_cabera">
                      <td width="60px" style="text-align:center;">CODIGO</td>
                      <td width="85px" style="text-align:center;">COMPROBANTE</td>
                      <td style="text-align:center;">CLIENTE</td>
                      <td style="text-align:center;">CAJERO</td>
                      <td width="130px" style="text-align:center;">FECHA DE VENTA</td>
                  </tr>
                  @foreach($value['detalle'] as $valuedetalle)
                  <tr>
                      <td class="tabla_subtitulo">{{ $valuedetalle['codigo'] }}</td>
                      <td class="tabla_subtitulo">{{ $valuedetalle['comprobante'] }}</td>
                      <td class="tabla_subtitulo">{{ $valuedetalle['cliente'] }}</td>
                      <td class="tabla_subtitulo">{{ $valuedetalle['cajero'] }}</td>
                      <td class="tabla_subtitulo" style="text-align:center;">{{ $valuedetalle['fechaconfirmacion'] }}</td>
                  </tr>
                  <tr>
                      <td style="text-align:left;padding:0px;" colspan="5">
                         <table class="tabla_informativa">
                            <tr>
                                <td class="tabla_titulo" style="text-align:center;width:1%;">NRO</td>
                                <td class="tabla_titulo" style="text-align:center;">PRODUCTO</td>
                                <td class="tabla_titulo" style="text-align:center;width:8%;">PRECIO</td>
                                <td class="tabla_titulo" style="text-align:center;width:8%;">CANT.</td>
                                <td class="tabla_titulo" style="text-align:center;width:8%;">TOTAL</td>
                            </tr>
                            @foreach($valuedetalle['detalleproducto'] as $valuedetalleproducto)
                            <tr>
                                <td style="text-align:center;">{{ $valuedetalleproducto['numero'] }}</td>
                                <td style="text-align:left;">{{ $valuedetalleproducto['producto'] }}</td>
                                <td style="text-align:right;">{{ $valuedetalleproducto['precio'] }}</td>
                                <td style="text-align:right;">{{ $valuedetalleproducto['cantidad'] }}</td>
                                <td style="text-align:right;">{{ $valuedetalleproducto['total'] }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td style="text-align:right;font-weight: bold;" colspan="4">TOTAL</td>
                                <td style="text-align:right;">{{ $valuedetalle['total'] }}</td>
                            </tr>
                        </table>
                      </td>
                  </tr>
                  @endforeach
          </table>
          <table class="tabla">
                  <tr class="tabla_cabera">
                      <td style="text-align:right;font-weight: bold;" colspan="4">TOTAL</td>
                      <td style="text-align:right;;width:8%;">{{ $total }}</td>
                  </tr>
          </table>
          <div class="espacio"></div>
          @endforeach
        @elseif($listarpor==4)
          @foreach($ventas as $value)
          <table class="tabla">
                  <tr class="tabla_cabera">
                      <td style="text-align:center;">CAJERO: {{$value['cajero_identificacion']}} - {{$value['cajero']}}</td>
                  </tr>
          </table>
          <table class="tabla">
                  <tr class="tabla_cabera">
                      <td width="60px" style="text-align:center;">CODIGO</td>
                      <td width="85px" style="text-align:center;">COMPROBANTE</td>
                      <td style="text-align:center;">CLIENTE</td>
                      <td style="text-align:center;">VENDEDOR</td>
                      <td width="130px" style="text-align:center;">FECHA DE VENTA</td>
                  </tr>
                  @foreach($value['detalle'] as $valuedetalle)
                  <tr>
                      <td class="tabla_subtitulo">{{ $valuedetalle['codigo'] }}</td>
                      <td class="tabla_subtitulo">{{ $valuedetalle['comprobante'] }}</td>
                      <td class="tabla_subtitulo">{{ $valuedetalle['cliente'] }}</td>
                      <td class="tabla_subtitulo">{{ $valuedetalle['vendedor'] }}</td>
                      <td class="tabla_subtitulo" style="text-align:center;">{{ $valuedetalle['fechaconfirmacion'] }}</td>
                  </tr>
                  <tr>
                      <td style="text-align:left;padding:0px;" colspan="5">
                         <table class="tabla_informativa">
                            <tr>
                                <td class="tabla_titulo" style="text-align:center;width:1%;">NRO</td>
                                <td class="tabla_titulo" style="text-align:center;">PRODUCTO</td>
                                <td class="tabla_titulo" style="text-align:center;width:8%;">PRECIO</td>
                                <td class="tabla_titulo" style="text-align:center;width:8%;">CANT.</td>
                                <td class="tabla_titulo" style="text-align:center;width:8%;">TOTAL</td>
                            </tr>
                            @foreach($valuedetalle['detalleproducto'] as $valuedetalleproducto)
                            <tr>
                                <td style="text-align:center;">{{ $valuedetalleproducto['numero'] }}</td>
                                <td style="text-align:left;">{{ $valuedetalleproducto['producto'] }}</td>
                                <td style="text-align:right;">{{ $valuedetalleproducto['precio'] }}</td>
                                <td style="text-align:right;">{{ $valuedetalleproducto['cantidad'] }}</td>
                                <td style="text-align:right;">{{ $valuedetalleproducto['total'] }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td style="text-align:right;font-weight: bold;" colspan="4">TOTAL</td>
                                <td style="text-align:right;">{{ $valuedetalle['total'] }}</td>
                            </tr>
                        </table>
                      </td>
                  </tr>
                  @endforeach
          </table>
          <table class="tabla">
                  <tr class="tabla_cabera">
                      <td style="text-align:right;font-weight: bold;" colspan="4">TOTAL</td>
                      <td style="text-align:right;;width:8%;">{{ $total }}</td>
                  </tr>
          </table>
          <div class="espacio"></div>
          @endforeach
        @endif
    </div>
</body>
</html>