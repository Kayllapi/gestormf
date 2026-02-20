    <div class="modal-header">
        <h5 class="modal-title">Reporte de Control de Apertura y Cierre de Ope. de Caja</h5>
        <button type="button" class="btn-close" id="modal-close-controlaperturaopecaja-valid" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
          <div class="row">
              <label for="fecha_inicio" class="col-sm-2 col-form-label">FECHA:</label>
              <div class="col-sm-3">
                  <input type="date" class="form-control" value="{{now()->format('Y-m-d')}}" id="fecha_corte_reporte">
              </div>
              <div class="col-sm-2">
                  <button type="button" class="btn btn-success" onclick="reporte_pdf()"><i class="fa-solid fa-search"></i> FILTRAR</button>
              </div>
          </div>
          <div class="col-sm-12">
            <div class="card">
            <iframe id="iframe_reporte" frameborder="0" width="100%" 
        style="height: calc(100vh - 62px)"></iframe>
            </div>
          </div>
    </div>

<script>
    reporte_pdf();
    function reporte_pdf(){
        let fecha_corte_reporte = $('#fecha_corte_reporte').val();
        $('#iframe_reporte').attr('src','{{ url('/backoffice/'.$tienda->id.'/cvcontrolaperturaopecaja/0/edit?view=reporte_pdf') }}&fecha_corte_reporte='+fecha_corte_reporte+'#zoom=100');
    }
</script> 
  