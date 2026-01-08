<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/gestioncobranza/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view: 'gestioncobranza',
          }
      },
      function(resultado){
        lista_credito();
        $('#modal-close-garantia-cliente').click();
      },this)"> 
    <div class="modal-header" style="border-bottom: 0;">
        <h5 class="modal-title">Compromiso de Pago </h5>
        <button type="button" class="btn-close text-white" id="modal-close-garantia-cliente" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
      <div class="row">
              <div class="col-md-10">
                <div class="row">
                  <label class="col-sm-5 col-form-label" style="text-align: right;">Cliente</label>
                  <div class="col-sm-7">
                    <input type="text" value="{{$credito->nombreclientecredito}}" class="form-control" id="cliente" disabled>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-5 col-form-label" style="text-align: right;">Fecha de Compromiso *</label>
                  <div class="col-sm-7">
                    <input type="date" class="form-control" id="fecha_compromiso">
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-5 col-form-label" style="text-align: right;">Comentario *</label>
                  <div class="col-sm-7">
                    <textarea class="form-control" cols="5" rows="3" id="comentario"></textarea>
                  </div>
                </div> 
                <div class="row mt-1">
                  <label class="col-sm-5 col-form-label" style="text-align: right;"></label>
                  <div class="col-sm-7">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Programar</button>
                  </div>
                </div> 
              </div>
      </div>
    </div>
</form>  
<script>
  
</script>    

