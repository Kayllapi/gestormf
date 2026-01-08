<form action="javascript:;" 
        onsubmit="callback({
            route: '{{ url('backoffice/'.$tienda->id.'/permisos') }}',
            method: 'POST',
            data:{
                view: 'registrar',
            }
        },
        function(resultado){
            $('#tabla-permisos').DataTable().ajax.reload();
            $('#modal-close-permisos-registrar').click();
        },this)"> 
    <div class="modal-header">
        <h5 class="modal-title">REGISTRAR CARGO/PERMISO</h5>
        <button type="button" class="btn-close" id="modal-close-permisos-registrar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-12 col-md-8">
                <label>Rango</label>
                <input type="number" id="rango" class="form-control">
                <label>Nombre Cargo</label>
                <input type="text" id="nombre" class="form-control">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form>
