<!DOCTYPE HTML>
<html lang="es">
    <head>
        <!--=============== basic  ===============-->
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="robots" content="index, follow"/>
        <!--=============== Tienda  ===============-->	
        @if($tienda!='')
        <title>{{ $tienda->nombre }}</title>
        <?php $rutaimagen = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/logo/'.$tienda->imagen; ?>
        @if(file_exists($rutaimagen))
          <link rel="shortcut icon" href="{{ url('public/backoffice/tienda/'.$tienda->id.'/logo/'.$tienda->imagen) }}">
        @endif
        <meta name="description" content="{{ $tienda->contenido }}" />
        <meta name="twitter:card" value="summary">
        <meta property="og:title" content="{{ $tienda->nombre }}" />
        <meta property="og:type" content="article" />
        <meta property="og:url" content="{{ Request::url() }}" />
        <?php $rutaimagen = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/portada/'.$tienda->imagenportada; ?>
        @if(file_exists($rutaimagen))
          <meta property="og:image" content="{{ url('public/backoffice/tienda/'.$tienda->id.'/portada/'.$tienda->imagenportada) }}" />
        @endif
        <meta property="og:description" content="{{ $tienda->contenido }}" />
        @endif
      
        <!--=============== css  ===============-->	
        <link rel="stylesheet" href="{{ url('public/layouts/css/reset.css') }}">
        <link rel="stylesheet" href="{{ url('public/layouts/css/plugins.css') }}">
        <link rel="stylesheet" href="{{ url('public/layouts/css/color.css') }}">

        <!--=============== otros ===============-->
        <link rel="stylesheet" href="{{ url('public/libraries/app/css/carga.css') }}">
        <link rel="stylesheet" href="{{ url('public/libraries/app/css/checkbox.css') }}">
        <link rel="stylesheet" href="{{ url('public/libraries/select2/select2.css') }}">
      
        <link rel="stylesheet" type="text/css" href="{{ url('public/layouts/css/menuhorizontal.css') }}">
        
        <link rel="stylesheet" href="{{ url('public/layouts/css/style.css') }}">

    </head>
    <body url="{{ url('/') }}">
        <div class="loader-wrap">
            <div class="pin"></div>
            <div class="pulse"></div>
        </div>
        <section class="scroll-con-sec hero-section" data-scrollax-parent="true" id="sec1">
            <div style="z-index: 10;position: absolute;text-align: center;width: 100%;">
                <a href="{{ $tienda->idestadoprivacidad==1?url($tienda->link):'javascript:;' }}" style="color: #fff;font-size: 20px;font-weight: bold;">
                    {{ $tienda->nombre }}
                </a>
            </div>
            <div style="width: 100%;margin-bottom: 20px;margin-top: 80px;">
                  @if(Auth::user()->idtienda!=0)
                      <?php $caja = caja($tienda->id,Auth::user()->id); ?>
                      @if($caja['resultado']=='PROCESO')
                        <a href="javascript:;" class="mx-alert-caja inactivo"><i class="fa fa-sync-alt"></i> Apertura en Proceso</span></a>
                      @elseif($caja['resultado']=='PENDIENTE')
                        <a href="javascript:;" class="mx-alert-caja inactivo"><i class="fa fa-sync-alt"></i> Apertura Pendiente</span></a> 
                      @elseif($caja['resultado']=='ABIERTO')
                         <a href="javascript:;" class="mx-alert-caja activo"><i class="fa fa-tags"></i> Caja Activa ({{$moneda_soles->simbolo}} {{ efectivo($tienda->id,$caja['apertura']->id)['total'] }}</a>
                      @else
                        <a href="javascript:;" class="mx-alert-caja inactivo"><i class="fa fa-tags"></i> Caja Inactiva</a>
                      @endif 
                  @endif 
                  <a href="javascript:;" onclick="ir_perfil()" class="mx-alert-caja inactivo">
                      @if(Auth::user()->idtienda!=0)
                      <?php 
                      $usuario = DB::table('users')
                              ->join('role_user','role_user.user_id','users.id')
                              ->join('roles','roles.id','role_user.role_id')
                              ->where('users.id',Auth::user()->id)
                              ->select('users.*','roles.description as permiso')
                              ->limit(1)
                              ->first();
                      $rutaimagen = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/sistema/'.$usuario->imagen; 
                      $urlimagenusuario = '';
                      if(file_exists($rutaimagen) AND $usuario->imagen!=''){
                          $urlimagenusuario = url('/public/backoffice/tienda/'.$tienda->id.'/sistema/'.$usuario->imagen);
                      }else{
                          $urlimagenusuario = url('public/backoffice/sistema/sin_imagen_redondo.png');
                      }
                      ?>
                       <img class="thumb" src="{{ $urlimagenusuario }}" class="imglogousuario" style="width: 30px;height: 30px;border-radius: 15px;">
                         {{ $usuario->nombre }} ({{ $usuario->permiso }})
                      @endif
                  </a>
            </div>
            <div class="bg"  data-bg="{{ url('public/backoffice/sistema/banner-1.png') }}" data-scrollax="properties: { translateY: '200px' }"></div>
            <div class="overlay"></div>
            <div class="hero-section-wrap fl-wrap">
                    <div class="container">
                        <div data-v-04cc2f02="" class="landing__categories">
                            <a href="javascript:;" onclick="ir_modulo()" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble modulo_perfil"  style="display:none;">
                              <img data-v-b789b216="" src="{{url('public/backoffice/sistema/modulosistema/atras.png')}}" class="category-bubble-icon">
                              <h2 data-v-b789b216="" class="category-bubble-title">Atras</h2>
                            </a>
                            <a href="javascript:;" onclick="perfil_index()" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble modulo_perfil"  style="display:none;">
                              <img data-v-b789b216="" src="{{url('public/backoffice/sistema/modulosistema/editar_usuario.png')}}" class="category-bubble-icon">
                              <h2 data-v-b789b216="" class="category-bubble-title">Editar Perfil</h2>
                            </a>
                            <a href="javascript:;" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble modulo_perfil"  style="display:none;">
                              <img data-v-b789b216="" src="{{url('public/backoffice/sistema/modulosistema/contrasena.png')}}" class="category-bubble-icon">
                              <h2 data-v-b789b216="" class="category-bubble-title">Cambiar Contraseña</h2>
                            </a>
                            <a href="javascript:;" onclick="document.getElementById('logout-form-sistema').submit()" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble modulo_perfil"  style="display:none;">
                              <img data-v-b789b216="" src="{{url('public/backoffice/sistema/modulosistema/salir.png')}}" class="category-bubble-icon">
                              <h2 data-v-b789b216="" class="category-bubble-title">Cerrar Sesión</h2>
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
                            </a>
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
                        ?>
                        @foreach($modulos as $value)
                            <?php 
                            $rutaimagen = getcwd().'/public/backoffice/sistema/modulo/'.$value->imagen; 
                            if(file_exists($rutaimagen) AND $value->imagen!=''){
                                $urlimagen = url('public/backoffice/sistema/modulo/'.$value->imagen);
                            }else{
                                $urlimagen = url('public/backoffice/sistema/sin_imagen_redondo.png');
                            }
                            ?>
                            <a href="javascript:;" onclick="ir_submodulo({{ $value->id }})" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-modulo">
                              <img data-v-b789b216="" src="{{$urlimagen}}" class="category-bubble-icon">
                              <h2 data-v-b789b216="" class="category-bubble-title">{{ $value->nombre }}</h2>
                            </a>
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
                                $ii = 0;
                                ?>
                                @foreach($submodulos as $subvalue)
                                    <?php 
                                    $rutasubimagen = getcwd().'/public/backoffice/sistema/modulo/'.$subvalue->imagen; 
                                    if(file_exists($rutasubimagen) AND $subvalue->imagen!=''){
                                        $urlsubimagen = url('public/backoffice/sistema/modulo/'.$subvalue->imagen);
                                    }else{
                                        $urlsubimagen = url('public/backoffice/sistema/sin_imagen_redondo.png');
                                    }
                                    ?>
                                    @if($ii==0)
                                    <a href="javascript:;" id="cont-submodulo{{ $value->id }}" onclick="ir_modulo()" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-submodulo" style="display:none;">
                                      <img data-v-b789b216="" src="{{url('public/backoffice/sistema/modulosistema/atras.png')}}" class="category-bubble-icon">
                                      <h2 data-v-b789b216="" class="category-bubble-title">Atras</h2>
                                    </a>
                                    @endif
                                    <?php $href = str_replace('{idtienda}', $tienda->id, $subvalue->vista); ?>
                                    <a href="{{ url($href) }}" id="cont-submodulo{{ $value->id }}" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-submodulo" style="display:none;">
                                      <img data-v-b789b216="" src="{{$urlsubimagen}}" class="category-bubble-icon">
                                      <h2 data-v-b789b216="" class="category-bubble-title">{{ $subvalue->nombre }}</h2>
                                    </a>
                                    <?php $ii++ ?>
                                @endforeach
                        @endforeach
                        </div>
                        <div data-v-04cc2f02="" class="landing__categories" id="cont-cuerposistema" style="background: #fff;border-radius: 5px;padding: 10px;">
                        </div>
                    </div>
            </div>
        </section>

        @include('app.footer')

        <!--=============== scripts  ===============-->     
        <script src="{{ url('public/layouts/js/jquery.min.js') }}"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="{{ url('public/layouts/js/plugins.js') }}"></script>
        <script src="{{ url('public/layouts/js/scripts.js') }}"></script>
        <script src="{{ url('public/libraries/app/js/app.js') }}"></script>
        <script src="{{ url('public/libraries/select2/select2.js') }}"></script>
      
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAyFtw-yEfh6GtiPyx_4ZQWt3g_vUCu5eQ"></script>
        <script src="{{ url('public/layouts/js/map_infobox.js') }}"></script>
        <script src="{{ url('public/layouts/js/markerclusterer.js') }}"></script>  
      
        
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/scroller/2.0.3/js/dataTables.scroller.min.js"></script>
        <script src="https://cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>
        <script src="https://cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.js"></script>
        <script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>

        <script src="{{ url('public/layouts/js/menuhorizontal.js') }}"></script>

       
        @section('sistema_htmls')
        @show
        @section('sistema_scripts')
        @show
      
      <style>
  /* Menu */
  .profile-edit-page-header {
      z-index: 10;
      background: none;
  }
  /* Caja */
      .mx-alert-caja {
        color: #fff;
        margin-left: 10px;
        padding: 15px;
        border-radius: 5px;
        margin-top: 20px;
        font-size: 13px;
        margin-right: 10px;    
        z-index: 10;
        font-weight: 400;
      }
      .activo {
        background-color: #08ab0f;
      }
      .inactivo {
        background-color: #31353d;
      }
  /* Caudros */
  section.hero-section {
      padding: 100px 0 100px;
  }
  .landing__categories[data-v-04cc2f02] {
      justify-content: center;
      flex-direction: row;
      display: flex;
      flex-wrap: wrap;
      margin: 0 auto;
  }
  .landing__categories>.category-bubble[data-v-04cc2f02] {
      margin: 12.5px;
  }
  .category-bubble[data-v-b789b216] {
      background-color: #fff;
      border-radius: 5px;
      cursor: pointer;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
      box-shadow: 0 2px 17px 0 #000000;
      height: 114.2px;
      width: 114.2px;
      text-align: center;
      transition: .3s;
      text-decoration: none;
      color: #000;
      font-weight: 300;
      margin: 20px 0 -10px;
  }
  .category-bubble .category-bubble-icon[data-v-b789b216] {
      margin-top: 10px;
      height: 60px;
  }
  .category-bubble .category-bubble-title[data-v-b789b216] {
      line-height: 16px;
      margin-top: 3px;
      font-weight: 300;
      /*width: 73px;*/
      margin-left: auto;
      margin-right: auto;
      overflow: hidden;
      font-size: 14px;
  }
  .category-bubble[data-v-b789b216]:hover {
      height: 130px;
      width: 130px;
      margin-left: 4.5px;
      margin-right: 4.5px;
      margin-top: -4px;
  }
  .category-bubble:hover .category-bubble-icon[data-v-b789b216] {
      height: 80px;
  }
  @media only screen and  (max-width: 440px) {
      .category-bubble[data-v-b789b216] {
          height: 90px;
          width: 90px;
      }
      .category-bubble .category-bubble-icon[data-v-b789b216] {
          margin-top: 13px;
          height: 40px;
      }
      .category-bubble[data-v-b789b216]:hover {
          height: 90px;
          width: 90px;
      }
      .category-bubble .category-bubble-icon[data-v-b789b216]:hover {
          margin-top: 13px;
          height: 40px;
      }
      section.hero-section {
          padding: 30px 0 30px;
      }
      .landing__categories>.category-bubble[data-v-04cc2f02] {
          margin: 8px;
      }
      .category-bubble .category-bubble-title[data-v-b789b216] {
          font-size: 12px;
      }
  }
</style>

<script>
    function ir_submodulo(idmodulo){
        $('a#cont-submodulo'+idmodulo).css('display','block');
        $('.cont-modulo').css('display','none');
    }
    function ir_modulo(){
        $('.cont-modulo').css('display','block');
        $('.cont-submodulo').css('display','none');
        $('.modulo_perfil').css('display','none');
        $('#cont-cuerposistema').html('');
    }
    function ir_perfil(){
        $('.modulo_perfil').css('display','block');
        $('.cont-modulo').css('display','none');
        $('.cont-submodulo').css('display','none');
    }
    function perfil_index(){
        pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id) }}/inicio/1/edit?view=editperfil',result:'#cont-cuerposistema'});
    }
</script>
    </body>
</html>



                    

