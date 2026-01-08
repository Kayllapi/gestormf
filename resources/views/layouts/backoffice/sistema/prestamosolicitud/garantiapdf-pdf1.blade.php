<!DOCTYPE html>
<html>
<head>
  <title>GARANTIAS</title>
  <style>
      html, body {
          margin: 0px;
          font-size: 9px;
          font-weight: bold;
          font-family: Courier;
      }
      .contenedor {
          padding: 20px;
          padding-top: 40px;
          padding-bottom: 40px;
      }
      .table-cabecera {
        width: 100%;
        margin:0px;
        padding:0px;
      }
      .table-cabecera td {
        padding: 0px;
      }
      .table {
        width: 100%;
        margin:0px;
        padding:0px;
        border-collapse: collapse;
        border-spacing: 0;
      }
      .table td {
        border:1px solid #ccc;
        padding: 5px;
      }
      .b-primary {
        background-color: {{$tienda->ecommerce_color}};
        color:white;
      }
      .b-primary td {
        border:1px solid {{$tienda->ecommerce_color}};
      }
      .b-titulo-master {
        height:20px;
        font-size:15px;
        text-align:center;
        margin-bottom:10px;
      }
      .b-titulo {
        height:15px;
        font-size:10px;
        text-align:left;
        padding:5px;
      }
      .b-subtitulo {
        background-color: #eae7e7;
      }
      .tienda-nombre {
          font-size:12px;
          margin-bottom:3px;
      }
      .imagen {
          background-repeat: no-repeat;
          background-size: contain;
          background-position: center;
          height:207px;
          width:240px;
      }
  </style>
</head>
<body>
    <div class="contenedor">
        <div class="b-titulo-master">GARANTIAS</div>
        <table class="table-cabecera" style="margin-bottom:10px;">
            <tr>
                <td rowspan="3" style="width:80px;text-align:center;border-left:3px solid #31353d;height:45.5px;">
                    <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/logo/'.$tienda->imagen) }}" height="40px">
                </td>
                <td rowspan="3" style="width:270px;padding-left:5px;">
                    <div class="tienda-nombre">{{ strtoupper($tienda->nombre) }}</div>
                    {{ strtoupper($tienda->direccion) }}<br>
                    {{ strtoupper($tienda->ubigeonombre) }}
                </td>
                <td style="width:50px;border-left:3px solid #31353d;padding-left:5px;">FECHA</td>
                <td>: {{ date_format(date_create($prestamocredito->fecharegistro), "d/m/Y") }}</td>
            </tr>
            <tr>
                <td style="border-left:3px solid #31353d;padding-left:5px;">CLIENTE</td>
                <td>: {{$prestamocredito->clienteidentificacion}} - {{$prestamocredito->clienteapellidos}}, {{$prestamocredito->clientenombre}}</td>
            </tr>
            <tr>
                <td style="border-left:3px solid #31353d;padding-left:5px;">DIRECCIÓN</td>
                <td>: <?php echo $prestamocredito->clientedireccion!=''?$prestamocredito->clientedireccion:'&nbsp;' ?></td>
            </tr>
        </table>
        <?php $num = 1; ?>
        @foreach($prestamobien1 as $value)
        <table class="table">
            <tr class="b-primary">
              <td class="b-titulo" colspan="3">GARANTIA {{$num}}: {{$value->producto}} / {{$prestamocredito->monedasimbolo}} {{$value->valorestimado}} / @if($value->idprestamo_documento==1)
                SIN DOCUMENTOS
            @elseif($value->idprestamo_documento==2)
                COPIA/LEGALIZADO
            @elseif($value->idprestamo_documento==3)
                ORIGINAL
            @endif</td>
            </tr>
            <tr>
              <td class="b-subtitulo" colspan="3">{{$value->descripcion}}</td>
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
        </table>
        <?php $num++; ?>
        @endforeach
        @for($i=$num; $i<=3; $i++)
        <table class="table">
            <tr class="b-primary">
              <td class="b-titulo" colspan="3">GARANTIA {{$num}}</td>
            </tr>
            <tr>
              <td class="b-subtitulo" colspan="3">---</td>
            </tr>
            <tr>
              <td style="text-align:center;height:207px;"></td>
              <td style="text-align:center;height:207px;"></td>
              <td style="text-align:center;height:207px;"></td>
            </tr>
        </table>
        <?php $num++; ?>
        @endfor
        <div style="height:90px;width:100%;"></div>
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
        <div style="height:40px;width:100%;"></div>
        
        
        @foreach($prestamobien2 as $value)
        <?php echo $num==4?'<div style="height:28px;width:100%;"></div>':'' ?>
        <table class="table">
            <tr class="b-primary">
              <td class="b-titulo" colspan="3">GARANTIA {{$num}}: {{$value->producto}} / {{$prestamocredito->monedasimbolo}} {{$value->valorestimado}} / @if($value->idprestamo_documento==1)
                SIN DOCUMENTOS
            @elseif($value->idprestamo_documento==2)
                COPIA/LEGALIZADO
            @elseif($value->idprestamo_documento==3)
                ORIGINAL
            @endif</td>
            </tr>
            <tr>
              <td class="b-subtitulo" colspan="3">{{$value->descripcion}}</td>
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
        </table>
        <?php $num++; ?>
        @endforeach
        @if(count($prestamobien2)>0)
        <div style="height:100px;width:100%;"></div>
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
        <div style="height:40px;width:100%;"></div>
        @endif

        @foreach($prestamobien3 as $value)
        <?php echo $num==7?'<div style="height:68px;width:100%;"></div>':'' ?>
        <table class="table">
            <tr class="b-primary">
              <td class="b-titulo" colspan="3">GARANTIA {{$num}}: {{$value->producto}} / {{$prestamocredito->monedasimbolo}} {{$value->valorestimado}} / @if($value->idprestamo_documento==1)
                SIN DOCUMENTOS
            @elseif($value->idprestamo_documento==2)
                COPIA/LEGALIZADO
            @elseif($value->idprestamo_documento==3)
                ORIGINAL
            @endif</td>
            </tr>
            <tr>
              <td class="b-subtitulo" colspan="3">{{$value->descripcion}}</td>
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
        </table>
        <?php $num++; ?>
        @endforeach
       
        @if(count($prestamobien3)>0)
        <div style="height:100px;width:100%;"></div>
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
        <div style="height:40px;width:100%;"></div>
        @endif
       
  </div>
</body>
</html>