@extends('layouts.buscador.tienda.master')
@section('cuerpotienda')
<?php
$calificacion = DB::table('calificacion')
    ->where('idtienda',$tienda->id)
    ->select('idtienda',DB::raw('CONCAT(SUM(numero)/COUNT(*)) as total'),DB::raw('COUNT(*) as cantidad'))
    ->groupBy('idtienda')
    ->first();
$totalsumacalificacion = 5;
$cantidadcalificacion = 0;
if($calificacion!=''){
    $totalsumacalificacion = floor($calificacion->total);
    $cantidadcalificacion = $calificacion->cantidad;
}
?>
<div class="row">
    <div class="col-md-7">
        <div class="list-single-main-wrapper">
            <div class="breadcrumbs gradient-bg fl-wrap">
              <span>Información</span>
            </div>
            <div class="list-single-main-item fl-wrap">
            <div class="list-single-header list-single-header-inside">
                 <div class="container">
                     <div class="list-single-header-item">
                         <div class="row">
                             <div class="col-md-8">
                                 <h2>{{ $tienda->nombre }} <span> - </span><a href="{{ url($tienda->link) }}">{{ $tienda->categorianombre }}</a> </h2>
                                 <span class="section-separator"></span>
                                 <div class="listing-rating card-popup-rainingvis" data-starrating2="{{ $cantidadcalificacion }}">
                                     <span>({{ $cantidadcalificacion }} Calificaciones)</span>
                                 </div>
                               @if(Auth::user())
                                  <a href="javascript:;" class="list-post-counter single-list-post-counter">
                                    <span>{{ $recomendaciones }}</span><i class="fa fa-heart"></i>
                                  </a>  
                              @else
                                  <a href="javascript:;" class="list-post-counter single-list-post-counter modal-open"><span>{{ $recomendaciones }}</span><i class="fa fa-heart"></i></a>
                              @endif
                               
                             </div>
                             <div class="col-md-4">
                                 <div class="fl-wrap list-single-header-column">
                                    @if($tienda->link!='')
                                    @if (Auth::user()) 
                                      <a href="javascript:;" id="modal-reportar" class="custom-scroll-link"> <i class="fa fa-exclamation-triangle"></i> Reportar Tienda</a>
                                    @else
                                      <a href="javascript:;" id="modal-iniciarsesion-master" class="custom-scroll-link"> <i class="fa fa-exclamation-triangle"></i> Reportar Tienda</a>
                                    @endif
                                    @endif
                                     <div class="share-holder hid-share">
                                         <div class="showshare"><span>Compartir </span><i class="fa fa-share"></i></div>
                                         <div class="share-container  isShare"></div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
            </div>
            <div class="list-single-main-item fl-wrap">
                <div class="list-single-main-item-title fl-wrap">
                    <h3>Descripción</h3>
                </div>
                <p>{{ $tienda->contenido }}</p>
            </div>
            <div class="list-single-main-item fl-wrap">
                <div class="list-single-main-item-title fl-wrap">
                    <h3>Galeria de Fotos</h3>
                </div>
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
                                <a href="{{ url('public/backoffice/tienda/'.$tienda->id.'/galeria/'.$value->imagen) }}" id="imggaleria{{$value->id}}" class="gal-link popup-image"><i class="fa fa-search"  ></i></a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>                            
            </div>   
            <div class="list-single-main-item fl-wrap">
                <div class="list-single-main-item-title fl-wrap">
                    <h3>Galeria de Videos</h3>
                </div>
               @foreach($tiendavideos as $value)
                  <?php
                  $linkyou = str_replace('https://www.youtube.com/watch?v=','https://www.youtube.com/embed/',$value->link);
                  ?>
                  <div class="col-md-6">
                      <div class="iframe-holder fl-wrap">
                          <div class="resp-video" style="padding-top: 0px;margin-bottom: 10px;">
                            <iframe src="{{ $linkyou }}" width="640" height="360" frameborder="0" allowfullscreen></iframe>
                         </div>  
                      </div> 
                  </div>
                @endforeach                  
            </div>
          
            <div class="list-single-main-item fl-wrap">
                <div class="list-single-main-item-title fl-wrap">
                    <h3>Productos/Servicios</h3>
                </div>
                <div class="list-single-tags tags-stylwrap">
                        @foreach($s_categorias as $value)
                            <?php $counts_productos = DB::table('s_producto')
                                ->where('s_idcategoria1',$value->id)
                                ->count(); ?>
                            <a href="{{ url($tienda->link.'/'.str_replace(' ','-',mb_strtolower($value->nombre))) }}">{{ $value->nombre }}  <span>({{ $counts_productos }})</span></a>
                        @endforeach                                                                               
                </div>
            </div>
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
                        <div class="leave-rating">
                            <input type="radio" name="rating" id="rating-5" value="5" class="modal-open"/>
                            <label for="rating-5" class="fa fa-star-o"></label>
                            <input type="radio" name="rating" id="rating-4" value="4" class="modal-open"/>
                            <label for="rating-4" class="fa fa-star-o"></label>
                            <input type="radio" name="rating" id="rating-3" value="3" class="modal-open"/>
                            <label for="rating-3" class="fa fa-star-o"></label>
                            <input type="radio" name="rating" id="rating-2" value="2" class="modal-open"/>
                            <label for="rating-2" class="fa fa-star-o"></label>
                            <input type="radio" name="rating" id="rating-1" value="1" class="modal-open"/>
                            <label for="rating-1" class="fa fa-star-o"></label>
                        </div>
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
@endsection
