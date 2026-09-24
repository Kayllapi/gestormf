-- Los gastos administrativos/operativos y los ingresos extraordinarios se registraban sin
-- "fechapago" (dependían del DEFAULT de la columna). Donde la columna no tiene
-- DEFAULT CURRENT_TIMESTAMP quedaron en NULL y no aparecen en sus tablas, que filtran
-- por fechapago. Se completa con la fecha de registro.

-- Diagnóstico (ejecutar antes para confirmar):
-- SHOW COLUMNS FROM `gastoadministrativooperativo` WHERE Field = 'fechapago';
-- SHOW COLUMNS FROM `ingresoextraordinario` WHERE Field = 'fechapago';
-- SELECT id, idtienda, fecharegistro, fechapago FROM `gastoadministrativooperativo` ORDER BY id DESC LIMIT 10;
-- SELECT id, idtienda, fecharegistro, fechapago FROM `ingresoextraordinario` ORDER BY id DESC LIMIT 10;

UPDATE `gastoadministrativooperativo`
  SET `fechapago` = `fecharegistro`
  WHERE `fechapago` IS NULL;

UPDATE `ingresoextraordinario`
  SET `fechapago` = `fecharegistro`
  WHERE `fechapago` IS NULL;
