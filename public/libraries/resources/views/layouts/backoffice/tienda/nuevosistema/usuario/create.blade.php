<form @include('app.nuevosistema.submit',['method'=>'POST','view'=>'registrar','estado_numtelefono' => ($modulo_prestamo!=''? 'required':'')])> 
                    <div class="row">
                        <div class="col-md-6">
                            <label>Tipo de Persona *</label>
                            <select id="idtipopersona">
                                @foreach($tipopersonas as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                            
                            <div id="cont-juridica" style="display:none;">
                                <div class="notification success fl-wrap">
                                    <p>Puedes registrar el RUC con "00000000000", si el cliente no tiene identificación.</p>
                                </div>
                                <label>RUC *</label>
                                <input type="text" id="ruc" onkeyup="buscar_ruc()">
                                <div id="resultado-ruc" style="float: right;margin-top: -56px;text-align: right;"></div>
                            </div>
                            <div id="cont-natural" style="display:none;">
                                <div class="notification success fl-wrap">
                                    <p>Puedes registrar el DNI con "00000000", si el cliente no tiene identificación.</p>
                                </div>
                                <label>DNI (8 Digitos) *</label>
                                <input type="text" id="dni" onkeyup="buscar_dni()">
                                <div id="resultado-dni" style="float: right;margin-top: -56px;text-align: right;"></div>
                            </div>
                            <div id="cont-carnetextranjeria" style="display:none;">
                                <label>Carnet Extranjería *</label>
                                <input type="text" id="carnetextranjeria">
                            </div>
                          
                            <div id="cont-juridica1" style="display:none;">
                                <label>Nombre Comercial *</label>
                                <input type="text" id="nombrecomercial" disabled/>
                                <label>Razón Social *</label>
                                <input type="text" id="razonsocial" disabled/>
                            </div>
                            <div id="cont-natural1" style="display:none;">
                                <label>Nombre *</label>
                                <input type="text" id="nombre" disabled>
                                <label>Apellidos *</label>
                                <input type="text" id="apellidos" disabled>
                            </div>
                            <div id="cont-carnetextranjeria1" style="display:none;">
                                <label>Nombre *</label>
                                <input type="text" id="nombre_carnetextranjeria" disabled>
                                <label>Apellidos *</label>
                                <input type="text" id="apellidos_carnetextranjeria" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Número de Teléfono {{$modulo_prestamo!=''? '*':''}}</label>
                            <input type="text" id="numerotelefono"/>
                            <label>Correo Electrónico</label>
                            <input type="text" id="email"/>
                            <label>Ubicación (Ubigeo) *</label>
                            <select id="idubigeo">
                              <option></option>
                            </select>
                            <label>Dirección *</label>
                            <input type="text" id="direccion"/>
                        </div>
                    </div>
                    <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form> 
<script>

    function buscar_dni(){
        limpiarcampos();
        $('#resultado-dni').html('');
        var identificacion = $('#dni').val();
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
                    $('#nombre').removeAttr('disabled');
                    $('#apellidos').removeAttr('disabled');
                    $('#idubigeo').removeAttr('disabled');
                    $('#direccion').removeAttr('disabled');
                    $('#numerotelefono').removeAttr('disabled');
                    $('#email').removeAttr('disabled');
                    if(respuesta.resultado=='ERROR'){
                        $('#nombre').val('');
                        $('#apellidos').val('');
                        $('#idubigeo').html('<option></option>');
                        $('#direccion').val('');
                    }else{
                        $('#nombre').val(respuesta.nombres);
                        $('#apellidos').val(respuesta.apellidoPaterno+' '+respuesta.apellidoMaterno);
                    }  
                }
            })
        }  
    }
    function buscar_ruc(){
        limpiarcampos();
        $('#resultado-ruc').html('');
        var identificacion = $('#ruc').val();
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
                    $('#nombrecomercial').removeAttr('disabled');
                    $('#razonsocial').removeAttr('disabled');
                    $('#idubigeo').removeAttr('disabled');
                    $('#direccion').removeAttr('disabled');
                    $('#numerotelefono').removeAttr('disabled');
                    $('#email').removeAttr('disabled');
                    if(respuesta.resultado=='ERROR'){
                        $('#nombrecomercial').val('');
                        $('#razonsocial').val('');
                        $('#idubigeo').html('<option></option>');
                        $('#direccion').val('');
                    }else{
                        $('#nombrecomercial').val(respuesta.nombreComercial);
                        $('#razonsocial').val(respuesta.razonSocial);
                        $('#idubigeo').html('<option value="'+respuesta.idubigeo+'">'+respuesta.ubigeo+'</option>');
                        $('#direccion').val(respuesta.direccion);
                    }  
                }
            })
        }  
    }
    function limpiarcampos(){
        $('#nombre').attr('disabled','true');
        $('#apellidos').attr('disabled','true');
        $('#nombrecomercial').attr('disabled','true');
        $('#razonsocial').attr('disabled','true');
        $('#nombre_carnetextranjeria').attr('disabled','true');
        $('#apellidos_carnetextranjeria').attr('disabled','true');
        $('#idubigeo').attr('disabled','true');
        $('#direccion').attr('disabled','true');
        $('#numerotelefono').attr('disabled','true');
        $('#email').attr('disabled','true');

        $('#nombre').val('');
        $('#apellidos').val('');
        $('#nombrecomercial').val('');
        $('#razonsocial').val('');
        $('#nombre_carnetextranjeria').val('');
        $('#apellidos_carnetextranjeria').val('');
        $('#idubigeo').html('<option></option>');
        $('#direccion').val('');
    }

    $('#idgenero').select2({
        placeholder: '-- Seleccionar Genero --',
        minimumResultsForSearch: -1
    });
    $('#idestadocivil').select2({
        placeholder: '-- Seleccionar Estado Civil --',
        minimumResultsForSearch: -1
    });
    $('#idnivelestudio').select2({
        placeholder: '-- Seleccionar Nivel de Estudio --',
        minimumResultsForSearch: -1
    });
  
    $("#idtipopersona").select2({
        placeholder: "---  Seleccionar ---",
        minimumResultsForSearch: -1
    }).on("change", function(e) {
        $('#cont-juridica, #cont-juridica1').css('display','none');
        $('#cont-natural, #cont-natural1').css('display','none');
        $('#cont-carnetextranjeria, #cont-carnetextranjeria1').css('display','none');
        if(e.currentTarget.value == 1) {
            $('#cont-natural, #cont-natural1').css('display','block');
            limpiarcampos();
        }else if(e.currentTarget.value == 2) {
            $('#cont-juridica, #cont-juridica1').css('display','block');
            limpiarcampos();
        }else if(e.currentTarget.value == 3) {
            $('#cont-carnetextranjeria, #cont-carnetextranjeria1').css('display','block');
            $('#nombre_carnetextranjeria').removeAttr('disabled');
            $('#apellidos_carnetextranjeria').removeAttr('disabled');
            $('#idubigeo').removeAttr('disabled');
            $('#direccion').removeAttr('disabled');
            $('#numerotelefono').removeAttr('disabled');
            $('#email').removeAttr('disabled');
        }
        $('#dni').val('');
        $('#ruc').val('');
        $('#carnetextranjeria').val('');
    }).val(1).trigger("change");

    $('#idubigeo_nacimiento').select2({
        @include('app.select2_ubigeo')
    });
    $("#idubigeo").select2({
        @include('app.select2_ubigeo')
    });
</script>
