<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Mora</title>
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
      .table {
          width: 100%;
          margin:0px;
          padding:0px;
          border: none;
      }
      .tablareporte {
          width: 100%;
          margin:0px;
          padding:0px;
          border: 1px solid black;
          border-collapse: collapse;
      }
      .tablareporte th, .tablareporte td {
          border: 1px solid black;
          border-collapse: collapse;
          padding:5px;
          text-align:right;
      }
    </style>
</head>
<body>
    <div class="contenedor">
        <table class="table">
            <thead>
                <tr>
                    <td colspan="4" style="text-align:center;font-size: 13px;">REPORTE DE MORA</td>
                </tr>
                <tr>
                    <td style="width:40px;">ASESOR</td>
                    <td>: {{Auth::user()->nombre}}, {{Auth::user()->apellidos}}</td>
                    @if($cliente!='')
                    <td style="width:40px;">CLIENTE</td>
                    <td>: {{$cliente->identificacion}} - {{$cliente->nombre}}, {{$cliente->apellidos}}</td>
                    @endif
                    <td style="width:30px;">FECHA</td>
                    <td style="width:70px;">: {{Carbon\Carbon::now()->format('d/m/Y')}}</td>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <table class="tablareporte">
            <thead>
                <tr>
                    <th style="text-align:left;">CLIENTE</th>
                    <th style="text-align:left;">DIRECCIÓN</th>
                    <th width="50px" style="text-align:left;">TELÉFONO</th>
                    <th width="40px">DESEMBOLSO</th>
                    <th width="40px">DEUDA CAPITAL</th>
                    <th width="40px">DEUDA PAGADA</th>
                    <th width="40px">DEUDA PENDIENTE</th>
                    <th width="40px">CUOTA FIJA</th>
                    <th width="10px">ATRASO X DIAS</th>
                    <th width="40px">CUOTA</th>
                    <th width="40px">MORA</th>
                    <th width="40px">DEUDA</th>
                    <th width="60px">ULTIMA FECHA</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $desembolso = 0;
                $deudacapital = 0;
                $deudapagada = 0;
                $deudapendiente = 0;
                $cuotafija = 0;
                $cuota = 0;
                $mora = 0;
                $deuda = 0;
                ?>
                @foreach($prestamo_creditos as $value)
              <?php
              /*$cronograma = prestamo_cobranza_cronograma($tienda->id,$value->id,0,0,1,0);
            DB::table('s_prestamo_credito')->whereId($cronograma['creditosolicitud']->id)->update([
                'cronograma_primeratraso' => $cronograma['primeratraso'],
                'cronograma_total_cancelada_atraso' => $cronograma['total_cancelada_atraso'],
                'cronograma_total_cancelada_cuota' => $cronograma['total_cancelada_cuota'],
                'cronograma_total_cancelada_mora' => $cronograma['total_cancelada_mora'],
                'cronograma_total_cancelada_moradescontado' => $cronograma['total_cancelada_moradescontado'],
                'cronograma_total_cancelada_moraapagar' => $cronograma['total_cancelada_moraapagar'],
                'cronograma_total_cancelada_acuenta' => $cronograma['total_cancelada_acuenta'],
                'cronograma_total_cancelada_cuotapago' => $cronograma['total_cancelada_cuotapago'],
                'cronograma_total_vencida_atraso' => $cronograma['total_vencida_atraso'],
                'cronograma_total_vencida_cuota' => $cronograma['total_vencida_cuota'],
                'cronograma_total_vencida_mora' => $cronograma['total_vencida_mora'],
                'cronograma_total_vencida_moradescontado' => $cronograma['total_vencida_moradescontado'],
                'cronograma_total_vencida_moraapagar' => $cronograma['total_vencida_moraapagar'],
                'cronograma_total_vencida_acuenta' => $cronograma['total_vencida_acuenta'],
                'cronograma_total_vencida_cuotapago' => $cronograma['total_vencida_cuotapago'],
                'cronograma_total_restante_atraso' => $cronograma['total_restante_atraso'],
                'cronograma_total_restante_cuota' => $cronograma['total_restante_cuota'],
                'cronograma_total_restante_mora' => $cronograma['total_restante_mora'],
                'cronograma_total_restante_moradescontado' => $cronograma['total_restante_moradescontado'],
                'cronograma_total_restante_moraapagar' => $cronograma['total_restante_moraapagar'],
                'cronograma_total_restante_acuenta' => $cronograma['total_restante_acuenta'],
                'cronograma_total_restante_cuotapago' => $cronograma['total_restante_cuotapago'],
                'cronograma_total_pendiente_atraso' => $cronograma['total_pendiente_atraso'],
                'cronograma_total_pendiente_cuota' => $cronograma['total_pendiente_cuota'],
                'cronograma_total_pendiente_mora' => $cronograma['total_pendiente_mora'],
                'cronograma_total_pendiente_moradescontado' => $cronograma['total_pendiente_moradescontado'],
                'cronograma_total_pendiente_moraapagar' => $cronograma['total_pendiente_moraapagar'],
                'cronograma_total_pendiente_acuenta' => $cronograma['total_pendiente_acuenta'],
                'cronograma_total_pendiente_cuotapago' => $cronograma['total_pendiente_cuotapago'],
            ]);*/
              ?>
                <tr>
                    <td style="text-align:left;">{{$value->clientenombre}}, {{$value->clienteapellidos}}</td>
                    <td style="text-align:left;">{{$value->clientedireccion}}</td>
                    <td style="text-align:left;">{{$value->clientenumerotelefono}}</td>
                    <td>{{$value->monto}}</td>
                    <td>{{$value->total_cuotafinal}}</td>
                    <td>{{$value->cronograma_total_cancelada_cuotapago}}</td>
                    <td>{{$value->cronograma_total_pendiente_cuotapago}}</td>
                    <td>{{$value->cuota}}</td>
                    <td>
                      @if($value->cronograma_total_vencida_atraso>0)
                      {{$value->cronograma_total_vencida_atraso}}
                      @else
                      {{$value->cronograma_total_restante_atraso}}
                      @endif
                    </td>
                    <td>{{$value->cronograma_total_vencida_cuota}}</td>
                    <td>{{$value->cronograma_total_vencida_mora}}</td>
                    <td>{{$value->cronograma_total_vencida_cuotapago}}</td>
                    <td>{{date_format(date_create($value->ultimafecha),"d/m/Y")}}</td>
                    <?php
                    $desembolso = $desembolso+$value->monto;
                    $deudacapital = $deudacapital+$value->total_cuotafinal;
                    $deudapagada = $deudapagada+$value->cronograma_total_cancelada_cuotapago;
                    $deudapendiente = $deudapendiente+$value->cronograma_total_pendiente_cuotapago;
                    $cuotafija = $cuotafija+$value->cuota;
                    $cuota = $cuota+$value->cronograma_total_vencida_cuota;
                    $mora = $mora+$value->cronograma_total_vencida_mora;
                    $deuda = $deuda+$value->cronograma_total_vencida_cuotapago;
                    ?>
                </tr>
                @endforeach
                <tr>
                    <td style="border-bottom-color:#fff;border-left-color:#fff;" colspan="3">TOTAL</td>
                    <td>{{number_format($desembolso, 2, '.', '')}}</td>
                    <td>{{number_format($deudacapital, 2, '.', '')}}</td>
                    <td>{{number_format($deudapagada, 2, '.', '')}}</td>
                    <td>{{number_format($deudapendiente, 2, '.', '')}}</td>
                    <td>{{number_format($cuotafija, 2, '.', '')}}</td>
                    <td style="border-bottom-color:#fff;"></td>
                    <td>{{number_format($cuota, 2, '.', '')}}</td>
                    <td>{{number_format($mora, 2, '.', '')}}</td>
                    <td>{{number_format($deuda, 2, '.', '')}}</td>
                    <td style="border-bottom-color:#fff;border-right-color:#fff;"></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>