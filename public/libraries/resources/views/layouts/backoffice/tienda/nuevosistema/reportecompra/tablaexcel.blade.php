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
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Comprobante</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Correlativo</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Total</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Proveedor</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Fecha de registro</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Responsable</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Estado</th>
        </tr>
    </thead>
    <tbody>
         @foreach($s_compra as $value)
        <tr>
          <td></td>
          <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
          <td>{{ $value->nombreComprobante }}</td>
          <td>{{ $value->seriecorrelativo }}</td>
          <td>
              @if($value->totalredondeado==0)
              <?php $montototal = DB::table('s_compradetalle')->where('s_idcompra',$value->id)->sum('preciototal'); ?>
              {{ number_format($montototal, 2, '.', '') }}
            @else
              {{$value->totalredondeado}}
            @endif
           </td>
           <td>{{$value->apellidoProveedor}}</td>
           <td>{{ date_format(date_create($value->fecharegistro), 'd/m/Y h:i:s A') }}</td>
           <td>{{$value->responsablenombre}}</td>
           <td>
            @if($value->s_idestado==2)
              <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Comprado</span></div>
            @else
              <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fas fa-sync-alt"></i> Pendiente</span></div> 
            @endif
           </td>
        </tr>
        @endforeach
    </tbody>
</table>