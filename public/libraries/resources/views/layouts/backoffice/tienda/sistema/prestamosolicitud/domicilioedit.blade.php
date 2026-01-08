
    <div class="tabs-container" id="tab-domicilio">
        <ul class="tabs-menu">
            <li class="current"><a href="#tab-domicilio-1">General</a></li>
            <li><a href="#tab-domicilio-2">Referencias</a></li>
            <li><a href="#tab-domicilio-3">Fotografias de Domicilio</a></li>
            <li><a href="#tab-domicilio-4">Suministro</a></li>
            <li><a href="#tab-domicilio-5">Fachada</a></li>
        </ul>
        <div class="tab">
            <div id="tab-domicilio-1" class="tab-content" style="display: block;">
                        <form action="javascript:;" 
                              onsubmit="callback({
                                    route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud/{{ $prestamocredito->id }}',
                                    method: 'PUT',
                                    carga: '#carga-credito',
                                    data:   {
                                        view: 'editar-domicilio',
                                        referencias: seleccinar_referencia(),
                                        domicilio_imagensuministro_anterior: $('#domicilio_imagensuministro_anterior').val(),
                                        domicilio_imagenfachada_anterior: $('#domicilio_imagenfachada_anterior').val(),
                                    },
                                    dataimage:   {
                                        domicilio_imagensuministro: '#domicilio_imagensuministro',
                                        domicilio_imagenfachada: '#domicilio_imagenfachada'
                                    }
                                },
                                function(resultado){
                                    removecarga({input:'#carga-credito'});
                                    domicilio_edit();
                                },this)" class="form-credito">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label>Ubigeo *</label>
                                    <select id="domicilio_editar_idubigeo" onchange="seleccionar_ubicacion($('#domicilio_editar_idubigeo').select2('data')[0].ubicacion)">
                                        <option value="{{$prestamodomicilio!=''?$prestamodomicilio->idubigeo:$prestamocredito->clienteidubigeo}}">{{$prestamodomicilio!=''?$prestamodomicilio->nombre_ubigeo:$prestamocredito->clienteubigeonombre}}</option>
                                    </select>
                                    <label>Dirección *</label>
                                    <input type="text" value="{{$prestamodomicilio!=''?$prestamodomicilio->direccion:$prestamocredito->clientedireccion}}" id="domicilio_editar_direccion" onkeyup="texto_mayucula(this)">
                                    <label>Referencia *</label>
                                    <input type="text" value="{{$prestamodomicilio!=''?$prestamodomicilio->referencia:$prestamocredito->clientereferencia}}" id="domicilio_editar_referencia" onkeyup="texto_mayucula(this)">
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
            <div id="tab-domicilio-3" class="tab-content" style="display: none;">
                <div id="carga-imagendomicilio">
                        <div class="profile-edit-container">
                            <div class="custom-form">
                                <div class="row">
                                    <div class="col-md-12">
                                        <form class="form-prestamodomicilio" action="javascript:;" 
                                              onsubmit="callback({
                                                                    route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud/{{ $prestamocredito->id }}',
                                                                    method: 'PUT',
                                                                    carga: '#carga-imagendomicilio',
                                                                    data:{
                                                                        view :  'editar-domicilioimagen'
                                                                    }
                                                                  },
                                                                  function(resultado){
                                                                      imagen_domicilio()
                                                                  },this)">
                                            <div class="fuzone" style="height: 205px;">
                                                <div class="fu-text">
                                                    <span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span>
                                                </div>
                                                <input type="file" class="upload" id="imagen-domicilio" multiple>
                                            </div>
                                        </form> 
                                    </div>
                                    <div class="col-md-12">
                                        <div class="gallery-items grid-small-pad list-single-gallery three-coulms lightgallery" id="cont-imagenes-domicilio">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div id="tab-domicilio-4" class="tab-content" style="display: none;">  
                <div class="fuzone" id="cont-domicilio_imagensuministro">
                    <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                    <input type="file" class="upload" id="domicilio_imagensuministro">
                    <input type="hidden" id="domicilio_imagensuministro_anterior">
                </div>
                <div id="resultado-domicilio_imagensuministro" style="display: none;"></div>
            </div>
            <div id="tab-domicilio-5" class="tab-content" style="display: none;">   
                <div class="fuzone" id="cont-domicilio_imagenfachada">
                    <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                    <input type="file" class="upload" id="domicilio_imagenfachada">
                    <input type="hidden" id="domicilio_imagenfachada_anterior">
                </div>
                <div id="resultado-domicilio_imagenfachada" style="display: none;"></div>
            </div>
        </div>
    </div>   
                  <a href="javascript:;" class="btn mx-btn-post" onclick="$('.form-credito').submit()" style="float: left;">Guardar Cambios</a>            

<style>

 #cont-domicilio_imagensuministro {
      height:300px;
  }
  #resultado-domicilio_imagensuministro {
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center;
      height:300px;
      width:100%;
      background-color: #eae7e7;
      border-radius: 5px;
      border: 1px solid #aaa;
      float: left;
      margin-bottom: 10px;
  }
  #resultado-domicilio_imagensuministro-cerrar {
      margin-top:10px;
      margin-left:10px;
      font-size:18px;
      background-color:#c12e2e;
      padding:0px;
      padding-left:9px;
      padding-right:9px;
      padding-bottom: 3px;
      border-radius:15px;
      color:#fff;
      font-weight:bold;
      cursor:pointer;
      position: absolute;
      z-index: 100;
  }
 #cont-domicilio_imagenfachada {
      height:300px;
  }
  #resultado-domicilio_imagenfachada {
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center;
      height:300px;
      width:100%;
      background-color: #eae7e7;
      border-radius: 5px;
      border: 1px solid #aaa;
      float: left;
      margin-bottom: 10px;
  }
  #resultado-domicilio_imagenfachada-cerrar {
      margin-top:10px;
      margin-left:10px;
      font-size:18px;
      background-color:#c12e2e;
      padding:0px;
      padding-left:9px;
      padding-right:9px;
      padding-bottom: 3px;
      border-radius:15px;
      color:#fff;
      font-weight:bold;
      cursor:pointer;
      position: absolute;
      z-index: 100;
  }
</style>
<style>
.orden-imagen{
    background-color: #000;
    width: 30px;
    height: 30px;
    float: right;
    margin-right: 10px;
    margin-top: 10px;
    color: #fff;
    line-height: 2;
    font-weight: bold;
    text-align: center;
}
#eliminar-imagen {
    left: 10px;
    margin-top: 10px;
    font-size: 18px;
    background-color: #c12e2e;
    padding: 2px;
    padding-left: 9px;
    padding-right: 9px;
    border-radius: 15px;
    color: #fff;
    font-weight: bold;
    cursor: pointer;
    position: absolute;
    z-index: 10;
}

</style>

<script>
  
  // domicilio_imagensuministro
  @if($prestamodomicilio!='')
  @if($prestamodomicilio->imagensuministro!='')
          mostrar_domicilio_imagensuministro("{{url('/public/backoffice/tienda/'.$tienda->id.'/creditodomicilio/'.$prestamodomicilio->imagensuministro)}}",'{{$prestamodomicilio->imagensuministro}}');
  @endif
  @endif
  
  subir_archivo({
          input:"#domicilio_imagensuministro"
      }, 
      function(resultado){ 
           mostrar_domicilio_imagensuministro(resultado.archivo);
      }
  );
  
  function mostrar_domicilio_imagensuministro(archivo,imagen=''){
          $('#cont-domicilio_imagensuministro').css('display','none');
          $('#resultado-domicilio_imagensuministro').attr('style','background-image: url('+archivo+')');
          $('#resultado-domicilio_imagensuministro').append('<div id="resultado-domicilio_imagensuministro-cerrar" onclick="limpiar_domicilio_imagensuministro()">x</div>');
          $('#domicilio_imagensuministro_anterior').val(imagen);
  }
  function limpiar_domicilio_imagensuministro(){
          $('#cont-domicilio_imagensuministro').css('display','block');
          $('#resultado-domicilio_imagensuministro').removeAttr('style');
          $('#resultado-domicilio_imagensuministro').css('display','none');
          $('#resultado-domicilio_imagensuministro').html('');
          $('#domicilio_imagensuministro').val(null);
          $('#domicilio_imagensuministro_anterior').val('');
  }
  // domicilio_imagenfachada
  @if($prestamodomicilio!='')
  @if($prestamodomicilio->imagenfachada!='')
          mostrar_domicilio_imagenfachada("{{url('/public/backoffice/tienda/'.$tienda->id.'/creditodomicilio/'.$prestamodomicilio->imagenfachada)}}",'{{$prestamodomicilio->imagenfachada}}');
  @endif
  @endif
  
  subir_archivo({
          input:"#domicilio_imagenfachada"
      }, 
      function(resultado){ 
           mostrar_domicilio_imagenfachada(resultado.archivo);
      }
  );
  
  function mostrar_domicilio_imagenfachada(archivo,imagen=''){
          $('#cont-domicilio_imagenfachada').css('display','none');
          $('#resultado-domicilio_imagenfachada').attr('style','background-image: url('+archivo+')');
          $('#resultado-domicilio_imagenfachada').append('<div id="resultado-domicilio_imagenfachada-cerrar" onclick="limpiar_domicilio_imagenfachada()">x</div>');
          $('#domicilio_imagenfachada_anterior').val(imagen);
  }
  function limpiar_domicilio_imagenfachada(){
          $('#cont-domicilio_imagenfachada').css('display','block');
          $('#resultado-domicilio_imagenfachada').removeAttr('style');
          $('#resultado-domicilio_imagenfachada').css('display','none');
          $('#resultado-domicilio_imagenfachada').html('');
          $('#domicilio_imagenfachada').val(null);
          $('#domicilio_imagenfachada_anterior').val('');
  }
</script>
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
    seleccionar_ubicacion('{{$prestamocredito->clienteubigeoubicacion}}');
@endif
function seleccionar_ubicacion(address) {
    singleMap_address({
        'map' : '#domicilio_editar_mapa',
        'address' : address,
        'result_lat' : '#domicilio_editar_mapa_latitud',
        'result_lng' : '#domicilio_editar_mapa_longitud'
    });
}
  
  
  // referencia
  function seleccinar_referencia(){
    var data = '';
    $("#tabla-analisiscualitativo-referencia tbody tr").each(function() {
        var num = $(this).attr('id');        
        var relacion_idpersona = $("#relacion_idpersona"+num+" option:selected").val();
        var relacion_idprestamo_tiporelacion = $("#relacion_idprestamo_tiporelacion"+num+" option:selected").val();
        var numerotelefono = $("#numerotelefono"+num).val();
        var comentario = $("#comentario"+num).val();
        data = data+'/&/'+relacion_idpersona+'/,/'+relacion_idprestamo_tiporelacion+'/,/'+numerotelefono+'/,/'+comentario;
    });
    return data;
  }
  @foreach($relaciones as $value)
      referencia_agregar('{{$value->idpersona}}','{{$value->completo_persona}}','{{$value->idprestamo_tiporelacion}}','{{$value->numerotelefono}}','{{$value->comentario}}');
  @endforeach
  function referencia_agregar(idpersona='',personanombre='',idtiporeacion='',numerotelefono='',comentario=''){
      var num = $("#tabla-analisiscualitativo-referencia > tbody").attr('num');
      $('#tabla-analisiscualitativo-referencia > tbody').append('<tr id="'+num+'">'+
                                                   '<td class="mx-td-input">'+
                                                     '<select id="relacion_idpersona'+num+'" style="width: 100%;padding: 9px;border: 1px solid #d3d8de;border-radius: 5px;">'+
                                                         '<option value="'+idpersona+'">'+personanombre+'</option>'+
                                                     '</select>'+
                                                   '</td>'+
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
    
      $('#relacion_idpersona'+num).select2({
          @include('app.select2_cliente')
      });
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

<script>
    $('#imagen-domicilio').change(function(evt) {
        $(".form-prestamodomicilio").submit();
    });
    function removeimagendomicilio(idprestamo_domicilioimagen) {
      $(".form-prestamodomicilioimagen"+idprestamo_domicilioimagen).submit();
    }
    imagen_domicilio();
    function imagen_domicilio(){
        removecarga({input:'#carga-imagendomicilio'});
        load('#cont-imagenes-domicilio');
        $.ajax({
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-imagendomicilio')}}",
            type:'GET',
            data: {
                idprestamocredito : {{$prestamocredito->id}}
            },
            success: function (respuesta){
                $('#cont-imagenes-domicilio').html(respuesta['imagenes']);
            }
        });
    }
</script>