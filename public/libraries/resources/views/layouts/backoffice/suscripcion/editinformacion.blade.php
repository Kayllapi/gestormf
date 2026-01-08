@extends('layouts.backoffice.master')
@section('cuerpobackoffice')

<form class="js-validation-signin px-30" 
                                          action="javascript:;" 
                                          onsubmit="callback({
                                            route: 'backoffice/suscripcion/{{ $suscripcion->id }}',
                                            method: 'PUT'
                                        },
                                        function(resultado){
                                            if (resultado.resultado == 'CORRECTO') {
                                                location.href = '{{ url('backoffice/suscripcion') }}';                                                                            
                                            }                                                                                 
                                        },this)" enctype="multipart/form-data">
<input type="hidden" value="editinformacion" id="view"/>

<div class="profile-edit-container">
  <div class="profile-edit-header fl-wrap">
      <h4>Editar Suscripción</h4>
  </div>
  <div class="custom-form">
      <div class="row">
        <div class="col-md-6">
          <div class="row">
            <div class="col-md-12">
              <label>Correo * <i class="fa fa-id-card"></i></label>
              <input type="text" id="email" value="{{ $suscripcion->email }}">
            </div>          
          </div>
        </div>
    </div>
</div>
  
  
<div class="profile-edit-container">
    <div class="custom-form">
        <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios<i class="fa fa-angle-right"></i></button>
    </div>
</div>
</form>
@endsection