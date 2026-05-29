<!DOCTYPE html>
<html>
<head>
    <title>TICKET DE GARANTIA</title>
    <style>
      *{
        font-family:helvetica;
        font-size:12px;
      }
      @page {
          margin: 15px;
      }
      .ticket_contenedor {
          width: 267px;
      }
      .cabecera {
          
      }
      .titulo {
        text-align: center;
        
      }
      .linea {
          border-top: 0.5px solid #000;
          width:100%;
      }
    </style>
</head>
<body>
    <div class="ticket_contenedor">  
      <div><b>{{ $tienda->nombre }} - {{ $tienda->nombreagencia }}</b> <span style="float: right;">{{ strtoupper($cajero->codigo) }}</span></div>
      <div class="linea"></div>
          <table class="tabla_informativa" width="100%">
              <tr>
                  <td>
                    <b>APE. Y NOM.:</b> {{ $usuario->nombrecompleto }}</td>
              </tr>
              <tr>
                  <td><b>CUENTA:</b> C{{ str_pad($credito->cuenta, 8, "0", STR_PAD_LEFT) }} <b>//</b> <span style="font-size: 11px;">{{ date_format(date_create($credito->fecha_desembolso),'d-m-Y h:s:i A') }}</span></td>
              </tr>
            <tr>
              <td style="border-top: 1px dashed #000;border-bottom: 1px dashed #000;padding-top:5px;padding-bottom:5px;">
                <b>{{ $num }}.- </b> GP{{ str_pad($garantias->id, 8, '0', STR_PAD_LEFT)  }}<br>
                <b>Descripción:</b> {{ $garantias->descripcion }}<br>
                <b>Serie/Motor/Nro Partida:</b> {{ $garantias->serie_motor_partida }}<br>
                <b>Color:</b> {{ $garantias->color }}<br>
                <b>Accesorios:</b> {{ $garantias->accesorio_doc }}<br>
              </td>
            </tr>
          </table> 
      
    </div>
</body>
</html>