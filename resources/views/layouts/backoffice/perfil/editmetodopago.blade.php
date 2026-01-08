@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>MÉTODO DE PAGO</span>
      <a class="btn btn-success" href="{{ url('backoffice/inicio') }}"><i class="fa fa-angle-left"></i> Ir a Inicio</a></a>
    </div>
</div>
  
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/perfil/0',
        method: 'PUT'
    },
    function(resultado){
        location.href = '{{ url('backoffice/perfil/1/edit?view=editmetodopago') }}';                                                                         
    },this)">
    <input type="hidden" value="editmetodopago" id="view"/>
    <!-- profile-edit-container--> 
    <div class="profile-edit-container">
        <div class="custom-form">
            <label>Banco *</label>
            <select id="idbanco">
              <option></option>
              @foreach($bancos as $value)
                  <option value="{{ $value->id }}">{{ $value->nombre }}</option>
              @endforeach
            </select>
            <label>Número de Cuenta</label>
            <input type="text" value="{{ $usuario!=''?$usuario->numerocuenta:'' }}" id="numerocuenta"/>
            <label>Número de Cuenta Interbancaria (CCI)</label>
            <input type="text" value="{{ $usuario!=''?$usuario->numerocuentainterbancario:'' }}" id="numerocuentainterbancario"/>
        </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios<i class="fa fa-angle-right"></i></button>
        </div>
    </div>
</form>
@endsection
@section('scriptsbackoffice')
<script>
@if($usuario!='')
$("#idbanco").select2({
    placeholder: "---  Seleccionar ---"
}).val({{ $usuario->idbanco }}).trigger("change");
@else
$("#idbanco").select2({
    placeholder: "---  Seleccionar ---"
});
@endif
</script>
@endsection