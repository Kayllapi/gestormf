<table style="width:100%">
    <thead>
        <tr></tr>
        <tr>
            <th></th>
            <th style="font-weight: 900; background-color:#31353d; color: #ffffff; text-align: center; font-size: 12px; " colspan="13">
              {{ $titulo }}
            </th>
        </tr>
    
        @if($inicio != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">Fecha de Inicio:</th>
            <th style="font-weight: 900;" colspan="12">{{date_format(date_create($inicio), 'd/m/Y') }}</th>
        </tr>
        @endif
        @if($fin != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">Fecha de Fin:</th>
            <th style="font-weight: 900;" colspan="12">{{date_format(date_create($fin), 'd/m/Y') }}</th>
        </tr>
        @endif
        <tr></tr>
        <tr>
          <th></th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Item</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Fecha de Emisión</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Emisor - RUC</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Emisor - Razón Social</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Tipo</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Serie-Correlativo</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Moneda</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Código/RUC/DNI</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Cliente</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Valor</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">IGV</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Total</th>
        </tr>
    </thead>
    <tbody>
          <?php $i=1 ?>
           @foreach($facturacionboletafactura as $value)
        <tr>
             <td></td>
             <td>{{ $i }}</td>
             <td>{{ date_format(date_create($value->venta_fechaemision), 'd/m/Y') }}</td>
             <td>{{ $value->emisor_ruc}}</td>
             <td>{{ $value->emisor_razonsocial}}</td>
             <td>{{ $value->venta_tipodocumento}}</td>
             <td>{{ $value->venta_serie}}-{{ $value->venta_correlativo }}</td>
             <td>
               @if($value->venta_tipomoneda=='PEN')
                  SOLES
              @elseif($value->venta_tipomoneda=='USD')
                  DOLARES
              @endif
             </td>
             <td>{{ $value->cliente_numerodocumento}}</td>
             <td>{{ $value->cliente_razonsocial}}</td>
             <td>{{ $value->venta_valorventa}}</td>
             <td>{{ $value->venta_totalimpuestos}}</td>
             <td>{{ $value->venta_montoimpuestoventa}}</td>
         </tr>
          <?php $i++ ?>
         @endforeach
    </tbody>
</table>