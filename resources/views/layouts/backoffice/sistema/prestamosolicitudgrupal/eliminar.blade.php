@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Eliminar Crédito Grupal',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamosolicitudgrupal: Ir Atras'
    ]
])
<div id="carga-credito">
        <div id="cont-expedientedetalle"></div> 
    <form action="javascript:;"
          onsubmit="callback({
              route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitudgrupal/{{$prestamocreditogrupal->id}}',
              method: 'DELETE',
              carga: '#carga-credito',
              data:   {
                  view: 'eliminar'
              }
          },
          function(resultado){
              location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitudgrupal') }}';
          },this)">
          <button type="submit" class="btn mx-btn-post"><i class="fa fa-trash"></i> Eliminar Crédito Grupal</button>
    </form>
</div>
@endsection

@section('subscripts')
<script>
    expedientedetalle_index({{$prestamocreditogrupal->id}});
    function expedientedetalle_index(idcredito){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitudgrupal/'+idcredito+'/edit?view=expedientedetalle',result:'#cont-expedientedetalle'});
    }
</script>
@endsection