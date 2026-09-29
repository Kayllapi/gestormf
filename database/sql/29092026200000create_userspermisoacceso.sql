-- Permisos por usuario (override del cargo).
-- Permite habilitar o deshabilitar un modulo especifico para un usuario, sin tocar
-- el cargo (permiso/permisoacceso) que tiene asignado.
--
-- idestado: 1 = habilitado para el usuario, 2 = deshabilitado para el usuario.
-- Si el usuario NO tiene registros en esta tabla, se respeta lo configurado en el
-- cargo (permisoacceso). Por eso solo se guardan aqui las diferencias con el cargo.
--
-- El ambito es (idusers, idtienda, idpermiso): el usuario puede tener el mismo cargo
-- en varias agencias y cada una puede tener un permiso distinto.

CREATE TABLE IF NOT EXISTS `userspermisoacceso` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idusers` int NOT NULL,
  `idtienda` int NOT NULL,
  `idpermiso` int NOT NULL,
  `idmodulo` int NOT NULL,
  `idestado` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_userspermisoacceso_acceso` (`idusers`,`idtienda`,`idpermiso`),
  KEY `idx_userspermisoacceso_modulo` (`idmodulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
