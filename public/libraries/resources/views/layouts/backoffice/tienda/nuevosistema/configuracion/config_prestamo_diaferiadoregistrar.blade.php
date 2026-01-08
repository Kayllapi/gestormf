<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Día Feriado</span>
      <a class="btn btn-success" href="javascript:;" onclick="index_diaferiado()"><i class="fa fa-angle-left"></i> Atras</a></a>
    </div>
</div>
<form action="javascript:;"
      onsubmit="callback({
                              route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion',
                              method: 'POST',
                              data:   {
                                  view: 'registrar-diaferiado'
                              }
                          },
                          function(resultado){
                              index_diaferiado();
                          },this)">
    <div class="row">
        <div class="col-sm-6">
            <label>Fecha (Día / Año) *</label>
            <div class="row">
                <div class="col-sm-6">
                    <input type="number" id="diaferiado_registrar_dia" min="1"/>
                </div>
                <div class="col-sm-6">
                    <select id="diaferiado_registrar_mes">
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
            <label>Motivo *</label>
            <input type="text" id="diaferiado_registrar_motivo"/>
        </div>
    </div>
    <button type="submit" class="btn mx-btn-post">Guardar Día Feriado</button>
</form>
<script>
  $('#diaferiado_registrar_mes').select2({
      placeholder: '-- Seleccionar Mes --',
      minimumResultsForSearch: -1
  });
</script>