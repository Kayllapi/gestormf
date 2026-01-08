@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Editar Imagenes de Producto</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/producto') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div class="profile-edit-container">
    <div class="custom-form">
        <div class="row">
            <div class="col-md-12">
                <label>Imagenes</label>
                <form class="js-validation-signin px-30 form-tiendagaleria" 
                    action="javascript:;" 
                    onsubmit="callback({
                        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/producto/{{ $producto->id }}',
                        method: 'PUT',
                        data:{
                            view : 'imagen'
                        }
                    },
                    function(resultado){
                        location.href = '{{ Request::url() }}?view=imagen'; 
                    },this)" enctype="multipart/form-data">
                    <div class="fuzone" style="height: 205px;">
                        <div class="fu-text">
                            <span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span>
                        </div>
                        <input type="file" class="upload" id="imagen">
                    </div>
                </form> 
            </div>
            <div class="col-md-12">
                <div class="gallery-items grid-small-pad  list-single-gallery three-coulms lightgallery">
                    <?php $i=1 ?>
                    @foreach($productogalerias as $value)
                    <div class="gallery-item">
                        <div class="grid-item-holder">
                            <div class="box-item" style="
                            background-image: url({{ url('public/backoffice/tienda/'.$tienda->id.'/producto/'.$value->imagen) }});
                            background-repeat: no-repeat;
                            background-size: contain;
                            background-position: center;" onclick="$('#imggaleria{{$value->id}}').click()">
                                <form class="js-validation-signin px-30 form-tiendagaleriaimagen{{ $value->id }}" 
                                    action="javascript:;" 
                                    onsubmit="callback({
                                        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/producto/{{ $value->id }}',
                                        method: 'DELETE',
                                        data:{
                                            view : 'eliminarimagen'
                                        }
                                    },
                                    function(resultado){
                                        location.href = '{{ Request::url() }}?view=imagen';
                                    },this)">
                                </form>
                                <a href="javascript:;" onclick="removeimagen({{ $value->id }})" id="eliminar-imagen">x</a>
                                <div class="orden-imagen">{{ $i }}</div>
                                <a href="{{ url('public/backoffice/tienda/'.$tienda->id.'/producto/'.$value->imagen) }}" id="imggaleria{{$value->id}}" class="gal-link popup-image">
                                    <i class="fa fa-search"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php $i++ ?>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div> 
                           
@endsection
@section('subscripts')
<style>
.orden-imagen{
    background-color: #000;
    width: 30px;
    height: 30px;
    float: right;
    margin-right: 10px;
    margin-top: 10px;
    color: #fff;
    line-height: 2;
    font-weight: bold;
    text-align: center;
}
#eliminar-imagen {
    left: 10px;
    margin-top: 10px;
    font-size: 18px;
    background-color: #c12e2e;
    padding: 2px;
    padding-left: 9px;
    padding-right: 9px;
    border-radius: 15px;
    color: #fff;
    font-weight: bold;
    cursor: pointer;
    position: absolute;
    z-index: 10;
}
  
.custom-form input[type="text"] {
    margin-bottom: 0px;
    float: none;
    padding: 11px;
  }
.btn {
    display: block;
    white-space: nowrap;
    padding-left: 18px;
    padding-right: 18px;
    text-align: center;
}
.btn i {
    padding-left: 0px;
}
.btn-danger {
    background-color: #ab2934 !important;
}
.btn-danger:hover {
    background-color: #4e0d13 !important;
}
</style>
<script>
$('#imagen').change(function(evt) {
  $(".form-tiendagaleria").submit();
});
function removeimagen(idtiendagaleria){
  $(".form-tiendagaleriaimagen"+idtiendagaleria).submit();
} 
function seleccionartabla(){
    var data = '';
    $('#tabla-contenido > tbody > tr').each(function() {
        var item = '';
        var nombre = '';
       $(this).find('td').each (function( column, td) {
           if(column==0){
                item = $(td).find('div').html();
            }else if(column==1){
                nombre = $(td).find('input').val();
            }
        });
        data = data+'/-/'+item+'/,/'+nombre;
    });
    return data;
  
}
</script>
@endsection