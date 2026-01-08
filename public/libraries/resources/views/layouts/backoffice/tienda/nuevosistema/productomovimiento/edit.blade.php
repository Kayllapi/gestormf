<form @include('app.nuevosistema.submit',['method'=>'PUT','view'=>'editar','id'=>$s_productomovimiento->id])>
    @if ($s_productomovimiento->s_idestado == 1)
      <div class="row">
        <div class="col-sm-6">
          <label>Producto</label>
          <input type="text" value="{{ $s_productomovimiento->productocodigo }} - {{ $s_productomovimiento->productonombre }}" disabled>
          <label>Tipo Movimiento *</label>
          <select id="s_idtipomovimiento">
            @foreach ($tipomovimiento as $value)
            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
            @endforeach
          </select>
          <label>Motivo *</label>
          <input type="text" id="motivo" value="{{ $s_productomovimiento->motivo }}"/>
        </div>
        <div class="col-sm-6">
          <label>Cantidad *</label>
          <input type="text" id="cantidad" value="{{ $s_productomovimiento->cantidad }}"/>
          <label>Estado</label>
          <select id="s_idestado">
            <option value="1">Pendiente</option>
            <option value="2">Confirmado</option>
          </select>
        </div>
    </div>
    <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
  @else
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¡Debe estar en estado Pendiente!.</b>
    </div>
  @endif
</form>
<script>
  $('#s_idtipomovimiento').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
  }).val({{ $s_productomovimiento->s_idtipomovimiento }}).trigger('change');
  
  $('#s_idestado').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
  }).val({{ $s_productomovimiento->s_idestado }}).trigger('change');
</script>