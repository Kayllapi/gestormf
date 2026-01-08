@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Detalle de Movimiento</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimiento') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div class="row">
   <div class="col-md-6">
      <label>Tipo</label>
      <input type="text" value="{{$s_movimiento->tiponombre}} - {{$s_movimiento->conceptonombre}}" disabled>
      <label>Monto</label>
      <input type="text" value="{{ $s_movimiento->monedasimbolo }} {{ $s_movimiento->monto }}" disabled/>
   </div>
   <div class="col-md-6">
      <label>Concepto</label>
      <textarea id="concepto" disabled>{{ $s_movimiento->concepto }}</textarea>
   </div>
 </div>                   
@endsection