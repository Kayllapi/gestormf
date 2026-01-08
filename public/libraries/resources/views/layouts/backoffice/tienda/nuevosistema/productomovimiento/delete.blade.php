<form @include('app.nuevosistema.submit',['method'=>'DELETE','view'=>'eliminar','id'=>$s_productomovimiento->id])>
    <table class="tabla-detalle">
      <tr>
        <th colspan="3" style="background-color: #afaeae;">GENERAL</th>
      </tr>
      <tr>
        <td width="10px">FECHA DE REGISTRO</td>
        <td width="1px">:</td>
        <td>{{ $s_productomovimiento->fecharegistro != null ? date_format(date_create($s_productomovimiento->fecharegistro), 'd/m/Y - h:i:s A' ) : '---' }}</td>
      </tr>
      <tr>
        <td>FECHA DE CONFIRMACION</td>
        <td>:</td>
        <td>{{ $s_productomovimiento->fechaconfirmacion != null ? date_format(date_create($s_productomovimiento->fechaconfirmacion), 'd/m/Y - h:i:s A' ) : '---' }}</td>
      </tr>
      <tr>
        <td>FECHA ELIMINADO</td>
        <td>:</td>
        <td>{{ $s_productomovimiento->fechaeliminado != null ? date_format(date_create($s_productomovimiento->fechaeliminado), 'd/m/Y - h:i:s A' ) : '---' }}</td>
      </tr>
      <tr>
        <td>MOTIVO</td>
        <td>:</td>
        <td>{{ $s_productomovimiento->motivo }}</td>
      </tr>
      <tr>
        <td>CANTIDAD</td>
        <td>:</td>
        <td>{{ $s_productomovimiento->cantidad }}</td>
      </tr>
</table>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro de eliminar?</b>
    </div>
    <button type="submit" class="btn mx-btn-post">Eliminar</button>
</form>
<!-- <script>
  $('#s_idtipomovimiento').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
  }).val({{ $s_productomovimiento->s_idtipomovimiento }}).trigger('change');
  
  $('#s_idestado').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
  }).val({{ $s_productomovimiento->s_idestado }}).trigger('change');
</script> -->