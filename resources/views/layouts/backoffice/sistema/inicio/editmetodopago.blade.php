@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>MÉTODO DE PAGO</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio') }}"><i class="fa fa-angle-left"></i> Ir a Inicio</a></a>
    </div>
</div>
  
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/perfil/{{ $usuario->id }}',
        method: 'PUT'
    },
    function(resultado){
        location.href = '{{ url('backoffice/perfil') }}';                                                                         
    },this)">
    <input type="hidden" value="editperfil" id="view"/>
    <!-- profile-edit-container--> 
    <div class="profile-edit-container">
        <div class="custom-form">
            <label>Banco *</label>
            <select id="idbanco">
              <option></option>
              @foreach($bancos as $value)
                  <option value="{{ $value->nombre }}">{{ $value->nombre }}</option>
              @endforeach
            </select>
            <label>Número de Cuenta</label>
            <input type="text" value="{{ $usuario->direccion }}" id="numerocuenta"/>
            <label>Número de Cuenta Interbancaria (CCI)</label>
            <input type="text" value="{{ $usuario->direccion }}" id="numerocuentainterbancaria"/>
        </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios<i class="fa fa-angle-right"></i></button>
        </div>
    </div>
</form>
@endsection
@section('subscripts')
<script>
$("#idbanco").select2({
    placeholder: "---  Seleccionar ---"
});
</script>
@endsection