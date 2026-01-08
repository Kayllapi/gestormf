<!DOCTYPE html>
<html>
<head>
    <title>CRONOGRAMA DE PAGOS</title>
    @include('app.pdf_style',['idtienda'=>$tienda->id])
</head>
<body>
    @include('app.pdf_headerfooter',[
        'logo'=>$prestamodesembolso->facturacion_agencialogo,
        'nombrecomercial'=>$prestamodesembolso->facturacion_agencianombrecomercial,
        'ruc'=>$prestamodesembolso->facturacion_agenciaruc,
        'direccion'=>$prestamodesembolso->facturacion_agenciadireccion,
        'ubigeo'=>$prestamodesembolso->facturacion_agenciaubigeonombre,
        'tienda'=>$tienda,
    ])
    <div class="content_pdf">
      <?php
  
  $documento = str_replace('[agencia_nombrecomercial]', $prestamodesembolso->facturacion_agencianombrecomercial, $prestamodocumento->contenido);
  $documento = str_replace('[agencia_razonsocial]', $prestamodesembolso->facturacion_agenciarazonsocial, $documento);
  $documento = str_replace('[agencia_ruc]', $prestamodesembolso->facturacion_agenciaruc, $documento);
  $documento = str_replace('[agencia_direccion]', $prestamodesembolso->facturacion_agenciadireccion, $documento);
  $documento = str_replace('[agencia_ubigeo]', $prestamodesembolso->facturacion_agenciaubigeonombre, $documento);
  $documento = str_replace('[agencia_representante_dni]', $prestamodesembolso->facturacion_representante_dni, $documento);
  $documento = str_replace('[agencia_representante_nombre]', $prestamodesembolso->facturacion_representante_nombre, $documento);
  $documento = str_replace('[agencia_representante_apellidos]', $prestamodesembolso->facturacion_representante_apellidos, $documento);
  $documento = str_replace('[agencia_representante_cargo]', $prestamodesembolso->facturacion_representante_cargo, $documento);
      
  $documento = str_replace('[cliente_dni]', $prestamodesembolso->facturacion_cliente_identificacion, $documento);
  $documento = str_replace('[cliente_nombre]', $prestamodesembolso->facturacion_cliente_nombre, $documento);
  $documento = str_replace('[cliente_apellidos]', $prestamodesembolso->facturacion_cliente_apellidos, $documento);
  $documento = str_replace('[cliente_direccion]', $prestamodesembolso->facturacion_cliente_direccion, $documento);
  $documento = str_replace('[cliente_ubigeo]', $prestamodesembolso->facturacion_cliente_ubigeonombre, $documento);
  $documento = str_replace('[cliente_estadocivil]', $prestamodesembolso->cliente_estadocivil, $documento);
      
  $documento = str_replace('[documento_pagare_correlativo]', str_pad($prestamodesembolso->codigo, 8, "0", STR_PAD_LEFT), $documento);
  $documento = str_replace('[documento_fechaactual]', Carbon\Carbon::now()->format("m/d/Y"), $documento);
  $documento = str_replace('[documento_horaactual]', Carbon\Carbon::now()->format("h:i:s A"), $documento);

  $documento = str_replace('[conyugue_dni]', $prestamodesembolso->conyugeidentificacion, $documento);
  $documento = str_replace('[conyugue_nombre]', $prestamodesembolso->conyugenombre, $documento);
  $documento = str_replace('[conyugue_apellidos]', $prestamodesembolso->conyugeapellidos, $documento);
  $documento = str_replace('[conyugue_direccion]', $prestamodesembolso->conyugedireccion, $documento);
  $documento = str_replace('[conyugue_ubigeo]', $prestamodesembolso->conyugeubigeonombre, $documento);
      
  $documento = str_replace('[garante_dni]', $prestamodesembolso->garanteidentificacion, $documento);
  $documento = str_replace('[garante_nombre]', $prestamodesembolso->garantenombre, $documento);
  $documento = str_replace('[garante_apellidos]', $prestamodesembolso->garanteapellidos, $documento);
  $documento = str_replace('[garante_direccion]', $prestamodesembolso->garantedireccion, $documento);
  $documento = str_replace('[garante_ubigeo]', $prestamodesembolso->garanteubigeonombre, $documento);
      
  $documento = str_replace('[credito_fechadesembolso]', $credito_fechadesembolso, $documento);
  $documento = str_replace('[credito_ultimafechapago]', $credito_ultimacuota, $documento);
  $documento = str_replace('[credito_monto]', $prestamodesembolso->monedasimbolo.' '.$prestamodesembolso->monto, $documento);
  $documento = str_replace('[credito_tasacredito]', $prestamodesembolso->tasa, $documento);
  $documento = str_replace('[credito_numerocuota]', $prestamodesembolso->numerocuota, $documento);
  $documento = str_replace('[credito_cuota]', $prestamodesembolso->monedasimbolo.' '.$prestamodesembolso->cuota, $documento);
  $documento = str_replace('[credito_totalapagar]', $prestamodesembolso->monedasimbolo.' '.$prestamodesembolso->total_cuota, $documento);
  $documento = str_replace('[credito_frecuencia]', $prestamodesembolso->frecuencia_nombre, $documento);
  $documento = str_replace('[credito_garantias]', $credito_garantias, $documento);
  $documento = str_replace('[credito_garantias_total]', $prestamodesembolso->monedasimbolo.' '.number_format($credito_garantias_total, 2, '.', ''), $documento);
  $documento = str_replace('[credito_monedasimbolo]', $prestamodesembolso->monedasimbolo, $documento);
      
  $documento = str_replace('[credito_negocio_nombre]', $prestamodesembolso->negocio_nombre, $documento);
  $documento = str_replace('[credito_negocio_giro]', $prestamodesembolso->negocio_giro, $documento);
  $documento = str_replace('[credito_negocio_actividad]', $prestamodesembolso->negocio_actividad, $documento);
  $documento = str_replace('[credito_negocio_direccion]', $prestamodesembolso->negocio_direccion, $documento);
  $documento = str_replace('[credito_negocio_ubigeo]', $prestamodesembolso->negocio_ubigeo, $documento);
  echo $documento;
?>
    </div>
   
   
</body>
</html>