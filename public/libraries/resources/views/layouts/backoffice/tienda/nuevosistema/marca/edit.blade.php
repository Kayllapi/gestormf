<form @include('app.nuevosistema.submit',['method'=>'PUT','view'=>'editar','id'=>$s_marca->id])>
    <div class="row">
        <div class="col-sm-6">
            <label>Nombre *</label>
            <input type="text" id="nombre" value="{{$s_marca->nombre}}"/>
        </div>
        <div class="col-sm-6">
            <label>Imagen</label>
            <div class="fuzone" id="cont-fileupload" style="height:120px">
                <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                <input type="file" class="upload" id="imagen">
                <div id="resultado-logo"></div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>                
<script>
uploadfile({
  input:"#imagen",
  cont:"#cont-fileupload",
  result:"#resultado-logo",
  ruta: "{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/') }}",
  image: "{{ $s_marca->imagen }}"
});
</script>