<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SOLICITUD DE CRÉDITO</title>
    <style>
        *{
            margin:0;
            padding:0;
        }
        body{
            font-family:helvetica;
            font-size:12px;
        }
        .container{
            position:absolute;  
            margin-top: -35px;
            height: 1040px;
            padding:5px 50px;
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
      
        /* NUEVOS */
      .border-td{
        border: 1px solid black;
      }
    </style>
</head>
<body>
    <style>
      html, body {
          margin-top: 38px;
          margin-bottom: 28px;
      }
      /* PDF A4 */
      .header { 
          position: fixed; 
          top: -38px; 
          left: 40px; 
          right: 40px; 
          height: 20px; 
          margin:15px;
          /* margin-left: 50px; */
          /* margin-right: 50px; */
          padding-bottom:5px;
          color: #0f0f0f;
      }
      .footer { 
          position: fixed; 
          left: 0px; 
          bottom: -25px; 
          right: 0px; 
          height: 10px; 
          margin:15px;
          margin-left: 50px;
          margin-right: 50px;
          padding-top:5px;
          /*border-top: 2px solid #31353d;*/
      }
      .page {
          float: right;
      }
      .content {
          width:100%;
          margin-left: 50px;
          margin-right: 50px;
      }
      .content_pdf {
          width:100%;
          margin-left: 50px;
          margin-right:-8px;
      }
      .content_pdf table {
          margin:0px;
          padding:0px;
          border-collapse: collapse;
          margin-right: 55px;
      }
      .content_pdf table td {
          padding:3px;
          text-align:left;
      }
      .footer .page:after { content: counter(page, decimal-leading-zero); }
      .header_agencia_logo {
          height: 50px;
          text-align: center;
          float: left;
          margin-right:10px;
      }
      .header_agencia_logo > img {
          display: block;
          max-width: 100%;
          height: 50px;
      }
      .header_agencia_informacion {
          float: right;
          width: 100%;
          text-align: right;
      }
      .header_agencia_nombrecomercial {
          font-size: 13px;
          font-weight: bold;
      }
      .header_agencia_ruc {
      }
      
    </style>
    
    <div class="header">
        <div class="header_agencia_informacion">
            <div class="header_agencia_nombrecomercial"><div style="float:left;font-size:15px;">{{ $tienda->nombre }} | {{ $tienda->nombreagencia }}</div> {{ Auth::user()->usuario }} | {{ date('d-m-Y H:iA') }}</div>
        </div>
    </div>
    <div class="footer">
        <p class="page">Página </p>
    </div>
    <div class="container">
      <h4 align="center">SOLICITUD DE CRÉDITO</h4>
      <br>
      <table>
        <tr>
          <td width="120px">N° solicitud</td>
          <td width="130px" class="border-td">S{{ str_pad($credito->id, 8, '0', STR_PAD_LEFT)  }}</td>
          <td width="10px"></td>
          <td width="110px">Fecha</td>
          <td width="120px" class="border-td">{{ date_format(date_create($credito->fecha),'d/m/Y') }}</td>
          <td width="10px"></td>
          <td width="80px">Cod. Cliente</td>
          <td class="border-td">{{ $credito->codigo_cliente }}</td>
        </tr>
        <tr>
          <td>Agencia</td>
          <td class="border-td">{{ $tienda->nombreagencia }}</td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td>Forma de Crédito</td>
          <td class="border-td">{{ $credito->forma_credito_nombre }}</td>
          <td></td>
          <td>Tipo de Cliente</td>
          <td class="border-td">{{ $credito->tipo_operacion_credito_nombre }}</td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td>Asesor(a)</td>
          <td class="border-td">JUAN ALFONZO</td>
          <td></td>
          <td>Modalidad</td>
          <td class="border-td">{{ $credito->modalidad_credito_nombre }}</td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      </table>
      <br>
      <h5>INFORMACIÓN DEL PRÉSTAMO SOLICITADO</h5>
      <br>
      <table>
        <tr>
          <td width="120px">Monto Solicitado (S/.)</td>
          <td width="130px" class="border-td">{{ $credito->monto_solicitado }}</td>
          <td width="10px"></td>
          <td width="110px">Forma de pago</td>
          <td width="120px" class="border-td">{{ $credito->forma_pago_credito_nombre }}</td>
          <td width="10px"></td>
          <td width="80px">Nro de cuotas</td>
          <td class="border-td">{{ $credito->cuotas }}</td>
        </tr>
        <tr>
          <td>Producto</td>
          <td class="border-td">{{ $credito->nombreproductocredito }}</td>
          <td></td>
          <td>Destino del Crédito</td>
          <td class="border-td">{{ $credito->tipo_destino_credito_nombre }}</td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      </table>
      <br>
      <h5 style="margin-bottom:10px;">FUENTE DE INGRESO: <b>{{ $users_prestamo ? strtoupper($users_prestamo->db_idfuenteingreso)  : '' }}</b></h5>

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
                    <td width="33%" style="padding:5px;">
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
                    <td width="33%" style="padding:5px;">
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
                    <td width="33%" style="padding:5px;">
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
        <h5 class="ficha-titulo">CENTRO LABORAL CLIENTE</h5>
        <table>
            <tbody>
                <tr>
                    @if($users_prestamo->ruc_laboral_cliente!='' or 
                        $users_prestamo->razonsocial_laboral_cliente!='' or 
                        $users_prestamo->fechainicio_laboral_cliente!='' or 
                        $users_prestamo->antiguedad_laboral_cliente!='')
                    <td width="33%" style="padding:5px;">
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
                    <td width="33%" style="padding:5px;">
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
      <br>
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
        <h5>DATOS DEL CLIENTE</h5>
        <table>
            <tr>
                @if($usuario->nombrecompleto!='' or 
                    $usuario->db_idgenero!='' or 
                    $usuario->db_idnivelestudio!='' or 
                    $users_prestamo->correo_electronico!='')
                <td width="33%" style="padding:5px;">
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
                <td width="33%" style="padding:5px;">
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
                <td width="33%" style="padding:5px;">
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
                    <td width="33%" style="padding:5px;">
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
                    <td width="33%" style="padding:5px;">
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
                    <!--td width="33%" style="padding:10px;">
                        <div class="container-informacion">
                            <p><b>Teléfono:</b></p>
                        </div>
                    </td-->
                </tr>
            </tbody>
        </table>
        @endif
      @else
        <h6 class="text-danger">Información de cliente incompleta...</h6>
      @endif
      <br>
      <h5 style="margin-bottom:10px;">GARANTÍAS DEL CLIENTE:</h5>
      <table>
        @if(count($garantia_cliente)>0)
        <thead>
          <th width="70px" class="border-td">Tipo</th>
          <th class="border-td">Descripción</th>
          <th width="120px" class="border-td">Valor de Mercado</th>
          <th width="120px" class="border-td">Valor Comercial (Tasador)</th>
          <th width="120px" class="border-td">Valor de realización(Tasador)</th>
        </thead>
        <tbody>
          <tr>
            @php
              $total_valor_mercado = 0;
              $total_valor_comercial = 0;
              $total_valor_realizacion = 0;
            @endphp
            @foreach($garantia_cliente as $value)
              <tr>
                 <td class="border-td">
                   @if($value->idgarantias!=0)
                      Prendario
                   @else
                      No Prendario
                   @endif
                 </td>
                 <td class="border-td">{{ $value->descripcion }}</td>
                 <td class="border-td" align="right">
                   @if($value->idgarantias!=0)
                      --
                   @else
                      {{ $value->valor_mercado }}
                   @endif
                 </td>
                 <td class="border-td" align="right">
                   @if($value->idgarantias!=0)
                      --
                   @else
                      {{ $value->valor_comercial }}
                   @endif
                 </td>
                 <td class="border-td" align="right">{{ $value->valor_realizacion }}</td>
              </tr>
              @php
                $total_valor_mercado += $value->idgarantias==0?$value->valor_mercado:0;
                $total_valor_comercial += $value->idgarantias==0?$value->valor_comercial:0;
                $total_valor_realizacion += $value->valor_realizacion;
              @endphp
            @endforeach
          </tr>
          <tr>
            <td align="right" colspan="2">Total (S/.)</td>
            <td class="border-td" align="right">{{ $total_valor_mercado }}</td>
            <td class="border-td" align="right">{{ $total_valor_comercial }}</td>
            <td class="border-td" align="right">{{ $total_valor_realizacion }}</td>
          </tr>
        </tbody>
        @else
        <thead>
          <th>Descripción</th>
          <th width="120px">Monto Solicitado</th>
        </thead>
        <tbody>
          <tr>
             <td>PAGARE</td>
             <td align="right">{{ $credito->monto_solicitado }}</td>
          </tr>
          <tr>
            <td align="right">Total (S/.)</td>
            <td class="border-td" align="right">{{ $credito->monto_solicitado }}</td>
          </tr>
        @endif
      </table>
      @if($credito->idaval != 0)
      <br>
      <h5 style="margin-bottom:10px;">GARANTÍAS  DEL AVAL(GARANTE)/FIADOR: 
        @if($usuario_aval->nombrecompleto!='')
          <b>{{ $usuario_aval->nombrecompleto }}</b>
        @endif
      </h5>
      <table border=1>
        @if(count($garantia_aval)>0)
        <thead>
          <th width="70px">Tipo</th>
          <th>Descripción</th>
          <th width="120px">Valor de Mercado</th>
          <th width="120px">Valor Comercial (Tasador)</th>
          <th width="120px">Valor de realización(Tasador)</th>
        </thead>
        <tbody>
          <tr>
            @php
              $total_valor_mercado_aval = 0;
              $total_valor_comercial_aval = 0;
              $total_valor_realizacion_aval = 0;
            @endphp
            @foreach($garantia_aval as $value)
              <tr>
                 <td>
                   @if($value->idgarantias!=0)
                      Prendario
                   @else
                      No Prendario
                   @endif
                 </td>
                 <td>{{ $value->descripcion }}</td>
                 <td align="right">
                   @if($value->idgarantias!=0)
                      --
                   @else
                      {{ $value->valor_mercado }}
                   @endif
                 </td>
                 <td align="right">
                   @if($value->idgarantias!=0)
                      --
                   @else
                      {{ $value->valor_comercial }}
                   @endif
                 </td>
                 <td align="right">{{ $value->valor_realizacion }}</td>
              </tr>
              @php
                $total_valor_mercado_aval += $value->idgarantias==0?$value->valor_mercado:0;
                $total_valor_comercial_aval += $value->idgarantias==0?$value->valor_comercial:0;
                $total_valor_realizacion_aval += $value->valor_realizacion;
              @endphp
            @endforeach
          </tr>
          <tr>
            <td align="right" colspan="2">Total (S/.)</td>
            <td class="border-td" align="right">{{ $total_valor_mercado_aval }}</td>
            <td class="border-td" align="right">{{ $total_valor_comercial_aval }}</td>
            <td class="border-td" align="right">{{ $total_valor_realizacion_aval }}</td>
          </tr>
        </tbody>
        @else
        <thead>
          <th>Descripción</th>
          <th width="120px">Monto Solicitado</th>
        </thead>
        <tbody>
          <tr>
             <td>PAGARE</td>
             <td align="right">{{ $credito->monto_solicitado }}</td>
          </tr>
          <tr>
            <td align="right">Total (S/.)</td>
            <td class="border-td" align="right">{{ $credito->monto_solicitado }}</td>
          </tr>
        @endif
      </table>
      @endif
      <p>La presente Información tiene carácter de Declaración Jurada. Donde la información proporcionada es verídica y autorizo a {{$tienda->nombre}} verifique los datos consignados en la presente Declaración Jurada.</p>
      <table style="margin-top:60px">
          <tr>
              <td style="padding:0px; width:33%;" align="center">
                  <div style="border-top:solid 1px #000;margin-left:20px;margin-right:20px;"></div>
                  <p style="padding-top:10px;">Firma del Cliente</p>
              </td>
            @if($users_prestamo->dni_pareja!='' or
            $users_prestamo->nombrecompleto_pareja!='' or 
            $users_prestamo->db_idnivelestudio_pareja!='' or 
            $users_prestamo->db_idocupacion_pareja!='' or 
            $users_prestamo->profesion_pareja!='')
              <td style="padding:0px; width:33%;" align="center">
                  <div style="border-top:solid 1px #000;margin-left:20px;margin-right:20px;"></div>
                  <p style="padding-top:10px;">Firma de pareja del Cliente</p>
              </td>
            @endif
            @if($credito->idaval != 0)
              @if($usuario_aval->nombrecompleto!='')
                <td style="padding:0px; width:33%;" align="center">
                    <div style="border-top:solid 1px #000;margin-left:20px;margin-right:20px;"></div>
                    <p style="padding-top:10px;">Firma de Aval</p>
                </td>
              @endif
              
            @endif
          </tr>
      </table>
      

      

    </div>
</body>
</html>