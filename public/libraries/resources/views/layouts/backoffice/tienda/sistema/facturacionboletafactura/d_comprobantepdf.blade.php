<!DOCTYPE html>
<html>
<head>
    <title>Comprobante</title>
    <style>
        html, body {
            font-family: helvetica;
          padding:0px;
          margin:10px ;
          margin-right:15px;
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
            font-size: 12px;
        }
        .logo {
            width: 50px;
            height: 50px;
        }
        .ruc {
            width: 40%;
            height:130px;
            background-color: #a9acb2;
            border-radius:10px;
            border:2px solid #3b404b;
            margin-top:15px;
            font-weight:bold;
            font-size:20px;
        
        }
        /* fin */
        /* Tablero de productos*/
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
       /* Fin*/
      
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
       /* Tablero de Codigo QR*/

        .codigoqr{
             width: 100px;
             height:100px;
        }
       /* Fin*/
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
                <b>{{ $facturacionboletafactura->emisor_direccion }}</b><br>
                <b>{{ $tienda->correo }}</b><br>
                <b>{{ $tienda->numerotelefono }}</b>
            </p>
        </div>
        <div class="ruc">
            <div class="center">
              <p style="margin-top:22px;padding:10px;">
                R.U.C. {{$facturacionboletafactura->emisor_ruc}}<br> 
                @if($facturacionboletafactura->venta_tipodocumento==3)
                 BOLETA <br>
                   {{$facturacionboletafactura->venta_serie}} - {{ str_pad($facturacionboletafactura->id, 8, "0", STR_PAD_LEFT) }}<br>
                @elseif($facturacionboletafactura->venta_tipodocumento==1)
                  FACTURA <br> 
                    {{$facturacionboletafactura->venta_serie}} - {{ str_pad($facturacionboletafactura->id, 8, "0", STR_PAD_LEFT) }}<br>
                @else($facturacionboletafactura->venta_tipodocumento==0)
                  TICKET<br> 
                    {{$facturacionboletafactura->venta_serie}} - {{ str_pad($facturacionboletafactura->id, 8, "0", STR_PAD_LEFT) }}<br>
                @endif
              </p>
            </div>
        </div>
    </div>
    <div class="container">
        <table class="table" cellspacing="0">
            <thead>
                <tr>
                    <td colspan="2">
                        <b>
                          SEÑOR(A): {{$facturacionboletafactura->cliente_razonsocial}}
                        </b>
                    </td>
                    <td>
                        <b>
                          @if($facturacionboletafactura->cliente_numerodocumento!='')
                            @if($facturacionboletafactura->cliente_tipodocumento==1)
                              DNI: {{$facturacionboletafactura->cliente_numerodocumento}}<br>
                            @else
                              R.U.C.: {{$facturacionboletafactura->cliente_numerodocumento}}<br>
                            @endif
                          @endif 
                        </b> 
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <b>DIRECCIÓN: {{$facturacionboletafactura->cliente_direccion}}</b> 
                    </td>
                  <td >
                        <b>UBIGEO: {{$facturacionboletafactura->cliente_departamento}}/{{$facturacionboletafactura->cliente_provincia}}/{{$facturacionboletafactura->cliente_distrito}}</b> 
                    </td>
                </tr>
               
                <tr>
                    <td>
                        <b>
                          F. EMISIÓN: {{date_format(date_create($facturacionboletafactura->venta_fechaemision),"d/m/Y") }}
                        </b> 
                    </td>
                    <td>
                        <b>
                          HORA: {{date_format(date_create($facturacionboletafactura->	venta_fechaemision),"h:i:s") }}
                        </b>
                    </td>
                    <td>
                        <b>
                          @if($facturacionboletafactura->venta_tipomoneda=='PEN')
                            MONEDA: Soles
                          @else($facturacionboletafactura->venta_tipomoneda=='USD')
                            MONEDA: Dolares
                          @endif
                        </b>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <b>
                          RESPONSABLE: {{$facturacionboletafactura->responsablenombre}}
                        </b> 
                    </td>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
      
        <table class="table" cellspacing="0">
            <tr class="table-header">
                <td width="3%">
                    <b>N°</b>
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
                <td class="center" width="50px">
                    <b>P. UNIT</b>
                </td>
                <td class="center" width="55px">
                    <b>TOTAL</b>
                </td>
            </tr>
            @php $item = 1 @endphp
            @foreach($boletafacturadetalle as $value)
                <tr>
                    <td class="center">{{ $item }}</td>
                    <td class="center">{{ $value->codigoproducto }}</td>
                    <td>{{ $value->descripcion }}</td>
                    <td class="center">{{ $value->cantidad }}</td>
                    <td class="center">{{$value->montopreciounitario}}</td>
                    <td class="center">{{number_format($value->cantidad*$value->montopreciounitario, 2, '.', '') }}</td>
                </tr>
            @php $item ++ @endphp
            @endforeach
            @for ($j = $item; $j<=29; $j++)
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
            <tr>
                <td colspan="6" class="left">
                    <b>SON : CIENTO OCHENTA CON 00/100 SOLES </b>
                </td>
                
            </tr>
      
        </table>
        <table class="table" cellspacing="0" style="border:0px solid #fff;">
            <tr style="border:0px solid #fff;">
              <td rowspan="3" width="400px" style="border:0px solid #fff;">
                Informacion: 
              </td>
              <td rowspan="3" width="100px" style="border:0px solid #fff;" >
                <img class="codigoqr center"src="https://static-unitag.com/images/help/QRCode/qrcode.png?mh=07b7c2a2">
              </td>
              <td class="right" width="80px">
               <b>SUB. TOTAL S/.</b> 
              </td>
              <td class="center" width="55px">
                {{ $facturacionboletafactura->venta_valorventa }}</td>
            </tr>
            <tr>
              <td class="right" width="80px">
                <b>IGV(18%) S/.</b>
              </td>
              <td class="center" width="55px" >
                {{ $facturacionboletafactura->venta_totalimpuestos }}
              </td>
            </tr>
            <tr>
              <td class="right" width="80px">
                <b>TOTAL S/.</b>
              </td>
              <td class="center" width="55px">
                {{ $facturacionboletafactura->venta_montoimpuestoventa }}
              </td>
            </tr>
        </table>

</body>
</html>