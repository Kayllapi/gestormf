<?php
          $prestamocreditogrupaldetalle = DB::table('s_prestamo_creditogrupaldetalle')
                ->where('s_prestamo_creditogrupaldetalle.idprestamo_creditogrupal', $idprestamocreditogrupal)
                ->orderBy('s_prestamo_creditogrupaldetalle.numero','asc')
                ->get();

          $prestamo_creditos = DB::table('s_prestamo_credito')
                ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
                ->where('s_prestamo_credito.idprestamo_creditogrupal',$idprestamocreditogrupal)
                ->select(
                    's_prestamo_credito.*',
                    'cliente.identificacion as clienteidentificacion',
                    DB::raw('IF(cliente.idtipopersona=1,
                    CONCAT(cliente.apellidos,", ",cliente.nombre),
                    CONCAT(cliente.apellidos)) as cliente'),
                )
                ->get();
?>
<div id="carga-credito">
    <div class="tabs-container" id="tab-credito-detalle-cliente">
        <ul class="tabs-menu">
          <li class="current"><a href="#tab-credito-detalle-cliente-0">Cronograma</a></li>
          <li><a href="#tab-credito-detalle-cliente-1">Integrantes</a></li>
        </ul>
        <div class="tab">
          <div id="tab-credito-detalle-cliente-0" class="tab-content" style="display: block;">
                              <table class="table">
                                  <tbody>
                                      <tr>
                                          <td class="tabla_titulo" style="width:20%;">Nombre</td>
                                          <td class="tabla_texto">{{$prestamocreditogrupal->nombre}}</td>
                                      </tr>
                                  </tbody>
                              </table>
              <div class="row">
                  <div class="col-sm-6">
                      <div class="list-single-main-wrapper fl-wrap">
                          <div class="breadcrumbs gradient-bg fl-wrap">
                            <span>CRÈDITO GRUPAL</span>
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-md-6">
                              <table class="table">
                                  <tbody>
                                      <tr>
                                          <td class="tabla_titulo">Tipo de Crédito</td>
                                          <td class="tabla_texto">{{$prestamocreditogrupal->tipocredito}}</td>
                                      </tr>
                                      <tr>
                                          <td class="tabla_titulo">Monto</td>
                                          <td class="tabla_texto">{{$prestamocreditogrupal->monto}}</td>
                                      </tr>
                                      <tr>
                                          <td class="tabla_titulo">Número de Cuotas</td>
                                          <td class="tabla_texto">{{$prestamocreditogrupal->numerocuota}} CUOTAS</td>
                                      </tr>
                                      <tr>
                                          <td class="tabla_titulo">Fecha de Inicio</td>
                                          <td class="tabla_texto">{{date_format(date_create($prestamocreditogrupal->fechainiciocero),"d/m/Y")}}</td>
                                      </tr>
                                      <tr>
                                          <td class="tabla_titulo">Frecuencia</td>
                                          <td class="tabla_texto">{{$prestamocreditogrupal->frecuencia_nombre}}</td>
                                      </tr>
                                      @if($prestamocreditogrupal->numerodias>0)
                                      <tr>
                                          <td class="tabla_titulo">Número de Días</td>
                                          <td class="tabla_texto">{{$prestamocreditogrupal->numerodias}}</td>
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
                                          <td class="tabla_texto">{{$prestamocreditogrupal->tasa}}</td>
                                      </tr>
                                      @if($prestamocreditogrupal->total_abono>0)
                                      <tr>
                                          <td class="tabla_titulo">Abono</td>
                                          <td class="tabla_texto">{{$prestamocreditogrupal->total_abono}}</td>
                                      </tr>
                                      @endif
                                      <tr>
                                          <td class="tabla_titulo">Interes Total</td>
                                          <td class="tabla_texto">{{$prestamocreditogrupal->total_interes}}</td>
                                      </tr>
                                      @if($prestamocreditogrupal->total_segurodesgravamen>0)
                                      <tr>
                                          <td class="tabla_titulo">Seguro Desgravamen</td>
                                          <td class="tabla_texto">{{$prestamocreditogrupal->total_segurodesgravamen}}</td>
                                      </tr>
                                      @endif
                                      <tr>
                                          <td class="tabla_titulo">Total a Pagar</td>
                                          <td class="tabla_texto">{{$prestamocreditogrupal->total_cuotafinaltotal}}</td>
                                      </tr>
                                  </tbody>
                              </table>
                          </div>
                      </div>
                  </div>
                  <div class="col-sm-6">
                      <div class="list-single-main-wrapper fl-wrap">
                          <div class="breadcrumbs gradient-bg fl-wrap">
                            <span>CRONOGRAMA GRUPAL</span>
                          </div>
                      </div>
                      <table class="table">
                          <thead>
                          <tr>
                              <th class="tabla_titulo" style="text-align:center;">Nº</th>
                              <th class="tabla_titulo" style="text-align:center;">F.VENCIMIENTO</th>
                              <th class="tabla_titulo" style="text-align:center;">CAPITAL</th>
                              <th class="tabla_titulo" style="text-align:center;">INTERÉS</th>
                              @if($prestamocreditogrupal->total_segurodesgravamen>0)
                              <th class="tabla_titulo" style="text-align:center;">SEGURO DESGRAVAMEN</th>
                              @endif
                              @if($prestamocreditogrupal->total_gastoadministrativo>0)
                              <th class="tabla_titulo" style="text-align:center;">GASTO ADMINISTRATIVO</th>
                              @endif
                              @if($prestamocreditogrupal->total_acumulado>0)
                              <th class="tabla_titulo" style="text-align:center;">ACUMULADO</th>
                              @endif
                              <th class="tabla_titulo" style="text-align:center;">CUOTA</th>
                              @if($prestamocreditogrupal->total_abono>0)
                              <th class="tabla_titulo" style="text-align:center;">ABONO</th>
                              <th class="tabla_titulo" style="text-align:center;">TOTAL</th>
                              @endif
                          </tr>
                          </thead>
                          <tbody>
                          @foreach($prestamocreditogrupaldetalle as $value)
                          <tr>
                              <td class="tabla_texto" style="text-align:center;">{{ str_pad($value->numero, 2, "0", STR_PAD_LEFT) }}</td>
                              <td class="tabla_texto" style="text-align:center;">{{ date_format(date_create($value->fechavencimiento),"d/m/Y") }}</td>
                              <td class="tabla_texto" style="text-align:right;">{{ $value->amortizacion }}</td>
                              <td class="tabla_texto" style="text-align:right;">{{ $value->interes }}</td>
                              @if($prestamocreditogrupal->total_segurodesgravamen>0)
                              <td class="tabla_texto" style="text-align:right;">{{ $value->seguro }}</td>
                              @endif
                              @if($prestamocreditogrupal->total_gastoadministrativo>0)
                              <td class="tabla_texto" style="text-align:right;">{{ $value->gastoadministrativo }}</td>
                              @endif
                              @if($prestamocreditogrupal->total_acumulado>0)
                              <td class="tabla_texto" style="text-align:right;">{{ $value->cuotanormal }} ({{ $value->acumulado }})</td>
                              @endif
                              <td class="tabla_texto" style="text-align:right;">{{ $value->total }}</td>
                              @if($prestamocreditogrupal->total_abono>0)
                              <td class="tabla_texto" style="text-align:right;">{{ $value->abono }}</td>
                              <td class="tabla_texto" style="text-align:right;">{{ $value->totalfinal }}</td>
                              @endif
                          </tr>
                          @endforeach
                          <tr>
                              <td class="tabla_titulo" colspan="2" style="text-align:right;padding-bottom: 10px;padding-top: 10px;">TOTAL</td>
                              <td class="tabla_titulo" style="text-align:right;">{{$prestamocreditogrupal->total_amortizacion}}</td>
                              <td class="tabla_titulo" style="text-align:right;">{{$prestamocreditogrupal->total_interes}}</td>
                              @if($prestamocreditogrupal->total_segurodesgravamen>0)
                              <td class="tabla_titulo" style="text-align:right;">{{$prestamocreditogrupal->total_segurodesgravamen}}</td>
                              @endif
                              @if($prestamocreditogrupal->total_gastoadministrativo>0)
                              <td class="tabla_titulo" style="text-align:right;">{{$prestamocreditogrupal->total_gastoadministrativo}}</td>
                              @endif
                              @if($prestamocreditogrupal->total_acumulado>0)
                              <td class="tabla_titulo" style="text-align:right;">{{$prestamocreditogrupal->total_cuotanormal}} ({{$prestamocreditogrupal->total_acumulado}})</td>
                              @endif
                              <td class="tabla_titulo" style="text-align:right;">{{$prestamocreditogrupal->total_cuotafinal}}</td>
                              @if($prestamocreditogrupal->total_abono>0)
                              <td class="tabla_titulo" style="text-align:right;">{{$prestamocreditogrupal->total_abono}}</td>
                              <td class="tabla_titulo" style="text-align:right;">{{$prestamocreditogrupal->total_cuotafinaltotal}}</td>
                              @endif
                          </tr>
                          <tbody>
                      </table>
                  </div>
              </div>
          </div>
  
          <div id="tab-credito-detalle-cliente-1" class="tab-content" style="display: none;">
                      <table class="table">
                          <thead>
                          <tr>
                              <th class="tabla_titulo" style="text-align:center;">CLIENTE</th>
                              <th class="tabla_titulo" style="text-align:center;">DESEMBOLSO</th>
                              <th class="tabla_titulo" style="text-align:center;">COMITÉ</th>
                          </tr>
                          </thead>
                          <tbody>
                          @foreach($prestamo_creditos as $value)
                          <tr>
                              <td class="tabla_texto" style="text-align:left;">{{$value->clienteidentificacion}} - {{$value->cliente}}</td>
                              <td class="tabla_texto" style="text-align:right;">{{$value->monto}}</td>
                              <td class="tabla_texto" style="text-align:right;">
                                @if($value->idprestamo_comite==1)
                                PRESIDENTA
                                @elseif($value->idprestamo_comite==2)
                                SECRETARIA
                                @elseif($value->idprestamo_comite==3)
                                TESESORERO(A)
                                @endif
                              </td>
                          </tr>
                          @endforeach
                          <tbody>
                      </table>
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
</style>
<!-- Tabulador de pestañas -->
<script>
    tab({click:'#tab-credito-detalle-cliente'});
</script>    