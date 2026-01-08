<table style="width:100%">
    <thead>
        <tr></tr>
        <tr>
            <th></th>
            <th style="font-weight: 900; background-color:#31353d; color: #ffffff; text-align: center; font-size: 12px; " colspan="12">
              {{ $titulo }}
            </th>
        </tr>
    
        @if($inicio != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">Fecha de Inicio:</th>
            <th style="font-weight: 900;" colspan="11">{{date_format(date_create($inicio), 'd/m/Y') }}</th>
        </tr>
        @endif
        @if($fin != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">Fecha de Fin:</th>
            <th style="font-weight: 900;" colspan="11">{{date_format(date_create($fin), 'd/m/Y') }}</th>
        </tr>
        @endif
        <tr></tr>
        <tr>
            <th></th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Código de Venta</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Producto</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Comprobante</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">P. Unitario</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Cant.</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Total</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Und. Medida</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Cliente</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Tipo de Pago</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Moneda</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Fecha Vendida</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Responsable</th>
        </tr>
    </thead>
    <tbody>
          @foreach($s_ventadetalle as $value)
          <tr>
           <td></td>
           <td>{{ str_pad($value->codigoventa, 8, "0", STR_PAD_LEFT) }}</td>
           <td>{{ str_pad($value->codigo, 13, "0", STR_PAD_LEFT) }} - {{ $value->nombreproducto}}</td>
           <td>{{ $value->nombreComprobante }}</td>
           <td>{{ $value->preciounitario }}</td>
           <td>{{ $value->cantidad }}</td>
           <td>{{ $value->total }}</td>
           <td>{{ $value->nombreunidadmedida}}</td>
           <td>{{ $value->apellidocliente}},{{ $value->nombrecliente}}</td>
           <td>{{ $value->tipoentreganombre }}</td>
           <td>{{ $value->monedanombre }}</td> 
           <td>{{ date_format(date_create($value->fechaventa),"d/m/Y h:i:s A")}}</td>
           <td>{{ $value->nombreresponsable}}</td>
          </tr>
         @endforeach
    </tbody>
</table>