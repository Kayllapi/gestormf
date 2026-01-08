@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Horario de Entrada y Salida</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div class="profile-edit-container">
    <div class="custom-form">
       <div class="profile-edit-container">
         <div class="row">
           <div class="col-md-3"></div>
           <div class="col-md-6">
            <div class="row">
               <div class="col-md-12">
                  <input type="number"  id="identificacion" placeholder="Ingrese su DNI" style="text-align: center;" >
               </div>
            </div>
           </div>
         </div>
        </div>
          <div id="carga-ingresosalida"></div>
        
    </div>
</div>
<div class="main-register-wrap"  id="modal-observacion">
    <div class="main-overlay"></div>
    <div class="main-register-holder">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Registrar Observación</h3>
            <div class="mx-modal-cuerpo" id="contenido-observacion">
              <div id="mx-carga-observacion">
              <form class="js-validation-signin px-30" 
                  action="javascript:;" 
                  onsubmit="callback({
                    route: 'backoffice/tienda/sistema/{{ $tienda->id }}/inicio',
                    method: 'POST',
                    carga: '#mx-carga-observacion',
                    data:{
                        view: 'registrarobservacionhorario',
                        idusuarioingresosalida: $('#idusuarioingresosalida').val(),
                    }
                },
                function(resultado){
                     location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio') }}';                                                            

                },this)">
                <input type="hidden" id="idusuarioingresosalida">
                <div class="profile-edit-container">
                    <div class="custom-form">
                        <label>Observación</label>
                        <textarea  id="observacion" cols="30" rows="10"></textarea>
                    </div>
                </div>
                <div class="profile-edit-container">
                    <div class="custom-form">
                        <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios</button>
                    </div>
                </div> 
            </form> 
            </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
  $('#identificacion').keyup( function(e) {
    if(e.keyCode == 13){
        buscaridentificacion( $('#identificacion').val());
    }
})
function buscaridentificacion(identificacion){
      load('#carga-ingresosalida'); 
      $('#cont-form-usuarioingresosalida').css('display', 'none');  
      $.ajax({
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/show-seleccionaridentificacion')}}",
            type:'GET',
            data: {
                identificacion : identificacion
            },
            success: function (respuesta){
                if(respuesta['resultado']=='CORRECTO'){
                  $('#carga-ingresosalida').html(
                           '<div class="fl-wrap" style="background: #e5ffdd;border-radius: 10px;"><div class="cont-confirm" style="margin-top: 15px; ">'+
                           '<div class="confirm"><i class="fa fa-check"></i></div>'+
                           '<div class="confirm-texto" style="color: #0b9c21;font-weight: 700;font-size: 22px;">¡Correcto!</div>'+
                           '<div  class="confirm-texto" style="font-size:17px;font-weight: bold;">'+respuesta['mensaje']+'</div>'+
                           '<div  class="confirm-texto" style="font-size:23px;font-weight: bold;">'+respuesta['fechaactual']+'</div>'+
                           '<div style="font-size:16px;color:#000;">Desea enviar alguna observación<a  href="javascript:;" onclick="boton_observacion('+respuesta['idusuarioingresosalida']+')" style="color: #008cea;font-weight: bold;font-size:16px;"> Ingresa aquí.</a></div></div></div>');
                  $('#idusario').val(respuesta['idusers']);
                }else if(respuesta['resultado']=='ERROR'){
                    $('#carga-ingresosalida').html(
                           '<div class="fl-wrap" style="background: #ffd9de;border-radius: 10px;"><div class="cont-confirm" style="margin-top: 15px;">'+
                           '<div class="confirm" style=" color: #ef0021 !important; border: 2px solid #ef0021 !important;"><i class="fa fa-times"></i></div>'+
                           '<div class="confirm-texto" style="color: #ef0021;font-weight: 700;font-size: 22px;">¡Error!</div>'+
                           '<div  class="confirm-texto" style="font-size:17px;font-weight: bold;">'+respuesta['mensaje']+'</div>'+
                           '<div  class="confirm-texto" style="font-size:23px;font-weight: bold;">'+respuesta['fechaactual']+'</div>'+
                           '<div style="font-size:16px;color:#000;">Desea enviar alguna observación <a  href="javascript:;" onclick="boton_observacion('+respuesta['idusuarioingresosalida']+')"  style="color: #008cea;font-weight: bold;font-size:16px;"> Ingresa aquí.</a></div></div></div>'
                      );
                } 
            }
      })
}
  function boton_observacion(idusuarioingresosalida){
         $('#modal-observacion').css('display','block');
         $('#idusuarioingresosalida').val(idusuarioingresosalida);
  }
</script>
@endsection