@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Detalle de Crédito',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamoaprobacion: Ir Atras'
    ]
])
        <div id="cont-expedientedetalle"></div> 

@endsection

@section('subscripts')
<script>
    expedientedetalle_index({{$prestamocredito->id}});
    function expedientedetalle_index(idcredito){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitud/'+idcredito+'/edit?view=expedientedetalle',result:'#cont-expedientedetalle'});
    }
</script>
@endsection