<!DOCTYPE html>
<html>
<head>
    <title>Cronograma</title>
    <style>
      html, body {
          margin: 0px;
          padding: 15px;
          font-size: 12px;
          font-weight: bold;
          font-family: Courier;
      }
      .contenedor {
        text-align: center;
      }
      .table {
          width: 100%;
          margin:0px;
          padding:0px;
          font-size: 11px;
          border: none;
      }
      .tablacronograma {
          width: 100%;
          margin:0px;
          padding:0px;
          font-size: 11px;
      }
      .tablacronograma, .tablacronograma th, .tablacronograma td {
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
        <table class="table">
            <thead>
                <tr>
                    <td style="width:100px;">AGENCIA</td>
                    <td style="width:300px;">: {{ $facturacion->agencianombrecomercial }}</td>
                    <td style="width:100px;">COD.CRÉDITO</td>
                    <td style="width:300px;">: {{ $prestamodesembolso->codigo }}</td>
                </tr>
                <tr>
                    <td>DNI</td>
                    <td>: {{ $prestamodesembolso->cliente_identificacion }}</td>
                    <td>MONEDA</td>
                    <td>: {{ $prestamodesembolso->monedanombre }}</td>
                </tr>
                <tr>
                    <td>CLIENTE</td>
                    <td>: {{ $prestamodesembolso->cliente_nombre }}</td>
                    <td>M. DESEMBOLSO</td>
                    <td>: {{ $prestamodesembolso->monedasimbolo }} {{ $prestamodesembolso->monto }}</td>
                </tr>
                <tr>
                    <td>ASESOR</td>
                    <td>: {{ $prestamodesembolso->asesor_nombre }}</td>
                    <td>Nº CUOTAS</td>
                    <td>: {{ $prestamodesembolso->numerocuota }}</td>
                </tr>
                <tr>
                    <td>VENTANILLA</td>
                    <td>: {{ $prestamodesembolso->cajero_nombre }}</td>
                    <td>FRECUENCIA</td>
                    <td>: {{ $prestamodesembolso->frecuencia_nombre }}</td>
                </tr>
                <tr>
                    <td>F. DESEMBOLSO</td>
                    <td>: {{ date_format(date_create($prestamodesembolso->fechadesembolsado),"d/m/Y h:i:s A") }}</td>
                    <td>F. DE INICIO</td>
                    <td>: {{ date_format(date_create($prestamodesembolso->fechainicio),"d/m/Y") }}</td>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <table class="tablacronograma">
            <thead>
                <tr>
                    <td>Nº</td>
                    <td>F.Vecimiento</td>
                    <td>Capital</td>
                    <td>Interes</td>
                    <td>Cuota</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($prestamodesembolsodetalle as $value)
                <tr>
                    <td>{{ $value->numero }}</td>
                    <td>{{ $value->fechavencimiento }}</td>
                    <td>{{ $value->amortizacion }}</td>
                    <td>{{ $value->interes }}</td>
                    <td>{{ $value->cuota }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>