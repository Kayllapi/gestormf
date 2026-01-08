@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Eliminar Crédito',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamosolicitud: Ir Atras'
    ]
])
<div id="carga-credito">
    <div id="resultado-credito"></div>  
  
    @include('app.prestamo_creditodetalle',[
      'idtienda'=>$tienda->id,
      'idprestamocredito'=>$prestamocredito->id
    ])    

    <form action="javascript:;"
          onsubmit="callback({
              route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud/{{$prestamocredito->id}}',
              method: 'DELETE',
              carga: '#carga-credito',
              data:   {
                  view: 'eliminar'
              }
          },
          function(resultado){
              location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud') }}';
          },this)">
          <button type="submit" class="btn mx-btn-post"><i class="fa fa-trash"></i> Eliminar Crédito</button>
    </form>
</div>
@endsection

@section('subscripts')
<script>
    tab({click:'#tab-resultado'});
</script>
@endsection