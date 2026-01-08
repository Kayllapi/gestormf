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
              padding-top: 3px;
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
          thead > tr > th,
          .table-dark > tr > th,
          .table-dark > tr > td {
              background-color:#144081 !important;
          }
          .btn-primary {
              background-color:#144081;
              border-color:#144081;
          }
          .table {
              border-color: #a6a9ab !important;
          }
          .table > thead{
              background-color:#212529;
          }
          .table > tbody{
              background-color:#fff;
          }
          .tabla-interno > thead,
          .tabla-interno > tbody {
              background-color: #efefef;
          }
          .tabla-interno > thead > tr > th {
              font-weight: 400;
              color: #000000;
          }
          body{
              /*background-color:#efefef;*/
              font-weight: bold;
              color:#000;
	            font-family: Arial, sans-serif !important;
          }
          .modal-body {
              background-color:#efefef;
          }
          
          .modal-title {
              color: white;
              font-weight: bold;
          }
          .modal-content {
              background-color: #144081;
              padding-bottom: 5px;
          }
          .badge {
              background-color: #144081;
              text-align: left;
              font-size: 12px;
          }
          .modal-header .fa-plus {
              font-size: 12px !important;
          }
          .modal-header .btn {
              justify-content: space-between;
          }
          
          .table {
              font-weight: normal !important;
          }
          #cuerposistema .modal-title {
              color: #000000;
          }
          .btn-close {
            background-color: #efefef;
            border-radius: 20px;
            opacity: 100;
            margin-right: 0px !important;
          }
          
            .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option.select2-results__option--highlighted {
    color: #000;
    background-color: #ffffff;
    font-weight: bold;
}

        </style>
</head>
<body url="{{ url('/') }}">
  <div class="container-fluid" style="background-color:#144081;
    font-size: 20px;
    font-weight: bold;">
    <a class="navbar-brand" href="#" style="color:#c59d25">
          @if($tienda->imagen!='')
          <img src="{{ url('public/backoffice/sistema/logo_gestormf.png') }}" 
               style="height: 25px;float: left;margin-top:5px;">
          @endif
     {{ $tienda->nombre }} - {{ $tienda->nombreagencia }}</a>
      <div id="contador_fechaactual" style="float: right;color: #fff;margin-left: 20px;">{{  Carbon\Carbon::now()->format('d-m-Y') }}</div>
  </div>
    <nav class="navbar navbar-expand-lg bg-body-tertiary" style="background-color:#144081 !important;">
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
              padding-left: 0 !important;
              padding-right: 0 !important;
              margin-left: 8px !important;
              margin-right: 8px !important;
        }
        .menu_click_li:hover > .menu_click {
              padding-bottom: 6px;
              border-bottom: 3px solid #fff;
        }
        .menu_click:focus {
              padding-bottom: 6px;
              border-bottom: 3px solid rgb(182, 172, 72) !important;
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
                <img class="thumb" src="{{ url('public/backoffice/sistema/logo_gestormf_icono1.png') }}" class="imglogousuario" style="width: 30px !important;height: 30px !important;border-radius: 15px;">
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
        //$('#menu_click'+num).css('background-color','rgb(182 172 72)');
        $('#menu_click'+num).attr('style','padding-bottom: 6px;border-bottom: 3px solid rgb(182, 172, 72);');
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