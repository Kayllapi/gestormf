@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div id="cont-tabla-comentario">
    <div class="list-single-main-wrapper fl-wrap">
        <div class="breadcrumbs gradient-bg fl-wrap">
          <span>Comentarios</span>
          <a class="btn btn-warning" href="javascript:;" onclick="registrar_comentario()"><i class="fa fa-angle-right"></i> Registrar</a></a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table" id="tabla-contenido-comentario">
        </table>
    </div>
</div>

<!-- registrar -->
<div id="cont-registrar-comentario" style="display:none;">
  <div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Comentario</span>
      <a class="btn btn-success" href="javascript:;" onclick="index_comentario()"><i class="fa fa-angle-left"></i> Atras</a></a>
    </div>
  </div>
  <form action="javascript:;"
        onsubmit="callback({
                                route:  'backoffice/noticiacomentario',
                                method: 'POST',
                                data:   {
                                    view: 'registrar-comentario',
                                }
                            },
                            function(resultado){
                                index_comentario();
                            },this)">
    <div class="profile-edit-container">
      <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
            <div class="col-sm-12">
              <label>Noticia *</label>
              <select id="comentario_registrar_idnoticia">
                <option></option>
                @foreach ($noticias as $value)
                <option value="{{ $value->id }}">{{ $value->titulo }}</option>
                @endforeach
              </select>
              
              <label>Comentario *</label>
              <textarea id="comentario_registrar_comentario"></textarea>
            </div>
          </div>
        </div>
      </div>
      <div class="custom-form">
        <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios</button>
      </div>
    </div>
  </form>
</div>

<!-- editar -->
<div id="cont-editar-comentario"style="display:none;">
  <div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Editar Comentario</span>
      <a class="btn btn-success" href="javascript:;" onclick="index_comentario()"><i class="fa fa-angle-left"></i> Atras</a></a>
    </div>
  </div>
  <div id="form-editar-comentario" style="display:none;">
    <form action="javascript:;"
          onsubmit="callback({
                                  route:  'backoffice/noticiacomentario/0',
                                  method: 'PUT',
                                  data:   {
                                      view: 'editar-comentario',
                                      idcomentario: $('#comentario_editar_idcomentario').val()
                                  }
                              },
                              function(resultado){
                                  index_comentario();
                              },this)">
      <div class="profile-edit-container">
        <div class="profile-edit-container">
          <div class="custom-form">
            <input type="hidden" id="comentario_editar_idcomentario" value="0">
            <div class="row">
              <div class="col-sm-12">
                <label>Noticia *</label>
                <select id="comentario_editar_idnoticia">
                  <option></option>
                  @foreach ($noticias as $value)
                  <option value="{{ $value->id }}">{{ $value->titulo }}</option>
                  @endforeach
                </select>

                <label>Comentario *</label>
                <textarea id="comentario_editar_comentario"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="custom-form">
          <button type="submit" class="btn  big-btn  color-bg flat-btn">Actualizar Comentario</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- confirmar -->
<div id="cont-confirmar-comentario"style="display:none;">
    <div class="list-single-main-wrapper fl-wrap">
        <div class="breadcrumbs gradient-bg fl-wrap">
            <span>Confirmar Comentario</span>
            <a class="btn btn-success" href="javascript:;" onclick="index_comentario()"><i class="fa fa-angle-left"></i> Atras</a></a>
        </div>
    </div>
    <div id="form-confirmar-comentario" style="display:none;">
        <form action="javascript:;" 
              onsubmit="callback({
                                      route:  'backoffice/noticiacomentario/0',
                                      method: 'PUT',
                                      data:   {
                                          view: 'confirmar-comentario',
                                          idcomentario: $('#comentario_confirmar_idcomentario').val()
                                      }
                                  },
                                  function(resultado){
                                      index_comentario();
                                  },this)">
            <div class="profile-edit-container">
              <div class="profile-edit-container">
                <div class="custom-form">
                  <input type="hidden" id="comentario_confirmar_idcomentario" value="0">
                  <div class="row">
                    <div class="col-sm-12">
                      <label>Noticia *</label>
                      <select id="comentario_confirmar_idnoticia" disabled>
                        <option></option>
                        @foreach ($noticias as $value)
                        <option value="{{ $value->id }}">{{ $value->titulo }}</option>
                        @endforeach
                      </select>

                      <label>Comentario *</label>
                      <textarea id="comentario_confirmar_comentario" disabled></textarea>
                    </div>
                  </div>
                </div>
              </div>
              <div class="custom-form">
                <button type="submit" class="btn  big-btn  color-bg flat-btn">Confirmar Comentario</button>
              </div>
            </div>
        </form>
    </div>
</div>

<!-- detalle -->
<div id="cont-detalle-comentario"style="display:none;">
  <div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Detalle Comentario</span>
      <a class="btn btn-success" href="javascript:;" onclick="index_comentario()"><i class="fa fa-angle-left"></i> Atras</a></a>
    </div>
  </div>
  <div id="form-detalle-comentario" style="display:none;">
    <div class="profile-edit-container">
      <div class="profile-edit-container">
        <div class="custom-form">
          <input type="hidden" id="comentario_detalle_idcomentario" value="0">
          <div class="row">
            <div class="col-sm-12">
              <label>Noticia *</label>
              <select id="comentario_detalle_idnoticia" disabled>
                <option></option>
                @foreach ($noticias as $value)
                <option value="{{ $value->id }}">{{ $value->titulo }}</option>
                @endforeach
              </select>

              <label>Comentario *</label>
              <textarea id="comentario_detalle_comentario" disabled></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- anular -->
<div id="cont-anular-comentario"style="display:none;">
    <div class="list-single-main-wrapper fl-wrap">
        <div class="breadcrumbs gradient-bg fl-wrap">
            <span>Anular Comentario</span>
            <a class="btn btn-success" href="javascript:;" onclick="index_comentario()"><i class="fa fa-angle-left"></i> Atras</a></a>
        </div>
    </div>
    <div id="form-anular-comentario" style="display:none;">
        <div class="mensaje-warning">
            <i class="fa fa-warning"></i> ¿Esta seguro de anular?</b>
        </div>
        <form action="javascript:;" 
              onsubmit="callback({
                                      route:  'backoffice/noticiacomentario/0',
                                      method: 'PUT',
                                      data:   {
                                          view: 'anular-comentario',
                                          idcomentario: $('#comentario_anular_idcomentario').val()
                                      }
                                  },
                                  function(resultado){
                                      index_comentario();
                                  },this)">
          
            <div class="profile-edit-container">
              <div class="profile-edit-container">
                <div class="custom-form">
                  <input type="hidden" id="comentario_anular_idcomentario" value="0">
                  <div class="row">
                    <div class="col-sm-12">
                      <label>Noticia *</label>
                      <select id="comentario_anular_idnoticia" disabled>
                        <option></option>
                        @foreach ($noticias as $value)
                        <option value="{{ $value->id }}">{{ $value->titulo }}</option>
                        @endforeach
                      </select>

                      <label>Comentario *</label>
                      <textarea id="comentario_anular_comentario" disabled></textarea>
                    </div>
                  </div>
                </div>
              </div>
              <div class="custom-form">
                <button type="submit" class="btn  big-btn  color-bg flat-btn">Anular Comentario</button>
              </div>
            </div>
        </form>
    </div>
</div>
<div id="load-comentario"></div>
@endsection
@section('scriptsbackoffice')
<script>
    // Noticia
    index_comentario();
    function index_comentario() {
        $('#cont-tabla-comentario').css('display','block');
        $('#cont-registrar-comentario, #cont-editar-comentario, #cont-detalle-comentario, #cont-confirmar-comentario, #cont-anular-comentario').css('display','none');
        load('#load-comentario');
        $('#tabla-contenido-comentario').html('');
        $.ajax({
            url:  "{{url('backoffice/noticiacomentario/show-index_comentario')}}",
            type: 'GET',
            data: {},
            success: function (respuesta){
                $('#load-comentario').html('');
                $('#tabla-contenido-comentario').html(respuesta);

                // menu tabla
                $("div#menu-opcion").on("click", function () {
                    $("ul",this).toggleClass("hu-menu-vis");
                    $("i",this).toggleClass("fa-angle-up");
                });
            }
        });
    }
    function registrar_comentario() {
        removecarga({input:'#mx-carga'});
        $('#comentario_registrar_idnoticia').select2({
          placeholder: '-- Seleccionar --',
          minimumResultsForSearch: -1
        }).val('').trigger('change');
      
        $('#comentario_registrar_comentario').val('');

        $('#cont-registrar-comentario').css('display','block');
        $('#cont-tabla-comentario, #cont-editar-comentario, #cont-detalle-comentario, #cont-confirmar-comentario, #cont-anular-comentario').css('display','none');
    }
    function editar_comentario(idcomentario) {
        removecarga({input:'#mx-carga'});
        $('#cont-editar-comentario').css('display','block');
        $('#cont-tabla-comentario, #cont-registrar-comentario, #cont-detalle-comentario, #cont-confirmar-comentario, #cont-anular-comentario').css('display','none');
        load('#load-comentario');
        $('#form-editar-comentario').css('display','none');
        $.ajax({
            url:  "{{url('backoffice/noticiacomentario/show-editar_comentario')}}",
            type: 'GET',
            data: {
                idcomentario : idcomentario
            },
            success: function (respuesta){
                $('#load-comentario').html('');
                $('#form-editar-comentario').css('display','block');
                $('#comentario_editar_comentario').val(respuesta['comentario'].comentario);
                $('#comentario_editar_idcomentario').val(respuesta['comentario'].id);
                $('#comentario_editar_idnoticia').select2({
                  placeholder: '-- Seleccionar --',
                  minimumResultsForSearch: -1
                }).val(respuesta['comentario'].idnoticia).trigger('change');
            }
        });
    }
    function detalle_comentario(idcomentario) {
        removecarga({input:'#mx-carga'});
        $('#cont-detalle-comentario').css('display','block');
        $('#cont-tabla-comentario, #cont-registrar-comentario, #cont-editar-comentario, #cont-confirmar-comentario, #cont-anular-comentario').css('display','none');
        load('#load-comentario');
        $('#form-detalle-comentario').css('display','none');
        $.ajax({
            url:  "{{url('backoffice/noticiacomentario/show-editar_comentario')}}",
            type: 'GET',
            data: {
                idcomentario : idcomentario
            },
            success: function (respuesta){
                $('#load-comentario').html('');
                $('#form-detalle-comentario').css('display','block');
                $('#comentario_detalle_comentario').val(respuesta['comentario'].comentario);
                $('#comentario_detalle_idcomentario').val(respuesta['comentario'].id);
                $('#comentario_detalle_idnoticia').select2({
                  placeholder: '-- Seleccionar --',
                  minimumResultsForSearch: -1
                }).val(respuesta['comentario'].idnoticia).trigger('change');
            }
        });
    }
    function confirmar_comentario(idcomentario) {
        removecarga({input:'#mx-carga'});
        $('#cont-confirmar-comentario').css('display','block');
        $('#cont-tabla-comentario, #cont-registrar-comentario, #cont-editar-comentario, #cont-detalle-comentario, #cont-anular-comentario').css('display','none');
        load('#load-comentario');
        $('#form-confirmar-comentario').css('display','none');
        $.ajax({
            url:  "{{url('backoffice/noticiacomentario/show-editar_comentario')}}",
            type: 'GET',
            data: {
                idcomentario : idcomentario
            },
            success: function (respuesta){
                $('#load-comentario').html('');
                $('#form-confirmar-comentario').css('display','block');
                $('#comentario_confirmar_comentario').val(respuesta['comentario'].comentario);
                $('#comentario_confirmar_idcomentario').val(respuesta['comentario'].id);
                $('#comentario_confirmar_idnoticia').select2({
                  placeholder: '-- Seleccionar --',
                  minimumResultsForSearch: -1
                }).val(respuesta['comentario'].idnoticia).trigger('change');
            }
        });
    }
    function anular_comentario(idcomentario) {
        removecarga({input:'#mx-carga'});
        $('#cont-anular-comentario').css('display','block');
        $('#cont-tabla-comentario, #cont-registrar-comentario, #cont-editar-comentario, #cont-confirmar-comentario, #cont-detalle-comentario').css('display','none');
        load('#load-comentario');
        $('#form-anular-comentario').css('display','none');
        $.ajax({
            url:  "{{url('backoffice/noticiacomentario/show-editar_comentario')}}",
            type: 'GET',
            data: {
                idcomentario : idcomentario
            },
            success: function (respuesta){
                $('#load-comentario').html('');
                $('#form-anular-comentario').css('display','block');
                $('#comentario_anular_comentario').val(respuesta['comentario'].comentario);
                $('#comentario_anular_idcomentario').val(respuesta['comentario'].id);
                $('#comentario_anular_idnoticia').select2({
                  placeholder: '-- Seleccionar --',
                  minimumResultsForSearch: -1
                }).val(respuesta['comentario'].idnoticia).trigger('change');
            }
        });
    }
</script>
@endsection