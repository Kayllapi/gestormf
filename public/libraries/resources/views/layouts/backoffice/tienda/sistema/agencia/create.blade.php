@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Registrar Empresa',
    'botones'=>[
        'atras:/'.$tienda->id.'/agencia: Ir Atras'
    ]
])
<form action="javascript:;" 
      onsubmit="callback({
          route: 'backoffice/tienda/sistema/{{ $tienda->id }}/agencia',
          method: 'POST',
          data:{
              view: 'registrar'
          }
      },
      function(resultado){
          location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/agencia') }}';                                               
      },this)"> 
      <div class="row">
        <div class="col-md-6">
               <div class="notification success fl-wrap">
                   <p>Puedes registrar el RUC con 00000000000, si aún no tiene ninguna Empresa.</p>
               </div>
           <label>RUC *</label>
           <input type="text" id="ruc" onkeyup="buscar_ruc()"/>
           <div id="resultado-ruc" style="float: right;margin-top: -56px;text-align: right;"></div>
           <label>Nombre Comercial *</label>
           <input type="text" id="nombrecomercial" disabled/>
           <label>Razón Social *</label>
           <input type="text" id="razonsocial" disabled/>
           <label>Ubicación (Ubigeo) *</label>
           <select id="idubigeo" disabled>
               <option></option>
           </select>
           <label>Dirección *</label>
           <input type="text" id="direccion" disabled/>
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
      <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>                       
@endsection
@section('subscripts') 
<script>
uploadfile({
  input:"#imagen",
  cont:"#cont-fileupload",
  result:"#resultado-logo"
});

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
</script>                
@endsection