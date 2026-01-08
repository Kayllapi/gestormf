<table style="width:100%">
    <thead>
        <tr></tr>
        <tr>
            <th></th>
            <th style="font-weight: 900; background-color:#31353d; color: #ffffff; text-align: center; font-size: 12px; " colspan="10">
              {{ $titulo }}
            </th>
        </tr>
    
        @if($inicio != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">Fecha de Inicio:</th>
            <th style="font-weight: 900;" colspan="9">{{date_format(date_create($inicio), 'd/m/Y') }}</th>
        </tr>
        @endif
        @if($fin != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">Fecha de Fin:</th>
            <th style="font-weight: 900;" colspan="9">{{date_format(date_create($fin), 'd/m/Y') }}</th>
        </tr>
        @endif
        <tr></tr>
        <tr>
            <th></th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Código</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Comprobante</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Total</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Tipo de Entrega</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Cliente</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Fecha Registro</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Fecha Vendida</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Vendedor</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Caja</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($s_venta as $value)
        <tr>
          <td></td>
          <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
          <td>{{ $value->nombreComprobante }}</td>
          <td>
          @if($value->totalredondeado==0)
               {{ number_format($value->montoredondeado, 2, '.', '') }}
               @else
                 {{$value->totalredondeado}}
           @endif
          </td>
           <td>{{$value->tipoentreganombre}}</td>
       
          <td>{{$value->clientenombre}}</td>
          <td>{{ ($value->s_idestado==2 or $value->s_idestado==3 or $value->s_idestado==4) ? date_format(date_create($value->fecharegistro),"d/m/Y h:i:s A") : '---' }}</td>
          <td>{{ ($value->s_idestado==3 or $value->s_idestado==4) ? date_format(date_create($value->fechaconfirmacion),"d/m/Y h:i:s A") : '---' }}</td>
          <td>{{$value->responsableregistronombre}}</td>
          <td>{{$value->responsablenombre}}</td>
          <td>
                        @if($value->s_idestado==1)
                          <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Pendiente</span></div> 
                        @elseif($value->s_idestado==2)
                          <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fas fa-sync-alt"></i> Confirmado</span></div> 
                        @elseif($value->s_idestado==3)
                          <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Vendido</span></div>
                        @elseif($value->s_idestado==4)
                          <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span></div>
                        @endif
                      </td> 
        </tr>
        @endforeach
    </tbody>
</table>