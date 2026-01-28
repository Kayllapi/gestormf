<div id="carga_cambiar_estado">
<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/garantiarecoger/'.$credito_garantia->id) }}',
          method: 'PUT',
          data:{
              view: 'entregar',
              idcredito: '{{$credito_garantia->idcredito}}'
          }
      },
      function(resultado){
          $('#modal-close-garantiarecoger-entrega').click(); 
          ticketgarantia();
          lista_credito();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Entrega de Garantía Prendaria</h5>
        <button type="button" class="btn-close" id="modal-close-garantiarecoger-entrega" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
          <i class="fa-solid fa-triangle-exclamation"></i> ¿Esta seguro de entregar la garantia prendaria?
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Entregar</button>
    </div>
</form>   
</div>