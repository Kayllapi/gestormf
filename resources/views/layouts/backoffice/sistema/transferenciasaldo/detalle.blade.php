@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
        <span>Detalle Transferencia de Saldo</span>
        <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/transferenciasaldo') }}">
            <i class="fa fa-angle-left"></i> Ir Atras</a>
        </a>
    </div>
</div>
<div id="carga-transferencia">
    <div class="profile-edit-container">
        <div class="custom-form">
            <div class="row">
                <div class="col-sm-6">
                    <label for="idcajaorigen"> De *</label>
                    <input type="text" id="idcajaorigen" value="{{ $transferenciasaldo->cajaorigen_nombre }}" disabled>
                    <label for="idcajadestino">Para *</label>
                    <input type="text" id="idcajadestino" value="{{ $transferenciasaldo->cajadestino_nombre }}" disabled>
                </div>
                <div class="col-sm-6">
                    <label for="monto">Monto *</label>
                    <input type="text" id="monto" value="{{ $transferenciasaldo->monto }}" disabled>
                    <label for="motivo">Motivo *</label>
                    <input type="text" id="motivo" value="{{ $transferenciasaldo->motivo }}" disabled>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('subscripts')
@endsection