@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
    <div id="wrapper" style="padding-top: 0px;margin-top: 15px;">
        <div class="content" style="background-color: #1a191f;">
            <section class="section section--first section--bg" data-bg="https://i1.wp.com/digitalpolicylaw.com/wp-content/uploads/2020/06/dplnews_educacio%CC%81n-on-line_dn300620-scaled.jpg">
              <div class="container">
                <div class="row">
                  <div class="col-1 col-sm-3">
                  </div>
                  <div class="col-10 col-sm-6">
                  <form action="{{url('backoffice/cine')}}" method="GET" style="width: 100%;">
                    <input class="header__search-input" name="searchpeliculas" type="text" placeholder="Buscar Curso..." style="width: 100%;">
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
                            <span class="filter__item-label">CATEGORIA:</span>
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
                   @foreach($cursos as $value)
                  <div class="col-6 col-sm-4 col-md-3 col-xl-3">
                    <div class="card">
                      <div class="card__cover" style="width:100%">
                        <?php 
                        $rutaimagen = getcwd().'/public/backoffice/usuario/'.$value->idusers.'/aulavirtual/'.$value->imagen;
                        $urlimagen = url('public/backoffice/sistema/sin_imagen_cuadrado.png');
                        if(file_exists($rutaimagen)){
                            $urlimagen = url('public/backoffice/usuario/'.$value->idusers.'/aulavirtual/'.$value->imagen);
                        }
                        ?>
                        <div style="background-image: url({{ $urlimagen }});
                                    background-size: cover;
                                    background-attachment: scroll;
                                    background-position: center center;
                                    background-repeat: repeat;
                                    background-origin: content-box;
                                    width: 100%;
                                    height: 200px;"></div>
                        <a href="{{url('backoffice/aulavirtual/'.$value->id.'/edit?view=detalle')}}" class="card__play">
                          <i class="icon ion-ios-play"></i>
                        </a>
                      </div>
                      <div class="card__content">
                        <h3 class="card__title"><a href="{{url('backoffice/aulavirtual/'.$value->id.'/edit?view=detalle')}} ">{{ $value->nombre }}</a></h3>
                        <span class="card__category">
                          <a href="javascript:;">Categoria: {{ $value->categorianombre }}</a>
                        </span>
                      </div>
                    </div>
                  </div>
                     @endforeach  
                </div>
                </div>
                {{ $cursos->links('app.tablepaginationcine', ['results' => $cursos]) }}
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
<script>
function seleccionar_categoria(categoria){
    if(categoria!=''){
        categoria = '?categoria='+categoria;
    }
    location.href = '{{ url('backoffice/cine') }}'+categoria;
}
</script>
@endsection



