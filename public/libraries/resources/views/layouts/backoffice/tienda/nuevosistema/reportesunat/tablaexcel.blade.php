<table style="width:100%">
    <thead>
        <tr></tr>
        <tr>
            <th></th>
            <th style="font-weight: 900; background-color:#31353d; color: #ffffff; text-align: center; font-size: 12px; " colspan="8">
              {{ $titulo }}
            </th>
        </tr>
    
        @if($inicio != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">Fecha de Inicio:</th>
            <th style="font-weight: 900;" colspan="7">{{date_format(date_create($inicio), 'd/m/Y') }}</th>
        </tr>
        @endif
        @if($fin != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">Fecha de Fin:</th>
            <th style="font-weight: 900;" colspan="7">{{date_format(date_create($fin), 'd/m/Y') }}</th>
        </tr>
        @endif
        <tr></tr>
        <tr>
          <th></th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Fecha de Registro</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Código</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Agencia</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Mensaje</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Comprobante</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Serie</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Correlativo</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Estado</th>
        </tr>
    </thead>
    <tbody>
       @foreach($facturacionrespuesta as $value)
        <?php 
        $data = ''; 
        if (!is_null($value->s_idfacturacionboletafactura)){
          $data = DB::table('s_facturacionboletafactura as data')
            ->whereId($value->s_idfacturacionboletafactura)
            ->select(
              'data.*',
              'data.venta_serie as emisor_serie',
              'data.venta_correlativo as emisor_correlativo'
            )
            ->first();
        }
        elseif (!is_null($value->s_idfacturacionnotacredito)) {
          $data = DB::table('s_facturacionnotacredito as data')
            ->whereId($value->s_idfacturacionnotacredito)
            ->select(
              'data.*',
              'data.notacredito_serie as emisor_serie',
              'data.notacredito_correlativo as emisor_correlativo'
            )
            ->first();
        }
        elseif (!is_null($value->s_idfacturacionresumendiario)) {
          $data = DB::table('s_facturacionresumendiario as data')
            ->whereId($value->s_idfacturacionresumendiario)
            ->select(
              'data.*',
              'data.resumen_correlativo as emisor_correlativo'
            )
            ->first();
        }
        elseif (!is_null($value->s_idfacturacioncomunicacionbaja)) {
          $data = DB::table('s_facturacioncomunicacionbaja as data')
            ->whereId($value->s_idfacturacionboletafactura)
            ->select(
              'data.*',
              'data.comunicacionbaja_correlativo as emisor_correlativo'
            )
            ->first();
        }
        elseif (!is_null($value->s_idfacturacionnotadebito)) {
          $data = DB::table('s_facturacionnotadebito as data')
            ->whereId($value->s_idfacturacionnotadebito)
            ->select(
              'data.*',
              'data.notadebito_serie as emisor_serie',
              'data.notadebito_correlativo as emisor_correlativo'
            )
            ->first();
        }
        elseif ((!is_null($value->s_idfacturacionguiaremision))) {
          $data = DB::table('s_facturacionguiaremision as data')
            ->whereId($value->s_idfacturacionboletafactura)
            ->select(
              'data.*',
              'data.despacho_serie as emisor_serie',
              'data.despacho_correlativo as emisor_correlativo'
            )
            ->first();
        }
        ?>
        <tr>
          <td></td>
          <td>{{ date_format(date_create($value->fecharegistro),"d/m/Y h:i:s A")}}</td>
          <td>{{ $value->codigo }}</td>
          <td>{{ $data->emisor_ruc ?? '' }} - {{ $data->emisor_razonsocial ?? '' }}</td>
          <td>{{ $value->mensaje }}</td>
          <td>
               @if($value->venta_tipodocumento == '01')
                     FACTURA
               @elseif($value->venta_tipodocumento == '03')
                     BOLETA 
               @elseif($value->notacredito_tipodocumento == '07')
                     NOTA CREDITO
               @elseif($value->notadebito_tipodocumento == '08')
                     NOTA DEBITO
               @elseif($value->despacho_tipodocumento == '09')
                     GUIA REMISIÓN
            @endif
          </td>
          <td>{{ $data->emisor_serie ?? '' }}</td>
          <td>{{ $data->emisor_correlativo ?? '' }}</td>
          <td>
              @if($value->estado == 'ACEPTADA')
                     ACEPTADA
               @elseif($value->estado == 'OBSERVACIONES')
                     OBSERVACIONES 
               @elseif($value->estado == 'RECHAZADA')
                     RECHAZADA
               @elseif($value->estado == 'EXCEPCION')
                     EXCEPCION
               @else
                     ERROR
               @endif 
          </td>
        </tr>
        @endforeach
    </tbody>
</table>