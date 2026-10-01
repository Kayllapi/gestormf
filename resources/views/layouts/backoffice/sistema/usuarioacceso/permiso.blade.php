<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/usuarioacceso/'.$usuario->id) }}',
          method: 'PUT',
          data: {
              view : 'permiso',
              accesos : listar_permisos(),
              modulos : listar_permisos_modulo()
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
            <div class="col-sm-12 col-md-5">
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
                    <div class="col-sm-12">
                        <label>Estado de <span style="background-color: #ffce39;padding-left: 5px;padding-right: 5px;">Acceso</span> de Usuario: <span class="text-danger">(*)</span></label>
                        <select class="form-control" id="idestadousuario">
                            <option value="1">ACTIVADO</option>
                            <option value="2">DESACTIVADO</option>
                        </select>
                    </div>
                    <div class="col-sm-12">
                        <label>Intentos de Acceso Permitidos: <span class="text-danger">(*)</span> <small class="text-muted">(0 = sin límite)</small></label>
                        <input type="number" min="0" step="1" id="intentos_maximo" class="form-control" value="{{ $usuario->intentos_maximo }}">
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-7">
                <table class="table" id="tabla-permisoacceso">
                    <thead>
                        <tr>
                            <th>Agencia</th>
                            <th>Cargo</th>
                            <th>Estado de <span style="background-color: #ffce39;padding-left: 5px;padding-right: 5px;">Permisos y Cargos</span></th>
                            <th>Permisos Específicos por Usuario</th>
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

<style>
    /* el acordeon de permisos debe leerse igual que las filas de abajo */
    #mx-modal-permisos .accordion-button{ font-size: 1rem; font-weight: normal; padding: .5rem 1rem; }
    #mx-modal-permisos .accordion-body{ padding: .5rem 1rem; }
    #mx-modal-permisos .table{ font-size: 1rem; }
    .btnhover:hover{ background-color: #b1b1b1 !important; }
</style>

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
    

    /* ------------------------------------------------------------------
       PERMISOS POR USUARIO
       El cargo (permiso/permisoacceso) sigue siendo la base. Lo que se
       marque en este pantalla son excepciones: habilitar un modulo que el
       cargo no tiene, o deshabilitar uno que si tiene. Si no hay excepciones
       el usuario hereda el cargo tal cual.
       Se declara antes de armar las filas porque agregar_permiso() ya lo usa.
    ------------------------------------------------------------------ */
    var ARBOL_MODULOS   = @json($arbol_modulos);
    var MODULOS_CARGO   = @json($modulos_cargo);
    var USUARIO_MODULOS = @json($usuario_modulos);
    var estado_modulos  = {};     // { num: { idmodulo: 1|2 } }  solo lo que el usuario toco
    var fila_modulos    = null;   // fila abierta en el modal
    var MAPA_MODULOS    = {};     // indice plano del arbol, con padre e hijos

    (function(){
        (function recorrer(nodos, padre){
            (nodos || []).forEach(function(nodo){
                var id = parseInt(nodo.id);
                MAPA_MODULOS[id] = {
                    id:     id,
                    nombre: nodo.nombre,
                    padre:  padre,
                    hijos:  (nodo.hijos || []).map(function(hijo){ return parseInt(hijo.id); })
                };
                recorrer(nodo.hijos, id);
            });
        })(ARBOL_MODULOS, null);
    })();

    function clave_fila(num){
        var idtienda  = $('#idtienda'+num+' option:selected').val();
        var idpermiso = $('#idpermiso'+num+' option:selected').val();
        if(!idtienda || !idpermiso){
            return '';
        }
        return idtienda+'_'+idpermiso;
    }

    // carga en la fila las excepciones ya guardadas para (agencia, cargo).
    // se descartan las que ya no aplican: modulos que se eliminaron/desactivaron
    // del arbol, o estados que el cargo ya tiene igual. asi lo que quedo marcado
    // se mantiene y lo que se quito se queda quitado.
    function cargar_permisos_fila(num, idpermiso, idtienda){
        estado_modulos[num] = excepciones_vigentes(idtienda+'_'+idpermiso, idpermiso);
    }

    function excepciones_vigentes(clave, idpermiso){
        var guardadas = USUARIO_MODULOS[clave];
        var limpias   = {};
        if(guardadas == undefined){
            return limpias;
        }
        var del_cargo = MODULOS_CARGO[idpermiso] || [];
        Object.keys(guardadas).forEach(function(idmodulo){
            var id = parseInt(idmodulo);
            if(MAPA_MODULOS[id] === undefined){
                return; // el modulo ya no existe en el arbol
            }
            var estado = parseInt(guardadas[idmodulo]);
            if(estado != 1 && estado != 2){
                return;
            }
            if((estado == 1 && del_cargo.indexOf(id) >= 0) ||
               (estado == 2 && del_cargo.indexOf(id) <  0)){
                return; // ya coincide con el cargo, no hace falta excepcion
            }
            limpias[id] = estado;
        });
        return limpias;
    }

    // si el usuario cambia de agencia o de cargo, la excepcion anterior ya no aplica
    $(document).on('change', '#tabla-permisoacceso select[id^=idtienda], #tabla-permisoacceso select[id^=idpermiso]', function(){
        var num = $(this).attr('id').replace(/[^0-9]/g, '');
        var idpermiso = $('#idpermiso'+num+' option:selected').val();
        var idtienda  = $('#idtienda'+num+' option:selected').val();
        estado_modulos[num] = excepciones_vigentes(idtienda+'_'+idpermiso, idpermiso);
    });

    // estado efectivo de un modulo: 1 habilitado, 2 deshabilitado
    function estado_modulo(id, num){
        var propio = estado_modulos[num];
        if(propio && propio[id] !== undefined){
            return propio[id];
        }
        var cargo = $('#idpermiso'+num+' option:selected').val();
        var del_cargo = MODULOS_CARGO[cargo] || [];
        return (del_cargo.indexOf(id) >= 0) ? 1 : 2;
    }

    function texto_seguro(texto){
        return $('<div>').text(texto == undefined ? '' : texto).html();
    }
    @foreach($user_permiso as $value)
        agregar_permiso('{{$value->idpermiso}}','{{$value->idtienda}}','{{$value->idestado}}');
    @endforeach
    
    function agregar_permiso(idpermiso = 0, idtienda = 0, idestado = 0){
        
        var num   = $("#tabla-permisoacceso > tbody").attr('num');
        var cant  = $("#tabla-permisoacceso > tbody > tr").length;
      
        var tdeliminar = '<td></td>';
        if(idestado==0){
            tdeliminar = '<td><a href="javascript:;" onclick="eliminar_permiso('+num+')" class="btn btn-danger "><i class="fa-solid fa-trash"></i></td>';
        }
        let option_permiso = '<option></option>';
        @foreach($permisos as $value)
            option_permiso += '<option value="{{ $value->id }}">{{ $value->nombre }}</option>';
        @endforeach
        let option_tienda = '<option></option>';
        @foreach($tiendas as $value_tienda)
            option_tienda += '<option value="{{ $value_tienda->id }}">{{ $value_tienda->nombreagencia }}</option>';
        @endforeach
        let option_estado = '<option></option><option value="1">Act.</option><option value="2">Desact.</option>';
        var tabla='<tr id="'+num+'">'+
                      '<td><select class="form-control" id="idtienda'+num+'">'+option_tienda+'</select></td>'+
                      '<td><select class="form-control" id="idpermiso'+num+'">'+option_permiso+'</select></td>'+
                      '<td><select class="form-control" id="idestado'+num+'">'+option_estado+'</select></td>'+
                      '<td><button type="button" class="btn btn-primary btnhover" onclick="abrir_permisos('+num+')"><i class="fa fa-key"></i> Permisos</button></td>'+
                      tdeliminar+
                  '</tr>';

        $("#tabla-permisoacceso > tbody").append(tabla);
        $("#tabla-permisoacceso > tbody").attr('num',parseInt(num)+1);  
        @include('app.nuevosistema.select2',['input'=>'#idpermiso/+num+/'])
        @include('app.nuevosistema.select2',['input'=>'#idtienda/+num+/'])
        @include('app.nuevosistema.select2',['input'=>'#idestado/+num+/'])
        $('#idpermiso'+num).val(idpermiso).trigger('change');

        $('#idtienda'+num).val(idtienda).trigger('change');
        $('#idestado'+num).val(idestado).trigger('change');

        cargar_permisos_fila(num,idpermiso,idtienda);
    }
    function eliminar_permiso(num){
        $("#tabla-permisoacceso > tbody > tr#"+num).remove();
        delete estado_modulos[num];
    }
    function listar_permisos(){
        var data = [];
        $("#tabla-permisoacceso > tbody > tr").each(function() {
            var num = $(this).attr('id');    
            data.push({ 
                idpermiso: $('#idpermiso'+num+' option:selected').val(),
                idtienda: $('#idtienda'+num+' option:selected').val(),
                idestado: $('#idestado'+num+' option:selected').val(),
            });
        });
        return JSON.stringify(data);
    }

    function checkbox_modulo(id, nivel, marcado){
        return '<label class="chk" style="justify-content: space-between;width:100%;">'+
                   '<span style="padding-left:'+((nivel-1)*16)+'px;">'+texto_seguro(MAPA_MODULOS[id].nombre)+'</span>'+
                   '<input class="form-check-input chk-modulo" type="checkbox" data-modulo="'+id+'" '+(marcado?'checked':'')+'>'+
                   '<span class="checkmark"></span>'+
               '</label>';
    }

    // solo el check, para cuando el nombre ya lo muestra el boton del acordeon
    function checkbox_solo(id, marcado){
        return '<label class="chk">'+
                   '<input class="form-check-input chk-modulo" type="checkbox" data-modulo="'+id+'" '+(marcado?'checked':'')+'>'+
                   '<span class="checkmark"></span>'+
               '</label>';
    }

    function fila_modulo(nivel, id){
        return '<div class="table-responsive" style="margin-bottom:4px;">'+
                   '<table class="table" style="margin-bottom:0;">'+
                       '<tr>'+
                           '<td width="10px"><i data-feather="check"></i></td>'+
                           '<td>'+checkbox_modulo(id, nivel, estado_modulo(id, fila_modulos) == 1)+'</td>'+
                       '</tr>'+
                   '</table>'+
               '</div>';
    }

    function pintar_arbol(num){
        var html = '';
        ARBOL_MODULOS.forEach(function(menu){
            var collapse_id = 'collapse_permisos_'+num+'_'+menu.id;
            var marcado = estado_modulo(menu.id, num) == 1;

            html += '<div class="accordion-item">'+
                        '<h2 class="accordion-header d-flex align-items-center">'+
                            '<button class="accordion-button collapsed flex-grow-1" type="button" data-bs-toggle="collapse"'+
                                    ' data-bs-target="#'+collapse_id+'" aria-expanded="false" aria-controls="'+collapse_id+'">'+
                                texto_seguro(menu.nombre)+
                            '</button>'+
                            '<div class="px-2">'+checkbox_solo(menu.id, marcado)+'</div>'+
                        '</h2>'+
                        '<div id="'+collapse_id+'" class="accordion-collapse collapse" data-bs-parent="#accordion_permisos_usuario">'+
                            '<div class="accordion-body">';

            (menu.hijos || []).forEach(function(item){
                html += fila_modulo(2, item.id);
                (item.hijos || []).forEach(function(subitem){
                    html += '<div style="padding-left:16px;">'+fila_modulo(3, subitem.id)+'</div>';
                });
            });

            html += '</div></div></div>';
        });

        $('#accordion_permisos_usuario').html(html);
        if(typeof feather !== 'undefined'){ feather.replace(); }
    }

    // el modal se crea al vuelo porque se abre encima del modal de Editar Usuario
    function modal_permisos(){
        if($('#mx-modal-permisos').length > 0){
            return;
        }
        $('body').append(
            '<div class="modal fade" id="mx-modal-permisos" aria-hidden="true">'+
                '<div class="modal-dialog modal-lg modal-dialog-scrollable">'+
                    '<div class="modal-content">'+
                        '<div class="modal-header">'+
                            '<h5 class="modal-title">Permisos por Usuario</h5>'+
                            '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>'+
                        '</div>'+
                        '<div class="modal-body">'+
                            '<div class="alert alert-info py-2" id="permisos-usuario-detalle"></div>'+
                            '<div class="mb-3 d-flex flex-wrap gap-2 mt-2">'+
                                '<button type="button" class="btn btn-success" onclick="marcar_todos_permisos(1)"><i class="fa fa-check"></i> Habilitar todo</button>'+
                                '<button type="button" class="btn btn-primary" onclick="marcar_todos_permisos(2)"><i class="fa fa-ban"></i> Deshabilitar todo</button>'+
                                '<button type="button" class="btn btn-warning" onclick="restablecer_permisos()"><i class="fa-solid fa-rotate-left"></i> Restablecer Permisos en Cargo</button>'+
                            '</div>'+
                            '<div class="accordion" id="accordion_permisos_usuario"></div>'+
                        '</div>'+
                        '<div class="modal-footer">'+
                            '<button type="button" class="btn btn-primary" data-bs-dismiss="modal">Listo</button>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div>'
        );

        // mismo manejo de z-index que usa la funcion modal() del proyecto
        $('.modal').on('shown.bs.modal', function(){
            const zIndex = 1040 + 10 * $('.modal:visible').length;
            $(this).css('z-index', zIndex);
            setTimeout(function(){
                $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
            });
        });
        $('#mx-modal-permisos').on('hide.bs.modal', function(){
            $(this).remove();
            fila_modulos = null;
        });
    }

    function abrir_permisos(num){
        var idpermiso = $('#idpermiso'+num+' option:selected').val();
        var idtienda  = $('#idtienda'+num+' option:selected').val();
        if(!idpermiso || !idtienda){
            alert('Seleccione primero la Agencia y el Cargo.');
            return;
        }
        if(estado_modulos[num] == undefined){
            estado_modulos[num] = {};
        }
        fila_modulos = num;

        modal_permisos();

        var cargo   = $('#idpermiso'+num+' option:selected').text();
        var agencia = $('#idtienda'+num+' option:selected').text();

        $('#permisos-usuario-detalle').html(
            'Agencia: <b>'+texto_seguro(agencia)+'</b> &nbsp;|&nbsp; '+
            'Cargo: <b>'+texto_seguro(cargo)+'</b> <br>'+
            'Apellidos y Nombres: <b>'+texto_seguro(@json(trim($usuario->apellidopaterno.' '.$usuario->apellidomaterno.', '.$usuario->nombre)))+'</b> &nbsp;|&nbsp;'+
            'Usuario: <b>'+texto_seguro(@json($usuario->usuario))+'</b>'
        );

        pintar_arbol(num);
        new bootstrap.Modal(document.getElementById('mx-modal-permisos')).show();
    }

    function marcar_modulo(id, marcado, num){
        $('#mx-modal-permisos .chk-modulo[data-modulo="'+id+'"]').prop('checked', marcado);
        estado_modulos[num][id] = marcado ? 1 : 2;
        (MAPA_MODULOS[id].hijos || []).forEach(function(hijo){
            marcar_modulo(hijo, marcado, num);
        });
    }

    function sincronizar_padres(id, num){
        // el recorrido termina en el modulo raiz (id 7), que no se dibuja en el
        // arbol: hay que cortarlo o se guardaria una excepcion para ese id.
        var padre = MAPA_MODULOS[id].padre;
        while(padre && MAPA_MODULOS[padre] !== undefined){
            var alguno = (MAPA_MODULOS[padre].hijos || []).some(function(hijo){
                return $('#mx-modal-permisos .chk-modulo[data-modulo="'+hijo+'"]').is(':checked');
            });
            $('#mx-modal-permisos .chk-modulo[data-modulo="'+padre+'"]').prop('checked', alguno);
            estado_modulos[num][padre] = alguno ? 1 : 2;
            padre = MAPA_MODULOS[padre].padre;
        }
    }

    $(document).on('change', '#mx-modal-permisos .chk-modulo', function(){
        if(fila_modulos === null){ return; }
        var id = parseInt($(this).data('modulo'));
        var marcado = $(this).is(':checked');
        estado_modulos[fila_modulos][id] = marcado ? 1 : 2;
        (MAPA_MODULOS[id].hijos || []).forEach(function(hijo){
            marcar_modulo(hijo, marcado, fila_modulos);
        });
        sincronizar_padres(id, fila_modulos);
    });

    function marcar_todos_permisos(idestado){
        if(fila_modulos === null){ return; }
        Object.keys(MAPA_MODULOS).forEach(function(id){
            estado_modulos[fila_modulos][id] = idestado;
        });
        pintar_arbol(fila_modulos);
    }

    function restablecer_permisos(){
        if(fila_modulos === null){ return; }
        estado_modulos[fila_modulos] = {};
        pintar_arbol(fila_modulos);
    }

    // Solo se envian las diferencias con el cargo, agrupadas por agencia y cargo.
    function listar_permisos_modulo(){
        var data = {};
        $("#tabla-permisoacceso > tbody > tr").each(function() {
            var num   = $(this).attr('id');
            var clave = clave_fila(num);
            if(clave === ''){ return; }
            if(estado_modulos[num] && Object.keys(estado_modulos[num]).length > 0){
                data[clave] = estado_modulos[num];
            }
        });
        return JSON.stringify(data);
    }

</script>
