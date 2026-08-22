-- Agrega la columna que distingue con cual de las 3 modalidades de Pago Anticipado
-- (reduccion_cuota / reduccion_plazo / cancelacion_total) se genero cada registro de
-- credito_pagoanticipado_historial, para poder armar el sufijo especifico de "Modalidad de C"
-- (ver PAGOANTICIPADO.md) en vez del sufijo generico "(pago anticipado)".

ALTER TABLE `credito_pagoanticipado_historial`
  ADD COLUMN `modalidad_pagoanticipado` VARCHAR(20) NOT NULL DEFAULT '' AFTER `idestado`;
