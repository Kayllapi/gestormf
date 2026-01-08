<div class="list-single-main-wrapper fl-wrap">
                        <div class="breadcrumbs gradient-bg fl-wrap">
                          <span>Subir Imagenes de Domicilio</span>
                          <a class="btn btn-success" href="javascript:;" onclick="domicilio_index()"><i class="fa fa-angle-left"></i> Atras</a></a>
                        </div>
                    </div>
                        <div class="profile-edit-container">
                            <div class="custom-form">
                                <div class="row">
                                    @if($estado!='lectura')
                                    <div class="col-md-12">
                                        <label>Imagenes</label>
                                        <form class="js-validation-signin px-30 form-prestamodomicilio" action="javascript:;" 
                                              onsubmit="callback({
                                                                    route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud/{{ $prestamocredito->id }}',
                                                                    method: 'PUT',
                                                                    data:{
                                                                        view :  'imagendomicilio',
                                                                        idprestamo_creditodomicilio: {{$prestamodomicilio->id}}
                                                                    }
                                                                  },
                                                                  function(resultado){
                                                                      imagen_domicilio({{$prestamodomicilio->id}})
                                                                  },this)" enctype="multipart/form-data">
                                            <div class="fuzone" style="height: 205px;">
                                                <div class="fu-text">
                                                    <span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span>
                                                </div>
                                                <input type="file" class="upload" id="imagen-domicilio">
                                            </div>
                                        </form> 
                                    </div>
                                    @endif
                                    <div class="col-md-12">
                                        <div class="gallery-items grid-small-pad list-single-gallery three-coulms lightgallery" id="cont-imagenes-domicilio">
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
    $('#imagen-domicilio').change(function(evt) {
        $(".form-prestamodomicilio").submit();
    });
    function removeimagendomicilio(idprestamo_domicilioimagen) {
      $(".form-prestamodomicilioimagen"+idprestamo_domicilioimagen).submit();
    }
    imagen_domicilio({{$prestamodomicilio->id}});
    function imagen_domicilio(iddomicilio){
        removecarga({input:'#mx-carga'});
        load('#cont-imagenes-domicilio');
        $.ajax({
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-imagendomicilio')}}",
            type:'GET',
            data: {
                iddomicilio : iddomicilio,
                idprestamo_creditodomicilio: {{$prestamodomicilio->id}},
                idtienda : {{$tienda->id}},
                estado : '{{$estado}}'
            },
            success: function (respuesta){
                $('#cont-imagenes-domicilio').html(respuesta['imagenes']);
            }
        });
    }
</script>