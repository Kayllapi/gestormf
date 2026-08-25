@php
    $titulos_modalidad = [
        'reduccion_plazo' => 'Previsualización - 1. Pago Anticipado Parcial (con Reducción de Plazo)',
        'reduccion_cuota' => 'Previsualización - 2. Pago Anticipado Parcial (con Reducción de Cuota)',
        'cancelacion_total' => 'Previsualización - 3. Pago Anticipado Total (Cancelación)',
    ];
    $titulo_modal = $titulos_modalidad[$modalidad ?? ''] ?? 'Previsualización - Pago Anticipado';
@endphp
<div class="modal-header">
    <h5 class="modal-title">{{ $titulo_modal }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
@if(isset($error))
    <div class="alert alert-danger">
        <i class="fa-solid fa-triangle-exclamation"></i>
        {{ $error }}
    </div>
@else
    <div class="alert alert-info" style="font-size: 13px; text-align: left; border: 1px solid #f8ebc4 !important; background-color: #f8ebc4 !important;">
        Monto a abonar: <b>S/. {{ number_format($monto, 2, '.', '') }}</b>.
        @if(!empty($resultado['cuota_reducida']))
            <b>Reducción de Cuota:</b> se recalcularían las cuotas pendientes sobre el saldo
            restante de <b>S/. {{ $resultado['monto_saldo_nuevo'] }}</b>; la nueva cuota sería de
            aproximadamente <b>S/. {{ $resultado['cuota_pago_nueva'] }}</b> (mismas fechas, mismo
            N° de cuotas).
        @elseif(!empty($resultado['plazo_reducido']))
            <b>Reducción de Plazo:</b> las cuotas restantes conservan su monto original y solo
            adelantan sus fechas; se eliminarían <b>{{ $resultado['cuotas_eliminadas'] }}</b>
            cuota(s) ya cubiertas por el abono, y el crédito terminaría el
            <b>{{ $resultado['fecha_ultimopago_nueva'] }}</b>.
        @else
            El pago cubre las cuotas seleccionadas sin generar cambios adicionales al
            cronograma (si eligió "Cancelación Total", esto significa que el crédito quedaría
            completamente cancelado).
        @endif
    </div>

    @if($modalidad == 'cancelacion_total')
        <table class="table table-sm table-bordered" style="width:100%;max-width:420px;">
            <tr>
                <td>Total de deuda</td>
                <td style="text-align:right;">S/. {{ number_format($total_sin_descuento, 2, '.', '') }}</td>
            </tr>
            <tr>
                <td>Descuento (Interés + Carg. x Cust. G./Ot. + Ss. Recau.)</td>
                <td style="text-align:right;color:#198754;">- S/. {{ number_format($total_descuento, 2, '.', '') }}</td>
            </tr>
            <tr style="font-weight:bold;background-color:#f8f9fa;">
                <td>Monto exacto a cancelar</td>
                <td style="text-align:right;">S/. {{ number_format($total_sin_descuento - $total_descuento, 2, '.', '') }}</td>
            </tr>
        </table>
    @endif

    <table class="table table-sm" style="width:100%;">
        <thead>
            <tr>
                <th>N° Cuota</th>
                <th>Fecha</th>
                <th>Capital</th>
                <th>Interés</th>
                <th>Cargo</th>
                <th>Ss. Recau.</th>
                <th>Cuota</th>
                <th>Estado tras el pago</th>
            </tr>
        </thead>
        <tbody>
        @php
            $total_capital = 0;
            $total_interes = 0;
            $total_cargo = 0;
            $total_comision = 0;
            $total_cuota = 0;
        @endphp
        @foreach($cuotas as $c)
            @php
                $total_capital += (float) $c->amortizacion;
                $total_interes += (float) $c->interes;
                $total_cargo += (float) $c->cargo;
                $total_comision += (float) $c->comision;
                $total_cuota += (float) $c->cuota_real;

                $estado_antes = $estados_antes[$c->id] ?? null;
                $fecha_antes = $fechas_antes[$c->id] ?? null;
                $monto_antes = $montos_antes[$c->id] ?? null;
                $numero_antes = $monto_antes->numerocuota ?? null;
                $numero_cambio = $numero_antes !== null && $numero_antes != $c->numerocuota;
                $monto_cambio = $monto_antes && (
                    $monto_antes->amortizacion != $c->amortizacion
                    || $monto_antes->interes != $c->interes
                    || $monto_antes->cargo != $c->cargo
                    || $monto_antes->comision != $c->comision
                    || $monto_antes->cuota_real != $c->cuota_real
                );
                if ($estado_antes == 1 && $c->idestadocredito_cronograma == 2) {
                    $label = 'SE CANCELA CON ESTE PAGO';
                    $color = '#d1e7dd';
                } elseif ($monto_cambio) {
                    $label = 'MONTO RECALCULADO (cuota antes: '.number_format($monto_antes->cuota_real, 2, '.', '').')';
                    $color = '#f8ebc4';
                } elseif ($fecha_antes && $fecha_antes != $c->fechapago) {
                    $label = 'FECHA REPROGRAMADA (antes: N° '.$numero_antes.', '.date_format(date_create($fecha_antes),'d-m-Y').')';
                    $color = '#f8ebc4';
                } elseif ($numero_cambio) {
                    $label = 'RENUMERADA (antes: N° '.$numero_antes.')';
                    $color = '#f8ebc4';
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
                <td>{{ $c->comision }}</td>
                <td>{{ $c->cuota_real }}</td>
                <td><b>{{ $label }}</b></td>
            </tr>
        @endforeach
        @php
            // Filas sinteticas "ELIMINADA": no corresponden a ninguna fila real de la base de
            // datos (esas cuotas ya no existen), se arman solo para mostrar, al final de la
            // numeracion consecutiva, cuantas cuotas y que fechas del calendario original ya no
            // hacen falta.
            $numero_siguiente = ($cuotas->max('numerocuota') ?? 0) + 1;
            $fechas_eliminadas = $resultado['fechas_eliminadas'] ?? [];
        @endphp
        @foreach($fechas_eliminadas as $fecha_eliminada)
            <tr style="background-color: #f8d7da !important;">
                <td>{{ $numero_siguiente++ }}</td>
                <td>{{ $fecha_eliminada }}</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td><b>ELIMINADA</b></td>
            </tr>
        @endforeach
        </tbody>
        <thead>
            <tr style="font-weight:bold;">
                <th colspan="2" style="text-align: right;">TOTALES:</th>
                <th>{{ number_format($total_capital, 2, '.', '') }}</th>
                <th>{{ number_format($total_interes, 2, '.', '') }}</th>
                <th>{{ number_format($total_cargo, 2, '.', '') }}</th>
                <th>{{ number_format($total_comision, 2, '.', '') }}</th>
                <th>{{ number_format($total_cuota, 2, '.', '') }}</th>
                <th></th>
            </tr>
        </thead>
    </table>
@endif
</div>
<div class="row mt-1">
    <div class="col" style="flex: 0 0 0%;">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i> CERRAR</button>
    </div>
</div>
