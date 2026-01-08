@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Método de pago</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/metodopago') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/metodopago',
        method: 'POST',
        data:{
            view: 'registrar'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/metodopago') }}';                                                                         
    },this)">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
            <div class="col-sm-12">
                <label>Método de Pago *</label>
                <select id="idtipometodopago">
                    <option></option>
                    @foreach($s_tipometodopagos as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6">
                <label>Llave Pública *</label>
                <input type="text" id="key_public"/>
            </div>
            <div class="col-sm-6">
                <label>Llave Privado *</label>
                <input type="text" id="key_private"/>
            </div>
          </div>
        </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios</button>
        </div>
    </div> 
</form>                             
@endsection
@section('subscripts')
<script>
$("#idtipometodopago").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
});
</script>
@endsection