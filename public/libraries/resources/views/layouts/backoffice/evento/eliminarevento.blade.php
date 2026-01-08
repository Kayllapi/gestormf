@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/evento/{{ $evento->id }}',
        method: 'PUT'
    },
    function(resultado){
      location.href = '{{ url('backoffice/evento') }}';                                                                            
    },this)" enctype="multipart/form-data">
    <input type="hidden" value="eventodelete" id="view"/>
    <!-- profile-edit-container--> 
    <div class="profile-edit-container">
        <div class="profile-edit-header fl-wrap">
            <h4>Eliminar</h4>
        </div>
        <div class="custom-form">
          <div class="row">
            <div class="col-md-6">
              <p>¿Seguro que desea eliminar el evento {{$evento->nombre}}?</p>
            </div>
          </div>
        </div>
    </div>
    <!-- profile-edit-container end-->  										
    <!-- profile-edit-container--> 
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Eliminar<i class="fa fa-angle-right"></i></button>
        </div>
    </div>
    <!-- profile-edit-container end-->  
</form>                             
@endsection
