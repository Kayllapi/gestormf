<?php
$moneda_soles = DB::table('s_moneda')->whereId(1)->first();
$moneda_dolares = DB::table('s_moneda')->whereId(2)->first();
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="robots" content="index, follow"/>
        <title>{{ $tienda->nombreagencia }}</title>
        
        <link rel="shortcut icon" href="{{ url('/public/backoffice/sistema/logo_gestormf_icono1.png') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"/>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css"/>
        <link rel="stylesheet" href="{{ url('public/nuevosistema/librerias/app/app.css') }}"/>
        <style>
          
  .dataTables_scrollBody {
    border-left: 1px solid #8b8b8b;
    border-right: 1px solid #8b8b8b;
  }
          .color_totales{
                background-color: #c8c8c8 !important;
                color: #000 !important;
          }
          .color_cajatexto {
              background-color: #dfdf79 !important;color: #000 !important;
          }
          .campo_moneda {
              text-align: right;
          }
          .modal-body-cualitativa .form-check-input[type=checkbox]
          /*.modal-body-cualitativa .select2-container--bootstrap-5 .select2-selection*/ {
                      background-color: #dfdf79 !important;color: #000 !important;
          }
          .select2-container--bootstrap-5 .select2-selection {
              min-height: 28px;
              padding-top: 4px;
              padding-left: 4px;
          }
          .btn {
              padding: 4px 8px 4px 8px;
          }
          .input-group-text{
          line-height: 1.1;
          }
          .input-group-text,
          .form-control {
              padding: 4px;
          }
          .modal-fullscreen {
            width:100% !important;
          }
          .form-check-input:checked {
              background-color: #585858;
              border-color: #585858;
          }
          .card-body {
              padding: 8px;
          }
          .btn {
              width:auto;
              margin-left: auto;
              order: 2;
              font-weight: bold;
          }
          body{
              background-color:#efefef;
              font-weight: bold;
              color:#000;
	            font-family: Arial, sans-serif !important;
          }
          .modal-body {
              background-color:#efefef;
          }
          
          .modal-title {
              font-weight: bold;
          }
          .badge {
              background-color: #a6a9ab;
              text-align: left;
              font-size: 14px;
              color: #000;
          }
          .modal-header .fa-plus {
              font-size: 12px !important;
          }
          .modal-header .btn {
              justify-content: space-between;
          }
          .modal-header,
          .modal-footer {
              background: #b7b6b7;
          }
          .btn-close {
            background-color: #efefef;
            border-radius: 20px;
            opacity: 100;
            margin-right: 0px !important;
            padding: 0px !important;
            height: 25px;
    width: 25px;
          }
          #cuerposistema .modal-title {
              color: #000000;
          }
          .subtitulo {
            border-radius: 5px;
    padding-left: 5px;
    padding-right: 5px;
    margin-bottom: 3px;
          }
          
          .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option.select2-results__option--highlighted {
              color: #000;
              background-color: #ffffff;
              font-weight: bold;
          }
          .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option.select2-results__option--selected {
              color: #000;
              background-color: #c3ddff;
              font-weight: bold;
          }
          .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option:hover {
              background-color: #d4d4d4;
          }
          .form-control:focus {
            box-shadow:none;
          } 
          .table {
              border-color: #a6a9ab !important;
              font-weight: normal !important;
          }
  
          .tabla-interno > thead,
          .tabla-interno > tbody {
              background-color: #efefef;
          }
          .tabla-interno > thead > tr > th {
              font-weight: bold;
              color: #000000;
          }
          .table > thead > tr > th, 
          .table > tbody > tr > th, 
          .table-dark > tr > th, 
          .table-dark > tr > td, 
          .table.dataTable tfoot th {
              background-color: #c2c0c2 !important;
              color: #000000 !important;
          }
          .table > tbody > tr:hover > th {
              background-color: #c2c0c2 !important;
          }
          .bg-primary,
          .btn-primary,
          .btn-primary:hover,
          .btn-primary:active,
          .btn-primary:focus {    
              background-color: #d4d4d4 !important;
              border: 1px solid #727171 !important;
              color: #000 !important;
          }
          .btn-primary  .fa-solid:before {    
              color: #000;
          }
          .bg-success,
          .btn-success,
          .btn-success:hover,
          .btn-success:active,
          .btn-success:focus {    
              background-color: #cfecc5 !important;
              border: 1px solid #6fa35e !important;
              color: #000 !important;
          }
          .btn-success  .fa-solid:before {    
              color: #000;
          }
          .bg-danger,
          .btn-danger,
          .btn-danger:hover,
          .btn-danger:active,
          .btn-danger:focus {    
              background-color: #ffc9ca !important;
              border: 1px solid #b35b5d !important;
              color: #000 !important;
          }
          .btn-danger  .fa-solid:before {    
              color: #cd2024;
          }
          .bg-info,
          .btn-info,
          .btn-info:hover,
          .btn-info:active,
          .btn-info:focus {    
              background-color: #c3ddff !important;
              border: 1px solid #5b81b3 !important;
              color: #000 !important;
          }
          .btn-info  .fa-solid:before {    
              color: #000;
          }

          #cuerposistema > .modal-body {
              padding: 0px;
              padding-top: 5px;
          }
          .modal-content > div {
              height: 100%;
          }
          .modal-content  .modal-body {
              /*height: calc(100vh - 38px);*/
          }
          .modal-content  .modal-body > iframe.modal-1 {
              width: 100%;
              height: calc(100vh - 54px);
          }
        </style>
</head>
<body url="{{ url('/') }}">
  <div class="container-fluid" style="background-color:#efefef;
    font-size: 20px;
    font-weight: bold;text-align: center;height: 35px;">
    <a class="navbar-brand" href="#" style="color: #1d549b">
          @if($tienda->imagen!='')
          <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$tienda->imagen) }}" 
               style="height: 32px;float: left;margin-top:2px;">
          @endif
     {{ $tienda->nombre }} - {{ $tienda->nombreagencia }}</a>
      <div id="contador_fechaactual" style="float: right;color: #b45126;margin-left: 20px;">{{  Carbon\Carbon::now()->format('d-m-Y') }}</div>
  </div>
    <nav class="navbar navbar-expand-lg bg-body-tertiary" style="background-color:#cdcdcd !important;    padding-top: 5px;
    padding-bottom: 0;">
  <div class="container-fluid">
    <button style="background-color: #fff;" class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">

            <?php


            $modulos = DB::table('permisoacceso')
                        ->join('modulo','modulo.id','permisoacceso.idmodulo')
                        ->where('permisoacceso.idpermiso',user_permiso()->idpermiso)
                        ->where('modulo.idestado',1)
                        ->where('modulo.idmodulo',7)
                        ->select('modulo.*')
                        ->get();

              // dd($modulos);
            ?>
            <?php $i = 1  ; ?>
            @foreach($modulos as $value)
               <li  class="nav-item dropdown menu_click_li" style="font-weight: normal;font-size: 13px;" >
                    <a href="javascript:;" class="nav-link dropdown-toggle menu_click" 
                    id="menu_click{{ $value->id }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="{{ $value->icono }}"></i> {{ $value->nombre }}</a>
                    <ul class="dropdown-menu">
                    <?php

                    $submodulos = DB::table('permisoacceso')
                                  ->join('modulo','modulo.id','permisoacceso.idmodulo')
                                  ->where('permisoacceso.idpermiso',user_permiso()->idpermiso)
                                  ->where('modulo.idestado',1)
                                  ->where('modulo.vista','<>','SOLO-ACCESO')
                                  ->where('modulo.idmodulo',$value->id)
                                  ->select('modulo.*')
                                  ->orderBy('modulo.orden','asc')
                                  ->get();
                    ?>
                    @foreach($submodulos as $subvalue)
                          <?php $href = str_replace('{idtienda}', $tienda->id, $subvalue->vista); ?>       
                          <li><a href="javascript:;"  class="dropdown-item" style="font-size: 13px;"
                          onclick="pagina({route:'{{url($href)}}?view=tabla',result:'#cuerposistema'}),menu_click({{ $value->id }})">
                            <i class="{{ $subvalue->icono }}"></i> {{ $subvalue->nombre }}</a></li>                  
                    @endforeach
                    </ul>
                </li>    
                <?php $i++; ?>  
            @endforeach
        <li class="nav-item">
          <a class="nav-link disabled"><div id="aperturacaja"></div></a>
        </li>
        
      </ul>
      <style>
        .menu_click {
              padding-bottom: 6px;
              border-radius: 5px 5px 0px 0px;
              border-bottom: 2px solid transparent;
        }
        /*.menu_click_li:hover > .menu_click {
              padding-bottom: 6px;
              border-bottom: 0px solid #fff;
        }*/
              
        /*.menu_click_li:hover,
        .menu_click_li:active,
        .menu_click_li:focus {
              background-color: #efefef;
              border-radius: 5px 5px 0px 0px;
        }*/
        .menu_click:hover {
              /*background-color: #efefef;*/
              padding-bottom: 6px;
              border-radius: 5px 5px 0px 0px;
              border-bottom: 2px solid #000;
        }
        .menu_click:active,
        .menu_click:focus {
              background-color: #fff;
              padding-bottom: 6px;
              border-bottom: 2px solid #efefef;
              border-radius: 5px 5px 0px 0px;
        }
        .nav-link {
            padding-top: 6px;
            padding-bottom: 5px;
        }
        .dropdown-item:hover {
            background-color:#e5e5e5;
        }
      </style>
      <ul class="navbar-nav d-flex">
      <div style="width:150px;float: right;margin-top: 2px;">
            <?php
              $tienda_permiso = DB::table('users_permiso')
                                  ->join('permiso','permiso.id','users_permiso.idpermiso')
                                  ->join('tienda','tienda.id','users_permiso.idtienda')
                                  ->where('users_permiso.idusers',Auth::user()->id)
                                  ->select(
                                    'tienda.*',
                                    //'permiso.nombre as nombrepermiso',
                                    'tienda.nombreagencia as nombretienda',
                                  )
                                  ->distinct()
                                  ->get();
            ?>
          <select class="form-control" id="idagenciapermiso">
            <option></option>
            @foreach($tienda_permiso as $val_permiso)
               <option value="{{$val_permiso->id}}">{{$val_permiso->nombretienda}}</option>
            @endforeach
          </select>
      </div>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-weight: normal;">
            @if($imagenusuario!='')
                <img class="thumb" src="{{ $imagenusuario }}" class="imglogousuario" 
                      style="width: 30px;height: 30px;border-radius: 15px;">
            @else
                <img class="thumb" src="{{ url('public/backoffice/sistema/icono_usuario.png') }}" class="imglogousuario" style="width: 30px !important;height: 30px !important;">
            @endif
            {{ $usuario->nombre }} ({{ $usuario->permiso }})
            
          </a>
                      
          <ul class="dropdown-menu dropdown-menu-end">
            <!--li>
              <a class="dropdown-item" 
                   href="javascript:;" 
                   onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/inicio/'.$usuario->id.'/edit')}}?view=editarperfil'})">
                <i class="fa-solid fa-edit"></i> Editar Perfil</a>
                
            </li-->
            <li>
              <a class="dropdown-item" href="javascript:;" onclick="ir_inicio()">
                <i class="fa-solid fa-home"></i> Ir a Inicio</a>
            </li>
            <li>
              <a class="dropdown-item" href="javascript:;" onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/inicio/'.$usuario->id.'/edit')}}?view=editarpassword',size:'modal-sm'})">
                <i class="fa-solid fa-lock"></i> Cambiar Contraseña</a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <div id="cont_tienda_permiso"></div>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item" href="javascript:;" onclick="document.getElementById('logout-form-sistema').submit()">
                <i class="fa-solid fa-power-off"></i> Cerrar Sesión</a>
                <form method="POST" id="logout-form-sistema" action="{{ route('logout') }}">
                    @csrf 
                    <input type="hidden" value="{{ $tienda->id }}" name="logoutidtienda">
                    <input type="hidden" value="{{ Auth::user()->idtipousuario }}" name="logoutidtipousuario">
                    <input type="hidden" value="{{ url('/login') }}" name="logoutlink">
                </form>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
    <div class="mx-subcuerpo">
        <div class="profile-edit-container">
            <div class="custom-form" id="cuerposistema" style="margin: 5px;">
            </div>
        </div> 
    </div>
      <style>
  #fecha {
  line-height: 5em;
  margin: 0 auto;      
  text-align: center;
  font-size: 250%;
}
</style>
  <script src="{{ url('public/nuevosistema/librerias/jquery/3.6.3/jquery.min.js') }}"></script>
  <!--script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.min.js"></script-->
  <!--script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script-->

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDMz3eE2VbbQ47xCnyv1OepO_kN_t21ip8"></script>
        <script src="{{ url('public/layouts/js/map_infobox.js') }}"></script>
        <script src="{{ url('public/layouts/js/markerclusterer.js') }}"></script>  

  <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/scroller/2.0.3/js/dataTables.scroller.min.js"></script>
  <script src="https://cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>
  <script src="https://cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.js"></script>
  <script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
  <script src="https://cdn.datatables.net/keytable/2.6.4/js/dataTables.keyTable.min.js"></script>

  <script src="{{ url('public/nuevosistema/librerias/app/app.js') }}"></script>
  <script src="{{ url('public/nuevosistema/librerias/app/scripts.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>
  <script>
    function valida_input_vacio(){
      $('input[valida_input_vacio]').inputmask("decimal", {
        digits  : 2,
        placeholder : "0.00",
        allowMinus : false,
        allowPlus : false,
        max : 9999999999999991,
        digitsOptional : false
      });
      $('input[valida_input_vacio]').on('click', function() {
        $(this).select();
      });
      $('input[valida_input_vacio]').on('blur', function() {
        if ($(this).val() === "") {
          $(this).val("0.00");
          let obtenerFuncion = $(this).attr("onkeyup");
          if (obtenerFuncion) {
            //console.log(obtenerFuncion);
            let event = jQuery.Event("keyup");
            event.keyCode = event.which = 13; 
            eval(obtenerFuncion);
          }
        }
      });
    }
    function menu_click(num){
        $('.menu_click').removeAttr('style');
        //$('.menu_click'+num).attr('style',`background-color: #fff;padding-bottom: 6px;border-bottom: 2px solid #fff;border-radius: 5px 5px 0px 0px;`);
        $('#menu_click'+num).attr('style','padding-bottom: 6px;border-bottom: 2px solid #fff;background-color: #fff;');
    }
  </script>
<style>
  .select2-container--bootstrap-5 .select2-dropdown {
    border-color: #144081;
    border-width:2px;
}
  .select2-container--bootstrap-5.select2-container--focus .select2-selection, .select2-container--bootstrap-5.select2-container--open .select2-selection {
    border-color: #144081;
    border-width:2px;
    box-shadow: inherit;
}
  </style>
    <script>
      
      sistema_select2({ input:'#idagenciapermiso', val:'{{ $tienda->id }}' });
      sistema_select2({ input:'#idagenciapermisopermiso' });
     
      $(`#idagenciapermiso`).on("change", function(e) {
          agenciapremiso($('#idagenciapermiso :selected').val());
      });
      $(`#idagenciapermisopermiso`).on("change", function(e) {
          cambiar_tienda($('#idagenciapermiso :selected').val(),$('#idagenciapermisopermiso :selected').val());
      })
      
      /*function cambiar_tiendafe(idpermiso,idtienda){
        console.log(idpermiso)
          cambiar_tienda(idpermiso,idtienda);
      }*/
      
      agenciapremiso('{{ $tienda->id }}');
      function agenciapremiso(idagencia){
          $.ajax({
              url:"{{url('backoffice/'.$tienda->id.'/inicio/show_agenciapermiso')}}",
              type:'GET',
              data: {
                idagenciapermiso : idagencia,
              },
              success: function (respuesta){
                  $('#cont_tienda_permiso').html(respuesta)
              }
          })
      }
      
      // arreglando el focus de select2
      $(document).on('select2:open', () => {
        document.querySelector('.select2-container--open .select2-search__field').focus();
      }); 
      
      ir_inicio();
      function ir_inicio(){
          $('.menu_click').removeAttr('style');
          pagina({route:'{{url('backoffice/'.$tienda->id.'/inicio/create?view=inicio')}}',result:'#cuerposistema'});
      }
          
      @include('app.nuevosistema.script')
      
      /*function cerrar_sesion_master(){
        document.getElementById('logout-form-sistema').submit()
      }*/
      
      function cambiar_tienda(idpermiso, idtienda){
          $.ajax({
              url:"{{url('backoffice/'.$tienda->id.'/inicio/show_cambiarsucursal')}}",
              type:'GET',
              data: {
                idpermiso : idpermiso,
                idtienda : idtienda,
              },
              success: function (respuesta){
                  location.reload();
              }
          })
      }
      
        var timeLimit = 5; //tiempo en minutos
   var conteo_nuevo = new Date(timeLimit * 60000);
   var conteo = new Date(timeLimit * 60000);
  
      /*$("html,body").on("click", function(e) {
         resetear_cierresesion();
      });*/
   /*contador_cierresesion();

   function contador_cierresesion(){
      intervaloRegresivo = setInterval("regresiva_cierresesion()", 1000);
   }

   function regresiva_cierresesion(){
      if(conteo.getTime() > 0){
         conteo.setTime(conteo.getTime() - 1000);
      }else{
         clearInterval(intervaloRegresivo);
          // cerrar sesion
         document.getElementById('logout-form-sistema').submit();
      }

      $('#contador_cierresesion').html((conteo.getMinutes()).toString().padStart(2, '0') + ":" + (conteo.getSeconds()).toString().padStart(2, '0'));
   }
   function resetear_cierresesion(){
      conteo.setTime(conteo_nuevo.getTime());
   }*/
         /* fechaactual();
          function fechaactual(){
              var hoy = new Date();
              var fecha = hoy.getDate() + '-' + ( hoy.getMonth() + 1 ) + '-' + hoy.getFullYear();
              //var hora = hoy.getHours() + ':' + hoy.getMinutes() + ':' + hoy.getSeconds();
              $('#contador_fechaactual').html(fecha);
          }*/

    </script>
</body>
</html>