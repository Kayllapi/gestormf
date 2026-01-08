@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Detalle de Cartera de Cliente</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamotransferenciacartera') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
    <div class="row">
        <div class="col-sm-6">
            <label>Fecha Reigstro</label>
            <input type="text" value="{{ date_format(date_create($s_prestamo_cartera->fecharegistro),"d/m/Y h:i:s A") }}" disabled>
            <label>Tipo</label>
            <input type="text" value="{{$s_prestamo_cartera->idestadotransferenciacartera==1?'REGISTRADO':'TRANSFERIDO'}}" disabled>
            <label>Cliente</label>
            <input type="text" value="{{$s_prestamo_cartera->cliente}}" disabled>
        </div>
        <div class="col-sm-6">
            <label>Responsable Anterior</label>
            <input type="text" value="{{$s_prestamo_cartera->asesororigen}}" disabled>
            <label>Responsable Actual</label>
            <input type="text" value="{{$s_prestamo_cartera->asesordestino}}" disabled>
        </div>
    </div> 
@endsection
@section('subscripts')
<script>
    $('#idasesordestino').select2({
        placeholder: "-- Seleccionar Cliente --",
    });
  
    $('#idcliente').select2({
        placeholder: "-- Seleccionar Cliente --",
    });
</script>
@endsection