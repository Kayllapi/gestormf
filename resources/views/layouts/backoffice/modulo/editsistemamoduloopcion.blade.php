@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>EDITAR SISTEMA-MÓDULO-OPCIÓN</span>
      <a class="btn btn-success" href="{{ url('backoffice/modulo') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/modulo/{{ $modulo->id }}',
        method: 'PUT',
        data: {
            view: 'editsistemamoduloopcion'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/modulo') }}';                                                                            
    },this)">
    <div class="profile-edit-container">
        <div class="custom-form">
            <div class="row">
                <div class="col-md-6">
                  <label>Módulo *</label>
                  <select id="idmodulo">
                    @foreach($modulos as $value)
                    <option value="{{ $value->id }}">{{ $value->orden }} - {{ $value->nombre }}</option>
                    <?php
                    $submodulos = DB::table('modulo')
                      ->where('idmodulo', $value->id)
                      ->orderBy('orden','asc')
                      ->get();
                    ?>
                    @foreach($submodulos as $subvalue)
                    <option value="{{ $subvalue->id }}">&nbsp;&nbsp;&nbsp;&nbsp;{{ $value->orden }}.{{ $subvalue->orden }} - {{ $subvalue->nombre }}</option>
                    <?php
                    $subsubmodulos = DB::table('modulo')
                      ->where('idmodulo', $subvalue->id)
                      ->orderBy('orden','asc')
                      ->get();
                    ?>
                    @foreach($subsubmodulos as $subsubvalue)
                    <option value="{{ $subsubvalue->id }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $value->orden }}.{{ $subvalue->orden }}.{{ $subsubvalue->orden }} - {{ $subsubvalue->nombre }}</option>
                    <?php
                    $subsubmodulosistemas = DB::table('modulo')
                      ->where('idmodulo', $subsubvalue->id)
                      ->orderBy('orden','asc')
                      ->get();
                    ?>
                    @foreach($subsubmodulosistemas as $subsubvaluesistema)
                    <option value="{{ $subsubvaluesistema->id }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $value->orden }}.{{ $subvalue->orden }}.{{ $subsubvalue->orden }}.{{ $subsubvaluesistema->orden }} - {{ $subsubvaluesistema->nombre }}</option>
                    @endforeach
                    @endforeach
                    @endforeach
                    @endforeach
                  </select>
                  <label>Nombre *</label>
                  <input type="text" value="{{ $modulo->nombre }}" id="nombre"/>
                  <label>Icono</label>
                  <input type="text" value="{{ $modulo->icono }}" id="icono"/>
                  <label>Imagen Icono (140x140)</label>
                  <div class="fuzone" id="cont-fileupload">
                      <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                      <input type="file" class="upload" id="imagen">
                      <div id="resultado-imagen"></div>
                  </div>
                </div>
                <div class="col-md-6">
                  <label>orden *</label>
                  <input type="text" id="orden" min="1" value="{{ $modulo->orden }}">
                  <label>Vista</label>
                  <input type="text" value="{{ $modulo->vista }}" id="vista"/>
                  <label>opcion *</label>
                  <input type="text" value="{{ $modulo->opcion }}" id="opcion"/>
                  <label>Estado *</label>
                  <select id="idestado">
                    <option value="1">Activado</option>
                    <option value="2">Desactivado</option>
                  </select>
                </div>
            </div>
            <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%;">Guardar Cambios</button>
        </div>
    </div> 
</form>                             
@endsection
@section('scriptssistema')
<script>
    $("#idmodulo").select2({
        placeholder: "---  Seleccionar ---",
        minimumResultsForSearch: -1
    }).val({{$modulo->idmodulo}}).trigger("change");
  
    $("#idestado").select2({
        placeholder: "---  Seleccionar ---",
        minimumResultsForSearch: -1
    }).val({{$modulo->idestado}}).trigger("change");

    @if($modulo->imagen!=null)
        uploadfile({
          input:"#imagen",
          cont:"#cont-fileupload",
          result:"#resultado-imagen",
          ruta: "{{ url('public/backoffice/sistema/modulo/')}}",
          image: "{{ $modulo->imagen }}"
        });
    @else
        uploadfile({
          input:"#imagen",
          cont:"#cont-fileupload",
          result:"#resultado-imagen"
        }); 
    @endif
</script>
@endsection