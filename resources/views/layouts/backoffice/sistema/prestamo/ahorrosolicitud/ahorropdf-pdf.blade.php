<!DOCTYPE html>
<html>
<head>
    <title>SOLICITUD DE AHORRO</title>
    @include('app.pdf_style',['idtienda'=>$tienda->id])
</head>
<body>
    @include('app.pdf_headerfooter',[
        'logo'=>$tienda->imagen,
        'nombrecomercial'=>$tienda->nombre,
        'direccion'=>$tienda->direccion,
        'ubigeo'=>$tienda->ubigeonombre,
        'tienda'=>$tienda,
    ])
    <div class="content">
        <div class="titulo">SOLICITUD DE AHORRO</div>
        <table class="tabla_informativa">
            <tr>
                <td style="text-align:right;">NEGOCIO {{$ahorro_laboral!=''?($ahorro_laboral->idprestamo_giro==1?'('.$ahorro_laboral->nombre_fuenteingreso.')':''):''}}</td>
                <td>(<?php echo $ahorro_laboral!=''?($ahorro_laboral->idprestamo_giro==1?'X':'&nbsp;'):'&nbsp;'?>)</td>
                <td style="text-align:right;">SERVICIO {{$ahorro_laboral!=''?($ahorro_laboral->idprestamo_giro==2?'('.$ahorro_laboral->nombre_fuenteingreso.')':''):''}}</td>
                <td>(<?php echo $ahorro_laboral!=''?($ahorro_laboral->idprestamo_giro==2?'X':'&nbsp;'):'&nbsp;'?>)</td>
                <td style="text-align:right;">TRANSPORTE {{$ahorro_laboral!=''?($ahorro_laboral->idprestamo_giro==3?'('.$ahorro_laboral->nombre_fuenteingreso.')':''):''}}</td>
                <td>(<?php echo $ahorro_laboral!=''?($ahorro_laboral->idprestamo_giro==3?'X':'&nbsp;'):'&nbsp;'?>)</td>
            </tr>
        </table>
        <div class="espacio"></div>
        <table class="tabla">
            <tr class="tabla_cabera">
              <td colspan="6">SOLICITUD DEL CLIENTE</td>
              <td style="text-align:right;">FECHA</td>
              <td>{{date_format(date_create($prestamoahorro->fecharegistro),"d/m/Y")}}</td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="3" style="width:40%;text-align:center;">APELLIDOS Y NOMBRES DEL SOLICITANTE</td>
              <td class="tabla_titulo" style="width:10%;text-align:center;">Nº DNI</td>
              <td class="tabla_titulo" colspan="4" style="width:50%;text-align:center;">INFORMACIÓN DEL NEGOCIO</td>
            </tr>
            <tr>
              <td colspan="3" style="text-align:center;">{{$prestamoahorro->clienteapellidos}}, {{$prestamoahorro->clientenombre}}</td>
              <td style="text-align:center;">{{$prestamoahorro->clienteidentificacion}}</td>
              <td class="tabla_titulo" style="text-align:center;">ACTIVIDAD</td>
              <td colspan="3"><?php echo $ahorro_laboral!=''?($ahorro_laboral->actividad!=''?$ahorro_laboral->actividad:'&nbsp;'):'&nbsp;'?></td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="4" style="text-align:center;">DIRECCIÓN DEL DOMICILIO</td>
              <td class="tabla_titulo" colspan="4" style="text-align:center;">DIRECCIÓN DEL NEGOCIO</td>
            </tr>
            <tr>
              <td colspan="4" style="text-align:center;"><?php echo $prestamoahorro->clientedireccion!=''?$prestamoahorro->clientedireccion:'&nbsp;' ?></td>
              <td colspan="4" style="text-align:center;"><?php echo $ahorro_laboral!=''?($ahorro_laboral->direccion!=''?$ahorro_laboral->direccion:'&nbsp;'):'&nbsp;'?></td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="4" style="text-align:center;">DISTRITO/PROVINCIA/DEPARTAMENTO</td>
              <td class="tabla_titulo" colspan="4" style="text-align:center;">DISTRITO/PROVINCIA/DEPARTAMENTO</td>
            </tr>
            <tr>
              <td colspan="4" style="text-align:center;"><?php echo $prestamoahorro->clienteubigeonombre!=''?$prestamoahorro->clienteubigeonombre:'&nbsp;' ?></td>
              <td colspan="4" style="text-align:center;"><?php echo $ahorro_laboral!=''?($ahorro_laboral->ubigeonombre!=''?$ahorro_laboral->ubigeonombre:'&nbsp;'):'&nbsp;'?></td>
            </tr>
            <tr>
              <td class="tabla_titulo">REFERENCIA</td>
              <td colspan="3"><?php echo $prestamoahorro->clientereferencia!=''?$prestamoahorro->clientereferencia:'&nbsp;' ?></td>
              <td class="tabla_titulo" colspan="4" style="text-align:center;">REFERENCIA DEL NEGOCIO</td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="3" style="text-align:center;">APELLIDOS Y NOMBRES DEL CONYUGUE</td>
              <td class="tabla_titulo" style="text-align:center;">Nº DNI</td>
              <td colspan="4" style="text-align:center;"><?php echo $ahorro_laboral!=''?($ahorro_laboral->referencia!=''?$ahorro_laboral->referencia:'&nbsp;'):'&nbsp;'?></td>
            </tr>
            <tr>
              <td colspan="3" style="text-align:center;">
                @if($prestamoahorro->idconyuge!=0)
                {{$prestamoahorro->conyugeapellidos}}, {{$prestamoahorro->conyugenombre}}
                @else
                &nbsp;
                @endif
              </td>
              <td style="text-align:center;">{{$prestamoahorro->conyugeidentificacion}}</td>
              <td class="tabla_titulo" colspan="4" style="text-align:center;">NOMBRE DEL NEGOCIO</td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="3" style="text-align:center;">APELLIDOS Y NOMBRES DE BENEFICIARIO</td>
              <td class="tabla_titulo" style="text-align:center;">Nº DNI</td>
              <td colspan="4" style="text-align:center;"><?php echo $ahorro_laboral!=''?($ahorro_laboral->nombrenegocio!=''?$ahorro_laboral->nombrenegocio:'&nbsp;'):'&nbsp;'?></td>
            </tr>
            <tr>
              <td colspan="3" style="text-align:center;">
                @if($prestamoahorro->idbeneficiario!=0)
                {{$prestamoahorro->beneficiarioapellidos}}, {{$prestamoahorro->beneficiarionombre}}
                @else
                &nbsp;
                @endif
              </td>
              <td style="text-align:center;">{{$prestamoahorro->beneficiarioidentificacion}}</td>
              
              <td class="tabla_titulo" colspan="2">FUENTE DE INGRESO</td>
              <td colspan="2"><?php echo $ahorro_laboral!=''?($ahorro_laboral->nombre_fuenteingreso!=''?$ahorro_laboral->nombre_fuenteingreso:'&nbsp;'):'&nbsp;'?></td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="4" style="text-align:center;">DIRECCIÓN DEL DOMICILIO DE BENEFICIARIO</td>
              
              <td class="tabla_titulo" colspan="2">GIRO DE NEGOCIO</td>
              <td colspan="2"><?php echo $ahorro_laboral!=''?($ahorro_laboral->nombre_giro!=''?$ahorro_laboral->nombre_giro:'&nbsp;'):'&nbsp;'?></td>
            </tr>
            <tr>
              <td colspan="4" style="text-align:center;"><?php echo $prestamoahorro->beneficiariodireccion!=''?$prestamoahorro->beneficiariodireccion:'&nbsp;' ?></td>
              <td class="tabla_titulo" colspan="2">REGISTRADO EN SUNAT</td>
              <td colspan="2">
           
              </td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="4" style="text-align:center;">DISTRITO/PROVINCIA/DEPARTAMENTO</td>
              
              <td class="tabla_titulo" colspan="2">NÚMERO DE RUC</td>
              <td colspan="2"></td>
            </tr>
            <tr>
              <td colspan="4" style="text-align:center;"><?php echo $prestamoahorro->beneficiarioubigeonombre!=''?$prestamoahorro->beneficiarioubigeonombre:'&nbsp;' ?></td>
              <td class="tabla_titulo" colspan="4" style="text-align:center;">EMITE FACTURA Y/O BOLETA DE VENTA</td>
            </tr>
            <tr>
              <td class="tabla_titulo">REFERENCIA</td>
              <td colspan="3"><?php echo $prestamoahorro->beneficiarioreferencia!=''?$prestamoahorro->beneficiarioreferencia:'&nbsp;' ?></td>
              <td colspan="4" style="text-align:center;">
              
              </td>
            </tr>
        </table>
        <div class="espacio"></div>
        <table class="tabla">
            <tr class="tabla_cabera">
              <td colspan="8">SOCIOS DEL CLIENTE</td>
            </tr>
          <tr>
              <td class="tabla_titulo" colspan="5" style="text-align:center;">PERSONAS QUE FIRMAN LA DECLARACIÓN Y CONTRATO</td>
              <td class="tabla_titulo" style="text-align:center;">DNI</td>
              <td class="tabla_titulo" style="text-align:right;">TOTAL</td>
              <td class="tabla_titulo" style="text-align:right;"></td>
            </tr>
            <tr>
              <td colspan="2" class="tabla_titulo">TITULAR</td>
              <td colspan="3">{{$prestamoahorro->clienteapellidos}}, {{$prestamoahorro->clientenombre}}</td>
              <td style="text-align:center;">{{$prestamoahorro->clienteidentificacion}}</td>
              <td colspan="2" rowspan="4"></td>
            </tr>
            <tr>
              <td colspan="2" class="tabla_titulo">CONYUGUE</td>
              <td colspan="3">
                @if($prestamoahorro->idconyuge!=0)
                {{$prestamoahorro->conyugeapellidos}}, {{$prestamoahorro->conyugenombre}}
                @else
                &nbsp;
                @endif
              </td>
              <td style="text-align:center;">{{$prestamoahorro->conyugeidentificacion}}</td>
            </tr>
            <tr>
              <td colspan="2" class="tabla_titulo">BENEFICIARIO</td>
              <td colspan="3">
                @if($prestamoahorro->idbeneficiario!=0)
                {{$prestamoahorro->beneficiarioapellidos}}, {{$prestamoahorro->beneficiarionombre}}
                @else
                &nbsp;
                @endif
              </td>
              <td style="text-align:center;">{{$prestamoahorro->beneficiarioidentificacion}}</td>
            </tr>
            <tr>
              <td colspan="3" style="width:160px;"><br><br><br><br><br><br></td>
              <td style="width:160px;">&nbsp;</td>
              <td colspan="2" style="width:160px;">&nbsp;</td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="3" style="text-align:center;">FIRMA DEL TITULAR</td>
              <td class="tabla_titulo" style="text-align:center;">FIRMA DEL CONYUGUE</td>
              <td class="tabla_titulo" colspan="2" style="text-align:center;">FIRMA DEL BENEFICIARIO</td>
              <td class="tabla_titulo" colspan="2" style="text-align:center;">FIRMA Y SELLO DEL ASESOR</td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="8" style="text-align:center;">DECLARO QUE LOS DATOS CONSIGNADOS EN LA PRESENTE SON DE MI ENTERA RESPONSABILIDAD</td>
            </tr>
        </table>
        <div class="espacio"></div>
        <table class="tabla">
            <tr class="tabla_cabera">
              <td colspan="6">PROPUESTA DEL ASESOR DE NEGOCIOS</td>
            </tr>
            <tr>
              <td class="tabla_titulo" style="height:15px;">TIPO DE AHORRO</td>
              <td colspan="5">
                <?php echo $prestamoahorro->tipoahorronombre ?>
              </td>
            </tr>
            <tr>
              <td class="tabla_titulo" style="text-align:center;">MONTO</td>
              <td class="tabla_titulo" style="text-align:center;">TASA (TEM)</td>
              <td class="tabla_titulo" style="text-align:center;">MONTO DE CUOTA</td>
              <td class="tabla_titulo" style="text-align:center;">CUOTAS</td>
              <td class="tabla_titulo" style="text-align:center;">FRECUENCIA</td>
              <td class="tabla_titulo" style="text-align:center;">PAGO DE LA 1º CUOTA</td>
            </tr>
            <tr>
              <td style="text-align:center;">{{$prestamoahorro->monto}}</td>
              <td style="text-align:center;">{{$prestamoahorro->tasa}}</td>
              <td style="text-align:center;">{{$prestamoahorro->monedasimbolo}} {{$prestamoahorro->monto}}</td>
              <td style="text-align:center;">{{$prestamoahorro->numerocuota}} CUOTAS</td>
              <td style="text-align:center;">{{$prestamoahorro->frecuencia_nombre}}</td>
              <td style="text-align:center;">{{date_format(date_create($prestamoahorro->fechainicio),"d/m/Y")}}</td>
            </tr>
        </table>
        <div class="espacio"></div>
        <table class="tabla">
            <tr class="tabla_cabera">
              <td colspan="6">RESOLUCIÓN DEL COMITÉ DEL AHORRO</td>
            </tr>
            <tr>
              <td class="tabla_titulo">ESTADO</td>
              <td colspan="3">
                @if( $prestamoahorro->idestadoaprobacion==1)
                AHORRO APROBADO
                @elseif( $prestamoahorro->idestadoaprobacion==2)
                AHORRO RECHAZADO
                @elseif( $prestamoahorro->idestadoaprobacion==3)
                AHORRO DENEGADO
                @endif
              </td>
              <td class="tabla_titulo">FECHA</td>
              <td>
                @if( $prestamoahorro->idestadoaprobacion==1)
                {{date_format(date_create($prestamoahorro->fechaaprobado),"d/m/Y h:i A")}}
                @elseif( $prestamoahorro->idestadoaprobacion==2)
                {{date_format(date_create($prestamoahorro->fecharechazado),"d/m/Y h:i A")}}
                @elseif( $prestamoahorro->idestadoaprobacion==3)
                {{date_format(date_create($prestamoahorro->fechadenegado),"d/m/Y h:i A")}}
                @endif
              </td>
            </tr>
            <tr>
              <td class="tabla_titulo" style="text-align:center;">MONTO</td>
              <td class="tabla_titulo" style="text-align:center;">TASA (TEM)</td>
              <td class="tabla_titulo" style="text-align:center;">MONTO DE CUOTA</td>
              <td class="tabla_titulo" style="text-align:center;">Nº DE CUOTAS</td>
              <td class="tabla_titulo" style="text-align:center;">PAGO DE LA 1º CUOTA</td>
              <td class="tabla_titulo" style="text-align:center;">FRECUENCIA</td>
            </tr>
            <tr>
              <td style="text-align:center;">{{$prestamoahorro->monto}}</td>
              <td style="text-align:center;">{{$prestamoahorro->tasa}}</td>
              <td style="text-align:center;">{{$prestamoahorro->monto}} CUOTAS</td>
              <td style="text-align:center;">{{$prestamoahorro->numerocuota}}</td>
              <td style="text-align:center;">{{date_format(date_create($prestamoahorro->fechainicio),"d/m/Y")}}</td>
              <td style="text-align:center;">{{$prestamoahorro->frecuencia_nombre}}</td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="2">COMENTARIOS/OBSERVACIONES</td>
              <td colspan="4">{{$prestamoahorro->comentariosupervisor}}</td>
            </tr>
          </tbody>
        </table>
    </div>
</body>
</html>