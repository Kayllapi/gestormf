<!DOCTYPE html>
<html>
<head>
    <title>TARJETA DE RECAUDACIÓN</title>
    @include('app.pdf_style',['idtienda'=>$tienda->id])
</head>
<body>
    <div class="tarjetapago_contenedor">
        <div class="contenedor">
          @include('app.pdf_headerfooter_ticket',[
              'logo'=>$tienda->imagen,
              'nombrecomercial'=>$prestamoahorro->facturacion_agencianombrecomercial,
              'ruc'=>$prestamoahorro->facturacion_agenciaruc,
              'direccion'=>$prestamoahorro->facturacion_agenciadireccion,
              'ubigeo'=>$prestamoahorro->facturacion_agenciaubigeonombre,
              'tienda'=>$tienda,
          ])
          <div class="espacio"></div>
          <table class="tabla_informativa">
              <tr>
                  <td class="tabla_informativa_subtitulo" style="width:16%;">CLIENTE</td>
                  <td class="tabla_informativa_punto" style="width:1%;">:</td>
                  <td class="tabla_informativa_descripcion" style="width:50%;">{{$prestamoahorro->cliente_nombre}}</td>
                  <td class="tabla_informativa_subtitulo" style="width:13%;">CÓDIGO</td>
                  <td class="tabla_informativa_punto" style="width:1%;">:</td>
                  <td class="tabla_informativa_descripcion" style="width:19%;">{{ str_pad($prestamoahorro->codigo, 8, "0", STR_PAD_LEFT) }}</td>
              </tr>
              <tr>
                  <td class="tabla_informativa_subtitulo">TELÉFONO</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{$prestamoahorro->cliente_numerotelefono}}</td>
                  <td class="tabla_informativa_subtitulo">MONEDA</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{$prestamoahorro->monedanombre}}</td>
              </tr>
              <tr>
                  <td class="tabla_informativa_subtitulo">CONFIRMADO</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{$prestamoahorro->monto}}</td>
                  <td class="tabla_informativa_subtitulo" style="color:#c42626;">MORA</td>
                  <td class="tabla_informativa_punto" style="color:#c42626;">:</td>
                  <?php
                  $mora_pordia = 0;
                  if(configuracion($tienda->id,'prestamo_ahorro_morapordefecto')['valor']==1){
                      if(configuracion($tienda->id,'prestamo_ahorro_moratipo')['valor']==1){ // por frecuencia de pagos

                          if($prestamoahorro->idprestamo_frecuencia==1){
                              $mora_pordia = configuracion($tienda->id,'prestamo_ahorro_mora_diario')['valor'];
                          }elseif($prestamoahorro->idprestamo_frecuencia==2){
                              $mora_pordia = configuracion($tienda->id,'prestamo_ahorro_mora_semanal')['valor'];
                          }elseif($prestamoahorro->idprestamo_frecuencia==3){
                              $mora_pordia = configuracion($tienda->id,'prestamo_ahorro_mora_quincenal')['valor'];
                          }elseif($prestamoahorro->idprestamo_frecuencia==4){
                              $mora_pordia = configuracion($tienda->id,'prestamo_ahorro_mora_mensual')['valor'];
                          }elseif($prestamoahorro->idprestamo_frecuencia==5){
                              $mora_pordia = configuracion($tienda->id,'prestamo_ahorro_mora_programado')['valor'];
                          }

                      }
                      elseif(configuracion($tienda->id,'prestamo_ahorro_moratipo')['valor']==2){ // por rango de montos

                          $morarangos = json_decode(configuracion($tienda->id,'prestamo_ahorro_morarango')['valor']);
                          foreach($morarangos as $value){
                              if($prestamoahorro->monto<=$value->morarango){
                                  $mora_pordia = $value->morarangomonto;
                                  break;
                              }
                          } 

                      }
                  }
                  elseif(configuracion($tienda->id,'prestamo_ahorro_morapordefecto')['valor']==2){
                          if($prestamoahorro->idprestamo_frecuencia==1){
                              $mora_pordia = configuracion($tienda->id,'prestamo_ahorro_mora_diario_efectiva')['valor'];
                          }elseif($prestamoahorro->idprestamo_frecuencia==2){
                              $mora_pordia = configuracion($tienda->id,'prestamo_ahorro_mora_semanal_efectiva')['valor'];
                          }elseif($prestamoahorro->idprestamo_frecuencia==3){
                              $mora_pordia = configuracion($tienda->id,'prestamo_ahorro_mora_quincenal_efectiva')['valor'];
                          }elseif($prestamoahorro->idprestamo_frecuencia==4){
                              $mora_pordia = configuracion($tienda->id,'prestamo_ahorro_mora_mensual_efectiva')['valor'];
                          }elseif($prestamoahorro->idprestamo_frecuencia==5){
                              $mora_pordia = configuracion($tienda->id,'prestamo_ahorro_mora_programado_efectiva')['valor'];
                          }
                  }  
                  ?>
                  <td class="tabla_informativa_descripcion" style="color:#c42626;">{{$mora_pordia}} X DÍA</td>
              </tr>
          </table>
          <div class="espacio"></div>
          <table class="tabla">
              <tr class="tabla_cabera">
                  <td>Nº</td>
                  <td>FECHA</td>
                  <td>SALDO</td>
                  <td>CUOTA</td>
                  <td>MORA</td>
                  <td>TOTAL</td>
                  <td>CANCELADO</td>
                  <td>FIRMA</td>
              </tr>
              @foreach ($prestamoahorrodetalle as $value)
              <tr>
                  <td>{{ str_pad($value->numero, 2, "0", STR_PAD_LEFT) }}</td>
                  <td>{{ date_format(date_create($value->fechaahorro),"d/m/Y") }}</td>
                  <td>{{ $value->saldocapital }}</td>
                  <td>{{ $value->total }}</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
              </tr>
              @endforeach
          </table>
          <div class="espacio"></div>
          <table class="tabla_informativa">
              <tr>
                  <td class="tabla_informativa_subtitulo" style="width:12%;">ASESOR</td>
                  <td class="tabla_informativa_punto" style="width:1%;">:</td>
                  <td class="tabla_informativa_descripcion" style="width:54%;">{{$prestamoahorro->asesornombre}}</td>
                  <td class="tabla_informativa_subtitulo" style="width:15%;">TELÉFONO</td>
                  <td class="tabla_informativa_punto" style="width:1%;">:</td>
                  <td class="tabla_informativa_descripcion" style="width:17%;">{{$prestamoahorro->asesor_numerotelefono}}</td>
              </tr>
          </table>
          <div class="espacio"></div>
          <div class="dato_adicional">
              TU MEJOR GARANTIA ES TU PUNTUALIDAD. EL NO PAGO PUNTUAL DE TU AHORRO TE GENERA MORA, QUE SE HARÁ EFECTO EN LOS SIGUIENTES RECAUDACIONES.
          </div>    
        </div>
    </div>
</body>
</html>