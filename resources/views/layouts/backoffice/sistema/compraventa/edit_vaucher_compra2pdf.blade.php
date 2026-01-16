<!DOCTYPE html>
<html>
<head>
    <title>VOUCHER COMPRA</title>
    <style>
        *{
            font-family:helvetica;
            font-size:12px;
        }
        @page {
            margin: 15px;
        }
        .ticket_contenedor {
            width: 300px;
        }
        .cabecera {
        }
        .titulo {
            text-align: center;
        }
        .linea {
            width:100%;
            border-top:1px solid #000;
        }
    </style>
</head>
<body>
    <div class="ticket_contenedor">
        <div class="cabecera">{{ $tienda->ticket_nombre }}</div>
        <div class="cabecera">{{ $tienda->ticket_direccion }}</div>
        <div class="cabecera">{{ $tienda->nombreagencia }}</div>
        <div class="linea"></div>
        <div class="titulo" style="margin-top:5px;"><b>COMPRA</b></div>  
        <table width="100%">
            <tr>
                <td>
                    <b>BIEN</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvcompra->descripcion }}</td>
            </tr>
            <tr>
                <td>
                    <b>APELLIDOS Y NOMBRES</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvcompra->vendedor_nombreapellidos }}</td>
            </tr><tr>
                <td>
                    <b>Código</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvcompra->idestadocvcompra == 1 ? 'CB' : 'VB' }}{{ $cvcompra->codigo }}</td>
            </tr><tr>
                <td>
                    <b>Descripción</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvcompra->descripcion }}</td>
            </tr><tr>
                <td>
                    <b>Serie/Motor/Nro Partida</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvcompra->serie_motor_partida }}</td>
            </tr><tr>
                <td>
                    <b>Color</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvcompra->color }}</td>
            </tr>
        </table>
    </div>
</body>
</html>