<!DOCTYPE html>
<html>
<head>
    <title>ASIGNACIÓN, REDUCCIÓN E INCREMENTO DE CAPITAL</title>
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
          <div class="cabecera"><b>{{ $tienda->ticket_nombre }}</b></div>
          <div class="cabecera">{{ $tienda->direccion }}</div>
          <div class="linea"></div><br>
          <div class="titulo"><b>ASIGNACIÓN, REDUCCIÓN E INCREMENTO DE CAPITAL</b></div>  <br>
          <table style="width:100%;">
            <tr>
                <td style="width:65px;">
                    <b>Fecha :</b> {{ date_format(date_create($asignacioncapital->fecharegistro),'d-m-Y h:i:s A') }}
                </td>
            </tr>
            <tr>
                <td>
                    <b>N° Op. :</b> {{$asignacioncapital->codigoprefijo}}{{ str_pad($asignacioncapital->codigo, 10, "0", STR_PAD_LEFT) }}
                </td>
            </tr>
          </table>  
          <div class="linea"></div>
          <table style="width:100%;">
            <tr>
                <td>
                    <b>T. Operación :</b> {{ $asignacioncapital->credito_tipooperacionnombre }}
                </td>
            </tr>
              @if($asignacioncapital->idtipodestino!=0)
            <tr>
                <td>
                    <b>Destino - depósito /Fuente - retiro :</b> {{ $asignacioncapital->credito_tipodestinonombre }}
                </td>
            </tr>
              @endif
          </table>  
          <div class="linea"></div>
          <table style="width:100%;">
              @if($asignacioncapital->idtipodestino==3)
            <tr>
                <td>
                    <b>Banco :</b> {{ $asignacioncapital->banco }} ***{{ substr($asignacioncapital->cuenta, -5) }}
                </td>
            </tr>
            <tr>
                <td>
                    <b>N° Oper. Banco :</b> {{ $asignacioncapital->numerooperacion }}
                </td>
            </tr>
              @endif
            <tr>
                <td style="width:80px;">
                    <b>Descripción :</b> {{ $asignacioncapital->descripcion }}
                </td>
            </tr>
          </table>  
          <div class="linea"></div>  
          <table style="width:100%;">
            <tr>
                <td>
                    <b>Moto (Soles) :</b> <span style="float:right">S/. {{ number_format($asignacioncapital->monto, 2, '.', '') }}</span>
                </td>
            </tr>
          </table>  
          <div class="linea"></div> 
                    <br>
                    <br>
                    <br>
                    <br> 
          <table class="tabla_informativa" style="text-align:center;">
              <tr>
                  <td style="width:140px;vertical-align: top;"> 
                    <div class="linea"></div> <b>Emisor</b>
                    <div>{{$asignacioncapital->nombrecompleto_responsable}}</div>
                    <div><b>{{$asignacioncapital->nombrepermiso_responsable}}</b></div>
                    <div>{{$asignacioncapital->codigo_responsable}}</div>
                    <div>(Firma y Huella D.)</div>
                  </td>
                  <td style="width:140px;vertical-align: top;"> 
                    <div class="linea"></div> <b>Receptor Final de Efectivo</b>
                    <div>{{$asignacioncapital->nombrecompleto_responsable_recfinal}}</div>
                    <div><b>{{$asignacioncapital->nombrepermiso_responsable_recfinal}}</b></div>
                    <div>{{$asignacioncapital->codigo_responsable_recfinal}}</div>
                    <div>(Firma y Huella D.)</div>
                </td>
              </tr>
          </table>  
  
    </div>
</body>
</html>