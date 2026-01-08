@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>{{$cinepelicula->nombre}} / Eliminar Episodio</span>
      <a class="btn btn-success" href="{{ url('backoffice/cinepelicula/'.$cinepelicula->id.'/edit?view=episodio') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
          route: 'backoffice/cinepelicula/{{$cineepisodio->id}}',
          method: 'DELETE',
          data: {
              view:'episodioeliminar'
         }
      },
      function(resultado){
        if (resultado.resultado == 'CORRECTO') {
          location.href = '{{ url('backoffice/cinepelicula/'.$cinepelicula->id.'/edit?view=episodio') }}';                                                                            
        }
      },this)">
  <div class="profile-edit-container">
    <div class="custom-form">
      <div class="row">
        <div class="col-md-6">
          <label>Título</label>
          <input type="text" value="{{$cineepisodio->nombre}}" id="nombre" disabled>
          <label>Descripción</label>
          <textarea id="descripcion" disabled>{{$cineepisodio->descripcion}}</textarea>
        </div>
        <div class="col-md-6">
          <label>Url de Video</label>
          <input type="text" value="{{$cineepisodio->urlvideo}}" id="urlvideo" disabled>
          <label>Duración de video (Minutos)</label>
          <input type="number" value="{{$cineepisodio->duracionvideo}}" id="duracionvideo" disabled>
          <label>Orden</label>
          <input type="number" value="{{$cineepisodio->orden}}" id="orden" disabled>
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


</script>
@endsection