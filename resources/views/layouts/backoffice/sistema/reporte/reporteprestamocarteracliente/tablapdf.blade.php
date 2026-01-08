<!DOCTYPE html>
<html>
<head>
    <title>REPORTE DE CARTERA DE CLIENTES</title>
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
            <div class="titulo">REPORTE DE CARTERA DE CLIENTES</div>
            @foreach($prestamocateras as $value)
                <table class="tabla">
                    <tr class="tabla_cabera">
                        <td style="text-align:center;">
                          ASESOR: {{$value['asesor']}}
                        </td>
                    </tr>
                </table>
                <table class="tabla">
                    <tr class="tabla_cabera">
                        <td style="text-align:center;width:1%;">Nº</td>
                        <td style="text-align:center;">CLIENTE (DNI - APELLIDOS, NOMBRES)</td>
                        <td style="text-align:center;width:4%;">TIPO</td>
                        <td style="text-align:center;width:10%;">TELÉFONO</td>
                        <td style="text-align:center;width:30%;">DIRECCIÓN</td>
                        <td style="text-align:center;width:8%;">ESTADO</td>
                    </tr>
                    <?php $i=1; ?>
                    @foreach($value['detalle'] as $valuedetalle)
                        <tr>
                            <td style="text-align:center;"><?php echo $i; ?></td>
                            <td>{{$valuedetalle['cliente_identificacion']}} - {{$valuedetalle['cliente_apellidos']}}, {{$valuedetalle['cliente_nombre']}}</td>
                            <td style="text-align:center;">{{$valuedetalle['tipo']}}</td>
                            <td style="text-align:center;">{{$valuedetalle['cliente_numerotelefono']}}</td>
                            <td>{{$valuedetalle['cliente_direccion']}}</td>
                            <td style="text-align:center;">{{$valuedetalle['estado']}}</td>
                        </tr>
                    <?php $i++; ?>
                    @endforeach
                </table>
                <div class="espacio"></div>
            @endforeach
    </div>
</body>
</html>