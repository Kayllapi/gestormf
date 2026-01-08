<!DOCTYPE html>
<html>
<head>
    <title>Guía de Remisión</title>
    <style>
        html, body {
            font-family: helvetica;
        }
      
        /* Inicio de Cabecera de pdf*/
        .header {
            height:150px;
            width: 100%;
        }
        .razonsocial, .ruc {
            float: left;
        }
        .razonsocial {
            width: 60%;
            height:190px;
        }
        .razonsocial > p {
            font-size: 9px;
        }
        .razonsocial > p > b {
            font-size: 10px;
        }
        .logo {
            width: 50px;
            height: 50px;
        }
        .ruc {
            width: 40%;
            height:150px;
            background-color: #999;
        }
        /* fin */
        
        .table {
            width: 99%;
            margin: auto;
            font-size: 12px;
        }
        table, tr, td {
            border: 1px solid #000;
            padding: 3px;
        }
        .table-header {
            background-color: #F18237;
            color: #ffffff;
        }
        b {
            font-size: 11px;
        }
      
        /* alineando al centro */
        .center {
            text-align: center;
        }
      
        /* alineando a la derecha */
        .right {
            text-align: right;
        }
      
        /* alineando a la izquierda */
        .left {
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="razonsocial center">
            <p>
                <img class="logo" src="{{ url('public/backoffice/tienda/'.$tienda->id.'/logo/'.$tienda->imagen) }}">
            </p>
            <p>{{ $tienda->contenido }}</p>
            <p>
                <b>AGENCIA</b> <br>
                <b>{{ $tienda->direccion }}</b> <br>
                <b>AGENCIA DIRECCION</b> <br>
                <b>{{ $tienda->correo }} / {{ $tienda->numerotelefono }}</b>
            </p>
        </div>
        <div class="ruc">
            <div class="center">
                <h4 style="margin-top:10px;">R.U.C {{ $facturacionguiaremision->emisor_ruc }}</h4>
                <div style="height:60px; background-color:#F18237" >
                    <h3 style="margin-top:-15px; color:white; padding-top:9px;">
                        GUIA DE REMISIÓN ELECTRÓNICA
                    </h3>
                </div>
                <h4 style="margin-top:-10px;">{{ $facturacionguiaremision->despacho_serie }} - {{ str_pad($facturacionguiaremision->despacho_correlativo, 3, "0", STR_PAD_LEFT) }}</h4>
            </div>
        </div>
    </div>
    <div class="container">
        <table class="table" cellspacing="0">
            <thead>
                <tr>
                    <td>
                        <b>RAZÓN SOCIAL:</b> {{ $facturacionguiaremision->emisor_razonsocial }}
                    </td>
                    <td>
                        <b>RUC:</b> {{ $facturacionguiaremision->emisor_ruc }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <b>DIRECCIÓN DE PARTIDA:</b> {{ $facturacionguiaremision->envio_direccionpartida }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <b>DIRECCIÓN DE LLEGADA:</b> {{ $facturacionguiaremision->envio_direccionllegada }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>DOCUMENTOS:</b> 
                    </td>
                    <td>
                        <b>FECHA DE TRASLADO:</b> {{ date_format(date_create($facturacionguiaremision->envio_fechatraslado), 'Y-m-d') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <b>MOTIVO DE TRASLADO:</b> {{ $facturacionguiaremision->envio_descripciontraslado }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>TRANSPORTISTA:</b> {{ $transportista->transportista }}
                    </td>
                    <td>
                        <b>Nro Placa:</b> {{ $facturacionguiaremision->transporte_placa }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <b>OBSERVACIÓN:</b> {{ $facturacionguiaremision->despacho_observacion }}
                    </td>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
      
        <table class="table" cellspacing="0">
            <tr class="table-header">
                <td width="3%">
                    <b>ITEM</b>
                </td>
                <td class="center" width="120px">
                    <b>CODIGO</b>
                </td>
                <td class="center">
                    <b>DESCRIPCIÓN</b>
                </td>
                <td width="30px">
                    <b>CANT.</b>
                </td>
            </tr>
            @php $item = 1 @endphp
            @foreach($facturacionguiaremisiondetalles as $value)
                <tr>
                    <td class="center">{{ $item }}</td>
                    <td class="center">{{ $value->codigo }}</td>
                    <td>{{ $value->descripcion }}</td>
                    <td class="center">{{ $value->cantidad }}</td>
                </tr>
            @php $item ++ @endphp
            @endforeach

            @for ($j = $item; $j<=30; $j++)
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>&nbsp;</td>
                </tr>
            @endfor
        </table>
    </div>
</body>
</html>