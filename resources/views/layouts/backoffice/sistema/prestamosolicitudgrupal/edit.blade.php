@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Editar Crédito Grupal',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamosolicitudgrupal: Ir Atras'
    ]
])

<form action="javascript:;"
      class="form-guardar_creditoexpediente"
      onsubmit="callback({
                  route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitudgrupal/{{$prestamocreditogrupal->id}}',
                  method: 'PUT',
                  data:   {
                      view: 'editar',
                      clientes: selectclientes(),
                  }
              },
              function(resultado){
                  removecarga({input:'#carga-credito'});
                  //location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitudgrupal') }}';
              },this)">
      <div class="tabs-container" id="tab-detalle-cliente">
        <ul class="tabs-menu">
              <li class="current"><a href="#tab-detalle-cliente-0">Cronograma</a></li>
              <li><a href="#tab-detalle-cliente-5">Integrantes</a></li>
        </ul>
        <div class="tab">
              <div id="tab-detalle-cliente-0" class="tab-content" style="display: block;">
                      <div class="row">
                        <div class="col-sm-6">
                            <label>Nombre de Grupo *</label>
                            <input type="text" value="{{$prestamocreditogrupal->nombre}}" id="nombregrupo" onkeyup="texto_mayucula(this)"/>
                            <div class="list-single-main-wrapper fl-wrap">
                                <div class="breadcrumbs gradient-bg fl-wrap">
                                  <span>Crédito</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <label>Monto *</label>
                                    <input type="number" id="monto" min="0" step="0.01" onkeyup="creditoCalendario()" onclick="creditoCalendario()" disabled/>
                                    <label>Número de Cuotas *</label>
                                    <input type="number" value="{{$prestamocreditogrupal->numerocuota}}" id="numerocuota" min="1" step="1" onkeyup="creditoCalendario()" onclick="creditoCalendario()"/>
                                    <label>Fecha de Inicio *</label>
                                    <input type="date" value="{{$prestamocreditogrupal->fechainiciocero}}" id="fechainicio" onchange="creditoCalendario()" onclick="creditoCalendario()"/>
                                    <label>Frecuencia *</label>
                                    <select id="idfrecuencia" onchange="creditoCalendario()">
                                        <option></option>
                                        @foreach($frecuencias as $value)
                                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <div id="cont-numerodias" style="display: none">
                                        <label>Número de Días *</label>
                                        <input type="number" value="{{$prestamocreditogrupal->numerodias}}" id="numerodias" value="0" min="0" step="1" onkeyup="creditoCalendario()" onclick="creditoCalendario()"/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label>Tipo de Interes</label>
                                    <select id="idtipotasa" disabled>
                                        <option value="1">INTERES FIJA</option>
                                        <option value="2">INTERES EFECTIVA</option>
                                    </select>
                                    <label>Interes % *</label>
                                    <input type="number" value="{{$prestamocreditogrupal->tasa}}" id="tasa" min="0" step="0.001" onclick="creditoCalendario()" onkeyup="creditoCalendario()"/>
                                    @if(configuracion($tienda->id,'prestamo_estadoabono')['valor']=='on')
                                    <label>Abono *</label>
                                    <input type="number" id="abono" value="{{$prestamocreditogrupal->total_abono}}" min="0" step="0.01" onclick="creditoCalendario()" onkeyup="creditoCalendario()"/>
                                    @endif
                                    <label>Interes Total</label>
                                    <input type="text" id="total_interes" value="0.00" disabled/>
                                    @if(configuracion($tienda->id,'prestamo_estadoseguro_degravamen')['valor']=='on')
                                    <label>Seguro Desgravamen</label>
                                    <input type="text" id="total_segurodesgravamen" value="0.00" disabled/>
                                    @endif
                                    <label>Total a Pagar</label>
                                    <input type="text" id="total_cuotafinal" value="0.00" disabled/>
                                </div>
                                <div class="col-md-4">
                                    <label>Excluir Días:</label>

                                    <table style="width: 100%;">
                                      <tr>
                                        <td style="text-align: right;padding: 10px;font-weight: bold;">Sábados</td>
                                        <td>
                                          <div class="onoffswitch">
                                              <input type="checkbox" class="onoffswitch-checkbox excluirsabado" id="excluirsabado" onclick="creditoCalendario()">
                                              <label class="onoffswitch-label" for="excluirsabado">
                                                  <span class="onoffswitch-inner"></span>
                                                  <span class="onoffswitch-switch"></span>
                                              </label> 
                                          </div>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td style="text-align: right;padding: 10px;font-weight: bold;">Domingos</td>
                                        <td>
                                          <div class="onoffswitch">
                                              <input type="checkbox" class="onoffswitch-checkbox excluirdomingo" id="excluirdomingo" onclick="creditoCalendario()">
                                              <label class="onoffswitch-label" for="excluirdomingo">
                                                  <span class="onoffswitch-inner"></span>
                                                  <span class="onoffswitch-switch"></span>
                                              </label> 
                                          </div>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td style="text-align: right;padding: 10px;font-weight: bold;">Feriados</td>
                                        <td>
                                          <div class="onoffswitch">
                                              <input type="checkbox" class="onoffswitch-checkbox excluirferiado" id="excluirferiado" onclick="creditoCalendario()">
                                              <label class="onoffswitch-label" for="excluirferiado">
                                                  <span class="onoffswitch-inner"></span>
                                                  <span class="onoffswitch-switch"></span>
                                              </label> 
                                          </div>
                                        </td>
                                      </tr>
                                    </table>
                                </div>
                            </div> 
                        </div>
                        <div class="col-sm-6">
                          <div id="cont-load-creditocalendario"></div>
                        </div>
                      </div>
              </div>
              <div id="tab-detalle-cliente-5" class="tab-content" style="display: none;">
                        <label>Cliente</label>
                        <div class="row">
                          <div class="col-md-12">
                              <select id="idcliente">
                                  <option></option>
                              </select>
                          </div>
                        </div>
                        <div id="cont-creditocliente"></div>
                        <div class="table-responsive" style="float: left;">
                            <table class="table" id="tabla-contenidocliente" style="width:100%;">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Desembolso</th>
                                        <th>Comité</th>
                                        <th width="10px"></th>
                                    </tr>
                                </thead>
                                <tbody num="0">
                                </tbody>
                            </table>
                        </div>
              </div>
        </div>
      </div>  
  <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>   
@endsection

@section('subscripts')
<!-- Tabulador de pestañas -->
<script>
    tab({click:'#tab-detalle-cliente'});
</script>

<!-- Detalle de Credito y Cronograma -->
<script>
  
  @if ($prestamocreditogrupal->excluirsabado == "on")
      $('#excluirsabado').prop("checked", true);
  @endif
  @if ($prestamocreditogrupal->excluirdomingo == "on")
      $('#excluirdomingo').prop("checked", true);
  @endif
  @if ($prestamocreditogrupal->excluirferiado == "on")
      $('#excluirferiado').prop("checked", true);
  @endif

    $('#idfrecuencia').select2({
        placeholder: '-- Seleccionar Frecuencia --',
        minimumResultsForSearch: -1,
    });
    $('#idtipotasa').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1,
    }).val({{configuracion($tienda->id,'prestamo_tasapordefecto')['valor']!=''?configuracion($tienda->id,'prestamo_tasapordefecto')['valor']:1}}).trigger("change");
    $('#idtasa').select2({
        placeholder: '-- Seleccionar Tasa --',
        minimumResultsForSearch: -1,
    }).val({{ $prestamocreditogrupal->idprestamo_tipotasa }}).trigger('change');
  
  $('#idcliente').select2({
        @if(modulo($tienda->id,Auth::user()->id,'cobranza_listarporasesor')['resultado']=='CORRECTO')
              @include('app.prestamo_select2_creditocliente',['idestadocredito' => 1,'idprestamo_estadocredito' => 2,'idasesor' => Auth::user()->id])
        @else
              @include('app.prestamo_select2_creditocliente',['idestadocredito' => 1,'idprestamo_estadocredito' => 2])
        @endif
    }).on("change", function(e) {
        load('#cont-creditocliente');
        $.ajax({
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitudgrupal/show-creditocliente')}}",
            type:'GET',
            data: {
                idcredito : e.currentTarget.value
            },
            success: function (respuesta){
                $('#cont-creditocliente').html('');
                agregarcliente(respuesta['prestamocredito'].id,respuesta['prestamocredito'].idcliente,respuesta['prestamocredito'].clienteidentificacion+' - '+respuesta['prestamocredito'].clienteapellidomaterno+' '+respuesta['prestamocredito'].clienteapellidopaterno+', '+respuesta['prestamocredito'].clientenombre,respuesta['prestamocredito'].monto);
            }
        })
    });
  

  @if($prestamocreditogrupal->idprestamo_frecuencia!=0)
  $("#idfrecuencia").change(function(){
      var frecuencia = $('#idfrecuencia').val();
      $('#numerodias').val('{{$prestamocreditogrupal->numerodias}}');
      $('#cont-numerodias').css('display', 'none');
      if (frecuencia == 5) {
          $('#cont-numerodias').css({'display':'block'});
      }
  }).val({{ $prestamocreditogrupal->idprestamo_frecuencia }}).trigger('change');
  @else
  $("#idfrecuencia").change(function(){
      var frecuencia = $('#idfrecuencia').val();
      $('#numerodias').val('{{$prestamocreditogrupal->numerodias}}');
      $('#cont-numerodias').css('display', 'none');
      if (frecuencia == 5) {
          $('#cont-numerodias').css({'display':'block'});
      }
  });
  @endif

  function creditoCalendario() {
      var monto = $('#monto').val();
      var numerocuota = $('#numerocuota').val();
      var fechainicio = $('#fechainicio').val();
      var frecuencia = $('#idfrecuencia').val();
      var numerodias = $('#numerodias').val();
      var tasa = $('#tasa').val();
      var abono = $('#abono').val();

      if(abono==undefined){
            abono = 0;
      }
    
      if(monto=='' || numerocuota=='' || fechainicio=='' || frecuencia=='' || tasa==''){
          return false;
      }
      else if(frecuencia==5 && numerodias==''){
          return false;
      }
      else if(frecuencia==5 && numerodias==0){
          return false;
      }

      $.ajax({
          url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitudgrupal/show-creditocalendario') }}",
          type: 'GET',
          data: {
              monto: monto,
              numerocuota: numerocuota,
              fechainicio: fechainicio,
              frecuencia: frecuencia,
              numerodias: numerodias,
              tasa: tasa,
              abono: abono,
              excluirsabado: $('#excluirsabado:checked').val(),
              excluirdomingo: $('#excluirdomingo:checked').val(),
              excluirferiado: $('#excluirferiado:checked').val(),
          },
          beforeSend: function (data) {
              load('#cont-load-creditocalendario');
          },
          success: function (res) {
              $('#total_interes').val(res['total_interes']);
              $('#total_segurodesgravamen').val(res['total_segurodesgravamen']);
              $('#total_cuotafinal').val(res['total_cuotafinal']);
              $('#cont-load-creditocalendario').html(res['html']);
          }
      });
  }
  
  @foreach($prestamo_creditos as $value)
      agregarcliente('{{$value->id}}','{{$value->idcliente}}','{{$value->clienteidentificacion}} - {{$value->cliente}}','{{$value->monto}}','{{$value->idprestamo_comite}}');
  @endforeach
  
  function agregarcliente(idcredito,idcliente,cliente,desembolso,idcomite=''){

      var num = $("#tabla-contenidocliente > tbody").attr('num');
  
      var nuevaFila='<tr id="'+num+'" idcredito="'+idcredito+'" idcliente="'+idcliente+'" desembolso="'+desembolso+'">';
          nuevaFila+='<td>'+cliente+'</td>';
          nuevaFila+='<td>'+desembolso+'</td>';   
          nuevaFila+='<td><select id="idcomite'+num+'">'+
                                  '<option></option>'+
                                  '<option value="1">PRESIDENTA</option>'+
                                  '<option value="2">SECRETARIA</option>'+
                                  '<option value="3">TESESORERO(A)</option>'+
                              '</select></td>';
          nuevaFila+='<td><a id="del'+num+'" href="javascript:;" onclick="eliminarcliente('+num+')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a></td>'
          nuevaFila+='</tr>';
      $("#tabla-contenidocliente > tbody").append(nuevaFila);
      $("#tabla-contenidocliente > tbody").attr('num',parseInt(num)+1); 
      $("#idcliente").html('<option></option>');
      if(idcliente!=0){
          $('#idcomite'+num).select2({
              placeholder: '-- Seleccionar Frecuencia --',
              minimumResultsForSearch: -1,
          }).val(idcomite).trigger("change");
      }else{
          $('#idcomite'+num).select2({
              placeholder: '-- Seleccionar Frecuencia --',
              minimumResultsForSearch: -1,
          });
      }
    calcularmonto();
    creditoCalendario();      
}
function selectclientes(){
    var data = '';
    $("#tabla-contenidocliente > tbody > tr").each(function() {
        var num = $(this).attr('id');        
        var idcredito = $(this).attr('idcredito');
        var idcomite = $('#idcomite'+num+' :selected').val();
        data = data+'/&/'+idcredito+'/,/'+idcomite;
    });
    return data;
} 
function eliminarcliente(num){
    $("#tabla-contenidocliente > tbody > tr#"+num).remove();
    calcularmonto();
}
function calcularmonto(){
    var total = 0;
    $("#tabla-contenidocliente > tbody > tr").each(function() {
        var desembolso = $(this).attr('desembolso');        
        total = total+parseFloat(desembolso);
    });
    $("#monto").val(parseFloat(total).toFixed(2));
    
} 
  // fin
</script>
@endsection