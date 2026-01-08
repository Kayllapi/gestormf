@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
 <div class="list-single-main-wrapper fl-wrap">
     <div class="breadcrumbs gradient-bg fl-wrap">
       <span>Cobranzas</span>
       <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
     </div>
 </div>
 @include('app.sistema.tabla',[
     'tabla' => 'tabla-pendientes',
     'script' => 'scriptsapp1',
     'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza/show-index'),
     'thead' => [
         ['data' => 'Código'],
         ['data' => 'Fecha de Pago'],
         ['data' => 'Monto'],
         ['data' => 'Cliente'],
         ['data' => 'Responsable'],
         ['data' => 'Estado'],
         ['data' => '', 'width' => '10px']
     ],
     'tbody' => [
         ['data' => 'codigo'],
         ['data' => 'fechapago'],
         ['data' => 'monto'],
         ['data' => 'cliente'],
         ['data' => 'responsable'],
         ['data' => 'estado'],
         ['render' => 'opcion']
     ],
     'tfoot' => [
         ['input' => 'text'],
         ['input' => 'date'],
         ['input' => ''],
         ['input' => 'text'],
         ['input' => 'text'],
         ['input' => ''],
         ['input' => ''],
     ]
 ])
@endsection