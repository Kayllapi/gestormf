<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/sucursal/'.$s_sucursal->id) }}',
          method: 'PUT',
          data:{
              view: 'editar',
              idprincipal: {{$id}},
              agencias: seleccionar_agencia(),
          }
      },
      function(resultado){
          $('#modal-close-sucursal-editar').click(); 
          $('#tabla-sucursal').DataTable().ajax.reload();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Editar Tienda</h5>
        <button type="button" class="btn-close" id="modal-close-sucursal-editar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <nav>
          <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" id="nav-1" data-bs-toggle="tab" data-bs-target="#nav-target-1" type="button" role="tab" aria-controls="nav-1" aria-selected="true">General</button>
            <button class="nav-link" id="nav-2" data-bs-toggle="tab" data-bs-target="#nav-target-2" type="button" role="tab" aria-controls="nav-2" aria-selected="false">Imagenes</button>
          </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
          <div class="tab-pane fade show active" id="nav-target-1" role="tabpanel" aria-labelledby="nav-1" tabindex="0">
              <div class="row">
                  <div class="col-md-6">
                      <div class="mb-1">
                          <label>Nombre *</label>
                          <input type="text" class="form-control" value="{{ $s_sucursal->nombre }}" id="nombre" {{$id==0?'disabled':''}}>
                      </div>
                      <div class="mb-1">
                          <label 
                                 data-bs-toggle="popover" 
                                 data-bs-placement="right" 
                                 data-bs-content="Puedes buscar por Distrito, Provincia ó Departamento.">Ubicación (Ubigeo) * 
                              <i class="fa-solid fa-circle-info"></i>
                          </label>
                          <select class="form-select" id="idubigeo">
                              <option value="{{$s_sucursal->idubigeo}}">{{$s_sucursal->ubigeonombre}}</option>
                          </select>
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="mb-1">
                          <label>Dirección *</label>
                          <input type="text" class="form-control" value="{{ $s_sucursal->direccion }}" id="direccion">
                      </div>
                      <div class="mb-1">
                          <label>Estado *</label>
                          <select class="form-select" class="form-control" id="idestadosucursal">
                              <option></option>
                          </select>
                      </div>
                  </div>
              </div>
          </div>
          <div class="tab-pane fade" id="nav-target-2" role="tabpanel" aria-labelledby="nav-2" tabindex="0">
              <div class="row">
                  <div class="col-md-6">
                      <div class="mb-1">
                          <label>Logo</label>
                          <div class="fuzone" id="cont-fileupload-logo" style="height: 179px;">
                              <div class="fuzone-text"><i class="fa-solid fa-cloud-arrow-up"></i>Haga clic aquí ó suelte para cargar</div>
                              <input type="file" class="upload" id="imagen_logo">
                              <div id="resultado-logo"></div>
                          </div>
                      </div>
                      <div class="mb-1">
                          <label>Icono</label>
                          <div class="fuzone" id="cont-fileupload-icono" style="height: 179px;">
                              <div class="fuzone-text"><i class="fa-solid fa-cloud-arrow-up"></i>Haga clic aquí ó suelte para cargar</div>
                              <input type="file" class="upload" id="imagen_icono">
                              <div id="resultado-icono"></div>
                          </div>
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="mb-1">
                          <label>Sistema</label>
                          <div class="fuzone" id="cont-fileupload-sistema" style="height: 179px;">
                              <div class="fuzone-text"><i class="fa-solid fa-cloud-arrow-up"></i>Haga clic aquí ó suelte para cargar</div>
                              <input type="file" class="upload" id="imagen_sistema">
                              <div id="resultado-sistema"></div>
                          </div>
                      </div>
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


    @include('app.nuevosistema.select2',['json'=>'ubigeo','input'=>'#idubigeo'])
    @include('app.nuevosistema.select2',['json'=>'estado','input'=>'#idestadosucursal','val'=>$s_sucursal->idestadosucursal])
  
    // uploadfile({
    //   input:"#imagen_logo",
    //   cont:"#cont-fileupload-logo",
    //   result:"#resultado-logo",
    //   ruta: "{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/') }}",
    //   image: "{{ $s_sucursal->imagen_logo }}"
    // });
    // uploadfile({
    //   input:"#imagen_icono",
    //   cont:"#cont-fileupload-icono",
    //   result:"#resultado-icono",
    //   ruta: "{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/') }}",
    //   image: "{{ $s_sucursal->imagen_icono }}"
    // });
    // uploadfile({
    //   input:"#imagen_sistema",
    //   cont:"#cont-fileupload-sistema",
    //   result:"#resultado-sistema",
    //   ruta: "{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/') }}",
    //   image: "{{ $s_sucursal->imagen_sistema }}"
    // });


</script>     