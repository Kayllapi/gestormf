<!DOCTYPE html>
<html>
<head>
    <title>TARJETA DE PAGO</title>
    @include('app.pdf_style',['idtienda'=>$tienda->id])
</head>
<body>
    <div class="tarjetapago_contenedor">
        <div class="contenedor">
          @include('app.pdf_headerticket',[
              'logo'=>$tienda->imagen,
              'nombrecomercial'=>$prestamodesembolso->facturacion_agencianombrecomercial,
              'ruc'=>$prestamodesembolso->facturacion_agenciaruc,
              'direccion'=>$prestamodesembolso->facturacion_agenciadireccion,
              'ubigeo'=>$prestamodesembolso->facturacion_agenciaubigeonombre,
              'tienda'=>$tienda,
          ])
          <table class="tabla_informativa">
              <tr>
                  <td style="width:16%;">CLIENTE</td>
                  <td style="width:1%;">:</td>
                  <td style="width:47%;">{{$prestamodesembolso->cliente_nombre}}</td>
                  <td style="width:18%;">CRÉDITO</td>
                  <td style="width:1%;">:</td>
                  <td style="width:17%;">{{ str_pad($prestamodesembolso->codigo, 8, "0", STR_PAD_LEFT) }}</td>
              </tr>
              <tr>
                  <td>TELÉFONO</td>
                  <td>:</td>
                  <td>{{$prestamodesembolso->cliente_numerotelefono}}</td>
                  <td>MONEDA</td>
                  <td>:</td>
                  <td>{{$prestamodesembolso->monedanombre}}</td>
              </tr>
              <tr>
                  <td>ASESOR</td>
                  <td>:</td>
                  <td>{{$prestamodesembolso->asesor_nombre}}</td>
                  <td>DESEMBOLSO</td>
                  <td>:</td>
                  <td>{{$prestamodesembolso->monto}}</td>
              </tr>
              <tr>
                  <td>TELÉFONO</td>
                  <td>:</td>
                  <td>{{$prestamodesembolso->asesor_numerotelefono}}</td>
                  <td>MORA X DÍA</td>
                  <td>:</td>
                  <?php
                  $mora_pordia = 0;
                  if(configuracion($tienda->id,'prestamo_morapordefecto')['valor']==1){
                      if(configuracion($tienda->id,'prestamo_moratipo')['valor']==1){ // por frecuencia de pagos

                          if($prestamodesembolso->idprestamo_frecuencia==1){
                              $mora_pordia = configuracion($tienda->id,'prestamo_mora_diario')['valor'];
                          }elseif($prestamodesembolso->idprestamo_frecuencia==2){
                              $mora_pordia = configuracion($tienda->id,'prestamo_mora_semanal')['valor'];
                          }elseif($prestamodesembolso->idprestamo_frecuencia==3){
                              $mora_pordia = configuracion($tienda->id,'prestamo_mora_quincenal')['valor'];
                          }elseif($prestamodesembolso->idprestamo_frecuencia==4){
                              $mora_pordia = configuracion($tienda->id,'prestamo_mora_mensual')['valor'];
                          }elseif($prestamodesembolso->idprestamo_frecuencia==5){
                              $mora_pordia = configuracion($tienda->id,'prestamo_mora_programado')['valor'];
                          }

                      }
                      elseif(configuracion($tienda->id,'prestamo_moratipo')['valor']==2){ // por rango de montos

                          $morarangos = json_decode(configuracion($tienda->id,'prestamo_morarango')['valor']);
                          foreach($morarangos as $value){
                              if($prestamodesembolso->monto<=$value->morarango){
                                  $mora_pordia = $value->morarangomonto;
                                  break;
                              }
                          } 

                      }
                  }
                  elseif(configuracion($tienda->id,'prestamo_morapordefecto')['valor']==2){
                          if($prestamodesembolso->idprestamo_frecuencia==1){
                              $mora_pordia = configuracion($tienda->id,'prestamo_mora_diario_efectiva')['valor'];
                          }elseif($prestamodesembolso->idprestamo_frecuencia==2){
                              $mora_pordia = configuracion($tienda->id,'prestamo_mora_semanal_efectiva')['valor'];
                          }elseif($prestamodesembolso->idprestamo_frecuencia==3){
                              $mora_pordia = configuracion($tienda->id,'prestamo_mora_quincenal_efectiva')['valor'];
                          }elseif($prestamodesembolso->idprestamo_frecuencia==4){
                              $mora_pordia = configuracion($tienda->id,'prestamo_mora_mensual_efectiva')['valor'];
                          }elseif($prestamodesembolso->idprestamo_frecuencia==5){
                              $mora_pordia = configuracion($tienda->id,'prestamo_mora_programado_efectiva')['valor'];
                          }
                  }  
                  ?>
                  <td>{{$mora_pordia}}</td>
              </tr>
          </table>
          <div class="espacio"></div>
          <table class="tabla">
              <tr class="tabla_cabera">
                  <td>Nº</td>
                  <td>FECHA</td>
                  <td>SALDO</td>
                  <td>CUOTA</td>
                  @if($prestamodesembolso->total_abono>0)
                  <td>ABONO</td>
                  @endif
                  <td>MORA</td>
                  <td>TOTAL</td>
                  <td>CANCELADO</td>
                  <td>FIRMA</td>
              </tr>
              @foreach ($prestamodesembolsodetalle as $value)
              <tr>
                  <td>{{ str_pad($value->numero, 2, "0", STR_PAD_LEFT) }}</td>
                  <td>{{ date_format(date_create($value->fechavencimiento),"d/m/Y") }}</td>
                  <td>{{ $value->saldomontototal }}</td>
                  <td>{{ $value->total }}</td>
                  @if($prestamodesembolso->total_abono>0)
                  <td>{{ $value->abono }}</td>
                  @endif
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
              </tr>
              @endforeach
          </table>
          <div class="espacio"></div>
          <div class="dato_adicional">
              TU MEJOR GARANTIA ES TU PUNTUALIDAD. EL NO PAGO PUNTUAL DE TU CRÉDITO TE GENERA MORA, QUE SE HARÁ EFECTO EN LOS SIGUIENTES PAGOS.
          </div>    
        </div>
    </div>
</body>
</html>