@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
    <div class="list-single-main-wrapper fl-wrap">
        <div class="breadcrumbs gradient-bg fl-wrap">
          <span>Registrar Cobranza</span>
          <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
        </div>
    </div>
    <div id="carga-cobranza">
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
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza/show-creditocliente')}}",
            dataType: 'json',
            delay: 250,
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
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamocobranza/'+e.currentTarget.value+'/edit?view=cobranza',result:'#cont-clientecredito'});
    });

    function registrar_prestamo() {
        callback({
            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamocobranza',
            method: 'POST',
            carga: '#carga-cobranza',
            data:   {
                view: 'registrar',
                idprestamo_credito: $('#idcliente').val(),
                idtipopago: $('#idtipopago').val(),
                check_moradescuento: $("#check_moradescuento:checked").val(),
                moradescuento: $('#moradescuento').val(),
                moradescuento_detalle: $('#moradescuento_detalle').val(),
                montocompleto: $('#montocompleto').val(),
                hastacuota: $('#hastacuota').val(),
                montorecibido: $('#montorecibido').val(),
                vuelto: $('#vuelto').val()
            }
        },
        function(resultado){
            location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza/create') }}';
        })
    }
</script>
@endsection