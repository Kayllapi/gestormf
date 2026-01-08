<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/valorizaciondescuento/'.$valorizacion->id) }}',
          method: 'DELETE',
          data:{
              view: 'eliminar'
          }
      },
      function(resultado){
          $('#modal-close-valorizaciongarantia-eliminar').click(); 
          
          lista_valorizacion_garantia();
          load_create_tipogarantia()
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Eliminar Valorización Descuento</h5>
        <button type="button" class="btn-close" id="modal-close-valorizaciongarantia-eliminar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
          <i class="fa-solid fa-triangle-exclamation"></i> ¿Esta seguro de eliminar la valorización descuento?<br>
          <b>"{{$valorizacion->detalle_descuento}}"</b>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-trash"></i> Eliminar</button>
    </div>
</form>   