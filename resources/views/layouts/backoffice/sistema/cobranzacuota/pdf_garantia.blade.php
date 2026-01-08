<!DOCTYPE html>
<html>
<head>
    <title>V. ENTREGA DE GARANTÍA</title>
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
    </style>
</head>
<body>
    <div class="ticket_contenedor">  
     
          <table class="tabla_informativa">
              <tr>
                  <td>
                    <b>N° PRÉSTAMO:</b> C{{ str_pad($credito_cobranzacuota->creditocuenta, 8, "0", STR_PAD_LEFT) }}</td>
              </tr>
              <tr>
                  <td><b>FECHA:</b> {{ date_format(date_create($credito_cobranzacuota->fecharegistro),'d-m-Y H:i:s A') }}</td>
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
          <table class="tabla_informativa">
              <tr>
                  <td style="text-align:center;"> ________________________________________<br><b>FIRMA DE CONFORMIDAD DE RECEPCIÓN:</b></td>
              </tr>
              <tr>
                  <td><b>APELLIDOS Y NOMBRES:</b> {{ strtoupper($usuario->nombrecompleto) }}</td>
              </tr>
              <tr>
                  <td><b>DNI:</b> {{ strtoupper($usuario->identificacion) }}</td>
              </tr>
              <tr>
                  <td><b>USUARIO:</b> {{ strtoupper($cajero->codigo) }}</td>
              </tr>
          </table>  
      
    </div>
</body>
</html>