<!DOCTYPE html>
<html>
<head>
  <title>HOJA DE EVALUACIÓN</title>
    @include('app.pdf_style',['idtienda'=>$tienda->id])
</head>
<body>
    @include('app.pdf_headerfooter',[
        'logo'=>$tienda->imagen,
        'nombrecomercial'=>$tienda->nombre,
        'direccion'=>$tienda->direccion,
        'ubigeo'=>$tienda->ubigeonombre,
        'tienda'=>$tienda,
    ])
    <div class="titulo">HOJA DE EVALUACIÓN</div>
    <div class="content">
        <table class="tabla_informativa">
            <tr>
                <td style="width:7%;">CLIENTE</td>
                <td style="width:1%;">:</td>
                <td style="width:62%;">{{$prestamocredito->clienteidentificacion}} - {{$prestamocredito->clienteapellidos}}, {{$prestamocredito->clientenombre}}</td>
                <td style="width:7%;">FECHA</td>
                <td style="width:1%;">:</td>
                <td style="width:22%;">{{ date_format(date_create($prestamocredito->fecharegistro), "d/m/Y") }}</td>
            </tr>
            <tr>
                <td>ACTIVIDAD</td>
                <td colspan="5">: <?php echo $prestamolaboral!=''?($prestamolaboral->nombrenegocio!=''?$prestamolaboral->nombrenegocio.' ('.$prestamolaboral->nombre_giro.' - '.$prestamolaboral->nombre_fuenteingreso.')':'&nbsp;'):'&nbsp;'?></td>
            </tr>
        </table>
        <div class="espacio"></div>
        <table class="tabla">
            <tr class="tabla_cabera">
              <td colspan="5" style="text-align:center;">CRÉDITO</td>
            </tr>
            <tr>
              <td class="tabla_titulo" style="text-align:center;">MONTO</td>
              <td class="tabla_titulo" style="text-align:center;">TASA</td>
              <td class="tabla_titulo" style="text-align:center;">Nº DE CUOTAS</td>
              <td class="tabla_titulo" style="text-align:center;">MONTO DE CUOTA</td>
              <td class="tabla_titulo" style="text-align:center;">FRECUENCIA DE PAGOS</td>
            </tr>
            <tr>
              <td style="text-align:center;">{{$prestamocredito->monto}}</td>
              <td style="text-align:center;">{{$prestamocredito->tasa}}</td>
              <td style="text-align:center;">{{$prestamocredito->numerocuota}}</td>
              <td style="text-align:center;">{{$prestamocredito->monedasimbolo}} {{$prestamocredito->cuota}}</td>
              <td style="text-align:center;">{{$prestamocredito->frecuencia_nombre}}</td>
            </tr>
        </table>
        @if($prestamolaboral!='')
        @if(($prestamolaboral->idfuenteingreso==1 && $prestamolaboral->idprestamo_giro==1) || ($prestamolaboral->idfuenteingreso==1 && $prestamolaboral->idprestamo_giro==3) || ($prestamolaboral->idfuenteingreso==1 && $prestamolaboral->idprestamo_giro==2))
            <div class="espacio"></div>
            <table class="tabla">
                <tr class="tabla_cabera">
                  <td style="text-align:center;">INGRESOS</td>
                </tr>
                <tr>
                  <td style="text-align:center;">{{$prestamolaboral->ingreso}}</td>
                </tr>
            </table>
        @elseif($prestamolaboral->idfuenteingreso==2 && $prestamolaboral->idprestamo_giro==1)
            @if(count($laboralventa)>0)
            <div class="espacio"></div>
            <table class="tabla">
                <tr class="tabla_cabera">
                  <td colspan="8" style="text-align:center;">VENTAS</td>
                </tr>
                <tr>
                  <td class="tabla_titulo" style="text-align:center;">Nº</td>
                  <td class="tabla_titulo" style="text-align:center;">PRODUCTO</td>
                  <td class="tabla_titulo" style="text-align:center;">CANTIDAD</td>
                  <td class="tabla_titulo" style="text-align:center;">VALOR UNITARIO</td>
                  <td class="tabla_titulo" style="text-align:center;">VENTA DIARIA</td>
                  <td class="tabla_titulo" style="text-align:center;">VENTA SEMANAL</td>
                  <td class="tabla_titulo" style="text-align:center;">VENTA QUINCENAL</td>
                  <td class="tabla_titulo" style="text-align:center;">VENTA MENSUAL</td>
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
                <tr>
                  <td class="tabla_titulo" colspan="3" style="text-align:right;">TOTAL</td>
                  <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_preciounitario, 2, '.', '')}}</td>
                  <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_preciototal_diario, 2, '.', '')}}</td>
                  <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_preciototal_semanal, 2, '.', '')}}</td>
                  <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_preciototal_quincenal, 2, '.', '')}}</td>
                  <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_preciototal_mensual, 2, '.', '')}}</td>
                </tr>
            </table>
            @endif
            @if(count($laboralcompra)>0)
            <div class="espacio"></div>
            <table class="tabla">
                <tr class="tabla_cabera">
                  <td colspan="8" style="text-align:center;">COSTO DE VENTA</td>
                </tr>
                <tr>
                  <td class="tabla_titulo" style="text-align:center;">Nº</td>
                  <td class="tabla_titulo" style="text-align:center;">PRODUCTO</td>
                  <td class="tabla_titulo" style="text-align:center;">CANTIDAD</td>
                  <td class="tabla_titulo" style="text-align:center;">VALOR UNITARIO</td>
                  <td class="tabla_titulo" style="text-align:center;">VENTA DIARIA</td>
                  <td class="tabla_titulo" style="text-align:center;">VENTA SEMANAL</td>
                  <td class="tabla_titulo" style="text-align:center;">VENTA QUINCENAL</td>
                  <td class="tabla_titulo" style="text-align:center;">VENTA MENSUAL</td>
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
                <tr>
                  <td class="tabla_titulo" colspan="3" style="text-align:right;">TOTAL</td>
                  <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_preciounitario, 2, '.', '')}}</td>
                  <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_preciototal_diario, 2, '.', '')}}</td>
                  <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_preciototal_semanal, 2, '.', '')}}</td>
                  <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_preciototal_quincenal, 2, '.', '')}}</td>
                  <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_preciototal_mensual, 2, '.', '')}}</td>
                </tr>
            </table>
            @endif
        @elseif(($prestamolaboral->idfuenteingreso==2 && $prestamolaboral->idprestamo_giro==2) || ($prestamolaboral->idfuenteingreso==2 && $prestamolaboral->idprestamo_giro==3))
            @if($laboralservicio!='')
            <div class="espacio"></div>
            <table class="tabla">
                <tr class="tabla_cabera">
                  <td colspan="7" style="text-align:center;">INGRESO POR TRANSPORTE</td>
                </tr>
                <tr>
                  <td class="tabla_titulo" style="text-align:center;">BUENO</td>
                  <td class="tabla_titulo" style="text-align:center;">REGULAR</td>
                  <td class="tabla_titulo" style="text-align:center;">MALO</td>
                  <td class="tabla_titulo" style="text-align:center;">PROMEDIO</td>
                  <td class="tabla_titulo" style="text-align:center;">SEMANAL</td>
                  <td class="tabla_titulo" style="text-align:center;">QUINCENAL</td>
                  <td class="tabla_titulo" style="text-align:center;">MENSUAL</td>
                </tr>
                <tr>
                  <td style="text-align:center;">{{$laboralservicio->bueno}}</td>
                  <td style="text-align:center;">{{$laboralservicio->regular}}</td>
                  <td style="text-align:center;">{{$laboralservicio->malo}}</td>
                  <td style="text-align:center;">{{$laboralservicio->promedio}}</td>
                  <td style="text-align:center;">{{$laboralservicio->semanal}}</td>
                  <td style="text-align:center;">{{$laboralservicio->quincenal}}</td>
                  <td style="text-align:center;">{{$laboralservicio->mensual}}</td>
                </tr>
            </table>
            @endif
        @endif
        @endif
        @if(count($laborallegresogasto)>0)
        <div class="espacio"></div>
        <table class="tabla">
            <tr class="tabla_cabera">
              <td colspan="3" style="text-align:center;">GASTOS OPERATIVOS (MENSUALIZADO)</td>
            </tr>
            <tr>
              <td class="tabla_titulo" style="text-align:center;width:2%;">Nº</td>
              <td class="tabla_titulo" style="text-align:center;">CONCEPTO</td>
              <td class="tabla_titulo" style="text-align:center;width:15%;">MONTO</td>
            </tr>
            <?php 
            $total_monto = 0; 
            $num = 1; 
            ?>
            @foreach ($laborallegresogasto as $value)
            <tr>
              <td style="text-align:center;">{{ $num }}</td>
              <td style="text-align:left;">{{ $value->concepto }}</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{ $value->monto }}</td>
            </tr>
            <?php 
            $total_monto = $total_monto+$value->monto; 
            $num++; 
            ?>
            @endforeach
            <tr>
              <td class="tabla_titulo" colspan="2" style="text-align:right;">TOTAL</td>
              <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_monto, 2, '.', '')}}</td>
            </tr>
        </table>
        @endif
        @if(count($laboralegresopago)>0)
        <div class="espacio"></div>
        <table class="tabla">
            <tr class="tabla_cabera">
              <td colspan="3" style="text-align:center;">PAGO DE CUOTAS (BANCOS)</td>
            </tr>
            <tr>
              <td class="tabla_titulo" style="text-align:center;width:2%;">Nº</td>
              <td class="tabla_titulo" style="text-align:center;">INSTITUCIONES FINANCIERAS</td>
              <td class="tabla_titulo" style="text-align:center;width:15%;">CUOTA</td>
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
            <tr>
              <td class="tabla_titulo" colspan="2" style="text-align:right;">TOTAL</td>
              <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_monto, 2, '.', '')}}</td>
            </tr>
        </table>
        @endif
        @if(count($laboralotroingreso)>0)
        <div class="espacio"></div>
        <table class="tabla">
            <tr class="tabla_cabera">
              <td colspan="3" style="text-align:center;">OTRO INGRESOS</td>
            </tr>
            <tr>
              <td class="tabla_titulo" style="text-align:center;width:2%;">Nº</td>
              <td class="tabla_titulo" style="text-align:center;">CONCEPTO</td>
              <td class="tabla_titulo" style="text-align:center;width:15%;">CUOTA</td>
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
            <tr>
              <td class="tabla_titulo" colspan="2" style="text-align:right;">TOTAL</td>
              <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_monto, 2, '.', '')}}</td>
            </tr>
        </table>
        @endif
        @if(count($laborallegresogastofamiliar)>0)
        <div class="espacio"></div>
        <table class="tabla">
            <tr class="tabla_cabera">
              <td colspan="3" style="text-align:center;">GASTOS FAMILIARES (MENSUALIZADO)</td>
            </tr>
            <tr>
              <td class="tabla_titulo" style="text-align:center;width:2%;">Nº</td>
              <td class="tabla_titulo" style="text-align:center;">CONCEPTO</td>
              <td class="tabla_titulo" style="text-align:center;width:15%;">MONTO</td>
            </tr>
            <?php 
            $total_monto = 0; 
            $num = 1; 
            ?>
            @foreach ($laborallegresogastofamiliar as $value)
            <tr>
              <td style="text-align:center;">{{ $num }}</td>
              <td style="text-align:left;">{{ $value->concepto }}</td>
              <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{ $value->monto }}</td>
            </tr>
            <?php 
            $total_monto = $total_monto+$value->monto; 
            $num++; 
            ?>
            @endforeach
            <tr>
              <td class="tabla_titulo" colspan="2" style="text-align:right;">TOTAL</td>
              <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_monto, 2, '.', '')}}</td>
            </tr>
        </table>
        @endif
        @if(count($laboralotrogasto)>0)
        <div class="espacio"></div>
        <table class="tabla">
            <tr class="tabla_cabera">
              <td colspan="3" style="text-align:center;">OTROS GASTOS</td>
            </tr>
            <tr>
              <td class="tabla_titulo" style="text-align:center;width:20px;">Nº</td>
              <td class="tabla_titulo" style="text-align:center;">CONCEPTO</td>
              <td class="tabla_titulo" style="text-align:center;width:70px;">CUOTA</td>
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
            <tr>
              <td class="tabla_titulo" colspan="2" style="text-align:right;">TOTAL</td>
              <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_monto, 2, '.', '')}}</td>
            </tr>
        </table>
        @endif
        <div class="espacio"></div>
        <table style="width:100%;">
            <tr>
              <td style="width:50%;">
                <table class="tabla">
                    <tr class="tabla_cabera">
                      <td colspan="2" style="text-align:center;">ESTADO DE RESULTADOS</td>
                    </tr>
                    <tr>
                      <td class="tabla_titulo" style="text-align:right;width:60%;">INGRESO TOTAL</td>
                      <td style="text-align:right;width:40%;">{{$prestamocredito->monedasimbolo}} {{$prestamolaboral!=''?$prestamolaboral->ingresototal:'0.00'}}</td>
                    </tr>
                    <tr>
                      <td>(-) COSTO DE VENTAS</td>
                      <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{$prestamolaboral!=''?$prestamolaboral->compra:'0.00'}}</td>
                    </tr>
                    <tr>
                      <td class="tabla_titulo" style="text-align:right;">UTILIDAD BRUTA</td>
                      <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{$prestamolaboral!=''?$prestamolaboral->utilidad_bruta:'0.00'}}</td>
                    </tr>
                    <tr>
                      <td>(-) GASTOS OPERATIVOS</td>
                      <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{$prestamolaboral!=''?$prestamolaboral->egresogasto:'0.00'}}</td>
                    </tr>
                    <tr>
                      <td class="tabla_titulo" style="text-align:right;">UTILIDAD OPERATIVA</td>
                      <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{$prestamolaboral!=''?$prestamolaboral->utilidad_operativa:'0.00'}}</td>
                    </tr>
                    <tr>
                      <td>(-) PAGO DE CUOTAS (BANCOS)</td>
                      <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{$prestamolaboral!=''?$prestamolaboral->egresopago:'0.00'}}</td>
                    </tr>
                    <tr>
                      <td class="tabla_titulo" style="text-align:right;">UTILIDAD NETA</td>
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
                      <td class="tabla_titulo" style="text-align:right;">EXCEDENTE NETO MENSUAL</td>
                      <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{$prestamolaboral!=''?$prestamolaboral->ingresomensual:'0.00'}}</td>
                    </tr>
                  
                </table>
              </td>
              <td style="width:50%;">
                <table class="tabla">
                    <tr class="tabla_cabera">
                      <td colspan="2" style="text-align:center;">CRÈDITO</td>
                    </tr>
                    <tr>
                      <td class="tabla_titulo" style="text-align:right;width:60%;">MONTO SOLICITADO</td>
                      <td style="text-align:right;width:40%;">{{$prestamocredito->monedasimbolo}} {{$prestamocredito->monto}}</td>
                    </tr>
                    <tr>
                      <td class="tabla_titulo" style="text-align:right;">INTERES</td>
                      <td style="text-align:right;">
                        {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                        {{$prestamocredito_resultado['prestamocredito']->total_interes}}
                      </td>
                    </tr>
                    @if($prestamocredito_resultado['prestamocredito']->total_segurodesgravamen>0)
                    <tr>
                      <td class="tabla_titulo" style="text-align:right;">SEGURO DESGRAVAMEN</td>
                      <td style="text-align:right;">
                        {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                        {{$prestamocredito_resultado['prestamocredito']->total_segurodesgravamen}}
                      </td>
                    </tr>
                    @endif
                    @if($prestamocredito_resultado['prestamocredito']->total_abono>0)
                    <tr>
                      <td class="tabla_titulo" style="text-align:right;">ABONO</td>
                      <td style="text-align:right;">
                        {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                        {{$prestamocredito_resultado['prestamocredito']->total_abono}}
                      </td>
                    </tr>
                    @endif
                    <tr>
                      <td class="tabla_titulo" style="text-align:right;">MONTO A PAGAR</td>
                      <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{$prestamocredito->total_cuotafinal}}</td>
                    </tr>
                    <tr>
                      <td class="tabla_titulo" style="text-align:right;">Nº DE CUOTAS</td>
                      <td >{{$prestamocredito->numerocuota}} CUOTAS</td>
                    </tr>
                    <tr>
                      <td class="tabla_titulo" style="text-align:right;">FRECUENCUA</td>
                      <td>{{$prestamocredito->frecuencia_nombre}}</td>
                    </tr>
                    <tr>
                      <td class="tabla_titulo" style="text-align:right;">CUOTA</td>
                      <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{$prestamocredito->cuota}}</td>
                    </tr>
                    <tr>
                      <td class="tabla_titulo" style="text-align:right;">CUOTA MENSUALIZADA</td>
                      <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{$prestamocredito_resultado['cuotamensualizada']}}</td>
                    </tr>
                    <tr>
                      <td style="border-width:0px;">&nbsp;</td>
                      <td style="border-width:0px;"></td>
                    </tr>
                    <tr>
                      @if($prestamocredito_resultado['resultado']=='APROBADO')
                      <td colspan="2" style="background-color: #179a4f;color: rgb(255 255 255);text-align:center;border-color:#179a4f;font-weight: bold;font-size: 12px;">
                          CRÉDITO APROBADO
                      </td>                                          
                      @elseif($prestamocredito_resultado['resultado']=='DESAPROBADO')
                      <td colspan="2" style="background-color: #8c1329;color: rgb(255 255 255);text-align:center;border-color:#8c1329;font-weight: bold;font-size: 12px;">
                          CRÉDITO DESAPROBADO
                      </td>                            
                      @endif  
                    </tr>
                    <tr>
                      <td style="border-width:0px;">&nbsp;</td>
                      <td style="border-width:0px;"></td>
                    </tr>
                    @if($prestamocredito_resultado['prestamocredito']->total_segurodesgravamen>0)
                    <tr>
                      <td style="border-width:0px;">&nbsp;</td>
                      <td style="border-width:0px;"></td>
                    </tr>
                    @endif
                    @if($prestamocredito_resultado['prestamocredito']->total_abono==0)
                    <tr>
                      <td style="border-width:0px;">&nbsp;</td>
                      <td style="border-width:0px;"></td>
                    </tr>
                    @endif
                </table>
              </td>
            </tr>
        </table>

                          
  </div>
</body>
</html>