@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<?php $page = (isset($_GET['page'])?'page='.$_GET['page']:'') ?>
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>ELIMINAR USUARIO</span>
      <a class="btn btn-success" href="{{ url('backoffice/cineusuario?'.$page) }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div class="profile-edit-container">
    <form class="js-validation-signin px-30" 
          action="javascript:;" 
          onsubmit="callback({
              route: 'backoffice/cineusuario/{{ $usuario->id }}',
              method: 'DELETE'
          },
          function(resultado){
              if (resultado.resultado == 'CORRECTO') {
                  location.href = '{{ url('backoffice/cineusuario?'.$page) }}';                                                                            
              }                                                                                                                    
          },this)" enctype="multipart/form-data">
      <input type="hidden" value="deletecine" id="view"/>
      <div class="profile-edit-container">
        <div class="custom-form">
            <div class="row">
              <div class="col-md-6">
                  <label>Nombre</label>
                  <input type="text" id="nombre" value="{{ $usuario->nombre }}" disabled/>
              </div>
              <div class="col-md-6">
                  <label>Usuario</label>
                  <input type="text" id="email" value="{{ $usuario->usuario }}" disabled/>
              </div>
          </div>
        </div>
    </div>
      <div class="mensaje-warning">
        <i class="fa fa-warning"></i> ¿Esta seguro de Eliminar el Usuario?
      </div>
      <div class="profile-edit-container">
          <div class="custom-form">
              <button type="submit" class="btn  big-btn  color-bg flat-btn btn-danger" style="width:100%;">Eliminar</button>
          </div>
      </div>
    </form> 
</div>
@endsection