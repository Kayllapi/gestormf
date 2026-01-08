<form @include('app.nuevosistema.submit',['method'=>'POST','view'=>'registrar'])> 
    <div class="row">
      <div class="col-sm-6">
          <label>Categoria *</label>
          <select id="idcategoria">
              <option></option>
              @foreach($categorias as $value)
              <option value="{{ $value->id }}">{{ $value->nombre }}</option>
              @endforeach
          </select>
          <label>Nombre *</label>
          <input type="text" id="nombre"/>
      </div>
      <div class="col-sm-6">
          <label>Imagen</label>
          <div class="fuzone" id="cont-fileupload" style="height:120px">
              <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
              <input type="file" class="upload" id="imagen">
              <div id="resultado-fileupload"></div>
          </div>
      </div>
    </div>
    <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>                 
<script>
    $("#idcategoria").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    });
    uploadfile({
      input:"#imagen",
      cont:"#cont-fileupload",
      result:"#resultado-fileupload"
    });
</script>