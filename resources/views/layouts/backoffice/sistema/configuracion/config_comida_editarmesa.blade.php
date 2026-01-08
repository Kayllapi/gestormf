<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
        <span><b>Piso {{ str_pad($piso->nombre, 2, "0", STR_PAD_LEFT) }} / Ambiente {{ str_pad($ambiente->nombre, 2, "0", STR_PAD_LEFT) }}</b> / Editar Mesa</span>
        <a class="btn btn-success" href="javascript:;" onclick="index_mesa({{ $tienda->id }},{{ $piso->id }},{{ $ambiente->id }})"><i class="fa fa-angle-left"></i> Atras</a>
    </div>
</div>
<div id="form-editar-mesa">
    <form action="javascript:;" 
          onsubmit="callback({
              route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion/0',
              method: 'PUT',
              carga: '#form-editar-mesa',
              data:   {
                  view: 'comida-editar-mesa',
                  idcomida_mesa: {{ $comidamesa->id }},
                  idambiente: {{ $ambiente->id }},
                  idpiso: {{ $piso->id }},
              }
          },
          function(resultado){
            index_mesa({{ $tienda->id }},{{ $piso->id }},{{ $ambiente->id }});
          },this)">
        <div class="row">
            <div class="col-sm-6">
                <label>Número *</label>
                <input type="number" min="0" step="1" id="mesa_editar_numero_mesa" value="{{ $comidamesa->numero_mesa }}">
            </div>
            <div class="col-sm-6">
                <label>Estado</label>
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
  }).val({{ $comidamesa->idestado }}).trigger('change');
</script>