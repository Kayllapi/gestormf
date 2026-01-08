@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Transferencia de Cartera</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamotransferenciacartera') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>

<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/prestamotransferenciacartera',
        method: 'POST',
        data:{
            view: 'registrar',
        }
    },
    function(resultado){
       location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamotransferenciacartera') }}';                                                                            
    },this)">
    <div class="row">
        <div class="col-sm-6">
            <label>Cliente</label>
            <select id="idcliente">
                <option></option>
            </select>
        </div>
        <div class="col-sm-6">
            <label>Destino (Asesor)</label>
            <select id="idasesordestino">
                <option></option>
            </select>
        </div>
    </div>
    <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>    
@endsection
@section('subscripts')
<script>
    $('#idcliente').select2({
        @include('app.prestamo_select2_cliente')
    });
  
    $('#idasesordestino').select2({
        @include('app.select2_acceso')
    });
</script>
@endsection