<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DireccionesNegocio
{
    public static function clientesSimilares($direccion, $ubigeo): array
    {
        if (! trim((string) $direccion) || ! $ubigeo) {
            return [];
        }

        return DB::table('s_users_prestamo')
            ->where('idubigeo_ac_economica', $ubigeo)
            ->whereNotNull('direccion_ac_economica')
            ->get(['id_s_users', 'direccion_ac_economica'])
            ->filter(fn ($cliente) => self::similares($direccion, $cliente->direccion_ac_economica))
            ->pluck('id_s_users')->unique()->values()->all();
    }

    public static function similares($primera, $segunda): bool
    {
        $normalizar = static function ($direccion) {
            $direccion = Str::upper(Str::ascii((string) $direccion));
            $direccion = preg_replace('/[^A-Z0-9]+/', ' ', $direccion);

            return trim(preg_replace('/\s+/', ' ', $direccion));
        };

        $primera = $normalizar($primera);
        $segunda = $normalizar($segunda);
        if ($primera === '' || $segunda === '') {
            return false;
        }

        preg_match_all('/\d+/', $primera, $numerosPrimera);
        preg_match_all('/\d+/', $segunda, $numerosSegunda);
        if ($numerosPrimera[0] !== $numerosSegunda[0]) {
            return false;
        }

        similar_text($primera, $segunda, $porcentaje);

        return $porcentaje >= 80;
    }
}
