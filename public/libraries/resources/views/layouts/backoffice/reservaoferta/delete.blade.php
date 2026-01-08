@extends('layouts.backoffice.master')
@section('cuerpobackoffice')

<div class="profile-edit-container">
    <div class="profile-edit-header fl-wrap">
        <h4>Eliminar Reserva de Oferta</h4>
    </div>
    <form class="js-validation-signin px-30" 
                                          action="javascript:;" 
                                          onsubmit="callback({
                                            route: 'backoffice/reservaoferta/{{ $reservaoferta->id }}',
                                            method: 'DELETE'
                                        },
                                        function(resultado){
                                            if (resultado.resultado == 'CORRECTO') {
                                                location.href = '{{ url('backoffice/reservaoferta') }}';                                                                            
                                            }                                                                                                                    
                                        },this)" enctype="multipart/form-data">
      <input type="hidden" value="deleteusuario" id="view"/>
      <div class="custom-form">
        <p>¿Esta Seguro de Eliminar la Reserva de Oferta <b>"{{ $reservaoferta->ofertanombre }}"</b>?</p>
      </div>
      <div class="profile-edit-container">
          <div class="custom-form">
              <button type="submit" class="btn  big-btn  color-bg flat-btn">Eliminar Reserva de Oferta <i class="fa fa-angle-right"></i></button>
          </div>
      </div>
    </form> 
</div>

@endsection