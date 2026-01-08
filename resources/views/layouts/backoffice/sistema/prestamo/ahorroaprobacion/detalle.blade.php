@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Detalle de Ahorro',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamo/ahorrosolicitud: Ir Atras'
    ]
])   
        <div id="cont-expedientedetalle"></div> 
 
@endsection

@section('subscripts')
<script>
    tab({click:'#tab-resultado'});
</script>
<script>
    expedientedetalle_index({{$prestamoahorro->id}});
    function expedientedetalle_index(idcredito){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorrosolicitud/'+idcredito+'/edit?view=expedientedetalle',result:'#cont-expedientedetalle'});
    }
</script>   
@endsection