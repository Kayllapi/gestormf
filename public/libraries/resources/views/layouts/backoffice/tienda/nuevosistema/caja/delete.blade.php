<form @include('app.nuevosistema.submit',['method'=>'DELETE','view'=>'eliminar','id'=>$s_caja->id])>
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
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro de eliminar?</b>
    </div>
    <button type="submit" class="btn mx-btn-post">Eliminar</button>
</form>
<!-- <script>
  $('#s_idestado').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
  }).val({{ $s_caja->s_idestado }}).trigger('change');
</script> -->