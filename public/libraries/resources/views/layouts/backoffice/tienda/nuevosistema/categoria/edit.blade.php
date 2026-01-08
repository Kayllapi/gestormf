<form @include('app.nuevosistema.submit',['method'=>'PUT','view'=>'editar','id'=>$s_categoria->id])>
    <div class="row">
      <div class="col-sm-6">
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
uploadfile({
  input:"#imagen",
  cont:"#cont-fileupload",
  result:"#resultado-fileupload",
  ruta: "{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/') }}",
  image: "{{ $s_categoria->imagen }}"
});
   $("#idestado").select2({
      placeholder: "---  Seleccionar ---",
      minimumResultsForSearch: -1
  }).val({{$s_categoria->idestado}}).trigger("change");
</script>