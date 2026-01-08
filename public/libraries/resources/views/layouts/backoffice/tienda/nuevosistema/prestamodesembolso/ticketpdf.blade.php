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
      .firma {
          margin-top:60px;
          text-align: center;
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
            </thead>
            <tbody>
                <tr>
                    <td>CRÉDITO</td>
                    <td>: {{ $prestamodesembolso->codigo }}</td>
                </tr>
                <tr>
                    <td>FECHA</td>
                    <td>: {{ date_format(date_create($prestamodesembolso->fechadesembolsado),"d/m/Y") }}</td>
                </tr>
                <tr>
                    <td>HORA</td>
                    <td>: {{ date_format(date_create($prestamodesembolso->fechadesembolsado),"h:i:s A") }}</td>
                </tr>
                <tr>
                    <td>DNI</td>
                    <td>: {{ $prestamodesembolso->cliente_identificacion }}</td>
                </tr>
                <tr>
                    <td>NOMBRE</td>
                    <td>: {{ $prestamodesembolso->cliente }}</td>
                </tr>
                <tr>
                    <td>APELLIDOS</td>
                    <td>: {{ $prestamodesembolso->cliente_apellido }}</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>MONEDA</td>
                    <td>: {{ $prestamodesembolso->monedanombre }}</td>
                </tr>
                <tr>
                    <td>MONTO</td>
                    <td>: {{ $prestamodesembolso->monedasimbolo }} {{ $prestamodesembolso->monto }}</td>
                </tr>
                <tr>
                    <td>Nº DE CUOTAS</td>
                    <td>: {{ $prestamodesembolso->numerocuota }}</td>
                </tr>
                <tr>
                    <td>FRECUENCIA</td>
                    <td>: {{ $prestamodesembolso->frecuencia_nombre }}</td>
                </tr>
                <tr>
                    <td>ASESOR</td>
                    <td>: {{ $prestamodesembolso->asesor_nombre }}</td>
                </tr>
                <tr>
                    <td>VENTANILLA</td>
                    <td>: {{ $prestamodesembolso->cajero_nombre }}</td>
                </tr>
            </tbody>
        </table>        
        <div class="datofinal">
            Recibi conforme el calendario de pagos aprobado por el credito desembolsado.
        </div>    
        <div class="firma">
            -----------------------<br>
          {{$prestamodesembolso->cliente}}<br>
          {{$prestamodesembolso->cliente_apellido}}
        </div>
    </div>
</body>
</html>