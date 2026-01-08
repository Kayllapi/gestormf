@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Eliminar Gu&iacute;a de Remisi&oacute;n</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionguiaremision') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/facturacionguiaremision/{{ $facturacionguiaremision->id }}',
        method: 'DELETE',
        data:{
            view: 'eliminar'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionguiaremision') }}';                                                                            
    },this)">
    <div class="mensaje-warning">
        <i class="fa fa-warning"></i> Esta seguro de Eliminar la Guia de Remision <b>"{{ $facturacionguiaremision->despacho_serie }} - {{ $facturacionguiaremision->despacho_correlativo }}"</b>!.
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn btn-danger">Eliminar</button>
        </div>
    </div> 
</form>                             
@endsection