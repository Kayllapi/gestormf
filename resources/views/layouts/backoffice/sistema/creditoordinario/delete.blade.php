<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/creditoordinario/'.$credito->id) }}',
          method: 'DELETE',
          data:{
              view: 'eliminar'
          }
      },
      function(resultado){
        $('#modal-close-credito-eliminar').click(); 
        lista_garantias_cliente();
        load_nuevo_credito();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Eliminar </h5>
        <button type="button" class="btn-close" id="modal-close-credito-eliminar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
          <i class="fa-solid fa-triangle-exclamation"></i> ¿Esta seguro de eliminar el producto?<br>
          <b>"{{$credito->nombre}}"</b>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-trash"></i> Eliminar</button>
    </div>
</form>   