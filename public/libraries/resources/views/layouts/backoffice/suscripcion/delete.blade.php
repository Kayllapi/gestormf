@extends('layouts.backoffice.master')
@section('cuerpobackoffice')

<div class="profile-edit-container">
    <div class="profile-edit-header fl-wrap">
        <h4>Eliminar Suscripción</h4>
    </div>
    <form class="js-validation-signin px-30" 
                                          action="javascript:;" 
                                          onsubmit="callback({
                                            route: 'backoffice/suscripcion/{{ $suscripcion->id }}',
                                            method: 'DELETE'
                                        },
                                        function(resultado){
                                            if (resultado.resultado == 'CORRECTO') {
                                                location.href = '{{ url('backoffice/suscripcion') }}';                                                                            
                                            }                                                                                                                    
                                        },this)" enctype="multipart/form-data">
      <input type="hidden" value="deleteusuario" id="view"/>
      <div class="custom-form">
        <p>¿Esta Seguro de Eliminar la Suscripción de <b>"{{ $suscripcion->email }}"</b>?</p>
      </div>
      <div class="profile-edit-container">
          <div class="custom-form">
              <button type="submit" class="btn  big-btn  color-bg flat-btn">Eliminar Mensaje de Contacto <i class="fa fa-angle-right"></i></button>
          </div>
      </div>
    </form> 
</div>

@endsection