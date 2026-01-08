<table style="width:100%">
    <thead>
        <tr></tr>
        <tr>
            <th></th>
            <th style="font-weight: 900; background-color:#31353d; color: #ffffff; text-align: center; font-size: 12px; " colspan="14">
              {{ $titulo }}
            </th>
        </tr>
    
        @if($inicio != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">FECHA DE INICIO:</th>
            <th style="font-weight: 900;" colspan="13">{{date_format(date_create($inicio), 'd/m/Y') }}</th>
        </tr>
        @endif
        @if($fin != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">FECHA FIN:</th>
            <th style="font-weight: 900;" colspan="13">{{date_format(date_create($fin), 'd/m/Y') }}</th>
        </tr>
        @endif
        <tr></tr>
        <tr>
          <th></th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">ITEM</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">FECHA DE EMISIÓN</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">RUC DE EMISIOR</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">RAZÓN SOCIAL DE EMISOR</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">TIPO DOCUMENTO</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">SERIE</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">CORRELATIVO</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">MOENDA</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">RUC/DNI DEL CLIENTE</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">CLIENTE</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">VALOR</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">IGV</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">TOTAL</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">ESTADO</th>
        </tr>
    </thead>
    <tbody>
            <?php 
            $i=1;
            $total_venta_valorventa=0;
            $total_venta_totalimpuestos=0;
            $total_venta_montoimpuestoventa=0;
            ?>
           @foreach($facturacionboletafactura as $value)
              <?php
              $style = '';
              $venta_valorventa = $value->venta_valorventa;
              $venta_totalimpuestos = $value->venta_totalimpuestos;
              $venta_montoimpuestoventa = $value->venta_montoimpuestoventa;
              if($value->comunicacionbaja_correlativo!='' && $value->venta_tipodocumento=='01'){
                  $style = 'style="background-color:#c40909; color:#ffffff;"';
                  $venta_valorventa = '0.00';
                  $venta_totalimpuestos = '0.00';
                  $venta_montoimpuestoventa = '0.00';
              }
              elseif($value->resumen_correlativo!='' && $value->venta_tipodocumento=='03'){
                  if($value->resumen_estado=='3'){
                      $style = 'style="background-color:#c40909; color:#ffffff;"';
                      $venta_valorventa = '0.00';
                      $venta_totalimpuestos = '0.00';
                      $venta_montoimpuestoventa = '0.00';
                  }
              }
              ?>
        
          <tr>
             <td></td>
             <td <?php $style ?>>{{ $i }}</td>
             <td <?php $style ?>>{{ date_format(date_create($value->venta_fechaemision), 'd/m/Y') }}</td>
             <td <?php $style ?>>{{ $value->emisor_ruc}}</td>
             <td <?php $style ?>>{{ $value->emisor_razonsocial}}</td>
             <td <?php $style ?>>{{ $value->venta_tipodocumento}}</td>
             <td <?php $style ?>>{{ $value->venta_serie}}</td>
             <td <?php $style ?>>{{ $value->venta_correlativo }}</td>
             <td <?php $style ?>>
               @if($value->venta_tipomoneda=='PEN')
                  SOLES
              @elseif($value->venta_tipomoneda=='USD')
                  DOLARES
              @endif
             </td>
             <td <?php $style ?>>{{ $value->cliente_numerodocumento}}</td>
             <td <?php $style ?>>{{ $value->cliente_razonsocial}}</td>
             <td <?php $style ?>>{{ $venta_valorventa}}</td>
             <td <?php $style ?>>{{ $venta_totalimpuestos}}</td>
             <td <?php $style ?>>{{ $venta_montoimpuestoventa}}</td>
             <td <?php $style ?>>
              @if($value->comunicacionbaja_correlativo!='' && $value->venta_tipodocumento=='01')
                  Anulado (CB: {{$value->comunicacionbaja_correlativo}}) 
              @elseif($value->resumen_correlativo!='' && $value->venta_tipodocumento=='03')
                  @if($value->resumen_estado=='1')
                    Aceptada (RD: {{$value->resumen_correlativo}})
                  @elseif($value->resumen_estado=='2')
                    Modificado (RD: {{$value->resumen_correlativo}})
                  @elseif($value->resumen_estado=='3')
                    Anulado (RD: {{$value->resumen_correlativo}})
                  @endif
              @else
                  @if($value->respuestaestado=='ACEPTADA')
                    Aceptada
                  @elseif($value->respuestaestado=='OBSERVACIONES')
                    Observaciones 
                  @elseif($value->respuestaestado=='RECHAZADA')
                    Rechazada 
                  @elseif($value->respuestaestado=='EXCEPCION')
                    @if($value->respuestacodigo==1033)
                    Aceptada
                    @else
                    Excepción
                    @endif
                  @else
                    No enviado
                  @endif
              @endif
             </td>
         </tr>
          <?php 
            $i++;
            $total_venta_valorventa=$total_venta_valorventa+$venta_valorventa;
            $total_venta_totalimpuestos=$total_venta_totalimpuestos+$venta_totalimpuestos;
            $total_venta_montoimpuestoventa=$total_venta_montoimpuestoventa+$venta_montoimpuestoventa;
          ?>
         @endforeach
        <tr>
          <th></th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;text-align:right;" colspan="10">TOTAL</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">{{number_format($total_venta_valorventa, 2, '.', '')}}</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">{{number_format($total_venta_totalimpuestos, 2, '.', '')}}</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">{{number_format($total_venta_montoimpuestoventa, 2, '.', '')}}</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;"></th>
        </tr>
    </tbody>
</table>