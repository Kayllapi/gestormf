-- Bancos, Depositarios y Representante Comun POR AGENCIA.
--
-- Estas tres tablas eran globales: no tenian columna de agencia, asi que un banco,
-- un depositario o un representante comun se editaba desde cualquier agencia y el
-- cambio afectaba a las tres a la vez (Ciudad U, SAÑOS y CHUPACA).
--
-- Se les agrega idtienda y se replica la configuracion actual en cada agencia para
-- que ninguna pierda lo que ya tiene cargado.
--
-- IMPORTANTE: banco esta referenciada por transacciones financieras ya emitidas
-- (credito_formapago.idbanco, credito_cobranzacuota.idbanco,
-- movimientointernodinero.idbanco, asignacioncapital.idbanco,
-- gastoadministrativooperativo.idbanco, ingresoextraordinario.idbanco y sus
-- equivalentes en Sistema1). Un banco NUNCA se borra por esa razon: la pantalla
-- solo permite cambiar su estado.
--
-- credito_gestiondepositario y credito_representantecomun no tienen referencias
-- desde otras tablas, pero su guardado en GestionDepositarioController borra y
-- vuelve a insertar toda la tabla, asi que los ids se regeneran en cada guardado.
--
-- OJO: users.idresponsable_depositario NO apunta a credito_gestiondepositario a
-- pesar del nombre: sus valores son ids de USUARIO (6277, 6297, 6290). No se
-- toca esa columna.
--
-- credito_representantecomun_prestamo es otra cosa (representante por prestamo, con
-- id_credito) y no se administra desde esta pantalla: queda intacta.

ALTER TABLE `banco`
  ADD COLUMN `idtienda` INT NOT NULL DEFAULT 0 AFTER `id`,
  ADD KEY `idx_banco_agencia` (`idtienda`,`nombre`);

ALTER TABLE `credito_gestiondepositario`
  ADD COLUMN `idtienda` INT NOT NULL DEFAULT 0 AFTER `id`,
  ADD KEY `idx_gestiondepositario_agencia` (`idtienda`,`constituciongarantia_id`);

ALTER TABLE `credito_representantecomun`
  ADD COLUMN `idtienda` INT NOT NULL DEFAULT 0 AFTER `id`,
  ADD KEY `idx_representantecomun_agencia` (`idtienda`,`nombre`);

-- Las filas actuales son de la agencia 194 (Ciudad U), que es la que esta en uso.
UPDATE `banco`                        SET `idtienda` = 194 WHERE `idtienda` = 0;
UPDATE `credito_gestiondepositario`   SET `idtienda` = 194 WHERE `idtienda` = 0;
UPDATE `credito_representantecomun`   SET `idtienda` = 194 WHERE `idtienda` = 0;

-- Replica para las otras dos agencias activas.
INSERT INTO `banco` (`idtienda`,`nombre`,`cuenta`,`estado`)
SELECT 198, `nombre`, `cuenta`, `estado` FROM `banco` WHERE `idtienda` = 194;
INSERT INTO `banco` (`idtienda`,`nombre`,`cuenta`,`estado`)
SELECT 199, `nombre`, `cuenta`, `estado` FROM `banco` WHERE `idtienda` = 194;

-- Se replica fecharegistro tal cual: es la fecha en que se registro el dato, no la
-- de la replica.
INSERT INTO `credito_gestiondepositario`
  (`idtienda`,`fecharegistro`,`custodiagarantia_id`,`custodiagarantia_nombre`,`nombre`,`doeruc`,`direccion`,`representante_doeruc`,`representante_nombre`,`estado_id`,`estado_nombre`,`constituciongarantia_id`,`constituciongarantia_nombre`)
SELECT 198, `fecharegistro`,`custodiagarantia_id`,`custodiagarantia_nombre`,`nombre`,`doeruc`,`direccion`,`representante_doeruc`,`representante_nombre`,`estado_id`,`estado_nombre`,`constituciongarantia_id`,`constituciongarantia_nombre`
FROM `credito_gestiondepositario` WHERE `idtienda` = 194;

INSERT INTO `credito_gestiondepositario`
  (`idtienda`,`fecharegistro`,`custodiagarantia_id`,`custodiagarantia_nombre`,`nombre`,`doeruc`,`direccion`,`representante_doeruc`,`representante_nombre`,`estado_id`,`estado_nombre`,`constituciongarantia_id`,`constituciongarantia_nombre`)
SELECT 199, `fecharegistro`,`custodiagarantia_id`,`custodiagarantia_nombre`,`nombre`,`doeruc`,`direccion`,`representante_doeruc`,`representante_nombre`,`estado_id`,`estado_nombre`,`constituciongarantia_id`,`constituciongarantia_nombre`
FROM `credito_gestiondepositario` WHERE `idtienda` = 194;

INSERT INTO `credito_representantecomun`
  (`idtienda`,`fecharegistro`,`nombre`,`doi`,`direccion`,`ubigeo_id`,`ubigeo_nombre`,`estado_id`,`estado_nombre`)
SELECT 198, `fecharegistro`,`nombre`,`doi`,`direccion`,`ubigeo_id`,`ubigeo_nombre`,`estado_id`,`estado_nombre`
FROM `credito_representantecomun` WHERE `idtienda` = 194;

INSERT INTO `credito_representantecomun`
  (`idtienda`,`fecharegistro`,`nombre`,`doi`,`direccion`,`ubigeo_id`,`ubigeo_nombre`,`estado_id`,`estado_nombre`)
SELECT 199, `fecharegistro`,`nombre`,`doi`,`direccion`,`ubigeo_id`,`ubigeo_nombre`,`estado_id`,`estado_nombre`
FROM `credito_representantecomun` WHERE `idtienda` = 194;

-- Verificacion: 4 bancos, 5 depositarios y 2 representantes por agencia.
--   SELECT idtienda, COUNT(*) FROM banco                      GROUP BY idtienda;
--   SELECT idtienda, COUNT(*) FROM credito_gestiondepositario GROUP BY idtienda;
--   SELECT idtienda, COUNT(*) FROM credito_representantecomun GROUP BY idtienda;
