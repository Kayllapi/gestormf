<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/banco') }}',
        method: 'POST',
        data:{
            view: 'registrar',
            idagencia: {{ (int) $agencia->id }}
        }
    },
    function(resultado){
        lista_banco();
        load_nuevo_banco();
    },this)"> 
    <div class="modal-body">
      <div class="row justify-content-center">
        <div class="col-sm-12 col-md-6">
          
           <div class="row">
            <label class="col-sm-3 col-form-label">Nombre:</label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="nombre">
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">Cuenta:</label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="cuenta">
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-3"></label>
            <div class="col-sm-6">
              <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> GUARDAR</button>
            </div>
          </div>
          
         
        </div>
      </div>
    </div>
</form>  
<script>

</script>    