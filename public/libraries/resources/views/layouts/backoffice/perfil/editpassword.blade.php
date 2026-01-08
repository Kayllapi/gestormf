@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>CAMBIAR CONTRASEÑA</span>
      <a class="btn btn-success" href="{{ url('backoffice/inicio') }}"><i class="fa fa-angle-left"></i> Ir a Inicio</a></a>
    </div>
</div>
  
<form class="js-validation-signin px-30" 
        action="javascript:;" 
        onsubmit="callback({
        route: 'backoffice/perfil/{{ $usuario->id }}',
        method: 'PUT'
    },
    function(resultado){                                                            
    },this)">
    <input type="hidden" value="editpassword" id="view"/>
    <div class="profile-edit-container">
        <div class="custom-form no-icons">
            <div class="pass-input-wrap fl-wrap">
                <label>Contraseña Actual</label>
                <input type="password" class="pass-input" value="" id="antpassword"/>
                <span class="eye"><i class="fa fa-eye" aria-hidden="true"></i> </span>
            </div>
            <div class="pass-input-wrap fl-wrap">
                <label>Nueva Contraseña</label>
                <input type="password" class="pass-input" value="" id="password"/>
                <span class="eye"><i class="fa fa-eye" aria-hidden="true"></i> </span>
            </div>
            <div class="pass-input-wrap fl-wrap">
                <label>Confirmar Nueva Contraseña</label>
                <input type="password" class="pass-input" value="" id="password_confirmation"/>
                <span class="eye"><i class="fa fa-eye" aria-hidden="true"></i> </span>
            </div>
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios</button>
        </div>
    </div> 	
</form>
<style>

  .eye{
    margin-top: -40px !important;
    margin-right: 15px !important;
  }
</style>
@endsection