<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $rutaimagen = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/logo/'.$tienda->imagen; ?>
    <title>{{ $tienda->nombre }}</title>
    @if(file_exists($rutaimagen))
      <link rel="shortcut icon" href="{{ url('public/backoffice/tienda/'.$tienda->id.'/logo/'.$tienda->imagen) }}">
    @endif
    <meta name="description" content="{{ $tienda->contenido }}" />
    <meta name="twitter:card" value="summary">
    <meta property="og:title" content="{{ $tienda->nombre }}" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="{{ Request::url() }}" />
    <meta property="og:description" content="{{ $tienda->contenido }}" />
  
    <link href="https://fonts.googleapis.com/css?family=Cairo:400,600,700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400i,700i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('public/layouts/ecommerce/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/layouts/ecommerce/assets/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/layouts/ecommerce/assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/layouts/ecommerce/assets/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ url('public/layouts/ecommerce/assets/css/slick.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/layouts/ecommerce/assets/css/style.css') }}">
    <style>
        .bg-main,
        .testml-elem-2,
        .btn.btn-thin:hover,
        .service-inner.style-02 .number,
        .biof-loading-center-absolute .dot,
        .biolife-service__type02 .foot-area,
        .biolife-tweet-item .tw-head .icon i,
        .biolife-social.circle-hover ul li a:hover,
        .footer.layout-02 .copy-rights-contain,
        .tab-head__default .tab-element .tab-link::before,
        .header-top .top-bar .social-list.circle-layout a:hover,
        .biolife-banner__grid:not(.type-02) .banner-contain .cat-name::before,
        .slider-opt04__layout01.light-version .buttons .btn.btn-bold:not(:hover),
        .biolife-banner__style-05 .btn-shopnow,
        .biolife-banner__style-06 .btn-shopnow,
        .biolife-banner__style-07 .btn-shopnow,
        .style-bottom-info.layout-02 .thumbnail .post-date,
        .style-bottom-info.layout-03 .thumbnail .post-date,
        .tab-head__icon-top-layout a::after,
        .slider-opt03__layout01.mode-03 .buttons .btn.btn-bold:not(:hover),
        .biolife-banner__special-02 .product-detail .add-to-cart-btn:not(:hover),
        .contain-product__right-info-layout3 .buttons .add-to-cart-btn:not(:hover),
        .contain-product__deal-layout .slide-down-box .buttons .add-to-cart-btn,
        .biolife-banner__special .product-detail .add-to-cart-btn,
        .post-item .group-buttons .count-number:hover .number,
        .post-item .post-content .btn.readmore:hover,
        .post-comments .form-row.last-btns .btn-sumit,
        .style-bottom-info.layout-03 .post-meta .liked .number,
        .single-post-contain .post-foot .socials-connection .social-list li a:hover,
        .wgt-twitter-item .detail .viewall a:hover::before,
        .biolife-carousel .slick-dots li.slick-active button,
        .btn.btn-bold:not(:hover),
        .contact-form-container .form-row .btn-submit,
        .login-on-checkout .form-row button,
        .minicart-block.layout-02 .icon-contain .btn-to-cart,
        .checkout-progress-wrap .checkout-act.active .title-box .number,
        .biolife-progress-bar .progress .progress-bar,
        .shpcart-subtotal-block .btn-checkout .btn,
        .shopping-cart-container table td.wrap-btn-control .btn,
        .review-form-wrapper .form-row button[type=submit],
        .header-area.layout-01 .mobile-search .open-searchbox,
        .product-tabs.single-layout .tab-head .tabs li a::before,
        .sumary-product .action-form .buttons .pull-row .btn:hover::before,
        .sumary-product .action-form .buttons .pull-row .btn:hover::after,
        .biolife-panigations-block ul li a:hover,
        .biolife-panigations-block ul li .current-page,
        .pr-detail-layout .info .buttons .add-to-cart-btn,
        .contain-product.layout-default .slide-down-box .buttons .add-to-cart-btn,
        .sidebar .price-filter .frm-contain .f-item .btn-submit,
        .newsletter-block .form-content .bnt-submit,
        .mobile-search .mobile-search-content .btn-submit,
        .vertical-category-block .block-title,
        .biolife-banner__promotion3 .product-detail .add-to-cart-btn,
        .biolife-banner__grid .banner-contain .cat-name::before,
        .biolife-cart-info .minicart-block .btn-control .view-cart,
        .sumary-product .action-form .buttons .add-to-cart-btn,
        .service-inner.color-reverse .number,
        .content-404 .button,
        .biolife-cart-info .icon-qty-combine .qty{
            background-color: {{$tienda->ecommerce_color}};
        }
        .hover-main-color:hover,
        .contain-product__right-info-layout2.cate .cat-info .cat-item:hover,
        .service-inner .srv-name:hover,
        .cmt-item a:hover,
        .btn-scroll-top:hover,
        .search-widget button[type=submit]:hover,
        .biolife-quickview-inner .product-attribute .title a:hover,
        .biolife-quickview-block .quickview-container .btn-close-quickview:hover,
        .biolife-quickview-inner .quickview-nav .slick-arrow:hover::before,
        .biolife-quickview-inner .product-atts-item .meta-list li a:hover,
        .main-slide.nav-change.type02.hover-main-color .slick-arrow:hover,
        .biolife-carousel.nav-top-right.nav-main-color .slick-arrow:hover,
        .biolife-title-box.link-all .blog-link:hover,
        .biolife-title-box.style-02 .subtitle,
        .biolife-banner__style-04 .text2 span,
        .main-slide.nav-change .slick-arrow,
        .biolife-banner__promotion3 .text-content .first-line,
        .newsletter-block_popup-layout .form-content .dismiss-newsletter:hover,
        .biolife-banner__grid:hover .cat-name,
        .minicart-block.layout-02 .icon-contain .biolife-icon,
        .header-area.layout-01 .header-top .top-bar .horizontal-menu a:hover,
        .tab-head__default .tab-element .tab-link:hover,
        .tab-head__default .tab-element.active .tab-link,
        .biolife-tweet-item .tw-content .message .link-bold:not(:hover),
        .header-area .biolife-cart-info .login-item .login-link:hover,
        .header-area.layout-02 .header-top .top-bar .horizontal-menu a:hover,
        .header-area.layout-02 .header-top .top-bar .social-list li a:hover,
        .style-bottom-info.layout-03 .post-meta__item a:hover,
        .style-bottom-info.layout-03 .group-buttons .btn.readmore,
        .style-bottom-info.layout-02 .group-buttons .btn.readmore,
        .style-bottom-info.layout-02 .post-meta__item-social-box li a:hover,
        .style-bottom-info.layout-02 .post-meta__item-social-box:hover .tbn,
        .style-bottom-info.layout-02 .post-meta__item.author:hover,
        .style-bottom-info.layout-02 .post-meta__item.btn:hover>.biolife-icon,
        .tab-head__icon-top-layout:not(.background-tab-include) .active a,
        .tab-head__icon-top-layout:not(.background-tab-include) .tab-element a:hover,
        .contain-product .product-thumb .lookup:hover,
        .service-inner .biolife-icon,
        .biolife-service__type01 .txt-show-02,
        .post-item .post-content .post-name a:hover,
        .wgt-post-item .detail .post-name a:hover,
        .post-comments  .wrap-post-comment .cmt-fooot .btn:hover,
        .post-comments .form-row.last-btns .btn:not(.btn-sumit):hover,
        .single-post-contain .post-foot .auth-info .avata:hover,
        .wgt-twitter-item .detail .viewall a:hover,
        .wgt-twitter-item .detail .tweet-count .btn:hover,
        .wgt-twitter-item .detail .tweet-content a,
        .wgt-twitter-item .detail .account-info .ath-name:hover,
        .wgt-twitter-item .detail .account-info .ath-taglink:hover,
        .welcome-us-block .qt-text,
        .signin-container .form-row .link-to-help,
        .login-on-checkout .msg a,
        .order-summary .title-block a:hover,
        .order-summary .subtotal-line a,
        .order-summary .cart-list .cart-item .info a:hover,
        .checkout-progress-wrap .checkout-act .box-content .txt-desc a:hover,
        .shopping-cart-container table td.product-thumbnail .prd-name:hover,
        .biolife-panigations-block.version-2 .result-count a:hover,
        .review-tab #comments .comment-review-form .actions li a:hover,
        .sumary-product .action-form .social-media ul li a:hover,
        .sumary-product .action-form .buttons .pull-row .btn:hover,
        .sumary-product .shipping-info p,
        .pr-detail-layout .info .buttons .wishlist-btn:hover,
        .pr-detail-layout .info .buttons .compare-btn:hover,
        .biolife-carousel.nav-center-02 .slick-arrow,
        .top-functions-area .viewmode-selector a.active,
        .top-functions-area .viewmode-selector a:hover,
        .contain-product .slide-down-box .buttons .btn:not(.add-to-cart-btn):hover,
        .sidebar .widget .wgt-title::after,
        .sidebar .wgt-content .color-list li.selected,
        .sidebar .wgt-content .color-list li a:hover,
        .sidebar .wgt-content .check-list li.selected a::after,
        .sidebar .wgt-content .check-list li.selected,
        .sidebar .wgt-content .check-list li a::after,
        .sidebar .wgt-content .check-list li a:hover,
        .sidebar .wgt-content .cat-list li a:hover,
        .biolife-nav ul li a:hover,
        .biolife-social ul li a:hover,
        .wrap-custom-menu.vertical-menu-2 li a:hover,
        .block-posts .menu-title,
        .biolife-brand .menu-title,
        .wrap-custom-menu .menu-title,
        .biolife-products-block .menu-title,
        .header-area .live-info .telephone i,
        .contain-product .info .product-title a:hover,
        .header-search-bar.layout-01 .btn-submit:hover,
        .biolife-carousel.nav-center-bold .slick-arrow:hover,
        .biolife-carousel.nav-center .slick-arrow:hover,
        .vertical-category-block .wrap-menu ul.sub-menu li:hover>a,
        .vertical-category-block .wrap-menu .menu>li:hover>a,
        .vertical-category-block .wrap-menu .menu li:hover>a:after,
        .block-posts .block-post-item .post-name a:hover,
        .header-area .primary-menu>ul>li.has-child .sub-menu a:hover,
        .wrap-custom-menu ul.menu>li a:hover,
        .minicart-item .left-info .product-name:hover,
        .header-area .primary-menu>ul>li.has-child:hover>a::after,
        .header-area .primary-menu>ul>li:hover>a,
        .nice-select .option:hover,
        .nice-select .option.focus,
        .content-404 .heading,
        .slider-opt03__layout01 .buttons .btn.btn-bold:not(:hover),
        .slider-opt03__layout01 .buttons .btn-thin:not(:hover),
        .nice-select .option.selected.focus{
            color: {{$tienda->ecommerce_color}};
        }
        body.top-refine-opened .top-functions-area .flt-item.group-on-mobile .wrap-selectors,
        .newsletter-block_popup-layout .form-content .dismiss-newsletter:hover::before,
        .mobile-search .mobile-search-content,
        .biolife-cart-info .minicart-block .cart-inner{
            border-top-color: {{$tienda->ecommerce_color}};
        }
        .btn.btn-thin:hover,
        .testml-elem-2 .avata,
        .biolife-service__type02,
        .contain-product__deal-layout,
        .minicart-block.layout-02 .icon-contain .span-index,
        .biolife-carousel.dots_ring_style .slick-dots li button,
        .post-item .post-content .btn.readmore:hover,
        .slider-opt03__layout02 .buttons .btn-thin,
        .single-post-contain .post-foot .socials-connection .social-list li a:hover,
        .biolife-quickview-block .quickview-nav li.slick-current img,
        .sumary-product .media .slider-nav li.slick-current img,
        .slider-opt04__layout01.light-version .buttons .btn-thin:not(:hover),
        .biolife-title-box.link-all .blog-link:hover::after,
        .biolife-cat-box-item:hover .cat-info,
        .biolife-panigations-block ul li a:hover,
        .biolife-panigations-block ul li .current-page,
        .sidebar .wgt-content .color-list li.selected a .hex-code,
        .sidebar .wgt-content .color-list li a:hover .hex-code,
        .sidebar .wgt-content .check-list li.selected a::before,
        .slider-opt03__layout01 .buttons .btn-thin:not(:hover),
        .sidebar .wgt-content .check-list li a:hover::before{
          border-color: {{$tienda->ecommerce_color}};
        }
  </style>
</head>
<body class="biolife-body">

    <!-- Preloader -->
    <div id="biof-loading">
        <div class="biof-loading-center">
            <div class="biof-loading-center-absolute">
                <div class="dot dot-one"></div>
                <div class="dot dot-two"></div>
                <div class="dot dot-three"></div>
            </div>
        </div>
    </div>

    <!-- HEADER -->
    <header id="header" class="header-area style-01 layout-03">
        <div class="header-top bg-main hidden-xs">
            <div class="container">
                <div class="top-bar left">
                    <ul class="horizontal-menu">
                        <li><a href="#"><i class="fa fa-envelope" aria-hidden="true"></i>{{ $tienda->correo }}</a></li>
                        <li><a href="#"><i class="fa fa-phone" aria-hidden="true"></i>({{ $tienda->codigotelefonicocodigo }}) {{ $tienda->numerotelefono }}</a></li>
                    </ul>
                </div>
                <div class="top-bar right">
                    <ul class="horizontal-menu">
                        <li><a href="login.html" class="login-link"><i class="biolife-icon icon-login"></i>Iniciar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="header-middle biolife-sticky-object ">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-2 col-md-6 col-xs-6">
                        <a href="{{ url('ecommerce/'.$tienda->link) }}" class="biolife-logo">
                           @if(file_exists($rutaimagen))
                              <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/logo/'.$tienda->imagen) }}" alt="{{ $tienda->nombre }}" style="height:43px;">
                           @else
                              <img src="{{ url('public/backoffice/sistema/sin_imagen_redondo.png') }}" style="height:43px;">
                           @endif
                      </a>
                    </div>
                    <div class="col-lg-6 col-md-7 hidden-sm hidden-xs">
                        <div class="primary-menu">
                            <ul class="menu biolife-menu clone-main-menu clone-primary-menu" id="primary-menu" data-menuname="main menu">
                                <li class="menu-item"><a href="{{ url('ecommerce/'.$tienda->link) }}">Inicio</a></li>
                                <li class="menu-item"><a href="#">Nosotros</a></li>
                                <li class="menu-item"><a href="#">Contactenos</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-md-6 col-xs-6">
                        <div class="biolife-cart-info">
                            <div class="mobile-search">
                                <a href="javascript:void(0)" class="open-searchbox"><i class="biolife-icon icon-search"></i></a>
                                <div class="mobile-search-content">
                                    <form action="{{ url('ecommerce/'.$url_link.'/producto') }}" class="form-search" name="mobile-seacrh" method="get">
                                        <a href="#" class="btn-close"><span class="biolife-icon icon-close-menu"></span></a>
                                        <input type="text" name="search" class="input-text" value="{{ isset($_GET['search']) ? $_GET['search'] : '' }}" placeholder="Buscar...">
                                        <select name="categoria">
                                            <option value="" selected>Toda las categorias</option>
                                            @foreach($s_categorias as $value)
                                            <option value="{{$value->nombre}}" {{isset($_GET['categoria'])?($_GET['categoria']==$value->nombre?'selected':''):''}}>{{ $value->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn-submit">Buscar</button>
                                    </form>
                                </div>
                            </div>
                            <div class="wishlist-block hidden-sm hidden-xs">
                                <a href="#" class="link-to">
                                    <span class="icon-qty-combine">
                                        <i class="icon-heart-bold biolife-icon"></i>
                                        <span class="qty">4</span>
                                    </span>
                                </a>
                            </div>
                            <div class="minicart-block">
                                <div class="minicart-contain">
                                    <a href="javascript:void(0)" class="link-to">
                                        <span class="icon-qty-combine">
                                            <i class="icon-cart-mini biolife-icon"></i>
                                            <span class="qty">8</span>
                                        </span>
                                        <span class="title">Mi Carrito -</span>
                                        <span class="sub-total">$0.00</span>
                                    </a>
                                    <div class="cart-content">
                                        <div class="cart-inner">
                                            <ul class="products">
                                                <li>
                                                    <div class="minicart-item">
                                                        <div class="thumb">
                                                            <a href="#"><img src="{{ url('public/layouts/ecommerce/assets/images/minicart/pr-01.jpg') }}" width="90" height="90" alt="National Fresh"></a>
                                                        </div>
                                                        <div class="left-info">
                                                            <div class="product-title"><a href="#" class="product-name">National Fresh Fruit</a></div>
                                                            <div class="price">
                                                                <ins><span class="price-amount"><span class="currencySymbol">£</span>85.00</span></ins>
                                                                <del><span class="price-amount"><span class="currencySymbol">£</span>95.00</span></del>
                                                            </div>
                                                            <div class="qty">
                                                                <label for="cart[id123][qty]">Qty:</label>
                                                                <input type="number" class="input-qty" name="cart[id123][qty]" id="cart[id123][qty]" value="1" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="action">
                                                            <a href="#" class="edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                            <a href="#" class="remove"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="minicart-item">
                                                        <div class="thumb">
                                                            <a href="#"><img src="{{ url('public/layouts/ecommerce/assets/images/minicart/pr-02.jpg') }}" width="90" height="90" alt="National Fresh"></a>
                                                        </div>
                                                        <div class="left-info">
                                                            <div class="product-title"><a href="#" class="product-name">National Fresh Fruit</a></div>
                                                            <div class="price">
                                                                <ins><span class="price-amount"><span class="currencySymbol">£</span>85.00</span></ins>
                                                                <del><span class="price-amount"><span class="currencySymbol">£</span>95.00</span></del>
                                                            </div>
                                                            <div class="qty">
                                                                <label for="cart[id124][qty]">Qty:</label>
                                                                <input type="number" class="input-qty" name="cart[id124][qty]" id="cart[id124][qty]" value="1" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="action">
                                                            <a href="#" class="edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                            <a href="#" class="remove"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="minicart-item">
                                                        <div class="thumb">
                                                            <a href="#"><img src="{{ url('public/layouts/ecommerce/assets/images/minicart/pr-03.jpg') }}" width="90" height="90" alt="National Fresh"></a>
                                                        </div>
                                                        <div class="left-info">
                                                            <div class="product-title"><a href="#" class="product-name">National Fresh Fruit</a></div>
                                                            <div class="price">
                                                                <ins><span class="price-amount"><span class="currencySymbol">£</span>85.00</span></ins>
                                                                <del><span class="price-amount"><span class="currencySymbol">£</span>95.00</span></del>
                                                            </div>
                                                            <div class="qty">
                                                                <label for="cart[id125][qty]">Qty:</label>
                                                                <input type="number" class="input-qty" name="cart[id125][qty]" id="cart[id125][qty]" value="1" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="action">
                                                            <a href="#" class="edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                            <a href="#" class="remove"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="minicart-item">
                                                        <div class="thumb">
                                                            <a href="#"><img src="{{ url('public/layouts/ecommerce/assets/images/minicart/pr-04.jpg') }}" width="90" height="90" alt="National Fresh"></a>
                                                        </div>
                                                        <div class="left-info">
                                                            <div class="product-title"><a href="#" class="product-name">National Fresh Fruit</a></div>
                                                            <div class="price">
                                                                <ins><span class="price-amount"><span class="currencySymbol">£</span>85.00</span></ins>
                                                                <del><span class="price-amount"><span class="currencySymbol">£</span>95.00</span></del>
                                                            </div>
                                                            <div class="qty">
                                                                <label for="cart[id126][qty]">Qty:</label>
                                                                <input type="number" class="input-qty" name="cart[id126][qty]" id="cart[id126][qty]" value="1" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="action">
                                                            <a href="#" class="edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                            <a href="#" class="remove"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="minicart-item">
                                                        <div class="thumb">
                                                            <a href="#"><img src="{{ url('public/layouts/ecommerce/assets/images/minicart/pr-05.jpg') }}" width="90" height="90" alt="National Fresh"></a>
                                                        </div>
                                                        <div class="left-info">
                                                            <div class="product-title"><a href="#" class="product-name">National Fresh Fruit</a></div>
                                                            <div class="price">
                                                                <ins><span class="price-amount"><span class="currencySymbol">£</span>85.00</span></ins>
                                                                <del><span class="price-amount"><span class="currencySymbol">£</span>95.00</span></del>
                                                            </div>
                                                            <div class="qty">
                                                                <label for="cart[id127][qty]">Qty:</label>
                                                                <input type="number" class="input-qty" name="cart[id127][qty]" id="cart[id127][qty]" value="1" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="action">
                                                            <a href="#" class="edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                            <a href="#" class="remove"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                            <p class="btn-control">
                                                <a href="#" class="btn view-cart">view cart</a>
                                                <a href="#" class="btn">checkout</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mobile-menu-toggle">
                                <a class="btn-toggle" data-object="open-mobile-menu" href="javascript:void(0)">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-bottom hidden-sm hidden-xs">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-4">
                        <div class="vertical-menu vertical-category-block">
                            <div class="block-title">
                                <span class="menu-icon">
                                    <span class="line-1"></span>
                                    <span class="line-2"></span>
                                    <span class="line-3"></span>
                                </span>
                                <span class="menu-title">Toda las categorias</span>
                                <span class="angle" data-tgleclass="fa fa-caret-down"><i class="fa fa-caret-up" aria-hidden="true"></i></span>
                            </div>
                            <div class="wrap-menu">
                                <ul class="menu clone-main-menu">
                                    @foreach($s_categorias as $value)
                                        <?php $subcategorias = DB::table('s_categoria')
                                                ->where('s_idcategoria',$value->id)
                                                ->get(); ?>
                                        <li class="menu-item {{count($subcategorias)>0?'menu-item-has-children has-child':''}}">
                                        <a href="{{ count($subcategorias)==0?url('ecommerce/'.$url_link.'/producto?categoria='.$value->nombre):'javascript:;' }}" 
                                           class="menu-name" data-title="{{ $value->nombre }}">{{ $value->nombre }}</a>
                                        @if(count($subcategorias)>0)
                                        <ul class="sub-menu">
                                            @foreach($subcategorias as $subvalue)
                                            <li class="menu-item"><a href="{{ url('ecommerce/'.$url_link.'/producto?categoria='.$subvalue->nombre) }}">{{ $subvalue->nombre }}</a></li>
                                            @endforeach
                                        </ul>
                                        @endif
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-8 padding-top-2px">
                        <div class="header-search-bar layout-01">
                            <form action="{{ url('ecommerce/'.$url_link.'/producto') }}" class="form-search" method="get">
                                <input type="text" name="search" class="input-text" value="{{ isset($_GET['search']) ? $_GET['search'] : '' }}" placeholder="Buscar...">
                                <select name="categoria">
                                    <option value="" selected>Toda las categorias </option>
                                    @foreach($s_categorias as $value)
                                    <option value="{{$value->nombre}}" {{isset($_GET['categoria'])?($_GET['categoria']==$value->nombre?'selected':''):''}}>{{ $value->nombre }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn-submit"><i class="biolife-icon icon-search"></i></button>
                            </form>
                        </div>
                        <div class="live-info">
                            <p class="telephone"><i class="fa fa-phone" aria-hidden="true"></i><b class="phone-number">({{ $tienda->codigotelefonicocodigo }}) {{ $tienda->numerotelefono }}</b></p>
                            <p class="working-time">{{ $tienda->correo }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    @yield('cuerpoecommerce')

    <!-- FOOTER -->
    <footer id="footer" class="footer layout-03">
        <div class="footer-content background-footer-03">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-9">
                        <section class="footer-item">
                            <a href="home-03-green.html" class="logo footer-logo">
                             @if(file_exists($rutaimagen))
                                <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/logo/'.$tienda->imagen) }}" alt="{{ $tienda->nombre }}" style="height:43px;">
                             @else
                                <img src="{{ url('public/backoffice/sistema/sin_imagen_redondo.png') }}" style="height:43px;">
                             @endif
                            <div class="newsletter-block layout-01">
                                <h4 class="title">Inscríbase al boletín</h4>
                                <div class="form-content">
                                    <form action="#" name="new-letter-foter">
                                        <input type="email" class="input-text email" value="" placeholder="Tu correo aquí...">
                                        <button type="submit" class="bnt-submit" name="ok">Registrarse</button>
                                    </form>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 md-margin-top-5px sm-margin-top-50px xs-margin-top-40px">
                        <section class="footer-item">
                            <h3 class="section-title">Enlaces Útiles</h3>
                            <div class="row">
                                <div class="col-lg-6 col-sm-6 col-xs-6">
                                    <div class="wrap-custom-menu vertical-menu-2">
                                        <ul class="menu">
                                            <li><a href="#">Acerca de nosotros</a></li>
                                            <li><a href="#">Compra segura</a></li>
                                            <li><a href="#">Información de entrega</a></li>
                                            <li><a href="#">Política de privacidad</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6 col-xs-6">
                                    <div class="wrap-custom-menu vertical-menu-2">
                                        <ul class="menu">
                                            <li><a href="#">Nuestros productos</a></li>
                                            <li><a href="#">Contactos de nosotros</a></li>
                                            <li><a href="#">Testimonios</a></li>
                                            <li><a href="#">Nuestro mapa del sitio</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 md-margin-top-5px sm-margin-top-50px xs-margin-top-40px">
                        <section class="footer-item">
                            <h3 class="section-title">Oficina</h3>
                            <div class="contact-info-block footer-layout xs-padding-top-10px">
                                <ul class="contact-lines">
                                    <li>
                                        <p class="info-item">
                                            <i class="biolife-icon icon-phone"></i>
                                            <b class="desc">Télefono: ({{$tienda->codigotelefonicocodigo}}) {{$tienda->numerotelefono}}</b>
                                        </p>
                                    </li>
                                    <li>
                                        <p class="info-item">
                                            <i class="biolife-icon icon-letter"></i>
                                            <b class="desc">Correo:  {{$tienda->correo}}</b>
                                        </p>
                                    </li>
                                    <li>
                                        <p class="info-item">
                                            <i class="biolife-icon icon-location"></i>
                                            <b class="desc">{{$tienda->direccion}}</b>
                                        </p>
                                    </li>
                                </ul>
                            </div>
                            <div class="biolife-social inline">
                                <ul class="socials">
                                    <li><a href="#" title="twitter" class="socail-btn"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                                    <li><a href="#" title="facebook" class="socail-btn"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                                    <li><a href="#" title="pinterest" class="socail-btn"><i class="fa fa-pinterest" aria-hidden="true"></i></a></li>
                                    <li><a href="#" title="youtube" class="socail-btn"><i class="fa fa-youtube" aria-hidden="true"></i></a></li>
                                    <li><a href="#" title="instagram" class="socail-btn"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                                </ul>
                            </div>
                        </section>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="separator sm-margin-top-62px xs-margin-top-40px"></div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-xs-12">
                       <div class="copy-right-text"><p><a href="{{url($tienda->link)}}">© {{$tienda->nombre}} 2020. Todos los derechos reservados.</a></p></div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-xs-12">
                        <div class="payment-methods">
                            <ul>
                                <li><a href="#" class="payment-link"><img src="{{ url('public/layouts/ecommerce/assets/images/card1.jpg') }}" width="51" height="36" alt=""></a></li>
                                <li><a href="#" class="payment-link"><img src="{{ url('public/layouts/ecommerce/assets/images/card2.jpg') }}" width="51" height="36" alt=""></a></li>
                                <li><a href="#" class="payment-link"><img src="{{ url('public/layouts/ecommerce/assets/images/card3.jpg') }}" width="51" height="36" alt=""></a></li>
                                <li><a href="#" class="payment-link"><img src="{{ url('public/layouts/ecommerce/assets/images/card4.jpg') }}" width="51" height="36" alt=""></a></li>
                                <li><a href="#" class="payment-link"><img src="{{ url('public/layouts/ecommerce/assets/images/card5.jpg') }}" width="51" height="36" alt=""></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!--Footer For Mobile-->
    <div class="mobile-footer">
        <div class="mobile-footer-inner">
            <div class="mobile-block block-menu-main">
                <a class="menu-bar menu-toggle btn-toggle" data-object="open-mobile-menu" href="javascript:void(0)">
                    <span class="fa fa-bars"></span>
                    <span class="text">Menu</span>
                </a>
            </div>
            <div class="mobile-block block-sidebar">
                <a class="menu-bar filter-toggle btn-toggle" data-object="open-mobile-filter" href="javascript:void(0)">
                    <i class="fa fa-sliders" aria-hidden="true"></i>
                    <span class="text">Sidebar</span>
                </a>
            </div>
            <div class="mobile-block block-minicart">
                <a class="link-to-cart" href="#">
                    <span class="fa fa-shopping-bag" aria-hidden="true"></span>
                    <span class="text">Cart</span>
                </a>
            </div>
            <div class="mobile-block block-global">
                <a class="menu-bar myaccount-toggle btn-toggle" data-object="global-panel-opened" href="javascript:void(0)">
                    <span class="fa fa-globe"></span>
                    <span class="text">Global</span>
                </a>
            </div>
        </div>
    </div>

    <div class="mobile-block-global">
        <div class="biolife-mobile-panels">
            <span class="biolife-current-panel-title">Global</span>
            <a class="biolife-close-btn" data-object="global-panel-opened" href="#">&times;</a>
        </div>
        <div class="block-global-contain">
            <div class="glb-item my-account">
                <b class="title">My Account</b>
                <ul class="list">
                    <li class="list-item"><a href="#">Login/register</a></li>
                    <li class="list-item"><a href="#">Wishlist <span class="index">(8)</span></a></li>
                    <li class="list-item"><a href="#">Checkout</a></li>
                </ul>
            </div>
            <div class="glb-item currency">
                <b class="title">Currency</b>
                <ul class="list">
                    <li class="list-item"><a href="#">€ EUR (Euro)</a></li>
                    <li class="list-item"><a href="#">$ USD (Dollar)</a></li>
                    <li class="list-item"><a href="#">£ GBP (Pound)</a></li>
                    <li class="list-item"><a href="#">¥ JPY (Yen)</a></li>
                </ul>
            </div>
            <div class="glb-item languages">
                <b class="title">Language</b>
                <ul class="list inline">
                    <li class="list-item"><a href="#"><img src="{{ url('public/layouts/ecommerce/assets/images/languages/us.jpg') }}" alt="flag" width="24" height="18"></a></li>
                    <li class="list-item"><a href="#"><img src="{{ url('public/layouts/ecommerce/assets/images/languages/fr.jpg') }}" alt="flag" width="24" height="18"></a></li>
                    <li class="list-item"><a href="#"><img src="{{ url('public/layouts/ecommerce/assets/images/languages/ger.jpg') }}" alt="flag" width="24" height="18"></a></li>
                    <li class="list-item"><a href="#"><img src="{{ url('public/layouts/ecommerce/assets/images/languages/jap.jpg') }}" alt="flag" width="24" height="18"></a></li>
                </ul>
            </div>
        </div>
    </div>

    <!--Quickview Popup-->
    <div id="biolife-quickview-block" class="biolife-quickview-block">
        <div class="quickview-container">
            <a href="#" class="btn-close-quickview" data-object="open-quickview-block"><span class="biolife-icon icon-close-menu"></span></a>
            <div class="biolife-quickview-inner">
                <div class="media">
                    <ul class="biolife-carousel quickview-for" data-slick='{"arrows":false,"dots":false,"slidesMargin":30,"slidesToShow":1,"slidesToScroll":1,"fade":true,"asNavFor":".quickview-nav"}'>
                        <li><img src="{{ url('public/layouts/ecommerce/assets/images/details-product/detail_01.jpg') }}" alt="" width="500" height="500"></li>
                        <li><img src="{{ url('public/layouts/ecommerce/assets/images/details-product/detail_02.jpg') }}" alt="" width="500" height="500"></li>
                        <li><img src="{{ url('public/layouts/ecommerce/assets/images/details-product/detail_03.jpg') }}" alt="" width="500" height="500"></li>
                        <li><img src="{{ url('public/layouts/ecommerce/assets/images/details-product/detail_04.jpg') }}" alt="" width="500" height="500"></li>
                        <li><img src="{{ url('public/layouts/ecommerce/assets/images/details-product/detail_05.jpg') }}" alt="" width="500" height="500"></li>
                        <li><img src="{{ url('public/layouts/ecommerce/assets/images/details-product/detail_06.jpg') }}" alt="" width="500" height="500"></li>
                        <li><img src="{{ url('public/layouts/ecommerce/assets/images/details-product/detail_07.jpg') }}" alt="" width="500" height="500"></li>
                    </ul>
                    <ul class="biolife-carousel quickview-nav" data-slick='{"arrows":true,"dots":false,"centerMode":false,"focusOnSelect":true,"slidesMargin":10,"slidesToShow":3,"slidesToScroll":1,"asNavFor":".quickview-for"}'>
                        <li><img src="{{ url('public/layouts/ecommerce/assets/images/details-product/thumb_01.jpg') }}" alt="" width="88" height="88"></li>
                        <li><img src="{{ url('public/layouts/ecommerce/assets/images/details-product/thumb_02.jpg') }}" alt="" width="88" height="88"></li>
                        <li><img src="{{ url('public/layouts/ecommerce/assets/images/details-product/thumb_03.jpg') }}" alt="" width="88" height="88"></li>
                        <li><img src="{{ url('public/layouts/ecommerce/assets/images/details-product/thumb_04.jpg') }}" alt="" width="88" height="88"></li>
                        <li><img src="{{ url('public/layouts/ecommerce/assets/images/details-product/thumb_05.jpg') }}" alt="" width="88" height="88"></li>
                        <li><img src="{{ url('public/layouts/ecommerce/assets/images/details-product/thumb_06.jpg') }}" alt="" width="88" height="88"></li>
                        <li><img src="{{ url('public/layouts/ecommerce/assets/images/details-product/thumb_07.jpg') }}" alt="" width="88" height="88"></li>
                    </ul>
                </div>
                <div class="product-attribute">
                    <h4 class="title"><a href="#" class="pr-name">National Fresh Fruit</a></h4>
                    <div class="rating">
                        <p class="star-rating"><span class="width-80percent"></span></p>
                    </div>

                    <div class="price price-contain">
                        <ins><span class="price-amount"><span class="currencySymbol">£</span>85.00</span></ins>
                        <del><span class="price-amount"><span class="currencySymbol">£</span>95.00</span></del>
                    </div>
                    <p class="excerpt">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris vel maximus lacus. Duis ut mauris eget justo dictum tempus sed vel tellus.</p>
                    <div class="from-cart">
                        <div class="qty-input">
                            <input type="text" name="qty12554" value="1" data-max_value="20" data-min_value="1" data-step="1">
                            <a href="#" class="qty-btn btn-up"><i class="fa fa-caret-up" aria-hidden="true"></i></a>
                            <a href="#" class="qty-btn btn-down"><i class="fa fa-caret-down" aria-hidden="true"></i></a>
                        </div>
                        <div class="buttons">
                            <a href="#" class="btn add-to-cart-btn btn-bold">add to cart</a>
                        </div>
                    </div>

                    <div class="product-meta">
                        <div class="product-atts">
                            <div class="product-atts-item">
                                <b class="meta-title">Categories:</b>
                                <ul class="meta-list">
                                    <li><a href="#" class="meta-link">Milk & Cream</a></li>
                                    <li><a href="#" class="meta-link">Fresh Meat</a></li>
                                    <li><a href="#" class="meta-link">Fresh Fruit</a></li>
                                </ul>
                            </div>
                            <div class="product-atts-item">
                                <b class="meta-title">Tags:</b>
                                <ul class="meta-list">
                                    <li><a href="#" class="meta-link">food theme</a></li>
                                    <li><a href="#" class="meta-link">organic food</a></li>
                                    <li><a href="#" class="meta-link">organic theme</a></li>
                                </ul>
                            </div>
                            <div class="product-atts-item">
                                <b class="meta-title">Brand:</b>
                                <ul class="meta-list">
                                    <li><a href="#" class="meta-link">Fresh Fruit</a></li>
                                </ul>
                            </div>
                        </div>
                        <span class="sku">SKU: N/A</span>
                        <div class="biolife-social inline add-title">
                            <span class="fr-title">Share:</span>
                            <ul class="socials">
                                <li><a href="#" title="twitter" class="socail-btn"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                                <li><a href="#" title="facebook" class="socail-btn"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                                <li><a href="#" title="pinterest" class="socail-btn"><i class="fa fa-pinterest" aria-hidden="true"></i></a></li>
                                <li><a href="#" title="youtube" class="socail-btn"><i class="fa fa-youtube" aria-hidden="true"></i></a></li>
                                <li><a href="#" title="instagram" class="socail-btn"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll Top Button -->
    <a class="btn-scroll-top"><i class="biolife-icon icon-left-arrow"></i></a>

    <script src="{{ url('public/layouts/ecommerce/assets/js/jquery-3.4.1.min.js') }}"></script>
    <script src="{{ url('public/layouts/ecommerce/assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ url('public/layouts/ecommerce/assets/js/jquery.countdown.min.js') }}"></script>
    <script src="{{ url('public/layouts/ecommerce/assets/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ url('public/layouts/ecommerce/assets/js/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ url('public/layouts/ecommerce/assets/js/slick.min.js') }}"></script>
    <script src="{{ url('public/layouts/ecommerce/assets/js/biolife.framework.js') }}"></script>
    <script src="{{ url('public/layouts/ecommerce/assets/js/functions.js') }}"></script>
</body>

</html>