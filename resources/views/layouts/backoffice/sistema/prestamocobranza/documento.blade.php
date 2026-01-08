<?php
$documentos = DB::table('s_prestamo_documento')
    ->where('s_prestamo_documento.idmostrar', 2)
    ->where('s_prestamo_documento.idtienda', $tienda->id)
    ->get();
?>
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
    <div id="iframe-carga"></div>
    <div id="load-documento"></div>
  </div>
</div>
<script>
  function mostrarDocumento(iddocumento) {
      
    load('#iframe-carga'); 
    $('#load-documento').html('<iframe onload="iframeload();" src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamodesembolso/'.$idprestamocredito.'/edit?view=documentopdf&iddocumento=') }}'+iddocumento+'" frameborder="0" width="100%" height="600px"></iframe>');
  }
function iframeload(){
    $('#iframe-carga').html('');
}    
</script>