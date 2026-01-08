<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Registrar Relación</span>
    <a class="btn btn-success" href="javascript:;" onclick="relacion_index()"><i class="fa fa-angle-left"></i> Atras</a></a>
  </div>
</div>
<form action="javascript:;" 
    onsubmit="callback({
              route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud',
              method: 'POST',
              data:   {
                  view: 'registrar-relacion',
                  idusuario: {{ $prestamocredito->idcliente }}
              }
          },
          function(resultado){
              relacion_index();
          },this)">
  <div class="row">
      <div class="col-sm-6">
          <label>Persona *</label>
          <select id="relacion_idpersona">
              <option></option>
          </select>
      </div>
      <div class="col-sm-6">
          <label>Tipo de Relación *</label>
          <select id="relacion_idprestamo_tiporelacion">
              <option></option>
              @foreach ($tiporelacion as $value)
              <option value="{{ $value->id }}">{{ $value->nombre }}</option>
              @endforeach
          </select>
      </div>
  </div>
  <button type="submit" class="btn mx-btn-post">Guardar Relación</button>
</form>
<script>
$('#relacion_idprestamo_tiporelacion').select2({
    placeholder: '-- Seleccionar Tipo de Bien --',
    minimumResultsForSearch: -1
});
$('#relacion_idpersona').select2({
    @include('app.select2_cliente')
});
</script>