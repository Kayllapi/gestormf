<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Eliminar Cuenta Bancaria</span>
      <a class="btn btn-success" href="javascript:;" onclick="index_cuentabancaria()"><i class="fa fa-angle-left"></i> Atras</a></a>
    </div>
</div>
<form action="javascript:;"
      onsubmit="callback({
                              route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion/0',
                              method: 'DELETE',
                              data:   {
                                  view: 'eliminar-cuentabancaria',
                                  idcuentabancaria: {{ $cuentabancaria->id }}
                              }
                          },
                          function(resultado){
                              index_cuentabancaria();
                          },this)">
    <div class="row">
        <div class="col-sm-6">
            <label>Banco *</label>
            <select id="cuentabancaria_idbanco" disabled>
                <option></option>
                @foreach($bancos as $value)
                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-6">
            <label>Número de Cuenta *</label>
            <input type="text" value="{{$cuentabancaria->numerocuenta}}" id="cuentabancaria_numerocuenta" disabled/>
        </div>
    </div>
    <button type="submit" class="btn mx-btn-post">Eliminar</button>
</form>
<script>
$('#cuentabancaria_idbanco').select2({
    placeholder: '-- Seleccionar Mes --',
    minimumResultsForSearch: -1
}).val("{{ $cuentabancaria->s_idbanco }}").trigger('change');
</script>