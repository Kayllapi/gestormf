@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>ANULAR PAGO</span>
      <a class="btn btn-success" href="{{ url('backoffice/pagos') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div class="profile-edit-container">
    <form class="js-validation-signin px-30" 
                                          action="javascript:;" 
                                          onsubmit="callback({
                                            route: 'backoffice/pagos/{{ $planadquirido->id }}',
                                            method: 'DELETE'
                                        },
                                        function(resultado){
                                            if (resultado.resultado == 'CORRECTO') {
                                                location.href = '{{ url('backoffice/pagos') }}';                                                                            
                                            }                                                                                                                    
                                        },this)" enctype="multipart/form-data">
      <input type="hidden" value="anular" id="view"/>
      <div class="custom-form">
        <p>¿Esta Seguro de Anular el Pago de <b>"{{ $planadquirido->nombre }} {{ $planadquirido->apellidos }}"</b>?</p>
      </div>
      <div class="profile-edit-container">
          <div class="custom-form">
              <button type="submit" class="btn  big-btn  color-bg flat-btn">Anular Pago</button>
          </div>
      </div>
    </form> 
</div>                             
@endsection
