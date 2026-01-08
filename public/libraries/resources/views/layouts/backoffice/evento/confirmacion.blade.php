@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/evento/{{ $registrado->id }}',
        method: 'PUT'
    },
    function(resultado){
        location.href = '{{ url('backoffice/evento') }}';                                                                            
    },this)" enctype="multipart/form-data">
    <input type="hidden" value="confirmacion" id="view"/>
    <!-- profile-edit-container--> 
    <div class="profile-edit-container">
        <div class="profile-edit-header fl-wrap">
            <h4>Confirmacion de Asistencia</h4>
        </div>
        <div class="custom-form">
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <label>Datos Personales * <i class="fa fa-briefcase"></i></label>
                  <input type="text" value="{{$registrado->nombre}}" readonly/>
                </div>
                <div class="col-md-12">
                  <label>Correo * <i class="fa fa-briefcase"></i></label>
                  <input type="text" value="{{$registrado->correo}}" readonly/>
                </div>
                <div class="col-md-12">
                  <label>Celular * <i class="fa fa-briefcase"></i></label>
                  <input type="text" value="{{$registrado->telefono}}" readonly/>
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
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Confirmar<i class="fa fa-angle-right"></i></button>
        </div>
    </div>
    <!-- profile-edit-container end-->  
</form>                             
@endsection
