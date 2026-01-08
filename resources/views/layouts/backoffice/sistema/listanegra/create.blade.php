<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/listanegra') }}',
          method: 'POST',
          data:{
              view: 'registrar',
          }
      },
      function(resultado){
          $('#tabla-listanegra').DataTable().ajax.reload();
          $('#modal-close-listanegra-registrar').click(); 
      },this)"> 
    <div class="modal-header">
        <h5 class="modal-title">REGISTRO LISTA NEGRA</h5>
        <button type="button" class="btn-close" id="modal-close-listanegra-registrar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <label>Cliente:</label>
                <select class="form-control" id="idcliente">
                    <option></option>
                </select>
            </div>
            <div class="col-sm-12 col-md-8">
                <label>Motivo</label>
                <input type="text" id="motivo" class="form-control">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form>                      
<script>
    sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idcliente' });
</script>
