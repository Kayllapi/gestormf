<!DOCTYPE html>
<html>
<head>
    <title>ANÁLISIS CUALITATIVO</title>
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
    <div class="titulo">ANÁLISIS CUALITATIVO</div>
    <div class="content">
        <table class="tabla_informativa">
            <tr>
                <td style="width:7%;">CLIENTE</td>
                <td style="width:1%;">:</td>
                <td style="width:62%;">{{$prestamocredito->clienteidentificacion}} - {{$prestamocredito->clienteapellidos}}, {{$prestamocredito->clientenombre}}</td>
                <td style="width:7%;">FECHA</td>
                <td style="width:1%;">:</td>
                <td style="width:22%;">{{ date_format(date_create($prestamocredito->fecharegistro), "d/m/Y") }}</td>
            </tr>
        </table>
        <div class="espacio"></div>
        <table class="tabla">
            <tr class="tabla_cabera">
              <td style="width:30px;text-align: center;">N°</td>
              <td>PREGUNTA</td>
              <td>RESPUESTA</td>
              <td style="width:40px;text-align: center;">VALOR</td>
            </tr>

            <?php 
          
            $laboradesde = Carbon\Carbon::now()->format('d-m-Y');
            $estabilidaddomiciliaria = 0;
            $referenciascrediticia = 0;
            $licenciafuncionamiento = '';
            $contratoalquiler = '';
            $ficharuc = '';
            $boletacompra = '';
            $experienciacredito = '';
            $pagoservicio = '';
            $endeudamiento = '';
            $invetario = '';
            $garantia = '';
            if($sustento!=''){
                $referenciascrediticia = $sustento->idprestamo_calificacion;
                $experienciacredito = $sustento->idprestamo_experienciacredito;
                $endeudamiento = $sustento->idprestamo_endeudamientosistema;
                $invetario = $sustento->idprestamo_inventario;
            }
            if($prestamolaboral!=''){
                $laboradesde = '01-'.$prestamolaboral->labora_desdemes.'-'.$prestamolaboral->labora_desdeanio;
                $licenciafuncionamiento = $prestamolaboral->estadolicenciafuncionamiento;
                $contratoalquiler = $prestamolaboral->estadocontratoalquiler;
                $ficharuc = $prestamolaboral->estadoficharuc;
                $boletacompra = $prestamolaboral->estadoboletacompra;
            }
            if($prestamodomicilio!=''){
                $estabilidaddomiciliaria = $prestamodomicilio->idtipopropiedad;
                $pagoservicio = $prestamodomicilio->iddeudapagoservicio;
            }
            if($prestamobien!=''){
                $garantia = $prestamobien->idprestamo_documento;
            }
          
            $valor = 0; 

            
            $dia_actual = date("Y-m-d");
            $edad_diff = date_diff(date_create($laboradesde), date_create($dia_actual));
            $antiguedadnegocio = ($edad_diff->format('%y')*12)+$edad_diff->format('%m');
            ?>
            <tr>
              <td style="text-align: center;"><b>1</b></td>
              <td>ANTIGÜEDAD DE NEGOCIO</td>
              <td>
                  <?php $numvalor = 0; ?>
                  @if($antiguedadnegocio<6)
                      MENOR DE 6 MESES ({{$edad_diff->format('%m')}} MESES)
                      <?php $valor = $valor+1; ?>  
                      <?php $numvalor = 1; ?>                      
                  @elseif($antiguedadnegocio<24)
                      MENOR A 2 AÑOS ({{$edad_diff->format('%y').' AÑOS Y '.$edad_diff->format('%m')}} MESES)
                      <?php $valor = $valor+2; ?> 
                      <?php $numvalor = 2; ?>     
                  @elseif($antiguedadnegocio>24)
                      MAYOR A 2 AÑOS ({{$edad_diff->format('%y').' AÑOS Y '.$edad_diff->format('%m')}} MESES)
                      <?php $valor = $valor+3; ?> 
                      <?php $numvalor = 3; ?>   
                  @else
                      ---
                  @endif
              </td>
              <td style="text-align: center;">{{$numvalor}}</td>
            </tr>
            <tr>
              <td style="text-align: center;"><b>2</b></td>
              <td>ESTABILIDAD DOMICILIARIA</td>
              <td>
                  <?php $numvalor = 0; ?>
                  @if($estabilidaddomiciliaria==1)
                      ALQUILADO
                      <?php $valor = $valor+1; ?>  
                      <?php $numvalor = 1; ?>       
                  @elseif($estabilidaddomiciliaria==2)
                      FAMILIAR
                      <?php $valor = $valor+2; ?>  
                      <?php $numvalor = 2; ?>       
                  @elseif($estabilidaddomiciliaria==3)
                      PROPIO
                      <?php $valor = $valor+3; ?>   
                      <?php $numvalor = 3; ?>      
                  @else
                      ---
                  @endif
              </td>
              <td style="text-align: center;">{{$numvalor}}</td>
            </tr>
            <tr>
              <td style="text-align: center;"><b>3</b></td>
              <td>REFERENCIAS CREDITICIAS</td>
              <td>
                  <?php $numvalor = 0; ?>
                  @if($referenciascrediticia==1)
                      HISTORIAL NORMAL
                      <?php $valor = $valor+1; ?>   
                      <?php $numvalor = 1; ?>   
                  @elseif($referenciascrediticia==2 or $referenciascrediticia==3 or $referenciascrediticia==4)
                      HISTORIAL HASTA CPP
                      <?php $valor = $valor+2; ?>   
                      <?php $numvalor = 2; ?>   
                  @elseif($referenciascrediticia==5)
                      HISTORIAL PERDIDA
                      <?php $valor = $valor+3; ?>  
                      <?php $numvalor = 3; ?>    
                  @else
                      ---
                  @endif
              </td>
              <td style="text-align: center;">{{$numvalor}}</td>
            </tr>
            <tr>
              <td style="text-align: center;"><b>4</b></td>
              <td>LICENCIA DE FUNCIONAMIENTO Y RUC</td>
              <td>
                  <?php $numvalor = 0; ?>
                  @if($licenciafuncionamiento=='' && $contratoalquiler=='' && $boletacompra=='' && $ficharuc=='')
                      SIN DOCUMENTOS
                      <?php $valor = $valor+1; ?>  
                      <?php $numvalor = 1; ?>    
                  @elseif($contratoalquiler=='on' && $boletacompra=='on')
                      CONTRATO, BOLETAS DE COMPRA
                      <?php $valor = $valor+2; ?>   
                      <?php $numvalor = 2; ?>   
                  @elseif($licenciafuncionamiento=='on' && $contratoalquiler=='on' && $boletacompra=='on' && $ficharuc=='on')
                      CONTRATO, LICENCIA, RUC Y BOLETAS DE COMPRA
                      <?php $valor = $valor+3; ?>   
                      <?php $numvalor = 3; ?>   
                  @else
                      <?php 
                      $documentos = '';
                      if($licenciafuncionamiento=='on'){
                          $documentos = $documentos.'LICENCIA';
                      } 
                      if($contratoalquiler=='on'){
                          $coma = '';
                          if($documentos!=''){
                              $coma = ', ';
                          }
                          $documentos = $documentos.$coma.'CONTRATO';
                      } 
                      if($ficharuc=='on'){
                          $coma = '';
                          if($documentos!=''){
                              $coma = ', ';
                          }
                          $documentos = $documentos.$coma.'RUC ';
                      } 
                      if($boletacompra=='on'){
                          $coma = '';
                          if($documentos!=''){
                              $coma = ', ';
                          }
                          $documentos = $documentos.$coma.'BOLETAS DE COMPRA ';
                      } 
                      if($documentos!=''){
                          $valor = $valor+1;
                          $numvalor = 1;
                      }else{
                          $documentos = '---';
                      }
                      ?>
                      {{$documentos}}   
                  @endif
              </td>
              <td style="text-align: center;">{{$numvalor}}</td>
            </tr>
            <tr>
              <td style="text-align: center;"><b>5</b></td>
              <td>EXPERIENCIA EN CREDITOS DIARIOS/SEMANALES</td>
              <td>
                  <?php $numvalor = 0; ?>
                  @if($experienciacredito==1)
                      MAYOR A 1 TARJETA
                      <?php $valor = $valor+1; ?>   
                      <?php $numvalor = 1; ?>   
                  @elseif($experienciacredito==2)
                      IGUAL A 1 TARJETA
                      <?php $valor = $valor+2; ?>  
                      <?php $numvalor = 2; ?>    
                  @elseif($experienciacredito==3)
                      NINGUNA TARJETA
                      <?php $valor = $valor+3; ?>   
                      <?php $numvalor = 3; ?>   
                  @else
                      ---
                  @endif
              </td>
              <td style="text-align: center;">{{$numvalor}}</td>
            </tr>
            <tr>
              <td style="text-align: center;"><b>6</b></td>
              <td>PAGO DE SERVICIOS</td>
              <td>
                  <?php $numvalor = 0; ?>
                  @if($pagoservicio==1)
                      CON CORTE
                      <?php $valor = $valor+1; ?>  
                      <?php $numvalor = 1; ?>    
                  @elseif($pagoservicio==2)
                      DEUDA POR 2 MESES
                      <?php $valor = $valor+2; ?>   
                      <?php $numvalor = 2; ?>   
                  @elseif($pagoservicio==3)
                      DEUDA POR 1 MES
                      <?php $valor = $valor+3; ?>   
                      <?php $numvalor = 3; ?>   
                  @else
                      ---
                  @endif
              </td>
              <td style="text-align: center;">{{$numvalor}}</td>
            </tr>
            <tr>
              <td style="text-align: center;"><b>7</b></td>
              <td>ENDEUDAMIENTO EN EL SISTEMA FINANCIERO (ULTIMO 6 MESES)</td>
              <td>
                  <?php $numvalor = 0; ?>
                  @if($endeudamiento==1)
                      AUMENTO DE DEUDA
                      <?php $valor = $valor+1; ?>   
                      <?php $numvalor = 1; ?>   
                  @elseif($endeudamiento==2)
                      DESMINUCIÓN NO SIGNIFICATIVA
                      <?php $valor = $valor+2; ?>   
                      <?php $numvalor = 2; ?>   
                  @elseif($endeudamiento==3) 
                      DISMINUYE DEUDA
                      <?php $valor = $valor+3; ?>  
                      <?php $numvalor = 3; ?>   
                  @else
                      ---
                  @endif
              </td>
              <td style="text-align: center;">{{$numvalor}}</td>
            </tr>
            <tr>
              <td style="text-align: center;"><b>8</b></td>
              <td>INVENTARIO, MUEBLES Y ENSERES</td>
              <td>
                  <?php $numvalor = 0; ?>
                  @if($invetario==1)
                      POCA MERCADERIA
                      <?php $valor = $valor+1; ?>   
                      <?php $numvalor = 1; ?>   
                  @elseif($invetario==2)
                      REGULAR MERCADERIA
                      <?php $valor = $valor+2; ?>  
                      <?php $numvalor = 2; ?>    
                  @elseif($invetario==3)
                      NEGOCIO BIEN IMPLEMENTADO
                      <?php $valor = $valor+3; ?>   
                      <?php $numvalor = 3; ?>   
                  @else
                      ---
                  @endif
              </td>
              <td style="text-align: center;">{{$numvalor}}</td>
            </tr>
            <tr>
              <td style="text-align: center;"><b>9</b></td>
              <td>NÚMERO DE ENTIDADES QUE POSEE CRÉDITOS</td>
              <td>
                  <?php $numvalor = 0; ?>
                  @if($numeroentidades>5)
                      MAYOR A 5 ENTIDADES ({{$numeroentidades}} ENTIDADES)
                      <?php $valor = $valor+1; ?>   
                      <?php $numvalor = 1; ?>   
                  @elseif($numeroentidades==1)
                      HASTA 5 ENTIDADES ({{$numeroentidades}} ENTIDADES)
                      <?php $valor = $valor+2; ?>   
                      <?php $numvalor = 2; ?>   
                  @elseif($numeroentidades<3)
                      MENOR O IGUAL A 3 ENTIDADES ({{$numeroentidades}} ENTIDADES)
                      <?php $valor = $valor+3; ?>   
                      <?php $numvalor = 3; ?>   
                  @else
                      ---
                  @endif
              </td>
              <td style="text-align: center;">{{$numvalor}}</td>
            </tr>
            <tr>
              <td style="text-align: center;"><b>10</b></td>
              <td>GARANTIAS QUE PRESENTA</td>
              <td>
                  <?php $numvalor = 0; ?>
                  @if($garantia==1)
                      SIN DOCUMENTOS
                      <?php $valor = $valor+1; ?>   
                      <?php $numvalor = 1; ?>   
                  @elseif($garantia==2)
                      COPIA/LEGALIZADO
                      <?php $valor = $valor+2; ?>   
                      <?php $numvalor = 2; ?>   
                  @elseif($garantia==3)
                      ORIGINAL
                      <?php $valor = $valor+3; ?>   
                      <?php $numvalor = 3; ?>   
                  @else
                      ---
                  @endif
              </td>
              <td style="text-align: center;">{{$numvalor}}</td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="3" style="text-align: right;">TOTAL</td>
              <td class="tabla_titulo" style="text-align: center;">{{ $valor }}</td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="3" style="text-align: right;">PROMEDIO</td>
              <td class="tabla_titulo" style="text-align: center;">{{ round($valor/3,2) }}</td>
            </tr>
        </table>
        <div class="espacio"></div>
        <table class="tabla">
            <tr class="tabla_cabera">
              <td colspan="8">SUSTENTO DEL ANALISTA</td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="4" style="width:50%;">DESCRIPCIÓN DEL DESTINO DEL CRÉDITO</td>
              <td class="tabla_titulo" colspan="4">RIESGOS QUE PRESENTA EL NEGOCIO</td>
            </tr>
            <tr>
              <td colspan="4" style="height:60px;">{{ $sustento->destinocredito ?? '' }}</td>
              <td colspan="4">{{ $sustento->riesgonegocio ?? '' }}</td>
            </tr>
            <tr>
              <td class="tabla_titulo" colspan="4">¿EL CLIENTE, A QUÉ DESTINA EL INGRESO EXCEDENTE DEL NEGOCIO?</td>
              <td class="tabla_titulo" colspan="4">SUSTENTO DE LA PROPUESTA</td>
            </tr>
            <tr>
              <td colspan="4" style="height:60px;">{{ $sustento->destinoexcendete ?? '' }}</td>
              <td colspan="4">{{ $sustento->sustentopropuesta ?? '' }}</td>
            </tr>
        </table>
        <div class="espacio"></div>
        <table class="tabla">
            <tr class="tabla_cabera">
              <td colspan="5">SOBRE REFERENCIAS</td>
            </tr>
              <tr>
                <td class="tabla_titulo" rowspan="3" style="width:80px">REFERENCIA 1</td>
                <td class="tabla_titulo" style="width:130px">DNI - APELLIDOS, NOMBRE</td>
                <td colspan="3">{{ $referencia1!=''?$referencia1->identificacion_persona:'' }} - {{ $referencia1!=''?$referencia1->completo_persona:'' }}</td>
              </tr>
              <tr>
                <td class="tabla_titulo">TELÉFONO</td>
                <td>{{ $referencia1!=''?$referencia1->numerotelefono:'' }}</td>
                <td class="tabla_titulo" style="width:80px">PARENTESCO</td>
                <td>{{ $referencia1!=''?$referencia1->nombre_tiporelacion:'' }}</td>
              </tr>
              <tr>
                <td class="tabla_titulo">COMENTARIO</td>
                <td colspan="3">{{ $referencia1!=''?$referencia1->comentario:'' }}</td>
              </tr>
              <tr>
                <td class="tabla_titulo" rowspan="3" style="width:80px">REFERENCIA 2</td>
                <td class="tabla_titulo" style="width:130px">DNI - APELLIDOS, NOMBRE</td>
                <td colspan="3">{{ $referencia2!=''?$referencia2->identificacion_persona:'' }} - {{ $referencia2!=''?$referencia2->completo_persona:'' }}</td>
              </tr>
              <tr>
                <td class="tabla_titulo">TELÉFONO</td>
                <td>{{ $referencia2!=''?$referencia2->numerotelefono:'' }}</td>
                <td class="tabla_titulo" style="width:80px">PARENTESCO</td>
                <td>{{ $referencia2!=''?$referencia2->nombre_tiporelacion:'' }}</td>
              </tr>
              <tr>
                <td class="tabla_titulo">COMENTARIO</td>
                <td colspan="3">{{ $referencia2!=''?$referencia2->comentario:'' }}</td>
              </tr>
              <tr>
                <td class="tabla_titulo" rowspan="3" style="width:80px">REFERENCIA 3</td>
                <td class="tabla_titulo" style="width:130px">DNI - APELLIDOS, NOMBRE</td>
                <td colspan="3">{{ $referencia3!=''?$referencia3->identificacion_persona:'' }} - {{ $referencia3!=''?$referencia3->completo_persona:'' }}</td>
              </tr>
              <tr>
                <td class="tabla_titulo">TELÉFONO</td>
                <td>{{ $referencia3!=''?$referencia3->numerotelefono:'' }}</td>
                <td class="tabla_titulo" style="width:80px">PARENTESCO</td>
                <td>{{ $referencia3!=''?$referencia3->nombre_tiporelacion:'' }}</td>
              </tr>
              <tr>
                <td class="tabla_titulo">COMENTARIO</td>
                <td colspan="3">{{ $referencia3!=''?$referencia3->comentario:'' }}</td>
              </tr>
              <tr>
                <td class="tabla_titulo" rowspan="3" style="width:80px">REFERENCIA 4</td>
                <td class="tabla_titulo" style="width:130px">DNI - APELLIDOS, NOMBRE</td>
                <td colspan="3">{{ $referencia4!=''?$referencia4->identificacion_persona:'' }} - {{ $referencia4!=''?$referencia4->completo_persona:'' }}</td>
              </tr>
              <tr>
                <td class="tabla_titulo">TELÉFONO</td>
                <td>{{ $referencia4!=''?$referencia4->numerotelefono:'' }}</td>
                <td class="tabla_titulo" style="width:80px">PARENTESCO</td>
                <td>{{ $referencia4!=''?$referencia4->nombre_tiporelacion:'' }}</td>
              </tr>
              <tr>
                <td class="tabla_titulo">COMENTARIO</td>
                <td colspan="3">{{ $referencia4!=''?$referencia4->comentario:'' }}</td>
              </tr>
              <tr>
                <td class="tabla_titulo" rowspan="3" style="width:80px">REFERENCIA 5</td>
                <td class="tabla_titulo" style="width:130px">DNI - APELLIDOS, NOMBRE</td>
                <td colspan="3">{{ $referencia5!=''?$referencia5->identificacion_persona:'' }} - {{ $referencia5!=''?$referencia5->completo_persona:'' }}</td>
              </tr>
              <tr>
                <td class="tabla_titulo">TELÉFONO</td>
                <td>{{ $referencia5!=''?$referencia5->numerotelefono:'' }}</td>
                <td class="tabla_titulo" style="width:80px">PARENTESCO</td>
                <td>{{ $referencia5!=''?$referencia5->nombre_tiporelacion:'' }}</td>
              </tr>
              <tr>
                <td class="tabla_titulo">COMENTARIO</td>
                <td colspan="3">{{ $referencia5!=''?$referencia5->comentario:'' }}</td>
              </tr>
        </table>
  </div>
</body>
</html>