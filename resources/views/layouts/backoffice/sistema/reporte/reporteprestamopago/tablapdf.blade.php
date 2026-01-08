<!DOCTYPE html>
<html>
<head>
    <title>REPORTE DE PAGOS</title>
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
    
    <div class="content">
        <div class="titulo">REPORTE DE PAGOS</div>
        <table class="tabla_informativa">
            <tr>
                <td style="text-align:right;width:37%;"><b>FECHA:</b> {{Carbon\Carbon::now()->format("d/m/Y h:i A")}}</td>
                <td style="text-align:center;width:2%;"><b>|</b></td>
                <td style="text-align:center;width:22%;"><b>FECHA INICIO:</b> {{$request->fechainicio!=''?date_format(date_create($request->fechainicio),"d/m/Y"):''}}</td>
                <td style="text-align:center;width:2%;"><b>|</b></td>
                <td style="text-align:left;width:37%;"><b>FECHA FIN:</b> {{$request->fechafin!=''?date_format(date_create($request->fechafin),"d/m/Y"):''}}</td>
            </tr>
        </table>
        <div class="espacio"></div>
        @if(count($prestamopagos)==0)
        <div class="mensaje_alerta">No tiene ningún registro!!</div>
        @endif
        @if($request->listarpor==1)
            <table class="tabla">
                <tr class="tabla_cabera">
                    <td style="text-align:center; width:10%;">ASESOR</td>
                    <td style="text-align:center;">CLIENTE</td>
                    <td style="text-align:center; width:8%;">ATRASO</td>
                    <td style="text-align:center; width:8%;">DESEMB.</td>
                    <td style="text-align:center; width:8%;">CAPITAL</td>
                    <td style="text-align:center; width:8%;">INTERES</td>
                    <td style="text-align:center; width:8%;">ACUENTA</td>
                    <td style="text-align:center; width:8%;">DEUDA CAPITAL</td>
                    <td style="text-align:center; width:8%;">MORA TOTAL</td>
                    <td style="text-align:center; width:8%;">DEUDA TOTAL</td>
                </tr>
            <?php $prestamopagos = collect($prestamopagos)->sortByDesc('primeratraso'); ?>
            @foreach($prestamopagos as $value)
                <tr>
                    <td style="text-align:left;">{{$value['asesor']}}</td>
                    <td style="text-align:left;">{{$value['cliente']}}</td>
                    <td style="text-align:center;">{{$value['primeratraso']}}</td>
                    <td style="text-align:right;">{{$value['total_desembolso']}}</td>
                    <td style="text-align:right;">{{$value['total_capital']}}</td>
                    <td style="text-align:right;">{{$value['total_interes']}}</td>
                    <td style="text-align:right;">{{$value['total_acuenta']}}</td>
                    <td style="text-align:right;">{{$value['total_deudacapital']}}</td>
                    <td style="text-align:right;">{{$value['total_mora']}}</td>
                    <td style="text-align:right;">{{$value['total_total']}}</td>
                </tr>
            @endforeach
                <tr class="tabla_cabera">
                    <td style="text-align:right;" colspan="3">TOTAL</td>
                    <td style="text-align:right;">{{$totalfinal_desembolso}}</td>
                    <td style="text-align:right;">{{$totalfinal_capital}}</td>
                    <td style="text-align:right;">{{$totalfinal_interes}}</td>
                    <td style="text-align:right;">{{$totalfinal_acuenta}}</td>
                    <td style="text-align:right;">{{$totalfinal_deudacapital}}</td>
                    <td style="text-align:right;">{{$totalfinal_mora}}</td>
                    <td style="text-align:right;">{{$totalfinal_total}}</td>
                </tr>
            </table>
            <div class="espacio"></div>
        @elseif($request->listarpor==2)
            @if(count($prestamopagos)>0)
            <?php $prestamopagos = collect($prestamopagos)->sortByDesc('primeratraso'); ?>
            @foreach($prestamopagos as $value)
                <table class="tabla">
                    <tr class="tabla_cabera">
                        <td style="text-align:left; width:73%;">
                          CLIENTE: {{$value['cliente_identificacion']}} - {{$value['cliente']}} | TELF.: <?php echo $value['cliente_numerotelefono']!=''?$value['cliente_numerotelefono']:'---' ?>
                        </td>
                        <td style="text-align:left; width:27%;">
                          ASESOR: {{$value['asesor']}}
                        </td>
                    </tr>
                </table>
                <table class="tabla">
                    <tr>
                        <td class="tabla_titulo" style="text-align:center;width:5%;">NRO</td>
                        <td class="tabla_titulo" style="text-align:center;">VENCIMIENTO</td>
                        <td class="tabla_titulo" style="text-align:center;width:13%;">CUOTA</td>
                        <td class="tabla_titulo" style="text-align:center;width:13%;">ATRASO</td>
                        <td class="tabla_titulo" style="text-align:center;width:13%;">MORA</td>
                        <td class="tabla_titulo" style="text-align:center;width:13%;">TOTAL</td>
                        <td class="tabla_titulo" style="text-align:center;width:13%;">ACUENTA</td>
                        <td class="tabla_titulo" style="text-align:center;width:13%;">PAGAR</td>
                    </tr>
                    @foreach($value['cuotas'] as $valuecuota)
                        <tr>
                            <td style="text-align:center;">{{$valuecuota['tabla_numero']}}</td>
                            <td style="text-align:center;">{{$valuecuota['tabla_fechavencimiento']}}</td>
                            <td style="text-align:right;">{{$valuecuota['tabla_cuota']}}</td>
                            <td style="text-align:right;">{{$valuecuota['tabla_atraso']}} días</td>
                            <td style="text-align:right;">{{$valuecuota['tabla_mora']}}</td>
                            <td style="text-align:right;">{{$valuecuota['tabla_cuotatotal']}}</td>
                            <td style="text-align:right;">{{$valuecuota['tabla_acuenta']}}</td>
                            <td style="text-align:right;">{{$valuecuota['tabla_cuotaapagar']}}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="tabla_titulo" colspan="2" style="text-align:right;">TOTAL</td>
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_cuota']}}</td>
                        <td class="tabla_titulo"></td>
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_mora']}}</td>
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_total']}}</td>
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_acuenta']}}</td>
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_apagar']}}</td>
                    </tr>
                </table>
                <div class="espacio"></div>
            @endforeach
                <table class="tabla">
                <tr class="tabla_cabera">
                    <td style="text-align:right;" colspan="2">TOTAL</td>
                    <td style="text-align:right;width:13%;">{{$totalfinal_cuota}}</td>
                    <td style="text-align:right;width:13%;"></td>
                    <td style="text-align:right;width:13%;">{{$totalfinal_mora}}</td>
                    <td style="text-align:right;width:13%;">{{$totalfinal_total}}</td>
                    <td style="text-align:right;width:13%;">{{$totalfinal_acuenta}}</td>
                    <td style="text-align:right;width:13%;">{{$totalfinal_apagar}}</td>
                </tr>
                </table>
            @endif
        @elseif($request->listarpor==3)
            @if(count($prestamopagos)>0)
            <?php $prestamopagos = collect($prestamopagos)->sortByDesc('primeratraso'); ?>
            @foreach($prestamopagos as $value)
                <table class="tabla">
                    <tr class="tabla_cabera">
                        <td style="text-align:left; width:35%;">
                          ASESOR: {{$value['asesor']}}
                        </td>
                        <td style="text-align:left; width:65%;">
                          CLIENTE: {{$value['cliente_identificacion']}} - {{$value['cliente']}} | TELF.: <?php echo $value['cliente_numerotelefono']!=''?$value['cliente_numerotelefono']:'---' ?>
                        </td>
                    </tr>
                </table>
                <table class="tabla">
                    <tr>
                        <td class="tabla_titulo" style="text-align:center;width:5%;">NRO</td>
                        <td class="tabla_titulo" style="text-align:center;">VENCIMIENTO</td>
                        <td class="tabla_titulo" style="text-align:center;width:13%;">CUOTA</td>
                        <td class="tabla_titulo" style="text-align:center;width:13%;">ATRASO</td>
                        <td class="tabla_titulo" style="text-align:center;width:13%;">MORA</td>
                        <td class="tabla_titulo" style="text-align:center;width:13%;">TOTAL</td>
                        <td class="tabla_titulo" style="text-align:center;width:13%;">ACUENTA</td>
                        <td class="tabla_titulo" style="text-align:center;width:13%;">PAGAR</td>
                    </tr>
                    @foreach($value['cuotas'] as $valuecuota)
                        <tr>
                            <td style="text-align:center;">{{$valuecuota['tabla_numero']}}</td>
                            <td style="text-align:center;">{{$valuecuota['tabla_fechavencimiento']}}</td>
                            <td style="text-align:right;">{{$valuecuota['tabla_cuota']}}</td>
                            <td style="text-align:right;">{{$valuecuota['tabla_atraso']}} días</td>
                            <td style="text-align:right;">{{$valuecuota['tabla_mora']}}</td>
                            <td style="text-align:right;">{{$valuecuota['tabla_cuotatotal']}}</td>
                            <td style="text-align:right;">{{$valuecuota['tabla_acuenta']}}</td>
                            <td style="text-align:right;">{{$valuecuota['tabla_cuotaapagar']}}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="tabla_titulo" colspan="2" style="text-align:right;">TOTAL</td>
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_cuota']}}</td>
                        <td class="tabla_titulo"></td>
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_mora']}}</td>
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_total']}}</td>
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_acuenta']}}</td>
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_apagar']}}</td>
                    </tr>
                </table>
                <div class="espacio"></div>
            @endforeach
                <table class="tabla">
                <tr class="tabla_cabera">
                    <td style="text-align:right;" colspan="2">TOTAL</td>
                    <td style="text-align:right;width:13%;">{{$totalfinal_cuota}}</td>
                    <td style="text-align:right;width:13%;"></td>
                    <td style="text-align:right;width:13%;">{{$totalfinal_mora}}</td>
                    <td style="text-align:right;width:13%;">{{$totalfinal_total}}</td>
                    <td style="text-align:right;width:13%;">{{$totalfinal_acuenta}}</td>
                    <td style="text-align:right;width:13%;">{{$totalfinal_apagar}}</td>
                </tr>
                </table>
            @endif
        @endif
    </div>
</body>
</html>