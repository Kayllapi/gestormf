<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>PDF A4</title>
    </head>
    <?php $color =  configuracion($tienda->id,'sistema_color')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_color')['valor']:'#31353d' ?>  
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
            margin-left:10px;
            width: 40%;
            height: 100px;
            float: left;
            line-height: 15px;
            margin-right: 1%;
            border: 1px solid <?php echo $color ?>;
            border-radius: 5px;
        }
        .info-empresa-borde {
            text-align: center;
            font-size: 10px;
            padding: 10px;
            position: absolute;
            top: 0px;
        }
  
        .ruc {
            width: 238px;
            height: 100px;
            float: left;
            text-align: center;
            border: 1px solid <?php echo $color ?>;
            border-radius: 5px;
        }
  
        .info-empresa-img {
            height: 100px;
            width: 185px;
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
  
        .ruc {
            padding-top:7px;
            height: 93px;
        }
        .ruc > span {
            line-height: 30px;
        }
  
        .text-ruc-serie {
            font-size: 12px;
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
            border-radius: 5px; 
            border: 1px solid <?php echo $color ?>;
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
            border-radius: 5px; 
            border: 1px solid <?php echo $color ?>;
            padding-left: 10px;
        }
  
        .container-detail {
          border-bottom:  1px solid <?php echo $color ?>;
          margin-right: -7.5px;
          height: auto;
        }
  
        .container-detail-credito {
          border-bottom:  1px solid <?php echo $color ?>;
          margin-right: -7.5px;
          height: auto;
          width: 400px;
        }
  
        .container-total {
          width: auto;
          margin-right: 22.6px;
        }
  
        .border-tr-left {
            border-left: 1px solid <?php echo $color ?>;
        }
  
        .border-tr-right {
            border-right: 1px solid <?php echo $color ?>;
        }
  
        .border-tr-top {
            border-top: 1px solid <?php echo $color ?>;
        }
  
        .container-leyenda {
            margin-top: 10px;
            width: 721px;
            height: auto;
            border-radius: 5px; 
            border: 1px solid <?php echo $color ?>;
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
            margin-top: 0px;
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
            background-color: <?php echo $color ?>; 
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
  
    </style>

    <body>
        {{-- Cabecera --}}
        <div id="header">
            <div class="container-head">
                <div class="info-empresa-img txt-12 txt-center">
                    @if($facturacionboletafactura->agencialogo!='')
                        <img height="60px" src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$facturacionboletafactura->agencialogo) }}"><br>
                    @endif
                    <span class="info-empresa-span-rz">{{ strtoupper($facturacionboletafactura->emisor_razonsocial) }}</span>
                    <span>{{ strtoupper($facturacionboletafactura->emisor_direccion) }}</span>
                    <span>{{ strtoupper($facturacionboletafactura->emisor_departamento.'/'.$facturacionboletafactura->emisor_provincia.'/'.$facturacionboletafactura->emisor_distrito) }}</span>
                </div>
                
                <div class="info-empresa txt-center">
                    <div class="info-empresa-borde">
                        <span class="logo-description">{{ $tienda->contenido }}</span>
                    </div>
                </div>
            
                <div class="ruc">
                    <span class="text-ruc-serie">R.U.C: {{ $facturacionboletafactura->emisor_ruc }}</span>
                    @if($facturacionboletafactura->venta_tipodocumento==3)
                      <span class="text-tipo-documento">
                          BOLETA ELECTRÓNICA
                      </span>
                    @elseif($facturacionboletafactura->venta_tipodocumento==1)
                      <span class="text-tipo-documento">
                          FACTURA ELECTRÓNICA
                      </span>
                    @endif
                    <span  class="text-ruc-serie">{{$facturacionboletafactura->venta_serie}}-{{ str_pad($facturacionboletafactura->venta_correlativo, 8, "0", STR_PAD_LEFT) }}</span>
                </div>
            </div>
        </div>

        {{-- Pie de Pagina --}}
        <div id="footer" class="txt-10 txt-center">
            <div class="container-img-txt">
                <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$facturacionboletafactura->agencialogo) }}" height="30px">
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
                        <td width="50px" class="text-black">CLIENTE</td>
                        <td width="1px">:</td>
                        <td>{{ strtoupper($facturacionboletafactura->cliente_razonsocial) }}</td>
                        <td width="110px" class="text-black">MONEDA</td>
                        <td width="1px">:</td>
                        <td>
                            @if($facturacionboletafactura->venta_tipomoneda=='PEN')
                                SOLES
                            @else($facturacionboletafactura->venta_tipomoneda=='USD')
                                DOLARES
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-black">RUC</td>
                        <td width="1px">:</td>
                        <td>{{ $facturacionboletafactura->cliente_numerodocumento }}</td>
                        <td class="text-black">FECHA DE EMISIÓN</td>
                        <td width="1px">:</td>
                        <td>{{ date_format(date_create($facturacionboletafactura->venta_fechaemision),"d/m/Y h:i:s A") }}</td>
                    </tr>
                    <tr>
                        <td class="text-black">DIRECCIÓN</td>
                        <td width="1px">:</td>
                        <td colspan="4">
                          {{ strtoupper($facturacionboletafactura->cliente_direccion) }}, 
                          {{ strtoupper($facturacionboletafactura->cliente_departamento.'/'.$facturacionboletafactura->cliente_provincia.'/'.$facturacionboletafactura->cliente_distrito) }}
                        </td>
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
                            <th class="border-tr-top border-tr-left thead-tr">DESCIPCIÓN</td>
                            <th class="border-tr-top border-tr-left thead-tr" width="80px">P.UNIT.</td>
                            <th class="border-tr-top border-tr-left border-tr-right thead-tr" width="80px">TOTAL</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $item = 0; ?>
                        @foreach($boletafacturadetalle as $value)
                            <?php $item++; ?>
                            <tr class="table-tr" style="border-bottom: 1px solid black;">
                                <td style="padding:3px;" class="border-tr-left txt-center" width="10px">{{ $item }}</td>
                                <td style="padding:3px;" class="border-tr-left txt-center" width="30px">{{ $value->cantidad }}</td>
                                <td style="padding:3px;" class="border-tr-left txt-center" width="70px">{{ $value->unidad }}</td>
                                <td style="padding:3px;" class="border-tr-left">{{ strtoupper($value->descripcion) }}</td>
                                <td style="padding:3px;" class="border-tr-left txt-right" width="80px">{{ $value->montopreciounitario }}</td>
                                <td style="padding:3px;" class="border-tr-left border-tr-right txt-right" width="80px">{{number_format($value->cantidad*$value->montopreciounitario, 2, '.', '') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Detalle Totales PDF --}}
            <div class="container-total">
                <table class="table txt-10" width="731px" cellspacing="0" style="margin-top: 10px;">
                    <tr>
                        <th style="padding:3px;" class="txt-right">SUBTOTAL</th>
                        <th width="3%" class="txt-right"></th>
                        <th style="padding:3px;" width="3%" class="txt-right">{{ $facturacionboletafactura->venta_valorventa }}</th>
                    </tr>
                    <tr>
                        <th style="padding:3px;" class="txt-right">IGV 18.00 %</th>
                        <th class="txt-right"></th>
                        <th style="padding:3px;" class="txt-right">{{ $facturacionboletafactura->venta_totalimpuestos }}</th>
                    </tr>
                    <tr>
                        <th style="padding:3px;" class="txt-right">TOTAL</th>
                        <th class="txt-right"></th>
                        <th style="padding:3px;" class="txt-right">{{ $facturacionboletafactura->venta_montoimpuestoventa }}</th>
                    </tr>
                </table>
            </div>
            
            <div class="container-leyenda txt-10">
                <b>IMPORTE EN LETRAS:</b> {{ $facturacionboletafactura->leyenda_value }}
            </div>

             {{-- Detalle Forma de Pago Credito --}}
                <!-- <div class="container-detail-credito">
                    <table class="table txt-10" cellspacing="0">
                        <thead>
                            <tr>
                                <th colspan="4" class="border-tr-top border-tr-left thead-tr border-tr-right">DETALLE CUOTA CREDITO</th>
                            </tr>
                            <tr>
                                <th class="border-tr-top border-tr-left thead-tr">NRO CUOTA</th>
                                <th class="border-tr-top border-tr-left thead-tr">FECHA DE VENCIMIENTO</th>
                                <th class="border-tr-top border-tr-left thead-tr">MONEDA</th>
                                <th class="border-tr-top border-tr-left thead-tr border-tr-right">MONTO CUOTA</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border-tr-left txt-center">SADAD</td>
                                    <td class="border-tr-left txt-center">12321</td>
                                    <td class="border-tr-left txt-center">SADASD</td>
                                    <td class="border-tr-left txt-center border-tr-right">ASDSAD</td>
                                </tr>
                        </tbody>
                    </table>
                </div> -->

                <div class="container-leyenda">
                    <table class="table">
                        <tr>
                            <td style="width:90px;">
                                <img src="<?php echo $facturacionboletafactura->respuestaqr ?>" width="80px">
                            </td>
                            <td>
                                <span> <b>REPRESENTACIÓN IMPRESA DE LA 
                                  @if($facturacionboletafactura->venta_tipodocumento==3)
                                      BOLETA ELECTRÓNICA
                                  @elseif($facturacionboletafactura->venta_tipodocumento==1)
                                      FACTURA ELECTRÓNICA
                                  @endif</b>
                                </span>
                                <span>AUTORIZADO MEDIANTE RESOLUCIÓN DE INTENDENCIA NRO 034-005-0005315, PUEDE SER CONSULTADA EN: <b>www.kayllapi.com/{{$tienda->link}}/comprobante</b></span><br>
                                <span> <b>¡GRACIAS POR SU COMPRA, QUE DIOS LE BENDIGA!</b> </span>
                            </td>
                        </tr>
                    </table>
                </div>

        </div>
    </body>
</html>