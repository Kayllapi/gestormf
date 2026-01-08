<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Eliminar Documento</span>
    <a class="btn btn-success" href="javascript:;" onclick="index_documento()"><i class="fa fa-angle-left"></i> Atras</a></a>
  </div>
</div>
<div class="mensaje-warning">
    <i class="fa fa-warning"></i> ¿Esta seguro de eliminar?</b>
</div>
<form action="javascript:;"
      onsubmit="callback({
          route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion/0',
          method: 'DELETE',
          data:   {
              view: 'eliminar-documento',
              idprestamo_documento: {{$prestamodocumento->id}}
          }
      },
      function(resultado){
          index_documento();
      },this)">
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
<script>
  CKEDITOR.replace('documento_eliminar_contenido', {
      height: 1000,
      toolbarGroups: [
        { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
        { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
        { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
        { name: 'forms', groups: [ 'forms' ] },
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
        { name: 'links', groups: [ 'links' ] },
        { name: 'insert', groups: [ 'insert' ] },
        { name: 'styles', groups: [ 'styles' ] },
        { name: 'colors', groups: [ 'colors' ] },
        { name: 'tools', groups: [ 'tools' ] },
        { name: 'others', groups: [ 'others' ] },
        { name: 'about', groups: [ 'about' ] }
      ],
      removeButtons: 'Source,Save,ExportPdf,Print,Cut,Find,Replace,SelectAll,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Iframe,About',
      contentsCss: [
        'https://cdn.ckeditor.com/4.19.0/full-all/contents.css',
        'https://ckeditor.com/docs/ckeditor4/4.19.0/examples/assets/css/pastefromword.css'
      ],
      bodyClass: 'document-editor'
    });
</script>