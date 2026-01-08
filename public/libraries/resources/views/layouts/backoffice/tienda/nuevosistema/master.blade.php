@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<?php
$dominio_perzonalizado = obtener_dominio_perzonalizado();
$url_path = Request::path(); 
$url = explode('backoffice/tienda/sistema/',$url_path);
//dd($url[1]);
?>
@if(count($url)>0)
<?php
$tienda = DB::table('tienda')->whereId($url[1])->first();
$agencia = DB::table('s_agencia')
    ->where('idtienda',$tienda->id)
    ->where('idestadofacturacion',1)
    ->limit(1)
    ->first();
$moneda_soles = DB::table('s_moneda')->whereId(1)->first();
$moneda_dolares = DB::table('s_moneda')->whereId(2)->first();
//print_r($agencia);
//dd($agencia);
?>
<div class="profile-edit-page-header">
    <a href="{{ $tienda->idestadoprivacidad==1?url($tienda->link):'javascript:;' }}"><h2>{{ $tienda->nombre }}</h2></a>
    @if(Auth::user()->idtienda!=0)
        <?php $caja = caja($tienda->id,Auth::user()->id); ?>
        @if($caja['resultado']=='PROCESO')
          <a href="javascript:;" class="mx-alert-caja inactivo"><i class="fa fa-sync-alt"></i> Apertura en Proceso</span></a>
        @elseif($caja['resultado']=='PENDIENTE')
          <a href="javascript:;" class="mx-alert-caja inactivo"><i class="fa fa-sync-alt"></i> Apertura Pendiente</span></a> 
        @elseif($caja['resultado']=='ABIERTO')
           <a href="javascript:;" class="mx-alert-caja activo"><i class="fa fa-tags"></i> Caja Activa ({{$moneda_soles->simbolo}} {{ efectivo($tienda->id,$caja['apertura']->id)['total'] }} <!-- - {{$moneda_dolares->simbolo}} {{ efectivo($tienda->id,$caja['apertura']->id,2)['total'] }}) --></a>
        @else
          <a href="javascript:;" class="mx-alert-caja inactivo"><i class="fa fa-tags"></i> Caja Inactiva</a>
        @endif 
    <style>
      .mx-alert-caja {
        float: left;
        color: #fff;
        margin-left: 20px;
        padding-left: 10px;
        padding-right: 10px;
        padding-top: 2px;
        padding-bottom: 2px;
        border-radius: 10px;
        margin-top: 20px;
        font-size: 11px;
      }
      .activo {
        background-color: #08ab0f;
      }
      .inactivo {
        background-color: #31353d;
      }
    </style>
    @endif 
    <nav class="menu-sistema">
      <div>
        <i class="fa fa-bars"></i>
      </div>
      <ul>
        <?php
          $modulos = DB::table('modulo')
            ->join('rolesmodulo','rolesmodulo.idmodulo','modulo.id')
            ->join('roles','roles.id','rolesmodulo.idroles')
            ->join('role_user','role_user.role_id','roles.id')
            ->where('role_user.user_id',Auth::user()->id)
            ->where('modulo.idmodulo',7)
            ->where('modulo.idestado',1)
            ->select('modulo.*')
            ->orderBy('modulo.orden','asc')
            ->get();
          //dd(Auth::user()->id);
          ?>
          <?php $i = 1  ; ?>
          <?php $cantmodulos = count($modulos); ?>
          @foreach($modulos as $value)
             <li><a href="javascript:;"><i class="{{ $value->icono }}"></i> {{ $value->nombre }} <i class="fa fa-sort-desc"></i></a>
                  <ul <?php echo (Auth::user()->idtienda==0 && $cantmodulos==$i)? 'style="right: 10px;"':'' ?>>
                  <?php
                  $submodulos = DB::table('modulo')
                    ->join('rolesmodulo','rolesmodulo.idmodulo','modulo.id')
                    ->join('roles','roles.id','rolesmodulo.idroles')
                    ->join('role_user','role_user.role_id','roles.id')
                    ->where('role_user.user_id',Auth::user()->id)
                    ->where('modulo.idmodulo',$value->id)
                    ->where('modulo.idestado',1)
                    ->select('modulo.*')
                    ->orderBy('modulo.orden','asc')
                    ->get();
                  ?>
                  @foreach($submodulos as $subvalue)
                  @if($subvalue->vista!='' && $subvalue->controlador!='')
                        <?php $href = str_replace('{idtienda}', $tienda->id, $subvalue->vista); ?>
                        <li><a href="{{ url($href) }}"><i class="{{ $subvalue->icono }}"></i> {{ $subvalue->nombre }}</a></li> 
                  @endif
                  @endforeach
                  </ul>
              </li>
              <?php $i++; ?>
          @endforeach

        @if(Auth::user()->idtienda!=0)
        <?php $usuario = DB::table('users')
                ->join('role_user','role_user.user_id','users.id')
                ->join('roles','roles.id','role_user.role_id')
                ->where('users.id',Auth::user()->id)
                ->select('users.*','roles.description as permiso')
                ->limit(1)
                ->first(); ?>
        <li><a href="javascript:;" style="background-color: #31353d;padding-top: 14px;padding-bottom: 12px;">
              <?php $rutaimagen = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/sistema/'.$usuario->imagen; ?>
              @if(file_exists($rutaimagen) AND $usuario->imagen!='')
                  <img class="thumb" src="{{ url('/public/backoffice/tienda/'.$tienda->id.'/sistema/'.$usuario->imagen) }}" class="imglogousuario" style="width: 30px;height: 30px;border-radius: 15px;">
              @else
                  <img class="thumb" src="{{ url('public/backoffice/sistema/sin_imagen_redondo.png') }}" class="imglogousuario" style="width: 30px;height: 30px;border-radius: 15px;">
              @endif
           {{ $usuario->nombre }} ({{ $usuario->permiso }}) <i class="fa fa-sort-desc" style="float: right;margin-top: 5px;"></i></a>
          <ul style="right: 10px;background: #31353d;">
            <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio') }}"><i class="fa fa-home"></i> Inicio</a></li>
            @if($tienda->idestadoprivacidad==1)
            <li><a href="{{ url($tienda->link) }}"><i class="fa fa-store"></i> Tienda Virtual</a></li>
            @endif
            <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/1/edit?view=editperfil') }}"><i class="fa fa-edit"></i> Editar Perfil</a></li>
            <!--li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/1/edit?view=editmetodopago') }}"><i class="fa fa-money-check-alt"></i> Método de Pago</a></li-->
            <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/1/edit?view=editcambiarclave') }}"><i class="fa fa-unlock-alt"></i> Cambiar Contraseña</a></li>
             <!--li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/create') }}"><i class="fa fa-clock-o"></i>Horario Ingreso y Salida</a></li-->
            <li><a href="javascript:;" onclick="document.getElementById('logout-form-sistema').submit()"><i class="fa fa-power-off"></i> Cerrar Sesión</a></li>
            <form method="POST" id="logout-form-sistema" action="{{ route('logout') }}">
              @csrf 
              <input type="hidden" value="{{ $tienda->id }}" name="logoutidtienda">
              <input type="hidden" value="{{ Auth::user()->idtipousuario}}" name="logoutidtipousuario">
              @if($dominio_perzonalizado!='')
              <input type="hidden" value="{{ $dominio_perzonalizado->dominio_personalizado }}" name="logoutlink">
              @else
              <input type="hidden" value="{{ url($tienda->link) }}/login" name="logoutlink">
              @endif
            </form>
          </ul>
        </li>
        @endif
      </ul>
    </nav> 
</div>
<div class="mx-subcuerpo">
    <div class="profile-edit-container">
        <div class="custom-form" id="mx-subcuerpo">
            @yield('cuerpotiendasistema')  
        </div>
    </div> 
</div>
@endif
@endsection
@section('scriptssistema')
<style>
  
.mx-btn-post{
    background: {{$tienda->ecommerce_color}};
    color:#fff;
    width:100%;
}
.mx-btn-post:hover{
    background: #31353d;
}
  
  .imglogousuario {
    width: 40px;
    margin-top: -12px;
    margin-bottom: -12px;
    margin-right: 5px;
  }
  .list-single-main-wrapper .breadcrumbs{
    background: #31353d;
  }
.fuzone:hover .fu-text i,
.header-user-menu ul li a:hover {
    color: {{$tienda->ecommerce_color}};
}
  .box-item a.gal-link,
  /*.btn.flat-btn,*/
  .pagination a.current-page,
  .pagination a:hover,
  .btn-info,
  .menu-sistema ul li ul li a:hover,
  .profile-edit-page-header {
    background: {{$tienda->ecommerce_color}};
}
.fuzone {
    border-color: {{$tienda->ecommerce_color}};
}
  
/*.gradient-bg {
    background-color: {{$tienda->ecommerce_color}};
    background: -webkit-gradient(linear, 0% 0%, 0% 100%, from({{$tienda->ecommerce_color}}), to(#008cea));
    background: -webkit-linear-gradient(top, {{$tienda->ecommerce_color}}, #008cea);
    background: -moz-linear-gradient(top, {{$tienda->ecommerce_color}}, #008cea);
    background: -ms-linear-gradient(top, {{$tienda->ecommerce_color}}, #008cea);
    background: -o-linear-gradient(top, {{$tienda->ecommerce_color}}, #008cea);
}*/
.gradient-bg h5 {
    font-size: 16px;
    font-weight: bold;
}
.statistic-item-numder {
    font-size: 16px;
}
.statistic-item i {
    font-size: 45px;
    right: 20px;
}
.statistic-item-numder {
    padding-bottom: 0px;
}
.statistic-item {
    padding: 20px 20px;
}
  
/*#montorecibido {
    border: 2px solid {{$tienda->ecommerce_color}};
    font-size: 14px;
    color: {{$tienda->ecommerce_color}};
}
#vuelto {
    border: 2px solid #343a40;
    font-size: 14px;
    color: #343a40;
}*/
.mx-tienda-subtitulo {
    background-color: {{$tienda->ecommerce_color}};
    color: #fff;
    border-radius: 5px;
    padding: 12px;
    text-align: left;
    margin-bottom: 5px;
    float: left;
    width: 100%;
}
  .mx-table-warning {
    background-color: #f1c40f;
  }
</style>
<link rel="stylesheet" type="text/css" href="{{ url('public/layouts/css/menuhorizontal.css') }}">
<script src="{{ url('public/layouts/js/menuhorizontal.js') }}"></script>
@section('subscripts')
@show
@endsection