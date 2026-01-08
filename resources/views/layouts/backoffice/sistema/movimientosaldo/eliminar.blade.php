@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
        <span>Eliminar Movimiento de Saldo</span>
        <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimientosaldo') }}">
            <i class="fa fa-angle-left"></i> Ir Atras</a>
        </a>
    </div>
</div>
    <form action="javascript:;" 
          onsubmit="callback({
              route: 'backoffice/tienda/sistema/{{ $tienda->id }}/movimientosaldo/{{ $movimientosaldo->id }}',
              method: 'DELETE',
              data: { 
                    view: 'eliminar' 
              }
          },
          function(resultado){
              location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimientosaldo') }}';
          },this)"> 
           <div class="row">
               <div class="col-sm-6">
                  <label>Tipo de Movimiento</label>
                  <input type="text" value="{{ $movimientosaldo->tipomovimientonombre }}" disabled>
                  <label>Caja</label>
                  <input type="text" value="{{ $movimientosaldo->cajanombre }}" disabled>
                  <label>Moneda</label>
                  <input type="text" value="{{ $movimientosaldo->monedanombre }}" disabled>
               </div>
               <div class="col-sm-6">
                  <label>Monto</label>
                  <input type="text" value="{{ $movimientosaldo->monto }}" id="monto" disabled/>
                  <label>Motivo</label>
                  <input type="text" value="{{ $movimientosaldo->motivo }}" id="motivo" disabled/>
               </div>
           </div>
           <button type="submit" class="btn mx-btn-post">Eliminar</button>
    </form>
@endsection