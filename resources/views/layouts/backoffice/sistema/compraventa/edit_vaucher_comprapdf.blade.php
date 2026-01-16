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
        <table style="width:100%;">
            <tr>
                <td style="width:105px;">
                    <b>Fecha</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ date_format(date_create($cvcompra->fecharegistro),'d-m-Y h:i:s A') }}</td>
            </tr>
            <tr>
                <td>
                    <b>A y N Propietario</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvcompra->vendedor_nombreapellidos }}</td>
            </tr>
            <tr>
                <td>
                    <b>RUC/DNI/CE</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvcompra->vendedor_dni }}</td>
            </tr>
            <tr>
                <td>
                    <b>Cod. Operación</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvcompra->idestadocvcompra == 1 ? 'CB' : 'VB' }}{{ $cvcompra->codigo }}</td>
            </tr>
            <tr>
                <td>
                    <b>Descripción</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvcompra->descripcion }}</td>
            </tr>
            <tr>
                <td>
                    <b>Serie/Motor/N° Partida</b>
                </td>
                <td style="width:1px;">
                    <b>:</b>
                </td>
                <td>{{ $cvcompra->serie_motor_partida }}</td>
            </tr>
            <tr>
                <td>
                    <b>Lugar de pago</b>
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
                        <b>N° Oper./Desc.</b>
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
                <td style="width:105px;">
                    <b>TOTAL RECIBIDO</b>
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
                <td>{{ $cvcompra->responsablecodigo }}</td>
            </tr>
            <tr>
                <td></td>
            </tr>
        </table>
    </div>
</body>
</html>