@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Código de Barra</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/producto') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
@if($producto->codigo!='')
    <iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/producto/'.$producto->id.'/edit?view=codigobarrapdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>
@else
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> El Producto no tiene Código!!.
    </div>
@endif
@endsection