<!DOCTYPE html>
<html>
<head>
    <title>REPORTE DE MOVIMIENTOS</title>
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
    <div class="titulo">REPORTE DE MOVIMIENTOS</div>
    
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
        @if(count($data)==0)
        <div class="mensaje_alerta">No tiene ningún registro!!</div>
        @endif
        @if($listarpor==1)
            @foreach($data as $value)
            <table class="tabla">
                <tr class="tabla_cabera">
                    <td style="text-align:center;">{{$value['tipo']}}</td>
                </tr>
            </table>
            <table class="tabla">
                <tr class="tabla_cabera">
                    <td style="text-align:center;">PERSONA RESPONSABLE</td>
                    <td style="text-align:center;">PERSONA ENTREGADO</td>
                    <td style="text-align:center;" width="8%">CÓDIGO</td>
                    <td style="text-align:center;">CONCEPTO</td>
                    <td style="text-align:center;">DESCRIPCIÓN</td>
                    <td style="text-align:center;" width="14%">FECHA CONFIRMADO</td>
                    <td style="text-align:center;" width="8%">MONTO</td>
                </tr>
                @foreach($value['detalle'] as $valuedetalle)
                <tr>
                    <td style="text-align:left;">{{$valuedetalle['responsable']}}</td>
                    <td style="text-align:left;">{{$valuedetalle['responsableentregado']}}</td>
                    <td style="text-align:center;">{{$valuedetalle['codigo']}}</td>
                    <td style="text-align:left;">{{$valuedetalle['concepto']}}</td>
                    <td style="text-align:left;">{{$valuedetalle['descripcion']}}</td>
                    <td style="text-align:center;">{{$valuedetalle['fechaconfirmado']}}</td>
                    <td style="text-align:right;">{{$valuedetalle['monto']}}</td>
                </tr>
                @endforeach
                <tr class="tabla_cabera">
                    <td colspan="6" style="text-align:right;">TOTAL</td>
                    <td style="text-align:right;">{{$value['total']}}</td>
                </tr>
            </table>
            <div class="espacio"></div>
            @endforeach
            <table class="tabla">
                <tr class="tabla_cabera">
                    <td colspan="6" style="text-align:right;">TOTAL</td>
                    <td style="text-align:right;" width="9%">{{$totalfinal}}</td>
                </tr>
            </table>
        @elseif($listarpor==2)
            @foreach($data as $value)
            <table class="tabla">
                <tr class="tabla_cabera">
                    <td style="text-align:center;">{{$value['usuarionombre']}}</td>
                </tr>
            </table>
            <table class="tabla">
                <tr class="tabla_cabera">
                    <td style="text-align:center;">PERSONA ENTREGADO</td>
                    <td style="text-align:center;" width="8%">CÓDIGO</td>
                    <td style="text-align:center;">CONCEPTO</td>
                    <td style="text-align:center;">DESCRIPCIÓN</td>
                    <td style="text-align:center;" width="14%">FECHA CONFIRMADO</td>
                    <td style="text-align:center;" width="8%">MONTO</td>
                </tr>
                @foreach($value['detalle'] as $valuedetalle)
                <tr>
                    <td style="text-align:left;">{{$valuedetalle['responsableentregado']}}</td>
                    <td style="text-align:center;">{{$valuedetalle['codigo']}}</td>
                    <td style="text-align:left;">{{$valuedetalle['conceptomovimientonombre']}}</td>
                    <td style="text-align:left;">{{$valuedetalle['descripcion']}}</td>
                    <td style="text-align:center;">{{$valuedetalle['fechaconfirmado']}}</td>
                    <td style="text-align:right;">{{$valuedetalle['monto']}}</td>
                </tr>
                @endforeach
                <tr class="tabla_cabera">
                    <td colspan="5" style="text-align:right;">TOTAL</td>
                    <td style="text-align:right;">{{$value['total']}}</td>
                </tr>
            </table>
            <div class="espacio"></div>
            @endforeach
        @endif
    </div>
</body>
</html>