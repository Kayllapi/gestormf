
<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/inicio/'.$usuario->id) }}',
        method: 'PUT',
        data:{
            view: 'editarperfil'
        }
    },
    function(resultado){
          $('#modal-close-inicio-editarperfil').click(); 
    },this)"> 
  <div class="modal-header">
    <h5 class="modal-title">Editar Perfil</h5>
    <button type="button" class="btn-close" id="modal-close-inicio-editarperfil" data-bs-dismiss="modal" aria-label="Close"></button>
  </div>
  <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-1">
                  <label>Indentificación (DNI)</label>
                  <input type="text" disabled class="form-control" value="{{ $usuario->identificacion }}" id="identificacion"/>
              </div>
              <div class="mb-1">
                  <label>Nombre *</label>
                  <input type="text" class="form-control" value="{{ $usuario->nombre }}" id="nombre"/>
              </div>
              <div class="mb-1">
                  <label>Apellidos Paterno *</label>
                  <input type="text" class="form-control" value="{{ $usuario->apellidopaterno }}" id="apellidopaterno"/>
              </div>
              <div class="mb-1">
                  <label>Apellidos Materno *</label>
                  <input type="text" class="form-control" value="{{ $usuario->apellidomaterno }}" id="apellidomaterno"/>
              </div>
              <div class="mb-1">
                  <label>Número de Teléfono</label>
                  <input type="text" class="form-control" value="{{ $usuario->numerotelefono }}" id="numerotelefono"/>
              </div>
              <div class="mb-1">
                  <label>Correo Electrónico</label>
                  <input type="text" class="form-control" value="{{ $usuario->email }}" id="email">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-1">
                <label>Logo (300x300)</label>
                <div class="fuzone" id="cont-fileupload-logo" style="height: 206px;">
                    <div class="fuzone-text"><i class="fa-solid fa-cloud-arrow-up"></i> Haga clic aquí o suelte para cargar</div>
                    <input type="file" class="upload" id="imagen">
                    <div id="resultado-logo"></div>
                </div>
              </div>
              <div class="mb-1">
                  <label>Ubicación (Ubigeo)</label>
                  <select id="idubigeo" >
                      <option></option>
                  </select>
              </div>
              <div class="mb-1">
                  <label>Dirección</label>
                  <input type="text" class="form-control" value="{{ $usuario->direccion }}" id="direccion"/>
              </div>
            </div>
          </div>
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
  </div>

</form>
<script>
    @include('app.nuevosistema.select2',['json'=>'ubigeo','input'=>'#idubigeo','val'=>$usuario->idubigeo])

    uploadfile({
      input: "#imagen",
      cont: "#cont-fileupload-logo",
      result: "#resultado-logo",
      ruta: "{{ url('/public/backoffice/tienda/'.$tienda->id.'/sistema/') }}",
      image: "{{ $usuario->imagen }}"
    });
</script>