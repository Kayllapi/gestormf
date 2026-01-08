<!DOCTYPE html>
<html>
<head>
    <title>SOLICITUD DE CRÉDITO</title>
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
        <div class="titulo">SOLICITUD DE CRÉDITO</div>
        <table class="tabla_informativa">
            <tr>
                <td style="text-align:right;">NEGOCIO {{$credito_laboral!=''?($credito_laboral->idprestamo_giro==1?'('.$credito_laboral->nombre_fuenteingreso.')':''):''}}</td>
                <td>(<?php echo $credito_laboral!=''?($credito_laboral->idprestamo_giro==1?'X':'&nbsp;'):'&nbsp;'?>)</td>
                <td style="text-align:right;">SERVICIO {{$credito_laboral!=''?($credito_laboral->idprestamo_giro==2?'('.$credito_laboral->nombre_fuenteingreso.')':''):''}}</td>
                <td>(<?php echo $credito_laboral!=''?($credito_laboral->idprestamo_giro==2?'X':'&nbsp;'):'&nbsp;'?>)</td>
                <td style="text-align:right;">TRANSPORTE {{$credito_laboral!=''?($credito_laboral->idprestamo_giro==3?'('.$credito_laboral->nombre_fuenteingreso.')':''):''}}</td>
                <td>(<?php echo $credito_laboral!=''?($credito_laboral->idprestamo_giro==3?'X':'&nbsp;'):'&nbsp;'?>)</td>
            </tr>
            <tr>
                <td style="text-align:right;">REFINANCIACIÓN</td>
                <td>(<?php echo $prestamocredito->idprestamo_creditorefinanciado!=0?'X':'&nbsp;' ?>)</td>
                <td style="text-align:right;">REPROGRAMACIÓN</td>
                <td>(<?php echo $prestamocredito->idprestamo_creditoreprogramado!=0?'X':'&nbsp;' ?>)</td>
                <td style="text-align:right;">AMPLIACIÓN</td>
                <td>(<?php echo $prestamocredito->idprestamo_creditoampliado!=0?'X':'&nbsp;' ?>)</td>
            </tr>
        </table>
        <div class="espacio"></div>
        <table class="tabla">
            <tr class="tabla_cabera">
              <td colspan="6">SOLICITUD DEL CLIENTE</td>
              <td style="text-align:right;">FECHA</td>
              <td>{{date_format(date_create($prestamocredito->fecharegistro),"d/m/Y")}}</td>
            </tr>
            <!--tr>
              <td class="tabla_titulo" colspan="2">AGENCIA/OFICINA</td>
              <td colspan="6">{{$prestamocredito->tiendanombre}}</td>
            </tr-->
            <tr>
              <td class="tabla_titulo" colspan="3" style="width:40%;text-align:center;">APELLIDOS Y NOMBRES DEL SOLICITANTE</td>
              <td class="tabla_titulo" style="width:10%;text-align:center;">Nº DNI</td>
              <td class="tabla_titulo" colspan="4" style="width:50%;text-align:center;">INFORMACIÓN DEL NEGOCIO</td>
            </tr>
            <tr>
              <td colspan="3" style="text-align:center;">{{$prestamocredito->clienteapellidos}}, {{$prestamocredito->clientenombre}}</td>
              <td style="text-align:center;">{{$prestamocredito->clienteidentificacion}}</td>
              <td class="tabla_titulo" style="text-align:center;">ACTIVIDAD</td>
              <td colspan="3"><?php echo $credito_laboral!=''?($credito_laboral->actividad!=''?$credito_laboral->actividad:'&nbsp;'):'&nbsp;'?></td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="4" style="text-align:center;">DIRECCIÓN DEL DOMICILIO</td>
              <td class="tabla_titulo" colspan="4" style="text-align:center;">DIRECCIÓN DEL NEGOCIO</td>
            </tr>
            <tr>
              <td colspan="4" style="text-align:center;"><?php echo $prestamocredito->clientedireccion!=''?$prestamocredito->clientedireccion:'&nbsp;' ?></td>
              <td colspan="4" style="text-align:center;"><?php echo $credito_laboral!=''?($credito_laboral->direccion!=''?$credito_laboral->direccion:'&nbsp;'):'&nbsp;'?></td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="4" style="text-align:center;">DISTRITO/PROVINCIA/DEPARTAMENTO</td>
              <td class="tabla_titulo" colspan="4" style="text-align:center;">DISTRITO/PROVINCIA/DEPARTAMENTO</td>
            </tr>
            <tr>
              <td colspan="4" style="text-align:center;"><?php echo $prestamocredito->clienteubigeonombre!=''?$prestamocredito->clienteubigeonombre:'&nbsp;' ?></td>
              <td colspan="4" style="text-align:center;"><?php echo $credito_laboral!=''?($credito_laboral->ubigeonombre!=''?$credito_laboral->ubigeonombre:'&nbsp;'):'&nbsp;'?></td>
            </tr>
            <tr>
              <td class="tabla_titulo">REFERENCIA</td>
              <td colspan="3"><?php echo $prestamocredito->clientereferencia!=''?$prestamocredito->clientereferencia:'&nbsp;' ?></td>
              <td class="tabla_titulo" colspan="4" style="text-align:center;">REFERENCIA DEL NEGOCIO</td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="3" style="text-align:center;">APELLIDOS Y NOMBRES DEL CONYUGUE</td>
              <td class="tabla_titulo" style="text-align:center;">Nº DNI</td>
              <td colspan="4" style="text-align:center;"><?php echo $credito_laboral!=''?($credito_laboral->referencia!=''?$credito_laboral->referencia:'&nbsp;'):'&nbsp;'?></td>
            </tr>
            <tr>
              <td colspan="3" style="text-align:center;">
                @if($prestamocredito->idconyuge!=0)
                {{$prestamocredito->conyugeapellidos}}, {{$prestamocredito->conyugenombre}}
                @else
                &nbsp;
                @endif
              </td>
              <td style="text-align:center;">{{$prestamocredito->conyugeidentificacion}}</td>
              <td class="tabla_titulo" colspan="4" style="text-align:center;">NOMBRE DEL NEGOCIO</td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="3" style="text-align:center;">APELLIDOS Y NOMBRES DE GARANTE</td>
              <td class="tabla_titulo" style="text-align:center;">Nº DNI</td>
              <td colspan="4" style="text-align:center;"><?php echo $credito_laboral!=''?($credito_laboral->nombrenegocio!=''?$credito_laboral->nombrenegocio:'&nbsp;'):'&nbsp;'?></td>
            </tr>
            <tr>
              <td colspan="3" style="text-align:center;">
                @if($prestamocredito->idgarante!=0)
                {{$prestamocredito->garanteapellidos}}, {{$prestamocredito->garantenombre}}
                @else
                &nbsp;
                @endif
              </td>
              <td style="text-align:center;">{{$prestamocredito->garanteidentificacion}}</td>
              
              <td class="tabla_titulo" colspan="2">FUENTE DE INGRESO</td>
              <td colspan="2"><?php echo $credito_laboral!=''?($credito_laboral->nombre_fuenteingreso!=''?$credito_laboral->nombre_fuenteingreso:'&nbsp;'):'&nbsp;'?></td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="4" style="text-align:center;">DIRECCIÓN DEL DOMICILIO DE GARANTE</td>
              
              <td class="tabla_titulo" colspan="2">GIRO DE NEGOCIO</td>
              <td colspan="2"><?php echo $credito_laboral!=''?($credito_laboral->nombre_giro!=''?$credito_laboral->nombre_giro:'&nbsp;'):'&nbsp;'?></td>
            </tr>
            <tr>
              <td colspan="4" style="text-align:center;"><?php echo $prestamocredito->garantedireccion!=''?$prestamocredito->garantedireccion:'&nbsp;' ?></td>
              <td class="tabla_titulo" colspan="2">REGISTRADO EN SUNAT</td>
              <td colspan="2">
                @if($credito_laboral!='')
                @if($credito_laboral->estadoficharuc=='on')
                    SI
                @else
                    NO
                @endif
                @else
                  &nbsp;
                @endif
              </td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="4" style="text-align:center;">DISTRITO/PROVINCIA/DEPARTAMENTO</td>
              
              <td class="tabla_titulo" colspan="2">NÚMERO DE RUC</td>
              <td colspan="2"><?php echo $credito_laboral!=''?($credito_laboral->rucficharuc!=''?$credito_laboral->rucficharuc:'SIN RUC'):'SIN RUC'?></td>
            </tr>
            <tr>
              <td colspan="4" style="text-align:center;"><?php echo $prestamocredito->garanteubigeonombre!=''?$prestamocredito->garanteubigeonombre:'&nbsp;' ?></td>
              <td class="tabla_titulo" colspan="4" style="text-align:center;">EMITE FACTURA Y/O BOLETA DE VENTA</td>
            </tr>
            <tr>
              <td class="tabla_titulo">REFERENCIA</td>
              <td colspan="3"><?php echo $prestamocredito->garantereferencia!=''?$prestamocredito->garantereferencia:'&nbsp;' ?></td>
              <td colspan="4" style="text-align:center;">
                @if($credito_laboral!='')
                @if($credito_laboral->emisioncomprobante==1)
                    FACTURA
                @elseif($credito_laboral->emisioncomprobante==2)
                    BOLETA
                @elseif($credito_laboral->emisioncomprobante==3)
                    FACTURA Y BOLETA
                @else
                  SIN COMPROBANTES
                @endif
                @else
                  SIN COMPROBANTES
                @endif
              </td>
            </tr>
        </table>
        <div class="espacio"></div>
        <table class="tabla">
            <tr class="tabla_cabera">
              <td colspan="8">GARANTIAS DEL CLIENTE</td>
            </tr>
            <tr>
              <td class="tabla_titulo" style="text-align:center;width:10px;">Nº</td>
              <td class="tabla_titulo" colspan="2" style="text-align:center;">PRODUCTO</td>
              <td class="tabla_titulo" colspan="3" style="text-align:center;">DESCRIPCIÓN</td>
              <td class="tabla_titulo" style="text-align:center;">DOCUMENTO</td>
              <td class="tabla_titulo" style="text-align:center;">VALOR</td>
            </tr>
                <?php $total_valor = 0;  ?>
                <?php $num = 1;  ?>
                @foreach($bienes as $value)
                <tr>
                  <td style="text-align:center;">{{ $num }}</td>
                  <td colspan="2">{{$value->producto}}</td>
                  <td colspan="3">{{$value->descripcion}}</td>
                  <td>
                      @if($value->idprestamo_documento==1)
                          SIN DOCUMENTOS
                      @elseif($value->idprestamo_documento==2)
                          COPIA/LEGALIZADO
                      @elseif($value->idprestamo_documento==3)
                          ORIGINAL
                      @endif
                  </td>
                  <td style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{$value->valorestimado}}</td>
                  <?php $total_valor = $total_valor+$value->valorestimado;  ?>
                  <?php $num++; ?>
                </tr>
                @endforeach 
                @for($i=$num; $i<=5; $i++)
                <tr>
                  <td>&nbsp;</td>
                  <td colspan="2"></td>
                  <td colspan="3"></td>
                  <td></td>
                  <td></td>
                </tr>
                @endfor
          <tr>
              <td class="tabla_titulo" colspan="5" style="text-align:center;">PERSONAS QUE FIRMAN LA LETRA DE CAMBIO, PAGARE Y CONTRATO</td>
              <td class="tabla_titulo" style="text-align:center;">DNI</td>
              <td class="tabla_titulo" style="text-align:right;">TOTAL</td>
              <td class="tabla_titulo" style="text-align:right;">{{$prestamocredito->monedasimbolo}} {{number_format($total_valor, 2, '.', '')}}</td>
            </tr>
            <tr>
              <td colspan="2" class="tabla_titulo">TITULAR</td>
              <td colspan="3">{{$prestamocredito->clienteapellidos}}, {{$prestamocredito->clientenombre}}</td>
              <td style="text-align:center;">{{$prestamocredito->clienteidentificacion}}</td>
              <td colspan="2" rowspan="4"></td>
            </tr>
            <tr>
              <td colspan="2" class="tabla_titulo">CONYUGUE</td>
              <td colspan="3">
                @if($prestamocredito->idconyuge!=0)
                {{$prestamocredito->conyugeapellidos}}, {{$prestamocredito->conyugenombre}}
                @else
                &nbsp;
                @endif
              </td>
              <td style="text-align:center;">{{$prestamocredito->conyugeidentificacion}}</td>
            </tr>
            <tr>
              <td colspan="2" class="tabla_titulo">GARANTE</td>
              <td colspan="3">
                @if($prestamocredito->idgarante!=0)
                {{$prestamocredito->garanteapellidos}}, {{$prestamocredito->garantenombre}}
                @else
                &nbsp;
                @endif
              </td>
              <td style="text-align:center;">{{$prestamocredito->garanteidentificacion}}</td>
            </tr>
            <tr>
              <td colspan="3" style="width:160px;"><br><br><br><br><br><br></td>
              <td style="width:160px;">&nbsp;</td>
              <td colspan="2" style="width:160px;">&nbsp;</td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="3" style="text-align:center;">FIRMA DEL TITULAR</td>
              <td class="tabla_titulo" style="text-align:center;">FIRMA DEL CONYUGUE</td>
              <td class="tabla_titulo" colspan="2" style="text-align:center;">FIRMA DEL GARANTE</td>
              <td class="tabla_titulo" colspan="2" style="text-align:center;">FIRMA Y SELLO DEL ASESOR</td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="8" style="text-align:center;">DECLARO QUE LOS DATOS CONSIGNADOS EN LA PRESENTE SON DE MI ENTERA RESPONSABILIDAD</td>
            </tr>
        </table>
        <div class="espacio"></div>
        <table class="tabla">
            <tr class="tabla_cabera">
              <td colspan="8">PROPUESTA DEL ASESOR DE NEGOCIOS</td>
            </tr>
            <tr>
              <td class="tabla_titulo" style="text-align:center;">MONTO</td>
              <td class="tabla_titulo" style="text-align:center;">TASA (TEM)</td>
              <td class="tabla_titulo" style="text-align:center;">MONTO DE CUOTA</td>
              <td class="tabla_titulo" style="text-align:center;">CUOTAS</td>
              <td class="tabla_titulo" style="text-align:center;">FRECUENCIA</td>
              <td class="tabla_titulo" style="text-align:center;">PAGO DE LA 1º CUOTA</td>
              <td class="tabla_titulo" style="text-align:center;" colspan="2">CALIFICACIÓN</td>
            </tr>
            <tr>
              <td style="text-align:center;">{{$prestamocredito->monto}}</td>
              <td style="text-align:center;">{{$prestamocredito->tasa}}</td>
              <td style="text-align:center;">{{$prestamocredito->monedasimbolo}} {{$prestamocredito->cuota}}</td>
              <td style="text-align:center;">{{$prestamocredito->numerocuota}} CUOTAS</td>
              <td style="text-align:center;">{{$prestamocredito->frecuencia_nombre}}</td>
              <td style="text-align:center;">{{date_format(date_create($prestamocredito->fechainicio),"d/m/Y")}}</td>
              <td style="text-align:center;" colspan="2">{{$prestamosustento!=''?$prestamosustento->calificacion:'SIN CALIFICACIÓN'}}</td>
            </tr>
            <tr>
              <td class="tabla_titulo" style="height:15px;">COMENTARIO</td>
              <td colspan="7">
                <?php echo $prestamosustento!=''?$prestamosustento->comentarioasesor:'&nbsp;' ?>
              </td>
            </tr>
        </table>
        <div class="espacio"></div>
        <table class="tabla">
            <tr class="tabla_cabera">
              <td colspan="6">RESOLUCIÓN DEL COMITÉ DEL CRÉDITO</td>
            </tr>
            <tr>
              <td class="tabla_titulo">ESTADO</td>
              <td colspan="3">
                @if( $prestamocredito->idestadoaprobacion==1)
                CRÉDITO APROBADO
                @elseif( $prestamocredito->idestadoaprobacion==2)
                CRÉDITO RECHAZADO
                @elseif( $prestamocredito->idestadoaprobacion==3)
                CRÉDITO DENEGADO
                @endif
              </td>
              <td class="tabla_titulo">FECHA</td>
              <td>
                @if( $prestamocredito->idestadoaprobacion==1)
                {{date_format(date_create($prestamocredito->fechaaprobado),"d/m/Y h:i A")}}
                @elseif( $prestamocredito->idestadoaprobacion==2)
                {{date_format(date_create($prestamocredito->fecharechazado),"d/m/Y h:i A")}}
                @elseif( $prestamocredito->idestadoaprobacion==3)
                {{date_format(date_create($prestamocredito->fechadenegado),"d/m/Y h:i A")}}
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
              <td style="text-align:center;">{{$prestamocredito->monto}}</td>
              <td style="text-align:center;">{{$prestamocredito->tasa}}</td>
              <td style="text-align:center;">{{$prestamocredito->cuota}} CUOTAS</td>
              <td style="text-align:center;">{{$prestamocredito->numerocuota}}</td>
              <td style="text-align:center;">{{date_format(date_create($prestamocredito->fechainicio),"d/m/Y")}}</td>
              <td style="text-align:center;">{{$prestamocredito->frecuencia_nombre}}</td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="2">COMENTARIOS/OBSERVACIONES</td>
              <td colspan="4">{{$prestamocredito->comentariosupervisor}}</td>
            </tr>
          </tbody>
        </table>
    </div>
</body>
</html>