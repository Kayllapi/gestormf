@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Eliminar Anuncio</span>
      <a class="btn btn-success" href="{{ url('backoffice/consumidor/adminanuncio') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div class="profile-edit-container">
    <div class="custom-form">
        <form action="javascript:;" 
              onsubmit="callback({
                  route: 'backoffice/consumidor/adminanuncio/{{ $anuncio->id }}',
                  method: 'DELETE',
              },
              function(resultado){
                  location.href = '{{ url('backoffice/consumidor/adminanuncio') }}';
              },this)">
            <div class="row">
                <div class="col-md-6">
                    <label>Nombre</label>
                    <input type="text" value="{{ $anuncio->nombre }}" disabled/>
                    <label>Precio Normal</label>
                    <input type="text" value="{{ $anuncio->precionormal }}" disabled/>
                    <label>Precio con Descuento</label>
                    <input type="text" value="{{ $anuncio->preciodescuento }}" disabled/>
                    <label>Stock</label>
                    <input type="text" value="{{ $anuncio->stock }}" disabled/>
                </div>
                <div class="col-md-6">
                    <label>Imagen</label>
                    <div class="fuzone" id="cont-imagen" style="height: 250px;">
                        <div id="resultado-imagen"></div>
                    </div>
                </div>
            </div>
        <button type="submit" class="btn big-btn color-bg flat-btn" style="width:100%;"><i class="fa fa-trash"></i> Eliminar</button>  
        </form>
    </div>
</div>
@endsection
@section('scriptsbackoffice')
<script>
uploadfile({
  input:"#imagen",
  cont:"#cont-imagen",
  result:"#resultado-imagen",
  ruta: "{{ url('public/backoffice/consumidor/anuncio/') }}",
  image: "{{ $anuncio->imagen }}"
});
</script>
@endsection