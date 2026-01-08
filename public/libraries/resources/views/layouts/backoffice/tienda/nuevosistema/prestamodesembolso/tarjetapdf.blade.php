<!DOCTYPE html>
<html>
<head>
    <title>Tarjeta de Pago</title>
    <style>
      html, body {
          margin: 0px;
          padding: 15px;
          font-size: 11px;
          font-weight: bold;
          font-family: Courier;
      }
      .contenedor {
        text-align: center;
        width: 400px;
      }
      .table {
          width: 100%;
          margin:0px;
          padding:0px;
          font-size: 11px;
      }
      .tablatarjeta {
          width: 100%;
          margin:0px;
          padding:0px;
          font-size: 11px;
      }
      .tablatarjeta, .tablatarjeta th, .tablatarjeta td {
          border: 1px solid black;
          border-collapse: collapse;
          padding:5px;
          text-align:right;
      }
      .nombrecomercial {
          font-size: 15px;
          margin-top:-10px;
      }
    </style>
</head>
<body>
    <div class="contenedor">
        @if($facturacion->agenciaruc != '')
            @if($facturacion->agencialogo != '')
            <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$facturacion->agencialogo) }}" height="60px">
            @endif
            <div class="nombrecomercial">{{ strtoupper($facturacion->agencianombrecomercial) }}</div>
            RUC: {{ $facturacion->agenciaruc }}<br>
            {{ strtoupper($facturacion->agenciadireccion) }}<br>
            {{ strtoupper($facturacion->agenciaubigeonombre) }}<br><br>
        @else
            <div class="nombrecomercial"> {{ strtoupper($tienda->nombre) }}</div>
            {{ strtoupper($tienda->direccion) }}<br><br>
        @endif
        <table>
            <thead>
                <tr>
                    <td>F. DESEMBOLSO</td>
                    <td>: {{ date_format(date_create($prestamodesembolso->fechadesembolsado),"d/m/Y h:i:s A") }}</td>
                </tr>
                <tr>
                    <td>CLIENTE</td>
                    <td>: {{ $prestamodesembolso->cliente_nombre }}</td>
                </tr>
                <tr>
                    <td>Nº CRÉDITO</td>
                    <td>: {{ $prestamodesembolso->codigo }}</td>
                </tr>
                <tr>
                    <td>TELF. OFICINA</td>
                    <td>: 987 654 321</td>
                </tr>
                <tr>
                    <td>MONEDA</td>
                    <td>: {{ $prestamodesembolso->monedanombre }}</td>
                </tr>
                <tr>
                    <td>DESEMBOLSO</td>
                    <td>: {{ $prestamodesembolso->monedasimbolo }} {{ $prestamodesembolso->monto }}</td>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <table class="tablatarjeta">
            <thead>
                <tr>
                    <td>Nº</td>
                    <td>Fecha de Pago</td>
                    <td>Cuota</td>
                    <td>F.Cancelado</td>
                    <td>Pago</td>
                    <td>Saldo</td>
                    <td>Firma</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($prestamodesembolsodetalle as $value)
                <tr>
                    <td>{{ $value->numero }}</td>
                    <td>{{ date_format(date_create($value->fechavencimiento),"d/m/Y") }}</td>
                    <td>{{ $value->cuota }}</td>
                    <td></td>
                    <td></td>
                    <td>{{ number_format($value->saldocapital + $value->interes, 2, '.', '') }}</td>
                    <td></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>