@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>TRANSFERIR TIENDA</span>
      <a class="btn btn-success" href="{{ url('backoffice/usuario') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/usuario/{{ $usuario->id }}',
        method: 'PUT'
    },
    function(resultado){
        if (resultado.resultado == 'CORRECTO') {
            location.href = '{{ url('backoffice/usuario/$usuario->id/edit?view=tienda') }}';                                  
        }                                                                                 
    },this)" enctype="multipart/form-data">
    <input type="hidden" value="transferir" id="view"/>
    <div class="profile-edit-container">
        <div class="custom-form">
            <div class="row">
              <div class="col-md-6">
                  <label>Nombre *</label>
                  <input type="text" id="nombre" value="{{ $usuario->nombre }}"/>
                  <input type="hidden" id="idtienda" value="{{ $tiendas->id }}"/>
              </div>
              <div class="col-md-6">
                    <label>Transferir a *<i class="fa fa-globe"></i> </label>
                    <select id="idTransferirTienda">
                        <option></option>
                        @foreach($usuarios as $value)
                          <option value="{{ $value->id }}"> {{ $value->nombre }} {{ $value->apellidos }} </option>
                        @endforeach
                    </select>
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
@section('scriptsbackoffice')
<script>
$("#idTransferirTienda").select2({
    placeholder: "---  Seleccionar ---",
    allowClear: true
}).val({{$usuario->id}}).trigger("change");
</script>
@endsection