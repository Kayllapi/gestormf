<div class="modal-header">
    <h5 class="modal-title">
      Penalidades y Comisiones
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2" id="form-credito-result">
          </div>
        </div>
      </div>
  </div>
</div>
<script>
  editar_select();
  function editar_select(e) { 
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/penalidadcomision/0/edit?view=editar", result:'#form-credito-result'});
  }
</script>  

