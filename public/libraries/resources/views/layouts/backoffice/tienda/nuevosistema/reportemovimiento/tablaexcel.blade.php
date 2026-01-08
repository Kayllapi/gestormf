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
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Código</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Tipo</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Concepto</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Descripción</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Monto</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Fecha de registro</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Responsable</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Estado</th>
        </tr>
    </thead>
    <tbody>
      <?php $total = 0 ?>
        @foreach($s_movimientos as $value)
       <?php $total += $value->monto ?>
        <tr>
          <td></td>
          <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
          <td>{{ $value->conceptomovimientotipo}}</td>
          <td>{{ $value->conceptomovimientonombre}}</td>
          <td>{{ $value->concepto}}</td>
          <td>{{ $value->monto}}</td>
          <td>{{ date_format(date_create($value->fecharegistro), 'd/m/Y h:i:s A') }}</td>
          <td>{{ $value->responsablenombre}}</td>
          <td>
            @if($value->fechaconfirmacion!='')
              <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div>
            @else
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Pendiente</span></div> 
            @endif
          </td>
        </tr>
        @endforeach
        <tr>
            <td></td>
            <td style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff; text-align: right;" colspan="7">
              Total:
            </td>
            <td style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff; text-align: center;">{{ $total }}</td>
        </tr>
    </tbody>
</table>