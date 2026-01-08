@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Eliminar Usuario</span>
      <a class="btn btn-success" href="{{ redirect()->getUrlGenerator()->previous() }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/usuario/{{ $usuario->id }}',
        method: 'DELETE',
        data:{
            view: 'eliminar'
        }
    },
    function(resultado){
        location.href = '{{ redirect()->getUrlGenerator()->previous() }}';                                                                            
    },this)">
          <div class="row">
             <div class="col-md-6">
                <label>Tipo de Persona</label>
                <select id="idtipopersona" disabled>
                    @foreach($tipopersonas as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
                <div id="cont-juridica" style="display:none;">
                    <label>RUC</label>
                    <input type="text" value="{{ $usuario->identificacion }}" disabled/>
                    <label>Nombre Comercial</label>
                    <input type="text" value="{{ $usuario->nombre }}" disabled/>
                    <label>Razòn Social</label>
                    <input type="text" value="{{ $usuario->apellidos }}" disabled/>
                </div>
                <div id="cont-natural" style="display:none;">
                    <label>DNI</label>
                    <input type="text" value="{{ $usuario->identificacion }}" disabled/>
                    <label>Nombre</label>
                    <input type="text" value="{{ $usuario->nombre }}" disabled/>
                    <label>Apellidos</label>
                    <input type="text" value="{{ $usuario->apellidos }}" disabled/>
                </div>
                <div id="cont-carnetextranjeria" style="display:none;">
                    <label>Carnet Extranjería</label>
                    <input type="text" value="{{ $usuario->identificacion }}" disabled>
                    <label>Nombre</label>
                    <input type="text" value="{{ $usuario->nombre }}" disabled>
                    <label>Apellidos</label>
                    <input type="text" value="{{ $usuario->apellidos }}" disabled>
                </div>
               
                <label>Número de Teléfono</label>
                <input type="text" value="{{ $usuario->numerotelefono }}" disabled/>
             </div>
             <div class="col-md-6">
                <label>Imagen de Perfil</label>
                  <div class="fuzone" id="cont-fileupload">
                      <div class="fu-text"><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</div>
                      <div id="resultado-logo"></div>
                  </div>
                <label>Correo Electrónico</label>
                <input type="text" value="{{ $usuario->email }}" disabled/>
                <label>Ubicación (Ubigeo)</label>
                <input type="text" value="{{ $usuario->ubigeonombre }}" disabled/>
                <label>Dirección</label>
                <input type="text" value="{{ $usuario->direccion }}" disabled/>
             </div>
           </div>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro de Eliminar el usuario?
    </div>
    <button type="submit" class="btn mx-btn-post">Eliminar</button>
</form>                             
@endsection
@section('subscripts')
<script>
$("#idtipopersona").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-juridica').css('display','none');
    $('#cont-natural').css('display','none');
    if(e.currentTarget.value == 1) {
        $('#cont-natural').css('display','block');
    }else if(e.currentTarget.value == 2) {
        $('#cont-juridica').css('display','block');
    }else if(e.currentTarget.value == 3) {
        $('#cont-carnetextranjeria').css('display','block');
    }
}).val({{$usuario->idtipopersona}}).trigger("change");
uploadfile({
  input: "#imagen",
  cont: "#cont-fileupload",
  result: "#resultado-logo",
  ruta: "{{ url('/public/backoffice/tienda/'.$tienda->id.'/sistema/') }}",
  image: "{{ $usuario->imagen }}"
});
</script>
@endsection