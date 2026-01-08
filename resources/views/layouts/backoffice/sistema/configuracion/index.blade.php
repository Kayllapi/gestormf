@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Configuraciones'
])
<div class="profile-edit-container">
        <!--a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/configuracion/1/edit?view=config_tiendavirtual') }}" class="statistic-item-wrap">
        <div class="statistic-item gradient-bg fl-wrap">
        <i class="fa fa-store"></i>
        <div class="statistic-item-numder">Configuración</div>
        <h5>de Tienda Virtual</h5>
        </div>
        </a-->
        <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/configuracion/1/edit?view=config_general') }}" class="statistic-item-wrap">
            <div class="statistic-item gradient-bg fl-wrap">
                <i class="fa fa-cog"></i>
                <div class="statistic-item-numder">Configuración</div>
                <h5>General</h5>
            </div>
        </a>
        @if($tienda->idcategoria==13)
        <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/configuracion/1/edit?view=config_credito') }}" class="statistic-item-wrap"> 
            <div class="statistic-item gradient-bg fl-wrap">
                <i class="fa fa-building"></i>
                <div class="statistic-item-numder">Configuración</div>
                <h5>de Crédito</h5>
            </div>
        </a>
         <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/configuracion/1/edit?view=config_ahorro') }}" class="statistic-item-wrap"> 
            <div class="statistic-item gradient-bg fl-wrap">
                <i class="fa fa-building"></i>
                <div class="statistic-item-numder">Configuración</div>
                <h5>de Ahorros</h5>
            </div>
        </a>
        @endif
        <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/configuracion/1/edit?view=config_almacen') }}" class="statistic-item-wrap"> 
            <div class="statistic-item gradient-bg fl-wrap">
                <i class="fa fa-building"></i>
                <div class="statistic-item-numder">Configuración</div>
                <h5>de Inventario</h5>
            </div>
        </a>
        <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/configuracion/1/edit?view=config_finanza') }}" class="statistic-item-wrap"> 
            <div class="statistic-item gradient-bg fl-wrap">
                <i class="fa fa-building"></i>
                <div class="statistic-item-numder">Configuración</div>
                <h5>de Finanza</h5>
            </div>
        </a>
       
        @if($tienda->idcategoria==30)
        <!-- <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/configuracion/1/edit?view=config_ventacomida') }}" class="statistic-item-wrap"> 
            <div class="statistic-item gradient-bg fl-wrap">
                <i class="fa fa-utensils"></i>
                <div class="statistic-item-numder">Configuración</div>
                <h5>de Ventas (Comida)</h5>
            </div>
        </a> -->
      
        <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/configuracion/1/edit?view=config_comida') }}" class="statistic-item-wrap"> 
            <div class="statistic-item gradient-bg fl-wrap">
                <i class="fa fa-utensils"></i>
                <div class="statistic-item-numder">Configuración</div>
                <h5>de Comida</h5>
            </div>
        </a>
        @endif
        <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/configuracion/1/edit?view=config_facturacion') }}" class="statistic-item-wrap"> 
            <div class="statistic-item gradient-bg fl-wrap">
                <i class="fa fa-list"></i>
                <div class="statistic-item-numder">Configuración</div>
                <h5>de Facturación</h5>
            </div>
        </a> 
    </div>  
</div>
@endsection
