<table class="table" id="table-cobranzapendiente">
    <thead style="background: #31353d; color: #fff;">
        <tr>
            <td style="padding: 8px;text-align: right;">Nº</td>
            <td style="padding: 8px;text-align: right;">Vencimiento</td>
            <td style="padding: 8px;text-align: right;">Cuota</td>
            <td style="padding: 8px;text-align: right;">Atraso</td>
            <td style="padding: 8px;text-align: right;">Mora</td>
            <td style="padding: 8px;text-align: right;">Mora D.</td>
            <td style="padding: 8px;text-align: right;">Mora P.</td>
            <td style="padding: 8px;text-align: right;">Total</td>
            <td style="padding: 8px;text-align: right;">A cuenta</td>
            <td style="padding: 8px;text-align: right;">Pagar</td>
        </tr>
    </thead>
    <tbody>
@foreach($cronograma['cuotas_pendientes'] as $value)
    <?php
      $style = '';
      if ($value['tabla_atraso'] > 0) {
          $style = $value['tabla_colortr'];
      }
    ?>
    <tr style="{{$style}};" {{$value['tabla_class']}}>
        <td style="padding: 8px;text-align: right;width: 10px;">{{$value['tabla_numero']}}</td>
        <td style="padding: 8px;text-align: right;width: 90px;">{{$value['tabla_fechavencimiento']}}</td>
        <td style="padding: 8px;text-align: right;">{{$value['tabla_cuota']}}</td>
        <td style="padding: 8px;text-align: right;">{{$value['tabla_atraso']}} días</td>
        <td style="padding: 8px;text-align: right;">{{$value['tabla_mora']}}</td>
        <td style="padding: 8px;text-align: right;{{$value['tabla_style_mora']}}">{{$value['tabla_moradescontado']}}</td>
        <td style="padding: 8px;text-align: right;{{$value['tabla_style_mora']}}">{{$value['tabla_moraapagar']}}</td>
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
  $('#cuotas_total_mora').val('{{$cronograma['select_mora']}}');
  $('#mora_pagadas').val('{{number_format($prestamo_morapagadas, 2, '.', '')}}');
  $('#total_moraapagar').val('{{number_format($cronograma['select_mora']-$prestamo_morapagadas, 2, '.', '')}}');
  $('#mora_pendiente').val('{{$cronograma['select_moraapagar']}}');
</script>