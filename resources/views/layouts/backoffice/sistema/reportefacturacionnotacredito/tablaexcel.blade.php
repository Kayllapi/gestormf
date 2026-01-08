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
           <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Serie</th>
          <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Correlativo</th>
          <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Base Imp.</th>
          <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">IGV	</th>
          <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Total</th>
          <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Moneda</th>
          <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Fecha de Emisión</th>
          <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">DNI/RUC</th>
          <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Cliente</th>
          <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">RUC</th>
          <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Emisor</th>
          <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Responsable</th>
          <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Modificado</th>
          <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Motivo</th>
          <th  style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">SUNAT</th>
        </tr>
    </thead>
    <tbody>
        @foreach($facturacionnotacredito as $value)
          <tr>
               <td></td>
               <td>{{ $value->notacredito_serie}}</td>
               <td>{{ str_pad($value->notacredito_correlativo, 8, "0", STR_PAD_LEFT) }}</td>
               <td>{{ $value->notacredito_valorventa}}</td>
               <td>{{ $value->notacredito_totalimpuestos}}</td>
               <td>{{ $value->notacredito_montoimpuestoventa}}</td>
               <td>
                 @if($value->notacredito_tipomoneda=='PEN')
                    SOLES
                @elseif($value->notacredito_tipomoneda=='USD')
                    DOLARES
                @endif
               </td>
               <td>{{ date_format(date_create($value->notacredito_fechaemision), 'd/m/Y h:i:s A') }}</td>
               <td>{{ $value->cliente_numerodocumento}}</td>
               <td>{{ $value->cliente_razonsocial}}</td>
               <td>{{ $value->emisor_ruc}}</td>
               <td>{{ $value->emisor_nombrecomercial}}</td>
               <td>{{ $value->responsablenombre}}</td>
               <td>{{ $value->notacredito_numerodocumentoafectado}}</td>
               <td>{{ $value->notacredito_descripcionmotivo}}</td>
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