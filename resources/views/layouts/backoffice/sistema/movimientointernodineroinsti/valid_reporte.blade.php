<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/movimientointernodineroinsti/0') }}',
          method: 'PUT',
          data:{
              view: 'valid_reporte'
          }
      },
      function(resultado){
          exportar_pdf();
          $('#modal-close-movimientointernodineroinsti-valid').click();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Validar</h5>
        <button type="button" class="btn-close" id="modal-close-movimientointernodineroinsti-valid" data-bs-dismiss="modal" aria-label="Close"></button>
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
                      <option value="{{$value->id}}">{{$value->nombrecompleto}} ({{$value->nombrepermiso}})</option>
                      @endforeach
                  </select>
              </div>
              <div class="mb-1">
                  <label>Contraseña *</label>
                  <input type="password" class="form-control" id="responsableclave">
              </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Validar</button>
    </div>
</form>  
<script>
    sistema_select2({ input:'#idresponsable' });
</script> 