<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DepositarioConsumidoresPorAgenciaTest extends TestCase
{
    use DatabaseTransactions;

    public function test_garantias_solo_ofrece_depositarios_activos_de_la_agencia_activa(): void
    {
        $this->actingAs(User::findOrFail(6277));
        $agencia = idtienda_actual();
        $otra = DB::table('tienda')->where('id', '!=', $agencia)->value('id');
        $fila = (array) DB::table('credito_gestiondepositario')->first();
        unset($fila['id']);

        foreach ([[$agencia, 1, 'PROPIO'], [$otra, 1, 'AJENO'], [$agencia, 2, 'INACTIVO']] as [$idtienda, $estado, $nombre]) {
            DB::table('credito_gestiondepositario')->insert(array_replace($fila, [
                'idtienda' => $idtienda,
                'estado_id' => $estado,
                'constituciongarantia_id' => 9876,
                'nombre' => $nombre,
            ]));
        }

        $this->getJson('/backoffice/0/garantias/show_constituciongarantia?constituciongarantia_id=9876')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.nombre', 'PROPIO');
    }

    public function test_detalle_resuelve_agencia_y_custodia_aunque_nombre_y_ruc_coincidan(): void
    {
        $this->actingAs(User::findOrFail(6277));
        $agencia = idtienda_actual();
        $otra = DB::table('tienda')->where('id', '!=', $agencia)->value('id');
        $fila = (array) DB::table('credito_gestiondepositario')->first();
        unset($fila['id']);

        foreach ([[$otra, 1, 'AJENA'], [$agencia, 2, 'OTRA CUSTODIA'], [$agencia, 1, 'CORRECTA']] as [$idtienda, $custodia, $direccion]) {
            DB::table('credito_gestiondepositario')->insert(array_replace($fila, [
                'idtienda' => $idtienda,
                'estado_id' => 1,
                'constituciongarantia_id' => 9876,
                'custodiagarantia_id' => $custodia,
                'nombre' => 'PRUEBA AGENCIA',
                'doeruc' => '12345678901',
                'direccion' => $direccion,
            ]));
        }

        $this->getJson('/backoffice/0/garantias/show_custodiagarantia?constituciongarantia_id=9876&custodiagarantia_id=1&nombre=PRUEBA%20AGENCIA&doeruc=12345678901')
            ->assertOk()
            ->assertJsonPath('credito_gestiondepositario.direccion', 'CORRECTA');
    }
}
