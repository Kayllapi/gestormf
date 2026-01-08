<!DOCTYPE html>
<html>
<head>
    <title>REPORTE DE MORA</title>
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
    <div class="titulo">REPORTE DE MORA</div>
    
    <div class="content">
        <table class="tabla_informativa">
            <tr>
                <td style="text-align:right;width:40%;"><b>FECHA:</b> {{Carbon\Carbon::now()->format("d/m/Y h:i A")}}</td>
                <td style="text-align:center;width:2%;"><b>|</b></td>
                <td style="text-align:center;width:15%;"><b>Nº DE CLIENTES:</b> {{$numeroclientes}}</td>
                <td style="text-align:center;width:2%;"><b>|</b></td>
                <td style="text-align:left;width:40%;"><b>PORCENTAJE:</b> {{$totalporcentaje}}%</td>
            </tr>
        </table>
        <div class="espacio"></div>
        @if(count($prestamomoras)==0)
        <div class="mensaje_alerta">No tiene ningún registro!!</div>
        @endif
        @if($request->listarpor==1)
           @foreach($prestamomoras as $value)
                <table class="tabla">
                    <tr class="tabla_cabera">
                        <td style="text-align:center;">
                          ASESOR: {{$value['asesor']}}
                        </td>
                        <td style="text-align:center;">
                          TOTAL DE DESEMBOLSOS: {{$value['desembolso']}}
                        </td>
                    </tr>
                </table>
               <table class="tabla">
                   <tr class="tabla_cabera">
                       <td style="text-align:center;width:1%;">Nº</td>
                       <td style="text-align:center;">CLIENTE (DNI - APELLIDOS, NOMBRES)</td>
                       <td style="text-align:center;width:12%;">FECHA DE DESEMBOLSO</td>
                       <td style="text-align:center;width:7%;">0-4 DÍAS</td>
                       <td style="text-align:center;width:7%;">5-7 DÍAS</td>
                       <td style="text-align:center;width:7%;">8-11 DÍAS</td>
                       <td style="text-align:center;width:7%;">12-15 DÍAS</td>
                       <td style="text-align:center;width:7%;">&gt; 15 DÍAS</td>
                       <td style="text-align:center;width:8%;">MORA TOTAL</td>
                       <td style="text-align:center;width:8%;">DEUDA CAPITAL</td>
                   </tr>
                    @foreach($value['detalle'] as $valuedetalle)
                    <tr>
                        <td style="text-align:center;">{{$valuedetalle['numeroclientes']}}</td>
                        <td style="text-align:left;">{{$valuedetalle['cliente']}}</td>
                        <td style="text-align:center;">{{$valuedetalle['fechadesembolso']}}</td>
                        <td style="text-align:right;<?php echo $valuedetalle['mora_1']>0? 'background-color:'.configuracion($tienda->id,'sistema_color')['valor'].'40;':'' ?>">{{$valuedetalle['mora_1']}}</td>
                        <td style="text-align:right;<?php echo $valuedetalle['mora_2']>0? 'background-color:'.configuracion($tienda->id,'sistema_color')['valor'].'40;':'' ?>">{{$valuedetalle['mora_2']}}</td>
                        <td style="text-align:right;<?php echo $valuedetalle['mora_3']>0? 'background-color:'.configuracion($tienda->id,'sistema_color')['valor'].'40;':'' ?>">{{$valuedetalle['mora_3']}}</td>
                        <td style="text-align:right;<?php echo $valuedetalle['mora_4']>0? 'background-color:'.configuracion($tienda->id,'sistema_color')['valor'].'40;':'' ?>">{{$valuedetalle['mora_4']}}</td>
                        <td style="text-align:right;<?php echo $valuedetalle['mora_5']>0? 'background-color:'.configuracion($tienda->id,'sistema_color')['valor'].'40;':'' ?>">{{$valuedetalle['mora_5']}}</td>
                        <td style="text-align:right;background-color:<?php echo configuracion($tienda->id,'sistema_color')['valor'] ?>90;">{{$valuedetalle['mora_total']}}</td>
                        <td style="text-align:right;">{{$valuedetalle['deuda']}}</td>
                    </tr>
                    @endforeach
                    <tr class="tabla_cabera">
                        <td style="text-align:right;" colspan="3">TOTAL</td>
                        <td style="text-align:right;">{{$value['moratotal_1']}}</td>
                        <td style="text-align:right;">{{$value['moratotal_2']}}</td>
                        <td style="text-align:right;">{{$value['moratotal_3']}}</td>
                        <td style="text-align:right;">{{$value['moratotal_4']}}</td>
                        <td style="text-align:right;">{{$value['moratotal_5']}}</td>
                        <td style="text-align:right;">{{$value['moratotal_total']}}</td>
                        <td style="text-align:right;">{{$value['deudatotal']}}</td>
                    </tr>
                </table>
                <div class="espacio"></div>
            @endforeach
        @elseif($request->listarpor==2)
           @foreach($prestamomoras as $value)
                <table class="tabla">
                    <tr class="tabla_cabera">
                        <td style="text-align:center;">
                          ASESOR: {{$value['asesor']}}
                        </td>
                        <td style="text-align:center;">
                          TOTAL DE DESEMBOLSOS: {{$value['desembolso']}}
                        </td>
                    </tr>
                </table>
               <table class="tabla">
                   <tr class="tabla_cabera">
                       <td style="text-align:center;width:1%;">Nº</td>
                       <td style="text-align:center;">CLIENTE (DNI - APELLIDOS, NOMBRES)</td>
                       <td style="text-align:center;width:12%;">FECHA DE DESEMBOLSO</td>
                       <td style="text-align:center;width:7%;">0-4 DÍAS</td>
                       <td style="text-align:center;width:7%;">5-7 DÍAS</td>
                       <td style="text-align:center;width:7%;">8-11 DÍAS</td>
                       <td style="text-align:center;width:7%;">12-15 DÍAS</td>
                       <td style="text-align:center;width:7%;">&gt; 15 DÍAS</td>
                       <td style="text-align:center;width:8%;">MORA TOTAL</td>
                       <td style="text-align:center;width:8%;">DEUDA CAPITAL</td>
                   </tr>
                    @foreach($value['detalle'] as $valuedetalle)
                    <tr>
                        <td style="text-align:center;">{{$valuedetalle['numeroclientes']}}</td>
                        <td style="text-align:left;">{{$valuedetalle['cliente']}}</td>
                        <td style="text-align:center;">{{$valuedetalle['fechadesembolso']}}</td>
                        <td style="text-align:right;<?php echo $valuedetalle['mora_1']>0? 'background-color:'.configuracion($tienda->id,'sistema_color')['valor'].'40;':'' ?>">{{$valuedetalle['mora_1']}}</td>
                        <td style="text-align:right;<?php echo $valuedetalle['mora_2']>0? 'background-color:'.configuracion($tienda->id,'sistema_color')['valor'].'40;':'' ?>">{{$valuedetalle['mora_2']}}</td>
                        <td style="text-align:right;<?php echo $valuedetalle['mora_3']>0? 'background-color:'.configuracion($tienda->id,'sistema_color')['valor'].'40;':'' ?>">{{$valuedetalle['mora_3']}}</td>
                        <td style="text-align:right;<?php echo $valuedetalle['mora_4']>0? 'background-color:'.configuracion($tienda->id,'sistema_color')['valor'].'40;':'' ?>">{{$valuedetalle['mora_4']}}</td>
                        <td style="text-align:right;<?php echo $valuedetalle['mora_5']>0? 'background-color:'.configuracion($tienda->id,'sistema_color')['valor'].'40;':'' ?>">{{$valuedetalle['mora_5']}}</td>
                        <td style="text-align:right;background-color:<?php echo configuracion($tienda->id,'sistema_color')['valor'] ?>90;">{{$valuedetalle['mora_total']}}</td>
                        <td style="text-align:right;">{{$valuedetalle['deuda']}}</td>
                    </tr>
                    @endforeach
                    <tr class="tabla_cabera">
                        <td style="text-align:right;" colspan="3">TOTAL</td>
                        <td style="text-align:right;">{{$value['moratotal_1']}}</td>
                        <td style="text-align:right;">{{$value['moratotal_2']}}</td>
                        <td style="text-align:right;">{{$value['moratotal_3']}}</td>
                        <td style="text-align:right;">{{$value['moratotal_4']}}</td>
                        <td style="text-align:right;">{{$value['moratotal_5']}}</td>
                        <td style="text-align:right;">{{$value['moratotal_total']}}</td>
                        <td style="text-align:right;">{{$value['deudatotal']}}</td>
                    </tr>
                </table>
                <div class="espacio"></div>
            @endforeach
        @endif
    </div>
</body>
</html>