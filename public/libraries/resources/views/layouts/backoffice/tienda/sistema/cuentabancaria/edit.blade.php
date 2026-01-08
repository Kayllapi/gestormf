@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Editar Cuenta Bancaria</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cuentabancaria') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form action="javascript:;" 
      onsubmit="callback({
          route: 'backoffice/tienda/sistema/{{ $tienda->id }}/cuentabancaria/{{ $s_cuentabancaria->id }}',
          method: 'PUT',
          data:{
              view: 'editar'
          }
      },
      function(resultado){
          location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cuentabancaria') }}';                                                                            
      },this)">

            <div class="col-sm-6">
                <label>Banco *</label>
                <input type="text" value="{{$s_cuentabancaria->banco}}" id="banco"/>
            </div>
            <div class="col-sm-6">
                <label>Número de cuenta *</label>
                <input type="text" value="{{$s_cuentabancaria->numerocuenta}}" id="numerocuenta"/>
            </div>

            <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>                             
@endsection