<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/responsableaprobacion') }}',
        method: 'POST',
        data:{
            view: 'registrar'
        }
    },
    function(resultado){
        lista_giro();
        load_nuevo_giro();
    },this)"> 
    <div class="modal-body">
       <div class="row justify-content-center">
          <div class="col-sm-12 col-md-5">
           <div class="row">
             <div class="col-sm-12">
                <label>Reponsable</label>
                <select class="form-control" id="idresponsable">
                  <option></option>
                  @foreach($usuarios as $value)
                    <option value="{{ $value->id }}">{{ $value->nombrecompleto }}</option>
                  @endforeach
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
sistema_select2({ input:'#idresponsable' });
</script>