@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/plan',
        method: 'POST'
    },
    function(resultado){
      location.href = '{{ url('backoffice/plan/'.$idplan.'/edit?view=detalle') }}';                                                                            
    },this)" enctype="multipart/form-data">
    <input type="hidden" value="createdetalleplan" id="view"/>
    <input type="hidden" value="{{$idplan}}" id="idplan"/>
    <!-- profile-edit-container--> 
    <div class="profile-edit-container">
        <div class="profile-edit-header fl-wrap">
            <h4>Crea detalle plan</h4>
        </div>
        <div class="custom-form">
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <label>Nombre * <i class="fa fa-briefcase"></i></label>
                  <input type="text" id="nombre"/>
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
