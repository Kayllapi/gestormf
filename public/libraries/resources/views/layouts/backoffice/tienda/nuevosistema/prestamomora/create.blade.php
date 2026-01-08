@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Mora</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomora') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div id="carga-mora">
  <div class="row">
      <div class="col-sm-12">
          <label>Crédito del Cliente</label>
          <select id="idcliente">
            <option></option>
          </select>
      </div>
      <div id="cont-clientecredito"></div>
  </div>
</div>
@endsection
@section('subscripts')
<script>
    $('#idcliente').select2({
        ajax: {
            url:      "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomora/show-creditocliente') }}",
            dataType: 'json',
            delay:    250,
            data: function (params) {
                return {
                      buscar: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        placeholder: "-- Seleccionar Cliente --",
        allowClear: true,
        minimumInputLength: 2,
        templateResult: function (state) {
            if (!state.id) {
                return state.text;
            }
            return $('<div><b>CRÉDITO:</b> '+state.estado+' <b>CÓDIGO:</b> '+state.creditocodigo+'</div>'+
                     '<div><b>CLIENTE:</b> '+state.clienteidentificacion+' - '+state.clienteapellidos+', '+state.clientenombre+'</div>'+
                     '<div><b>DESEMBOLSO:</b> '+state.creditofechadesembolsado+' <b>MONTO:</b> '+state.monedasimbolo+' '+state.creditomonto+'</div>');
        },
        templateSelection: function (repo) {
            if (!repo.id) {
                return repo.text;
            }
            return $('<span>'+repo.text+'</span>');
        },
    }).on("change", function(e) {
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamomora/'+e.currentTarget.value+'/edit?view=mora',result:'#cont-clientecredito'});
    });
</script>
@endsection