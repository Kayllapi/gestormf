<table class="table" id="table-cobranzacancelada">
    <thead style="background: #31353d; color: #fff;">
     <tr>
         <td style="padding: 8px;text-align: right;">Nº</td>
         <td style="padding: 8px;text-align: right;">Recaudacion</td>
         <td style="padding: 8px;text-align: right;">Cuota</td>
         <td style="padding: 8px;text-align: right;">Atraso</td>
         <td style="padding: 8px;text-align: right;">Mora</td>
         <td style="padding: 8px;text-align: right;">Mora D.</td>
         <td style="padding: 8px;text-align: right;">Mora P.</td>
         <td style="padding: 8px;text-align: right;">Total</td>
         <td style="padding: 8px;text-align: right;">A cuenta</td>
         <td style="padding: 8px;text-align: right;">Pagado</td>
     </tr>
    </thead>
 <tbody>
@foreach($cronograma['cuotas_canceladas'] as $value)
    <tr>
        <td style="padding: 8px;text-align: right;width: 10px;">{{$value['tabla_numero']}}</td>
        <td style="padding: 8px;text-align: right;width: 90px;">{{$value['tabla_fechaahorro']}}</td>
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
           <td style="padding: 8px;text-align: right;">{{$cronograma['total_cancelada_cuota']}}</td>
           <td style="padding: 8px;text-align: right;">{{$cronograma['total_cancelada_atraso']}} días</td>
           <td style="padding: 8px;text-align: right;">{{$cronograma['total_cancelada_mora']}}</td>
           <td style="padding: 8px;text-align: right;">{{$cronograma['total_cancelada_moradescontado']}}</td>
           <td style="padding: 8px;text-align: right;">{{$cronograma['total_cancelada_moraapagar']}}</td>
           <td style="padding: 8px;text-align: right;">{{$cronograma['total_cancelada_cuotapago']}}</td>
           <td style="padding: 8px;text-align: right;">{{$cronograma['total_cancelada_acuenta']}}</td>
           <td style="padding: 8px;text-align: right;">{{$cronograma['total_cancelada_cuotaapagar']}}</td>
       </tr>
    </tfoot>
 </table>