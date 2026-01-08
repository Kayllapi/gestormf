<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Cobranzas</title>
    <style>
      html, body {
          margin: 0px;
          padding: 0px;
          font-size: 9px;
          font-weight: bold;
          font-family: Courier;
      }
      .contenedor {
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
          text-align:left;
      }
      .tablareporte {
          width: 738px;
          margin:0px;
          padding:0px;
          border: 1px solid #ccc;
          border-collapse: collapse;
      }
      .tablareporte th, .tablareporte td {
          border: 1px solid #ccc;
          border-collapse: collapse;
          padding:5px;
          text-align:left;
      }
      .tablacabecera {
          background-color:#eae7e7;
       
      }
      .tablacabecera2 {
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
        <div class="titulo">REPORTE DE COBRANZA</div>
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
                    <td style="border-left:3px solid #31353d;padding-left:5px;">FECHA VCTO.</td>
                    <td>: {{$fechafin!=''?date_format(date_create($fechafin),"d/m/Y"):''}}</td>
                    <td style="width:70px;border-left:3px solid #31353d;padding-left:5px;">DEUDA TOTAL</td>
                    <td style="width:120px;">: {{$montototal}}</td>
                </tr>
        </table>
        <br>
        @foreach($prestamocredito_reporte as $value)
            <table class="tablareporte" style="margin-bottom:5px;width:765px;">
                <tr>
                    <th class="tablacabecera" colspan="8" style="text-align:center;">
                      CLIENTE: {{$value['cliente_identificacion']}} - {{$value['cliente']}} | TELF.: <?php echo $value['cliente_numerotelefono']!=''?$value['cliente_numerotelefono']:'---' ?>
                    </th>
                </tr>
                <tr class="tablacabecera2">
                    <td style="text-align:right;">NRO</td>
                    <td style="text-align:right;">VENCIMIENTO</td>
                    <td style="text-align:right;">CUOTA</td>
                    <td style="text-align:right;">ATRASO</td>
                    <td style="text-align:right;">MORA</td>
                    <td style="text-align:right;">TOTAL</td>
                    <td style="text-align:right;">ACUENTA</td>
                    <td style="text-align:right;">PAGAR</td>
                </tr>
                @foreach($value['cuotas'] as $valuecuota)
                    <tr>
                        <td style="text-align:right;">{{$valuecuota['tabla_numero']}}</td>
                        <td style="text-align:right;">{{$valuecuota['tabla_fechavencimiento']}}</td>
                        <td style="text-align:right;">{{$valuecuota['tabla_cuota']}}</td>
                        <td style="text-align:right;">{{$valuecuota['tabla_atraso']}} días</td>
                        <td style="text-align:right;">{{$valuecuota['tabla_mora']}}</td>
                        <td style="text-align:right;">{{$valuecuota['tabla_cuotatotal']}}</td>
                        <td style="text-align:right;">{{$valuecuota['tabla_acuenta']}}</td>
                        <td style="text-align:right;">{{$valuecuota['tabla_cuotaapagar']}}</td>
                    </tr>
                @endforeach
                <tr class="tablacabecera2">
                    <td colspan="2" style="text-align:right;">TOTAL</td>
                    <td style="text-align:right;">{{$value['total_cuota']}}</td>
                    <td></td>
                    <td style="text-align:right;">{{$value['total_mora']}}</td>
                    <td style="text-align:right;">{{$value['total_total']}}</td>
                    <td style="text-align:right;">{{$value['total_acuenta']}}</td>
                    <td style="text-align:right;">{{$value['total_apagar']}}</td>
                </tr>
            </table>
        @endforeach
    </div>
</body>
</html>