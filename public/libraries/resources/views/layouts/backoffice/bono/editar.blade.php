@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/bono/{{ $bono->id }}',
        method: 'PUT'
    },
    function(resultado){
        location.href = '{{ url('backoffice/bono') }}';                                                                            
    },this)" enctype="multipart/form-data">
    <input type="hidden" value="confirmacionpago" id="view"/>
    <!-- profile-edit-container--> 
    <div class="profile-edit-container">
        <div class="profile-edit-header fl-wrap">
            <h4>Bono</h4>
        </div>
        <div class="custom-form">
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <label>Nombre * <i class="fa fa-briefcase"></i></label>
                  <input type="text" id="nombre" value="{{$bono->nombre}}" />
                </div>
                <div class="col-md-12">
                  <label>Costo <i class="fa fa-briefcase"></i></label>
                  <input type="text" id="costo" value="{{$bono->porcentaje}}"/>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    <!-- profile-edit-container end-->  										
    <!-- profile-edit-container--> 
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios<i class="fa fa-angle-right"></i></button>
        </div>
    </div>
    <!-- profile-edit-container end-->  
</form>                             
@endsection
