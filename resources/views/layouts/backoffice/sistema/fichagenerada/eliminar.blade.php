<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/fichagenerada/'.$idcredito_garantia) }}',
          method: 'PUT',
          data:{
              view: 'eliminar',
              idagencia: $('#idagencia').val(),
          }
      },
      function(resultado){
          lista_credito();
          $('#modal-close-garantias-modificar').click();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Eliminar</h5>
        <button type="button" class="btn-close" id="modal-close-garantias-modificar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        @if($credito->idliquidaciongarantia==2)
            <div class="alert alert-danger">
              <i class="fa-solid fa-triangle-exclamation"></i> ¡La cuenta ya fue pagada!<br>
            </div>
        @else
            <div class="alert alert-danger">
              <i class="fa-solid fa-triangle-exclamation"></i> ¿Esta seguro de eliminar de la lista de remates?<br>
            </div>
                <div class="mt-2 bg-primary subtitulo">Aprobación</div>
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
        @endif
    </div>
        @if($credito->idliquidaciongarantia==1)
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-trash-can"></i> Eliminar</button>
    </div>
        @endif
</form>   
<script>
    sistema_select2({ input:'#idresponsable' });
</script>