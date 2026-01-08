@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Comprobante de Pedido</span>
      <a class="btn btn-success" href="{{ url('backoffice/carritocompra') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<iframe src="{{ url('backoffice/carritocompra/'.$venta->id.'/edit?view=comprobantepdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>
@endsection