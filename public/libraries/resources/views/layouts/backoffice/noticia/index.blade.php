@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div id="cont-tabla-noticia">
    <div class="list-single-main-wrapper fl-wrap">
        <div class="breadcrumbs gradient-bg fl-wrap">
          <span>Noticias</span>
          <a class="btn btn-warning" href="javascript:;" onclick="registrar_noticia()"><i class="fa fa-angle-right"></i> Registrar</a></a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table" id="tabla-contenido-noticia">
        </table>
    </div>
</div>

<!-- registrar -->
<div id="cont-registrar-noticia" style="display:none;">
  <div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Noticia</span>
      <a class="btn btn-success" href="javascript:;" onclick="index_noticia()"><i class="fa fa-angle-left"></i> Atras</a></a>
    </div>
  </div>
  <form action="javascript:;" onsubmit="registrarNoticia(this)">
    <div class="profile-edit-container">
      <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
            <div class="col-sm-6">
              <label>Título *</label>
              <input type="text" id="noticia_registrar_titulo">
            </div>
            <div class="col-md-6">
                <label>Imagen</label>
                <div class="fuzone" id="cont-noticia_registrar_imagen" style="height: 177px;">
                    <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                    <input type="file" class="upload" id="noticia_registrar_imagen">
                    <div id="resultado-noticia_registrar_imagen"></div>
                </div>
            </div>
            <div class="col-sm-12" style="text-align: left;float:left">
              <textarea id="noticia_registrar_descripcion"></textarea>
            </div>
          </div>
        </div>
      </div>
      <div class="custom-form">
        <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width: 100%; margin-top: 15px;">Guardar Cambios</button>
      </div>
    </div>
  </form>
</div>

<!-- editar -->
<div id="cont-editar-noticia"style="display:none;">
  <div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Editar Noticia</span>
      <a class="btn btn-success" href="javascript:;" onclick="index_noticia()"><i class="fa fa-angle-left"></i> Atras</a></a>
    </div>
  </div>
  <div id="form-editar-noticia" style="display:none;">
    <form action="javascript:;" onsubmit="editarNoticia(this)">
      <div class="profile-edit-container">
        <div class="profile-edit-container">
          <div class="custom-form">
            <input type="hidden" id="noticia_editar_idnoticia" value="0">
            <div class="row">
              <div class="col-sm-6">
                <label>Título *</label>
                <input type="text" id="noticia_editar_titulo">
              </div>
              <div class="col-md-6">
                  <label>Imagen</label>
                  <div class="fuzone" id="cont-noticia_editar_imagen" style="height: 177px;">
                      <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                      <input type="file" class="upload" id="noticia_editar_imagen">
                      <div id="resultado-noticia_editar_imagen"></div>
                  </div>
              </div>
              <div class="col-sm-12" style="text-align: left;float:left">
                <textarea id="noticia_editar_descripcion"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="custom-form">
          <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width: 100%; margin-top: 15px;">Actualizar Noticia</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- confirmar -->
<div id="cont-confirmar-noticia"style="display:none;">
    <div class="list-single-main-wrapper fl-wrap">
        <div class="breadcrumbs gradient-bg fl-wrap">
            <span>Confirmar Noticia</span>
            <a class="btn btn-success" href="javascript:;" onclick="index_noticia()"><i class="fa fa-angle-left"></i> Atras</a></a>
        </div>
    </div>
    <div id="form-confirmar-noticia" style="display:none;">
        <form action="javascript:;" 
              onsubmit="callback({
                                      route:  'backoffice/noticia/0',
                                      method: 'PUT',
                                      data:   {
                                          view: 'confirmar-noticia',
                                          idnoticia: $('#noticia_confirmar_idnoticia').val()
                                      }
                                  },
                                  function(resultado){
                                      index_noticia();
                                  },this)">
          
            <div class="profile-edit-container">
              <div class="profile-edit-container">
                <div class="custom-form">
                  <input type="hidden" id="noticia_confirmar_idnoticia" value="0">
                  <div class="row">
                      <div class="col-sm-6">
                          <label>Titulo *</label>
                          <input type="text" id="noticia_confirmar_titulo" disabled>
                      </div>
                      <div class="col-md-6">
                          <label>Imagen</label>
                          <div class="fuzone" id="cont-noticia_confirmar_imagen" style="height: 177px;">
                              <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                              <input type="file" class="upload" id="noticia_confirmar_imagen">
                              <div id="resultado-noticia_confirmar_imagen"></div>
                          </div>
                      </div>
                      <div class="col-sm-12" style="text-align: left; float:left">
                          <textarea id="noticia_confirmar_descripcion" disabled></textarea>
                      </div>
                  </div>
                </div>
              </div>
              <div class="custom-form">
                <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width: 100%; margin-top: 15px;">Confirmar Noticia</button>
              </div>
            </div>
        </form>
    </div>
</div>

<!-- detalle -->
<div id="cont-detalle-noticia"style="display:none;">
  <div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Detalle Noticia</span>
      <a class="btn btn-success" href="javascript:;" onclick="index_noticia()"><i class="fa fa-angle-left"></i> Atras</a></a>
    </div>
  </div>
  <div id="form-detalle-noticia" style="display:none;">
    <div class="profile-edit-container">
      <div class="profile-edit-container">
        <div class="custom-form">
          <input type="hidden" id="noticia_detalle_idnoticia" value="0">
          <div class="row">
            <div class="col-sm-6">
              <label>Título *</label>
              <input type="text" id="noticia_detalle_titulo" disabled>
            </div>
            <div class="col-md-6">
                <label>Imagen</label>
                <div class="fuzone" id="cont-noticia_detalle_imagen" style="height: 177px;">
                    <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                    <input type="file" class="upload" id="noticia_detalle_imagen">
                    <div id="resultado-noticia_detalle_imagen"></div>
                </div>
            </div>
            <div class="col-sm-12" style="text-align: left;float:left">
              <textarea id="noticia_detalle_descripcion" disabled></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- anular -->
<div id="cont-anular-noticia"style="display:none;">
    <div class="list-single-main-wrapper fl-wrap">
        <div class="breadcrumbs gradient-bg fl-wrap">
            <span>Anular Noticia</span>
            <a class="btn btn-success" href="javascript:;" onclick="index_noticia()"><i class="fa fa-angle-left"></i> Atras</a></a>
        </div>
    </div>
    <div id="form-anular-noticia" style="display:none;">
        <div class="mensaje-warning">
            <i class="fa fa-warning"></i> ¿Esta seguro de anular?</b>
        </div>
        <form action="javascript:;" 
              onsubmit="callback({
                                      route:  'backoffice/noticia/0',
                                      method: 'PUT',
                                      data:   {
                                          view: 'anular-noticia',
                                          idnoticia: $('#noticia_anular_idnoticia').val()
                                      }
                                  },
                                  function(resultado){
                                      index_noticia();
                                  },this)">
          
            <div class="profile-edit-container">
              <div class="profile-edit-container">
                <div class="custom-form">
                  <input type="hidden" id="noticia_anular_idnoticia" value="0">
                  <div class="row">
                      <div class="col-sm-6">
                          <label>Titulo *</label>
                          <input type="text" id="noticia_anular_titulo" disabled>
                      </div>
                      <div class="col-md-6">
                          <label>Imagen</label>
                          <div class="fuzone" id="cont-noticia_anular_imagen" style="height: 177px;">
                              <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                              <input type="file" class="upload" id="noticia_anular_imagen">
                              <div id="resultado-noticia_anular_imagen"></div>
                          </div>
                      </div>
                      <div class="col-sm-12" style="text-align: left; float:left">
                          <textarea id="noticia_anular_descripcion" disabled></textarea>
                      </div>
                  </div>
                </div>
              </div>
              <div class="custom-form">
                <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width: 100%; margin-top: 15px;">Anular Noticia</button>
              </div>
            </div>
        </form>
    </div>
</div>
<div id="load-noticia"></div>
@endsection
@section('scriptsbackoffice')
<script>
    // Noticia
    index_noticia();
    function index_noticia() {
        $('#cont-tabla-noticia').css('display','block');
        $('#cont-registrar-noticia, #cont-editar-noticia, #cont-detalle-noticia, #cont-confirmar-noticia, #cont-anular-noticia').css('display','none');
        load('#load-noticia');
        $('#tabla-contenido-noticia').html('');
        $.ajax({
            url:  "{{url('backoffice/noticia/show-index_noticia')}}",
            type: 'GET',
            data: {},
            success: function (respuesta){
                $('#load-noticia').html('');
                $('#tabla-contenido-noticia').html(respuesta);

                // menu tabla
                $("div#menu-opcion").on("click", function () {
                    $("ul",this).toggleClass("hu-menu-vis");
                    $("i",this).toggleClass("fa-angle-up");
                });
            }
        });
    }
    function registrar_noticia() {
        removecarga({input:'#mx-carga'});
        $('#noticia_registrar_titulo').val('');
        $('#noticia_registrar_descripcion').val('');

        $('#cont-registrar-noticia').css('display','block');
        $('#cont-tabla-noticia, #cont-editar-noticia, #cont-detalle-noticia, #cont-confirmar-noticia, #cont-anular-noticia').css('display','none');
    }
    uploadfile({
      input:  "#noticia_registrar_imagen",
      cont:   "#cont-noticia_registrar_imagen",
      result: "#resultado-noticia_registrar_imagen"
    });
    function editar_noticia(idnoticia) {
        removecarga({input:'#mx-carga'});
        $('#cont-editar-noticia').css('display','block');
        $('#cont-tabla-noticia, #cont-registrar-noticia, #cont-detalle-noticia, #cont-confirmar-noticia, #cont-anular-noticia').css('display','none');
        load('#load-noticia');
        $('#form-editar-noticia').css('display','none');
        $.ajax({
            url:  "{{url('backoffice/noticia/show-editar_noticia')}}",
            type: 'GET',
            data: {
                idnoticia : idnoticia
            },
            success: function (respuesta){
                $('#load-noticia').html('');
                $('#form-editar-noticia').css('display','block');
                $('#noticia_editar_titulo').val(respuesta['noticia'].titulo);
                $('#noticia_editar_idnoticia').val(respuesta['noticia'].id);
                editorEditar.setContents(respuesta['noticia'].descripcion);
                uploadfile({
                  input:  "#noticia_editar_imagen",
                  cont:   "#cont-noticia_editar_imagen",
                  result: "#resultado-noticia_editar_imagen",
                  ruta:   "{{ url('public/backoffice/noticia/')}}",
                  image:  respuesta['noticia'].imagen
                });
            }
        });
    }
    function detalle_noticia(idnoticia) {
        removecarga({input:'#mx-carga'});
        $('#cont-detalle-noticia').css('display','block');
        $('#cont-tabla-noticia, #cont-registrar-noticia, #cont-editar-noticia, #cont-confirmar-noticia, #cont-anular-noticia').css('display','none');
        load('#load-noticia');
        $('#form-detalle-noticia').css('display','none');
        $.ajax({
            url:  "{{url('backoffice/noticia/show-editar_noticia')}}",
            type: 'GET',
            data: {
                idnoticia : idnoticia
            },
            success: function (respuesta){
                $('#load-noticia').html('');
                $('#form-detalle-noticia').css('display','block');
                $('#noticia_detalle_titulo').val(respuesta['noticia'].titulo);
                $('#noticia_detalle_idnoticia').val(respuesta['noticia'].id);
                editorDetalle.setContents(respuesta['noticia'].descripcion);
                uploadfile({
                  input:  "#noticia_detalle_imagen",
                  cont:   "#cont-noticia_detalle_imagen",
                  result: "#resultado-noticia_detalle_imagen",
                  ruta:   "{{ url('public/backoffice/noticia/')}}",
                  image:  respuesta['noticia'].imagen
                });
            }
        });
    }
    function confirmar_noticia(idnoticia) {
        removecarga({input:'#mx-carga'});
        $('#cont-confirmar-noticia').css('display','block');
        $('#cont-tabla-noticia, #cont-registrar-noticia, #cont-editar-noticia, #cont-detalle-noticia, #cont-anular-noticia').css('display','none');
        load('#load-noticia');
        $('#form-confirmar-noticia').css('display','none');
        $.ajax({
            url:  "{{url('backoffice/noticia/show-editar_noticia')}}",
            type: 'GET',
            data: {
                idnoticia : idnoticia
            },
            success: function (respuesta){
                $('#load-noticia').html('');
                $('#form-confirmar-noticia').css('display','block');
                $('#noticia_confirmar_titulo').val(respuesta['noticia'].titulo);
                $('#noticia_confirmar_idnoticia').val(respuesta['noticia'].id);
                editorConfirmar.setContents(respuesta['noticia'].descripcion);
                uploadfile({
                  input:  "#noticia_confirmar_imagen",
                  cont:   "#cont-noticia_confirmar_imagen",
                  result: "#resultado-noticia_confirmar_imagen",
                  ruta:   "{{ url('public/backoffice/noticia/')}}",
                  image:  respuesta['noticia'].imagen
                });
            }
        });
    }
    function anular_noticia(idnoticia) {
        removecarga({input:'#mx-carga'});
        $('#cont-anular-noticia').css('display','block');
        $('#cont-tabla-noticia, #cont-registrar-noticia, #cont-editar-noticia, #cont-confirmar-noticia, #cont-detalle-noticia').css('display','none');
        load('#load-noticia');
        $('#form-anular-noticia').css('display','none');
        $.ajax({
            url:  "{{url('backoffice/noticia/show-editar_noticia')}}",
            type: 'GET',
            data: {
                idnoticia : idnoticia
            },
            success: function (respuesta){
                $('#load-noticia').html('');
                $('#form-anular-noticia').css('display','block');
                $('#noticia_anular_titulo').val(respuesta['noticia'].titulo);
                $('#noticia_anular_idnoticia').val(respuesta['noticia'].id);
                editorAnular.setContents(respuesta['noticia'].descripcion);
                uploadfile({
                  input:  "#noticia_anular_imagen",
                  cont:   "#cont-noticia_anular_imagen",
                  result: "#resultado-noticia_anular_imagen",
                  ruta:   "{{ url('public/backoffice/noticia/')}}",
                  image:  respuesta['noticia'].imagen
                });
            }
        });
    }
</script>
<!-- Editor de noticias -->
<link href="https://kothing.github.io/editor/dist/css/kothing-editor.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.11.1/dist/katex.min.css"/>
<script src="https://kothing.github.io/editor/dist/kothing-editor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/katex@0.11.1/dist/katex.min.js"></script>
<script>
    var editorRegistar = editor('noticia_registrar_descripcion');
    var editorEditar = editor('noticia_editar_descripcion');
    var editorConfirmar = editor('noticia_confirmar_descripcion');
    var editorDetalle = editor('noticia_detalle_descripcion');
    var editorAnular = editor('noticia_anular_descripcion');
  
    function editor(input) {
        var editorRegistar = KothingEditor.create(input, {
            display: "block",
            width: "100%",
            height: "auto",
            popupDisplay: "full",
            katex: katex,
            toolbarItem: [
                ["undo", "redo"],
                ["font", "fontSize", "formatBlock"],
                [
                  "bold",
                  "underline",
                  "italic",
                  "strike",
                  "subscript",
                  "superscript",
                  "fontColor",
                  "hiliteColor",
                ],
                ["outdent", "indent", "align", "list", "horizontalRule"],
                ["link", "table", "image", "audio", "video"],
                ["lineHeight", "paragraphStyle", "textStyle"],
                ["showBlocks", "codeView"],
                ["math"],
                ["preview", "print", "fullScreen"],
                ["save", "template"],
                ["removeFormat"],
            ],
            templates: [
              {
                name: "Template-1",
                html: "<p>HTML source1</p>",
              },
              {
                name: "Template-2",
                html: "<p>HTML source2</p>",
              },
            ],
            charCounter: true,
        });
        return editorRegistar;
    }
  
    function registrarNoticia(pthis){
        callback({
            route:  'backoffice/noticia',
            method: 'POST',
            data:   {
                view: 'registrar-noticia',
                noticia_registrar_descripcion: editorRegistar.getContents()
            }
        },
        function(resultado){
            index_noticia();
        },pthis)
    }

    function editarNoticia(pthis) {
        callback({
            route:  'backoffice/noticia/0',
            method: 'PUT',
            data:   {
                view: 'editar-noticia',
                idnoticia: $('#noticia_editar_idnoticia').val(),
                noticia_editar_descripcion: editorEditar.getContents()
            }
        },
        function(resultado){
            index_noticia();
        },pthis)
    }
</script>
<!-- Fin Editor de noticias -->
@endsection