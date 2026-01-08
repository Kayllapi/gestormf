@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Eliminar Compra</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/compra') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/compra/{{ $compra->id }}',
        method: 'DELETE',
        data:{
            view: 'eliminar'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/compra') }}';                                                                            
    },this)">
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> Esta seguro Elimar la Compra de <b>"{{ $compra->proveedornombre }} "</b>!.
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn btn-danger"><i class="fa fa-trash"></i> Eliminar</button>
        </div>
    </div> 
</form>                             
@endsection