<?php
$prestamocredito_resultado = prestamo_resultado_solicitud($idtienda,$prestamocredito->id);
?>
    <div class="tabs-container" id="tab-resultado">
        <ul class="tabs-menu">
            <li class="current"><a href="#tab-resultado-1">Estado de Crédito</a></li>
            <li><a href="#tab-resultado-2" onclick="creditopdf_index()">Solicitud de Crédito</a></li>
            <li><a href="#tab-resultado-3" onclick="cualitativopdf_index()">Analisis Cualitativo</a></li>
            <li><a href="#tab-resultado-4" onclick="evaluacionpdf_index()">Hoja de Evaluación</a></li>
            <li><a href="#tab-resultado-6" onclick="garantiapdf_index()">Garantias</a></li>
            <li><a href="#tab-resultado-5" onclick="negociopdf_index()">Información de Negocio</a></li>
            <li><a href="#tab-resultado-7" onclick="domiciliopdf_index()">Información de Domicilio</a></li>
        </ul>
        <div class="tab">
            <div id="tab-resultado-1" class="tab-content" style="display: block;">
                          
                <div class="row">
                    <div class="col-md-6">
                        <div class="table-responsive">
                          <table class="table" id="tabla-contenidoproducto-resultado">
                              <tbody>
                                  <tr>
                                    <th style="background-color: #4a330a;color:#fff;padding: 10px;text-align: center;font-weight: bold;" colspan="2">ESTADO DE RESULTADOS</th>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;width:300px;text-align: right;"><b>INGRESO TOTAL</b></td>
                                    <td style="padding: 10px;background-color: orange;font-weight: bold;">
                                      {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                                      {{$prestamocredito_resultado['total_laboralingresototal']}}
                                    </td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;">(-) COSTO DE VENTAS</td>
                                    <td style="padding: 10px;background-color: #ffea00;font-weight: bold;">
                                      {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                                      {{$prestamocredito_resultado['total_laboralcompra']}}
                                    </td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;text-align: right;"><b>UTILIDAD BRUTA</b></td>
                                    <td style="padding: 10px;">
                                      {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                                      {{$prestamocredito_resultado['total_laboralutilidad_bruta']}}
                                    </td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;">(-) GASTOS OPERATIVOS</td>
                                    <td style="padding: 10px;">
                                      {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                                      {{$prestamocredito_resultado['total_laboralegresogasto']}}
                                    </td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;text-align: right;"><b>UTILIDAD OPERATIVA</b></td>
                                    <td style="padding: 10px;">
                                      {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                                      {{$prestamocredito_resultado['total_laboralutilidad_operativa']}}
                                    </td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;">(-) PAGO DE CUOTAS (BANCOS)</td>
                                    <td style="padding: 10px;">
                                      {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                                      {{$prestamocredito_resultado['total_laboralegresopago']}}
                                    </td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;text-align: right;"><b>UTILIDAD NETA</b></td>
                                    <td style="padding: 10px;">
                                      {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                                      {{$prestamocredito_resultado['total_laboralutilidad_neta']}}
                                    </td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;">(+) OTROS INGRESOS</td>
                                    <td style="padding: 10px;">
                                      {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                                      {{$prestamocredito_resultado['total_laboralotroingreso']}}
                                    </td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;">(-) OTROS GASTOS</td>
                                    <td style="padding: 10px;">
                                      {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                                      {{$prestamocredito_resultado['total_laboralotrogasto']}}
                                    </td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;">(-) GASTOS FAMILIARES</td>
                                    <td style="padding: 10px;">
                                      {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                                      {{$prestamocredito_resultado['total_laboralegresogastofamiliares']}}
                                    </td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;text-align: right;"><b>EXCEDENTE NETO MENSUAL</b></td>
                                    <td style="padding: 10px;background-color: #a26b07;color:#fff;">
                                      {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                                      {{$prestamocredito_resultado['total_laboralexcedentenetomensual']}}
                                    </td>
                                  </tr>
                            </tbody>
                          </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive">
                          <table class="table" id="tabla-contenidoproducto-resultado">
                              <tbody>
                                  <tr>
                                    <th style="background-color: #4a330a;color:#fff;padding: 10px;text-align: center;font-weight: bold;" colspan="2">CRÉDITO</th>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;width:200px;"><b>Monto Solicitado</b></td>
                                    <td style="padding: 10px;">
                                      {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                                      {{$prestamocredito_resultado['prestamocredito']->monto}}
                                    </td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;width:200px;"><b>Interes</b></td>
                                    <td style="padding: 10px;">
                                      {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                                      {{$prestamocredito_resultado['prestamocredito']->total_interes}}
                                    </td>
                                  </tr>
                                  @if($prestamocredito_resultado['prestamocredito']->total_segurodesgravamen>0)
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;width:200px;"><b>Seguro Desgravamen</b></td>
                                    <td style="padding: 10px;">
                                      {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                                      {{$prestamocredito_resultado['prestamocredito']->total_segurodesgravamen}}
                                    </td>
                                  </tr>
                                  @endif
                                  @if($prestamocredito_resultado['prestamocredito']->total_abono>0)
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;width:200px;"><b>Abono</b></td>
                                    <td style="padding: 10px;">
                                      {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                                      {{$prestamocredito_resultado['prestamocredito']->total_abono}}
                                    </td>
                                  </tr>
                                  @endif
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;width:200px;"><b>Monto a Pagar</b></td>
                                    <td style="padding: 10px;">
                                      {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                                      {{$prestamocredito_resultado['prestamocredito']->total_cuotafinal}}
                                    </td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>Número de Cuotas</b></td>
                                    <td style="padding: 10px;">{{$prestamocredito_resultado['prestamocredito']->numerocuota}}</td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>Frecuencia</b></td>
                                    <td style="padding: 10px;">{{$prestamocredito_resultado['prestamocredito']->frecuencia_nombre}}</td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>Cuota</b></td>
                                    <td style="padding: 10px;">
                                      {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                                      {{$prestamocredito_resultado['prestamocredito']->cuota}}
                                    </td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>Cuota Mensualizada</b></td>
                                    <td style="padding: 10px;background-color: #a26b07;color:#fff;">
                                      {{$prestamocredito_resultado['prestamocredito']->monedasimbolo}} 
                                      {{$prestamocredito_resultado['cuotamensualizada']}}
                                    </td>
                                  </tr>
                            </tbody>
                          </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="tab-resultado-2" class="tab-content" style="display: none;">
                <div id="cont-creditopdf"></div>
            </div>
            <div id="tab-resultado-3" class="tab-content" style="display: none;">
                <div id="cont-cualitativopdf"></div>
            </div>
            <div id="tab-resultado-4" class="tab-content" style="display: none;">
                <div id="cont-evaluacionpdf"></div>
            </div>
            <div id="tab-resultado-5" class="tab-content" style="display: none;">
                <div id="cont-negociopdf"></div>
            </div>
            <div id="tab-resultado-6" class="tab-content" style="display: none;">
                <div id="cont-garantiapdf"></div>
            </div>
            <div id="tab-resultado-7" class="tab-content" style="display: none;">
                <div id="cont-domiciliopdf"></div>
            </div>
        </div>
    </div>   

<style>
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
<script>
   function creditopdf_index(){
        $('#cont-creditopdf').html('<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/'.$prestamocredito->id.'/edit?view=creditopdf-pdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>')
   }
   function cualitativopdf_index(){
        $('#cont-cualitativopdf').html('<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/'.$prestamocredito->id.'/edit?view=cualitativopdf-pdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>')
   }
   function evaluacionpdf_index(){
        $('#cont-evaluacionpdf').html('<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/'.$prestamocredito->id.'/edit?view=evaluacionpdf-pdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>')
   }
   function negociopdf_index(){
        $('#cont-negociopdf').html('<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/'.$prestamocredito->id.'/edit?view=negociopdf-pdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>')
   }
   function garantiapdf_index(){
        $('#cont-garantiapdf').html('<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/'.$prestamocredito->id.'/edit?view=garantiapdf-pdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>')
   }
   function domiciliopdf_index(){
        $('#cont-domiciliopdf').html('<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/'.$prestamocredito->id.'/edit?view=domiciliopdf-pdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>')
   }
   @if($prestamocredito_resultado['resultado']=='APROBADO')
        var elem = document.getElementById('resultado-credito');
        elem.innerHTML = '<div class="resultado-aprobado">CRÉDITO APROBADO</div>';                                                
    @elseif($prestamocredito_resultado['resultado']=='DESAPROBADO')
        var elem = document.getElementById('resultado-credito');
        elem.innerHTML = '<div class="resultado-desaprobado">CRÉDITO DESAPROBADO</div>';
    @endif
</script>