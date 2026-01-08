@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Eliminar Comprobante</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionboletafactura') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/facturacionboletafactura/{{ $facturacionboletafactura->id }}',
        method: 'DELETE',
        data:{
            view: 'eliminar'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionboletafactura') }}';                                                                            
    },this)">
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> Esta seguro de Eliminarel Comprobante <b>"{{ $facturacionboletafactura->venta_serie }} {{ $facturacionboletafactura->venta_correlativo }}"</b>!.
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn mx-btn-post">Eliminar</button>
        </div>
    </div> 
</form>                             
@endsection