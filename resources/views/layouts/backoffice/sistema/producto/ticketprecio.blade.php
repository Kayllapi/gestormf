@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Ticket de Precio</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/producto') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/producto/'.$producto->id.'/edit?view=ticketpreciopdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>
@endsection
