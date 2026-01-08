<!DOCTYPE html>
<html>
<head>
  <title>INFORMACIÓN DE DOMICILIO</title>
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
    <style>
      .imagen {
          background-repeat: no-repeat;
          background-size: contain;
          background-position: center;
          width: 340px;
          height: 190px;
      }
    </style>
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
              <td colspan="2">FOTOGRAFIAS DE DOMICILIO</td>
            </tr>
            <tr>
              <td class="tabla_titulo">IMAGEN 1</td>
              <td class="tabla_titulo">IMAGEN 2</td>
            </tr>
            <tr>
              <td style="text-align:center;height:190px;">
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
              <td class="tabla_titulo">IMAGEN 3</td>
              <td class="tabla_titulo">IMAGEN 4</td>
            </tr>
            <tr>
              <td style="text-align:center;height:190px;">
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
            <tr>
              <td class="tabla_titulo">IMAGEN 5</td>
              <td class="tabla_titulo">IMAGEN 6</td>
            </tr>
            <tr>
              <td style="text-align:center;height:190px;">
                @if($prestamodomicilioimagen5!='')
                    <div class="imagen" style="background-image: url({{ url('public/backoffice/tienda/'.$tienda->id.'/creditodomicilio/'.$prestamodomicilioimagen5->imagen) }});"></div>
                @endif
              </td>
              <td style="text-align:center;">
                @if($prestamodomicilioimagen6!='')
                    <div class="imagen" style="background-image: url({{ url('public/backoffice/tienda/'.$tienda->id.'/creditodomicilio/'.$prestamodomicilioimagen6->imagen) }});"></div>
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