<!DOCTYPE html>
<html>
<head>
    <title>ENTREGA DE GARANTÍA</title>
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
      <div><b>ENTREGA DE GARANTÍA</b></div><div class="linea"></div>

          <table class="tabla_informativa" width="100%">
              <tr>
                  <td>
                    <b>N° DE CUENTA:</b> C{{ str_pad($creditocuenta, 8, "0", STR_PAD_LEFT) }}</td>
              </tr>
              <tr>
                  <td><b>FECHA:</b> {{ date_format(date_create($fechaentrega),'d-m-Y H:i:s A') }}</td>
              </tr>
              
            <?php $i=1 ?>
            @foreach($garantias as $value)
            <tr>
              <td style="border-top: 1px dashed #000;border-bottom: 1px dashed #000;padding-top:5px;padding-bottom:5px;">
                <b>{{ $i }}.- </b> GP{{ str_pad($value->id, 8, '0', STR_PAD_LEFT)  }}<br>
                <b>Descripción:</b> {{ $value->descripcion }}<br>
                <b>Serie/Motor/Nro Partida:</b> {{ $value->serie_motor_partida }}<br>
                <b>Color:</b> {{ $value->color }}<br>
                <b>Accesorios:</b> {{ $value->accesorio_doc }}<br>
              </td>
            </tr>
            <?php $i++ ?>
            @endforeach
          </table> 
      
      <br><br>
          <table class="tabla_informativa" width="100%">
              <tr>
                  <td style="text-align:left;"><br><br><div class="linea"></div><b>FIRMA Y HUELLA DE CONFOR. DE RECEPCIÓN:</b><br>
                <b>A. N. CLIENTE:</b> {{ strtoupper($usuario->nombrecompleto) }}<br>
                <b>DNI:</b> {{ strtoupper($usuario->identificacion) }}<br>
                 <b>USUARIO:</b> {{ strtoupper($cajero->codigo) }}
                </td>
              </tr>
          </table>  
      
    </div>
</body>
</html>