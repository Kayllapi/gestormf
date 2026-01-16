<!DOCTYPE html>
<html>
<head>
    <title>VOUCHER VENTA</title>
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
        <div class="titulo"><b>VENTA</b></div>  
        <table style="width:100%;">
            <tr>
                <td style="width:118px;">
                    <b>AyN COMPRADOR</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvventa->comprador_nombreapellidos }}</td>
            </tr>
            <tr>
                <td>
                    <b>DNI</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvventa->comprador_dni }}</td>
            </tr>
            <tr>
                <td>
                    <b>CODIGO</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>VB{{ $cvventa->codigo }}</td>
            </tr>
            <tr>
                <td>
                    <b>DESCRIPCION</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvventa->descripcioncvcompra }}</td>
            </tr>
            <tr>
                <td>
                    <b>Serie/Motor/Nro Partida</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvventa->serie_motor_partidacvcompra }}</td>
            </tr>
            <tr>
                <td>
                    <b>FECHA</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ date_format(date_create($cvventa->fecharegistro),'d-m-Y h:i:s A') }}</td>
            </tr>
            <tr>
                <td>
                    <b>PAGO</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvventa->venta_idformapago == '1' ? 'CAJA' : 'BANCO' }}</td>
            </tr>
            @if($cvventa->venta_idformapago==2)
                <tr>
                    <td>
                        <b>Banco</b>
                    </td>
                    <td style="width:1px;">
                        <b>:</b>
                    </td>
                    <td>{{ $cvventa->venta_banco }}</td>
                </tr>
                <tr>
                    <td>
                        <b>N° Op./Dt.</b>
                    </td>
                    <td style="width:1px;">
                        <b>:</b>
                    </td>
                    <td>{{ $cvventa->venta_numerooperacion }}</td>
                </tr>
            @endif
        </table>
        <div class="linea"></div>
        <table style="width:100%;">
            <tr>
                <td style="width:65px;">
                    <b>TOTAL RECIBIDO</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvventa->venta_precio_venta_descuento }} SOLES</td>
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
                <td><b>{{ $cvventa->comprador_nombreapellidos }}</b></td>
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
    </div>
</body>
</html>