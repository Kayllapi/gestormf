<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CastigoCredito
{
    public static function puedeSolicitar(int $usuario, int $agencia): bool
    {
        $cargos = DB::table('users_permiso')->where('idusers', $usuario)->where('idtienda', $agencia);
        $activos = (clone $cargos)->where('idsession', 2);
        return ((clone $activos)->exists() ? $activos : $cargos)->where('idpermiso', 1)->exists();
    }

    public static function puedeConfirmar(int $usuario, int $agencia): bool
    {
        $cargos = DB::table('users_permiso')->join('permiso', 'permiso.id', 'users_permiso.idpermiso')
            ->where('users_permiso.idusers', $usuario)->where('users_permiso.idtienda', $agencia);
        $activos = (clone $cargos)->where('users_permiso.idsession', 2);
        $efectivos = (clone $activos)->exists() ? $activos : $cargos;
        return $efectivos->where(function ($query) {
            $query->where('permiso.rango', 1)->orWhere('users_permiso.idpermiso', 1);
        })->exists();
    }

    private static function tieneCreditosVigentes(int $cliente): bool
    {
        return DB::table('credito')->where('idcliente', $cliente)->where('estado', 'DESEMBOLSADO')
            ->where('idestadocredito', 1)->where('castigo_estado', '!=', 'CONFIRMADO')
            ->where(fn ($q) => $q->where('saldo_pendientepago', '>', 0)->orWhere('total_pendientepago', '>', 0))
            ->exists();
    }

    // Cuando un credito termina de pagarse fuera del flujo de castigo (ej. desde cobranzacuota),
    // si el cliente ya no tiene creditos vigentes pero tiene al menos un credito castigado
    // (CONFIRMADO), pasa al pool global, igual que cuando se confirma un castigo.
    public static function pasarAGlobalSiSoloQuedanCastigados(int $cliente): void
    {
        if (self::tieneCreditosVigentes($cliente)) {
            return;
        }
        $tieneCastigoActivo = DB::table('credito')->where('idcliente', $cliente)
            ->where('castigo_estado', 'CONFIRMADO')->exists();
        if (!$tieneCastigoActivo) {
            return;
        }
        DB::table('users')->where('id', $cliente)->update(['idasesor' => 0, 'origen_global' => 'CASTIGADO']);
    }

    public function procesar(array $ids, int $usuario, int $agencia, bool $confirmar, ?int $idusuariocobranza = null): void
    {
        DB::transaction(function () use ($ids, $usuario, $agencia, $confirmar, $idusuariocobranza) {
            // Serializar solicitudes del mismo cliente antes de bloquear sus cuentas.
            $clientes = DB::table('credito')->whereIn('id', $ids)->pluck('idcliente')->unique()->sort()->values();
            DB::table('users')->whereIn('id', $clientes)->orderBy('id')->lockForUpdate()->get();
            $creditos = DB::table('credito')->whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get();
            if ($creditos->count() !== count($ids)) {
                throw ValidationException::withMessages(['creditos' => 'Hay créditos que ya no existen.']);
            }
            foreach ($creditos as $credito) {
                $valido = $credito->estado === 'DESEMBOLSADO' && (int) $credito->idestadocredito === 1
                    && (int) $credito->idforma_credito === 2 // solo CNP se puede castigar
                    && ((float) $credito->saldo_pendientepago > 0 || (float) $credito->total_pendientepago > 0)
                    && $credito->castigo_estado === ($confirmar ? 'PENDIENTE' : 'NINGUNO');
                if ($confirmar) {
                    abort_unless(self::puedeConfirmar($usuario, (int) $credito->idtienda), 403, 'Solo el gerente o administrador de la agencia puede confirmar.');
                } else {
                    abort_unless(self::puedeSolicitar($usuario, (int) $credito->idtienda), 403);
                    $valido = $valido && (int) $credito->idtienda === $agencia;
                }
                if (!$valido) {
                    throw ValidationException::withMessages(['creditos' => 'La selección contiene créditos sin deuda, no disponibles o ajenos a su cartera. Actualice la tabla.']);
                }
            }
            DB::table('credito')->whereIn('id', $ids)->update($confirmar ? [
                'castigo_estado' => 'CONFIRMADO', 'castigo_responsable' => $usuario, 'castigo_confirmado_at' => now(),
                'idusuariocobranza' => $idusuariocobranza,
            ] : [
                'castigo_estado' => 'PENDIENTE', 'castigo_solicitante' => $usuario, 'castigo_solicitado_at' => now(),
            ]);
            if ($confirmar) {
                foreach ($clientes as $cliente) {
                    if (!self::tieneCreditosVigentes($cliente)) {
                        // El ejecutivo de cobranza queda registrado en la cuenta (idusuariocobranza), no reemplaza
                        // al asesor del cliente: si ya no tiene creditos vigentes pasa al pool global.
                        DB::table('users')->where('id', $cliente)->update(['idasesor' => 0, 'origen_global' => 'CASTIGADO']);
                    }
                }
            }
        }, 3);
    }

    public function cancelar(array $ids, int $usuario, int $agencia): void
    {
        DB::transaction(function () use ($ids, $usuario, $agencia) {
            $creditos = DB::table('credito')->whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get();
            if ($creditos->count() !== count($ids)) {
                throw ValidationException::withMessages(['creditos' => 'Hay créditos que ya no existen.']);
            }
            foreach ($creditos as $credito) {
                abort_unless(self::puedeSolicitar($usuario, (int) $credito->idtienda), 403);
                $valido = $credito->castigo_estado === 'PENDIENTE' && (int) $credito->idtienda === $agencia;
                if (!$valido) {
                    throw ValidationException::withMessages(['creditos' => 'La selección contiene cuentas que ya no están pendientes o son ajenas a su cartera. Actualice la tabla.']);
                }
            }
            DB::table('credito')->whereIn('id', $ids)->update([
                'castigo_estado' => 'NINGUNO', 'castigo_solicitante' => null, 'castigo_solicitado_at' => null,
            ]);
        }, 3);
    }
}
