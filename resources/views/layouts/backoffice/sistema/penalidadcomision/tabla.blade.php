<div class="modal-header">
    <h5 class="modal-title">
      Penalidades y Comisiones
      @if($agencias->count()>1)
      <button type="button" class="btn btn-primary mb-1" onclick="copiar_configuracion()">
        <i class="fa fa-copy"></i> Copiar Configuración
      </button>
      @endif
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2">
            <div class="row">
              <label for="idagencia" class="col-sm-2 col-form-label" style="text-align: right;">AGENCIA</label>
              <div class="col-sm-5">
                <select class="form-control" id="idagencia">
                  @foreach($agencias as $value)
                    <option value="{{ $value->id }}" {{ (int) $value->id == (int) $idtienda_seleccionada ? 'selected' : '' }}>{{ $value->nombreagencia }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-sm-5" style="text-align: right;">
                <span class="badge" style="background-color: #bcbcbc;color: #000;">
                  La configuración se visualiza y guarda sobre la agencia seleccionada
                </span>
              </div>
            </div>
            <div id="form-credito-result"></div>
          </div>
        </div>
      </div>
  </div>
</div>
<script>
  editar_select();
  $("#idagencia").on("change", function () {
    editar_select();
  });
  function editar_select(e) {
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/penalidadcomision/0/edit?view=editar&idagencia="+$('#idagencia').val(), result:'#form-credito-result'});
  }
  function copiar_configuracion(){
    modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/penalidadcomision/0/edit?view=copiar&idagencia="+$('#idagencia').val(), size: 'modal-sm' })
  }
</script>