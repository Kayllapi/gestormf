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
            <div class="col-sm-12">
                <div class="mt-2 bg-primary subtitulo">Aprobación</div>
                <div class="mb-1">
                    <label>Responsable *</label>
                    <select class="form-select" id="idresponsable">
                        <option value=""></option>
                        @foreach($usuarios as $value)
                        <option value="{{$value->id}}">{{$value->nombrecompleto}} ({{$value->nombrepermiso}})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <label>Contraseña *</label>
                    <input type="password" class="form-control" id="responsableclave">
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form>                      
<script>
    sistema_select2({ input:'#idresponsable' });
    sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idcliente' });
</script>
