<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$idtienda.'/giroeconomico/'.$giro->id) }}',
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
        <h5 class="modal-title">Editar Giro Económico</h5>
    </div>
    <div class="modal-body">
      <div class="row justify-content-center">
        <div class="col-sm-12 col-md-6">
          <div class="row">
            <div class="col-sm-6">
                <label>Tipo de Giro</label>
                <select class="form-control" id="idtipo_giro_economico">
                  <option value=""></option>
                  @foreach($tipo_giro_economico as $value)
                  <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                  @endforeach
                </select>
                <label>Giro Económico</label>
                <input type="text" class="form-control" id="nombre" value="{{ $giro->nombre }}">
            </div>
            <div class="col-sm-6">
                <label>Margen de Vta. Máximo (%)</label>
                <input type="number" class="form-control" value="{{ $giro->porcentaje }}" id="porcentaje" step="any">
                <label>Estado:</label>
                <select class="form-control" id="estado">
                  <option value="HABILITADO">HABILITADO</option>
                  <option value="BLOQUEADO">BLOQUEADO</option>
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
  @include('app.nuevosistema.select2',['input'=>'#idtipo_giro_economico', 'val' => $giro->idtipo_giro_economico ])
  @include('app.nuevosistema.select2',['input'=>'#estado', 'val' => $giro->estado ])
  
  function eliminar_giro(){
    modal({ route:"{{url('backoffice/'.$tienda->id.'/giroeconomico/'.$giro->id.'/edit?view=eliminar')}}" });  
  }
</script>