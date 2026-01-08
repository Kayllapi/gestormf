<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Prestamos</title>
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
      }
      .tablareporte {
          width: 100%;
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
        <div class="titulo">REPORTE DE PRESTAMOS</div>
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
                    <td style="width:70px;border-left:3px solid #31353d;padding-left:5px;">ASESOR</td>
                    <td colspan="3">: {{Auth::user()->nombre}}, {{Auth::user()->apellidos}}</td>
                </tr>
                <tr>
                    @if($cliente!='')
                    <td style="border-left:3px solid #31353d;padding-left:5px;">CLIENTE</td>
                    <td colspan="3">: {{$cliente->identificacion}} - {{$cliente->nombre}}, {{$cliente->apellidos}}</td>
                    @else
                    <td style="border-left:3px solid #31353d;padding-left:5px;">CLIENTE</td>
                    <td colspan="3">: </td>
                    @endif
                </tr>
                <tr>
                    <td style="border-left:3px solid #31353d;padding-left:5px;">FECHA INICIO</td>
                    <td>: {{$fechainicio!=''?date_format(date_create($fechainicio),"d/m/Y"):''}}</td>
                    <td style="width:70px;border-left:3px solid #31353d;padding-left:5px;">FECHA FIN</td>
                    <td style="width:120px;">: {{$fechafin!=''?date_format(date_create($fechafin),"d/m/Y"):''}}</td>
                </tr>
        </table>
        <br>
        <table class="tablareporte">
            <thead>
                <tr class="tablacabecera">
                    <th style="text-align:left;">CLIENTE (DNI - APELLIDOS, NOMBRES)</th>
                    <th width="60px" style="text-align:left;">ULT. CUOTA</th>
                    <th width="70px">DESEMBOLSADO</th>
                    <th width="70px">D. PAGADA</th>
                    <th width="70px">D. VENCIDA</th>
                    <th width="70px">D. RESTANTE</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_desembolso = 0;
                $total_cancelada = 0;
                $total_vencida = 0;
                $total_restante = 0;
                ?>
                @foreach($prestamoscredito as $value)
                <tr>
                    <td style="text-align:left;">{{$value->cliente_identificacion}} - {{$value->cliente}}</td>
                    <td style="text-align:left;">{{date_format(date_create($value->ultimafecha),"d/m/Y")}}</td>
                    <td>{{$value->monedasimbolo}} {{$value->monto}}</td>
                    <td>{{$value->monedasimbolo}} {{$value->cronograma_total_cancelada_cuotapago}}</td>
                    <td>{{$value->monedasimbolo}} {{$value->cronograma_total_vencida_cuotapago}}</td>
                    <td>{{$value->monedasimbolo}} {{$value->cronograma_total_restante_cuotapago}}</td>
                    <?php
                    $total_desembolso = $total_desembolso+$value->monto;
                    $total_cancelada = $total_cancelada+$value->cronograma_total_cancelada_cuotapago;
                    $total_vencida = $total_vencida+$value->cronograma_total_vencida_cuotapago;
                    $total_restante = $total_restante+$value->cronograma_total_restante_cuotapago;
                    ?>
                </tr>
                @endforeach
                <tr class="tablacabecera">
                    <td colspan="2">TOTAL</td>
                    <td>{{number_format($total_desembolso, 2, '.', '')}}</td>
                    <td>{{number_format($total_cancelada, 2, '.', '')}}</td>
                    <td>{{number_format($total_vencida, 2, '.', '')}}</td>
                    <td>{{number_format($total_restante, 2, '.', '')}}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>