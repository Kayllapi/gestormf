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
                <button type="button" onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/usuario/create?view=autorizacion')}}'})" class="btn btn-success"><i class="fa fa-pencil"></i> Editar</button></button>
            </div>
        </div>
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
        <div class="mb-1">
        <span class="badge d-block">DATOS DEL CLIENTE</span>
        </div>
        <div class="row ">
            <div class="col-md-4 container-tipopersona">
                <label>Tipo de Persona <span class="text-danger">(*)</span></label>
                <select class="form-control" id="idtipopersona" autorizacion-form>
                    <option value=""></option>
                    <option value="1">NATURAL</option>
                    <option value="2">JURÍDICA</option>
                </select>
            </div>
            <div class="col-md-4">
                
                <label>Tipo de Documento <span class="text-danger">(*)</span></label>
                <select class="form-control" id="idtipodocumento" autorizacion-form>
                    <option value=""></option>
                </select>
            </div>
            <div class="col-md-4">
                <div id="cont-juridica" class="d-none">
                    <label 
                            data-bs-toggle="popover" 
                            data-bs-placement="right" 
                            data-bs-content="Puedes registrar el RUC con 0, si aún no tiene ninguna Empresa.">RUC (11 Digitos) <span class="text-danger">(*)</span>
                        <i class="fa-solid fa-circle-info"></i>
                    </label>
                    <input type="number" class="form-control" id="ruc" value="{{ $usuario->identificacion }}" autorizacion-form onkeyup="buscar_ruc()"/>
                    <div id="resultado-ruc" style="float: right;margin-top: -35px;margin-right: 3px;text-align: right;"></div>
                    <div id="resultado-ruc-resultado" style="color:#e22d02;"></div>
                </div>
                <div id="cont-natural" class="d-none">
                    <label 
                            data-bs-toggle="popover" 
                            data-bs-placement="right" 
                            data-bs-content="Puedes registrar el DNI con 0, si el cliente no tiene identificación.">DNI (8 Digitos) <span class="text-danger">(*)</span>
                        <i class="fa-solid fa-circle-info"></i>
                    </label>
                    <input type="number" class="form-control" id="dni" value="{{ $usuario->identificacion }}" autorizacion-form onkeyup="buscar_dni()"/>
                    <div id="resultado-dni" style="float: right;margin-top: -35px;margin-right: 3px;text-align: right;"></div>
                    <div id="resultado-dni-resultado" style="color:#e22d02;"></div>
                </div>
                <div id="cont-carnetextranjeria" class="d-none">
                    <label>Carnet Extranjería *</label>
                    <input type="number" class="form-control" value="{{ $usuario->identificacion }}" autorizacion-form id="carnetextranjeria">
                </div>
            </div>
            

        </div>
        <div class="row d-none" id="cont-juridica1">
            <div class="col-md-6">
                <div class="mb-1">
                    <label>Nombre Comercial</label>
                    <input type="text" class="form-control" id="nombrecomercial" value="{{ $usuario->nombre }}" autorizacion-form/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-1">
                    <label id="cont-juridica-razonsocial">Razón Social  <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="razonsocial" value="{{ $usuario->razonsocial }}" autorizacion-form/>
                </div>
            </div>
        </div>
        <div class="row d-none" id="cont-natural1">
            <div class="col-md-4">
                <div class="mb-1">
                    <label>Nombre  <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="nombre" value="{{ $usuario->nombre }}" autorizacion-form/>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-1">
                    <label id="cont-natural-apellidopaterno">Apellido Paterno  <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="apellidopaterno" value="{{ $usuario->apellidopaterno }}" autorizacion-form>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-1">
                    <label id="cont-natural-apellidomaterno">Apellido Materno  <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="apellidomaterno" value="{{ $usuario->apellidomaterno }}" autorizacion-form>
                </div>
            </div>  
        </div>
        <div class="row d-none"  id="cont-carnetextranjeria1">
            <div class="col-md-4">
                <div class="mb-1">
                    <label>Nombre  <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="nombre_carnetextranjeria" value="{{ $usuario->nombre }}">
                </div>
            </div> 
            <div class="col-md-4">
                <div class="mb-1">
                    <label>Apellido Paterno <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="apellidopaterno_carnetextranjeria" value="{{ $usuario->apellidopaterno }}">
                </div>
            </div> 
            <div class="col-md-4">
                <div class="mb-1">
                    <label>Apellido Materno <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="apellidomaterno_carnetextranjeria" value="{{ $usuario->apellidomaterno }}">
                </div>
            </div> 
        </div>
        <!-- INICIO - NO USADO -->
        <div class="mb-1 d-none">
            <label>Número de Teléfono <span class="text-danger">(*)</span></label>
            <input type="text" class="form-control" id="numerotelefono"/>
        </div>
        <div class="mb-1 d-none">
            <label>Imagen de Perfil</label>
            <div class="fuzone" id="cont-fileupload" style="height: 206px;">
                <div class="fuzone-text"><i class="fa-solid fa-cloud-arrow-up"></i> Haga clic aquí o suelte para cargar</div>
                <input type="file" class="upload" id="imagen">
                <div id="resultado-logo"></div>
            </div>
        </div>
        <div class="mb-1 d-none">
            <label>Correo Electrónico </label>
            <input type="text" class="form-control" id="email"/>
        </div>
        <div class="mb-1 d-none">
            <label>Ubicación (Mover Marcador)</label>
            <div id="domicilio_mapa" class="mapa" style="height: 207px;"></div>
            <input type="hidden" class="form-control" id="domicilio_mapa_latitud"/>
            <input type="hidden" class="form-control" id="domicilio_mapa_longitud"/>
        </div>
        <!-- FIN - NO USADO -->
        <div class="row">
            <div class="col-sm-12 col-md-4 d-none container-representante-legal">
                <div class="mb-1">
                    <label>Nro Documento Representante Legal <span class="text-danger">(*)</span></label>
                    <input type="number" class="form-control" id="documento_representantelegal" value="{{ $users_prestamo ? $users_prestamo->documento_representantelegal : '' }}">
                </div>
            </div>
            <div class="col-sm-12 col-md-8 d-none container-representante-legal">
                <div class="mb-1">
                    <label>Nombre Completo Representante Legal <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="nombrecompelto_representantelegal" value="{{ $users_prestamo ? $users_prestamo->nombrecompelto_representantelegal : '' }}">
                </div>
            </div>

            <div class="col-sm-12 col-md-4 container-data-per-natural d-none">
                <div class="mb-1">
                    <label>Genero <span class="text-danger">(*)</span></label>
                    <select class="form-select" id="idgenero">
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
                    <input type="date" class="form-control" id="fechanacimientocreacion" value="{{ $users_prestamo ? ( $usuario->fechanacimiento != '' ? $usuario->fechanacimiento : date('Y-m-d') ) : date('Y-m-d') }}">
                </div>
                
            </div>
            
            <div class="col-sm-12 col-md-4 container-data-per-natural d-none">
                <div class="mb-1">
                    <label>Estado Civil <span class="text-danger">(*)</span></label>
                    <select class="form-select" id="idestadocivil">
                        <option value=""></option>
                        @foreach($estadocivil as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                        @endforeach
                    </select>
                    
                </div>
            </div>
            <div class="col-sm-12 col-md-4 container-data-per-natural d-none">
                <div class="mb-1">
                    <label>Nivel de Estudios <span class="text-danger">(*)</span></label>
                    <select class="form-select" id="idnivelestudio">
                        <option value=""></option>
                        @foreach($nivelestudio as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-12 col-md-4 container-data-per-natural d-none">
                <div class="mb-1">
                    <label>Profesión </label>
                    <input type="text" class="form-control" id="profesion" value="{{ $users_prestamo ? $users_prestamo->profesion : '' }}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="mb-1">
                    <label>Correo Electrónico </label>
                    <input type="text" class="form-control" id="correo_electronico" value="{{ $users_prestamo ? $users_prestamo->correo_electronico : '' }}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="mb-1">
                    <label>Telf./Celular <span class="text-danger">(*)</span>
                        <a href="javascript:;" onclick="agregar_cliente_financiera('celular-cliente')">
                            <i class="fa-solid fa-circle-plus"></i>
                        </a>
                    </label>
                    <table class="table tabla-interno" id="tabla-celular-cliente">
                        <tbody num="0">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mb-1 mt-1 continer-data-pareja d-none">
            <span class="badge d-block">DATOS DE PAREJA</span>
        </div>
        <div class="row continer-data-pareja d-none">
            <div class="col-sm-12 d-none">
                <div class="mb-1">
                    <label>Pareja <span class="text-danger">(*)</span></label>
                    <select class="form-control" id="idpareja">
                        <option></option>
                    </select>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>DNI/CE <span class="text-danger">(*)</span></label>
                    <input type="number" class="form-control" id="dni_pareja" value="{{ $users_prestamo ? $users_prestamo->dni_pareja : '' }}">
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Nombres <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="nombres_pareja" value="{{ $users_prestamo ? $users_prestamo->nombres_pareja : '' }}">
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Apellido Paterno <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="ap_paterno_pareja" value="{{ $users_prestamo ? $users_prestamo->ap_paterno_pareja : '' }}">
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Apellido Materno <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="ap_materno_pareja" value="{{ $users_prestamo ? $users_prestamo->ap_materno_pareja : '' }}">
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Ocupación <span class="text-danger">(*)</span></label>
                    <select class="form-control" id="idocupacion_pareja">
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
                    <input type="text" class="form-control" id="profesion_pareja" value="{{ $users_prestamo ? $users_prestamo->profesion_pareja : '' }}">
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Nivel de Estudios</label>
                    <select class="form-control" id="idnivelestudio_pareja">
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
                        <a href="javascript:;" onclick="agregar_cliente_financiera('celular-pareja')">
                            <i class="fa-solid fa-circle-plus"></i>
                        </a>
                    </label>
                    <table class="table tabla-interno" id="tabla-celular-pareja">
                        <tbody num="0">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mb-1 mt-1">
            <span class="badge d-block">DOMICILIO CLIENTE</span>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-7">
                <div class="mb-1">
                <label id="cont-direccion">Dirección <span class="text-danger">(*)</span></label>
                <input type="text" class="form-control" id="direccion"  value="{{ $usuario->direccion }}" disabled/>
                </div>
            </div>
            <div class="col-sm-12 col-md-5">
                <div class="mb-1">
                <label id="cont-ubigeo">Distrito – Provincia – Departamento <span class="text-danger">(*)</span></label>
                <select class="form-control" id="idubigeo" disabled>
                    <option></option>
                </select>
                </div>
            </div>
            <div class="col-sm-12 col-md-12">
                <div class="mb-1">
                    <label>Referencia Ubicación <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="referencia_direccion" value="{{ $users_prestamo ? $users_prestamo->referencia_direccion : '' }}">
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="mb-1">
                    <label>Suministro Elect(Caso no exista N° Domicilio)</label>
                    <input type="text" class="form-control" id="suministro_electrocentro" value="{{ $users_prestamo ? $users_prestamo->suministro_electrocentro : '' }}">
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="mb-1">
                    <label>Condición de Vivienda/Local <span class="text-danger">(*)</span></label>
                    <select class="form-control" id="idcondicionviviendalocal">
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
                        <a href="javascript:;" onclick="agregar_referencia()">
                            <i class="fa-solid fa-circle-plus"></i>
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

        <div class="mb-1 mt-1 continer-data-actividadeconomica d-none">
            <span class="badge d-block">ACTIVIDAD ECONÓMICA CLIENTE</span>
        </div>
        <div class="row continer-data-actividadeconomica d-none">
            
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Forma de Activ. Econom. <span class="text-danger">(*)</span></label>
                    <select class="form-control" id="idforma_ac_economica">
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
                    <select class="form-control" id="idgiro_ac_economica">
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
                    <input type="text" class="form-control" id="descripcion_ac_economica" value="{{ $users_prestamo ? $users_prestamo->descripcion_ac_economica : '' }}">
                </div>
            </div>
            
            <div class="col-sm-8 d-none">
                <div class="mb-1">
                    <label>Empresa  <span class="text-danger">(*)</span></label>
                    <select class="form-control" id="idempresa_ac_economica">
                        <option></option>
                    </select>
                </div>
            </div>
            <div class="col-sm-12 col-md-4 container-empresa-ac-economica d-none">
                <div class="mb-1">
                    <label>RUC</label>
                    <input type="number" class="form-control" id="ruc_ac_economica" value="{{ $users_prestamo ? $users_prestamo->ruc_ac_economica : '' }}">
                </div>
            </div>
            <div class="col-sm-12 col-md-8 container-empresa-ac-economica d-none">
                <div class="mb-1">
                    <label>Nombre: Persona Natural/Persona Jurídica</label>
                    <input type="text" class="form-control" id="razonsocial_ac_economica" value="{{ $users_prestamo ? $users_prestamo->razonsocial_ac_economica : '' }}">
                </div>
            </div>
            
            <div class="col-sm-12 col-md-3">
                <div class="mb-1">
                    <label>&nbsp; </label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"  id="casanegocio" {{ $users_prestamo ? ($users_prestamo->casanegocio == 'SI' ? 'checked' : ''):'' }} >
                        <label class="form-check-label" for="casanegocio" style="margin-top: 0px">
                        Casa/Negocio:
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-5 container-casanegocio">
                <div class="mb-1">
                    <label>Direccion  <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="direccion_ac_economica" value="{{ $users_prestamo ? $users_prestamo->direccion_ac_economica : '' }}" disabled>
                </div>
            </div>

            <div class="col-sm-12 col-md-4 container-casanegocio">
                <div class="mb-1">
                    <label id="cont-ubigeo">Distrito – Provincia – Departamento <span class="text-danger">(*)</span></label>
                    <select class="form-control" id="idubigeo_ac_economica">
                        <option></option>
                    </select>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 container-casanegocio">
                <div class="mb-1">
                    <label>Referencia de Ubicación <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="referencia_ac_economica" value="{{ $users_prestamo ? $users_prestamo->referencia_ac_economica : '' }}">
                </div>
            </div>
            <div class="col-sm-12 col-md-4 container-casanegocio">
                <div class="mb-1">
                    <label>Local Negocio  <span class="text-danger">(*)</span></label>
                    <select class="form-control" id="idlocalnegocio_ac_economica">
                        <option value=""></option>
                        @foreach($condicionviviendalocal as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>


        </div>

        <div class="mb-1 mt-1 continer-data-centrolaboral d-none">
            <span class="badge d-block">CENTRO LABORAL CLIENTE</span>
        </div>
        <div class="row continer-data-centrolaboral d-none">
            <div class="col-sm-6 d-none">
                <div class="mb-1">
                    <label>Empresa</label>
                    <select class="form-control" id="idempresa_laboral_cliente">
                        <option></option>
                    </select>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>RUC</label>
                    <input type="number" class="form-control" id="ruc_laboral_cliente" value="{{ $users_prestamo ? $users_prestamo->ruc_laboral_cliente : '' }}">
                </div>
            </div>
            <div class="col-sm-12 col-md-8">
                <div class="mb-1">
                    <label>Nombre: Persona Natural/Persona Jurídica <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="razonsocial_laboral_cliente" value="{{ $users_prestamo ? $users_prestamo->razonsocial_laboral_cliente : '' }}">
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Fecha Inicio</label>
                    <input type="date" class="form-control" id="fechainicio_laboral_cliente" value="{{ $users_prestamo ? $users_prestamo->fechainicio_laboral_cliente : '' }}">
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Antiguedad (en años) <span class="text-danger">(*)</span></label>
                    <input type="text"  class="form-control" id="antiguedad_laboral_cliente" value="{{ $users_prestamo ? $users_prestamo->antiguedad_laboral_cliente : ''}}">
                </div>
            </div>

            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Cargo <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="cargo_laboral_cliente" value="{{ $users_prestamo ? $users_prestamo->cargo_laboral_cliente : '' }}">
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Área </label>
                    <input type="text" class="form-control" id="area_laboral_cliente" value="{{ $users_prestamo ? $users_prestamo->area_laboral_cliente : '' }}">
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Contrato Laboral</label>
                    <select class="form-control" id="idtipocontrato_laboral_cliente">
                        <option value=""></option>
                        @foreach($contratolaboral as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div id="cont-centrolaboral-pareja" style="display:none;">
            <div class="mb-1 mt-1">
                <span class="badge">CENTRO LABORAL DE: PAREJA/REPRESENTANTE LEG.</span>
            </div>
            <div class="row">
                <div class="col-sm-6 d-none">
                    <div class="mb-1">
                        <label>Empresa </label>
                        <select class="form-control" id="idempresa_laboral_pareja">
                            <option></option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>RUC</label>
                        <input type="number" class="form-control" id="ruc_laboral_pareja" value="{{ $users_prestamo ? $users_prestamo->ruc_laboral_pareja : '' }}">
                    </div>
                </div>
                <div class="col-sm-12 col-md-8">
                    <div class="mb-1">
                        <label>Nombre: Persona Natural/Persona Jurídica <span class="text-danger">(*)</span></label>
                        <input type="text" class="form-control" id="razonsocial_laboral_pareja" value="{{ $users_prestamo ? $users_prestamo->razonsocial_laboral_pareja : '' }}">
                    </div>
                </div>

                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Fecha Incio <span class="text-danger">(*)</span></label>
                        <input type="date" class="form-control" id="fechainicio_laboral_pareja" value="{{ $users_prestamo ? ( $users_prestamo->fechainicio_laboral_pareja !='' ? $users_prestamo->fechainicio_laboral_pareja : date('Y-m-d') ) : date('Y-m-d') }}">
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Antiguedad (en años) </label>
                        <input type="text"  class="form-control" id="antiguedad_laboral_pareja" value="{{ $users_prestamo ? $users_prestamo->antiguedad_laboral_pareja : '' }}">
                    </div>
                </div>

                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Cargo </label>
                        <input type="text" class="form-control" id="cargo_laboral_pareja" value="{{ $users_prestamo ? $users_prestamo->cargo_laboral_pareja : '' }}">
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Área </label>
                        <input type="text" class="form-control" id="area_laboral_pareja" value="{{ $users_prestamo ? $users_prestamo->area_laboral_pareja : '' }}">
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Contrato Laboral <span class="text-danger">(*)</span></label>
                        <select class="form-control" id="idtipocontrato_laboral_pareja">
                            <option value=""></option>
                            @foreach($contratolaboral as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div id="cont-negocio-pareja" style="display:none;">
            <div class="mb-1 mt-1">
                <span class="badge">NEGOCIO DE: PAREJA/REPRESENTANTE LEG.</span>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Forma de Activ. Econom. <span class="text-danger">(*)</span></label>
                        <select class="form-control" id="idforma_negocio_pareja">
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
                        <select class="form-control" id="idgiro_negocio_pareja">
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
                        <input type="text" class="form-control" id="descripcion_negocio_pareja" value="{{ $users_prestamo ? $users_prestamo->descripcion_negocio_pareja : '' }}">
                    </div>
                </div>
                
                <div class="col-sm-6 d-none">
                    <div class="mb-1">
                        <label>Empresa <span class="text-danger">(*)</span></label>
                        <select class="form-control" id="idempresa_negocio_pareja">
                            <option></option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 container-empresa-negocio-pareja">
                    <div class="mb-1">
                        <label>RUC</label>
                        <input type="number" class="form-control" id="ruc_negocio_pareja" value="{{ $users_prestamo ? $users_prestamo->ruc_negocio_pareja : '' }}">
                    </div>
                </div>
                <div class="col-sm-12 col-md-8 container-empresa-negocio-pareja">
                    <div class="mb-1">
                        <label>Nombre: Persona Natural/Persona Jurídica</label>
                        <input type="text" class="form-control" id="razonsocial_negocio_pareja" value="{{ $users_prestamo ? $users_prestamo->razonsocial_negocio_pareja : '' }}">
                    </div>
                </div>
                
                <div class="col-sm-12 col-md-7">
                    <div class="mb-1">
                        <label>Direccion <span class="text-danger">(*)</span></label>
                        <input type="text" class="form-control" id="direccion_negocio_pareja" value="{{ $users_prestamo ? $users_prestamo->direccion_negocio_pareja : '' }}">
                    </div>
                </div>

                <div class="col-sm-12 col-md-5">
                    <div class="mb-1">
                        <label id="cont-ubigeo">Distrito – Provincia – Departamento <span class="text-danger">(*)</span></label>
                        <select class="form-control" id="idubigeo_negocio_pareja">
                            <option></option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-md-7">
                    <div class="mb-1">
                        <label>Referencia de Ubicación</label>
                        <input type="text" class="form-control" id="referencia_negocio_pareja"  value="{{ $users_prestamo ? $users_prestamo->referencia_negocio_pareja : '' }}">
                    </div>
                </div>
                <div class="col-sm-12 col-md-5">
                    <div class="mb-1">
                        <label>Local Negocio <span class="text-danger">(*)</span></label>
                        <select class="form-control" id="idlocalnegocio_negocio_pareja">
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
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form> 
<script>
    autorizar_edicion();
    function autorizar_edicion(estado = true){
        $('#form-editar-cliente').find('select[autorizacion-form]').attr('disabled',estado);
        $('#form-editar-cliente').find('input[autorizacion-form]').attr('disabled',estado);
        
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


    @include('app.nuevosistema.select2',['json'=>'ubigeo','input'=>'#idubigeo','val'=>$usuario->idubigeo!=0?$usuario->idubigeo:''])

    @include('app.nuevosistema.select2',['input'=>'#idtipopersona','val'=>$usuario->idtipopersona])
    @include('app.nuevosistema.select2',['input'=>'#idtipodocumento'])
    
    
    @include('app.nuevosistema.select2',['input'=>'#idgenero', 'val' => $usuario->idgenero !=0 ? $usuario->idgenero : '' ])
    @include('app.nuevosistema.select2',['input'=>'#idestadocivil', 'val' => $usuario->idestadocivil !=0 ? $usuario->idestadocivil : ''])
    @include('app.nuevosistema.select2',['input'=>'#idnivelestudio', 'val' => $usuario->idnivelestudio !=0 ? $usuario->idnivelestudio : ''])

    


    @include('app.nuevosistema.select2',['input'=>'#idtipoinformacion' , 'val' =>  $users_prestamo ? ( $users_prestamo->idtipoinformacion != 0 ? $users_prestamo->idtipoinformacion : '' ) : '' ]);
    @include('app.nuevosistema.select2',['input'=>'#idfuenteingreso' , 'val' =>  $users_prestamo ? ( $users_prestamo->idfuenteingreso != 0 ? $users_prestamo->idfuenteingreso : '' ) : '' ]);
    
    sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idpareja', val: {{ $users_prestamo ? $users_prestamo->idpareja : 0 }} });
    
    
    @include('app.nuevosistema.select2',['input'=>'#idocupacion_pareja', 'val' => $users_prestamo ? ( $users_prestamo->idocupacion_pareja != 0 ? $users_prestamo->idocupacion_pareja : '' ) : '' ])
    @include('app.nuevosistema.select2',['input'=>'#idnivelestudio_pareja', 'val' => $users_prestamo ? ( $users_prestamo->idnivelestudio_pareja != 0 ? $users_prestamo->idnivelestudio_pareja : '' ) : '' ])
    @include('app.nuevosistema.select2',['input'=>'#idcondicionviviendalocal', 'val' => $users_prestamo ? ( $users_prestamo->idcondicionviviendalocal != 0 ? $users_prestamo->idcondicionviviendalocal : '' ) : '' ])

    @include('app.nuevosistema.select2',['input'=>'#idforma_ac_economica' , 'val' =>  $users_prestamo ? ( $users_prestamo->idforma_ac_economica != 0 ? $users_prestamo->idforma_ac_economica : '' ) : '' ]);
    @include('app.nuevosistema.select2',['input'=>'#idgiro_ac_economica' , 'val' =>  $users_prestamo ? ( $users_prestamo->idgiro_ac_economica != 0 ? $users_prestamo->idgiro_ac_economica : '' ) : '' ]);
    sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idempresa_ac_economica',val: {{ $users_prestamo ? $users_prestamo->idempresa_ac_economica : 0 }} });
    
    @include('app.nuevosistema.select2',['json'=>'ubigeo','input'=>'#idubigeo_ac_economica' , 'val' =>  $users_prestamo ? ( $users_prestamo->idubigeo_ac_economica != 0 ? $users_prestamo->idubigeo_ac_economica : '' ) : '' ]);
    @include('app.nuevosistema.select2',['input'=>'#idlocalnegocio_ac_economica' , 'val' =>  $users_prestamo ? ( $users_prestamo->idlocalnegocio_ac_economica != 0 ? $users_prestamo->idlocalnegocio_ac_economica : '' ) : '' ]);
    
    sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idempresa_laboral_cliente', val: {{ $users_prestamo ? $users_prestamo->idempresa_laboral_cliente : 0 }} });
    @include('app.nuevosistema.select2',['input'=>'#idtipocontrato_laboral_cliente', 'val' =>  $users_prestamo ? ( $users_prestamo->idtipocontrato_laboral_cliente != 0 ? $users_prestamo->idtipocontrato_laboral_cliente : '' ) : '' ]);

    sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idempresa_laboral_pareja', val: {{ $users_prestamo ? $users_prestamo->idempresa_laboral_pareja : 0 }} });
    @include('app.nuevosistema.select2',['input'=>'#idtipocontrato_laboral_pareja', 'val' =>  $users_prestamo ? ( $users_prestamo->idtipocontrato_laboral_pareja != 0 ? $users_prestamo->idtipocontrato_laboral_pareja : '' ) : '' ]);


    @include('app.nuevosistema.select2',['input'=>'#idforma_negocio_pareja' , 'val' =>  $users_prestamo ? ( $users_prestamo->idforma_negocio_pareja != 0 ? $users_prestamo->idforma_negocio_pareja : '' ) : '' ]);
    @include('app.nuevosistema.select2',['input'=>'#idgiro_negocio_pareja' , 'val' =>  $users_prestamo ? ( $users_prestamo->idgiro_negocio_pareja != 0 ? $users_prestamo->idgiro_negocio_pareja : '' ) : '' ]);
    sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idempresa_negocio_pareja',val: {{ $users_prestamo ? $users_prestamo->idempresa_negocio_pareja : 0 }} });
    
    @include('app.nuevosistema.select2',['json'=>'ubigeo','input'=>'#idubigeo_negocio_pareja' , 'val' =>  $users_prestamo ? ( $users_prestamo->idubigeo_negocio_pareja != 0 ? $users_prestamo->idubigeo_negocio_pareja : '' ) : '' ]);
    @include('app.nuevosistema.select2',['input'=>'#idlocalnegocio_negocio_pareja' , 'val' =>  $users_prestamo ? ( $users_prestamo->idlocalnegocio_negocio_pareja != 0 ? $users_prestamo->idlocalnegocio_negocio_pareja : '' ) : '' ]);
    

    // INICIO FINANCIERA
    // INICIO FINANCIERA
    $("#idestadocivil").on("change", function(e) {
        if(e.currentTarget.value == 2 || e.currentTarget.value == 4){
            $('.continer-data-pareja').removeClass('d-none');
            $('.continer-data-pareja-ocupacion').removeClass('d-none');
            // console.log("mostrar data pareja");
        }else{
            $('.continer-data-pareja').addClass('d-none');
            $('.continer-data-pareja-ocupacion').addClass('d-none');
            // console.log("no mostrar data pareja");
        }

    }).val("{{ $usuario->idestadocivil!=0?$usuario->idestadocivil:'' }}").trigger('change');

    $('#idtipopersona').change(function() {
        var idtipoinformacion = $('#idtipoinformacion').val();
        var idfuenteingreso = $('#idfuenteingreso').val();
        var idtipopersona = $('#idtipopersona').val();
        var idestadocivil = $('#idestadocivil').val();
        $('.continer-data-centrolaboral').removeClass('d-none');
        //$('#cont-centrolaboral-pareja').css('display','none');
        
        if( idtipopersona == 2 ){
            $('.continer-data-pareja').addClass('d-none');
            $('.continer-data-pareja-ocupacion').addClass('d-none');
            $('.container-data-per-natural').addClass('d-none');
            $('.continer-data-centrolaboral').addClass('d-none');
            if(idtipoinformacion==2 && idfuenteingreso==1){
                //$('#cont-centrolaboral-pareja').css('display','block');
                $('.continer-data-pareja-ocupacion').removeClass('d-none');
                //$('#cont-negocio-pareja').css('display','none');
            }
            $('#idtipodocumento').html('<option value=""></option><option value="2">RUC</option>');
        }else{
            if( idestadocivil == 2 || idestadocivil == 4 ){
                $('.continer-data-pareja').removeClass('d-none');
                $('.continer-data-pareja-ocupacion').removeClass('d-none');
                
            }else{
                $('.continer-data-pareja').addClass('d-none');
                $('.continer-data-pareja-ocupacion').addClass('d-none');
                
            }
            
            $('.container-data-per-natural').removeClass('d-none');
            $('#idtipodocumento').html('<option value=""></option><option value="1">DNI</option><option value="3">CE</option>');
            
            if(idtipoinformacion==1 && idfuenteingreso==1){
                $('.continer-data-centrolaboral').addClass('d-none');  
                $('.continer-data-pareja-ocupacion').addClass('d-none');    
            }
        }
        $('#idtipodocumento').val('{{ $users_prestamo ? ( $users_prestamo->idtipodocumento != 0 ? $users_prestamo->idtipodocumento : '' ) : '' }}').trigger('change');
        
    }).val({{$usuario->idtipopersona}}).trigger("change");
    // INICIO TIPO INFORMACION && FUENTE DE INGRESO
    detecta_cambio_tipoinfo_fuenteingreso();
    $('#idtipoinformacion, #idfuenteingreso').change(function() {
        detecta_cambio_tipoinfo_fuenteingreso();
    });
    function detecta_cambio_tipoinfo_fuenteingreso(){
        let tipoinformacion = $('#idtipoinformacion').val();
        let fuenteingreso = $('#idfuenteingreso').val();
        let estadocivil = $("#idestadocivil").val();
        $('#idtipopersona').val(0).trigger('change');
        $('.container-tipopersona').removeClass('d-none');
        $('.continer-data-pareja').addClass('d-none');
        // remover ruc
        let select_idtipodocumento = $('#idtipodocumento');
        
        let rucOption = select_idtipodocumento.find('option[value="2"]');
        let rucOptionIndex = rucOption.index();
        // añadir ruc solo si no existe
        let existe_ruc = select_idtipodocumento.find('option[value="2"]').length > 0;
        if (!existe_ruc) {
            let options = select_idtipodocumento.find('option');
            let html_option_ruc = `<option value="2">RUC</option>`;
            if (options.length >= rucOptionIndex) {
                options.eq(rucOptionIndex - 1).after(html_option_ruc);
            } else {
                select_idtipodocumento.append(html_option_ruc);
            }
        }
        
        
        
        if( tipoinformacion == 1 && fuenteingreso == 1 ){
            $('.continer-data-actividadeconomica').removeClass('d-none');
            $('.continer-data-centrolaboral').addClass('d-none');
        
            $('.continer-data-pareja-ocupacion').addClass('d-none');
        }
        else if( tipoinformacion == 1 && fuenteingreso == 2){
            $('#idtipopersona').val(1).trigger('change');
            $('.container-tipopersona').addClass('d-none');
            $('.continer-data-actividadeconomica').addClass('d-none');
            $('.continer-data-centrolaboral').removeClass('d-none');
            $('.continer-data-pareja-ocupacion').addClass('d-none');
        }
        else if( tipoinformacion == 2 && fuenteingreso == 2){
            $('#idtipopersona').val(1).trigger('change');
            $('.container-tipopersona').addClass('d-none');
            rucOption.detach();
        }
        else if( tipoinformacion == 2 ){
            
            $('.continer-data-pareja-ocupacion').removeClass('d-none');
        }
    }

    
    $('#idtipopersona, #idtipodocumento').change(function() {
        let tipopersona = $('#idtipopersona').val();
        let tipodocumento = $('#idtipodocumento').val();

        $('.container-empresa-ac-economica').removeClass('d-none');
        //$('.continer-data-pareja').addClass('d-none');
        if( tipopersona == 2 && tipodocumento == 2 ){
            // $('.continer-data-actividadeconomica').removeClass('d-none');
            $('.container-empresa-ac-economica').addClass('d-none');

    
            
        }
       
        
    });
    // ##########################
    // INICIO CHECK CASA NEGOCIO
    detecta_cambio_casanegocio()
    $('#casanegocio').change(function() {
        detecta_cambio_casanegocio();
    });
    function detecta_cambio_casanegocio(){
        if ($('#casanegocio').is(':checked')) {
            
            $('.container-casanegocio').addClass('d-none')
        } else {
            $('.container-casanegocio').removeClass('d-none')
        }
    }
    // FIN CHECK CASA NEGOCIO
  
    $("#idocupacion_pareja").on("change", function(e) {

        var idtipoinformacion = $('#idtipoinformacion').val();
        $('#cont-centrolaboral-pareja').css('display','none');
        $('#cont-negocio-pareja').css('display','none');
        if(e.currentTarget.value == 1){
            $('#cont-negocio-pareja').css('display','block');
            /*if(idtipoinformacion == 1){
                $('#cont-negocio-pareja').css('display','none');
            }*/
        }else if(e.currentTarget.value == 2){
            $('#cont-centrolaboral-pareja').css('display','block');
            /*if(idtipoinformacion == 1){
                $('#cont-centrolaboral-pareja').css('display','none');
            }*/
        }
    }).val("{{ $users_prestamo ? ( $users_prestamo->idocupacion_pareja != 0 ? $users_prestamo->idocupacion_pareja : '' ) : '' }}").trigger('change');


    $('.container-empresa-ac-economica').addClass('d-none');
    $("#idforma_ac_economica").on("change", function(e) {
        
        if( e.currentTarget.value == 1 ){
            $('.container-empresa-ac-economica').removeClass('d-none');
        }else if(e.currentTarget.value == 2){
            $('.container-empresa-ac-economica').addClass('d-none');
        }

    }).val("{{ $users_prestamo ? ( $users_prestamo->idforma_ac_economica != 0 ? $users_prestamo->idforma_ac_economica : '' ) : '' }}").trigger('change');

    $('.container-empresa-negocio-pareja').addClass('d-none');
    $("#idforma_negocio_pareja").on("change", function(e) {
        
        if( e.currentTarget.value == 1 ){
            $('.container-empresa-negocio-pareja').removeClass('d-none');
        }else if(e.currentTarget.value == 2){
            $('.container-empresa-negocio-pareja').addClass('d-none');
        }

    }).val("{{ $users_prestamo ? ( $users_prestamo->idforma_negocio_pareja != 0 ? $users_prestamo->idforma_negocio_pareja : '' ) : '' }}").trigger('change');
    


    // agregar_cliente_financiera('celular-cliente');
    // agregar_cliente_financiera('celular-pareja');
    function agregar_cliente_financiera(tabla,valor=''){
        
        var num   = $("#tabla-"+tabla+" > tbody").attr('num');
        var cant  = $("#tabla-"+tabla+" > tbody > tr").length;
      
        var tdtable = `<td></td>`;
        if(cant>0){
            tdtable = `<td><a href="javascript:;" onclick="eliminar_cliente_financiera(${num},'${tabla}')" class="btn btn-danger "><i class="fa-solid fa-trash"></i></td>`;
        }
      
      
        var html='<tr id="'+num+'">'+
                      '<td><input class="form-control" type="text" id="'+tabla+'texto'+num+'" value="'+valor+'"></td>'+
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


        var tabla='<tr id="'+num+'">'+
                    '<td><input type="number" class="form-control" id="celular'+num+'" value="'+celular+'"></td>'+
                    '<td><input type="text" class="form-control" id="vinculo'+num+'" value="'+vinculo+'"></td>'+
                    '<td><input type="text" class="form-control" id="referencia'+num+'" value="'+referencia+'"></td>'+
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
    // FIN FINANCIERA
    $("#idubigeo").on("select2:select", function(e) {
        seleccionar_ubicacion(e.params.data.nombre);    
    });
  
    if({{$usuario->idtipopersona}}==1){
        $('#cont-ubigeo').html('Ubicación (Ubigeo)');
        $('#cont-direccion').html('Dirección');
    }
    else if({{$usuario->idtipopersona}}==2){
        $('#cont-ubigeo').html('Ubicación *');
        $('#cont-direccion').html('Dirección *');
    }
    else if({{$usuario->idtipopersona}}==3){
        $('#cont-ubigeo').html('Ubicación (Ubigeo)');
        $('#cont-direccion').html('Dirección');
    }
  
    if('{{$usuario->identificacion}}'==0){
        if({{$usuario->idtipopersona}}==1){
            $('#resultado-dni').html('');
            $('#nombre').removeAttr('disabled');
            $('#apellidopaterno').removeAttr('disabled');
            $('#apellidomaterno').removeAttr('disabled');
            $('#idubigeo').removeAttr('disabled');
            $('#direccion').removeAttr('disabled');
            $('#numerotelefono').removeAttr('disabled');
            $('#email').removeAttr('disabled');
          
            $('#cont-natural-apellidopaterno').html('Apellido Paterno');
            $('#cont-natural-apellidomaterno').html('Apellido Materno');
        }
        if({{$usuario->idtipopersona}}==2){
            $('#resultado-ruc').html('');
            $('#nombrecomercial').removeAttr('disabled');
            $('#razonsocial').removeAttr('disabled');
            $('#idubigeo').removeAttr('disabled');
            $('#direccion').removeAttr('disabled');
            $('#numerotelefono').removeAttr('disabled');
            $('#email').removeAttr('disabled');
          
            $('#cont-juridica-razonsocial').html('Razón Social');
            $('#cont-ubigeo').html('Ubicación (Ubigeo)');
            $('#cont-direccion').html('Dirección');
        }
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
            $('#nombre').removeAttr('disabled');
            $('#apellidopaterno').removeAttr('disabled');
            $('#apellidomaterno').removeAttr('disabled');
            $('#idubigeo').removeAttr('disabled');
            $('#direccion').removeAttr('disabled');
            $('#numerotelefono').removeAttr('disabled');
            $('#email').removeAttr('disabled');
          
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
                    $('#nombrecomercial').removeAttr('disabled');
                    $('#razonsocial').removeAttr('disabled');
                    $('#idubigeo').removeAttr('disabled');
                    $('#direccion').removeAttr('disabled');
                    $('#numerotelefono').removeAttr('disabled');
                    $('#email').removeAttr('disabled');
                    if(respuesta.resultado=='ERROR'){
                        $('#resultado-ruc-resultado').html(respuesta.mensaje);
                        $('#nombrecomercial').attr('disabled','true');
                        $('#razonsocial').attr('disabled','true');
                        $('#idubigeo').val(null).trigger("change");
                        $('#direccion').val('');
                    }else{
                        $('#nombrecomercial').val(respuesta.nombreComercial);
                        $('#razonsocial').val(respuesta.razonSocial);
                        $('#idubigeo').html('<option value="'+respuesta.idubigeo+'">'+respuesta.ubigeo+'</option>');
                        $('#direccion').val(respuesta.direccion);
                    }  
                }
            })
        }else if(identificacion!='' && identificacion==0){
            $('#resultado-ruc').html('');
            $('#nombrecomercial').removeAttr('disabled');
            $('#razonsocial').removeAttr('disabled');
            $('#idubigeo').removeAttr('disabled');
            $('#direccion').removeAttr('disabled');
            $('#numerotelefono').removeAttr('disabled');
            $('#email').removeAttr('disabled');
          
            $('#cont-juridica-razonsocial').html('Razón Social');
            $('#cont-ubigeo').html('Ubicación (Ubigeo)');
            $('#cont-direccion').html('Dirección');
        }
    }
    function limpiarcampos(){
        $('#nombre').attr('disabled','true');
        $('#apellidopaterno').attr('disabled','true');
        $('#apellidomaterno').attr('disabled','true');
        $('#nombrecomercial').attr('disabled','true');
        $('#razonsocial').attr('disabled','true');

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

    $("#idtipopersona").on("change", function(e) {
        $('#cont-juridica, #cont-juridica1').addClass('d-none');
        $('#cont-natural, #cont-natural1').addClass('d-none');
        $('#cont-carnetextranjeria, #cont-carnetextranjeria1').addClass('d-none');
        $('.container-representante-legal').addClass('d-none');
        if(e.currentTarget.value == 1) {
            $('#cont-natural, #cont-natural1').removeClass('d-none');
            $('.container-representante-legal').addClass('d-none');
        }else if(e.currentTarget.value == 2) {
            $('#cont-juridica, #cont-juridica1').removeClass('d-none');
            $('.container-representante-legal').removeClass('d-none');
        }else if(e.currentTarget.value == 3) {
            $('#cont-carnetextranjeria, #cont-carnetextranjeria1').removeClass('d-none');
            $('#nombre_carnetextranjeria').removeAttr('disabled');
            $('#apellidopaterno_carnetextranjeria').removeAttr('disabled');
            $('#apellidomaterno_carnetextranjeria').removeAttr('disabled');
            $('#idubigeo').removeAttr('disabled');
            $('#direccion').removeAttr('disabled');
            $('#numerotelefono').removeAttr('disabled');
            $('#email').removeAttr('disabled');
            $('.container-representante-legal').addClass('d-none');
        }
    }).val({{$usuario->idtipopersona}}).trigger("change");

    uploadfile({
        input: "#imagen",
        cont: "#cont-fileupload",
        result: "#resultado-logo",
        ruta: "{{ url('/public/backoffice/tienda/'.$tienda->id.'/sistema/') }}",
        image: "{{ $usuario->imagen }}"
    });
  
    @if($usuario->mapa_latitud!='' && $usuario->mapa_longitud!='')
        singleMap({
            'map' : '#domicilio_mapa',
            'lat' : '{{$usuario->mapa_latitud}}',
            'lng' : '{{$usuario->mapa_longitud}}',
            'result_lat' : '#domicilio_mapa_latitud',
            'result_lng' : '#domicilio_mapa_longitud'
        });
    @else
        seleccionar_ubicacion('{{$usuario->ubigeonombre}}');
    @endif
    function seleccionar_ubicacion(address) {
        singleMap_address({
            'map' : '#domicilio_mapa',
            'address' : address,
            'result_lat' : '#domicilio_mapa_latitud',
            'result_lng' : '#domicilio_mapa_longitud'
        });
    }
  
    @if($usuario->idlogisticaruta!='')
    $("#idlogisticaruta").val({{$usuario->idlogisticaruta}}).trigger("change");
    @endif
</script>