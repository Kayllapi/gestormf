@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Pelicula/Serie</span>
      <a class="btn btn-success" href="{{ url('backoffice/cinepelicula') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
          route: 'backoffice/cinepelicula',
          method: 'POST',
          data: {
              view:'create',
              categorias:selectcategorias()
         }
      },
      function(resultado){
        if (resultado.resultado == 'CORRECTO') {
          location.href = '{{ url('backoffice/cinepelicula') }}';                                                                            
        }
      },this)">
  <div class="profile-edit-container">
    <div class="custom-form">
      <div class="row">
        <div class="col-md-4">
        </div>
        <div class="col-md-4">
          <label>Tipo Pelicula/Serie *</label>
          <select id="idcinetipo">
              <option></option>
              @foreach($cinetipos as $value)
              <option value="{{ $value->id }}">{{ $value->nombre }}</option>
              @endforeach
          </select>
        </div>
      </div>
      <div id="cont-cinetipo" style="display:none;">
      <div class="row">
        <div class="col-md-6">
          <label>Título *</label>
          <input type="text" id="nombre">
          <label>Descripción</label>
          <textarea id="descripcion"></textarea>
          <label>Url de Video Trailer (youtube) *</label>
          <input type="text" id="urlvideotrailer">
          <label>Fecha de Publicación *</label>
          <input type="text" id="fechapublicacion">
          <label>Idioma *</label>
          <select id="ididioma">
              <option></option>
              @foreach($idiomas as $value)
              <option value="{{ $value->id }}">{{ $value->nombre }}</option>
              @endforeach
          </select>
          <div id="cont-cinetipo-pelicula" style="display:none;">
          <label>Url de Video *</label>
          <input type="text" id="urlvideo">
          <label>Duración de video (Minutos) *</label>
          <input type="number" id="duracionvideo">
          </div>
        </div>
        <div class="col-md-6">
          <label>Imagen</label>
          <div class="fuzone" id="cont-fileuploadimagen" style="height: 178px;">
              <div class="fu-text"><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</div>
              <input type="file" class="upload" id="imagen">
              <div id="resultado-imagen"></div>
          </div>
          <div class="table-responsive">
            <table class="table" id="tabla-categoria">
                <thead class="thead-dark">
                  <tr>
                    <th>Categoria *</th>
                    <th width="10px" style="padding: 1px;"><a href="javascript:;" onclick="agregarcategoria()" class="btn btn-warning big-btn"><i class="fa fa-plus"></i></a></th>
                  </tr>
                </thead>
                <tbody num="0"></tbody>
            </table>
          </div>
        </div>
      </div>
      </div>
    </div>
    <div class="custom-form">
        <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%;">Guardar Cambios</button>
    </div>
  </div>
</form>
@endsection
@section('scriptsbackoffice')
<script>
$("#ididioma").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
});
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
});

uploadfile({input:"#imagen",cont:"#cont-fileuploadimagen",result:"#resultado-imagen"});
agregarcategoria();  
function agregarcategoria(){
      var num = $("#tabla-categoria > tbody").attr('num');
      var cantidadtr = $("#tabla-categoria > tbody > tr").length;
      var btneliminar = '';
      if(cantidadtr>0){
          btneliminar = '<a id="del'+num+'" href="javascript:;" onclick="eliminarcategoria('+num+')" class="btn btn-danger big-btn"><i class="fa fa-close"></i></a>';
      }
      var nuevaFila='<tr id="'+num+'">';
          nuevaFila+='<td class="mx-td-input"><select id="idcinecategoria'+num+'">'+
              '<option></option>'+
              @foreach($cinecategorias as $value)
              '<option value="{{ $value->id }}">{{ $value->nombre }}</option>'+
              @endforeach
          '</select></td>';
          nuevaFila+='<td>'+btneliminar+'</td>'
          nuevaFila+='</tr>';
      $("#tabla-categoria > tbody").append(nuevaFila);
      $("#tabla-categoria > tbody").attr('num',parseInt(num)+1);

      $("#idcinecategoria"+num).select2({
          placeholder: "---  Seleccionar ---"
      });
}
function eliminarcategoria(num){
    $("#tabla-categoria tbody tr#"+num).remove();
}
function selectcategorias(){
    var data = '';
    $("#tabla-categoria tbody tr").each(function() {
        var num = $(this).attr('id');        
        var idcinecategoria = $("#idcinecategoria"+num).val();
        data = data+'&'+idcinecategoria;
    });
    return data;
}
</script>
@endsection