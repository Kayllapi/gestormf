@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Anular Confirmación',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamo/ahorroconfirmacion: Ir Atras'
    ]
])
<div id="cont-expedientedetalle"></div>
<form action="javascript:;"
      onsubmit="callback({
          route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamo/ahorroconfirmacion/{{ $prestamoahorro->id }}',
          method: 'PUT',
          data:   {view: 'anularaprobacion'}
      },
      function(resultado){
          location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorroconfirmacion') }}';
      },this)">
    <div class="mensaje-warning">
        <i class="fa fa-exclamation-circle"></i> ¿Esta seguro de Anular la Confirmación?      
    </div>
    <button type="submit" class="btn mx-btn-post"><i class="fa fa-ban"></i> Anular Confirmación</button>
</form>  

@endsection

@section('subscripts')
<script>
    expedientedetalle_index({{$prestamoahorro->id}});
    function expedientedetalle_index(idahorro){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorrosolicitud/'+idahorro+'/edit?view=expedientedetalle',result:'#cont-expedientedetalle'});
    }
</script>
@endsection

