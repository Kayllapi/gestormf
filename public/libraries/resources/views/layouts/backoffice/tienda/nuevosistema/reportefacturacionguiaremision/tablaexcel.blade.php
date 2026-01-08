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
          <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Serie</th>
                <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Correlativo</th>
                <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Fecha de Emisión</th>
                <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">RUC/DNI</th>
                <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Remitente</th>
                <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">RUC</th>
                <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Destinatario</th>
                <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Motivo</th>
                <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Traslado</th>
                <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">RUC/DNI</th>
                <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Transportista</th>
                <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Responsable</th>
        </tr>
    </thead>
    <tbody>
         @foreach($facturacionguiaremision as $value)
          <tr>
            <td></td>
            <td>{{ $value->despacho_serie }}</td>
                    <td>{{ str_pad($value->despacho_correlativo, 8, "0", STR_PAD_LEFT) }}</td>
                    <td>{{ date_format(date_create($value->despacho_fechaemision), 'd/m/Y h:i:s A') }}</td>
                    <td>{{ $value->emisor_ruc }}</td>
                    <td>{{ $value->emisor_nombrecomercial }}</td>
                    <td>{{ $value->despacho_destinatario_numerodocumento }}</td>
                    <td>{{ $value->despacho_destinatario_razonsocial }}</td>
                    <td>{{ $value->motivotrasladonombre}}</td>
                    <td>{{ $value->envio_fechatraslado }}</td>
                    <td>{{ $value->transporte_choferdocumento }}</td>
                    <td>{{ $value->transportista }}</td>
                    <td>{{ $value->responsablenombre}}</td>
            
           </tr>
         @endforeach
    </tbody>
</table>