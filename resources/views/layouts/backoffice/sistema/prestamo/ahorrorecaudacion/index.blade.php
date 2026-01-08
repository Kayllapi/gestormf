@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
 <div class="list-single-main-wrapper fl-wrap">
     <div class="breadcrumbs gradient-bg fl-wrap">
       <span>Recaudaciones</span>
       <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrorecaudacion/create') }}"><i class="fa fa-angle-right"></i> Registrar</a>
     </div>
 </div>
 @include('app.sistema.tabla',[
     'tabla' => 'tabla-pendientes',
     'script' => 'scriptsapp1',
     'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrorecaudacion/show-index'),
     'thead' => [
         ['data' => 'Código'],
         ['data' => 'Cod. Ahorro'],
         ['data' => 'Fecha de Pago'],
         ['data' => 'Monto Efectivo'],
         ['data' => 'Monto Depósito'],
         ['data' => 'Cliente'],
         ['data' => 'Cajero'],
         ['data' => 'Estado'],
     ],
     'tbody' => [
         ['data' => 'codigo'],
         ['data' => 'codigoahorro'],
         ['data' => 'fechapago'],
         ['data' => 'monto_efectivo'],
         ['data' => 'monto_deposito'],
         ['data' => 'cliente'],
         ['data' => 'cajero_nombre'],
         ['data' => 'estado'],
     ],
     'tfoot' => [
         ['input' => 'text'],
         ['input' => 'text'],
         ['input' => 'date'],
         ['input' => ''],
         ['input' => ''],
         ['input' => 'text'],
         ['input' => 'text'],
         ['input' => ''],
     ]
 ])
@endsection