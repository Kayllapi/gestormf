@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Eliminar Pelicula/Serie</span>
      <a class="btn btn-success" href="{{ url('backoffice/cinepelicula') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
          route: 'backoffice/cinepelicula/{{ $cinepelicula->id }}',
          method: 'DELETE',
          data: {
              view:'eliminar'
         }
      },
      function(resultado){
        if (resultado.resultado == 'CORRECTO') {
          //location.href = '{{ url()->previous() }}';                                                         
        }
      },this)">
  <div class="profile-edit-container">
    <div class="custom-form">
      <div class="row">
        <div class="col-md-4">
        </div>
        <div class="col-md-4">
          <label>Tipo Pelicula/Serie *</label>
          <select id="idcinetipo" disabled>
              <option></option>
              @foreach($cinetipos as $value)
              <option value="{{ $value->id }}">{{ $value->nombre }}</option>
              @endforeach
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <label>Título</label>
          <input type="text" value="{{$cinepelicula->nombre}}" id="nombre" disabled>
          <label>Descripción</label>
          <textarea id="descripcion" disabled>{{$cinepelicula->descripcion}}</textarea>
          <label>Url de Video Trailer</label>
          <input type="text" value="{{$cinepelicula->urlvideotrailer}}" id="urlvideotrailer" disabled>
          <label>Fecha de Publicación</label>
          <input type="text" value="{{$cinepelicula->fechapublicacion}}" id="fechapublicacion" disabled>
          <label>Idioma</label>
          <select id="ididioma" disabled>
              <option></option>
              @foreach($idiomas as $value)
              <option value="{{ $value->id }}">{{ $value->nombre }}</option>
              @endforeach
          </select>
          <div id="cont-cinetipo-pelicula" style="display:none;">
          <label>Url de Video *</label>
          <input type="text" value="{{$cinepelicula->urlvideo}}" id="urlvideo" disabled>
          <label>Duración de video (Minutos) *</label>
          <input type="number" value="{{$cinepelicula->duracionvideo}}" id="duracionvideo" disabled>
          </div>
        </div>
        <div class="col-md-6">
          <label>Imagen</label>
          <div class="fuzone" id="cont-fileuploadimagen" style="height: 197px;">
              <div class="fu-text"><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</div>
              <div id="resultado-imagen"></div>
          </div>
          <div class="table-responsive">
            <table class="table" id="tabla-categoria">
                <thead class="thead-dark">
                  <tr>
                    <th>Categoria</th>
                  </tr>
                </thead>
                <tbody num="0"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro de Eliminar?
    </div>
    <div class="custom-form">
        <button type="submit" class="btn  big-btn  color-bg flat-btn btn-danger" style="width:100%;">Eliminar</button>
    </div>
  </div>
</form>
@endsection
@section('scriptsbackoffice')
<script>
$("#ididioma").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{$cinepelicula->ididioma}}).trigger("change");

$("#idcinetipo").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-cinetipo').css('display','block');
    $('#cont-cinetipo-pelicula').css('display','none');
    if(e.currentTarget.value == 1) {
        $('#cont-cinetipo-pelicula').css('display','block');
    }else if(e.currentTarget.value == 2) {
        $('#cont-cinetipo-pelicula').css('display','none');
    }
}).val({{$cinepelicula->idcine_tipo}}).trigger("change");
  
uploadfile({
  input: "#imagen",
  cont: "#cont-fileuploadimagen",
  result: "#resultado-imagen",
  ruta: "{{ url('public/backoffice/web/cinepelicula/') }}",
  image: "{{ $cinepelicula->imagen }}"
});
  
@foreach($cine_peliculacategorias as $value)
    agregarcategoria({{$value->idcine_categoria}})
@endforeach
  
function agregarcategoria(idcinecategoria){
      var num = $("#tabla-categoria tbody").attr('num');
      var nuevaFila='<tr id="'+num+'">';
          nuevaFila+='<td class="mx-td-input"><select id="idcinecategoria'+num+'" disabled>'+
              '<option></option>'+
              @foreach($cinecategorias as $value)
              '<option value="{{ $value->id }}">{{ $value->nombre }}</option>'+
              @endforeach
          '</select></td>';
          nuevaFila+='</tr>';
      $("#tabla-categoria").append(nuevaFila);
      $("#tabla-categoria tbody").attr('num',parseInt(num)+1);
          $("#idcinecategoria"+num).select2({
              placeholder: "---  Seleccionar ---",
              minimumResultsForSearch: -1
          }).val(idcinecategoria).trigger("change");  
}
</script>
@endsection