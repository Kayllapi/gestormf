
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

        <!--=============== otros ===============-->
        <link rel="stylesheet" href="{{ url('public/libraries/app/css/carga.css') }}">
        <link rel="stylesheet" href="{{ url('public/libraries/select2/select2.css') }}">
    
        <link rel="stylesheet" href="https://unpkg.com/css-pro-layout/dist/css/css-pro-layout.min.css"/>
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/remixicon@2.2.0/fonts/remixicon.css'>
        <link rel="stylesheet" href="{{ url('public/libraries/css-pro-layout/style.css') }}">
        <link rel="stylesheet" href="{{ url('public/layouts/css/style.css') }}">
    </head>
    <body url="">
        <div class="loader-wrap">
            <div class="pin"></div>
            <div class="pulse"></div>
        </div>

      

<!-- partial:index.partial.html -->
<div class="layout has-sidebar fixed-sidebar fixed-header">
  <aside id="sidebar" class="sidebar break-point-lg has-bg-image">
    <div class="image-wrapper">
      <img src="https://user-images.githubusercontent.com/25878302/144499035-2911184c-76d3-4611-86e7-bc4e8ff84ff5.jpg" alt="sidebar background" />
    </div>
    <div class="sidebar-layout">
      <div class="sidebar-header">
        <span style="
                text-transform: uppercase;
                font-size: 15px;
                letter-spacing: 3px;
                font-weight: bold;
              ">{{ $tienda->nombre }}</span>
        
                  
      </div>
      <div class="sidebar-content">

       
                       

        <nav class="menu open-current-submenu">
          <ul>
            <li class="menu-item sub-menu">
              <a href="#" style="background-color: #23272e;margin-bottom: 10px;height: 60px;">
                <img src="{{$urlimagenusuario}}" height="40px" style="margin-right: 15px; border-radius:100px;">
                <span class="menu-title">
                     {{ $usuario->nombre }} <br>({{ $usuario->permiso }})
                </span>
              </a>
              <div class="sub-menu-list">
                <ul>
                  <li class="menu-item">
                    <a href="#">
                      <span class="menu-title">Editar Perfil</span>
                    </a>
                  </li>
                  <li class="menu-item">
                    <a href="#">
                      <span class="menu-title">Cambiar Contraseña</span>
                    </a>
                  </li>
                  <li class="menu-item">
                    <a href="#">
                      <span class="menu-title">Cerrar Sesión</span>
                    </a>
                  </li>
                </ul>
              </div>
            </li>
            
            @if(Auth::user()->idtienda!=0)
                <?php $caja = caja($tienda->id,Auth::user()->id); ?>
                @if($caja['resultado']=='PROCESO')
                  <a href="javascript:;" class="mx-alert-caja inactivo"><i class="fa fa-sync-alt"></i> Apertura en Proceso</span></a>
                @elseif($caja['resultado']=='PENDIENTE')
                  <a href="javascript:;" class="mx-alert-caja inactivo"><i class="fa fa-sync-alt"></i> Apertura Pendiente</span></a> 
                @elseif($caja['resultado']=='ABIERTO')
                    <li class="menu-item">
                      <a href="#" style="background-color: #08ab0f;color: #fff;text-align: center;height: 40px;margin-bottom: 10px;">
                        <span class="menu-title">
                             ({{$moneda_soles->simbolo}} {{ efectivo($tienda->id,$caja['apertura']->id)['total'] }} - {{$moneda_dolares->simbolo}} {{ efectivo($tienda->id,$caja['apertura']->id,2)['total'] }})
                        </span>
                      </a>
                    </li>
                @else
                    <a href="javascript:;" class="mx-alert-caja inactivo"><i class="fa fa-tags"></i> Caja Inactiva</a>
                @endif 
            @endif
            
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
            <li class="menu-item">
              <a href="javascript:;" onclick="ir_submodulo({{ $value->id }})">
                <img src="{{$urlimagen}}" class="category-bubble-icon" height="20px" style="margin-right: 15px;">
                <span class="menu-title"> {{ $value->nombre }}</span>
              </a>
            </li>
            @endforeach
          </ul>
        </nav>
      </div>
      <div class="sidebar-footer"><span><a href="{{url('/')}}"><img src="{{ url('public/backoffice/sistema/kayllapi-logo2.png') }}" alt="Kayllapi"></a></span></div>
    </div>
  </aside>
  <div id="overlay" class="overlay"></div>
  <div class="layout">
    <header class="header">
      <a id="btn-collapse" href="#">
        <i class="ri-menu-line ri-xl"></i>
      </a>
      <a id="btn-toggle" href="#" class="sidebar-toggler break-point-lg">
        <i class="ri-menu-line ri-xl"></i>
      </a>
      
        
    </header>
    <main class="content">
      <div class="profile-edit-container">
        <div class="custom-form">
        <section class="scroll-con-sec hero-section" data-scrollax-parent="true" id="sec1">
           
            <div class="bg"  data-bg="{{ url('public/backoffice/sistema/banner-1.png') }}" data-scrollax="properties: { translateY: '200px' }"></div>
            <div class="overlay"></div>
            <div class="hero-section-wrap fl-wrap">
                    <div class="container">
                        <div data-v-04cc2f02="" class="landing__categories">
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
                                    <?php 
                                    $rutasubimagen = getcwd().'/public/backoffice/sistema/modulo/'.$subvalue->imagen; 
                                    if(file_exists($rutasubimagen) AND $subvalue->imagen!=''){
                                        $urlsubimagen = url('public/backoffice/sistema/modulo/'.$subvalue->imagen);
                                    }else{
                                        $urlsubimagen = url('public/backoffice/sistema/sin_imagen_redondo.png');
                                    }
                                    ?>
                                    <?php $href_nombre = explode('sistema/{idtienda}/', $subvalue->vista); ?>
                                    <a href="javascript:;" onclick="pagina_index(
                                                                      '{{$value->nombre}}',
                                                                      '{{$urlimagen}}',
                                                                      {{ $value->id }},
                                                                      '{{$subvalue->nombre}}',
                                                                      '{{$urlsubimagen}}',
                                                                      {{ $subvalue->id }},
                                                                      '{{$href_nombre[1]}}')" 
                                       id="cont-submodulo{{ $value->id }}" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-submodulo" style="display:none;">
                                      <img data-v-b789b216="" src="{{$urlsubimagen}}" class="category-bubble-icon">
                                      <h2 data-v-b789b216="" class="category-bubble-title">{{ $subvalue->nombre }}</h2>
                                    </a>
                                @endforeach
                        @endforeach
                        </div>
                        <div data-v-04cc2f02="" class="landing__categories" id="cont-cuerposistema" style="border-radius: 5px;">
                        </div>
                    </div>
            </div>
        </section>
      </div>
      </div>
  
      <footer class="footer">
        <small style="margin-bottom: 20px; display: inline-block">
          © Kayllapi 2022. Todos los derechos reservados.
        </small>
      </footer>
    </main>
    <div class="overlay"></div>
  </div>
</div>
<!-- partial -->


      
    

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
        <script src="https://cdn.datatables.net/keytable/2.6.4/js/dataTables.keyTable.min.js"></script>

        
        <script src='https://unpkg.com/@popperjs/core@2'></script>
        <script  src="{{ url('public/libraries/css-pro-layout/script.js') }}"></script>

        @include('app.nuevosistema.modal',[
            'name'=>'modal-master',
            'screen'=>'fullscreen'
        ])

      
<style>
  body{
    font-weight:bold;
  }
  /* FORM */
  .main-register h3 {
      font-size: 16px;
  }
  .mx-btn-post{
      background: {{$tienda->ecommerce_color}};
      color:#fff;
      width:100%;
      font-weight: bold;
  }
  .mx-btn-post:hover{
      background: #31353d;
  }
  
  p,
  .profile-edit-container .custom-form label,
  textarea, 
  input[type="date"], 
  input[type="time"], 
  input[type="text"], 
  input[type="number"], 
  input[type="email"], 
  input[type="password"], 
  input[type="button"] {
      font-weight: bold;
  }
  /* Menu */
  .profile-edit-page-header {
      z-index: 10;
      background: none;
  }
  /* Caja */
  .mx-alert-caja {    
    color: #fff;
    margin-left: 5px;
    padding: 10px;
    border-radius: 5px;
    margin-top: 120px;
    font-size: 13px;
    margin-right: 5px;
    z-index: 10;
    font-weight: bold;
    white-space: nowrap;
  }
  .activo {
      background-color: #08ab0f;
  }
  .inactivo {
      background-color: #31353d;
  }
  .cont-alert {
    width: 100%;
    margin-bottom: 20px;
    margin-top: 60px;
  }
  .mx-alert-user {
    top: 0px;
  }
  /* Modal */
  .mx-modal-fullscreen {
      max-width : 1200px;
  }
  .mx-modal-normal {
      max-width : 480px;
  }
  .mx-modal-alert {
      max-width : 350px;
  }
  .main-register h3 {
      color: #ffffff;
      border-bottom: 1px solid {{$tienda->ecommerce_color}};
      background-color: {{$tienda->ecommerce_color}};
      border-radius: 5px 5px 0px 0px;
  }
  .close-reg {
      background: #31353d;
  }
  /* General */
  #mx-cuerposistema {
      background-color: #fff;
      padding: 5px;
      border-radius: 5px;
  }
  .table-responsive {
  background-color: #fff;
      border-radius: 5px;
      padding: 5px;
  }
  .table td {
      padding:5px;
      white-space: nowrap;
      cursor: pointer;
  }
  
  table.dataTable tbody tr.odd {
    background-color: #e8e8e8;
  }
  table.dataTable tbody tr:hover {
      background-color: {{$tienda->ecommerce_color}}50;
  }
  table.dataTable tbody tr.selected {
      background-color: {{$tienda->ecommerce_color}};
      color: #fff;
  }

  .dts_label{
    display:none;
  }
  .dataTables_scrollFootInner .dataTable {
      margin: 0 !important;
  }
  mark {
      padding: 0;
      background: #f1c40f;
  }
  div.dataTables_wrapper div.dataTables_processing {
      display: none !important;
  }
  .mx-header-search {
    width: 400px;
    margin: auto;
  }
  .mx-header-search-input-item {
    width: 347px;
  }
  /* Cuadros */
  section.hero-section {
      padding: 30px 0 100px;
  }
  .landing__categories[data-v-04cc2f02] {
      justify-content: center;
      flex-direction: row;
      display: flex;
      flex-wrap: wrap;
      margin: 0 auto;
  }

  .category-bubble[data-v-b789b216] {
      height: 90px;
      width: 120px;
      background-color: #fff;
      border-radius: 5px;
      cursor: pointer;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
      box-shadow: 0 2px 17px 0 #000000;
      text-align: center;
      transition: .3s;
      text-decoration: none;
      color: #000;
      font-weight: 300;
      margin: 8px;
      margin-bottom: 18px;
  }
  .category-bubble[data-v-b789b216]:hover {
      height: 100px; 
      width: 127px; 
      margin-left: 4.5px;
      margin-right: 4.5px;
      margin-top: -3px;
  }
  .category-bubble .category-bubble-icon[data-v-b789b216] {
      margin-top: 13px; 
      height: 40px;
  }
  .category-bubble .category-bubble-icon[data-v-b789b216]:hover {
      margin-top: 13px;
      height: 40px;
  }
  .category-bubble .category-bubble-title[data-v-b789b216] {
      line-height: 16px;
      margin-top: 3px;
      font-weight: 300;
      margin-left: auto;
      margin-right: auto;
      overflow: hidden;
      font-size: 14px;
      font-weight:bold;
      padding-left: 5px;
      padding-right: 5px;
  }
  .category-bubble:hover .category-bubble-icon[data-v-b789b216] {
      margin-top: 15px;
      height: 50px;
  }
  
@media only screen and (max-width: 400px){
  .cont-alert {
    margin-bottom: 40px;
  }
  .mx-alert-user {
    top: 23px;
  }
  .mx-header-search {
    width: 300px;
  }
  .mx-header-search-input-item {
    width: 247px;
  }
}
</style>
<script>

            
            function ir_submodulo(idmodulo){
                $('a#cont-submodulo'+idmodulo).css('display','block');
                $('.cont-modulo').css('display','none');
                $('#cont-cuerposistema').html('');
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
                pagina({route:'{{url('backoffice/tienda/nuevosistema/'.$tienda->id) }}/inicio/1/edit'+
                        '?view=editperfil '+
                        '&name_modulo=inicio'+
                        '&url_sistema={{url('backoffice/tienda/nuevosistema') }}',
                        result:'#modal_cuerpo_modal-master'});
                $('#modal_titulo_modal-master').html('Editar Perfil');
            }
            function ir_cambiarclave(){
                $('.modulo_perfil').css('display','block');
                $('.cont-modulo').css('display','none');
                $('.cont-submodulo').css('display','none');
                pagina({route:'{{url('backoffice/tienda/nuevosistema/'.$tienda->id) }}/inicio/1/edit'+
                        '?view=editcambiarclave'+
                        '&name_modulo=inicio'+
                        '&url_sistema={{url('backoffice/tienda/nuevosistema') }}',
                        result:'#modal_cuerpo_modal-master'});
                $('#modal_titulo_modal-master').html('Cambiar Contraseña');
            }
  
            function pagina_index(nombremodulo,imagenmodulo,idmodulo,nombresubmodulo,imagensubmodulo,idsubmodulo,name_modulo,view=''){
                pagina({route:'{{url('backoffice/tienda/nuevosistema/'.$tienda->id) }}/'+name_modulo+
                        '?view='+view+
                        '&nombre_modulo='+nombremodulo+
                        '&imagen_modulo='+imagenmodulo+
                        '&idmodulo='+idmodulo+
                        '&nombre_submodulo='+nombresubmodulo+
                        '&imagen_submodulo='+imagensubmodulo+
                        '&idsubmodulo='+idsubmodulo+
                        '&name_modulo='+name_modulo+
                        '&url_sistema={{url('backoffice/tienda/nuevosistema') }}',
                        result:'#cont-cuerposistema'});
                $('.cont-submodulo').css('display','none');
            }
  
            /*function pagina_edit(nombremodulo,imagenmodulo,idmodulo,nombresubmodulo,imagensubmodulo,idsubmodulo,name_modulo,view,id){
                pagina({route:'{{url('backoffice/tienda/nuevosistema/'.$tienda->id) }}/'+name_modulo+'/'+id+'/edit'+
                        '?view='+view+
                        '&nombre_modulo='+nombremodulo+
                        '&imagen_modulo='+imagenmodulo+
                        '&idmodulo='+idmodulo+
                        '&nombre_submodulo='+nombresubmodulo+
                        '&imagen_submodulo='+imagensubmodulo+
                        '&idsubmodulo='+idsubmodulo+
                        '&name_modulo='+name_modulo+
                        '&url_sistema={{url('backoffice/tienda/nuevosistema') }}',
                        result:'#cont-cuerposistema'});
            }*/
          
            
            function modulo_create(nombremodulo,imagenmodulo,idmodulo,nombresubmodulo,imagensubmodulo,idsubmodulo,name_modulo,titulo,view){
                
                $('#modal_titulo_modal-registrar-submodulo').html(titulo);
                pagina({route:'{{url('backoffice/tienda/nuevosistema/'.$tienda->id) }}/'+name_modulo+'/create'+
                        '?view='+view+
                        '&nombre_modulo='+nombremodulo+
                        '&imagen_modulo='+imagenmodulo+
                        '&idmodulo='+idmodulo+
                        '&nombre_submodulo='+nombresubmodulo+
                        '&imagen_submodulo='+imagensubmodulo+
                        '&idsubmodulo='+idsubmodulo+
                        '&name_modulo='+name_modulo+
                        '&url_sistema={{url('backoffice/tienda/nuevosistema') }}',
                        result:'#modal_cuerpo_modal-registrar-submodulo'});
            }
  
            function modulo_edit(nombremodulo,imagenmodulo,idmodulo,nombresubmodulo,imagensubmodulo,idsubmodulo,name_modulo,titulo,view){
      
                $('#modal_titulo_modal-editar-submodulo').html(titulo);
                var idmodulotabla = $('#idmodulotabla').val();
                if(idmodulotabla!=''){
                    $('.modal-editar-submodulo .main-register-holder').removeClass('mx-modal-alert').addClass('mx-modal-fullscreen');
                    pagina({route:'{{url('backoffice/tienda/nuevosistema/'.$tienda->id) }}/'+name_modulo+'/'+idmodulotabla+'/edit'+
                        '?view='+view+
                        '&nombre_modulo='+nombremodulo+
                        '&imagen_modulo='+imagenmodulo+
                        '&idmodulo='+idmodulo+
                        '&nombre_submodulo='+nombresubmodulo+
                        '&imagen_submodulo='+imagensubmodulo+
                        '&idsubmodulo='+idsubmodulo+
                        '&name_modulo='+name_modulo+
                        '&url_sistema={{url('backoffice/tienda/nuevosistema') }}',
                        result:'#modal_cuerpo_modal-editar-submodulo'});
                }else{
                    $('.modal-editar-submodulo .main-register-holder').removeClass('mx-modal-fullscreen').addClass('mx-modal-alert');
                    $('#modal_cuerpo_modal-editar-submodulo').html('<div class="mensaje-warning"><i class="fa fa-warning"></i> Debe seleccionar un Dato!!</div>');
                }
            }
  
            function modulo_delete(nombremodulo,imagenmodulo,idmodulo,nombresubmodulo,imagensubmodulo,idsubmodulo,name_modulo,titulo,view){
      
                $('#modal_titulo_modal-eliminar-submodulo').html(titulo);
                var idmodulotabla = $('#idmodulotabla').val();
                if(idmodulotabla!=''){
                    $('.modal-eliminar-submodulo .main-register-holder').removeClass('mx-modal-alert').addClass('mx-modal-fullscreen');
                    pagina({route:'{{url('backoffice/tienda/nuevosistema/'.$tienda->id) }}/'+name_modulo+'/'+idmodulotabla+'/edit'+
                        '?view='+view+
                        '&nombre_modulo='+nombremodulo+
                        '&imagen_modulo='+imagenmodulo+
                        '&idmodulo='+idmodulo+
                        '&nombre_submodulo='+nombresubmodulo+
                        '&imagen_submodulo='+imagensubmodulo+
                        '&idsubmodulo='+idsubmodulo+
                        '&name_modulo='+name_modulo+
                        '&url_sistema={{url('backoffice/tienda/nuevosistema') }}',
                        result:'#modal_cuerpo_modal-eliminar-submodulo'});
                }else{
                    $('.modal-eliminar-submodulo .main-register-holder').removeClass('mx-modal-fullscreen').addClass('mx-modal-alert');
                    $('#modal_cuerpo_modal-eliminar-submodulo').html('<div class="mensaje-warning"><i class="fa fa-warning"></i> Debe seleccionar un Dato!!</div>');
                }
            }
  
            function modulo_other(nombremodulo,imagenmodulo,idmodulo,nombresubmodulo,imagensubmodulo,idsubmodulo,name_modulo,titulo,view){
                $('#modal_titulo_modal-otro-submodulo').html(titulo);
                var idmodulotabla = $('#idmodulotabla').val();
                if(idmodulotabla!=''){
                    $('.modal-otro-submodulo .main-register-holder').removeClass('mx-modal-alert').addClass('mx-modal-fullscreen');
                    pagina({route:'{{url('backoffice/tienda/nuevosistema/'.$tienda->id) }}/'+name_modulo+'/'+idmodulotabla+'/edit'+
                        '?view='+view+
                        '&nombre_modulo='+nombremodulo+
                        '&imagen_modulo='+imagenmodulo+
                        '&idmodulo='+idmodulo+
                        '&nombre_submodulo='+nombresubmodulo+
                        '&imagen_submodulo='+imagensubmodulo+
                        '&idsubmodulo='+idsubmodulo+
                        '&name_modulo='+name_modulo+
                        '&url_sistema={{url('backoffice/tienda/nuevosistema') }}',
                        result:'#modal_cuerpo_modal-otro-submodulo'});
                }else{
                    $('.modal-otro-submodulo .main-register-holder').removeClass('mx-modal-fullscreen').addClass('mx-modal-alert');
                    $('#modal_cuerpo_modal-otro-submodulo').html('<div class="mensaje-warning"><i class="fa fa-warning"></i> Debe seleccionar un Dato!!</div>');
                }
            }
  
            function modulo_search(nombremodulo,imagenmodulo,idmodulo,nombresubmodulo,imagensubmodulo,idsubmodulo,name_modulo,titulo,view){
                $('#modal_titulo_modal-buscador-submodulo').html(titulo);
                var idmodulotabla = $('#idmodulotabla').val();
                if(idmodulotabla!=''){
                    $('.modal-buscador-submodulo .main-register-holder').removeClass('mx-modal-alert').addClass('mx-modal-fullscreen');
                    pagina({route:'{{url('backoffice/tienda/nuevosistema/'.$tienda->id) }}/'+name_modulo+'/'+idmodulotabla+'/edit'+
                        '?view='+view+
                        '&nombre_modulo='+nombremodulo+
                        '&imagen_modulo='+imagenmodulo+
                        '&idmodulo='+idmodulo+
                        '&nombre_submodulo='+nombresubmodulo+
                        '&imagen_submodulo='+imagensubmodulo+
                        '&idsubmodulo='+idsubmodulo+
                        '&name_modulo='+name_modulo+
                        '&url_sistema={{url('backoffice/tienda/nuevosistema') }}',
                        result:'#modal_cuerpo_modal-buscador-submodulo'});
                }else{
                    $('.modal-buscador-submodulo .main-register-holder').removeClass('mx-modal-fullscreen').addClass('mx-modal-alert');
                    $('#modal_cuerpo_modal-buscador-submodulo').html('<div class="mensaje-warning"><i class="fa fa-warning"></i> Debe seleccionar un Dato!!</div>');
                }
            }
  
            function modulo_actualizar(name_modulo){
                $('#modal_cuerpo_modal-registrar-submodulo').html('');
                $('#modal_cuerpo_modal-editar-submodulo').html('');
                $('#modal_cuerpo_modal-eliminar-submodulo').html('');
                $('#modal_cuerpo_modal-otro-submodulo').html('');
                $('.modal-registrar-submodulo').css('display','none'); 
                $('.modal-editar-submodulo').css('display','none');  
                $('.modal-eliminar-submodulo').css('display','none'); 
                $('.modal-otro-submodulo').css('display','none'); 
                $('#idmodulotabla').val('');  
                $.ajax({
                    url: '{{url('backoffice/tienda/nuevosistema/'.$tienda->id) }}/'+name_modulo+'/show-moduloactualizar?name_modulo='+name_modulo,
                    type:"GET",
                    beforeSend: function (data) {
                        //
                    },
                    success:function(respuesta){   
                        $('#tabla-'+name_modulo).DataTable().ajax.reload();
                    }
                });
            }

        </script>
    </body>
</html>



                    

