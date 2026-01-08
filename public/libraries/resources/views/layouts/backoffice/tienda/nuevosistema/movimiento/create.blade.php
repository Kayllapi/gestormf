<form @include('app.nuevosistema.submit',['method'=>'POST','view'=>'registrar'])> 
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
          <select id="idmoneda">
            @foreach ($s_monedas as $value)
            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
            @endforeach
          </select>
          <label>Monto *</label>
          <input type="number" id="monto" placeholder="0.00" step="0.01">
      </div>
      <div class="col-sm-6">
          <label>Concepto *</label>
          <textarea id="concepto" cols="30" rows="10"></textarea>
      </div>
    </div>
    <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>
<script>
  $('#idconceptomovimiento').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
  });
  
  $('#idmoneda').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
  });
</script>