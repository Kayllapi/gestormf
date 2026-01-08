@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Editar Galeria</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div class="profile-edit-container">
  <div class="custom-form">
    <label>Imagenes (1500x900)</label>
      <form class="js-validation-signin px-30 form-tiendagaleria" 
                  action="javascript:;" 
                  onsubmit="callback({
                    route: 'backoffice/tienda/{{ $tienda->id }}',
                    method: 'PUT'
                },
                function(resultado){
                    location.href = '{{ url('backoffice/tienda/'.$tienda->id.'/edit?view=galeria') }}';                                                                          
                },this)" enctype="multipart/form-data">
          <input type="hidden" value="editgaleria" id="view"/>
           <div class="add-list-media-wrap">
             <div class="fuzone" style="margin-top: 0px;">
                 <div class="fu-text">
                     <span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span>
                 </div>
                 <input type="file" class="upload" id="imagen">
             </div>
           </div>
      </form>                     
    </div>
 </div>
        <div class="list-single-main-wrapper fl-wrap">
              <div class="gallery-items grid-small-pad  list-single-gallery three-coulms lightgallery">
                    @foreach($tiendagalerias as $value)
                    <div class="gallery-item">
                        <div class="grid-item-holder">
                            <div class="box-item" 
                                onclick="$('#imggaleria{{$value->id}}').click()"
                                style="background-image: url({{ url('/public/backoffice/tienda/'.$tienda->id.'/galeria/'.$value->imagen) }});
                                        background-repeat: no-repeat;
                                        background-size: cover;
                                        background-position: center;
                                        background-color: #31353d;">
                                <form class="js-validation-signin px-30 form-tiendagaleriaimagen{{ $value->id }}" 
                                  action="javascript:;" 
                                  onsubmit="callback({
                                    route: 'backoffice/tienda/{{ $value->id }}',
                                    method: 'DELETE'
                                },
                                function(resultado){
                                    location.href = '{{ url('backoffice/tienda/'.$tienda->id.'/edit?view=galeria') }}';                                                                          
                                },this)">
                                <input type="hidden" value="deletegaleria" id="view"/>  
                                </form>
                                
                                <a href="javascript:;" onclick="removeimagen({{ $value->id }})" id="eliminar-imagen">x</a>
                                <a href="{{ url('public/backoffice/tienda/'.$tienda->id.'/galeria/'.$value->imagen) }}" id="imggaleria{{$value->id}}" class="gal-link popup-image"><i class="fa fa-search"  ></i></a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>  
              </div>  
    
    
              
      <div class="custom-form">
           <form class="js-validation-signin px-30 form-tiendagaleriavideo" 
                                          action="javascript:;" 
                                          onsubmit="callback({
                                            route: 'backoffice/tienda/{{ $tienda->id }}',
                                            method: 'PUT',
                                            data: {
                                              detallevideo:seleccionartabla()   
                                            }
                                        },
                                        function(resultado){
                                            location.href = '{{ url('backoffice/tienda/'.$tienda->id.'/edit?view=galeria') }}';                                                                          
                                        },this)" enctype="multipart/form-data">
            <div>
            <label>Videos</label>
            <input type="hidden" value="editvideo" id="view">
            <table class="table" id="tabla-contenido">
                <thead class="thead-dark">
                  <tr>
                    <th scope="col" width="30px">#</th>
                    <th scope="col" width="100%" style="text-align: center">Link (Video)</th>
                    <th scope="col" colspan="2"><a href="javascript:;" class="btn  color-bg flat-btn" onclick="agregartitulo()"><i class="fa fa-angle-right"></i> Agregar</a></th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i = 1;?>
                  @foreach($tiendavideos as $tvalue)
                    <tr id="{{ $i }}">
                      <td scope="row" width="30px"><div class="item">{{ $i }}</div></td>
                      <td><input type="text" value="{{ $tvalue->link }}"></td>
                      <td width="10px"></td>
                      <td width="10px">
                        <a href="javascript:;" id="btneliminar{{ $i }}" class="btn btn-danger color-bg flat-btn" onclick="aliminartitulo({{ $i }})"><i class="fa fa-trash"></i></a>
                      </td>
                    </tr>
                  <?php $i++;?>
                  @endforeach
                </tbody>
            </table>
            <div class="profile-edit-container">
                <div class="custom-form">
                    <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Videos <i class="fa fa-angle-right"></i> </button>
                </div>
            </div>
              </div>
           </form>
      </div>    

@endsection
@section('scriptssistema')
<style>
#eliminar-imagen {
    left: 10px;
    top: 10px;
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
</style>
<script>
$('#imagen').change(function(evt) {
  $(".form-tiendagaleria").submit();
});
function removeimagen(idtiendagaleria){
  $(".form-tiendagaleriaimagen"+idtiendagaleria).submit();
}
  
  
function agregartitulo(){
    $('#tabla-contenido > tbody').append('<tr>'+
                                          '<td scope="row" width="30px">0</td>'+
                                          '<td><input type="text"></td>'+
                                          '<td width="10px"></td>'+
                                          '<td width="10px"></td>'+
                                        '</tr>');
    actualizaritem();
}
  
function actualizaritem(){
    var i = 1;
    $('#tabla-contenido > tbody > tr').each(function() {
        var submenu = $(this).attr('submenu');
        if(submenu==undefined){
            $(this).attr('id',i);
            $(this).find('td').each (function( column, td) {
                if(column==0){
                    $(td).html('<div class="item">'+i+'</div>');
                }else if(column==1){
                    //$(td).html('<input type="text" value="" id="nombre'+i+'">');
                }else if(column==2){
                    //$(td).html('<a href="javascript:;" class="btn color-bg flat-btn" onclick="agregarsubtitulo('+i+')"><i class="fa fa-plus"></i></a>');
                }else if(column==3){
                    $(td).html('<a href="javascript:;" id="btneliminar'+i+'" class="btn btn-danger color-bg flat-btn" onclick="aliminartitulo('+i+')"><i class="fa fa-trash"></i></a>');
                }
            }); 
            i++;
        }   
    });
}

function aliminartitulo(num){
    $('#tabla-contenido > tbody tr#'+num).remove();
    actualizaritem();
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