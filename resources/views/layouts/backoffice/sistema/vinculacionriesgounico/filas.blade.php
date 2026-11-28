@php($total = 0)
@foreach($filas as $fila)
    @php($total += $fila['total'])
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $fila['identificacion'] }}</td>
        <td>{{ $fila['nombre'] }}</td>
        <td class="text-center">@foreach($fila['gestion'] as $cuenta)C{{ $cuenta['cuenta'] }}<br>@endforeach</td>
        <td class="text-end">@foreach($fila['gestion'] as $cuenta){{ number_format($cuenta['monto'], 2, '.', '') }}<br>@endforeach</td>
        <td class="text-center">@foreach($fila['familia'] as $cuenta)C{{ $cuenta['cuenta'] }}<br>@endforeach</td>
        <td class="text-end">@foreach($fila['familia'] as $cuenta){{ number_format($cuenta['monto'], 2, '.', '') }}<br>@endforeach</td>
        <td class="text-center">@foreach($fila['propiedad'] as $cuenta)C{{ $cuenta['cuenta'] }}<br>@endforeach</td>
        <td class="text-end">@foreach($fila['propiedad'] as $cuenta){{ number_format($cuenta['monto'], 2, '.', '') }}<br>@endforeach</td>
        <td class="text-end">{{ number_format($fila['total'], 2, '.', '') }}</td>
    </tr>
@endforeach
<tr class="tabla-total">
    <th colspan="9" class="text-end" style="text-align:right">TOTAL S/.</th>
    <th class="text-end" style="text-align:right">{{ number_format($total, 2, '.', '') }}</th>
</tr>
