<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Eliminar Piso</span>
      <a class="btn btn-success" href="javascript:;" onclick="index_piso()"><i class="fa fa-angle-left"></i> Atras</a>
    </div>
</div>
<div id="form-anular-piso">
  <form action="javascript:;" 
        onsubmit="callback({
            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion/0',
            method: 'PUT',
            carga: '#form-anular-piso',
            data:   {
                view: 'comida-anular-piso',
                idpiso: {{ $piso->id }}
            }
        },
        function(resultado){
          index_piso();
        },this)">
      <div class="row">
          <div class="col-sm-6">
              <label>Número</label>
              <input type="text" id="nombre" value="{{ $piso->nombre }}" disabled>
          </div>
          <div class="col-sm-6">
              <label>Estado</label>
              <select id="idestado" disabled>
                <option value="1">Activado</option>
                <option value="2">Desactivado</option>
              </select>
          </div>
      </div>
      <div class="mensaje-danger">
        <i class="fa fa-warning"></i> ¿Esta seguro Eliminar el Piso?
      </div>
      <button type="submit" class="btn mx-btn-post">Eliminar</button>
  </form>
</div>
<script>
  $('#idestado').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
  }).val({{ $piso->idestado }}).trigger('change');
</script>