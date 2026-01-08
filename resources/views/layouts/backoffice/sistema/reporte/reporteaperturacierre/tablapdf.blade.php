<!DOCTYPE html>
<html>
<head>
    <title>REPORTE DE APERTURAS Y CIERRES</title>
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
    <div class="titulo">REPORTE DE APERTURAS Y CIERRES</div>
    
    <div class="content">
        <table class="tabla_informativa">
            <tr>
                <td style="width:14%;">FECHA</td>
                <td style="width:1%;">:</td>
                <td style="width:35%;">{{Carbon\Carbon::now()->format('d/m/Y H:i:s A')}}</td>
                <td style="width:14%;">TOTAL APERTURAS</td>
                <td style="width:1%;">:</td>
                <td style="width:35%;">{{$totalaperturafinal}}</td>
            </tr>
            <tr>
                <td>FECHA INICIO</td>
                <td>:</td>
                <td>{{$fechainicio!=''?date_format(date_create($fechainicio),"d/m/Y"):''}}</td>
                <td>TOTAL CIERRES</td>
                <td>:</td>
                <td>{{$totalcierrefinal}}</td>
            </tr>
            <tr>
                <td>FECHA FIN</td>
                <td>:</td>
                <td>{{$fechafin!=''?date_format(date_create($fechafin),"d/m/Y"):''}}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
        <div class="espacio"></div>
        @if(count($data)==0)
        <div class="mensaje-info">No tiene ningún registro!!</div>
        @endif
        @if($listarpor==1)
            @foreach($data as $value)
            <table class="tabla">
                <tr class="tabla_cabera">
                    <td colspan="6">{{$value['cajanombre']}}</td>
                </tr>
                <tr class="tabla_cabera">
                    <td>PERSONA RESPONSABLE</td>
                    <td>PERSONA ASIGNADO</td>
                    <td width="105px">FECHA DE APERTURA</td>
                    <td width="105px">FECHA DE CIERRE</td>
                    <td width="60px">APERTURA</td>
                    <td width="60px">CIERRE</td>
                </tr>
                @foreach($value['detalle'] as $valuedetalle)
                <tr>
                    <td style="text-align:left;">{{$valuedetalle['responsable']}}</td>
                    <td style="text-align:left;">{{$valuedetalle['recepcion']}}</td>
                    <td>{{$valuedetalle['fechaapertura']}}</td>
                    <td>{{$valuedetalle['fechacierre']}}</td>
                    <td style="text-align:right;">{{$valuedetalle['montoapertura']}}</td>
                    <td style="text-align:right;">{{$valuedetalle['montocierre']}}</td>
                </tr>
                @endforeach
                <tr class="tabla_cabera">
                    <td colspan="4" style="text-align:right;">TOTAL</td>
                    <td style="text-align:right;">{{$value['totalapertura']}}</td>
                    <td style="text-align:right;">{{$value['totalcierre']}}</td>
                </tr>
            </table>
            <div class="espacio"></div>
            @endforeach
        @elseif($listarpor==2)
            @foreach($data as $value)
            <table class="tabla">
                <tr class="tabla_cabera">
                    <td colspan="6">{{$value['usuarionombre']}}</td>
                </tr>
                <tr class="tabla_cabera">
                    <td>CAJA</td>
                    <td>PERSONA ASIGNADO</td>
                    <td width="105px">FECHA DE APERTURA</td>
                    <td width="105px">FECHA DE CIERRE</td>
                    <td width="60px">APERTURA</td>
                    <td width="60px">CIERRE</td>
                </tr>
                @foreach($value['detalle'] as $valuedetalle)
                <tr>
                    <td style="text-align:left;">{{$valuedetalle['cajanombre']}}</td>
                    <td style="text-align:left;">{{$valuedetalle['recepcion']}}</td>
                    <td>{{$valuedetalle['fechaapertura']}}</td>
                    <td>{{$valuedetalle['fechacierre']}}</td>
                    <td style="text-align:right;">{{$valuedetalle['montoapertura']}}</td>
                    <td style="text-align:right;">{{$valuedetalle['montocierre']}}</td>
                </tr>
                @endforeach
                <tr class="tabla_cabera">
                    <td colspan="4" style="text-align:right;">TOTAL</td>
                    <td style="text-align:right;">{{$value['totalapertura']}}</td>
                    <td style="text-align:right;">{{$value['totalcierre']}}</td>
                </tr>
            </table>
            <div class="espacio"></div>
            @endforeach
        @elseif($listarpor==3)
            @foreach($data as $value)
            <table class="tabla">
                <tr class="tabla_cabera">
                    <td colspan="6">{{$value['usuarionombre']}}</td>
                </tr>
                <tr class="tabla_cabera">
                    <td>CAJA</td>
                    <td>PERSONA RESPONSABLE</td>
                    <td width="105px">FECHA DE APERTURA</td>
                    <td width="105px">FECHA DE CIERRE</td>
                    <td width="60px">APERTURA</td>
                    <td width="60px">CIERRE</td>
                </tr>
                @foreach($value['detalle'] as $valuedetalle)
                <tr>
                    <td style="text-align:left;">{{$valuedetalle['cajanombre']}}</td>
                    <td style="text-align:left;">{{$valuedetalle['responsable']}}</td>
                    <td>{{$valuedetalle['fechaapertura']}}</td>
                    <td>{{$valuedetalle['fechacierre']}}</td>
                    <td style="text-align:right;">{{$valuedetalle['montoapertura']}}</td>
                    <td style="text-align:right;">{{$valuedetalle['montocierre']}}</td>
                </tr>
                @endforeach
                <tr class="tabla_cabera">
                    <td colspan="4" style="text-align:right;">TOTAL</td>
                    <td style="text-align:right;">{{$value['totalapertura']}}</td>
                    <td style="text-align:right;">{{$value['totalcierre']}}</td>
                </tr>
            </table>
            <div class="espacio"></div>
            @endforeach
        @endif
    </div>
</body>
</html>