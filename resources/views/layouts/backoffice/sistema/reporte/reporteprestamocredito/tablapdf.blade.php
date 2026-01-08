<!DOCTYPE html>
<html>
<head>
    <title>REPORTE DE CRÉDITO</title>
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
    <div class="titulo">REPORTE DE
            @if($estadocredito==2)
                CRÉDITOS PENDIENTES
            @elseif($estadocredito==3)
                CRÉDITOS CANCELADOS
            @else
                CRÉDITOS DESEMBOLSADOS
            @endif
      </div>
    
    <div class="content">
        <table class="tabla_informativa">
            <tr>
                <td style="text-align:right;width:38%;"><b>FECHA:</b> {{Carbon\Carbon::now()->format("d/m/Y h:i A")}}</td>
                <td style="text-align:center;width:2%;"><b>|</b></td>
                <td style="text-align:center;width:19%;"><b>FECHA INICIO:</b> {{$fechainicio!=''?date_format(date_create($fechainicio),"d/m/Y"):''}}</td>
                <td style="text-align:center;width:2%;"><b>|</b></td>
                <td style="text-align:left;width:38%;"><b>FECHA FIN:</b> {{$fechafin!=''?date_format(date_create($fechafin),"d/m/Y"):''}}</td>
            </tr>
        </table>
        <div class="espacio"></div>
        @if(count($prestamocreditos)==0)
        <div class="mensaje_alerta">No tiene ningún registro!!</div>
        @endif
        @if($listarpor==1)
            @foreach($prestamocreditos as $value)
            <table class="tabla">
                    <tr class="tabla_cabera">
                        <td class="tabla_titulo" style="text-align:center;width:1%;">Nº</td>
                        <td style="text-align:center;">CLIENTE (DNI - APELLIDOS, NOMBRES)</td>
                        <td width="8%" style="text-align:center;">CÓDIGO CRÉDITO</td>
                        <td width="8%" style="text-align:center;">FECHA DESEM.</td>
                        <td width="8%" style="text-align:center;">MONTO DESEM.</td>
                        <td width="8%" style="text-align:center;">TOTAL CANCE.</td>
                        <td width="8%" style="text-align:center;">TOTAL PENDI.</td>
                        <td width="8%" style="text-align:center;">TOTAL INTERES</td>
                        @if(configuracion($tienda->id,'prestamo_estadodescuentointeres')['resultado']=='CORRECTO')
                        <td width="8%" style="text-align:center;">DESC INTERES</td>
                        @endif
                        <td width="8%" style="text-align:center;">TOTAL PAGAR</td>
                        <!--td width="9%" style="text-align:center;">TOTAL CANCELADO</td>
                        <td width="9%" style="text-align:center;">TOTAL PENDIENTE</td-->
                    </tr>
                    <?php $i=1; ?>
                    @foreach($value['detalle'] as $valuedetalle)
                    <tr>
                        <td style="text-align:center;"><?php echo $i; ?></td>
                        <td>{{$valuedetalle['clienteidentificacion']}} - {{$valuedetalle['cliente']}}</td>
                        <td style="text-align:center;">{{$valuedetalle['creditocodigo']}}</td>
                        <td style="text-align:center;">{{$valuedetalle['fechadesembolso']}}</td>
                        <td style="text-align:right;">{{$valuedetalle['desembolso']}}</td>
                        <td style="text-align:right;">{{$valuedetalle['cancelado']}}</td>
                        <td style="text-align:right;">{{$valuedetalle['pendiente']}}</td>
                        <td style="text-align:right;">{{$valuedetalle['interes']}}</td>
                        @if(configuracion($tienda->id,'prestamo_estadodescuentointeres')['resultado']=='CORRECTO')
                        <td style="text-align:right;">{{$valuedetalle['interesdescontado']}}</td>
                        @endif
                        <td style="text-align:right;">{{$valuedetalle['pagar']}}</td>
                        <!--td style="text-align:right;">{{$valuedetalle['cancelado']}}</td>
                        <td style="text-align:right;">{{$valuedetalle['pendiente']}}</td-->
                    </tr>
                    <?php $i++; ?>
                    @endforeach
                    <tr class="tabla_cabera">
                        <td style="text-align:right;" colspan="4">TOTAL</td>
                        <td style="text-align:right;">{{$value['total_desembolso']}}</td>
                        <td style="text-align:right;">{{$value['total_cancelado']}}</td>
                        <td style="text-align:right;">{{$value['total_pendiente']}}</td>
                        <td style="text-align:right;">{{$value['total_interes']}}</td>
                        @if(configuracion($tienda->id,'prestamo_estadodescuentointeres')['resultado']=='CORRECTO')
                        <td style="text-align:right;">{{$value['total_interesdescontado']}}</td>
                        @endif
                        <td style="text-align:right;">{{$value['total_pagar']}}</td>
                        <!--td style="text-align:right;">{{$value['total_cancelado']}}</td>
                        <td style="text-align:right;">{{$value['total_pendiente']}}</td-->
                    </tr>
            </table>
            @endforeach
        @elseif($listarpor==2)

            @foreach($prestamocreditos as $value)
            <table class="tabla">
                    <tr class="tabla_cabera">
                        <td class="tabla_titulo" style="text-align:center;width:1%;">Nº</td>
                        <td style="text-align:center;">CLIENTE (DNI - APELLIDOS, NOMBRES)</td>
                        <td width="8%" style="text-align:center;">CÓDIGO CRÉDITO</td>
                        <td width="8%" style="text-align:center;">FECHA DESEM.</td>
                        <td width="8%" style="text-align:center;">MONTO DESEM.</td>
                        <td width="8%" style="text-align:center;">TOTAL CANCE.</td>
                        <td width="8%" style="text-align:center;">TOTAL PENDI.</td>
                        @if(modulo($tienda->id,Auth::user()->id,'reportecredito_listarporasesor')['resultado']=='CORRECTO')
                        @else
                        <td width="8%" style="text-align:center;">TOTAL INTERES</td>
                        @if(configuracion($tienda->id,'prestamo_estadodescuentointeres')['resultado']=='CORRECTO')
                        <td width="8%" style="text-align:center;">DESC INTERES</td>
                        @endif
                        <td width="8%" style="text-align:center;">TOTAL PAGAR</td>
                        @endif
                    </tr>
                    <?php $i=1; ?>
                    @foreach($value['detalle'] as $valuedetalle)
                    <tr>
                        <td style="text-align:center;"><?php echo $i; ?></td>
                        <td>{{$valuedetalle['clienteidentificacion']}} - {{$valuedetalle['cliente']}}</td>
                        <td style="text-align:center;">{{$valuedetalle['creditocodigo']}}</td>
                        <td style="text-align:center;">{{$valuedetalle['fechadesembolso']}}</td>
                        <td style="text-align:right;">{{$valuedetalle['desembolso']}}</td>
                        <td style="text-align:right;">{{$valuedetalle['cancelado']}}</td>
                        <td style="text-align:right;">{{$valuedetalle['pendiente']}}</td>
                        @if(modulo($tienda->id,Auth::user()->id,'reportecredito_listarporasesor')['resultado']=='CORRECTO')
                        @else
                        <td style="text-align:right;">{{$valuedetalle['interes']}}</td>
                        @if(configuracion($tienda->id,'prestamo_estadodescuentointeres')['resultado']=='CORRECTO')
                        <td style="text-align:right;">{{$valuedetalle['interesdescontado']}}</td>
                        @endif
                        <td style="text-align:right;">{{$valuedetalle['pagar']}}</td>
                        @endif
                    </tr>
                    <?php $i++; ?>
                    @endforeach
                    <tr class="tabla_cabera">
                        <td style="text-align:right;" colspan="4">TOTAL</td>
                        <td style="text-align:right;">{{$value['total_desembolso']}}</td>
                        <td style="text-align:right;">{{$value['total_cancelado']}}</td>
                        <td style="text-align:right;">{{$value['total_pendiente']}}</td>
                        @if(modulo($tienda->id,Auth::user()->id,'reportecredito_listarporasesor')['resultado']=='CORRECTO')
                        @else
                        <td style="text-align:right;">{{$value['total_interes']}}</td>
                        @if(configuracion($tienda->id,'prestamo_estadodescuentointeres')['resultado']=='CORRECTO')
                        <td style="text-align:right;">{{$value['total_interesdescontado']}}</td>
                        @endif
                        <td style="text-align:right;">{{$value['total_pagar']}}</td>
                        @endif
                    </tr>
            </table>
            @endforeach
        @elseif($listarpor==3)
          @foreach($prestamocreditos as $value)
            <table class="tabla">
                    <tr class="tabla_cabera">
                        <td style="text-align:center;">ASESOR: {{$value['asesor_identificacion']}} - {{$value['asesor']}}</td>
                    </tr>
            </table>
            <table class="tabla">
                    <tr>
                        <td class="tabla_titulo" style="text-align:center;width:1%;">Nº</td>
                        <td class="tabla_titulo" style="text-align:center;">CLIENTE (DNI - APELLIDOS, NOMBRES)</td>
                        <td class="tabla_titulo" width="8%" style="text-align:center;">CÓDIGO CRÉDITO</td>
                        <td class="tabla_titulo" width="8%" style="text-align:center;">FECHA DESEM.</td>
                        <td class="tabla_titulo" width="8%" style="text-align:center;">MONTO DESEM.</td>
                        <td class="tabla_titulo" width="8%" style="text-align:center;">TOTAL CANCE.</td>
                        <td class="tabla_titulo" width="8%" style="text-align:center;">TOTAL PENDI.</td>
                        @if(modulo($tienda->id,Auth::user()->id,'reportecredito_listarporasesor')['resultado']=='CORRECTO')
                        @else
                        <td class="tabla_titulo" width="8%" style="text-align:center;">TOTAL INTERES</td>
                        @if(configuracion($tienda->id,'prestamo_estadodescuentointeres')['resultado']=='CORRECTO')
                        <td class="tabla_titulo" width="8%" style="text-align:center;">DESC INTERES</td>
                        @endif
                        <td class="tabla_titulo" width="8%" style="text-align:center;">TOTAL PAGAR</td>
                        @endif
                    </tr>
                    <?php $i=1; ?>
                    @foreach($value['detalle'] as $valuedetalle)
                    <tr>
                        <td style="text-align:center;"><?php echo $i; ?></td>
                        <td>{{$valuedetalle['clienteidentificacion']}} - {{$valuedetalle['cliente']}} <?php echo $valuedetalle['estadocobranza']; ?></td>
                        <td style="text-align:center;">{{$valuedetalle['creditocodigo']}}</td>
                        <td style="text-align:center;">{{$valuedetalle['fechadesembolso']}}</td>
                        <td style="text-align:right;">{{$valuedetalle['desembolso']}}</td>
                        <td style="text-align:right;">{{$valuedetalle['cancelado']}}</td>
                        <td style="text-align:right;">{{$valuedetalle['pendiente']}}</td>
                        @if(modulo($tienda->id,Auth::user()->id,'reportecredito_listarporasesor')['resultado']=='CORRECTO')
                        @else
                        <td style="text-align:right;">{{$valuedetalle['interes']}}</td>
                        @if(configuracion($tienda->id,'prestamo_estadodescuentointeres')['resultado']=='CORRECTO')
                        <td style="text-align:right;">{{$valuedetalle['interesdescontado']}}</td>
                        @endif
                        <td style="text-align:right;">{{$valuedetalle['pagar']}}</td>
                        @endif
                    </tr>
                    <?php $i++; ?>
                    @endforeach
                    <tr class="tabla_cabera">
                        <td style="text-align:right;" colspan="4">TOTAL</td>
                        <td style="text-align:right;">{{$value['total_desembolso']}}</td>
                        <td style="text-align:right;">{{$value['total_cancelado']}}</td>
                        <td style="text-align:right;">{{$value['total_pendiente']}}</td>
                        @if(modulo($tienda->id,Auth::user()->id,'reportecredito_listarporasesor')['resultado']=='CORRECTO')
                        @else
                        <td style="text-align:right;">{{$value['total_interes']}}</td>
                        @if(configuracion($tienda->id,'prestamo_estadodescuentointeres')['resultado']=='CORRECTO')
                        <td style="text-align:right;">{{$value['total_interesdescontado']}}</td>
                        @endif
                        <td style="text-align:right;">{{$value['total_pagar']}}</td>
                        @endif
                    </tr>
            </table>
            <div class="espacio"></div>
          @endforeach
          @if(count($prestamocreditos)>1)
                <table class="tabla">
                <tr class="tabla_cabera">
                    <td style="text-align:right;" colspan="4">TOTAL</td>
                    <td style="text-align:right;width:8%;">{{$totalfinal_desembolso}}</td>
                    <td style="text-align:right;width:8%;">{{$totalfinal_cancelado}}</td>
                    <td style="text-align:right;width:8%;">{{$totalfinal_pendiente}}</td>
                    @if(modulo($tienda->id,Auth::user()->id,'reportecredito_listarporasesor')['resultado']=='CORRECTO')
                    @else
                    <td style="text-align:right;width:8%;">{{$totalfinal_interes}}</td>
                    @if(configuracion($tienda->id,'prestamo_estadodescuentointeres')['resultado']=='CORRECTO')
                    <td style="text-align:right;width:8%;">{{$totalfinal_interesdescontado}}</td>
                    @endif
                    <td style="text-align:right;width:8%;">{{$totalfinal_pagar}}</td>
                    @endif
                </tr>
                </table>
          @endif
        @endif
    </div>
</body>
</html>