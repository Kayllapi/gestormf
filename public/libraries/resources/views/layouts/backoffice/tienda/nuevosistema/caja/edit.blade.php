<form @include('app.nuevosistema.submit',['method'=>'PUT','view'=>'editar','id'=>$s_caja->id])>
    @if ($s_caja->s_idestado == 1)
      <div class="row">
        <div class="col-sm-12">
          <label>Nombre *</label>
          <input type="text" id="nombre" value="{{$s_caja->nombre}}"/>
          <label>Estado</label>
          <select id="s_idestado">
            <option value="1">Activado</option>
            <option value="2">Desactivado</option>
          </select>
        </div>
      </div>
      <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
    @else
      <div class="mensaje-warning">
        <i class="fa fa-warning"></i> ¡Debe estar en estado Activado!.</b>
      </div>
    @endif
</form>
<script>
  $('#s_idestado').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
  }).val({{ $s_caja->s_idestado }}).trigger('change');
</script>