<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/cvasignacioncapital/'.$asignacioncapital->id) }}',
          method: 'PUT',
          data:{
              view: 'recepcionar',
          }
      },
      function(resultado){
          lista_asignacioncapital();
          $('#cont_recepcionar').css('display','none');
          $('#cont_voucher').css('display','none');  
          $('#modal-close-asignacioncapital-valid').click();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Recepcionar</h5>
        <button type="button" class="btn-close" id="modal-close-asignacioncapital-valid" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <label class="mt-1" style="background-color: #636363;
          color: #fff;
          width: 100%;
          border-radius: 5px;
          padding: 0px 5px;
          margin-bottom: 5px;">Aprobación</label>
              <div class="mb-1">
                  <label>Responsable *</label>
                  <select class="form-select" id="idresponsable">
                      <option value=""></option>
                      @foreach($usuarios as $value)
                      <option value="{{$value->id}}" idpermiso="{{$value->idpermiso}}">{{$value->nombrecompleto}} ({{$value->nombrepermiso}})</option>
                      @endforeach
                  </select>
                  <input type="hidden" id="idpermiso">
              </div>
              <div class="mb-1">
                  <label>Contraseña *</label>
                  <input type="password" class="form-control" id="responsableclave">
              </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Recepcionar</button>
    </div>
</form>  
<script>
    sistema_select2({ input:'#idresponsable' });
    $("#idresponsable").on("change", function(e) {
        var idpermiso = $('#idresponsable :selected').attr('idpermiso');
        $('#idpermiso').val(idpermiso);
    });
</script> 