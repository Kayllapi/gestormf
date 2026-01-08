<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/creditoprendatario/'.$credito->id) }}',
        method: 'PUT',
          data:{
              view: 'editar',
          }
      },
      function(resultado){
          lista_garantias_cliente();
        load_nuevo_credito();
      },this)"> 
<!--     <div class="modal-header">
        <h5 class="modal-title">Modificar</h5>
        <button type="button" class="btn-close" id="modal-close-editar-credito" data-bs-dismiss="modal" aria-label="Close"></button>
    </div> -->
    <div class="modal-body">
      <div class="row justify-content-center">
        <div class="col-sm-12 col-md-8">
          
          <div class="row">
            <label class="col-sm-3 col-form-label">Nombre Producto</label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="nombre" value="{{ $credito->nombre }}">
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">Modalidad de Calculo</label>
            <div class="col-sm-6">
              <select class="form-control" id="modalidad">
                <option value="Interes Simple">Interes Simple</option>
                <option value="Interes Compuesto">Interes Compuesto</option>
              </select>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">Garantia Prendaria</label>
            <div class="col-sm-6">
              <select class="form-control" id="garantiaprendatario">
                <option value="SI">SI</option>
                <option value="NO">NO</option>
              </select>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">Con Evaluación</label>
            <div class="col-sm-6">
              <select class="form-control" id="conevaluacion">
                <option value="SI">SI</option>
                <option value="NO">NO</option>
              </select>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">Estado</label>
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
              <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS</button>
              <!--button type="button" onclick="eliminar_credito()" class="btn btn-danger"><i class="fa-solid fa-trash"></i> ELIMINAR</button-->
            </div>
          </div>
          
         
        </div>
      </div>
    </div>
</form>  
<script>
  @include('app.nuevosistema.select2',['input'=>'#modalidad', 'val' => $credito->modalidad ])
  @include('app.nuevosistema.select2',['input'=>'#garantiaprendatario', 'val' => $credito->garantiaprendatario])
  @include('app.nuevosistema.select2',['input'=>'#conevaluacion', 'val' => $credito->conevaluacion])
  @include('app.nuevosistema.select2',['input'=>'#estado', 'val' => $credito->estado])
  function eliminar_credito(){
    modal({ route:"{{url('backoffice/'.$tienda->id.'/creditoprendatario/'.$credito->id.'/edit?view=eliminar')}}" });  
  }
</script>    