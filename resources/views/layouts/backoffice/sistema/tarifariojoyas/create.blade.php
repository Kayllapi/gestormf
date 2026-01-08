<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/tarifariojoyas') }}',
        method: 'POST',
        data:{
            view: 'registrar'
        }
    },
    function(resultado){
        $('#tabla-tarifariojoyas').DataTable().ajax.reload();
        $('#modal-close-tarifariojoyas-registrar').click();                                   
    },this)"> 
    <div class="modal-header">
        <h5 class="modal-title">Registra Tarifario</h5>
        <button type="button" class="btn-close" id="modal-close-tarifariojoyas-registrar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row">
           
          <div class="col-sm-12">
              <label>Tipo de Oro</label>
              <input type="text" class="form-control" id="tipo">
          </div>
          <div class="col-sm-12">
              <label>Precio Por Gramo (S/)</label>
              <input type="number" class="form-control" value="0.00" id="precio" step="any">
          </div>
          <div class="col-sm-12">
              <label>Cobertura de C.(%)</label>
              <input type="number" class="form-control" value="0.00" id="cobertura" step="any">
          </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form>  