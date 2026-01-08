@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Horario de Entrada y Salida</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/usuarioingresosalida') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div class="profile-edit-container">
    <div class="custom-form">
       <div class="profile-edit-container" id="cont-identificacion-buscador">
         <div class="row">
           <div class="col-md-3"></div>
           <div class="col-md-6">
            <div class="row">
               <div class="col-md-12">
                  <input type="number"  id="identificacion" placeholder="Ingrese su DNI" style="text-align: center; margin: 20px;" >
               </div>
            </div>
           </div>
         </div>
         <div class="row" id="datos-usuario" style="display:none;">
           <div class="col-md-2"></div>
           <div class="col-md-4">
             <div class="row">
               <div class="col-md-12">
                  <input type="text" id="nombre"  disabled>
               </div>
              </div> 
           </div>
           <div class="col-md-4">
             <div class="row">
               <div class="col-md-12">
                  <input type="text" id="apellidos"  disabled>
               </div>
              </div> 
           </div>
         </div>
        </div>
        <div id="carga-ingresosalida"></div>
        <div id="cont-form-usuarioingresosalida" style="display:none;">
            <form class="js-validation-signin px-30" 
                  action="javascript:;" 
                  onsubmit="callback({
                    route: 'backoffice/tienda/sistema/{{ $tienda->id }}/inicio',
                    method: 'POST',
                    data:{
                        view: 'registrar',
                        idtienda:  '{{ $tienda->id }}',
                        carga: '#carga-ingresosalida',
                    }
                    },
                    function(resultado){
                        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio') }}';
                    },this)">
                <div class="row">
                    <input type="hidden" id="idusario" value="0" disabled>
                </div>  
                <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%;" >Guardar</button>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
  $('#identificacion').keyup( function(e) {
    if(e.keyCode == 13){
        buscaridentificacion( $('#identificacion').val())
    }
})
function buscaridentificacion(identificacion){
      load('#carga-ingresosalida'); 
      $('#cont-form-usuarioingresosalida').css('display', 'none');  
      $.ajax({
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/usuarioingresosalida/show-seleccionaridentificacion')}}",
            type:'GET',
            data: {
                identificacion : identificacion
            },
            success: function (respuesta){
                if(respuesta['ingresosalida'] != undefined){
                     $('#cont-form-usuarioingresosalida').css('display', 'block');
                     $('#datos-usuario').css('display', 'block');
                     $('#carga-ingresosalida').html(`<div class="alert alert-info" style="font-size: 20px;
                                                                                                        padding-top: 10px;
                                                                                                        padding-bottom: 10px;
                                                                                                        margin-bottom: 15px;
                                                                                                        margin-top: 5px;">
                                                                    Verifica tus Datos si son Correctos. 
                                                                 </div>`);
                     $('#idusario').val(respuesta['ingresosalida'].id);
                     $('#nombre').val(respuesta['ingresosalida'].nombre);
                     $('#apellidos').val(respuesta['ingresosalida'].apellidos);
                }else {
                    $('#datos-usuario').css('display', 'none');
                    $('#carga-ingresosalida').html(`<div class="alert alert-danger" style="font-size: 20px;
                                                                                                        padding-top: 10px;
                                                                                                        padding-bottom: 10px;
                                                                                                        margin-bottom: 15px;
                                                                                                        margin-top: 5px;">
                                                                    No existe este Usuario!
                                                                 </div>`);
                } 
            }
      })
}
 
</script>
@endsection