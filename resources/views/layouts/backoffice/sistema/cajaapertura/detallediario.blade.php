@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Detalle de Caja</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
    @include('app.sistema_efectivo',['tienda'=>$tienda,'idaperturacierre'=>$s_aperturacierre->id])                      
@endsection
@section('subscripts')
<script>
  tab({click:'#tab-detalledeldia'});
</script>
@endsection