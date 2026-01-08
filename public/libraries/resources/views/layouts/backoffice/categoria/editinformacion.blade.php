@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>EDITAR CATEGORIA</span>
      <a class="btn btn-success" href="{{ url('backoffice/categoria') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/categoria/{{ $categoria->id }}',
        method: 'PUT'
    },
    function(resultado){
        location.href = '{{ url('backoffice/categoria') }}';                                                                            
    },this)" enctype="multipart/form-data">
    <input type="hidden" value="editinformacion" id="view"/>
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <label>Nombre * <i class="fa fa-briefcase"></i></label>
                  <input type="text" value="{{ $categoria->nombre }}" id="nombre"/>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios</button>
        </div>
    </div>  
</form>                             
@endsection