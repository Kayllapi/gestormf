<?php

namespace Tests\Unit;

use App\Support\DireccionesNegocio;
use PHPUnit\Framework\TestCase;

class DireccionesNegocioTest extends TestCase
{
    public function test_compara_direcciones_con_variaciones_de_escritura(): void
    {
        $this->assertTrue(DireccionesNegocio::similares('Jr. Unión 123', 'JR UNION N 123'));
        $this->assertFalse(DireccionesNegocio::similares('Jr. Unión 123', 'Jr. Unión 124'));
        $this->assertFalse(DireccionesNegocio::similares('', 'Jr. Unión 123'));
        $this->assertFalse(DireccionesNegocio::similares('Jr. Unión 123', 'Av. Los Rosales 123'));
    }
}
