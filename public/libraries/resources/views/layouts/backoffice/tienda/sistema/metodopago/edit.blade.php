@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Editar Método de pago</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/metodopago') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/metodopago/{{ $s_metodopago->id }}',
        method: 'PUT',
        data:{
            view: 'editar'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/metodopago') }}';                                                                            
    },this)">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
            <div class="col-sm-6">
                <label>Método de Pago *</label>
                <select id="idtipometodopago">
                    <option></option>
                    @foreach($s_tipometodopagos as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
                <label>Estado por defecto *</label>
                <select id="idestado">
                    <option></option>
                    <option value="1">Activado</option>
                    <option value="2">Desactivado</option>
                </select>
            </div>
            <div class="col-sm-6">
                <label>Llave Pública *</label>
                <input type="text" value="{{ $s_metodopago->key_public }}" id="key_public"/>
                <label>Llave Privado *</label>
                <input type="text" value="{{ $s_metodopago->key_private }}" id="key_private"/>
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
}).val({{$s_metodopago->s_idtipometodopago}}).trigger("change");
$("#idestado").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{$s_metodopago->s_idestado}}).trigger("change");
</script>
@endsection