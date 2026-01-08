@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
        <span>PDF Guía de Remisión</span>
        <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionguiaremision') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionguiaremision/'.$facturacionguiaremision->id.'/edit?view=comprobantepdf') }}#zoom=100" frameborder="0" width="100%" height="600px"></iframe>
@endsection