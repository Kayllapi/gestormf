<div class="modal-header">
    <h5 class="modal-title">GIRO ECONOMICO</h5>
    <button type="button" class="btn-close" id="modal-close-usuario-registrar" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
  <script>
    crea_iframe();
    function crea_iframe(){
      let idtipo_giro_economico = $('#idtipo_giro_economico').val();
      let estado = $('#estado').val();
      // La agencia sale de la validada por el controlador ({{ (int) $agencia->id }}),
      // no de una fija: antes estaba 194 hardcodeado y el PDF salia siempre de esa
      // agencia, sin importar desde cual se abria.
      let link = "{{url('backoffice')}}/{{ (int) $agencia->id }}/giroeconomico/0/edit?view=pdf&idagencia={{ (int) $agencia->id }}&idtipo_giro_economico="+idtipo_giro_economico+"&estado="+estado+"#zoom=100";
      $('#iframegiro').attr('src',link);
    }
  </script>
    <iframe src="" id="iframegiro" frameborder="0" width="100%" 
        style="height: calc(100vh - 62px)"></iframe>
</div>

