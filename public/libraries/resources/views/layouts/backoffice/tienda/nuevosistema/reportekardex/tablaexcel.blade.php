<table style="width:100%">
    <thead>
        <tr></tr>
        <tr>
            <th></th>
            <th style="font-weight: 900; background-color:#31353d; color: #ffffff; text-align: center; font-size: 12px; " colspan="8">
              {{ $titulo }}
            </th>
        </tr>
        <tr></tr>
        <tr>
            <th></th>
              <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Fecha / Hora</th>
              <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Concepto</th>
              <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Producto</th>
              <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Cant.</th>
              <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">P. Unitario</th>
              <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">P. Total</th>
              <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Saldo</th>
              <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Restante</th>
        </tr>
    </thead>
    <tbody>
          @foreach($productosaldos as $value)
        <?php
        $class_dato = 'td_dato';
        $class_es = 'td_es';
        $class_saldo = 'td_saldo';
        if($value->concepto=='SALDO INICIAL'){
            $class_dato = 'td_reset_dato';
            $class_es = 'td_reset_es';
            $class_saldo = 'td_reset_saldo';
        }
        ?>
        <tr>
          <td></td>
          @if($value->concepto=='SALDO INICIAL')
          <td class="{{$class_dato}}" colspan="3">{{ $value->concepto }}</td>
          @else
          <td class="{{$class_dato}}">{{ $value->concepto=='SALDO INICIAL'?'': date_format(date_create($value->fecharegistro), 'd/m/Y h:i:s A') }}</td>
          <td class="{{$class_dato}}">{{ $value->concepto }}</td>
          <?php $list_produtc = explode('/<br>/',$value->producto) ?>
          <td class="{{$class_dato}}">
            @if(count($list_produtc)>1)
            @for($i=1;$i<count($list_produtc);$i++)
            {{ $list_produtc[$i] }}<br>
            @endfor
            @else
            {{$value->producto}}
            @endif
          </td>
          @endif
          <td class="{{$class_es}}">{{ $value->cantidad }} - {{ $value->cantidadrestante }}</td>
          <td class="{{$class_es}}">{{ $value->preciounitario }}</td>
          <td class="{{$class_es}}">{{ $value->preciototal }}</td>
          <td class="{{$class_saldo}}">{{ $value->saldo_cantidad }}</td>
          <td class="{{$class_saldo}}">{{ $value->saldo_cantidadrestante }}</td>
        </tr>
        @endforeach
    </tbody>
</table>