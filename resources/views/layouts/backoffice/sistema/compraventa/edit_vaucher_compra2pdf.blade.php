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
        <div class="cabecera">{{ $tienda->ticket_nombre }} / {{ $tienda->nombreagencia }} / {{ $cvcompra->responsablecodigo }}</div>
        <div class="linea"></div>
        <table width="100%">
            <tr>
                <td>
                    <b>BIEN</b>
                </td>
            </tr>
            <tr>
                <td style="">
                    <b>A y N (Propietario) :</b> {{ $cvcompra->vendedor_nombreapellidos }}
                </td>
            </tr><tr>
                <td>
                    <b>Código :</b> {{ $cvcompra->idestadocvcompra == 1 ? 'CB' : 'VB' }}{{ $cvcompra->codigo }}
                </td>
            </tr><tr>
                <td>
                    <b>Descripción :</b> {{ $cvcompra->descripcion }}
                </td>
            </tr><tr>
                <td>
                    <b>Serie/Motor/N° Partida :</b> {{ $cvcompra->serie_motor_partida }}
                </td>
            </tr><tr>
                <td>
                    <b>Color :</b> {{ $cvcompra->color }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>