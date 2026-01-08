<!DOCTYPE html>
<html>
<head>
  <title>INFORMACIÓN DE DOMICILIO</title>
    @include('app.pdf_style',['idtienda'=>$tienda->id])
    <style>
      .imagen {
          background-repeat: no-repeat;
          background-size: contain;
          background-position: center;
          width: 340px;
          height: 410px;
          /*background-color:red;*/
      }
      .imagen_croquis {
          background-repeat: no-repeat;
          background-size: contain;
          background-position: center;
          width: 687px;
          height: 380px;
      }
      .imagen_suministro {
          background-repeat: no-repeat;
          background-size: contain;
          background-position: center;
          width: 340px;
          height: 380px;
      }
      .imagen_fachada {
          background-repeat: no-repeat;
          background-size: contain;
          background-position: center;
          width: 340px;
          height: 380px;
      }
    </style>
</head>
<body>
    @include('app.pdf_headerfooter',[
        'logo'=>$tienda->imagen,
        'nombrecomercial'=>$tienda->nombre,
        'direccion'=>$tienda->direccion,
        'ubigeo'=>$tienda->ubigeonombre,
        'tienda'=>$tienda,
    ])
    <div class="titulo">INFORMACIÓN DE DOMICILIO</div>

    <div class="content">
        <table class="tabla_informativa">
            <tr>
                <td style="width:7%;">CLIENTE</td>
                <td style="width:1%;">:</td>
                <td style="width:62%;">{{$prestamocredito->clienteidentificacion}} - {{$prestamocredito->clienteapellidos}}, {{$prestamocredito->clientenombre}}</td>
                <td style="width:7%;">FECHA</td>
                <td style="width:1%;">:</td>
                <td style="width:22%;">{{ date_format(date_create($prestamocredito->fecharegistro), "d/m/Y") }}</td>
            </tr>
            <tr>
                <td>DIRECCIÓN</td>
                <td colspan="5">: <?php echo $prestamocredito->clientedireccion!=''?$prestamocredito->clientedireccion:'&nbsp;' ?></td>
            </tr>
        </table>
        <div class="espacio"></div>
        <table class="tabla">
            <tr class="tabla_cabera">
              <td colspan="2" style="text-align:center;">FOTOGRAFIAS DE DOMICILIO</td>
            </tr>
            <tr>
              <td class="tabla_titulo" style="text-align:center;">IMAGEN 1</td>
              <td class="tabla_titulo" style="text-align:center;">IMAGEN 2</td>
            </tr>
            <tr>
              <td style="text-align:center;height:410px;">
                @if($prestamodomicilioimagen1!='')
                    <div class="imagen" style="background-image: url({{ url('public/backoffice/tienda/'.$tienda->id.'/creditodomicilio/'.$prestamodomicilioimagen1->imagen) }});"></div>
                @endif
              </td>
              <td style="text-align:center;">
                @if($prestamodomicilioimagen2!='')
                    <div class="imagen" style="background-image: url({{ url('public/backoffice/tienda/'.$tienda->id.'/creditodomicilio/'.$prestamodomicilioimagen2->imagen) }});"></div>
                @endif
              </td>
            </tr>
            <tr>
              <td class="tabla_titulo" style="text-align:center;">IMAGEN 3</td>
              <td class="tabla_titulo" style="text-align:center;">IMAGEN 4</td>
            </tr>
            <tr>
              <td style="text-align:center;height:410px;">
                @if($prestamodomicilioimagen3!='')
                    <div class="imagen" style="background-image: url({{ url('public/backoffice/tienda/'.$tienda->id.'/creditodomicilio/'.$prestamodomicilioimagen3->imagen) }});"></div>
                @endif
              </td>
              <td style="text-align:center;">
                @if($prestamodomicilioimagen4!='')
                    <div class="imagen" style="background-image: url({{ url('public/backoffice/tienda/'.$tienda->id.'/creditodomicilio/'.$prestamodomicilioimagen4->imagen) }});"></div>
                @endif
              </td>
            </tr>
        </table>
        <div class="espacio"></div>
        <div class="espacio"></div>
        <div class="espacio"></div>
        <table class="tabla">
            <tr class="tabla_cabera">
              <td style="text-align:center;">FOTOGRAFIAS DE DOMICILIO</td>
            </tr>
            <tr>
              <td class="tabla_titulo" style="text-align:center;">CROQUIS</td>
            </tr>
            <tr>
              <td style="text-align:center;height:380px;">
            

                
                  @if($prestamodomicilio!='')
                    @if($prestamodomicilio->mapa_latitud!='' && $prestamodomicilio->mapa_longitud!='')
                    <img class="imagen_croquis" src="http://maps.googleapis.com/maps/api/staticmap?center={{$prestamodomicilio->mapa_latitud}},{{$prestamodomicilio->mapa_longitud}}&zoom=16&scale=false&size=640x353&maptype=roadmap&format=png&visual_refresh=true&markers=icon:{{url('public/backoffice/sistema/marker.png')}}|size:mid|color:red|label:|{{$prestamodomicilio->mapa_latitud}},{{$prestamodomicilio->mapa_longitud}}&key=AIzaSyAyFtw-yEfh6GtiPyx_4ZQWt3g_vUCu5eQ"/>

                    @endif
                  @endif
              </td>
            </tr>
        </table>
        <table class="tabla">
            <tr>
              <td class="tabla_titulo" style="text-align:center;width:50%;">SUMINISTRO</td>
              <td class="tabla_titulo" style="text-align:center;">FACHADA</td>
            </tr>
            <tr>
              <td style="text-align:center;height:380px;">
                  @if($prestamodomicilio!='')
                    <div class="imagen_suministro" style="background-image: url({{ url('public/backoffice/tienda/'.$tienda->id.'/creditodomicilio/'.$prestamodomicilio->imagensuministro) }});"></div>
                  @endif
              </td>
              <td style="text-align:center;">
                  @if($prestamodomicilio!='')
                    <div class="imagen_fachada" style="background-image: url({{ url('public/backoffice/tienda/'.$tienda->id.'/creditodomicilio/'.$prestamodomicilio->imagenfachada) }});"></div>
                  @endif
              </td>
            </tr>
        </table>
        <div class="espacio"></div>
        <div class="dato_firma">
        <table style="width:100%;">
            <tr>
              <td style="text-align:center;"> 
                ____________________________<br>
                FIRMA DEL CLIENTE
                <div style="text-align:center;">
                DNI: {{$prestamocredito->clienteidentificacion}}<br>
                {{$prestamocredito->clientenombre}}, {{$prestamocredito->clienteapellidos}}
                </div>
              </td>
              <td style="text-align:center;"> 
                ____________________________<br>
                FIRMA DEL ASESOR
                <div style="text-align:center;">
                DNI: {{$prestamocredito->asesoridentificacion}}<br>
                {{$prestamocredito->asesornombre}}, {{$prestamocredito->asesorapellidos}}
                </div>
              </td>
            </tr>
        </table>
        </div>
  </div>
</body>
</html>