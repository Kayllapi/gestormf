@extends('layouts.backoffice.master')
@section('cuerpobackoffice')  

<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Bonos por Inversión</span>
    </div>
</div>
<div class="profile-edit-container">
    <div class="statistic-container fl-wrap">
     <?php 
     $totalreparticion = DB::table('consumidor_reparticion')
         ->where('consumidor_reparticion.idusersrecibe',Auth::user()->id)
         ->sum('monto_ganancia');
      
     $primeraver = number_format($totalreparticion, 2, '.', '');
     $segundavez = 0.00;
     $gananciapendiente = 0.00;
     $totalretirado = 0.00;
     ?>
     <div class="statistic-item-wrap"> 
        <div class="statistic-item gradient-bg fl-wrap">
            <i class="fa fa-money"></i>
            <div class="statistic-item-numder">{{ $primeraver }} KAY</div>
            <h5>Primera vez (50%)</h5>
        </div>
     </div>
     <div class="statistic-item-wrap"> 
        <div class="statistic-item gradient-bg fl-wrap">
            <i class="fa fa-money"></i>
            <div class="statistic-item-numder">{{ $segundavez }} KAY</div>
            <h5>Segunda vez a más (5%)</h5>
        </div>
     </div>
     <div class="statistic-item-wrap"> 
        <div class="statistic-item gradient-bg fl-wrap">
            <i class="fa fa-money"></i>
            <div class="statistic-item-numder">{{ $gananciapendiente }} KAY</div>
            <h5>Total Retirado</h5>
        </div>
      </div> 
     <div class="statistic-item-wrap"> 
        <div class="statistic-item gradient-bg fl-wrap">
            <i class="fa fa-money"></i>
            <div class="statistic-item-numder">{{ $totalretirado }} KAY</div>
            <h5>Total Ganancia</h5>
        </div>
     </div>   
    </div>                     
</div>

<div class="table-responsive">
  <table class="table">
      <thead class="thead-dark">
        <tr>
          <th width="150px">Fecha de registro</th>
          <th>Afiliado (Apellidos y Nombres)</th>
          <th>Usuario</th>
          <th>Plan</th>
          <th>Porcentaje</th>
          <th>Ganancia</th>
          <th width="10px"></th>
        </tr>
      </thead>
        @include('app.tablesearch',[
            'searchs'=>['date','name'],
            'search_url'=> url('backoffice/reparticion')
        ])
      <tbody> 
        <?php $contar = 1;?>
        @foreach($reparticiones as $value)
          <tr>
            <td>{{ $value->fecharegistro }}</td>
            <td>{{ $value->apellidos}}, {{$value->nombre}}</td>
            <td>{{ $value->usuario}}</td>
            <td>{{ $value->monto_plan }} KAY</td>
            <td>{{ $value->monto_porcentaje }}%</td>
            <td>{{ $value->monto_ganancia }} KAY</td>
            <td><a href="javascript:;" class="btn btn-info"><i class="fa fa-list"></i> Detalle</a></td>
          </tr>
        <?php $contar++;?>
        @endforeach
      </tbody>
  </table>
     {{ $reparticiones->links('app.tablepagination', ['results' => $reparticiones]) }}
</div> 

@endsection