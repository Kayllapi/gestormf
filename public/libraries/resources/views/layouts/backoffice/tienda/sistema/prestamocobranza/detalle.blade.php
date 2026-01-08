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
      <label>Agencia</label>
      <input type="text" value="{{ $agencia->nombrecomercial }}" disabled>
      <label>Código</label>
      <input type="text" value="{{ $cobranza->codigo }}" disabled>
      <label>Crédito</label>
      <input type="text" value="{{ $cobranza->creditocodigo }}" disabled>
      <label>Fecha de Pago</label>
      <input type="text" value="{{ date_format(date_create($cobranza->fecharegistro), "d/m/Y h:i:s A") }}" disabled>
      <label>Cliente</label>
      <input type="text" value="{{ $cobranza->cliente_identificacion }} - {{ $cobranza->cliente }}" disabled>
    </div>
    <div class="col-sm-6">
      <label>Asesor</label>
      <input type="text" value="{{ $cobranza->asesor_apellidos }}, {{ $cobranza->asesor_nombre }}" disabled>
      <label>Ventanilla</label>
      <input type="text" value="{{ $cobranza->cajero_apellidos }}, {{ $cobranza->cajero_nombre }}" disabled>
      @if($cobranza->proximo_vencimiento!='')
      <label>Prox. Vencimiento</label>
      <input type="text" value="{{date_format(date_create($cobranza->proximo_vencimiento), "d/m/Y")}}" disabled>
      @endif
      <label>Tipo de Pago</label>
      @if($cobranza->cronograma_idtipopago==1)
      <input type="text" value="POR CUOTAS" disabled>
      @elseif($cobranza->cronograma_idtipopago==2)
      <input type="text" value="COMPLETO" disabled>
      @endif
      <label>Moneda</label>
      <input type="text" value="{{ $cobranza->monedanombre }}" disabled>
    </div>
  </div>
  
    <table class="table" style="width:100%;">
        <tr>
          <td colspan="2" style="text-align: center; height:10px;height: 1px;background-color: #eeeeee;padding: 2px;"></td>
        </tr>
        @if(count($cobranzadetalle)>0)
        @foreach ($cobranzadetalle as $value)
        <tr>
          <td style="font-weight: bold;">NRO DE CUOTA <div style="float:right">:</div></td>
          <td>{{ str_pad($value->numero, 2, "0", STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
          <td style="font-weight: bold;">CUOTA (+)<div style="float:right">:</div></td>
          <td>{{ $cobranza->monedasimbolo }} {{ $value->cuota }}</td>
        </tr>
        @if($value->moradescuento>0)
        <tr>
          <td style="font-weight: bold;">MORA (+)<div style="float:right">:</div></td>
          <td>{{ $cobranza->monedasimbolo }} {{ $value->mora }}</td>
        </tr>
        <tr>
          <td style="font-weight: bold;">DESC. MORA (-)<div style="float:right">:</div></td>
          <td>{{ $cobranza->monedasimbolo }} {{ $value->moradescuento }}</td>
        </tr>
        @endif
        <tr>
          <td style="font-weight: bold;">MORA A PAGAR (+)<div style="float:right">:</div></td>
          <td>{{ $cobranza->monedasimbolo }} {{ $value->moraapagar }}</td>
        </tr>
        @if($value->acuenta>0)
        <tr>
          <td style="font-weight: bold;">A CUENTA (-)<div style="float:right">:</div></td>
          <td>{{ $cobranza->monedasimbolo }} {{ $value->acuenta }}</td>
        </tr>
        @endif
        @if(count($cobranzadetalle)>1)
        <tr>
          <td style="font-weight: bold;">TOTAL <div style="float:right">:</div></td>
          <td>{{ $cobranza->monedasimbolo }} {{ $value->cuotaapagar }}</td>
        </tr>
        <tr>
          <td colspan="2" style="text-align: center; height:10px;height: 1px;background-color: #eeeeee;padding: 2px;"></td>
        </tr>
        @endif
        @endforeach
        @if(count($cobranzadetalle)==1)
        <tr style="background-color: #eeeeee;">
          <td colspan="2" style="text-align: center; height:10px;height: 1px;background-color: #eeeeee;padding: 2px;"></td>
        </tr>
        @endif
        <tr style="background-color: #eeeeee;">
          <td style="text-align: right;font-weight: bold;">TOTAL A PAGAR<div style="float:right;width: 10px;">:</div></td>
          <td>{{ $cobranza->monedasimbolo }} {{ $cobranza->cronograma_total }}</td>
        </tr>
        @endif
        @if($cobranza->cronograma_acuentaanterior>0)
        <tr style="background-color: #eeeeee;">
          <td style="text-align: right;font-weight: bold;">A CUENTA (ANTERIOR)<div style="float:right;width: 10px;">:</div></td>
          <td>{{ $cobranza->monedasimbolo }} {{ $cobranza->cronograma_acuentaanterior }}</td>
        </tr>
        @endif
        @if($cobranza->cronograma_idtipopago==1)
        <tr style="background-color: #eeeeee;">
          <td style="text-align: right;font-weight: bold;">TOTAL PAGADO<div style="float:right;width: 10px;">:</div></td>
          <td>{{ $cobranza->monedasimbolo }} {{ $cobranza->cronograma_montorecibido }}</td>
        </tr>
        @elseif($cobranza->cronograma_idtipopago==2)
        <tr style="background-color: #eeeeee;">
          <td style="text-align: right;font-weight: bold;">TOTAL PAGADO<div style="float:right;width: 10px;">:</div></td>
          <td>{{ $cobranza->monedasimbolo }} {{ $cobranza->cronograma_pagado }}</td>
        </tr>
        @endif
        @if($cobranza->cronograma_vuelto>0)
        <tr style="background-color: #eeeeee;">
          <td style="text-align: right;font-weight: bold;">VUELTO<div style="float:right;width: 10px;">:</div></td>
          <td>{{ $cobranza->monedasimbolo }} {{ $cobranza->cronograma_vuelto }}</td>
        </tr>
        @endif
        @if($cobranza->cronograma_acuentaproxima>0)
        <tr style="background-color: #eeeeee;">
          <td style="text-align: right;font-weight: bold;">A CUENTA (PRÓXIMA)<div style="float:right;width: 10px;">:</div></td>
          <td>{{ $cobranza->monedasimbolo }} {{ $cobranza->cronograma_acuentaproxima }}</td>
        </tr>
        @endif
        @if($cobranza->cronograma_acuentatotal>0 && $cobranza->cronograma_acuentaanterior>0)
        <tr style="background-color: #eeeeee;">
          <td style="text-align: right;font-weight: bold;">TOTAL A CUENTA<div style="float:right;width: 10px;">:</div></td>
          <td>{{ $cobranza->monedasimbolo }} {{ $cobranza->cronograma_acuentatotal }}</td>
        </tr>
        @endif
    </table>
<style>
  .table tr td {
    padding: 8px;
  }
</style>
@endsection
@section('subscripts')
@endsection