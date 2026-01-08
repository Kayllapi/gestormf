@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Detalle de Socilitud de Descuento de Mora</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomorasolicitud') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
            <div class="col-sm-6">
                  <label>Código de Crédito</label>
                  <input type="text" value="{{ str_pad($s_prestamo_mora->codigo, 8, "0", STR_PAD_LEFT) }}" disabled>
                  <label>Cliente (DNI - Apellidos, Nombres)</label>
                  <input type="text" value="{{ $s_prestamo_mora->clienteidentificacion }} - {{ $s_prestamo_mora->clienteapellidos }}, {{ $s_prestamo_mora->clientenombre }}" disabled>
                  <label>Monto Solicitado</label>
                  <input type="text" value="{{ $s_prestamo_mora->total_moradescuento }}" disabled>
                  <label>Motivo de descuento</label>
                  <textarea id="moradescuento_detalle" style="height:85px;" disabled>{{ $s_prestamo_mora->motivo }}</textarea>
            </div>
            <div class="col-sm-6">
                  <label>Foto de sustento</label>
                  <div id="resultado-imagendocumento"></div>
            </div>
<style>

  #resultado-imagendocumento {
      background-image: url({{url('/public/backoffice/tienda/'.$tienda->id.'/prestamomora/'.$s_prestamo_mora->documento)}});
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center;
      height:293px;
      width:100%;
      background-color: #eae7e7;
      border-radius: 5px;
      border: 1px solid #aaa;
      float: left;
      margin-bottom: 10px;
  }
</style>
@endsection