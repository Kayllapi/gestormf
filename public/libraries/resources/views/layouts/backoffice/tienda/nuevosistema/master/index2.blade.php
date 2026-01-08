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
    <body url="">
        <div class="loader-wrap">
            <div class="pin"></div>
            <div class="pulse"></div>
        </div>

      
<div class="profile-edit-page-header">
  <div class="container">
    <a href="{{ $tienda->idestadoprivacidad==1?url($tienda->link):'javascript:;' }}"><h2>{{ $tienda->nombre }}</h2></a>
    @if(Auth::user()->idtienda!=0)
        <?php $caja = caja($tienda->id,Auth::user()->id); ?>
        @if($caja['resultado']=='PROCESO')
          <a href="javascript:;" class="mx-alert-caja inactivo"><i class="fa fa-sync-alt"></i> Apertura en Proceso</span></a>
        @elseif($caja['resultado']=='PENDIENTE')
          <a href="javascript:;" class="mx-alert-caja inactivo"><i class="fa fa-sync-alt"></i> Apertura Pendiente</span></a> 
        @elseif($caja['resultado']=='ABIERTO')
           <a href="javascript:;" class="mx-alert-caja activo"><i class="fa fa-tags"></i> Caja Activa ({{$moneda_soles->simbolo}} {{ efectivo($tienda->id,$caja['apertura']->id)['total'] }} - {{$moneda_dolares->simbolo}} {{ efectivo($tienda->id,$caja['apertura']->id,2)['total'] }})</a>
        @else
          <a href="javascript:;" class="mx-alert-caja inactivo"><i class="fa fa-tags"></i> Caja Inactiva</a>
        @endif 
    @endif 
    <nav class="menu-sistema">
      <div>
        <i class="fa fa-bars"></i>
      </div>
      <ul>


        @if(Auth::user()->idtienda!=0)
        <?php $usuario = DB::table('users')
                ->join('role_user','role_user.user_id','users.id')
                ->join('roles','roles.id','role_user.role_id')
                ->where('users.id',Auth::user()->id)
                ->select('users.*','roles.description as permiso')
                ->limit(1)
                ->first(); ?>
        <li><a href="javascript:;" style="background-color: #31353d;padding-top: 10px;padding-bottom: 10px;">
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
</div>
      
    <div class="profile-edit-container">
        <div class="custom-form">
        <section class="scroll-con-sec hero-section" data-scrollax-parent="true" id="sec1">
            <div class="container">
            <div style="text-align: center;padding: 10px;font-size: 14px;margin-bottom: 20px;" id="mx-cont-modulo-titulo">
                <a href="javascript:;" class="mx-modulo-titulo-desactivado">Inicio</a>
            </div>
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
                            <a href="javascript:;" onclick="ir_perfil()" id="modal-master" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble modulo_perfil"  style="display:none;">
                              <img data-v-b789b216="" src="{{url('public/backoffice/sistema/modulosistema/editar_usuario.png')}}" class="category-bubble-icon">
                              <h2 data-v-b789b216="" class="category-bubble-title">Editar Perfil</h2>
                            </a>
                            <a href="javascript:;" onclick="ir_cambiarclave()" id="modal-master" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble modulo_perfil"  style="display:none;">
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
                            <a href="javascript:;" onclick="ir_submodulo({{ $value->id }},'{{ $value->nombre }}')" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-modulo">
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
                                    <?php $ii++ ?>
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
        <script src="https://cdn.datatables.net/keytable/2.6.4/js/dataTables.keyTable.min.js"></script>

        <script src="{{ url('public/layouts/js/menuhorizontal.js') }}"></script>

       
        @section('sistema_htmls')
        @show
        @section('sistema_scripts')
        @show

        @include('app.nuevosistema.modal',[
            'name'=>'modal-master',
            'screen'=>'fullscreen'
        ])

      
<style>
  body{
    font-weight:bold;
  }
  .mx-modulo-titulo-activado {
    color: #fff;
    font-size: 14px;
    font-weight: bold;
    background-color: #008cea;
    padding: 10px;
    border-radius: 20px;
    padding-left: 20px;
    padding-right: 20px;
  }
  .mx-modulo-titulo-desactivado {
    color: #fff;
    font-size: 14px;
    font-weight: bold;
    background-color: #31353c;
    padding: 10px;
    border-radius: 20px;
    padding-left: 20px;
    padding-right: 20px;
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
      background: #31353c;
      margin-bottom: 0px;
  }
  .menu-sistema div {
    background: #31353c;
}
  /* Caja */
 
      .mx-alert-caja {
        float: left;
        color: #fff;
        margin-left: 20px;
        padding-left: 10px;
        padding-right: 10px;
        padding-top: 2px;
        padding-bottom: 2px;
        border-radius: 10px;
        margin-top: 15px;
        font-size: 11px;
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

            
            function ir_submodulo(idmodulo,titulo){
                $('#mx-cont-modulo-titulo').html('<a href="javascript:;" class="mx-modulo-titulo-activado">Inicio</a> <a href="javascript:;" class="mx-modulo-titulo-desactivado">'+titulo+'</a>');
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
  
            function pagina_index(nombremodulo,imagenmodulo,idmodulo,nombresubmodulo,imagensubmodulo,idsubmodulo,name_modulo,titulo='',view=''){
                
                $('#mx-cont-modulo-titulo').html('<a href="javascript:;" class="mx-modulo-titulo-activado">Inicio</a> <a href="javascript:;" class="mx-modulo-titulo-activado">'+nombremodulo+'</a> <a href="javascript:;" class="mx-modulo-titulo-desactivado">'+nombresubmodulo+'</a>');
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



                    

