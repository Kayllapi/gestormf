@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="mx-cont-btn">
    <a href="{{ url('backoffice/tienda') }}" class="btn big-btn mx-btn-atras"><i class="fa fa-angle-left"></i> Atras</a>
<?php
$planadquirido = negocio_planadquirido($tienda->id,$idusers);
?>
@if($planadquirido['estado']=='NINGUNO')
</div>
    @include('app.planesnegocio') 
@elseif($planadquirido['estado']=='PENDIENTE')
</div>
    <div class="mensaje-warning">
        <i class="fa fa-check"></i>  Acaba de solicitar la <a href="#">Activación para sus Ofertas</a>, la aprobación se realizara lo más antes posible, gracias.
    </div>
@elseif($planadquirido['estado']=='CORRECTO')
  @if($idusers!=1)
    <a href="{{ url('backoffice/tienda/'.$tienda->id.'/edit?view=ofertacreate') }}" class="btn big-btn mx-btn-create"><i class="fa fa-angle-right"></i> Nueva Oferta</a>
</div>
    <div class="mensaje-success">
        <i class="fa fa-check"></i>  Usted tiene el <a href="#">Plan de Ofertas</a> Activado.</a>
    </div>
  @else
</div>
  @endif
    <div class="dashboard-list-box fl-wrap">
       <div class="dashboard-header fl-wrap mx-dashboard-list">
           <div class="mx-header-title">Ofertas de {{ $tienda->nombre }}</div>
           <div class="header-search mx-header-search">
             <form action="{{ url('backoffice/tienda') }}" method="GET">
               <div class="header-search-input-item">
                   <input type="text" value="{{ isset($_GET['searchtienda']) ? $_GET['searchtienda'] : '' }}" name="searchtienda" placeholder="Buscar..."/>
               </div>
               <button class="header-search-button mx-header-search-button" type="submit">Buscar</button>
             </form>
           </div>
       </div>
       @foreach($ofertaproducto as $value)
         <div class="dashboard-list">
             <div class="dashboard-message">
                  <div class="dashboard-listing-table-image">  
                   <?php
                   $rutaimagen = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/oferta/'.$value->imagen; 
                   ?>
                   @if(file_exists($rutaimagen) AND $value->imagen!='')
                       <img src="{{ url('redimensionar/tienda/oferta/300/180/'.$tienda->id.'/'.$value->imagen) }}" style="height: 180px;">
                   @else
                       <img src="{{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }}" style="height: 180px;">
                   @endif
                 </div>
                 <div class="dashboard-listing-table-text">
                    <?php 
                     $countreservaoferta = DB::table('reservaoferta')
                        ->where('idoferta',$value->id)
                        ->count();
                    ?>
                     <a href="javascript:;"><h4>{{ $value->nombre }} </h4></a>
                     <span class="dashboard-listing-table-address">
                       <i class="fa fa-home"></i>{{ $value->tiendanombre }} <br>
                       <i class="fa fa-tags"></i><b>Antes:</b> S/. {{ $value->precio }} - <b>Ahora:</b> S/. {{ $value->preciooferta }}<br>
                       <i class="fa fa-check"></i> 
                       <?php 
                        $stock = stock_oferta($value->id);
                        $stockactual = $stock['stockactual'];
                        if($stockactual==10000000000){
                            $stockactual = 'ILIMITADO';
                        }
                       ?>
                       <b>Stock:</b> {{ $stockactual }} - <b>Consumido:</b> {{ $stock['stockconsumido'] }} - <b>Reserva:</b> {{ $stock['stockreserva'] }}<br>
                       <i class="fa fa-calendar-alt"></i> 
                       <b>Inicio:</b> {{ date("d/m/Y",strtotime($value->fechainicio)) }} - <b>Fin:</b> {{ date("d/m/Y",strtotime($value->fechafin)) }}
                     </span>
                     <ul class="dashboard-listing-table-opt  fl-wrap">
                         <li><a href="{{ url('backoffice/tienda/'.$tienda->id.'/edit?view=ofertaedit&idoferta='.$value->id) }}">
                           <i class="fa fa-pencil-square-o"></i> Editar</a></li>
                         
                         @if($countreservaoferta==0)
                         <li><a href="{{ url('backoffice/tienda/'.$tienda->id.'/edit?view=categoriadelete&idtiendaproducto='.$value->id) }}" class="del-btn">
                           <i class="fa fa-trash"></i> Eliminar</a></li>
                         @else
                         @if($value->idestado==1)
                         <li><a href="javascript:;" onclick="estadooferta({{$value->id}},2)" style="background: #8BC34A;">
                           <i class="fa fa-check"></i> En venta</a></li>
                         @else
                         <li><a href="javascript:;" onclick="estadooferta({{$value->id}},1)" style="background: #607D8B;">
                           <i class="fa fa-close"></i> Vendido</a></li>
                         @endif 
                         @endif
                     </ul>
                 </div>
             </div>
         </div>
       @endforeach
     </div>   
@elseif($planadquirido['estado']=='VENCIDO')
    <div class="mensaje-danger">
        <i class="fa fa-close"></i>  Su <a href="#">Plan de Oferta</a> se ha vencido el {{$planadquirido['data']->fechafin}}, no pierda las mejores ofertas, renueve su plan.
    </div>
    @include('app.planesnegocio')
@endif             
@endsection
@section('subscripts')
<script>
function estadooferta(idoferta,idestado){
    $.ajax({
          url: raiz()+'/backoffice/tienda/showestadooferta?idoferta='+idoferta+'&idestado='+idestado,
          type:"GET",
          success:function(respuesta){
              location.href = '{{ Request::fullUrl() }}';
          }
      });
}
</script>
<style>
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
.color-dark {
    background-color: #2c3b5a !important;
}
.color-dark:hover {
    background-color: #050b17 !important;
}
</style>
@endsection