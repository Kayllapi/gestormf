<form @include('app.nuevosistema.submit',['method'=>'POST','view'=>'editarperfil'])> 
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <label>Indentificación (DNI)</label>
                  <input type="text" value="{{ $usuario->identificacion }}" id="identificacion"/>
                </div>
                <div class="col-md-12">
                  <label>Nombre *</label>
                  <input type="text" value="{{ $usuario->nombre }}" id="nombre"/>
                </div>
                <div class="col-md-12">
                  <label>Apellidos *</label>
                  <input type="text" value="{{ $usuario->apellidos }}" id="apellidos"/>
                </div>
                <div class="col-md-12">
                  <label>Número de Teléfono</label>
                  <input type="text" value="{{ $usuario->numerotelefono }}" id="numerotelefono"/>
                </div>
                <div class="col-md-12">
                  <label>Correo Electrónico</label>
                  <input type="text" value="{{ $usuario->email }}" id="email">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                <label>Logo (300x300)</label>
                <div class="fuzone" id="cont-fileupload-logo" style="height: 178px;">
                  <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                    <input type="file" class="upload" id="imagen">
                    <div id="resultado-logo"></div>
                </div>
                </div>
                <div class="col-md-12">
                  <label>Ubicación (Ubigeo)</label>
                  <select id="idubigeo" >{{ $usuario->idubigeo==0 ? $value->id==1026 : $usuario->idubigeo }}
                            @if($usuario->idubigeo!=0 or $usuario->idubigeo!='')
                            <option value="{{ $usuario->idubigeo }}">{{ $usuario->ubigeonombre }}</option>
                            @else
                            <option></option>
                            @endif
                  </select>
                </div>
                <div class="col-md-12">
                  <label>Dirección</label>
                  <input type="text" value="{{ $usuario->direccion }}" id="direccion"/>
                </div>
              </div>
            </div>
          </div>
    <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
        </div>
    </div>  
</form>
<script>
    $("#idubigeo").select2({
        @include('app.nuevosistema.select2_ubigeo')
    });

    uploadfile({
      input: "#imagen",
      cont: "#cont-fileupload-logo",
      result: "#resultado-logo",
      ruta: "{{ url('/public/backoffice/tienda/'.$tienda->id.'/sistema/') }}",
      image: "{{ $usuario->imagen }}"
    });
</script>