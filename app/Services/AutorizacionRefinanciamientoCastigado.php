<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AutorizacionRefinanciamientoCastigado
{
    public static function verificar(int $responsable, int $agencia, string $clave): void
    {
        $usuario = DB::table('users')->where('id', $responsable)
            ->where('idestado', 1)->where('idestadousuario', 1)->where('idtipousuario', 1)->first();

        if ($clave === '' || !$usuario || (string) $usuario->clave === '' || !CastigoCredito::puedeConfirmar($responsable, $agencia)
            || !hash_equals((string) $usuario->clave, $clave)) {
            throw ValidationException::withMessages([
                'responsableclave' => 'El responsable o la contraseña no son válidos para autorizar en esta agencia.',
            ]);
        }
    }
}
