<!DOCTYPE html>
<html>
<head>
    <title>CRONOGRAMA DE PAGOS</title>
    @include('app.pdf_style',['idtienda'=>$tienda->id])
</head>
<body>
    @include('app.pdf_headerfooter',[
        'logo'=>$prestamodesembolso->facturacion_agencialogo,
        'nombrecomercial'=>$prestamodesembolso->facturacion_agencianombrecomercial,
        'ruc'=>$prestamodesembolso->facturacion_agenciaruc,
        'direccion'=>$prestamodesembolso->facturacion_agenciadireccion,
        'ubigeo'=>$prestamodesembolso->facturacion_agenciaubigeonombre,
        'tienda'=>$tienda,
    ])
    <div class="titulo">CRONOGRAMA DE PAGOS</div>
    <div class="content">
        <table class="tabla_informativa">
            <tr>
                <td class="tabla_informativa_subtitulo" style="width:14%;">CLIENTE</td>
                <td class="tabla_informativa_punto" style="width:1%;">:</td>
                <td class="tabla_informativa_descripcion" style="width:50%;">{{ $prestamodesembolso->cliente_nombre }}</td>
                <td class="tabla_informativa_subtitulo" style="width:14%;">MONEDA</td>
                <td class="tabla_informativa_punto" style="width:1%;">:</td>
                <td class="tabla_informativa_descripcion" style="width:20%;">{{ $prestamodesembolso->monedanombre }}</td>
            </tr>
            <tr>
                <td class="tabla_informativa_subtitulo">DNI</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $prestamodesembolso->facturacion_cliente_identificacion }}</td>
                <td class="tabla_informativa_subtitulo">DESEMBOLSADO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $prestamodesembolso->monedasimbolo }} {{ $prestamodesembolso->monto }}</td>
            </tr>
            <tr>
                <td class="tabla_informativa_subtitulo">ASESOR</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $prestamodesembolso->asesor_nombre }}</td>
                <td class="tabla_informativa_subtitulo">Nº DE CUOTAS</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $prestamodesembolso->numerocuota }} CUOTAS</td>
            </tr>
            <tr>
                <td class="tabla_informativa_subtitulo">VENTANILLA</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $prestamodesembolso->cajero_nombre }}</td>
                <td class="tabla_informativa_subtitulo">FRECUENCIA</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $prestamodesembolso->frecuencia_nombre }}</td>
            </tr>
            <tr>
                <td class="tabla_informativa_subtitulo">TASA DE INTERÉS</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $prestamodesembolso->tasa }}%</td>
                <td class="tabla_informativa_subtitulo">FECHA DE INICIO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ date_format(date_create($prestamodesembolso->fechainicio),"d/m/Y") }}</td>
            </tr>
        </table>
        <div class="espacio"></div>
        <table class="tabla">
                <tr class="tabla_cabera">
                    <td style="text-align:center;">Nº</td>
                    <td style="text-align:center;">F.VENCIMIENTO</td>
                    <td style="text-align:center;">CAPITAL</td>
                    <td style="text-align:center;">INTERÉS</td>
                    @if($prestamodesembolso->total_segurodesgravamen>0)
                    <td style="text-align:center;">SEGURO DESGRAVAMEN</td>
                    @endif
                    @if($prestamodesembolso->total_gastoadministrativo>0)
                    <td style="text-align:center;">GASTO ADMINISTRATIVO</td>
                    @endif
                    @if($prestamodesembolso->total_acumulado>0)
                    <td style="text-align:center;">ACUMULADO</td>
                    @endif
                    <td style="text-align:center;">CUOTA</td>
                    @if($prestamodesembolso->total_abono>0)
                    <td style="text-align:center;">ABONO</td>
                    <td style="text-align:center;">TOTAL</td>
                    @endif
                </tr>
                @foreach ($prestamodesembolsodetalle as $value)
                <tr>
                    <td style="text-align:center;">{{ str_pad($value->numero, 2, "0", STR_PAD_LEFT) }}</td>
                    <td style="text-align:center;">{{ date_format(date_create($value->fechavencimiento),"d/m/Y") }}</td>
                    <td style="text-align:right;">{{ $value->amortizacion }}</td>
                    <td style="text-align:right;">{{ $value->interes }}</td>
                    @if($prestamodesembolso->total_segurodesgravamen>0)
                    <td style="text-align:right;">{{ $value->seguro }}</td>
                    @endif
                    @if($prestamodesembolso->total_gastoadministrativo>0)
                    <td style="text-align:right;">{{ $value->gastoadministrativo }}</td>
                    @endif
                    @if($prestamodesembolso->total_acumulado>0)
                    <td style="text-align:right;">{{ $value->cuotanormal }} ({{ $value->acumulado }})</td>
                    @endif
                    <td style="text-align:right;">{{ $value->total }}</td>
                    @if($prestamodesembolso->total_abono>0)
                    <td style="text-align:right;">{{ $value->abono }}</td>
                    <td style="text-align:right;">{{ $value->totalfinal }}</td>
                    @endif
                </tr>
                @endforeach
                <tr class="tabla_resultado">
                    <td colspan="2" style="text-align:right;">TOTAL</td>
                    <td style="text-align:right;">{{$prestamodesembolso->total_amortizacion}}</td>
                    <td style="text-align:right;">{{$prestamodesembolso->total_interes}}</td>
                    @if($prestamodesembolso->total_segurodesgravamen>0)
                    <td style="text-align:right;">{{$prestamodesembolso->total_segurodesgravamen}}</td>
                    @endif
                    @if($prestamodesembolso->total_gastoadministrativo>0)
                    <td style="text-align:right;">{{$prestamodesembolso->total_gastoadministrativo}}</td>
                    @endif
                    @if($prestamodesembolso->total_acumulado>0)
                    <td style="text-align:right;">{{$prestamodesembolso->total_cuotanormal}} ({{$prestamodesembolso->total_acumulado}})</td>
                    @endif
                    <td style="text-align:right;">{{$prestamodesembolso->total_cuotafinal}}</td>
                    @if($prestamodesembolso->total_abono>0)
                    <td style="text-align:right;">{{$prestamodesembolso->total_abono}}</td>
                    <td style="text-align:right;">{{$prestamodesembolso->total_cuotafinaltotal}}</td>
                    @endif
                </tr>
        </table>
        <div class="espacio"></div>
        <?php
        $cant_firma = 1;
        if($prestamodesembolso->conyugeidentificacion!=''){
            $cant_firma++;
        }
        if($prestamodesembolso->garanteidentificacion!=''){
            $cant_firma++;
        }
        ?>
        <table class="tabla_informativa tabla_firma">
            <tr>
                <td>
                    <table class="tabla_informativa" style="<?php echo $cant_firma==1?'width:33%':($cant_firma==2?'width:66%':($cant_firma==3?'width:100%':'100%')) ?>;margin:auto;">
                        <tr>
                            <td class="tabla_dato_firma" style="width:33%;">
                                <div class="dato_firma_linea"></div>
                                <div>CLIENTE</div>
                                <div>{{$prestamodesembolso->facturacion_cliente_apellidos.', '.$prestamodesembolso->facturacion_cliente_nombre}}</div>
                                <div>DNI: {{$prestamodesembolso->facturacion_cliente_identificacion}}</div>
                            </td>
                            @if($prestamodesembolso->conyugeidentificacion!='')
                            <td class="tabla_dato_firma" style="width:34%;">
                                <div class="dato_firma_linea"></div>
                                <div>CONYUGUE</div>
                                <div>{{$prestamodesembolso->conyugeapellidos.', '.$prestamodesembolso->conyugenombre}}</div>
                                <div>DNI: {{$prestamodesembolso->conyugeidentificacion}}</div>
                            </td>
                            @endif
                            @if($prestamodesembolso->garanteidentificacion!='')
                            <td class="tabla_dato_firma" style="width:33%;">
                                <div class="dato_firma_linea"></div>
                                <div>GARANTE</div>
                                <div>{{$prestamodesembolso->garanteapellidos.' '.$prestamodesembolso->garantenombre}}</div>
                                <div>DNI: {{$prestamodesembolso->garanteidentificacion}}</div>
                            </td>
                            @endif
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>