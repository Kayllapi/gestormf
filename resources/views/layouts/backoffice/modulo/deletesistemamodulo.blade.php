@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>ELIMINAR SISTEMA-MÓDULO</span>
      <a class="btn btn-success" href="{{ url('backoffice/modulo') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
    <form class="js-validation-signin px-30" 
          action="javascript:;" 
          onsubmit="callback({
            route: 'backoffice/modulo/{{ $modulo->id }}',
            method: 'DELETE',
            data: {
                view: 'deletesubsubmodulo'
            }
            },
            function(resultado){
                if (resultado.resultado == 'CORRECTO') {
                    location.href = '{{ url('backoffice/modulo') }}';                                                                            
                }                                                                                                                    
            },this)">
      <div class="profile-edit-container">
        <div class="custom-form">
            <div class="row">
                <div class="col-md-6">
                  <label>Módulo *</label>
                  <select id="idmodulo" disabled>
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
                    <option value="{{ $subsubvalue->id }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $value->orden }}.{{ $subsubvalue->orden }} - {{ $subsubvalue->nombre }}</option>
                    @endforeach
                    @endforeach
                    @endforeach
                  </select>
                  <label>Nombre *</label>
                  <input type="text" value="{{ $modulo->nombre }}" id="nombre" disabled/>
                  <label>Icono</label>
                  <input type="text" value="{{ $modulo->icono }}" id="icono" disabled/>
                  <label>Imagen Icono (140x140)</label>
                  <div class="fuzone" id="cont-fileupload">
                      <div id="resultado-imagen"></div>
                  </div>
                </div>
                <div class="col-md-6">
                  <label>orden *</label>
                  <input type="text" id="orden" min="1" value="{{ $modulo->orden }}" disabled>
                  <label>Vista</label>
                  <input type="text" value="{{ $modulo->vista }}" id="vista" disabled/>
                  <label>Controlador</label>
                  <input type="text" value="{{ $modulo->controlador }}" id="controlador" disabled/>
                  <label>Estado *</label>
                  <select id="idestado" disabled>
                    <option value="1">Activado</option>
                    <option value="2">Desactivado</option>
                  </select>
                </div>
            </div>
            <div class="mensaje-warning">
              <i class="fa fa-warning"></i> ¿Esta Seguro de Eliminar?
            </div>
            <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%;">Eliminar</button>
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