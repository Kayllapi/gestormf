-- Tarifario y productos de credito POR AGENCIA.
--
-- Estas dos tablas eran globales: no tenian columna de agencia, asi que una
-- agencia editaba el tarifario o un producto y el cambio afectaba a TODAS las
-- agencias (Ciudad U, SAÑOS y CHUPACA) sin que se notase.
--
-- Se les agrega idtienda y se replica la configuracion actual en cada agencia
-- para que ninguna pierda lo que ya tenia cargado. A partir de aqui cada
-- agencia mantiene su propia copia y edita solo la suya.
--
-- IMPORTANTE: forma_credito y forma_pago_credito SIGUEN siendo globales a
-- proposito. Sus ids estan fijados por codigo:
--   - forma_credito: 1 = Prendario, 2 = No Prendario, escrito literal en
--     CreditoPrendatarioController (insert y update).
--   - forma_pago_credito: 1..4 = Diario/Semanal/Quincenal/Mensual, usados como
--     indice en $frecuenciaDiasMap para convertir frecuencia a dias en el
--     calculo de interes y comision (FunctionsPrestamo, CreditoController,
--     CreditoController1, CalculoSimple, CalculoCompuesto, Refinanciamiento,
--     Cobranzacuota y sus blades).
-- Duplicarlas por agencia cambiaria esos ids y dejaria el calculo financiero
-- dividiendo entre null en las agencias nuevas.
--
-- Los 163 creditos historicos NO se reasignan: siguen apuntando a su producto
-- y a su forma de pago, que ahora son los ids globales. Por eso el tarifario y
-- el catalogo de productos SI se filtran por idtienda, pero los JOIN de solo
-- lectura que existen para resolver el nombre del producto de un credito ya
-- emitido NO se filtran: esos creditos apuntan a la copia de su agencia y la
-- consulta ya resuelve por id, sin ambiguedad.

ALTER TABLE `credito_prendatario`
  ADD COLUMN `idtienda` INT NOT NULL DEFAULT 0 AFTER `id`,
  ADD KEY `idx_credito_prendatario_agencia` (`idtienda`,`idforma_credito`,`estado`);

ALTER TABLE `tarifario`
  ADD COLUMN `idtienda` INT NOT NULL DEFAULT 0 AFTER `id`,
  ADD KEY `idx_tarifario_agencia` (`idtienda`,`idcredito_prendatario`,`idforma_pago_credito`);

-- Las filas actuales son de la agencia 194 (Ciudad U), que es la que esta en
-- uso. Se les asigna ese idtienda para que sigan siendo suyas.
UPDATE `credito_prendatario` SET `idtienda` = 194 WHERE `idtienda` = 0;
UPDATE `tarifario`          SET `idtienda` = 194 WHERE `idtienda` = 0;

-- Replica productos y tarifario para las otras agencias activas.
-- Los ids de forma_credito y forma_pago_credito NO se tocan: son globales.
-- El codigo se regenera con el id nuevo para que siga siendo unico.
INSERT INTO `credito_prendatario`
  (`idtienda`,`codigo`,`nombre`,`idtipo_credito`,`modalidad`,`garantiaprendatario`,`estado`,`conevaluacion`,`idforma_credito`)
SELECT 198, CONCAT(SUBSTRING(`codigo`,1,2), LPAD(`id` + 1000,4,'0')), `nombre`, `idtipo_credito`, `modalidad`, `garantiaprendatario`, `estado`, `conevaluacion`, `idforma_credito`
FROM `credito_prendatario` WHERE `idtienda` = 194;

INSERT INTO `credito_prendatario`
  (`idtienda`,`codigo`,`nombre`,`idtipo_credito`,`modalidad`,`garantiaprendatario`,`estado`,`conevaluacion`,`idforma_credito`)
SELECT 199, CONCAT(SUBSTRING(`codigo`,1,2), LPAD(`id` + 2000,4,'0')), `nombre`, `idtipo_credito`, `modalidad`, `garantiaprendatario`, `estado`, `conevaluacion`, `idforma_credito`
FROM `credito_prendatario` WHERE `idtienda` = 194;

-- Cada tarifario copiado debe apuntar al producto de SU agencia, no al de la 194.
-- Se resuelve con el codigo: la copia de la 198 es CP/CO + (id+1000) y la de la
-- 199 es CP/CO + (id+2000), sobre el id original del producto.
INSERT INTO `tarifario`
  (`idtienda`,`codigo`,`idforma_credito`,`idcredito_prendatario`,`idforma_pago_credito`,`monto`,`cuotas`,`tem`,`cargos_otros`)
SELECT 198,
       CONCAT('T', LPAD(`t`.`id` + 1000,4,'0')),
       `t`.`idforma_credito`,
       `p`.`id`,
       `t`.`idforma_pago_credito`,
       `t`.`monto`, `t`.`cuotas`, `t`.`tem`, `t`.`cargos_otros`
FROM `tarifario` `t`
JOIN `credito_prendatario` `o` ON `o`.`id` = `t`.`idcredito_prendatario` AND `o`.`idtienda` = 194
JOIN `credito_prendatario` `p` ON `p`.`idtienda` = 198
                          AND `p`.`codigo` = CONCAT(SUBSTRING(`o`.`codigo`,1,2), LPAD(`o`.`id` + 1000,4,'0'))
WHERE `t`.`idtienda` = 194;

INSERT INTO `tarifario`
  (`idtienda`,`codigo`,`idforma_credito`,`idcredito_prendatario`,`idforma_pago_credito`,`monto`,`cuotas`,`tem`,`cargos_otros`)
SELECT 199,
       CONCAT('T', LPAD(`t`.`id` + 2000,4,'0')),
       `t`.`idforma_credito`,
       `p`.`id`,
       `t`.`idforma_pago_credito`,
       `t`.`monto`, `t`.`cuotas`, `t`.`tem`, `t`.`cargos_otros`
FROM `tarifario` `t`
JOIN `credito_prendatario` `o` ON `o`.`id` = `t`.`idcredito_prendatario` AND `o`.`idtienda` = 194
JOIN `credito_prendatario` `p` ON `p`.`idtienda` = 199
                          AND `p`.`codigo` = CONCAT(SUBSTRING(`o`.`codigo`,1,2), LPAD(`o`.`id` + 2000,4,'0'))
WHERE `t`.`idtienda` = 194;

-- Verificacion: deben quedar 12 productos y 103 tarifarios por agencia.
--   SELECT idtienda, COUNT(*) FROM credito_prendatario GROUP BY idtienda;
--   SELECT idtienda, COUNT(*) FROM tarifario          GROUP BY idtienda;
