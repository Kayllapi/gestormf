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
@section('htmls')
<!--  modal cobranzarealizada --> 
<div class="main-register-wrap modal-cobranzarealizada" id="modal-cobranzarealizada">
    <div class="main-overlay"></div>
    <div class="main-register-holder" style="margin: 10px auto 50px;">
        <div class="main-register fl-wrap">
            <div id="contenido-cobranzarealizada"></div>
        </div>
    </div>
</div>
<!--  fin modal cobranzarealizada --> 
@endsection
@section('subscripts')
<script>
    $('#idcliente').select2({
        @include('app.prestamo_select2_creditocliente')
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