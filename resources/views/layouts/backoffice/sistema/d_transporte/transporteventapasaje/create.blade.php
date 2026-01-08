@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Venta de Pasaje</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/transporte/transporteventapasaje') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/transporte/transporteventapasaje',
        method: 'POST',
        data:{
            view: 'registrar'
        }
    },
    function(resultado){
       location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/transporte/transporteventapasaje') }}';                                                                           
    },this)">

            <div class="col-sm-6">
                            <label>Tipo de Persona *</label>
                            <select id="idtipopersona">
                                @foreach($tipopersonas as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                            
                            <div id="cont-juridica" style="display:none;">
                                <label>RUC *</label>
                                <input type="text" id="ruc" onkeyup="buscar_ruc()">
                                <div id="resultado-ruc" style="float: right;margin-top: -56px;text-align: right;"></div>
                            </div>
                            <div id="cont-natural" style="display:none;">
                                <label>DNI (8 Digitos) *</label>
                                <input type="text" id="dni" onkeyup="buscar_dni()">
                                <div id="resultado-dni" style="float: right;margin-top: -56px;text-align: right;"></div>
                            </div>
                            <div id="cont-carnetextranjeria" style="display:none;">
                                <label>Carnet Extranjería *</label>
                                <input type="text" id="carnetextranjeria">
                            </div>
                          
                            <div id="cont-juridica1" style="display:none;">
                                <label>Razón Social *</label>
                                <input type="text" id="razonsocial" onkeyup="texto_mayucula(this)" disabled/>
                            </div>
                            <div id="cont-natural1" style="display:none;">
                                <label>Nombre y Apellidos *</label>
                                <input type="text" id="nombreapellidos" onkeyup="texto_mayucula(this)" disabled>
                            </div>
                            <div id="cont-carnetextranjeria1" style="display:none;">
                                <label>Nombre y Apellidos *</label>
                                <input type="text" id="nombreapellidos_carnetextranjeria" onkeyup="texto_mayucula(this)">
                            </div>
                            <label>Ubicación (Ubigeo) *</label>
                            <select id="idubigeo">
                              <option></option>
                            </select>
                            <label>Dirección *</label>
                            <input type="text" id="direccion" onkeyup="texto_mayucula(this)"/>
            </div>
            <div class="col-sm-6">
              
                <label>Número de Asiento *</label>
                <input type="number" value="0" id="numeroasiento"/>
                <label>Origen *</label>
                <select id="idorigen">
                    <option></option>
                    @foreach($transporterutas as $value)
                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
                <label>Destino *</label>
                <select id="iddestino">
                    <option></option>
                    @foreach($transporterutas as $value)
                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
                <?php $comprobante = DB::table('s_tipocomprobante')->get(); ?>
                <label>Comprobante *</label>
                <select id="idcomprobante">
                    <option></option>
                    @foreach($comprobante as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
                <label>Precio *</label>
                <input type="number" id="precio" placeholder="0.00"/>
            </div>

            <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>

</form>                             
@endsection
@section('subscripts')
<script>
    function buscar_dni(){
        limpiarcampos();
        $('#resultado-dni').html('');
        var identificacion = $('#dni').val();
        if(identificacion.length==8 && identificacion!='00000000'){
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
                    $('#idubigeo').removeAttr('disabled');
                    $('#direccion').removeAttr('disabled');
                    if(respuesta.resultado=='ERROR'){
                        $('#nombreapellidos').val('');
                        $('#idubigeo').html('<option></option>');
                        $('#direccion').val('');
                    }else{
                        $('#nombreapellidos').val(respuesta.nombres+' '+respuesta.apellidoPaterno+' '+respuesta.apellidoMaterno);
                    }  
                }
            })
        }else if(identificacion.length==8 && identificacion=='00000000'){
                    $('#resultado-dni').html('');
                    $('#nombre').removeAttr('disabled');
                    $('#nombreapellidos').removeAttr('disabled');
                    $('#idubigeo').removeAttr('disabled');
                    $('#direccion').removeAttr('disabled');
        }
    }
    function buscar_ruc(){
        limpiarcampos();
        $('#resultado-ruc').html('');
        var identificacion = $('#ruc').val();
        if(identificacion.length==11 && identificacion!='00000000000'){
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
                    $('#idubigeo').removeAttr('disabled');
                    $('#direccion').removeAttr('disabled');
                    if(respuesta.resultado=='ERROR'){
                        $('#razonsocial').val('');
                        $('#idubigeo').html('<option></option>');
                        $('#direccion').val('');
                    }else{
                        $('#razonsocial').val(respuesta.razonSocial);
                        $('#idubigeo').html('<option value="'+respuesta.idubigeo+'">'+respuesta.ubigeo+'</option>');
                        $('#direccion').val(respuesta.direccion);
                    }  
                }
            })
        }else if(identificacion.length==11 && identificacion=='00000000000'){
                    $('#resultado-ruc').html('');
                    $('#razonsocial').removeAttr('disabled');
                    $('#idubigeo').removeAttr('disabled');
                    $('#direccion').removeAttr('disabled');
        }
    }
    function limpiarcampos(){
        $('#nombre').attr('disabled','true');
        $('#nombreapellidos').attr('disabled','true');
        $('#razonsocial').attr('disabled','true');
        $('#idubigeo').attr('disabled','true');
        $('#direccion').attr('disabled','true');

        $('#nombreapellidos').val('');
        $('#razonsocial').val('');
        $('#nombreapellidos_carnetextranjeria').val('');
        $('#idubigeo').html('<option></option>');
        $('#direccion').val('');
    }
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
            $('#apellidopaterno_carnetextranjeria').removeAttr('disabled');
            $('#apellidomaterno_carnetextranjeria').removeAttr('disabled');
            $('#idubigeo').removeAttr('disabled');
            $('#direccion').removeAttr('disabled');
            $('#numerotelefono').removeAttr('disabled');
            $('#email').removeAttr('disabled');
        }
        $('#dni').val('');
        $('#ruc').val('');
        $('#carnetextranjeria').val('');
    }).val(1).trigger("change");
  
    $("#idubigeo").select2({
        @include('app.select2_ubigeo')
    });
@if(configuracion($tienda->id,'facturacion_comprobantepordefecto')['resultado']=='CORRECTO')
    $("#idcomprobante").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    }).val({{ configuracion($tienda->id,'facturacion_comprobantepordefecto')['valor'] }}).trigger("change");   
@else
    $("#idcomprobante").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    });
@endif
$("#idorigen").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
});
$("#iddestino").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
});
</script>
@endsection