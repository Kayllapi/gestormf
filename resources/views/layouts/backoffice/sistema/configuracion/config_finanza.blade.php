@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Configuración de Finanza',
    'botones'=>[
        'atras:/'.$tienda->id.'/configuracion/:Ir Atras'
    ]
])

<form action="javascript:;" 
      onsubmit="callback({
        route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion/0',
        method: 'PUT',
        data:   {
            view: 'config_finanza',
            prestamo_tipomovimiento: seleccinar_tipomovimiento()
        }
    },
    function(resultado){
        location.reload();                                                                        
    },this)">
  <div class="tabs-container" id="tab-modulo">
      <ul class="tabs-menu">
          <li class="current"><a href="#tab-modulo-1">Caja</a></li>
          <li><a href="#tab-modulo-2">Apertura de Caja</a></li>
          <li><a href="#tab-modulo-3">Cierre de Caja</a></li>
          <li><a href="#tab-modulo-4">Movimiento</a></li>
          @if($tienda->idcategoria==24 or $tienda->idcategoria==30 or $tienda->idcategoria==12)
          <li><a href="#tab-modulo-8">Compra</a></li>
          <li><a href="#tab-modulo-9">Devolución de Compra</a></li>
          <li><a href="#tab-modulo-10">Venta</a></li>
          <li><a href="#tab-modulo-11">Devolución de Venta</a></li>
          @elseif($tienda->idcategoria==13)
          <li><a href="#tab-modulo-5">Desembolso</a></li>
          <li><a href="#tab-modulo-6">Cobranza</a></li>
          <li><a href="#tab-modulo-7">Transferencia de saldo</a></li>
          @endif
      </ul>
      <div class="tab">
          <div id="tab-modulo-1" class="tab-content" style="display: block;">
          </div>
          <div id="tab-modulo-2" class="tab-content" style="display: none;">
              <div class="row">
                  <div class="col-sm-6">
                      <label>Moneda a usar</label>
                      <select id="sistema_moneda_usar">
                        <option></option>
                        <option value="1">Soles</option>
                        <option value="2">Dolares</option>
                        <option value="3">Soles y Dolares</option>
                      </select>
                      <div id="cont-monedapordefecto" style="display:none;">
                      <label>Moneda por defecto</label>
                      <select id="sistema_monedapordefecto">
                        <option></option>
                        <option value="1">Soles</option>
                        <option value="2">Dolares</option>
                      </select>
                      </div>
                  </div>
              </div>
          </div>
          <div id="tab-modulo-3" class="tab-content" style="display: none;">
              <div class="row">
                  <div class="col-sm-6">
                      <label>Tipo de Cierre</label>
                      <select id="prestamo_tipocierrecaja">
                        <option></option>
                        <option value="1">Detallado</option>
                        <option value="3">Billetaje</option>
                      </select>
                  </div>
              </div>
          </div>
          <div id="tab-modulo-4" class="tab-content" style="display: none;">
             <div class="row">
                 <div class="col-sm-6">
                     <label>Tipos de Movimientos</label>
                     <table class="table" id="tabla-tipomovimiento">
                         <thead class="thead-dark">
                           <tr>
                             <th>Tipo</th>
                             <th>Nombre</th>
                             <th width="10px" style="padding: 0px;padding-right: 1px;">
                             <a href="javascript:;" class="btn  color-bg flat-btn" onclick="tipomovimiento_agregar()"><i class="fa fa-plus"></i></a>
                             </th>
                           </tr>
                         </thead>
                         <tbody num="0"></tbody>
                     </table>
                 </div>
              </div>
          </div>
          @if($tienda->idcategoria==24 or $tienda->idcategoria==30 or $tienda->idcategoria==12)
          <div id="tab-modulo-8" class="tab-content" style="display: none;">
          </div>
          <div id="tab-modulo-9" class="tab-content" style="display: none;">
          </div>
          <div id="tab-modulo-10" class="tab-content" style="display: none;">
              <div class="row">
                <div class="col-sm-6">
                      <label>Tipo de entrega por Defecto</label>
                      <select id="sistema_tipoentregapordefecto">
                          <option></option>
                          @foreach($tipoentregas as $value)
                              <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                          @endforeach
                      </select>
                      <label>Nivel de Venta</label>
                      <select id="sistema_nivelventa">
                          <option></option>
                          <option value="1">1.- (Confirmar Pedido, Realizar Venta)</option>
                          <option value="2">2.- (Realizar Pedido, Confirmar Pedido, Realizar Venta)</option>
                      </select>
                      <label>Estado de Venta por Defecto</label>
                      <select id="sistema_estadoventa">
                          <option></option>
                      </select>
                      <label>Estado de Precio Unitario</label>
                      <select id="sistema_estadopreciounitario">
                          <option></option>
                          <option value="1">Habilitado</option>
                          <option value="2">Desabilitado</option>
                      </select>
                </div>
                <div class="col-sm-6">
                      <label>Estado de Descuento Total</label>
                      <select id="sistema_estadodescuentoventatotal">
                          <option></option>
                          <option value="1">Habilitado</option>
                          <option value="2">Desabilitado</option>
                      </select>
                      <label>Estado de Forma de Pago</label>
                      <select id="sistema_estadoformapago">
                          <option></option>
                          <option value="1">Habilitado</option>
                          <option value="2">Desabilitado</option>
                      </select>
                      <label>Mensaje adicional del Ticket</label>
                      <table class="table">
                              <tr>
                                  <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Mensaje 1</td>
                                  <td><input type="text" value="{{ configuracion($tienda->id,'sistema_mensajeadicionalticket_1')['valor'] }}" id="sistema_mensajeadicionalticket_1"></td>
                              </tr>
                              <tr>
                                  <td style="background-color: #eae7e7;padding: 10px;font-weight: bold;">Mensaje 2</td>
                                  <td><input type="text" value="{{ configuracion($tienda->id,'sistema_mensajeadicionalticket_2')['valor'] }}" id="sistema_mensajeadicionalticket_2"></td>
                              </tr>
                              <tr>
                                  <td style="background-color: #eae7e7;padding: 10px;font-weight: bold;">Mensaje 3</td>
                                  <td><input type="text" value="{{ configuracion($tienda->id,'sistema_mensajeadicionalticket_3')['valor'] }}" id="sistema_mensajeadicionalticket_3"></td>
                              </tr>
                      </table>
                </div>
              </div>
          </div>
          <div id="tab-modulo-11" class="tab-content" style="display: none;">
          </div>
          @elseif($tienda->idcategoria==13)
          <div id="tab-modulo-5" class="tab-content" style="display: none;">
              <div class="tabs-container" id="tab-modulodesembolso">
                  <ul class="tabs-menu">
                      <li class="current"><a href="#tab-modulodesembolso-1">Tarjeta de Pago</a></li>
                      <li><a href="#tab-modulodesembolso-2">Gasto Administrativo</a></li>
                      <li><a href="#tab-modulodesembolso-3">Documentos</a></li>
                  </ul>
                  <div class="tab">
                      <div id="tab-modulodesembolso-1" class="tab-content" style="display: block;">
                          <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label>Ubicación de Logo</label>
                                        <select id="prestamo_tarjetapago_ubicacionlogo">
                                          <option></option>
                                          <option value="1">Izquierda</option>
                                          <option value="2">Derecha</option>
                                          <option value="3">Centro</option>
                                        </select>
                                        <label>Ancho de Impresión (cm)</label>
                                        <input type="text" value="{{ configuracion($tienda->id,'prestamo_tarjetapago_anchoimpresion')['valor'] }}" id="prestamo_tarjetapago_anchoimpresion">
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Mensaje adicional del Ticket</label>
                                        <table class="table">
                                                <tr>
                                                    <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Mensaje 1</td>
                                                    <td><input type="text" value="{{ configuracion($tienda->id,'prestamo_tarjetapago_mensajeadicionalticket_1')['valor'] }}" id="prestamo_tarjetapago_mensajeadicionalticket_1"></td>
                                                </tr>
                                                <tr>
                                                    <td style="background-color: #eae7e7;padding: 10px;font-weight: bold;">Mensaje 2</td>
                                                    <td><input type="text" value="{{ configuracion($tienda->id,'prestamo_tarjetapago_mensajeadicionalticket_2')['valor'] }}" id="prestamo_tarjetapago_mensajeadicionalticket_2"></td>
                                                </tr>
                                                <tr>
                                                    <td style="background-color: #eae7e7;padding: 10px;font-weight: bold;">Mensaje 3</td>
                                                    <td><input type="text" value="{{ configuracion($tienda->id,'prestamo_tarjetapago_mensajeadicionalticket_3')['valor'] }}" id="prestamo_tarjetapago_mensajeadicionalticket_3"></td>
                                                </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                          </div>
                      </div>
                      <div id="tab-modulodesembolso-2" class="tab-content" style="display: none;">
                          <div class="mensaje-info">
                             <i class="fa fa-warning"></i> ¿Esta seguro de activar los "Gastos Administrativos"</b>?
                           </div>
                           <div style="width: 100%;text-align: center;float: left;margin-bottom: 5px;">
                           <div class="onoffswitch" style="margin: auto;">
                               <input type="checkbox" class="onoffswitch-checkbox prestamo_estadogasto_administrativo" id="prestamo_estadogasto_administrativo"  <?php echo configuracion($tienda->id,'prestamo_estadogasto_administrativo')['valor']=='on'?'checked':''?>>
                               <label class="onoffswitch-label" for="prestamo_estadogasto_administrativo">
                               <span class="onoffswitch-inner"></span>
                               <span class="onoffswitch-switch"></span>
                               </label>
                           </div>
                           </div>
                           <div id="cont-prestamo_estadogasto_administrativo" <?php echo configuracion($tienda->id,'prestamo_estadogasto_administrativo')['valor']=='on'?'':'style="display:none;"'?>>
                              <div class="row">
                                  <div class="col-sm-12">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td style="background-color: #31353c;padding: 10px;width: 50%;font-weight: bold;color: #fff;">Rango de Crédito</td>
                                                <td style="background-color: #31353c;padding: 10px;width: 50%;font-weight: bold;color: #fff;">Monto</td>
                                            </tr>
                                            <tr>
                                                <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">De S/ 0 - S/ 500.00</td>
                                                <td><input type="number" value="{{ configuracion($tienda->id,'prestamo_gasto_administrativo_uno')['valor'] }}" id="prestamo_gasto_administrativo_uno" step="0.01"></td>
                                            </tr>
                                            <tr>
                                                <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">De S/ 500.01 - S/ 1000.00</td>
                                                <td><input type="number" value="{{ configuracion($tienda->id,'prestamo_gasto_administrativo_dos')['valor'] }}" id="prestamo_gasto_administrativo_dos" step="0.01"></td>
                                            </tr>
                                            <tr>
                                                <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">De S/ 1000.01 - S/ 2000.00</td>
                                                <td><input type="number" value="{{ configuracion($tienda->id,'prestamo_gasto_administrativo_tres')['valor'] }}" id="prestamo_gasto_administrativo_tres" step="0.01"></td>
                                            </tr>
                                            <tr>
                                                <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">De S/ 2000.01 - S/ 5000.00</td>
                                                <td><input type="number" value="{{ configuracion($tienda->id,'prestamo_gasto_administrativo_cuatro')['valor'] }}" id="prestamo_gasto_administrativo_cuatro" step="0.01"></td>
                                            </tr>
                                            <tr>
                                                <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">De S/ 5000.01 a más</td>
                                                <td><input type="number" value="{{ configuracion($tienda->id,'prestamo_gasto_administrativo_cinco')['valor'] }}" id="prestamo_gasto_administrativo_cinco" step="0.01"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div id="tab-modulodesembolso-3" class="tab-content" style="display: none;">
                          <div id="cont-tabla-documento">
                              <div class="list-single-main-wrapper fl-wrap">
                                  <div class="breadcrumbs gradient-bg fl-wrap">
                                    <span>Documentos</span>
                                    <a class="btn btn-warning" href="javascript:;" onclick="registrar_documento({{ $tienda->id }})"><i class="fa fa-angle-right"></i> Registrar</a></a>
                                  </div>
                              </div>
                              @include('app.sistema.tabla',[
                                  'tabla' => 'tabla-documentos',
                                  'script' => 'scriptsapp3',
                                  'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/configuracion/show-indexdocumento'),
                                  'thead' => [
                                      ['data' => 'Nombre'],
                                      ['data' => 'Mostrar'],
                                      ['data' => '', 'width' => '10px']
                                  ],
                                  'tbody' => [
                                      ['data' => 'nombre'],
                                      ['data' => 'mostrar'],
                                      ['render' => 'opcion']
                                  ],
                                  'tfoot' => [
                                      ['input' => ''],
                                      ['input' => ''],
                                      ['input' => '']
                                  ]
                              ])
                          </div>
                          <div id="cont-resultado-documento"></div>
                      </div>
                  </div>
              </div>
          </div>
          <div id="tab-modulo-6" class="tab-content" style="display: none;">
              <div class="row">
                  <div class="col-sm-6">
                      <label>Estado de Descuento de Interes</label>
                      <select id="prestamo_estadodescuentointeres">
                        <option></option>
                        <option value="1">Habilitado</option>
                        <option value="2">Bloqueado</option>
                      </select>
                      <label>Redondeo de Efectivo</label>
                      <select id="prestamo_redondeoefectivo">
                        <option></option>
                        <option value="1">Al Menor (Ej: 10.08 = 10.00)</option>
                        <option value="2">Normal (Ej: 10.08 = 11.00)</option>
                        <option value="3">Al Mayor (Ej: 10.02 = 11.00)</option>
                      </select>
                      <label>Formato de Ticket</label>
                      <select id="prestamo_formatoticket">
                        <option></option>
                        <option value="1">Detallado</option>
                        <option value="2">Resumido</option>
                      </select>
                  </div>
                  <div class="col-sm-6">
                      <label>Mensaje adicional del Ticket</label>
                      <table class="table">
                              <tr>
                                  <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Mensaje 1</td>
                                  <td><input type="text" value="{{ configuracion($tienda->id,'prestamo_mensajeadicionalticket_1')['valor'] }}" id="prestamo_mensajeadicionalticket_1"></td>
                              </tr>
                              <tr>
                                  <td style="background-color: #eae7e7;padding: 10px;font-weight: bold;">Mensaje 2</td>
                                  <td><input type="text" value="{{ configuracion($tienda->id,'prestamo_mensajeadicionalticket_2')['valor'] }}" id="prestamo_mensajeadicionalticket_2"></td>
                              </tr>
                              <tr>
                                  <td style="background-color: #eae7e7;padding: 10px;font-weight: bold;">Mensaje 3</td>
                                  <td><input type="text" value="{{ configuracion($tienda->id,'prestamo_mensajeadicionalticket_3')['valor'] }}" id="prestamo_mensajeadicionalticket_3"></td>
                              </tr>
                      </table>
                  </div>
              </div>
          </div>
          @endif
          <div id="tab-modulo-7" class="tab-content" style="display: none;">
          </div>
      </div>
  </div>
  <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>

@endsection
@section('subscripts')
<!-- tab -->
<script>
  tab({click:'#tab-modulo'});
  tab({click:'#tab-modulodesembolso'});
</script>
<!-- Caja -->
<script>
</script>
<!-- Apertura de Caja -->
<script>
  @if(configuracion($tienda->id,'sistema_moneda_usar')['resultado']=='CORRECTO')
      $("#sistema_moneda_usar").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      }).on("change", function (e) {
          $('#cont-monedapordefecto').css('display','none');
          if (e.currentTarget.value == 3) {
              $('#cont-monedapordefecto').css('display','block');
          }
      }).val({{ configuracion($tienda->id,'sistema_moneda_usar')['valor'] }}).trigger("change");    
  @else
      $("#sistema_moneda_usar").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      }).on("change", function (e) {
          $('#cont-monedapordefecto').css('display','none');
          if (e.currentTarget.value == 3) {
              $('#cont-monedapordefecto').css('display','block');
          }
      });
  @endif
  @if(configuracion($tienda->id,'sistema_monedapordefecto')['resultado']=='CORRECTO')
      $("#sistema_monedapordefecto").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      }).val({{ configuracion($tienda->id,'sistema_monedapordefecto')['valor'] }}).trigger("change");    
  @else
      $("#sistema_monedapordefecto").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      });
  @endif
</script>
<!-- Cierre de Caja -->
<script>
  @if(configuracion($tienda->id,'prestamo_tipocierrecaja')['resultado']=='CORRECTO')
      $("#prestamo_tipocierrecaja").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      }).val({{ configuracion($tienda->id,'prestamo_tipocierrecaja')['valor'] }}).trigger("change");    
  @else
      $("#prestamo_tipocierrecaja").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      });
  @endif
</script>
<!-- Movimiento -->
<script>
  @if(configuracion($tienda->id,'prestamo_tipomovimiento')['resultado']=='CORRECTO')
  <?php 
    $prestamo_tipomovimiento = json_decode(configuracion($tienda->id,'prestamo_tipomovimiento')['valor']);
    usort($prestamo_tipomovimiento,  function($a, $b) { return strcmp($a->tipomovimiento, $b->tipomovimiento); });
  ?>
  @foreach($prestamo_tipomovimiento as $value)
      tipomovimiento_agregar('{{$value->tipomovimiento}}','{{$value->tipomovimientonombre}}');
  @endforeach
  @endif
  function seleccinar_tipomovimiento(){
     var data = [];
     $("#tabla-tipomovimiento > tbody > tr").each(function() {
          var num = $(this).attr('id');  
          data.push({
            tipomovimiento: $("#tipomovimiento"+num).val(),
            tipomovimientonombre: $("#tipomovimientonombre"+num).val(),
          });
      });
      if(data.length==0){
          return '';
      }else{
          return JSON.stringify(data);
      }
  }
  function tipomovimiento_agregar(tipomovimiento='',tipomovimientonombre=''){
      var num = $("#tabla-tipomovimiento > tbody").attr('num');
      $('#tabla-tipomovimiento > tbody').append('<tr id="'+num+'">'+
                                                   '<td class="mx-td-input"><select id="tipomovimiento'+num+'">'+
                                                       '<option></option>'+
                                                       '<option value="INGRESO">INGRESO</option>'+
                                                       '<option value="EGRESO">EGRESO</option>'+
                                                   '</select></td>'+
                                                   '<td class="mx-td-input"><input id="tipomovimientonombre'+num+'" type="text" value="'+tipomovimientonombre+'" onkeyup="texto_mayucula(this)"></td>'+
                                                   '<td class="mx-td-input"><a id="del'+num+'" href="javascript:;" onclick="eliminartipomovimiento('+num+')" class="btn btn-danger big-btn" style="padding: 12px 15px;"><i class="fa fa-close"></i></a></td>'+
                                               '</tr>');
      $("#tabla-tipomovimiento > tbody").attr('num',parseInt(num)+1);
    
      if(tipomovimiento!=''){
          $("#tipomovimiento"+num).select2({
              placeholder: "--  Seleccionar --",
              minimumResultsForSearch: -1
          }).val(tipomovimiento).trigger("change");
      }else{
          $("#tipomovimiento"+num).select2({
              placeholder: "--  Seleccionar --",
              minimumResultsForSearch: -1
          });
      }
          
  }
  function eliminartipomovimiento(num){
    $("#tabla-tipomovimiento > tbody > tr#"+num).remove();
  }
</script>
@if($tienda->idcategoria==13)
<!-- Desembolso -->
<script>
  $("#prestamo_estadogasto_administrativo").click(function(){
      $('#cont-prestamo_estadogasto_administrativo').css('display','none');
      var checked = $("#prestamo_estadogasto_administrativo:checked").val();
      if(checked=='on'){
          $('#cont-prestamo_estadogasto_administrativo').css('display','block');
      }
  });
  
  @if(configuracion($tienda->id,'prestamo_tarjetapago_ubicacionlogo')['resultado']=='CORRECTO')
      $("#prestamo_tarjetapago_ubicacionlogo").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      }).val({{ configuracion($tienda->id,'prestamo_tarjetapago_ubicacionlogo')['valor'] }}).trigger("change");    
  @else
      $("#prestamo_tarjetapago_ubicacionlogo").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      });
  @endif
  
  function index_documento() {
    $('#tabla-documentos').DataTable().ajax.reload();
    $('#cont-tabla-documento').css('display','block');
    $('#cont-resultado-documento').html('');
  }
  function registrar_documento(idtienda) {
      $('#cont-tabla-documento').css('display','none');
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/'+idtienda+'/configuracion/create?view=prestamo_registrardocumento',result:'#cont-resultado-documento'});
  }
  function editar_documento(idtienda,iddocumento) {
      $('#cont-tabla-documento').css('display','none');
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/'+idtienda+'/configuracion/'+iddocumento+'/edit?view=prestamo_editardocumento',result:'#cont-resultado-documento'});
  }
  function eliminar_documento(idtienda,iddocumento) {
      $('#cont-tabla-documento').css('display','none');
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/'+idtienda+'/configuracion/'+iddocumento+'/edit?view=prestamo_eliminardocumento',result:'#cont-resultado-documento'});
  }
</script>
<!-- Cobranza -->
<script>
  @if(configuracion($tienda->id,'prestamo_estadodescuentointeres')['resultado']=='CORRECTO')
      $("#prestamo_estadodescuentointeres").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      }).val({{ configuracion($tienda->id,'prestamo_estadodescuentointeres')['valor'] }}).trigger("change");    
  @else
      $("#prestamo_estadodescuentointeres").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      });
  @endif
  @if(configuracion($tienda->id,'prestamo_formatoticket')['resultado']=='CORRECTO')
      $("#prestamo_formatoticket").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      }).val({{ configuracion($tienda->id,'prestamo_formatoticket')['valor'] }}).trigger("change");    
  @else
      $("#prestamo_formatoticket").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      });
  @endif
  @if(configuracion($tienda->id,'prestamo_redondeoefectivo')['resultado']=='CORRECTO')
      $("#prestamo_redondeoefectivo").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      }).val({{ configuracion($tienda->id,'prestamo_redondeoefectivo')['valor'] }}).trigger("change");    
  @else
      $("#prestamo_redondeoefectivo").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      });
  @endif
</script>
@elseif($tienda->idcategoria==24 or $tienda->idcategoria==30 or $tienda->idcategoria==12)
<!-- Compra -->
<script>
</script>
<!-- Devolución de Compra -->
<script>
</script>
<!-- Venta -->
<script>
    @if(configuracion($tienda->id,'sistema_tipoentregapordefecto')['resultado']=='CORRECTO')
        $("#sistema_tipoentregapordefecto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        }).on("change", function(e) {
            $('#cont-tipo-delivery-info').css('display','none');
            $('#cont-tipo-delivery-mapa').css('display','none');
            $('#cont-costoenvio').css('display','none');
            if(e.currentTarget.value == 2) {
                $('#cont-tipo-delivery-info').css('display','block');
                $('#cont-tipo-delivery-mapa').css('display','block');
                $('#cont-costoenvio').css('display','block');
            }
        }).val({{ configuracion($tienda->id,'sistema_tipoentregapordefecto')['valor'] }}).trigger("change");   
    @else
        $("#sistema_tipoentregapordefecto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        }).on("change", function(e) {
            $('#cont-tipo-delivery-info').css('display','none');
            $('#cont-tipo-delivery-mapa').css('display','none');
            $('#cont-costoenvio').css('display','none');
            if(e.currentTarget.value == 2) {
                $('#cont-tipo-delivery-info').css('display','block');
                $('#cont-tipo-delivery-mapa').css('display','block');
                $('#cont-costoenvio').css('display','block');
            }
        });
    @endif
  
    @if(configuracion($tienda->id,'sistema_nivelventa')['resultado']=='CORRECTO')
        $("#sistema_nivelventa").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        }).on("change", function(e) {
            if(e.currentTarget.value==1){
                $('#sistema_estadoventa').html('<option></option><option value="2">Confirmar Pedido</option><option value="3">Realizar Venta</option>');
            }else if(e.currentTarget.value==2){
                $('#sistema_estadoventa').html('<option></option><option value="1">Realizar Pedido</option><option value="2">Confirmar Pedido</option>');
            }
        }).val({{ configuracion($tienda->id,'sistema_nivelventa')['valor'] }}).trigger("change");   
    @else
        $("#sistema_nivelventa").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        }).on("change", function(e) {
            if(e.currentTarget.value==1){
                $('#sistema_estadoventa').html('<option></option><option value="2">Confirmar Pedido</option><option value="3">Realizar Venta</option>');
            }else if(e.currentTarget.value==2){
                $('#sistema_estadoventa').html('<option></option><option value="1">Realizar Pedido</option><option value="2">Confirmar Pedido</option>');
            }
        });
    @endif  
  
    @if(configuracion($tienda->id,'sistema_estadoventa')['resultado']=='CORRECTO')
        $("#sistema_estadoventa").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        }).val({{ configuracion($tienda->id,'sistema_estadoventa')['valor'] }}).trigger("change");   
    @else
        $("#sistema_estadoventa").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        });
    @endif
  
    @if(configuracion($tienda->id,'sistema_estadopreciounitario')['resultado']=='CORRECTO')
        $("#sistema_estadopreciounitario").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        }).val({{ configuracion($tienda->id,'sistema_estadopreciounitario')['valor'] }}).trigger("change");   
    @else
        $("#sistema_estadopreciounitario").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        });
    @endif
    @if(configuracion($tienda->id,'sistema_estadodescuentoventatotal')['resultado']=='CORRECTO')
        $("#sistema_estadodescuentoventatotal").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        }).val({{ configuracion($tienda->id,'sistema_estadodescuentoventatotal')['valor'] }}).trigger("change");   
    @else
        $("#sistema_estadodescuentoventatotal").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        });
    @endif
    @if(configuracion($tienda->id,'sistema_estadoformapago')['resultado']=='CORRECTO')
        $("#sistema_estadoformapago").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        }).val({{ configuracion($tienda->id,'sistema_estadoformapago')['valor'] }}).trigger("change");   
    @else
        $("#sistema_estadoformapago").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        });
    @endif
</script>
<!-- Devolución de Venta -->
<script>
</script>
@endif
<!-- Transferencia de saldo -->
<script>
</script>
@if($tienda->idcategoria==13)
<script src="{{url('/public/libraries/ckeditor/ckeditor.js')}}"></script>
@endif
@endsection