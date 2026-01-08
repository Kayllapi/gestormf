@extends('layouts.backoffice.master')
@section('cuerpobackoffice')

<div class="profile-edit-container">
    <div class="profile-edit-header fl-wrap">
        <h4>Eliminar Mensaje de Contacto</h4>
    </div>
    <form class="js-validation-signin px-30" 
                                          action="javascript:;" 
                                          onsubmit="callback({
                                            route: 'backoffice/mensajecontacto/{{ $mensajecontacto->id }}',
                                            method: 'DELETE'
                                        },
                                        function(resultado){
                                            if (resultado.resultado == 'CORRECTO') {
                                                location.href = '{{ url('backoffice/mensajecontacto') }}';                                                                            
                                            }                                                                                                                    
                                        },this)" enctype="multipart/form-data">
      <input type="hidden" value="deleteusuario" id="view"/>
      <div class="custom-form">
        <p>¿Esta Seguro de Eliminar la Calificación de <b>"{{ $mensajecontacto->id }}"?</b>?</p>
      </div>
      <div class="profile-edit-container">
          <div class="custom-form">
              <button type="submit" class="btn  big-btn  color-bg flat-btn">Eliminar Mensaje de Contacto <i class="fa fa-angle-right"></i></button>
          </div>
      </div>
    </form> 
</div>

@endsection