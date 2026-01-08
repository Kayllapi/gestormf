<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/banco/'.$feriado->id) }}',
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
            <label class="col-sm-3 col-form-label">Nombre:</label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="nombre" value="{{ $feriado->nombre }}">
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">Cuenta:</label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="cuenta" value="{{ $feriado->cuenta }}">
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">Estado:</label>
            <div class="col-sm-6">
              <select class="form-control" id="estado">
                <option value="ACTIVO">Activo</option>
                <option value="INACTIVO">Inactivo</option>
              </select>
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-3"></label>
            <div class="col-sm-6">
              <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS</button>
              <!--button type="button" onclick="eliminar_feriado()" class="btn btn-danger"><i class="fa-solid fa-trash"></i> ELIMINAR</button-->
            </div>
          </div>
          
         
        </div>
      </div>
    </div>
</form>  
<script>
  @include('app.nuevosistema.select2',['input'=>'#estado', 'val' => $feriado->estado])

  function eliminar_feriado(){
    modal({ route:"{{url('backoffice/'.$tienda->id.'/feriados/'.$feriado->id.'/edit?view=eliminar')}}" });  
  }
</script>    