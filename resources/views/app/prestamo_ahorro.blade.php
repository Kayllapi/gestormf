<?php
          $prestamoahorrodetalle = DB::table('s_prestamo_ahorrodetalle')
                ->where('s_prestamo_ahorrodetalle.idprestamo_ahorro', $idprestamoahorro)
                ->orderBy('s_prestamo_ahorrodetalle.numero','asc')
                ->get();
          
            // domicilio 
            $prestamodomicilio = DB::table('s_prestamo_ahorrodomicilio')
                ->join('ubigeo', 'ubigeo.id', 's_prestamo_ahorrodomicilio.idubigeo')
                ->where('s_prestamo_ahorrodomicilio.idprestamo_ahorro',  $idprestamoahorro)
                ->select(
                    's_prestamo_ahorrodomicilio.*',
                    'ubigeo.nombre as nombre_ubigeo',
                    DB::raw('CONCAT(ubigeo.distrito, ", ", ubigeo.provincia, ", ", ubigeo.departamento) as ubigeoubicacion'),
                )
                ->first();
            $relaciones = DB::table('s_prestamo_ahorrosocio')
                ->join('s_prestamo_tiporelacion', 's_prestamo_tiporelacion.id', 's_prestamo_ahorrosocio.idprestamo_tiporelacion')
                ->where([
                    ['s_prestamo_ahorrosocio.idprestamo_ahorro', $idprestamoahorro],
                    ['s_prestamo_ahorrosocio.idtienda', $tienda->id],
                ])
                ->select(
                    's_prestamo_ahorrosocio.*',
                    's_prestamo_tiporelacion.nombre as nombre_tiporelacion'
                )
                ->orderBy('s_prestamo_ahorrosocio.id','asc')
                ->get();
          
            // laboral
            $prestamolaboral = DB::table('s_prestamo_ahorrolaboral')
                ->leftJoin('s_prestamo_giro', 's_prestamo_giro.id', 's_prestamo_ahorrolaboral.idprestamo_giro')
                ->leftJoin('s_prestamo_fuenteingreso', 's_prestamo_fuenteingreso.id', 's_prestamo_ahorrolaboral.idfuenteingreso')
                ->leftJoin('ubigeo', 'ubigeo.id', 's_prestamo_ahorrolaboral.idubigeo')
                ->where('s_prestamo_ahorrolaboral.idprestamo_ahorro', $idprestamoahorro)
                ->select(
                    's_prestamo_ahorrolaboral.*',
                    's_prestamo_giro.nombre as nombre_giro',
                    'ubigeo.nombre as nombre_ubigeo',
                    's_prestamo_fuenteingreso.nombre as fuenteingreso'
                )
                ->first();
?>
<div id="carga-credito">
    <div class="tabs-container" id="tab-credito-detalle-cliente">
        <ul class="tabs-menu">
          <li class="current"><a href="#tab-credito-detalle-cliente-0">Cronograma</a></li>
          <li><a href="#tab-credito-detalle-cliente-1">Domicilio</a></li>
          <li><a href="#tab-credito-detalle-cliente-2">Laboral</a></li>
          <li><a href="#tab-credito-detalle-cliente-6">Resultado</a></li>
        </ul>
        <div class="tab">
          <div id="tab-credito-detalle-cliente-0" class="tab-content" style="display: block;">
              <div class="row">
                  <div class="col-sm-6">
                      <div class="list-single-main-wrapper fl-wrap">
                          <div class="breadcrumbs gradient-bg fl-wrap">
                            <span>GENERAL</span>
                          </div>
                      </div>
                      <table class="table">
                          <tbody>
                              <tr>
                                  <td class="tabla_titulo">CLIENTE</td>
                                  <td class="tabla_texto">{{$prestamoahorro->cliente_nombre}}</td>
                              </tr>
                              @if($prestamoahorro->idconyuge!=0)
                              <tr>
                                  <td class="tabla_titulo">CONYUGUE</td>
                                  <td class="tabla_texto">{{$prestamoahorro->conyuge_nombre}}</td>
                              </tr>
                              @endif
                              @if($prestamoahorro->idbeneficiario!=0)
                              <tr>
                                  <td class="tabla_titulo">BENEFICIARIO</td>
                                  <td class="tabla_texto">{{$prestamoahorro->beneficiario_nombre}}</td>
                              </tr>
                              @endif
                          </tbody>
                      </table>
                      <div class="list-single-main-wrapper fl-wrap">
                          <div class="breadcrumbs gradient-bg fl-wrap">
                            <span>AHORRO</span>
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-md-6">
                              <table class="table">
                                  <tbody>
                                      <tr>
                                          <td class="tabla_titulo" style="width:50%;">TIPO DE AHORRO</td>
                                          <td class="tabla_texto">{{$prestamoahorro->tipoahorronombre}} {{$prestamoahorro->ahorrolibre_tiponombre!=''?'('.$prestamoahorro->ahorrolibre_tiponombre.')':''}}</td>
                                      </tr>
                                      @if($prestamoahorro->idprestamo_tipoahorro==1 or $prestamoahorro->idprestamo_tipoahorro==2)
                                      <tr>
                                          <td class="tabla_titulo">AHORRO</td>
                                          <td class="tabla_texto">{{$prestamoahorro->monto}}</td>
                                      </tr>
                                      @endif
                                      @if($prestamoahorro->idprestamo_tipoahorro==2)
                                      <tr>
                                          <td class="tabla_titulo">NÚMERO DE CUOTAS</td>
                                          <td class="tabla_texto">{{$prestamoahorro->numerocuota}} CUOTAS</td>
                                      </tr>
                                      @endif
                                      <tr>
                                          <td class="tabla_titulo">FECHA DE INICIO</td>
                                          <td class="tabla_texto">{{ date_format(date_create($prestamoahorro->fechainicio),"d/m/Y") }}</td>
                                      </tr>
                                      @if($prestamoahorro->idprestamo_tipoahorro==1 or $prestamoahorro->idprestamo_tipoahorro==2)
                                      <tr>
                                          <td class="tabla_titulo">FECHA DE RETIRO</td>
                                          <td class="tabla_texto">{{ date_format(date_create($prestamoahorro->fecharetiro),"d/m/Y") }}</td>
                                      </tr>
                                      @endif
                                      @if($prestamoahorro->idprestamo_tipoahorro==2)
                                      <tr>
                                          <td class="tabla_titulo">FRECUENCIA</td>
                                          <td class="tabla_texto">{{$prestamoahorro->frecuencia_nombre}}</td>
                                      </tr>
                                      @if($prestamoahorro->numerodias>0)
                                      <tr>
                                          <td class="tabla_titulo">NÚMERO DE DÍAS</td>
                                          <td class="tabla_texto">{{$prestamoahorro->numerodias}}</td>
                                      </tr>
                                      @endif
                                      @endif
                                  </tbody>
                              </table>
                          </div>
                          <div class="col-md-6">
                              @if($prestamoahorro->idprestamo_tipoahorro==3 && $prestamoahorro->ahorrolibre_producto!='')
                              <table class="table">
                                  <tbody>
                                      <tr>
                                          <td class="tabla_titulo" style="width:55%;">MONTO A AHORRAR</td>
                                          <td class="tabla_texto">{{$prestamoahorro->ahorrolibre_monto}}</td>
                                      </tr>
                                      <tr>
                                          <td class="tabla_titulo">PRODUCTO A AHORRAR</td>
                                          <td class="tabla_texto">{{$prestamoahorro->ahorrolibre_producto}}</td>
                                      </tr>
                                  </tbody>
                              </table>
                              @endif
                              @if($prestamoahorro->idprestamo_tipoahorro==1 or $prestamoahorro->idprestamo_tipoahorro==2)
                              <table class="table">
                                  <tbody>
                                      <tr>
                                          <td class="tabla_titulo" style="width:50%;">TIPO DE INTERES</td>
                                          <td class="tabla_texto">
                                            <?php $idtipointeres = configuracion($tienda->id,'prestamo_tasapordefecto')['valor']!=''?configuracion($tienda->id,'prestamo_tasapordefecto')['valor']:1 ?>
                                            @if($idtipointeres==1)
                                                INTERES FIJA
                                            @elseif($idtipointeres==2)
                                                INTERES EFECTIVA
                                            @endif
                                          </td>
                                      </tr>
                                      <tr>
                                          <td class="tabla_titulo">INTERES GANADO %</td>
                                          <td class="tabla_texto">{{$prestamoahorro->tasa}}</td>
                                      </tr>
                                      <tr>
                                          <td class="tabla_titulo">INTERES GANADO TOTAL</td>
                                          <td class="tabla_texto">{{$prestamoahorro->total_interesganado}}</td>
                                      </tr>
                                      <tr>
                                          <td class="tabla_titulo">TOTAL A RETIRAR</td>
                                          <td class="tabla_texto">{{$prestamoahorro->total_total}}</td>
                                      </tr>
                                  </tbody>
                              </table>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="col-sm-6">
                      @if($prestamoahorro->idprestamo_tipoahorro==1 or $prestamoahorro->idprestamo_tipoahorro==2)
                      <div class="list-single-main-wrapper fl-wrap">
                          <div class="breadcrumbs gradient-bg fl-wrap">
                            <span>CRONOGRAMA</span>
                          </div>
                      </div>
                      <table class="table">
                          <thead class="thead-dark">
                          <tr>
                              <th style="text-align:center;">Nº</th>
                              @if($prestamoahorro->idprestamo_tipoahorro==1)
                              <th style="text-align:center;">F. DE GANANCIA</th>
                              @elseif($prestamoahorro->idprestamo_tipoahorro==2)
                              <th style="text-align:center;">F. DE RECAUDACIÓN</th>
                              @endif
                              <th style="text-align:center;">CUOTA</th>
                              <th style="text-align:center;">INTERÉS</th>
                              <th style="text-align:center;">TOTAL</th>
                          </tr>
                          </thead>
                          <tbody>
                          @foreach($prestamoahorrodetalle as $value)
                          <tr>
                              <td class="tabla_texto" style="text-align:center;">{{ str_pad($value->numero, 2, "0", STR_PAD_LEFT) }}</td>
                              <td class="tabla_texto" style="text-align:center;">{{ date_format(date_create($value->fechaahorro),"d/m/Y") }}</td>
                              <td class="tabla_texto" style="text-align:right;">{{ $value->cuota }}</td>
                              <td class="tabla_texto" style="text-align:right;">{{ $value->interesganado }}</td>
                              <td class="tabla_texto" style="text-align:right;">{{ $value->total }}</td>
                          </tr>
                          @endforeach
                          <tr style="background-color: #353a3f;color: #ffffff;">
                              <td colspan="2" style="text-align:right;padding-bottom: 10px;padding-top: 10px;">TOTAL</td>
                              <td style="text-align:right;">{{$prestamoahorro->total_cuota}}</td>
                              <td style="text-align:right;">{{$prestamoahorro->total_interesganado}}</td>
                              <td style="text-align:right;">{{$prestamoahorro->total_total}}</td>
                          </tr>
                          <tbody>
                      </table>
                      @endif
                  </div>
              </div>
          </div>
          <div id="tab-credito-detalle-cliente-1" class="tab-content" style="display: none;">
              <div class="row">
                  <div class="col-sm-6">
                      <div class="list-single-main-wrapper fl-wrap">
                          <div class="breadcrumbs gradient-bg fl-wrap">
                            <span>GENERAL</span>
                          </div>
                      </div>
                      <table class="table">
                          <tbody>
                              <tr>
                                  <td class="tabla_titulo">RESIDE DESDE (MES/AÑO)</td>
                                  <td class="tabla_texto">
                                    @if($prestamodomicilio!='')
                                    @if($prestamodomicilio->reside_desdemes!=0)
                                    @if($prestamodomicilio->reside_desdemes==1)
                                      ENERO, 
                                    @elseif($prestamodomicilio->reside_desdemes==2) 
                                      FEBRERO, 
                                    @elseif($prestamodomicilio->reside_desdemes==3)
                                      MARZO, 
                                    @elseif($prestamodomicilio->reside_desdemes==4) 
                                      ABRIL, 
                                    @elseif($prestamodomicilio->reside_desdemes==5)
                                      MAYO, 
                                    @elseif($prestamodomicilio->reside_desdemes==6)
                                      JUNIO, 
                                    @elseif($prestamodomicilio->reside_desdemes==7)
                                      JULIO, 
                                    @elseif($prestamodomicilio->reside_desdemes==8)
                                      AGOSTO, 
                                    @elseif($prestamodomicilio->reside_desdemes==9)
                                      SEPTIEMBRE, 
                                    @elseif($prestamodomicilio->reside_desdemes==10)
                                      OCTUBRE, 
                                    @elseif($prestamodomicilio->reside_desdemes==11)
                                      NOVIEMBRE, 
                                    @elseif($prestamodomicilio->reside_desdemes==12)
                                      DICIEMBRE, 
                                    @endif
                                    {{$prestamodomicilio->reside_desdeanio}}
                                    @endif
                                    @endif
                                  </td>
                              </tr>
                              <tr>
                                  <td class="tabla_titulo">HORARIO DE UBICACIÓN</td>
                                  <td class="tabla_texto">{{$prestamodomicilio!=''?date_format(date_create($prestamodomicilio->horaubicacion_de), "h:i A"):'00:00'}} - {{$prestamodomicilio!=''?date_format(date_create($prestamodomicilio->horaubicacion_hasta), "h:i A"):'00:00'}}</td>
                              </tr>
                              <tr>
                                  <td class="tabla_titulo">TIPO DE PROPIEDAD</td>
                                  <td class="tabla_texto">
                                    @if($prestamodomicilio!='')
                                    @if($prestamodomicilio->idtipopropiedad==1)
                                      ALQUILADO 
                                    @elseif($prestamodomicilio->idtipopropiedad==2) 
                                      FAMILIAR 
                                    @elseif($prestamodomicilio->idtipopropiedad==3)
                                      PROPIO 
                                    @endif
                                    @endif
                                  </td>
                              </tr>
                              <tr>
                                  <td class="tabla_titulo">PAGO DE SERVICIOS</td>
                                  <td class="tabla_texto">
                                    @if($prestamodomicilio!='')
                                    @if($prestamodomicilio->iddeudapagoservicio==1)
                                      CON CORTE 
                                    @elseif($prestamodomicilio->iddeudapagoservicio==2) 
                                      PAGO PUNTUAL 
                                    @elseif($prestamodomicilio->iddeudapagoservicio==3)
                                      DEUDA X 1 MES 
                                    @elseif($prestamodomicilio->iddeudapagoservicio==4)
                                      DEUDA X 2 MESES 
                                    @elseif($prestamodomicilio->iddeudapagoservicio==5)
                                      DEUDA X 3 MESES 
                                    @elseif($prestamodomicilio->iddeudapagoservicio==6)
                                      DEUDA X 4 MESES 
                                    @elseif($prestamodomicilio->iddeudapagoservicio==7)
                                      DEUDA X 5 MESES 
                                    @elseif($prestamodomicilio->iddeudapagoservicio==8)
                                      DEUDA X 6 MESES 
                                    @endif
                                    @endif
                                  </td>
                              </tr>
                          </tbody>
                      </table>
                      <div class="list-single-main-wrapper fl-wrap">
                          <div class="breadcrumbs gradient-bg fl-wrap">
                            <span>SOCIOS</span>
                          </div>
                      </div>
                      <div class="table-responsive">
                          <table class="table">
                              <thead>
                                <tr>
                                  <td class="tabla_titulo">PERSONA</td>
                                  <td class="tabla_titulo">TIPO DE RELACIÓN</td>
                                  <td class="tabla_titulo">TELÉFONO</td>
                                  <td class="tabla_titulo">COMENTARIO</td>
                                </tr>
                              </thead>
                              <tbody num="0">
                                  @foreach($relaciones as $value)
                                  <tr>
                                    <th class="tabla_texto">{{$value->personanombre}}</th>
                                    <th class="tabla_texto">{{$value->nombre_tiporelacion}}</th>
                                    <th class="tabla_texto">{{$value->numerotelefono}}</th>
                                    <th class="tabla_texto">{{$value->comentario}}</th>
                                  </tr>
                                  @endforeach
                              </tbody>
                          </table>
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="list-single-main-wrapper fl-wrap">
                          <div class="breadcrumbs gradient-bg fl-wrap">
                            <span>UBICACIÓN</span>
                          </div>
                      </div>
                      <table class="table">
                          <tbody>
                              <tr>
                                  <td class="tabla_titulo" style="width:40%;">UBIGEO</td>
                                  <td class="tabla_texto">{{$prestamodomicilio!=''?$prestamodomicilio->nombre_ubigeo:$prestamoahorro->clienteubigeonombre}}</td>
                              </tr>
                              <tr>
                                  <td class="tabla_titulo">DIRECCIÓN</td>
                                  <td class="tabla_texto">{{$prestamodomicilio!=''?$prestamodomicilio->direccion:$prestamoahorro->clientedireccion}}</td>
                              </tr>
                              <tr>
                                  <td class="tabla_titulo">REFERENCIA</td>
                                  <td class="tabla_texto">{{$prestamodomicilio!=''?$prestamodomicilio->referencia:$prestamoahorro->clientereferencia}}</td>
                              </tr>
                          </tbody>
                      </table>
                    @if($prestamodomicilio!='')
                      @if($prestamodomicilio->mapa_latitud!='' && $prestamodomicilio->mapa_longitud!='')
                      <div class="imagen_croquis" style="height:273px;background-image: url(https://maps.googleapis.com/maps/api/staticmap?center={{$prestamodomicilio->mapa_latitud}},{{$prestamodomicilio->mapa_longitud}}&zoom=16&size=640x353&markers=icon:{{url('public/backoffice/sistema/marker.png')}}|{{$prestamodomicilio->mapa_latitud}},{{$prestamodomicilio->mapa_longitud}}&key=AIzaSyAyFtw-yEfh6GtiPyx_4ZQWt3g_vUCu5eQ);"></div>
                      @endif
                    @endif
                  </div>
              </div>
  
                      
          </div>
          <div id="tab-credito-detalle-cliente-2" class="tab-content" style="display: none;">
              <div class="row">
                  <div class="col-sm-6">
                      <div class="list-single-main-wrapper fl-wrap">
                          <div class="breadcrumbs gradient-bg fl-wrap">
                            <span>GENERAL</span>
                          </div>
                      </div>
                      <table class="table">
                          <tbody>
                              <tr>
                                  <td class="tabla_titulo" style="width:40%;">FUENTE DE INGRESO</td>
                                  <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->fuenteingreso:''}}</td>
                              </tr>
                              <tr>
                                  <td class="tabla_titulo">GIRO</td>
                                  <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->nombre_giro:''}}</td>
                              </tr>
                              <tr>
                                  <td class="tabla_titulo">ACTIVIDAD</td>
                                  <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->actividad:''}}</td>
                              </tr>
                              <tr>
                                  <td class="tabla_titulo">NOMBRE DE NEGOCIO</td>
                                  <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->nombrenegocio:''}}</td>
                              </tr>
                              <tr>
                                  <td class="tabla_titulo">RESIDE DESDE (AÑO/MES)</td>
                                  <td class="tabla_texto">
                                    @if($prestamolaboral!='')
                                    @if($prestamolaboral->labora_desdemes!=0)
                                    @if($prestamolaboral->labora_desdemes==1)
                                      ENERO, 
                                    @elseif($prestamolaboral->labora_desdemes==2) 
                                      FEBRERO, 
                                    @elseif($prestamolaboral->labora_desdemes==3)
                                      MARZO, 
                                    @elseif($prestamolaboral->labora_desdemes==4) 
                                      ABRIL, 
                                    @elseif($prestamolaboral->labora_desdemes==5)
                                      MAYO, 
                                    @elseif($prestamolaboral->labora_desdemes==6)
                                      JUNIO, 
                                    @elseif($prestamolaboral->labora_desdemes==7)
                                      JULIO 
                                    @elseif($prestamolaboral->labora_desdemes==8)
                                      AGOSTO, 
                                    @elseif($prestamolaboral->labora_desdemes==9)
                                      SEPTIEMBRE, 
                                    @elseif($prestamolaboral->labora_desdemes==10)
                                      OCTUBRE, 
                                    @elseif($prestamolaboral->labora_desdemes==11)
                                      NOVIEMBRE, 
                                    @elseif($prestamolaboral->labora_desdemes==12)
                                      DICIEMBRE, 
                                    @endif
                                    {{$prestamolaboral->labora_desdeanio}}
                                    @endif
                                    @endif
                                  </td>
                              </tr>
                          </tbody>
                      </table>
                      <div class="list-single-main-wrapper fl-wrap">
                          <div class="breadcrumbs gradient-bg fl-wrap">
                            <span>DÍAS LABORABLES</span>
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-md-6">
                              <table class="table">
                                  <tbody>
                                      <tr>
                                          <td class="tabla_titulo" style="width:50%;">LUNES</td>
                                          <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->labora_lunes:''}}</td>
                                      </tr>
                                      <tr>
                                          <td class="tabla_titulo">MARTES</td>
                                          <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->labora_martes:''}}</td>
                                      </tr>
                                      <tr>
                                          <td class="tabla_titulo">MIERCOLES</td>
                                          <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->labora_miercoles:''}}</td>
                                      </tr>
                                      <tr>
                                          <td class="tabla_titulo">JUEVES</td>
                                          <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->labora_jueves:''}}</td>
                                      </tr>
                                  </tbody>
                              </table>
                          </div>
                          <div class="col-md-6">
                              <table class="table">
                                  <tbody>
                                      <tr>
                                          <td class="tabla_titulo" style="width:50%;">VIERNES</td>
                                          <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->labora_viernes:''}}</td>
                                      </tr>
                                      <tr>
                                          <td class="tabla_titulo">SABADOS</td>
                                          <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->labora_sabados:''}}</td>
                                      </tr>
                                      <tr>
                                          <td class="tabla_titulo">DOMINGOS</td>
                                          <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->labora_domingos:''}}</td>
                                      </tr>
                                  </tbody>
                              </table>
                          </div>
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="list-single-main-wrapper fl-wrap">
                          <div class="breadcrumbs gradient-bg fl-wrap">
                            <span>UBICACIÓN</span>
                          </div>
                      </div>
                      <table class="table">
                          <tbody>
                              <tr>
                                  <td class="tabla_titulo">UBIGEO</td>
                                  <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->nombre_ubigeo:$prestamoahorro->clienteubigeonombre}}</td>
                              </tr>
                              <tr>
                                  <td class="tabla_titulo">DIRECCIÓN</td>
                                  <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->direccion:$prestamoahorro->clientedireccion}}</td>
                              </tr>
                              <tr>
                                  <td class="tabla_titulo">REFERENCIA</td>
                                  <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->referencia:$prestamoahorro->clientereferencia}}</td>
                              </tr>
                          </tbody>
                      </table>
                      @if($prestamolaboral!='')
                        @if($prestamolaboral->mapa_latitud!='' && $prestamolaboral->mapa_longitud!='')
                        <div class="imagen_croquis" style="height:285px;background-image: url(https://maps.googleapis.com/maps/api/staticmap?center={{$prestamolaboral->mapa_latitud}},{{$prestamolaboral->mapa_longitud}}&zoom=16&size=640x353&markers=icon:{{url('public/backoffice/sistema/marker.png')}}|{{$prestamolaboral->mapa_latitud}},{{$prestamolaboral->mapa_longitud}}&key=AIzaSyAyFtw-yEfh6GtiPyx_4ZQWt3g_vUCu5eQ);"></div>
                        @endif
                      @endif
                  </div>
              </div>
          </div>
          <div id="tab-credito-detalle-cliente-6" class="tab-content" style="display: none;">
            <div id="cont-resultado"></div>
          </div>
        </div>
      </div>  
</div>
<style>
  .imagen_croquis {
      border: 1px solid #bdbdbd;
      border-radius: 5px;
      background-repeat: no-repeat;
      background-color: #bdbdbd;
      background-position: center;
      margin-bottom: 5px;
  }
  .tabla_titulo {
      padding-top: 10px !important;
      padding-bottom: 10px !important;    
      background-color: #bdbdbd !important;
  }
  .tabla_texto {   
      padding-top: 10px !important;
      padding-bottom: 10px !important;    
      background-color: #e6e6e6 !important;
  }
  .resultado-aprobado {
    background-color: #179a4f;
    padding: 5px;
    border-radius: 5px;
    color: rgb(255 255 255);
    font-weight: bold;
    font-size: 20px;
    margin-bottom: 5px;
    float: left;
    width: 100%;
  }
  .resultado-desaprobado {
    background-color: #8c1329;
    padding: 5px;
    border-radius: 5px;
    color: rgb(255 255 255);
    font-weight: bold;
    font-size: 20px;
    margin-bottom: 5px;
    float: left;
    width: 100%;
  }
</style>     

<!-- Tabulador de pestañas -->
<script>
  tab({click:'#tab-credito-detalle-cliente'});
  tab({click:'#tab-credito-detalle-cliente-domicilio'});
    resultado_index();
    function resultado_index(){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorrosolicitud/{{ $prestamoahorro->id }}/edit?view=resultado',result:'#cont-resultado'});
    }
</script>    