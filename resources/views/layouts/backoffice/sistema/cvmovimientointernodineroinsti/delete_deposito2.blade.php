<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/cvmovimientointernodineroinsti/'.$movimientointernodinero->id) }}',
          method: 'DELETE',
          data:{
              view: 'eliminar_deposito2'
          }
      },
      function(resultado){
          lista_movimientointernodinero_deposito2();
          load_nuevo_movimientointernodinero_deposito2();
          $('#modal-close-movimientointernodinero-eliminar_deposito2').click();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Eliminar</h5>
        <button type="button" class="btn-close" id="modal-close-movimientointernodinero-eliminar_deposito2" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
          <i class="fa-solid fa-triangle-exclamation"></i> ¿Esta seguro de eliminar?<br>
        </div>
        <div class="mt-2 bg-primary subtitulo">Aprobación</div>
              <div class="mb-1">
                  <label>Responsable *</label>
                  <select class="form-select" id="idresponsable_deposito2">
                      <option value=""></option>
                      @foreach($usuarios as $value)
                      <option value="{{$value->id}}">{{$value->nombrecompleto}} ({{$value->nombrepermiso}})</option>
                      @endforeach
                  </select>
              </div>
              <div class="mb-1">
                  <label>Contraseña *</label>
                  <input type="password" class="form-control" id="responsableclave_deposito2">
              </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-trash"></i> Eliminar</button>
    </div>
</form>  
<script>
    sistema_select2({ input:'#idresponsable_deposito2' });
</script> 