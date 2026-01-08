@extends('layouts.master')
@section('cuerpo')
<?php 
$usuario = DB::table('users')
    ->whereId(Auth::user()->id)
    ->first(); 
?>
<?php
  $role_admin = DB::table('role_user')
    ->where('user_id',$usuario->id)
    ->first(); 
?>
<div class="page-wrapper chiller-theme toggled">
  @if(Auth::user()->idtienda==0 && Auth::user()->idtipousuario==1)
  <a id="show-sidebar" class="btn btn-sm" href="javascript:;"><i class="fas fa-bars"></i>
  </a>
  <nav id="sidebar" class="sidebar-wrapper">
    <div class="sidebar-content">
      <div class="sidebar-brand">
        <a href="{{ url('backoffice/inicio') }}">BACKOFFICE</a>
        <div id="close-sidebar">
          <i class="fas fa-times"></i>
        </div>
      </div>
      <div class="sidebar-header">
        <div class="user-pic">
              <?php $rutaimagen = getcwd().'/public/backoffice/usuario/'.$usuario->id.'/perfil/'.$usuario->imagen; ?>
              @if(file_exists($rutaimagen) AND $usuario->imagen!='')
              <img class="img-responsive img-rounded" src="{{ url('public/backoffice/usuario/'.$usuario->id.'/perfil/'.$usuario->imagen) }}"/>
              @else
              <img class="img-responsive img-rounded" src="{{ url('public/backoffice/sistema/sin_imagen_redondo.png') }}">
              @endif
        </div>
        <div class="user-info">
          <span class="user-name">{{ $usuario->nombre }}
          </span>
          <span class="user-role">{{ $usuario->apellidos }}</span>
          <span class="user-status">
            <i class="fa fa-circle"></i>
            <span>Activo</span>
          </span>
        </div>
      </div>
      <!-- sidebar-search  -->
      <div class="sidebar-menu">
        <ul>
          <?php
          $modulos = DB::table('modulo')
            ->join('rolesmodulo','rolesmodulo.idmodulo','modulo.id')
            ->join('roles','roles.id','rolesmodulo.idroles')
            ->join('role_user','role_user.role_id','roles.id')
            ->where('role_user.user_id',Auth::user()->id)
            ->where('modulo.idmodulo',0)
            ->where('modulo.idestado',1)
            ->select('modulo.*')
            ->orderBy('modulo.orden','asc')
            ->get();
          ?>
          @foreach($modulos as $value)
          <li class="sidebar-dropdown mx-sidebar-dropdown">
            <a href="javascript:;"><i class="{{ $value->icono }}"></i><span>{{ $value->nombre }}</span></a>
            <div class="sidebar-submenu mx-sidebar-submenu">
               <ul>
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
                  @if($subvalue->vista=='' && $subvalue->controlador=='')
                  <li class="sidebar-dropdown mx-subsidebar-dropdown">
                    <a href="javascript:;">{{ $subvalue->nombre }}</a>
                    <div class="sidebar-submenu mx-subsidebar-submenu">
                       <ul>
                          <?php
                          $subsubmodulos = DB::table('modulo')
                            ->join('rolesmodulo','rolesmodulo.idmodulo','modulo.id')
                            ->join('roles','roles.id','rolesmodulo.idroles')
                            ->join('role_user','role_user.role_id','roles.id')
                            ->where('role_user.user_id',Auth::user()->id)
                            ->where('modulo.idmodulo',$subvalue->id)
                            ->where('modulo.idestado',1)
                            ->select('modulo.*')
                            ->orderBy('modulo.orden','asc')
                            ->get();
                          ?>
                          @foreach($subsubmodulos as $subsubvalue)
                          <li><a href="{{ url($subsubvalue->vista) }}">{{ $subsubvalue->nombre }}</a></li>
                          @endforeach
                       </ul>
                    </div>
                  </li>
                  @else
                  <li><a href="{{ url($subvalue->vista) }}">{{ $subvalue->nombre }}</a></li>
                  @endif
                  @endforeach
               </ul>
            </div>
          </li>
          @endforeach
        </ul>
      </div>
      <!-- sidebar-menu  -->
    </div>
  </nav>
  @else
  <style>
    .page-wrapper.toggled .page-content {
        padding: 0px !important;
    }
    .mx-subcuerpo {
        padding-left: 10px;
        padding-right: 10px;
    }
  </style>
  @endif
  <!-- sidebar-wrapper  -->
  <main class="page-content mx-cuerpo">
      <?php 
      $estadocine = 0;
      $url_path = Request::path();
      if($role_admin->role_id==1 or 
         $url_path=='backoffice/consumidor/red' or
         $url_path=='backoffice/consumidor/reparticion' or
         $url_path=='backoffice/consumidor/cobraganancia'){
      $planadquirido = consumidor_planadquirido(Auth::user()->id);
      //dd($planadquirido);
      ?>
      @if($role_admin->role_id==1)
          @yield('cuerpobackoffice') 
      @elseif($role_admin->role_id!=1 AND ($planadquirido['estado']=='NINGUNO' OR $planadquirido['estado']=='RED'))
          
          @if($url_path=='backoffice/cine')
              <?php 
              $userscine = DB::table('userscine')
                  ->where('userscine.idusers',Auth::user()->id)
                  ->orderBy('userscine.id','DESC')
                  ->limit(1)
                  ->first();
              ?>
              @if($userscine!='')
              @if($userscine->idestadouserscine==2)
                  <?php $estadocine=1 ?>
                  @yield('cuerpobackoffice')  
              @else
                  @include('app.habilitarentretenimiento') 
                  @include('app.consumidor.planes')  
              @endif
              @else
                  @include('app.habilitarentretenimiento') 
                  @include('app.consumidor.planes')  
              @endif
          @else
              @include('app.consumidor.planes')       
          @endif
      @elseif($planadquirido['estado']=='PENDIENTE')
          <div class="mensaje-warning">
              <i class="fa fa-check"></i>  Acaba de adquirir un <a href="javascript:;">Plan {{$planadquirido['data']->plannombre}}</a>, la confirmación se realizara lo más antes posible, gracias.
          </div>
      @elseif($planadquirido['estado']=='CORRECTO')
          @if($url_path=='backoffice/cine'  or $url_path=='backoffice/aulavirtual')
          <?php $estadocine=1 ?>
          @endif
          <div class="mensaje-success">
              <i class="fa fa-check"></i>  Usted tiene <a href="javascript:;">Plan {{$planadquirido['data']->plannombre}}</a>, de {{$planadquirido['data']->fechainicio}} hasta {{$planadquirido['data']->fechafin}}.</a>
          </div>
          @yield('cuerpobackoffice') 
      @elseif($planadquirido['estado']=='VENCIDO')
          <div class="mensaje-danger">
              Su <a href="javascript:;">Plan {{$planadquirido['data']->plannombre}}</a> se ha vencido el {{$planadquirido['data']->fechafin}}, no pierda las mejores ofertas, renueve su plan.
          </div>
          @include('app.consumidor.planes')
      @endif     
      <?php 
      }else{  ?>
          @yield('cuerpobackoffice')
      <?php } ?>

  </main>
  <!-- page-content" -->
</div>

<div class="limit-box fl-wrap"></div>


@endsection
@section('scripts')
<link rel="stylesheet" type="text/css" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
<link rel="stylesheet" type="text/css" href="{{ url('public/layouts/css/menuvertical.css') }}">

<script type="text/javascript">
  var width = $(window).width();
  if (width <= 1000){
    $(".page-wrapper").removeClass("toggled");
  }else{
    $(".page-wrapper").addClass("toggled");
  }
  $(window).resize(function() {
      var width = $(window).width();
      if (width <= 1000){
        $(".page-wrapper").removeClass("toggled");
      }else{
        $(".page-wrapper").addClass("toggled");
      }
  });
  //$(".page-wrapper").removeClass("toggled");
  $(".mx-sidebar-dropdown > a").click(function() {
      $(".mx-sidebar-submenu").slideUp(200);
      if($(this).parent().hasClass("active")) {
        $(".mx-sidebar-dropdown").removeClass("active");
        $(this).parent().removeClass("active");
      }else{
        $(".mx-sidebar-dropdown").removeClass("active");
        $(this).next(".mx-sidebar-submenu").slideDown(200);
        $(this).parent().addClass("active");
      }
  });
  $(".mx-subsidebar-dropdown > a").click(function() {
      $(".mx-subsidebar-submenu").slideUp(200);
      if($(this).parent().hasClass("active")) {
        $(".mx-subsidebar-dropdown").removeClass("active");
        $(this).parent().removeClass("active");
      }else{
        $(".mx-subsidebar-dropdown").removeClass("active");
        $(this).next(".mx-subsidebar-submenu").slideDown(200);
        $(this).parent().addClass("active");
      }
  });

$("#close-sidebar").click(function() {
  $(".page-wrapper").removeClass("toggled");
});
$("#show-sidebar").click(function() {
  $(".page-wrapper").addClass("toggled");
});


</script>

<style>
  .pricing-wrap {
    padding-left: 10px;
}

.parallax-section .section-title h2 {
    font-size: 16px !important;
}
  .mx-color-bg {
    background: #292929;
  }
  .shapes-bg-big:before {
    opacity: 0.5;
}
.mx-shapes-bg-big:before {
    background-image: url({{ url('public/backoffice/sistema/sitioweb/login/banner-login.jpg') }});
}
</style>


<?php
if($estadocine==1){
?>
  <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/bootstrap-reboot.min.css') }}">
  <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/bootstrap-grid.min.css') }}">
  <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/owl.carousel.min.css') }}">
  <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/jquery.mCustomScrollbar.min.css') }}">
  <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/nouislider.min.css') }}">
  <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/ionicons.min.css') }}">
  <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/magnific-popup.css') }}">
  <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/plyr.css') }}">
  <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/photoswipe.css') }}">
  <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/default-skin.css') }}">
  <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/main.css') }}">

  <script src="{{ url('public/layouts/cinema/backoffice/js/jquery-3.5.1.min.js') }}"></script>
  <script src="{{ url('public/layouts/cinema/backoffice/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ url('public/layouts/cinema/backoffice/js/owl.carousel.min.js') }}"></script>
  <script src="{{ url('public/layouts/cinema/backoffice/js/jquery.magnific-popup.min.js') }}"></script>
  <script src="{{ url('public/layouts/cinema/backoffice/js/jquery.mousewheel.min.js') }}"></script>
  <script src="{{ url('public/layouts/cinema/backoffice/js/jquery.mCustomScrollbar.min.js') }}"></script>
  <script src="{{ url('public/layouts/cinema/backoffice/js/wNumb.js') }}"></script>
  <script src="{{ url('public/layouts/cinema/backoffice/js/nouislider.min.js') }}"></script>
  <script src="{{ url('public/layouts/cinema/backoffice/js/plyr.min.js') }}"></script>
  <script src="{{ url('public/layouts/cinema/backoffice/js/photoswipe.min.js') }}"></script>
  <script src="{{ url('public/layouts/cinema/backoffice/js/photoswipe-ui-default.min.js') }}"></script>
  <script src="{{ url('public/layouts/cinema/backoffice/js/main.js') }}"></script>    

  <style>
.container {
    position: inherit;
}
    .content {
      background-color:#1a191f;
    }
    .card {
      background-color: #31353D;
      /*background-image: url({{ url('public/layouts/cinema/backoffice/img/textura.png') }})*/
    }
  </style>
  <script>
    function seleccionar_categoria(categoria){
      if(categoria!=''){
          categoria = '?categoria='+categoria;
      }
      location.href = '{{ url('backoffice/cine') }}'+categoria;
    }
  </script>
<?php
} 
?>


@section('scriptsbackoffice')
@show
@section('scriptsbackoffice1')
@show
@section('scriptsbackoffice2')
@show
@section('scriptsbackoffice3')
@show
@section('scriptsapp1')
@show
@section('scriptsapp2')
@show
@section('scriptsapp3')
@show
@section('scriptsapp4')
@show
@section('scriptsapp5')
@show
@section('scriptssistema')
@show
@endsection