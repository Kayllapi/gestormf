@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Tarjeta de Pago',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamodesembolso: Ir Atras'
    ]
])
<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamodesembolso/'.$prestamodesembolso->id.'/edit?view=tarjetapdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>
@endsection