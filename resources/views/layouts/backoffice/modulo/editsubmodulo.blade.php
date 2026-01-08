@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>EDITAR SUB-MÓDULO</span>
      <a class="btn btn-success" href="{{ url('backoffice/modulo') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/modulo/{{ $modulo->id }}',
        method: 'PUT',
        data: {
          view: 'editsubmodulo'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/modulo') }}';                                                                            
    },this)" enctype="multipart/form-data">
    <div class="profile-edit-container">
        <div class="custom-form">
            <div class="row">
                <div class="col-md-6">
                  <label>Módulo *</label>
                  <select id="idmodulo">
                    @foreach($modulos as $value)
                    <option value="{{ $value->id }}" <?php echo $modulo->idmodulo==$value->id ? 'selected' : '' ?>>{{ $value->orden }} - {{ $value->nombre }}</option>
                    <?php
                    $submodulos = DB::table('modulo')
                      ->where('idmodulo', $value->id)
                      ->orderBy('orden','asc')
                      ->get();
                    ?>
                    @foreach($submodulos as $subvalue)
                    <option value="{{ $subvalue->id }}" <?php echo $modulo->idmodulo==$subvalue->id ? 'selected' : '' ?>>&nbsp;&nbsp;&nbsp;&nbsp;{{ $value->orden }}.{{ $subvalue->orden }} - {{ $subvalue->nombre }}</option>
                    @endforeach
                    @endforeach
                  </select>
                  <label>Nombre *</label>
                  <input type="text" value="{{ $modulo->nombre }}" id="nombre"/>
                  <label>Icono</label>
                  <input type="text" value="{{ $modulo->icono }}" id="icono"/>
                  <label>orden *</label>
                  <div class="quantity fl-wrap">
                    <div class="quantity-item">
                        <input type="button" value="-" class="minus">
                        <input type="text" id="orden" class="qty" min="1" max="100000" step="1" value="{{ $modulo->orden }}" style="padding-left: 0px;">
                        <input type="button" value="+" class="plus">
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <label>Vista</label>
                  <input type="text" value="{{ $modulo->vista }}" id="vista"/>
                  <label>Controlador</label>
                  <input type="text" value="{{ $modulo->controlador }}" id="controlador"/>
                  <label>Estado *</label>
                  <select id="idestado">
                    <option value="1">Activado</option>
                    <option value="2">Desactivado</option>
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
$("#idmodulo").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{$modulo->idmodulo}}).trigger("change");
$("#idestado").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{$modulo->idestado}}).trigger("change");
</script>
@endsection