<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Editar Piso</span>
      <a class="btn btn-success" href="javascript:;" onclick="index_piso()"><i class="fa fa-angle-left"></i> Atras</a>
    </div>
</div>
<div id="form-editar-piso">
  <form action="javascript:;" 
        onsubmit="callback({
            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion/0',
            method: 'PUT',
            carga: '#form-editar-piso',
            data:   {
                view: 'comida-editar-piso',
                idpiso: {{ $piso->id }}
            }
        },
        function(resultado){
          index_piso();
        },this)">
      <div class="row">
          <div class="col-sm-6">
              <label>Número *</label>
              <input type="number" id="nombre" value="{{ $piso->nombre }}">
          </div>
          <div class="col-sm-6">
              <label>Estado *</label>
              <select id="idestado">
                <option value="1">Activado</option>
                <option value="2">Desactivado</option>
              </select>
          </div>
      </div>
      <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
  </form>
</div>
<script>
  $('#idestado').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
  }).val({{ $piso->idestado }}).trigger('change');
</script>