<div class="main-register-wrap modal-{{$modal}}">
    <div class="main-overlay"></div>
    <div class="main-register-holder">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>{{$nombre}}</h3>
            <div class="mx-modal-cuerpo" id="contenido-{{$modal}}">
              <div id="mx-carga-{{$modal}}">
              <form class="js-validation-signin px-30" 
                  action="javascript:;" 
                  onsubmit="callback({
                    route: 'backoffice/tienda/sistema/{{ $tienda->id }}/inicio',
                    method: 'POST',
                    carga: '#mx-carga-{{$modal}}',
                    data:{
                        view: 'registrarusuario'
                    }
                },
                function(resultado){
                    $('#{{isset($idusuario)?$idusuario:'idusuario'}}').html('<option value=\''+resultado['cliente'].id+'\'>'+resultado['cliente'].identificacion+' - '+resultado['cliente'].apellidos+', '+resultado['cliente'].nombre+'</option>');
                    $('#{{isset($usuariodireccion)?$usuariodireccion:'usuariodireccion'}}').val(resultado['cliente'].direccion);
                    //$('#{{isset($usuarioubigeo)?$usuarioubigeo:'usuarioubigeo'}}').html('<option></option>');
                    if(resultado['cliente'].idubigeo!=0){
                        $('#{{isset($usuarioubigeo)?$usuarioubigeo:'usuarioubigeo'}}').html('<option value=\''+resultado['ubigeocliente'].id+'\'>'+resultado['ubigeocliente'].nombre+'</option>');                                                             
                    }
                    $('#contenido-{{$modal}}').css('display','none');
                    confirm({
                        input:'#contenido-confirmar-{{$modal}}',
                        resultado:'CORRECTO',
                        mensaje:'Se ha registrado correctamente!.',
                        cerrarmodal:'.modal-{{$modal}}'
                    });
                },this)">
                <div class="profile-edit-container">
                    <div class="custom-form">
                            <label>Tipo de Persona *</label>
                            <select id="cliente_idtipopersona" class="cliente_idtipopersona{{$modal}}">
                                @foreach($tipopersonas as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                            <div id="cont-juridica{{$modal}}" style="display:none;">
                                <div class="notification success fl-wrap">
                                    <p>Puedes registrar el RUC con "00000000000", si el cliente no tiene identificación.</p>
                                </div>
                                <label>RUC *</label>
                                <input type="text" id="cliente_ruc" onkeyup="buscar_ruc{{$modal}}()">
                                <div id="resultado-ruc" style="float: right;margin-top: -56px;text-align: right;"></div>
                                <label>Nombre Comercial *</label>
                                <input type="text" id="cliente_nombrecomercial" disabled>
                                <label>Razón Social *</label>
                                <input type="text" id="cliente_razonsocial" disabled>
                            </div>
                            <div id="cont-natural{{$modal}}" style="display:none;">
                                <div class="notification success fl-wrap">
                                    <p>Puedes registrar el DNI con "00000000", si el cliente no tiene identificación.</p>
                                </div>
                                <label>DNI *</label>
                                <input type="text" id="cliente_dni" onkeyup="buscar_dni{{$modal}}()">
                                <div id="resultado-dni" style="float: right;margin-top: -56px;text-align: right;"></div>
                                <label>Nombre *</label>
                                <input type="text" id="cliente_nombre" disabled>
                                <label>Apellidos *</label>
                                <input type="text" id="cliente_apellidos" disabled>
                            </div>
                            <div id="cont-carnetextranjeria{{$modal}}" style="display:none;">
                                <label>Carnet Extranjería *</label>
                                <input type="text" id="cliente_carnetextranjeria">
                                <label>Nombre *</label>
                                <input type="text" id="cliente_nombre_carnetextranjeria" disabled>
                                <label>Apellidos *</label>
                                <input type="text" id="cliente_apellidos_carnetextranjeria" disabled>
                            </div>
                              <label>Número de Teléfono</label>
                              <input type="text" id="cliente_numerotelefono" disabled>
                              <label>Correo Electrónico</label>
                              <input type="text" id="cliente_email" disabled>
                              <label>Ubicación (Ubigeo) *</label>
                              <select id="cliente_idubigeo" class="cliente_idubigeo{{$modal}}" disabled>
                                  <option></option>
                              </select>
                              <label>Dirección *</label>
                              <input type="text" id="cliente_direccion" disabled>
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
            <div class="mx-modal-cuerpo" id="contenido-confirmar-{{$modal}}"></div>
        </div>
    </div>
</div>
<script>
modal({click:'#modal-{{$modal}}'});
  
function buscar_dni{{$modal}}(){
   limpiarcampos();
   $('#resultado-dni').html('');
   var identificacion = $('#cliente_dni').val();
   if(identificacion.length==8){
       load('#resultado-dni');
       $.ajax({
           url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/showbuscaridentificacion')}}",
           type:'GET',
           data: {
               buscar_identificacion : identificacion,
               tipo_persona : 1
           },
           success: function (respuesta){
               $('#resultado-dni').html('');
               $('#cliente_nombre').removeAttr('disabled');
               $('#cliente_apellidos').removeAttr('disabled');
               $('#cliente_idubigeo').removeAttr('disabled');
               $('#cliente_direccion').removeAttr('disabled');
               $('#cliente_numerotelefono').removeAttr('disabled');
               $('#cliente_email').removeAttr('disabled');
               if(respuesta.resultado=='ERROR'){
                   $('#cliente_nombre').val('');
                   $('#cliente_apellidos').val('');
               }else{
                   $('#cliente_nombre').val(respuesta.nombres);
                   $('#cliente_apellidos').val(respuesta.apellidoPaterno+' '+respuesta.apellidoMaterno);
               }  
           }
       })
   }  
}
function buscar_ruc{{$modal}}(){
   limpiarcampos();
   $('#resultado-ruc').html('');
   var identificacion = $('#cliente_ruc').val();
   if(identificacion.length==11){
       load('#resultado-ruc');
       $.ajax({
           url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/showbuscaridentificacion')}}",
           type:'GET',
           data: {
               buscar_identificacion : identificacion,
               tipo_persona : 2
           },
           success: function (respuesta){
               $('#resultado-ruc').html('');
               $('#cliente_nombrecomercial').removeAttr('disabled');
               $('#cliente_razonsocial').removeAttr('disabled');
               $('#cliente_idubigeo').removeAttr('disabled');
               $('#cliente_direccion').removeAttr('disabled');
               $('#cliente_numerotelefono').removeAttr('disabled');
               $('#cliente_email').removeAttr('disabled');
               if(respuesta.resultado=='ERROR'){
                   $('#cliente_nombrecomercial').val('');
                   $('#cliente_razonsocial').val('');
                    $('#cliente_idubigeo').val('');
                    $('#cliente_direccion').val('');
               }else{
                   $('#cliente_nombrecomercial').val(respuesta.nombreComercial);
                   $('#cliente_razonsocial').val(respuesta.razonSocial);
                    $('#cliente_idubigeo').html('<option value="'+respuesta.idubigeo+'">'+respuesta.ubigeo+'</option>');
                    $('#cliente_direccion').val(respuesta.direccion);
               }  
           }
       })
   }  
 }
 
function limpiarcampos(){
    $('#cliente_nombre').attr('disabled','true');
    $('#cliente_apellidos').attr('disabled','true');
    $('#cliente_nombrecomercial').attr('disabled','true');
    $('#cliente_razonsocial').attr('disabled','true');
    $('#cliente_idubigeo').attr('disabled','true');
    $('#cliente_direccion').attr('disabled','true');
    $('#cliente_numerotelefono').attr('disabled','true');
    $('#cliente_email').attr('disabled','true');
    $('#cliente_nombre_carnetextranjeria').attr('disabled','true');
    $('#cliente_apellidos_carnetextranjeria').attr('disabled','true');

    $('#cliente_nombre').val('');
    $('#cliente_apellidos').val('');
    $('#cliente_nombrecomercial').val('');
    $('#cliente_razonsocial').val('');
    $('#cliente_idubigeo').html('');
    $('#cliente_direccion').val('');
    $('#cliente_numerotelefono').html('');
    $('#cliente_email').val('');
    $('#cliente_nombre_carnetextranjeria').val('');
    $('#cliente_apellidos_carnetextranjeria').val('');
}
  
$('#modal-{{$modal}}').click(function(e) {
    $(".cliente_idtipopersona{{$modal}}").select2({
        placeholder: "---  Seleccionar ---",
        minimumResultsForSearch: -1
    }).on("change", function(e) {
        $('#cont-juridica{{$modal}}').css('display','none');
        $('#cont-natural{{$modal}}').css('display','none');
        $('#cont-carnetextranjeria{{$modal}}').css('display','none');
        if(e.currentTarget.value == 1) {
            $('#cont-natural{{$modal}}').css('display','block');
            limpiarcampos();
        }else if(e.currentTarget.value == 2) {
            $('#cont-juridica{{$modal}}').css('display','block');
            limpiarcampos();
        }else if(e.currentTarget.value == 3) {
            $('#cont-carnetextranjeria{{$modal}}').css('display','block');
            $('#cliente_nombre_carnetextranjeria').removeAttr('disabled');
            $('#cliente_apellidos_carnetextranjeria').removeAttr('disabled');
            $('#cliente_idubigeo').removeAttr('disabled');
            $('#cliente_direccion').removeAttr('disabled');
            $('#cliente_numerotelefono').removeAttr('disabled');
            $('#cliente_email').removeAttr('disabled');
        }
        $('#cliente_dni').val('');
        $('#cliente_ruc').val('');
        $('#cliente_carnetextranjeria').val('');
        // limpiarcampos();
    }).val(1).trigger("change");

    $(".cliente_idubigeo{{$modal}}").select2({
        @include('app.select2_ubigeo')
    });
  
    $('#contenido-{{$modal}}').css('display','block');
    $('#contenido-confirmar-{{$modal}}').html('');
    removecarga({input:'#mx-carga-{{$modal}}'});
  
    $('#cliente_dni').val('');
    $('#cliente_ruc').val('');
    // limpiarcampos();
})
</script>