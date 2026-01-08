@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Cambiar Contraseña</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio') }}"><i class="fa fa-angle-left"></i> Ir a Inicio</a></a>
    </div>
</div>
  
<form class="js-validation-signin px-30" 
        action="javascript:;" 
        onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{$tienda->id}}/inicio/{{ $usuario->id }}',
        method: 'PUT'
    },
    function(resultado){                                                            
    },this)">
    <input type="hidden" value="editpassword" id="view"/>
    <div class="profile-edit-container">
        <div class="custom-form no-icons">
            <div class="pass-input-wrap fl-wrap">
                <label>Contraseña Actual <span class="eye"><i class="fa fa-eye" aria-hidden="true"></i> </span></label>
                
                <input type="password" class="pass-input" value="" id="antpassword"/>
            </div>
            <div class="pass-input-wrap fl-wrap">
                <label>Nueva Contraseña <span class="eye"><i class="fa fa-eye" aria-hidden="true"></i> </span></label>
                
                <input type="password" class="pass-input" value="" id="password"/>
            </div>
            <div class="pass-input-wrap fl-wrap">
                <label>Confirmar Nueva Contraseña <span class="eye"><i class="fa fa-eye" aria-hidden="true"></i> </span></label>
                
                <input type="password" class="pass-input" value="" id="password_confirmation"/>
            </div>
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios</button>
        </div>
    </div> 	
</form>
@endsection