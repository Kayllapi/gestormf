<form action="javascript:;" onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/usuarioacceso') }}',
        method: 'POST',
        data: {
            view    : 'registrar',
            accesos : listar_permisos()
        }
    },
    function(resultado){
        $('#tabla-usuarioacceso').DataTable().ajax.reload();
        $('#modal-close-usuarioacceso-registrar').click();                                  
    },this)">
    <div class="modal-header">
        <h5 class="modal-title">Registrar Usuario</h5>
        <button type="button" class="btn-close" id="modal-close-usuarioacceso-registrar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row d-none">
            <div class="col-md-6">
                <div class="mb-1">
                    <label>Persona *</label>
                    <select class="form-select" id="idusuario">
                        <option></option>
                    </select>
                </div>
                <div class="mb-1">
                    <!-- <label>Usuario (Login) *</label>
                    <input type="text" class="form-control" id="usuario" disabled> -->
                </div>
                <div class="mb-1">
                    <!-- <label>Contraseña *</label>
                    <input type="text" class="form-control" id="password" disabled>  -->
                </div>
            </div>
            <div class="col-md-6">
                <!-- <div class="mb-1">
                    <label>Cargo *</label>
                    <input type="text" class="form-control" id="cargo" disabled> 
                </div> -->
                <!-- <div class="mb-1">
                    <label>Estado *</label>
                    <select class="form-select" id="idestadousuario" disabled>
                        <option></option>
                    </select>
                </div> -->
            </div>
        </div>
        <div class="row">   
            <div class="col-sm-12 col-md-6">
                <div class="row">
                    <h6>Datos Generales</h6>
                    <div class="col-sm-12 col-md-6">
                        <label>Apellido Paterno: <span class="text-danger">(*)</span></label>
                        <input type="text" id="apellido_parterno" class="form-control">
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label>Apellido Materno: <span class="text-danger">(*)</span></label>
                        <input type="text" id="apellido_marterno" class="form-control">
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label>Nombres: <span class="text-danger">(*)</span><span class="text-danger">(*)</span></label>
                        <input type="text" id="nombres" class="form-control">
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label>DNI: <span class="text-danger">(*)</span></label>
                        <input type="number" id="identificacion" class="form-control">
                    </div>
                    <div class="col-sm-12">
                        <label>Dirección: <span class="text-danger">(*)</span></label>
                        <input type="text" id="direccion" class="form-control">
                    </div>
                    <div class="col-sm-12">
                        <div class="mb-1">
                        <label id="cont-ubigeo">Distrito – Provincia – Departamento <span class="text-danger">(*)</span></label>
                        <select class="form-control" id="idubigeo">
                            <option></option>
                        </select>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <label>Fecha de Nacimiento: <span class="text-danger">(*)</span></label>
                        <input type="date" value="{{ date('Y-m-d') }}" id="fecha_nacimiento" class="form-control">
                    </div>
                    <div class="col-sm-12 col-md-12">
                        <label>Número de Celular: <span class="text-danger">(*)</span></label>
                        <input type="number" id="celular" class="form-control">
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label>Usuario (Login) <span class="text-danger">(*)</span></label>
                        <input type="text" class="form-control" id="usuario">
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label>Contraseña <span class="text-danger">(*)</span></label>
                        <input type="password" class="form-control" id="password"> 
                    </div>

                    <div class="col-sm-12">
                        <label>Estado civil: <span class="text-danger">(*)</span></label>
                        <select class="form-control" id="idestadodivil">
                            <option value=""></option>
                            @foreach($estadocivil as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-12">
                        <label>Profesión: <span class="text-danger">(*)</span></label>
                        <input type="text" id="profesion" class="form-control">
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label>Nivel Aprox. Crédito: <span class="text-danger">(*)</span></label>
                        <input type="number" id="nivel_aprox_credito" value="0.00" class="form-control">
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label>E. Caja: <span class="text-danger">(*)</span></label>
                        <input type="number" id="e_caja" value="0.00" class="form-control">
                    </div>
                    <div class="col-sm-12">
                        <label>Estado: <span class="text-danger">(*)</span></label>
                        <select class="form-control" id="idestadousuario">
                            <option value="1">ACTIVADO</option>
                            <option value="2">DESACTIVADO</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <table class="table" id="tabla-permisoacceso">
                    <thead>
                        <tr>
                            <th>Agencia</th>
                            <th>Cargo</th>
                            <th width="10px"><button type="button" class="btn btn-success" onclick="agregar_permiso()"><i class="fa fa-plus"></i></button></th>
                        </tr>
                    </thead>
                    <tbody num="0">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form> 
<script>
    @include('app.nuevosistema.select2',['input'=>'#idestado'])
    @include('app.nuevosistema.select2',['input'=>'#idestadodivil'])
    @include('app.nuevosistema.select2',['json'=>'tienda:usuario','input'=>'#idusuario'])
    @include('app.nuevosistema.select2',['json'=>'estado','input'=>'#idestadousuario','val'=>1])
    @include('app.nuevosistema.select2',['json'=>'tienda:sucursal','input'=>'#idsucursal','val'=>Auth::user()->idsucursal])
    @include('app.nuevosistema.select2',['json'=>'ubigeo','input'=>'#idubigeo'])

    $("#idusuario").on("change", function(e) {
        $('#usuario').removeAttr('disabled');
        $('#password').removeAttr('disabled');
        $('#cargo').removeAttr('disabled');
        $('#idestadousuario').removeAttr('disabled');
    });
    
    function agregar_permiso(idpermiso = 0, idtienda = 0){
        
        var num   = $("#tabla-permisoacceso > tbody").attr('num');
        var cant  = $("#tabla-permisoacceso > tbody > tr").length;
      
        var tdeliminar = '<td></td>';
        // if(cant>0){
            tdeliminar = '<td><a href="javascript:;" onclick="eliminar_permiso('+num+')" class="btn btn-danger "><i class="fa-solid fa-trash"></i></td>';
        // }
        let option_permiso = '<option></option>';
        @foreach($permisos as $value)
            option_permiso += '<option value="{{ $value->id }}">{{ $value->nombre }}</option>';
        @endforeach
        let option_tienda = '<option></option>';
        @foreach($tiendas as $value_tienda)
            option_tienda += '<option value="{{ $value_tienda->id }}">{{ $value_tienda->nombreagencia }}</option>';
        @endforeach
        var tabla='<tr id="'+num+'">'+
                      '<td><select class="form-control" id="idtienda'+num+'">'+option_tienda+'</select></td>'+
                      '<td><select class="form-control" id="idpermiso'+num+'">'+option_permiso+'</select></td>'+
                      tdeliminar+
                  '</tr>';

        $("#tabla-permisoacceso > tbody").append(tabla);
        $("#tabla-permisoacceso > tbody").attr('num',parseInt(num)+1);  
        @include('app.nuevosistema.select2',['input'=>'#idpermiso/+num+/'])
        @include('app.nuevosistema.select2',['input'=>'#idtienda/+num+/'])
        $('#idpermiso'+num).val(idpermiso).trigger('change');

        $('#idtienda'+num).val(idtienda).trigger('change');

        
    }
    function eliminar_permiso(num){
        $("#tabla-permisoacceso > tbody > tr#"+num).remove();
    }
    function listar_permisos(){
        var data = [];
        $("#tabla-permisoacceso > tbody > tr").each(function() {
            var num = $(this).attr('id');    
            data.push({ 
                idpermiso: $('#idpermiso'+num+' option:selected').val(),
                idtienda: $('#idtienda'+num+' option:selected').val(),
            });
        });
        return JSON.stringify(data);
    }
</script>