<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$idtienda.'/tarifariojoyas/'.$tarifario->id) }}',
          method: 'PUT',
          data:{
              view: 'editar',
              idtienda: {{$tienda->id}}
          }
      },
      function(resultado){
          $('#modal-close-tarifariojoyas-editar').click(); 
          $('#tabla-tarifariojoyas').DataTable().ajax.reload();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Editar Tarifario Joyas</h5>
        <button type="button" class="btn-close" id="modal-close-tarifariojoyas-editar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
              <label>Tipo de Oro</label>
              <input type="text" class="form-control" id="tipo" value="{{ $tarifario->tipo }}">
          </div>
          <div class="col-sm-12">
              <label>Precio Por Gramo (S/)</label>
              <input type="number" class="form-control" value="{{ $tarifario->precio }}" id="precio" step="any">
          </div>
          <div class="col-sm-12">
              <label>Cobertura de C.(%)</label>
              <input type="number" class="form-control" value="{{ $tarifario->cobertura }}" id="cobertura" step="any">
          </div>
        </div>
    </div>
    <div class="modal-footer">
      
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form>     