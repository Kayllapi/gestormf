@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Detalle de Crédito Grupal',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamosolicitudgrupal: Ir Atras'
    ]
])   
        <div id="cont-expedientedetalle"></div> 
 
@endsection

@section('subscripts')
<script>
    expedientedetalle_index({{$prestamocreditogrupal->id}});
    function expedientedetalle_index(idcredito){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitudgrupal/'+idcredito+'/edit?view=expedientedetalle',result:'#cont-expedientedetalle'});
    }
</script>   
@endsection