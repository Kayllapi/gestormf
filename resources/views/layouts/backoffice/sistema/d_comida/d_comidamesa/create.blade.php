@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Marca</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidamesa') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form action="javascript:;" 
      onsubmit="callback({
          route: 'backoffice/tienda/sistema/{{ $tienda->id }}/comida/comidamesa',
          method: 'POST',
          data:{
              view: 'registrar',
              idtienda: '{{ $tienda->id }}'
          }
      },
      function(resultado){
         location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidamesa') }}';                                                                            
      },this)">
      <div class="row">
        <div class="col-sm-6">
            <label>Número *</label>
            <input type="number" min="1" id="numero"/>
        </div>
      </div>
      <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>                             
@endsection