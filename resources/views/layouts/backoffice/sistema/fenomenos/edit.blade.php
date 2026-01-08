<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$idtienda.'/fenomenos/'.$fenomeno->id) }}',
          method: 'PUT',
          data:{
              view: 'editar',
              idtienda: {{$tienda->id}}
          }
      },
      function(resultado){
        lista_fenomenos();
        load_nuevo_fenomenos();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Editar Fenomenos</h5>
    </div>
    <div class="modal-body">
      <div class="row justify-content-center">
        <div class="col-sm-12 col-md-5">
          <div class="row">
            <div class="col-sm-12">
                <label>Nombre</label>
                <input type="text" class="form-control" id="nombre" value="{{ $fenomeno->nombre }}">
            </div>
            <div class="col-sm-12">
                <label>Estado:</label>
                <select class="form-control" id="estado">
                  <option value="HABILITADO">HABILITADO</option>
                  <option value="BLOQUEADO">BLOQUEADO</option>
                </select>
            </div>
            <div class="col-sm-12 mt-2">
              <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
              <button type="button" onclick="eliminar_fenomenos()" class="btn btn-danger"><i class="fa-solid fa-trash"></i> ELIMINAR</button>
            </div>
          </div>
        </div>
      </div>
        
    </div>
    <div class="modal-footer">
      
        
    </div>
</form>
<script>
  @include('app.nuevosistema.select2',['input'=>'#estado', 'val' => $fenomeno->estado ])
  
  function eliminar_fenomenos(){
    modal({ route:"{{url('backoffice/'.$tienda->id.'/fenomenos/'.$fenomeno->id.'/edit?view=eliminar')}}" });  
  }
</script>