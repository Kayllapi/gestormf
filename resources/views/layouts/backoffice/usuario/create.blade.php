@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Usuario</span>
      <a class="btn btn-success" href="{{ url('backoffice/usuario') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
                  route: 'backoffice/usuario',
                  method: 'POST',
                  data: {
                      view : 'create'
                  }
              },
              function(resultado){
                if (resultado.resultado == 'CORRECTO') {
                  location.href = '{{ url('backoffice/usuario') }}';                           
                }
              },this)">
    <div class="profile-edit-container">
        <div class="custom-form">
            <div class="row">
              <div class="col-md-6">
                  <label>DNI (8 Digitos) *</label>
                  <input type="text" id="dni"/>
                  <label>Nombre *</label>
                  <input type="text" id="nombre"/>
                  <label>Apellidos *</label>
                  <input type="text" id="apellidos"/>
              </div>
              <div class="col-md-6">
                  <label>Número de Teléfono *</label>
                  <input type="text" id="numerotelefono"/>
                  <label>Correo Electrónico (Usuario) *</label>
                  <input type="text" id="email"/>
                  <label>Contraseña *</label>
                  <input type="text" id="clave"/>
              </div>
          </div>
        </div>
    </div>
    <div class="custom-form">
        <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%;">Guardar Cambios</button>
    </div>
</form>
@endsection