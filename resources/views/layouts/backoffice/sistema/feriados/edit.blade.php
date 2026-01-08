<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/feriados/'.$feriado->id) }}',
        method: 'PUT',
          data:{
              view: 'editar',
          }
      },
      function(resultado){
          lista_feriado();
          load_nuevo_feriado();
      },this)"> 

    <div class="modal-body">
      <div class="row justify-content-center">
        <div class="col-sm-12 col-md-6">
          <div class="row">
            <label class="col-sm-3 col-form-label">Fecha:</label>
            <div class="col-sm-6">
              <input type="date" class="form-control" id="fecha_feriado" value="{{ $feriado->fecha_feriado }}">
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">Motivo:</label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="motivo_feriado" value="{{ $feriado->motivo_feriado }}">
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-3"></label>
            <div class="col-sm-6">
              <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS</button>
              <button type="button" onclick="eliminar_feriado()" class="btn btn-danger"><i class="fa-solid fa-trash"></i> ELIMINAR</button>
            </div>
          </div>
          
         
        </div>
      </div>
    </div>
</form>  
<script>

  function eliminar_feriado(){
    modal({ route:"{{url('backoffice/'.$tienda->id.'/feriados/'.$feriado->id.'/edit?view=eliminar')}}" });  
  }
</script>    