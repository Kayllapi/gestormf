<table class="tabla-detalle">
    <tr>
        <th colspan="3" style="background-color: #afaeae;">GENERAL</th>
    </tr>
    <tr>
      <td width="10px">CÓDIGO DE PRODUCTO</td>
      <td width="1px">:</td>
      <td>{{ $producto->codigo }}</td>
    </tr>
    <tr>
      <td>NOMBRE</td>
      <td>:</td>
      <td>{{ $producto->nombre }}</td>
    </tr>
    <tr>
      <td>PRECIO PUBLICO</td>
      <td>:</td>
      <td>{{ $producto->precioalpublico }}</td>
    </tr>
    <tr>
      <td>CATEGORIA</td>
      <td>:</td>
      <td>{{ $producto->categoria1nombre }}</td>
    </tr>
    <tr>
      <td>MARCA</td>
      <td>:</td>
      <td>{{ $producto->marcanombre }}</td>
    </tr>
    <tr>
      <td>STOCK MÍNIMO</td>
      <td>:</td>
      <td>{{ $producto->stockminimo }}</td>
    </tr>
    <tr>
      <td>VENCIMIENTO</td>
      <td>:</td>
      <td>{{ $producto->alertavencimiento }}</td>
    </tr>
    <tr>

      <td>ESTADO DE DETALLE</td>
      <td>:</td>
        @if ($producto->s_idestadodetalle == 1)
          <td>Activado</td>
        @elseif($producto->s_idestadodetalle == 2)
              <td>Desactivado</td>
        @endif

    </tr>
    <tr>
      <td>ESTADO</td>
      <td>:</td>
        @if ($producto->s_idestado == 1)
          <td>ACTIVADO</td>
        @elseif($producto->s_idestado == 2)
              <td>DESACTIVADO</td>
        @endif
    </tr>
    <tr>
      <td>ESTADO TIENDA VIRTUAL</td>
      <td>:</td>
        @if ($producto->s_idestadotiendavirtual == 1)
          <td>ACTIVADO</td>
        @elseif($producto->s_idestadotiendavirtual == 2)
              <td>DESACTIVADO</td>
        @endif
    </tr>
</table>
