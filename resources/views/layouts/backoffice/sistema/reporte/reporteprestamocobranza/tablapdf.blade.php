<!DOCTYPE html>
<html>
<head>
    <title>REPORTE DE COBRANZAS</title>
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
    <div class="titulo">REPORTE DE COBRANZAS</div>
    
    <div class="content">
        <table class="tabla_informativa">
            <tr>
                <td style="text-align:right;width:37%;"><b>FECHA:</b> {{Carbon\Carbon::now()->format("d/m/Y h:i A")}}</td>
                <td style="text-align:center;width:2%;"><b>|</b></td>
                <td style="text-align:center;width:22%;"><b>FECHA INICIO:</b> {{$fechainicio!=''?date_format(date_create($fechainicio),"d/m/Y"):''}}</td>
                <td style="text-align:center;width:2%;"><b>|</b></td>
                <td style="text-align:left;width:37%;"><b>FECHA FIN:</b> {{$fechafin!=''?date_format(date_create($fechafin),"d/m/Y"):''}}</td>
            </tr>
        </table>
        <div class="espacio"></div>
        @if(count($prestamocobranzas)==0)
        <div class="mensaje_alerta">No tiene ningún registro!!</div>
        @endif
        @if($listarpor==1)
          @if(count($prestamocobranzas)>0)
            <table class="tabla">
                    <tr class="tabla_cabera">
                        <td style="text-align:center;width:17%;">CAJERO</td>
                        <td style="text-align:center;">CLIENTE</td>
                        <td style="text-align:center;width:10%;">FECHA DE COBRANZA</td>
                        <td style="text-align:center;width:10%;">CÓDIGO DE COBRANZA</td>
                        <td style="text-align:center;width:10%;">CÓDIGO DE CRÉDITO</td>
                        <td style="text-align:center;width:8%;">TOTAL COBRANZA</td>
                    </tr>
                    @foreach($prestamocobranzas as $value)
                    <tr>
                        <td>{{$value['cajero_apellidos']}},<br>{{$value['cajero_nombre']}}</td>
                        <td>{{$value['cliente_identificacion']}}<br>{{$value['cliente']}}</td>
                        <td>{{$value['fecharegistro']}}</td>
                        <td style="text-align:center;">{{$value['codigo']}}</td>
                        <td style="text-align:center;">{{$value['creditocodigo']}}</td>
                        <td style="text-align:right;">{{$value['cronograma_total']}}</td>
                    </tr>
                    @endforeach
                    <tr class="tabla_cabera">
                        <td colspan="5" style="text-align:right;">TOTAL</td>
                        <td style="text-align:right;">{{$totalfinal_total}}</td>
                    </tr>
            </table>
          @endif
        @elseif($listarpor==2)
          @foreach($prestamocobranzas as $value)
                <table class="tabla">
                    <tr class="tabla_cabera">
                        <td style="text-align:left; width:73%;">
                          CLIENTE: {{$value['cliente_identificacion']}} - {{$value['cliente']}}
                        </td>
                    </tr>
                </table>
            <table class="tabla">
                    <tr>
                        <td class="tabla_titulo" style="text-align:center;">CAJERO</td>
                        <td class="tabla_titulo" style="text-align:center;width:10%;">FECHA DE COBRANZA</td>
                        <td class="tabla_titulo" style="text-align:center;width:10%;">CÓDIGO DE COBRANZA</td>
                        <td class="tabla_titulo" style="text-align:center;width:10%;">CÓDIGO DE CRÉDITO</td>
                        <td class="tabla_titulo" style="text-align:center;width:8%;">TOTAL CUOTA</td>
                        <!--td class="tabla_titulo" style="text-align:center;width:8%;">TOTAL ACUENTA</td-->
                        <td class="tabla_titulo" style="text-align:center;width:8%;">TOTAL MORA</td>
                        <td class="tabla_titulo" style="text-align:center;width:8%;">TOTAL COBRANZA</td>
                    </tr>
                    @foreach($value['detalle'] as $valuedetalle)
                    <tr>
                        <td>{{$valuedetalle['cajero']}}</td>
                        <td style="text-align:center;">{{$valuedetalle['fecharegistro']}}</td>
                        <td style="text-align:center;">{{$valuedetalle['codigo']}}</td>
                        <td style="text-align:center;">{{$valuedetalle['creditocodigo']}}</td>
                        <td style="text-align:right;">{{$valuedetalle['cronograma_totalcuota']}}</td>
                        <!--td style="text-align:right;">{{$valuedetalle['cronograma_totalacuenta']}}</td-->
                        <td style="text-align:right;">{{$valuedetalle['cronograma_morapagar']}}</td>
                        <td style="text-align:right;">{{$valuedetalle['cronograma_total']}}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td class="tabla_titulo" colspan="4" style="text-align:right;">TOTAL</td>
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_cuota']}}</td>
                        <!--td class="tabla_titulo" style="text-align:right;">{{$value['total_acuenta']}}</td-->
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_mora']}}</td>
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_total']}}</td>
                    </tr>
            </table>
            <div class="espacio"></div>
          @endforeach
                <table class="tabla">
                <tr class="tabla_cabera">
                    <td style="text-align:right;" colspan="4">TOTAL</td>
                    <td style="text-align:right;width:13%;">{{$totalfinal_cuota}}</td>
                    <td style="text-align:right;width:13%;">{{$totalfinal_mora}}</td>
                    <td style="text-align:right;width:13%;">{{$totalfinal_total}}</td>
                </tr>
                </table>
        @elseif($listarpor==3)
          @foreach($prestamocobranzas as $value)
                <table class="tabla">
                    <tr class="tabla_cabera">
                        <td style="text-align:center;">CAJERO: {{$value['cajero_identificacion']}} - {{$value['cajero']}}</td>
                    </tr>
                </table>
            <table class="tabla">
                    <tr>
                        <td class="tabla_titulo" style="text-align:center;">CLIENTE</td>
                        <td class="tabla_titulo" style="text-align:center;width:10%;">FECHA DE COBRANZA</td>
                        <td class="tabla_titulo" style="text-align:center;width:10%;">CÓDIGO DE COBRANZA</td>
                        <td class="tabla_titulo" style="text-align:center;width:10%;">CÓDIGO DE CRÉDITO</td>
                        <td class="tabla_titulo" style="text-align:center;width:8%;">TOTAL CUOTA</td>
                        <td class="tabla_titulo" style="text-align:center;width:8%;">TOTAL MORA</td>
                        <td class="tabla_titulo" style="text-align:center;width:8%;">TOTAL COBRANZA</td>
                    </tr>
                    @foreach($value['detalle'] as $valuedetalle)
                    <tr>
                        <td>{{$valuedetalle['cliente_identificacion']}} - {{$valuedetalle['cliente']}}</td>
                        <td style="text-align:center;">{{$valuedetalle['fecharegistro']}}</td>
                        <td style="text-align:center;">{{$valuedetalle['codigo']}}</td>
                        <td style="text-align:center;">{{$valuedetalle['creditocodigo']}}</td>
                        <td style="text-align:right;">{{$valuedetalle['cronograma_totalcuota']}}</td>
                        <td style="text-align:right;">{{$valuedetalle['cronograma_morapagar']}}</td>
                        <td style="text-align:right;">{{$valuedetalle['cronograma_total']}}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td class="tabla_titulo" colspan="4" style="text-align:right;">TOTAL</td>
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_cuota']}}</td>
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_mora']}}</td>
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_total']}}</td>
                    </tr>
            </table>
            <div class="espacio"></div>
          @endforeach
                <table class="tabla">
                <tr class="tabla_cabera">
                    <td style="text-align:right;" colspan="4">TOTAL</td>
                    <td style="text-align:right;width:8%;">{{$totalfinal_cuota}}</td>
                    <td style="text-align:right;width:8%;">{{$totalfinal_mora}}</td>
                    <td style="text-align:right;width:8%;">{{$totalfinal_total}}</td>
                </tr>
                </table>
        @endif
    </div>
</body>
</html>