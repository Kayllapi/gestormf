<table class="tabla-detalle">
      <tr>
        <th colspan="3" style="background-color: #afaeae;">GENERAL</th>
      </tr>
      <tr>
        <td width="10%">FECHA DE REGISTRO</td>
        <td width="1px">:</td>
        <td>{{ $s_caja->fecharegistro != null ? date_format(date_create($s_caja->fecharegistro), 'd/m/Y - h:i:s A' ) : '---' }}</td>
      </tr>
      <tr>
        <td>NOMBRE</td>
        <td>:</td>
        <td>{{ $s_caja->nombre }}</td>
      </tr>
      <tr>
        <td>SALDO</td>
        <td>:</td>
        <td>{{ efectivocaja($tienda->id,$s_caja->id)['total'] }} </td>
      </tr>
</table>