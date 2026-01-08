@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
  <div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Detalle Cobranza</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-6">
      <label>Código</label>
      <input type="text" value="{{ $cobranza->codigo }}" disabled>
      <label>Fecha de Pago</label>
      <input type="text" value="{{ date_format(date_create($cobranza->fecharegistro), "d/m/Y h:i:s A") }}" disabled>
      <label>Agencia</label>
      <input type="text" value="{{ $agencia->nombrecomercial }}" disabled>
    </div>
    <div class="col-sm-6">
      <label>Cliente</label>
      <input type="text" value="{{ $cobranza->cliente_identificacion }} - {{ $cobranza->cliente }}" disabled>
      <label>Asesor</label>
      <input type="text" value="{{ $cobranza->asesor_apellidos }}, {{ $cobranza->asesor_nombre }} " disabled>
      <label>Ventanilla</label>
      <input type="text" value="{{ $cobranza->cajero_apellidos }}, {{ $cobranza->cajero_nombre }} " disabled>
    </div>
  </div>
  <table class="table">
    <thead style="background: #31353d; color: #fff;">
      <tr>
        <td style="padding: 8px; text-align: center;">Nº</td>
        <td style="padding: 8px; text-align: center;">Cuota</td>
        <td style="padding: 8px; text-align: center;">Mora</td>
        <td style="padding: 8px; text-align: center;">Total</td>
      </tr>
    </thead>
    <tbody>
      @foreach ($cobranzadetalle as $value)
        <?php
          $credito = DB::table('s_prestamo_creditodetalle')
            ->whereId($value->idprestamo_creditodetalle)
            ->first();
        ?>
        <tr>
          <td style="padding: 8px; text-align: center;">{{ $credito->numero }}</td>
          <td style="padding: 8px; text-align: center;">{{ $credito->cuota }}</td>
          <td style="padding: 8px; text-align: center;">{{ $credito->moraapagar }}</td>
          <td style="padding: 8px; text-align: center;">{{ $credito->total }}</td>
        </tr>
      @endforeach
      <tr>
        <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL CUOTA:</td>
        <td style="white-space: nowrap; padding: 8px; text-align: center;">{{ $cobranza->total_cuota }}</td>
      </tr>
      <tr>
        <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL MORA:</td>
        <td style="white-space: nowrap; padding: 8px; text-align: center;">{{ $cobranza->total_mora }}</td>
      </tr>
      <tr>
        <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL:</td>
        <td style="white-space: nowrap; padding: 8px; text-align: center;">{{ $cobranza->total_cuotaapagar }}</td>
      </tr>
    </tbody>
  </table>
@endsection
@section('subscripts')
@endsection