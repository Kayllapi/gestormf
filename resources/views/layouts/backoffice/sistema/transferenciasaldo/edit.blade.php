@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
        <span>Editar Transferencia de Saldo</span>
        <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/transferenciasaldo') }}">
            <i class="fa fa-angle-left"></i> Ir Atras</a>
        </a>
    </div>
</div>
<form class="js-validation-signin px-30" action="javascript:;" 
      onsubmit="callback({
                              route:  'backoffice/tienda/sistema/{{ $tienda->id }}/transferenciasaldo/{{ $transferenciasaldo->id }}',
                              method: 'PUT',
                              data:   { view: 'editar' }
                          },
                          function(resultado){
                              // location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/transferenciasaldo') }}';
                          },this)"> 
    <div id="carga-transferencia">
        <div class="profile-edit-container">
            <div class="custom-form">
                <div class="row">
                    <div class="col-sm-6">
                        <label for="idcajaorigen"> De *</label>
                        <select class="form-control" id="idcajaorigen">
                            <option></option>
                            @foreach($cajas as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                        <label for="idcajadestino">Para *</label>
                        <select class="form-control" id="idcajadestino">
                            <option></option>
                            @foreach($cajas as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label for="monto">Monto *</label>
                        <input type="number" value="{{ $transferenciasaldo->monto }}" min="0" step="0.01" id="monto">
                        <label for="motivo">Motivo *</label>
                        <input type="text" id="motivo" value="{{ $transferenciasaldo->motivo }}" onkeyup="texto_mayucula(this)">
                    </div>
                </div>
            </div>
        </div>
        <div class="profile-edit-container">
            <div class="custom-form">
                <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%;">Guardar Cambios</button>
            </div>
        </div> 
    </div>
</form>
@endsection
@section('subscripts')
<script>
    $('#idcajaorigen').select2({
        placeholder: '--Seleccionar--',
        minimumResultsForSearch: -1
    }).val({{ $transferenciasaldo->idcajaorigen }}).trigger('change'); 
    $('#idcajadestino').select2({
        placeholder: '--Seleccionar--',
        minimumResultsForSearch: -1
    }).val({{ $transferenciasaldo->idcajadestino }}).trigger('change'); 
</script>
@endsection