<table style="width:100%">
    <thead>
        <tr></tr>
        <tr>
            <th></th>
            <th style="font-weight: 900; background-color:#31353d; color: #ffffff; text-align: center; font-size: 12px; " colspan="10">
              {{ $titulo }}
            </th>
        </tr>
    
        @if($inicio != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">Fecha de Inicio:</th>
            <th style="font-weight: 900;" colspan="9">{{date_format(date_create($inicio), 'd/m/Y') }}</th>
        </tr>
        @endif
        @if($fin != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">Fecha de Fin:</th>
            <th style="font-weight: 900;" colspan="9">{{date_format(date_create($fin), 'd/m/Y') }}</th>
        </tr>
        @endif
        <tr></tr>
        <tr>
            <th></th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Código</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Producto</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Comprobante</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Serie Correlativo</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">P. Unitario</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Cant.</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Total</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Proveedor</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Fecha Emisión</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Responsable</th>
        </tr>
    </thead>
    <tbody>
        @foreach($s_compradetalle as $value)
          <tr>
           <td></td>
           <td>{{ str_pad($value->codigocompra, 8, "0", STR_PAD_LEFT) }}</td>
           <td>{{ str_pad($value->codigo, 13, "0", STR_PAD_LEFT) }} - {{ $value->nombreproducto}}</td>
           <td>{{ $value->nombreComprobante }}</td>
           <td>{{ str_pad($value->seriecorrelativo, 8, "0", STR_PAD_LEFT) }}</td>
           <td>{{ $value->preciounitario }}</td>
           <td>{{ $value->cantidad }}</td>
           <td>{{ $value->preciototal }}</td>
           <td>{{ $value->nombreproveedor}}</td>
           <td>{{ date_format(date_create($value->fechacompra), 'd/m/Y h:i:s A') }}</td>
           <td>{{ $value->apellidosresponsable}},{{ $value->nombreresponsable}}</td>
          </tr>
         @endforeach
    </tbody>
</table>