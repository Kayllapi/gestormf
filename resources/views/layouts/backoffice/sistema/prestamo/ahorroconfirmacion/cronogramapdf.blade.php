<!DOCTYPE html>
<html>
<head>
    <title>CRONOGRAMA DE RECAUDACIÓN</title>
    @include('app.pdf_style',['idtienda'=>$tienda->id])
</head>
<body>
    @include('app.pdf_headerfooter',[
              'logo'=>$tienda->imagen,
              'nombrecomercial'=>$prestamoahorro->facturacion_agencianombrecomercial,
              'ruc'=>$prestamoahorro->facturacion_agenciaruc,
              'direccion'=>$prestamoahorro->facturacion_agenciadireccion,
              'ubigeo'=>$prestamoahorro->facturacion_agenciaubigeonombre,
              'tienda'=>$tienda,
    ])
    <div class="titulo">CRONOGRAMA DE RECAUDACIÓN</div>
    <div class="content">
        <table class="tabla_informativa">
            <tr>
                <td class="tabla_informativa_subtitulo" style="width:14%;">CLIENTE</td>
                <td class="tabla_informativa_punto" style="width:1%;">:</td>
                <td class="tabla_informativa_descripcion" style="width:50%;">{{ $prestamoahorro->cliente_nombre }}</td>
                <td class="tabla_informativa_subtitulo" style="width:14%;">CONFIRMADO</td>
                <td class="tabla_informativa_punto" style="width:1%;">:</td>
                <td class="tabla_informativa_descripcion" style="width:20%;">{{ $prestamoahorro->monedasimbolo }} {{ $prestamoahorro->monto }}</td>
            </tr>
            <tr>
                <td class="tabla_informativa_subtitulo">ASESOR</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $prestamoahorro->asesor_nombre }}</td>
                <td class="tabla_informativa_subtitulo">TASA DE INTERÉS</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $prestamoahorro->tasa }}%</td>
            </tr>
            <tr>
                <td class="tabla_informativa_subtitulo">VENTANILLA</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $prestamoahorro->cajero_nombre }}</td>
                <td class="tabla_informativa_subtitulo">FECHA DE INICIO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ date_format(date_create($prestamoahorro->fechainicio),"d/m/Y") }}</td>
            </tr>
            <tr>
                <td class="tabla_informativa_subtitulo">TIPO DE AHORRO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $prestamoahorro->tipoahorronombre }}</td>
                <td class="tabla_informativa_subtitulo">FECHA DE RETIRO</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ date_format(date_create($prestamoahorro->fecharetiro),"d/m/Y") }}</td>
            </tr>
            <tr>
                @if($prestamoahorro->idprestamo_tipoahorro==2)
                <td class="tabla_informativa_subtitulo">FRECUENCIA</td>
                <td class="tabla_informativa_punto">:</td>
                <td class="tabla_informativa_descripcion">{{ $prestamoahorro->frecuencia_nombre }} ({{ $prestamoahorro->numerocuota }} CUOTAS)</td>
                @endif
                <td class="tabla_informativa_subtitulo"></td>
                <td class="tabla_informativa_punto"></td>
                <td class="tabla_informativa_descripcion"></td>
            </tr>
        </table>
        <div class="espacio"></div>
        <table class="tabla">
                <tr class="tabla_cabera">
                    <td style="text-align:center;">Nº</td>
                    <td style="text-align:center;">F. RECAUDACIÓN</td>
                    <td style="text-align:center;">CUOTA</td>
                    <td style="text-align:center;">INTERÉS GANADO</td>
                </tr>
                @foreach ($prestamoahorrodetalle as $value)
                <tr>
                    <td style="text-align:center;">{{ str_pad($value->numero, 2, "0", STR_PAD_LEFT) }}</td>
                    <td style="text-align:center;">{{ date_format(date_create($value->fechaahorro),"d/m/Y") }}</td>
                    <td style="text-align:right;">{{ $value->cuota }}</td>
                    <td style="text-align:right;">{{ $value->interesganado }}</td>
                </tr>
                @endforeach
                <tr class="tabla_resultado">
                    <td colspan="2" style="text-align:right;">TOTAL</td>
                    <td style="text-align:right;">{{$prestamoahorro->total_cuota}}</td>
                    <td style="text-align:right;">{{$prestamoahorro->total_interesganado}}</td>
                </tr>
        </table>
        <div class="espacio"></div>
        <?php
        $cant_firma = 1;
        if($prestamoahorro->conyugeidentificacion!=''){
            $cant_firma++;
        }
        if($prestamoahorro->beneficiarioidentificacion!=''){
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
                                <div>{{$prestamoahorro->clienteapellidos.', '.$prestamoahorro->clientenombre}}</div>
                                <div>DNI: {{$prestamoahorro->clienteidentificacion}}</div>
                            </td>
                            @if($prestamoahorro->conyugeidentificacion!='')
                            <td class="tabla_dato_firma" style="width:34%;">
                                <div class="dato_firma_linea"></div>
                                <div>CONYUGUE</div>
                                <div>{{$prestamoahorro->conyugeapellidos.', '.$prestamoahorro->conyugenombre}}</div>
                                <div>DNI: {{$prestamoahorro->conyugeidentificacion}}</div>
                            </td>
                            @endif
                            @if($prestamoahorro->beneficiarioidentificacion!='')
                            <td class="tabla_dato_firma" style="width:33%;">
                                <div class="dato_firma_linea"></div>
                                <div>GARANTE</div>
                                <div>{{$prestamoahorro->beneficiarioapellidos.' '.$prestamoahorro->beneficiarionombre}}</div>
                                <div>DNI: {{$prestamoahorro->beneficiarioidentificacion}}</div>
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