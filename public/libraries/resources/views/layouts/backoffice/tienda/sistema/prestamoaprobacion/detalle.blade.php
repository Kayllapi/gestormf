@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Detalle de Crédito',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamoaprobacion: Ir Atras'
    ]
])
    <div id="resultado-credito"></div>  
    @include('app.prestamo_creditodetalle',[
      'idtienda'=>$tienda->id,
      'idprestamocredito'=>$prestamocredito->id
    ])    

@endsection

@section('subscripts')
<script>
    tab({click:'#tab-resultado'});
</script>
@endsection