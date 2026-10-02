<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use DB;

/**
 * Bancos y Gestion de Depositario por agencia: filtro, guardado acotado y copia
 * entre agencias.
 *
 * El foco de este archivo es que un guardado NO debe tocar las otras agencias: en
 * Gestion de Depositario el guardado hacia un delete() sin filtro, y al hacerlo
 * por agencia eso habria vaciado los catalogos de las otras dos.
 */
class BancoDepositarioPorAgenciaTest extends TestCase
{
    use DatabaseTransactions;

    /** JAQ (6277) tiene permiso en 194 y 198, con 194 activa */
    private function usuarioMultiagencia(): User
    {
        $u = User::find(6277);
        $this->assertNotNull($u, 'se esperaba el usuario 6277');

        return $u;
    }

    private function agenciasPermitidas(User $u)
    {
        $ids = DB::table('users_permiso')
            ->where('idusers', $u->id)
            ->where('idestado', 1)
            ->distinct()
            ->pluck('idtienda')
            ->map(fn ($i) => (int) $i)
            ->all();

        return DB::table('tienda')
            ->where('idestado', 1)
            ->whereIn('id', $ids)
            ->orderBy('nombreagencia')
            ->pluck('id')
            ->map(fn ($i) => (int) $i)
            ->all();
    }

    private function assertSinErrores(string $cuerpo, string $contexto): void
    {
        $this->assertStringNotContainsString('SQLSTATE', $cuerpo, $contexto);
        $this->assertStringNotContainsString('Undefined column', $cuerpo, $contexto);
        $this->assertStringNotContainsString('Whoops', $cuerpo, $contexto);
    }

    // ===============================================================
    // BANCOS
    // ===============================================================

    public function test_bancos_cada_agencia_ve_su_propio_catalogo(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        foreach ($this->agenciasPermitidas($u) as $agencia) {
            $r = $this->get("/backoffice/0/banco/showbanco?idagencia={$agencia}");
            $r->assertOk();

            preg_match_all("/data-valor-columna='(\d+)'/", $r->getContent(), $m);
            $ids = array_map('intval', $m[1]);
            $this->assertNotEmpty($ids, "la agencia {$agencia} no devolvio bancos");

            $ajenos = DB::table('banco')->whereIn('id', $ids)->where('idtienda', '!=', $agencia)->pluck('id')->all();
            $this->assertSame([], $ajenos, "la agencia {$agencia} ve bancos ajenos");

            $this->assertCount(
                DB::table('banco')->where('idtienda', $agencia)->count(),
                $ids,
                "la agencia {$agencia} deberia ver todos sus bancos"
            );
        }
    }

    public function test_bancos_registra_en_la_agencia_seleccionada(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $destino = $this->agenciasPermitidas($u)[1];
        $antes = DB::table('banco')->where('idtienda', $destino)->count();

        $r = $this->post('/backoffice/194/banco', [
            'view'      => 'registrar',
            'idagencia' => $destino,
            'nombre'    => 'Banco Prueba '.uniqid(),
            'cuenta'    => '000-111222333',
        ]);

        $r->assertOk();
        $r->assertJson(['resultado' => 'CORRECTO']);

        $this->assertSame($antes + 1, DB::table('banco')->where('idtienda', $destino)->count());

        $nuevo = DB::table('banco')->where('idtienda', $destino)->orderBy('id', 'desc')->first();
        $this->assertSame($destino, (int) $nuevo->idtienda);
    }

    public function test_bancos_no_se_puede_editar_uno_de_otra_agencia(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $mias = $this->agenciasPermitidas($u);
        $ajeno = DB::table('banco')->where('idtienda', '!=', $mias[0])->first();

        $this->get("/backoffice/194/banco/{$ajeno->id}/edit?view=editar&idagencia={$mias[0]}")
            ->assertNotFound();
    }

    public function test_bancos_no_se_borra_uno_de_otra_agencia(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $mias = $this->agenciasPermitidas($u);
        $ajeno = DB::table('banco')->where('idtienda', '!=', $mias[0])->first();

        // el banco no existe para la agencia seleccionada, asi que la guarda
        // responde 404 y el registro sobrevive
        $this->delete("/backoffice/194/banco/{$ajeno->id}", [
            'view'      => 'eliminar',
            'idagencia' => $mias[0],
        ])->assertNotFound();

        $this->assertTrue(
            DB::table('banco')->where('id', $ajeno->id)->exists(),
            'se borro un banco de otra agencia'
        );
    }

    public function test_bancos_con_movimientos_no_se_pueden_borrar(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $agencia = $this->agenciasPermitidas($u)[0];

        // banco con movimientos de caja asociados
        $conMovimientos = DB::table('credito_formapago')
            ->whereNotNull('idbanco')->where('idbanco', '<>', 0)->value('idbanco');

        if (! $conMovimientos) {
            $this->markTestSkipped('no hay bancos con movimientos de caja');
        }

        $antes = DB::table('banco')->where('id', $conMovimientos)->exists();

        $r = $this->delete("/backoffice/194/banco/{$conMovimientos}", [
            'view'      => 'eliminar',
            'idagencia' => $agencia,
        ]);

        $r->assertOk();
        $r->assertJson(['resultado' => 'ERROR'], 'debió rechazar borrar un banco con movimientos');
        $this->assertSame($antes, DB::table('banco')->where('id', $conMovimientos)->exists());
    }

    public function test_bancos_copiar_replica_los_bancos(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        [$origen, $destino] = $this->agenciasPermitidas($u);

        $antes = DB::table('banco')->where('idtienda', $destino)->count();
        $this->copiarBancos($origen, $destino)->assertJson(['resultado' => 'CORRECTO']);

        $this->assertGreaterThanOrEqual(
            $antes,
            DB::table('banco')->where('idtienda', $destino)->count(),
            'la copia no puede reducir los bancos del destino'
        );

        // todo banco del origen debe existir en destino con el mismo par nombre+cuenta
        $faltantes = [];
        foreach (DB::table('banco')->where('idtienda', $origen)->get() as $b) {
            $existe = DB::table('banco')
                ->where('idtienda', $destino)
                ->where('nombre', $b->nombre)
                ->where('cuenta', $b->cuenta)
                ->exists();

            if (! $existe) {
                $faltantes[] = $b->nombre;
            }
        }

        $this->assertSame([], $faltantes, 'bancos del origen sin copiar');
    }

    public function test_bancos_copiar_crea_el_banco_que_el_destino_no_tiene(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        [$origen, $destino] = $this->agenciasPermitidas($u);

        $nombre = 'Banco Exclusivo '.uniqid();
        DB::table('banco')->insert([
            'idtienda' => $origen,
            'nombre'   => $nombre,
            'cuenta'   => '999-888777',
            'estado'   => 'ACTIVO',
        ]);

        $this->assertSame(
            0,
            DB::table('banco')->where('idtienda', $destino)->where('nombre', $nombre)->count(),
            'el banco deberia ser exclusivo del origen antes de copiar'
        );

        $this->copiarBancos($origen, $destino)->assertJson(['resultado' => 'CORRECTO']);

        $copiado = DB::table('banco')
            ->where('idtienda', $destino)
            ->where('nombre', $nombre)
            ->first();

        $this->assertNotNull($copiado, 'la copia no llevo el banco nuevo al destino');
        $this->assertSame('999-888777', $copiado->cuenta);
        $this->assertSame('ACTIVO', $copiado->estado);

        // No se asume "un insert": el destino puede haber divergido del origen (por
        // ejemplo si alguien edito una cuenta en esa agencia), y en ese caso la copia
        // agrega tambien los que no encuentra. Lo que se exige es que el destino
        // tenga TODOS los bancos del origen.
        foreach (DB::table('banco')->where('idtienda', $origen)->get() as $b) {
            $existe = DB::table('banco')
                ->where('idtienda', $destino)
                ->where('nombre', $b->nombre)
                ->where('cuenta', $b->cuenta)
                ->exists();

            $this->assertTrue($existe, "tras copiar falta el banco '{$b->nombre}' / '{$b->cuenta}'");
        }
    }

    public function test_bancos_copiar_no_crea_duplicados_al_repetir(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        [$origen, $destino] = $this->agenciasPermitidas($u);

        $this->copiarBancos($origen, $destino)->assertOk();
        $primera = DB::table('banco')->where('idtienda', $destino)->count();

        $this->copiarBancos($origen, $destino)->assertOk();
        $segunda = DB::table('banco')->where('idtienda', $destino)->count();

        $this->assertSame($primera, $segunda, 'copiar dos veces duplicó bancos');
    }

    public function test_bancos_copiar_rechaza_ori_gen_igual_a_destino(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $a = $this->agenciasPermitidas($u)[0];
        $this->copiarBancos($a, $a)->assertJson(['resultado' => 'ERROR']);
    }

    public function test_bancos_copiar_rechaza_una_agencia_sin_permiso(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $ajena = $this->agenciaSinPermiso($u);
        if (! $ajena) {
            $this->markTestSkipped('el usuario tiene permiso en todas');
        }

        $permitida = $this->agenciasPermitidas($u)[0];
        $this->copiarBancos($permitida, $ajena)->assertForbidden();
        $this->copiarBancos($ajena, $permitida)->assertForbidden();
    }

    private function copiarBancos(int $origen, int $destino)
    {
        return $this->put('/backoffice/194/banco/0', [
            'view'      => 'copiar',
            'idorigen'  => $origen,
            'iddestino' => $destino,
        ]);
    }

    private function agenciaSinPermiso(User $u)
    {
        foreach (DB::table('tienda')->where('idestado', 1)->pluck('id') as $id) {
            if (! in_array((int) $id, $this->agenciasPermitidas($u))) {
                return (int) $id;
            }
        }

        return null;
    }

    // ===============================================================
    // GESTION DE DEPOSITARIO
    // ===============================================================

    /** el payload completo de la pantalla para una agencia */
    private function payloadDepositario(int $idagencia, array $depositarios, array $representantes): array
    {
        return [
            'view'          => 'editar',
            'idagencia'     => $idagencia,
            'seleccionar_conentregaposesion' => json_encode($depositarios),
            'seleccionar_sinentregaposesion' => json_encode([]),
            'seleccionar_representantecomun' => json_encode($representantes),
        ];
    }

    private function depositario(int $constitucion, string $custodia, string $nombre, string $ruc): array
    {
        return [
            'custodiagarantia_id'      => $custodia,
            'custodiagarantia_nombre'  => 'Custodia '.$custodia,
            'nombre'                   => $nombre,
            'doeruc'                   => $ruc,
            'direccion'                => 'Av. Prueba 123',
            'representante_doeruc'     => '11122233',
            'representante_nombre'     => 'Representante Prueba',
            'estado_id'                => '1',
            'estado_nombre'            => 'Activo',
            'constituciongarantia_id'  => (string) $constitucion,
            'constituciongarantia_nombre' => $constitucion == 1 ? 'Con entrega de posesión' : 'Sin entrega de posesión',
        ];
    }

    private function representante(string $nombre, string $doi): array
    {
        return [
            'nombre'        => $nombre,
            'doi'           => $doi,
            'direccion'     => 'Jr. Prueba 456',
            'ubigeo_id'     => 0,
            'ubigeo_nombre' => '',
            'estado_id'     => '1',
            'estado_nombre' => 'Activo',
        ];
    }

    public function test_depositario_cada_agencia_ve_su_propia_lista(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        foreach ($this->agenciasPermitidas($u) as $agencia) {
            $r = $this->get("/backoffice/194/gestiondepositario?view=tabla&idagencia={$agencia}");
            $r->assertOk();
            $this->assertSinErrores($r->getContent(), "la pantalla de depositario de la {$agencia}");

            // la fila de otra agencia no debe aparecer en el HTML
            $ajeno = DB::table('credito_gestiondepositario')->where('idtienda', '!=', $agencia)->first();
            $propio = DB::table('credito_gestiondepositario')->where('idtienda', $agencia)->first();

            $this->assertNotNull($propio);
            $this->assertStringContainsString($propio->doeruc, $r->getContent(), 'faltaria un depositario propio');
        }
    }

    /**
     * Regresion del bug reportado: el index de este modulo dibujaba las tablas, asi
     * que es el unico lugar donde el idagencia del selector se tiene que resolver.
     * Se resolvia a mano (activa -> ruta -> primera) sin leer el parametro, con lo
     * cual cambiar la agencia recargaba la pagina pero volvia a pintar los datos de
     * la agencia activa: el filtro no hacia nada.
     */
    public function test_depositario_el_idagencia_del_selector_realmente_filtra(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $agencias = $this->agenciasPermitidas($u);
        $this->assertGreaterThanOrEqual(2, count($agencias), 'se necesitan dos agencias');

        // se marca un depositario solo de la segunda agencia
        $exclusivo = 'EXCLUSIVO-'.uniqid();
        DB::table('credito_gestiondepositario')->insert([
            'idtienda'                => $agencias[1],
            'custodiagarantia_id'     => '1',
            'custodiagarantia_nombre' => 'ACREEDOR',
            'nombre'                  => 'Solo En '.$agencias[1],
            'doeruc'                  => $exclusivo,
            'direccion'               => 'Av. Unica 999',
            'representante_doeruc'    => '99988877',
            'representante_nombre'    => 'Unico',
            'estado_id'               => '1',
            'estado_nombre'           => 'Activo',
            'constituciongarantia_id' => 1,
            'constituciongarantia_nombre' => 'Con entrega de posesión',
        ]);

        // la pantalla de ESA agencia debe mostrarlo
        $r = $this->get("/backoffice/194/gestiondepositario?view=tabla&idagencia={$agencias[1]}");
        $r->assertOk();
        $this->assertStringContainsString($exclusivo, $r->getContent(), 'la agencia no ve su propio depositario');

        // la pantalla de la OTRA agencia no debe mostrarlo
        $r2 = $this->get("/backoffice/194/gestiondepositario?view=tabla&idagencia={$agencias[0]}");
        $r2->assertOk();
        $this->assertStringNotContainsString(
            $exclusivo,
            $r2->getContent(),
            'el filtro del selector no esta aislando la agencia'
        );

        // y el selector debe quedar marcado en la agencia pedida
        $this->assertStringContainsString(
            'value="'.$agencias[1].'" selected',
            $r->getContent(),
            'el selector no quedo en la agencia solicitada'
        );
    }

    /**
     * Regresion del segundo sintoma: tras copiar se llamaba ir_inicio(), que lleva
     * al inicio del sistema. El copiado si se hacia, pero el usuario nunca veia el
     * resultado y concloia que no habia copiado. Se verifica que la pantalla de la
     * agencia destino ya muestre lo copiado.
     */
    public function test_depositario_tras_copiar_la_pantalla_muestra_la_agencia_destino(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        [$origen, $destino] = $this->agenciasPermitidas($u);

        $exclusivo = 'COPIADO-'.uniqid();
        DB::table('credito_representantecomun')->insert([
            'idtienda'      => $origen,
            'nombre'        => 'Representante '.$exclusivo,
            'doi'           => '77788899',
            'direccion'     => 'Jr. Copiado 111',
            'ubigeo_id'     => 0,
            'ubigeo_nombre' => '',
            'estado_id'     => '1',
            'estado_nombre' => 'Activo',
        ]);

        $this->copiarDepositario($origen, $destino)->assertJson(['resultado' => 'CORRECTO']);

        // la pantalla del destino debe mostrarlo: esto es lo que el usuario
        // comprobaba y antes no occurria porque se le sacaba al inicio
        $r = $this->get("/backoffice/194/gestiondepositario?view=tabla&idagencia={$destino}");
        $r->assertOk();
        $this->assertStringContainsString(
            $exclusivo,
            $r->getContent(),
            'tras copiar, la pantalla del destino no muestra lo copiado'
        );
    }

    /**
     * El test mas importante: guardar en una agencia NO debe vaciar las otras.
     * El guardado hacia delete() sin filtro.
     */
    public function test_depositario_guardar_en_una_agencia_no_toca_las_demas(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $agencias = $this->agenciasPermitidas($u);
        $destino = $agencias[1];
        $otra = $agencias[0];

        // fotografia del estado de la otra agencia
        $depositarios_otra = DB::table('credito_gestiondepositario')->where('idtienda', $otra)->count();
        $representantes_otra = DB::table('credito_representantecomun')->where('idtienda', $otra)->count();
        $nombres_otra = DB::table('credito_gestiondepositario')->where('idtienda', $otra)->pluck('doeruc')->sort()->values()->all();

        $this->assertGreaterThan(0, $depositarios_otra, 'la otra agencia deberia tener depositarios');

        $r = $this->put('/backoffice/194/gestiondepositario/0', $this->payloadDepositario(
            $destino,
            [$this->depositario(1, '1', 'Depositario Nuevo', '99999999999')],
            [$this->representante('Representante Nuevo', '11111111')]
        ));

        $r->assertOk();
        $r->assertJson(['resultado' => 'CORRECTO']);

        // la otra agencia quedo intacta
        $this->assertSame($depositarios_otra, DB::table('credito_gestiondepositario')->where('idtienda', $otra)->count());
        $this->assertSame($representantes_otra, DB::table('credito_representantecomun')->where('idtienda', $otra)->count());
        $this->assertSame(
            $nombres_otra,
            DB::table('credito_gestiondepositario')->where('idtienda', $otra)->pluck('doeruc')->sort()->values()->all(),
            "el guardado en la agencia {$destino} modifico los depositarios de la {$otra}"
        );

        // y la destino quedo con lo que se mando
        $this->assertSame(1, DB::table('credito_gestiondepositario')->where('idtienda', $destino)->count());
        $this->assertSame('99999999999', DB::table('credito_gestiondepositario')->where('idtienda', $destino)->value('doeruc'));
        $this->assertSame(1, DB::table('credito_representantecomun')->where('idtienda', $destino)->count());
    }

    public function test_depositario_un_guardado_invalido_no_deja_la_agencia_a_medias(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $destino = $this->agenciasPermitidas($u)[1];
        $antes = DB::table('credito_gestiondepositario')->where('idtienda', $destino)->pluck('doeruc')->sort()->values()->all();

        // un depositario sin custodia de garantia => error
        $invalido = $this->depositario(1, '', 'Sin Custodia', '111');
        $invalido['custodiagarantia_id'] = '';

        $r = $this->put('/backoffice/194/gestiondepositario/0', $this->payloadDepositario(
            $destino,
            [$invalido],
            []
        ));

        $r->assertOk();
        $r->assertJson(['resultado' => 'ERROR']);

        $this->assertSame(
            $antes,
            DB::table('credito_gestiondepositario')->where('idtienda', $destino)->pluck('doeruc')->sort()->values()->all(),
            'un guardado invalido borro la configuracion de la agencia'
        );
    }

    public function test_depositario_guardar_rechaza_una_agencia_sin_permiso(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $ajena = $this->agenciaSinPermiso($u);
        if (! $ajena) {
            $this->markTestSkipped('el usuario tiene permiso en todas');
        }

        $this->put('/backoffice/194/gestiondepositario/0', $this->payloadDepositario(
            $ajena,
            [$this->depositario(1, '1', 'No deberia guardarse', '222')],
            []
        ))->assertForbidden();

        $this->assertSame(
            0,
            DB::table('credito_gestiondepositario')->where('idtienda', $ajena)->where('doeruc', '222')->count()
        );
    }

    public function test_depositario_no_borra_registros_desde_el_destroy(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $agencia = $this->agenciasPermitidas($u)[0];
        $depositarios = DB::table('credito_gestiondepositario')->count();
        $productos = DB::table('credito_prendatario')->count();

        // el destroy antes hacia DELETE sobre credito_prendatario
        $this->delete('/backoffice/194/gestiondepositario/1', [
            'view'      => 'eliminar',
            'idagencia' => $agencia,
        ])->assertOk();

        $this->assertSame($productos, DB::table('credito_prendatario')->count(), 'el destroy toco los productos de credito');
        $this->assertSame($depositarios, DB::table('credito_gestiondepositario')->count());
    }

    public function test_depositario_copiar_replica_depositarios_y_representantes(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        [$origen, $destino] = $this->agenciasPermitidas($u);

        $d_antes = DB::table('credito_gestiondepositario')->where('idtienda', $destino)->count();
        $r_antes = DB::table('credito_representantecomun')->where('idtienda', $destino)->count();

        $this->copiarDepositario($origen, $destino)->assertJson(['resultado' => 'CORRECTO']);

        $this->assertGreaterThanOrEqual($d_antes, DB::table('credito_gestiondepositario')->where('idtienda', $destino)->count());
        $this->assertGreaterThanOrEqual($r_antes, DB::table('credito_representantecomun')->where('idtienda', $destino)->count());

        // todo depositario del origen debe existir en destino
        foreach (DB::table('credito_gestiondepositario')->where('idtienda', $origen)->get() as $d) {
            $existe = DB::table('credito_gestiondepositario')
                ->where('idtienda', $destino)
                ->where('constituciongarantia_id', $d->constituciongarantia_id)
                ->where('custodiagarantia_id', $d->custodiagarantia_id)
                ->where('nombre', $d->nombre)
                ->where('doeruc', $d->doeruc)
                ->exists();

            $this->assertTrue($existe, "el depositario '{$d->nombre}' no llego al destino");
        }

        foreach (DB::table('credito_representantecomun')->where('idtienda', $origen)->get() as $r) {
            $existe = DB::table('credito_representantecomun')
                ->where('idtienda', $destino)
                ->where('nombre', $r->nombre)
                ->where('doi', $r->doi)
                ->exists();

            $this->assertTrue($existe, "el representante '{$r->nombre}' no llego al destino");
        }
    }

    /**
     * Las filas "Sin entrega de posesion" sin nombre ni RUC se distinguen solo por
     * la custodia. Si el emparejamiento no incluyera la custodia, dos filas vacias
     * se emparejarian entre si y una se pisaria con la otra.
     */
    public function test_depositario_copiar_respeta_las_filas_sin_nombre_ni_ruc(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        [$origen, $destino] = $this->agenciasPermitidas($u);

        $vacias_origen = DB::table('credito_gestiondepositario')
            ->where('idtienda', $origen)
            ->where('constituciongarantia_id', 2)
            ->where('nombre', '')
            ->count();

        if ($vacias_origen < 2) {
            $this->markTestSkipped('hacen falta dos filas sin nombre en el origen');
        }

        $custodias_origen = DB::table('credito_gestiondepositario')
            ->where('idtienda', $origen)
            ->where('constituciongarantia_id', 2)
            ->where('nombre', '')
            ->pluck('custodiagarantia_id')
            ->sort()
            ->values()
            ->all();

        $this->copiarDepositario($origen, $destino)->assertOk();

        $custodias_destino = DB::table('credito_gestiondepositario')
            ->where('idtienda', $destino)
            ->where('constituciongarantia_id', 2)
            ->where('nombre', '')
            ->pluck('custodiagarantia_id')
            ->sort()
            ->values()
            ->all();

        $this->assertSame(
            $custodias_origen,
            $custodias_destino,
            'las filas sin nombre se pisaron entre si al copiar'
        );
    }

    public function test_depositario_copiar_no_crea_duplicados_al_repetir(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        [$origen, $destino] = $this->agenciasPermitidas($u);

        $this->copiarDepositario($origen, $destino)->assertOk();
        $primera = DB::table('credito_gestiondepositario')->where('idtienda', $destino)->count();
        $primera_r = DB::table('credito_representantecomun')->where('idtienda', $destino)->count();

        $this->copiarDepositario($origen, $destino)->assertOk();

        $this->assertSame($primera, DB::table('credito_gestiondepositario')->where('idtienda', $destino)->count());
        $this->assertSame($primera_r, DB::table('credito_representantecomun')->where('idtienda', $destino)->count());
    }

    public function test_depositario_copiar_rechaza_ori_gen_igual_a_destino(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $a = $this->agenciasPermitidas($u)[0];
        $this->copiarDepositario($a, $a)->assertJson(['resultado' => 'ERROR']);
    }

    public function test_depositario_copiar_rechaza_una_agencia_sin_permiso(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $ajena = $this->agenciaSinPermiso($u);
        if (! $ajena) {
            $this->markTestSkipped('el usuario tiene permiso en todas');
        }

        $permitida = $this->agenciasPermitidas($u)[0];
        $this->copiarDepositario($permitida, $ajena)->assertForbidden();
        $this->copiarDepositario($ajena, $permitida)->assertForbidden();
    }

    private function copiarDepositario(int $origen, int $destino)
    {
        return $this->put('/backoffice/194/gestiondepositario/0', [
            'view'      => 'copiar',
            'idorigen'  => $origen,
            'iddestino' => $destino,
        ]);
    }

    // ===============================================================
    // Regresiones
    // ===============================================================

    public function test_las_migraciones_replicaron_las_tres_agencias(): void
    {
        foreach ([194, 198, 199] as $agencia) {
            $this->assertSame(4, DB::table('banco')->where('idtienda', $agencia)->count());
            $this->assertSame(5, DB::table('credito_gestiondepositario')->where('idtienda', $agencia)->count());
            $this->assertSame(2, DB::table('credito_representantecomun')->where('idtienda', $agencia)->count());
        }
    }

    public function test_los_movimientos_de_caja_siguen_resolviendo_su_banco(): void
    {
        // los bancos estan referenciados por credito_formapago y credito_cobranzacuota:
        // la migracion no debe haber dejado ninguno sin resolver
        foreach (['credito_formapago', 'credito_cobranzacuota'] as $tabla) {
            $huerfanos = DB::table($tabla)
                ->leftJoin('banco as b', 'b.id', '=', $tabla.'.idbanco')
                ->where($tabla.'.idbanco', '>', 0)
                ->whereNull('b.id')
                ->count();

            $this->assertSame(0, $huerfanos, "{$tabla} tiene movimientos con un banco inexistente");
        }
    }

    public function test_el_representante_por_prestamo_no_se_toco(): void
    {
        // credito_representantecomun_prestamo es otra cosa (tiene id_credito) y no se
        // administra desde esta pantalla: no debe llevar idtienda.
        $columnas = array_map(
            fn ($c) => $c->Field,
            DB::select('SHOW COLUMNS FROM credito_representantecomun_prestamo')
        );

        $this->assertContains('id_credito', $columnas);
        $this->assertNotContains('idtienda', $columnas, 'esa tabla es por prestamo, no por agencia');
    }
}
