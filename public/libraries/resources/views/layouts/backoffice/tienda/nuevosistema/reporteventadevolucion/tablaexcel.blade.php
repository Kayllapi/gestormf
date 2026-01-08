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
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Código de Devolución</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Total</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Moneda</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Fecha Registro</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Responsable</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Comprobante</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">DNI/RUC</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Cliente</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Tipo Entega</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Motivo</th>
          <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Estado</th>
        </tr>
    </thead>
    <tbody>
            @foreach($ventadevoluciondetalle as $value)
        <tr>
          <td></td>
          <td>{{ str_pad($value->codigoventa, 8, "0", STR_PAD_LEFT) }}</td>
          <td>{{ str_pad($value->codigoimpresion, 8, "0", STR_PAD_LEFT) }}</td>
          <td>{{ $value->total}}</td>
          <td>{{ $value->nombremoneda}}</td>
          <td>{{ date_format(date_create($value->fechaconfirmacion),"d/m/Y h:i:s A")}}</td>
          <td>{{ $value->apellidosresponsable}},{{ $value->nombreresponsable}}</td>
          <td>{{ $value->nombreComprobante}}</td>
          <td>{{ $value->identificacioncliente}}</td>
          <td>{{ $value->apellidocliente}},{{ $value->nombrecliente}}</td>
          <td>{{ $value->nombretipoentrega}}</td>
          <td>{{ $value->motivo}}</td>
          <td> 
          @if($value->s_idestado==1)
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Pendiente</span></div> 
            @elseif($value->s_idestado==2)
              <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fas fa-check"></i> Confirmado</span></div> 
            @elseif($value->s_idestado==3)
              <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span></div>
            @endif
          </td>
        </tr>
         @endforeach
    </tbody>
</table>