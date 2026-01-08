@extends('layouts.buscador.master')
@section('cuerpotienda')
<div class="row">
    <div class="col-md-7">
        <div class="list-single-main-wrapper">
            <div class="list-single-main-item fl-wrap">
                <div class="list-single-main-item-title fl-wrap">
                    <h3>Comentarios -  <span> {{ count($tiendacomentarios) }} </span></h3>
                </div>
              <div class="reviews-comments-wrap">
                 @foreach($tiendacomentarios as $value)
                    <div class="reviews-comments-item">
                        <div class="review-comments-avatar">
                            @if($value->usersimagen!='')
                            <img class="thumb" src="{{ url('public/backoffice/usuario/'.$value->idusers.'/perfil/'.$value->usersimagen) }}"/>
                            @else
                            <img src="{{ url('public/backoffice/sistema/sin_imagen_redondo.png') }}">
                            @endif
                        </div>
                        <div class="reviews-comments-item-text">
                            <h4><a href="#">{{ $value->usersnombre }}</a></h4>
                            <div class="clearfix"></div>
                            <p>{{ $value->contenido }}</p>
                            <span class="reviews-comments-item-date"><i class="fa fa-calendar-check-o"></i>{{ date_format(date_create($value->fechaaprobacion), 'd-m-Y H:i:s A') }}</span>
                        </div>
                    </div>
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
