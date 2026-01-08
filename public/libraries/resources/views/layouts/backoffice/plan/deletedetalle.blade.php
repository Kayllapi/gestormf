@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/plan/{{ $plandetalle->id }}',
        method: 'PUT'
    },
    function(resultado){
        location.href = '{{ url('backoffice/plan/'.$plandetalle->idplan.'/edit?view=detalle') }}';                                                                            
    },this)" enctype="multipart/form-data">
    <input type="hidden" value="deletedetalle" id="view"/>
    <!-- profile-edit-container--> 
    <div class="profile-edit-container">
        <div class="profile-edit-header fl-wrap">
            <h4>Elminar Detalle de Plan</h4>
        </div>
        <div class="custom-form">
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <label>Nombre * <i class="fa fa-briefcase"></i></label>
                  <input type="text" id="nombre" value="{{$plandetalle->contenido}}" readonly />
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
            <button type="submit" class="btn big-btn color-bg flat-btn" style="background: #E91E63;">Si, Eliminar<i class="fa fa-angle-right"></i></button>
        </div>
    </div>
    <!-- profile-edit-container end-->  
</form>                             
@endsection
