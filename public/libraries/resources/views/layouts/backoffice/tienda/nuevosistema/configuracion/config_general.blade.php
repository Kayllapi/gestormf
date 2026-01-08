<form @include('app.nuevosistema.submit',['method'=>'PUT','view'=>'config_general','id'=>$configuracion['idconfiguracion']])>
    <div class="row">
      <div class="col-sm-12">
      </div>
       <div class="col-md-6">
                <label>Imagen de Fondo para Login</label>
                <div class="fuzone" id="cont-fileupload" style="height: 177px;">
                    <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                    <input type="file" class="upload" id="imagen">
                    <div id="resultado-imagen"></div>
                </div>
            </div>
      <div class="col-md-6">
                <label>Imagen de Fondo para Sistema (2000x750)</label>
                <div class="fuzone" id="cont-fileupload-portada" style="height: 177px;">
                    <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                    <input type="file" class="upload" id="imagenportada">
                    <div id="resultado-portada"></div>
                </div>
            </div>
    </div>
    <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>
<script>
 
  
    @if($configuracion['imagenlogin']!=null)
        uploadfile({
          input:"#imagen",
          cont:"#cont-fileupload",
          result:"#resultado-imagen",
          ruta: "{{ url('public/backoffice/tienda/'.$tienda->id.'/imagenlogin/')}}",
          image: "{{ $configuracion['imagenlogin'] }}"
        });
    @else
        uploadfile({
          input:"#imagen",
          cont:"#cont-fileupload",
          result:"#resultado-imagen"
        }); 
    @endif
  
    @if($configuracion['imagensistema']!=null)
        uploadfile({
          input:"#imagenportada",
          cont:"#cont-fileupload-portada",
          result:"#resultado-portada",
          ruta: "{{ url('public/backoffice/tienda/'.$tienda->id.'/imagensistema/')}}",
          image: "{{ $configuracion['imagensistema'] }}"
        });
    @else
        uploadfile({
          input:"#imagenportada",
          cont:"#cont-fileupload-portada",
          result:"#resultado-portada"
        }); 
    @endif

</script>