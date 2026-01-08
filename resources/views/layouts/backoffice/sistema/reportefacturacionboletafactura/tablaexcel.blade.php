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
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Comprobante</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Serie</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Correlativo</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Base Imp.</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">IGV	</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Total</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Moneda</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Fecha de Emisión</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">DNI/RUC</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Cliente</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">RUC</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Emisor</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Responsable</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Cod. Venta</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">SUNAT</th>
        </tr>
    </thead>
    <tbody>
           @foreach($facturacionboletafactura as $value)
        <tr>
             <td></td>
             <td>
               @if($value->venta_tipodocumento=='03')
                  BOLETA
              @elseif($value->venta_tipodocumento=='01')
                  FACTURA
              @else
                  TICKET
              @endif
             </td>
             <td>{{ $value->venta_serie}}</td>
             <td>{{ str_pad($value->venta_correlativo, 8, "0", STR_PAD_LEFT) }}</td>
             <td>{{ $value->venta_valorventa}}</td>
             <td>{{ $value->venta_totalimpuestos}}</td>
             <td>{{ $value->venta_montoimpuestoventa}}</td>
             <td>
               @if($value->venta_tipomoneda=='PEN')
                  SOLES
              @elseif($value->venta_tipomoneda=='USD')
                  DOLARES
              @endif
             </td>
             <td>{{ date_format(date_create($value->venta_fechaemision), 'd/m/Y h:i:s A') }}</td>
             <td>{{ $value->cliente_numerodocumento}}</td>
             <td>{{ $value->cliente_razonsocial}}</td>
             <td>{{ $value->emisor_ruc}}</td>
             <td>{{ $value->emisor_razonsocial}}</td>
             <td>{{ $value->responsablenombre}}</td>
             <td>{{ $value->ventacodigo!=''?str_pad($value->ventacodigo, 8, "0", STR_PAD_LEFT):'---' }}</td>
             <td>

              @if($value->comunicacionbaja_correlativo!='' && $value->venta_tipodocumento=='01')
                  <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado (CB: {{$value->comunicacionbaja_correlativo}})</span></div>    
              @elseif($value->resumen_correlativo!='' && $value->venta_tipodocumento=='03')
                  @if($value->resumen_estado=='1')
                    <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Adicionado (RD: {{$value->resumen_correlativo}})</span></div>  
                  @elseif($value->resumen_estado=='2')
                    <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Modificado (RD: {{$value->resumen_correlativo}})</span></div>  
                  @elseif($value->resumen_estado=='3')
                    <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado (RD: {{$value->resumen_correlativo}})</span></div>  
                  @endif 
              @else
                  @if($value->respuestaestado=='ACEPTADA')
                    <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Aceptada</span></div>
                  @elseif($value->respuestaestado=='OBSERVACIONES')
                    <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Observaciones</span></div> 
                  @elseif($value->respuestaestado=='RECHAZADA')
                    <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Rechazada</span></div> 
                  @elseif($value->respuestaestado=='EXCEPCION')
                    @if($value->respuestacodigo==1033)
                    <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Aceptada</span></div>
                    @else
                    <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-sync-alt"></i> Excepción</span></div>
                    @endif
                  @else
                    <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> No enviado</span></div>
                  @endif
              @endif
             </td>
         </tr>
         @endforeach
    </tbody>
</table>