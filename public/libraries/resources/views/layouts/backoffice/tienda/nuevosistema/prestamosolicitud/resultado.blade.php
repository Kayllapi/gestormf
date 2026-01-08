 <div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Resultado</span>
    </div>
</div>    
    <div class="tabs-container" id="tab-resultado">
        <ul class="tabs-menu">
            <li class="current"><a href="#tab-resultado-0">General</a></li>
            <li><a href="#tab-resultado-2">PDF</a></li>
            <li><a href="#tab-resultado-3">CUALITATIVO</a></li>
        </ul>
        <div class="tab">
            <div id="tab-resultado-0" class="tab-content" style="display: block;">
                          
                <div class="row">
                    <div class="col-md-6">
                        <div class="table-responsive">
                          <table class="table" id="tabla-contenidoproducto-resultado">
                              <tbody>
                                  <tr>
                                    <th style="background-color: #c2c0c0;padding: 10px;text-align: center;font-weight: bold;" colspan="2">RESULTADO</th>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;width:200px;"><b>(+) Ventas</b></td>
                                    <td style="padding: 10px;">{{$total_laboralventa}}</td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;width:200px;"><b>(+) Ingresos</b></td>
                                    <td style="padding: 10px;">{{$total_laboralingreso}}</td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>(+) Servicios</b></td>
                                    <td style="padding: 10px;">{{$total_laboralservicio}}</td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>(-) Gastos</b></td>
                                    <td style="padding: 10px;">{{$total_laboralegresogasto}}</td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>(-) Compras</b></td>
                                    <td style="padding: 10px;">{{$total_laboralcompra}}</td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>(-) Pagos</b></td>
                                    <td style="padding: 10px;">{{$total_laboralegresopago}}</td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>Excedente Neto Mensual</b></td>
                                    <td style="padding: 10px;background-color: #f1c40f;">{{$ingreso}}</td>
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
                                    <th style="background-color: #c2c0c0;padding: 10px;text-align: center;font-weight: bold;" colspan="2">CRÉDITO</th>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;width:200px;"><b>Monto Solicitado</b></td>
                                    <td style="padding: 10px;">{{$prestamocredito->monto}}</td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>Número de Cuotas</b></td>
                                    <td style="padding: 10px;">{{$prestamocredito->numerocuota}}</td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>Frecuencia</b></td>
                                    <td style="padding: 10px;">{{$prestamocredito->frecuencia_nombre}}</td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>Cuota Mensualizada</b></td>
                                    <td style="padding: 10px;background-color: #008cea;">{{$prestamocredito->total_cuotafinal}}</td>
                                  </tr>
                            </tbody>
                          </table>
                        </div>
                    </div>
                    <div class="col-md-12">
                        @if($prestamocredito->total_cuotafinal<$ingreso)
                            <div class="resultado-aprobado">
                                Aprobado
                            </div>                                                     
                        @else
                            <div class="resultado-desaprobado">
                                Desaprobado
                            </div>    
                        @endif
                    </div>
                </div>
            </div>
            <div id="tab-resultado-2" class="tab-content" style="display: none;">
                <div id="cont-creditopdf"></div>
            </div>
            <div id="tab-resultado-3" class="tab-content" style="display: none;">
                <div id="cont-cualitativopdf"></div>
            </div>
        </div>
    </div>             
<style>
  .resultado-aprobado {
    background-color: #179a4f;
    padding: 10px;
    border-radius: 5px;
    color: rgb(255 255 255);
    font-weight: bold;
    font-size: 30px;
    margin: 12px 0;
  }
  .resultado-desaprobado {
    background-color: #8c1329;
    padding: 10px;
    border-radius: 5px;
    color: rgb(255 255 255);
    font-weight: bold;
    font-size: 30px;
    margin: 12px 0;
  }
</style>

<script>
    tab({click:'#tab-resultado'});
  
    creditopdf_index();
    function creditopdf_index(){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=creditopdf',result:'#cont-creditopdf'});
    }
    
    cualitativopdf_index();
    function cualitativopdf_index(){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=cualitativopdf',result:'#cont-cualitativopdf'});
    }
</script>