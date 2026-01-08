<!DOCTYPE html>
<html>
<head>
    <title>Cronograma</title>
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
      .tablacronograma {
          width: 100%;
          margin:0px;
          padding:0px;
      }
      .tablacronograma, .tablacronograma th, .tablacronograma td {
          border: 1px solid #ccc;
          border-collapse: collapse;
          padding:5px;
          text-align:right;
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
      .tienda-nombre {
          font-size:12px;
          margin-bottom:3px;
      }
      .imagen-logo {
          background-repeat: no-repeat;
          background-size: contain;
          background-position: center;
          height:60px;
          width:100%;
      }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="b-titulo-master">CRONOGRAMA DE CRÉDITO</div>
        <table class="table-cabecera" style="margin-bottom:10px;">
            <tr>
                <td rowspan="5" style="width:120px;text-align:center;border-left:3px solid #31353d;padding:0px;padding-left:3px;">
                    <div class="imagen-logo" style="background-image: url({{ url('public/backoffice/tienda/'.$tienda->id.'/logo/'.$tienda->imagen) }});"></div>
                </td>
                <td rowspan="5" style="width:200px;padding-left:5px;">
                    <div class="tienda-nombre">{{ strtoupper($tienda->nombre) }}</div>
                    {{ strtoupper($tienda->direccion) }}<br>
                    {{ strtoupper($tienda->ubigeonombre) }}
                </td>
                <td style="padding-left:5px;border-left:3px solid #31353d;width:75px;">DNI</td>
                <td>: {{ $prestamodesembolso->facturacion_cliente_identificacion }}</td>
                <td style="width:70px;">MONEDA</td>
                <td style="width:80px;">: {{ $prestamodesembolso->monedanombre }}</td>
            </tr>
            <tr>
                <td style="padding-left:5px;border-left:3px solid #31353d;">CLIENTE</td>
                <td>: {{ $prestamodesembolso->cliente_nombre }}</td>
                <td>DESEMBOLSADO</td>
                <td>: {{ $prestamodesembolso->monedasimbolo }} {{ $prestamodesembolso->monto }}</td>
            </tr>
            <tr>
                <td style="padding-left:5px;border-left:3px solid #31353d;">ASESOR</td>
                <td>: {{ $prestamodesembolso->asesor_nombre }}</td>
                <td>Nº CUOTAS</td>
                <td>: {{ $prestamodesembolso->numerocuota }}</td>
            </tr>
            <tr>
                <td style="padding-left:5px;border-left:3px solid #31353d;">VENTANILLA</td>
                <td>: {{ $prestamodesembolso->cajero_nombre }}</td>
                <td>FRECUENCIA</td>
                <td>: {{ $prestamodesembolso->frecuencia_nombre }}</td>
            </tr>
            <tr>
                <td style="padding-left:5px;border-left:3px solid #31353d;">F. DESEMBOLSO</td>
                <td>: {{ date_format(date_create($prestamodesembolso->fechadesembolsado),"d/m/Y h:i A") }}</td>
                <td>F. DE INICIO</td>
                <td>: {{ date_format(date_create($prestamodesembolso->fechainicio),"d/m/Y") }}</td>
            </tr>
        </table>

        <table class="tablacronograma">
                <tr class="b-primary">
                    <td>Nº</td>
                    <td>F.VENCIMIENTO</td>
                    <td>CAPITAL</td>
                    <td>INTERES</td>
                    @if($prestamodesembolso->total_segurodesgravamen>0)
                    <td>SEGURO DESGRAVAMEN</td>
                    @endif
                    @if($prestamodesembolso->total_gastoadministrativo>0)
                    <td>GASTO ADMINISTRATIVO</td>
                    @endif
                    <td>CUOTA</td>
                    @if($prestamodesembolso->total_abono>0)
                    <td>ABONO</td>
                    @endif
                </tr>
                @foreach ($prestamodesembolsodetalle as $value)
                <tr>
                    <td>{{ $value->numero }}</td>
                    <td>{{ date_format(date_create($value->fechavencimiento),"d/m/Y") }}</td>
                    <td>{{ $prestamodesembolso->monedasimbolo }} {{ $value->amortizacion }}</td>
                    <td>{{ $prestamodesembolso->monedasimbolo }} {{ $value->interes }}</td>
                    @if($prestamodesembolso->total_segurodesgravamen>0)
                    <td>{{ $prestamodesembolso->monedasimbolo }} {{ $value->seguro }}</td>
                    @endif
                    @if($prestamodesembolso->total_gastoadministrativo>0)
                    <td>{{ $prestamodesembolso->monedasimbolo }} {{ $value->gastoadministrativo }}</td>
                    @endif
                    <td>{{ $prestamodesembolso->monedasimbolo }} {{ $value->total }}</td>
                    @if($prestamodesembolso->total_abono>0)
                    <td>{{ $prestamodesembolso->monedasimbolo }} {{ $value->abono }}</td>
                    @endif
                </tr>
                @endforeach
                <tr class="b-primary">
                    <td colspan="2" style="text-align:right;">TOTAL</td>
                    <td>{{ $prestamodesembolso->monedasimbolo }} {{$prestamodesembolso->total_amortizacion}}</td>
                    <td>{{ $prestamodesembolso->monedasimbolo }} {{$prestamodesembolso->total_interes}}</td>
                    @if($prestamodesembolso->total_segurodesgravamen>0)
                    <td>{{ $prestamodesembolso->monedasimbolo }} {{$prestamodesembolso->total_segurodesgravamen}}</td>
                    @endif
                    @if($prestamodesembolso->total_gastoadministrativo>0)
                    <td>{{ $prestamodesembolso->monedasimbolo }} {{$prestamodesembolso->total_gastoadministrativo}}</td>
                    @endif
                    <td>{{ $prestamodesembolso->monedasimbolo }} {{$prestamodesembolso->total_cuotafinal}}</td>
                    @if($prestamodesembolso->total_abono>0)
                    <td>{{ $prestamodesembolso->monedasimbolo }} {{$prestamodesembolso->total_abono}}</td>
                    @endif
                </tr>
        </table>
    </div>
</body>
</html>