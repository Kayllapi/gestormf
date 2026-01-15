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
        <div class="cabecera">{{ $tienda->nombre }}</div>
        <div class="cabecera">{{ $tienda->direccion }}</div>
        <div class="cabecera">{{ $tienda->nombreagencia }}</div>
        <div class="linea"></div>
        <div class="titulo"><b>COMPRA</b></div>  
        <table style="width:100%;">
            <tr>
                <td style="width:118px;">
                    <b>AyN PROPIETARIO</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvcompra->vendedor_nombreapellidos }}</td>
            </tr>
            <tr>
                <td>
                    <b>DNI</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvcompra->vendedor_dni }}</td>
            </tr>
            <tr>
                <td>
                    <b>COD</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvcompra->idestadocvcompra == 1 ? 'CB' : 'VB' }}{{ $cvcompra->codigo }}</td>
            </tr>
            <tr>
                <td>
                    <b>DESCRIPCION</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvcompra->descripcion }}</td>
            </tr>
            <tr>
                <td>
                    <b>Serie/Motor/Nro Partida</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvcompra->serie_motor_partida }}</td>
            </tr>
            <tr>
                <td>
                    <b>FECHA</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ date_format(date_create($cvcompra->fecharegistro),'d-m-Y h:i:s A') }}</td>
            </tr>
            <tr>
                <td>
                    <b>PAGO</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvcompra->compra_idformapago == '1' ? 'CAJA' : 'BANCO' }}</td>
            </tr>
            @if($cvcompra->compra_idformapago==2)
                <tr>
                    <td>
                        <b>Banco</b>
                    </td>
                    <td style="width:1px;">
                        <b>:</b>
                    </td>
                    <td>{{ $cvcompra->compra_banco }}</td>
                </tr>
                <tr>
                    <td>
                        <b>N° Op./Dt.</b>
                    </td>
                    <td style="width:1px;">
                        <b>:</b>
                    </td>
                    <td>{{ $cvcompra->compra_numerooperacion }}</td>
                </tr>
            @endif
        </table>
        <div class="linea"></div>
        <table style="width:100%;">
            <tr>
                <td style="width:65px;">
                    <b>TOTAL PAGADO</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvcompra->valorcompra }} SOLES</td>
            </tr>
        </table>
        <div class="linea"></div>
        <table style="width:100%; text-align:center;">
            <tr>
                <td style="height:50px; vertical-align:bottom;">_____________________</td>
            </tr>
            <tr>
                <td>FIRMA</td>
            </tr>
            <tr>
                <td><b>{{ $cvcompra->vendedor_nombreapellidos }}</b></td>
            </tr>
        </table>
        <table>
            <tr>
                <td>
                    <b>Usuario</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>
        </table>
        <div class="linea"></div>
        <table>
            <tr>
                <td>
                    <b>BIEN</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <b>APELLIDOS Y NOMBRES</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td></td>
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