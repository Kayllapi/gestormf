@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Detalle Ahorro',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamo/ahorrosolicitud: Ir Atras'
    ]
])  
        <div id="cont-expedientedetalle"></div>

@endsection

@section('subscripts')
<script>
    expedientedetalle_index({{$prestamoahorro->id}});
    function expedientedetalle_index(idahorro){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorrosolicitud/'+idahorro+'/edit?view=expedientedetalle',result:'#cont-expedientedetalle'});
    }
</script>     
@endsection