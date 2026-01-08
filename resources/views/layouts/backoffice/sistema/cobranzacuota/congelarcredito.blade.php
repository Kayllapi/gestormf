<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/cobranzacuota') }}',
        method: 'POST',
        data:{
            view: 'congelarcredito',
            idcredito: '{{$credito->id}}',
        }
    },
    function(resultado){
        show_data_credito({{$credito->id}});
        $('#close_opcionescredito').click();
    },this)"> 
    <div class="modal-header">
        <h5 class="modal-title">Congelar Crédito</h5>
        <button type="button" class="btn-close" id="close_opcionescredito" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
              <div class="mb-1">
                  <label>Responsables (Administración) *</label>
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
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Congelar Crédito</button>
    </div>
</form>  
<script>
    sistema_select2({ input:'#idresponsable' });
</script>    