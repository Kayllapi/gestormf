-- Registra el usuario (sesión iniciada) que ejecuta el extorno de un pago en Pago de Préstamo.
-- idresponsableextorno sigue guardando el responsable que aprueba el extorno con su contraseña.

ALTER TABLE `credito_cobranzacuota`
  ADD COLUMN `idusuarioextorno` INT NOT NULL DEFAULT 0 AFTER `idresponsableextorno`;
