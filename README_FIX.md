# FIX: Créditos muestran saldo/estado incorrecto tras extorno de pago

## Contexto

Base de datos: `sgm` (MySQL, servidor de producción). Módulo de créditos/cobranza
(tablas `credito`, `credito_cobranzacuota`, `credito_cronograma`).

Se detectó que varios créditos muestran en el header (tabla `credito`) un saldo
pendiente / estado de cancelación que **no coincide con la realidad**, porque
quedó basado en un pago que luego fue anulado (extornado). El módulo de
cobranza (pantalla que lista cuotas pendientes, alimentada por
`credito_cobranzacuota` / `credito_cronograma`) sigue mostrando el crédito como
si no estuviera al día, aunque a simple vista (según el header) parecía
cancelado — o viceversa.

## Causa raíz

Cuando se registra un pago en `credito_cobranzacuota`, el proceso actualiza los
campos cacheados en el header del crédito:

- `credito.saldo_pendientepago`
- `credito.total_pendientepago`
- `credito.fecha_cancelado`
- `credito.estado` / `credito.idestadocredito`

Cuando ese mismo pago se **anula (extorno)** — lo cual se registra en
`credito_cobranzacuota.fechaextorno` (deja de ser NULL) e
`idestadoextorno = 2` — **el proceso de extorno no vuelve a recalcular esos
campos del header**. El resultado: el header del crédito queda "congelado" con
los valores que dejó el último pago, sin importar si ese pago fue
posteriormente anulado o si hubo pagos válidos después.

Es decir: el saldo real de un crédito debe calcularse como

```
deuda_total (credito.total_pagar)
  - SUM(credito_cobranzacuota.total_recibido)
    WHERE idcredito = X AND fechaextorno IS NULL   -- solo pagos vigentes
```

pero el sistema en su lugar confía en los campos cacheados del header, que no
se recalculan tras un extorno.

## Tablas / campos involucrados

- `credito`
  - `saldo_pendientepago`, `total_pendientepago`, `total_pagar`
  - `fecha_cancelado`, `estado`, `idestadocredito`
- `credito_cobranzacuota`
  - `fechaextorno` (NULL = vigente, NOT NULL = anulado)
  - `idestadoextorno` (0 = vigente, 2 = extornado)
  - `total_recibido`, `total_pagar`
  - `idcredito` (FK a `credito.id`)
- `credito_cronograma`
  - Cronograma de cuotas por `idcredito`; sus campos `pagar_*` /
    `totalcuota` tampoco reflejan los pagos vigentes reales (siguen
    mostrando el monto íntegro de la cuota, incluso en cuotas cuyo pago
    vigente ya la cubrió). Revisar si este cronograma se actualiza al
    registrar/extornar un pago, o si solo se usa como plantilla original.

## Casos reales encontrados (BD `sgm`, créditos de ejemplo)

| Cuenta | idcredito | Deuda total (`total_pagar`) | Pagado (solo pagos vigentes) | Saldo real | Lo que muestra el header | Diagnóstico |
|---|---|---|---|---|---|---|
| 00000165 | 206 | 1,095.85 | 1,095.90 | ≈ 0.00 | saldo 425.00 / pendiente 547.95 | **Está pagado**, pero el header no se actualizó tras el extorno del pago del 24/06 → cobranza lo sigue mostrando pendiente. |
| 00000134 | 169 | 1,525.89 | 2,042.60 | -516.71 (sobrepagado) | saldo 750.00 / pendiente 896.87 | **Está pagado y sobrepagado**, header congelado en un pago extornado del 18/06. |
| 00000130 | 132 | 1,055.88 | 184.70 | 871.18 | saldo 0.00, `fecha_cancelado` seteado | Caso inverso: el header dice "cancelado" (saldo 0) por un pago que canceló las cuotas 3-12 y que fue **extornado 2 minutos después**. En realidad casi no tiene pagos vigentes; **sigue debiendo ~871.18**. Revisar con el área de cobranzas antes de asumir la cifra, por si el extorno fue un error de digitación corregido de otra forma. |

Patrón común: en los tres casos, el último valor que quedó en
`credito.saldo_pendientepago` / `total_pendientepago` / `fecha_cancelado`
corresponde a un registro de `credito_cobranzacuota` que **tiene
`fechaextorno` distinto de NULL**, es decir, fue anulado minutos después de
registrarse — y el sistema nunca volvió a recalcular el header con los pagos
realmente vigentes (ni con pagos posteriores hechos en fechas distintas).

## Qué debe corregirse en el código

1. **Ubicar el endpoint/función de "extorno" de pago** (el que actualiza
   `credito_cobranzacuota.fechaextorno` / `idestadoextorno`) y agregar, al
   final de esa transacción, un recálculo del header del crédito:
   - Recalcular `saldo_pendientepago` y `total_pendientepago` a partir de la
     suma de `credito_cobranzacuota.total_recibido` donde `fechaextorno IS
     NULL` para ese `idcredito`.
   - Si el saldo recalculado es 0 (o negativo), setear `fecha_cancelado` y el
     estado correspondiente; si no, **limpiar `fecha_cancelado`** (dejarlo en
     NULL) y devolver el crédito a estado activo/pendiente.
2. **Idealmente, centralizar el recálculo del saldo del crédito en una sola
   función** (ej. `recalcularSaldoCredito(idcredito)`) y llamarla tanto al
   registrar un pago como al extornarlo, en vez de tener lógica de
   actualización duplicada en cada flujo. Así se evita que este bug reaparezca
   en otros puntos donde se toque `credito_cobranzacuota`.
3. **Revisar también `credito_cronograma`**: confirmar si esa tabla debería
   reflejar pagos aplicados por cuota (campos `pagar_*`, `acuenta`,
   `totalcuota`) y si el proceso de pago/extorno la está actualizando. En los
   casos revisados, `idcredito_cobranzacuota` quedó en 0 en todas las filas de
   cronograma, lo que sugiere que el vínculo cuota↔pago tampoco se está
   guardando ahí.
4. **Dato para pruebas**: usar los créditos `132`, `169`, `206` (cuentas
   `130`, `134`, `165`) como casos de prueba para validar la corrección,
   comparando el saldo recalculado contra la tabla de arriba.

## Script de diagnóstico usado (para reproducir/verificar)

```sql
-- Saldo real de un crédito según pagos vigentes (no extornados)
SELECT c.id, c.cuenta, c.total_pagar AS deuda_total,
  SUM(CASE WHEN cc.fechaextorno IS NULL THEN cc.total_recibido ELSE 0 END) AS pagado_vigente,
  c.total_pagar - SUM(CASE WHEN cc.fechaextorno IS NULL THEN cc.total_recibido ELSE 0 END) AS saldo_real,
  c.saldo_pendientepago AS saldo_en_header,
  c.total_pendientepago AS total_pendiente_header,
  c.fecha_cancelado
FROM credito c
JOIN credito_cobranzacuota cc ON cc.idcredito = c.id
WHERE c.cuenta IN (130, 134, 165)
GROUP BY c.id;
```

## Pendiente / no incluido en este documento

- No se aplicó ningún cambio en producción; este documento es solo
  diagnóstico.
- Antes de correr cualquier script de corrección masiva de saldos
  (recalcular todos los créditos existentes con pagos extornados), hacer
  backup de `credito` y `credito_cobranzacuota`, y correr primero en modo
  "solo lectura" (SELECT) para revisar cuántos créditos están afectados en
  todo el sistema, no solo estos tres.
