@if(count($cronograma['cuotas_pendientes'])==0)
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> No tiende ninguna deuda pendiente!!.
    </div>
@else

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
            <td style="padding: 8px;text-align: right;">Pago</td>
            @if($cronograma['total_pendiente_abono']>0)
            <td style="padding: 8px;text-align: right;">Abono</td>
            @endif
        </tr>
    </thead>
    <tbody>
    <?php $acuenta_anterior = 0; ?>
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
        @if($cronograma['total_pendiente_abono']>0)
        <td style="padding: 8px;text-align: right;">{{$value['tabla_abono']}}</td>
        @endif
    </tr>
    <?php $acuenta_anterior = $value['tabla_acuenta']; ?>
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
              @if($cronograma['total_pendiente_abono']>0)
              <td style="padding: 8px;text-align: right;">{{$cronograma['total_pendiente_abono']}}</td>
              @endif
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
  $('#moradescuento').val('{{$cronograma['select_moradescontado']}}');
  $('#cuotas_total_mora_pendiente').val('{{$cronograma['morapendiente']}}');
  $('#cuotas_total_mora_pendiente_span').html('{{$cronograma['morapendiente']}}');
  $('#cont-morapendiente').css('display','none');
  $('#cont-morapendiente-resultado').css('display','none');
  $('#cont-descontarmora').css('display', 'none');
  $('#cont-moradescuento').css('display', 'none');
  $('#cont-vueltopagocompleto').css('display', 'none');
  //$('#cont-cuotas_total').css('display', 'none');
  @if($request->idtipopago==1)
      @if(isset($request->checked_moradescuento))
          $('#moradescuento').prop('disabled', false);
          $('#moradescuento_detalle').prop('disabled', false);
      @endif
      @if($cronograma['morapendiente']>0)
          $('#cont-morapendiente').css('display','block');  
          @if($cronograma['ultimacuota']=='ok')
              $('#cont-morapendiente-resultado').css('display','block');
          @endif
      @endif
      @if(isset($request->hastacuota))
        @if($cronograma['select_mora']>0)
          @if($cronograma['select_moradescontado']==0)
          $('#cont-descontarmora').css('display', 'block');
          @endif
        @endif
        @if($cronograma['select_abono']>0)
          $('#cuotas_totalabono').val('{{$cronograma['select_abono']}}');
          $('#cont-abono').css('display', 'block');
        @endif
        @if($cronograma['select_atrasorestante']<0)
          $('#cuotas_total_interes').val('{{$cronograma['select_interesrestante']}}');
          $('#cont-descontarinteres').css('display', 'block');
        @endif
        $('#montorecibido').prop('disabled', false);
      @endif
      //$('#cont-cuotas_total').css('display', 'block');
      $('#cont-moradescuento').css('display', 'block');
  @elseif($request->idtipopago==2)
      @if(($cronograma['total_pendiente_cuotapago']-$cronograma['select_acuentaanterior'])<$cronograma['montorecibido'])
          $('#cont-acuentaproxima').css('display', 'none');
          $('#cont-vueltopagocompleto').css('display', 'block');
          $('#vueltopagocompleto').val('{{number_format($cronograma['montorecibido']-($cronograma['total_pendiente_cuotapago']-$cronograma['select_acuentaanterior']), 2, '.', '')}}');
      @endif
  @endif
  
  $('#cuotas_total_cuota').val('{{$cronograma['select_cuota']}}');
  $('#cuotas_total_mora').val('{{number_format($cronograma['select_mora'], 2, '.', '')}}');
  
  $('#cuotas_cuotapago').val('{{number_format($cronograma['select_cuotapago'], 2, '.', '')}}');
  
  $('#acuentaanterior').val('{{number_format($cronograma['select_acuentaanterior'], 2, '.', '')}}');
  $('#cont-acuentaanterior').css('display', 'none');
  @if($cronograma['select_acuentaanterior']>0)
      $('#cont-acuentaanterior').css('display', 'block');
  @endif
  
  $('#cuotas_total').val('{{number_format($cronograma['select_cuotaapagar'], 2, '.', '')}}');
  $('#cuotas_totalredondeado').val('{{$cronograma['select_cuotaapagarredondeado']}}');
  //$('#totalmonto_efectivo').html('{{number_format($cronograma['select_cuotaapagarredondeado'], 2, '.', '')}}');
  $('#total_moraapagar').val('{{$cronograma['select_moraapagar']}}');
  
  $('#acuenta').val('{{$cronograma['select_acuentaproxima']}}');
  
  
 /* var montorecibido = 0;
  if($('#idtipopago :selected').val()==1){
      var montorecibido = parseFloat($('#montorecibido').val());
  }else if($('#idtipopago :selected').val()==2){
      var montorecibido = parseFloat($('#montocompleto').val());
  }

  var cuotas_totalredondeado = parseFloat({{$cronograma['select_cuotaapagarredondeado']}});
  var vuelto = (montorecibido-cuotas_totalredondeado).toFixed(2);
  $('#vuelto').val(vuelto);*/
      
  
  // Resumen Credito
  $('#resumen-desembolso-monto').val('{{$cronograma['creditosolicitud']->monto}}');
  $('#resumen-desembolso-interes').val('{{$cronograma['creditosolicitud']->total_interes}}');
  $('#resumen-desembolso-gastoadministrativo').val('{{$cronograma['creditosolicitud']->total_gastoadministrativo}}');
  $('#resumen-desembolso-montototal').val('{{$cronograma['creditosolicitud']->total_cuotafinal}}');
  $('#resumen-desembolso-frecuencia').val('{{$cronograma['creditosolicitud']->frecuencianombre}}');
  $('#resumen-desembolso-fechainicio').val('{{date_format(date_create($cronograma['creditosolicitud']->fechainicio),"d/m/Y")}}');
  $('#resumen-desembolso-ultimafecha').val('{{date_format(date_create($cronograma['creditosolicitud']->ultimafecha),"d/m/Y")}}');
  $('#resumen-desembolso-cuotafija').val('{{$cronograma['creditosolicitud']->cuota}}');
  $('#resumen-desembolso-atraso').val('{{$cronograma['primeratraso']}} días');

  $('#resumen-cancelada-atraso').val('{{$cronograma['total_cancelada_atraso']}} días');
  $('#resumen-cancelada-cuota').val('{{$cronograma['total_cancelada_cuota']}}');
  $('#resumen-cancelada-mora').val('{{$cronograma['total_cancelada_mora']}}');
  $('#resumen-cancelada-moradescontado').val('{{$cronograma['total_cancelada_moradescontado']}}');
  $('#resumen-cancelada-moraapagar').val('{{$cronograma['total_cancelada_moraapagar']}}');
  $('#resumen-cancelada-acuenta').val('{{$cronograma['total_cancelada_acuenta']}}');
  $('#resumen-cancelada-total').val('{{$cronograma['total_cancelada_cuotapago']}}');
  
  $('#resumen-vencida-atraso').val('{{$cronograma['total_vencida_atraso']}} días');
  $('#resumen-vencida-cuota').val('{{$cronograma['total_vencida_cuota']}}');
  $('#resumen-vencida-mora').val('{{$cronograma['total_vencida_mora']}}');
  $('#resumen-vencida-moradescontado').val('{{$cronograma['total_vencida_moradescontado']}}');
  $('#resumen-vencida-moraapagar').val('{{$cronograma['total_vencida_moraapagar']}}');
  $('#resumen-vencida-acuenta').val('{{$cronograma['total_vencida_acuenta']}}');
  $('#resumen-vencida-total').val('{{$cronograma['total_vencida_cuotapago']}}');
  
  $('#resumen-restante-atraso').val('{{$cronograma['total_restante_atraso']}} días');
  $('#resumen-restante-cuota').val('{{$cronograma['total_restante_cuota']}}');
  $('#resumen-restante-mora').val('{{$cronograma['total_restante_mora']}}');
  $('#resumen-restante-moradescontado').val('{{$cronograma['total_restante_moradescontado']}}');
  $('#resumen-restante-moraapagar').val('{{$cronograma['total_restante_moraapagar']}}');
  $('#resumen-restante-acuenta').val('{{$cronograma['total_restante_acuenta']}}');
  $('#resumen-restante-total').val('{{$cronograma['total_restante_cuotapago']}}');
  
  $('#resumen-pendiente-atraso').val('{{$cronograma['total_pendiente_atraso']}} días');
  $('#resumen-pendiente-cuota').val('{{$cronograma['total_pendiente_cuota']}}');
  $('#resumen-pendiente-mora').val('{{$cronograma['total_pendiente_mora']}}');
  $('#resumen-pendiente-moradescontado').val('{{$cronograma['total_pendiente_moradescontado']}}');
  $('#resumen-pendiente-moraapagar').val('{{$cronograma['total_pendiente_moraapagar']}}');
  $('#resumen-pendiente-acuenta').val('{{$cronograma['total_pendiente_acuenta']}}');
  $('#resumen-pendiente-total').val('{{$cronograma['total_pendiente_cuotapago']}}');      

</script>
@endif