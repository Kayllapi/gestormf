-- Penalidades por tipo de garantia, POR AGENCIA.
--
-- La columna tipo_garantia.penalidad (y subtipo_garantia_noprendaria_ii.penalidad) es un
-- unico valor compartido por todas las agencias, asi que al editarla desde la pantalla de
-- Penalidades y Comisiones cambiaba el dato de todas las agencias a la vez sin que se notase
-- en el filtro por agencia.
--
-- Estas dos tablas guardan SOLO la diferencia de cada agencia respecto de ese valor comun:
--
--   - Si la agencia NO tiene fila aqui, se usa tipo_garantia.penalidad (valor comun).
--   - Si tiene fila, se usa ese valor.
--
-- Es un override, no una copia: el catalogo tipo_garantia no se duplica por agencia (lo
-- referencian garantias.idtipogarantia y tipo_garantia_detalle) y borrar la fila devuelve la
-- agencia al valor comun.
--
-- Las penalidades se editan en:
--   backoffice/<idtienda>/penalidadcomision -> vista 'editar'

CREATE TABLE IF NOT EXISTS `tipo_garantia_penalidad` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idtienda` int NOT NULL,
  `idtipo_garantia` int NOT NULL,
  `penalidad` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_tipo_garantia_penalidad` (`idtienda`,`idtipo_garantia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `subtipo_garantia_noprendaria_ii_penalidad` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idtienda` int NOT NULL,
  `idsubtipo_garantia_noprendaria_ii` int NOT NULL,
  `penalidad` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_subtipo_garantia_noprendaria_ii_penalidad` (`idtienda`,`idsubtipo_garantia_noprendaria_ii`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
