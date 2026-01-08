@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Anuncio</span>
      <a class="btn btn-success" href="{{ url('backoffice/consumidor/adminanuncio') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div class="profile-edit-container">
    <div class="custom-form">
        <form action="javascript:;" 
              onsubmit="callback({
                  route: 'backoffice/consumidor/adminanuncio',
                  method: 'POST',
              },
              function(resultado){
                  location.href = '{{ url('backoffice/consumidor/adminanuncio') }}';
              },this)">
            <div class="row">
                <div class="col-md-6">
                    <label>Nombre *</label>
                    <input type="text" id="nombre"/>
                    <label>Precio Normal *</label>
                    <input type="number" id="precionormal" step="0.01" min="0"/>
                    <label>Precio con Descuento *</label>
                    <input type="number" id="preciodescuento" step="0.01" min="0"/>
                    <label>Stock *</label>
                    <input type="number" id="stock" step="0.01" min="0"/>
                </div>
                <div class="col-md-6">
                    <label>Imagen *</label>
                    <div class="fuzone" id="cont-imagen" style="height: 250px;">
                        <div class="fu-text"><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</div>
                        <input type="file" class="upload" id="imagen">
                        <div id="resultado-imagen"></div>
                    </div>
                </div>
            </div>
        <button type="submit" class="btn big-btn color-bg flat-btn" style="width:100%;">Guardar Cambios</button>  
        </form>
    </div>
</div>
@endsection
@section('scriptsbackoffice')
<script>
uploadfile({input:"#imagen",cont:"#cont-imagen",result:"#resultado-imagen"});
</script>
@endsection