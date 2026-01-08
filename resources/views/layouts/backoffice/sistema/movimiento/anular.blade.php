@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Anular Movimiento</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimiento') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
@if(Auth::user()->idtienda==0)
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¡Con el usuario Master no puede realizar movimientos, ingrese con un usuario de esta tienda!
    </div>
@else
<form action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/movimiento/{{ $s_movimiento->id }}',
        method: 'PUT',
        data:{
            view: 'anular'
        }
    },
    function(resultado){
            location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimiento') }}';                                                            
    },this)">
          <div class="row">
           <div class="col-md-6">
              <label>Tipo de Movimiento</label>
              <input type="text" value="{{$s_movimiento->tiponombre}} - {{$s_movimiento->conceptonombre}}" disabled>
              <label>Monto</label>
              <input type="text" value="{{ $s_movimiento->monedasimbolo }} {{ $s_movimiento->monto }}" disabled/>
              <label>Persona Entregado</label>
              <input type="text" value="{{$s_movimiento->responsableentregadoidentificacion}} - {{$s_movimiento->responsableentregadoapellidos}}, {{$s_movimiento->responsableentregadonombre}}" disabled/>
           </div>
           <div class="col-md-6">
              <label>Concepto</label>
              <textarea id="concepto" disabled>{{ $s_movimiento->concepto }}</textarea>
           </div>
         </div> 
          <div class="mensaje-warning">
            <i class="fa fa-warning"></i> ¿Esta seguro de Anular el Movimiento?
          </div>
          <button type="submit" class="btn mx-btn-post"><i class="fa fa-ban"></i> Anular</button>
</form>       
@endif
@endsection