<!DOCTYPE html>
<html>
<head>
  <title>Ticket</title>
  <style>
    html, body {
      margin: 0px;
      padding: 15px;
      font-size: 12px;
      font-weight: bold;
      font-family: Courier;
    }
    .contenedor {
      width: <?php echo  $configuracion['venta_anchoticket']!=null?($configuracion['venta_anchoticket']>0?($configuracion['venta_anchoticket']-1):'6.62'):'6.62' ?>cm;
      text-align: center;
    }
    .table {
      width: 100%;
      margin:0px;
      padding:0px;
      font-size: 11px;
    }
    .nombrecomercial {
      font-size: 15px;
      margin-top:-10px;
    }
    .datofinal {
      text-align: center;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="contenedor">
    @if($agencia != '')
      @if($agencia->logo != '')
      <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$agencia->logo) }}" height="60px">
      @endif
      <div class="nombrecomercial">{{ strtoupper($agencia->nombrecomercial) }}</div>
      RUC: {{ $agencia->ruc }}<br>
      {{ strtoupper($agencia->direccion) }}<br>
      {{ strtoupper($agencia->ubigeonombre) }}<br><br>
    @else
      <div class="nombrecomercial"> {{ strtoupper($tienda->nombre) }}</div>
      {{ strtoupper($tienda->direccion) }}<br><br>
    @endif
    <table>
      <thead>
        <tr>
          <td>FECHA</td>
          <td>: {{ $cobranza->fecharegistro }}</td>
        </tr>
        <tr>
          <td>AGENCIA</td>
          <td>: {{ $cobranza->agencia_nombre }}</td>
        </tr>
        <tr>
          <td>APELLIDOS</td>
          <td>: {{ $cobranza->cliente_apellidos }}</td>
        </tr>
        <tr>
          <td>NOMBRES</td>
          <td>: {{ $cobranza->cliente_nombre }}</td>
        </tr>
        <tr>
          <td>DNI</td>
          <td>: {{ $cobranza->cliente_identificacion }}</td>
        </tr>
        <tr>
          <td>CODIGO</td>
          <td>: {{ $cobranza->codigo }}</td>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
    <table class="table" style="margin-top:5px;">
      <thead>
        <tr>
          <td colspan="4" style="text-align: center; height:10px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
        </tr>
        <tr>
          <th style="white-space: nowrap;text-align: center;">N°</th>
          <th style="white-space: nowrap;text-align: center;">Cuota</th>
          <th style="white-space: nowrap;text-align: center;">Mora</th>
          <th style="white-space: nowrap;text-align: center;">Total</th>
        </tr>
        <tr>
          <td colspan="4" style="text-align: center; height:10px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
        </tr>
      </thead>
      <tbody>
        @foreach ($cobranzadetalle as $value)
          <?php
            $credito = DB::table('s_prestamo_creditodetalle')
              ->whereId($value->idprestamo_creditodetalle)
              ->first();
          ?>
          <tr>
            <td style="white-space: nowrap;text-align: center;">{{ $credito->numero }}</td>
            <td style="white-space: nowrap;text-align: center;">{{ $credito->cuota }}</td>
            <td style="white-space: nowrap;text-align: center;">{{ $credito->moraapagar }}</td>
            <td style="white-space: nowrap;text-align: center;">{{ $credito->total }}</td>
          </tr>
        @endforeach
        <tr>
          <td colspan="4" style="text-align: center; height:10px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
        </tr>
        <tr>
          <td colspan="3" style="text-align: right;">TOTAL CUOTA:</td>
          <td style="white-space: nowrap; text-align: center;">
            {{ $cobranza->total_cuota }}
          </td>
        </tr>
        <tr>
          <td colspan="3" style="text-align: right;">TOTAL MORA:</td>
          <td style="white-space: nowrap; text-align: center;">
            {{ $cobranza->total_mora }}
          </td>
        </tr>
        <tr>
          <td colspan="3" style="text-align: right;">TOTAL:</td>
          <td style="white-space: nowrap; text-align: center;">
            {{ $cobranza->total_cuotaapagar }}
          </td>
        </tr>
      </tbody>
    </table>
    <div class="datofinal">
      ¡Muchas Gracias!
    </div>
  </div>
</body>
</html>