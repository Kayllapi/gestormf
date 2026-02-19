<!DOCTYPE html>
<html>
<head>
    <title>TICKET DE DESEMBOLSO</title>
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
          border-top: 1px solid #000;
          width:100%;
      }
    </style>
</head>
<body>
    <div class="ticket_contenedor">
          <div class="cabecera"><b>{{ $tienda->nombre }} - {{ $tienda->nombreagencia }}</b></div>
            <div class="linea"></div>
          <br>
          <div class="titulo"><b>DESEMBOLSO</b></div>
          <table class="tabla_informativa">
              <tr>
                  <td><b>OP:</b> OP{{ str_pad($operacion, 8, "0", STR_PAD_LEFT) }}</td>
              </tr>
              <tr>
                  <td><b>CUENTA:</b> C{{ str_pad($credito->cuenta, 8, "0", STR_PAD_LEFT) }}</td>
              </tr>
              <tr>
                  <td><b>NOMBRES:</b> {{ $usuario->nombrecompleto }}</td>
              </tr>
              <tr>
                  <td><b>DNI:</b> {{ $usuario->identificacion }}</td>
              </tr>
              <tr>
                  <td><b>FECHA:</b> {{ date_format(date_create($credito->fecha_desembolso),'d-m-Y h:s:i A') }}</td>
              </tr>
              <tr>
                  <td><b>F. DESEMBOLSO:</b> {{ $idformapago==1?'CAJA':'BANCO' }} </td>
              </tr>
              @if($idformapago==2)
              <tr>
                  <td><b>BANCO:</b> {{ $banco }} ***{{ substr($bancocuenta, -5) }}</td>
              </tr>
              <tr>
                  <td><b>OPE. / DETALLE:</b> {{ $numerooperacion }}</td>
              </tr>
              @endif
          </table>   
     
          <table style="width:100%;">
            <tr>
                <td style="border-top: 1px dashed #000;padding-top:5px;padding-bottom:5px;">
                    <b>DESEMBOLSO</b><br>
                    <b>SALDO A DESCONTAR</b>
                </td>
                <td width="5px" style="border-top: 1px dashed #000;padding-top:5px;padding-bottom:5px;text-align:right;">
                    <b>S/.:</b><br>
                    <b>S/.:</b>
                </td>
                <td width="60px" style="border-top: 1px dashed #000;padding-top:5px;padding-bottom:5px;text-align:right;">
                    {{ $credito->monto_solicitado }}<br>
                    {{ $credito->descuento_saldo }}
                </td>
            </tr>
            <tr>
              <td style="border-bottom: 1px dashed #000;border-top: 1px dashed #000;padding-top:5px;padding-bottom:5px;">
                <b>NETO A ENTREGAR</b> 
              </td>
                <td width="5px" style="border-bottom: 1px dashed #000;border-top: 1px dashed #000;padding-top:5px;padding-bottom:5px;text-align:right;">
                    <b>S/.:</b>
                </td>
              <td style="border-bottom: 1px dashed #000;border-top: 1px dashed #000;padding-top:5px;padding-bottom:5px;text-align:right;">
                @if($credito->idmodalidad_credito == 2)
                {{ $credito->neto_entregar }}
                @else
                {{ $credito->monto_solicitado }}
                @endif
              </td>
            </tr>
          </table>     
      
      <br><br><br><br>
          <table class="tabla_informativa">
              <tr>
                  <td><b>FIRMA:</b> ______________________</td>
              </tr>
              <tr>
                  <td><b>CAJA:</b> {{ strtoupper($cajero->codigo) }}</td>
              </tr>
          </table>  
    </div>
</body>
</html>