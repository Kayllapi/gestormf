@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Pre Aprobar Ahorro',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamo/ahorrosolicitud: Ir Atras'
    ]
])
<div id="carga-ahorro">
    <div id="cont-estadoexpediente1" <?php echo ($prestamoahorro->estadoexpediente=='si' or $prestamoahorro->estadoexpediente=='')? 'style="display:block;"': 'style="display:none;"' ?>>
        <div id="resultado-ahorro"></div>   
        <div id="cont-expedientedetalle"></div>
    </div>
    <div id="cont-estadoexpediente2" <?php echo ($prestamoahorro->estadoexpediente=='si' or $prestamoahorro->estadoexpediente=='')? 'style="display:none;"': 'style="display:block;"' ?>>
        <div class="resultado-aprobado">CRÉDITO APROBADO</div>
                      <div class="mensaje-warning">
                        El Ahorro esta sin Expediente!!
                      </div>
    </div> 
    
      
    <form action="javascript:;"
          onsubmit="callback({
              route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamo/ahorrosolicitud/{{$prestamoahorro->id}}',
              method: 'PUT',
              carga: '#carga-ahorro',
              data:   {
                  view: 'preaprobar'
              }
          },
          function(resultado){
              location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrosolicitud') }}';
          },this)">
          <button type="submit" class="btn mx-btn-post"><i class="fa fa-check"></i> Pre Aprobar Ahorro</button>
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