@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Confirmar Mora</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomora') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div id="carga-mora">
    <div class="col-sm-12">
        <label>Crédito del Cliente</label>
        <input type="text" value="{{ $s_prestamo_mora->estado }} / {{ $s_prestamo_mora->clienteidentificacion }} / {{ $s_prestamo_mora->clienteapellidos }} / {{ $s_prestamo_mora->clientenombre }} / {{ date_format(date_create($s_prestamo_mora->creditofechadesembolsado),'d-m-Y') }} /  {{ $s_prestamo_mora->monedasimbolo }} {{ $s_prestamo_mora->creditomonto }}" disabled>
    </div>

    <form action="javascript:;" 
          onsubmit="callback({
                route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamomora/{{ $s_prestamo_mora->id }}',
                method: 'PUT',
                carga:  '#carga-mora',
                data:   {
                    view: 'confirmar-mora',
                    documento: '{{$s_prestamo_mora->documento}}'
                }
            },
            function(resultado){
              location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomora') }}';
            }, this)">
      <div class="col-sm-6">
      <div class="row">
        <div class="col-sm-6">
              <label>Total de Moras</label>
              <input type="number" id="cuotas_total_mora" value="0.00" min="0" step="0.01" disabled>
              <label>Mora Pagada</label>
              <input type="number" id="mora_pagadas" value="0.00" min="0" step="0.01" disabled>
              <label>Mora a Pagar</label>
              <input type="number" id="total_moraapagar" value="0.00" min="0" step="0.01" disabled>
              <label>Mora a Descontar *</label>
              <input type="number" id="moradescuento" value="{{ $s_prestamo_mora->monto }}" placeholder="0.00" min="0" step="0.01" disabled>
              <label>Mora Pendiente</label>
              <input type="number" id="mora_pendiente" value="0.00" min="0" step="0.01" disabled>
        </div>
        <div class="col-sm-6">
              <label>Motivo de descuento *</label>
              <textarea id="moradescuento_detalle" style="height:85px;" disabled>{{ $s_prestamo_mora->motivo }}</textarea>
              @if ($s_prestamo_mora->documento != '')
                <label>Documento Actual: <b>{{ $s_prestamo_mora->documento }}</b></label>
                <div class="mensaje-success">
                  <i class="fa fa-file-pdf"></i> <a href="{{ url('/public/backoffice/tienda/'.$tienda->id.'/prestamomora/'.$s_prestamo_mora->documento) }}" target="_blank">Ver Documento</a>
                </div>
              @endif
        </div>
      </div>
          <button type="submit" class="btn mx-btn-post" style="margin-bottom: 5px;">Confirmar Mora</button>
      </div>  
    </form>

    <div class="col-sm-6">
        <div id="cont-cobranzapendiente"></div>
    </div>
</div>
@endsection
@section('subscripts')
<script>
    mostrar_cuotapendiente();
  
    let time_moradescuento;
    document.getElementById("moradescuento").addEventListener('keydown', () => {
      clearTimeout(time_moradescuento)
      time_moradescuento = setTimeout(() => {
        mostrar_cuotapendiente();
        clearTimeout(time_moradescuento)
      },700)
    });
  
    function mostrar_cuotapendiente(){
        $.ajax({
            url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomora/'.$s_prestamo_mora->idprestamo_credito.'/edit') }}",
            type: 'GET',
            data: {
                view: 'cuotapendiente',
                moradescuento: $('#moradescuento').val(),
                estado: 'confirmar'
            },
            beforeSend: function (data) {
                load('#cont-cobranzapendiente');
            },
            success: function (res) {
                $('#cont-cobranzapendiente').html(res);
            }
        });
    }
</script>
@endsection