<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/refinanciamiento/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view: 'refinanciamiento',
          }
      },
      function(resultado){
        lista_credito();
        $('#close_opcionescredito').click();
      },this)"> 
  <div class="modal-header">
    <h5 class="modal-title">REFINANCIAR</h5>
    <button type="button" class="btn-close" id="close_opcionescredito" data-bs-dismiss="modal" aria-label="Close"></button>
  </div>
  <div class="modal-body">
    <div class="row" style="font-size: 14px;padding: 7px;">
      <div class="col-md-6"><b>CLIENTE:</b> {{ $usuario->nombrecompleto }}</div>
      <div class="col-md-6" style="text-align: right;"><b>PRODUCTO:</b> {{ $credito->nombreproductocredito }}</div>
    </div>

                <div class="row">
                  <label class="col-sm-5 col-form-label" style="text-align: right;">Monto a Refinanciar</label>
                  <div class="col-sm-2">
                    <input type="number" step="any" class="form-control" disabled id="monto_solicitado" value="{{$cronograma['cuota_pendiente'] }}" onclick="showtasa()" onkeyup="showtasa()">
                  </div>
                </div>
    <div class="col-sm-12 mt-2" style="text-align: center;">
      <button type="submit" class="btn btn-success me-1" onclick="modal_credito('solicitud')">REFINANCIAR EL CRÉDITO</button>
    </div><br>
                        <p class="text-center" 
                           style="background-color: #343a40;
                                  padding: 10px;
                                  border-radius: 5px;
                                  color: #fff;
                                  width: 100%;
                                  margin: auto;">¡¡Esta seguro de refinanciar!!.</p>
  </div>

</form>
<style>
  
</style>

<script>

</script>