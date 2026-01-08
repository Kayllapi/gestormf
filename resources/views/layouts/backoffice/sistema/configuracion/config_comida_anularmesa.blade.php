<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
        <span><b>Piso {{ str_pad($piso->nombre, 2, "0", STR_PAD_LEFT) }} / Ambiente {{ str_pad($ambiente->nombre, 2, "0", STR_PAD_LEFT) }}</b> / Eliminar Mesa</span>
        <a class="btn btn-success" href="javascript:;" onclick="index_mesa({{ $tienda->id }},{{ $piso->id }},{{ $ambiente->id }})"><i class="fa fa-angle-left"></i> Atras</a>
    </div>
</div>
<div id="form-anular-mesa">
    <form action="javascript:;"
          onsubmit="callback({
                                  route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion/0',
                                  method: 'PUT',
                                  carga: '#form-anular-mesa',
                                  data:   {
                                      view: 'comida-anular-mesa',
                                      idcomida_mesa: {{ $comidamesa->id }}
                                  }
                              },
                              function(resultado){
                                  index_mesa({{ $tienda->id }},{{ $piso->id }},{{ $ambiente->id }});
                              },this)">
        <div class="row">
            <div class="col-sm-6">
                <label>Número</label>
                <input type="number" value="{{ $comidamesa->numero_mesa }}" disabled>
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
          <i class="fa fa-warning"></i> ¿Esta seguro Eliminar la Mesa?
        </div>
        <button type="submit" class="btn mx-btn-post">Eliminar</button>
    </form>
</div>
<script>
  $('#idestado').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
  }).val({{ $comidamesa->idestado }}).trigger('change');
</script>