<?php
          $prestamocreditodetalle = DB::table('s_prestamo_creditodetalle')
                ->where('s_prestamo_creditodetalle.idprestamo_credito', $idprestamocredito)
                ->orderBy('s_prestamo_creditodetalle.numero','asc')
                ->get();
          
            // domicilio 
            $prestamodomicilio = DB::table('s_prestamo_creditodomicilio')
                ->leftJoin('ubigeo', 'ubigeo.id', 's_prestamo_creditodomicilio.idubigeo')
                ->where('s_prestamo_creditodomicilio.idprestamo_credito',  $idprestamocredito)
                ->select(
                    's_prestamo_creditodomicilio.*',
                    'ubigeo.nombre as nombre_ubigeo',
                    DB::raw('CONCAT(ubigeo.distrito, ", ", ubigeo.provincia, ", ", ubigeo.departamento) as ubigeoubicacion'),
                )
                ->first();
            $relaciones = DB::table('s_prestamo_creditorelacion')
                ->leftJoin('s_prestamo_tiporelacion', 's_prestamo_tiporelacion.id', 's_prestamo_creditorelacion.idprestamo_tiporelacion')
                ->where([
                    ['s_prestamo_creditorelacion.idprestamo_credito', $idprestamocredito],
                    //['s_prestamo_creditorelacion.idtienda', $tienda->id],
                ])
                ->select(
                    's_prestamo_creditorelacion.*',
                    's_prestamo_tiporelacion.nombre as nombre_tiporelacion'
                )
                ->orderBy('s_prestamo_creditorelacion.id','asc')
                ->get();
            $prestamodomicilioimagen = DB::table('s_prestamo_creditodomicilioimagen')
              ->where('s_prestamo_creditodomicilioimagen.idprestamo_credito', $idprestamocredito)
              ->get();
          
            // laboral
            $prestamolaboral = DB::table('s_prestamo_creditolaboral')
                ->leftJoin('s_prestamo_giro', 's_prestamo_giro.id', 's_prestamo_creditolaboral.idprestamo_giro')
                ->leftJoin('s_prestamo_fuenteingreso', 's_prestamo_fuenteingreso.id', 's_prestamo_creditolaboral.idfuenteingreso')
                ->leftJoin('ubigeo', 'ubigeo.id', 's_prestamo_creditolaboral.idubigeo')
                ->where('s_prestamo_creditolaboral.idprestamo_credito', $idprestamocredito)
                ->select(
                    's_prestamo_creditolaboral.*',
                    's_prestamo_giro.nombre as nombre_giro',
                    's_prestamo_fuenteingreso.nombre as fuenteingreso',
                    'ubigeo.nombre as nombre_ubigeo',
                )
                ->first();
          
            $idprestamolavoral = 0;
            if($prestamolaboral!=''){
                $idprestamolavoral = $prestamolaboral->id;
            }
          
            $fuenteingreso = DB::table('s_prestamo_fuenteingreso')->get();
            $giro = DB::table('s_prestamo_giro')->get();

            $laboralventa = DB::table('s_prestamo_creditolaboralventa')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->orderBy('id','asc')->get();
            $laboralcompra = DB::table('s_prestamo_creditolaboralcompra')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->orderBy('id','asc')->get();
            $laboralingreso = DB::table('s_prestamo_creditolaboralingreso')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->limit(1)->first();
            $laboralegresogasto = DB::table('s_prestamo_creditolaboralegresogasto')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->orderBy('id','asc')->get();
            $laboralegresogastofamiliares = DB::table('s_prestamo_creditolaboralegresogastofamiliar')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->orderBy('id','asc')->get();
            $laboralegresopago = DB::table('s_prestamo_creditolaboralegresopago')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->orderBy('id','asc')->get();
            $laboralotroingreso = DB::table('s_prestamo_creditolaboralotroingreso')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->orderBy('id','asc')->get();
            $laboralotrogasto = DB::table('s_prestamo_creditolaboralotrogasto')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->orderBy('id','asc')->get();
            $laboralservicio = DB::table('s_prestamo_creditolaboralservicio')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->limit(1)->first();

            $prestamolaboralnegocioimagen = DB::table('s_prestamo_creditolaboralnegocioimagen')
              ->where('s_prestamo_creditolaboralnegocioimagen.idprestamo_credito', $idprestamocredito)
              ->get();

            $prestamolaborallicenciafuncionamientoimagen = DB::table('s_prestamo_creditolaborallicenciafuncionamientoimagen')
              ->where('s_prestamo_creditolaborallicenciafuncionamientoimagen.idprestamo_credito', $idprestamocredito)
              ->get();
            $prestamolaboralcontratoalquilerimagen = DB::table('s_prestamo_creditolaboralcontratoalquilerimagen')
              ->where('s_prestamo_creditolaboralcontratoalquilerimagen.idprestamo_credito', $idprestamocredito)
              ->get();
            $prestamolaboralficharucimagen = DB::table('s_prestamo_creditolaboralficharucimagen')
              ->where('s_prestamo_creditolaboralficharucimagen.idprestamo_credito', $idprestamocredito)
              ->get();
            $prestamolaboralreciboaguaimagen = DB::table('s_prestamo_creditolaboralreciboaguaimagen')
              ->where('s_prestamo_creditolaboralreciboaguaimagen.idprestamo_credito', $idprestamocredito)
              ->get();
            $prestamolaboralreciboluzimagen = DB::table('s_prestamo_creditolaboralreciboluzimagen')
              ->where('s_prestamo_creditolaboralreciboluzimagen.idprestamo_credito', $idprestamocredito)
              ->get();
            $prestamolaboralboletacompraimagen = DB::table('s_prestamo_creditolaboralboletacompraimagen')
              ->where('s_prestamo_creditolaboralboletacompraimagen.idprestamo_credito', $idprestamocredito)
              ->get();

          // GARANTIA
          $bienes = DB::table('s_prestamo_creditobien')
                ->where([
                    ['s_prestamo_creditobien.idprestamo_credito', $idprestamocredito],
                    ['s_prestamo_creditobien.idtienda', $tienda->id],
                    ['s_prestamo_creditobien.idestado', 1]
                ])
                ->orderBy('s_prestamo_creditobien.id','desc')
                ->get();

          // SUSTENTO
          
          $sustento = DB::table('s_prestamo_creditosustento')
                ->leftJoin('s_prestamo_calificacion', 's_prestamo_calificacion.id', 's_prestamo_creditosustento.idprestamo_calificacion')
                ->where('s_prestamo_creditosustento.idprestamo_credito', $idprestamocredito)
                ->select(
                    's_prestamo_creditosustento.*',
                    's_prestamo_calificacion.nombre as calificacionnombre',
                )
                ->first();
?>
<div id="carga-credito">
    <div class="tabs-container" id="tab-credito-detalle-cliente">
        <ul class="tabs-menu">
          <li class="current"><a href="#tab-credito-detalle-cliente-0">Cronograma</a></li>
          <li><a href="#tab-credito-detalle-cliente-1">Domicilio</a></li>
          <li><a href="#tab-credito-detalle-cliente-2">Ingresos</a></li>
          <li><a href="#tab-credito-detalle-cliente-3">Garantias</a></li>
          <li><a href="#tab-credito-detalle-cliente-5">Sustento</a></li>
          <li><a href="#tab-credito-detalle-cliente-6">Resultado</a></li>
        </ul>
        <div class="tab">
          <div id="tab-credito-detalle-cliente-0" class="tab-content" style="display: block;">
              <div class="row">
                  <div class="col-sm-6">
                      <div class="list-single-main-wrapper fl-wrap">
                          <div class="breadcrumbs gradient-bg fl-wrap">
                            <span>CLIENTE</span>
                          </div>
                      </div>
                      <table class="table">
                          <tbody>
                              <tr>
                                  <td class="tabla_titulo">Cliente</td>
                                  <td class="tabla_texto">{{$prestamocredito->cliente_nombre}}</td>
                              </tr>
                              @if($prestamocredito->idconyuge!=0)
                              <tr>
                                  <td class="tabla_titulo">Conyugue</td>
                                  <td class="tabla_texto">{{$prestamocredito->conyuge_nombre}}</td>
                              </tr>
                              @endif
                              @if($prestamocredito->idgarante!=0)
                              <tr>
                                  <td class="tabla_titulo">Garante ó Aval</td>
                                  <td class="tabla_texto">{{$prestamocredito->garante_nombre}}</td>
                              </tr>
                              @endif
                          </tbody>
                      </table>
                      <div class="list-single-main-wrapper fl-wrap">
                          <div class="breadcrumbs gradient-bg fl-wrap">
                            <span>CRÈDITO</span>
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-md-6">
                              <table class="table">
                                  <tbody>
                                      <tr>
                                          <td class="tabla_titulo">Tipo de Crédito</td>
                                          <td class="tabla_texto">{{$prestamocredito->tipocredito}}</td>
                                      </tr>
                                      <tr>
                                          <td class="tabla_titulo">Monto</td>
                                          <td class="tabla_texto">{{$prestamocredito->monto}}</td>
                                      </tr>
                                      <tr>
                                          <td class="tabla_titulo">Número de Cuotas</td>
                                          <td class="tabla_texto">{{$prestamocredito->numerocuota}} CUOTAS</td>
                                      </tr>
                                      <tr>
                                          <td class="tabla_titulo">Fecha de Inicio</td>
                                          <td class="tabla_texto">{{date_format(date_create($prestamocredito->fechainiciocero),"d/m/Y")}}</td>
                                      </tr>
                                      <tr>
                                          <td class="tabla_titulo">Frecuencia</td>
                                          <td class="tabla_texto">{{$prestamocredito->frecuencia_nombre}}</td>
                                      </tr>
                                      @if($prestamocredito->numerodias>0)
                                      <tr>
                                          <td class="tabla_titulo">Número de Días</td>
                                          <td class="tabla_texto">{{$prestamocredito->numerodias}}</td>
                                      </tr>
                                      @endif
                                  </tbody>
                              </table>
                          </div>
                          <div class="col-md-6">
                              <table class="table">
                                  <tbody>
                                      <tr>
                                          <td class="tabla_titulo">Tipo de Interes</td>
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
                                          <td class="tabla_titulo">Interes %</td>
                                          <td class="tabla_texto">{{$prestamocredito->tasa}}</td>
                                      </tr>
                                      @if($prestamocredito->total_abono>0)
                                      <tr>
                                          <td class="tabla_titulo">Abono</td>
                                          <td class="tabla_texto">{{$prestamocredito->total_abono}}</td>
                                      </tr>
                                      @endif
                                      <tr>
                                          <td class="tabla_titulo">Interes Total</td>
                                          <td class="tabla_texto">{{$prestamocredito->total_interes}}</td>
                                      </tr>
                                      @if($prestamocredito->total_segurodesgravamen>0)
                                      <tr>
                                          <td class="tabla_titulo">Seguro Desgravamen</td>
                                          <td class="tabla_texto">{{$prestamocredito->total_segurodesgravamen}}</td>
                                      </tr>
                                      @endif
                                      <tr>
                                          <td class="tabla_titulo">Total a Pagar</td>
                                          <td class="tabla_texto">{{$prestamocredito->total_cuotafinaltotal}}</td>
                                      </tr>
                                  </tbody>
                              </table>
                          </div>
                      </div>
                  </div>
                  <div class="col-sm-6">
                      <div class="list-single-main-wrapper fl-wrap">
                          <div class="breadcrumbs gradient-bg fl-wrap">
                            <span>CRONOGRAMA</span>
                          </div>
                      </div>
                      <table class="table">
                          <thead>
                          <tr>
                              <th class="tabla_titulo" style="text-align:center;">Nº</th>
                              <th class="tabla_titulo" style="text-align:center;">F.VENCIMIENTO</th>
                              <th class="tabla_titulo" style="text-align:center;">CAPITAL</th>
                              <th class="tabla_titulo" style="text-align:center;">INTERÉS</th>
                              @if($prestamocredito->total_segurodesgravamen>0)
                              <th class="tabla_titulo" style="text-align:center;">SEGURO DESGRAVAMEN</th>
                              @endif
                              @if($prestamocredito->total_gastoadministrativo>0)
                              <th class="tabla_titulo" style="text-align:center;">GASTO ADMINISTRATIVO</th>
                              @endif
                              @if($prestamocredito->total_acumulado>0)
                              <th class="tabla_titulo" style="text-align:center;">ACUMULADO</th>
                              @endif
                              <th class="tabla_titulo" style="text-align:center;">CUOTA</th>
                              @if($prestamocredito->total_abono>0)
                              <th class="tabla_titulo" style="text-align:center;">ABONO</th>
                              <th class="tabla_titulo" style="text-align:center;">TOTAL</th>
                              @endif
                          </tr>
                          </thead>
                          <tbody>
                          @foreach($prestamocreditodetalle as $value)
                          <tr>
                              <td class="tabla_texto" style="text-align:center;">{{ str_pad($value->numero, 2, "0", STR_PAD_LEFT) }}</td>
                              <td class="tabla_texto" style="text-align:center;">{{ date_format(date_create($value->fechavencimiento),"d/m/Y") }}</td>
                              <td class="tabla_texto" style="text-align:right;">{{ $value->amortizacion }}</td>
                              <td class="tabla_texto" style="text-align:right;">{{ $value->interes }}</td>
                              @if($prestamocredito->total_segurodesgravamen>0)
                              <td class="tabla_texto" style="text-align:right;">{{ $value->seguro }}</td>
                              @endif
                              @if($prestamocredito->total_gastoadministrativo>0)
                              <td class="tabla_texto" style="text-align:right;">{{ $value->gastoadministrativo }}</td>
                              @endif
                              @if($prestamocredito->total_acumulado>0)
                              <td class="tabla_texto" style="text-align:right;">{{ $value->cuotanormal }} ({{ $value->acumulado }})</td>
                              @endif
                              <td class="tabla_texto" style="text-align:right;">{{ $value->total }}</td>
                              @if($prestamocredito->total_abono>0)
                              <td class="tabla_texto" style="text-align:right;">{{ $value->abono }}</td>
                              <td class="tabla_texto" style="text-align:right;">{{ $value->totalfinal }}</td>
                              @endif
                          </tr>
                          @endforeach
                          <tr>
                              <td class="tabla_titulo" colspan="2" style="text-align:right;padding-bottom: 10px;padding-top: 10px;">TOTAL</td>
                              <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->total_amortizacion}}</td>
                              <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->total_interes}}</td>
                              @if($prestamocredito->total_segurodesgravamen>0)
                              <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->total_segurodesgravamen}}</td>
                              @endif
                              @if($prestamocredito->total_gastoadministrativo>0)
                              <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->total_gastoadministrativo}}</td>
                              @endif
                              @if($prestamocredito->total_acumulado>0)
                              <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->total_cuotanormal}} ({{$prestamocredito->total_acumulado}})</td>
                              @endif
                              <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->total_cuotafinal}}</td>
                              @if($prestamocredito->total_abono>0)
                              <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->total_abono}}</td>
                              <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->total_cuotafinaltotal}}</td>
                              @endif
                          </tr>
                          <tbody>
                      </table>
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
                                      <td class="tabla_titulo" style="width:50%">Reside Desde (mes/año)</td>
                                      <td class="tabla_texto">
                                        @if($prestamodomicilio!='')
                                        @if($prestamodomicilio->reside_desdemes!=0)
                                        @if($prestamodomicilio->reside_desdemes==1)
                                          ENERO 
                                        @elseif($prestamodomicilio->reside_desdemes==2) 
                                          FEBRERO 
                                        @elseif($prestamodomicilio->reside_desdemes==3)
                                          MARZO 
                                        @elseif($prestamodomicilio->reside_desdemes==4) 
                                          ABRIL 
                                        @elseif($prestamodomicilio->reside_desdemes==5)
                                          MAYO 
                                        @elseif($prestamodomicilio->reside_desdemes==6)
                                          JUNIO 
                                        @elseif($prestamodomicilio->reside_desdemes==7)
                                          JULIO 
                                        @elseif($prestamodomicilio->reside_desdemes==8)
                                          AGOSTO 
                                        @elseif($prestamodomicilio->reside_desdemes==9)
                                          SEPTIEMBRE 
                                        @elseif($prestamodomicilio->reside_desdemes==10)
                                          OCTUBRE 
                                        @elseif($prestamodomicilio->reside_desdemes==11)
                                          NOVIEMBRE 
                                        @elseif($prestamodomicilio->reside_desdemes==12)
                                          DICIEMBRE 
                                        @endif
                                        {{$prestamodomicilio->reside_desdeanio}}
                                        @endif
                                        @endif
                                      </td>
                                  </tr>
                                  <tr>
                                      <td class="tabla_titulo">Hora Ubicación (Desde - Hasta)</td>
                                      <td class="tabla_texto">{{$prestamodomicilio!=''?$prestamodomicilio->horaubicacion_de:'00:00'}} - {{$prestamodomicilio!=''?$prestamodomicilio->horaubicacion_hasta:'00:00'}}</td>
                                  </tr>
                                  <tr>
                                      <td class="tabla_titulo">Tipo de Propiedad</td>
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
                                      <td class="tabla_titulo">Pago de Servicios</td>
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
                                  <span>UBICACIÒN</span>
                                </div>
                            </div>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="tabla_titulo">Ubigeo</td>
                                        <td class="tabla_texto">{{$prestamodomicilio!=''?$prestamodomicilio->nombre_ubigeo:$prestamocredito->clienteubigeonombre}}</td>
                                    </tr>
                                    <tr>
                                        <td class="tabla_titulo">Dirección</td>
                                        <td class="tabla_texto">{{$prestamodomicilio!=''?$prestamodomicilio->direccion:$prestamocredito->clientedireccion}}</td>
                                    </tr>
                                    <tr>
                                        <td class="tabla_titulo">Referencia</td>
                                        <td class="tabla_texto">{{$prestamodomicilio!=''?$prestamodomicilio->referencia:$prestamocredito->clientereferencia}}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="cont-imagen_croquis">
                                  @if($prestamodomicilio!='')
                                    @if($prestamodomicilio->mapa_latitud!='' && $prestamodomicilio->mapa_longitud!='')
                                    <div class="imagen_croquis" style="background-image: url(https://maps.googleapis.com/maps/api/staticmap?center={{$prestamodomicilio->mapa_latitud}},{{$prestamodomicilio->mapa_longitud}}&zoom=16&size=640x353&markers=icon:{{url('public/backoffice/sistema/marker.png')}}|{{$prestamodomicilio->mapa_latitud}},{{$prestamodomicilio->mapa_longitud}}&key=AIzaSyAyFtw-yEfh6GtiPyx_4ZQWt3g_vUCu5eQ);"></div>
                                    @endif
                                  @endif
                            </div>
                          @if(count($relaciones)>0)
                          <div class="list-single-main-wrapper fl-wrap">
                              <div class="breadcrumbs gradient-bg fl-wrap">
                                <span>REFERENCIAS</span>
                              </div>
                          </div>
                          <div class="table-responsive">
                              <table class="table" id="tabla-analisiscualitativo-referencia">
                                  <thead>
                                    <tr>
                                      <th class="tabla_titulo">Persona</th>
                                      <th class="tabla_titulo">Tipo de relación</th>
                                      <th class="tabla_titulo">Nro de Teléfono</th>
                                      <th class="tabla_titulo">Comentario</th>
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
                         @endif
                      </div>
                      <div class="col-sm-6">
                          <div class="list-single-main-wrapper fl-wrap">
                              <div class="breadcrumbs gradient-bg fl-wrap">
                                <span>FOTOGRAFIAS DE DOMICILIO</span>
                              </div>
                          </div>
                            <div class="row">
                            @foreach($prestamodomicilioimagen as $value)
                                <div class="col-sm-4">
                                        <div style="
                                              background-image: url({{url('public/backoffice/tienda/'.$tienda->id.'/creditodomicilio/'.$value->imagen)}});
                                              background-repeat: no-repeat;
                                              background-size: contain;
                                              background-position: center;
                                              background-color: #b4b4b4;
                                              border-radius: 5px;
                                              height: 250px;
                                              border: 1px solid #bdbdbd;
                                              margin-bottom:5px;">
                                        </div>
                                </div>
                            @endforeach
                            </div>
                          <div class="list-single-main-wrapper fl-wrap">
                              <div class="breadcrumbs gradient-bg fl-wrap">
                                <span>FOTOGRAFIA DE SUMINISTRO</span>
                              </div>
                          </div>
                            @if($prestamodomicilio!='')
                            @if($prestamodomicilio->imagensuministro!='')
                                        <div style="
                                              background-image: url({{url('/public/backoffice/tienda/'.$tienda->id.'/creditodomicilio/'.$prestamodomicilio->imagensuministro)}});
                                              background-repeat: no-repeat;
                                              background-size: contain;
                                              background-position: center;
                                              background-color: #b4b4b4;
                                              border-radius: 5px;
                                              height: 350px;
                                              border: 1px solid #bdbdbd;
                                              margin-bottom:5px;
                                              width: 100%;
                                              float: left;">
                                        </div>
                            @endif
                            @endif
                          <div class="list-single-main-wrapper fl-wrap">
                              <div class="breadcrumbs gradient-bg fl-wrap">
                                <span>FOTOGRAFIA DE FACHADA</span>
                              </div>
                          </div>
                            @if($prestamodomicilio!='')
                            @if($prestamodomicilio->imagenfachada!='')
                                        <div style="
                                              background-image: url({{url('/public/backoffice/tienda/'.$tienda->id.'/creditodomicilio/'.$prestamodomicilio->imagenfachada)}});
                                              background-repeat: no-repeat;
                                              background-size: contain;
                                              background-position: center;
                                              background-color: #b4b4b4;
                                              border-radius: 5px;
                                              height: 350px;
                                              border: 1px solid #bdbdbd;
                                              margin-bottom:5px;
                                              width: 100%;
                                              float: left;">
                                        </div>
                            @endif
                            @endif
                      </div>  
              </div>
          </div>
          <div id="tab-credito-detalle-cliente-2" class="tab-content" style="display: none;">
              <div class="tabs-container" id="tab-credito-detalle-cliente-domicilio">
                  <ul class="tabs-menu">
                      <li class="current"><a href="#tab-credito-detalle-cliente-domicilio-0">General</a></li>
                      <li><a href="#tab-credito-detalle-cliente-domicilio-12">Documentos</a></li>
                      <li class="tab-credito-detalle-cliente-domicilio-ingreso"><a href="#tab-credito-detalle-cliente-domicilio-4">Evaluaciòn</a></li>
                  </ul>
                  <div class="tab">
                      <div id="tab-credito-detalle-cliente-domicilio-0" class="tab-content" style="display: block;">
                          
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
                                                <td class="tabla_titulo" style="width:50%">Fuente de Ingreso</td>
                                                <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->fuenteingreso:''}}</td>
                                            </tr>
                                            <tr>
                                                <td class="tabla_titulo">Giro</td>
                                                <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->nombre_giro:''}}</td>
                                            </tr>
                                            <tr>
                                                <td class="tabla_titulo">Actividad</td>
                                                <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->actividad:''}}</td>
                                            </tr>
                                            <tr>
                                                <td class="tabla_titulo">Nombre de Negocio</td>
                                                <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->nombrenegocio:''}}</td>
                                            </tr>
                                            <tr>
                                                <td class="tabla_titulo">Reside Desde (mes/año)</td>
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
                                          <span>DÌAS LABORABLES</span>
                                        </div>
                                    </div>
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td class="tabla_titulo" style="width:25%">Lunes</td>
                                                <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->labora_lunes:''}}</td>
                                                <td class="tabla_titulo" style="width:25%">Viernes</td>
                                                <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->labora_viernes:''}}</td>
                                            </tr>
                                            <tr>
                                                <td class="tabla_titulo">Martes</td>
                                                <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->labora_martes:''}}</td>
                                                <td class="tabla_titulo">Sabados</td>
                                                <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->labora_sabados:''}}</td>
                                            </tr>
                                            <tr>
                                                <td class="tabla_titulo">Miercoles</td>
                                                <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->labora_miercoles:''}}</td>
                                                <td class="tabla_titulo">Domingos</td>
                                                <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->labora_domingos:''}}</td>
                                            </tr>
                                            <tr>
                                                <td class="tabla_titulo">Jueves</td>
                                                <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->labora_jueves:''}}</td>
                                                <td class="tabla_titulo"></td>
                                                <td class="tabla_texto"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                  <div class="list-single-main-wrapper fl-wrap">
                                      <div class="breadcrumbs gradient-bg fl-wrap">
                                        <span>UBICACIÒN</span>
                                      </div>
                                  </div>
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td class="tabla_titulo">Ubigeo</td>
                                                <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->nombre_ubigeo:$prestamocredito->clienteubigeonombre}}</td>
                                            </tr>
                                            <tr>
                                                <td class="tabla_titulo">Dirección</td>
                                                <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->direccion:$prestamocredito->clientedireccion}}</td>
                                            </tr>
                                            <tr>
                                                <td class="tabla_titulo">Referencia</td>
                                                <td class="tabla_texto">{{$prestamolaboral!=''?$prestamolaboral->referencia:$prestamocredito->clientereferencia}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    @if($prestamolaboral!='')
                                      @if($prestamolaboral->mapa_latitud!='' && $prestamolaboral->mapa_longitud!='')
                                      <div class="imagen_croquis" style="background-image: url(https://maps.googleapis.com/maps/api/staticmap?center={{$prestamolaboral->mapa_latitud}},{{$prestamolaboral->mapa_longitud}}&zoom=16&size=640x353&markers=icon:{{url('public/backoffice/sistema/marker.png')}}|{{$prestamolaboral->mapa_latitud}},{{$prestamolaboral->mapa_longitud}}&key=AIzaSyAyFtw-yEfh6GtiPyx_4ZQWt3g_vUCu5eQ);"></div>
                                      @endif
                                    @endif
                              </div>
                              <div class="col-md-6">
                                  <div class="list-single-main-wrapper fl-wrap">
                                      <div class="breadcrumbs gradient-bg fl-wrap">
                                        <span>FOTOGRAFIAS DE NEGOCIO</span>
                                      </div>
                                  </div>
                                  <div class="row">
                                  @foreach($prestamolaboralnegocioimagen as $value)
                                      <div class="col-sm-4">
                                              <div style="
                                                    background-image: url({{url('public/backoffice/tienda/'.$tienda->id.'/creditolaboral/'.$value->imagen)}});
                                                    background-repeat: no-repeat;
                                                    background-size: contain;
                                                    background-position: center;
                                                    background-color: #b4b4b4;
                                                    border-radius: 5px;
                                                    height: 250px;
                                                    border: 1px solid #bdbdbd;
                                                    margin-bottom:5px;">
                                              </div>
                                      </div>
                                  @endforeach
                                  </div>
                                  <div class="list-single-main-wrapper fl-wrap">
                                      <div class="breadcrumbs gradient-bg fl-wrap">
                                        <span>FOTOGRAFIA DE SUMINISTRO</span>
                                      </div>
                                  </div>
                                  @if($prestamolaboral!='')
                                  @if($prestamolaboral->imagensuministro!='')
                                              <div style="
                                                    background-image: url({{url('/public/backoffice/tienda/'.$tienda->id.'/creditolaboral/'.$prestamolaboral->imagensuministro)}});
                                                    background-repeat: no-repeat;
                                                    background-size: contain;
                                                    background-position: center;
                                                    background-color: #b4b4b4;
                                                    border-radius: 5px;
                                                    height: 350px;
                                                    border: 1px solid #bdbdbd;
                                                    margin-bottom:5px;">
                                              </div>
                                  @endif
                                  @endif
                                  <div class="list-single-main-wrapper fl-wrap">
                                      <div class="breadcrumbs gradient-bg fl-wrap">
                                        <span>FOTOGRAFIA DE FACHADA</span>
                                      </div>
                                  </div>
                                  @if($prestamolaboral!='')
                                  @if($prestamolaboral->imagenfachada!='')
                                              <div style="
                                                    background-image: url({{url('/public/backoffice/tienda/'.$tienda->id.'/creditolaboral/'.$prestamolaboral->imagenfachada)}});
                                                    background-repeat: no-repeat;
                                                    background-size: contain;
                                                    background-position: center;
                                                    background-color: #b4b4b4;
                                                    border-radius: 5px;
                                                    height: 350px;
                                                    border: 1px solid #bdbdbd;
                                                    margin-bottom:5px;">
                                              </div>
                                  @endif
                                  @endif
                              </div>
                          </div>
                      </div>
                      <div id="tab-credito-detalle-cliente-domicilio-12" class="tab-content" style="display: none;">
                        
                          <div class="row">
                              <div class="col-sm-6">
                                  
                                  <div class="list-single-main-wrapper fl-wrap">
                                      <div class="breadcrumbs gradient-bg fl-wrap">
                                        <span>LICENCIA DE FUNCIONAMIENTO</span>
                                      </div>
                                  </div>
                                  <div class="row">
                                  @foreach($prestamolaborallicenciafuncionamientoimagen as $value)
                                      <div class="col-sm-4">
                                              <div style="
                                                    background-image: url({{url('public/backoffice/tienda/'.$tienda->id.'/creditolaboral/'.$value->imagen)}});
                                                    background-repeat: no-repeat;
                                                    background-size: contain;
                                                    background-position: center;
                                                    background-color: #b4b4b4;
                                                    border-radius: 5px;
                                                    height: 250px;
                                                    border: 1px solid #bdbdbd;
                                                    margin-bottom:5px;">
                                              </div>
                                      </div>
                                  @endforeach
                                  </div> 
                                  <div class="list-single-main-wrapper fl-wrap">
                                      <div class="breadcrumbs gradient-bg fl-wrap">
                                        <span>CONTRATO DE ALQUILER</span>
                                      </div>
                                  </div>
                                  <div class="row">
                                  @foreach($prestamolaboralcontratoalquilerimagen as $value)
                                      <div class="col-sm-4">
                                              <div style="
                                                    background-image: url({{url('public/backoffice/tienda/'.$tienda->id.'/creditolaboral/'.$value->imagen)}});
                                                    background-repeat: no-repeat;
                                                    background-size: contain;
                                                    background-position: center;
                                                    background-color: #b4b4b4;
                                                    border-radius: 5px;
                                                    height: 250px;
                                                    border: 1px solid #bdbdbd;
                                                    margin-bottom:5px;">
                                              </div>
                                      </div>
                                  @endforeach
                                  </div> 
                                  <div class="list-single-main-wrapper fl-wrap">
                                      <div class="breadcrumbs gradient-bg fl-wrap">
                                        <span>FICHA RUC</span>
                                      </div>
                                  </div>
                                  <div class="row">
                                  @foreach($prestamolaboralficharucimagen as $value)
                                      <div class="col-sm-4">
                                              <div style="
                                                    background-image: url({{url('public/backoffice/tienda/'.$tienda->id.'/creditolaboral/'.$value->imagen)}});
                                                    background-repeat: no-repeat;
                                                    background-size: contain;
                                                    background-position: center;
                                                    background-color: #b4b4b4;
                                                    border-radius: 5px;
                                                    height: 250px;
                                                    border: 1px solid #bdbdbd;
                                                    margin-bottom:5px;">
                                              </div>
                                      </div>
                                  @endforeach
                                  </div> 
                                  
                              </div>
                              <div class="col-sm-6">
                                  <div class="list-single-main-wrapper fl-wrap">
                                      <div class="breadcrumbs gradient-bg fl-wrap">
                                        <span>RECIBO DE AGUA</span>
                                      </div>
                                  </div>
                                  <div class="row">
                                  @foreach($prestamolaboralreciboaguaimagen as $value)
                                      <div class="col-sm-4">
                                              <div style="
                                                    background-image: url({{url('public/backoffice/tienda/'.$tienda->id.'/creditolaboral/'.$value->imagen)}});
                                                    background-repeat: no-repeat;
                                                    background-size: contain;
                                                    background-position: center;
                                                    background-color: #b4b4b4;
                                                    border-radius: 5px;
                                                    height: 250px;
                                                    border: 1px solid #bdbdbd;
                                                    margin-bottom:5px;">
                                              </div>
                                      </div>
                                  @endforeach
                                  </div> 
                                  <div class="list-single-main-wrapper fl-wrap">
                                      <div class="breadcrumbs gradient-bg fl-wrap">
                                        <span>RECIBO DE LUZ</span>
                                      </div>
                                  </div>
                                  <div class="row">
                                  @foreach($prestamolaboralreciboluzimagen as $value)
                                      <div class="col-sm-4">
                                              <div style="
                                                    background-image: url({{url('public/backoffice/tienda/'.$tienda->id.'/creditolaboral/'.$value->imagen)}});
                                                    background-repeat: no-repeat;
                                                    background-size: contain;
                                                    background-position: center;
                                                    background-color: #b4b4b4;
                                                    border-radius: 5px;
                                                    height: 250px;
                                                    border: 1px solid #bdbdbd;
                                                    margin-bottom:5px;">
                                              </div>
                                      </div>
                                  @endforeach
                                  </div> 
                                  <div class="list-single-main-wrapper fl-wrap">
                                      <div class="breadcrumbs gradient-bg fl-wrap">
                                        <span>BOLETAS DE COMPRAS</span>
                                      </div>
                                  </div>
                                  <div class="row">
                                  @foreach($prestamolaboralboletacompraimagen as $value)
                                      <div class="col-sm-4">
                                              <div style="
                                                    background-image: url({{url('public/backoffice/tienda/'.$tienda->id.'/creditolaboral/'.$value->imagen)}});
                                                    background-repeat: no-repeat;
                                                    background-size: contain;
                                                    background-position: center;
                                                    background-color: #b4b4b4;
                                                    border-radius: 5px;
                                                    height: 250px;
                                                    border: 1px solid #bdbdbd;
                                                    margin-bottom:5px;">
                                              </div>
                                      </div>
                                  @endforeach
                                  </div> 
                              </div>
                          </div>       
                      </div>
                      <div id="tab-credito-detalle-cliente-domicilio-4" class="tab-content" style="display: none;">
                          <div class="row">
                              <div class="col-sm-6">
                              @if($prestamolaboral!='')
                              @if(($prestamolaboral->idfuenteingreso==1 && $prestamolaboral->idprestamo_giro==1) || ($prestamolaboral->idfuenteingreso==1 && $prestamolaboral->idprestamo_giro==3) || ($prestamolaboral->idfuenteingreso==1 && $prestamolaboral->idprestamo_giro==2))
                                  <div class="list-single-main-wrapper fl-wrap">
                                      <div class="breadcrumbs gradient-bg fl-wrap">
                                        <span>INGRESOS</span>
                                      </div>
                                  </div>
                                  <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <td class="tabla_titulo">PAGO MENSUAL</td>
                                            <td class="tabla_texto">{{$laboralingreso!=''?$laboralingreso->monto:'0.00'}}</td>
                                        </tr>
                                    </table>
                                  </div>
                              @elseif($prestamolaboral->idfuenteingreso==2 && $prestamolaboral->idprestamo_giro==1)
                                  @if(count($laboralventa)>0)
                                  <div class="list-single-main-wrapper fl-wrap">
                                      <div class="breadcrumbs gradient-bg fl-wrap">
                                        <span>VENTAS</span>
                                      </div>
                                  </div>
                                  <div class="table-responsive">
                                      <table class="table">
                                          <thead>
                                            <tr>
                                              <th class="tabla_titulo">Producto</th>
                                              <th class="tabla_titulo" width="60px">Cantidad</th>
                                              <th class="tabla_titulo" width="110px">P. Unitario</th>
                                              <th class="tabla_titulo" width="110px">Venta Diaria</th>
                                              <th class="tabla_titulo" width="110px">Venta Semanal</th>
                                              <th class="tabla_titulo" width="110px">Venta Quincenal</th>
                                              <th class="tabla_titulo" width="110px">Venta Mensual</th>
                                            </tr>
                                          </thead>
                                          <tbody>
                                            <?php 
                                            $preciounitario = 0;
                                            $preciototal = 0;
                                            $preciototal_semanal = 0;
                                            $preciototal_quincenal = 0;
                                            $preciototal_mensual = 0;
                                            ?>
                                            @foreach ($laboralventa as $value)
                                              <tr>
                                                <td class="tabla_texto">{{ $value->producto }}</td>
                                                <td class="tabla_texto">{{ $value->cantidad }}</td>
                                                <td class="tabla_texto">{{ $value->preciounitario }}</td>
                                                <td class="tabla_texto">{{ $value->preciototal }}</td>
                                                <td class="tabla_texto">{{ $value->preciototal_semanal }}</td>
                                                <td class="tabla_texto">{{ $value->preciototal_quincenal }}</td>
                                                <td class="tabla_texto">{{ $value->preciototal_mensual }}</td>
                                              </tr>
                                            <?php 
                                            $preciounitario = $preciounitario+$value->preciounitario;
                                            $preciototal = $preciototal+$value->preciototal;
                                            $preciototal_semanal = $preciototal_semanal+$value->preciototal_semanal;
                                            $preciototal_quincenal = $preciototal_quincenal+$value->preciototal_quincenal;
                                            $preciototal_mensual = $preciototal_mensual+$value->preciototal_mensual;
                                            ?>
                                            @endforeach
                                          </tbody>
                                          <tfoot>
                                            <tr>
                                              <th class="tabla_titulo"></th>
                                              <th class="tabla_titulo"></th>
                                              <th class="tabla_titulo">{{ $preciounitario }}</th>
                                              <th class="tabla_titulo">{{ $preciototal }}</th>
                                              <th class="tabla_titulo">{{ $preciototal_semanal }}</th>
                                              <th class="tabla_titulo">{{ $preciototal_quincenal }}</th>
                                              <th class="tabla_titulo">{{ $preciototal_mensual }}</th>
                                            </tr>
                                          </tfoot>
                                      </table>
                                  </div>
                                  @endif
                                  @if(count($laboralcompra)>0)
                                  <div class="list-single-main-wrapper fl-wrap">
                                      <div class="breadcrumbs gradient-bg fl-wrap">
                                        <span>COSTO DE VENTAS</span>
                                      </div>
                                  </div>
                                  <div class="table-responsive">
                                      <table class="table">
                                          <thead>
                                            <tr>
                                              <th class="tabla_titulo">Producto</th>
                                              <th class="tabla_titulo" width="60px">Cantidad</th>
                                              <th class="tabla_titulo" width="110px">P. Unitario</th>
                                              <th class="tabla_titulo" width="110px">Venta Diaria</th>
                                              <th class="tabla_titulo" width="110px">Venta Semanal</th>
                                              <th class="tabla_titulo" width="110px">Venta Quincenal</th>
                                              <th class="tabla_titulo" width="110px">Venta Mensual</th>
                                            </tr>
                                          </thead>
                                          <tbody>
                                            <?php 
                                            $preciounitario = 0;
                                            $preciototal = 0;
                                            $preciototal_semanal = 0;
                                            $preciototal_quincenal = 0;
                                            $preciototal_mensual = 0;
                                            ?>
                                            @foreach ($laboralcompra as $value)
                                              <tr>
                                                <td class="tabla_texto">{{ $value->producto }}</td>
                                                <td class="tabla_texto">{{ $value->cantidad }}</td>
                                                <td class="tabla_texto">{{ $value->preciounitario }}</td>
                                                <td class="tabla_texto">{{ $value->preciototal }}</td>
                                                <td class="tabla_texto">{{ $value->preciototal_semanal }}</td>
                                                <td class="tabla_texto">{{ $value->preciototal_quincenal }}</td>
                                                <td class="tabla_texto">{{ $value->preciototal_mensual }}</td>
                                              </tr>
                                            <?php 
                                            $preciounitario = $preciounitario+$value->preciounitario;
                                            $preciototal = $preciototal+$value->preciototal;
                                            $preciototal_semanal = $preciototal_semanal+$value->preciototal_semanal;
                                            $preciototal_quincenal = $preciototal_quincenal+$value->preciototal_quincenal;
                                            $preciototal_mensual = $preciototal_mensual+$value->preciototal_mensual;
                                            ?>
                                            @endforeach
                                          </tbody>
                                          <tfoot>
                                            <tr>
                                              <th class="tabla_titulo"></th>
                                              <th class="tabla_titulo"></th>
                                              <th class="tabla_titulo">{{ $preciounitario }}</th>
                                              <th class="tabla_titulo">{{ $preciototal }}</th>
                                              <th class="tabla_titulo">{{ $preciototal_semanal }}</th>
                                              <th class="tabla_titulo">{{ $preciototal_quincenal }}</th>
                                              <th class="tabla_titulo">{{ $preciototal_mensual }}</th>
                                            </tr>
                                          </tfoot>
                                      </table>
                                  </div>
                                  @endif
                              @elseif(($prestamolaboral->idfuenteingreso==2 && $prestamolaboral->idprestamo_giro==2) || ($prestamolaboral->idfuenteingreso==2 && $prestamolaboral->idprestamo_giro==3))
                                  @if($laboralservicio!='')
                                  <div class="list-single-main-wrapper fl-wrap">
                                      <div class="breadcrumbs gradient-bg fl-wrap">
                                        <span>INGRESOS</span>
                                      </div>
                                  </div>
                                  <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                          <tr>
                                            <td class="tabla_titulo">Bueno</td>
                                            <td class="tabla_texto">{{$laboralservicio!=''?$laboralservicio->bueno:'0.00'}}</td>
                                          </tr>
                                          <tr>
                                            <td class="tabla_titulo">Regular</td>
                                            <td class="tabla_texto">{{$laboralservicio!=''?$laboralservicio->regular:'0.00'}}</td>
                                          </tr>
                                          <tr>
                                            <td class="tabla_titulo">Malo</td>
                                            <td class="tabla_texto">{{$laboralservicio!=''?$laboralservicio->malo:'0.00'}}</td>
                                          </tr>
                                          <tr>
                                            <td class="tabla_titulo">Promedio</td>
                                            <td class="tabla_texto">{{$laboralservicio!=''?$laboralservicio->promedio:'0.00'}}</td>
                                          </tr>
                                          <tr>
                                            <td class="tabla_titulo">Venta Semanal</td>
                                            <td class="tabla_texto">{{$laboralservicio!=''?$laboralservicio->semanal:'0.00'}}</td>
                                          </tr>
                                          <tr>
                                            <td class="tabla_titulo">Venta Quincenal</td>
                                            <td class="tabla_texto">{{$laboralservicio!=''?$laboralservicio->quincenal:'0.00'}}</td>
                                          </tr>
                                          <tr>
                                            <td class="tabla_titulo">Venta Mensual</td>
                                            <td class="tabla_texto">{{$laboralservicio!=''?$laboralservicio->mensual:'0.00'}}</td>
                                          </tr>
                                        </thead>
                                    </table>
                                  </div>
                                  @endif
                              @endif
                              @endif
                                
                              @if(count($laboralegresogasto)>0)
                                  <div class="list-single-main-wrapper fl-wrap">
                                      <div class="breadcrumbs gradient-bg fl-wrap">
                                        <span>GASTOS OPERATIVOS</span>
                                      </div>
                                  </div>
                                  <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                          <tr>
                                            <td class="tabla_titulo">Concepto</td>
                                            <td class="tabla_titulo" width="110px">Monto</td>
                                          </tr>
                                        </thead>
                                        <tbody num="0">
                                          <?php $total = 0; ?>
                                          @foreach ($laboralegresogasto as $value)
                                          <tr>
                                            <td class="tabla_texto">{{ $value->concepto }}</td>
                                            <td class="tabla_texto">{{ $value->monto }}</td>
                                          </tr>
                                          <?php $total = $total+$value->monto; ?>
                                          @endforeach
                                          <tr>
                                            <td class="tabla_titulo" style="text-aligth:right;">TOTAL</td>
                                            <td class="tabla_titulo">{{ $total }}</td>
                                          </tr>
                                        </tbody>
                                    </table>
                                  </div>
                              @endif
                              </div>
                              <div class="col-sm-6">
                              @if(count($laboralegresogastofamiliares)>0)
                                  <div class="list-single-main-wrapper fl-wrap">
                                      <div class="breadcrumbs gradient-bg fl-wrap">
                                        <span>GASTOS FAMILIARES</span>
                                      </div>
                                  </div>
                                  <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                          <tr>
                                            <td class="tabla_titulo">Concepto</td>
                                            <td class="tabla_titulo" width="110px">Monto</td>
                                          </tr>
                                        </thead>
                                        <tbody num="0">
                                          <?php $total = 0; ?>
                                          @foreach ($laboralegresogastofamiliares as $value)
                                          <tr>
                                            <td class="tabla_texto">{{ $value->concepto }}</td>
                                            <td class="tabla_texto">{{ $value->monto }}</td>
                                          </tr>
                                          <?php $total = $total+$value->monto; ?>
                                          @endforeach
                                          <tr>
                                            <td class="tabla_titulo" style="text-aligth:right;">TOTAL</td>
                                            <td class="tabla_titulo">{{ $total }}</td>
                                          </tr>
                                        </tbody>
                                    </table>
                                  </div>
                              @endif
                              @if(count($laboralegresopago)>0)
                                  <div class="list-single-main-wrapper fl-wrap">
                                      <div class="breadcrumbs gradient-bg fl-wrap">
                                        <span>PAGO DE CUOTAS (BANCOS)</span>
                                      </div>
                                  </div>
                                  <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                          <tr>
                                            <td class="tabla_titulo">Institución Financiera</td>
                                            <td class="tabla_titulo" width="110px">Monto</td>
                                          </tr>
                                        </thead>
                                        <tbody num="0">
                                          <?php $total = 0; ?>
                                          @foreach ($laboralegresopago as $value)
                                          <tr>
                                            <td class="tabla_texto">{{ $value->conceptoegresopago }}</td>
                                            <td class="tabla_texto">{{ $value->monto }}</td>
                                          </tr>
                                          <?php $total = $total+$value->monto; ?>
                                          @endforeach
                                          <tr>
                                            <td class="tabla_titulo" style="text-aligth:right;">TOTAL</td>
                                            <td class="tabla_titulo">{{ $total }}</td>
                                          </tr>
                                        </tbody>
                                    </table>
                                  </div>
                              @endif
                              @if(count($laboralotroingreso)>0)
                                  <div class="list-single-main-wrapper fl-wrap">
                                      <div class="breadcrumbs gradient-bg fl-wrap">
                                        <span>GASTOS FAMILIARES</span>
                                      </div>
                                  </div>
                                  <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                          <tr>
                                            <td class="tabla_titulo">Concepto</td>
                                            <td class="tabla_titulo" width="110px">Monto</td>
                                          </tr>
                                        </thead>
                                        <tbody num="0">
                                          <?php $total = 0; ?>
                                          @foreach ($laboralotroingreso as $value)
                                          <tr>
                                            <td class="tabla_texto">{{ $value->conceptootroingreso }}</td>
                                            <td class="tabla_texto">{{ $value->monto }}</td>
                                          </tr>
                                          <?php $total = $total+$value->monto; ?>
                                          @endforeach
                                          <tr>
                                            <td class="tabla_titulo" style="text-aligth:right;">TOTAL</td>
                                            <td class="tabla_titulo">{{ $total }}</td>
                                          </tr>
                                        </tbody>
                                    </table>
                                  </div>
                              @endif
                              @if(count($laboralotrogasto)>0)
                                  <div class="list-single-main-wrapper fl-wrap">
                                      <div class="breadcrumbs gradient-bg fl-wrap">
                                        <span>OTROS GASTOS</span>
                                      </div>
                                  </div>
                                  <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                          <tr>
                                            <td class="tabla_titulo">Concepto</td>
                                            <td class="tabla_titulo" width="110px">Monto</td>
                                          </tr>
                                        </thead>
                                        <tbody num="0">
                                          <?php $total = 0; ?>
                                          @foreach ($laboralotrogasto as $value)
                                          <tr>
                                            <td class="tabla_texto">{{ $value->conceptootrogasto }}</td>
                                            <td class="tabla_texto">{{ $value->monto }}</td>
                                          </tr>
                                          <?php $total = $total+$value->monto; ?>
                                          @endforeach
                                          <tr>
                                            <td class="tabla_titulo" style="text-aligth:right;">TOTAL</td>
                                            <td class="tabla_titulo">{{ $total }}</td>
                                          </tr>
                                        </tbody>
                                    </table>
                                  </div>
                              @endif
                              </div>
                          </div>
                      </div>
                  </div>
              </div> 
          </div>
          <div id="tab-credito-detalle-cliente-3" class="tab-content" style="display: none;">
            <div class="table-responsive">
              <table class="table" id="tabla-contenido">
                  <thead>
                    <tr>
                      <th class="tabla_titulo">Producto</th>
                      <th class="tabla_titulo">Descripción</th>
                      <th class="tabla_titulo">Valor Estimado</th>
                      <th class="tabla_titulo">Documento</th>
                      <th class="tabla_titulo">Imagenes</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($bienes as $value)
                      <tr>
                        <td class="tabla_texto">{{$value->producto}}</td>
                        <td class="tabla_texto">{{$value->descripcion}}</td>
                        <td class="tabla_texto">{{$value->valorestimado}}</td>
                        <td class="tabla_texto">
                          @if($value->idprestamo_documento==1)
                              SIN DOCUMENTOS
                          @elseif($value->idprestamo_documento==2)
                              COPIA/LEGALIZADO
                          @elseif($value->idprestamo_documento==3)
                              ORIGINAL
                          @endif
                        </td>
                        <td class="tabla_texto">
                          <?php $prestamobienimagen = DB::table('s_prestamo_creditobienimagen')->where('idprestamo_creditobien', $value->id)->get(); ?>
                          @foreach($prestamobienimagen as $valueimagen)
                              <div style="background-image: url({{url('public/backoffice/tienda/'.$tienda->id.'/creditobien/'.$valueimagen->imagen)}});
                                            background-repeat: no-repeat;
                                            background-size: contain;
                                            background-position: center;
                                            height: 42px;
                                            width: 50px;
                                            background-color: #31353c;
                                            float: left;
                                            margin-right: 1px;">
                              </div>
                          @endforeach 
                        </td>
                      </tr>
                    @endforeach 
                  </tbody>
              </table>
            </div>
          </div>
          <div id="tab-credito-detalle-cliente-5" class="tab-content" style="display: none;">
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
                                  <td class="tabla_titulo" style="width:50%">Calificación Crediticio</td>
                                  <td class="tabla_texto">{{$sustento->calificacionnombre ?? '' }}</td>
                              </tr>
                              <tr>
                                  <td class="tabla_titulo">Experiencia en Créditos Diarios/Semanales</td>
                                  <td class="tabla_texto">
                                    @if($sustento!='')
                                    @if($sustento->idprestamo_experienciacredito==1)
                                        MAYOR A UNA TARJETA
                                    @elseif($sustento->idprestamo_experienciacredito==2)
                                        IGUAL A 1 TARJETA
                                    @elseif($sustento->idprestamo_experienciacredito==3)
                                        NINGUNA TARJETA
                                    @endif
                                    @endif
                                  </td>
                              </tr>
                              <tr>
                                  <td class="tabla_titulo">Endeudamiento en el Sistema Financiera (ultimos 6 meses)</td>
                                  <td class="tabla_texto">
                                    @if($sustento!='')
                                    @if($sustento->idprestamo_endeudamientosistema==1)
                                        AUMENTO DE DEUDA
                                    @elseif($sustento->idprestamo_endeudamientosistema==2)
                                        DISMINUCIÓN NO SIGNIFICATIVA
                                    @elseif($sustento->idprestamo_endeudamientosistema==3)
                                        DIMINUYE DEUDA
                                    @endif
                                    @endif
                                  </td>
                              </tr>
                              <tr>
                                  <td class="tabla_titulo">Inventario, Muebles y Enseres</td>
                                  <td class="tabla_texto">
                                    @if($sustento!='')
                                    @if($sustento->idprestamo_inventario==1)
                                        POCA MERCADERIA
                                    @elseif($sustento->idprestamo_inventario==2)
                                        REGULAR MERCADERIA
                                    @elseif($sustento->idprestamo_inventario==3)
                                        NEGOCIO BIEN IMPLEMENTADO
                                    @endif
                                    @endif
                                  </td>
                              </tr>
                          </tbody>
                      </table>
                  </div>
                  <div class="col-sm-6">
                      <div class="list-single-main-wrapper fl-wrap">
                          <div class="breadcrumbs gradient-bg fl-wrap">
                            <span>COMENTARIO DEL ASESOR</span>
                          </div>
                      </div>
                      <table class="table">
                          <tbody>
                              <tr>
                                  <td class="tabla_titulo" style="width:50%">Comentario sobre el Negocio</td>
                                  <td class="tabla_texto">{{ $sustento->comentarioasesor ?? '' }}</td>
                              </tr>
                              <tr>
                                  <td class="tabla_titulo">Descripción del Destino del Crédito</td>
                                  <td class="tabla_texto">{{ $sustento->destinocredito ?? '' }}</td>
                              </tr>
                              <tr>
                                  <td class="tabla_titulo">Riesgos que presenta el Negocio</td>
                                  <td class="tabla_texto">{{ $sustento->riesgonegocio ?? '' }}</td>
                              </tr>
                              <tr>
                                  <td class="tabla_titulo">¿El Cliente, a qué destina el ingreso excedente del Negocio?</td>
                                  <td class="tabla_texto">{{ $sustento->destinoexcendete ?? '' }}</td>
                              </tr>
                              <tr>
                                  <td class="tabla_titulo">Sustento de la Propuesta</td>
                                  <td class="tabla_texto">{{ $sustento->sustentopropuesta ?? '' }}</td>
                              </tr>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
          <div id="tab-credito-detalle-cliente-6" class="tab-content" style="display: none;">
            <div id="cont-resultado"></div>
          </div>
        </div>
      </div>  
</div>
<?php $color =  configuracion($tienda->id,'sistema_color')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_color')['valor']:'#31353d' ?> 
<style>
  .tabla_titulo {
      padding-top: 10px !important;
      padding-bottom: 10px !important;    
      background-color: <?php echo $color ?> !important;
      text-transform: uppercase;
      color: #FFFFFF !important;    
      border-top: 0px solid <?php echo $color ?>60 !important;
  }
  .tabla_texto {   
      padding-top: 10px !important;
      padding-bottom: 10px !important;    
      background-color: <?php echo $color ?>60 !important;
      border-top: 1px solid <?php echo $color ?>60 !important;
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
  .imagen_croquis {
      background-repeat: no-repeat;
      background-size: cover;
      background-position: center;
      height: 350px;
      border: 1px solid #bdbdbd;
      border-radius: 5px;
      margin-bottom:5px;
  }
</style>     

<!-- Tabulador de pestañas -->
<script>
  tab({click:'#tab-credito-detalle-cliente'});
  tab({click:'#tab-credito-detalle-cliente-domicilio'});
  tab({click:'#tab-credito-detalle-cliente-domiciliodocumento'});
  tab({click:'#tab-credito-detalle-cliente-domiciliodocumentonegocio'});
    resultado_index();
    function resultado_index(){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=resultado',result:'#cont-resultado'});
    }
</script>    