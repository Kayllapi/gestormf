-- Giro Economico POR AGENCIA.
--
-- giro_economico_evaluacion no tenia columna de agencia, asi que el catalogo de
-- giros y su margen de venta maximo era UNO SOLO para las tres agencias: una
-- agencia cambiaba el margen y les cambiaba el dato a las demas sin que se notase.
--
-- Se le agrega idtienda y se replica el catalogo actual en cada agencia para que
-- ninguna pierda lo que ya tiene cargado. A partir de aqui cada agencia mantiene
-- el suyo.
--
-- IMPORTANTE: tipo_giro_economico (Comercio / Servicio / Produccion) SIGUE siendo
-- GLOBAL a proposito. Son 3 filas fijas que clasifican el giro y se referencian por
-- id desde el flujo de evaluacion del credito (credito_evaluacion_cualitativa,
-- credito_evaluacion_resumida y credito_cuantitativa_ingreso_adicional). Duplicar
-- ese catalogo obligaria a reasignar esas referencias y a cambiar los desplegables
-- del credito sin ganar nada.
--
-- Los 40 creditos que ya tienen un giro elegido (5 en evaluacion_cualitativa, 30
-- en evaluacion_resumida y 5 en ingresos adicionales) NO se reasignan: siguen
-- apuntando a su giro, que ahora es el de su agencia. Por eso los JOIN de solo
-- lectura que resuelven el nombre del giro de un credito ya emitido NO se
-- filtran por idtienda: la consulta ya resuelve por id y no hay ambiguedad.
-- Un giro NUNCA se borra por esa misma razon.

ALTER TABLE `giro_economico_evaluacion`
  ADD COLUMN `idtienda` INT NOT NULL DEFAULT 0 AFTER `id`,
  ADD KEY `idx_giro_economico_evaluacion_agencia` (`idtienda`,`idtipo_giro_economico`,`estado`),
  ADD KEY `idx_giro_economico_evaluacion_tipo_nombre` (`idtienda`,`idtipo_giro_economico`,`nombre`);

-- Las filas actuales son de la agencia 194 (Ciudad U), que es la que esta en uso.
UPDATE `giro_economico_evaluacion` SET `idtienda` = 194 WHERE `idtienda` = 0;

-- Replica el catalogo para las otras dos agencias activas.
-- idtipo_giro_economico NO se toca: apunta al catalogo global.
INSERT INTO `giro_economico_evaluacion`
  (`idtienda`,`idtipo_giro_economico`,`nombre`,`porcentaje`,`estado`)
SELECT 198, `idtipo_giro_economico`, `nombre`, `porcentaje`, `estado`
FROM `giro_economico_evaluacion` WHERE `idtienda` = 194;

INSERT INTO `giro_economico_evaluacion`
  (`idtienda`,`idtipo_giro_economico`,`nombre`,`porcentaje`,`estado`)
SELECT 199, `idtipo_giro_economico`, `nombre`, `porcentaje`, `estado`
FROM `giro_economico_evaluacion` WHERE `idtienda` = 194;

-- Verificacion: deben quedar 18 giros por agencia.
--   SELECT idtienda, COUNT(*) FROM giro_economico_evaluacion GROUP BY idtienda;
