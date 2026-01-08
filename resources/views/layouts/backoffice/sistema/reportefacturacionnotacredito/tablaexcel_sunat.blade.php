<table style="width:100%">
    <thead>
        <tr></tr>
        <tr>
            <th></th>
            <th style="font-weight: 900; background-color:#31353d; color: #ffffff; text-align: center; font-size: 12px; " colspan="17">
              {{ $titulo }}
            </th>
        </tr>
    
        @if($inicio != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">Fecha de Inicio:</th>
            <th style="font-weight: 900;" colspan="16">{{date_format(date_create($inicio), 'd/m/Y') }}</th>
        </tr>
        @endif
        @if($fin != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">Fecha de Fin:</th>
            <th style="font-weight: 900;" colspan="16">{{date_format(date_create($fin), 'd/m/Y') }}</th>
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
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Tipo de Documento Referencial</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Serie-Correlativo Referencial</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Fecha Referencial</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">IGV Referencial</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Base Imponible Referencial</th>
        </tr>
    </thead>
    <tbody>
          <?php $i=1;?>
           @foreach($facturacionnotacredito as $value)
          <tr>
             <td></td>
             <td>{{ $i }}</td>
             <td>{{ date_format(date_create($value->notacredito_fechaemision), 'd/m/Y') }}</td>
             <td>{{ $value->emisor_ruc}}</td>
             <td>{{ $value->emisor_razonsocial}}</td>
             <td>{{ $value->notacredito_tipodocumento}}</td>
             <td>{{ $value->notacredito_serie}}-{{ $value->notacredito_correlativo }}</td>
             <td>
               @if($value->notacredito_tipomoneda=='PEN')
                  SOLES
              @elseif($value->notacredito_tipomoneda=='USD')
                  DOLARES
              @endif
             </td>
             <td>{{ $value->cliente_numerodocumento}}</td>
             <td>{{ $value->cliente_razonsocial}}</td>
             <td>{{ $value->notacredito_valorventa}}</td>
             <td>{{ $value->notacredito_totalimpuestos}}</td>
             <td>{{ $value->notacredito_montoimpuestoventa}}</td>
              <?php
                $serie_correlativo = explode('-',$value->notacredito_numerodocumentoafectado);
                $tipo_documento_referencial = '';
                $fecha_referencial = '';
                $igv_referencial = '';
                $base_imponible_referencial = '';
                if(count($serie_correlativo)>1){
                    $facturacionboletafactura = DB::table('s_facturacionboletafactura')
                      ->where('s_facturacionboletafactura.idtienda',$idtienda)
                      ->where('s_facturacionboletafactura.venta_serie',$serie_correlativo[0])
                      ->where('s_facturacionboletafactura.venta_correlativo',$serie_correlativo[1])
                      ->first();
                    if($facturacionboletafactura!=''){
                        $tipo_documento_referencial = $facturacionboletafactura->venta_tipodocumento;
                        $fecha_referencial = date_format(date_create($facturacionboletafactura->venta_fechaemision), 'd/m/Y');
                        $igv_referencial = $facturacionboletafactura->venta_totalimpuestos;
                        $base_imponible_referencial = $facturacionboletafactura->venta_valorventa;
                    }
                }   
              ?>
             <td>{{ $tipo_documento_referencial }}</td>
             <td>{{ $value->notacredito_numerodocumentoafectado }}</td>
             <td>{{ $fecha_referencial }}</td>
             <td>{{ $igv_referencial }}</td>
             <td>{{ $base_imponible_referencial }}</td>
         </tr>
          <?php $i++ ?>
          @endforeach
    </tbody>
</table>