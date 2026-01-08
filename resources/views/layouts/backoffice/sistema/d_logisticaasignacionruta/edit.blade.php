@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Editar Asiganción de Ruta</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/logisticaasignacionruta') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/logisticaasignacionruta/{{ $s_logisticaasignacionruta->id }}',
        method: 'PUT',
        data:{
            view: 'editar'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/logisticaasignacionruta') }}';                                                                            
    },this)">
        <div class="row">
          <div class="col-sm-6">
                <label>Nombre *</label>
                <input type="text" id="nombre" value="{{$s_logisticaasignacionruta->nombre}}" onkeyup="texto_mayucula(this)"/>
          </div>
        </div>
        <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>                             
@endsection