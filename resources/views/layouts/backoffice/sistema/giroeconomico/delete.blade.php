<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/giroeconomico/'.$giro->id) }}',
          method: 'DELETE',
          data:{
              view: 'eliminar',
              idagencia: {{ (int) $agencia->id }}
          }
      },
      function(resultado){
          lista_giro();
          load_nuevo_giro();
          $('#modal-close-giroeconomico-eliminar').click();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Eliminar Giro Económico</h5>
        <button type="button" class="btn-close" id="modal-close-giroeconomico-eliminar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
          <i class="fa-solid fa-triangle-exclamation"></i> ¿Esta seguro de eliminar el giro económico?<br>
          <b>"{{$giro->nombre}}"</b><br>
          <small>De la agencia <b>{{$agencia->nombreagencia}}</b>. Esta accion no se puede deshacer.</small>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-trash"></i> Eliminar</button>
    </div>
</form>   