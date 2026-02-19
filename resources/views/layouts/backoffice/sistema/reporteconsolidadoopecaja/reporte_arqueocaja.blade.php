    <div class="modal-header">
        <h5 class="modal-title">Reporte de Arqueo de Caja</h5>
        <button type="button" class="btn-close" id="modal-close-reporteconsolidadoopecaja-valid" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
          <div class="row">
              <label for="fecha_inicio" class="col-sm-2 col-form-label">AGENCIA:</label>
              <div class="col-sm-7">
                  <select class="form-control" id="idagencia_reporte_arqueocaja" disabled>
                    <option></option>
                    @foreach($agencias as $value)
                        <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                    @endforeach
                  </select>
              </div>
          </div>
          <div class="row">
              <label for="fecha_inicio" class="col-sm-2 col-form-label">FECHA:</label>
              <div class="col-sm-3">
                  <input type="date" class="form-control" value="{{now()->format('Y-m-d')}}" id="fecha_reporte_arqueocaja">
              </div>
              <div class="col-sm-2">
                  <button type="button" class="btn btn-success" onclick="reporte_arqueocaja_pdf()"><i class="fa-solid fa-search"></i> FILTRAR</button>
              </div>
          </div>
          <div class="col-sm-12">
            <div class="card">
            <iframe id="iframe_reporte_arqueocaja" frameborder="0" width="100%" height="600px"></iframe>
            </div>
          </div>
    </div>

<script>
    sistema_select2({ input:'#idagencia_reporte_arqueocaja',val:'{{$agencia->id}}' });
  
    reporte_arqueocaja_pdf();
    function reporte_arqueocaja_pdf(){
        let fecha_reporte_arqueocaja = $('#fecha_reporte_arqueocaja').val();
        let idagencia_reporte_arqueocaja = $('#idagencia_reporte_arqueocaja').val();
        $('#iframe_reporte_arqueocaja').attr('src','{{ url('/backoffice/'.$tienda->id.'/reporteconsolidadoopecaja/0/edit?view=reporte_arqueocaja_pdf') }}&fecha_reporte_arqueocaja='+fecha_reporte_arqueocaja+'&idagencia_reporte_arqueocaja='+idagencia_reporte_arqueocaja+'#zoom=100');
    }
</script> 