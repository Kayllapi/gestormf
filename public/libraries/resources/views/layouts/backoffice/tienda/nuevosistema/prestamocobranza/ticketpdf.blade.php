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
      width: <?php echo  $configuracion_facturacion['anchoticket']!=null?($configuracion_facturacion['anchoticket']>0?($configuracion_facturacion['anchoticket']-1):'6.62'):'6.62' ?>cm;
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
    <table style="width:100%;">
        <tr>
          <td style="width:87px;">CÓDIGO <div style="float:right">:</div></td>
          <td>{{ $cobranza->codigo }}</td>
        </tr>
        <tr>
          <td>FECHA <div style="float:right">:</div></td>
          <td>{{ date_format(date_create($cobranza->fecharegistro), "d/m/Y") }}</td>
        </tr>
        <tr>
          <td>HORA <div style="float:right">:</div></td>
          <td>{{ date_format(date_create($cobranza->fecharegistro), "h:i:s A") }}</td>
        </tr>
        <tr>
          <td>DNI <div style="float:right">:</div></td>
          <td>{{ $cobranza->cliente_identificacion }}</td>
        </tr>
        <tr>
          <td>CLIENTE <div style="float:right">:</div></td>
          <td>{{ $cobranza->cliente }}</td>
        </tr>
        <tr>
          <td>ASESOR <div style="float:right">:</div></td>
          <td>{{ $cobranza->asesor_apellidos }}, {{ $cobranza->asesor_nombre }}</td>
        </tr>
        <tr>
          <td>VENTANILLA <div style="float:right">:</div></td>
          <td>{{ $cobranza->cajero_apellidos }}, {{ $cobranza->cajero_nombre }}</td>
        </tr>
        <tr>
          <td>CUOTAS <div style="float:right">:</div></td>
          <td>
               <?php $ic=''; ?>
               @foreach ($cobranzadetalle as $value)
                  @if($ic == 0)
                  {{ str_pad($value->numero, 2, "0", STR_PAD_LEFT) }}
                  @else
                  ,{{ str_pad($value->numero, 2, "0", STR_PAD_LEFT) }}
                  @endif
                <?php $ic++; ?>
               @endforeach
          </td>
        </tr>
        <tr>
          <td>PROX. VENC. <div style="float:right">:</div></td>
          <td>---</td>
        </tr>
    </table>
    <table style="width:100%;">
        <tr>
          <td colspan="2" style="text-align: center; height:10px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
        </tr>
        <tr>
          <td style="width:130px;">CUOTA <div style="float:right">:</div></td>
          <td>{{ $cobranza->monedasimbolo }} {{ $cobranza->select_cuota }}</td>
        </tr>
        <tr>
          <td>MORA<div style="float:right">:</div></td>
          <td>{{ $cobranza->monedasimbolo }} {{ $cobranza->select_moraapagar }}</td>
        </tr>
        @if($cobranza->select_moradescontado>0)
        <tr>
          <td>DESC. MORA (-)<div style="float:right">:</div></td>
          <td>{{ $cobranza->monedasimbolo }} {{ $cobranza->select_moradescontado }}</td>
        </tr>
        @endif
        <tr>
          <td>TOTAL PAGADO<div style="float:right">:</div></td>
          <td>{{ $cobranza->monedasimbolo }} {{ $cobranza->select_acuentacuotaapagar }}</td>
        </tr>
        <tr>
          <td>TOTAL REDONDEADO<div style="float:right">:</div></td>
          <td>{{ $cobranza->monedasimbolo }} {{ $cobranza->select_acuentacuotaapagarredondeado }}</td>
        </tr>
        <tr>
          <td colspan="2" style="text-align: center; height:10px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
        </tr>
    </table>
    <div class="datofinal">
      ¡Muchas Gracias!
    </div>
  </div>
</body>
</html>