@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="custom-form no-icons" style="height: 50px;width: auto;position: inherit;    margin-top: 15px;">
   <a href="https://kayllapi.com/backoffice/plan/create?view=createdetalleplan&id={{$plan->id}}" class="btn  big-btn  color-bg flat-btn">Nuevo<i class="fa fa-angle-right"></i></a><br>
</div>
<div class="dashboard-list-box fl-wrap">
   <div class="dashboard-header fl-wrap mx-dashboard-list">
       <div class="mx-header-title">Detalles del Plan {{$plan->nombre}}</div>
   </div>
   @foreach($plandetalle as $value)
  
        <div class="dashboard-list">
         <div class="dashboard-message">
             <div class="dashboard-listing-table-text">
                 
                 <h4>{{$value->contenido}}</h4>
                 <ul class="dashboard-listing-table-opt  fl-wrap">
                     <li><a href="{{ url('backoffice/plan/'.$value->id.'/edit?view=editardetalle') }}"><i class="fa fa-pencil-square-o"></i> Editar</a></li>
                     <li><a href="{{ url('backoffice/plan/'.$value->id.'/edit?view=eliminardetalle') }}" style="background: #E91E63;"><i class="fa fa-pencil-square-o"></i> Eliminar</a></li>
                 </ul>
             </div>
         </div>
        </div>
  @endforeach
</div>    
                           
@endsection
