<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Pagos</title>
    <style>
      html, body {
          margin: 0px;
          padding: 0px;
          font-size: 9px;
          font-weight: bold;
          font-family: Courier;
      }
      .contenedor {
          text-align: center;
          padding: 15px;
      }
      .titulo {
          text-align:center;
          font-size: 14px;
          padding-bottom:5px;
      }
      .fechaimpresion {
          font-size: 10px;
          position: absolute;
          right:15px;
          top:20px;
      }
      .table {
          width: 100%;
          margin:0px;
          padding:0px;
      }
      .table th, .table td {
          padding:0px;
          white-space: nowrap;
      }
      .tablareporte {
          width: 744px;
          margin:0px;
          padding:0px;
          border: 1px solid #ccc;
          border-collapse: collapse;
      }
      .tablareporte th, .tablareporte td {
          border: 1px solid #ccc;
          border-collapse: collapse;
          padding:5px;
          text-align:right;
          white-space: nowrap;
      }
      .tablacabecera {
          background-color:#eae7e7;
      }
      .tienda-nombre {
          font-size:12px;
          margin-bottom:3px;
      }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="titulo">REPORTE DE PAGO</div>
        <div class="fechaimpresion">{{Carbon\Carbon::now()->format('d/m/Y')}}</div>
        <table class="table">
                <tr>
                    <td rowspan="3" style="width:70px;text-align:center;border-left:3px solid #31353d;padding-left:5px;">
                      <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/logo/'.$tienda->imagen) }}" height="50px">
                    </td>
                    <td rowspan="3" style="width:250px;padding-left:5px;">
                      <div class="tienda-nombre">{{ strtoupper($tienda->nombre) }}</div>
                      {{ strtoupper($tienda->direccion) }}<br>
                      {{ strtoupper($tienda->ubigeonombre) }}
                    </td>
                    <td style="width:70px;border-left:3px solid #31353d;padding-left:5px;">FECHA INICIO</td>
                    <td>: {{$fechainicio!=''?date_format(date_create($fechainicio),"d/m/Y"):''}}</td>
                </tr>
                <tr>
                    <td style="width:70px;border-left:3px solid #31353d;padding-left:5px;">FECHA FIN</td>
                    <td>: {{$fechafin!=''?date_format(date_create($fechafin),"d/m/Y"):''}}</td>
                </tr>
                <tr>
                    <td style="width:70px;border-left:3px solid #31353d;padding-left:5px;">TOTAL PAGO</td>
                    <td>: {{$total}}</td>
                </tr>
        </table>
        <br>
        @if($listarpor==1)
        <table class="tablareporte">
                <tr class="tablacabecera">
                    <th style="text-align:left;">ASESOR (APELLIDOS, NOMBRES)</th>
                    <th style="text-align:left;">CLIENTE (APELLIDOS, NOMBRES)</th>
                    <th width="105px">FECHA DE PAGO</th>
                    <th width="60px">Nº CRÉDITO</th>
                    <th width="60px">Nº OPERACIÓN</th>
                    <th width="40px">TOTAL</th>
                </tr>
                @foreach($prestamocobranzas as $value)
                <tr>
                    <td style="text-align:left;">{{$value['asesor']}}</td>
                    <td style="text-align:left;">{{$value['cliente']}}</td>
                    <td>{{$value['fecharegistro']}}</td>
                    <td>{{$value['codigo']}}</td>
                    <td>{{$value['creditocodigo']}}</td>
                    <td>{{$value['cronograma_total']}}</td>
                </tr>
                @endforeach
                <tr class="tablacabecera">
                    <td colspan="5">TOTAL</td>
                    <td>{{$total}}</td>
                </tr>
        </table>
        @elseif($listarpor==2)
        @foreach($prestamocobranzas as $value)
        <table class="tablareporte" style="margin-bottom:5px;">
                <tr class="tablacabecera">
                    <th colspan="6" style="text-align:center;">CLIENTE: {{$value['cliente_identificacion']}} - {{$value['cliente']}}</th>
                </tr>
                <tr class="tablacabecera">
                    <th width="105px">FECHA DE PAGO</th>
                    <th width="60px">Nº CRÉDITO</th>
                    <th width="60px">Nº OPERACIÓN</th>
                    <th width="40px">TOTAL CUOTA</th>
                    <th width="40px">TOTAL MORA</th>
                    <th width="40px">TOTAL PAGADO</th>
                </tr>
                @foreach($value['detalle'] as $valuedetalle)
                <tr>
                    <td>{{$valuedetalle['fecharegistro']}}</td>
                    <td>{{$valuedetalle['codigo']}}</td>
                    <td>{{$valuedetalle['creditocodigo']}}</td>
                    <td>{{$valuedetalle['cronograma_totalcuota']}}</td>
                    <td>{{$valuedetalle['cronograma_morapagar']}}</td>
                    <td>{{$valuedetalle['cronograma_total']}}</td>
                </tr>
                @endforeach
                <tr class="tablacabecera">
                    <td colspan="3">TOTAL</td>
                    <td>{{$value['total_cuota']}}</td>
                    <td>{{$value['total_mora']}}</td>
                    <td>{{$value['total_total']}}</td>
                </tr>
        </table>
        @endforeach
        @elseif($listarpor==3)
        @foreach($prestamocobranzas as $value)
        <table class="tablareporte" style="margin-bottom:5px;">
                <tr class="tablacabecera">
                    <th colspan="7" style="text-align:center;">ASESOR: {{$value['asesor_identificacion']}} - {{$value['asesor']}}</th>
                </tr>
                <tr class="tablacabecera">
                    <th style="text-align:left;">CLIENTE</th>
                    <th width="105px">FECHA DE PAGO</th>
                    <th width="60px">Nº CRÉDITO</th>
                    <th width="60px">Nº OPERACIÓN</th>
                    <th width="40px">TOTAL CUOTA</th>
                    <th width="40px">TOTAL MORA</th>
                    <th width="40px">TOTAL PAGADO</th>
                </tr>
                @foreach($value['detalle'] as $valuedetalle)
                <tr>
                    <td style="text-align:left;">{{$valuedetalle['cliente']}}</td>
                    <td>{{$valuedetalle['fecharegistro']}}</td>
                    <td>{{$valuedetalle['codigo']}}</td>
                    <td>{{$valuedetalle['creditocodigo']}}</td>
                    <td>{{$valuedetalle['cronograma_totalcuota']}}</td>
                    <td>{{$valuedetalle['cronograma_morapagar']}}</td>
                    <td>{{$valuedetalle['cronograma_total']}}</td>
                </tr>
                @endforeach
                <tr class="tablacabecera">
                    <td colspan="4">TOTAL</td>
                    <td>{{$value['total_cuota']}}</td>
                    <td>{{$value['total_mora']}}</td>
                    <td>{{$value['total_total']}}</td>
                </tr>
        </table>
        @endforeach
        @endif
    </div>
</body>
</html>