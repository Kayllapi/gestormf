<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/sucursal') }}',
        method: 'POST',
        data:{
            view: 'registrar'
        }
    },
    function(resultado){
        $('#tabla-sucursal').DataTable().ajax.reload();
        $('#modal-close-sucursal-registrar').click();                                   
    },this)"> 
    <div class="modal-header">
        <h5 class="modal-title">Registrar Agencia</h5>
        <button type="button" class="btn-close" id="modal-close-sucursal-registrar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="mb-1">
                <label>AGENCIA *</label>
                <input type="text" class="form-control" id="nombreagencia">
            </div>
            <div class="mb-1">
                <label>NOMBRE EMPRESA *</label>
                <input type="text" class="form-control" id="nombre">
            </div>
            <div class="mb-1">
                <label>REPRESENTANTE *</label>
                <input type="text" class="form-control" id="representante">
            </div>
            <div class="mb-1">
                <label>DIRECCIÓN *</label>
                <input type="text" class="form-control" id="direccion">
            </div>
            <div class="mb-1">
                <label>RUC*</label>
                <input type="text" class="form-control" id="ruc">
            </div>
            <div class="mb-1">
                <label data-bs-toggle="popover" 
                        data-bs-placement="right" 
                        data-bs-content="Puedes buscar por Distrito, Provincia ó Departamento.">Ubicación (Ubigeo) * 
                    <i class="fa-solid fa-circle-info"></i>
                </label>
                <select class="form-select" id="idubigeo">
                    <option></option>
                </select>
            </div>
            <div class="mb-1">
                <label>TELÉFONO *</label>
                <input type="text" class="form-control" id="telefono">
            </div>
            <div class="mb-1">
                <label>PÁGINA WEB *</label>
                <input type="text" class="form-control" id="paginaweb">
            </div>
            <div class="mb-1">
                <label>TIPO EMPRESA *</label>
                <input type="text" class="form-control" id="tipo_empresa">
            </div>
            <div class="mb-1">
                <label>CONTRASEÑA SISTEMA *</label>
                <input type="text" class="form-control" id="password_agencia">
            </div>
            <div class="mb-1">
                <label>CONTRASEÑA COMPRA VENTA</label>
                <input type="text" class="form-control" id="password_compraventa">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form>  
<script>
    @include('app.nuevosistema.select2',['json'=>'ubigeo','input'=>'#idubigeo'])
</script>    