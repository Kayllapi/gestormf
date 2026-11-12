<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $vista = 'backoffice/{idtienda}/usuarioaccesomaster';
        if (DB::table('modulo')->where('vista', $vista)->exists()) {
            throw new RuntimeException('El modulo usuarioaccesomaster ya existe; revise su configuracion antes de migrar.');
        }
        $origen = DB::table('modulo')->where('vista', 'backoffice/{idtienda}/usuarioacceso')->first();
        if (! $origen) {
            throw new RuntimeException('Debe existir el modulo Usuarios para ubicar Gerentes generales en el menu.');
        }
        $modulo = (array) $origen;
        unset($modulo['id']);
        DB::table('modulo')->insert(array_replace($modulo, [
            'nombre' => 'Gerentes generales', 'vista' => $vista,
            'controlador' => 'Layouts\\Backoffice\\Sistema\\UsuarioaccesoMasterController',
            'idestado' => 1, 'orden' => $origen->orden + 1,
        ]));
        Cache::forget('modulo');
    }

    public function down(): void
    {
        DB::transaction(function () {
            $ids = DB::table('modulo')->where('vista', 'backoffice/{idtienda}/usuarioaccesomaster')
                ->where('controlador', 'Layouts\\Backoffice\\Sistema\\UsuarioaccesoMasterController')->pluck('id');
            DB::table('permisoacceso')->whereIn('idmodulo', $ids)->delete();
            DB::table('userspermisoacceso')->whereIn('idmodulo', $ids)->delete();
            DB::table('modulo')->whereIn('id', $ids)->delete();
        });
        Cache::forget('modulo');
    }
};
