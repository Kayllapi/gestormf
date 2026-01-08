<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$idtienda.'/responsableaprobacion/'.$responsable->id) }}',
          method: 'PUT',
          data:{
              view: 'editar',
              idtienda: {{$tienda->id}}
          }
      },
      function(resultado){
        lista_giro();
        load_nuevo_giro();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Editar Responsable</h5>
    </div>
    <div class="modal-body">
      <div class="row justify-content-center">
        <div class="col-sm-12 col-md-5">
          <div class="row">
            <div class="col-sm-12">
                <label>Reponsable</label>
                <select class="form-control" id="idresponsable">
                  <option value=""></option>
                  @foreach($usuarios as $value)
                    <option value="{{ $value->id }}">{{ $value->nombrecompleto }}</option>
                  @endforeach
                </select>
            </div>
            <div class="col-sm-12 mt-2">
              <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
              <button type="button" onclick="eliminar_giro()" class="btn btn-danger"><i class="fa-solid fa-trash"></i> ELIMINAR</button>
            </div>
          </div>
        </div>
      </div>
        
    </div>
    <div class="modal-footer">
      
        
    </div>
</form>
<script>
  sistema_select2({ input:'#idresponsable', val:"{{ $responsable->idusers }}" });
  function eliminar_giro(){
    modal({ route:"{{url('backoffice/'.$tienda->id.'/responsableaprobacion/'.$responsable->id.'/edit?view=eliminar')}}" });  
  }
</script>