<!DOCTYPE html>
<html>
<head>
    <title>CRONOGRAMA DE RECAUDACIÓN</title>
    @include('app.pdf_style',['idtienda'=>$tienda->id])
</head>
<body>
    @include('app.pdf_headerfooter',[
        'logo'=>$prestamoahorro->facturacion_agencialogo,
        'nombrecomercial'=>$prestamoahorro->facturacion_agencianombrecomercial,
        'ruc'=>$prestamoahorro->facturacion_agenciaruc,
        'direccion'=>$prestamoahorro->facturacion_agenciadireccion,
        'ubigeo'=>$prestamoahorro->facturacion_agenciaubigeonombre,
        'tienda'=>$tienda,
    ])

    <div class="content_pdf">
      <?php
  
  $documento = str_replace('[agencia_nombrecomercial]', $prestamoahorro->facturacion_agencianombrecomercial, $prestamodocumento->contenido);
  $documento = str_replace('[agencia_razonsocial]', $prestamoahorro->facturacion_agenciarazonsocial, $documento);
  $documento = str_replace('[agencia_ruc]', $prestamoahorro->facturacion_agenciaruc, $documento);
  $documento = str_replace('[agencia_direccion]', $prestamoahorro->facturacion_agenciadireccion, $documento);
  $documento = str_replace('[agencia_ubigeo]', $prestamoahorro->facturacion_agenciaubigeonombre, $documento);
  $documento = str_replace('[agencia_representante_dni]', $prestamoahorro->facturacion_representante_dni, $documento);
  $documento = str_replace('[agencia_representante_nombre]', $prestamoahorro->facturacion_representante_nombre, $documento);
  $documento = str_replace('[agencia_representante_apellidos]', $prestamoahorro->facturacion_representante_apellidos, $documento);
  $documento = str_replace('[agencia_representante_cargo]', $prestamoahorro->facturacion_representante_cargo, $documento);
      
  $documento = str_replace('[cliente_dni]', $prestamoahorro->facturacion_cliente_identificacion, $documento);
  $documento = str_replace('[cliente_nombre]', $prestamoahorro->facturacion_cliente_nombre, $documento);
  $documento = str_replace('[cliente_apellidos]', $prestamoahorro->facturacion_cliente_apellidos, $documento);
  $documento = str_replace('[cliente_direccion]', $prestamoahorro->facturacion_cliente_direccion, $documento);
  $documento = str_replace('[cliente_ubigeo]', $prestamoahorro->facturacion_cliente_ubigeonombre, $documento);
  $documento = str_replace('[cliente_estadocivil]', $prestamoahorro->cliente_estadocivil, $documento);
      
  $documento = str_replace('[documento_pagare_correlativo]', str_pad($prestamoahorro->codigo, 8, "0", STR_PAD_LEFT), $documento);
  $documento = str_replace('[documento_fechaactual]', Carbon\Carbon::now()->format("m/d/Y"), $documento);
  $documento = str_replace('[documento_horaactual]', Carbon\Carbon::now()->format("h:i:s A"), $documento);

  $documento = str_replace('[conyugue_dni]', $prestamoahorro->conyugeidentificacion, $documento);
  $documento = str_replace('[conyugue_nombre]', $prestamoahorro->conyugenombre, $documento);
  $documento = str_replace('[conyugue_apellidos]', $prestamoahorro->conyugeapellidos, $documento);
  $documento = str_replace('[conyugue_direccion]', $prestamoahorro->conyugedireccion, $documento);
  $documento = str_replace('[conyugue_ubigeo]', $prestamoahorro->conyugeubigeonombre, $documento);
      
  $documento = str_replace('[beneficiario_dni]', $prestamoahorro->beneficiarioidentificacion, $documento);
  $documento = str_replace('[beneficiario_nombre]', $prestamoahorro->beneficiarionombre, $documento);
  $documento = str_replace('[beneficiario_apellidos]', $prestamoahorro->beneficiarioapellidos, $documento);
  $documento = str_replace('[beneficiario_direccion]', $prestamoahorro->beneficiariodireccion, $documento);
  $documento = str_replace('[beneficiario_ubigeo]', $prestamoahorro->beneficiarioubigeonombre, $documento);
      
  /*$documento = str_replace('[ahorro_fechaconfirmacion]', $ahorro_fechaconfirmacion, $documento);
  $documento = str_replace('[ahorro_ultimafechapago]', $ahorro_ultimacuota, $documento);
  $documento = str_replace('[ahorro_monto]', $prestamoahorro->monedasimbolo.' '.$prestamoahorro->monto, $documento);
  $documento = str_replace('[ahorro_tasaahorro]', $prestamoahorro->tasa, $documento);
  $documento = str_replace('[ahorro_numerocuota]', $prestamoahorro->numerocuota, $documento);
  $documento = str_replace('[ahorro_cuota]', $prestamoahorro->monedasimbolo.' '.$prestamoahorro->cuota, $documento);
  $documento = str_replace('[ahorro_totalapagar]', $prestamoahorro->monedasimbolo.' '.$prestamoahorro->total_cuota, $documento);
  $documento = str_replace('[ahorro_frecuencia]', $prestamoahorro->frecuencia_nombre, $documento);
  $documento = str_replace('[ahorro_garantias]', $ahorro_garantias, $documento);
  $documento = str_replace('[ahorro_garantias_total]', $prestamoahorro->monedasimbolo.' '.number_format($ahorro_garantias_total, 2, '.', ''), $documento);
  $documento = str_replace('[ahorro_monedasimbolo]', $prestamoahorro->monedasimbolo, $documento);*/

  echo $documento;
?>
    </div>
   
   
</body>
</html>