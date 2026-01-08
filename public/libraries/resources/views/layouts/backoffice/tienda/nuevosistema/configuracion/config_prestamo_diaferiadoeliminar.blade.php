<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Eliminar Día Feriado</span>
      <a class="btn btn-success" href="javascript:;" onclick="index_diaferiado()"><i class="fa fa-angle-left"></i> Atras</a></a>
  </div>
</div>
<div id="form-eliminar-diaferiado">
  <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro de eliminar?</b>
  </div>
  <form action="javascript:;"
        onsubmit="callback({
                                route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion/0',
                                method: 'DELETE',
                                data:   {
                                    view: 'eliminar-diaferiado',
                                    idprestamo_diaferiado: $('#diaferiado_eliminar_idprestamo_diaferiado').val()
                                }
                            },
                            function(resultado){
                                index_diaferiado();
                            },this)">
      <input type="hidden" id="diaferiado_eliminar_idprestamo_diaferiado" value="0">
      <div class="row">
          <div class="col-sm-6">
              <label>Fecha (Día / Año)</label>
              <div class="row">
                  <div class="col-sm-6">
                      <input type="number" id="diaferiado_eliminar_dia" value="{{ $prestamodiaferiado->dia }}" min="1" disabled/>
                  </div>
                  <div class="col-sm-6">
                      <select id="diaferiado_eliminar_mes" disabled>
                          <option></option>
                          <option value="1">01 - Enero</option>
                          <option value="2">02 - Febrero</option>
                          <option value="3">03 - Marzo</option>
                          <option value="4">04 - Abril</option>
                          <option value="5">05 - Mayo</option>
                          <option value="6">06 - Junio</option>
                          <option value="7">07 - Julio</option>
                          <option value="8">08 - Agosto</option>
                          <option value="9">09 - Septiembre</option>
                          <option value="10">10 - Octubre</option>
                          <option value="11">11 - Noviembre</option>
                          <option value="12">12 - Diciembre</option>
                      </select>
                  </div>
              </div>
          </div>
          <div class="col-sm-6">
              <label>Motivo</label>
              <input type="text" id="diaferiado_eliminar_motivo" value="{{ $prestamodiaferiado->motivo }}" disabled/>
          </div>
      </div>
      <button type="submit" class="btn mx-btn-post">Eliminar Día Feriado</button>
  </form>
</div>

<script>
$('#diaferiado_eliminar_mes').select2({
    placeholder: '-- Seleccionar Mes --',
    minimumResultsForSearch: -1
}).val("{{ $prestamodiaferiado->mes }}").trigger('change');
</script>