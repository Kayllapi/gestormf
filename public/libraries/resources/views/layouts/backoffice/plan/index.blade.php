@extends('layouts.backoffice.master')
@section('cuerpobackoffice')


<div class="dashboard-list-box fl-wrap">
   <div class="dashboard-header fl-wrap mx-dashboard-list">
       <div class="mx-header-title">Mis Planes</div>
   </div>
   @foreach($plan as $value)
     <div class="dashboard-list">
         <div class="dashboard-message">
             <div class="dashboard-listing-table-text">
                 
                 <h4>{{$value->nombre}} - S/. {{$value->costo}}</h4>
                 <ul class="dashboard-listing-table-opt  fl-wrap">
                     <li><a href="{{ url('backoffice/plan/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-pencil-square-o"></i> Editar</a></li>
                     <li><a href="{{ url('backoffice/plan/'.$value->id.'/edit?view=detalle') }}"><i class="fa fa-pencil-square-o"></i> Detalle</a></li>
<!--                      <li><a href="{{ url('backoffice/plan/'.$value->id.'/edit?view=eliminar') }}" class="del-btn"><i class="fa fa-trash-o"></i> Eliminar</a></li> -->
                 </ul>
             </div>
         </div>
     </div>
   @endforeach
 </div>                        
@endsection
@section('scriptsbackoffice')
<style>
  .dashboard-listing-table-opt li a.del-btn{
    background-color: #ff3c3c;
  }
.new-dashboard-item {
    background-color: transparent;
    border: 1px solid #1877b7;
    color: #1877b7;
    z-index: 0;
}
.mx-dashboard-list {
    border-top-right-radius: 5px;
    border-top-left-radius: 5px;
    background-color: #4db7fe;
}
.mx-header-search{
    top: 0px;
    margin: auto;
    float: right;
}
.mx-header-title{
    color: #fff;
    float: left;
    font-size: 18px;
    line-height: 2;
}
.mx-header-search-button {
    background: #2f3b59;
}
</style>
@endsection