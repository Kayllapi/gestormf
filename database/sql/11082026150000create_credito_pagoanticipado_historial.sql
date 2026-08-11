-- Tabla: credito_pagoanticipado_historial
-- Guarda una foto de las condiciones del credito (tasa, cuotas, montos, fechas)
-- justo antes de que un "Pago Anticipado - Variante 2" las sobreescriba al generar
-- el nuevo cronograma para el saldo restante, sobre el MISMO idcredito/cuenta.

CREATE TABLE `credito_pagoanticipado_historial` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fecharegistro` DATETIME NOT NULL,

  -- condiciones del credito ANTES de este pago anticipado (foto historica)
  `monto_solicitado` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `saldo_pendientepago` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_pendientepago` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `cuotas` INT NOT NULL DEFAULT 0,
  `dia_gracia` INT NOT NULL DEFAULT 0,
  `tasa_tem` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `tasa_tem_minima` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `tasa_tip` DECIMAL(10,8) NOT NULL DEFAULT 0.00000000,
  `tasa_tcem` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `comision` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `cargo` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `cuota_pago` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `cuota_comision` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `cuota_cargo` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `cuota_comisioncargo` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_comision` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_cargo` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_comisioncargo` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `interes_total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_pagar` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_propuesta` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `fecha_primerpago` DATE DEFAULT NULL,
  `fecha_ultimopago` DATE DEFAULT NULL,

  -- datos del "corte": lo que paso a formar el nuevo cronograma
  `saldo_capital_trasladado` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `numerocuota_ultima_anterior` INT NOT NULL DEFAULT 0,
  `numerocuota_desde_nuevo` INT NOT NULL DEFAULT 0,

  `idcredito` INT NOT NULL,
  `idcredito_cobranzacuota` INT NOT NULL DEFAULT 0,
  `idresponsable` BIGINT UNSIGNED NOT NULL,
  `idtienda` INT UNSIGNED NOT NULL,
  `idestado` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idcredito` (`idcredito`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
