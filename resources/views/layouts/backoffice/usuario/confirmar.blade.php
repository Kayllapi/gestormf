@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>CONFIRMAR AUSUARIO</span>
      <a class="btn btn-success" href="{{ url('backoffice/usuario') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
                action="javascript:;" 
                onsubmit="callback({
                          
                  route: 'backoffice/usuario/{{ $usuario->id }}',
                  method: 'PUT',
                  data:{
                      view:'confirmar'       
                  }        
              },
              function(resultado){
                          
                 location.href = '{{ url('backoffice/usuario') }}';                                                                            
                          
              },this)">
    <p>¿Está seguro de confirmar al Usuario <b>"{{ $usuario->apellidos }}, {{ $usuario->nombre }}"</b>?</p>
    <div class="custom-form">
        <button type="submit" class="btn  big-btn  color-bg flat-btn">Confirmar</button>
    </div>
</form>                                             
@endsection