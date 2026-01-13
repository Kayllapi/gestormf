<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha Cliente</title>
    <style>
        *{
            margin:0;
            padding:0;
        }
        body {
            margin-top: 1.2cm;
            margin-left: 0.7cm;
            margin-right: 0.7cm;
            margin-bottom: 2cm;
            font-family:helvetica;
            font-size:12px;
        }
        .container{
            padding:5px 0px;
            padding-left: 5px;
        }
        table{
            width:100%;
            border-collapse: collapse;
        }
        table > tbody > tr > td{
            padding:2px;
            vertical-align: top;
        }

        .container-informacion {
            border-collapse: collapse;
            width: 100%; 
        }
        .ficha-titulo{
            padding-bottom:2px;
        }
        .container-informacion > p {
            font-size:10px;
            border: 0.5px solid black; 
            padding: 5px; 
            margin: 0; 
        }

    </style>
</head>
<body>
    
    @include('app/nuevosistema/cabecerapdf_a4')
    <div class="container">
        <h3 align="center">FICHA DE INFORMACIÓN</h3>
        @if($usuario->identificacion!='' or
            $usuario->nombrecompleto!='' or 
            $usuario->db_idgenero!='' or 
            $usuario->db_idnivelestudio!='' or 
            $users_prestamo->correo_electronico!='' or
            $users_prestamo->db_idtipodocumento!='' or 
            $users_prestamo->razonsocial_ac_economica!='' or 
            $usuario->fechanacimiento!='' or 
            $users_prestamo->profesion!='' or
            $users_prestamo->documento_representantelegal!='' or 
            $users_prestamo->nombrecompelto_representantelegal!='' or 
            $usuario->db_idestadocivil!='' or 
            $users_prestamo->telefono_cliente!='')
        <h5 class="ficha-titulo">DATOS DE CLIENTE</h5>
        <table>
            <tbody>
                <tr>
                    @if($usuario->nombrecompleto!='' or 
                        $usuario->db_idgenero!='' or 
                        $usuario->fechanacimiento!='' or 
                        $usuario->db_idnivelestudio!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            @if($usuario->nombrecompleto!='')
                            <p><b>Cliente:</b> {{ $usuario->nombrecompleto }}</p>
                            @endif
                            @if($usuario->identificacion!='')
                            <p><b>DNI/RUC:</b> {{ $usuario->identificacion }}</p>
                            @endif
                            <!--p><b>Nacionalidad:</b></p-->
                            @if($usuario->db_idgenero!='')
                            <p><b>Género:</b> {{ $usuario->db_idgenero }}</p>
                            @endif
                            @if($usuario->db_idnivelestudio!='')
                            <p><b>Nivel de estudios:</b> {{ $usuario->db_idnivelestudio }}</p>
                            @endif
                            @if($usuario->fechanacimiento!='')
                            <p><b>Fecha Nacimiento/Creación:</b> {{ date_format(date_create($usuario->fechanacimiento),'d-m-Y') }}</p>
                            @endif
                        </div>
                    </td>
                    @endif
                    @if($users_prestamo->db_idtipodocumento!='' or 
                        $users_prestamo->razonsocial_ac_economica!='' or 
                        $users_prestamo->profesion!='' or
                        $users_prestamo->documento_representantelegal!='' or 
                        $users_prestamo->nombrecompelto_representantelegal!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            @if($users_prestamo->db_idtipodocumento!='')
                            <!--p><b>Tipo de Documento:</b> {{ $users_prestamo->db_idtipodocumento }}</p-->
                            @endif
                            @if($users_prestamo->razonsocial_ac_economica!='')
                            <p><b>Empresa:</b> {{ $users_prestamo->razonsocial_ac_economica }}</p>
                            @endif
                            @if($users_prestamo->db_idfuenteingreso!='')
                            <p><b>Fuente de Ingreso:</b> {{ $users_prestamo->db_idfuenteingreso }}</p>
                            @endif
                            @if($users_prestamo->profesion!='')
                            <p><b>Profesión:</b> {{ $users_prestamo->profesion }}</p>
                            @endif
                            @if($users_prestamo->nombrecompelto_representantelegal!='')
                            <p><b>Representate Legal:</b> {{ $users_prestamo->nombrecompelto_representantelegal }}</p>
                            @endif
                            @if($users_prestamo->documento_representantelegal!='')
                            <p><b>Nro Documento:</b> {{ $users_prestamo->documento_representantelegal }}</p>
                            @endif
                        </div>
                    </td>
                    @endif
                    @if($usuario->db_idestadocivil!='' or 
                        $users_prestamo->telefono_cliente!='' or 
                        $users_prestamo->correo_electronico!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            @if($usuario->db_idestadocivil!='')
                            <p><b>Estado Civil:</b> {{ $usuario->db_idestadocivil }}</p>
                            @endif
                            @if($users_prestamo->correo_electronico!='')
                            <p><b>Email:</b> {{ $users_prestamo->correo_electronico }}</p>
                            @endif
                            @if($users_prestamo->telefono_cliente!='')
                              <p><b>Teléfono:</b> 
                              <?php
                                 $telefono_cliente = $users_prestamo ? ( is_null($users_prestamo->telefono_cliente) ? [] : json_decode($users_prestamo->telefono_cliente) ) : [];
                                  $i = 0;
                                ?>
                              @foreach($telefono_cliente as $value)
                                <?php $coma = ''; ?>
                                @if($i > 0)
                                <?php $coma = ' / '; ?>
                                @endif
                                {{ $coma.$value->valor }}
                                <?php $i++ ?>
                              @endforeach
                    
                                
                              </p>
                            @endif
                        </div>
                    </td>
                    @endif
                </tr>
            </tbody>
        </table>
        @endif
        @if($users_prestamo->dni_pareja!='' or
            $users_prestamo->nombrecompleto_pareja!='' or 
            $users_prestamo->db_idnivelestudio_pareja!='' or 
            $users_prestamo->db_idocupacion_pareja!='' or 
            $users_prestamo->profesion_pareja!='')
        <h5 class="ficha-titulo">DATOS DE PAREJA</h5>
        <table>
            <tbody>
                <tr>
                    @if($users_prestamo->dni_pareja!='' or 
                        $users_prestamo->nombrecompleto_pareja!='' or 
                        $users_prestamo->db_idnivelestudio_pareja!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            @if($users_prestamo->dni_pareja!='')
                            <p><b>DNI/CE:</b> {{ $users_prestamo->dni_pareja }}</p>
                            @endif
                            @if($users_prestamo->nombrecompleto_pareja!='')
                            <p><b>Apellidos y Nombres:</b> {{ $users_prestamo->nombrecompleto_pareja }}</p>
                            @endif
                            @if($users_prestamo->db_idnivelestudio_pareja!='')
                            <p><b>Nivel de estudios:</b> {{ $users_prestamo->db_idnivelestudio_pareja }}</p> 
                            @endif
                        </div>
                    </td>
                    @endif
                    @if($users_prestamo->db_idocupacion_pareja!='' or 
                        $users_prestamo->profesion_pareja!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            @if($users_prestamo->db_idocupacion_pareja!='')
                            <p><b>Ocupación:</b> {{ $users_prestamo->db_idocupacion_pareja }}</p>
                            @endif
                            @if($users_prestamo->profesion_pareja!='')
                            <p><b>Profesión:</b> {{ $users_prestamo->profesion_pareja }}</p>
                            @endif
                        </div>
                    </td>
                    @endif
                      
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            <p><b>Teléfono:</b>
                            <?php
                               $telefono_pareja = $users_prestamo ? ( is_null($users_prestamo->telefono_pareja) ? [] : json_decode($users_prestamo->telefono_pareja) ) : [];
                               $i = 0;
                            ?>
                            @foreach($telefono_pareja as $value)
                                <?php $coma = ''; ?>
                                @if($i > 0)
                                <?php $coma = ' / '; ?>
                                @endif
                                {{ $coma.$value->valor }}
                                <?php $i++ ?>
                            @endforeach
                            </p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        @endif
        
        @if($usuario->direccion!='' or
            $usuario->db_idubigeo!='' or 
            $users_prestamo->referencia_direccion!='' or 
            $users_prestamo->suministro_electrocentro!='' or 
            $users_prestamo->db_idcondicionviviendalocal!='')
        <h5 class="ficha-titulo">DOMICILIO CLIENTE</h5>
        <table>
            <tbody>
                <tr>
                    @if($usuario->direccion!='' or 
                        $usuario->db_idubigeo!='' or 
                        $users_prestamo->referencia_direccion!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            @if($usuario->direccion!='')
                            <p><b>Dirección:</b> {{ $usuario->direccion }}</p>
                            @endif
                            @if($usuario->db_idubigeo!='')
                            <p><b>Ubigeo:</b> {{ $usuario->db_idubigeo }}</p>
                            @endif
                            @if($users_prestamo->referencia_direccion!='')
                            <p><b>Referencia Ubicación:</b> {{ $users_prestamo->referencia_direccion }}</p> 
                            @endif
                        </div>
                    </td>
                    @endif
                    @if($users_prestamo->suministro_electrocentro!='' or 
                        $users_prestamo->db_idcondicionviviendalocal!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            @if($users_prestamo->suministro_electrocentro!='')
                            <p><b>Suministro:</b> {{ $users_prestamo->suministro_electrocentro }}</p>
                            @endif
                            @if($users_prestamo->db_idcondicionviviendalocal!='')
                            <p><b>Condición de Vivienda:</b> {{ $users_prestamo->db_idcondicionviviendalocal }}</p>
                            @endif
                        </div>
                    </td>
                    @endif
                    <!--td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            <p><b>Referencia telefónica:</b> </p>
                        </div>
                    </td-->
                </tr>
            </tbody>
        </table>
        @endif
        <?php
          $referencia_cliente = $users_prestamo ? ( is_null($users_prestamo->referencia_cliente) ? [] : json_decode($users_prestamo->referencia_cliente) ) : [];
        ?>
        <h5 class="ficha-titulo">REFERENCIA TELEFONICA</h5>
        <table border=1 style="padding:10px;">
          
          <tbody>
            <tr>
              <td>Telf./Celular</td>
              <td>Nombres y Apellidos</td> 
              <td>Vinculo Familiar/Personas/Otros</td>
            </tr>
            @foreach($referencia_cliente as $value)
              <tr>
                <td style="padding:2px 10px;">{{ $value->referencia }}</td>
                <td style="padding:2px 10px;">{{ $value->vinculo }}</td>
                <td style="padding:2px 10px;">{{ $value->celular }}</td>
              </tr>
            @endforeach
          </tbody>
          
          
        </table>
        
        @if($users_prestamo->db_idforma_ac_economica!='' or
            $users_prestamo->db_idgiro_ac_economica!='' or 
            $users_prestamo->descripcion_ac_economica!='' or 
            $users_prestamo->ruc_ac_economica!='' or 
            $users_prestamo->razonsocial_ac_economica!='' or
            $users_prestamo->direccion_ac_economica!='' or 
            $users_prestamo->referencia_ac_economica!='' or 
            $users_prestamo->ruc_ac_economica!='' or 
            $users_prestamo->db_idlocalnegocio_ac_economica!='')
        <h5 class="ficha-titulo">ACTIVIDAD ECONÓMICA CLIENTE</h5>
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
                        $users_prestamo->direccion_ac_economica!='' or
                        $users_prestamo->db_idubigeo_ac_economica!='' or
                        $users_prestamo->casanegocio!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            @if($users_prestamo->casanegocio!='')
                            <p><b>Casa/Negocio:</b> {{ $users_prestamo->casanegocio }}</p>
                            @endif
                            <!-- @if($users_prestamo->ruc_ac_economica!='')
                            <p><b>RUC:</b> {{ $users_prestamo->ruc_ac_economica }}</p>
                            @endif
                            @if($users_prestamo->razonsocial_ac_economica!='')
                            <p><b>Nombre: Persona Natural/Persona Jurídica:</b> {{ $users_prestamo->razonsocial_ac_economica }}</p>
                            @endif -->
                            @if($users_prestamo->direccion_ac_economica!='')
                            <p><b>Direccion:</b> {{ $users_prestamo->direccion_ac_economica }}</p>
                            @endif
                            @if($users_prestamo->db_idubigeo_ac_economica!='')
                            <p><b>Distrito – Provincia – Departamento:</b> {{ $users_prestamo->db_idubigeo_ac_economica }}</p>
                            @endif
                        </div>
                    </td>
                    @endif
                    @if($users_prestamo->referencia_ac_economica!='' or 
                        $users_prestamo->db_idlocalnegocio_ac_economica!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
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
        <h5 class="ficha-titulo">CENTRO LABORAL CLIENTE</h5>
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
        
        @if($users_prestamo->ruc_laboral_pareja!='' or
            $users_prestamo->razonsocial_laboral_pareja!='' or 
            $users_prestamo->fechainicio_laboral_pareja!='' or 
            $users_prestamo->antiguedad_laboral_pareja!='' or 
            $users_prestamo->cargo_laboral_pareja!='' or
            $users_prestamo->area_laboral_pareja!='' or 
            $users_prestamo->db_idtipocontrato_laboral_pareja!='')
        <h5 class="ficha-titulo">CENTRO LABORAL DE: PAREJA/REPRESENTANTE LEG.</h5>
        <table>
            <tbody>
                <tr>
                    @if($users_prestamo->ruc_laboral_pareja!='' or 
                        $users_prestamo->razonsocial_laboral_pareja!='' or 
                        $users_prestamo->fechainicio_laboral_pareja!='' or 
                        $users_prestamo->antiguedad_laboral_pareja!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            @if($users_prestamo->ruc_laboral_pareja!='')
                            <p><b>RUC:</b> {{ $users_prestamo->ruc_laboral_pareja }}</p>
                            @endif
                            @if($users_prestamo->razonsocial_laboral_pareja!='')
                            <p><b>Nombre: Persona Natural/Persona Jurídica:</b> {{ $users_prestamo->razonsocial_laboral_pareja }}</p>
                            @endif
                            @if($users_prestamo->fechainicio_laboral_pareja!='')
                            <p><b>Fecha Incio:</b> {{ $users_prestamo->fechainicio_laboral_pareja }}</p>
                            @endif
                            @if($users_prestamo->antiguedad_laboral_pareja!='')
                            <p><b>Antiguedad (en años):</b> {{ $users_prestamo->antiguedad_laboral_pareja }}</p>
                            @endif
                        </div>
                    </td>
                    @endif
                    @if($users_prestamo->cargo_laboral_pareja!='' or 
                        $users_prestamo->area_laboral_pareja!='' or 
                        $users_prestamo->db_idtipocontrato_laboral_pareja!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            @if($users_prestamo->cargo_laboral_pareja!='')
                            <p><b>Cargo:</b> {{ $users_prestamo->cargo_laboral_pareja }}</p>
                            @endif
                            @if($users_prestamo->area_laboral_pareja!='')
                            <p><b>Área:</b> {{ $users_prestamo->area_laboral_pareja }}</p>
                            @endif
                            @if($users_prestamo->db_idtipocontrato_laboral_pareja!='')
                            <p><b>Contrato:</b> {{ $users_prestamo->db_idtipocontrato_laboral_pareja }}</p>
                            @endif
                        </div>
                    </td>
                    @endif
                </tr>
            </tbody>
        </table>
        @endif
        
        @if($users_prestamo->db_idforma_negocio_pareja!='' or
            $users_prestamo->db_idgiro_negocio_pareja!='' or 
            $users_prestamo->descripcion_negocio_pareja!='' or 
            $users_prestamo->ruc_negocio_pareja!='' or 
            $users_prestamo->razonsocial_negocio_pareja!='' or
            $users_prestamo->direccion_negocio_pareja!='' or 
            $users_prestamo->db_idubigeo_negocio_pareja!='' or 
            $users_prestamo->referencia_negocio_pareja!='' or 
            $users_prestamo->db_idlocalnegocio_negocio_pareja!='')
        <h5 class="ficha-titulo">NEGOCIO DE: PAREJA/REPRESENTANTE LEG.</h5>
        <table>
            <tbody>
                <tr>
                    @if($users_prestamo->db_idforma_negocio_pareja!='' or 
                        $users_prestamo->db_idgiro_negocio_pareja!='' or 
                        $users_prestamo->descripcion_negocio_pareja!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            @if($users_prestamo->db_idforma_negocio_pareja!='')
                            <p><b>Forma de Activ. Econom:</b> {{ $users_prestamo->db_idforma_negocio_pareja }}</p>
                            @endif
                            @if($users_prestamo->db_idgiro_negocio_pareja!='')
                            <p><b>Giro Económico:</b> {{ $users_prestamo->db_idgiro_negocio_pareja }}</p>
                            @endif
                            @if($users_prestamo->descripcion_negocio_pareja!='')
                            <p><b>Descripción:</b> {{ $users_prestamo->descripcion_negocio_pareja }}</p> 
                            @endif
                        </div>
                    </td>
                    @endif
                    @if($users_prestamo->ruc_negocio_pareja!='' or 
                        $users_prestamo->razonsocial_negocio_pareja!='' or 
                        $users_prestamo->direccion_negocio_pareja!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            @if($users_prestamo->ruc_negocio_pareja!='')
                            <p><b>RUC:</b> {{ $users_prestamo->ruc_negocio_pareja }}</p>
                            @endif
                            @if($users_prestamo->razonsocial_negocio_pareja!='')
                            <p><b>Nombre: Persona Natural/Persona Jurídica:</b> {{ $users_prestamo->razonsocial_negocio_pareja }}</p>
                            @endif
                            @if($users_prestamo->direccion_negocio_pareja!='')
                            <p><b>Direccion:</b> {{ $users_prestamo->direccion_negocio_pareja }}</p>
                            @endif
                        </div>
                    </td>
                    @endif
                    @if($users_prestamo->db_idubigeo_negocio_pareja!='' or 
                        $users_prestamo->referencia_negocio_pareja!='' or 
                        $users_prestamo->db_idlocalnegocio_negocio_pareja!='')
                    <td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            @if($users_prestamo->db_idubigeo_negocio_pareja!='')
                            <p><b>Distrito – Provincia – Departamento:</b> {{ $users_prestamo->db_idubigeo_negocio_pareja }}</p>
                            @endif
                            @if($users_prestamo->referencia_negocio_pareja!='')
                            <p><b>Referencia de Ubicación:</b> {{ $users_prestamo->referencia_negocio_pareja }}</p>
                            @endif
                            @if($users_prestamo->db_idlocalnegocio_negocio_pareja!='')
                            <p><b>Local Negocio:</b> {{ $users_prestamo->db_idlocalnegocio_negocio_pareja }}</p>
                            @endif
                        </div>
                    </td>
                    @endif
                </tr>
            </tbody>
        </table>
        @endif

        <table style="margin-top:30px">
            <tr>
                <td style="padding:40px" align="center">
                    <hr style="border:solid 1px #ccc;">
                    <p style="padding-top:10px;">Firma y huella del cliente ó representante legal</p>
                </td>
                <td style="padding:40px" align="center">
                    <hr style="border:solid 1px #ccc;">
                    <p style="padding-top:10px;">Firma y huella de pareja del cliente</p>
                </td>
            </tr>
        </table>

    </div>
</body>
</html>