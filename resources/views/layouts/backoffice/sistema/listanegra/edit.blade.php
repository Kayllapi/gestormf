<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/listanegra/'.$listanegra->id) }}',
        method: 'PUT',
          data:{
              view: 'editar',
          }
      },
      function(resultado){
          $('#tabla-listanegra').DataTable().ajax.reload();
          $('#modal-close-listanegra-registrar').click(); 
      },this)"> 
    <div class="modal-header">
        <h5 class="modal-title">Cambiar Estado de Cliente</h5>
        <button type="button" class="btn-close" id="modal-close-listanegra-registrar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row justify-content-center">
            <div class="col-sm-12 ">
                <label>Cliente:</label>
                <input type="text" class="form-control" disabled value="{{ $listanegra->identificacion }} - {{ $listanegra->db_cliente }}">
            </div>
            <div class="col-sm-12">
                <label>Motivo</label>
                <input type="text" id="motivo" disabled class="form-control" value="{{ $listanegra->motivo }}">
            </div>
            <div class="col-sm-12 col-md-6">
                <label>Fecha Registro:</label>
                <input type="date" class="form-control" disabled value="{{ $listanegra->fecharegistro }}">
            </div>
            <div class="col-sm-12 col-md-6">
                <label class="chk mt-4">
                    <input type="checkbox" id="idestado">
                    <span class="checkmark"></span>
                    QUITAR DE LISTA
                </label>
            </div>
            <div class="col-sm-12">
                <div class="alert mb-1 mt-1 text-start py-0" style="background-color: #ffffff;border: 1px solid grey;">
                    <b>REGISTRADO POR:</b> <span style="font-weight: normal;">{{ $responsable->nombrecompleto }}</span>
                </div>
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
</script>  
