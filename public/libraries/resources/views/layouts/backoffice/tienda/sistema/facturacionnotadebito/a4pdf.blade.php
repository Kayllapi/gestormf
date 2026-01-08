<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>PDF A4</title>
    </head>
    <style>
        @page {
            margin: 190px 35px; 
      font-family: <?php echo  configuracion($tienda->id,'facturacion_tipoletra')['resultado']=='CORRECTO'?configuracion($tienda->id,'facturacion_tipoletra')['valor']:'Courier' ?>,sans-serif;
            padding: 0;
        }
  
        #header { 
            position: fixed; 
            left: 0px; 
            top: -170px; 
            right: 0px; 
            height: auto; 
            text-align: center; 
        }
        #footer { 
            position: fixed; 
            left: 0px; 
            bottom: -190px;
            right: 0px; 
            height: 150px; 
        }
  
        /* Estilos Head */
        .container-head {
            height: 100px;
        }
        
        .info-empresa {
            width: 43%;
            height: 100px;
            float: left;
            line-height: 15px;
            margin-right: 1%;
            border: 1px solid #7F7F7F;
            border-radius: 10px;
        }
        .info-empresa-borde {
            text-align: center;
            font-size: 10px;
            padding: 10px;
            position: absolute;
            top: 5px;
        }
  
        .ruc {
            width: 238px;
            height: 100px;
            float: left;
            text-align: center;
            border: 1px solid #7F7F7F;
            border-radius: 10px;
        }
  
        .info-empresa-img {
            height: 100px;
            width: 175px;
            float: left;
        }
  
        .info-empresa-img > img {
          margin-top: 2px;
        }
  
        .info-empresa-span-rz {
            margin-top: 3px; 
            color: #2f2f2f; 
            font-weight: bold;
        }
  
        .ruc > span {
            line-height: 30px;
        }
  
        .text-ruc-serie {
            font-size: 12px;
            color: #58555A;
        }
  
        .text-tipo-documento {
            color: #2f2f2f;
            font-weight: bold;
        }
  
        /* Estilos Informacion Cliente y Factura */
        .container-client {
            margin-top: -52px;
            width: 711px;
            height: auto;
            border-radius: 10px; 
            border: 1px solid #7F7F7F;
            padding: 10px;
        }
  
        .text-black {
            color: #2f2f2f;
            font-weight: bold;
        }
  
        .text-gray {
            color: #58555A;
        }
  
        .container-other {
            margin-top: 10px;
            width: 721px;
            height: auto;
            border-radius: 10px; 
            border: 1px solid #7F7F7F;
            padding-left: 10px;
        }
  
        .container-detail {
          border-bottom:  1px solid #7F7F7F;
          margin-right: -7.5px;
          height: auto;
        }
  
        .container-detail-credito {
          border-bottom:  1px solid #7F7F7F;
          margin-right: -7.5px;
          height: auto;
          width: 400px;
        }
  
        .container-total {
          width: auto;
          margin-right: 22.6px;
        }
  
        .border-tr-left {
            border-left: 1px solid #7F7F7F;
        }
  
        .border-tr-right {
            border-right: 1px solid #7F7F7F;
        }
  
        .border-tr-top {
            border-top: 1px solid #7F7F7F;
        }
  
        .container-leyenda {
            margin-top: 10px;
            width: 721px;
            height: auto;
            border-radius: 10px; 
            border: 1px solid #7F7F7F;
            padding-left: 10px;
            padding-top: 5px;
            padding-bottom: 5px;
        }
  
        .container-leyenda span {
            font-size: 10px;
        }
  
        #detalle-factura tr:nth-child(even) {
            background-color: #dadada;
        }
  
        span {
            display: block;
        }
  
        .container-img-txt {
            position: absolute; 
            top: 85px;
        }
  
        .logo-description {
            margin-top: 10px;
        }
  
        .container-detail table {
            margin-top: 20px;
            width: 731px;
        }
  
        .container-detail-credito table {
            margin-top: 20px;
            width: 400px;
        }
  
        .thead-tr {
            background-color: #58555A; 
            color: #fff; 
            padding: 4px;
        }
  
        .txt-center {
            text-align: center;
        }
  
        .txt-right {
            text-align: right;
        }
  
        .txt-12 {
            font-size: 10px
        }
      
        .txt-10 {
            font-size: 10px
        }
  
        .txt-7 {
            font-size: 7px;
        }
  
        .txt-8 {
            font-size: 8px;
        }
  
        .txt-grey {
            color: #58555A;
        }
    </style>

    <body>
        {{-- Cabecera --}}
        <div id="header">
            <div class="container-head">
                <div class="info-empresa-img txt-12 txt-center">
                    @if($facturacionnotadebito->agencialogo!='')
                        <img height="60px" src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$facturacionnotadebito->agencialogo) }}"><br>
                    @endif
                    <span class="info-empresa-span-rz">{{ strtoupper($facturacionnotadebito->emisor_nombrecomercial) }}</span>
                    <span class="txt-grey">{{ strtoupper($facturacionnotadebito->emisor_direccion) }}</span>
                    <span class="txt-grey">{{ strtoupper($facturacionnotadebito->emisor_departamento.'/'.$facturacionnotadebito->emisor_provincia.'/'.$facturacionnotadebito->emisor_distrito) }}</span>
                </div>
                
                <div class="info-empresa txt-center">
                    <div class="info-empresa-borde">
                        <span class="logo-description txt-grey txt-9">{{ $tienda->contenido }}</span>
                    </div>
                </div>
            
                <div class="ruc">
                    <span class="text-ruc-serie">R.U.C: {{ $facturacionnotadebito->emisor_ruc }}</span>
                    <span class="text-tipo-documento">NOTA DE DÉBITO</span>
                    <span  class="text-ruc-serie">{{$facturacionnotadebito->notadebito_serie}}-{{ str_pad($facturacionnotadebito->notadebito_correlativo, 8, "0", STR_PAD_LEFT) }}</span>
                </div>
            </div>
        </div>

        {{-- Pie de Pagina --}}
        <div id="footer" class="txt-10 txt-center">
            <div class="container-img-txt">
                <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$facturacionnotadebito->agencialogo) }}" height="30px">
                <span>{{ $tienda->paginaweb }} - {{ $tienda->correo }}</span>
                <span>{{ $tienda->numerotelefono }}</span>
            </div>
        </div>

        {{-- Contenido --}}
        <div id="content">
            {{-- Detalle Cliente PDF --}}
            <div class="container-client">
                <table class="table txt-10" width="100%">
                    <tr>
                        <td width="50px" class="text-black">Cliente:</td>
                        <td class="text-gray">{{ strtoupper($facturacionnotadebito->cliente_razonsocial) }}</td>
                        <td width="50px" class="text-black">Moneda:</td>
                        <td class="text-gray">
                            @if($facturacionnotadebito->notadebito_tipomoneda=='PEN')
                                SOLES
                            @else($facturacionnotadebito->notadebito_tipomoneda=='USD')
                                DOLARES
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="50px" class="text-black">RUC:</td>
                        <td class="text-gray">{{ $facturacionnotadebito->cliente_numerodocumento }}</td>
                        <td width="50px" class="text-black">IGV</td>
                        <td class="text-gray">18.00 %</td>
                    </tr>
                    <tr>
                        <td width="50px" class="text-black">Dirección:</td>
                        <td class="text-gray">{{ strtoupper($facturacionnotadebito->cliente_direccion) }}</td>
                        <!-- <td width="80px" class="text-black">Forma de Pago:</td>
                        <td class="text-gray"></td> -->
                    </tr>
                    <tr>
                        <td width="50px" class="text-black">Ciudad:</td>
                        <td class="text-gray">{{ strtoupper($facturacionnotadebito->cliente_departamento.'/'.$facturacionnotadebito->cliente_provincia.'/'.$facturacionnotadebito->cliente_distrito) }}</td>
                    </tr>
                </table>
            </div>

            <div class="container-other">
                <table class="table txt-10" width="100%">
                    <tr>
                        <td width="100px" class="text-black">Fecha de Emisión: </td>
                        <td class="text-gray">{{ date_format(date_create($facturacionnotadebito->notadebito_fechaemision),"d/m/Y h:i:s A") }}</td>
                    </tr>
                </table>
            </div>

            {{-- Detalle Factura/Boleta PDF --}}
            <div class="container-detail" id="detalle-factura">
                <table class="table txt-10" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="border-tr-top border-tr-left thead-tr" width="15px">ITEM</td>
                            <th class="border-tr-top border-tr-left thead-tr" width="50px">CANT.</td>
                            <th class="border-tr-top border-tr-left thead-tr" width="70px">UND.</td>
                            <th class="border-tr-top border-tr-left thead-tr" width="70px">COD.</td>
                            <th class="border-tr-top border-tr-left thead-tr">DESCIPCIÓN</td>
                            <th class="border-tr-top border-tr-left thead-tr" width="80px">P.UNIT.</td>
                            <th class="border-tr-top border-tr-left border-tr-right thead-tr" width="80px">TOTAL</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $item = 0; ?>
                        @foreach($notadebitodetalle as $value)
                            <?php $item++; ?>
                            <tr class="table-tr" style="border-bottom: 1px solid black;">
                                <td class="border-tr-left txt-center" width="10px">{{ $item }}</td>
                                <td class="border-tr-left txt-center" width="30px">{{ $value->cantidad }}</td>
                                <td class="border-tr-left txt-center" width="70px">{{ $value->unidad }}</td>
                                <td class="border-tr-left txt-center" width="70px">{{ $value->codigoproducto }}</td>
                                <td class="border-tr-left">{{ strtoupper($value->descripcion) }}</td>
                                <td class="border-tr-left txt-right" width="80px">{{ $value->montopreciounitario }}</td>
                                <td class="border-tr-left border-tr-right txt-right" width="80px">{{number_format($value->cantidad*$value->montopreciounitario, 2, '.', '') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Detalle Totales PDF --}}
            <div class="container-total">
                <table class="table txt-10" width="731px" cellspacing="0" style="margin-top: 20px;">
                    <tr>
                        <th colspan="9" class="txt-right">SUBTOTAL</th>
                        <th width="20px" class="txt-right">S/.</th>
                        <th class="txt-right">{{ $facturacionnotadebito->notadebito_valorventa }}</th>
                    </tr>
                    <tr>
                        <th colspan="9" class="txt-right">IGV 18.00 %</th>
                        <th width="20px" class="txt-right">S/.</th>
                        <th class="txt-right">{{ $facturacionnotadebito->notadebito_totalimpuestos }}</th>
                    </tr>
                    <tr>
                        <th colspan="9" class="txt-right">TOTAL</th>
                        <th width="20px" class="txt-right">S/.</th>
                        <th class="txt-right">{{ $facturacionnotadebito->notadebito_montoimpuestoventa }}</th>
                    </tr>
                </table>
            </div>
            
            <div class="container-leyenda txt-10">
                <b>IMPORTE EN LETRAS:</b> {{ $facturacionnotadebito->leyenda_value }}
            </div>

            {{-- Codigo QR y otra informacion --}}
            @if($respuesta!='')
                <div class="container-leyenda">
                    <table class="table">
                        <tr>
                            <td>
                                <img src="<?php echo $respuesta->qr ?>" width="80px">
                            </td>
                            <td>
                                <span> <b>Representacion Impresa de la :</b> NOTA DE CRÉDITO</span>
                                <span>Autorizado mediante Resolucion de Intendencia No. 034-005-0005315, puede ser consulta en: kayllapi.com/{{$tienda->link}}/comprobante</span>
                                <span> <B>Resumen:</B> </span>
                                <span> <b>Direccion Fiscal:</b> </span>
                            </td>
                        </tr>
                    </table>
                </div>
            @endif

        </div>
    </body>
</html>