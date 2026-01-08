<table style="width:100%">
    <thead>
        <tr></tr>
        <tr>
            <th></th>
            <th style="font-weight: 900; background-color:#31353d; color: #ffffff; text-align: center; font-size: 12px; " colspan="15">
              {{ $titulo }}
            </th>
        </tr>
    
        @if($inicio != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">Fecha de Inicio:</th>
            <th style="font-weight: 900;" colspan="14">{{date_format(date_create($inicio), 'd/m/Y') }}</th>
        </tr>
        @endif
        @if($fin != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">Fecha de Fin:</th>
            <th style="font-weight: 900;" colspan="14">{{date_format(date_create($fin), 'd/m/Y') }}</th>
        </tr>
        @endif
        <tr></tr>
        <tr>
          <th></th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Correlativo</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Fecha de Generación</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Fecha de Resumen</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">RUC</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Emisor	</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Comprobante</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Serie-Correlativo</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">DNI/RUC</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Cliente</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Sub Total</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">IGV</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Total</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Estado</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Responsable</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">SUNAT</th>
        </tr>
    </thead>
    <tbody>
       @foreach($facturacionresumendiario as $value)
        <tr>
          <td></td>
          <td>{{ str_pad($value->resumen_correlativo, 8, "0", STR_PAD_LEFT) }}</td>
          <td>{{ date_format(date_create($value->resumen_fechageneracion), 'd/m/Y h:i:s A') }}</td>
          <td>{{ date_format(date_create($value->resumen_fecharesumen), 'd/m/Y h:i:s A') }}</td>
          <td>{{$value->emisor_ruc}}</td>
          <td>{{$value->emisor_nombrecomercial}}</td>
          <td>{{ $value->tipodocumento=='03'?'BOLETA':($value->tipodocumento=='07'?'NOTA DE CRÉDITO':'---') }}</td>
          <td>{{ $value->serienumero }}</td>
          <td>{{ $value->clientenumero }}</td>
          <td>{{ $value->cliente }}</td>
          <td>{{ $value->operacionesgravadas }}</td>
          <td>{{ $value->montoigv }}</td>
          <td>{{ $value->total }}</td>
          <td>{{ $value->estado==1?'Adicionado':($value->estado==2?'Modificado':($value->estado==3?'Anulado':'---')) }}</td>
          <td>{{ $value->responsablenombre }}</td>
          <td>
            @if($value->respuestaestado=='ACEPTADA')
              <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Aceptada</span></div>
            @elseif($value->respuestaestado=='OBSERVACIONES')
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Observaciones</span></div> 
            @elseif($value->respuestaestado=='RECHAZADA')
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Rechazada</span></div> 
            @elseif($value->respuestaestado=='EXCEPCION')
              <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-sync-alt"></i> Excepción</span></div>
            @else
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> No enviado</span></div>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
</table>