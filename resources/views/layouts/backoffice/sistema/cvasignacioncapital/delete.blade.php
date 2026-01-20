<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/cvasignacioncapital/'.$asignacioncapital->id) }}',
          method: 'DELETE',
          data:{
              view: 'eliminar'
          }
      },
      function(resultado){
          lista_asignacioncapital();
          load_nuevo_asignacioncapital();
          $('#modal-close-asignacioncapital-eliminar').click();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Eliminar Gasto Administrativo y Operativo</h5>
        <button type="button" class="btn-close" id="modal-close-asignacioncapital-eliminar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
          <i class="fa-solid fa-triangle-exclamation"></i> ¿Esta seguro de eliminar el  Gasto Administrativo y Operativo?<br>
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
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-trash"></i> Eliminar</button>
    </div>
</form>  
<script>
    sistema_select2({ input:'#idresponsable' });
</script> 