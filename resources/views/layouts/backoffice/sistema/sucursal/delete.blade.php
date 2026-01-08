<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/sucursal/'.$s_sucursal->id) }}',
          method: 'DELETE',
          data:{
              view: 'eliminar'
          }
      },
      function(resultado){
          $('#modal-close-sucursal-eliminar').click(); 
          $('#tabla-sucursal').DataTable().ajax.reload();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Eliminar Sucursal</h5>
        <button type="button" class="btn-close" id="modal-close-sucursal-eliminar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
          <i class="fa-solid fa-triangle-exclamation"></i> ¿Esta seguro de eliminar la sucursal?<br>
          <b>"{{$s_sucursal->nombre}}"</b>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-trash"></i> Eliminar</button>
    </div>
</form>   