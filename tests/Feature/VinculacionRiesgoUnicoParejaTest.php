<?php

namespace Tests\Feature;

use App\Support\VinculacionRiesgoUnico;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class VinculacionRiesgoUnicoParejaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'riesgo_test', 'database.connections.riesgo_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('users', function (Blueprint $t) {
            $t->integer('id')->primary(); $t->integer('idestadocivil');
            $t->string('identificacion'); $t->string('nombrecompleto');
        });
        Schema::create('s_users_prestamo', function (Blueprint $t) {
            $t->integer('id_s_users'); $t->string('dni_pareja')->nullable();
            $t->string('direccion_ac_economica')->nullable(); $t->integer('idubigeo_ac_economica')->nullable();
        });
        Schema::create('credito', function (Blueprint $t) {
            $t->integer('id')->primary(); $t->integer('idcliente'); $t->string('cuenta');
            $t->decimal('monto_solicitado', 12, 2); $t->integer('idestadocredito'); $t->string('estado');
        });
        DB::table('users')->insert([
            ['id' => 1, 'idestadocivil' => 2, 'identificacion' => '11111111', 'nombrecompleto' => 'Cliente'],
            ['id' => 2, 'idestadocivil' => 1, 'identificacion' => '01234567', 'nombrecompleto' => 'Pareja'],
        ]);
        DB::table('s_users_prestamo')->insert([
            ['id_s_users' => 1, 'dni_pareja' => '01234567', 'direccion_ac_economica' => 'Jr Union 123', 'idubigeo_ac_economica' => 10],
            ['id_s_users' => 2, 'dni_pareja' => null, 'direccion_ac_economica' => 'Jr Union 123', 'idubigeo_ac_economica' => 10],
        ]);
        DB::table('credito')->insert([
            ['id' => 1, 'idcliente' => 2, 'cuenta' => '180', 'monto_solicitado' => 100.50, 'idestadocredito' => 1, 'estado' => 'DESEMBOLSADO'],
            ['id' => 2, 'idcliente' => 2, 'cuenta' => '181', 'monto_solicitado' => 20, 'idestadocredito' => 0, 'estado' => 'DESEMBOLSADO'],
        ]);
    }

    public function test_pareja_en_ambas_columnas_sin_duplicar_total(): void
    {
        foreach ([2, 4, 6] as $estado) {
            DB::table('users')->where('id', 1)->update(['idestadocivil' => $estado]);
            $filas = VinculacionRiesgoUnico::filas(1);
            $this->assertCount(1, $filas);
            $this->assertSame('180', $filas[0]['familia'][0]['cuenta']);
            $this->assertCount(1, $filas[0]['gestion']);
            $this->assertEquals(100.50, $filas[0]['total']);
        }
    }

    public function test_no_vincula_pareja_para_otro_estado_o_dni_ambiguo(): void
    {
        DB::table('users')->where('id', 1)->update(['idestadocivil' => 1]);
        $this->assertEmpty(VinculacionRiesgoUnico::filas(1)[0]['familia']);

        DB::table('users')->where('id', 1)->update(['idestadocivil' => 2]);
        DB::table('users')->insert(['id' => 3, 'idestadocivil' => 1, 'identificacion' => '01234567', 'nombrecompleto' => 'Duplicado']);
        $this->assertEmpty(VinculacionRiesgoUnico::filas(1)[0]['familia']);
    }

    public function test_encuentra_pareja_aunque_no_tenga_direccion_de_negocio(): void
    {
        DB::table('s_users_prestamo')->where('id_s_users', 1)->update(['direccion_ac_economica' => null]);
        $filas = VinculacionRiesgoUnico::filas(1);
        $this->assertCount(1, $filas);
        $this->assertEmpty($filas[0]['gestion']);
        $this->assertSame('180', $filas[0]['familia'][0]['cuenta']);
    }
}
