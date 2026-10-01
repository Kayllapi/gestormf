<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use DB;

/**
 * Giro Economico por agencia: filtro, copia entre agencias y, sobre todo, que el
 * catalogo que se le ofrece al evaluador de un credito sea el de su agencia.
 */
class GiroEconomicoPorAgenciaTest extends TestCase
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

    // ---------------------------------------------------------------
    // Filtro por agencia
    // ---------------------------------------------------------------

    public function test_el_filtro_muestra_solo_las_agencias_permitidas(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $r = $this->get('/backoffice/194/giroeconomico?view=tabla');
        $r->assertOk();
        $this->assertSinErrores($r->getContent(), 'la pantalla de giro economico');

        foreach ($this->agenciasPermitidas($u) as $id) {
            $this->assertStringContainsString('value="'.$id.'"', $r->getContent());
        }
    }

    public function test_cada_agencia_ve_su_propio_catalogo(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        foreach ($this->agenciasPermitidas($u) as $agencia) {
            $r = $this->get("/backoffice/0/giroeconomico/show_table?idagencia={$agencia}");
            $r->assertOk();

            preg_match_all("/data-valor-columna='(\d+)'/", $r->getContent(), $m);
            $ids = array_map('intval', $m[1]);
            $this->assertNotEmpty($ids, "la agencia {$agencia} no devolvio giros");

            $ajenos = DB::table('giro_economico_evaluacion')
                ->whereIn('id', $ids)
                ->where('idtienda', '!=', $agencia)
                ->pluck('id')
                ->all();

            $this->assertSame([], $ajenos, "la agencia {$agencia} ve giros ajenos");
        }
    }

    public function test_una_agencia_sin_permiso_es_rechazada(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $sin_permiso = null;
        foreach (DB::table('tienda')->where('idestado', 1)->pluck('id') as $id) {
            if (! in_array((int) $id, $this->agenciasPermitidas($u))) {
                $sin_permiso = (int) $id;
                break;
            }
        }

        if (! $sin_permiso) {
            $this->markTestSkipped('el usuario tiene permiso en todas');
        }

        $this->get("/backoffice/0/giroeconomico/show_table?idagencia={$sin_permiso}")->assertForbidden();
    }

    // ---------------------------------------------------------------
    // Alta / edicion / borrado acotados a la agencia
    // ---------------------------------------------------------------

    public function test_registra_en_la_agencia_seleccionada_y_no_en_la_de_la_ruta(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $permitidas = $this->agenciasPermitidas($u);
        $destino = end($permitidas);

        $antes = DB::table('giro_economico_evaluacion')->where('idtienda', $destino)->count();

        $r = $this->post('/backoffice/194/giroeconomico', [
            'view'                 => 'registrar',
            'idagencia'            => $destino,
            'nombre'               => 'GIRO PRUEBA '.uniqid(),
            'porcentaje'           => '12.34',
            'idtipo_giro_economico' => 1,
            'estado'               => 'HABILITADO',
        ]);

        $r->assertOk();
        $r->assertJson(['resultado' => 'CORRECTO']);

        $this->assertSame(
            $antes + 1,
            DB::table('giro_economico_evaluacion')->where('idtienda', $destino)->count(),
            "no se registro en la agencia {$destino}"
        );

        $nuevo = DB::table('giro_economico_evaluacion')
            ->where('idtienda', $destino)
            ->orderBy('id', 'desc')
            ->first();

        $this->assertSame($destino, (int) $nuevo->idtienda);
        $this->assertSame('12.34', (string) $nuevo->porcentaje);
    }

    public function test_el_formulario_de_editar_no_abre_un_giro_de_otra_agencia(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $mias = $this->agenciasPermitidas($u);
        $ajeno = DB::table('giro_economico_evaluacion')->where('idtienda', '!=', $mias[0])->first();
        $this->assertNotNull($ajeno);

        $this->get("/backoffice/194/giroeconomico/{$ajeno->id}/edit?view=editar&idagencia={$mias[0]}")
            ->assertNotFound();
    }

    public function test_el_update_rechaza_un_giro_de_otra_agencia(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $mias = $this->agenciasPermitidas($u);
        $ajeno = DB::table('giro_economico_evaluacion')->where('idtienda', '!=', $mias[0])->first();
        $porcentajeOriginal = $ajeno->porcentaje;

        $r = $this->put("/backoffice/194/giroeconomico/{$ajeno->id}", [
            'view'                 => 'editar',
            'idagencia'            => $mias[0],
            'nombre'               => $ajeno->nombre,
            'porcentaje'           => '77.77',
            'idtipo_giro_economico' => $ajeno->idtipo_giro_economico,
            'estado'               => $ajeno->estado,
        ]);

        $r->assertOk();
        $this->assertSame(
            (string) $porcentajeOriginal,
            (string) DB::table('giro_economico_evaluacion')->where('id', $ajeno->id)->value('porcentaje'),
            'se modifico un giro de otra agencia'
        );
    }

    public function test_el_borrado_no_alcanza_giros_de_otra_agencia(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $mias = $this->agenciasPermitidas($u);
        $ajeno = DB::table('giro_economico_evaluacion')->where('idtienda', '!=', $mias[0])->first();

        $this->delete("/backoffice/194/giroeconomico/{$ajeno->id}", [
            'view'      => 'eliminar',
            'idagencia' => $mias[0],
        ])->assertOk();

        $this->assertTrue(
            DB::table('giro_economico_evaluacion')->where('id', $ajeno->id)->exists(),
            'se borro un giro de otra agencia'
        );
    }

    // ---------------------------------------------------------------
    // Copia entre agencias
    // ---------------------------------------------------------------

    private function copiar(User $u, int $origen, int $destino)
    {
        return $this->put('/backoffice/194/giroeconomico/0', [
            'view'      => 'copiar',
            'idorigen'  => $origen,
            'iddestino' => $destino,
        ]);
    }

    public function test_copiar_replica_los_giros_con_su_margen(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        [$origen, $destino] = $this->agenciasPermitidas($u);

        $giro_origen = DB::table('giro_economico_evaluacion')->where('idtienda', $origen)->first();
        $antes = DB::table('giro_economico_evaluacion')->where('idtienda', $destino)->count();

        $r = $this->copiar($u, $origen, $destino);
        $r->assertOk();
        $r->assertJson(['resultado' => 'CORRECTO']);

        $this->assertGreaterThanOrEqual(
            $antes,
            DB::table('giro_economico_evaluacion')->where('idtienda', $destino)->count(),
            'la copia no puede reducir el catalogo del destino'
        );

        // cada giro del origen debe existir en destino con el mismo margen
        $faltantes = [];
        foreach (DB::table('giro_economico_evaluacion')->where('idtienda', $origen)->get() as $g) {
            $dest = DB::table('giro_economico_evaluacion')
                ->where('idtienda', $destino)
                ->where('idtipo_giro_economico', $g->idtipo_giro_economico)
                ->where('nombre', $g->nombre)
                ->first();

            if (! $dest) {
                $faltantes[] = $g->nombre;
                continue;
            }

            $this->assertSame(
                (string) $g->porcentaje,
                (string) $dest->porcentaje,
                "el margen de '{$g->nombre}' no quedo igual en destino"
            );
        }

        $this->assertSame([], $faltantes, 'giros del origen sin copiar');
    }

    public function test_copiar_crea_el_giro_que_el_destino_no_tiene(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        [$origen, $destino] = $this->agenciasPermitidas($u);

        $nombre = 'GIRO EXCLUSIVO '.uniqid();
        DB::table('giro_economico_evaluacion')->insert([
            'idtienda'              => $origen,
            'idtipo_giro_economico' => 3,
            'nombre'                => $nombre,
            'porcentaje'            => '44.00',
            'estado'                => 'HABILITADO',
        ]);

        $this->assertSame(0, DB::table('giro_economico_evaluacion')
            ->where('idtienda', $destino)->where('nombre', $nombre)->count());

        $r = $this->copiar($u, $origen, $destino);
        $r->assertOk();

        $copiado = DB::table('giro_economico_evaluacion')
            ->where('idtienda', $destino)
            ->where('nombre', $nombre)
            ->first();

        $this->assertNotNull($copiado, 'la copia no llevo el giro nuevo al destino');
        $this->assertSame('44.00', (string) $copiado->porcentaje);
        $this->assertSame(3, (int) $copiado->idtipo_giro_economico, 'debe heredar el tipo de giro');
    }

    public function test_copiar_dos_veces_no_duplica(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        [$origen, $destino] = $this->agenciasPermitidas($u);

        $this->copiar($u, $origen, $destino)->assertOk();
        $despues_primera = DB::table('giro_economico_evaluacion')->where('idtienda', $destino)->count();

        $this->copiar($u, $origen, $destino)->assertOk();
        $despues_segunda = DB::table('giro_economico_evaluacion')->where('idtienda', $destino)->count();

        $this->assertSame($despues_primera, $despues_segunda, 'copiar dos veces genero filas de mas');
    }

    public function test_copiar_actualiza_el_margen_de_un_giro_que_ya_existe(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        [$origen, $destino] = $this->agenciasPermitidas($u);

        $origen_giro = DB::table('giro_economico_evaluacion')->where('idtienda', $origen)->first();

        $destino_giro = DB::table('giro_economico_evaluacion')
            ->where('idtienda', $destino)
            ->where('idtipo_giro_economico', $origen_giro->idtipo_giro_economico)
            ->where('nombre', $origen_giro->nombre)
            ->first();
        $this->assertNotNull($destino_giro, 'el destino deberia tener el mismo giro por la migracion');

        DB::table('giro_economico_evaluacion')->where('id', $destino_giro->id)->update(['porcentaje' => '1.11']);

        $this->copiar($u, $origen, $destino)->assertOk();

        $this->assertSame(
            (string) $origen_giro->porcentaje,
            (string) DB::table('giro_economico_evaluacion')->where('id', $destino_giro->id)->value('porcentaje'),
            'la copia no sobrescribio el margen de un giro que ya existia'
        );
    }

    public function test_copiar_rechaza_origen_igual_a_destino(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $a = $this->agenciasPermitidas($u)[0];
        $this->copiar($u, $a, $a)->assertJson(['resultado' => 'ERROR']);
    }

    public function test_copiar_rechaza_una_agencia_sin_permiso(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $ajena = null;
        foreach (DB::table('tienda')->where('idestado', 1)->pluck('id') as $id) {
            if (! in_array((int) $id, $this->agenciasPermitidas($u))) {
                $ajena = (int) $id;
                break;
            }
        }

        if (! $ajena) {
            $this->markTestSkipped('el usuario tiene permiso en todas');
        }

        $permitida = $this->agenciasPermitidas($u)[0];

        $this->copiar($u, $permitida, $ajena)->assertForbidden();
        $this->copiar($u, $ajena, $permitida)->assertForbidden();
    }

    public function test_la_pantalla_de_copiar_abre(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $r = $this->get('/backoffice/194/giroeconomico/0/edit?view=copiar');
        $r->assertOk();
        $this->assertSinErrores($r->getContent(), 'el modal de copiar');

        $this->assertStringContainsString('idorigen', $r->getContent());
        $this->assertStringContainsString('iddestino', $r->getContent());
    }

    /**
     * Regresion del bug que reporto el usuario: al guardar la edicion de un giro,
     * la tabla se recargaba filtrada por el Tipo y Estado del formulario de
     * edicion, y como Servicio y Produccion tienen 2 giros cada uno (Comercio 14)
     * la lista quedaba con 2 filas y el resto del catalogo dejaba de verse.
     *
     * El filtro de la tabla ahora vive en FILTRO_LISTA_GIRO y no se relee del
     * formulario, asi que el listado sin filtro debe devolver el catalogo COMPLETO
     * de la agencia, y el de un tipo, solo los de ese tipo.
     */
    public function test_el_listado_sin_filtro_devuelve_el_catalogo_completo_de_la_agencia(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $agencia = $this->agenciasPermitidas($u)[0];

        // lo que pide lista_giro() cuando el usuario no filtro nada
        $r = $this->get("/backoffice/0/giroeconomico/show_table?idagencia={$agencia}");
        $r->assertOk();

        preg_match_all("/data-valor-columna='(\d+)'/", $r->getContent(), $m);
        $ids = array_map('intval', $m[1]);

        $this->assertCount(
            DB::table('giro_economico_evaluacion')->where('idtienda', $agencia)->count(),
            $ids,
            'el listado sin filtro debe traer todos los giros de la agencia, no solo los de un tipo'
        );
    }

    public function test_filtrar_por_tipo_trae_solo_ese_tipo(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $agencia = $this->agenciasPermitidas($u)[0];

        foreach ([1, 2, 3] as $tipo) {
            $r = $this->get("/backoffice/0/giroeconomico/show_table?idagencia={$agencia}&idtipo_giro_economico={$tipo}");
            $r->assertOk();

            preg_match_all("/data-valor-columna='(\d+)'/", $r->getContent(), $m);
            $ids = array_map('intval', $m[1]);

            $esperados = DB::table('giro_economico_evaluacion')
                ->where('idtienda', $agencia)
                ->where('idtipo_giro_economico', $tipo)
                ->count();

            $this->assertCount($esperados, $ids, "el filtro por tipo {$tipo} devolvio otra cosa");

            foreach ($ids as $id) {
                $this->assertSame(
                    $tipo,
                    (int) DB::table('giro_economico_evaluacion')->where('id', $id)->value('idtipo_giro_economico')
                );
            }
        }
    }

    // ---------------------------------------------------------------
    // Lo mas importante: el catalogo que ve el evaluador de un credito
    // ---------------------------------------------------------------

    public function test_el_evaluador_de_credito_solo_ofrece_giros_de_su_agencia(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);
        $agencia = idtienda_actual();

        foreach ([1, 2, 3] as $tipo) {
            $r = $this->getJson("/backoffice/0/credito/showgiroeconomico?tipogiro={$tipo}");
            $r->assertOk();

            $dec = json_decode($r->getContent(), true);
            if (! is_array($dec) || ! $dec) {
                continue;
            }

            $ajenos = DB::table('giro_economico_evaluacion')
                ->whereIn('id', array_column($dec, 'id'))
                ->where('idtienda', '!=', $agencia)
                ->pluck('id')
                ->all();

            $this->assertSame([], $ajenos, "el evaluador ofrece giros ajenos (tipo {$tipo})");
        }
    }

    public function test_el_margen_que_calcula_el_evaluador_es_el_de_su_agencia(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);
        $agencia = idtienda_actual();

        $propio = DB::table('giro_economico_evaluacion')->where('idtienda', $agencia)->first();
        $this->assertNotNull($propio);

        $r = $this->getJson("/backoffice/0/credito/showgiroeconomico_giro?giro={$propio->id}");
        $r->assertOk();

        $this->assertSame(
            (string) $propio->porcentaje,
            (string) $r->getContent(),
            'el margen devuelto no es el del giro de su agencia'
        );

        // un giro de otra agencia no debe devolver margen
        $ajeno = DB::table('giro_economico_evaluacion')->where('idtienda', '!=', $agencia)->first();
        $r2 = $this->getJson("/backoffice/0/credito/showgiroeconomico_giro?giro={$ajeno->id}");
        $this->assertSame('0', trim($r2->getContent()), 'devolvio el margen de un giro ajeno');
    }

    public function test_el_pdf_sale_de_la_agencia_seleccionada(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $permitidas = $this->agenciasPermitidas($u);
        $agencia = $permitidas[0];

        $r = $this->get("/backoffice/194/giroeconomico/0/edit?view=pdf&idagencia={$agencia}");
        $r->assertOk();
        $this->assertStringContainsString('%PDF', $r->getContent(), 'no se genero el PDF');
    }

    public function test_tipo_de_giro_sigue_siendo_global(): void
    {
        // regresion: el catalogo de tipos es comun y no debe duplicarse
        $this->assertSame(3, DB::table('tipo_giro_economico')->count());
        $this->assertSame(
            ['Comercio', 'Servicio', 'Produccion'],
            DB::table('tipo_giro_economico')->orderBy('id')->pluck('nombre')->all()
        );
    }

    public function test_los_creditos_historicos_siguen_resolviendo_su_giro(): void
    {
        $sin_resolver = DB::table('credito_evaluacion_cualitativa as c')
            ->leftJoin('giro_economico_evaluacion as g', 'g.id', '=', 'c.idgiro_economico_evaluacion')
            ->where('c.idgiro_economico_evaluacion', '>', 0)
            ->whereNull('g.id')
            ->count();

        $this->assertSame(0, $sin_resolver, 'hay creditos que ya no resuelven su giro');

        $totales = DB::table('credito_evaluacion_cualitativa')->where('idgiro_economico_evaluacion', '>', 0)->count();
        $resueltos = DB::table('credito_evaluacion_cualitativa as c')
            ->join('giro_economico_evaluacion as g', 'g.id', '=', 'c.idgiro_economico_evaluacion')
            ->count();

        $this->assertSame($totales, $resueltos);
    }
}
