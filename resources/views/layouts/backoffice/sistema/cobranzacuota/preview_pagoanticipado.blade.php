<div class="modal-header">
    <h5 class="modal-title">Previsualización - Pago Anticipado</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
@if(isset($error))
    <div class="alert alert-danger">
        <i class="fa-solid fa-triangle-exclamation"></i>
        {{ $error }}
    </div>
@else
    <div class="alert alert-info" style="font-size: 13px;">
        Monto simulado: <b>S/. {{ number_format($monto, 2, '.', '') }}</b>.
        @if(!empty($resultado['nuevo_cronograma_generado']))
            Se generaría un <b>nuevo cronograma</b> por el saldo restante de
            <b>S/. {{ $resultado['monto_saldo_nuevo'] }}</b>,
            desde la cuota N° <b>{{ $resultado['numerocuota_desde_nuevo'] }}</b>.
        @else
            El pago no genera un cronograma nuevo (no se marcó "generar crédito nuevo por la
            diferencia", o el monto ingresado alcanza a cubrir todas las cuotas pendientes).
        @endif
        <br>
        <span style="color:#6c757d;">Esta es solo una previsualización, no se ha guardado nada.</span>
    </div>

    <table class="table table-sm" style="width:100%;">
        <thead>
            <tr>
                <th>N° Cuota</th>
                <th>Fecha</th>
                <th>Capital</th>
                <th>Interés</th>
                <th>Cargo</th>
                <th>Cuota</th>
                <th>Estado tras el pago</th>
            </tr>
        </thead>
        <tbody>
        @foreach($cuotas as $c)
            @php
                $estado_antes = $estados_antes[$c->numerocuota] ?? null;
                $es_cuota_nueva = $numerocuota_ultima_anterior > 0 && $c->numerocuota > $numerocuota_ultima_anterior;
                if ($es_cuota_nueva) {
                    $label = 'CRONOGRAMA NUEVO';
                    $color = '#cfe2ff';
                } elseif ($estado_antes == 1 && $c->idestadocredito_cronograma == 2) {
                    $label = 'SE CANCELA CON ESTE PAGO';
                    $color = '#d1e7dd';
                } elseif ($c->idestadocredito_cronograma == 2) {
                    $label = 'YA PAGADA/CANCELADA';
                    $color = '#efefef';
                } else {
                    $label = 'SIGUE PENDIENTE';
                    $color = '#fff3cd';
                }
            @endphp
            <tr style="background-color: {{ $color }} !important;">
                <td>{{ $c->numerocuota }}</td>
                <td>{{ date_format(date_create($c->fechapago), 'd-m-Y') }}</td>
                <td>{{ $c->amortizacion }}</td>
                <td>{{ $c->interes }}</td>
                <td>{{ $c->cargo }}</td>
                <td>{{ $c->cuota_real }}</td>
                <td><b>{{ $label }}</b></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
</div>
<div class="row mt-1">
    <div class="col" style="flex: 0 0 0%;">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i> CERRAR</button>
    </div>
</div>
