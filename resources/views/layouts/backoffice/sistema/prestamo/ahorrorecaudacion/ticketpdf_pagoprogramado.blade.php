<!DOCTYPE html>
<html>
<head>
    <title>TICKET DE PAGO PROGRAMADO</title>
    @include('app.pdf_style',['idtienda'=>$tienda->id])
</head>
<body>
    <div class="ticket_contenedor">
      <div class="contenedor">
          @include('app.pdf_headerticket',[
              'logo'=>$agencia->logo,
              'nombrecomercial'=>$agencia->nombrecomercial,
              'ruc'=>$agencia->ruc,
              'direccion'=>$agencia->direccion,
              'ubigeo'=>$agencia->ubigeonombre,
              'tienda'=>$tienda,
          ])
      </div>
    </body>
</html>