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
            <li><a href="javascript:;" onclick="ir_modulo()"><i class="fa fa-home"></i> Inicio</a></li>
            <li><a href="javascript:;" onclick="ir_perfil()" id="modal-master"><i class="fa fa-edit"></i> Editar Perfil</a></li>
            <li><a href="javascript:;" onclick="ir_cambiarclave()" id="modal-master"><i class="fa fa-unlock-alt"></i> Cambiar Contraseña</a></li>
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
  .img-tabla {
      
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center;
      height:66px;
      width:80px;
      border: 1px solid #ccc;
  }
  .option2 {
      white-space: nowrap;
      width: 168px;
  }
  .option3 {
      white-space: nowrap;
      width: 252px;
  }
  .option4 {
      white-space: nowrap;
      width: 336px;
  }
  .option5 {
      white-space: nowrap;
      width: 420px;
  }
  .cont-td-tabla {
      white-space: nowrap;
      width: 10px;
  }
  .btn-tabla {
      height: 60px;
      width:80px;
      border-radius: 5px;
      background-color: white;
      padding: 5px;
      text-align: center;    
      color: #383c40;
      float:left;    
      border: 1px solid #bebebe;
      margin: 2px;
  }
  .btn-tabla-cabecera:hover,
  .btn-tabla:hover {
      background-color: #e1e1e1;
  }
  .btn-tabla-cabecera {
      height: 50px;
      border-radius: 5px !important;
      background-color: white;
      padding: 5px !important;
      text-align: center;    
      color: #383c40 !important;
      width: 80px;
      float: left !important;
      margin: 5px !important;
  }
  .btn-tabla-edit {
      background-image: url({{url('public/backoffice/sistema/modulosistema/editar.png')}});
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center;
      height: 34px;
      border-radius: 5px; 
  }
  .btn-tabla-detail {
      background-image: url({{url('public/backoffice/sistema/modulosistema/detalle.png')}});
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center;
      height: 34px;
      border-radius: 5px; 
  }
  .btn-tabla-delete {
      background-image: url({{url('public/backoffice/sistema/modulosistema/eliminar.png')}});
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center;
      height: 34px;
      border-radius: 5px; 
  }
  .btn-tabla-atras {
      background-image: url({{url('public/backoffice/sistema/modulosistema/atras.png')}});
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center;
      height: 25px;
      border-radius: 5px; 
  }
  .btn-tabla-register {
      background-image: url({{url('public/backoffice/sistema/modulosistema/registrar.png')}});
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center;
      height: 25px;
      border-radius: 5px; 
  }
  .mx-modulo-titulo-activado {
    color: #fff;
    font-size: 14px;
    font-weight: bold;
    background-color: {{$tienda->ecommerce_color}};
    padding: 10px;
    border-radius: 20px;
    padding-left: 20px;
    padding-right: 20px;
    border-radius: 20px 0px 0px 20px;
  }
  .mx-modulo-titulo-activado + .mx-modulo-titulo-activado {
    border-radius: 0px;
  }
  .mx-modulo-titulo-desactivado {
    color: #31353d;
    font-size: 14px;
    font-weight: bold;
    background-color: #f9f9f9;
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
      top: 4px;
  }
  /* General */
  #mx-cuerposistema {
      background-color: #fff;
      padding: 5px;
      border-radius: 5px;
  }
  .table td {
      padding:5px;
      white-space: nowrap;
      cursor: pointer;
  }
  
  .tabla-detalle {
      width: 100%;
      text-align: left;
      white-space: nowrap;
  }
  .tabla-detalle tr{
      background-color: #fff;
  }
  .tabla-container {
      background-color: #fff;
      border-radius: 5px;
      padding: 5px;
      width: 100%;
  }
  .tabla-detalle td,
  .tabla-detalle th{
      padding: 10px;
      padding-top: 5px;
      padding-bottom: 5px;
  }
  
  .tabla-detalle tr:hover{
      background-color: {{$tienda->ecommerce_color}}3b !important;
  } 
  
  .tabla-detalle th{
      background-color: {{$tienda->ecommerce_color}} !important;
      color: #fff;
      border-radius: 5px;
      padding-top: 8px;
      padding-bottom: 8px;
  } 
  .tabla-cabecera {
      background-color: {{$tienda->ecommerce_color}} !important;
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
  .dataTables_scrollHead {
      height:0px;
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
      padding: 20px 0 100px;
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
                $('#mx-cont-modulo-titulo').html('<a href="javascript:;" onclick="ir_modulo()" class="mx-modulo-titulo-activado">Inicio</a> <a href="javascript:;" class="mx-modulo-titulo-desactivado" style="border-radius: 0px 20px 20px 0px;">'+titulo+'</a>');
                $('a#cont-submodulo'+idmodulo).css('display','block');
                $('.cont-modulo').css('display','none');
                $('#cont-cuerposistema').html('');
            }
            function ir_modulo(){
                $('#mx-cont-modulo-titulo').html('<a href="javascript:;" class="mx-modulo-titulo-desactivado">Inicio</a>');
                $('.cont-modulo').css('display','block');
                $('.cont-submodulo').css('display','none');
                $('.modulo_perfil').css('display','none');
                $('#cont-cuerposistema').html('');
            }
            function ir_perfil(){
                pagina({route:'{{url('backoffice/tienda/nuevosistema/'.$tienda->id) }}/inicio/1/edit'+
                        '?view=editarperfil '+
                        '&name_modulo=inicio'+
                        '&url_sistema={{url('backoffice/tienda/nuevosistema') }}',
                        result:'#modal_cuerpo_modal-master'});
                $('#modal_titulo_modal-master').html('Editar Perfil');
            }
            function ir_cambiarclave(){
                $('.modal-master .main-register-holder').removeClass('mx-modal-fullscreen').removeClass('mx-modal-alert').addClass('mx-modal-normal');
                pagina({route:'{{url('backoffice/tienda/nuevosistema/'.$tienda->id) }}/inicio/1/edit'+
                        '?view=editarcambiarclave'+
                        '&name_modulo=inicio'+
                        '&url_sistema={{url('backoffice/tienda/nuevosistema') }}',
                        result:'#modal_cuerpo_modal-master'});
                $('#modal_titulo_modal-master').html('Cambiar Contraseña');
            }
  
            function pagina_index(nombremodulo,imagenmodulo,idmodulo,nombresubmodulo,imagensubmodulo,idsubmodulo,name_modulo,titulo='',view=''){
                
                $('#mx-cont-modulo-titulo').html('<a href="javascript:;" onclick="ir_modulo()" class="mx-modulo-titulo-activado">Inicio</a> <a href="javascript:;" onclick="ir_submodulo('+idmodulo+',\''+nombremodulo+'\')" class="mx-modulo-titulo-activado">'+nombremodulo+'</a> <a href="javascript:;" class="mx-modulo-titulo-desactivado" style="border-radius: 0px 20px 20px 0px;">'+nombresubmodulo+'</a>');
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
            
            function load_modulo(nombremodulo,imagenmodulo,idmodulo,nombresubmodulo,imagensubmodulo,idsubmodulo,name_modulo,titulo,view,id=0){
   
                var ruta = '/create';
                if(id!=0){
                    ruta = '/'+id+'/edit';
                }
                $('.modal-master').css('display','block');
                $('.modal-master .main-register-holder').removeClass('mx-modal-normal').removeClass('mx-modal-alert').addClass('mx-modal-fullscreen');
                $('#modal_titulo_modal-master').html(titulo);
                pagina({route:'{{url('backoffice/tienda/nuevosistema/'.$tienda->id) }}/'+name_modulo+ruta+
                    '?view='+view+
                    '&nombre_modulo='+nombremodulo+
                    '&imagen_modulo='+imagenmodulo+
                    '&idmodulo='+idmodulo+
                    '&nombre_submodulo='+nombresubmodulo+
                    '&imagen_submodulo='+imagensubmodulo+
                    '&idsubmodulo='+idsubmodulo+
                    '&name_modulo='+name_modulo+
                    '&url_sistema={{url('backoffice/tienda/nuevosistema') }}',
                    result:'#modal_cuerpo_modal-master'});
            }
  
            function modulo_actualizar(name_modulo){
                $('#modal_cuerpo_modal-master').html('');
                $('.modal-master').css('display','none'); 
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



                    

