<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/garantias/'.$garantias->id) }}',
          method: 'DELETE',
          data:{
              view: 'eliminar'
          }
      },
      function(resultado){
          $('#cont-ultimamodificacion').addClass('d-none');
          $('#alert-ultimamodificacion').html('');
          $('#modal-close-garantias-eliminar').click(); 
          $('#tabla-garantias').DataTable().ajax.reload();
          lista_garantias_cliente({{ $garantias->idcliente }});
          $('#btn-delete-garantia').addClass('d-none');
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Eliminar Garantia</h5>
        <button type="button" class="btn-close" id="modal-close-garantias-eliminar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
          <i class="fa-solid fa-triangle-exclamation"></i> ¿Esta seguro de eliminar la garantia?<br>
          <b>"{{$garantias->descripcion}}"</b>
        </div>
        <label class="mt-1" style="background-color: #636363;
          color: #fff;
          width: 100%;
          border-radius: 5px;
          padding: 0px 5px;
          margin-bottom: 5px;">Aprobación</label>
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
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-trash"></i> Eliminar</button>
    </div>
</form>   
<script>
    sistema_select2({ input:'#idresponsable' });
</script>