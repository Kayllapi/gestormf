@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Anular Mora</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomora') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div id="carga-mora">
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> Esta seguro Anular la Mora!.
    </div>
  
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
                    view: 'anular-mora',
                }
            },
            function(resultado){
              location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomora') }}';
            }, this)">
      <div class="col-sm-6">
          <div class="row">
            <div class="col-sm-4">
                  <label>Total de Cuotas</label>
                  <input type="number" id="cuotas_total_cuota" s_prestamo_mora="0.00" min="0" step="0.01" disabled>
                  <label>A Cuenta</label>
                  <input type="number" id="acuenta" value="0.00" min="0" step="0.01" disabled>
            </div>
            <div class="col-sm-4">
                  <label>Total de Moras</label>
                  <input type="number" id="cuotas_total_mora" value="0.00" min="0" step="0.01" disabled>
                  <div style="display: block;" id="cont-moradescuento">
                      <div class="row">
                          <div class="col-sm-6">
                              <input type="number" id="moradescuento" value="{{ $s_prestamo_mora->monto }}" disabled>
                          </div>
                          <div class="col-sm-6">
                              <input type="number" id="total_moraapagar" value="0.00" min="0" step="0.01" disabled>
                          </div>
                      </div>
                      <label>Motivo de descuento *</label>
                      <textarea id="moradescuento_detalle" style="height:85px;" disabled>{{ $s_prestamo_mora->motivo }}</textarea>
                  </div>
                  @if ($s_prestamo_mora->documento != '')
                    <label>Documento: <b>{{ $s_prestamo_mora->documento }}</b></label>
                    <div class="mensaje-success">
                      <i class="fa fa-file-pdf"></i> <a href="{{ url('/public/backoffice/tienda/'.$tienda->id.'/prestamomora/'.$s_prestamo_mora->documento) }}" target="_blank">Ver Documento</a>
                    </div>
                  @endif
            </div>
            <div class="col-sm-4">
                  <label>Total</label>
                  <input type="number" id="cuotas_total" value="0.00" min="0" step="0.01" disabled>
                  <label>Redondeado</label>
                  <input type="number" id="cuotas_totalredondeado" value="0.00" min="0" step="0.01" disabled>
            </div>
          </div>
          <button type="submit" class="btn mx-btn-post" style="margin-bottom: 5px;">Anular Mora</button>
      </div>  
    </form>

    <div class="col-sm-6">
        <div id="cont-cobranzapendiente"></div>
    </div>
</div>
@endsection
@section('subscripts')
<script>
    mostrar_cuotapendiente({{ $ultima_cuota->numero }});
  
    let time_moradescuento;
    document.getElementById("moradescuento").addEventListener('keydown', () => {
      clearTimeout(time_moradescuento)
      time_moradescuento = setTimeout(() => {
        mostrar_cuotapendiente({{ $ultima_cuota->numero }});
        clearTimeout(time_moradescuento)
      },700)
    });
  
    function mostrar_cuotapendiente(hastacuota){
        $.ajax({
            url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomora/'.$s_prestamo_mora->idprestamo_credito.'/edit') }}",
            type: 'GET',
            data: {
                view: 'cuotapendiente',
                idtipopago: 1,
                moradescuento: $('#moradescuento').val(),
                montocompleto: 0,
                hastacuota: hastacuota,
                checked_moradescuento: $("#check_moradescuento:checked").val()
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