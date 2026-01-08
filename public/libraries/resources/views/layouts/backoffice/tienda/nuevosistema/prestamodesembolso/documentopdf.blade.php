<?php
  $documento = str_replace('|agencia_nombrecomercial|', $facturacion->agencianombrecomercial, $prestamodocumento->contenido);
  $documento = str_replace('|agencia_representante_nombre|', '', $documento);
  $documento = str_replace('|agencia_representante_apellidos|', '', $documento);
  $documento = str_replace('|agencia_representante_direccion|', '', $documento);
  $documento = str_replace('|agencia_razonsocial|', '', $documento);
  $documento = str_replace('|agencia_ruc|', '', $documento);
  $documento = str_replace('|apellidoscliente|', $prestamodesembolso->cliente_apellido, $documento);
  $documento = str_replace('|nombrecliente|', $prestamodesembolso->cliente, $documento);
  $documento = str_replace('|dnicliente|', $prestamodesembolso->cliente_identificacion, $documento);
  $documento = str_replace('|direccioncliente|', $prestamodesembolso->cliente_direccion, $documento);
  $documento = str_replace('|estadocivilcliente|', '', $documento);
  $documento = str_replace('|apellidosconyugue|', '', $documento);
  $documento = str_replace('|nombreconyugue|', '', $documento);
  $documento = str_replace('|dniconyugue|', '', $documento);
  $documento = str_replace('|dnigarante|', '', $documento);
  $documento = str_replace('|fechadesembolso|', $prestamodesembolso->fechadesembolsado, $documento);
  $documento = str_replace('|montocredito|', $prestamodesembolso->monto, $documento);
  $documento = str_replace('|tasacredito|', $prestamodesembolso->tasa, $documento);
  $documento = str_replace('|numerocuota|', $prestamodesembolso->numerocuota, $documento);
  $documento = str_replace('|montocuota|', $prestamodesembolso->total_cuota, $documento);
  echo $documento;
?>