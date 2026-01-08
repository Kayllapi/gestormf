<form @include('app.nuevosistema.submit',['method'=>'PUT','view'=>'editar','id'=>$s_categoria->id])>
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
            <input type="text" id="nombre" value="{{$s_categoria->nombre}}"/>
            <label>Estado *</label>
                            <select id="idestado">
                                <option></option>
                                <option value="1">Activado</option>
                                <option value="2">Desactivado</option>
             </select>
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
    }).val({{ $s_categoria->s_idcategoria }}).trigger("change");
    uploadfile({
      input:"#imagen",
      cont:"#cont-fileupload",
      result:"#resultado-fileupload",
      ruta: "{{ url('public/backoffice/tienda/'.$tienda->id.'/nuevosistema/') }}",
      image: "{{ $s_categoria->imagen }}"
    });
    $("#idestado").select2({
      placeholder: "---  Seleccionar ---",
      minimumResultsForSearch: -1
  }).val({{$s_categoria->idestado}}).trigger("change");
    
</script>