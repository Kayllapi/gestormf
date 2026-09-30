-- Registra cuándo se asignó el precio de liquidación de la garantía.
-- Se usa en Garantías > Remate de Agencia > Liquidación para limpiar los precios
-- que quedaron registrados en un día anterior sin haber generado la ficha,
-- obligando a registrar el precio y generar la ficha el mismo día.

ALTER TABLE `credito_garantia`
  ADD COLUMN `fechaprecioliquidacion` DATETIME NULL DEFAULT NULL
  AFTER `precioliquidacion`;
