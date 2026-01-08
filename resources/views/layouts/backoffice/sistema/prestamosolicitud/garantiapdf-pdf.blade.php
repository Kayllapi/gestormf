<!DOCTYPE html>
<html>
<head>
    <title>GARANTIAS</title>
    @include('app.pdf_style',['idtienda'=>$tienda->id])
    <style>
      .imagen {
          background-repeat: no-repeat;
          background-size: contain;
          background-position: center;
          width: 224px;
          height: 207px;
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
    <div class="titulo">GARANTIAS</div>
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
        <?php $num = 1; ?>
        @foreach($prestamobien1 as $value)
        <table class="tabla">
            <tr class="tabla_cabera">
              <td colspan="3">GARANTIA {{$num}}: {{$value->producto}} / {{$prestamocredito->monedasimbolo}} {{$value->valorestimado}} / @if($value->idprestamo_documento==1)
                SIN DOCUMENTOS
            @elseif($value->idprestamo_documento==2)
                COPIA/LEGALIZADO
            @elseif($value->idprestamo_documento==3)
                ORIGINAL
            @endif</td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="3">
                <?php $result = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[A-Z0-9+&@#\/%=~_|]/i', '<a href="\0" target="_blank">Ir Enlace</a>', $value->descripcion); ?>
                <?php echo $result ?>
              </td>
            </tr>
            <tr>
              
              <?php
              $prestamobienimagen = DB::table('s_prestamo_creditobienimagen')
                  ->where('s_prestamo_creditobienimagen.idprestamo_creditobien', $value->id)
                  ->where('s_prestamo_creditobienimagen.idtienda',  $tienda->id)
                  ->limit(3)
                  ->orderBy('s_prestamo_creditobienimagen.id','asc')
                  ->get();
              ?> 
              <?php $subnum = 1; ?>
              @foreach($prestamobienimagen as $valueimagen)
              <td style="text-align:center;height:207px;">
                    <div class="imagen" style="background-image: url({{ url('public/backoffice/tienda/'.$tienda->id.'/creditobien/'.$valueimagen->imagen) }});"></div>
              </td>
              <?php $subnum++; ?>
              @endforeach
              @for($ii=$subnum; $ii<=3; $ii++)
              <td style="text-align:center;height:207px;"></td>
              @endfor
            </tr>
            <tr>
              <?php
              $prestamobienimagen = DB::table('s_prestamo_creditobienimagen')
                  ->where('s_prestamo_creditobienimagen.idprestamo_creditobien', $value->id)
                  ->where('s_prestamo_creditobienimagen.idtienda',  $tienda->id)
                  ->offset(3)
                  ->limit(3)
                  ->orderBy('s_prestamo_creditobienimagen.id','asc')
                  ->get();
              ?> 
              <?php $subnum = 1; ?>
              @foreach($prestamobienimagen as $valueimagen)
              <td style="text-align:center;height:207px;">
                    <div class="imagen" style="background-image: url({{ url('public/backoffice/tienda/'.$tienda->id.'/creditobien/'.$valueimagen->imagen) }});"></div>
              </td>
              <?php $subnum++; ?>
              @endforeach
              @for($ii=$subnum; $ii<=3; $ii++)
              <td style="text-align:center;height:207px;"></td>
              @endfor
            </tr>
        </table>
        <?php $num++; ?>
        @endforeach
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