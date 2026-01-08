@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="profile-edit-container">
    <div class="statistic-container fl-wrap">
     <?php 
     $directos = DB::table('consumidor_red')
         ->where('consumidor_red.iduserspatrocinador',Auth::user()->id)
         ->count();
     ?>
     <div class="statistic-item-wrap"> 
        <div class="statistic-item gradient-bg fl-wrap">
            <i class="fa fa-users"></i>
            <div class="statistic-item-numder">0</div>
            <h5>Afiliados Pendientes</h5>
        </div>
     </div> 
     <div class="statistic-item-wrap"> 
        <div class="statistic-item gradient-bg fl-wrap">
            <i class="fa fa-user"></i>
            <div class="statistic-item-numder">{{ $directos }}</div>
            <h5>Afiliados Directos</h5>
        </div>
     </div>
     <div class="statistic-item-wrap"> 
        <div class="statistic-item gradient-bg fl-wrap">
            <i class="fa fa-user"></i>
            <div class="statistic-item-numder"> 0</div>
            <h5>Afiliados Indirectos</h5>
        </div>
     </div>
     <div class="statistic-item-wrap"> 
        <div class="statistic-item gradient-bg fl-wrap">
            <i class="fa fa-users"></i>
            <div class="statistic-item-numder">0</div>
            <h5>Total de Afiliados</h5>
        </div>
     </div>  
    </div>                     
</div>
<?php 


//dd(consumidor_red_abajo(Auth::user()->id)); ?>
    <div class="dashboard-list-box fl-wrap activities">
    <div class="dashboard-header fl-wrap">
        <h3>Mi Equipo</h3>
    </div>
    <div id="template-red"></div> 
    </div> 
@endsection
@section('htmls')
<!--  modal red-afiliado --> 
<div class="main-register-wrap modal-red-afiliado">
    <div class="main-overlay"></div>
    <div class="main-register-holder">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Afiliado</h3>
            <div class="body-modal">
                <div class="box-widget-content" style="padding:0px;padding-bottom: 10px;">
                    <div class="list-author-widget-contacts list-item-widget-contacts">
                        <ul>
                            <li><span><i class="fa fa-user"></i> Nombre:</span> <a href="javascript:;" id="txtnombre" style="cursor: inherit;">---</a></li>
                            <li><span><i class="fa fa-user"></i> Apellidos:</span> <a href="javascript:;" id="txtapellidos" style="cursor: inherit;">---</a></li>
                            <li><span><i class="fa fa-phone"></i>Teléfono:</span> <a href="javascript:;" id="txtnumerotelefono" style="cursor: inherit;">---</a></li>
                            <li><span><i class="fa fa-envelope"></i> Correo:</span> <a href="javascript:;" id="txtemail" style="cursor: inherit;">---</a></li>
                        </ul>
                    </div>
                </div>
                
              <div id="mx-carga-afiliadored">
                <div class="custom-form">
                    <form action="javascript:;" 
                          onsubmit="callback({
                              route: 'backoffice/red/0',
                              method: 'DELETE',
                              carga: '#mx-carga-afiliadored',
                              data: {
                                view: 'eliminarred'
                              }
                          },
                          function(resultado){
                              if (resultado.resultado == 'CORRECTO') {
                                location.href = '{{ Request::fullUrl() }}';                                                  
                              }
                          },this)">
                        <input type="hidden" id="idusuario">
                        <div id="btndeltered"></div>
                        <div class="clearfix"></div>
                    </form>
                </div>
              </div>
            </div>
        </div>
    </div>
</div>
<!--  fin modal reportar --> 
@endsection
@section('scriptsbackoffice')
<script>
selectred();
function selectred(){
    $('#template-red').jstree({
        'core' : {
        'themes' : { 'stripes' : true },
        'animation' : 0,
        'check_callback' : true,
        'data' : {
            'url' : '{{ url('backoffice/consumidor/red/mostrarred') }}',
                'data' : function (node) {
                    return { 
                        id : node.id 
                    };
                }
            }
        }
    });
}
function selectafiliado(pthis,idusuario,nombre,apellidos,numerotelefono,email,count){

    $('.modal-red-afiliado').fadeIn();

    $('#idusuario').val(idusuario);
    $('#txtnombre').html(nombre);
    $('#txtapellidos').html(apellidos);
    $('#txtnumerotelefono').html(numerotelefono);
    $('#txtemail').html(email);
    $('#btndeltered').html('');
    if(count==0){
        $('#btndeltered').html('<button type="submit"  class="log-submit-btn btn-danger">Eliminar de la Red</button>');
    }
}
</script>  
@endsection