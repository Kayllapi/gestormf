<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/usuarioacceso/'.$usuario->id) }}',
          method: 'PUT',
          data: {
              view : 'editar',
          }
      },
    function(resultado){
        $('#tabla-usuarioacceso').DataTable().ajax.reload();
        $('#modal-close-usuarioacceso-registrar').click();                                  
    },this)">
    <div class="modal-header">
        <h5 class="modal-title">Registrar Acceso</h5>
        <button type="button" class="btn-close" id="modal-close-usuarioacceso-registrar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row justify-content-center">   
            <div class="col-sm-12 col-md-6">
                <div class="row">
                    <h6>Datos Generales</h6>
                    <div class="col-sm-12 col-md-6">
                        <label>Apellido Paterno:</label>
                        <input type="text" id="apellido_parterno" class="form-control" value="{{ $usuario->apellidopaterno }}">
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label>Apellido Materno:</label>
                        <input type="text" id="apellido_marterno" class="form-control" value="{{ $usuario->apellidomaterno }}">
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label>Nombres:</label>
                        <input type="text" id="nombres" class="form-control" value="{{ $usuario->nombre }}">
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label>DNI:</label>
                        <input type="number" id="identificacion" class="form-control" value="{{ $usuario->identificacion }}">
                    </div>
                    <div class="col-sm-12">
                        <label>Dirección:</label>
                        <input type="text" id="direccion" class="form-control"  value="{{ $usuario->direccion }}">
                    </div>
                    <div class="col-sm-12">
                        <label>Fecha de Nacimiento:</label>
                        <input type="date" value="{{ $usuario->fechanacimiento }}" id="fecha_nacimiento" class="form-control">
                    </div>
                    <div class="col-sm-12 col-md-12">
                        <label>Número de Celular:</label>
                        <input type="number" id="celular" class="form-control" value="{{ $usuario->numerotelefono }}">
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label>Usuario (Login) *</label>
                        <input type="text" class="form-control" id="usuario" value="{{ $usuario->usuario }}" >
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label>Contraseña *</label>
                        <input type="password" class="form-control" id="password"> 
                    </div>

                    <div class="col-sm-12">
                        <label>Estado civil:</label>
                        <select class="form-control" id="idestadodivil">
                            <option value=""></option>
                            @foreach($estadocivil as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-12">
                        <label>Profesión:</label>
                        <input type="text" id="profesion" class="form-control"  value="{{ $usuario->profesion }}">
                    </div>
                    <div class="col-sm-12">
                        <label>Estado:</label>
                        <select class="form-control" id="idestadousuario">
                            <option value="1">ACTIVADO</option>
                            <option value="2">DESACTIVADO</option>
                        </select>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form> 
<script>
    @include('app.nuevosistema.select2',['input'=>'#idestado'])
    @include('app.nuevosistema.select2',['input'=>'#idestadodivil','val'=>$usuario->idestadocivil])
    @include('app.nuevosistema.select2',['json'=>'tienda:usuario','input'=>'#idusuario'])
    @include('app.nuevosistema.select2',['json'=>'estado','input'=>'#idestadousuario','val'=>$usuario->idestadousuario])
    @include('app.nuevosistema.select2',['json'=>'tienda:sucursal','input'=>'#idsucursal','val'=>Auth::user()->idsucursal])


</script>