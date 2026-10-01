<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/tarifario/'.$tarifario->id) }}',
          method: 'DELETE',
          data:{
              view: 'eliminar',
              idagencia: {{ (int) $agencia->id }}
          }
      },
      function(resultado){
        reiniciar_filtro_tarifario();
        lista_tarifario();
        load_nuevo_tarifario();
        $('#modal-close-credito-eliminar').click(); 
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Eliminar </h5>
        <button type="button" class="btn-close" id="modal-close-credito-eliminar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
          <i class="fa-solid fa-triangle-exclamation"></i> ¿Esta seguro de eliminar tarifario?<br>
          <b>"{{$tarifario->nombrecredito}}"</b><br>
          <small>De la agencia <b>{{$agencia->nombreagencia}}</b>. Esta accion no se puede deshacer.</small>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-trash"></i> Eliminar</button>
    </div>
</form>   