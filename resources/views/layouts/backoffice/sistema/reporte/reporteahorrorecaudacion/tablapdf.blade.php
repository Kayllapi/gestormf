<!DOCTYPE html>
<html>
<head>
    <title>REPORTE DE RECAUDACIONES</title>
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
    <div class="titulo">REPORTE DE RECAUDACIONES</div>
    
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
        @if(count($ahorrorecaudaciones)==0)
        <div class="mensaje_alerta">No tiene ningún registro!!</div>
        @endif
        @if($listarpor==1)
          @if(count($ahorrorecaudaciones)>0)
            <table class="tabla">
                    <tr class="tabla_cabera">
                        <td class="tabla_titulo" style="text-align:center;width:12%;">TIPO</td>
                        <td style="text-align:center;width:17%;">CAJERO</td>
                        <td style="text-align:center;">CLIENTE</td>
                        <td style="text-align:center;width:10%;">FECHA DE RECAUDACIÓN</td>
                        <td style="text-align:center;width:10%;">CÓDIGO DE AHORRO</td>
                        <td style="text-align:center;width:10%;">CÓDIGO</td>
                        <td style="text-align:center;width:8%;">TOTAL</td>
                    </tr>
                    @foreach($ahorrorecaudaciones as $value)
                    <tr>
                        <td style="text-align:center;">{{$value['tipo']}}</td>
                        <td>{{$value['cajero_apellidos']}},<br>{{$value['cajero_nombre']}}</td>
                        <td>{{$value['cliente_identificacion']}}<br>{{$value['cliente']}}</td>
                        <td>{{$value['fecharegistro']}}</td>
                        <td style="text-align:center;">{{$value['ahorrocodigo']}}</td>
                        <td style="text-align:center;">{{$value['codigo']}}</td>
                        <td style="text-align:right;">{{$value['monto_total']}}</td>
                    </tr>
                    @endforeach
                    <tr class="tabla_cabera">
                        <td colspan="6" style="text-align:right;">TOTAL SALDO</td>
                        <td style="text-align:right;">{{$totalfinal_total}}</td>
                    </tr>
            </table>
          @endif
        @elseif($listarpor==2)
          @foreach($ahorrorecaudaciones as $value)
                <table class="tabla">
                    <tr class="tabla_cabera">
                        <td style="text-align:center;">
                          CLIENTE: {{$value['cliente_identificacion']}} - {{$value['cliente']}}
                        </td>
                    </tr>
                </table>
            <table class="tabla">
                    <tr>
                        <td class="tabla_titulo" style="text-align:center;width:12%;">TIPO</td>
                        <td class="tabla_titulo" style="text-align:center;">CAJERO</td>
                        <td class="tabla_titulo" style="text-align:center;width:10%;">FECHA DE RECAUDACIÓN</td>
                        <td class="tabla_titulo" style="text-align:center;width:10%;">CÓDIGO DE AHORRO</td>
                        <td class="tabla_titulo" style="text-align:center;width:10%;">CÓDIGO</td>
                        <td class="tabla_titulo" style="text-align:center;width:10%;">TOTAL</td>
                    </tr>
                    @foreach($value['detalle'] as $valuedetalle)
                    <tr>
                        <td style="text-align:center;">{{$valuedetalle['tipo']}}</td>
                        <td>{{$valuedetalle['cajero']}}</td>
                        <td style="text-align:left;">{{$valuedetalle['fecharegistro']}}</td>
                        <td style="text-align:center;">{{$valuedetalle['codigo']}}</td>
                        <td style="text-align:center;">{{$valuedetalle['ahorrocodigo']}}</td>
                        <td style="text-align:right;">{{$valuedetalle['monto_total']}}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td class="tabla_titulo" colspan="5" style="text-align:right;">TOTAL RECAUDACIÓN</td>
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_recaudacion']}}</td>
                    </tr>
                    <tr>
                        <td class="tabla_titulo" colspan="5" style="text-align:right;">TOTAL RETIRO</td>
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_retiro']}}</td>
                    </tr>
                    <tr>
                        <td class="tabla_titulo" colspan="5" style="text-align:right;">TOTAL SALDO</td>
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_total']}}</td>
                    </tr>
            </table>
            <div class="espacio"></div>
          @endforeach
                <table class="tabla">
                <tr class="tabla_cabera">
                    <td style="text-align:right;" colspan="5">TOTAL</td>
                    <td style="text-align:right;width:10%;">{{$totalfinal_total}}</td>
                </tr>
                </table>
        @elseif($listarpor==3)
          @foreach($ahorrorecaudaciones as $value)
                <table class="tabla">
                    <tr class="tabla_cabera">
                        <td style="text-align:center;">CAJERO: {{$value['cajero_identificacion']}} - {{$value['cajero']}}</td>
                    </tr>
                </table>
                <table class="tabla">
                    <tr>
                        <td class="tabla_titulo" style="text-align:center;width:12%;">TIPO</td>
                        <td class="tabla_titulo" style="text-align:center;">CLIENTE</td>
                        <td class="tabla_titulo" style="text-align:center;width:10%;">FECHA DE RECAUDACIÓN</td>
                        <td class="tabla_titulo" style="text-align:center;width:10%;">CÓDIGO DE AHORRO</td>
                        <td class="tabla_titulo" style="text-align:center;width:10%;">CÓDIGO</td>
                        <td class="tabla_titulo" style="text-align:center;width:10%;">TOTAL</td>
                    </tr>
                    @foreach($value['detalle'] as $valuedetalle)
                    <tr>
                        <td style="text-align:center;">{{$valuedetalle['tipo']}}</td>
                        <td>{{$valuedetalle['cliente_identificacion']}} - {{$valuedetalle['cliente']}}</td>
                        <td style="text-align:left;">{{$valuedetalle['fecharegistro']}}</td>
                        <td style="text-align:center;">{{$valuedetalle['ahorrocodigo']}}</td>
                        <td style="text-align:center;">{{$valuedetalle['codigo']}}</td>
                        <td style="text-align:right;">{{$valuedetalle['monto_total']}}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td class="tabla_titulo" colspan="5" style="text-align:right;">TOTAL RECAUDACIÓN</td>
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_recaudacion']}}</td>
                    </tr>
                    <tr>
                        <td class="tabla_titulo" colspan="5" style="text-align:right;">TOTAL RETIRO</td>
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_retiro']}}</td>
                    </tr>
                    <tr>
                        <td class="tabla_titulo" colspan="5" style="text-align:right;">TOTAL SALDO</td>
                        <td class="tabla_titulo" style="text-align:right;">{{$value['total_total']}}</td>
                    </tr>
                </table>
                <div class="espacio"></div>
          @endforeach
                <table class="tabla">
                <tr class="tabla_cabera">
                    <td style="text-align:right;" colspan="5">TOTAL</td>
                    <td style="text-align:right;width:10%;">{{$totalfinal_total}}</td>
                </tr>
                </table>
        @endif
    </div>
</body>
</html>