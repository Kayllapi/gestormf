
<!DOCTYPE HTML>
<html lang="es">
    <head>
        <!--=============== basic  ===============-->
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="robots" content="index, follow"/>
        <!--=============== Tienda  ===============-->	
        <title>{{ config('app.name', 'Kayllapi') }}</title>
        <link rel="shortcut icon" href="{{ url('public/backoffice/sistema/favicon.ico') }}">
        <meta name="description" content="Somos una plataforma de búsqueda, con la finalidad de ayudar a todo los usuarios a encontrar el producto y/o servicio adecuado que tu negocio brinda, así mismo buscamos ser la mejor plataforma de búsqueda de negocios online"/>
        <meta name="twitter:card" value="summary">
        <meta property="og:title" content="Kayllapi" />
        <meta property="og:type" content="article" />
        <meta property="og:url" content="{{ Request::url() }}" />
        <meta property="og:image" content="http://kayllapi.com/public/backoffice/sistema/banner.png" />
        <meta property="og:description" content="Somos una plataforma de búsqueda, con la finalidad de ayudar a todo los usuarios a encontrar el producto y/o servicio adecuado que tu negocio brinda, así mismo buscamos ser la mejor plataforma de búsqueda de negocios online" />

      
        <!--link href="https://vjs.zencdn.net/7.8.4/video-js.css" rel="stylesheet" /-->

        <!-- If you'd like to support IE8 (for Video.js versions prior to v7) -->
        <!--script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script-->
      
        <!--=============== css  ===============-->	
        <link rel="stylesheet" href="{{ url('public/layouts/css/reset.css') }}">
        <link rel="stylesheet" href="{{ url('public/layouts/css/plugins.css') }}">
        <link rel="stylesheet" href="{{ url('public/layouts/css/color.css') }}">

        <!--=============== otros ===============-->
        <link rel="stylesheet" href="{{ url('public/libraries/app/css/carga.css') }}">
        <link rel="stylesheet" href="{{ url('public/libraries/app/css/checkbox.css') }}">
        <link rel="stylesheet" href="{{ url('public/libraries/jstree-3.2.1/style.min.css') }}">
        <link rel="stylesheet" href="{{ url('public/libraries/select2/select2.css') }}">
        
        <link rel="stylesheet" href="{{ url('public/layouts/css/style.css') }}">

      
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
      
    </head>
    <body url="{{ url('/') }}" id="bodymaster">
        <!--loader-->
        <div class="loader-wrap">
            <div class="pin"></div>
            <div class="pulse"></div>
        </div>
        <!--loader end-->
        <!-- Main  -->
        <div id="main">

            <!-- header-->
            <header class="main-header dark-header fs-header sticky">
              <div class="container">
                <div class="header-inner">
                    <div class="logo-holder">
                        <a href="{{ url('/') }}"><img src="{{ url('public/backoffice/sistema/kayllapi-logo2.png') }}" alt="{{ config('app.name', 'Kayllapi') }}"></a>
                    </div>
                    <div class="header-search vis-header-search">
                      <form action="{{ url('buscador/tienda') }}" method="GET">
                        <div class="header-search-input-item">
                            <input type="text" value="{{ isset($_GET['search']) ? $_GET['search'] : '' }}" name="search" placeholder="¿qué estás buscando?"/>
                        </div>
                        <button class="header-search-button" type="submit"><i class="fa fa-search"></i></button>
                      </form>
                    </div>
                    <div class="show-search-button"><i class="fa fa-search"></i> <span>Buscar</span></div>
              
                    @if(Auth::user())
                    <?php  $usuario = DB::table('users')
                      ->whereId(Auth::user()->id)
                      ->first();
                  ?>
                    @if(Auth::user()->idtienda==0 && Auth::user()->idtipousuario==1)
                    <div class="header-user-menu" id="menu-master">
                        <div class="header-user-name">
                              <?php 
                              $rutaimagen = getcwd().'/public/backoffice/usuario/'.$usuario->id.'/perfil/'.$usuario->imagen; 
                              $urlimagen = url('public/backoffice/sistema/sin_imagen_redondo.png');
                              if(file_exists($rutaimagen) AND $usuario->imagen!=''){
                                  $urlimagen = url('public/backoffice/usuario/'.$usuario->id.'/perfil/'.$usuario->imagen);
                              }
                              ?>
                            <span style="background-image: url({{ $urlimagen }});
                                              background-repeat: no-repeat;
                                              background-size: cover;
                                              background-position: center;">
                            </span>
                         
                            <div class="header-user-nombre">{{ $usuario->nombre }}</div>
                        </div>
                        <ul>
                            <li><a href="{{ url('backoffice/inicio') }}"><i class="fa fa-home"></i> BackOffice</a></li>
                            <!--li><a href="{{ url('backoffice/carritocompra') }}"><i class="fa fa-shopping-cart"></i> Mis Pedidos</a></li-->
                            <li><a href="{{ url('backoffice/perfil') }}"><i class="fa fa-edit"></i> Editar Perfil</a></li>
                            <li><a href="{{ url('backoffice/perfil/0/edit?view=monedaskay') }}"><i class="fa fa-money"></i> Monedas Kay</a></li>
                            <li><a href="{{ url('backoffice/perfil/0/edit?view=editmetodopago') }}"><i class="fa fa-money-check-alt"></i> Método de Pago</a></li>
                            <li><a href="{{ url('backoffice/perfil/usuario/edit?view=editcambiarclave') }}"><i class="fa fa-unlock-alt"></i> Cambiar Contraseña</a></li>
                            <li><a href="javascript:;" onclick="document.getElementById('logout-form').submit()"><i class="fa fa-power-off"></i> Cerrar Sesión</a></li>
                        </ul>
                    </div>
                    <form method="POST" id="logout-form" action="{{ route('logout') }}">
                      @csrf
                      <input type="hidden" value="0" name="logoutidtienda">
                      <input type="hidden" value="{{ Auth::user()->idtipousuario}}" name="logoutidtipousuario">
                      <input type="hidden" value="" name="logoutlink">
                    </form>
                    <!-- nav-button-wrap-->
                    <div class="nav-button-wrap color-bg">
                        <div class="nav-button">
                            <span></span><span></span><span></span>
                        </div>
                    </div>
                    <!-- nav-button-wrap end-->
                    <a class="show-reg-form logreg-modal-open" id="modal-carritocompra" onclick="selectcarritocompra()"><i class="fa fa-shopping-cart"></i></a>
                    <style>
                    .show-reg-form {
                          margin-right: 57px;
                      }
                    </style>
                    @else
                      <a href="{{ url('backoffice/'.Auth::user()->idtienda.'/inicio') }}" class="add-list"><span><i class="fa fa-cloud"></i></span> Ir a Sistema</a>
                      <!-- nav-button-wrap-->
                      <div class="nav-button-wrap color-bg">
                          <div class="nav-button">
                              <span></span><span></span><span></span>
                          </div>
                      </div>
                      <!-- nav-button-wrap end-->
                      <a class="show-reg-form logreg-modal-open" id="modal-carritocompra" onclick="selectcarritocompra()"><i class="fa fa-shopping-cart"></i></a>
                      <style>
                        .nav-holder {
                            margin-right: 0px;
                        }
                    </style>
                    @endif
                    <style>
                        .nav-button-wrap{
                          margin-right: 65px;
                        }
                    </style>
                    @else
                      <a href="javascript:;" class="add-list" id="modal-iniciarsesion-master"><span><i class="fa fa-sign-in"></i></span> BackOffice</a>
                      <!-- nav-button-wrap-->
                      <div class="nav-button-wrap color-bg">
                          <div class="nav-button">
                              <span></span><span></span><span></span>
                          </div>
                      </div>
                      <!-- nav-button-wrap end-->
                      <a class="show-reg-form logreg-modal-open" id="modal-carritocompra" onclick="selectcarritocompra()"><i class="fa fa-shopping-cart"></i></a>
                      <style>
                        .menu-movil-kayllapi{
                          display:none;
                        }
                        @media (max-width: 540px) {
                          .menu-movil-kayllapi {
                            display: block;
                          }
                        }
                      </style>
                    @endif
                    <!--  navigation -->
                   
                    <div class="nav-holder main-menu">
                        <nav>
                            <ul>
                                <li>
                                    <a href="{{ url('/') }}" <?php echo Request::path()=='/' ? 'class="act-link"':'' ?>>Inicio</a>
                                </li>
                                <li>
                                    <a href="{{ url('pagina/noticias') }}" <?php echo Request::path()=='noticias' ? 'class="act-link"':'' ?>>Noticias</a>
                                </li>
                                <li>
                                    <!--<a href="{{ url('comofunciona') }}" <?php /*echo Request::path()=='comofunciona' ? 'class="act-link"':'' */?>>Como funona</a> -->
                                    <a href="{{ url('pagina/nosotros') }}" <?php echo Request::path()=='nosotros' ? 'class="act-link"':'' ?>>¿Quienes Somos?</a>
                                </li>
                                <!--li>
                                    <a href="{{ url('pagina/cine') }}">Entretenimiento</a>
                                </li-->
                                <!--li>
                                    <a href="{{ url('contacto') }}" <?php echo Request::path()=='contacto' ? 'class="act-link"':'' ?>>Contactenos</a>
                                </li-->
                            </ul>
                        </nav>
                    </div>
                    <!-- navigation  end -->
                </div>
              </div>
            </header>
            <!--  header end -->

            <!--  wrapper  -->
            <div id="wrapper">
                <!-- Content-->
                <div class="content">
                @yield('cuerpo')
                </div>
                <!-- Content end -->
            </div>
        </div>
        <!-- Main end -->
      
      
        <!--=============== scripts  ===============-->     
        <script src="{{ url('public/layouts/js/jquery.min.js') }}"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="{{ url('public/layouts/js/plugins.js') }}"></script>
        <script src="{{ url('public/layouts/js/scripts.js') }}"></script>
        <script src="{{ url('public/libraries/app/js/app.js') }}"></script>
        <script src="{{ url('public/libraries/jstree-3.2.1/jstree.min.js') }}"></script>
        <script src="{{ url('public/libraries/select2/select2.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js"></script> 
      
        <script src="https://checkout.culqi.com/js/v3"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDMz3eE2VbbQ47xCnyv1OepO_kN_t21ip8"></script>
        <script src="{{ url('public/layouts/js/map_infobox.js') }}"></script>
        <script src="{{ url('public/layouts/js/markerclusterer.js') }}"></script>  
      
        
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/scroller/2.0.3/js/dataTables.scroller.min.js"></script>
        <script src="https://cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>
        <script src="https://cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.js"></script>
        <script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
      
        <!--script src="https://vjs.zencdn.net/7.8.4/video.js"></script-->
        <script>
        $(".nav-button-wrap").click(function() {
            $(".header-user-menu > ul").removeClass("hu-menu-vis");
        });
        $(".header-user-name > span").click(function() {
            $(".main-menu").removeClass("vismobmenu");
        });


        // menu master
        $("div#menu-master").on("click", function () {
            $("ul",this).toggleClass("hu-menu-vis");
            $("div",this).toggleClass("hu-menu-visdec");
        });

        // menu tabla
        $("div#menu-opcion").on("click", function () {
            //$("div#menu-opcion > ul").removeClass("hu-menu-vis");
            $("ul",this).toggleClass("hu-menu-vis");
            $("i",this).toggleClass("fa-angle-up");
        });
          
        //carrito compra
        modal({click:'#modal-carritocompra'});
        function selectcarritocompra(){
       
            $('#contenido-carritocompra').html('<div class="mx-alert-load"><img src="{{ url('/public/libraries/app/img/loading.gif') }}"></div>');  
            $.ajax({
                url:"{{ url('pagina/carritocompra') }}",
                type:'GET',
                success: function (respuesta){
                    $('#contenido-carritocompra').html(respuesta);
                }
            })
        }

        </script>
        <style>
        .mx-color-bg-footer {
            background: #0f78bd;
        }
        </style>
       
        @section('htmls')
        @show
        @section('htmls1')
        @show
        @section('htmls2')
        @show
      
        @section('scripts')
        @show
       
            @if(!Auth::user())
            <!--Login form -->
            <div class="main-register-wrap modal-iniciarsesion-master">
                <div class="main-overlay"></div>
                <div class="main-register-holder">
                    <div class="main-register fl-wrap">
                        <div class="close-reg"><i class="fa fa-times"></i></div>
                        <h3>BackOffice de <span>Kay<strong>llapi</strong></span></h3>
                        <div class="mx-modal-cuerpo">
                        <div class="tabs-container" id="tab-iniciarsesion-master">
                            <ul class="tabs-menu">
                                <li <?php echo ((isset($_GET['user']) || isset($_GET['login']))&&(Request::path()=='register'))? '':'class="current"' ?>><a href="#tab-master-1">Iniciar sesión</a></li>
                                <li <?php echo ((isset($_GET['user']) || isset($_GET['login']))&&(Request::path()=='register'))? 'class="current"':'' ?>><a href="#tab-master-2">Registrarse</a></li>
                            </ul>
                            <div class="tab">
                                <div id="tab-master-1" class="tab-content" <?php echo ((isset($_GET['user']) || isset($_GET['login']))&&(Request::path()=='register'))? 'style="display: none;"':'style="display: block;"' ?>>
                                    <div class="custom-form">
                                        <div id="mx-carga-1">
                                        <form action="javascript:;" 
                                            autocomplete="off"
                                            onsubmit="callback({
                                                route: 'login',
                                                method: 'POST',
                                                carga: '#mx-carga-1',
                                                data:{
                                                    idtienda: 0,
                                                    idtipousuario: 1
                                                }
                                            },
                                            function(resultado){
                                                if(resultado.resultado=='ERRORCONFIRMEMAIL'){
                                                    location.href = '{{ url('/email/verify') }}';
                                                }else{
                                                    location.href = '{{ url()->current() }}'; 
                                                    //location.href = '{{ url('/backoffice/inicio') }}';   
                                                }                                            
                                            },this)">
                                            <label>Usuario ó Correo Electrónico * </label>
                                            <input id="usuario" type="text">
                                            <label >Contraseña * </label>
                                            <input id="password" type="password">
                                            <button type="submit"  class="log-submit-btn"><span>Iniciar sesión</span></button>
                                            <!--div class="clearfix"></div>
                                            <div class="filter-tags">
                                                <input id="remember" type="checkbox">
                                                <label for="remember">Recuérdame</label>
                                            </div-->
                                        </form>
                                        <div class="lost_password">
                                            <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="tab-master-2" class="tab-content" <?php echo ((isset($_GET['user']) || isset($_GET['login']))&&(Request::path()=='register'))? 'style="display: block;"':'style="display: none;"' ?>>
                                    <div class="custom-form">
                                        <div id="mx-carga-2">
                                        <form action="javascript:;" 
                                            autocomplete="off"
                                            onsubmit="callback({
                                                route: 'register',
                                                method: 'POST',
                                                carga: '#mx-carga-2',
                                                data:{
                                                    idtienda: 0
                                                }
                                            },
                                            function(resultado){
                                                location.href = '{{ url('/email/verify') }}';      
                                            },this)">
                                            <?php
                                            $nompatrocinador = '';
                                            $idpatrocinador = 0;
                                            if(Request::path()=='register'){
                                                if(isset($_GET['user'])){
                                                    $patrocinador = DB::table('users')
                                                        ->where('usuario','<>','')
                                                        ->where('id','<>',1)
                                                        ->where('usuario',$_GET['user'])
                                                        ->first();
                                                    if($patrocinador!=''){
                                                        $idpatrocinador = $patrocinador->id;
                                                        $nompatrocinador = $patrocinador->nombre;
                                                    }
                                                }else{
                                                    $idpatrocinador = 1;
                                                }
                                            }
                                            ?>
                                            @if($nompatrocinador!='')
                                            <input id="idpatrocinador" value="{{ $idpatrocinador }}" type="hidden">
                                            @endif
                                            <label>Nombre * </label>
                                            <input id="nombre" type="text">
                                            <label>Apellidos *</label>
                                            <input id="apellidos" type="text">
                                            <label>Número de Teléfono *</label>
                                            <input id="numerotelefono" type="text">
                                            <label>Correo Electrónico (Usuario) *</label>
                                            <input id="email" type="text" value="" autocomplete="off">
                                            <label >Contraseña *</label>
                                            <input id="regis_password" type="password" value="" autocomplete="off">
                                            <label >Repetir Contraseña *</label>
                                            <input id="regis_password_confirmation" type="password" value="" autocomplete="off">
                                            <button type="submit" class="log-submit-btn"  ><span>Registrar</span></button>
                                        </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Login form end -->
            <script>
              modal({click:'#modal-iniciarsesion-master'});
              tab({click:'#tab-iniciarsesion-master'});
            </script>
            @endif
    </body>
</html>
