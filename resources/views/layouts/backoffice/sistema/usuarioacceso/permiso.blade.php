<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/usuarioacceso/'.$usuario->id) }}',
          method: 'PUT',
          data: {
              view : 'permiso',
              accesos : listar_permisos()
          }
      },
      function(resultado){
          $('#modal-close-usuarioacceso-permiso').click(); 
          $('#tabla-usuarioacceso').DataTable().ajax.reload();
          mostrar_sucursales();
      },this)"> 
    <input type="hidden" id="iduser_modificacion" value="0">
    <div class="modal-header">
        <h5 class="modal-title">Editar Usuario</h5>
        <button type="button" class="btn-close" id="modal-close-usuarioacceso-permiso" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body"> 
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="row">
                    <div class="col-md-6">
                    <h6>Datos Generales</h6>
                    </div>
                    <div class="col-md-6 d-md-flex justify-content-md-end">
                        <button type="button" 
                                onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/usuarioacceso/create?view=autorizacion&idusuario='.$usuario->id)}}',  size: 'modal-sm'})" class="btn btn-success"><i class="fa fa-pencil"></i> Editar</button></button>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label>Apellido Paterno: <span class="text-danger">(*)</span></label>
                        <input type="text" id="apellido_parterno" class="form-control" value="{{ $usuario->apellidopaterno }}" disabled>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label>Apellido Materno: <span class="text-danger">(*)</span></label>
                        <input type="text" id="apellido_marterno" class="form-control" value="{{ $usuario->apellidomaterno }}" disabled>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label>Nombres: <span class="text-danger">(*)</span></label>
                        <input type="text" id="nombres" class="form-control" value="{{ $usuario->nombre }}" disabled>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label>DNI: <span class="text-danger">(*)</span></label>
                        <input type="number" id="identificacion" class="form-control" value="{{ $usuario->identificacion }}" disabled>
                    </div>
                    <div class="col-sm-12">
                        <label>Dirección: <span class="text-danger">(*)</span></label>
                        <input type="text" id="direccion" class="form-control"  value="{{ $usuario->direccion }}">
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
                        <input type="date" value="{{ $usuario->fechanacimiento }}" id="fecha_nacimiento" class="form-control">
                    </div>
                    <div class="col-sm-12 col-md-12">
                        <label>Número de Celular: <span class="text-danger">(*)</span></label>
                        <input type="number" id="celular" class="form-control" value="{{ $usuario->numerotelefono }}">
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label>Usuario (Login) <span class="text-danger">(*)</span></label>
                        <input type="text" class="form-control" id="usuario" value="{{ $usuario->usuario }}" >
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
                        <input type="text" id="profesion" class="form-control"  value="{{ $usuario->profesion }}">
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label>Nivel Aprox. Crédito: <span class="text-danger">(*)</span></label>
                        <input type="number" id="nivel_aprox_credito" value="{{ $usuario->nivelcredito }}" class="form-control">
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label>E. Caja: <span class="text-danger">(*)</span></label>
                        <input type="number" id="e_caja" value="{{ $usuario->ecaja }}" class="form-control">
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
              
                    <!--h6>Configuraciones</h6>
              
                        <div class="mb-1">
                            <label>&nbsp; </label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"  id="estadocreditoprendario" <?php echo $usuario->estadocreditoprendario=='on'?'checked':'' ?>>
                                <label class="form-check-label" for="estadocreditoprendario" style="margin-top: 0px">
                                Habilitar Crédito Prendario
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"  id="estadocreditonoprendario" <?php echo $usuario->estadocreditonoprendario=='on'?'checked':'' ?>>
                                <label class="form-check-label" for="estadocreditonoprendario" style="margin-top: 0px">
                                Habilitar Crédito No Prendario
                                </label>
                            </div>
                        </div-->
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form>

<script>
    function autorizar_edicion(){
        $('#apellido_parterno').removeAttr('disabled');
        $('#apellido_marterno').removeAttr('disabled');
        $('#nombres').removeAttr('disabled');
        $('#identificacion').removeAttr('disabled');
        
    }
    //mostrar_permisomodulo();
    @include('app.nuevosistema.select2',['input'=>'#idestado'])
    @include('app.nuevosistema.select2',['input'=>'#idestadodivil','val'=>$usuario->idestadocivil])
    @include('app.nuevosistema.select2',['json'=>'tienda:usuario','input'=>'#idusuario'])
    @include('app.nuevosistema.select2',['json'=>'estado','input'=>'#idestadousuario','val'=>$usuario->idestadousuario])
    @include('app.nuevosistema.select2',['json'=>'tienda:sucursal','input'=>'#idsucursal','val'=>Auth::user()->idsucursal])
    @include('app.nuevosistema.select2',['json'=>'ubigeo','input'=>'#idubigeo','val'=>$usuario->idubigeo!=0?$usuario->idubigeo:''])
    
    @foreach($user_permiso as $value)
        agregar_permiso('{{$value->idpermiso}}','{{$value->idtienda}}');
    @endforeach
    
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