@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
  
@foreach($suscripcion as $value)
<div class="dashboard-list">
  <div class="dashboard-message">
      <div class="dashboard-listing-table-text">
          <h4>Fecha Suscripción: <span> {{ date_format(date_create($value->fecharegistro), 'd-m-Y h:i A') }}</span></h4>
          <div class="booking-details fl-wrap">
              <span class="booking-title"><i class="fa fa-envelope-o"></i> Correo Electronico: {{ $value->email }}</span>
          </div>
          <ul class="dashboard-listing-table-opt  fl-wrap">
              <li><a href="{{ url('backoffice/suscripcion/'.$value->id.'/edit?view=informacion') }}"><i class="fa fa-pencil-square-o"></i> Información</a></li>
              <li><a href="{{ url('backoffice/suscripcion/'.$value->id.'/edit?view=eliminar') }}" class="del-btn"><i class="fa fa-trash-o"></i> Eliminar</a></li>
          </ul>
      </div>
  </div>
</div>
@endforeach

@endsection