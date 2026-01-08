@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>REGISTRAR</span>
      <a class="btn btn-warning" href="{{ url('backoffice/evento') }}"><i class="fa fa-angle-left"></i>ATRÁS</a></a>
    </div>
</div>

<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/evento',
        method: 'POST',
        view:'user_event'
    },
    function(resultado){
      if (resultado.resultado == 'CORRECTO') {
        location.href = '{{ url('backoffice/evento/'.$evento->id.'/edit?view=registrado') }}';                                                                            
      }
    },this)"> 
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
             <div class="col-md-6">
               <input type="hidden" value="{{$evento->id}}" id="idevento">
                <label>Nombre *<i class="fa fa-user"></i></label>
                <input type="text" id="nombre"/>
                <label>Correo *<i class="fa fa-envelope"></i></label>
                <input type="text" id="correo"/>
                <label>Telefono *<i class="fa fa-phone"></i></label>
                <input type="text" id="telefono"/>
             </div>
           </div>
        </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios <i class="fa fa-angle-right"></i></button>
        </div>
    </div> 
</form>  

@endsection