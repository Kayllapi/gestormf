@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
        <span>Anular Transferencia de Saldo</span>
        <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/transferenciasaldo') }}">
            <i class="fa fa-angle-left"></i> Ir Atras</a>
        </a>
    </div>
</div>
<form class="js-validation-signin px-30" action="javascript:;" 
      onsubmit="callback({
                              route:  'backoffice/tienda/sistema/{{ $tienda->id }}/transferenciasaldo/{{ $transferenciasaldo->id }}',
                              method: 'DELETE',
                              data:   { view: 'anular' }
                          },
                          function(resultado){
                              location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/transferenciasaldo') }}';
                          },this)"> 
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
        <div class="profile-edit-container">
            <div class="custom-form">
                <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%;">Anular</button>
            </div>
        </div> 
    </div>
</form>
@endsection
@section('subscripts')
@endsection