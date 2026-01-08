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
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Persona Responsable</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Persona Asignado</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Caja</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Apertura</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Cierre</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Fecha de Apertura</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Fecha de Cierre</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($s_aperturacierres as $value)
        <tr>
          <td></td>
          <td>{{$value->usersresponsableapellidos}}, {{$value->usersresponsablenombre}}</td>
          <td>{{$value->usersrecepcionapellidos}}, {{$value->usersrecepcionnombre}}</td>
          <td>{{$value->cajanombre}}</td>
          <td>{{$value->montoasignar}}</td>
          <td>{{$value->montocierre}}</td>
                <td>{{ date_format(date_create($value->fechaconfirmacion), 'd/m/Y h:i:s A') }}</td>
          <td>{{ date_format(date_create($value->fechacierreconfirmacion), 'd/m/Y h:i:s A') }}</td>
          <td>
            @if($value->s_idestado==1)
              <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-sync-alt"></i> Apertura en Proceso</span></div>
            @elseif($value->s_idestado==2 && $value->fechaconfirmacion=='')
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Apertura Pendiente</span></div> 
            @elseif($value->s_idestado==2 && $value->fechaconfirmacion!='')
              <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Aperturado</span></div>
            @elseif($value->s_idestado==3 && $value->fechacierreconfirmacion=='')
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Cierre Pendiente</span></div>
            @elseif($value->s_idestado==3 &&$value->fechacierreconfirmacion!='')
              <div class="td-badge"><span class="badge badge-pill badge-primary"><i class="fa fa-check"></i> Cerrado</span></div>
            @endif
          </td>
        </tr>
        @endforeach
    </tbody>
</table>