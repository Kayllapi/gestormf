@extends('layouts.buscador.ecommerce')
@section('cuerpoecommerce')
    <!-- Page Contain -->
    <div class="page-contain">

        <!-- Main content -->
        <div id="main-content" class="main-content">

            <!--Block 01: Main Slide-->
            <div class="main-slide block-slider nav-change">
                <ul class="biolife-carousel" data-slick='{"arrows": true, "dots": false, "slidesMargin": 0, "slidesToShow": 1, "infinite": false, "speed": 800}' >
                    @foreach($s_ecommerceportada as $value)
                    <?php                
                     $rutaimagen = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/ecommerceportada/'.$value->imagen; 
                     $ruta_imagenproducto = url('public/backoffice/sistema/sin_imagen_cuadrado.png');
                     if(file_exists($rutaimagen) AND $value->imagen!=''){
                         $ruta_imagenproducto = url('/public/backoffice/tienda/'.$tienda->id.'/ecommerceportada/'.$value->imagen);
                     }
                    ?>
                    <li>
                        <div class="slide-contain slider-opt03__layout01">
                            <div class="media background-geen-01" style="background-image: url({{ $ruta_imagenproducto }});"></div>
                            <div class="text-content">
                                <i class="first-line">{{$value->titulo}}</i>
                                <h3 class="second-line">{{$value->nombre}}</h3>
                                <p class="third-line">{{$value->descripcion}}</p>
                                <p class="buttons">
                                    <a href="#" class="btn btn-bold">Comprar ahora</a>
                                    <a href="#" class="btn btn-thin">Ver Detalle</a>
                                </p>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>

      
            <!--Block 06: Products-->
            <div class="Product-box sm-margin-top-96px">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4 col-md-5 col-sm-6">
                            <div class="advance-product-box">
                                <div class="biolife-title-box bold-style biolife-title-box__bold-style">
                                    <h3 class="title">Ofertas del dia</h3>
                                </div>
                                <ul class="products biolife-carousel nav-top-right nav-none-on-mobile" data-slick='{"arrows":true, "dots":false, "infinite":false, "speed":400, "slidesMargin":30, "slidesToShow":1}'>
                                    <li class="product-item">
                                        <div class="contain-product deal-layout contain-product__deal-layout">
                                            <div class="product-thumb">
                                                <a href="#" class="link-to-product">
                                                    <img src="{{ url('public/layouts_ecommerce/assets/images/home-03/product_deal_330x330.jpg') }}" alt="dd" width="330" height="330" class="product-thumnail">
                                                </a>
                                                <div class="labels">
                                                    <span class="sale-label">-50%</span>
                                                </div>
                                            </div>
                                            <div class="info">
                                                <div class="biolife-countdown" data-datetime="2020-01-18 00:00 +00:00"></div>
                                                <b class="categories">Fresh Fruit</b>
                                                <h4 class="product-title"><a href="#" class="pr-name">National Fresh Fruit</a></h4>
                                                <div class="price ">
                                                    <ins><span class="price-amount"><span class="currencySymbol">£</span>85.00</span></ins>
                                                    <del><span class="price-amount"><span class="currencySymbol">£</span>95.00</span></del>
                                                </div>
                                                <div class="slide-down-box">
                                                    <p class="message">All products are carefully selected to ensure food safety.</p>
                                                    <div class="buttons">
                                                        <a href="#" class="btn wishlist-btn"><i class="fa fa-heart" aria-hidden="true"></i></a>
                                                        <a href="#" class="btn add-to-cart-btn">add to cart</a>
                                                        <a href="#" class="btn compare-btn"><i class="fa fa-random" aria-hidden="true"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="product-item">
                                        <div class="contain-product deal-layout contain-product__deal-layout">
                                            <div class="product-thumb">
                                                <a href="#" class="link-to-product">
                                                    <img src="{{ url('public/layouts_ecommerce/assets/images/home-03/product_deal-02_330x330.jpg') }}" alt="dd" width="330" height="330" class="product-thumnail">
                                                </a>
                                                <div class="labels">
                                                    <span class="sale-label">-50%</span>
                                                </div>
                                            </div>
                                            <div class="info">
                                                <div class="biolife-countdown" data-datetime="2020-01-03 00:00 +00:00"></div>
                                                <b class="categories">Fresh Fruit</b>
                                                <h4 class="product-title"><a href="#" class="pr-name">National Fresh Fruit</a></h4>
                                                <div class="price ">
                                                    <ins><span class="price-amount"><span class="currencySymbol">£</span>85.00</span></ins>
                                                    <del><span class="price-amount"><span class="currencySymbol">£</span>95.00</span></del>
                                                </div>
                                                <div class="slide-down-box">
                                                    <p class="message">All products are carefully selected to ensure food safety.</p>
                                                    <div class="buttons">
                                                        <a href="#" class="btn wishlist-btn"><i class="fa fa-heart" aria-hidden="true"></i></a>
                                                        <a href="#" class="btn add-to-cart-btn">add to cart</a>
                                                        <a href="#" class="btn compare-btn"><i class="fa fa-random" aria-hidden="true"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="product-item">
                                        <div class="contain-product deal-layout contain-product__deal-layout">
                                            <div class="product-thumb">
                                                <a href="#" class="link-to-product">
                                                    <img src="{{ url('public/layouts_ecommerce/assets/images/home-03/product_deal-03_330x330.jpg') }}" alt="dd" width="330" height="330" class="product-thumnail">
                                                </a>
                                                <div class="labels">
                                                    <span class="sale-label">-50%</span>
                                                </div>
                                            </div>
                                            <div class="info">
                                                <div class="biolife-countdown" data-datetime="2020-02-05 00:00 +00:00"></div>
                                                <b class="categories">Fresh Fruit</b>
                                                <h4 class="product-title"><a href="#" class="pr-name">National Fresh Fruit</a></h4>
                                                <div class="price ">
                                                    <ins><span class="price-amount"><span class="currencySymbol">£</span>85.00</span></ins>
                                                    <del><span class="price-amount"><span class="currencySymbol">£</span>95.00</span></del>
                                                </div>
                                                <div class="slide-down-box">
                                                    <p class="message">All products are carefully selected to ensure food safety.</p>
                                                    <div class="buttons">
                                                        <a href="#" class="btn wishlist-btn"><i class="fa fa-heart" aria-hidden="true"></i></a>
                                                        <a href="#" class="btn add-to-cart-btn">add to cart</a>
                                                        <a href="#" class="btn compare-btn"><i class="fa fa-random" aria-hidden="true"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-7 col-sm-6">
                            <div class="advance-product-box">
                                <div class="biolife-title-box bold-style biolife-title-box__bold-style">
                                    <h3 class="title">Productos mejor calificados</h3>
                                </div>
                                <ul class="products biolife-carousel nav-center-03 nav-none-on-mobile row-space-29px" data-slick='{"rows":2,"arrows":true,"dots":false,"infinite":false,"speed":400,"slidesMargin":30,"slidesToShow":2,"responsive":[{"breakpoint":1200,"settings":{ "rows":2, "slidesToShow": 2}},{"breakpoint":992, "settings":{ "rows":2, "slidesToShow": 1}},{"breakpoint":768, "settings":{ "rows":2, "slidesToShow": 2}},{"breakpoint":500, "settings":{ "rows":2, "slidesToShow": 1}}]}'>
                                    
                                    @foreach($productosvalorados as $valueproducto)
                                    <li class="product-item">
                                        <div class="contain-product right-info-layout contain-product__right-info-layout">
                                            <div class="product-thumb">
                                                <a href="#" class="link-to-product">
                                                    <?php 
                                                    $s_productogaleria = DB::table('s_productogaleria')
                                                        ->where('s_idproducto',$valueproducto->id)
                                                        ->orderBy('orden','asc')
                                                        ->limit(1)
                                                        ->first();
                                                    $ruta_imagenproducto = url('public/backoffice/sistema/sin_imagen_cuadrado.png');
                                                    if($s_productogaleria!=''){
                                                        $rutaimagen = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/producto/'.$s_productogaleria->imagen; 
                                                        if(file_exists($rutaimagen) AND $s_productogaleria->imagen!=''){
                                                            $ruta_imagenproducto = url('/public/backoffice/tienda/'.$tienda->id.'/producto/'.$s_productogaleria->imagen);
                                                        }
                                                    }
                                                   ?>
                                                    <img src="{{ $ruta_imagenproducto }}" alt="{{$valueproducto->nombre}}" width="270" height="270" class="product-thumnail">
                                                </a>
                                            </div>
                                            <div class="info">
                                                <b class="categories">{{$valueproducto->categorianombre}}</b>
                                                <h4 class="product-title"><a href="#" class="pr-name">{{$valueproducto->nombre}}</a></h4>
                                                <div class="price">
                                                    <ins><span class="price-amount"><span class="currencySymbol">S/. </span>{{ $valueproducto->preciopormayor }}</span></ins>
                                                    <del><span class="price-amount"><span class="currencySymbol">S/. </span>{{ $valueproducto->precioalpublico }}</span></del>
                                                </div>
                                                <div class="rating">
                                                    <p class="star-rating"><span class="width-80percent"></span></p>
                                                    <span class="review-count">(05 Vistas)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                    <li class="product-item">
                                        <div class="contain-product right-info-layout contain-product__right-info-layout">
                                            <div class="product-thumb">
                                                <a href="#" class="link-to-product">
                                                    <img src="{{ url('public/layouts_ecommerce/assets/images/products/p-03.jpg') }}" alt="dd" width="270" height="270" class="product-thumnail">
                                                </a>
                                            </div>
                                            <div class="info">
                                                <b class="categories">Vegetables</b>
                                                <h4 class="product-title"><a href="#" class="pr-name">Passover Cauliflower</a></h4>
                                                <div class="price ">
                                                    <ins><span class="price-amount"><span class="currencySymbol">£</span>85.00</span></ins>
                                                    <del><span class="price-amount"><span class="currencySymbol">£</span>95.00</span></del>
                                                </div>
                                                <div class="rating">
                                                    <p class="star-rating"><span class="width-80percent"></span></p>
                                                    <span class="review-count">(05 Reviews)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="product-item">
                                        <div class="contain-product right-info-layout contain-product__right-info-layout">
                                            <div class="product-thumb">
                                                <a href="#" class="link-to-product">
                                                    <img src="{{ url('public/layouts_ecommerce/assets/images/products/p-02.jpg') }}" alt="dd" width="270" height="270" class="product-thumnail">
                                                </a>
                                            </div>
                                            <div class="info">
                                                <b class="categories">Vegetables</b>
                                                <h4 class="product-title"><a href="#" class="pr-name">Hot Chili Peppers</a></h4>
                                                <div class="price ">
                                                    <ins><span class="price-amount"><span class="currencySymbol">£</span>85.00</span></ins>
                                                    <del><span class="price-amount"><span class="currencySymbol">£</span>95.00</span></del>
                                                </div>
                                                <div class="rating">
                                                    <p class="star-rating"><span class="width-80percent"></span></p>
                                                    <span class="review-count">(05 Reviews)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="product-item">
                                        <div class="contain-product right-info-layout contain-product__right-info-layout">
                                            <div class="product-thumb">
                                                <a href="#" class="link-to-product">
                                                    <img src="{{ url('public/layouts_ecommerce/assets/images/products/p-22.jpg') }}" alt="dd" width="270" height="270" class="product-thumnail">
                                                </a>
                                            </div>
                                            <div class="info">
                                                <b class="categories">Vegetables</b>
                                                <h4 class="product-title"><a href="#" class="pr-name">Cherry Tomato Seeds</a></h4>
                                                <div class="price ">
                                                    <ins><span class="price-amount"><span class="currencySymbol">£</span>85.00</span></ins>
                                                    <del><span class="price-amount"><span class="currencySymbol">£</span>95.00</span></del>
                                                </div>
                                                <div class="rating">
                                                    <p class="star-rating"><span class="width-80percent"></span></p>
                                                    <span class="review-count">(05 Reviews)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="product-item">
                                        <div class="contain-product right-info-layout contain-product__right-info-layout">
                                            <div class="product-thumb">
                                                <a href="#" class="link-to-product">
                                                    <img src="{{ url('public/layouts_ecommerce/assets/images/products/p-20.jpg') }}" alt="dd" width="270" height="270" class="product-thumnail">
                                                </a>
                                            </div>
                                            <div class="info">
                                                <b class="categories">Vegetables</b>
                                                <h4 class="product-title"><a href="#" class="pr-name">National Fresh Fruit</a></h4>
                                                <div class="price ">
                                                    <ins><span class="price-amount"><span class="currencySymbol">£</span>85.00</span></ins>
                                                    <del><span class="price-amount"><span class="currencySymbol">£</span>95.00</span></del>
                                                </div>
                                                <div class="rating">
                                                    <p class="star-rating"><span class="width-80percent"></span></p>
                                                    <span class="review-count">(05 Reviews)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="product-item">
                                        <div class="contain-product right-info-layout contain-product__right-info-layout">
                                            <div class="product-thumb">
                                                <a href="#" class="link-to-product">
                                                    <img src="{{ url('public/layouts_ecommerce/assets/images/products/p-05.jpg') }}" alt="dd" width="270" height="270" class="product-thumnail">
                                                </a>
                                            </div>
                                            <div class="info">
                                                <b class="categories">Vegetables</b>
                                                <h4 class="product-title"><a href="#" class="pr-name">Organic Hass Avocado</a></h4>
                                                <div class="price ">
                                                    <ins><span class="price-amount"><span class="currencySymbol">£</span>85.00</span></ins>
                                                    <del><span class="price-amount"><span class="currencySymbol">£</span>95.00</span></del>
                                                </div>
                                                <div class="rating">
                                                    <p class="star-rating"><span class="width-80percent"></span></p>
                                                    <span class="review-count">(05 Reviews)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="product-item">
                                        <div class="contain-product right-info-layout contain-product__right-info-layout">
                                            <div class="product-thumb">
                                                <a href="#" class="link-to-product">
                                                    <img src="{{ url('public/layouts_ecommerce/assets/images/products/p-06.jpg') }}" alt="dd" width="270" height="270" class="product-thumnail">
                                                </a>
                                            </div>
                                            <div class="info">
                                                <b class="categories">Vegetables</b>
                                                <h4 class="product-title"><a href="#" class="pr-name">Packham's Pears</a></h4>
                                                <div class="price ">
                                                    <ins><span class="price-amount"><span class="currencySymbol">£</span>85.00</span></ins>
                                                    <del><span class="price-amount"><span class="currencySymbol">£</span>95.00</span></del>
                                                </div>
                                                <div class="rating">
                                                    <p class="star-rating"><span class="width-80percent"></span></p>
                                                    <span class="review-count">(05 Reviews)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="product-item">
                                        <div class="contain-product right-info-layout contain-product__right-info-layout">
                                            <div class="product-thumb">
                                                <a href="#" class="link-to-product">
                                                    <img src="{{ url('public/layouts_ecommerce/assets/images/products/p-20.jpg') }}" alt="dd" width="270" height="270" class="product-thumnail">
                                                </a>
                                            </div>
                                            <div class="info">
                                                <b class="categories">Vegetables</b>
                                                <h4 class="product-title"><a href="#" class="pr-name">National Fresh Fruit</a></h4>
                                                <div class="price ">
                                                    <ins><span class="price-amount"><span class="currencySymbol">£</span>85.00</span></ins>
                                                    <del><span class="price-amount"><span class="currencySymbol">£</span>95.00</span></del>
                                                </div>
                                                <div class="rating">
                                                    <p class="star-rating"><span class="width-80percent"></span></p>
                                                    <span class="review-count">(05 Reviews)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <div class="biolife-banner style-01 biolife-banner__style-01 sm-margin-top-30px xs-margin-top-80px">
                                    <div class="banner-contain">
                                        <a href="#" class="bn-link"></a>
                                        <div class="text-content">
                                            <span class="first-line">Daily Fresh</span>
                                            <b class="second-line">Natural</b>
                                            <i class="third-line">Fresh Food</i>
                                            <span class="fourth-line">Premium Quality</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--Block 02: Banners-->
            <div class="special-slide">
                <div class="container">
                    <div class="biolife-service type01 biolife-service__type01 sm-margin-top-0 xs-margin-top-45px">
                        <b class="txt-show-01" >Delivery</b>
                        <i class="txt-show-02" >Delivery</i>
                        <ul class="services-list">
                            <li>
                                <div class="service-inner color-reverse">
                                    <span class="number">1</span>
                                    <span class="biolife-icon icon-beer"></span>
                                    <a class="srv-name" href="#">producto estampado completo</a>
                                </div>
                            </li>
                            <li>
                                <div class="service-inner color-reverse">
                                    <span class="number">2</span>
                                    <span class="biolife-icon icon-schedule"></span>
                                    <a class="srv-name" href="#">lugar y entrega a tiempo</a>
                                </div>
                            </li>
                            <li>
                                <div class="service-inner color-reverse">
                                    <span class="number">3</span>
                                    <span class="biolife-icon icon-car"></span>
                                    <a class="srv-name" href="#">Envío gratis en la ciudad</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!--Block 07: Brands-->
            <div class="brand-slide sm-margin-top-100px background-fafafa xs-margin-top-80px xs-margin-bottom-80px">
                <div class="container">
                    <ul class="biolife-carousel nav-center-bold nav-none-on-mobile" data-slick='{"rows":1,"arrows":true,"dots":false,"infinite":false,"speed":400,"slidesMargin":30,"slidesToShow":4, "responsive":[{"breakpoint":1200, "settings":{ "slidesToShow": 4}},{"breakpoint":992, "settings":{ "slidesToShow": 3}},{"breakpoint":768, "settings":{ "slidesToShow": 1}}]}'>
                        <li>
                            <div class="biolife-brd-container">
                                <a href="#" class="link">
                                    <figure><img src="{{ url('public/layouts_ecommerce/assets/images/home-03/brd-01.jpg') }}" width="214" height="163" alt=""></figure>
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="biolife-brd-container">
                                <a href="#" class="link">
                                    <figure><img src="{{ url('public/layouts_ecommerce/assets/images/home-03/brd-02.jpg') }}" width="214" height="163" alt=""></figure>
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="biolife-brd-container">
                                <a href="#" class="link">
                                    <figure><img src="{{ url('public/layouts_ecommerce/assets/images/home-03/brd-03.jpg') }}" width="153" height="163" alt=""></figure>
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="biolife-brd-container">
                                <a href="#" class="link">
                                    <figure><img src="{{ url('public/layouts_ecommerce/assets/images/home-03/brd-04.jpg') }}" width="224" height="163" alt=""></figure>
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="biolife-brd-container">
                                <a href="#" class="link">
                                    <figure><img src="{{ url('public/layouts_ecommerce/assets/images/home-03/brd-01.jpg') }}" width="214" height="163" alt=""></figure>
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="biolife-brd-container">
                                <a href="#" class="link">
                                    <figure><img src="{{ url('public/layouts_ecommerce/assets/images/home-03/brd-02.jpg') }}" width="214" height="163" alt=""></figure>
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="biolife-brd-container">
                                <a href="#" class="link">
                                    <figure><img src="{{ url('public/layouts_ecommerce/assets/images/home-03/brd-03.jpg') }}" width="153" height="163" alt=""></figure>
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="biolife-brd-container">
                                <a href="#" class="link">
                                    <figure><img src="{{ url('public/layouts_ecommerce/assets/images/home-03/brd-04.jpg') }}" width="224" height="163" alt=""></figure>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

       
        </div>
    </div>
@endsection