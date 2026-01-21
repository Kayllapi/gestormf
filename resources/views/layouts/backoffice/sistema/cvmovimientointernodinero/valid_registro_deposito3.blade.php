<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/cvmovimientointernodinero/'.$idmovimientointernodinero) }}',
          method: 'PUT',
          data:{
              view: 'valid_registro_deposito3'
          }
      },
      function(resultado){
          //submit_registro_deposito3();
          lista_movimientointernodinero_deposito3();
          load_nuevo_movimientointernodinero_deposito3();
          $('#modal-close-movimientointernodinero-valid').click();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Confirmar</h5>
        <button type="button" class="btn-close" id="modal-close-movimientointernodinero-valid" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="mt-2 bg-primary subtitulo">Aprobación</div>
              <div class="mb-1">
                  <label>Responsable *</label>
                  <select class="form-select" id="idresponsable">
                      <option value=""></option>
                      @foreach($usuarios as $value)
                      <option value="{{$value->id}}" idpermiso="{{$value->idpermiso}}">{{$value->nombrecompleto}} ({{$value->nombrepermiso}})</option>
                      @endforeach
                  </select>
              </div>
              <input type="hidden" id="idresponsable_permiso">
              <div class="mb-1">
                  <label>Contraseña *</label>
                  <input type="password" class="form-control" id="responsableclave">
              </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Confirmar</button>
    </div>
</form>  
<script>
    sistema_select2({ input:'#idresponsable' });
  
    $("#idresponsable").on("change", function(e) {
        $('#idresponsable_permiso').val($('#idresponsable :selected').attr('idpermiso'));
    });
  
</script> 