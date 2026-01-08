<table class="table" id="table-cobranzacancelada">
    <thead style="background: #31353d; color: #fff;">
     <tr>
         <td style="padding: 8px;text-align: center;">Nº</td>
         <td style="padding: 8px;text-align: center;">Vencimiento</td>
         <td style="padding: 8px;text-align: center;">Cuota</td>
         <td style="padding: 8px;text-align: center;">Atraso</td>
         <td style="padding: 8px;text-align: center;">Total Mora</td>
         <td style="padding: 8px;text-align: center;">Mora Descontado</td>
         <td style="padding: 8px;text-align: center;">Mora Pagado</td>
         <td style="padding: 8px;text-align: center;">Total</td>
         <td style="padding: 8px;text-align: center;">A cuenta</td>
         <td style="padding: 8px;text-align: center; width:10px;">Monto</td>
         @if($cronograma['total_cancelada_abono']>0)
         <td style="padding: 8px;text-align: center; width:10px;">Abono</td>
         @endif
         <td style="padding: 8px;text-align: center; width:10px;">Pago</td>
         <td style="padding: 8px;text-align: center; width:10px;">F. Pagado</td>
     </tr>
    </thead>
 <tbody>
<?php $cronograma_canceladas = collect($cronograma['cuotas_canceladas'])->sortByDesc('tabla_numero'); ?>
<?php $montototal = 0; ?>
<?php $codigocobranza = 0; ?>
<?php $i = 1; ?>

@foreach($cronograma_canceladas as $value)
    <?php
    $s_prestamo_cobranzadetalle = DB::table('s_prestamo_cobranzadetalle')
        ->join('s_prestamo_cobranza','s_prestamo_cobranza.id','s_prestamo_cobranzadetalle.idprestamo_cobranza')
        ->where('s_prestamo_cobranzadetalle.idprestamo_creditodetalle', $value['idprestamo_creditodetalle'])
        ->where('s_prestamo_cobranza.idestadocobranza',2)
        ->first();
    $cant_prestamo_cobranzadetalle = DB::table('s_prestamo_cobranzadetalle')
        ->where('s_prestamo_cobranzadetalle.idprestamo_cobranza', $s_prestamo_cobranzadetalle->idprestamo_cobranza)
        ->count();
    $cant_prestamo_cobranzadetalle_1 = DB::table('s_prestamo_cobranzadetalle')
        ->join('s_prestamo_cobranza','s_prestamo_cobranza.id','s_prestamo_cobranzadetalle.idprestamo_cobranza')
        ->where('s_prestamo_cobranzadetalle.idprestamo_creditodetalle', $value['idprestamo_creditodetalle'])
        ->where('s_prestamo_cobranza.idestadocobranza',2)
        ->get();
    ?>
    <tr idcreditodetalle="{{$value['idprestamo_creditodetalle']}}">
        <td style="padding: 8px;text-align: right;width: 10px;">{{$value['tabla_numero']}}</td>
        <td style="padding: 8px;text-align: center;width: 90px;">{{$value['tabla_fechavencimiento']}}</td>
        <td style="padding: 8px;text-align: right;">{{$value['tabla_cuota']}}</td>
        <td style="padding: 8px;text-align: right;">{{$value['tabla_atraso']}} días</td>
        <td style="padding: 8px;text-align: right;">{{$value['tabla_mora']}}</td>
        <td style="padding: 8px;text-align: right;background-color: #ff1f43;color: white;">{{$value['tabla_moradescontado']}}</td>
        <td style="padding: 8px;text-align: right;background-color: #0ec529;color: white;">{{$value['tabla_moraapagar']}}</td>
        <td style="padding: 8px;text-align: right;background-color: orange;color: white;">{{$value['tabla_cuotatotal']}}</td>
        <td style="padding: 8px;text-align: right;">{{$value['tabla_acuenta']}}</td>
        <td style="padding: 8px;text-align: right;">{{$value['tabla_cuotaapagar']}}</td>
        @if($cronograma['total_cancelada_abono']>0)
        <td style="padding: 8px;text-align: right;background-color: #178ae8;color: #fff;">{{$value['tabla_abono']}}</td>
        @endif
        @if($s_prestamo_cobranzadetalle->codigo!=$codigocobranza)
        <td style="padding: 8px;text-align: right;background-color: #178ae8;color: #fff;" rowspan="{{$cant_prestamo_cobranzadetalle}}">
          @foreach($cant_prestamo_cobranzadetalle_1 as $valuedetalle)
          <?php
                $monto = 0;
                if($valuedetalle->cronograma_idtipopago==1){
                    $monto = $valuedetalle->cronograma_totalredondeado;
                }
                elseif($valuedetalle->cronograma_idtipopago==2){
                    $monto = $valuedetalle->cronograma_pagado;
                }
          ?>
          {{$monto}}<br>
          <?php $montototal = $montototal+$monto; ?>
          @endforeach
        </td>
        <td style="padding: 8px;text-align: center;background-color: #178ae8;color: #fff;" rowspan="{{$cant_prestamo_cobranzadetalle}}">
          @foreach($cant_prestamo_cobranzadetalle_1 as $valuedetalle)
          {{date_format(date_create($valuedetalle->fecharegistro), "d/m/Y ")}}<br>
          @endforeach
        </td>
        <?php $codigocobranza = $s_prestamo_cobranzadetalle->codigo; ?>
        @endif
    </tr>
@endforeach
    </tbody>
    <tfoot style="background: #31353d; color: #fff;">
       <tr>
           <td style="padding: 8px;text-align: right;" colspan="2">TOTAL</td>
           <td style="padding: 8px;text-align: right;">{{$cronograma['total_cancelada_cuota']}}</td>
           <td style="padding: 8px;text-align: right;">{{$cronograma['total_cancelada_atraso']}} días</td>
           <td style="padding: 8px;text-align: right;">{{$cronograma['total_cancelada_mora']}}</td>
           <td style="padding: 8px;text-align: right;">{{$cronograma['total_cancelada_moradescontado']}}</td>
           <td style="padding: 8px;text-align: right;">{{$cronograma['total_cancelada_moraapagar']}}</td>
           <td style="padding: 8px;text-align: right;">{{$cronograma['total_cancelada_cuotapago']}}</td>
           <td style="padding: 8px;text-align: right;">{{$cronograma['total_cancelada_acuenta']}}</td>
           <td style="padding: 8px;text-align: right;">{{$cronograma['total_cancelada_cuotaapagar']}}</td>
           @if($cronograma['total_cancelada_abono']>0)
           <td style="padding: 8px;text-align: right;">{{$cronograma['total_cancelada_abono']}}</td>
           @endif
           <td style="padding: 8px;text-align: right;">{{number_format($montototal, 2, '.', '')}}</td>
           <td style="padding: 8px;text-align: right;"></td>
       </tr>
    </tfoot>
 </table>