<!DOCTYPE html>
<html>
<head>
    <title>TICKET DE CONFIRMACIÓN</title>
    @include('app.pdf_style',['idtienda'=>$tienda->id])
</head>
<body>
    <div class="ticket_contenedor">
      <div class="contenedor">
          @include('app.pdf_headerticket',[
              'logo'=>$tienda->imagen,
              'nombrecomercial'=>$prestamoahorro->facturacion_agencianombrecomercial,
              'ruc'=>$prestamoahorro->facturacion_agenciaruc,
              'direccion'=>$prestamoahorro->facturacion_agenciadireccion,
              'ubigeo'=>$prestamoahorro->facturacion_agenciaubigeonombre,
              'tienda'=>$tienda,
          ])
          <table class="tabla_informativa">
              <tr>
                  <td class="tabla_informativa_subtitulo">FECHA</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{ date_format(date_create($prestamoahorro->fechaconfirmado),"d/m/Y h:i A") }}</td>
              </tr>
              <tr>
                  <td class="tabla_informativa_subtitulo" style="width:75px;">CÓDIGO</td>
                  <td class="tabla_informativa_punto" style="width:5px;">:</td>
                  <td class="tabla_informativa_descripcion">{{ str_pad($prestamoahorro->codigo, 8, "0", STR_PAD_LEFT) }}</td>
              </tr>
              <tr>
                  <td class="tabla_informativa_subtitulo">DNI</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{ $prestamoahorro->clienteidentificacion }}</td>
              </tr>
              <tr>
                  <td class="tabla_informativa_subtitulo">NOMBRE</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{ $prestamoahorro->clientenombre }}</td>
              </tr>
              <tr>
                  <td class="tabla_informativa_subtitulo">APELLIDOS</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{ $prestamoahorro->clienteapellidos }}</td>
              </tr>
              <tr>
                  <td class="tabla_informativa_subtitulo">AHORRO</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{ $prestamoahorro->tipoahorronombre }} {{$prestamoahorro->ahorrolibre_tiponombre!=''?'('.$prestamoahorro->ahorrolibre_tiponombre.')':''}}</td>
              </tr>
              @if($prestamoahorro->idprestamo_tipoahorro==1 or $prestamoahorro->idprestamo_tipoahorro==2)
              <tr>
                  <td class="tabla_informativa_subtitulo">CONFIRMADO</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{ $prestamoahorro->monedasimbolo }} {{ $prestamoahorro->monto }}</td>
              </tr>
              @if($prestamoahorro->idprestamo_tipoahorro==2)
              <tr>
                  <td class="tabla_informativa_subtitulo">Nº DE CUOTAS</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{ $prestamoahorro->numerocuota }} CUOTAS</td>
              </tr>
              <tr>
                  <td class="tabla_informativa_subtitulo">FRECUENCIA</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{ $prestamoahorro->frecuencia_nombre }}</td>
              </tr>
              @endif
              @endif
              <tr>
                  <td class="tabla_informativa_subtitulo">ASESOR</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{ $prestamoahorro->asesor_nombre }}</td>
              </tr>
              <tr>
                  <td class="tabla_informativa_subtitulo">VENTANILLA</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{ $prestamoahorro->cajero_nombre }}</td>
              </tr>
          </table>        
          <div class="espacio"></div>
          <div class="dato_adicional">
              ESTOY CONFORME CON LA APERTURA DE LA CUENTA DE AHORRO {{ $prestamoahorro->tipoahorronombre }}
          </div>    
          <div class="dato_firma">
            <div>____________________________</div>
            <div>{{$prestamoahorro->clientenombre.' '.$prestamoahorro->clienteapellidos}}</div>
            <div>DNI: {{$prestamoahorro->clienteidentificacion}}</div>
          </div>
      </div>
    </div>
</body>
</html>