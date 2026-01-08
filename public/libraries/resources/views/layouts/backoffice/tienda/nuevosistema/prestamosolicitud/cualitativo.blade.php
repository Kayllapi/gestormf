 <div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Análisis Cualitativo</span>
  </div>
</div>
<form action="javascript:;" 
      onsubmit="callback({
                            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud',
                            method: 'POST',
                            data:   {
                              view: 'registrar-cualitativo',
                              idprestamo_credito: {{ $prestamocredito->id }},
                              respuestas: selectRespuesta(),
                              referencias: seleccinar_referencia(),
                              avales: seleccinar_aval()
                            }
                          },
                          function(resultado){
                            cualitativo_index();
                          },this)">
  
    <div class="tabs-container" id="tab-cualitativo">
        <ul class="tabs-menu">
          <li class="current"><a href="#tab-cualitativo-0">General</a></li>
          <li><a href="#tab-cualitativo-1">Sustento</a></li>
          <li><a href="#tab-cualitativo-2">Avales</a></li>
          <li><a href="#tab-cualitativo-3">Referencias</a></li>
        </ul>
        <div class="tab">
          <div id="tab-cualitativo-0" class="tab-content" style="display: block;">
             <div class="table-responsive">
                <table class="table" id="tabla-contenidopregunta-cualitativo">
                  <thead>
                    <tr class="thead-dark">
                      <th width="10px">N°</th>
                      <th>Preguntas</th>
                      <th>Malo (1)</th>
                      <th>Regular (2)</th>
                      <th>Bueno (3)</th>
                      <th width="10px" colspan="3">Resultado</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $count = 1; $valordata = 0; ?>
                    @if ($cualitativodetalles != '[]')
                      @foreach ($cualitativodetalles as $value)
                      <tr count="{{ $count }}" idcualitativopregunta="{{ $value->idprestamo_cualitativopregunta }}">
                        <td style="padding:8px;background-color: #eae7e7;text-align: center;"><b>{{ $count }}</b></td>
                        <td>{{ $value->nombre }}</td>
                        <td>{{ $value->descripcion1 }}</td>
                        <td>{{ $value->descripcion2 }}</td>
                        <td>{{ $value->descripcion3 }}</td>
                        <td style="text-align: center;font-weight: bold;" id="bueno{{ $count }}"></td>
                        <td style="text-align: center;font-weight: bold;" id="regular{{ $count }}"></td>
                        <td style="text-align: center;font-weight: bold;" id="malo{{ $count }}"></td>
                        <td>
                          @if ($value->valorbueno != 0)
                          <?php $valordata = $value->valorbueno; ?>
                          @elseif ($value->valorregular != 0)
                          <?php $valordata = $value->valorregular; ?>
                          @elseif ($value->valormalo != 0)
                          <?php $valordata = $value->valormalo; ?>
                          @endif
                          <input type="number" min="1" max="3" step="1" id="votacion{{ $count }}" value="{{ $valordata }}" onclick="respuesta({{ $count }})" onkeyup="respuesta({{ $count }})" <?php echo $estado=='lectura'?'disabled':'' ?>>
                          <input type="hidden" id="valorBueno{{ $count }}" value="{{ $value->valorbueno }}">
                          <input type="hidden" id="valorRegular{{ $count }}" value="{{ $value->valorregular }}">
                          <input type="hidden" id="valorMalo{{ $count }}" value="{{ $value->valormalo }}">
                        </td>
                      </tr>
                      <?php $count++; ?>
                      @endforeach
                    @else
                      @foreach ($preguntas as $value)
                      <tr count="{{ $count }}" idcualitativopregunta="{{ $value->id }}">
                        <td style="padding:8px;background-color: #eae7e7;text-align: center;"><b>{{ $count }}</b></td>
                        <td>{{ $value->nombre }}</td>
                        <td>{{ $value->descripcion1 }}</td>
                        <td>{{ $value->descripcion2 }}</td>
                        <td>{{ $value->descripcion3 }}</td>
                        <td style="text-align: center;font-weight: bold;" id="bueno{{ $count }}"></td>
                        <td style="text-align: center;font-weight: bold;" id="regular{{ $count }}"></td>
                        <td style="text-align: center;font-weight: bold;" id="malo{{ $count }}"></td>
                        <td>
                          <input type="number" min="1" max="3" step="1" id="votacion{{ $count }}" onclick="respuesta({{ $count }})" onkeyup="respuesta({{ $count }})" <?php echo $estado=='lectura'?'disabled':'' ?>>
                          <input type="hidden" id="valorBueno{{ $count }}" value="0">
                          <input type="hidden" id="valorRegular{{ $count }}" value="0">
                          <input type="hidden" id="valorMalo{{ $count }}" value="0">
                        </td>
                      </tr>
                      <?php $count++; ?>
                      @endforeach
                    @endif
                  </tbody>
                  <tfoot>
                    <tr class="thead-dark">
                      <th colspan="5" style="text-align: right;">SUBTOTAL</th>
                      <th id="totalBueno">0</th>
                      <th id="totalRegular">0</th>
                      <th id="totalMalo">0</th>
                      <th></th>
                    </tr>
                    <tr class="thead-dark">
                      <th colspan="5" style="text-align: right;">TOTAL</th>
                      <th colspan="3" id="totalGeneral" style="text-align: center;">0</th>
                      <th></th>
                    </tr>
                  </tfoot>
                </table>
              </div>
          </div>
          <div id="tab-cualitativo-1" class="tab-content" style="display: none;">
            <div class="row">
                <div class="col-sm-6">
                  <label>Destino del Crédito *</label>
                  <textarea name="destino" id="destino" cols="30" rows="10" <?php echo $estado=='lectura'?'disabled':'' ?>>{{ $cualitativo->destino ?? '' }}</textarea>

                  <label>Descripción del Crédito *</label>
                  <textarea name="descripcion" id="descripcion" cols="30" rows="10" <?php echo $estado=='lectura'?'disabled':'' ?>>{{ $cualitativo->descripcion ?? '' }}</textarea>
                </div>
                <div class="col-sm-6">
                  <label>Comentario del Asesor de Negocio *</label>
                  <textarea name="comentario" id="comentario" cols="30" rows="10" <?php echo $estado=='lectura'?'disabled':'' ?>>{{ $cualitativo->comentario ?? '' }}</textarea>
                  <label>Calificación Central de Riesgo *</label>
                  <select id="idcalificacion" <?php echo $estado=='lectura'?'disabled':'' ?>>
                      <option></option>
                      @foreach($calificaciones as $value)
                          <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                      @endforeach
                  </select>
                </div>
              </div>
          </div>
          <div id="tab-cualitativo-2" class="tab-content" style="display: none;">
              <div class="table-responsive">
                  <table class="table" id="tabla-analisiscualitativo-aval">
                      <thead class="thead-dark">
                        <tr>
                          <th>Persona</th>
                          @if($estado!='lectura')
                          <th width="10px" style="padding: 0px;padding-right: 1px;">
                          <a href="javascript:;" class="btn  color-bg flat-btn" onclick="aval_agregar()"><i class="fa fa-angle-right"></i> Agregar</a>
                          </th>
                          @endif
                        </tr>
                      </thead>
                      <tbody num="0">
                      
                      </tbody>
                  </table>
                </div>
          </div>
          <div id="tab-cualitativo-3" class="tab-content" style="display: none;">
              <div class="table-responsive">
                  <table class="table" id="tabla-analisiscualitativo-referencia">
                      <thead class="thead-dark">
                        <tr>
                          <th>Persona</th>
                          <th>Tipo de relación</th>
                          <th>Nro de Teléfono</th>
                          @if($estado!='lectura')
                          <th width="10px" style="padding: 0px;padding-right: 1px;">
                          <a href="javascript:;" class="btn  color-bg flat-btn" onclick="referencia_agregar()"><i class="fa fa-angle-right"></i> Agregar</a>
                          </th>
                          @endif
                        </tr>
                      </thead>
                      <tbody num="0">
                      
                      </tbody>
                  </table>
                </div>
          </div>
        </div>
      </div>
  
              
    @if($estado!='lectura')         
    <button type="submit" class="btn mx-btn-post">Guardar</button>
    @endif
</form>
<script>
  
    
  tab({click:'#tab-cualitativo'});
 
  @if($cualitativo!='')
  $('#idcalificacion').select2({
      placeholder: '-- Seleccionar Frecuencia --',
      minimumResultsForSearch: -1,
  }).val({{ $cualitativo->idprestamo_calificacion }}).trigger('change');
  @else
  $('#idcalificacion').select2({
      placeholder: '-- Seleccionar Frecuencia --',
      minimumResultsForSearch: -1,
  });
  @endif
  
  @if ($cualitativodetalles != '[]')
    var cx = 1;
    @foreach ($cualitativodetalles as $value)
      respuesta(cx);
      cx++;
    @endforeach
  @endif
  function respuesta(count) {
    var valor = $('#votacion'+count).val();
    if (valor == 1) {
        $('#malo'+count).html('1');
        $('#regular'+count).html('');
        $('#bueno'+count).html('');
        $('#valorMalo'+count).val(1);
        $('#valorRegular'+count).val(0);
        $('#valorBueno'+count).val(0);
        $('#malo'+count).css({'background-color':'#2ecc71','color':'#fff'});
        $('#regular'+count).css({'background-color':'#fff','color':'#fff'});
        $('#bueno'+count).css({'background-color':'#fff','color':'#fff'});
    } else if (valor == 2) {
        $('#regular'+count).html('2');
        $('#malo'+count).html('');
        $('#bueno'+count).html('');
        $('#valorMalo'+count).val(0);
        $('#valorRegular'+count).val(2);
        $('#valorBueno'+count).val(0);
        $('#regular'+count).css({'background-color':'#3498db','color':'#fff'});
        $('#malo'+count).css({'background-color':'#fff','color':'#fff'});
        $('#bueno'+count).css({'background-color':'#fff','color':'#fff'});
    } else if (valor == 3) {
        $('#bueno'+count).html('3');
        $('#malo'+count).html('');
        $('#regular'+count).html('');
        $('#valorMalo'+count).val(0);
        $('#valorRegular'+count).val(0);
        $('#valorBueno'+count).val(3);
        $('#bueno'+count).css({'background-color':'rgb(214 2 31)','color':'#fff'});
        $('#malo'+count).css({'background-color':'#fff','color':'#fff'});
        $('#regular'+count).css({'background-color':'#fff','color':'#fff'});
    } else {
        $('#valorMalo'+count).val(0);
        $('#valorRegular'+count).val(0);
        $('#valorBueno'+count).val(0);
        $('#malo'+count).html('');
        $('#regular'+count).html('');
        $('#bueno'+count).html('');
        $('#malo'+count).css({'background-color':'#fff','color':'#000'});
        $('#regular'+count).css({'background-color':'#fff','color':'#000'});
        $('#bueno'+count).css({'background-color':'#fff','color':'#000'});
    }
    sumaRespuesta();
  }
  function sumaRespuesta() {
    var totalBueno = 0;
    var totalRegular = 0;
    var totalMalo = 0;
    var totalGeneral = 0;
    $("#tabla-contenidopregunta-cualitativo > tbody > tr").each(function() {
        var count = $(this).attr('count');
        var bueno = parseInt($('#valorBueno'+count).val());
        var regular = parseInt($('#valorRegular'+count).val());
        var malo = parseInt($('#valorMalo'+count).val());
        totalBueno = parseInt(totalBueno + bueno);
        totalRegular = parseInt(totalRegular + regular);
        totalMalo = parseInt(totalMalo + malo);
        totalGeneral = parseFloat((totalBueno + totalRegular + totalMalo)/3);
    });
    $('#totalBueno').html(totalBueno);
    $('#totalRegular').html(totalRegular);
    $('#totalMalo').html(totalMalo);
    $('#totalGeneral').html(totalGeneral.toFixed(2));
  }
  function selectRespuesta() {
    var data = [];
    $("#tabla-contenidopregunta-cualitativo > tbody > tr").each(function() {
      var count = $(this).attr('count');
      var idcualitativopregunta = $(this).attr('idcualitativopregunta');
      var valorbueno = $('#valorBueno'+count).val();
      var valorregular = $('#valorRegular'+count).val();
      var valormalo = $('#valorMalo'+count).val();
      data.push({
        valorbueno: valorbueno,
        valorregular: valorregular,
        valormalo: valormalo,
        idcualitativopregunta: idcualitativopregunta
      });
    });
    return JSON.stringify(data);
  }
  
  // aval
  function seleccinar_aval(){
    var data = '';
    $("#tabla-analisiscualitativo-aval tbody tr").each(function() {
        var num = $(this).attr('id');        
        var aval_idpersona = $("#aval_idpersona"+num+" option:selected").val();
        var relacion_idprestamo_tiporelacion = $("#relacion_idprestamo_tiporelacion"+num+" option:selected").val();
        var numerotelefono = $("#numerotelefono"+num).val();
        data = data+'/&/'+aval_idpersona+'/,/'+relacion_idprestamo_tiporelacion+'/,/'+numerotelefono;
    });
    return data;
  }
  @foreach($avales as $value)
      aval_agregar('{{$value->idpersona}}','{{$value->completo_persona}}');
  @endforeach
  function aval_agregar(idpersona='',personanombre=''){
      var num = $("#tabla-analisiscualitativo-aval > tbody").attr('num');
      $('#tabla-analisiscualitativo-aval > tbody').append('<tr id="'+num+'">'+
                                                   '<td class="mx-td-input">'+
                                                     '<select id="aval_idpersona'+num+'" style="width: 100%;padding: 9px;border: 1px solid #d3d8de;border-radius: 5px;" <?php echo $estado=='lectura'?'disabled':'' ?>>'+
                                                         '<option value="'+idpersona+'">'+personanombre+'</option>'+
                                                     '</select>'+
                                                   '</td>'+
                                                   @if($estado!='lectura')
                                                   '<td><a id="del'+num+'" href="javascript:;" onclick="eliminaraval('+num+')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a></td>'+
                                                   @endif
                                               '</tr>');
      $("#tabla-analisiscualitativo-aval > tbody").attr('num',parseInt(num)+1);
    
      $('#aval_idpersona'+num).select2({
          @include('app.select2_cliente')
      });
  }
  function eliminaraval(num){
    $("#tabla-analisiscualitativo-aval > tbody > tr#"+num).remove();
  }
  
  // referencia
  function seleccinar_referencia(){
    var data = '';
    $("#tabla-analisiscualitativo-referencia tbody tr").each(function() {
        var num = $(this).attr('id');        
        var relacion_idpersona = $("#relacion_idpersona"+num+" option:selected").val();
        var relacion_idprestamo_tiporelacion = $("#relacion_idprestamo_tiporelacion"+num+" option:selected").val();
        var numerotelefono = $("#numerotelefono"+num).val();
        data = data+'/&/'+relacion_idpersona+'/,/'+relacion_idprestamo_tiporelacion+'/,/'+numerotelefono;
    });
    return data;
  }
  @foreach($relaciones as $value)
      referencia_agregar('{{$value->idpersona}}','{{$value->completo_persona}}','{{$value->idprestamo_tiporelacion}}','{{$value->nombre_tiporelacion}}','{{$value->numerotelefono}}');
  @endforeach
  function referencia_agregar(idpersona='',personanombre='',idtiporeacion='',tiporelacionnombre='',numerotelefono=''){
      var num = $("#tabla-analisiscualitativo-referencia > tbody").attr('num');
      $('#tabla-analisiscualitativo-referencia > tbody').append('<tr id="'+num+'">'+
                                                   '<td class="mx-td-input">'+
                                                     '<select id="relacion_idpersona'+num+'" style="width: 100%;padding: 9px;border: 1px solid #d3d8de;border-radius: 5px;" <?php echo $estado=='lectura'?'disabled':'' ?>>'+
                                                         '<option value="'+idpersona+'">'+personanombre+'</option>'+
                                                     '</select>'+
                                                   '</td>'+
                                                   '<td class="mx-td-input">'+
                                                     '<select id="relacion_idprestamo_tiporelacion'+num+'" style="width: 100%;padding: 9px;border: 1px solid #d3d8de;border-radius: 5px;" <?php echo $estado=='lectura'?'disabled':'' ?>>'+
                                                         '<option value="'+idtiporeacion+'">'+tiporelacionnombre+'</option>'+
                                                     '</select>'+
                                                   '</td>'+
                                                   '<td class="mx-td-input"><input id="numerotelefono'+num+'" type="text" value="'+numerotelefono+'" <?php echo $estado=='lectura'?'disabled':'' ?>></td>'+
                                                   @if($estado!='lectura')
                                                   '<td><a id="del'+num+'" href="javascript:;" onclick="eliminarreferencia('+num+')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a></td>'+
                                                   @endif
                                               '</tr>');
      $("#tabla-analisiscualitativo-referencia > tbody").attr('num',parseInt(num)+1);
    
      $('#relacion_idpersona'+num).select2({
          @include('app.select2_cliente')
      });
      $('#relacion_idprestamo_tiporelacion'+num).select2({
          ajax: {
              url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-select-tiporelacion')}}",
              dataType: 'json',
              delay: 250,
              data: function (params) {
                  return {
                        buscar: params.term
                  };
              },
              processResults: function (data) {
                  return {
                      results: data
                  };
              },
              cache: true
          },
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      });
  }
  function eliminarreferencia(num){
    $("#tabla-analisiscualitativo-referencia > tbody > tr#"+num).remove();
  }
</script>