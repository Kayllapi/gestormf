@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Eliminar Ahorro',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamo/ahorrosolicitud: Ir Atras'
    ]
])
<div id="carga-ahorro">
    <div id="resultado-ahorro"></div>  
    <div id="cont-expedientedetalle"></div>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro de Eliminar?
    </div>
    <form action="javascript:;"
          onsubmit="callback({
              route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamo/ahorrosolicitud/{{$prestamoahorro->id}}',
              method: 'DELETE',
              carga: '#carga-ahorro',
              data:   {
                  view: 'eliminar'
              }
          },
          function(resultado){
              location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrosolicitud') }}';
          },this)">
          <button type="submit" class="btn mx-btn-post"><i class="fa fa-trash"></i> Eliminar Ahorro</button>
    </form>
</div>
@endsection

@section('subscripts')
<script>
    expedientedetalle_index({{$prestamoahorro->id}});
    function expedientedetalle_index(idahorro){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorrosolicitud/'+idahorro+'/edit?view=expedientedetalle',result:'#cont-expedientedetalle'});
    }
</script>     
@endsection