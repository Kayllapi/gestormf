<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/usuarioacceso') }}',
          method: 'POST',
          data:{
              view: 'autorizacion'
          }
      },
      function(res){
        $('#modal-close-usuario-autorizacion').click(); 
        $('#iduser_modificacion').val(res.iduser_modificacion)
        autorizar_edicion(); 
        
      },this)"> 
    <div class="modal-header">
        <h5 class="modal-title">AUTORIZAR</h5>
        <button type="button" class="btn-close" id="modal-close-usuario-autorizacion" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        
        <div class="alert mb-1 text-start py-0" id="cont-ultimamodificacion" 
             style="background-color: #ffffff;border: 1px solid grey;">
            <b>ULTIMA MODIFICACIÓN:</b> <span style="font-weight: normal;">{{ $responsable->nombrecompleto }}</span>
        </div>
                <div class="mt-2 bg-primary subtitulo">Aprobación</div>
              <div class="mb-1">
                  <label>Responsable *</label>
                  <select class="form-select" id="idresponsable">
                      <option value=""></option>
                      @foreach($usuarios as $value)
                      <option value="{{$value->id}}">{{$value->nombrecompleto}} ({{$value->nombrepermiso}})</option>
                      @endforeach
                  </select>
              </div>
              <div class="mb-1">
                  <label>Contraseña *</label>
                  <input type="password" class="form-control" id="responsableclave">
              </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Autorizar</button>
    </div>
</form>                      
<script>
    sistema_select2({ input:'#idresponsable' });
</script>
