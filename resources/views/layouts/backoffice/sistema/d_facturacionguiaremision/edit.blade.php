@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Editar Gu&iacute;a de Remisi&oacute;n</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionguiaremision') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/facturacionguiaremision/{{ $facturacionguiaremision->id }}',
        method: 'PUT',
        data:{
            view: 'editar'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionguiaremision') }}';                                                                            
    },this)">
    <div class="profile-edit-container">
        <div class="custom-form">
            <div class="row">
                <div class="col-sm-6">
                    <label>Emisor Ruc *</label>
                    <input type="text" id="emisor_ruc" value="{{$facturacionguiaremision->emisor_ruc}}"/>
                </div>
            </div>
        </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios</button>
        </div>
    </div> 
</form>                             
@endsection
@section('subscripts')
<script>
</script>
@endsection