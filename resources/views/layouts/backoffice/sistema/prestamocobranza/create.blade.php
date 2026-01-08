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
<?php
            /*$s_prestamo_creditodetalle = DB::table('s_prestamo_creditodetalle')
                ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_creditodetalle.idprestamo_credito')
                ->where('s_prestamo_creditodetalle.acuenta','>',0)
                ->where('s_prestamo_credito.idtienda',167)
                ->select(
                  's_prestamo_creditodetalle.*',
                  's_prestamo_credito.idtienda',
                  's_prestamo_credito.codigo',
                  's_prestamo_credito.id as idcredito',
                )
                ->orderBy('s_prestamo_creditodetalle.numero','desc')
                ->get();
            foreach($s_prestamo_creditodetalle as $value){
                $s_prestamo_cobranzadetalle = DB::table('s_prestamo_cobranzadetalle')
                ->join('s_prestamo_cobranza','s_prestamo_cobranza.id','s_prestamo_cobranzadetalle.idprestamo_cobranza')
                ->where('s_prestamo_cobranzadetalle.idprestamo_creditodetalle',$value->id)
                ->select(
                  's_prestamo_cobranzadetalle.*',
                )
                ->get();
                if(count($s_prestamo_cobranzadetalle)==1 ){
                    foreach($s_prestamo_cobranzadetalle as $val){
                    echo $value->idtienda.'/'.$value->codigo.'/'.$value->idcredito.'///'.$value->id.'///'.$value->acuenta.'/'.$val->id.'<br>';
                    }
                }
            }*/
?>
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
        @if(modulo($tienda->id,Auth::user()->id,'cobranza_listarporasesor')['resultado']=='CORRECTO')
              @include('app.prestamo_select2_creditocliente',['idasesor' => Auth::user()->id])
        @else
              @include('app.prestamo_select2_creditocliente')
        @endif
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