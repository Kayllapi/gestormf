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
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" id="idestado">
                    <label class="form-check-label" for="idestado" style="margin-top:0px;">
                        QUITAR DE LISTA
                    </label>
                </div>
            </div>
        <div class="alert mb-1 text-start py-0" id="cont-ultimamodificacion" 
             style="background-color: #ffffff;border: 1px solid grey;">
            <b>ULTIMA MODIFICACIÓN:</b> {{ $responsable->nombrecompleto }}
        </div>
        <label class="mt-1" style="background-color: #636363;
          color: #fff;
          width: 100%;
          border-radius: 5px;
          padding: 0px 5px;
          margin-bottom: 5px;">Aprobación</label>
              <div class="mb-1">
                  <label>Responsable (Administración) *</label>
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
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form>                   
<script>
    sistema_select2({ input:'#idresponsable' });
</script>  
