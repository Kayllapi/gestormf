@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>REGISTRAR PERMISO</span>
      <a class="btn btn-success" href="{{ url('backoffice/permiso') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
  action="javascript:;" 
  onsubmit="callback({
      route: 'backoffice/permiso',
      method: 'POST'
  },
  function(resultado){
    if (resultado.resultado == 'CORRECTO') {
      location.href = '{{ url('backoffice/permiso') }}';                                                                            
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
                  <label>Categoria *</label>
                  <select id="idcategoria">
                    <option></option>
                    @foreach($categorias as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                  </select>
              </div>
              <div class="col-md-6">
                  <label>Tipo *</label>
                  <select id="idtipo">
                    <option></option>
                    <option value="1">Master</option>
                    <option value="2">Sistema</option>
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
$("#idtipo").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
});
$("#idcategoria").select2({
    placeholder: "---  Seleccionar ---"
});
</script>
@endsection