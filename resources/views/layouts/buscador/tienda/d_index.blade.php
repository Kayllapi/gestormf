@extends('layouts.buscador.master')
@section('cuerpotienda')
<div class="row">
    <div class="col-md-7">
        @if(count($s_ecommerceportada)>0)
        <section class="hero-section no-dadding"  style="border-radius: 5px;margin-bottom: 10px;">
            <div class="slider-container-wrap fl-wrap">
                <div class="slider-container">
                    @foreach($s_ecommerceportada as $value)
                    <?php                
                     $rutaimagen = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/ecommerceportada/'.$value->imagen; 
                     $ruta_imagenproducto = url('public/backoffice/sistema/sin_imagen_cuadrado.png');
                     if(file_exists($rutaimagen) AND $value->imagen!=''){
                         $ruta_imagenproducto = url('/public/backoffice/tienda/'.$tienda->id.'/ecommerceportada/'.$value->imagen);
                     }
                    ?>
                    <div class="slider-item fl-wrap" style="background-image: url(<?php echo $ruta_imagenproducto ?>);background-size: cover;
                      background-attachment: scroll;
                      background-position: center;
                      background-repeat: repeat;
                      background-origin: content-box;">
                        <div class="overlay" style="opacity: 0.05;"></div>
                        <div class="hero-section-wrap fl-wrap" >
                            <div class="container">
                                <div style="width: 100%;">
                                <div class="intro-item" style="padding: 10px;border-radius: 5px;">
                                    <h2 style="text-shadow: 0.1em 0.1em 0.2em black">{{$value->nombre}}</h2>
                                    <h3 style="text-shadow: 0.1em 0.1em 0.2em black">{{$value->descripcion}}</h3>
                                </div>
                                </div>
                                @if($value->s_idproducto!=0)
                                <div style="width: 100%;">
                                <div class="box-cat-container">
                                    <a href="javascript:;" id="modal-tiendaproducto" onclick="selectproducto({{$value->s_idproducto}})" class="box-cat color-bg">
                                        Ver detalle
                                    </a>
                                </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="swiper-button-prev sw-btn"><i class="fa fa-long-arrow-left"></i></div>
                <div class="swiper-button-next sw-btn"><i class="fa fa-long-arrow-right"></i></div>
            </div>
        </section>
        @endif
        <div class="list-single-main-wrapper fl-wrap">
            <div class="breadcrumbs gradient-bg fl-wrap">
              <span>Porductos Destacados</span>
            </div>
        </div>
        <div class="row home-posts">
            <?php $count = count($s_productos); ?>
            @if($count==0)
                <div class="col-md-12">
                    <div class="notification success fl-wrap" style="background: #cccf5e;">
                        <p style="font-size: 13px;text-align: center;">No se ha encontrado ningun producto y/o servicio.</p>
                    </div>
                </div>  
            @else
                <?php $i = 1; ?>
                @foreach($s_productos as $valueproducto)
                <div class="col-md-4">
                    <article class="card-post">
                        <div class="card-post-img fl-wrap">
                            <div class="gallery-items grid-small-pad  list-single-gallery three-coulms lightgallery">
                              <?php 
                               $s_productogaleria = DB::table('s_productogaleria')
                                  ->where('s_idproducto',$valueproducto->id)
                                  ->orderBy('orden','asc')
                                  ->limit(1)
                                  ->first();
                               ?>
                               @if($s_productogaleria!='')
                                <?php
                               $rutaimagen = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/producto/'.$s_productogaleria->imagen; 
                               ?>
                               @if(file_exists($rutaimagen) AND $s_productogaleria->imagen!='')
                                   
                              
                                  <div class="gallery-item">
                                      <div class="grid-item-holder">
                                          <div class="box-item" 
                                              style="background-image: url({{ url('/public/backoffice/tienda/'.$tienda->id.'/producto/'.$s_productogaleria->imagen) }});
                                                        background-repeat: no-repeat;
                                                        background-size: contain;
                                                        background-position: center;">
                                              <a href="javascript:;" id="modal-tiendaproducto" onclick="selectproducto({{$valueproducto->id}})" class="gal-link">
                                              <i class="fa fa-search"></i></a>
                                          </div>
                                      </div>
                                  </div>
                                  <?php
                                  $s_productogalerias = DB::table('s_productogaleria')
                                      ->where('s_idproducto',$valueproducto->id)
                                      ->where('id','<>',$s_productogaleria->id)
                                      ->orderBy('orden','asc')
                                      ->get();
                                  ?>
                                  @foreach($s_productogalerias as $value)
                                  <div class="gallery-items" style="display: none;">
                                      <div class="grid-item-holder">
                                          <div class="box-item">
                                            <a href="{{ url('public/backoffice/tienda/'.$tienda->id.'/producto/'.$value->imagen) }}" class="gal-link popup-image">
                                              <i class="fa fa-search"></i></a>
                                          </div>
                                      </div>
                                  </div>
                                  @endforeach
                               @else
                                  <div class="gallery-item">
                                      <div class="grid-item-holder">
                                          <div class="box-item" 
                                              style="background-image: url({{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }});
                                                        background-repeat: no-repeat;
                                                        background-size: 100% 100%;">
                                              <a href="javascript:;" id="modal-tiendaproducto" onclick="selectproducto({{$valueproducto->id}})" class="gal-link">
                                              <i class="fa fa-search"></i></a>
                                          </div>
                                      </div>
                                  </div>
                               @endif
                               @else
                                  <div class="gallery-item">
                                      <div class="grid-item-holder">
                                          <div class="box-item" 
                                              style="background-image: url({{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }});
                                                        background-repeat: no-repeat;
                                                        background-size: 100% 100%;">
                                              <a href="javascript:;" id="modal-tiendaproducto" onclick="selectproducto({{$valueproducto->id}})" class="gal-link">
                                              <i class="fa fa-search"></i></a>
                                          </div>
                                      </div>
                                  </div>
                               @endif
                            </div>
                        </div>
                        <div class="card-post-content fl-wrap">
                            <h3><a href="javascript:;" id="modal-tiendaproducto" onclick="selectproducto({{$valueproducto->id}})">{{ $valueproducto->nombre }}</a></h3>
                            <div class="post-opt">
                              <ul>
                                    @if($valueproducto->preciopormayor>0)
                                    <!--li><i class="fa fa-tags"></i> <span style="text-decoration: line-through;">s/. {{ $valueproducto->preciopormayor }}</span></li-->
                                    @endif
                                    <li><i class="fa fa-tags"></i> <a href="#">{{ $valueproducto->precioalpublico }}</a>  </li>
                                </ul>
                              <a href="javascript:;" id="modal-tiendaproducto" onclick="selectproducto({{$valueproducto->id}})" class="price-link">
                                <i class="fa fa-th-list"></i> Ver Detalle</a>
                            </div>
                        </div>
                    </article>
                </div>
                @if($i==3 and $count>3)
                <span class="fw-separator"></span>
                <?php $i = 0; ?>
                @endif
                <?php $i++; ?>
                @endforeach
            @endif
        </div>
    </div>
    <div class="col-md-5">
        <div class="list-single-main-item fl-wrap">
            <div id="add-review" class="add-review-box">
                <div class="leave-rating-wrap">
                    <span class="leave-rating-title">Su calificación para esta Tienda: </span>
                    @if(Auth::user())
                        <?php
                        $calificacion = DB::table('calificacion')
                            ->where('idtienda',$tienda->id)
                            ->where('idusers',Auth::user()->id)
                            ->first();
                        $numerocalificacion = 0;
                        if($calificacion!=''){
                            $numerocalificacion = $calificacion->numero;
                        }
                        ?>
                        <form class="form-calificar" action="javascript:;" 
                                onsubmit="callback({
                                    route: 'backoffice/tienda',
                                    method: 'POST',
                                    carga: '',
                                    data: {
                                        idtienda: '{{ $tienda->id }}',
                                        idusers: '{{ Auth::user()->id }}',
                                        view: 'calificar',
                                    }
                                },
                                function(resultado){
                                    $('#spancalificacion').html('Calificación - '+resultado.numero);      
                                },this)">
                        <div class="leave-rating">
                            <input type="radio" name="rating" id="rating-5" value="5" onclick="calificar()"/>
                            <label for="rating-5" class="fa fa-star-o"></label>
                            <input type="radio" name="rating" id="rating-4" value="4" onclick="calificar()"/>
                            <label for="rating-4" class="fa fa-star-o"></label>
                            <input type="radio" name="rating" id="rating-3" value="3" onclick="calificar()"/>
                            <label for="rating-3" class="fa fa-star-o"></label>
                            <input type="radio" name="rating" id="rating-2" value="2" onclick="calificar()"/>
                            <label for="rating-2" class="fa fa-star-o"></label>
                            <input type="radio" name="rating" id="rating-1" value="1" onclick="calificar()"/>
                            <label for="rating-1" class="fa fa-star-o"></label>
                        </div>
                        </form>
                    @else
                        <a class="leave-rating" id="modal-iniciarsesion-master">
                            <input type="radio" name="rating" id="rating-5" value="5" />
                            <label for="rating-5" class="fa fa-star-o"></label>
                            <input type="radio" name="rating" id="rating-4" value="4" />
                            <label for="rating-4" class="fa fa-star-o"></label>
                            <input type="radio" name="rating" id="rating-3" value="3" />
                            <label for="rating-3" class="fa fa-star-o"></label>
                            <input type="radio" name="rating" id="rating-2" value="2" />
                            <label for="rating-2" class="fa fa-star-o"></label>
                            <input type="radio" name="rating" id="rating-1" value="1" />
                            <label for="rating-1" class="fa fa-star-o"></label>
                        </a>
                    @endif
                </div>
            </div>
        </div> 
        <div class="list-single-main-item fl-wrap">
            <div class="list-single-main-item-title fl-wrap">
                <h3>Agregar comentario</h3>
            </div>
            @if(Auth::user())
            <div class="add-review-box">
                <form class="add-comment custom-form" action="javascript:;" 
                                onsubmit="callback({
                                    route: 'backoffice/tienda',
                                    method: 'POST',
                                    data: {
                                        idtienda: '{{ $tienda->id }}',
                                        idusers: '{{ Auth::user()->id }}',
                                        view: 'comentar',
                                    }
                                },
                                function(resultado){
                                    removecarga({input:'#mx-carga'});
                                    $('#tab-comentarios').click();
                                    location.reload();
                                },this)">
                    <fieldset>
                        <textarea id="contenido" style="height: 120px;" placeholder="Comentario:"></textarea>
                    </fieldset>
                    <button type="submit" class="btn  big-btn  color-bg flat-btn"><i class="fa fa-paper-plane-o"></i> Enviar Comentario</button>
                </form>
                </div>
                @else
                <div class="add-review-box" style="float: left;">
                    <a href="javascript:;" class="btn  big-btn color-bg flat-btn" id="modal-iniciarsesion-master"><i class="fa fa-sign-in"></i> Iniciar Sesión</a>
                </div>
                @endif
        </div> 
    </div>
</div>
<style>
.gallery-item {
    width: 100%;
}
</style>
@endsection
