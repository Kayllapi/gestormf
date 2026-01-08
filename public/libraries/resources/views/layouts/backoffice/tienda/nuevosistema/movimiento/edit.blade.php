<form @include('app.nuevosistema.submit',['method'=>'PUT','view'=>'editar','id'=>$s_movimiento->id])>
    @if ($s_movimiento->s_idestado != 1)
      <div class="row">
        <div class="col-sm-6">
            <label>Tipo *</label>
            <select id="idconceptomovimiento">
              <option></option>
              @foreach ($s_conceptomovimientos as $value)
              <option value="{{ $value->id }}">{{ $value->nombre }}</option>
              @endforeach
            </select>
            <label>Moneda *</label>
            <select id="s_idmoneda">
              @foreach ($s_monedas as $value)
              <option value="{{ $value->id }}">{{ $value->nombre }}</option>
              @endforeach
            </select>
            <label>Monto *</label>
            <input type="number" id="monto" value="{{ $s_movimiento->monto }}" step="0.01">
        </div>
        <div class="col-sm-6">
            <label>Concepto *</label>
            <textarea id="concepto" cols="30" rows="10">{{ $s_movimiento->concepto }}</textarea>
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
  $('#idconceptomovimiento').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
  }).val({{ $s_movimiento->s_idconceptomovimiento }}).trigger('change');
  
  $('#s_idmoneda').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
  }).val({{ $s_movimiento->s_idmoneda }}).trigger('change');
</script>