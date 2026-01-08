@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>ELIMINAR MÓDULO</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/modulo') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/modulo/{{ $s_modulo->id }}',
        method: 'DELETE',
        data:{
            view: 'eliminar'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/modulo') }}';                                                                            
    },this)">
    <div class="mensaje-danger">
      <i class="fa fa-warning"></i> Esta seguro de Eliminar el módulo <b>"{{ $s_modulo->nombre }}"</b>!.
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn"><i class="fa fa-trash"></i> Eliminar</button>
        </div>
    </div> 
</form>                             
@endsection
@section('subscripts')
@endsection