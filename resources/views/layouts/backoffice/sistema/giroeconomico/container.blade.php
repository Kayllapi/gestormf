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
      let link = "/backoffice/194/giroeconomico/0/edit?view=pdf&idtipo_giro_economico="+idtipo_giro_economico+"&estado="+estado+"#zoom=90";
      $('#iframegiro').attr('src',link);
    }
  </script>
    <iframe src="" id="iframegiro" frameborder="0" width="100%" height="600px"></iframe>
</div>

