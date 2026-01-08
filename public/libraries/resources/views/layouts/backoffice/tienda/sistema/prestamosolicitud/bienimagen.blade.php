<div id="carga-imagenbien">
                    <div class="list-single-main-wrapper fl-wrap">
                        <div class="breadcrumbs gradient-bg fl-wrap">
                          <span>Subir Imagenes de Garantia</span>
                          <a class="btn btn-success" href="javascript:;" onclick="bien_index()"><i class="fa fa-angle-left"></i> Atras</a></a>
                        </div>
                    </div>
                        <div class="profile-edit-container">
                            <div class="custom-form">
                                <div class="row">
                                    <div class="col-md-12">
                                        <form class="js-validation-signin px-30 form-prestamobien" action="javascript:;" 
                                              onsubmit="callback({
                                                                    route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud/{{ $prestamocredito->id }}',
                                                                    method: 'PUT',
                                                                    carga: '#carga-imagenbien',
                                                                    data:{
                                                                        view :  'imagenbien',
                                                                        idprestamo_creditobien: {{$prestamobien->id}}
                                                                    }
                                                                  },
                                                                  function(resultado){
                                                                      imagen_bien({{$prestamobien->id}});
                                                                  },this)" enctype="multipart/form-data">
                                            <input type="hidden" id="bien_imagen_idprestamo_bien" value="0">
                                            <div class="fuzone" style="height: 205px;">
                                                <div class="fu-text">
                                                    <span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span>
                                                </div>
                                                <input type="file" class="upload" id="imagen-bien" multiple>
                                            </div>
                                        </form> 
                                    </div>
                                    <div class="col-md-12">
                                        <div class="gallery-items grid-small-pad list-single-gallery three-coulms lightgallery" id="cont-imagenes-bien">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
</div>
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

</style>
<script>
    $('#imagen-bien').change(function(evt) {
        $(".form-prestamobien").submit();
    });
    function removeimagenbien(idprestamo_bienimagen) {
      $(".form-prestamobienimagen"+idprestamo_bienimagen).submit();
    }
  
    imagen_bien({{$prestamobien->id}});
    function imagen_bien(idbien){
        removecarga({input:'#carga-imagenbien'});
        load('#cont-imagenes-bien');
        $.ajax({
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-imagenbien')}}",
            type:'GET',
            data: {
                idbien : idbien,
                idprestamo_creditobien: {{$prestamobien->id}},
                idtienda : {{$tienda->id}},
            },
            success: function (respuesta){
                $('#cont-imagenes-bien').html(respuesta['imagenes']);
            }
        });
    }
</script>