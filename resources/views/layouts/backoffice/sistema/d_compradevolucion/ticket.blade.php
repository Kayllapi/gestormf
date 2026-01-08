@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Ticket de Compra de Devolución</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/compradevolucion') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/compradevolucion/'.$compradevolucion->id.'/edit?view=ticketpdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>
@endsection