<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use DB;

/**
 * Filtro por agencia y copia entre agencias en la pantalla de Tasas Activas.
 *
 * Usa DatabaseTransactions porque estos tests registran, editan y copian
 * tarifarios de verdad sobre la base de desarrollo: todo se revierte al final.
 */
class TarifarioCopiaAgenciaTest extends TestCase
{
    use DatabaseTransactions;

    /** JAQ (6277) tiene permiso en 194 y en 198, con 194 activa */
    private function usuarioMultiagencia(): User
    {
        $u = User::find(6277);
        $this->assertNotNull($u, 'se esperaba el usuario 6277');
        $this->assertNotEmpty($this->agenciasDe($u), 'el usuario debe tener agencias');

        return $u;
    }

    private function agenciasDe(User $u)
    {
        return DB::table('users_permiso')
            ->where('idusers', $u->id)
            ->where('idestado', 1)
            ->distinct()
            ->pluck('idtienda')
            ->map(fn ($i) => (int) $i)
            ->all();
    }

    /** las agencias que el usuario puede ver en esta pantalla */
    private function agenciasPermitidas(User $u)
    {
        return DB::table('tienda')
            ->where('idestado', 1)
            ->whereIn('id', $this->agenciasDe($u))
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

        $r = $this->get('/backoffice/194/tarifario?view=tabla');
        $r->assertOk();
        $this->assertSinErrores($r->getContent(), 'la pantalla de tarifario');

        $esperadas = $this->agenciasPermitidas($u);

        // cada agencia permitida aparece como option en el selector
        foreach ($esperadas as $id) {
            $this->assertStringContainsString(
                'value="'.$id.'"',
                $r->getContent(),
                "la agencia {$id} deberia estar en el selector"
            );
        }

        // y solo esas: la 199 no tiene permiso este usuario
        if (! in_array(199, $esperadas)) {
            $this->assertStringNotContainsString(
                'value="199"',
                $r->getContent(),
                'la 199 no deberia aparecer si el usuario no tiene permiso'
            );
        }
    }

    public function test_cambiar_de_agencia_en_el_selector_muestra_su_tarifario(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $permitidas = $this->agenciasPermitidas($u);
        $this->assertGreaterThanOrEqual(2, count($permitidas), 'se necesitan 2 agencias para esta prueba');

        foreach ($permitidas as $agencia) {
            $r = $this->get("/backoffice/0/tarifario/showtarifario?idagencia={$agencia}");
            $r->assertOk();

            preg_match_all("/data-valor-columna='(\d+)'/", $r->getContent(), $m);
            $ids = array_map('intval', $m[1]);
            $this->assertNotEmpty($ids, "la agencia {$agencia} no devolvio tarifarios");

            $ajenos = DB::table('tarifario')
                ->whereIn('id', $ids)
                ->where('idtienda', '!=', $agencia)
                ->pluck('id')
                ->all();

            $this->assertSame([], $ajenos, "la agencia {$agencia} ve tarifarios ajenos");
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
            $this->markTestSkipped('el usuario tiene permiso en todas las agencias');
        }

        $this->get("/backoffice/0/tarifario/showtarifario?idagencia={$sin_permiso}")->assertForbidden();
    }

    // ---------------------------------------------------------------
    // Alta / edicion sobre la agencia seleccionada
    // ---------------------------------------------------------------

    public function test_registra_en_la_agencia_seleccionada_y_no_en_la_de_la_ruta(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $permitidas = $this->agenciasPermitidas($u);
        $destino = end($permitidas);          // la distinta de la ruta
        $producto = DB::table('credito_prendatario')->where('idtienda', $destino)->first();
        $this->assertNotNull($producto, "la agencia {$destino} no tiene productos");

        $antes = DB::table('tarifario')->where('idtienda', $destino)->count();

        // la ruta dice 194 pero el selector manda 198
        $r = $this->post('/backoffice/194/tarifario', [
            'view'                 => 'registrar',
            'idagencia'            => $destino,
            'idforma_credito'      => $producto->idforma_credito,
            'idcredito_prendatario' => $producto->id,
            'idforma_pago_credito' => 4,
            'monto'                => '12345.00',
            'cuotas'               => '7',
            'tem'                  => '9.99',
            'cargos_otros'         => '1.50',
        ]);

        $r->assertOk();
        $r->assertJson(['resultado' => 'CORRECTO']);

        $despues = DB::table('tarifario')->where('idtienda', $destino)->count();
        $this->assertSame($antes + 1, $despues, "no se registro en la agencia {$destino}");

        $nueva = DB::table('tarifario')
            ->where('idtienda', $destino)
            ->where('monto', '12345.00')
            ->first();

        $this->assertNotNull($nueva, 'la tasa nueva no aparece en la agencia destino');
        $this->assertSame($destino, (int) $nueva->idtienda);
        $this->assertSame((int) $producto->id, (int) $nueva->idcredito_prendatario);
    }

    public function test_no_registra_con_un_producto_de_otra_agencia(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $permitidas = $this->agenciasPermitidas($u);
        $destino = $permitidas[0];
        $producto_ajeno = DB::table('credito_prendatario')->where('idtienda', '!=', $destino)->first();
        $this->assertNotNull($producto_ajeno);

        $antes = DB::table('tarifario')->count();

        $r = $this->post('/backoffice/194/tarifario', [
            'view'                 => 'registrar',
            'idagencia'            => $destino,
            'idforma_credito'      => 1,
            'idcredito_prendatario' => $producto_ajeno->id,
            'idforma_pago_credito' => 4,
            'monto'                => '555.00',
            'cuotas'               => '3',
            'tem'                  => '5.00',
            'cargos_otros'         => '0',
        ]);

        $r->assertOk();
        $r->assertJson(['resultado' => 'ERROR']);

        $this->assertSame($antes, DB::table('tarifario')->count(), 'se registro con un producto ajeno');
    }

    public function test_el_formulario_de_editar_no_abre_una_tasa_de_otra_agencia(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $permitidas = $this->agenciasPermitidas($u);
        $ajena = DB::table('tarifario')->where('idtienda', '!=', $permitidas[0])->first();
        $this->assertNotNull($ajena);

        $this->get("/backoffice/194/tarifario/{$ajena->id}/edit?view=editar&idagencia={$permitidas[0]}")
            ->assertNotFound();
    }

    // ---------------------------------------------------------------
    // El listado de la tabla
    // ---------------------------------------------------------------

    /**
     * Regresion: la tabla tomaba sus filtros del formulario. Al editar una tasa,
     * el formulario de edicion traia su propio Producto, Tipo de Credito y Forma
     * de Pago, y al guardar la tabla se recargaba filtrada por esa tasa: solo
     * aparecia esa fila y el resto del tarifario de la agencia quedaba
     * invisible. El filtro ahora vive en FILTRO_LISTA_TARIFARIO y el listado sin
     * filtro debe traer el tarifario completo de la agencia.
     */
    public function test_el_listado_sin_filtro_devuelve_el_tarifario_completo_de_la_agencia(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        foreach ($this->agenciasPermitidas($u) as $agencia) {
            $r = $this->get("/backoffice/0/tarifario/showtarifario?idagencia={$agencia}");
            $r->assertOk();

            preg_match_all("/data-valor-columna='(\d+)'/", $r->getContent(), $m);
            $ids = array_map('intval', $m[1]);

            $this->assertCount(
                DB::table('tarifario')->where('idtienda', $agencia)->count(),
                $ids,
                "la agencia {$agencia} deberia ver su tarifario completo sin filtros"
            );
        }
    }

    public function test_filtrar_por_producto_trae_solo_las_tasas_de_ese_producto(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $agencia = $this->agenciasPermitidas($u)[0];

        // un producto que tenga varias tasas, para que el filtro se note
        $producto = DB::table('tarifario')
            ->where('idtienda', $agencia)
            ->select('idcredito_prendatario', DB::raw('count(*) c'))
            ->groupBy('idcredito_prendatario')
            ->orderByDesc('c')
            ->first();
        $this->assertNotNull($producto);

        $r = $this->get("/backoffice/0/tarifario/showtarifario?idagencia={$agencia}&idcredito_prendatario={$producto->idcredito_prendatario}");
        $r->assertOk();

        preg_match_all("/data-valor-columna='(\d+)'/", $r->getContent(), $m);
        $ids = array_map('intval', $m[1]);

        $this->assertCount((int) $producto->c, $ids, 'el filtro por producto devolvio otra cantidad');

        foreach ($ids as $id) {
            $this->assertSame(
                (int) $producto->idcredito_prendatario,
                (int) DB::table('tarifario')->where('id', $id)->value('idcredito_prendatario'),
                'el filtro por producto trajo una tasa de otro producto'
            );
        }
    }

    // ---------------------------------------------------------------
    // Copia entre agencias
    // ---------------------------------------------------------------

    /** payload valido para el PUT de copia */
    private function copiar(User $u, int $origen, int $destino)
    {
        return $this->put('/backoffice/194/tarifario/0', [
            'view'      => 'copiar',
            'idorigen'  => $origen,
            'iddestino' => $destino,
        ]);
    }

    public function test_copiar_replica_las_tasas_remapeando_los_productos(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $permitidas = $this->agenciasPermitidas($u);
        [$origen, $destino] = $permitidas;

        $tasa_origen = DB::table('tarifario')->where('idtienda', $origen)->first();
        $this->assertNotNull($tasa_origen);

        $antes_destino = DB::table('tarifario')->where('idtienda', $destino)->count();

        $r = $this->copiar($u, $origen, $destino);
        $r->assertOk();
        $r->assertJson(['resultado' => 'CORRECTO']);

        $nombre_origen = DB::table('credito_prendatario')
            ->where('id', $tasa_origen->idcredito_prendatario)->value('nombre');

        $producto_destino = DB::table('credito_prendatario')
            ->where('idtienda', $destino)
            ->where('nombre', $nombre_origen)
            ->first();
        $this->assertNotNull($producto_destino, 'el destino deberia tener el producto equivalente');

        // se busca la tasa del DESTINO: mismo producto (el del destino), misma
        // forma de pago, mismo monto y mismas cuotas que la de origen.
        $copiada = DB::table('tarifario')
            ->where('idtienda', $destino)
            ->where('idcredito_prendatario', $producto_destino->id)
            ->where('idforma_pago_credito', $tasa_origen->idforma_pago_credito)
            ->where('monto', $tasa_origen->monto)
            ->where('cuotas', $tasa_origen->cuotas)
            ->get();

        $this->assertNotEmpty($copiada, 'la tasa de origen no aparecio en el destino');

        foreach ($copiada as $fila) {
            $this->assertSame(
                (int) $producto_destino->id,
                (int) $fila->idcredito_prendatario,
                'la tasa copiada no apunta al producto del destino'
            );
            $this->assertSame(
                (string) $tasa_origen->tem,
                (string) $fila->tem,
                'la tasa copiada no heredo el TEM del origen'
            );
        }

        // Semantica de la copia: agrega y actualiza, no reemplaza. Como la
        // migracion dejo ambas agencias con los mismos combos, casi todo se
        // actualiza y el destino no crece; lo que no puede pasar es que el
        // destino pierda filas o le falte alguna tasa del origen.
        $this->assertGreaterThanOrEqual(
            $antes_destino,
            DB::table('tarifario')->where('idtienda', $destino)->count(),
            'la copia no puede reducir el tarifario del destino'
        );

        // toda tasa del origen debe existir en el destino con su producto mapeado
        $faltantes = 0;
        foreach (DB::table('tarifario')->where('idtienda', $origen)->get() as $t) {
            $nombre = DB::table('credito_prendatario')->where('id', $t->idcredito_prendatario)->value('nombre');
            $prod_destino = DB::table('credito_prendatario')->where('idtienda', $destino)->where('nombre', $nombre)->first();
            if (! $prod_destino) {
                continue;
            }

            $existe = DB::table('tarifario')
                ->where('idtienda', $destino)
                ->where('idcredito_prendatario', $prod_destino->id)
                ->where('idforma_pago_credito', $t->idforma_pago_credito)
                ->where('monto', $t->monto)
                ->where('cuotas', $t->cuotas)
                ->exists();

            if (! $existe) {
                $faltantes++;
            }
        }

        $this->assertSame(0, $faltantes, "quedaron {$faltantes} tasas del origen sin copiar al destino");
    }

    public function test_copiar_no_deja_tarifarios_cruzados_ni_duplicados(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $permitidas = $this->agenciasPermitidas($u);
        [$origen, $destino] = $permitidas;

        $this->copiar($u, $origen, $destino)->assertOk();

        // cada tasa del destino apunta a un producto del destino
        $cruzados = DB::table('tarifario as t')
            ->join('credito_prendatario as p', 'p.id', '=', 't.idcredito_prendatario')
            ->where('t.idtienda', $destino)
            ->where(fn ($q) => $q->where('p.idtienda', '!=', $destino)->orWhereNull('p.idtienda'))
            ->count();
        $this->assertSame(0, $cruzados, 'hay tarifas del destino apuntando a productos ajenos');

        // copiar dos veces no duplica: el combo es la clave
        $this->copiar($u, $origen, $destino)->assertOk();

        $dup = DB::table('tarifario')
            ->where('idtienda', $destino)
            ->select('idcredito_prendatario', 'idforma_pago_credito', 'monto', 'cuotas', DB::raw('count(*) c'))
            ->groupBy('idcredito_prendatario', 'idforma_pago_credito', 'monto', 'cuotas')
            ->havingRaw('count(*) > 1')
            ->get();

        // solo se admiten los duplicados que ya venian del origen
        $dup_heredados = DB::table('tarifario')
            ->where('idtienda', $origen)
            ->select('idcredito_prendatario', 'idforma_pago_credito', 'monto', 'cuotas', DB::raw('count(*) c'))
            ->groupBy('idcredito_prendatario', 'idforma_pago_credito', 'monto', 'cuotas')
            ->havingRaw('count(*) > 1')
            ->get()
            ->count();

        $this->assertLessThanOrEqual(
            $dup_heredados,
            $dup->count(),
            'copiar dos veces genero duplicados nuevos en el destino'
        );
    }

    public function test_copiar_actualiza_las_tasas_que_el_destino_ya_tenia(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $permitidas = $this->agenciasPermitidas($u);
        [$origen, $destino] = $permitidas;

        $tasa_origen = DB::table('tarifario')->where('idtienda', $origen)->first();
        $nombre = DB::table('credito_prendatario')->where('id', $tasa_origen->idcredito_prendatario)->value('nombre');
        $producto_destino = DB::table('credito_prendatario')->where('idtienda', $destino)->where('nombre', $nombre)->first();

        $existente = DB::table('tarifario')
            ->where('idtienda', $destino)
            ->where('idcredito_prendatario', $producto_destino->id)
            ->where('idforma_pago_credito', $tasa_origen->idforma_pago_credito)
            ->where('monto', $tasa_origen->monto)
            ->where('cuotas', $tasa_origen->cuotas)
            ->first();
        $this->assertNotNull($existente, 'el destino deberia tener el mismo combo por la migracion');

        // se altera el TEM del destino para comprobar que la copia lo sobrescribe
        DB::table('tarifario')->where('id', $existente->id)->update(['tem' => '99.11']);

        $r = $this->copiar($u, $origen, $destino);
        $r->assertOk();
        $r->assertJson(['resultado' => 'CORRECTO']);

        $this->assertSame(
            (string) $tasa_origen->tem,
            (string) DB::table('tarifario')->where('id', $existente->id)->value('tem'),
            'la copia no actualizo el TEM de una tasa que ya existia'
        );
    }

    public function test_copiar_crea_el_producto_si_el_destino_no_lo_tiene(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $permitidas = $this->agenciasPermitidas($u);
        [$origen, $destino] = $permitidas;

        // Se crea un producto que existe SOLO en el origen, con su tasa, para que
        // la copia tenga que resolverlo: no hay equivalente en destino.
        $nombre = 'Producto Exclusivo Prueba '.uniqid();

        $origen_id = DB::table('credito_prendatario')->insertGetId([
            'idtienda'            => $origen,
            'nombre'              => $nombre,
            'idtipo_credito'      => 0,
            'modalidad'           => 'INTERES SIMPLE',
            'garantiaprendatario' => 'SI',
            'estado'              => 'ACTIVO',
            'conevaluacion'       => 'NO',
            'idforma_credito'     => 1,
        ]);
        DB::table('credito_prendatario')->where('id', $origen_id)
            ->update(['codigo' => 'CP'.str_pad($origen_id, 4, '0', STR_PAD_LEFT)]);

        DB::table('tarifario')->insert([
            'idtienda'              => $origen,
            'idforma_credito'       => 1,
            'idcredito_prendatario' => $origen_id,
            'idforma_pago_credito'  => 4,
            'monto'                 => '777.00',
            'cuotas'                => '5',
            'tem'                   => '13.25',
            'cargos_otros'          => '2.00',
        ]);

        $this->assertSame(
            0,
            DB::table('credito_prendatario')->where('idtienda', $destino)->where('nombre', $nombre)->count(),
            'el producto deberia ser exclusivo del origen antes de copiar'
        );

        $antes = DB::table('credito_prendatario')->where('idtienda', $destino)->count();

        $r = $this->copiar($u, $origen, $destino);
        $r->assertOk();
        $r->assertJson(['resultado' => 'CORRECTO']);

        $this->assertSame(
            $antes + 1,
            DB::table('credito_prendatario')->where('idtienda', $destino)->count(),
            'la copia deberia haber creado el producto que faltaba en destino'
        );

        $copiado = DB::table('credito_prendatario')
            ->where('idtienda', $destino)
            ->where('nombre', $nombre)
            ->first();

        $this->assertNotNull($copiado);
        $this->assertSame(1, (int) $copiado->idforma_credito, 'debe heredar la forma de credito');
        $this->assertSame('INTERES SIMPLE', $copiado->modalidad, 'debe heredar la modalidad');
        $this->assertSame('ACTIVO', $copiado->estado);
        $this->assertNotEmpty($copiado->codigo, 'debe recibir codigo propio de destino');

        // y la tasa tambien debe haberse copiado apuntando a ese producto nuevo
        $tasa = DB::table('tarifario')
            ->where('idtienda', $destino)
            ->where('idcredito_prendatario', $copiado->id)
            ->where('monto', '777.00')
            ->first();

        $this->assertNotNull($tasa, 'la tasa del producto nuevo no se copio');
        $this->assertSame('13.25', (string) $tasa->tem);
    }

    public function test_copiar_nunca_borra_productos_ni_tarifarios(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $permitidas = $this->agenciasPermitidas($u);
        [$origen, $destino] = $permitidas;

        $productos_antes = DB::table('credito_prendatario')->where('idtienda', $destino)->count();
        $tarifarios_antes = DB::table('tarifario')->where('idtienda', $destino)->count();
        $creditos_antes = DB::table('credito')->count();

        $this->copiar($u, $origen, $destino)->assertOk();

        $this->assertSame($productos_antes, DB::table('credito_prendatario')->where('idtienda', $destino)->count()
            - $this->productosFaltantes($origen, $destino), 'la copia borro productos del destino');

        $this->assertGreaterThanOrEqual($tarifarios_antes, DB::table('tarifario')->where('idtienda', $destino)->count());
        $this->assertSame($creditos_antes, DB::table('credito')->count(), 'la copia toco el historico de creditos');
    }

    /** cuantos productos del origen no existian en el destino (se crearan al copiar) */
    private function productosFaltantes(int $origen, int $destino): int
    {
        return DB::table('credito_prendatario')
            ->where('idtienda', $origen)
            ->whereNotIn('nombre', function ($q) use ($destino) {
                $q->select('nombre')->from('credito_prendatario')->where('idtienda', $destino);
            })
            ->count();
    }

    public function test_copiar_rechaza_origen_igual_a_destino(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $a = $this->agenciasPermitidas($u)[0];

        $r = $this->copiar($u, $a, $a);
        $r->assertOk();
        $r->assertJson(['resultado' => 'ERROR']);
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

    public function test_la_pantalla_de_copiar_pide_dos_agencias(): void
    {
        $u = $this->usuarioMultiagencia();
        $this->actingAs($u);

        $r = $this->get('/backoffice/194/tarifario/0/edit?view=copiar');
        $r->assertOk();
        $this->assertSinErrores($r->getContent(), 'el modal de copiar');

        $this->assertStringContainsString('idorigen', $r->getContent());
        $this->assertStringContainsString('iddestino', $r->getContent());
    }
}
