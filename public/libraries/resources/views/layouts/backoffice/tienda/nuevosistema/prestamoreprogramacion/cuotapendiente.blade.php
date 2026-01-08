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
  $('#idtipopago').prop('disabled', false);
  $('#hastacuota').prop('disabled', false);
  $('#montocompleto').prop('disabled', false);
  @if(isset($request->checked_moradescuento))
  $('#moradescuento').prop('disabled', false);
  $('#moradescuento_detalle').prop('disabled', false);
  @endif
  @if(isset($request->hastacuota))
  $('#montorecibido').prop('disabled', false);
  @endif
  $('#cuotas_total_cuota').val('{{$cronograma['select_cuota']}}');
  $('#cuotas_total_mora').val('{{$cronograma['select_mora']}}');
  $('#cuotas_total').val('{{$cronograma['select_cuotaapagar']}}');
  $('#cuotas_totalredondeado').val('{{$cronograma['select_cuotaapagarredondeado']}}');
  $('#monto').val('{{$cronograma['select_cuotaapagarredondeado']}}');
  $('#total_moraapagar').val('{{$cronograma['select_moraapagar']}}');
  $('#acuenta').val('{{$cronograma['select_acuenta']}}');
  
    
  var montocompleto = parseFloat($('#montocompleto').val());
  var cuotas_totalredondeado = parseFloat({{$cronograma['select_cuotaapagarredondeado']}});
  var vuelto = (montocompleto-cuotas_totalredondeado).toFixed(2);
  if(vuelto<0){
      vuelto = '0.00';
  }
  $('#vuelto').val(vuelto);
  
  // Resumen Credito
  $('#resumen-desembolso-frecuencia').val('{{$cronograma['creditosolicitud']->frecuencianombre}}');
  $('#resumen-desembolso-fecha').val('{{date_format(date_create($cronograma['creditosolicitud']->fechadesembolsado),"d/m/Y h:i:s A")}}');
  $('#resumen-desembolso-monto').val('{{$cronograma['creditosolicitud']->monto}}');
  $('#resumen-desembolso-interes').val('{{$cronograma['creditosolicitud']->total_interes}}');
  $('#resumen-desembolso-montototal').val('{{$cronograma['creditosolicitud']->total_cuota}}');

  $('#resumen-vencida-deudaactual').val('{{$cronograma['total_vencida_cuota']}}');
  $('#resumen-vencida-moraactual').val('{{$cronograma['total_vencida_mora']}}');
  $('#resumen-vencida-totalactual').val('{{$cronograma['total_vencida_cuotapago']}}');
              
  $('#resumen-pendiente-deudarestante').val('{{$cronograma['total_pendiente_cuota']}}');
  $('#resumen-pendiente-morarestante').val('{{$cronograma['total_pendiente_mora']}}');
  $('#resumen-pendiente-totalrestante').val('{{$cronograma['total_pendiente_cuotapago']}}');
              
  $('#resumen-cancelada-deudapagada').val('{{$cronograma['total_cancelada_cuotapago']}}');
  $('#resumen-cancelada-morapagada').val('{{$cronograma['total_cancelada_moraapagar']}}');
  $('#resumen-cancelada-totalpagada').val('{{number_format($cronograma['total_cancelada_acuenta']+$cronograma['total_cancelada_cuotaapagar'], 2, '.', '')}}');
</script>