@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>DETALLE DE CURSO</span>
      <a class="btn btn-success" href="{{ url('backoffice/aulavirtual') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>


<div class="row">
  <div class="col-md-6">
    <div class="list-single-main-item fl-wrap" id="sec6">
        <div class="list-single-main-item-title fl-wrap">
            <h3>{{ $curso->nombre }}</h3>
        </div>
        <p>{{ $curso->descripcion }}</p>
                   
        <div class="row">
        <div class="col-md-3">               
            <div class="list-author-widget-header shapes-bg-small  color-bg fl-wrap" 
            style="
            <?php 
                   $rutaimagen = getcwd().'/public/backoffice/usuario/'.$curso->idusers.'/aulavirtual/'.$curso->imagen;
                   if(file_exists($rutaimagen)){
                      $imagen = url('/public/backoffice/usuario/'.$curso->idusers.'/aulavirtual/'.$curso->imagen);
                   }else{
                      $imagen = url('public/backoffice/sistema/sin_imagen_cuadrado.png');
                   }
                   ?>
            background-image: url({{ $imagen }});
            background-repeat: no-repeat;
            background-size: 100%;
            background-color: #fff;
            margin-bottom: 10px;
            height: 120px;
            padding: 0px;">
                <span class="list-author-widget-link"></span>
            </div>
        </div>
        <div class="col-md-9">               
        <div class="box-widget-content">
            <div class="list-author-widget-text">
                <div class="list-author-widget-contacts list-item-widget-contacts">
                    <ul>
                        <li><span><i class="fa fa-book"></i> Categoria :</span> <a href="#">{{ $cursocategoria->nombre }}</a></li>
                        <li><span><i class="fa fa-graduation-cap"></i>Curso :</span> <a href="#">{{ $curso->nombre }}</a></li>
                        <li style="border: 0px;"><span><i class="fa fa-university"></i> Idioma :</span> <a href="#">Español</a></li>
                    </ul>
                </div>
            </div>
        </div>
        </div>
        </div>
        <div class="accordion">
            <?php $ii = 0; ?>
            @foreach($modulos as $value)
            <?php
            $styletoggle = '';
            $styletogglevisible = 'style="display: none;"';
            if($ii==0){
                $styletoggle = 'act-accordion';
                $styletogglevisible = 'style="display: block;"';
            }
            ?>
            <a class="toggle <?php echo $styletoggle; ?>" href="#"> {{ $value->nombre }} <i class="fa fa-angle-down"></i></a>
            <div class="accordion-inner" <?php echo $styletogglevisible; ?>>
                <div class="opening-hours">
                    <div class="box-widget-content">
                        <ul>
                          <?php
                          $cursomodulotemas = DB::table('cursomodulotema')
                            ->where('idcursomodulo',$value->id)
                            ->orderBy('id','asc')
                            ->get();
                          $countm = count($cursomodulotemas);
                          $i = 1;
                          ?>
                          @foreach($cursomodulotemas as $valuemodulo)
                            <?php
                            $style = "";
                            if($countm==$i){
                                $style = 'style="border: 0px;margin-bottom:0px;"';
                            }
                            ?>
                            <!--li <?php echo $style ?>><a href="{{ $valuemodulo->urlvideo }}" class="image-popup"><span class="opening-hours-day"><i class="fa fa-play-circle"></i> {{ $valuemodulo->nombre }}</span><span class="opening-hours-time"></span></a></li-->
                            <li <?php echo $style ?>><a href="javascript:;" onclick="vervideo('{{ $valuemodulo->urlvideo }}')"><span class="opening-hours-day"><i class="fa fa-play-circle"></i> {{ $valuemodulo->nombre }}</span><span class="opening-hours-time"></span></a></li>
                            <?php $i++; ?>
                          @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <?php $ii++; ?>
            @endforeach
        </div>
    </div>
    
  </div>
  <div class="col-md-6">
    <div id="cont-videocurso">
      
        <!--video
          id="my-video"
          class="video-js"
          controls
          preload="auto"
          style="width:100%;"
          poster="{{$imagen}}"
          data-setup="{}"
          >
          <source src="http://kayllapi.club/contenido/PELICULAS/ACCION/ACCION%20-%20Misi%c3%b3n%20Rescate.mp4" type="video/mp4" />
          <p class="vjs-no-js">
            To view this video please enable JavaScript, and consider upgrading to a
            web browser that
            <a href="https://videojs.com/html5-video-support/" target="_blank"
              >supports HTML5 video</a
            >
          </p>
        </video-->
    </div>
  </div>
</div>
   
@endsection
@section('subscripts')
<style>
.act-accordion {
  background-color: #31353d !important;
}
</style>
<script>
function vervideo(url){
    var video = '<video '+
          'id="my-video" '+
          'class="video-js" '+
          'controls '+
          'preload="auto" '+
          'style="width:100%;height: 350px;" '+
          'poster="" '+
          'data-setup="{}" '+
        '>'+
         '<source src="'+url+'"/>'+
          '<p class="vjs-no-js">'+
            'To view this video please enable JavaScript, and consider upgrading to a'+
            'web browser that'+
            '<a href="https://videojs.com/html5-video-support/" target="_blank"'+
              '>supports HTML5 video</a'+
            '>'+
          '</p></video>';
    $('#cont-videocurso').html(video);
}
</script>
@endsection