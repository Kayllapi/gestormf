<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Registrar Laboral</span>
    <a class="btn btn-success" href="javascript:;" onclick="laboral_index()"><i class="fa fa-angle-left"></i> Atras</a></a>
  </div>
</div>
<form action="javascript:;" 
      onsubmit="callback({
                route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud',
                method: 'POST',
                data:   {
                    view: 'registrar-laboral',
                    idusuario: {{ $prestamocredito->idcliente }}
                }
            },
            function(resultado){
                laboral_index();
            },this)">
  <div class="row">
    <div class="col-sm-6">
        <label>Fuente de Ingreso *</label>
        <select id="laboral_idfuenteingreso">
            <option></option>
            <option value="1">Dependiente</option>
            <option value="2">Independiente</option>
        </select>

        <label>Giro *</label>
        <select id="laboral_idprestamo_giro" onchange="cargarActividad('laboral_idprestamo_giro', 'laboral_idprestamo_actividad')">
            <option></option>
            @foreach ($giro as $value)
            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
            @endforeach
        </select>

        <label>Actividad *</label>
        <select id="laboral_idprestamo_actividad" disabled>
            <option></option>
        </select>

        <label>Labora Desde (mes / año) *</label>
        <div class="row">
          <div class="col-sm-6">
              <select id="laboral_labora_desdemes">
                  <option></option>
                  <option value="1">Enero</option>
                  <option value="2">Febrero</option>
                  <option value="3">Marzo</option>
                  <option value="4">Abril</option>
                  <option value="5">Mayo</option>
                  <option value="6">Junio</option>
                  <option value="7">Julio</option>
                  <option value="8">Agosto</option>
                  <option value="9">Septiembre</option>
                  <option value="10">Octubre</option>
                  <option value="11">Noviembre</option>
                  <option value="12">Diciembre</option>
              </select>
          </div>
          <div class="col-sm-6">
              <input type="number" id="laboral_labora_desdeanio" min="1" step="1">
          </div>
        </div>
    </div>
    <div class="col-sm-6">
        <label>Ingreso Mensual *</label>
        <input type="number" id="laboral_ingresomensual" min="0" step="0.1">
        <label>Ubigeo *</label>
        <select id="laboral_idubigeo">
            <option></option>
            @foreach ($ubigeo as $value)
            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
            @endforeach
        </select>
        <label>Dirección *</label>
        <input type="text" id="laboral_direccion">
        <label>Referencia</label>
        <input type="text" id="laboral_referencia">
    </div>
    <div class="col-md-12">
        <label>Ubicación (Mapa)</label>
        <div id="laboral_mapa" style="height: 386px;width: 100%;margin-bottom: 5px;"></div>
        <input type="hidden" value="-12.071871667822409" id="laboral_mapa_latitud"/>
        <input type="hidden" value="-75.21026847919165" id="laboral_mapa_longitud"/>
    </div>
  </div>
  <button type="submit" class="btn mx-btn-post">Guardar Laboral</button>
</form>

<script>
$('#laboral_idfuenteingreso').select2({
    placeholder: '-- Seleccionar Ubigeo --',
    minimumResultsForSearch: -1
}).val(null).trigger('change');
$('#laboral_idprestamo_giro').select2({
    placeholder: '-- Seleccionar Ubigeo --',
    minimumResultsForSearch: -1
}).val(null).trigger('change');
$('#laboral_idprestamo_actividad').select2({
    placeholder: '-- Seleccionar Ubigeo --',
    minimumResultsForSearch: -1
}).val(null).trigger('change');      
$('#laboral_idubigeo').select2({
    placeholder: '-- Seleccionar Ubigeo --',
    minimumResultsForSearch: -1,
    minimumInputLength: 2
}).val(null).trigger('change');
$('#laboral_labora_desdemes').select2({
    placeholder: '-- Seleccionar Ubigeo --',
    minimumResultsForSearch: -1
}).val(null).trigger('change');

singleMap('laboral',-12.071871667822409,-75.21026847919165);
</script>