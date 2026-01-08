@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>ELIMINAR PUNTOS</span>
      <a class="btn btn-success" href="{{ url('backoffice/puntoskay') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>

<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/puntoskay/{{ $puntoskay->id }}',
        method: 'DELETE',
        data:{
            view:'eliminar'        
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/puntoskay') }}';                                                                            
    },this)">
      <div class="mensaje-danger">
        ¿Estas Seguro de Eliminar los puntos KAY de <br><b>"{{ $puntoskay->usersemail }} - {{ $puntoskay->usersapellidos }}, {{ $puntoskay->usersnombre }}"</b>?
      </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Eliminar</button>
        </div>
    </div> 
</form>                           
@endsection