<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/tarifario/'.$credito->id) }}',
          method: 'DELETE',
          data:{
              view: 'eliminar'
          }
      },
      function(resultado){
        lista_tarifario();
        load_nuevo_tarifario();
        $('#modal-close-credito-eliminar').click(); 
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">DATOS CLIENTE </h5>
        <button type="button" class="btn-close" id="modal-close-credito-eliminar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
      @if($users_prestamo)
       @if($usuario->nombrecompleto!='' or 
            $usuario->db_idgenero!='' or 
            $usuario->db_idnivelestudio!='' or 
            $users_prestamo->correo_electronico!='' or
            $users_prestamo->db_idtipodocumento!='' or 
            $users_prestamo->razonsocial_ac_economica!='' or 
            $usuario->fechanacimiento!='' or 
            $users_prestamo->profesion!='' or
            $usuario->identificacion!='' or 
            $users_prestamo->nombrecompelto_representantelegal!='' or 
            $usuario->db_idestadocivil!='' or 
            $usuario->numerotelefono!='')
        
        <table>
            <tbody>
                <tr>
                    @if($usuario->nombrecompleto!='' or 
                        $usuario->db_idgenero!='' or 
                        $usuario->db_idnivelestudio!='' or 
                        $users_prestamo->correo_electronico!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            @if($usuario->nombrecompleto!='')
                            <p><b>Apellidos y Nombres:</b> {{ $usuario->nombrecompleto }}</p>
                            @endif
                            <!--p><b>Nacionalidad:</b></p-->
                            @if($usuario->db_idgenero!='')
                            <p><b>Género:</b> {{ $usuario->db_idgenero }}</p>
                            @endif
                            @if($usuario->db_idnivelestudio!='')
                            <p><b>Nivel de estudios:</b> {{ $usuario->db_idnivelestudio }}</p>
                            @endif
                            @if($users_prestamo->correo_electronico)
                            <p><b>Email:</b> {{ $users_prestamo->correo_electronico }}</p>
                            @endif
                        </div>
                    </td>
                    @endif
                    @if($users_prestamo->db_idtipodocumento!='' or 
                        $users_prestamo->razonsocial_ac_economica!='' or 
                        $usuario->fechanacimiento!='' or 
                        $users_prestamo->profesion!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            @if($users_prestamo->db_idtipodocumento!='')
                            <p><b>Tipo de Documento:</b> {{ $users_prestamo->db_idtipodocumento }}</p>
                            @endif
                            @if($users_prestamo->razonsocial_ac_economica!='')
                            <p><b>Empresa:</b> {{ $users_prestamo->razonsocial_ac_economica }}</p>
                            @endif
                            @if($usuario->fechanacimiento!='')
                            <p><b>Fecha Nacimiento/Creación:</b> {{ date_format(date_create($usuario->fechanacimiento),'d-m-Y') }}</p>
                            @endif
                            @if($users_prestamo->profesion!='')
                            <p><b>Profesión:</b> {{ $users_prestamo->profesion }}</p>
                            @endif
                        </div>
                    </td>
                    @endif
                    @if($usuario->identificacion!='' or 
                        $users_prestamo->nombrecompelto_representantelegal!='' or 
                        $usuario->db_idestadocivil!='' or 
                        $usuario->numerotelefono!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            @if($usuario->identificacion!='')
                            <p><b>Nro Documento:</b> {{ $usuario->identificacion }}</p>
                            @endif
                            @if($users_prestamo->nombrecompelto_representantelegal!='')
                            <p><b>Representate Legal:</b> {{ $users_prestamo->nombrecompelto_representantelegal }}</p>
                            @endif
                            @if($usuario->db_idestadocivil!='')
                            <p><b>Estado Civil:</b> {{ $usuario->db_idestadocivil }}</p>
                            @endif
                            @if($usuario->numerotelefono!='')
                            <p><b>Teléfono:</b> {{ $usuario->numerotelefono }}</p>
                            @endif
                        </div>
                    </td>
                    @endif
                </tr>
            </tbody>
        </table>
        @endif
      @else
        <h6 class="text-danger">Información de cliente incompleta...</h6>
      @endif
    </div>
    <div class="modal-footer">
<!--         <button type="submit" class="btn btn-primary"><i class="fa-solid fa-trash"></i> Eliminar</button> -->
    </div>
</form>   