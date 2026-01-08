@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Aprobar Solicitud de Descuento de Mora</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomoraaprobacion') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div id="carga-mora">
    <form action="javascript:;" 
          onsubmit="callback({
                route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamomoraaprobacion/{{ $s_prestamo_mora->id }}',
                method: 'PUT',
                carga:  '#carga-mora',
                data:   {
                    view: 'aprobar',
                    documento: '{{$s_prestamo_mora->documento}}'
                }
            },
            function(resultado){
              location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomoraaprobacion') }}';
            }, this)">
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
                  <div id="resultado-imagendocumento" style="display: none;"></div>
            </div>
            <button type="submit" class="btn mx-btn-post" style="margin-bottom: 5px;"><i class="fa fa-check"></i> Aprobar</button> 
    </form>
</div>
<style>

  #cont-imagendocumento {
      height:293px;
  }
  #resultado-imagendocumento {
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
@section('subscripts')
<script>
  @if($s_prestamo_mora->documento!='')
          mostrar_documento("{{url('/public/backoffice/tienda/'.$tienda->id.'/prestamomora/'.$s_prestamo_mora->documento)}}");
  @endif
  
  function mostrar_documento(archivo){
          $('#cont-imagendocumento').css('display','none');
          $('#resultado-imagendocumento').attr('style','background-image: url('+archivo+')');
  }
</script>
@endsection