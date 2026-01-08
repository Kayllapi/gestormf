@extends('layouts.buscador.ecommerce')
@section('cuerpoecommerce')
<!--Hero Section-->
    <div class="hero-section hero-background">
        <h1 class="page-title">{{ isset($_GET['categoria'])?($_GET['categoria']!=''?$_GET['categoria']:(isset($_GET['search']) ? ($_GET['search']!=''?$_GET['search']:'Todo los Productos') : 'Todo los Productos')):(isset($_GET['search']) ? ($_GET['search']!=''?$_GET['search']:'Todo los Productos') : 'Todo los Productos') }}</h1>
    </div>
<!--Navigation section-->
    <div class="container">
        <nav class="biolife-nav">
            
        </nav>
    </div>
<div class="page-contain category-page left-sidebar">
        <div class="container">
            <div class="row">
                <!-- Main content -->
                <div id="main-content" class="main-content col-lg-9 col-md-8 col-sm-12 col-xs-12">

                    <div class="product-category grid-style">

                        <div id="top-functions-area" class="top-functions-area" >
                            <div class="flt-item to-left group-on-mobile">
                                <span class="flt-title">Ordenar por</span>
                                <a href="#" class="icon-for-mobile">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </a>
                                <div class="wrap-selectors">
                                    <form action="#" name="frm-refine" method="get">
                                        <span class="title-for-mobile">Refine Products By</span>
                                        <div data-title="precio:" class="selector-item">
                                            <select name="price" class="selector">
                                                <option>Precio</option>
                                                <option value="1">Menor Precio</option>
                                                <option value="2">Mayor Precio</option>
                                            </select>
                                        </div>
                                        <div data-title="Marca:" class="selector-item">
                                            <select name="brad" class="selector">
                                                <option>Marca</option>
                                                @foreach($s_marca as $value)
                                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <p class="btn-for-mobile"><button type="submit" class="btn-submit">Go</button></p>
                                    </form>
                                </div>
                            </div>
                            <div class="flt-item to-right">
                                <div class="wrap-selectors">
                                    <div class="selector-item viewmode-selector">
                                        <a href="javascript:;" class="viewmode grid-mode active"><i class="biolife-icon icon-grid"></i></a>
                                        <a href="javascript:;" class="viewmode detail-mode"><i class="biolife-icon icon-list"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <ul class="products-list">
                                @foreach($s_productos as $valueproducto)
                                <li class="product-item col-lg-4 col-md-4 col-sm-4 col-xs-6">
                                    <div class="contain-product layout-default">
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
                                            <div class="shipping-info">
                                                <p class="shipping-day">Envio Inmediato</p>
                                                <p class="for-today">Hoy envio Gratis</p>
                                            </div>
                                            <div class="slide-down-box">
                                                <p class="message">Se garantiza la compra segura.</p>
                                                <div class="buttons">
                                                    <a href="#" class="btn wishlist-btn"><i class="fa fa-heart" aria-hidden="true"></i></a>
                                                    <a href="#" class="btn add-to-cart-btn"><i class="fa fa-cart-arrow-down" aria-hidden="true"></i>Agregar a Carrito</a>
                                                    <a href="#" class="btn compare-btn"><i class="fa fa-random" aria-hidden="true"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="biolife-panigations-block">
                            <ul class="panigation-contain">
                                <li><span class="current-page">1</span></li>
                                <li><a href="#" class="link-page">2</a></li>
                                <li><a href="#" class="link-page">3</a></li>
                                <li><span class="sep">....</span></li>
                                <li><a href="#" class="link-page">20</a></li>
                                <li><a href="#" class="link-page next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
                            </ul>
                        </div>

                    </div>

                </div>
                <!-- Sidebar -->
                <aside id="sidebar" class="sidebar col-lg-3 col-md-4 col-sm-12 col-xs-12">
                    <div class="biolife-mobile-panels">
                        <span class="biolife-current-panel-title">Sidebar</span>
                        <a class="biolife-close-btn" href="#" data-object="open-mobile-filter">&times;</a>
                    </div>
                    <div class="sidebar-contain">
                        <div class="widget biolife-filter">
                            <h4 class="wgt-title">Categorias</h4>
                            <div class="wgt-content">
                                <ul class="check-list multiple">
                                    @foreach($s_categorias as $value)
                                    <li class="cat-list-item"><a href="{{ url($url_link.'/'.$value->id) }}" class="check-link">{{ $value->nombre }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="widget biolife-filter">
                            <h4 class="wgt-title">Marcas</h4>
                            <div class="wgt-content">
                                <ul class="check-list multiple">
                                    @foreach($s_marca as $value)
                                    <li class="cat-list-item"><a href="{{ $value->id }}" class="check-link">{{ $value->nombre }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="widget biolife-filter">
                            <h4 class="wgt-title">Precio</h4>
                            <div class="wgt-content">
                                <ul class="check-list multiple">
                                    <li class="cat-list-item"><a href="#" class="check-link">Menor Precio</a></li>
                                    <li class="cat-list-item"><a href="#" class="check-link">Mayor Precio</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="widget biolife-filter">
                            <h4 class="wgt-title">Visto recientemente</h4>
                            <div class="wgt-content">
                                <ul class="products">
                                    <li class="pr-item">
                                        <div class="contain-product style-widget">
                                            <div class="product-thumb">
                                                <a href="#" class="link-to-product" tabindex="0">
                                                    <img src="{{ url('public/layouts_ecommerce/assets/images/products/p-13.jpg') }}" alt="dd" width="270" height="270" class="product-thumnail">
                                                </a>
                                            </div>
                                            <div class="info">
                                                <b class="categories">Fresh Fruit</b>
                                                <h4 class="product-title"><a href="#" class="pr-name" tabindex="0">National Fresh Fruit</a></h4>
                                                <div class="price">
                                                    <ins><span class="price-amount"><span class="currencySymbol">£</span>85.00</span></ins>
                                                    <del><span class="price-amount"><span class="currencySymbol">£</span>95.00</span></del>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="pr-item">
                                        <div class="contain-product style-widget">
                                            <div class="product-thumb">
                                                <a href="#" class="link-to-product" tabindex="0">
                                                    <img src="{{ url('public/layouts_ecommerce/assets/images/products/p-14.jpg') }}" alt="dd" width="270" height="270" class="product-thumnail">
                                                </a>
                                            </div>
                                            <div class="info">
                                                <b class="categories">Fresh Fruit</b>
                                                <h4 class="product-title"><a href="#" class="pr-name" tabindex="0">National Fresh Fruit</a></h4>
                                                <div class="price">
                                                    <ins><span class="price-amount"><span class="currencySymbol">£</span>85.00</span></ins>
                                                    <del><span class="price-amount"><span class="currencySymbol">£</span>95.00</span></del>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="pr-item">
                                        <div class="contain-product style-widget">
                                            <div class="product-thumb">
                                                <a href="#" class="link-to-product" tabindex="0">
                                                    <img src="{{ url('public/layouts_ecommerce/assets/images/products/p-10.jpg') }}" alt="dd" width="270" height="270" class="product-thumnail">
                                                </a>
                                            </div>
                                            <div class="info">
                                                <b class="categories">Fresh Fruit</b>
                                                <h4 class="product-title"><a href="#" class="pr-name" tabindex="0">National Fresh Fruit</a></h4>
                                                <div class="price">
                                                    <ins><span class="price-amount"><span class="currencySymbol">£</span>85.00</span></ins>
                                                    <del><span class="price-amount"><span class="currencySymbol">£</span>95.00</span></del>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="widget biolife-filter">
                            <h4 class="wgt-title">Etiquetas de producto</h4>
                            <div class="wgt-content">
                                <ul class="tag-cloud">
                                    <li class="tag-item"><a href="#" class="tag-link">Fresh Fruit</a></li>
                                    <li class="tag-item"><a href="#" class="tag-link">Natural Food</a></li>
                                    <li class="tag-item"><a href="#" class="tag-link">Hot</a></li>
                                    <li class="tag-item"><a href="#" class="tag-link">Organics</a></li>
                                    <li class="tag-item"><a href="#" class="tag-link">Dried Organic</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </aside>
            </div>
        </div>
    </div>
@endsection