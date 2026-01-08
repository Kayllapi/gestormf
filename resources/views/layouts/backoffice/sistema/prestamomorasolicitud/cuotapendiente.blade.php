@if(count($cronograma['cuotas_pendientes'])==0)
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> No tiende ninguna deuda pendiente!!.
    </div>
@else
<table class="table" id="table-cobranzapendiente">
    <thead style="background: #31353d; color: #fff;">
        <tr>
            <td style="padding: 8px;text-align: center;">Nº</td>
            <td style="padding: 8px;text-align: center;">Vencimiento</td>
            <td style="padding: 8px;text-align: center;">Cuota</td>
            <td style="padding: 8px;text-align: center;">Atraso</td>
            <td style="padding: 8px;text-align: center;">Total Mora</td>
            <td style="padding: 8px;text-align: center;">Mora a Descontar</td>
            <td style="padding: 8px;text-align: center;">Mora a Pagar</td>
            <td style="padding: 8px;text-align: center;">Total</td>
            <td style="padding: 8px;text-align: center;">A cuenta</td>
            <td style="padding: 8px;text-align: center;">Pagar</td>
        </tr>
    </thead>
    <tbody>
@foreach($cronograma['cuotas_pendientes'] as $value)
    <tr style="{{$value['tabla_colortr']}};" {{$value['tabla_class']}}>
        <td style="padding: 8px;text-align: right;width: 10px;">{{$value['tabla_numero']}}</td>
        <td style="padding: 8px;text-align: right;width: 90px;">{{$value['tabla_fechavencimiento']}}</td>
        <td style="padding: 8px;text-align: right;">{{$value['tabla_cuota']}}</td>
        <td style="padding: 8px;text-align: right;">{{$value['tabla_atraso']}} días</td>
        <td style="padding: 8px;text-align: right;">{{$value['tabla_mora']}}</td>
        <td style="padding: 8px;text-align: right;background-color: #ff1f43;color: white;">{{$value['tabla_moradescontado']}}</td>
        <td style="padding: 8px;text-align: right;background-color: #0ec529;color: white;">{{$value['tabla_moraapagar']}}</td>
        <td style="padding: 8px;text-align: right;background-color: orange;color: white;">{{$value['tabla_cuotatotal']}}</td>
        <td style="padding: 8px;text-align: right;">{{$value['tabla_acuenta']}}</td>
        <td style="padding: 8px;text-align: right;">{{$value['tabla_cuotaapagar']}}</td>
    </tr>
@endforeach
    </tbody>
       <tfoot style="background: #31353d; color: #fff;">
          <tr>
              <td style="padding: 8px;text-align: right;" colspan="2">TOTAL</td>
              <td style="padding: 8px;text-align: right;">{{$cronograma['total_pendiente_cuota']}}</td>
              <td style="padding: 8px;text-align: right;">{{$cronograma['total_pendiente_atraso']}} días</td>
              <td style="padding: 8px;text-align: right;">{{$cronograma['total_pendiente_mora']}}</td>
              <td style="padding: 8px;text-align: right;">{{$cronograma['total_pendiente_moradescontado']}}</td>
              <td style="padding: 8px;text-align: right;">{{$cronograma['total_pendiente_moraapagar']}}</td>
              <td style="padding: 8px;text-align: right;">{{$cronograma['total_pendiente_cuotapago']}}</td>
              <td style="padding: 8px;text-align: right;">{{$cronograma['total_pendiente_acuenta']}}</td>
              <td style="padding: 8px;text-align: right;">{{$cronograma['total_pendiente_cuotaapagar']}}</td>
          </tr>
       </tfoot>
    </table>
  
<style>
  .mx-tableselect {
      background-image: url({{url('public/backoffice/sistema/text3.png')}}) !important;
  }
</style>

<script>
  @if(!isset($_GET['estado']))
  $('#moradescuento').prop('disabled', false);
  $('#moradescuento_detalle').prop('disabled', false);
  @endif

  /*@if($cronograma['select_moradescontado']>0)
      $('#cont-mora1').css('display', 'none');
      $('#cont-mora2').css('display', 'block');
  @else
      $('#cont-mora1').css('display', 'block');
      $('#cont-mora2').css('display', 'none');
  @endif*/
  
  $('#pendiente_mora_apagar').val('{{number_format(($cronograma['select_mora']-$cronograma['morarestante'])<0?0:($cronograma['select_mora']-$cronograma['morarestante']), 2, '.', '')}}');

  $('#pendiente_mora_total').val('{{$cronograma['total_pendiente_mora']}}');
  $('#pendiente_mora_descontado').val('{{$cronograma['total_pendiente_moradescontado']}}');
  $('#pendiente_mora_pagado').val('{{$cronograma['total_pendiente_moraapagar']}}');
  
  $('#cancelado_mora_total').val('{{$cronograma['total_cancelada_mora']}}');
  $('#cancelado_mora_descontado').val('{{$cronograma['total_cancelada_moradescontado']}}');
  $('#cancelado_mora_pagado').val('{{$cronograma['total_cancelada_moraapagar']}}');
  
  //$('#cuotas_total_mora').val('{{$cronograma['select_mora']}}');
  //$('#mora_pagadas').val('{{$cronograma['select_moraapagar']}}');
  $('#cuotas_total_mora_actual').val('{{number_format($cronograma['total_pendiente_mora']+$cronograma['total_cancelada_mora'], 2, '.', '')}}');
  $('#cuotas_total_mora_descontado').val('{{number_format($cronograma['total_pendiente_moradescontado']+$cronograma['total_cancelada_moradescontado'], 2, '.', '')}}');
  $('#cuotas_total_mora_pagado').val('{{number_format($cronograma['total_pendiente_moraapagar']+$cronograma['total_cancelada_moraapagar'], 2, '.', '')}}');
  $('#cuotas_total_mora_solicitado').val('{{$cronograma['morasolicitado']}}');
  $('#cuotas_total_mora_restante').val('{{$cronograma['morarestante']}}');
  $('#cuotas_total_mora_aprobado').val('{{$cronograma['moraaprobado']}}');
  $('#cuotas_total_mora_pendiente').val('{{$cronograma['morapendiente']}}');
</script>

@endif