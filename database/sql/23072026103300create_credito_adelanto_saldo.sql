-- Tabla: credito_adelanto_saldo
-- Guarda el desglose de saldo (custodia, compensatorio, moratorio, etc.)
-- asociado a un pago a cuenta / adelanto (credito_adelanto).

CREATE TABLE `credito_adelanto_saldo` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fecharegistro` DATETIME NOT NULL,
  `cuota` INT UNSIGNED NOT NULL,
  `capital` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `interes` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `cargo` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `recaudo` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `custodia` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `compensatorio` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `moratorio` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `idcredito_adelanto` INT UNSIGNED NOT NULL,
  `idcredito` INT UNSIGNED NOT NULL,
  `idresponsable` BIGINT UNSIGNED NOT NULL,
  `idtienda` INT UNSIGNED NOT NULL,
  `idestado` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
