<div class="tabs-container" id="tab-domicilio">
        <ul class="tabs-menu">
            <li class="current"><a href="#tab-domicilio-1">General</a></li>
            <li><a href="#tab-domicilio-2">Socios</a></li>
        </ul>
        <div class="tab">
            <div id="tab-domicilio-1" class="tab-content" style="display: block;">
                        <form action="javascript:;" 
                              onsubmit="callback({
                                    route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamo/ahorrosolicitud/{{ $prestamoahorro->id }}',
                                    method: 'PUT',
                                    carga: '#carga-ahorro',
                                    data:   {
                                        view: 'editar-domicilio',
                                        referencias: seleccinar_referencia(),
                                    }
                                },
                                function(resultado){
                                    removecarga({input:'#carga-ahorro'});
                                    domicilio_edit();
                                },this)" class="form-ahorro">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label>Ubigeo *</label>
                                    <select id="domicilio_editar_idubigeo" onchange="seleccionar_ubicacion($('#domicilio_editar_idubigeo').select2('data')[0].ubicacion)">
                                        <option value="{{$prestamodomicilio!=''?$prestamodomicilio->idubigeo:$prestamoahorro->clienteidubigeo}}">{{$prestamodomicilio!=''?$prestamodomicilio->nombre_ubigeo:$prestamoahorro->clienteubigeonombre}}</option>
                                    </select>
                                    <label>Dirección *</label>
                                    <input type="text" value="{{$prestamodomicilio!=''?$prestamodomicilio->direccion:$prestamoahorro->clientedireccion}}" id="domicilio_editar_direccion" onkeyup="texto_mayucula(this)">
                                    <label>Referencia</label>
                                    <input type="text" value="{{$prestamodomicilio!=''?$prestamodomicilio->referencia:$prestamoahorro->clientereferencia}}" id="domicilio_editar_referencia" onkeyup="texto_mayucula(this)">
                                    <label>Reside Desde (mes/año)</label>
                                    <div class="row">
                                      <div class="col-sm-6">
                                          <select id="domicilio_editar_reside_desdemes">
                                              <option></option>
                                              <option value="1">ENERO</option>
                                              <option value="2">FEBRERO</option>
                                              <option value="3">MARZO</option>
                                              <option value="4">ABRIL</option>
                                              <option value="5">MAYO</option>
                                              <option value="6">JUNIO</option>
                                              <option value="7">JULIO</option>
                                              <option value="8">AGOSTO</option>
                                              <option value="9">SEPTIEMBRE</option>
                                              <option value="10">OCTUBRE</option>
                                              <option value="11">NOVIEMBRE</option>
                                              <option value="12">DICIEMBRE</option>
                                          </select>
                                      </div>
                                      <div class="col-sm-6">
                                          <select id="domicilio_editar_reside_desdeanio">
                                              <option></option>
                                              <?php $fecha_reside = Carbon\Carbon::now()->format('Y') ?>
                                              @for($i=$fecha_reside;$i>1950;$i--)
                                              <option value="{{$fecha_reside}}">{{$fecha_reside}}</option>
                                              <?php $fecha_reside=$fecha_reside-1 ?>
                                              @endfor
                                          </select>
                                      </div>
                                    </div>
                                    <label>Hora Ubicación (Desde - Hasta)</label>
                                    <div class="row">
                                      <div class="col-sm-6">
                                        <input type="time" value="{{$prestamodomicilio!=''?$prestamodomicilio->horaubicacion_de:'00:00'}}" id="domicilio_editar_horaubicacion_de">
                                      </div>
                                      <div class="col-sm-6">
                                        <input type="time" value="{{$prestamodomicilio!=''?$prestamodomicilio->horaubicacion_hasta:'00:00'}}" id="domicilio_editar_horaubicacion_hasta">
                                      </div>
                                    </div>

                                    <label>Tipo de Propiedad</label>
                                    <select id="domicilio_editar_idtipopropiedad">
                                        <option></option>
                                        <option value="1">ALQUILADO</option>
                                        <option value="2">FAMILIAR</option>
                                        <option value="3">PROPIO</option>
                                    </select>
                                  
                                    <label>Pago de Servicios</label>
                                    <select id="domicilio_editar_iddeudapagoservicio">
                                        <option></option>
                                        <option value="1">CON CORTE</option>
                                        <option value="2">PAGO PUNTUAL</option>
                                        <option value="3">DEUDA X 1 MES</option>
                                        <option value="4">DEUDA X 2 MESES</option>
                                        <option value="5">DEUDA X 3 MESES</option>
                                        <option value="6">DEUDA X 4 MESES</option>
                                        <option value="7">DEUDA X 5 MESES</option>
                                        <option value="8">DEUDA X 6 MESES</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                  
                                    <label>Ubicación (Mover Marcador)</label>
                                    <div id="domicilio_editar_mapa" style="height: 457px;width: 100%;margin-bottom: 5px;margin-bottom: 5px;border: 1px solid #ada9a9;border-radius: 5px;"></div>
                                    <input type="hidden" value="{{$prestamodomicilio!=''?$prestamodomicilio->mapa_latitud:''}}" id="domicilio_editar_mapa_latitud"/>
                                    <input type="hidden" value="{{$prestamodomicilio!=''?$prestamodomicilio->mapa_longitud:''}}" id="domicilio_editar_mapa_longitud"/>
                                </div>
                            </div>
                        </form>
            </div>
            <div id="tab-domicilio-2" class="tab-content" style="display: none;">
                <div class="table-responsive">
                    <table class="table" id="tabla-analisiscualitativo-referencia">
                        <thead class="thead-dark">
                          <tr>
                            <th>Persona</th>
                            <th>Tipo de relación</th>
                            <th>Nro de Teléfono</th>
                            <th>Comentario</th>
                            <th width="10px" style="padding: 0px;padding-right: 1px;">
                            <a href="javascript:;" class="btn  color-bg flat-btn" onclick="referencia_agregar()"><i class="fa fa-angle-right"></i> Agregar</a>
                            </th>
                          </tr>
                        </thead>
                        <tbody num="0"></tbody>
                    </table>
                  </div>
            </div>
        </div>
    </div>  
    
                  <a href="javascript:;" class="btn mx-btn-post" onclick="$('.form-ahorro').submit()" style="float: left;">Guardar Cambios</a>            


<script>
  
  tab({click:'#tab-domicilio'});
    $('#domicilio_editar_idubigeo').select2({
        @include('app.select2_ubigeo')
    });
  
@if($prestamodomicilio!='')
    @if($prestamodomicilio->idtipopropiedad!=0)
    $('#domicilio_editar_idtipopropiedad').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1
    }).val({{$prestamodomicilio->idtipopropiedad}}).trigger('change');
    @else
    $('#domicilio_editar_idtipopropiedad').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1
    });
    @endif
@else
    $('#domicilio_editar_idtipopropiedad').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1
    });
@endif
  
@if($prestamodomicilio!='')
    @if($prestamodomicilio->iddeudapagoservicio!=0)
    $('#domicilio_editar_iddeudapagoservicio').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1
    }).val({{$prestamodomicilio->iddeudapagoservicio}}).trigger('change');
    @else
    $('#domicilio_editar_iddeudapagoservicio').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1
    });
    @endif
@else
    $('#domicilio_editar_iddeudapagoservicio').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1
    });
@endif
  
@if($prestamodomicilio!='')
@if($prestamodomicilio->reside_desdemes!=0)
    $('#domicilio_editar_reside_desdemes').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1
    }).val({{$prestamodomicilio->reside_desdemes}}).trigger('change');
@else
    $('#domicilio_editar_reside_desdemes').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1
    });
@endif
@else
    $('#domicilio_editar_reside_desdemes').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1
    });
@endif
  
@if($prestamodomicilio!='')
@if($prestamodomicilio->reside_desdeanio!=0)
    $('#domicilio_editar_reside_desdeanio').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1
    }).val({{$prestamodomicilio->reside_desdeanio}}).trigger('change');
@else
    $('#domicilio_editar_reside_desdeanio').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1
    });
@endif
@else
    $('#domicilio_editar_reside_desdeanio').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1
    });
@endif
  
@if($prestamodomicilio!='')
    singleMap({
        'map' : '#domicilio_editar_mapa',
        'lat' : '{{$prestamodomicilio->mapa_latitud}}',
        'lng' : '{{$prestamodomicilio->mapa_longitud}}',
        'result_lat' : '#domicilio_editar_mapa_latitud',
        'result_lng' : '#domicilio_editar_mapa_longitud'
    });
@else
    seleccionar_ubicacion('{{$prestamoahorro->clienteubigeoubicacion}}');
@endif
function seleccionar_ubicacion(address) {
    singleMap_address({
        'map' : '#domicilio_editar_mapa',
        'address' : address,
        'result_lat' : '#domicilio_editar_mapa_latitud',
        'result_lng' : '#domicilio_editar_mapa_longitud'
    });
}
  // socios
  function seleccinar_referencia(){
    var data = '';
    $("#tabla-analisiscualitativo-referencia tbody tr").each(function() {
        var num = $(this).attr('id');        
        var personanombre = $("#personanombre"+num).val();
        var relacion_idprestamo_tiporelacion = $("#relacion_idprestamo_tiporelacion"+num+" option:selected").val();
        var numerotelefono = $("#numerotelefono"+num).val();
        var comentario = $("#comentario"+num).val();
        data = data+'/&/'+personanombre+'/,/'+relacion_idprestamo_tiporelacion+'/,/'+numerotelefono+'/,/'+comentario;
    });
    return data;
  }
  @foreach($relaciones as $value)
      referencia_agregar('{{$value->personanombre}}','{{$value->idprestamo_tiporelacion}}','{{$value->numerotelefono}}','{{$value->comentario}}');
  @endforeach
  function referencia_agregar(personanombre='',idtiporeacion='',numerotelefono='',comentario=''){
      var num = $("#tabla-analisiscualitativo-referencia > tbody").attr('num');
      $('#tabla-analisiscualitativo-referencia > tbody').append('<tr id="'+num+'">'+
                                                   '<td class="mx-td-input"><input id="personanombre'+num+'" type="text" value="'+personanombre+'" onkeyup="texto_mayucula(this)"></td>'+
                                                   '<td class="mx-td-input">'+
                                                     '<select id="relacion_idprestamo_tiporelacion'+num+'" style="width: 100%;padding: 9px;border: 1px solid #d3d8de;border-radius: 5px;">'+
                                                          '<option></option>'+
                                                         '@foreach($tiporelaciones as $value)'+
                                                              '<option value="{{$value->id}}">{{$value->nombre}}</option>'+
                                                         '@endforeach'+
                                                     '</select>'+
                                                   '</td>'+
                                                   '<td class="mx-td-input"><input id="numerotelefono'+num+'" type="text" value="'+numerotelefono+'" onkeyup="texto_mayucula(this)"></td>'+
                                                   '<td class="mx-td-input"><input id="comentario'+num+'" type="text" value="'+comentario+'" onkeyup="texto_mayucula(this)"></td>'+
                                                   '<td><a id="del'+num+'" href="javascript:;" onclick="eliminarreferencia('+num+')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a></td>'+
                                               '</tr>');
      $("#tabla-analisiscualitativo-referencia > tbody").attr('num',parseInt(num)+1);

      if(idtiporeacion!=''){
          $('#relacion_idprestamo_tiporelacion'+num).select2({
              placeholder: "--  Seleccionar --",
              minimumResultsForSearch: -1
          }).val(idtiporeacion).trigger('change');
      }else{
          $('#relacion_idprestamo_tiporelacion'+num).select2({
              placeholder: "--  Seleccionar --",
              minimumResultsForSearch: -1
          });
      }
          
  }
  function eliminarreferencia(num){
    $("#tabla-analisiscualitativo-referencia > tbody > tr#"+num).remove();
  }

</script>