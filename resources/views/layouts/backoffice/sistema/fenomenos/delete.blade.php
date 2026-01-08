<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/fenomenos/'.$fenomeno->id) }}',
          method: 'DELETE',
          data:{
              view: 'eliminar'
          }
      },
      function(resultado){
          lista_fenomenos();
          load_nuevo_fenomenos();
          $('#modal-close-fenomenos-eliminar').click();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Eliminar Fenomeno</h5>
        <button type="button" class="btn-close" id="modal-close-fenomenos-eliminar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
          <i class="fa-solid fa-triangle-exclamation"></i> ¿Esta seguro de eliminar el fenomeno?<br>
          <b>"{{$fenomeno->nombre}}"</b>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-trash"></i> Eliminar</button>
    </div>
</form>   