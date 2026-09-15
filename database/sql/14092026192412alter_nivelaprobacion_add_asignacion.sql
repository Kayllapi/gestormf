-- Agrega la columna "asignacion" para la nueva columna ASIGNACION en Niveles de
-- Aprobacion, con el mismo formato (JSON de tipo_uno/tipo_dos) que nivelaprobacion,
-- autonomiaadministracion y autonomiagerencia.

ALTER TABLE `nivelaprobacion`
  ADD COLUMN `asignacion` TEXT NOT NULL AFTER `autonomiagerencia`;

UPDATE `nivelaprobacion`
  SET `asignacion` = '[{"tipo_uno":[],"tipo_dos":[]}]'
  WHERE `asignacion` = '';
