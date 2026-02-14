<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/garantiaremateagencia/0') }}',
          method: 'PUT',
          data:{
              view: 'registrar_precio_liquidacion',
              idcredito: $credito->id,
          }
      },
      function(resultado){
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Precio de Liquidación</h5>
        <button type="button" class="btn-close" id="modal-close-garantias-modificar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row">
            <label for="cliente" class="col-sm-5 col-form-label">Precio de Liquidación *</label>
            <div class="col-sm-7">
                <input type="number" class="form-control" id="registrar_precio_liquidacion" value="">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"> Ingresar</button>
    </div>
</form>   