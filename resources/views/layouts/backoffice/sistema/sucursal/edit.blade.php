<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$idtienda.'/sucursal/'.$tienda->id) }}',
          method: 'PUT',
          data:{
              view: 'editar',
              idtienda: {{$tienda->id}}
          }
      },
      function(resultado){
          $('#modal-close-sucursal-editar').click(); 
          $('#tabla-sucursal').DataTable().ajax.reload();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Editar Agencia</h5>
        <button type="button" class="btn-close" id="modal-close-sucursal-editar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row">
          <div class="col-sm-12 col-md-8">
            <div class="mb-1">
                <label>AGENCIA *</label>
                <input type="text" class="form-control" id="nombreagencia" value="{{ $tienda->nombreagencia }}">
            </div>
            <div class="mb-1">
                <label>NOMBRE EMPRESA *</label>
                <input type="text" class="form-control" id="nombre" value="{{ $tienda->nombre }}">
            </div>
            <div class="mb-1">
                <label>REPRESENTANTE *</label>
                <input type="text" class="form-control" id="representante" value="{{ $tienda->representante }}">
            </div>
            <div class="mb-1">
                <label>DIRECCIÓN *</label>
                <input type="text" class="form-control" id="direccion" value="{{ $tienda->direccion }}">
            </div>
            <div class="mb-1">
                <label>RUC*</label>
                <input type="text" class="form-control" id="ruc" value="{{ $tienda->ruc }}">
            </div>
            <div class="mb-1">
                <label data-bs-toggle="popover" 
                        data-bs-placement="right" 
                        data-bs-content="Puedes buscar por Distrito, Provincia ó Departamento.">Ubicación (Ubigeo) * 
                    <i class="fa-solid fa-circle-info"></i>
                </label>
                <select class="form-select" id="idubigeo">
                    <option></option>
                </select>
            </div>
            <div class="mb-1">
                <label>TELÉFONO *</label>
                <input type="text" class="form-control" id="telefono" value="{{ $tienda->numerotelefono }}">
            </div>
            <div class="mb-1">
                <label>PÁGINA WEB *</label>
                <input type="text" class="form-control" id="paginaweb" value="{{ $tienda->paginaweb }}">
            </div>
            <div class="mb-1">
                <label>TIPO EMPRESA *</label>
                <input type="text" class="form-control" id="tipo_empresa" value="{{ $tienda->tipo_empresa }}">
            </div>
            <div class="mb-1">
                <label>CONTRASEÑA SISTEMA*</label>
                <input type="text" class="form-control" id="password_agencia" value="{{ $tienda->password_agencia }}">
            </div>
            <div class="mb-1">
                <label>CONTRASEÑA COMPRA VENTA</label>
                <input type="text" class="form-control" id="password_compraventa" value="{{ $tienda->password_compraventa }}">
            </div>
          </div>
          <div class="col-sm-12 col-md-4">
            <div class="mb-1 text-center">
              <label>FIRMA CONTRATO</label><br>
              <label>Ancho: 170px - Altura: 70px</label><br>
              <label>Formato: .PNG</label>
              <div class="fuzone" id="cont-fileupload" style="height: 150px;">
                  <div class="fuzone-text"><i class="fa-solid fa-cloud-arrow-up"></i>Haga clic aquí ó suelte para cargar</div>
                  <input type="file" class="upload" id="imagen">
                  <div id="resultado-firma"></div>
              </div>
            </div>
            <div class="mb-1 text-center">
              <label>LOGO</label><br>
              <label>Ancho: 170px - Altura: 70px</label><br>
              <label>Formato: .PNG</label>
              <div class="fuzone" id="cont-fileupload-logo" style="height: 150px;">
                  <div class="fuzone-text"><i class="fa-solid fa-cloud-arrow-up"></i>Haga clic aquí ó suelte para cargar</div>
                  <input type="file" class="upload" id="imagen-logo">
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
      result:"#resultado-firma",
      ruta: "{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/') }}",
      image: "{{ $tienda->firma }}"
    });
    uploadfile({
      input:"#imagen-logo",
      cont:"#cont-fileupload-logo",
      result:"#resultado-logo",
      ruta: "{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/') }}",
      image: "{{ $tienda->imagen }}"
    });
    @include('app.nuevosistema.select2',['json'=>'ubigeo','input'=>'#idubigeo','val' => $tienda->idubigeo ])
  


</script>     