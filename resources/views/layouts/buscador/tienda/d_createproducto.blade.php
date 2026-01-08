@extends('layouts.buscador.master')
@section('cuerpotienda')
<div class="row">
    <div class="col-md-9">
        <div class="list-single-main-wrapper fl-wrap">
            <div class="breadcrumbs gradient-bg fl-wrap">
              <span>Inicio {{ $menucategoria }}</span>
            </div>
        </div>

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
                                $url_imagen = '/public/backoffice/tienda/'.$tienda->id.'/producto/250/';
                                $rutaimagen = getcwd().$url_imagen.$s_productogaleria->imagen; 
                               ?>
                               @if(file_exists($rutaimagen) AND $s_productogaleria->imagen!='')
                                   
                              
                                  <div class="gallery-item">
                                      <div class="grid-item-holder">
                                          <div class="box-item" 
                                              style="background-image: url({{ url($url_imagen.$s_productogaleria->imagen) }});
                                                        background-repeat: no-repeat;
                                                        background-size: contain;
                                                        background-position: center;">
                                              <a href="javascript:;" id="modal-tiendaproducto" onclick="selectproducto({{$valueproducto->id}})" class="gal-link">
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
                                  <!--<div class="gallery-items" style="display: none;">
                                      <div class="grid-item-holder">
                                          <div class="box-item">
                                            <a href="{{ url($url_imagen.$value->imagen) }}" class="gal-link popup-image">
                                              <i class="fa fa-search"></i></a>
                                          </div>
                                      </div>
                                  </div>-->
                                  @endforeach
                               @else
                                  <div class="gallery-item">
                                      <div class="grid-item-holder">
                                          <div class="box-item" 
                                              style="background-image: url({{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }});
                                                        background-repeat: no-repeat;
                                                        background-size: 100% 100%;">
                                              <a href="javascript:;" id="modal-tiendaproducto" onclick="selectproducto({{$valueproducto->id}})" class="gal-link">
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
                                              <a href="javascript:;" id="modal-tiendaproducto" onclick="selectproducto({{$valueproducto->id}})" class="gal-link">
                                              <i class="fa fa-search"></i></a>
                                          </div>
                                      </div>
                                  </div>
                               @endif
                            </div>
                        </div>
                        <div class="card-post-content fl-wrap">
                            <h3><a href="javascript:;" id="modal-tiendaproducto" onclick="selectproducto({{$valueproducto->id}})">{{ $valueproducto->nombre }}</a></h3>
                            <div class="post-opt">
                              <ul>
                                    @if($valueproducto->preciopormayor>0)
                                    <!--li><i class="fa fa-tags"></i> <span style="text-decoration: line-through;">s/. {{ $valueproducto->preciopormayor }}</span></li-->
                                    @endif
                                    <li><i class="fa fa-tags"></i> <a href="#">{{ $valueproducto->precioalpublico }}</a>  </li>
                                </ul>
                              <a href="javascript:;" id="modal-tiendaproducto" onclick="selectproducto({{$valueproducto->id}})" class="price-link">
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
                <h3>Busqueda</h3>
            </div>
            <div class="box-widget-item">
                <div class="list-author-widget-contacts list-item-widget-contacts">
                    <ul>
                        <?php 
                        $checknombre = explode('/',$menucategoria);
                        $check = str_replace(' ','-',ltrim(mb_strtolower($checknombre[1])));
                        ?>
                        @if($check!='todo-los-productos')
                        <input type="hidden" id="categoria" value="{{ $check }}">
                        <li style="border: 0px;">
                          <label class="radio inline"> 
                              <input type="radio" name="checkcategoria" checked disabled>
                              <span><a href="javascript:;">{{ $checknombre[1] }}</a></span> 
                          </label>
                          <a href="javascript:;" onclick="search_tienda_categoria()" style="float: right;"><i class="fa fa-close" style="color: #d61212;font-size: 15px;"></i></a>
                        </li>
                        @else
                        <input type="hidden" id="categoria" value="searchtienda">
                        <li style="border: 0px;">
                          <label class="radio inline"> 
                              <input type="radio" name="checkcategoria" checked disabled>
                              <span><a href="javascript:;">Toda las categorias</a></span> 
                          </label>
                        </li>
                        @endif
                        @if(isset($_GET['marca']))
                        @if($_GET['marca']!='')
                        <?php
                        $get_marca = str_replace('-',' ',ucfirst($_GET['marca']));
                        ?>
                        <input type="hidden" id="marca" value="{{ $_GET['marca'] }}">
                        <li style="border: 0px;">
                          <label class="radio inline"> 
                              <input type="radio" name="checkmarca" checked disabled>
                              <span><a href="javascript:;">{{ $get_marca }}</a></span> 
                          </label>
                          <a href="javascript:;" onclick="search_tienda_marca()" style="float: right;"><i class="fa fa-close" style="color: #d61212;font-size: 15px;"></i></a>
                        </li>
                        @endif
                        @endif
                        @if(isset($_GET['precio']))
                        @if($_GET['precio']!='')
                        <?php
                        $get_precio = str_replace('-',' ',ucfirst($_GET['precio']));
                        ?>
                        <input type="hidden" id="precio" value="{{ $_GET['precio'] }}">
                        <li style="border: 0px;">
                          <label class="radio inline"> 
                              <input type="radio" name="checkprecio" checked disabled>
                              <span><a href="javascript:;">{{ $get_precio }}</a></span> 
                          </label>
                          <a href="javascript:;" onclick="search_tienda_precio()" style="float: right;"><i class="fa fa-close" style="color: #d61212;font-size: 15px;"></i></a>
                        </li>
                        @endif
                        @endif
                    </ul>
                </div>
            </div>
        </div>
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
                              <input type="radio" name="gender{{$value->id}}" <?php if($check == $validcategorianombre){ echo 'checked'; } ?> disabled>
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
.grid-small-pad .grid-item-holder {
    padding: 0;
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
//val("{{isset($_GET['marca'])?ucfirst($_GET['marca']):''}}").trigger("change");
  
function search_tienda_categoria(value=''){
    if(value==''){
        value = 'searchtienda';
    }
    var categoria = value; 
    var marca = $("#marca").val();  
    var precio = $("#precio").val();
    search_tienda(categoria,marca,precio);
}
function search_tienda_marca(value=''){
    var categoria = $("#categoria").val(); 
    var marca = value;  
    var precio = $("#precio").val();
    search_tienda(categoria,marca,precio);
}
function search_tienda_precio(value=''){
    var categoria = $("#categoria").val(); 
    var marca = $("#marca").val();  
    var precio = value;
    search_tienda(categoria,marca,precio);
}
function search_tienda(categoria,marca,precio){
    var t_categoria = '';
    if(categoria!=undefined){
       t_categoria = categoria;
    }
    var t_marca = '';
    if(marca!=undefined){
       t_marca = marca;
    }
    var t_precio = '';
    if(precio!=undefined){
       t_precio = precio;
    }
    location.href = '{{ url($url_link) }}/'+t_categoria+'?marca='+t_marca+'&precio='+t_precio;
}
</script>
@endsection