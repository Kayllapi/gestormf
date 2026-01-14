<?php
$tienda = DB::table('tienda')->whereId(194)->first();
?>
<!DOCTYPE HTML>
<html lang="es">
    <head>
        <meta charset="utf-8">
	      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    
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

        <title>{{ config('app.name', 'Kayllapi') }}</title>
        <link rel="shortcut icon" href="{{ url('/public/backoffice/sistema/logo_gestormf_icono1.png') }}">
        <meta name="description" content="Somos una plataforma de búsqueda, con la finalidad de ayudar a todo los usuarios a encontrar el producto y/o servicio adecuado que tu negocio brinda, así mismo buscamos ser la mejor plataforma de búsqueda de negocios online"/>
        <meta name="twitter:card" value="summary">
        <meta property="og:title" content="Kayllapi" />
        <meta property="og:type" content="article" />
        <meta property="og:url" content="{{ Request::url() }}" />
        <meta property="og:image" content="http://kayllapi.com/public/backoffice/sistema/banner.png" />
        <meta property="og:description" content="Somos una plataforma de búsqueda, con la finalidad de ayudar a todo los usuarios a encontrar el producto y/o servicio adecuado que tu negocio brinda, así mismo buscamos ser la mejor plataforma de búsqueda de negocios online" />
     
      <style>
        .select2-container,
      .cs-wrapper .subcribe-form #subscribe select {
          margin-bottom: 20px;
          max-width: 380px;
      }      .select2-container .select2-selection--single {

          height: 48px;
        box-shadow: 0px 0px 0px 7px rgb(255 255 255 / 20%);
          border-radius:30px;
      }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
          text-align: center;
          line-height: 46px;
      }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
          height: 32px;
          right: 10px;
      }
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
          color: #31353d;
      }
        .select2-container--open .select2-dropdown--below {
          box-shadow: 0px 0px 0px 7px rgb(255 255 255 / 20%);
      }
      </style>
      <style>
          .mx-logo-login {
              background-repeat: no-repeat;
              background-size: contain;
              background-position: center;
              height: 65px;
              margin: auto;
                background-image: url({{url('public/backoffice/sistema/logo_gestormf.png')}});
                margin-top: 20px;
    margin-bottom: 15px;
          }
          .bg {
            -webkit-background-size: contain;
          }
          <?php
          $urlimagen_escritorio = url('public/backoffice/nuevosistema/sistema/login_fondo_escritorio.png');
          $urlimagen_movil = url('public/backoffice/nuevosistema/sistema/login_fondo_movil.png');
          if($tienda!=''){
          if(configuracion($tienda->id,'sistema_imagenfondologin')['resultado']=='CORRECTO'){
              $rutaimagen = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/imagenlogin/'.configuracion($tienda->id,'sistema_imagenfondologin')['valor']; 
              if(file_exists($rutaimagen)){
                  $urlimagen_escritorio = url('public/backoffice/tienda/'.$tienda->id.'/imagenlogin/'.configuracion($tienda->id,'sistema_imagenfondologin')['valor']);
                  $urlimagen_movil = url('public/backoffice/tienda/'.$tienda->id.'/imagenlogin/'.configuracion($tienda->id,'sistema_imagenfondologin')['valor']);
              }
          }  
          }
          ?>
          .imagen-login {
              background-image: url(<?php echo $urlimagen_escritorio ?>)
          }
          @media only screen and (max-width: 500px){
              .imagen-login {
                  background-image: url(<?php echo $urlimagen_movil ?>)
              } 
          }
          footer.main-footer {
              position: fixed;
              z-index: 5 !important;
          }
        .sub-footer{
          padding:15px 0 !important;
        }
          .logo {
              max-width:300px;
              max-height:100px;
          }
          .error-input {
            display: none;
          }
          .counter-widget {
              padding: 0px 10px;
          }
          .cs-wrapper {
              padding: 80px 0px 100px;
              padding-top:8%;
          }
          .soon-title {
              font-size: 20px;
              color: #000;
          }
          .cs-wrapper .countdown {
              margin: 10px 0;
          }
          .cs-wrapper .subcribe-form #subscribe input {
              margin-bottom: 20px;
              max-width: 380px;
          }
              .subscribe-button {
              height: 43px;
              padding: 0 30px;
              border-radius: 30px;
              border: none;
              right: 6px;
              top: 6px;
              color: #fff;
              cursor: pointer;
          }
          @media only screen and (max-width: 540px){
            .cs-countdown-item {
                width: 25% !important;
            }

          }
          @media only screen and (max-width: 768px){
            .cs-countdown-item span {
                font-size: 34px;
            }

            .cs-countdown-item p {
                font-size: 13px;
            }
            .cs-wrapper {
                padding: 100px 0px 100px;
            }
          }
          .btn-primary,
          .btn-primary:hover,
          .btn-primary:active,
          .btn-primary:focus {    
              background-color: #d4d4d4 !important;
              border: 1px solid #919191 !important;
              color: #000 !important;
          }
          .section-separator:before {
                background: #253c7b;
          }
      </style>  
    </head>
    <body url="{{ url('/') }}"> 
      @if(Auth::user())
      <script>
      location.href = '{{ url('backoffice/'.Auth::user()->idtienda.'/inicio') }}'; 
      </script>
      @else  
          <div class="fixed-bg">
              <div class="bg imagen-login"></div>
              <div class="overlay"></div>
              <div class="bubble-bg"></div>
          </div>
      <div style="height: 82.7vh;">
          <div class="cs-wrapper fl-wrap">
            <div style="max-width: 300px;margin: auto;">
              <div id="mx-carga-usuario-1">
                    <div style="background-color: #f8faf0;
                            border-radius: 10px;
                            padding: 5px;
                            padding-top: 15px;">
                  <div class="container small-container counter-widget">
                      @if($tienda->imagen!='')
                      <div class="cs-logo">
                        <div class="mx-logo-login">
                        </div>
                      </div>
                      @endif
                      <h3 class="soon-title">GESTOR MF</h3>  <span class="section-separator"></span>             
                      <!-- countdown -->
                      <div id="countdown"></div>
                      <form action="javascript:;" 
                             onsubmit="callback({
                                        route: 'login',
                                        method: 'POST',
                                        carga: '#mx-carga-usuario-1',
                                        data:{
                                            idtienda: '{{$tienda->id}}',
                                            idtipousuario: 2
                                        }
                                    },
                                    function(resultado){
                                        location.href = '{{ url('/backoffice/'.$tienda->id.'/inicio') }}'; 
                                    },this)">
                      <div class="subcribe-form fl-wrap">
                          <div style="font-weight: bold;
                                    font-size: 15px;
                                    margin-top: 2px;
                                    margin-bottom: 10px;">Iniciar Sesión</div> 
                          <div id="subscribe">
                              <input id="usuario" placeholder="Usuario" type="text" style="text-align: center;border: 1px solid #808d9f">
                          </div>
                          <div id="subscribe">
                              <input id="password" placeholder="Contraseña" type="password" style="text-align: center;border: 1px solid #808d9f">
                          </div>
                          <div id="subscribe" style="margin-bottom: 10px;">
                              <button type="submit" class="subscribe-button btn-primary" style="position: inherit;">
                                Ingresar</button>
                          </div>
                      </div>
                      </form>
                  </div>
                  </div>
                  <!-- container end -->
              </div>
            </div>
          </div>
          <!-- cs-wrapper end-->
      </div>
       @endif
  
        <!--=============== scripts  ===============-->     
        <script src="{{ url('public/layouts/js/jquery.min.js') }}"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="{{ url('public/layouts/js/plugins.js') }}"></script>
        <script src="{{ url('public/libraries/app/js/app.js') }}"></script>
        <script src="{{ url('public/libraries/select2/select2.js') }}"></script>
        <script>
        $("#idtienda").select2({    
            ajax: {
                url:"{{url('movil/login/showlistartiendas')}}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                          buscar: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            placeholder: "Seleccionar Tienda",
            minimumInputLength: 2,
            allowClear: true
        }); 
        </script>
    </body>
</html>