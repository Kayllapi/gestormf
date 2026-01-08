<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/tarifariojoyas/'.$tarifario->id) }}',
          method: 'DELETE',
          data:{
              view: 'eliminar'
          }
      },
      function(resultado){
          $('#modal-close-tarifariojoyas-eliminar').click(); 
          $('#tabla-tarifariojoyas').DataTable().ajax.reload();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Eliminar Tarifa</h5>
        <button type="button" class="btn-close" id="modal-close-tarifariojoyas-eliminar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
          <i class="fa-solid fa-triangle-exclamation"></i> ¿Esta seguro de eliminar la tarifa de joya?<br>
          <b>"{{$tarifario->tipo}}"</b>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-trash"></i> Eliminar</button>
    </div>
</form>   