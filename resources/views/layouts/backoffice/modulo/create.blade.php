@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>REGISTRAR MÓDULO</span>
      <a class="btn btn-success" href="{{ url('backoffice/modulo') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" action="javascript:;" 
    onsubmit="callback({
                            route: 'backoffice/modulo',
                            method: 'POST'
                        },
                        function(resultado){
                            if (resultado.resultado == 'CORRECTO') {
                                location.href = '{{ url('backoffice/modulo') }}';                                                                            
                            }
                        },this)">
 
  <input type="hidden" value="create" id="view"/>
  <div class="profile-edit-container">
    <div class="profile-edit-container">
        <div class="custom-form">
            <div class="row">
                <div class="col-md-6">
                  <label>Nombre *</label>
                  <input type="text" id="nombre"/>
                  <label>Icono</label>
                  <input type="text" id="icono"/>
                </div>
                <div class="col-md-6">
                  <label>orden *</label>
                  <div class="quantity fl-wrap">
                    <div class="quantity-item">
                        <input type="button" value="-" class="minus">
                        <input type="text" id="orden" class="qty" min="1" max="100000" step="1" value="0" style="padding-left: 0px;">
                        <input type="button" value="+" class="plus">
                    </div>
                  </div>
                  <label>Estado *</label>
                  <select id="idestado">
                      <option value="1" selected>Activado</option>
                      <option value="2">Desactivado</option>
                  </select>
                </div>
            </div>
          </div>
      </div>
    <div class="custom-form">
        <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios</button>
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
</script>
@endsection