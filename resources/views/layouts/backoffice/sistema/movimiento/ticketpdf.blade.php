<!DOCTYPE html>
<html>
<head>
    <title>TICKET DE MOVIMIENTO</title>
    @include('app.pdf_style',['idtienda'=>$tienda->id])
</head>
<body>
    <div class="ticket_contenedor">
      <div class="contenedor">
          @include('app.pdf_headerticket',[
              'logo'=>$tienda->imagen,
              'nombrecomercial'=>$tienda->nombre,
              'direccion'=>$tienda->direccion,
              'ubigeo'=>$tienda->ubigeonombre,
              'tienda'=>$tienda,
          ])
          <table class="tabla_informativa">
              <tr>
                <td class="tabla_informativa_subtitulo">FECHA</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ date_format(date_create($s_movimiento->fecharegistro), "d/m/Y h:i A") }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo" style="width:70px;">CÓDIGO</td>
                <td class="tabla_informativa_punto" style="width:5px;">:</td>
                <td class="tabla_informativa_descripcion">{{ str_pad($s_movimiento->codigo, 8, "0", STR_PAD_LEFT) }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">CAJERO(A)</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $s_movimiento->responsableapellidos }}, {{ $s_movimiento->responsablenombre }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">ENTREGADO(A)</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{$s_movimiento->responsableentregadoapellidos}}, {{$s_movimiento->responsableentregadonombre}}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">MONTO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $s_movimiento->monedasimbolo }} {{ $s_movimiento->monto }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">TIPO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $s_movimiento->tiponombre }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">CONCEPTO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $s_movimiento->conceptonombre }}</td>
              </tr>
              <tr>
                <td class="tabla_informativa_subtitulo">DESCRIPCIÓN</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $s_movimiento->concepto }}</td>
              </tr>
          </table>
      </div>
    </body>
</html>