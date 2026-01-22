<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/garantias/'.$cliente->id) }}',
          method: 'PUT',
          data:{
              view: 'modificar'
          }
      },
      function(resultado){
          $('#modal-close-garantias-modificar').click(); 
          $('#idresponsable_modificado').val(resultado.idresponsable); 
          autorizar_edicion_depositario({{$val}});
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Modificar Garantía</h5>
        <button type="button" class="btn-close" id="modal-close-garantias-modificar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
          <i class="fa-solid fa-triangle-exclamation"></i> ¿Esta seguro de modificar la garantía?<br>
          <b>"{{$cliente->nombrecompleto}}"</b>
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
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Modificar</button>
    </div>
</form>   
<script>
    sistema_select2({ input:'#idresponsable' });
</script>