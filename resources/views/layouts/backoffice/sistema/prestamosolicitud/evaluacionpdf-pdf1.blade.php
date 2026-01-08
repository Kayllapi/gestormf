<!DOCTYPE html>
<html>
<head>
  <title>HOJA DE EVALUACIÓN</title>
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
      .table {
        width: 100%;
        margin:0px;
        padding:0px;
        border-collapse: collapse;
        border-spacing: 0;
      }
      .table td {
        border:1px solid #ccc;
        padding: 5px;
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
      .b-titulo {
        height:15px;
        font-size:10px;
        text-align:left;
        padding:5px;
      }
      .b-subtitulo {
        background-color: #eae7e7;
      }
      .tienda-nombre {
          font-size:12px;
          margin-bottom:3px;
      }
     .resultado-aprobado {
        background-color: #179a4f;
        padding: 5px;
        border-radius: 5px;
        color: rgb(255 255 255);
        font-weight: bold;
        font-size: 20px;
        margin-bottom: 5px;
        margin-top: 20px;
        float: left;
        width: 100%;
        text-align:center;
      }
      .resultado-desaprobado {
        background-color: #8c1329;
        padding: 5px;
        border-radius: 5px;
        color: rgb(255 255 255);
        font-weight: bold;
        font-size: 20px;
        margin-bottom: 5px;
        margin-top: 20px;
        float: left;
        width: 100%;
        text-align:center;
      }
  </style>
</head>
<body>
    <div class="contenedor">
        <div class="b-titulo-master">HOJA DE EVALUACIÓN</div>
        <table class="table-cabecera" style="margin-bottom:10px;">
            <tr>
                <td rowspan="3" style="width:80px;text-align:center;border-left:3px solid #31353d;height:45.5px;">
                    <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/logo/'.$tienda->imagen) }}" height="40px">
                </td>
                <td rowspan="3" style="width:270px;padding-left:5px;">
                    <div class="tienda-nombre">{{ strtoupper($tienda->nombre) }}</div>
                    {{ strtoupper($tienda->direccion) }}<br>
                    {{ strtoupper($tienda->ubigeonombre) }}
                </td>
                <td style="width:50px;border-left:3px solid #31353d;padding-left:5px;">FECHA</td>
                <td>: {{ date_format(date_create($prestamocredito->fecharegistro), "d/m/Y") }}</td>
            </tr>
            <tr>
                <td style="border-left:3px solid #31353d;padding-left:5px;">CLIENTE</td>
                <td>: {{$prestamocredito->clienteidentificacion}} - {{$prestamocredito->clienteapellidos}}, {{$prestamocredito->clientenombre}}</td>
            </tr>
            <tr>
                <td style="border-left:3px solid #31353d;padding-left:5px;">NEGOCIO</td>
                <td>: <?php echo $prestamolaboral!=''?($prestamolaboral->nombrenegocio!=''?$prestamolaboral->nombrenegocio:'&nbsp;'):'&nbsp;'?></td>
            </tr>
        </table>
        <table class="table">
            <tr class="b-primary">
              <th class="b-titulo" colspan="5">CRÉDITO</th>
            </tr>
            <tr>
              <td class="b-subtitulo" style="text-align:center;">MONTO</td>
              <td class="b-subtitulo" style="text-align:center;">TASA</td>
              <td class="b-subtitulo" style="text-align:center;">Nº DE CUOTAS</td>
              <td class="b-subtitulo" style="text-align:center;">MONTO DE CUOTA</td>
              <td class="b-subtitulo" style="text-align:center;">FRECUENCIA DE PAGOS</td>
            </tr>
            <tr>
              <td style="text-align:center;">{{$prestamocredito->monto}}</td>
              <td style="text-align:center;">{{$prestamocredito->tasa}}</td>
              <td style="text-align:center;">{{$prestamocredito->numerocuota}}</td>
              <td style="text-align:center;">{{$prestamocredito->monedasimbolo}} {{$prestamocredito->cuota}}</td>
              <td style="text-align:center;">{{$prestamocredito->frecuencia_nombre}}</td>
            </tr>
        </table>
        <table class="table">
            <tr class="b-primary">
              <th class="b-titulo" colspan="8">VENTAS</th>
            </tr>
            <tr>
              <td class="b-subtitulo" style="text-align:center;">Nº</td>
              <td class="b-subtitulo" style="text-align:center;">PRODUCTO</td>
              <td class="b-subtitulo" style="text-align:center;">CANTIDAD</td>
              <td class="b-subtitulo" style="text-align:center;">VALOR UNITARIO</td>
              <td class="b-subtitulo" style="text-align:center;">VENTA DIARIA</td>
              <td class="b-subtitulo" style="text-align:center;">VENTA SEMANAL</td>
              <td class="b-subtitulo" style="text-align:center;">VENTA QUINCENAL</td>
              <td class="b-subtitulo" style="text-align:center;">VENTA MENSUAL</td>
            </tr>
            <?php 
            $total_preciounitario = 0; 
            $total_preciototal_diario = 0; 
            $total_preciototal_semanal = 0; 
            $total_preciototal_quincenal = 0; 
            $total_preciototal_mensual = 0; 
            $num = 1; 
            ?>
            @foreach ($laboralventa as $value)
            <tr>
              <td style="text-align:center;">{{ $num }}</td>
              <td style="text-align:left;">{{ $value->producto }}</td>
              <td style="text-align:center;">{{ $value->cantidad }}</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{ $value->preciounitario }}</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{ $value->preciototal }}</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{ $value->preciototal_semanal }}</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{ $value->preciototal_quincenal }}</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{ $value->preciototal_mensual }}</td>
            </tr>
            <?php 
            $total_preciounitario = $total_preciounitario+$value->preciounitario; 
            $total_preciototal_diario = $total_preciototal_diario+$value->preciototal; 
            $total_preciototal_semanal = $total_preciototal_semanal+$value->preciototal_semanal; 
            $total_preciototal_quincenal = $total_preciototal_quincenal+$value->preciototal_quincenal; 
            $total_preciototal_mensual = $total_preciototal_mensual+$value->preciototal_mensual; 
            $num++; 
            ?>
            @endforeach
            @for($i=$num; $i<=10; $i++)
            <tr>
              <td>&nbsp;</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            @endfor
            <tr>
              <td class="b-subtitulo" colspan="3" style="text-align:right;">TOTAL</td>
              <td class="b-subtitulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_preciounitario, 2, '.', '')}}</td>
              <td class="b-subtitulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_preciototal_diario, 2, '.', '')}}</td>
              <td class="b-subtitulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_preciototal_semanal, 2, '.', '')}}</td>
              <td class="b-subtitulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_preciototal_quincenal, 2, '.', '')}}</td>
              <td class="b-subtitulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_preciototal_mensual, 2, '.', '')}}</td>
            </tr>
        </table>
        <table class="table">
            <tr class="b-primary">
              <th class="b-titulo" colspan="8">COSTO DE VENTA</th>
            </tr>
            <tr>
              <td class="b-subtitulo" style="text-align:center;">Nº</td>
              <td class="b-subtitulo" style="text-align:center;">PRODUCTO</td>
              <td class="b-subtitulo" style="text-align:center;">CANTIDAD</td>
              <td class="b-subtitulo" style="text-align:center;">VALOR UNITARIO</td>
              <td class="b-subtitulo" style="text-align:center;">VENTA DIARIA</td>
              <td class="b-subtitulo" style="text-align:center;">VENTA SEMANAL</td>
              <td class="b-subtitulo" style="text-align:center;">VENTA QUINCENAL</td>
              <td class="b-subtitulo" style="text-align:center;">VENTA MENSUAL</td>
            </tr>
            <?php 
            $total_preciounitario = 0; 
            $total_preciototal_diario = 0; 
            $total_preciototal_semanal = 0; 
            $total_preciototal_quincenal = 0; 
            $total_preciototal_mensual = 0; 
            $num = 1; 
            ?>
            @foreach ($laboralcompra as $value)
            <tr>
              <td style="text-align:center;">{{ $num }}</td>
              <td style="text-align:left;">{{ $value->producto }}</td>
              <td style="text-align:center;">{{ $value->cantidad }}</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{ $value->preciounitario }}</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{ $value->preciototal }}</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{ $value->preciototal_semanal }}</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{ $value->preciototal_quincenal }}</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{ $value->preciototal_mensual }}</td>
            </tr>
            <?php 
            $total_preciounitario = $total_preciounitario+$value->preciounitario; 
            $total_preciototal_diario = $total_preciototal_diario+$value->preciototal; 
            $total_preciototal_semanal = $total_preciototal_semanal+$value->preciototal_semanal; 
            $total_preciototal_quincenal = $total_preciototal_quincenal+$value->preciototal_quincenal; 
            $total_preciototal_mensual = $total_preciototal_mensual+$value->preciototal_mensual; 
            $num++; 
            ?>
            @endforeach
            @for($i=$num; $i<=10; $i++)
            <tr>
              <td>&nbsp;</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            @endfor
            <tr>
              <td class="b-subtitulo" colspan="3" style="text-align:right;">TOTAL</td>
              <td class="b-subtitulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_preciounitario, 2, '.', '')}}</td>
              <td class="b-subtitulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_preciototal_diario, 2, '.', '')}}</td>
              <td class="b-subtitulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_preciototal_semanal, 2, '.', '')}}</td>
              <td class="b-subtitulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_preciototal_quincenal, 2, '.', '')}}</td>
              <td class="b-subtitulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_preciototal_mensual, 2, '.', '')}}</td>
            </tr>
        </table>
        <table class="table" style="width:50%;">
            <tr class="b-primary">
              <th class="b-titulo" colspan="3">GASTOS OPERATIVOS (MENSUALIZADO)</th>
            </tr>
            <tr>
              <td class="b-subtitulo" style="text-align:center;width:20px;">Nº</td>
              <td class="b-subtitulo" style="text-align:center;">CONCEPTO</td>
              <td class="b-subtitulo" style="text-align:center;width:70px;">MONTO</td>
            </tr>
            <?php 
            $total_monto = 0; 
            $num = 1; 
            ?>
            @foreach ($tipogastos as $value)
            <?php 
            $monto_egresogasto = DB::table('s_prestamo_creditolaboralegresogasto')
                ->where('s_idprestamo_creditolaboral', $idprestamolavoral)
                ->where('s_idprestamo_tipogasto', $value->id)
                ->sum('monto'); 
            ?>
            <tr>
              <td style="text-align:center;">{{ $num }}</td>
              <td style="text-align:left;">{{ $value->nombre }}</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{ $monto_egresogasto }}</td>
            </tr>
            <?php 
            $total_monto = $total_monto+$monto_egresogasto; 
            $num++; 
            ?>
            @endforeach
            <tr>
              <td class="b-subtitulo" colspan="2" style="text-align:right;">TOTAL</td>
              <td class="b-subtitulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_monto, 2, '.', '')}}</td>
            </tr>
        </table>
        <table class="table" style="width:50%;position:absolute;top:737px;right:20px;">
            <tr class="b-primary">
              <th class="b-titulo" colspan="3">PAGO DE CUOTAS (BANCOS)</th>
            </tr>
            <tr>
              <td class="b-subtitulo" style="text-align:center;width:20px;">Nº</td>
              <td class="b-subtitulo" style="text-align:center;">INSTITUCIONES FINANCIERAS</td>
              <td class="b-subtitulo" style="text-align:center;width:70px;">CUOTA</td>
            </tr>
            <?php 
            $total_monto = 0; 
            $num = 1; 
            ?>
            @foreach ($laboralegresopago as $value)
            <tr>
              <td style="text-align:center;">{{ $num }}</td>
              <td style="text-align:left;">{{ $value->conceptoegresopago }}</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{ $value->monto }}</td>
            </tr>
            <?php 
            $total_monto = $total_monto+$value->monto; 
            $num++; 
            ?>
            @endforeach
            @for($i=$num; $i<=10; $i++)
            <tr>
              <td>&nbsp;</td>
              <td></td>
              <td></td>
            </tr>
            @endfor
            <tr>
              <td class="b-subtitulo" colspan="2" style="text-align:right;">TOTAL</td>
              <td class="b-subtitulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_monto, 2, '.', '')}}</td>
            </tr>
        </table>
        
        <table class="table" style="margin-top:60px;">
            <tr class="b-primary">
              <th class="b-titulo" colspan="3">OTRO INGRESOS</th>
            </tr>
            <tr>
              <td class="b-subtitulo" style="text-align:center;width:20px;">Nº</td>
              <td class="b-subtitulo" style="text-align:center;">CONCEPTO</td>
              <td class="b-subtitulo" style="text-align:center;width:70px;">CUOTA</td>
            </tr>
            <?php 
            $total_monto = 0; 
            $num = 1; 
            ?>
            @foreach ($laboralotroingreso as $value)
            <tr>
              <td style="text-align:center;">{{ $num }}</td>
              <td style="text-align:left;">{{ $value->conceptootroingreso }}</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{ $value->monto }}</td>
            </tr>
            <?php 
            $total_monto = $total_monto+$value->monto; 
            $num++; 
            ?>
            @endforeach
            @for($i=$num; $i<=10; $i++)
            <tr>
              <td>&nbsp;</td>
              <td></td>
              <td></td>
            </tr>
            @endfor
            <tr>
              <td class="b-subtitulo" colspan="2" style="text-align:right;">TOTAL</td>
              <td class="b-subtitulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_monto, 2, '.', '')}}</td>
            </tr>
        </table>
        <table class="table" style="width:50%;">
            <tr class="b-primary">
              <th class="b-titulo" colspan="3">GASTOS FAMILIARES (MENSUALIZADO)</th>
            </tr>
            <tr>
              <td class="b-subtitulo" style="text-align:center;width:20px;">Nº</td>
              <td class="b-subtitulo" style="text-align:center;">CONCEPTO</td>
              <td class="b-subtitulo" style="text-align:center;width:70px;">MONTO</td>
            </tr>
            <?php 
            $total_monto = 0; 
            $num = 1; 
            ?>
            @foreach ($tipogastofamiliares as $value)
            <?php 
            
            $monto_egresogasto = DB::table('s_prestamo_creditolaboralegresogastofamiliar')
                ->where('s_idprestamo_creditolaboral', $idprestamolavoral)
                ->where('s_idprestamo_tipogasto', $value->id)
                ->sum('monto'); 
            ?>
            <tr>
              <td style="text-align:center;">{{ $num }}</td>
              <td style="text-align:left;">{{ $value->nombre }}</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{ $monto_egresogasto }}</td>
            </tr>
            <?php 
            $total_monto = $total_monto+$monto_egresogasto; 
            $num++; 
            ?>
            @endforeach
            <tr>
              <td class="b-subtitulo" colspan="2" style="text-align:right;">TOTAL</td>
              <td class="b-subtitulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_monto, 2, '.', '')}}</td>
            </tr>
        </table>
        <table class="table" style="width:50%;position:absolute;top:329px;right:20px;">
            <tr class="b-primary">
              <th class="b-titulo" colspan="3">OTROS GASTOS</th>
            </tr>
            <tr>
              <td class="b-subtitulo" style="text-align:center;width:20px;">Nº</td>
              <td class="b-subtitulo" style="text-align:center;">CONCEPTO</td>
              <td class="b-subtitulo" style="text-align:center;width:70px;">CUOTA</td>
            </tr>
            <?php 
            $total_monto = 0; 
            $num = 1; 
            ?>
            @foreach ($laboralotrogasto as $value)
            <tr>
              <td style="text-align:center;">{{ $num }}</td>
              <td style="text-align:left;">{{ $value->conceptootrogasto }}</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{ $value->monto }}</td>
            </tr>
            <?php 
            $total_monto = $total_monto+$value->monto; 
            $num++; 
            ?>
            @endforeach
            @for($i=$num; $i<=10; $i++)
            <tr>
              <td>&nbsp;</td>
              <td></td>
              <td></td>
            </tr>
            @endfor
            <tr>
              <td class="b-subtitulo" colspan="2" style="text-align:right;">TOTAL</td>
              <td class="b-subtitulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_monto, 2, '.', '')}}</td>
            </tr>
        </table>
        <table class="table" style="width:50%;">
            <tr class="b-primary">
              <th class="b-titulo" colspan="2">ESTADO DE RESULTADOS</th>
            </tr>
            <tr>
              <td class="b-subtitulo" style="text-align:right;">INGRESO TOTAL</td>
              <td style="text-align:right;width:100px;">{{$prestamocredito->monedasimbolo}} {{$prestamolaboral!=''?$prestamolaboral->ingresototal:'0.00'}}</td>
            </tr>
            <tr>
              <td>(-) COSTO DE VENTAS</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{$prestamolaboral!=''?$prestamolaboral->compra:'0.00'}}</td>
            </tr>
            <tr>
              <td class="b-subtitulo" style="text-align:right;">UTILIDAD BRUTA</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{$prestamolaboral!=''?$prestamolaboral->utilidad_bruta:'0.00'}}</td>
            </tr>
            <tr>
              <td>(-) GASTOS OPERATIVOS</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{$prestamolaboral!=''?$prestamolaboral->egresogasto:'0.00'}}</td>
            </tr>
            <tr>
              <td class="b-subtitulo" style="text-align:right;">UTILIDAD OPERATIVA</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{$prestamolaboral!=''?$prestamolaboral->utilidad_operativa:'0.00'}}</td>
            </tr>
            <tr>
              <td>(-) PAGO DE CUOTAS (BANCOS)</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{$prestamolaboral!=''?$prestamolaboral->egresopago:'0.00'}}</td>
            </tr>
            <tr>
              <td class="b-subtitulo" style="text-align:right;">UTILIDAD NETA</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{$prestamolaboral!=''?$prestamolaboral->utilidad_neta:'0.00'}}</td>
            </tr>
            <tr>
              <td>(+) OTROS INGRESOS</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{$prestamolaboral!=''?$prestamolaboral->otroingreso:'0.00'}}</td>
            </tr>
            <tr>
              <td>(-) OTROS GASTOS</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{$prestamolaboral!=''?$prestamolaboral->otrogasto:'0.00'}}</td>
            </tr>
            <tr>
              <td>(-) GASTOS FAMILIARES</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{$prestamolaboral!=''?$prestamolaboral->egresogastofamiliar:'0.00'}}</td>
            </tr>
            <tr>
              <td class="b-subtitulo" style="text-align:right;">EXCEDENTE NETO MENSUAL</td>
              <td class="b-subtitulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{$prestamolaboral!=''?$prestamolaboral->ingresomensual:'0.00'}}</td>
            </tr>
        </table>
        <table class="table" style="width:370px;position:absolute;top:605px;right:20px;">
            <tr>
              <td class="b-primary b-titulo">MONTO SOLICITADO</td>
              <td style="width:150px;">{{$prestamocredito->monedasimbolo}} {{$prestamocredito->monto}}</td>
            </tr>
            <tr>
              <td class="b-primary b-titulo"><b>INTERES</b></td>
              <td>
                {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                {{$prestamocredito_resultado['prestamocredito']->total_interes}}
              </td>
            </tr>
            @if($prestamocredito_resultado['prestamocredito']->total_segurodesgravamen>0)
            <tr>
              <td class="b-primary b-titulo"><b>SEGURO DESGRAVAMEN</b></td>
              <td>
                {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                {{$prestamocredito_resultado['prestamocredito']->total_segurodesgravamen}}
              </td>
            </tr>
            @endif
            @if($prestamocredito_resultado['prestamocredito']->total_abono>0)
            <tr>
              <td class="b-primary b-titulo"><b>ABONO</b></td>
              <td>
                {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                {{$prestamocredito_resultado['prestamocredito']->total_abono}}
              </td>
            </tr>
            @endif
            <tr>
              <td class="b-primary b-titulo">MONTO A PAGAR</td>
              <td>{{$prestamocredito->monedasimbolo}} {{$prestamocredito->total_cuotafinal}}</td>
            </tr>
            <tr>
              <td class="b-primary b-titulo">Nº DE CUOTAS</td>
              <td>{{$prestamocredito->numerocuota}}</td>
            </tr>
            <tr>
              <td class="b-primary b-titulo">FRECUENCUA</td>
              <td>{{$prestamocredito->frecuencia_nombre}}</td>
            </tr>
            <tr>
              <td class="b-primary b-titulo">CUOTA</td>
              <td>{{$prestamocredito->monedasimbolo}} {{$prestamocredito->cuota}}</td>
            </tr>
            <tr>
              <td class="b-primary b-titulo">CUOTA MENSUALIZADA</td>
              <td>{{$prestamocredito->monedasimbolo}} {{$prestamocredito_resultado['cuotamensualizada']}}</td>
            </tr>
        </table>

        @if($prestamocredito_resultado['resultado']=='APROBADO')
            <div class="resultado-aprobado">CRÉDITO APROBADO</div>                                             
        @elseif($prestamocredito_resultado['resultado']=='DESAPROBADO')
            <div class="resultado-desaprobado">CRÉDITO DESAPROBADO</div>
        @endif
  </div>
</body>
</html>