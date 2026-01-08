  <table class="tabla-detalle">
       <tr>
        <th colspan="3" style="background-color: #afaeae;">GENERAL</th>
      </tr>
      <tr>
        <td width="10px">CAJA</td>
        <td width="1px">:</td>
        <td>{{ $s_aperturacierre->cajanombre }}</td>
      </tr>
      <tr>
        <td width="10px">MONTO ASIGNADO EN SOLES</td>
        <td width="1px">:</td>
        <td>{{ $s_aperturacierre->montoasignar }}</td>
      </tr>
      <tr>
        <td width="10px">MONTO ASIGNADO EN DOLARES</td>
        <td width="1px">:</td>
        <td>{{ $s_aperturacierre->montoasignar_dolares }}</td>
      </tr>
      <tr>
        <td width="10px">PERSONA ASIGNADO</td>
        <td width="1px">:</td>
        <td> {{ $s_aperturacierre->usersrecepcionapellidos }}, {{ $s_aperturacierre->usersrecepcionnombre }}</td>
      </tr>
      <tr>
        <td width="10px">PERSONA RESPONSABLE</td>
        <td width="1px">:</td>
        <td> {{ $s_aperturacierre->usersresponsableapellidos }}, {{ $s_aperturacierre->usersresponsablenombre }}</td>
      </tr>
      <tr>
        <td width="10px">FECHA DE APERTURA</td>
        <td width="1px">:</td>
        <td> {{ date_format(date_create($s_aperturacierre->fecharegistro),"d/m/Y h:i:s A") }}</td>
      </tr>
      <tr>
        <td width="10px">FECHA DE CONFIRMACION DE APERTURA</td>
        <td width="1px">:</td>
        <td> {{ date_format(date_create($s_aperturacierre->fechaconfirmacion),"d/m/Y h:i:s A") }}</td>
      </tr>
  </table>
