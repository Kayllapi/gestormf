<!DOCTYPE html>
<html>
<head>
    <title>TARJETA DE PAGO</title>
    @include('app.pdf_style',['idtienda'=>$tienda->id])
</head>
<body>
    <div class="tarjetapago_contenedor">
        <div class="contenedor">
          @include('app.pdf_headerfooter_ticket',[
              'logo'=>$tienda->imagen,
              'nombrecomercial'=>$prestamodesembolso->facturacion_agencianombrecomercial,
              'direccion'=>$prestamodesembolso->facturacion_agenciadireccion,
              'ubigeo'=>$prestamodesembolso->facturacion_agenciaubigeonombre,
              'tienda'=>$tienda,
          ])
          <div class="espacio"></div>
          <table class="tabla_informativa">
              <tr>
                  <td class="tabla_informativa_subtitulo" style="width:16%;">CLIENTE</td>
                  <td class="tabla_informativa_punto" style="width:1%;">:</td>
                  <td class="tabla_informativa_descripcion" style="width:50%;">{{$prestamodesembolso->cliente_nombre}}</td>
                  <td class="tabla_informativa_subtitulo" style="width:13%;">CRÉDITO</td>
                  <td class="tabla_informativa_punto" style="width:1%;">:</td>
                  <td class="tabla_informativa_descripcion" style="width:19%;">{{ str_pad($prestamodesembolso->codigo, 8, "0", STR_PAD_LEFT) }}</td>
              </tr>
              <tr>
                  <td class="tabla_informativa_subtitulo">TELÉFONO</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{$prestamodesembolso->cliente_numerotelefono}}</td>
                  <td class="tabla_informativa_subtitulo">MONEDA</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{$prestamodesembolso->monedanombre}}</td>
              </tr>
              <tr>
                  <td class="tabla_informativa_subtitulo">DESEMBOLSO</td>
                  <td class="tabla_informativa_punto">:</td>
                  <td class="tabla_informativa_descripcion">{{$prestamodesembolso->monto}}</td>
                  <td class="tabla_informativa_subtitulo" style="color:#c42626;">MORA</td>
                  <td class="tabla_informativa_punto" style="color:#c42626;">:</td>
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
              <?php $cronograma_canceladas = collect($cronograma['cuotas_canceladas'])->sortBy('tabla_numero'); ?>
              <?php $montototal = 0; ?>
              <?php $codigocobranza = 0; ?>
              <?php $totalapagar = $prestamodesembolso->total_cuotafinaltotal; ?>
              @foreach ($cronograma_canceladas as $value)
              <?php
              $s_prestamo_cobranzadetalle = DB::table('s_prestamo_cobranzadetalle')
                  ->join('s_prestamo_cobranza','s_prestamo_cobranza.id','s_prestamo_cobranzadetalle.idprestamo_cobranza')
                  ->where('s_prestamo_cobranzadetalle.idprestamo_creditodetalle', $value['idprestamo_creditodetalle'])
                  ->where('s_prestamo_cobranza.idestadocobranza',2)
                  ->first();
              $cant_prestamo_cobranzadetalle = DB::table('s_prestamo_cobranzadetalle')
                  ->where('s_prestamo_cobranzadetalle.idprestamo_cobranza', $s_prestamo_cobranzadetalle->idprestamo_cobranza)
                  ->count();
              $cant_prestamo_cobranzadetalle_1 = DB::table('s_prestamo_cobranzadetalle')
                  ->join('s_prestamo_cobranza','s_prestamo_cobranza.id','s_prestamo_cobranzadetalle.idprestamo_cobranza')
                  ->where('s_prestamo_cobranzadetalle.idprestamo_creditodetalle', $value['idprestamo_creditodetalle'])
                  ->where('s_prestamo_cobranza.idestadocobranza',2)
                  ->get();
              ?>
              <tr>
                  <td style="text-align: center;">{{$value['tabla_numero']}}</td>
                  <td style="text-align: center;">{{$value['tabla_fechavencimiento']}}</td>
                  <td style="text-align: right;">{{number_format($totalapagar, 2, '.', '')}}</td>
                  <td style="text-align: right;">{{$value['tabla_cuota']}}</td>
                  <td style="text-align: center;">{{$value['tabla_moraapagar']>0?$value['tabla_moraapagar']:''}}</td>
                  @if($s_prestamo_cobranzadetalle->codigo!=$codigocobranza)
                  <td style="text-align: right;" rowspan="{{$cant_prestamo_cobranzadetalle}}">
                    @foreach($cant_prestamo_cobranzadetalle_1 as $valuedetalle)
                    <?php
                          $monto = 0;
                          if($valuedetalle->cronograma_idtipopago==1){
                              $monto = $valuedetalle->cronograma_totalredondeado;
                          }
                          elseif($valuedetalle->cronograma_idtipopago==2){
                              $monto = $valuedetalle->cronograma_pagado;
                          }
                    ?>
                    {{$monto}}<br>
                    <?php $montototal = $montototal+$monto; ?>
                    @endforeach
                  </td>
                  <td style="text-align: center;" rowspan="{{$cant_prestamo_cobranzadetalle}}">
                    @foreach($cant_prestamo_cobranzadetalle_1 as $valuedetalle)
                    {{date_format(date_create($valuedetalle->fecharegistro), "d/m/Y ")}}<br>
                    @endforeach
                  </td>
                  <?php $codigocobranza = $s_prestamo_cobranzadetalle->codigo; ?>
                  @endif
                  <td></td>
              </tr>
              <?php
              $totalapagar = number_format($totalapagar, 2, '.', '')-$value['tabla_cuota'];
              ?>
              @endforeach
              @foreach($cronograma['cuotas_pendientes'] as $value)
              <tr>
                  <td style="text-align: center;">{{$value['tabla_numero']}}</td>
                  <td style="text-align: center;">{{$value['tabla_fechavencimiento']}}</td>
                  <td style="text-align: right;">{{number_format($totalapagar, 2, '.', '')}}</td>
                  <td style="text-align: right;">{{$value['tabla_cuota']}}</td>
                  <td style="text-align: center;"></td>
                  <td></td>
                  <td></td>
                  <td></td>
              </tr>
              <?php
              $totalapagar = number_format($totalapagar, 2, '.', '')-$value['tabla_cuota'];
              ?>
              @endforeach
          </table>
          <div class="espacio"></div>
          <table class="tabla_informativa">
              <tr>
                  <td class="tabla_informativa_subtitulo" style="width:12%;">ASESOR</td>
                  <td class="tabla_informativa_punto" style="width:1%;">:</td>
                  <td class="tabla_informativa_descripcion" style="width:54%;">{{$prestamodesembolso->asesornombre}}</td>
                  <td class="tabla_informativa_subtitulo" style="width:15%;">TELÉFONO</td>
                  <td class="tabla_informativa_punto" style="width:1%;">:</td>
                  <td class="tabla_informativa_descripcion" style="width:17%;">{{$prestamodesembolso->asesor_numerotelefono}}</td>
              </tr>
          </table>
          <div class="espacio"></div>
          @if(configuracion($tienda->id,'prestamo_tarjetapago_mensajeadicionalticket_1')['resultado']=='CORRECTO')
          <div>
            <?php echo configuracion($tienda->id,'prestamo_tarjetapago_mensajeadicionalticket_1')['valor'] ?>
          </div>
          <div class="espacio"></div>
          @endif
          @if(configuracion($tienda->id,'prestamo_tarjetapago_mensajeadicionalticket_2')['resultado']=='CORRECTO')
          <div>
            <?php echo configuracion($tienda->id,'prestamo_tarjetapago_mensajeadicionalticket_2')['valor'] ?>
          </div>
          <div class="espacio"></div>
          @endif
          @if(configuracion($tienda->id,'prestamo_tarjetapago_mensajeadicionalticket_3')['resultado']=='CORRECTO')
          <div>
            <?php echo configuracion($tienda->id,'prestamo_tarjetapago_mensajeadicionalticket_3')['valor'] ?>
          </div>
          <div class="espacio"></div>
          @endif
          <div class="dato_adicional">
              TU MEJOR GARANTIA ES TU PUNTUALIDAD. EL NO PAGO PUNTUAL DE TU CRÉDITO TE GENERA MORA, QUE SE HARÁ EFECTO EN LOS SIGUIENTES PAGOS.
          </div>  
        </div>
    </div>
</body>
</html>