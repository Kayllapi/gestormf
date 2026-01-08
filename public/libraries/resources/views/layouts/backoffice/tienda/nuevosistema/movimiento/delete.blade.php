<form @include('app.nuevosistema.submit',['method'=>'DELETE','view'=>'eliminar','id'=>$s_movimiento->id])>
    <table class="tabla-detalle">
      <tr>
        <th colspan="3" style="background-color: #afaeae;">GENERAL</th>
      </tr>
      <tr>
        <td width="10px">FECHA DE REGISTRO</td>
        <td width="1px">:</td>
        <td>{{ $s_movimiento->fecharegistro != null ? date_format(date_create($s_movimiento->fecharegistro), 'd/m/Y - h:i:s A' ) : '---' }}</td>
      </tr>
      <tr>
        <td width="10px">Fecha de Confirmacion</td>
        <td width="1px">:</td>
        <td>{{ $s_movimiento->fechaconfirmacion != null ? date_format(date_create($s_movimiento->fechaconfirmacion), 'd/m/Y - h:i:s A' ) : '---' }}</td>
      </tr>
      <tr>
        <td>TIPO</td>
        <td>:</td>
        <td>{{ $s_movimiento->tipo }}</td>
      </tr>
      <tr>
        <td>RESPONSABLE</td>
        <td>:</td>
        <td>{{ $s_movimiento->responsablenombre }}</td>
      </tr>
      <tr>
        <td>MONEDA</td>
        <td>:</td>
        <td>{{ $s_movimiento->monedanombre }} </td>
      </tr>
      <tr>
        <td>MONTO</td>
        <td>:</td>
        <td>{{ $s_movimiento->monto }} </td>
      </tr>
       <tr>
         <td>CONCEPTO</td>
         <td>:</td>
         <td>{{ $s_movimiento->concepto }} </td>
      </tr>
    </table>
  
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro de eliminar?</b>
    </div>
    <button type="submit" class="btn mx-btn-post">Eliminar</button>
</form>
<script>
  $('#s_idconceptomovimiento').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
  }).val({{ $s_movimiento->s_idconceptomovimiento }}).trigger('change');
  
  $('#s_idmoneda').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
  }).val({{ $s_movimiento->s_idmoneda }}).trigger('change');
</script>