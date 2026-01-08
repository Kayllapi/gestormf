<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span><b>Piso {{ str_pad($piso->nombre, 2, "0", STR_PAD_LEFT) }}</b> / Editar Ambiente</span>
      <a class="btn btn-success" href="javascript:;" onclick="index_ambiente({{ $tienda->id }}, {{ $piso->id }})"><i class="fa fa-angle-left"></i> Atras</a>
    </div>
</div>
<div id="form-editar-ambiente">
  <form action="javascript:;" 
        onsubmit="callback({
            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion/0',
            method: 'PUT',
            carga: '#form-editar-ambiente',
            data:   {
                view: 'comida-editar-ambiente',
                idambiente: {{ $ambiente->id }}
            }
        },
        function(resultado){
          index_ambiente({{ $tienda->id }}, {{ $piso->id }})
        },this)">
      <div class="row">
          <div class="col-sm-6">
              <label>Número *</label>
              <input type="number" id="nombre" value="{{ $ambiente->nombre }}">
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
  }).val({{ $ambiente->idestado }}).trigger('change');
</script>