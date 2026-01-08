@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
  <div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Anular Cobranza</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
  </div>
  <form action="javascript:;" 
        onsubmit="callback({
                            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamocobranza/{{ $cobranza->id }}',
                            method: 'PUT',
                            data:   {
                              view: 'anular_pagorealizado'
                            }
                          },
                          function(resultado){
                              //location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza') }}';
                          },this)">
  <div class="row">
    <div class="col-sm-6">
      <label>Código de Cobranza</label>
      <input type="text" value="{{ str_pad($cobranza->codigo, 8, "0", STR_PAD_LEFT) }}" disabled>
      <label>Código ce Crédito</label>
      <input type="text" value="{{ str_pad($cobranza->creditocodigo, 8, "0", STR_PAD_LEFT) }}" disabled>
      <label>Cliente</label>
      <input type="text" value="{{ $cobranza->cliente_identificacion }} - {{ $cobranza->cliente }}" disabled>
      <label>Ventanilla</label>
      <input type="text" value="{{ $cobranza->cajero_apellidos }}, {{ $cobranza->cajero_nombre }}" disabled>
    </div>
    <div class="col-sm-6">
      <label>Agencia</label>
      <input type="text" value="{{ $agencia->nombrecomercial }}" disabled>
      <label>Fecha de Pago</label>
      <input type="text" value="{{ date_format(date_create($cobranza->fecharegistro), "d/m/Y h:i:s A") }}" disabled>
      <label>Moneda</label>
      <input type="text" value="{{ $cobranza->monedanombre }}" disabled>
      <label>Total</label>
      <input type="text" value="{{ $cobranza->cronograma_pagado }}" disabled>
    </div>
  </div>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro de Anular la Cobranza?</b>
    </div>
    <button type="submit" class="btn  mx-btn-post"><i class="fa fa-ban"></i> Anular</button>
  </form>
@endsection