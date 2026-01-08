<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Cuenta Bancaria</span>
      <a class="btn btn-success" href="javascript:;" onclick="index_cuentabancaria()"><i class="fa fa-angle-left"></i> Atras</a></a>
    </div>
</div>
<form action="javascript:;"
      onsubmit="callback({
                              route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion',
                              method: 'POST',
                              data:   {
                                  view: 'registrar-cuentabancaria'
                              }
                          },
                          function(resultado){
                              index_cuentabancaria();
                          },this)">
    <div class="row">
        <div class="col-sm-6">
            <label>Banco *</label>
            <select id="cuentabancaria_idbanco">
                <option></option>
                @foreach($bancos as $value)
                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-6">
            <label>Número de Cuenta *</label>
            <input type="text" id="cuentabancaria_numerocuenta"/>
        </div>
    </div>
    <button type="submit" class="btn mx-btn-post">Guardar</button>
</form>
<script>
$('#cuentabancaria_idbanco').select2({
    placeholder: '-- Seleccionar Mes --',
    minimumResultsForSearch: -1
});
</script>