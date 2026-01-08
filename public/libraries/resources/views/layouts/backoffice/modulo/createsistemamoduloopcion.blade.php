@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>REGISTRAR SISTEMA-MÓDULO-OPCIÓN</span>
      <a class="btn btn-success" href="{{ url('backoffice/modulo') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" action="javascript:;" 
      onsubmit="callback({
                              route: 'backoffice/modulo',
                              method: 'POST',
                              data:{
                                  'view' : 'createsistemamoduloopcion',
                                  'idmodulo' : {{ $modulo->id }}
                              }
                          },
                          function(resultado){
                              if (resultado.resultado == 'CORRECTO') {
                                  location.href = '{{ url('backoffice/modulo') }}';
                              }
                          },this)">
    <div class="profile-edit-container">
        <div class="custom-form">
            <div class="row">
                <div class="col-md-6">
                    <label>Nombre *</label>
                    <input type="text" id="nombre"/>
                    <label>Icono</label>
                    <input type="text" id="icono"/>
                    <label>Imagen Icono (140x140)</label>
                    <div class="fuzone" id="cont-fileupload">
                        <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                        <input type="file" class="upload" id="imagen">
                        <div id="resultado-imagen"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label>orden *</label>
                    <input type="text" id="orden" min="1">
                  <label>opcion *</label>
                  <input type="text" id="opcion"/>
                    <label>Estado *</label>
                    <select id="idestado">
                        <option value="1" selected>Activado</option>
                        <option value="2">Desactivado</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%;">Guardar Cambios</button>
        </div>
    </div>
</form>
@endsection
@section('scriptsbackoffice')
<script>
    $("#idestado").select2({
        placeholder: "---  Seleccionar ---",
        minimumResultsForSearch: -1
    });
  
    uploadfile({
      input:"#imagen",
      cont:"#cont-fileupload",
      result:"#resultado-imagen"
    }); 
</script>
@endsection