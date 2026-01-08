<!DOCTYPE html>
<html>
<head>
    <title>TICKET DE GASTO ADMINISTRATIVO</title>
    @include('app.pdf_style',['idtienda'=>$tienda->id])
</head>
<body>
    <div class="ticket_contenedor">
      <div class="contenedor">
          @include('app.pdf_headerticket',[
              'logo'=>$tienda->imagen,
              'nombrecomercial'=>$prestamodesembolso->facturacion_agencianombrecomercial,
              'ruc'=>$prestamodesembolso->facturacion_agenciaruc,
              'direccion'=>$prestamodesembolso->facturacion_agenciadireccion,
              'ubigeo'=>$prestamodesembolso->facturacion_agenciaubigeonombre,
              'tienda'=>$tienda,
          ])
          <table class="tabla_informativa">
              <tr>
                  <td class="tabla_informativa_subtitulo">FECHA</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{ date_format(date_create($prestamodesembolso->fechadesembolsado),"d/m/Y h:i A") }}</td>
              </tr>
              <tr>
                  <td class="tabla_informativa_subtitulo" style="width:75px;">CRÉDITO</td>
                  <td class="tabla_informativa_punto" style="width:5px;">:</td>
                  <td class="tabla_informativa_descripcion">{{ str_pad($prestamodesembolso->codigo, 8, "0", STR_PAD_LEFT) }}</td>
              </tr>
              <tr>
                  <td class="tabla_informativa_subtitulo">DNI</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{ $prestamodesembolso->facturacion_cliente_identificacion }}</td>
              </tr>
              <tr>
                  <td class="tabla_informativa_subtitulo">NOMBRE</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{ $prestamodesembolso->facturacion_cliente_nombre }}</td>
              </tr>
              <tr>
                  <td class="tabla_informativa_subtitulo">APELLIDOS</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{ $prestamodesembolso->facturacion_cliente_apellidos }}</td>
              </tr>
              <tr>
                  <td class="tabla_informativa_subtitulo">VENTANILLA</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{ $prestamodesembolso->cajero_nombre }}</td>
              </tr>
              <tr>
                  <td class="tabla_informativa_subtitulo">G. ADTVO.</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{ $prestamodesembolso->monedasimbolo }} {{ $prestamodesembolso->facturacion_montorecibido-$prestamodesembolso->facturacion_vuelto }}</td>
              </tr>
          </table>  
      </div>
    </div>
</body>
</html>