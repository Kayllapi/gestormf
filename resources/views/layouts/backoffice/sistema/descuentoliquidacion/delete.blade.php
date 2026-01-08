<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/descuentoliquidacion/'.$idcredito_descuentocuota) }}',
          method: 'DELETE',
          data:{
              view: 'eliminar'
          }
      },
      function(resultado){
          $('#cont_descuentosdecuotas').html('');
          $('#modal-close-descuentoliquidacion-eliminar').click(); 
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Eliminar Cuota Descuento</h5>
        <button type="button" class="btn-close" id="modal-close-descuentoliquidacion-eliminar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
          <i class="fa-solid fa-triangle-exclamation"></i> ¿Esta seguro de eliminar la Cuota Descuento?<br>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-trash"></i> Eliminar</button>
    </div>
</form>   