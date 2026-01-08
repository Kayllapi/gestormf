@extends('layouts.backoffice.master')
@section('cuerpobackoffice')

<div class="dashboard-list-box fl-wrap">
   <div class="dashboard-header fl-wrap mx-dashboard-list">
       <div class="mx-header-title">Bonos</div>
   </div>
   @foreach($bono as $value)
     <div class="dashboard-list">
         <div class="dashboard-message">
             <div class="dashboard-listing-table-text">
                 
                 <h4>{{$value->nombre}} - {{$value->porcentaje}}%</h4>
                 <ul class="dashboard-listing-table-opt  fl-wrap">
                     <li><a href="{{ url('backoffice/bono/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-pencil-square-o"></i> Editar</a></li>
<!--                      <li><a href="{{ url('backoffice/bono/'.$value->id.'/edit?view=eliminar') }}" class="del-btn"><i class="fa fa-trash-o"></i> Eliminar</a></li> -->
                 </ul>
             </div>
         </div>
     </div>
   @endforeach
 </div>  
</section>
@endsection