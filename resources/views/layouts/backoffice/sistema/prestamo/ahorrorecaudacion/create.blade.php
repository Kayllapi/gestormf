@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
    <div class="list-single-main-wrapper fl-wrap">
        <div class="breadcrumbs gradient-bg fl-wrap">
          <span>Registrar Recaudación</span>
          <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrorecaudacion') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
        </div>
    </div>
    <div id="carga-recaudacion">
        <div class="row">
            <div class="col-sm-12">
                <label>Crédito del Cliente</label>
                <select id="idcliente">
                    <option></option>
                </select>
            </div>
            <div id="cont-clienteahorro"></div>
        </div>
    </div>     
@endsection
@section('htmls')
<!--  modal recaudacion-ahorrolibre --> 
<div class="main-register-wrap modal-recaudacion-ahorrolibre" id="modal-recaudacion-ahorrolibre">
    <div class="main-overlay"></div>
    <div class="main-register-holder" style="margin: 10px auto 50px;">
        <div class="main-register fl-wrap">
            <div id="contenido-recaudacion-ahorrolibre"></div>
        </div>
    </div>
</div>
<!--  fin modal recaudacion-ahorrolibre --> 
@endsection
@section('subscripts')
<script>
    $('#idcliente').select2({
        @include('app.prestamo_select2_ahorrocliente')
    }).on("change", function(e) {
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorrorecaudacion/'+e.currentTarget.value+'/edit?view=recaudacion',result:'#cont-clienteahorro'});
    });

    /*function registrar_prestamo() {
        callback({
            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamo/ahorrorecaudacion',
            method: 'POST',
            carga: '#carga-recaudacion',
            data:   {
                view: 'registrar',
                idprestamo_ahorro: $('#idcliente').val(),
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
            location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrorecaudacion/create') }}';
        })
    }*/
</script>
@endsection