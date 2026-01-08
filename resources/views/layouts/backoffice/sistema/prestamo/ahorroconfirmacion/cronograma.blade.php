@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Cronograma de Ahorro',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamo/ahorroconfirmacion: Ir Atras'
    ]
])
<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorroconfirmacion/'.$prestamoahorro->id.'/edit?view=cronogramapdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>
@endsection
