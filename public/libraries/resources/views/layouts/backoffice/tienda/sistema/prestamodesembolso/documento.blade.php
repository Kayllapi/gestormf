@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Documentos',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamodesembolso: Ir Atras'
    ]
])
<div class="row">
  <div class="col-sm-12">
    @foreach ($documentos as $value)
      <a href="javascript:;" class="statistic-item-wrap" onclick="mostrarDocumento({{ $value->id }})">
        <div class="statistic-item gradient-bg fl-wrap">
          <i class="fas fa-folder-open"></i>
          <div class="statistic-item-numder">Documento</div>
          <h5>de {{ $value->nombre }}</h5>
        </div>
      </a>
    @endforeach
  </div>
  <div class="col-sm-12">
    <div id="load-documento"></div>
  </div>
</div>
@endsection
@section('subscripts')
<script>
  function mostrarDocumento(iddocumento) {
    $('#load-documento').html('<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamodesembolso/'.$prestamodesembolso->id.'/edit?view=documentopdf&iddocumento=') }}'+iddocumento+'" frameborder="0" width="100%" height="600px"></iframe>');
  }
</script>
@endsection