<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/creditoprendatario/0') }}',
          method: 'PUT',
          data:{
              view: 'diasdegracia',
          }
      },
      function(resultado){
        $('#modal-close-editar-credito').click(); 
      },this)"> 
    <div class="modal-header">
        <h5 class="modal-title">DIAS DE GRACIA</h5>
        <button type="button" class="btn-close" id="modal-close-editar-credito" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
      <div class="row justify-content-center">
        <div class="col-sm-12 col-md-8">
          
          <div class="row">
            <label class="col-sm-3 col-form-label">Días de Gracia</label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="dias" value="{{ $diasdegracia->dias }}">
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-3"></label>
            <div class="col-sm-6">
              <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS</button>
            </div>
          </div>
          
         
        </div>
      </div>
    </div>
</form>  
  