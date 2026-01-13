<div class="modal-header">
    <h5 class="modal-title">
      Margen Previsto / Descuento de Venta
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2" id="form-margendescuento-result">
          </div>
        </div>
      </div>
  </div>
</div>
<script>
    editar_select();
    function editar_select(e) { 
        pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/margendescuento/0/edit?view=editar", result:'#form-margendescuento-result'});
    }
</script>  

