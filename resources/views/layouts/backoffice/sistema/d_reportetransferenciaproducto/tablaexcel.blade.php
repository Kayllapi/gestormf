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
        <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Fecha Registro</th>
        <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Tienda Origen (Responsable)</th>
        <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Tienda Destino (Responsable)</th>
        <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Productos</th>
        <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Unidad de Medida</th>
        <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Cantidad</th>
        <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">cantidad Envio</th>
        <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Cantidad Recepcionado</th>
        <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Motivo</th>
        </tr>
    </thead>
    <tbody>
           @foreach($detalletransferencia as $value)
    <tr>
      <td></td>
      <td>{{ str_pad($value->codigotransferencia, 6, "0", STR_PAD_LEFT) }}</td>
      <td>{{ date_format(date_create($value->fecharegistro), 'd/m/Y - h:i A')  }}</td>
      <td>{{ $value->tienda_origen_nombre}} {{ $value->idusersorigen!=0?'('.$value->nombreorigen.')':'' }}</td>
      <td>{{ $value->tienda_destino_nombre}} {{ $value->idusersdestino!=0?'('.$value->nombredestino.')':'' }}</td>
      <td>{{ $value->nombreproducto }}</td>
      <td>{{ $value->nombremedida }}</td>
      <td>{{ $value->cantidad }}</td>
      <td>{{ $value->cantidadenviado }}</td>
      <td>{{ $value->cantidadrecepcion }}</td>
      <td>{{ $value->motivo}}</td>
    </tr>
    @endforeach
    </tbody>
</table>