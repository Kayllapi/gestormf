<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/usuario') }}',
          method: 'POST',
          data:{
              view: 'registrar',
              telefono_cliente: seleccionar_cliente_financiera(`celular-cliente`),
              telefono_pareja: seleccionar_cliente_financiera(`celular-pareja`),
              referencia_cliente: seleccionar_referencia(),
          }
      },
      function(resultado){
          $('#tabla-usuario').DataTable().ajax.reload();
          $('#modal-close-usuario-registrar').click(); 
      },this)"> 
    <div class="modal-header">
        <h5 class="modal-title">REGISTRAR CLIENTE</h5>
        <button type="button" class="btn-close" id="modal-close-usuario-registrar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Tipo de Información <span class="text-danger">(*)</span></label>
                    <select class="form-select" id="idtipoinformacion">
                        <option value=""></option>
                        @foreach($tipoinformacion as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Fuente de Ingreso  <span class="text-danger">(*)</span></label>
                    <select class="form-select" id="idfuenteingreso">
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
                <select class="form-control" id="idtipopersona">
                    <option value=""></option>
                    <option value="1">NATURAL</option>
                    <option value="2">JURÍDICA</option>
                </select>
                <script>
                    $('#idtipopersona').change(function() {
                        var idtipoinformacion = $('#idtipoinformacion').val();
                        var idfuenteingreso = $('#idfuenteingreso').val();
                        var idtipopersona = $('#idtipopersona').val();
                        $('.continer-data-centrolaboral').removeClass('d-none');
                        $('#cont-centrolaboral-pareja').css('display','none');      
                        if( idtipopersona == 2 ){
                            $('.continer-data-pareja').addClass('d-none');
                            $('.continer-data-pareja-ocupacion').addClass('d-none');
                            $('.container-data-per-natural').addClass('d-none');
                            $('.continer-data-centrolaboral').addClass('d-none');
                            if(idtipoinformacion==2 && idfuenteingreso==1){
                                $('#cont-centrolaboral-pareja').css('display','block');
                                $('.continer-data-pareja-ocupacion').removeClass('d-none');
                                $('#cont-negocio-pareja').css('display','none');
                            }
                            $('#idtipodocumento').html('<option value=""></option><option value="2">RUC</option>');
                        }else{
                            $('.continer-data-pareja').removeClass('d-none');
                            $('.continer-data-pareja-ocupacion').removeClass('d-none');
                            $('.container-data-per-natural').removeClass('d-none');
                            $('#idtipodocumento').html('<option value=""></option><option value="1">DNI</option><option value="3">CE</option>');
                            if(idtipoinformacion==1 && idfuenteingreso==1){
                                $('.continer-data-centrolaboral').addClass('d-none');  
                                $('.continer-data-pareja-ocupacion').addClass('d-none');    
                            }
                            else if(idtipoinformacion==1 && idfuenteingreso==2){
                                $('#cont-centrolaboral-pareja').css('display','none');
                                $('#cont-negocio-pareja').css('display','none');      
                            }
                        }
                        
                    });
                </script>
            </div>
            <div class="col-md-4">
                <label>Tipo de Documento <span class="text-danger">(*)</span></label>
                <select class="form-control" id="idtipodocumento">
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
                    <input type="number" class="form-control" id="ruc" onkeyup="buscar_ruc()"/>
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
                    <input type="number" class="form-control" id="dni" onkeyup="buscar_dni()"/>
                    <div id="resultado-dni" style="float: right;margin-top: -35px;margin-right: 3px;text-align: right;"></div>
                    <div id="resultado-dni-resultado" style="color:#e22d02;"></div>
                </div>
                <div id="cont-carnetextranjeria" class="d-none">
                    <label>Carnet Extranjería *</label>
                    <input type="number" class="form-control" id="carnetextranjeria">
                </div>
            </div>
        </div>
        <div class="row d-none" id="cont-juridica1">
            <div class="col-md-6">
                <div class="mb-1">
                    <label>Nombre Comercial</label>
                    <input type="text" class="form-control" id="nombrecomercial"/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-1">
                    <label id="cont-juridica-razonsocial">Razón Social  <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="razonsocial" disabled/>
                </div>
            </div>
        </div>
        <div class="row d-none" id="cont-natural1">
            <div class="col-md-4">
                <div class="mb-1">
                    <label>Nombre  <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="nombre"/>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-1">
                    <label id="cont-natural-apellidopaterno">Apellido Paterno  <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="apellidopaterno">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-1">
                    <label id="cont-natural-apellidomaterno">Apellido Materno  <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="apellidomaterno">
                </div>
            </div>  
        </div>
        <div class="row d-none" id="cont-carnetextranjeria1">
            <div class="col-md-4">
                <div class="mb-1">
                    <label>Nombre  <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="nombre_carnetextranjeria">
                </div>
            </div> 
            <div class="col-md-4">
                <div class="mb-1">
                    <label>Apellido Paterno <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="apellidopaterno_carnetextranjeria">
                </div>
            </div> 
            <div class="col-md-4">
                <div class="mb-1">
                    <label>Apellido Materno <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="apellidomaterno_carnetextranjeria">
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
                    <input type="number" class="form-control" id="documento_representantelegal">
                </div>
            </div>
            <div class="col-sm-12 col-md-8 d-none container-representante-legal">
                <div class="mb-1">
                    <label>Nombre Completo Representante Legal <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="nombrecompelto_representantelegal">
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
                    <input type="date" class="form-control" id="fechanacimientocreacion">
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
                    <label>Profesión</label>
                    <input type="text" class="form-control" id="profesion">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="mb-1">
                    <label>Correo Electrónico </label>
                    <input type="text" class="form-control" id="correo_electronico">
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
                    <input type="number" class="form-control" id="dni_pareja">
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Nombres <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="nombres_pareja">
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Apellido Paterno <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="ap_paterno_pareja">
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Apellido Materno <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="ap_materno_pareja">
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
                    <input type="text" class="form-control" id="profesion_pareja">
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
                <input type="text" class="form-control" id="direccion"/>
                </div>
            </div>
            <div class="col-sm-12 col-md-5">
                <div class="mb-1">
                <label id="cont-ubigeo">Distrito – Provincia – Departamento <span class="text-danger">(*)</span></label>
                <select class="form-control" id="idubigeo">
                    <option></option>
                </select>
                </div>
            </div>
            <div class="col-sm-12 col-md-12">
                <div class="mb-1">
                    <label>Referencia Ubicación <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="referencia_direccion">
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="mb-1">
                    <label>Suministro Elect(Caso no exista N° Domicilio)</label>
                    <input type="text" class="form-control" id="suministro_electrocentro">
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
                    <input type="text" class="form-control" id="descripcion_ac_economica">
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
                    <input type="number" class="form-control" id="ruc_ac_economica">
                </div>
            </div>
            <div class="col-sm-12 col-md-8 container-empresa-ac-economica d-none">
                <div class="mb-1">
                    <label>Nombre: Persona Natural/Persona Jurídica</label>
                    <input type="text" class="form-control" id="razonsocial_ac_economica">
                </div>
            </div>
            
            <div class="col-sm-12 col-md-3">
                <div class="mb-1">
                    <label>&nbsp; </label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"  id="casanegocio">
                        <label class="form-check-label" for="casanegocio" style="margin-top: 0px">
                        Casa/Negocio:
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-5 container-casanegocio">
                <div class="mb-1">
                    <label>Direccion  <span class="text-danger">(*)</span></label>
                    <input type="text" class="form-control" id="direccion_ac_economica">
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
                    <input type="text" class="form-control" id="referencia_ac_economica">
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
                    <input type="number" class="form-control" id="ruc_laboral_cliente">
                </div>
            </div>
            <div class="col-sm-12 col-md-8">
                <div class="mb-1">
                    <label>Nombre: Persona Natural/Persona Jurídica</label>
                    <input type="text" class="form-control" id="razonsocial_laboral_cliente" >
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Fecha Inicio</label>
                    <input type="date" class="form-control" id="fechainicio_laboral_cliente">
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Antiguedad (en años) </label>
                    <input type="text"  class="form-control" id="antiguedad_laboral_cliente" >
                </div>
            </div>

            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Cargo </label>
                    <input type="text" class="form-control" id="cargo_laboral_cliente" >
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="mb-1">
                    <label>Área </label>
                    <input type="text" class="form-control" id="area_laboral_cliente" >
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
        <div id="cont-centrolaboral-pareja">
            <div class="mb-1 mt-1 continer-data-pareja-ocupacion d-none">
                <span class="badge d-block">CENTRO LABORAL DE: PAREJA/REPRESENTANTE LEG.</span>
            </div>
            <div class="row continer-data-pareja-ocupacion d-none">
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
                        <input type="number" class="form-control" id="ruc_laboral_pareja">
                    </div>
                </div>
                <div class="col-sm-12 col-md-8">
                    <div class="mb-1">
                        <label>Nombre: Persona Natural/Persona Jurídica <span class="text-danger">(*)</span></label>
                        <input type="text" class="form-control" id="razonsocial_laboral_pareja">
                    </div>
                </div>

                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Fecha Inicio</label>
                        <input type="date" class="form-control" id="fechainicio_laboral_pareja">
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Antiguedad (en años) <span class="text-danger">(*)</span></label>
                        <input type="text"  class="form-control" id="antiguedad_laboral_pareja">
                    </div>
                </div>

                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Cargo <span class="text-danger">(*)</span></label>
                        <input type="text" class="form-control" id="cargo_laboral_pareja">
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="mb-1">
                        <label>Área </label>
                        <input type="text" class="form-control" id="area_laboral_pareja">
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
        <div id="cont-negocio-pareja">
            <div class="mb-1 mt-1 continer-data-pareja-ocupacion d-none">
                <span class="badge d-block">NEGOCIO DE: PAREJA/REPRESENTANTE LEG.</span>
            </div>
            <div class="row continer-data-pareja-ocupacion d-none">
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
                        <input type="text" class="form-control" id="descripcion_negocio_pareja">
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
                        <input type="number" class="form-control" id="ruc_negocio_pareja">
                    </div>
                </div>
                <div class="col-sm-12 col-md-8 container-empresa-negocio-pareja">
                    <div class="mb-1">
                        <label>Nombre: Persona Natural/Persona Jurídica</label>
                        <input type="text" class="form-control" id="razonsocial_negocio_pareja">
                    </div>
                </div>
                
                <div class="col-sm-12 col-md-7">
                    <div class="mb-1">
                        <label>Direccion <span class="text-danger">(*)</span></label>
                        <input type="text" class="form-control" id="direccion_negocio_pareja">
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
                        <input type="text" class="form-control" id="referencia_negocio_pareja">
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
  
    @include('app.nuevosistema.select2',['json'=>'ubigeo','input'=>'#idubigeo'])

    @include('app.nuevosistema.select2',['input'=>'#idtipopersona'])
    @include('app.nuevosistema.select2',['input'=>'#idtipodocumento'])

    @include('app.nuevosistema.select2',['input'=>'#idgenero' ])
    @include('app.nuevosistema.select2',['input'=>'#idestadocivil'])
    @include('app.nuevosistema.select2',['input'=>'#idnivelestudio'])

    @include('app.nuevosistema.select2',['input'=>'#idtipoinformacion' ]);
    @include('app.nuevosistema.select2',['input'=>'#idfuenteingreso']);

    sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idpareja' });

    @include('app.nuevosistema.select2',['input'=>'#idocupacion_pareja'])
    @include('app.nuevosistema.select2',['input'=>'#idnivelestudio_pareja' ])
    @include('app.nuevosistema.select2',['input'=>'#idcondicionviviendalocal' ])

    @include('app.nuevosistema.select2',['input'=>'#idforma_ac_economica' ]);
    @include('app.nuevosistema.select2',['input'=>'#idgiro_ac_economica' ]);
    sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idempresa_ac_economica' });

    @include('app.nuevosistema.select2',['json'=>'ubigeo','input'=>'#idubigeo_ac_economica'  ]);
    @include('app.nuevosistema.select2',['input'=>'#idlocalnegocio_ac_economica'  ]);

    sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idempresa_laboral_cliente' });
    @include('app.nuevosistema.select2',['input'=>'#idtipocontrato_laboral_cliente' ]);

    sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idempresa_laboral_pareja' });
    @include('app.nuevosistema.select2',['input'=>'#idtipocontrato_laboral_pareja' ]);

    @include('app.nuevosistema.select2',['input'=>'#idforma_negocio_pareja' ]);
    @include('app.nuevosistema.select2',['input'=>'#idgiro_negocio_pareja' ]);
    sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idempresa_negocio_pareja' });

    @include('app.nuevosistema.select2',['json'=>'ubigeo','input'=>'#idubigeo_negocio_pareja' ]);
    @include('app.nuevosistema.select2',['input'=>'#idlocalnegocio_negocio_pareja'  ]);

    // INICIO FINANCIERA
    $("#idestadocivil").on("change", function(e) {
        
        if(e.currentTarget.value == 2 || e.currentTarget.value == 4){
            $('.continer-data-pareja').removeClass('d-none');
            $('.continer-data-pareja-ocupacion').removeClass('d-none');
        }else{
            $('.continer-data-pareja').addClass('d-none');
            $('.continer-data-pareja-ocupacion').addClass('d-none');
        }

    });
    // INICIO TIPO INFORMACION && FUENTE DE INGRESO
    $('#idtipoinformacion, #idfuenteingreso').change(function() {
        let tipoinformacion = $('#idtipoinformacion').val();
        let fuenteingreso = $('#idfuenteingreso').val();
        
        $('#idtipopersona').val(null).trigger('change');
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
        
        console.log('El valor seleccionado en el tipoinformacion es: ' + tipoinformacion);
        console.log('El valor seleccionado en el fuenteingreso es: ' + fuenteingreso);
        
    });
    $('#idtipopersona, #idtipodocumento').change(function() {
        let tipopersona = $('#idtipopersona').val();
        let tipodocumento = $('#idtipodocumento').val();

        $('.container-empresa-ac-economica').removeClass('d-none');
        $('.continer-data-pareja').addClass('d-none');
        if( tipopersona == 2 && tipodocumento == 2 ){
            // $('.continer-data-actividadeconomica').removeClass('d-none');
            $('.container-empresa-ac-economica').addClass('d-none');

            
            // ruc_ac_economica
            // razonsocial_ac_economica
            
        }
       
        console.log('El valor seleccionado en el tipopersona es: ' + tipopersona);
        console.log('El valor seleccionado en el tipodocumento es: ' + tipodocumento);
        
    });
    

    // FIN TIPO INFORMACION && FUENTE DE INGRESO

    // INICIO CHECK CASA NEGOCIO
    $('#casanegocio').change(function() {
        if ($(this).is(':checked')) {
            
            $('.container-casanegocio').addClass('d-none')
        } else {
            $('.container-casanegocio').removeClass('d-none')
        }
    });
    // FIN CHECK CASA NEGOCIO
  
    $("#idocupacion_pareja").on("change", function(e) {
        var idtipoinformacion = $('#idtipoinformacion').val();
        $('#cont-centrolaboral-pareja').css('display','none');
        $('#cont-negocio-pareja').css('display','none');
        if(e.currentTarget.value == 1){
            $('#cont-negocio-pareja').css('display','block');
            if(idtipoinformacion == 1){
                $('#cont-negocio-pareja').css('display','none');
            }
        }else if(e.currentTarget.value == 2){
            $('#cont-centrolaboral-pareja').css('display','block');
            if(idtipoinformacion == 1){
                $('#cont-centrolaboral-pareja').css('display','none');
            }
        }
    });

    $('.continer-data-centrolaboral').addClass('d-none');
    $('.continer-data-actividadeconomica').addClass('d-none');
    $("#idfuenteingreso").on("change", function(e) {
        
        if( e.currentTarget.value == 1 ){
            $('.continer-data-actividadeconomica').removeClass('d-none');
            $('.continer-data-centrolaboral').addClass('d-none');
        }else if(e.currentTarget.value == 2){
            $('.continer-data-actividadeconomica').addClass('d-none');
            $('.continer-data-centrolaboral').removeClass('d-none');
        }

    });

    $('.container-empresa-ac-economica').addClass('d-none');
    $("#idforma_ac_economica").on("change", function(e) {
        
        if( e.currentTarget.value == 1 ){
            $('.container-empresa-ac-economica').removeClass('d-none');
        }else if(e.currentTarget.value == 2){
            $('.container-empresa-ac-economica').addClass('d-none');
        }

    });

    $('.container-empresa-negocio-pareja').addClass('d-none');
    $("#idforma_negocio_pareja").on("change", function(e) {
        
        if( e.currentTarget.value == 1 ){
            $('.container-empresa-negocio-pareja').removeClass('d-none');
        }else if(e.currentTarget.value == 2){
            $('.container-empresa-negocio-pareja').addClass('d-none');
        }

    });



    agregar_cliente_financiera('celular-cliente');
    agregar_cliente_financiera('celular-pareja');
    agregar_referencia()
  
    function agregar_cliente_financiera(tabla,valor=''){
        
        var num   = $("#tabla-"+tabla+" > tbody").attr('num');
        var cant  = $("#tabla-"+tabla+" > tbody > tr").length;
    
        var tdtable = `<td></td>`;
        if(cant>0){
            tdtable = `<td><a href="javascript:;" onclick="eliminar_cliente_financiera(${num},'${tabla}')" class="btn btn-danger "><i class="fa-solid fa-trash"></i></td>`;
        }
    
    
        var html='<tr id="'+num+'">'+
                    '<td><input type="number" class="form-control" id="'+tabla+'texto'+num+'" value="'+valor+'"></td>'+
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


        var tabla= '<tr id="'+num+'">'+
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

    $("#idtipodocumento").on("change", function(e) {
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
    });

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
                        // $('#idubigeo').val(null).trigger("change");
                        $('#direccion').val('');
                    }else{
                        $('#nombrecomercial').val(respuesta.nombreComercial);
                        $('#razonsocial').val(respuesta.razonSocial);
                        // $('#idubigeo').html('<option value="'+respuesta.idubigeo+'">'+respuesta.ubigeo+'</option>');
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
            $('#cont-ubigeo').html('Distrito – Provincia – Departamento');
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
</script>
