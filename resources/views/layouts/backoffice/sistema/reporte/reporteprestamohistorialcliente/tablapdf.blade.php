<!DOCTYPE html>
<html>
<head>
    <title>REPORTE DE HISTORIAL DE CLIENTE</title>
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
  
    <div class="titulo">REPORTE DE HISTORIAL DE CLIENTE</div>
    
    <div class="content">
        @if(count($prestamocreditos)==0)
        <div class="mensaje_alerta">No tiene ningún registro!!</div>
        @endif
        @if(count($prestamocreditos)>0)
          @foreach($prestamocreditos as $value)
            <table class="tabla">
                    <tr class="tabla_cabera">
                        <td style="text-align:center;">{{$value['cliente']}}</td>
                    </tr>
            </table>
            <div class="espacio"></div>
            @foreach($value['detalle'] as $valuedetalle)
            <table class="tabla">
                <tr class="tabla_cabera">
                    <td style="text-align:center;">CÓDIGO</td>
                    <td style="text-align:center;">DESEMBOLSO</td>
                    <td style="text-align:center;">MONTO</td>
                    <td style="text-align:center;">TASA</td>
                    <td style="text-align:center;">CUOTA</td>
                    <td style="text-align:center;">Nº DE CUOTAS</td>
                    <td style="text-align:center;">FRECUENCIA</td>
                    <td style="text-align:center;">TIPO DE CRÉDITO</td>
                </tr>
                <tr>
                    <td style="text-align:center;">{{$valuedetalle['creditocodigo']}}</td>
                    <td style="text-align:center;">{{$valuedetalle['creditofechadesembolso']}}</td>
                    <td style="text-align:center;">{{$valuedetalle['creditodesembolso']}}</td>
                    <td style="text-align:center;">{{$valuedetalle['creditotasa']}}</td>
                    <td style="text-align:center;">{{$valuedetalle['creditocuota']}}</td>
                    <td style="text-align:center;">{{$valuedetalle['creditonumerocuota']}}</td>
                    <td style="text-align:center;">{{$valuedetalle['creditofrecuencia']}}</td>
                    <td style="text-align:center;">{{$valuedetalle['creditotipo']}}</td>
                </tr>
            </table>
            <table class="tabla">
                <tr>
                    <td style="text-align:left;" class="tabla_titulo" colspan="8">
                         <table class="tabla_ajustado">
                            <tr>
                                <td class="tabla_titulo" style="text-align:right;">CUOTA</td>
                                @foreach(array_slice($valuedetalle['cuotas'], 0, 35) as $valuecuota)
                                <td style="text-align:center;">{{$valuecuota['numero']}}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="tabla_titulo" style="text-align:right;">ATRASO</td>
                                @foreach(array_slice($valuedetalle['cuotas'], 0, 35) as $valuecuota)
                                <td style="text-align:center;">{{$valuecuota['atraso']}}</td>
                                @endforeach
                            </tr>
                        </table>
                        @if(count(array_slice($valuedetalle['cuotas'], 35, 70))>0)
                         <table class="tabla_ajustado">
                            <tr>
                                <td class="tabla_titulo" style="text-align:right;">CUOTA</td>
                                @foreach(array_slice($valuedetalle['cuotas'], 35, 70) as $valuecuota)
                                <td style="text-align:center;">{{$valuecuota['numero']}}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="tabla_titulo" style="text-align:right;">ATRASO</td>
                                @foreach(array_slice($valuedetalle['cuotas'], 35, 70) as $valuecuota)
                                <td style="text-align:center;">{{$valuecuota['atraso']}}</td>
                                @endforeach
                            </tr>
                        </table>
                        @endif
                        @if(count(array_slice($valuedetalle['cuotas'], 70, 104))>0)
                         <table class="tabla_ajustado">
                            <tr>
                                <td class="tabla_titulo" style="text-align:right;">CUOTA</td>
                                @foreach(array_slice($valuedetalle['cuotas'], 70, 104) as $valuecuota)
                                <td style="text-align:center;">{{$valuecuota['numero']}}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="tabla_titulo" style="text-align:right;">ATRASO</td>
                                @foreach(array_slice($valuedetalle['cuotas'], 70, 104) as $valuecuota)
                                <td style="text-align:center;">{{$valuecuota['atraso']}}</td>
                                @endforeach
                            </tr>
                        </table>
                        @endif
                    </td>
                </tr>
            </table>
            <div class="espacio"></div>
            @endforeach
          @endforeach
        @endif
    </div>
</body>
</html>