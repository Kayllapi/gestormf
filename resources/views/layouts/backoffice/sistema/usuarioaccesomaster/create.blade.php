<form action="javascript:;" onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/usuarioaccesomaster') }}',
        method: 'POST',
        data: { view: 'registrar', idagencia: $('#idagencia').val() }
    }, function(resultado){
        $('#tabla-usuarioaccesomaster').DataTable().ajax.reload();
        $('#modal-close-usuarioaccesomaster-registrar').click();
    }, this)">
    <div class="modal-header">
        <h5 class="modal-title">Registrar Gerente General</h5>
        <button type="button" class="btn-close" id="modal-close-usuarioaccesomaster-registrar" data-bs-dismiss="modal" aria-label="Cerrar"></button>
    </div>
    <div class="modal-body">
        <div class="row">
            <h6>Datos Generales</h6>
            <div class="col-sm-12 mb-1">
                <label for="idagencia">Agencia: <span class="text-danger">(*)</span></label>
                <select class="form-control" id="idagencia" name="idagencia" required>
                    <option value="">-- Seleccionar --</option>
                    @foreach($tiendas as $agencia)
                        <option value="{{ $agencia->id }}">{{ $agencia->nombreagencia }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-12 mb-1">
                <label for="cargo-gerente-general">Cargo</label>
                <input class="form-control" id="cargo-gerente-general" value="Gerente General" readonly disabled>
            </div>
            <div class="col-sm-12 col-md-6">
                <label for="apellido_parterno">Apellido Paterno: <span class="text-danger">(*)</span></label>
                <input type="text" id="apellido_parterno" class="form-control" maxlength="65" required>
            </div>
            <div class="col-sm-12 col-md-6">
                <label for="apellido_marterno">Apellido Materno: <span class="text-danger">(*)</span></label>
                <input type="text" id="apellido_marterno" class="form-control" maxlength="65" required>
            </div>
            <div class="col-sm-12 col-md-6">
                <label for="nombres">Nombres: <span class="text-danger">(*)</span></label>
                <input type="text" id="nombres" class="form-control" maxlength="65" required>
            </div>
            <div class="col-sm-12 col-md-6">
                <label for="identificacion">DNI: <span class="text-danger">(*)</span></label>
                <input type="text" inputmode="numeric" id="identificacion" class="form-control" maxlength="15" required>
            </div>
            <div class="col-sm-12">
                <label for="direccion">Dirección: <span class="text-danger">(*)</span></label>
                <input type="text" id="direccion" class="form-control" maxlength="255" required>
            </div>
            <div class="col-sm-12 mb-1">
                <label for="idubigeo">Distrito – Provincia – Departamento <span class="text-danger">(*)</span></label>
                <select class="form-control" id="idubigeo" required><option value=""></option></select>
            </div>
            <div class="col-sm-12 col-md-6">
                <label for="fecha_nacimiento">Fecha de Nacimiento: <span class="text-danger">(*)</span></label>
                <input type="date" id="fecha_nacimiento" class="form-control" max="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-sm-12 col-md-6">
                <label for="celular">Número de Celular: <span class="text-danger">(*)</span></label>
                <input type="text" inputmode="tel" id="celular" class="form-control" maxlength="255" required>
            </div>
            <div class="col-sm-12 col-md-6">
                <label for="usuario">Usuario (Login): <span class="text-danger">(*)</span></label>
                <input type="text" id="usuario" class="form-control" maxlength="100" autocomplete="off" required>
            </div>
            <div class="col-sm-12 col-md-6">
                <label for="password">Contraseña: <span class="text-danger">(*)</span></label>
                <input type="password" id="password" class="form-control" maxlength="50" autocomplete="new-password" required>
            </div>
            <div class="col-sm-12 col-md-6">
                <label for="idestadodivil">Estado civil: <span class="text-danger">(*)</span></label>
                <select class="form-control" id="idestadodivil" required>
                    <option value=""></option>
                    @foreach($estadocivil as $estado)
                        <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-12 col-md-6">
                <label for="profesion">Profesión: <span class="text-danger">(*)</span></label>
                <input type="text" id="profesion" class="form-control" maxlength="255" required>
            </div>
            <div class="col-sm-12">
                <label for="idestadousuario">Estado de Usuario: <span class="text-danger">(*)</span></label>
                <select class="form-control" id="idestadousuario" required>
                    <option value="1">ACTIVADO</option>
                    <option value="2">DESACTIVADO</option>
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Registrar Gerente General</button>
    </div>
</form>
<script>
    @include('app.nuevosistema.select2',['input'=>'#idagencia'])
    @include('app.nuevosistema.select2',['input'=>'#idestadodivil'])
    @include('app.nuevosistema.select2',['input'=>'#idestadousuario','val'=>1])
    @include('app.nuevosistema.select2',['json'=>'ubigeo','input'=>'#idubigeo'])
</script>
