@extends('layouts.backoffice.master')
@section('cuerpobackoffice')

    <div id="wrapper" style="padding-top: 0px;margin-top: 15px;">
        <div class="content" style="background-color: #1a191f;">
            <section class="section section--first section--bg" data-bg="https://www.supermasymas.com/blog/wp-content/uploads/2020/04/cine.jpg">
              <div class="container">
                <div class="row">
                  <div class="col-1 col-sm-3">
                  </div>
                  <div class="col-10 col-sm-6">
                  <form action="{{url('backoffice/cine')}}" method="GET" style="width: 100%;">
                    <input class="header__search-input" name="searchpeliculas" type="text" placeholder="Buscar Pelicula..." style="width: 100%;">
                    <button class="header__search-button" type="button">
                      <i class="icon ion-ios-search"></i>
                    </button>
                  </form>
                  </div>
                </div>
              </div>
            </section>
            <div style="float: left;width: 100%;">
              <div class="filter">
                <div class="container">
                  <div class="row">
                    <div class="col-12">
                      <div class="filter__content">
                        <div class="filter__items">
                          <div class="filter__item" id="filter__genre">
                            <span class="filter__item-label">GENERO:</span>
                            <div class="filter__item-btn dropdown-toggle" role="navigation" id="filter-genre" 
                                 data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              @if(isset($_GET['categoria']))
                              <input type="button" value="{{$_GET['categoria']}}">
                              @else
                              <input type="button" value="Mostrar Todo">
                              @endif
                              <span></span>
                            </div>
                            <ul class="filter__item-menu dropdown-menu scrollbar-dropdown" aria-labelledby="filter-genre">
                                  <li onclick="seleccionar_categoria('')">Mostrar Todo</li>
                               @foreach($categorias as $value)
                                  <li onclick="seleccionar_categoria('{{ $value->nombre }}')">{{ $value->nombre }}</li>
                               @endforeach  
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="catalog">
                <div class="container">
                <div class="row row--grid">
                   @foreach($cinepeliculas as $value)
                  <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                    <div class="card">
                      <div class="card__cover" style="width:100%">
                        <?php
                        $urlimagen = url('/public/backoffice/web/cinepelicula/'.$value->imagen);
                        if($value->imagen==''){
                            $urlimagen = url('public/backoffice/sistema/sin_imagen_cuadrado.png');
                        }
                        ?>
                        <div style="background-image: url({{ $urlimagen }});
                                    background-size: cover;
                                    background-attachment: scroll;
                                    background-position: center center;
                                    background-repeat: repeat;
                                    background-origin: content-box;
                                    width: 100%;
                                    height: 240px;"></div>
                        <a href="{{url('backoffice/cine/'.$value->id.'/edit?view=detalle')}}" class="card__play">
                          <i class="icon ion-ios-play"></i>
                        </a>
                        @if($value->idcine_tipo==1)
                        <span class="card__rate card__rate--green" style="display: initial;">
                          <div style="text-align: center;margin-top: 2px;">{{ $value->duracionvideo }}</div>
                          <div style="text-align: center;padding: 0px;margin: -10px;font-size: 11px;">min</div>
                        </span>
                        @endif
                      </div>
                      <div class="card__content">
                        <h3 class="card__title"><a href="{{url('backoffice/cine/'.$value->id.'/edit?view=detalle')}} ">{{ $value->nombre }}</a></h3>
                        <span class="card__category">
                         <?php
                          $cinecategorias = DB::table('cine_categoria')
                              ->join('cine_peliculacategoria','cine_peliculacategoria.idcine_categoria','cine_categoria.id')
                              ->where('cine_peliculacategoria.idcine_pelicula',$value->id)
                              ->select(
                                  'cine_categoria.*'
                              )
                              ->orderBy('cine_categoria.nombre','asc')
                              ->get();
                         ?>
                            @foreach($cinecategorias as $valuegenero)
                              <a href="javascript:;">{{ $valuegenero->nombre }}</a>
                             @endforeach  
                        </span>
                        <span class="card__category">
                          <a href="javascript:;">Año: {{ $value->fechapublicacion }}</a>
                        </span>
                      </div>
                    </div>
                  </div>
                     @endforeach  
                </div>
                </div>
                {{ $cinepeliculas->links('app.tablepaginationcine', ['results' => $cinepeliculas]) }}
              </div>
            </div>
        </div>
    </div>

<style>
  #wrapper {
    padding-top: 65px;
  }
  .page-content {
    background-color:#1a191f;
  } 
</style>
@endsection
@section('scriptsbackoffice')
  

  <!-- Imagen de Vaucher -->
  <script>
    uploadfile({
      input: "#imagen",
      cont: "#cont-fileupload",
      result: "#resultado-imagen",
    });
  </script>
@endsection



