<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use DB;

/**
 * Verifica que el tarifario y los productos de credito esten aislados por
 * agencia: cada agencia solo lee y escribe la suya.
 */
class TarifarioPorAgenciaTest extends TestCase
{
    /**
     * Usuario activo con un cargo asignado en esa agencia.
     *
     * La agencia activa sale de users_permiso.idtienda (es lo que devuelve
     * user_permiso() y por lo tanto idtienda_actual()), NO de users.idtienda:
     * un mismo usuario puede tener cargo en varias agencias.
     */
    private function usuarioDeAgencia(int $idtienda): ?User
    {
        $idusers = DB::table('users_permiso')
            ->where('idtienda', $idtienda)
            ->orderBy('idsession', 'desc') // el cargo activo es idsession = 2
            ->value('idusers');

        if (!$idusers) {
            return null;
        }

        return User::where('id', $idusers)->where('idestadousuario', 1)->first();
    }

    /**
     * El invariante real: las filas que devuelve la pantalla deben pertenecer
     * SIEMPRE a la agencia que la app resolvió para ese usuario
     * (idtienda_actual()), nunca a otra.
     *
     * No se puede fijar la agencia esperada con antemano: un usuario puede
     * tener cargo en varias agencias y la que manda es la de idsession = 2.
     */
    public function test_cada_usuario_solo_ve_el_tarifario_de_su_agencia_activa(): void
    {
        $probados = 0;

        foreach ($this->usuariosConCargo() as $u) {
            $this->actingAs($u);

            $r = $this->get('/backoffice/0/tarifario/showtarifario');
            $r->assertOk();

            preg_match_all("/data-valor-columna='(\d+)'/", $r->getContent(), $m);
            $ids = array_map('intval', $m[1]);

            if (! $ids) {
                continue; // usuario sin tarifario en su agencia
            }

            $esperada = idtienda_actual();
            $this->assertContains($esperada, [194, 198, 199]);

            $ajenos = DB::table('tarifario')
                ->whereIn('id', $ids)
                ->where('idtienda', '!=', $esperada)
                ->pluck('id')
                ->all();

            $this->assertSame(
                [],
                $ajenos,
                "{$u->usuario} (agencia activa {$esperada}) ve tarifarios ajenos: ".implode(',', $ajenos)
            );

            $probados++;
        }

        $this->assertGreaterThan(0, $probados, 'no se probo ningun usuario con tarifario');
    }

    /**
     * Los dropdowns de producto de los simuladores y del credito deben listar
     * solo el catalogo de la agencia: si no, el usuario ve productos de otras
     * agencias y al elegir uno no encuentra tarifario propio.
     */
    public function test_los_dropdowns_de_producto_muestran_solo_la_agencia(): void
    {
        $usuario = $this->usuarioDeAgenciaActiva();
        $agencia = idtienda_actual();

        $endpoints = [
            'calculosimple'   => ['modalidad' => 'PRENDARIO', 'tipo' => ''],
            'calculocompuesto' => ['modalidad' => 'PRENDARIO', 'tipo' => ''],
        ];

        foreach ($endpoints as $controlador => $params) {
            $r = $this->get("/backoffice/0/{$controlador}/show_producto_credito?modalidad={$params['modalidad']}");
            $r->assertOk("el dropdown de {$controlador} no respondio 200");

            $dec = json_decode($r->getContent(), true);
            if (! is_array($dec) || ! $dec) {
                continue; // ese filtro de modalidad no tiene productos
            }

            $ajenos = DB::table('credito_prendatario')
                ->whereIn('id', array_column($dec, 'id'))
                ->where('idtienda', '!=', $agencia)
                ->pluck('id')
                ->all();

            $this->assertSame([], $ajenos, "el dropdown de {$controlador} lista productos ajenos");
        }

        // el dropdown del credito, que ademas filtra por forma de credito
        foreach ([1, 2] as $tipo) {
            $r = $this->get("/backoffice/0/credito/show_producto_credito?tipo={$tipo}");
            $r->assertOk("el dropdown de credito (tipo {$tipo}) no respondio 200");

            $dec = json_decode($r->getContent(), true);
            if (! is_array($dec) || ! $dec) {
                continue;
            }

            $ajenos = DB::table('credito_prendatario')
                ->whereIn('id', array_column($dec, 'id'))
                ->where('idtienda', '!=', $agencia)
                ->pluck('id')
                ->all();

            $this->assertSame([], $ajenos, "el dropdown de credito (tipo {$tipo}) lista productos ajenos");

            // y debe traer una cantidad parecida a la de la agencia, no 3x
            $this->assertLessThanOrEqual(
                DB::table('credito_prendatario')->where('idtienda', $agencia)->count(),
                count($dec),
                "el dropdown de credito (tipo {$tipo}) trae mas productos que los de la agencia"
            );
        }
    }

    /** usuarios activos que tienen algun cargo asignado */
    private function usuariosConCargo(): array
    {
        $ids = DB::table('users_permiso')->distinct()->pluck('idusers');

        return User::whereIn('id', $ids)->where('idestadousuario', 1)->orderBy('id')->get()->all();
    }

    public function test_los_codigos_de_otras_agencias_no_aparecen(): void
    {
        // un usuario cuyo cargo activo es la 194
        $usuario = null;
        foreach ($this->usuariosConCargo() as $u) {
            $this->actingAs($u);
            if (idtienda_actual() === 194) {
                $usuario = $u;
                break;
            }
        }

        if (! $usuario) {
            $this->markTestSkipped('ningun usuario activo tiene cargo en la 194');
        }

        $r = $this->actingAs($usuario)
            ->getJson('/backoffice/0/tarifario/showproductocredito?tipo=1');

        $r->assertOk();
        $json = $r->getContent();

        $this->assertStringContainsString('CP0002', $json, 'debe ver su propio producto');
        $this->assertStringNotContainsString('CP1002', $json, 'no debe ver el producto de la 198');
        $this->assertStringNotContainsString('CP2002', $json, 'no debe ver el producto de la 199');
    }

    public function test_cada_usuario_solo_ve_los_productos_de_su_agencia(): void
    {
        foreach ($this->usuariosConCargo() as $u) {
            $this->actingAs($u);

            $r = $this->get('/backoffice/0/creditoprendatario/showcreditos');
            $r->assertOk();

            preg_match_all("/data-valor-columna='(\d+)'/", $r->getContent(), $m);
            $ids = array_map('intval', $m[1]);

            if (! $ids) {
                continue;
            }

            $esperada = idtienda_actual();

            $ajenos = DB::table('credito_prendatario')
                ->whereIn('id', $ids)
                ->where('idtienda', '!=', $esperada)
                ->pluck('id')
                ->all();

            $this->assertSame(
                [],
                $ajenos,
                "{$u->usuario} (agencia {$esperada}) ve productos ajenos: ".implode(',', $ajenos)
            );
        }
    }

    public function test_no_se_puede_editar_un_tarifario_de_otra_agencia(): void
    {
        $usuario = $this->usuarioDeAgenciaActiva();
        $agencia = idtienda_actual();

        $ajeno = DB::table('tarifario')->where('idtienda', '!=', $agencia)->first();
        $this->assertNotNull($ajeno, 'no hay tarifario de otra agencia para probar');

        $r = $this->actingAs($usuario)
            ->get("/backoffice/{$agencia}/tarifario/{$ajeno->id}/edit?view=editar");

        // el GET de edicion no debe cargar el valor del tarifario ajeno
        $this->assertStringNotContainsString(
            'value="'.$ajeno->monto.'"',
            $r->getContent(),
            'el formulario de edicion cargo un tarifario de otra agencia'
        );
    }

    public function test_el_update_rechaza_un_tarifario_de_otra_agencia(): void
    {
        $usuario = $this->usuarioDeAgenciaActiva();
        $agencia = idtienda_actual();

        $ajeno = DB::table('tarifario')->where('idtienda', '!=', $agencia)->first();
        $temOriginal = $ajeno->tem;

        $r = $this->actingAs($usuario)
            ->put("/backoffice/{$agencia}/tarifario/".$ajeno->id, [
                'view'              => 'editar',
                'usuario'           => $usuario->usuario,
                'idestadousuario'   => $usuario->idestadousuario,
                'apellido_parterno' => $usuario->apellidopaterno,
                'apellido_marterno' => $usuario->apellidomaterno,
                'nombres'           => $usuario->nombre,
                'identificacion'    => $usuario->identificacion,
                'direccion'         => $usuario->direccion,
                'idubigeo'          => $usuario->idubigeo,
                'fecha_nacimiento'  => $usuario->fechanacimiento,
                'celular'           => $usuario->numerotelefono,
                'idestadodivil'     => $usuario->idestadocivil,
                'profesion'         => $usuario->profesion,
                'intentos_maximo'   => $usuario->intentos_maximo,
                'idforma_credito'       => $ajeno->idforma_credito,
                'idcredito_prendatario' => $ajeno->idcredito_prendatario,
                'idforma_pago_credito'  => $ajeno->idforma_pago_credito,
                'monto'                 => 999999,
                'cuotas'                => 99,
                'tem'                   => 99.99,
                'cargos_otros'          => 0,
            ]);

        $r->assertOk();

        // el TEM ajeno debe seguir igual
        $this->assertSame(
            (string) $temOriginal,
            (string) DB::table('tarifario')->where('id', $ajeno->id)->value('tem'),
            'se modifico un tarifario de otra agencia'
        );
    }

    /**
     * Regresion: el formulario de alta pide el catalogo de productos con la
     * agencia COCIDA en el HTML (la que se uso al renderizarlo), no con la que
     * hay seleccionada en el momento.
     *
     * Como el formulario se vuelve a pedir en cada cambio de agencia y las
     * peticiones no se cancelan entre si, el formulario de la agencia anterior
     * puede pintarse despues del nuevo: sus productos quedaban en pantalla
     * mientras la tabla consultaba la agencia nueva, y al elegir un producto
     * la tabla salia vacia (idagencia 198 con un producto de la 194).
     *
     * La agencia tiene que leerse de #idagencia en el momento de la peticion.
     */
    public function test_el_formulario_de_alta_consulta_la_agencia_del_selector(): void
    {
        $usuario = $this->usuarioDeAgenciaActiva();
        $agencia = idtienda_actual();

        $r = $this->actingAs($usuario)
            ->get("/backoffice/{$agencia}/tarifario/create?view=registrar&idagencia={$agencia}");
        $r->assertOk();

        $html = $r->getContent();

        $this->assertStringContainsString(
            'idagencia_tarifario()',
            $html,
            'el catalogo de productos debe pedir la agencia al selector, no al formulario'
        );

        // el valor cocido queda solo como respaldo: no debe ir en la peticion
        // del catalogo de productos ni en el alta de la tasa
        $this->assertDoesNotMatchRegularExpression(
            '/idagencia\s*:\s*'.$agencia.'\s*,/',
            $html,
            'el formulario sigue mandando la agencia cocida en vez de la del selector'
        );
    }

    /** primer usuario activo con cargo, para las pruebas que no dependen del id concreto */
    private function usuarioDeAgenciaActiva(): User
    {
        $usuarios = $this->usuariosConCargo();
        $this->assertNotEmpty($usuarios, 'no hay usuarios con cargo para probar');

        $this->actingAs($usuarios[0]);

        return $usuarios[0];
    }

    public function test_las_formas_de_pago_y_credito_siguen_siendo_globales(): void
    {
        // regresion: los ids de estos catalogos estan fijados por codigo y no
        // deben duplicarse por agencia
        $this->assertSame(2, DB::table('forma_credito')->count());
        $this->assertSame(4, DB::table('forma_pago_credito')->count());

        $this->assertSame(1, DB::table('forma_credito')->where('nombre', 'Prendario')->count());
        $this->assertSame(1, DB::table('forma_pago_credito')->where('nombre', 'Diario')->count());

        // y las 3 agencias deben seguir viendo las mismas 4 formas de pago
        $dias = DB::table('forma_pago_credito')->orderBy('id')->pluck('dias')->all();
        $this->assertCount(4, $dias);
    }
}
