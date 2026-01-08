@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Reprogramación</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamoreprogramacion') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div id="carga-reprogramacion">
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
            url:      "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamoreprogramacion/show-creditocliente') }}",
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
        placeholder: "--  Seleccionar Producto --",
        allowClear: true,
        minimumInputLength: 2,
        templateResult: function (state) {
            if (!state.id) {
                return state.text;
            }
            return $('<div><b>CRÉDITO:</b> '+state.estado+'</div>'+
                     '<div><b>CLIENTE:</b> '+state.clienteidentificacion+' - '+state.clienteapellidos+', '+state.clientenombre+'</div>'+
                     '<div><b>DESEMBOLSO:</b> '+state.creditofechadesembolsado+'</div>'+
                     '<div><b>MONTO:</b> '+state.monedasimbolo+' '+state.creditomonto+'</div>');
        },
        templateSelection: function (repo) {
            if (!repo.id) {
                return repo.text;
            }
            return $('<span>'+repo.text+'</span>');
        },
    }).on("change", function(e) {
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamoreprogramacion/'+e.currentTarget.value+'/edit?view=reprogramacion',result:'#cont-clientecredito'});
    });
</script>
@endsection