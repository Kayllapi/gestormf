<!DOCTYPE html>
<html>
<head>
    <title>CRONOGRAMA DE PAGOS</title>
    @include('app.pdf_style',['idtienda'=>$tienda->id])
</head>
<body>
    @include('app.pdf_headerfooter',[
        'logo'=>$prestamodesembolso->facturacion_agencialogo,
        'nombrecomercial'=>$prestamodesembolso->facturacion_agencianombrecomercial,
        'ruc'=>$prestamodesembolso->facturacion_agenciaruc,
        'direccion'=>$prestamodesembolso->facturacion_agenciadireccion,
        'ubigeo'=>$prestamodesembolso->facturacion_agenciaubigeonombre,
        'tienda'=>$tienda,
    ])
    <div class="titulo">CRONOGRAMA DE PAGOS</div>
    <div class="content">
        <table class="tabla_informativa">
            <tr>
                <td style="width:14%;">CLIENTE</td>
                <td style="width:1%;">:</td>
                <td style="width:50%;">{{ $prestamodesembolso->cliente_nombre }}</td>
                <td style="width:14%;">MONEDA</td>
                <td style="width:1%;">:</td>
                <td style="width:20%;">{{ $prestamodesembolso->monedanombre }}</td>
            </tr>
            <tr>
                <td>DNI</td>
                <td>:</td>
                <td>{{ $prestamodesembolso->facturacion_cliente_identificacion }}</td>
                <td>DESEMBOLSADO</td>
                <td>:</td>
                <td>{{ $prestamodesembolso->monedasimbolo }} {{ $prestamodesembolso->monto }}</td>
            </tr>
            <tr>
                <td>ASESOR</td>
                <td>:</td>
                <td>{{ $prestamodesembolso->asesor_nombre }}</td>
                <td>Nº DE CUOTAS</td>
                <td>:</td>
                <td>{{ $prestamodesembolso->numerocuota }} CUOTAS</td>
            </tr>
            <tr>
                <td>VENTANILLA</td>
                <td>:</td>
                <td>{{ $prestamodesembolso->cajero_nombre }}</td>
                <td>FRECUENCIA</td>
                <td>:</td>
                <td>{{ $prestamodesembolso->frecuencia_nombre }}</td>
            </tr>
            <tr>
                <td>TASA DE INTERÉS</td>
                <td>:</td>
                <td>{{ $prestamodesembolso->tasa }}%</td>
                <td>FECHA DE INICIO</td>
                <td>:</td>
                <td>{{ date_format(date_create($prestamodesembolso->fechainicio),"d/m/Y") }}</td>
            </tr>
        </table>
        <div class="espacio"></div>
        <table class="tabla">
                <tr class="tabla_cabera">
                    <td style="text-align:center;">Nº</td>
                    <td style="text-align:center;">F.VENCIMIENTO</td>
                    <td style="text-align:center;">CAPITAL</td>
                    <td style="text-align:center;">INTERÉS</td>
                    @if($prestamodesembolso->total_segurodesgravamen>0)
                    <td style="text-align:center;">SEGURO DESGRAVAMEN</td>
                    @endif
                    @if($prestamodesembolso->total_gastoadministrativo>0)
                    <td style="text-align:center;">GASTO ADMINISTRATIVO</td>
                    @endif
                    <td style="text-align:center;">CUOTA</td>
                    @if($prestamodesembolso->total_abono>0)
                    <td style="text-align:center;">ABONO</td>
                    <td style="text-align:center;">TOTAL</td>
                    @endif
                </tr>
                @foreach ($prestamodesembolsodetalle as $value)
                <tr>
                    <td style="text-align:center;">{{ str_pad($value->numero, 2, "0", STR_PAD_LEFT) }}</td>
                    <td style="text-align:right;">{{ date_format(date_create($value->fechavencimiento),"d/m/Y") }}</td>
                    <td style="text-align:right;">{{ $value->amortizacion }}</td>
                    <td style="text-align:right;">{{ $value->interes }}</td>
                    @if($prestamodesembolso->total_segurodesgravamen>0)
                    <td style="text-align:right;">{{ $value->seguro }}</td>
                    @endif
                    @if($prestamodesembolso->total_gastoadministrativo>0)
                    <td style="text-align:right;">{{ $value->gastoadministrativo }}</td>
                    @endif
                    <td style="text-align:right;">{{ $value->total }}</td>
                    @if($prestamodesembolso->total_abono>0)
                    <td style="text-align:right;">{{ $value->abono }}</td>
                    <td style="text-align:right;">{{ $value->totalfinal }}</td>
                    @endif
                </tr>
                @endforeach
                <tr class="tabla_resultado">
                    <td colspan="2" style="text-align:right;">TOTAL</td>
                    <td style="text-align:right;">{{$prestamodesembolso->total_amortizacion}}</td>
                    <td style="text-align:right;">{{$prestamodesembolso->total_interes}}</td>
                    @if($prestamodesembolso->total_segurodesgravamen>0)
                    <td style="text-align:right;">{{$prestamodesembolso->total_segurodesgravamen}}</td>
                    @endif
                    @if($prestamodesembolso->total_gastoadministrativo>0)
                    <td style="text-align:right;">{{$prestamodesembolso->total_gastoadministrativo}}</td>
                    @endif
                    <td style="text-align:right;">{{$prestamodesembolso->total_cuotafinal}}</td>
                    @if($prestamodesembolso->total_abono>0)
                    <td style="text-align:right;">{{$prestamodesembolso->total_abono}}</td>
                    <td style="text-align:right;">{{$prestamodesembolso->total_cuotafinaltotal}}</td>
                    @endif
                </tr>
        </table>
        <div class="espacio"></div>
        <div class="espacio"></div>
        <div class="dato_firma">
          <div>___________________________________</div>
          <div>{{$prestamodesembolso->facturacion_cliente_nombre.' '.$prestamodesembolso->facturacion_cliente_apellidos}}</div>
          <div>DNI: {{$prestamodesembolso->facturacion_cliente_identificacion}}</div>
        </div>
    </div>
        
<style>
      .table {
        width: 100%;
        margin:0px;
        padding:0px;
        border-collapse: collapse;
        border-spacing: 0;
      }
      .table td {
        border:1px solid #ccc;
        padding: 5px;
      }
</style>
</body>
</html>