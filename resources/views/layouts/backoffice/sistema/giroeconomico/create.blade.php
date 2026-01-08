<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/giroeconomico') }}',
        method: 'POST',
        data:{
            view: 'registrar'
        }
    },
    function(resultado){
        lista_giro();
        load_nuevo_giro();
    },this)"> 
<!--     <div class="modal-header">
        <h5 class="modal-title">Registra Giro Económico</h5>
        <button type="button" class="btn-close" id="modal-close-giroeconomico-registrar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div> -->
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
                <input type="text" class="form-control" id="nombre">
             </div>
             <div class="col-sm-6">
                <label>Margen de Vta. Máximo (%)</label>
                <input type="number" class="form-control" value="0.00" id="porcentaje" step="any">
                <label>Estado:</label>
                <select class="form-control" id="estado">
                  <option value="HABILITADO">HABILITADO</option>
                  <option value="BLOQUEADO">BLOQUEADO</option>
                </select>
            </div>
             <div class="col-sm-12 mt-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
               <button type="button" class="btn btn-danger" 
                    onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/giroeconomico/0/edit?view=container')}}', size: 'modal-fullscreen' })">
              <i class="fa-solid fa-file-pdf"></i></button>
             </div>
            </div>
          </div>
       </div>
    </div>
</form>  
<script>
@include('app.nuevosistema.select2',['input'=>'#idtipo_giro_economico'])
@include('app.nuevosistema.select2',['input'=>'#estado', 'val' => 'HABILITADO' ])
$("#idtipo_giro_economico").on("change", function(e) {
  lista_giro();
});
$("#estado").on("change", function(e) {
  lista_giro();
});
</script>