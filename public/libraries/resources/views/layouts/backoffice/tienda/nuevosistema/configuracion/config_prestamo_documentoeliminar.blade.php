<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Eliminar Documento</span>
    <a class="btn btn-success" href="javascript:;" onclick="index_documento()"><i class="fa fa-angle-left"></i> Atras</a></a>
  </div>
</div>
<div class="mensaje-warning">
    <i class="fa fa-warning"></i> ¿Esta seguro de eliminar?</b>
</div>
<form action="javascript:;" onsubmit="callback({
                                                    route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion/0',
                                                    method: 'DELETE',
                                                    data:   {
                                                        view: 'eliminar-documento',
                                                        idprestamo_documento: $('#documento_eliminar_idprestamo_documento').val()
                                                    }
                                                },
                                                function(resultado){
                                                    index_documento();
                                                },this)">
    <input type="hidden" id="documento_eliminar_idprestamo_documento" value="0">
    <div class="row">
        <div class="col-sm-12">
            <label>Nombre *</label>
            <input type="text" id="documento_eliminar_nombre" value="{{ $prestamodocumento->nombre }}" disabled>
        </div>
        <div class="col-sm-12" style="text-align: left; float:left">
            <textarea id="documento_eliminar_contenido" disabled>{{ $prestamodocumento->contenido }}</textarea>
        </div>
    </div>
    <button type="submit" class="btn mx-btn-post">Eliminar Documento</button>
</form>


<link href="https://kothing.github.io/editor/dist/css/kothing-editor.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.11.1/dist/katex.min.css"/>
<script src="https://kothing.github.io/editor/dist/kothing-editor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/katex@0.11.1/dist/katex.min.js"></script>
<script>
  KothingEditor.create("documento_eliminar_contenido", {
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
</script>