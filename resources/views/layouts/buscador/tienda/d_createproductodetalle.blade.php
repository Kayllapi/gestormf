<div class="col-md-12">
    <div class="row">
        <div class="col-md-5">
            <div class="video-box fl-wrap">
                <div class="gallery-items grid-small-pad  list-single-gallery three-coulms lightgallery">
                    <?php 
                    $s_productogaleria = DB::table('s_productogaleria')
                        ->where('s_idproducto',$s_producto->id)
                        ->select(
                            's_productogaleria.*'
                        )
                        ->orderBy('orden','asc')
                        ->limit(1)
                        ->first();
                    ?>
                    <?php
                    $tiendaproducto = DB::table('tienda')
                        ->join('s_categoria','s_categoria.idtienda','=','tienda.id')
                        ->join('s_producto','s_producto.s_idcategoria1','=','s_categoria.id')
                        ->where('s_producto.id',$s_producto->id)
                        ->select(
                            'tienda.*'
                        )
                        ->limit(1)
                        ->first();
                    ?>
                    @if($s_productogaleria!='')
                        <?php
                        $url_imagen = '/public/backoffice/tienda/'.$tiendaproducto->id.'/producto/250/';
                        $url_imagen_original = '/public/backoffice/tienda/'.$tiendaproducto->id.'/producto/';
                        $rutaimagen = getcwd().$url_imagen.$s_productogaleria->imagen; 
                        ?>
                        @if(file_exists($rutaimagen) AND $s_productogaleria->imagen!='')
                            <div class="gallery-item mx-gallery-item">
                                <div class="grid-item-holder">
                                    <div class="box-item" 
                                    onclick="$('#imggaleria{{$s_productogaleria->id}}').click()"
                                    style="background-image: url({{ url($url_imagen.$s_productogaleria->imagen) }});
                                                            background-repeat: no-repeat;
                                                            background-size: contain;
                                                            background-position: center;">
                                        <a href="{{ url('public/backoffice/tienda/'.$tiendaproducto->id.'/producto/'.$s_productogaleria->imagen) }}" id="imggaleria{{$s_productogaleria->id}}" class="gal-link popup-image-detalle">
                                        <i class="fa fa-search"></i></a>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $s_productogalerias = DB::table('s_productogaleria')
                                ->where('s_idproducto',$s_producto->id)
                                ->where('id','<>',$s_productogaleria->id)
                                ->orderBy('orden','asc')
                                ->get();
                            ?>
                            @foreach($s_productogalerias as $value)
                            <?php
                            $rutaimagen_original = getcwd().$url_imagen.$s_productogaleria->imagen; 
                            ?>
                            @if(file_exists($url_imagen_original) AND $value->imagen!='')
                            <div class="gallery-item" style="display: none;">
                                <div class="grid-item-holder">
                                    <div class="box-item">
                                      <a href="{{ url($rutaimagen_original.$value->imagen) }}" class="gal-link popup-image-detalle">
                                        <i class="fa fa-search"></i></a>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        @else
                            <div class="gallery-item mx-gallery-item">
                                <div class="grid-item-holder">
                                    <div class="box-item" 
                                    onclick="$('#imggaleria1').click()"
                                    style="background-image: url({{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }});
                                                            background-repeat: no-repeat;
                                                            background-size: contain;
                                                            background-position: center;">
                                        <a href="{{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }}" id="imggaleria1" class="gal-link popup-image-detalle">
                                        <i class="fa fa-search"></i></a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                            <div class="gallery-item mx-gallery-item">
                                <div class="grid-item-holder">
                                    <div class="box-item" 
                                    onclick="$('#imggaleria1').click()"
                                    style="background-image: url({{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }});
                                                            background-repeat: no-repeat;
                                                            background-size: contain;
                                                            background-position: center;">
                                        <a href="{{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }}" id="imggaleria1" class="gal-link popup-image-detalle">
                                        <i class="fa fa-search"></i></a>
                                    </div>
                                </div>
                            </div>
                    @endif   
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="list-single-main-item-title fl-wrap">
                <h3>{{ $s_producto->nombre }}</h3>
                <div>
                <!--b>Precio Normal:</b> <span style="text-decoration: line-through;">{{ $s_producto->preciopormayor }}</span><br-->
                <b>Precio Normal:</b> <label>{{ $s_producto->precioalpublico }}</label>
                </div>
                <span class="section-separator fl-sec-sep"></span>
            </div>
            <div class="profile-edit-container">
              <div class="custom-form">
                  <div class="row">
                      <div class="col-md-6">
                          <label>Cantidad *</label>
                          <input type="number" value="1" min="1" id="cantidadcarritocompra">
                      </div>
                      <div class="col-md-6">
                          <label>Total</label>
                          <input type="number" value="{{ $s_producto->precioalpublico }}" disabled="">
                      </div>
                  </div>
              </div>
            </div>
            
            <a href="javascript:;" 
                onclick="click_addproducto(
                  '{{$s_producto->id}}',
                  '{{$tiendaproducto->id}}',
                  '{{url($tiendaproducto->link)}}',
                  '{{$tiendaproducto->nombre}}',
                  '{{$s_producto->codigo}}',
                  '{{$s_producto->nombre}}',
                  '{{$s_producto->preciopormayor}}',
                  '{{$s_producto->precioalpublico}}',
                  $('#cantidadcarritocompra').val()
                )" class="btn transparent-btn float-btn custom-scroll-link"><i class="fa fa-check"></i> Agregar a Carrito</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <span class="fw-separator"></span>
            <div class="list-single-main-item-title fl-wrap">
                <h3 style="margin-bottom: 0;">Descripción</h3>
            </div>
            <div class="listing-features fl-wrap">
                <ul>
                <?php 
                $s_productodetalles = DB::table('s_productodetalle')
                    ->where('s_idproducto',$s_producto->id)
                    ->orderBy('orden','asc')
                    ->orderBy('suborden','asc')
                    ->orderBy('subsuborden','asc')
                    ->get();
                ?>
                @foreach($s_productodetalles as $value)
                    @if($value->orden!='' && $value->suborden==0 && $value->subsuborden==0)
                        <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><i class="fa fa-angle-right"></i> {{ $value->nombre }}</b></li>
                    @elseif($value->orden!='' && $value->suborden!='' && $value->subsuborden==0)
                        <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><i class="fa fa-angle-double-right"></i> {{ $value->nombre }}</b></li>
                    @elseif($value->orden!='' && $value->suborden!='' && $value->subsuborden!='')
                        <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-angle-right"></i> {{ $value->nombre }}</li>
                    @endif
                @endforeach
                </ul>
              </div>
        </div>
    </div>
</div>
<script>
//   lightGallery------------------
    $(".image-popup").lightGallery({
        selector: "this",
        cssEasing: "cubic-bezier(0.25, 0, 0.25, 1)",
        download: false,
        counter: false
    });
    var o = $(".lightgallery"),
        p = o.data("looped");
    o.lightGallery({
        selector: ".lightgallery a.popup-image-detalle",
        cssEasing: "cubic-bezier(0.25, 0, 0.25, 1)",
        download: false,
        loop: false,
		counter: false
    });
</script>