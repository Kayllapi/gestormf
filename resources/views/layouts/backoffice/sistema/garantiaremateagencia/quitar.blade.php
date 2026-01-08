<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/garantiaremateagencia/0') }}',
          method: 'PUT',
          data:{
              view: 'quitar',
              idagencia: $('#idagencia').val(),
              check_destino: $('#check_destino').val(),
          }
      },
      function(resultado){
          $('#modal-close-garantias-modificar').click(); 
          $('#view').val('quitar'); 
          $('#idresponsable_modificado').val(resultado.idresponsable); 
          actualizar_tabla_origen();
          actualizar_tabla_destino();
          $('#check_destino').val('');
          $('#check_origen').val('');
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Quitar</h5>
        <button type="button" class="btn-close" id="modal-close-garantias-modificar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
          <i class="fa-solid fa-triangle-exclamation"></i> ¿Esta seguro de quitar garantias?<br>
        </div>
        <label class="mt-1" style="background-color: #636363;
          color: #fff;
          width: 100%;
          border-radius: 5px;
          padding: 0px 5px;
          margin-bottom: 5px;">Aprobación</label>
              <div class="mb-1">
                  <label>Responsable (Administración) *</label>
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
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Quitar</button>
    </div>
</form>   
<script>
    sistema_select2({ input:'#idresponsable' });
</script>