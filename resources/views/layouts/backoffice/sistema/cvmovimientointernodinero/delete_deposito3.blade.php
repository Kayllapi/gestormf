<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/cvmovimientointernodinero/'.$movimientointernodinero->id) }}',
          method: 'DELETE',
          data:{
              view: 'eliminar_deposito3'
          }
      },
      function(resultado){
          lista_movimientointernodinero_deposito3();
          load_nuevo_movimientointernodinero_deposito3();
          $('#modal-close-movimientointernodinero-eliminar_deposito3').click();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Eliminar</h5>
        <button type="button" class="btn-close" id="modal-close-movimientointernodinero-eliminar_deposito3" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
          <i class="fa-solid fa-triangle-exclamation"></i> ¿Esta seguro de eliminar?<br>
        </div>
        <label class="mt-1" style="background-color: #636363;
          color: #fff;
          width: 100%;
          border-radius: 5px;
          padding: 0px 5px;
          margin-bottom: 5px;">Aprobación</label>
              <div class="mb-1">
                  <label>Responsable *</label>
                  <select class="form-select" id="idresponsable_deposito3">
                      <option value=""></option>
                      @foreach($usuarios as $value)
                      <option value="{{$value->id}}">{{$value->nombrecompleto}} ({{$value->nombrepermiso}})</option>
                      @endforeach
                  </select>
              </div>
              <div class="mb-1">
                  <label>Contraseña *</label>
                  <input type="password" class="form-control" id="responsableclave_deposito3">
              </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-trash"></i> Eliminar</button>
    </div>
</form>  
<script>
    sistema_select2({ input:'#idresponsable_deposito3' });
</script> 