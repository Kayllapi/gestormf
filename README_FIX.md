# FIX: Créditos muestran saldo/estado incorrecto tras extorno de pago

## Estado

- ✅ **Código corregido** (ver [Qué se corrigió en el código](#qué-se-corrigió-en-el-código-ya-aplicado)) y **subido**.
- ⏳ **Pendiente:** actualizar la base de datos `sgm` del servidor de producción — el fix de código
  solo evita que el problema vuelva a ocurrir en pagos nuevos; los créditos que ya quedaron con el
  header desincronizado por extornos pasados necesitan una corrección de datos aparte (ver
  [Pendiente: actualizar la base de datos de producción](#pendiente-actualizar-la-base-de-datos-de-producción-sgm)).

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

Se identificaron **dos bugs relacionados**, ambos en el flujo de extorno de pago:

1. **El extorno nunca recalculaba el header del crédito.** Cuando se registra un
   pago en `credito_cobranzacuota`, el proceso actualiza los campos cacheados
   del header (`credito.saldo_pendientepago`, `total_pendientepago`,
   `fecha_cancelado`, `idestadocredito`). Pero cuando ese mismo pago se
   **anula (extorno)** — `credito_cobranzacuota.fechaextorno` deja de ser NULL,
   `idestadoextorno = 2` — el proceso de extorno no volvía a recalcular esos
   campos. El header quedaba "congelado" con los valores del último pago,
   sin importar si ese pago fue anulado después o si hubo pagos válidos
   posteriores.

2. **El registro de pago nunca guardaba el vínculo cuota↔pago en
   `credito_cronograma.idcredito_cobranzacuota`.** Ese campo debía indicar
   qué pago canceló cada cuota del cronograma, para que el extorno supiera
   qué cuotas revertir. En `CobranzacuotaController.php` la línea que
   guardaba ese vínculo estaba **comentada**, así que siempre quedaba en `0`.
   Consecuencia: aunque se arreglara el extorno para restaurar el cronograma,
   no tenía forma de encontrar qué cuotas pertenecían al pago que se estaba
   anulando — el cronograma se quedaba marcado como "pagado" para siempre,
   incluso después de extornar el pago que lo había pagado.

El saldo real de un crédito debe calcularse como

```
deuda_total (credito.total_pagar)
  - SUM(credito_cobranzacuota.total_recibido)
    WHERE idcredito = X AND fechaextorno IS NULL   -- solo pagos vigentes
```

pero el sistema en su lugar confiaba en los campos cacheados del header (bug 1),
que a su vez dependían de un `credito_cronograma` que tampoco se revertía
correctamente (bug 2).

## Tablas / campos involucrados

- `credito`
  - `saldo_pendientepago`, `total_pendientepago`, `total_pagar`
  - `fecha_cancelado`, `estado`, `idestadocredito`
- `credito_cobranzacuota`
  - `fechaextorno` (NULL = vigente, NOT NULL = anulado)
  - `idestadoextorno` (0 = vigente, 2 = extornado)
  - `total_recibido`, `total_pagar`, `opcion_pago` (`PAGO_CUOTA`, `PAGO_TOTAL`, `PAGO_ACUENTA`, ...)
  - `idcredito` (FK a `credito.id`)
- `credito_cronograma`
  - Cronograma de cuotas por `idcredito`. `idestadocredito_cronograma` (1 =
    pendiente, 2 = pagada), `idcredito_cobranzacuota` (FK al pago que la
    canceló — este es el campo que no se estaba guardando).

## Casos reales encontrados (BD `sgm`, créditos de ejemplo)

| Cuenta | idcredito | Deuda total (`total_pagar`) | Pagado (solo pagos vigentes) | Saldo real | Lo que muestra el header | Diagnóstico |
|---|---|---|---|---|---|---|
| 00000165 | 206 | 1,095.85 | 1,095.90 | ≈ 0.00 | saldo 425.00 / pendiente 547.95 | **Está pagado**, pero el header no se actualizó tras el extorno del pago del 24/06 → cobranza lo sigue mostrando pendiente. |
| 00000134 | 169 | 1,525.89 | 2,042.60 | -516.71 (sobrepagado) | saldo 750.00 / pendiente 896.87 | **Está pagado y sobrepagado**, header congelado en un pago extornado del 18/06. |
| 00000130 | 132 | 1,055.88 | 184.70 | 871.18 | saldo 0.00, `fecha_cancelado` seteado | Caso inverso: el header dice "cancelado" (saldo 0) por un pago que canceló las cuotas 3-12 y que fue **extornado 2 minutos después**. En realidad casi no tiene pagos vigentes; sigue debiendo. Este es el caso que además confirmó el bug 2: el `credito_cronograma` seguía marcando esas cuotas como pagadas aun después del extorno, porque nunca quedó registrado qué pago las había pagado. |

Patrón común: en los tres casos, el último valor que quedó en
`credito.saldo_pendientepago` / `total_pendientepago` / `fecha_cancelado`
corresponde a un registro de `credito_cobranzacuota` que **tiene
`fechaextorno` distinto de NULL**, es decir, fue anulado minutos después de
registrarse — y el sistema nunca volvió a recalcular el header con los pagos
realmente vigentes (ni con pagos posteriores hechos en fechas distintas).

## Qué se corrigió en el código (ya aplicado)

1. **`app/Http/Controllers/Layouts/Backoffice/Sistema/CobranzacuotaController.php`**
   Se descomentó el guardado de `credito_cronograma.idcredito_cobranzacuota`
   al registrar un pago (cuotas completas / pago total), para que quede
   registrado qué pago canceló cada cuota. Sin esto, el extorno no tiene
   forma de saber qué revertir.

2. **`app/Http/Controllers/Layouts/Backoffice/Sistema/PagoprestamoController.php`**
   En el extorno (`update()`, `view=extornar`):
   - Se agregó la restauración del `credito_cronograma` para pagos
     `PAGO_CUOTA` / `PAGO_TOTAL` (antes solo restauraba cuotas pagadas vía
     adelanto/acuenta; una cuota pagada de forma directa se quedaba
     marcada como pagada para siempre).
   - Se agregó el recálculo de `saldo_pendientepago` / `total_pendientepago`
     del crédito tras el extorno (usando la misma función `select_cronograma()`
     que usa el resto del sistema).
   - Se limpia `fecha_cancelado` cuando el extorno reactiva un crédito que
     había quedado marcado como cancelado.

3. **`app/Http/Controllers/Layouts/Backoffice/Sistema/PagoprestamocajaController.php`**
   Ya restauraba bien el `credito_cronograma`; se le agregó el mismo
   recálculo de saldo y limpieza de `fecha_cancelado` que al archivo anterior.

Con estos tres cambios, un pago nuevo que se registre y luego se extorne
queda consistente automáticamente — sin intervención manual.

> Nota: `Pagoprestamo1Controller.php` tiene el mismo bug del punto 2 pero
> **no está enrutado** (no aparece en la tabla `modulo` como módulo activo),
> por lo que no se tocó. Si en algún momento se reactiva ese controlador, hay
> que aplicarle el mismo fix.

## Pendiente: actualizar la base de datos de producción (`sgm`)

El fix de código no corrige los créditos que **ya** quedaron con el header (y,
en algunos casos, el `credito_cronograma`) desincronizados por extornos
pasados. Hay que correr una corrección de datos una sola vez. Este es el
procedimiento que se usó para corregir la base local de desarrollo
(`gestormf`) y que debería replicarse en `sgm`:

### 1. Backup

Antes de tocar nada:

```bash
mysqldump -u <usuario> -p sgm credito credito_cronograma credito_cobranzacuota > backup_credito_sgm_$(date +%Y%m%d).sql
```

### 2. Diagnóstico — ver cuántos créditos están afectados

```sql
SELECT c.id, c.cuenta, c.total_pagar AS deuda_total,
  SUM(CASE WHEN cc.fechaextorno IS NULL THEN cc.total_recibido ELSE 0 END) AS pagado_vigente,
  c.total_pagar - SUM(CASE WHEN cc.fechaextorno IS NULL THEN cc.total_recibido ELSE 0 END) AS saldo_real,
  c.saldo_pendientepago AS saldo_en_header,
  c.total_pendientepago AS total_pendiente_header,
  c.fecha_cancelado
FROM credito c
JOIN credito_cobranzacuota cc ON cc.idcredito = c.id
WHERE c.id IN (SELECT DISTINCT idcredito FROM credito_cobranzacuota WHERE fechaextorno IS NOT NULL)
GROUP BY c.id;
```

Esto es solo orientativo (una resta simple no considera intereses/mora
acumulados); el recálculo real lo hace el script del paso 3, con la misma
lógica (`select_cronograma()`) que usa el sistema en pantalla.

### 3. Recalcular el header (dry-run primero, luego aplicar)

Guardar como `fix_headers_extornados.php` en la raíz del proyecto en el
servidor y ejecutar con `php fix_headers_extornados.php` (dry-run, solo
muestra) y `php fix_headers_extornados.php --apply` (aplica):

```php
<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$apply = in_array('--apply', $argv);

$ids = DB::table('credito_cobranzacuota')->whereNotNull('fechaextorno')->distinct()->pluck('idcredito');
echo "Creditos con al menos un pago extornado: {$ids->count()}\n";
echo "Modo: " . ($apply ? "APLICANDO CAMBIOS" : "DRY RUN") . "\n" . str_repeat('-', 120) . "\n";

$cambios = 0; $sinCambios = 0; $errores = 0;

foreach ($ids as $idcredito) {
    $credito = DB::table('credito')
        ->leftJoin('credito_prendatario', 'credito_prendatario.id', 'credito.idcredito_prendatario')
        ->where('credito.id', $idcredito)
        ->select('credito.*', 'credito_prendatario.modalidad as modalidadproductocredito')
        ->first();
    if (!$credito || $credito->estado !== 'DESEMBOLSADO') continue;

    try {
        $primera_cuota_pendiente = DB::table('credito_cronograma')
            ->where('idcredito', $idcredito)->where('idestadocredito_cronograma', 1)
            ->orderBy('numerocuota', 'asc')->value('numerocuota');

        $desc = DB::table('credito_descuentocuota')
            ->where('idcredito', $idcredito)->where('idestadocredito_descuentocuota', 1)->first();
        [$dcap,$dint,$dcom,$dcar,$dpen,$dten,$dcom2] = [0,0,0,0,0,0,0];
        if ($desc && $primera_cuota_pendiente >= $desc->numerocuota_fin) {
            [$dcap,$dint,$dcom,$dcar,$dpen,$dten,$dcom2] =
                [$desc->capital,$desc->interes,$desc->comision,$desc->cargo,$desc->penalidad,$desc->tenencia,$desc->compensatorio];
        }

        $cronograma = select_cronograma(
            $credito->idtienda, $idcredito, $credito->idforma_credito, $credito->modalidadproductocredito,
            $credito->cuotas, $dcap,$dint,$dcom,$dcar,$dpen,$dten,$dcom2, 0, 1, 'detalle_cobranza'
        );

        $count_pendientes = DB::table('credito_cronograma')->where('idcredito', $idcredito)
            ->whereIn('idestadocredito_cronograma', [1, 3])->count();

        $nuevo_saldo = $cronograma['saldo_capital'];
        $nuevo_pendiente = $cronograma['cuota_pendiente'];
        $nuevo_idestadocredito = $count_pendientes == 0 ? 2 : 1;
        $nueva_fecha_cancelado = $count_pendientes == 0 ? ($credito->fecha_cancelado ?: Carbon::now()) : null;

        $cambia = round((float)$credito->saldo_pendientepago,2) !== round((float)$nuevo_saldo,2)
            || round((float)$credito->total_pendientepago,2) !== round((float)$nuevo_pendiente,2)
            || (int)$credito->idestadocredito !== $nuevo_idestadocredito
            || (($credito->fecha_cancelado ? 1 : 0) !== ($nueva_fecha_cancelado ? 1 : 0));

        if ($cambia) {
            $cambios++;
            printf("id=%-4d cuenta=%-10s | ANTES saldo=%.2f pend=%.2f idestado=%d cancel=%s | DESPUES saldo=%.2f pend=%.2f idestado=%d cancel=%s%s\n",
                $idcredito, $credito->cuenta, $credito->saldo_pendientepago, $credito->total_pendientepago,
                $credito->idestadocredito, $credito->fecha_cancelado ?: '--',
                $nuevo_saldo, $nuevo_pendiente, $nuevo_idestadocredito, $nueva_fecha_cancelado ?: '--',
                $apply ? '' : ' [PENDIENTE]');
            if ($apply) {
                DB::table('credito')->where('id', $idcredito)->update([
                    'saldo_pendientepago' => $nuevo_saldo,
                    'total_pendientepago' => $nuevo_pendiente,
                    'idestadocredito' => $nuevo_idestadocredito,
                    'fecha_cancelado' => $nueva_fecha_cancelado,
                ]);
            }
        } else { $sinCambios++; }
    } catch (\Throwable $e) { $errores++; echo "id=$idcredito ERROR: {$e->getMessage()}\n"; }
}

echo str_repeat('-', 120) . "\nCon cambios: $cambios | Sin cambios: $sinCambios | Errores: $errores\n";
```

Este script recalcula el header **a partir del `credito_cronograma` tal como
está hoy en la BD** — usa la misma función (`select_cronograma()`) que usa la
pantalla de cobranza, así que el resultado es consistente con lo que el
sistema mostraría.

### 4. ⚠️ Revisión manual obligatoria antes del paso 3 (o inmediatamente después)

Como el bug 2 (arriba) viene de más tiempo atrás, es posible que existan
créditos en `sgm` donde el `credito_cronograma` **ya está mal** (cuotas
marcadas como pagadas por un pago que fue extornado, sin que ningún otro pago
vigente las cubra) — el mismo patrón que el crédito 132 de este documento. El
script del paso 3 **no detecta ni corrige eso**: si el cronograma ya está mal,
el script simplemente confirma el header con el número equivocado.

Para cada crédito que el script del paso 3 reporte con cambios, comparar:

```sql
-- Para un idcredito puntual: pagos vs. cronograma
SELECT id, fecharegistro, opcion_pago, total_recibido, fechaextorno FROM credito_cobranzacuota WHERE idcredito = <ID> ORDER BY fecharegistro;
SELECT numerocuota, idestadocredito_cronograma, idestadocronograma_pago, idcredito_cobranzacuota, totalcuota, acuenta FROM credito_cronograma WHERE idcredito = <ID> ORDER BY numerocuota;
```

Si hay cuotas marcadas como pagadas (`idestadocredito_cronograma = 2`) cuyo
monto (`totalcuota`/`acuenta`) no corresponde a ningún pago vigente
(`fechaextorno IS NULL`), esas cuotas hay que revertirlas a pendiente antes de
correr el recálculo del header:

```sql
UPDATE credito_cronograma SET
  tenencia=0, penalidad=0, compensatorio=0, totalcuota=0, acuenta=0,
  atraso_dias=0, pagar_amortizacion=0, pagar_interes=0, pagar_comision=0, pagar_cargo=0, pagar_cuota=0,
  pagar_tenencia=0, pagar_penalidad=0, pagar_compensatorio=0, pagar_totalcuota=0,
  descontar_amortizacion=0, descontar_interes=0, descontar_comision=0, descontar_cargo=0, descontar_cuota=0,
  descontar_tenencia=0, descontar_penalidad=0, descontar_compensatorio=0, descontar_totalcuota=0,
  idestadocredito_cronograma=1, idestadocronograma_pago=0, idcredito_cobranzacuota=0
WHERE idcredito = <ID> AND numerocuota BETWEEN <inicio> AND <fin>;
```

y volver a correr el script del paso 3 (`--apply`) para que el header quede
consistente con el cronograma ya corregido.

Esto **no es mecanizable a ciegas** para todos los créditos: como se vio con
las cuentas 133/128/176 al hacer este mismo ejercicio en local, en varios
casos las cuotas "sospechosas" en realidad sí estaban cubiertas por un pago
vigente (el `idcredito_cobranzacuota` solo apuntaba al pago equivocado, un
problema de trazabilidad, no de saldo) y no había que tocarlas. Revisar
crédito por crédito antes de aplicar el UPDATE.

### 5. Verificar

Volver a correr el script del paso 3 sin `--apply`: debería reportar
"Con cambios: 0" para todos los créditos ya revisados.

## Pendiente / no incluido en este documento

- El campo `credito_cronograma.idcredito_cobranzacuota` puede seguir teniendo
  valores "viejos" (apuntando a un pago que ya no es el que realmente cubre
  esa cuota) en créditos que no se hayan revisado a mano — es un problema de
  trazabilidad, no afecta el saldo calculado, pero puede confundir una
  auditoría futura.
- `Pagoprestamo1Controller.php` (no enrutado) no se corrigió — ver nota en la
  sección de código.
