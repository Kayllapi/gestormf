<!DOCTYPE html>
<html>
<head>
  <title>PDF Análisis Cualitativo</title>
  <style>
    html, body {
      margin: 0px;
      padding: 15px;
      font-size: 11px;
      font-weight: bold;
      font-family: Courier;
    }
    
    .table {
      width: 100%;
      margin:0px;
      padding:0px;
      border-collapse: collapse;
      border-spacing: 0;
      /* font-size: 11px; */
      /* border: none; */
    }
    .table td {
      border:1px solid #ccc;
      padding: 5px;
    }
    .b-primary {
      background-color: #008cea;
      color:white;
    }
    .b-titulo {
      height:20px;
    }
    .b-subtitulo {
      background-color: #eae7e7;
    }
    .text-left {
      text-align: left;
    }
  </style>
</head>
<body>
  <table class="table">
    <tbody>
      <tr>
        <td class="b-primary b-titulo" colspan="4" style="text-align: center;">Análisis Cualitativo</td>
        <td colspan="4" rowspan="2">
          <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$agencia->logo) }}" width="50px">
        </td>
      </tr>
      <tr>
        <td class="b-subtitulo">Fecha:</td>
        <td colspan="3">{{ $prestamocredito->fechainicio }}</td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="3">Apellidos y Nombres del Solicitante:</td>
        <td colspan="5">{{ $prestamocredito->cliente_nombre }}</td>
      </tr>

      <tr class="b-primary">
        <td width="10%">N°</td>
        <td width="30%">Preguntas</td>
        <td width="10%">Malo (1)</td>
        <td width="10%">Regular (2)</td>
        <td width="10%">Bueno (3)</td>
        <td width="30%" colspan="3">Resultado</td>
      </tr>
      <?php $count = 0; $tbueno = 0; $tregular = 0; $tmalo = 0; ?>
      @if ($cualitativodetalles != '[]')
        @foreach ($cualitativodetalles as $value)
        <?php $count++; $tbueno += $value->valorbueno; $tregular += $value->valorregular; $tmalo += $value->valormalo; ?>
        <tr>
          <td width="10%" style="padding:8px;background-color: #eae7e7;text-align: center;"><b>{{ $count }}</b></td>
          <td width="30%">{{ $value->nombre }}</td>
          <td width="10%">{{ $value->descripcion1 }}</td>
          <td width="10%">{{ $value->descripcion2 }}</td>
          <td width="10%">{{ $value->descripcion3 }}</td>
          <td width="10%" style="text-align: center;font-weight: bold;">{{ $value->valorbueno }}</td>
          <td width="10%" style="text-align: center;font-weight: bold;">{{ $value->valorregular }}</td>
          <td width="10%" style="text-align: center;font-weight: bold;">{{ $value->valormalo }}</td>
        </tr>
        @endforeach
      @else
        @foreach ($preguntas as $value)
        <?php $count++; ?>
        <tr count="{{ $count }}">
          <td width="10%" style="padding:8px;background-color: #eae7e7;text-align: center;"><b>{{ $count }}</b></td>
          <td width="30%">{{ $value->nombre }}</td>
          <td width="10%">{{ $value->descripcion1 }}</td>
          <td width="10%">{{ $value->descripcion2 }}</td>
          <td width="10%">{{ $value->descripcion3 }}</td>
          <td width="10%" style="text-align: center;font-weight: bold;"></td>
          <td width="10%" style="text-align: center;font-weight: bold;"></td>
          <td width="10%" style="text-align: center;font-weight: bold;"></td>
        </tr>
        @endforeach
      @endif
      <tr class="thead-dark">
        <th class="b-subtitulo" colspan="5" style="text-align: right;">SUBTOTAL</th>
        <th class="b-subtitulo">{{ $tbueno }}</th>
        <th class="b-subtitulo">{{ $tregular }}</th>
        <th class="b-subtitulo">{{ $tmalo }}</th>
      </tr>
      <tr class="thead-dark">
        <th class="b-subtitulo" colspan="5" style="text-align: right;">TOTAL</th>
        <th class="b-subtitulo" colspan="3" style="text-align: center;">{{ $tbueno + $tregular + $tmalo }}</th>
      </tr>

      <tr class="b-primary">
        <th class="b-titulo" colspan="8">2. Sustento de Analisita</th>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="4">Destino del Crédito</td>
        <td class="b-subtitulo" colspan="4">Comentario del Asesor de Negocio</td>
      </tr>
      <tr>
        <td colspan="4">{{ $cualitativo->destino ?? '' }}</td>
        <td colspan="4">{{ $cualitativo->comentario ?? '' }}</td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="4">Descripción del Crédito</td>
        <td class="b-subtitulo" colspan="4">Calificación Central de Riesgo</td>
      </tr>
      <tr>
        <td colspan="4">{{ $cualitativo->descripcion ?? '' }}</td>
        <td colspan="4">{{ $calificacion->nombre ?? '' }}</td>
      </tr>

      <tr class="b-primary">
        <th class="b-titulo" colspan="8">3. Sobre las Referencias</th>
      </tr>
      <?php $cref = 1; ?>
      @foreach ($relaciones as $value)
        <tr>
          <td class="b-subtitulo" rowspan="3" width="10%">Referencia {{ $cref }}</td>
          <td class="b-subtitulo" width="20%">DNI:</td>
          <td colspan="6" width="70%">{{ $value->identificacion_persona }}</td>
        </tr>
        <tr>
          <td class="b-subtitulo" width="20%">Nombre y Apellido:</td>
          <td colspan="6" width="70%">{{ $value->completo_persona }}</td>
        </tr>
        <tr>
          <td class="b-subtitulo" width="20%">Teléfono</td>
          <td colspan="2" width="20%">{{ $value->numerotelefono }}</td>
          <td class="b-subtitulo" colspan="2" width="20%">Parentesco</td>
          <td colspan="2" width="30%">{{ $value->nombre_tiporelacion }}</td>
        </tr>
      <?php $cref++; ?>
      @endforeach
    </tbody>
  </table>
</body>
</html>