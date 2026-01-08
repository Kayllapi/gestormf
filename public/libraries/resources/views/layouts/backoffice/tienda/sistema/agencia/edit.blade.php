@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Editar Empresa',
    'botones'=>[
        'atras:/'.$tienda->id.'/agencia: Ir Atras'
    ]
])
<form action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/agencia/{{ $s_agencia->id }}',
        method: 'PUT',
        data: {
            view : 'editar'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/agencia') }}';                                                            
    },this)"
      autocomplete="off">
          <div class="tabs-container" id="tab-empresa">
              <ul class="tabs-menu">
                  <li class="current"><a href="#tab-empresa-0">General</a></li>
                  <li><a href="#tab-empresa-1">Representante Legal</a></li>
                  <li><a href="#tab-empresa-2">Facturación</a></li>
              </ul>
              <div class="tab">
                  <div id="tab-empresa-0" class="tab-content" style="display: block;">
                      <div class="row">
                         <div class="col-md-6">
                            <label>Ruc *</label>
                            <input type="text" id="ruc" value="{{ $s_agencia->ruc }}" onkeyup="buscar_ruc()"/>
                            <label>Nombre Comercial *</label>
                            <input type="text" id="nombrecomercial" value="{{ $s_agencia->nombrecomercial }}"/>
                            <label>Razón Social *</label>
                            <input type="text" id="razonsocial" value="{{ $s_agencia->razonsocial }}"/>
                            <label>Ubicación (Ubigeo) *</label>
                            <select id="idubigeo">
                                <option value="{{$s_agencia->idubigeo}}">{{$s_agencia->ubigeonombre}}</option>
                            </select>
                            <label>Dirección *</label>
                            <input type="text" id="direccion" value="{{ $s_agencia->direccion }}"/>
                         </div>
                         <div class="col-md-6">
                            <label>Logo</label>
                            <div class="fuzone" id="cont-fileupload" style="height: 177px;">
                                <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                                <input type="file" class="upload" id="imagen">
                                <div id="resultado-logo"></div>
                            </div>
                          </div>
                       </div>
                  </div>
                  <div id="tab-empresa-1" class="tab-content" style="display: none;">
                        <div class="row">
                         <div class="col-md-6">
                            <label>DNI</label>
                            <input type="text" id="representante_dni" value="{{ $s_agencia->representante_dni }}"/>
                            <label>Nombre</label>
                            <input type="text" id="representante_nombre" value="{{ $s_agencia->representante_nombre }}"/>
                         </div>
                         <div class="col-md-6">
                            <label>Apelidos</label>
                            <input type="text" id="representante_apellidos" value="{{ $s_agencia->representante_apellidos }}"/>
                            <label>Cargo</label>
                            <input type="text" id="representante_cargo" value="{{ $s_agencia->representante_cargo }}"/>
                          </div>
                       </div>
                  </div>
                  <div id="tab-empresa-2" class="tab-content" style="display: none;">
                        <div class="row">
                          <div class="col-md-6">
                            <label>Estado *</label>
                            <select id="idestadofacturacion">
                                <option value="1">Habilitado</option>
                                <option value="2">Desabilitado</option>
                            </select>
                            <div id="cont-facturacion_estado1">
                            <label>Serie * </label>
                            <input type="number" value="{{ $s_agencia->facturacion_serie }}" id="facturacion_serie" min="0">
                            <label>Correlativo Inicial *</label>
                            <input type="number" value="{{ $s_agencia->facturacion_correlativoinicial }}" id="facturacion_correlativoinicial" min="0">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div id="cont-facturacion_estado2">
                            <label>Usuario de SUNAT *</label>
                            <input type="text" value="{{ $s_agencia->facturacion_usuario }}" id="facturacion_usuario">
                            <label>Clave de SUNAT *</label>
                            <input type="text" value="{{ $s_agencia->facturacion_clave }}" id="facturacion_clave">
                            <label>Certificado Digital para SUNAT (.pem) * </label>
                            <div style="width: 100%;margin-top: 15px;margin-bottom: 10px;float: left;">
                             <input type="file" class="upload" id="facturacion_certificado"> 
                            </div>
                            
                            @if($s_agencia->facturacion_certificado!='')
                                <div class="mensaje-info" style="background-color:#3c4e64 !important;">
                                  <i class="fa fa-exclamation-circle" style="font-size: 30px;margin-bottom: 5px;"></i><br>
                                  <b>¿Dese cambiar el certificado?</b><br>
                                  Al cambiar el certificado, se eliminar el certificado actual.<br>
                                  <b>Descargar certificado actual: <a href="{{ url('public/backoffice/tienda/'.$tienda->id.'/sunat/produccion/certificado/'.$s_agencia->facturacion_certificado) }}" class="btn mx-btn-post" style="background-color: #08b451;font-size: 18px;margin-top: 10px;"  target="_blank">Descargar Certificado</a></b> 
                                </div>
                              
                            @else
                              <div class="mensaje-info" style="background-color:#3c4e64 !important;">
                                  <i class="fa fa-exclamation-circle" style="font-size: 30px;margin-bottom: 5px;"></i><br>
                                  <b>¿Como obtener un certificado GRATUITO de la SUNAT?</b><br>
                                  <b><a href="https://kayllapi.com/pagina/noticias/certificado-digital-sunat-gratis" class="btn mx-btn-post" style="background-color: #08b451;font-size: 18px;margin-top: 10px;" target="_blank">Ir a Pagina</a></b> 
                                </div>
                            @endif
                            </div>
                          </div>
                       </div>
                  </div>
              </div>
          </div>
    <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>   
@endsection
@section('subscripts')            
<style>
</style>
<script>  
uploadfile({
  input:"#imagen",
  cont:"#cont-fileupload",
  result:"#resultado-logo",
  ruta: "{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/') }}",
  image: "{{ $s_agencia->logo }}"
});

tab({click:'#tab-empresa'});

function buscar_ruc(){
    $('#nombrecomercial').val('');
    $('#razonsocial').val('');
    $('#idubigeo').html('<option></option>');
    $('#direccion').val('');
    $('#nombrecomercial').attr('disabled','true');
    $('#razonsocial').attr('disabled','true');
    $('#idubigeo').attr('disabled','true');
    $('#direccion').attr('disabled','true');
    $('#resultado-ruc').html('');
    var identificacion = $('#ruc').val();
    if(identificacion.length==11){
        load('#resultado-ruc');
        $.ajax({
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/agencia/showbuscaridentificacion')}}",
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
$("#idubigeo").select2({
    @include('app.select2_ubigeo')
});
  
$("#idestadofacturacion").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-facturacion_estado1').css('display','block');
    $('#cont-facturacion_estado2').css('display','block');
    if(e.currentTarget.value == 2) {
        $('#cont-facturacion_estado1').css('display','none');
        $('#cont-facturacion_estado2').css('display','none');
    }
}).val({{$s_agencia->idestadofacturacion}}).trigger("change");
</script>
@endsection