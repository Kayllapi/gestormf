@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>EDITAR REGISTRADO</span>
      <a class="btn btn-warning" href="{{ url('backoffice/evento/'.$registered->idevento.'/edit?view=registrado') }}"><i class="fa fa-angle-left"></i>ATRÁS</a></a>
    </div>
</div>

<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/evento/{{$registered->idevento}}',
        method: 'PUT',
        data:{
            view: 'editinscription'
        }
    },
    function(resultado){
       location.href = '{{ url('backoffice/evento/'.$registered->idevento.'/edit?view=registrado') }}';                                                               
    },this)" enctype="multipart/form-data"> 
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
             <div class="col-md-6">
                <input type="hidden" value="{{$registered->idevento}}" id="idevento">
                <input type="hidden" value="{{$registered->id}}" id="id">
                <label>Nombre *<i class="fa fa-user"></i></label>
                <input type="text" id="nombre" value=" {{$registered->nombre}} "/>
                <label>Correo *<i class="fa fa-envelope"></i></label>
                <input type="text" id="correo" value=" {{$registered->correo}} "/>
                <label>Telefono *<i class="fa fa-phone"></i></label>
                <input type="text" id="telefono" value=" {{$registered->telefono}} "/>
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