<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/agencia') }}',
        method: 'POST',
        data:{
            view: 'registrar'
        }
    },
    function(resultado){
        $('#modal-close-agencia-registrar').click();  
        $('#tabla-agencia').DataTable().ajax.reload();                                 
    },this)"> 
    <div class="modal-header">
        <h5 class="modal-title">Registrar Agencia</h5>
        <button type="button" class="btn-close" id="modal-close-agencia-registrar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
      <div class="row">
          <div class="col-md-12 ">
              
              <div class="mb-1">
                  <label>Nombre Agencia *</label>
                  <input type="text" class="form-control" id="nombrecomercial">
              </div>
              
              <div class="mb-1">
                  <label>Dirección *</label>
                  <input type="text" class="form-control" id="direccion">
              </div>
              <div class="mb-1">
                  <label 
                         data-bs-toggle="popover" 
                         data-bs-placement="right" 
                         data-bs-content="Puedes buscar por Distrito, Provincia ó Departamento.">Ubicación (Ubigeo) * 
                      <i class="fa-solid fa-circle-info"></i>
                  </label>
                  <select class="form-select" id="idubigeo">
                      <option></option>
                  </select>
              </div>
              <div class="mb-1">
                  <label>Teléfono *</label>
                  <input type="text" class="form-control" id="telefono">
              </div>
          </div>
          <div class="col-md-6 d-none">
              <div class="mb-1">
                  <label>Logo</label>
                  <div class="fuzone" id="cont-fileupload" style="height: 150px;">
                      <div class="fuzone-text"><i class="fa-solid fa-cloud-arrow-up"></i>Haga clic aquí ó suelte para cargar</div>
                      <input type="file" class="upload" id="imagen">
                      <div id="resultado-logo"></div>
                  </div>
              </div>
          </div>
      </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form>  
<script>
    uploadfile({
      input:"#imagen",
      cont:"#cont-fileupload",
      result:"#resultado-logo"
    });

    @include('app.nuevosistema.select2',['json'=>'ubigeo','input'=>'#idubigeo'])

    function buscar_ruc(){
        $('#nombrecomercial').val('');
        $('#razonsocial').val('');
        $('#idubigeo').val(null).trigger("change");
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
                url:"{{url('backoffice/'.$tienda->id.'/inicio/showbuscaridentificacion')}}",
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
                        $('#idubigeo').val(null).trigger("change");
                        $('#direccion').val('');
                    }else{
                        $('#nombrecomercial').val(respuesta.nombreComercial);
                        $('#razonsocial').val(respuesta.razonSocial);
                        $('#idubigeo').val(respuesta.idubigeo).trigger("change");
                        $('#direccion').val(respuesta.direccion);
                    }  
                }
            })
        }  
        else if(identificacion!='' && identificacion==0){
            $('#nombrecomercial').removeAttr('disabled');
            $('#razonsocial').removeAttr('disabled');
            $('#idubigeo').removeAttr('disabled');
            $('#direccion').removeAttr('disabled');
        }
    }

</script>    