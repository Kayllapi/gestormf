<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/usuario/'.$usuario->id) }}',
          method: 'PUT',
          data:{
              view: 'editar',
              telefono_cliente: seleccionar_cliente_financiera(`celular-cliente`),
              telefono_pareja: seleccionar_cliente_financiera(`celular-pareja`),
              referencia_cliente: seleccionar_referencia(),
          }
      },
      function(resultado){
          $('#modal-close-usuario-editar').click();  
          $('#tabla-usuario').DataTable().ajax.reload(); 
      },this)" id="form-editar-cliente"> 
    <input type="hidden" id="iduser_modificacion" value="0">
    <div class="modal-header">
        <h5 class="modal-title">EDITAR DE CLIENTE</h5>
        <button type="button" class="btn-close" id="modal-close-usuario-editar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Tipo de Información <span class="text-danger">(*)</span></label>
                    <select class="form-select" id="idtipoinformacion" disabled>
                        <option value=""></option>
                        @foreach($tipoinformacion as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-12 col-md-8 d-md-flex justify-content-md-end">
                <button type="button" onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/usuario/create?view=autorizacion&idusuario='.$usuario->idcliente_modificacion)}}',size:'modal-sm'})" class="btn btn-success"><i class="fa fa-pencil"></i> Editar</button></button>
            </div>
        </div>
        <div id="cont_tipoinformacion" style="display:none;">
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Fuente de Ingreso  <span class="text-danger">(*)</span></label>
                    <select class="form-select" id="idfuenteingreso" disabled>
                        <option value=""></option>
                        @foreach($fuenteingreso as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div id="cont_datosdelcliente" style="display:none;">
            <div class="mb-1 mt-1">
            <span class="badge d-block">DATOS DEL CLIENTE</span>
            </div>
            <div class="row">
                <div class="col-md-4" id="cont_datosdelcliente_tipopersona" style="display:none;">
                    <label>Tipo de Persona <span class="text-danger">(*)</span></label>
                    <select class="form-control" id="idtipopersona" disabled>
                        <option value=""></option>
                    </select>
                </div>
                <div class="col-md-4" id="cont_datosdelcliente_tipodocumento" style="display:none;">
                    <label>Tipo de Documento <span class="text-danger">(*)</span></label>
                    <select class="form-control" id="idtipodocumento" disabled>
                        <option value=""></option>
                    </select>
                </div>
                <div class="col-md-4">
                    <div id="cont_datosdelcliente_ruc" style="display:none;">
                        <label>RUC (11 Digitos) <span class="text-danger">(*)</span></label>
                        <input type="number" class="form-control" id="ruc" value="{{ $usuario->identificacion }}" onkeyup="buscar_ruc()" disabled/>
                        <div id="resultado-ruc" style="float: right;margin-top: -35px;margin-right: 3px;text-align: right;"></div>
                        <div id="resultado-ruc-resultado" style="color:#e22d02;"></div>
                    </div>
                    <div id="cont_datosdelcliente_dni" style="display:none;">
                        <label>DNI (8 Digitos) <span class="text-danger">(*)</span></label>
                        <input type="number" class="form-control" id="dni" value="{{ $usuario->identificacion }}" onkeyup="buscar_dni()" disabled/>
                        <div id="resultado-dni" style="float: right;margin-top: -35px;margin-right: 3px;text-align: right;"></div>
                        <div id="resultado-dni-resultado" style="color:#e22d02;"></div>
                    </div>
                    <div id="cont_datosdelcliente_ce" style="display:none;">
                        <label>Carnet Extranjería <span class="text-danger">(*)</span></label>
                        <input type="number" class="form-control" id="carnetextranjeria" value="{{ $usuario->identificacion }}" disabled>
                    </div>
                </div>
            </div>
            <div id="cont_datosdelcliente_ruc_data" style="display:none;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-1">
                            <label>Nombre Comercial</label>
                            <input type="text" class="form-control" value="{{ $usuario->nombre }}" id="nombrecomercial" disabled/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-1">
                            <label id="cont-juridica-razonsocial">Razón Social  <span class="text-danger">(*)</span></label>
                            <input type="text" class="form-control" value="{{ $usuario->razonsocial }}" id="razonsocial" disabled/>
                        </div>
                    </div>
                </div>
            </div>
            <div id="cont_datosdelcliente_dni_data" style="display:none;">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-1">
                            <label>Nombre  <span class="text-danger">(*)</span></label>
                            <input type="text" class="form-control" value="{{ $usuario->nombre }}" id="nombre" disabled/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-1">
                            <label id="cont-natural-apellidopaterno">Apellido Paterno  <span class="text-danger">(*)</span></label>
                            <input type="text" class="form-control" value="{{ $usuario->apellidopaterno }}" id="apellidopaterno" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-1">
                            <label id="cont-natural-apellidomaterno">Apellido Materno  <span class="text-danger">(*)</span></label>
                            <input type="text" class="form-control" value="{{ $usuario->apellidomaterno }}" id="apellidomaterno" disabled>
                        </div>
                    </div>  
                </div>
            </div>
            <div id="cont_datosdelcliente_ce_data" style="display:none;">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-1">
                            <label>Nombre  <span class="text-danger">(*)</span></label>
                            <input type="text" class="form-control" value="{{ $usuario->nombre }}" id="nombre_carnetextranjeria" disabled>
                        </div>
                    </div> 
                    <div class="col-md-4">
                        <div class="mb-1">
                            <label>Apellido Paterno <span class="text-danger">(*)</span></label>
                            <input type="text" class="form-control" value="{{ $usuario->apellidopaterno }}" id="apellidopaterno_carnetextranjeria" disabled>
                        </div>
                    </div> 
                    <div class="col-md-4">
                        <div class="mb-1">
                            <label>Apellido Materno <span class="text-danger">(*)</span></label>
                            <input type="text" class="form-control" value="{{ $usuario->apellidomaterno }}" id="apellidomaterno_carnetextranjeria" disabled> 
                        </div>
                    </div> 
                </div>
            </div>
            <div id="cont_datosdelcliente_data_adicional" style="display:none;">
                <div class="row">
                    <div class="col-sm-12 col-md-4" id="cont_datosdelcliente_data_adicional_ruc" style="display:none;">
                        <div class="mb-1">
                            <label>Nro Documento Representante Legal <span class="text-danger">(*)</span></label>
                            <input type="number" {{ $prendaria>0 ? 'disabled' : '' }} class="form-control" value="{{ $users_prestamo ? $users_prestamo->documento_representantelegal : '' }}" id="documento_representantelegal">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-8" id="cont_datosdelcliente_data_adicional_ruc" style="display:none;">
                        <div class="mb-1">
                            <label>Nombre Completo Representante Legal <span class="text-danger">(*)</span></label>
                            <input type="text" {{ $prendaria>0 ? 'disabled' : '' }} class="form-control" value="{{ $users_prestamo ? $users_prestamo->nombrecompelto_representantelegal : '' }}" id="nombrecompelto_representantelegal">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4" id="cont_datosdelcliente_data_adicional_dnice" style="display:none;">
                        <div class="mb-1">
                            <label>Genero <span class="text-danger">(*)</span></label>
                            <select class="form-select" {{ $prendaria>0 ? 'disabled' : '' }} id="idgenero">
                                <option></option>
                                @foreach($genero as $value)
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="mb-1">
                            <label>Fecha Nac./Creación <span class="text-danger">(*)</span></label>
                            <input type="date" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $usuario ? ( $usuario->fechanacimiento != '' ? $usuario->fechanacimiento : date('Y-m-d') ) : date('Y-m-d') }}" id="fechanacimientocreacion">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4" id="cont_datosdelcliente_data_adicional_dnice" style="display:none;">
                        <div class="mb-1">
                            <label>Estado Civil <span class="text-danger">(*)</span></label>
                            <select class="form-select" {{ $prendaria>0 ? 'disabled' : '' }} id="idestadocivil">
                                <option value=""></option>
                                @foreach($estadocivil as $value)
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4" id="cont_datosdelcliente_data_adicional_dnice" style="display:none;">
                        <div class="mb-1">
                            <label>Nivel de Estudios <span class="text-danger">(*)</span></label>
                            <select class="form-select" {{ $prendaria>0 ? 'disabled' : '' }} id="idnivelestudio">
                                <option value=""></option>
                                @foreach($nivelestudio as $value)
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4" id="cont_datosdelcliente_data_adicional_dnice" style="display:none;">
                        <div class="mb-1">
                            <label>Profesión</label>
                            <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} id="profesion" value="{{ $users_prestamo ? $users_prestamo->profesion : '' }}">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="mb-1">
                            <label>Correo Electrónico </label>
                            <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} id="correo_electronico" value="{{ $users_prestamo ? $users_prestamo->correo_electronico : '' }}">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="mb-1">
                            <label>Telf./Celular <span class="text-danger">(*)</span>
                                <a href="javascript:;" onclick="agregar_cliente_financiera('celular-cliente')" class="btn-info" style="border-radius: 10px;padding: 2px;padding-bottom: 0px;">
                                    <i class="fa-solid fa-plus" style="color:#000"></i>
                                </a>
                            </label>
                            <table class="table tabla-interno" id="tabla-celular-cliente">
                                <tbody num="0">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div id="cont_datosdepareja" style="display:none;">
            <div class="mb-1 mt-1">
                <span class="badge d-block">DATOS DE PAREJA</span>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>DNI/CE <span class="text-danger">(*)</span></label>
                        <input type="number" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->dni_pareja : '' }}" id="dni_pareja">
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Nombres <span class="text-danger">(*)</span></label>
                        <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->nombres_pareja : '' }}" id="nombres_pareja">
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Apellido Paterno <span class="text-danger">(*)</span></label>
                        <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->ap_paterno_pareja : '' }}" id="ap_paterno_pareja">
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Apellido Materno <span class="text-danger">(*)</span></label>
                        <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->ap_materno_pareja : '' }}" id="ap_materno_pareja">
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Ocupación <span class="text-danger">(*)</span></label>
                        <select class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} id="idocupacion_pareja">
                            <option value=""></option>                                
                            @foreach($ocupacion as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Profesión </label>
                        <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->profesion_pareja : '' }}" id="profesion_pareja">
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Nivel de Estudios</label>
                        <select class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} id="idnivelestudio_pareja">
                            <option value=""></option>
                            @foreach($nivelestudio as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="mb-1">
                        <label>Telf./Celular de PAREJA 
                            <a href="javascript:;" onclick="agregar_cliente_financiera('celular-pareja')" class="btn-info" style="border-radius: 10px;padding: 2px;padding-bottom: 0px;">
                                <i class="fa-solid fa-plus" style="color:#000"></i>
                            </a>
                        </label>
                        <table class="table tabla-interno" id="tabla-celular-pareja">
                            <tbody num="0">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
            <div id="cont_domiciliodecliente" style="display:none;">
                <div class="mb-1 mt-1">
                    <span class="badge d-block">DOMICILIO DE CLIENTE</span>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-7">
                        <div class="mb-1">
                        <label id="cont-direccion">Dirección <span class="text-danger">(*)</span></label>
                        <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $usuario->direccion }}" id="direccion"/>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-5">
                        <div class="mb-1">
                        <label id="cont-ubigeo">Distrito – Provincia – Departamento <span class="text-danger">(*)</span></label>
                        <select class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} id="idubigeo">
                            <option></option>
                        </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12">
                        <div class="mb-1">
                            <label>Referencia Ubicación <span class="text-danger">(*)</span></label>
                            <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->referencia_direccion : '' }}" id="referencia_direccion">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="mb-1">
                            <label>Suministro Elect(Caso no exista N° Domicilio)</label>
                            <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->suministro_electrocentro : '' }}" id="suministro_electrocentro">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="mb-1">
                            <label>Condición de Vivienda/Local <span class="text-danger">(*)</span></label>
                            <select class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} id="idcondicionviviendalocal">
                                <option value=""></option>
                                @foreach($condicionviviendalocal as $value)
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="mb-1">
                            <label>Referencia telefónica <span class="text-danger">(*)</span>
                                <a href="javascript:;" onclick="agregar_referencia()" class="btn-info" style="border-radius: 10px;padding: 2px;padding-bottom: 0px;">
                                    <i class="fa-solid fa-plus" style="color:#000"></i>
                                </a>
                            </label>
                            <table class="table tabla-interno" id="tabla-referencia">
                                <thead>
                                    <th>Telf./Celular</th>
                                    <th>Nombres y Apellidos</th>
                                    <th>Vinculo Familiar/Personas/Otros</th>
                                </thead>
                                <tbody num="0">
                                </tbody>
                            </table>
                        </div>
                    </div>



                </div>
            </div>
            <div id="cont_actividadeconomicadelcliente" style="display:none;">
                <div class="mb-1 mt-1">
                    <span class="badge d-block">ACTIVIDAD ECONÓMICA DEL CLIENTE</span>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <div class="mb-1">
                            <label>Forma de Activ. Econom. <span class="text-danger">(*)</span></label>
                            <select class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} id="idforma_ac_economica">
                                <option value=""></option>
                                @foreach($formactividadeconomica as $value)
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="mb-1">
                            <label>Giro Económico <span class="text-danger">(*)</span></label>
                            <select class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} id="idgiro_ac_economica">
                                <option value=""></option>
                                <option value="1">Comercio</option>
                                <option value="2">Servicio</option>
                                <option value="3">Produccion</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="mb-1">
                            <label>Descripción <span class="text-danger">(*)</span></label>
                            <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->descripcion_ac_economica : '' }}" id="descripcion_ac_economica">
                        </div>
                    </div>

                    <div class="col-sm-8 d-none">
                        <div class="mb-1">
                            <label>Empresa  <span class="text-danger">(*)</span></label>
                            <select class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} id="idempresa_ac_economica">
                                <option></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4 container-empresa-ac-economica d-none">
                        <div class="mb-1">
                            <label>RUC</label>
                            <input type="number" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->ruc_ac_economica : '' }}" id="ruc_ac_economica">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-8 container-empresa-ac-economica d-none">
                        <div class="mb-1">
                            <label>Nombre: Persona Natural/Persona Jurídica</label>
                            <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->razonsocial_ac_economica : '' }}" id="razonsocial_ac_economica">
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">
                        <div class="mb-1">
                            <label>&nbsp;</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"  id="casanegocio" {{ $users_prestamo ? ($users_prestamo->casanegocio=='SI'?'checked':'') : '' }}>
                                <label class="form-check-label" for="casanegocio" style="margin-top: 0px">
                                Casa/Negocio:
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-5">
                        <div id="cont_casanegocio">
                            <div class="mb-1">
                                <label>Direccion  <span class="text-danger">(*)</span></label>
                                <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->direccion_ac_economica : '' }}" id="direccion_ac_economica">
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-4">
                        <div id="cont_casanegocio">
                            <div class="mb-1">
                                <label id="cont-ubigeo">Distrito – Provincia – Departamento <span class="text-danger">(*)</span></label>
                                <select class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} id="idubigeo_ac_economica">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div id="cont_casanegocio">
                            <div class="mb-1">
                                <label>Referencia de Ubicación <span class="text-danger">(*)</span></label>
                                <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->referencia_ac_economica : '' }}" id="referencia_ac_economica">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div id="cont_casanegocio">
                            <div class="mb-1">
                                <label>Local Negocio  <span class="text-danger">(*)</span></label>
                                <select class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} id="idlocalnegocio_ac_economica">
                                    <option value=""></option>
                                    @foreach($condicionviviendalocal as $value)
                                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="cont_centrolaboraldelcliente" style="display:none;">
                <div class="mb-1 mt-1">
                    <span class="badge d-block">CENTRO LABORAL DEL CLIENTE</span>
                </div>
                <div class="row">
                    <div class="col-sm-6 d-none">
                        <div class="mb-1">
                            <label>Empresa</label>
                            <select class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} id="idempresa_laboral_cliente">
                                <option></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="mb-1">
                            <label>RUC</label>
                            <input type="number" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->ruc_laboral_cliente : '' }}" id="ruc_laboral_cliente">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-8">
                        <div class="mb-1">
                            <label>Nombre: Persona Natural/Persona Jurídica</label>
                            <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->razonsocial_laboral_cliente : '' }}" id="razonsocial_laboral_cliente" >
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="mb-1">
                            <label>Fecha Inicio</label>
                            <input type="date" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->fechainicio_laboral_cliente : '' }}" id="fechainicio_laboral_cliente">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="mb-1">
                            <label>Antiguedad (en años) </label>
                            <input type="text"  class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->antiguedad_laboral_cliente : ''}}" id="antiguedad_laboral_cliente" >
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-4">
                        <div class="mb-1">
                            <label>Cargo </label>
                            <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->cargo_laboral_cliente : '' }}" id="cargo_laboral_cliente" >
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="mb-1">
                            <label>Área </label>
                            <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->area_laboral_cliente : '' }}" id="area_laboral_cliente" >
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="mb-1">
                            <label>Contrato Laboral</label>
                            <select class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} id="idtipocontrato_laboral_cliente">
                                <option value="0">Ninguno</option>
                                @foreach($contratolaboral as $value)
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        <div id="cont_centrolaboraldepareja" style="display:none;">
            <div class="mb-1 mt-1">
                <span class="badge d-block">CENTRO LABORAL DE: PAREJA/REPRESENTANTE LEG.</span>
            </div>
            <div class="row">
                <div class="col-sm-6 d-none">
                    <div class="mb-1">
                        <label>Empresa </label>
                        <select class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} id="idempresa_laboral_pareja">
                            <option></option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>RUC</label>
                        <input type="number" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->ruc_laboral_pareja : '' }}" id="ruc_laboral_pareja">
                    </div>
                </div>
                <div class="col-sm-12 col-md-8">
                    <div class="mb-1">
                        <label>Nombre: Persona Natural/Persona Jurídica
                        <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->razonsocial_laboral_pareja : '' }}" id="razonsocial_laboral_pareja">
                    </div>
                </div>

                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Fecha Inicio</label>
                        <input type="date" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? ( $users_prestamo->fechainicio_laboral_pareja !='' ? $users_prestamo->fechainicio_laboral_pareja : '' ) : '' }}" id="fechainicio_laboral_pareja">
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Antiguedad (en años)
                        <input type="text"  class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->antiguedad_laboral_pareja : '' }}" id="antiguedad_laboral_pareja">
                    </div>
                </div>

                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Cargo
                        <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->cargo_laboral_pareja : '' }}" id="cargo_laboral_pareja">
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Área </label>
                        <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->area_laboral_pareja : '' }}" id="area_laboral_pareja">
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Contrato Laboral
                        <select class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} id="idtipocontrato_laboral_pareja">
                            <option value="0">Ninguno</option>
                            @foreach($contratolaboral as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div id="cont_negociodepareja" style="display:none;">
            <div class="mb-1 mt-1">
                <span class="badge d-block">NEGOCIO DE: PAREJA</span>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Forma de Activ. Econom. <span class="text-danger">(*)</span></label>
                        <select class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} id="idforma_negocio_pareja">
                            <option value=""></option>
                            @foreach($formactividadeconomica as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Giro Económico <span class="text-danger">(*)</span></label>
                        <select class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} id="idgiro_negocio_pareja">
                            <option value=""></option>
                            <option value="1">Comercio</option>
                            <option value="2">Servicio</option>
                            <option value="3">Produccion</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Descripción <span class="text-danger">(*)</span></label>
                        <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->descripcion_negocio_pareja : '' }}" id="descripcion_negocio_pareja">
                    </div>
                </div>
                
                <div class="col-sm-6 d-none">
                    <div class="mb-1">
                        <label>Empresa <span class="text-danger">(*)</span></label>
                        <select class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} id="idempresa_negocio_pareja">
                            <option></option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 container-empresa-negocio-pareja">
                    <div class="mb-1">
                        <label>RUC</label>
                        <input type="number" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->ruc_negocio_pareja : '' }}" id="ruc_negocio_pareja">
                    </div>
                </div>
                <div class="col-sm-12 col-md-8 container-empresa-negocio-pareja">
                    <div class="mb-1">
                        <label>Nombre: Persona Natural/Persona Jurídica</label>
                        <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->razonsocial_negocio_pareja : '' }}" id="razonsocial_negocio_pareja">
                    </div>
                </div>
                
                <div class="col-sm-12 col-md-7">
                    <div class="mb-1">
                        <label>Direccion <span class="text-danger">(*)</span></label>
                        <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} value="{{ $users_prestamo ? $users_prestamo->direccion_negocio_pareja : '' }}" id="direccion_negocio_pareja">
                    </div>
                </div>

                <div class="col-sm-12 col-md-5">
                    <div class="mb-1">
                        <label id="cont-ubigeo">Distrito – Provincia – Departamento <span class="text-danger">(*)</span></label>
                        <select class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} id="idubigeo_negocio_pareja">
                            <option></option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-md-7">
                    <div class="mb-1">
                        <label>Referencia de Ubicación</label>
                        <input type="text" class="form-control" {{ $prendaria>0 ? 'disabled' : '' }}value="{{ $users_prestamo ? $users_prestamo->referencia_negocio_pareja : '' }}"  id="referencia_negocio_pareja">
                    </div>
                </div>
                <div class="col-sm-12 col-md-5">
                    <div class="mb-1">
                        <label>Local Negocio <span class="text-danger">(*)</span></label>
                        <select class="form-control" {{ $prendaria>0 ? 'disabled' : '' }} id="idlocalnegocio_negocio_pareja">
                            <option value="0">Ninguno</option>
                            @foreach($condicionviviendalocal as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
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
    function autorizar_edicion(){
        $('#form-editar-cliente').find('select[disabled]').removeAttr('disabled');
        $('#form-editar-cliente').find('input[disabled]').removeAttr('disabled');
        
    }
  
    @php 
      $telefono_cliente = $users_prestamo ? ( is_null($users_prestamo->telefono_cliente) ? [] : json_decode($users_prestamo->telefono_cliente) ) : [];
      $telefono_pareja = $users_prestamo ? ( is_null($users_prestamo->telefono_pareja) ? [] : json_decode($users_prestamo->telefono_pareja) ) : [];
      $referencia_cliente = $users_prestamo ? ( is_null($users_prestamo->referencia_cliente) ? [] : json_decode($users_prestamo->referencia_cliente) ) : [];
    @endphp
  
    @foreach($telefono_cliente as $value)
        agregar_cliente_financiera('celular-cliente','{{$value->valor}}');
    @endforeach
    @foreach($telefono_pareja as $value)
        agregar_cliente_financiera('celular-pareja','{{$value->valor}}');
    @endforeach
    @foreach($referencia_cliente as $value)
        agregar_referencia('{{ $value->referencia }}', '{{ $value->vinculo }}', '{{ $value->celular }}');
    @endforeach

    @if(count($telefono_cliente)==0)
        agregar_cliente_financiera('celular-cliente');
    @endif

    @if(count($telefono_pareja)==0)
        agregar_cliente_financiera('celular-pareja');
    @endif
  
    // DATOS DEL CLIENTE
    @include('app.nuevosistema.select2',['input'=>'#idtipoinformacion' , 'val' =>  $users_prestamo ? $users_prestamo->idtipoinformacion:0 ]);
    @include('app.nuevosistema.select2',['input'=>'#idfuenteingreso' , 'val' =>  $users_prestamo ? $users_prestamo->idfuenteingreso:0 ]);
                                         
    @if($users_prestamo)
    if({{$users_prestamo->idfuenteingreso}}==1){
        $('#idtipopersona').html('<option value=""></option><option value="1">Natural</option><option value="2">Jurídica</option>');
    }else if({{$users_prestamo->idfuenteingreso}}==2){
        $('#idtipopersona').html('<option value=""></option><option value="1">Natural</option>');
    }
    @endif
    @include('app.nuevosistema.select2',['input'=>'#idtipopersona' , 'val' =>  $usuario->idtipopersona ]);
    if({{$usuario->idtipopersona}}==1){
        $('#idtipodocumento').html('<option value=""></option><option value="1">DNI</option><option value="3">CE</option>');
    }else if({{$usuario->idtipopersona}}==2){
        $('#idtipodocumento').html('<option value=""></option><option value="2">RUC</option>');
    }
    @include('app.nuevosistema.select2',['input'=>'#idtipodocumento', 'val' =>  $users_prestamo ? $users_prestamo->idtipodocumento:0]);

    @include('app.nuevosistema.select2',['input'=>'#idgenero', 'val' => $usuario->idgenero !=0 ? $usuario->idgenero : '' ])
    @include('app.nuevosistema.select2',['input'=>'#idestadocivil', 'val' => $usuario->idestadocivil !=0 ? $usuario->idestadocivil : ''])
    @include('app.nuevosistema.select2',['input'=>'#idnivelestudio', 'val' => $usuario->idnivelestudio !=0 ? $usuario->idnivelestudio : ''])
  
    // DATOS DE PAREJA
    @include('app.nuevosistema.select2',['input'=>'#idocupacion_pareja', 'val' => $users_prestamo ? ( $users_prestamo->idocupacion_pareja != 0 ? $users_prestamo->idocupacion_pareja : '' ) : '' ])
    @include('app.nuevosistema.select2',['input'=>'#idnivelestudio_pareja', 'val' => $users_prestamo ? ( $users_prestamo->idnivelestudio_pareja != 0 ? $users_prestamo->idnivelestudio_pareja : '' ) : '' ])
   
    // CENTRO LABORAL DE: PAREJA/REPRESENTANTE LEG.
    @include('app.nuevosistema.select2',['json'=>'ubigeo','input'=>'#idubigeo','val'=>$usuario->idubigeo!=0?$usuario->idubigeo:''])
    @include('app.nuevosistema.select2',['input'=>'#idcondicionviviendalocal', 'val' => $users_prestamo ? ( $users_prestamo->idcondicionviviendalocal != 0 ? $users_prestamo->idcondicionviviendalocal : '' ) : '' ])
  
    // ACTIVIDAD ECONÓMICA DEL CLIENTE 
    @include('app.nuevosistema.select2',['input'=>'#idforma_ac_economica' , 'val' =>  $users_prestamo ? ( $users_prestamo->idforma_ac_economica != 0 ? $users_prestamo->idforma_ac_economica : '' ) : '' ]);
    @include('app.nuevosistema.select2',['input'=>'#idgiro_ac_economica' , 'val' =>  $users_prestamo ? ( $users_prestamo->idgiro_ac_economica != 0 ? $users_prestamo->idgiro_ac_economica : '' ) : '' ]);
    sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idempresa_ac_economica',val: {{ $users_prestamo ? $users_prestamo->idempresa_ac_economica : 0 }} });
    @include('app.nuevosistema.select2',['json'=>'ubigeo','input'=>'#idubigeo_ac_economica' , 'val' =>  $users_prestamo ? ( $users_prestamo->idubigeo_ac_economica != 0 ? $users_prestamo->idubigeo_ac_economica : '' ) : '' ]);
    @include('app.nuevosistema.select2',['input'=>'#idlocalnegocio_ac_economica' , 'val' =>  $users_prestamo ? ( $users_prestamo->idlocalnegocio_ac_economica != 0 ? $users_prestamo->idlocalnegocio_ac_economica : '' ) : '' ]);
  
    // CENTRO LABORAL DEL CLIENTE
    sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idempresa_laboral_cliente', val: {{ $users_prestamo ? $users_prestamo->idempresa_laboral_cliente : 0 }} });
    @include('app.nuevosistema.select2',['input'=>'#idtipocontrato_laboral_cliente', 'val' =>  $users_prestamo ? ( $users_prestamo->idtipocontrato_laboral_cliente != 0 ? $users_prestamo->idtipocontrato_laboral_cliente : '' ) : '' ]);

    // CENTRO LABORAL DE: PAREJA/REPRESENTANTE LEG.
    sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idempresa_laboral_pareja', val: {{ $users_prestamo ? $users_prestamo->idempresa_laboral_pareja : 0 }} });
    @include('app.nuevosistema.select2',['input'=>'#idtipocontrato_laboral_pareja', 'val' =>  $users_prestamo ? ( $users_prestamo->idtipocontrato_laboral_pareja != 0 ? $users_prestamo->idtipocontrato_laboral_pareja : '' ) : '' ]);

    // NEGOCIO DE: PAREJA/REPRESENTANTE LEG.
    @include('app.nuevosistema.select2',['input'=>'#idforma_negocio_pareja' , 'val' =>  $users_prestamo ? ( $users_prestamo->idforma_negocio_pareja != 0 ? $users_prestamo->idforma_negocio_pareja : '' ) : '' ]);
    @include('app.nuevosistema.select2',['input'=>'#idgiro_negocio_pareja' , 'val' =>  $users_prestamo ? ( $users_prestamo->idgiro_negocio_pareja != 0 ? $users_prestamo->idgiro_negocio_pareja : '' ) : '' ]);
    sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idempresa_negocio_pareja',val: {{ $users_prestamo ? $users_prestamo->idempresa_negocio_pareja : 0 }} });
    @include('app.nuevosistema.select2',['json'=>'ubigeo','input'=>'#idubigeo_negocio_pareja' , 'val' =>  $users_prestamo ? ( $users_prestamo->idubigeo_negocio_pareja != 0 ? $users_prestamo->idubigeo_negocio_pareja : '' ) : '' ]);
    @include('app.nuevosistema.select2',['input'=>'#idlocalnegocio_negocio_pareja' , 'val' =>  $users_prestamo ? ( $users_prestamo->idlocalnegocio_negocio_pareja != 0 ? $users_prestamo->idlocalnegocio_negocio_pareja : '' ) : '' ]);
    
  
    $('#cont_centrolaboraldepareja').css('display','none');
    $('#cont_negociodepareja').css('display','none');
    //$('#cont_actividadeconomicadelcliente').css('display','none');
    //$('#cont_centrolaboraldelcliente').css('display','none');
    $('div#cont_tipoinformacion').css('display','block');

    @if($users_prestamo)
        /*if({{$users_prestamo->idtipoinformacion}}==2 && {{$users_prestamo->idfuenteingreso}}==1){
            if({{$usuario->idestadocivil!=''?$usuario->idestadocivil:0}}==2 || {{$usuario->idestadocivil!=''?$usuario->idestadocivil:0}}==4){
                $('#cont_centrolaboraldepareja').css('display','block');
            }
            $('#cont_negociodepareja').css('display','block');
        }*/
        if({{$users_prestamo->idtipoinformacion}}==1 && {{$users_prestamo->idocupacion_pareja}}==1){
            if(({{$usuario->idestadocivil!=''?$usuario->idestadocivil:0}}==2 || {{$usuario->idestadocivil!=''?$usuario->idestadocivil:0}}==4) && {{$users_prestamo->idtipoinformacion}}==2){
                $('#cont_centrolaboraldepareja').css('display','block');
            }
            $('#cont_negociodepareja').css('display','block');
        }
        else if({{$users_prestamo->idtipoinformacion}}==2 && {{$users_prestamo->idocupacion_pareja}}==2){
            if({{$usuario->idestadocivil!=''?$usuario->idestadocivil:0}}==2 || {{$usuario->idestadocivil!=''?$usuario->idestadocivil:0}}==4){
                $('#cont_centrolaboraldepareja').css('display','block');
            }
        }
    @endif
  
   $("#idtipoinformacion").on("select2:select", function(e) {
        $('#cont_centrolaboraldepareja').css('display','none');
        $('#cont_negociodepareja').css('display','none');
        //$('#cont_actividadeconomicadelcliente').css('display','none');
        //$('#cont_centrolaboraldelcliente').css('display','none');
        $('div#cont_tipoinformacion').css('display','block');
        /*if($("#idfuenteingreso").val()==1){
            $('#cont_actividadeconomicadelcliente').css('display','block');
        }else if($("#idfuenteingreso").val()==2){
            $('#cont_centrolaboraldelcliente').css('display','block');
        }*/
        if($("#idtipoinformacion").val()==2 && $("#idfuenteingreso").val()==1){
            if($("#idestadocivil").val()==2 || $("#idestadocivil").val()==4 || $("#idtipopersona").val()==2){
                $('#cont_centrolaboraldepareja').css('display','block');
            }
            //$('#cont_negociodepareja').css('display','block');
        }
        if($("#idtipoinformacion").val()==1 && $("#idocupacion_pareja").val()==1){
            if(($("#idestadocivil").val()==2 || $("#idestadocivil").val()==4) && $("#idtipoinformacion").val()==2){
                $('#cont_centrolaboraldepareja').css('display','block');
            }
            //$('#cont_negociodepareja').css('display','block');
        }
        else if($("#idtipoinformacion").val()==2 && $("#idocupacion_pareja").val()==2){
            if($("#idestadocivil").val()==2 || $("#idestadocivil").val()==4){
                $('#cont_centrolaboraldepareja').css('display','block');
            }
        }
    });
  
    $('#cont_datosdelcliente').css('display','block');
    $('#cont_datosdelcliente_tipopersona').css('display','block');
  
    $("#idfuenteingreso").on("select2:select", function(e) {
        restaurar();
        $('#cont_datosdelcliente').css('display','block');
        $('#cont_datosdelcliente_tipopersona').css('display','block');
        if(e.params.data.id==1){
            $('#idtipopersona').html('<option value=""></option><option value="1">Natural</option><option value="2">Jurídica</option>');
        }else if(e.params.data.id==2){
            $('#idtipopersona').html('<option value=""></option><option value="1">Natural</option>');
        }
    });
  
    $('#cont_datosdelcliente_tipodocumento').css('display','block');
  
    $("#idtipopersona").on("select2:select", function(e) {
        restaurar_tipopersona()
        $('#cont_datosdelcliente_tipodocumento').css('display','block');
        if(e.params.data.id==1){
            $('#idtipodocumento').html('<option value=""></option><option value="1">DNI</option><option value="3">CE</option>');
        }else if(e.params.data.id==2){
            $('#idtipodocumento').html('<option value=""></option><option value="2">RUC</option>');
        }
    });
    
    @if($users_prestamo)
        $('#cont_datosdelcliente_data_adicional').css('display','block');
        if({{$users_prestamo->idtipodocumento}}==1){
            $('#cont_datosdelcliente_dni').css('display','block');
            $('#cont_datosdelcliente_dni_data').css('display','block');
            $('div#cont_datosdelcliente_data_adicional_dnice').css('display','block');
            $('#cont_domiciliodecliente').css('display','block');
            
        }else if({{$users_prestamo->idtipodocumento}}==2){
            $('#cont_datosdelcliente_ruc').css('display','block');
            $('#cont_datosdelcliente_ruc_data').css('display','block');
            $('div#cont_datosdelcliente_data_adicional_ruc').css('display','block');
            $('#cont_domiciliodecliente').css('display','block');
        }else if({{$users_prestamo->idtipodocumento}}==3){
            $('#cont_datosdelcliente_ce').css('display','block');
            $('#cont_datosdelcliente_ce_data').css('display','block');
            $('div#cont_datosdelcliente_data_adicional_dnice').css('display','block');
            $('#cont_domiciliodecliente').css('display','block');
        }
  
        
        if(({{$users_prestamo->idtipodocumento}}==1 || {{$users_prestamo->idtipodocumento}}==3) && {{$users_prestamo->idfuenteingreso}}==1){
            $('#cont_actividadeconomicadelcliente').css('display','block');
            $('#cont_centrolaboraldelcliente').css('display','block');
        }else if(({{$users_prestamo->idtipodocumento}}==1 || {{$users_prestamo->idtipodocumento}}==3) && {{$users_prestamo->idfuenteingreso}}==2){
            $('#cont_centrolaboraldelcliente').css('display','block');
        }else if({{$users_prestamo->idtipodocumento}}==2 && {{$users_prestamo->idfuenteingreso}}==1){
            $('#cont_actividadeconomicadelcliente').css('display','block');
            if({{$users_prestamo->idtipoinformacion}}==2){
                $('#cont_centrolaboraldepareja').css('display','block');
            }
        }
    @endif
        
    $("#idtipodocumento").on("select2:select", function(e) {
        restaurar_tipodocumento();
        $('#cont_datosdelcliente_data_adicional').css('display','block');
        if(e.params.data.id==1){
            $('#cont_datosdelcliente_dni').css('display','block');
            $('#cont_datosdelcliente_dni_data').css('display','block');
            $('div#cont_datosdelcliente_data_adicional_dnice').css('display','block');
            $('#cont_domiciliodecliente').css('display','block');
        }else if(e.params.data.id==2){
            $('#cont_datosdelcliente_ruc').css('display','block');
            $('#cont_datosdelcliente_ruc_data').css('display','block');
            $('div#cont_datosdelcliente_data_adicional_ruc').css('display','block');
            $('#cont_domiciliodecliente').css('display','block');
        }else if(e.params.data.id==3){
            $('#cont_datosdelcliente_ce').css('display','block');
            $('#cont_datosdelcliente_ce_data').css('display','block');
            $('div#cont_datosdelcliente_data_adicional_dnice').css('display','block');
            $('#cont_domiciliodecliente').css('display','block');
        }
        if((e.params.data.id==1 || e.params.data.id==3) && $("#idfuenteingreso").val()==1){
            $('#cont_actividadeconomicadelcliente').css('display','block');
            $('#cont_centrolaboraldelcliente').css('display','block');
        }else if((e.params.data.id==1 || e.params.data.id==3) && $("#idfuenteingreso").val()==2){
            $('#cont_centrolaboraldelcliente').css('display','block');
        }else if(e.params.data.id==2 && $("#idfuenteingreso").val()==1){
            $('#cont_actividadeconomicadelcliente').css('display','block');
            if($("#idtipoinformacion").val()==2){
                $('#cont_centrolaboraldepareja').css('display','block');
            }
        }
    });
  
    if({{$usuario->idestadocivil!=''?$usuario->idestadocivil:0}}==2 || {{$usuario->idestadocivil!=''?$usuario->idestadocivil:0}}==4){
        $('#cont_datosdepareja').css('display','block');
    }
  
    $("#idestadocivil").on("select2:select", function(e) {
        restaurar_estadocivil();
        if(e.params.data.id==2 || e.params.data.id==4){
            $('#cont_datosdepareja').css('display','block');
        }
    });
  
    @if($users_prestamo)
    //restaurar_ocupacion_pareja();
    $('#cont_negociodepareja').css('display','none');
  
    if({{$users_prestamo->idocupacion_pareja}}==1 && {{$users_prestamo->idtipoinformacion}}==2){
        $('#cont_negociodepareja').css('display','block');
    }
    else if({{$users_prestamo->idocupacion_pareja}}==2 && {{$users_prestamo->idtipoinformacion}}==2){
        $('#cont_centrolaboraldepareja').css('display','block');
    }
    @endif
  
    $("#idocupacion_pareja").on("select2:select", function(e) {
        restaurar_ocupacion_pareja();
        if(e.params.data.id==1 && $("#idtipoinformacion").val()==2){
            $('#cont_negociodepareja').css('display','block');
        }
        else if(e.params.data.id==2 && $("#idtipoinformacion").val()==2){
            $('#cont_centrolaboraldepareja').css('display','block');
        }
    });
   
    function restaurar(){
        $('#cont_datosdelcliente').css('display','none');
        $('#cont_datosdelcliente_tipopersona').css('display','none');
        $('#cont_actividadeconomicadelcliente').css('display','none');
        $('#cont_centrolaboraldelcliente').css('display','none');
        $('#cont_domiciliodecliente').css('display','none');
        $('#idtipodocumento').html('<option value=""></option>');
      
        $('#cont_datosdelcliente_ruc').css('display','none');
        $('#cont_datosdelcliente_dni').css('display','none');
        $('#cont_datosdelcliente_ce').css('display','none');
        $('#cont_datosdelcliente_ruc_data').css('display','none');
        $('#cont_datosdelcliente_dni_data').css('display','none');
        $('#cont_datosdelcliente_ce_data').css('display','none');
        $('#cont_datosdelcliente_data_adicional').css('display','none');
        $('div#cont_datosdelcliente_data_adicional_ruc').css('display','none');
        $('div#cont_datosdelcliente_data_adicional_dnice').css('display','none');
    }
    function restaurar_tipopersona(){
        $('#cont_datosdelcliente_tipodocumento').css('display','none');
        $('#cont_datosdelcliente_ruc').css('display','none');
        $('#cont_datosdelcliente_dni').css('display','none');
        $('#cont_datosdelcliente_ce').css('display','none');
        $('#cont_datosdelcliente_ruc_data').css('display','none');
        $('#cont_datosdelcliente_dni_data').css('display','none');
        $('#cont_datosdelcliente_ce_data').css('display','none');
        $('#cont_datosdelcliente_data_adicional').css('display','none');
        $('div#cont_datosdelcliente_data_adicional_ruc').css('display','none');
        $('div#cont_datosdelcliente_data_adicional_dnice').css('display','none');
    }
    function restaurar_tipodocumento(){
        $('#cont_datosdelcliente_ruc').css('display','none');
        $('#cont_datosdelcliente_dni').css('display','none');
        $('#cont_datosdelcliente_ce').css('display','none');
        $('#cont_datosdelcliente_ruc_data').css('display','none');
        $('#cont_datosdelcliente_dni_data').css('display','none');
        $('#cont_datosdelcliente_ce_data').css('display','none');
        $('#cont_datosdelcliente_data_adicional').css('display','none');
        $('div#cont_datosdelcliente_data_adicional_ruc').css('display','none');
        $('div#cont_datosdelcliente_data_adicional_dnice').css('display','none');
        $('#cont_actividadeconomicadelcliente').css('display','none');
        $('#cont_centrolaboraldelcliente').css('display','none');
        $('#cont_domiciliodecliente').css('display','none');
        $('#cont_centrolaboraldepareja').css('display','none');
        $('#cont_negociodepareja').css('display','none');
    }
    function restaurar_estadocivil(){
        $('#cont_datosdepareja').css('display','none');
        $('#cont_negociodepareja').css('display','none');
        $('#cont_centrolaboraldepareja').css('display','none');
    }
    function restaurar_ocupacion_pareja(){
        $('#cont_negociodepareja').css('display','none');
        $('#cont_centrolaboraldepareja').css('display','none');
    }
  
    // INICIO CHECK CASA NEGOCIO
    @if($users_prestamo)
    if('{{$users_prestamo->casanegocio}}'=='SI'){
        $('div#cont_casanegocio').css('display','none');
    }
    @endif
  
    $('#casanegocio').change(function() {
        $('div#cont_casanegocio').css('display','none');
        if(!$('#casanegocio').is(":checked")){
            $('div#cont_casanegocio').css('display','block');
        }
    });
    // FIN CHECK CASA NEGOCIO

  
    function agregar_cliente_financiera(tabla,valor=''){
        
        var num   = $("#tabla-"+tabla+" > tbody").attr('num');
        var cant  = $("#tabla-"+tabla+" > tbody > tr").length;
    
        var tdtable = `<td></td>`;
        if(cant>0){
            tdtable = `<td><a href="javascript:;" onclick="eliminar_cliente_financiera(${num},'${tabla}')" class="btn btn-danger "><i class="fa-solid fa-trash"></i></td>`;
        }
    
        var disabled = '';
        if(num==0){
           disabled = '{{ $prendaria>0 ? 'disabled' : '' }}';
        }
        var html='<tr id="'+num+'">'+
                    '<td><input type="number" class="form-control" id="'+tabla+'texto'+num+'" value="'+valor+'" '+disabled+'></td>'+
                    tdtable+
                '</tr>';

        $("#tabla-"+tabla+" > tbody").append(html);
        $("#tabla-"+tabla+" > tbody").attr('num',parseInt(num)+1);  
        
    }
    function eliminar_cliente_financiera(num,tabla){
        $("#tabla-"+tabla+" > tbody > tr#"+num).remove();
    }
    function seleccionar_cliente_financiera(tabla){
        var data = [];
        $("#tabla-"+tabla+" > tbody > tr").each(function() {
            var num = $(this).attr('id');    
            data.push({ 
                valor: $('#'+tabla+'texto'+num).val(),
            });
        });
        return JSON.stringify(data);
    }
    function agregar_referencia(referencia='', vinculo='', celular=''){

        var num   = $("#tabla-referencia > tbody").attr('num');
        var cant  = $("#tabla-referencia > tbody > tr").length;

        var tdeliminar = '<td></td>';
        if(cant>0){
            tdeliminar = '<td><a href="javascript:;" onclick="eliminar_cliente_financiera('+num+',`referencia`)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></td>';
        }

        var disabled = '';
        if(num==0){
           disabled = '{{ $prendaria>0 ? 'disabled' : '' }}';
        }

        var tabla= '<tr id="'+num+'">'+
                    '<td><input type="number" class="form-control" id="celular'+num+'" value="'+celular+'" '+disabled+'></td>'+
                    '<td><input type="text" class="form-control" id="vinculo'+num+'" value="'+vinculo+'" '+disabled+'></td>'+
                    '<td><input type="text" class="form-control" id="referencia'+num+'" value="'+referencia+'" '+disabled+'></td>'+
                    tdeliminar+
                   '</tr>';

        $("#tabla-referencia > tbody").append(tabla);
        $("#tabla-referencia > tbody").attr('num',parseInt(num)+1);  
        
    }
    function seleccionar_referencia(){
        var data = [];
        $("#tabla-referencia > tbody > tr").each(function() {
            var num = $(this).attr('id');    
            data.push({ 
                referencia: $('#referencia'+num).val(),
                vinculo: $('#vinculo'+num).val(),
                celular: $('#celular'+num).val(),
            });
        });
        return JSON.stringify(data);
    }

    function buscar_dni(){
        limpiarcampos();
        $('#resultado-dni').html('');
        var identificacion = $('#dni').val();
        if(identificacion.length==8){
            load('#resultado-dni');
            $.ajax({
                url:"{{url('backoffice/'.$tienda->id.'/inicio/showbuscaridentificacion')}}",
                type:'GET',
                data: {
                    buscar_identificacion : identificacion,
                    tipo_persona : 1
                },
                success: function (respuesta){
                    $('#resultado-dni').html('');
                    if(respuesta.resultado=='ERROR'){
                        $('#resultado-dni-resultado').html(respuesta.mensaje);
                        $('#nombre').val('');
                        $('#apellidopaterno').val('');
                        $('#apellidomaterno').val('');
                    }else{
                        $('#nombre').val(respuesta.nombres);
                        $('#apellidopaterno').val(respuesta.apellidoPaterno);
                        $('#apellidomaterno').val(respuesta.apellidoMaterno);
                    }  
                }
            })
        }else if(identificacion!='' && identificacion==0){
            $('#resultado-dni').html('');
            $('#cont-natural-apellidopaterno').html('Apellido Paterno');
            $('#cont-natural-apellidomaterno').html('Apellido Materno');
        }
    }
    function buscar_ruc(){
        limpiarcampos();
        $('#resultado-ruc').html('');
        var identificacion = $('#ruc').val();
        if(identificacion.length==11){
            load('#resultado-ruc');
            $.ajax({
                url:"{{url('backoffice/'.$tienda->id.'/inicio/showbuscaridentificacion')}}",
                type:'GET',
                data: {
                    buscar_identificacion : identificacion,
                    tipo_persona : 2
                },
                success: function (respuesta){
                    $('#resultado-ruc').html('');
                    if(respuesta.resultado=='ERROR'){
                        $('#resultado-ruc-resultado').html(respuesta.mensaje);
                        $('#idubigeo').val(null).trigger("change");
                        $('#direccion').val('');
                    }else{
                        $('#nombrecomercial').val(respuesta.nombreComercial);
                        $('#razonsocial').val(respuesta.razonSocial);
                        if(respuesta.ubigeo!=''){
                            $('#idubigeo').val(respuesta.idubigeo).trigger("change");
                            //$('#idubigeo').html('<option value="'+respuesta.idubigeo+'">'+respuesta.ubigeo+'</option>');
                        }else{
                            $('#idubigeo').val(null).trigger("change");
                        }
                        $('#direccion').val(respuesta.direccion);
                    }  
                }
            })
        }else if(identificacion!='' && identificacion==0){
            $('#resultado-ruc').html('');
        }
    }
    function limpiarcampos(){
        $('#nombre').val('');
        $('#apellidopaterno').val('');
        $('#apellidomaterno').val('');
        $('#nombrecomercial').val('');
        $('#razonsocial').val('');
    
        $('#cont-natural-apellidopaterno').html('Apellido Paterno *');
        $('#cont-natural-apellidomaterno').html('Apellido Materno *');
        $('#cont-juridica-razonsocial').html('Razón Social *');
    
        $('#resultado-dni-resultado').html('');
        $('#resultado-ruc-resultado').html('');
    }
</script>