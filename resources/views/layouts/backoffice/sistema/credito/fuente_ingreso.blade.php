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
        <h5 class="modal-title">FUENTE DE INGRESOS </h5>
        <button type="button" class="btn-close" id="modal-close-credito-eliminar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        @if($users_prestamo->db_idforma_ac_economica!='' or
            $users_prestamo->db_idgiro_ac_economica!='' or 
            $users_prestamo->descripcion_ac_economica!='' or 
            $users_prestamo->ruc_ac_economica!='' or 
            $users_prestamo->razonsocial_ac_economica!='' or
            $users_prestamo->direccion_ac_economica!='' or 
            $users_prestamo->referencia_ac_economica!='' or 
            $users_prestamo->ruc_ac_economica!='' or 
            $users_prestamo->db_idlocalnegocio_ac_economica!='')
        <h6 class="ficha-titulo">ACTIVIDAD ECONÓMICA CLIENTE</h6>
        <table>
            <tbody>
                <tr>
                    @if($users_prestamo->db_idforma_ac_economica!='' or 
                        $users_prestamo->db_idgiro_ac_economica!='' or 
                        $users_prestamo->descripcion_ac_economica!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            @if($users_prestamo->db_idforma_ac_economica!='')
                            <p><b>Forma de Activ. Econom:</b> {{ $users_prestamo->db_idforma_ac_economica }}</p>
                            @endif
                            @if($users_prestamo->db_idgiro_ac_economica!='')
                            <p><b>Giro Económico:</b> {{ $users_prestamo->db_idgiro_ac_economica }}</p>
                            @endif
                            @if($users_prestamo->descripcion_ac_economica!='')
                            <p><b>Descripción:</b> {{ $users_prestamo->descripcion_ac_economica }}</p> 
                            @endif
                        </div>
                    </td>
                    @endif
                    @if($users_prestamo->ruc_ac_economica!='' or 
                        $users_prestamo->razonsocial_ac_economica!='' or 
                        $users_prestamo->direccion_ac_economica!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            @if($users_prestamo->ruc_ac_economica!='')
                            <p><b>RUC:</b> {{ $users_prestamo->ruc_ac_economica }}</p>
                            @endif
                            @if($users_prestamo->razonsocial_ac_economica!='')
                            <p><b>Nombre: Persona Natural/Persona Jurídica:</b> {{ $users_prestamo->razonsocial_ac_economica }}</p>
                            @endif
                            @if($users_prestamo->direccion_ac_economica!='')
                            <p><b>Direccion:</b> {{ $users_prestamo->direccion_ac_economica }}</p>
                            @endif
                        </div>
                    </td>
                    @endif
                    @if($users_prestamo->db_idubigeo_ac_economica!='' or 
                        $users_prestamo->referencia_ac_economica!='' or 
                        $users_prestamo->db_idlocalnegocio_ac_economica!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            @if($users_prestamo->db_idubigeo_ac_economica!='')
                            <p><b>Distrito – Provincia – Departamento:</b> {{ $users_prestamo->db_idubigeo_ac_economica }}</p>
                            @endif
                            @if($users_prestamo->referencia_ac_economica!='')
                            <p><b>Referencia de Ubicación:</b> {{ $users_prestamo->referencia_ac_economica }}</p>
                            @endif
                            @if($users_prestamo->db_idlocalnegocio_ac_economica!='')
                            <p><b>Local Negocio:</b> {{ $users_prestamo->db_idlocalnegocio_ac_economica }}</p>
                            @endif
                        </div>
                    </td>
                    @endif
                </tr>
            </tbody>
        </table>
        @endif
        @if($users_prestamo->ruc_laboral_cliente!='' or
            $users_prestamo->razonsocial_laboral_cliente!='' or 
            $users_prestamo->fechainicio_laboral_cliente!='' or 
            $users_prestamo->antiguedad_laboral_cliente!='' or 
            $users_prestamo->cargo_laboral_cliente!='' or
            $users_prestamo->area_laboral_cliente!='' or 
            $users_prestamo->db_idtipocontrato_laboral_cliente!='')
        <h6 class="ficha-titulo">CENTRO LABORAL CLIENTE</h6>
        <table>
            <tbody>
                <tr>
                    @if($users_prestamo->ruc_laboral_cliente!='' or 
                        $users_prestamo->razonsocial_laboral_cliente!='' or 
                        $users_prestamo->fechainicio_laboral_cliente!='' or 
                        $users_prestamo->antiguedad_laboral_cliente!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            @if($users_prestamo->ruc_laboral_cliente!='')
                            <p><b>RUC:</b> {{ $users_prestamo->ruc_laboral_cliente }}</p>
                            @endif
                            @if($users_prestamo->razonsocial_laboral_cliente!='')
                            <p><b>Nombre: Persona Natural/Persona Jurídica:</b> {{ $users_prestamo->razonsocial_laboral_cliente }}</p>
                            @endif
                            @if($users_prestamo->fechainicio_laboral_cliente!='')
                            <p><b>Fecha Inicio:</b> {{ $users_prestamo->fechainicio_laboral_cliente }}</p> 
                            @endif
                            @if($users_prestamo->antiguedad_laboral_cliente!='')
                            <p><b>Antiguedad (en años):</b> {{ $users_prestamo->antiguedad_laboral_cliente }}</p>
                            @endif
                        </div>
                    </td>
                    @endif
                    @if($users_prestamo->cargo_laboral_cliente!='' or 
                        $users_prestamo->area_laboral_cliente!='' or 
                        $users_prestamo->db_idtipocontrato_laboral_cliente!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            @if($users_prestamo->cargo_laboral_cliente!='')
                            <p><b>Cargo:</b> {{ $users_prestamo->cargo_laboral_cliente }}</p>
                            @endif
                            @if($users_prestamo->area_laboral_cliente!='')
                            <p><b>Área:</b> {{ $users_prestamo->area_laboral_cliente }}</p>
                            @endif
                            @if($users_prestamo->db_idtipocontrato_laboral_cliente!='')
                            <p><b>Contrato Laboral:</b> {{ $users_prestamo->db_idtipocontrato_laboral_cliente }}</p>
                            @endif
                        </div>
                    </td>
                    @endif
                </tr>
            </tbody>
        </table>
        @endif
    </div>
    <div class="modal-footer">
<!--         <button type="submit" class="btn btn-primary"><i class="fa-solid fa-trash"></i> Eliminar</button> -->
    </div>
</form>   