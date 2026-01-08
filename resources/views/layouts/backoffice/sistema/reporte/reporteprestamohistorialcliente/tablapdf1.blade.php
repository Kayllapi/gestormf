<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Historial de Cliente</title>
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
        <div class="titulo">REPORTE DE HISTORIAL DE CLIENTE</div>
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
                    @if($cliente!='')
                    <td style="border-left:3px solid #31353d;padding-left:5px;">CLIENTE</td>
                    <td colspan="3">: {{$cliente->identificacion}} - {{$cliente->nombre}}, {{$cliente->apellidos}}</td>
                    @else
                    <td style="border-left:3px solid #31353d;padding-left:5px;">CLIENTE</td>
                    <td colspan="3">: </td>
                    @endif
                </tr>
                <tr>
                    <td style="border-left:3px solid #31353d;padding-left:5px;">DINERO PRESTADO</td>
                    <td>: 0.00</td>
                    <td style="width:70px;border-left:3px solid #31353d;padding-left:5px;">TOTAL A PAGAR</td>
                    <td style="width:120px;">: 0.00</td>
                </tr>
                <tr>
                    <td style="border-left:3px solid #31353d;padding-left:5px;">TOTAL INTERES</td>
                    <td>: {{$fechainicio!=''?date_format(date_create($fechainicio),"d/m/Y"):''}}</td>
                    <td style="width:70px;border-left:3px solid #31353d;padding-left:5px;">TOTAL DEUDA</td>
                    <td style="width:120px;">: 0.00</td>
                </tr>
        </table>
        <br>
        <table class="tablareporte">
            <thead>
                <tr class="tablacabecera">
                    <th style="text-align:left;">CRÉDITO</th>
                    <th style="text-align:left;">FRECUENCIA</th>
                    <th>ESTADO</th>
                    <th>DESEMBOLSADO</th>
                    <th>TOTAL A PAGAR</th>
                    <th>DEBE</th>
                    <th>PAGOS EN CUOTA</th>
                    <th>PAGOS EN MORA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prestamoscredito as $value)
                <?php $cronograma = prestamo_cobranza_cronograma($tienda->id,$value->id,0,0,1,$value->numerocuota); ?>
                <tr>
                    <td style="text-align:left;">{{$value->creditocodigo}}</td>
                    <td style="text-align:left;">{{$value->frecuencianombre}}</td>
                    <td>{{$value->estado}}</td>
                    <td>{{$value->monedasimbolo}} {{$value->creditomonto}}</td>
                    <td>{{$value->monedasimbolo}} {{$value->total_cuotafinal}}</td>
                    <td>{{$value->monedasimbolo}} {{$cronograma['total_pendiente_cuotapago']}}</td>
                    <td>{{$value->monedasimbolo}} {{$cronograma['total_cancelada_cuota']}}</td>
                    <td>{{$value->monedasimbolo}} {{$cronograma['total_cancelada_moraapagar']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>