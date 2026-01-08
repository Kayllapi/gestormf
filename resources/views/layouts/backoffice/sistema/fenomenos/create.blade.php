<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/fenomenos') }}',
        method: 'POST',
        data:{
            view: 'registrar'
        }
    },
    function(resultado){
        lista_fenomenos();
        load_nuevo_fenomenos();
    },this)"> 
    <div class="modal-body">
       <div class="row justify-content-center">
          <div class="col-sm-12 col-md-5">
           <div class="row">
             <div class="col-sm-12">
                <label>Nombre</label>
                <input type="text" class="form-control" id="nombre">
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
               
             </div>
            </div>
          </div>
       </div>
    </div>
</form>  
<script>
@include('app.nuevosistema.select2',['input'=>'#estado', 'val' => 'HABILITADO' ])
$("#estado").on("change", function(e) {
  lista_fenomenos();
});
</script>