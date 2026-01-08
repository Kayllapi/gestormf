@extends('layouts.buscador.tienda.master')
@section('cuerpotienda')
<div class="row">
    <div class="col-md-9">
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
                              <?php 
                               $s_producto = DB::table('s_producto')
                                  ->whereId($value->s_idproducto)
                                  ->first();
                               ?>
                                @if($s_producto!='')
                                <div style="width: 100%;">
                                <div class="box-cat-container">
                                    <a href="{{ url($tienda->link.'/producto/'.mb_strtolower(str_replace('/','%2F',$s_producto->nombre))) }}" class="box-cat color-bg">
                                        Ver detalle
                                    </a>
                                </div>
                                </div>
                                @endif
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
                <div class="col-md-3">
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
                                              <a href="{{ url($tienda->link.'/producto/'.mb_strtolower(str_replace('/','-----',$valueproducto->nombre))) }}" class="gal-link">
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
                                              <a href="{{ url($tienda->link.'/producto/'.mb_strtolower(str_replace('/','-----',$valueproducto->nombre))) }}" class="gal-link">
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
                                              <a href="{{ url($tienda->link.'/producto/'.mb_strtolower(str_replace('/','-----',$valueproducto->nombre))) }}" class="gal-link">
                                              <i class="fa fa-search"></i></a>
                                          </div>
                                      </div>
                                  </div>
                               @endif
                            </div>
                        </div>
                        <div class="card-post-content fl-wrap">
                            <h3><a href="{{ url($tienda->link.'/producto/'.mb_strtolower(str_replace('/','-----',$valueproducto->nombre))) }}">{{ $valueproducto->nombre }}</a></h3>
                            <div class="post-opt">
                              <ul>
                                    @if($valueproducto->preciopormayor>0)
                                    <!--li><i class="fa fa-tags"></i> <span style="text-decoration: line-through;">s/. {{ $valueproducto->preciopormayor }}</span></li-->
                                    @endif
                                    <li><i class="fa fa-tags"></i> <a href="#">{{ $valueproducto->precioalpublico }}</a>  </li>
                                </ul>
                              <a href="{{ url($tienda->link.'/producto/'.mb_strtolower(str_replace('/','-----',$valueproducto->nombre))) }}" class="price-link">
                                <i class="fa fa-th-list"></i> Ver Detalle</a>
                            </div>
                        </div>
                    </article>
                </div>
                @if($i==4 and $count>4)
                <span class="fw-separator"></span>
                <?php $i = 0; ?>
                @endif
                <?php $i++; ?>
                @endforeach
                {{ $s_productos->links('app.tablepagination', ['results' => $s_productos]) }} 
            @endif
        </div>
    </div>
    <div class="col-md-3">
        <div class="list-single-main-item fl-wrap">
            <div class="list-single-main-item-title">
                <h3>Categoria</h3>
            </div>
            <div class="box-widget-item">
                <div class="list-author-widget-contacts list-item-widget-contacts">
                    <ul>
                        <?php 
                        $i = 1; 
                        $c = count($s_categorias);
                        ?>
                        @foreach($s_categorias as $value)
                        <?php 
                        $categorianombre = ucfirst(mb_strtolower($value->nombre));
                        $validcategorianombre = str_replace(' ','-',ltrim(mb_strtolower($value->nombre)));
                        ?>
                        <li <?php echo $c==$i?'style="border: 0px;"':''?>>
                          <label class="radio inline"> 
                              <input type="radio" name="gender{{$value->id}}" disabled>
                              <span><a href="javascript:;" onclick="search_tienda_categoria('{{str_replace(' ','-',mb_strtolower($value->nombre))}}')">{{ $categorianombre }}</a></span> 
                          </label>
                        </li>
                        <?php $i++; ?>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @if(count($marcas)>0)
        <div class="list-single-main-item fl-wrap">
            <div class="list-single-main-item-title">
                <h3>Marca</h3>
            </div>
            <select id="idmarca">
                <option></option>
                @foreach($marcas as $value)
                <option value="{{ str_replace(' ','-',mb_strtolower($value->nombre)) }}">{{ ucfirst(mb_strtolower($value->nombre)) }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="list-single-main-item fl-wrap">
            <div class="list-single-main-item-title">
                <h3>Precio</h3>
            </div>
            <div class="box-widget-item">
                <div class="list-author-widget-contacts list-item-widget-contacts">
                    <ul>
                        <li>
                          <label class="radio inline"> 
                              <input type="radio" name="genderprecio1" <?php echo isset($_GET['precio'])?($_GET['precio']=='menor-precio'?'checked':''):'' ?>>
                              <span><a href="javascript:;" onclick="search_tienda_precio('menor-precio')">Menor Precio</a></span> 
                          </label>
                        </li>
                        <li style="border: 0px;">
                          <label class="radio inline"> 
                              <input type="radio" name="genderprecio2" <?php echo isset($_GET['precio'])?($_GET['precio']=='mayor-precio'?'checked':''):'' ?>>
                              <span><a href="javascript:;" onclick="search_tienda_precio('mayor-precio')">Mayor Precio</a></span> 
                          </label>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
.gallery-item {
    width: 100%;
}
</style>
@endsection
@section('tiendascripts')
<script>
$("#idmarca").select2({
    placeholder: "-- Seleccionar Marca --",
    allowClear: true
}).on("change", function(e) {
    search_tienda_marca(e.currentTarget.value);
});
  
function search_tienda_categoria(value=''){
    location.href = '{{ url($tienda->link) }}/categoria/'+value+'';
}
function search_tienda_marca(value=''){
    location.href = '{{ url($tienda->link) }}/categoria/searchtienda?marca='+value;
}
function search_tienda_precio(value=''){
    location.href = '{{ url($tienda->link) }}/categoria/searchtienda?precio='+value;
}
</script>
@endsection


