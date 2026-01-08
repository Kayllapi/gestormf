<table style="width:100%">
    <thead>
        <tr></tr>
        <tr>
            <th></th>
            <th style="font-weight: 900; background-color:#31353d; color: #ffffff; text-align: center; font-size: 12px; " colspan="7">
              {{ $titulo }}
            </th>
        </tr>
    
        @if($inicio != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">Fecha de Inicio:</th>
            <th style="font-weight: 900;" colspan="6">{{ date_format(date_create($inicio), 'd/m/Y') }}</th>
        </tr>
        @endif
        @if($fin != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">Fecha de Fin:</th>
            <th style="font-weight: 900;" colspan="6">{{ date_format(date_create($fin), 'd/m/Y') }}</th>
        </tr>
        @endif
        <tr></tr>
        <tr>
            <th></th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Tipo</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Motivo</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Responsable</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Producto</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Cantidad</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Fecha de registro</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($s_productomovimiento as $value)
        <tr>
          <td></td>
          <td>{{$value->nombretipomovimiento}}</td>
          <td>{{$value->motivo}}</td>
          <td>{{$value->responsablenombre}}</td>
          <td>{{$value->productonombre}}</td>
          <td>{{$value->cantidad}}</td>
          <td>{{ date_format(date_create($value->fecharegistro), 'd/m/Y h:i:s A') }}</td>
          <td>
             @if($value->s_idestado==2)
              <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div>
            @else
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Pendiente</span></div> 
            @endif
          </td>
        </tr>
        @endforeach
    </tbody>
</table>