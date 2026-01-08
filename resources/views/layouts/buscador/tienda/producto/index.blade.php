@extends('layouts.buscador.tienda.master')
@section('cuerpotienda')

<div id="contenido-producto-carritocompra-load"></div>
<div id="contenido-producto-carritocompra" style="display:none;">
  <div class="cont-confirm">
    <div class="confirm"><i class="fa fa-check"></i></div>
    <div class="confirm-texto">¡Correcto!</div>
    <div class="confirm-subtexto">Se ha agreado correctamente.</div>
  </div>
  <div class="custom-form" style="text-align: center;">
      <a href="javascript:;" class="btn big-btn color-bg flat-btn" style="margin: auto;float: none;" id="modal-carritocompra" onclick="selectcarritocompra()">
          <i class="fa fa-shopping-cart"></i> Ver  Carrito de compra
      </a>
  </div>
</div>
<div id="contenido-producto-detalle">
<div class="col-md-12">
            <div class="list-single-main-item-title fl-wrap">
                <h3>{{ $s_producto->nombre }}</h3>
            </div>
    <div class="row">
        <div class="col-md-6">
            <?php
            $s_productogaleria = DB::table('s_productogaleria')
                                  ->join('s_producto','s_producto.id','s_productogaleria.s_idproducto')
                                  ->where('s_producto.idtienda',$tienda->id)
                                  ->where('s_productogaleria.s_idproducto',$s_producto->id)
                                  ->select('s_productogaleria.*')
                                  ->orderBy('s_productogaleria.orden','asc')
                                  ->limit(1)
                                  ->first();
               
            $tiendaproducto = DB::table('tienda')
                        ->join('s_categoria','s_categoria.idtienda','=','tienda.id')
                        ->join('s_producto','s_producto.s_idcategoria1','=','s_categoria.id')
                        ->where('s_producto.idtienda',$tienda->id)
                        ->where('s_producto.id',$s_producto->id)
                        ->select(
                            'tienda.*'
                        )
                        ->limit(1)
                        ->first();
              
          
            
            $imagenes = '<div class="gallery-items grid-small-pad  list-single-gallery three-coulms lightgallery">
                                <div class="">
                                      <div class="grid-item-holder">
                                          <div class="box-item" 
                                              style="background-image: url('.url('public/backoffice/sistema/sin_imagen_cuadrado.png').');
                                                        background-repeat: no-repeat;
                                                        background-size: contain;
                                                        background-position: center;
                                                        height: 131px;">
                                              <a href="javascript:;" id="modal-tiendaproducto" class="gal-link">
                                              <i class="fa fa-search"></i></a>
                                          </div>
                                      </div>
                                  </div>
                             </div>';
          
            if($s_productogaleria!=''){
                $rutaimagen = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/producto/'.$s_productogaleria->imagen; 
                if(file_exists($rutaimagen) AND $s_productogaleria->imagen!=''){
                      $imagenes = '<div class="gallery-items grid-small-pad  list-single-gallery three-coulms lightgallery">
                                  <div class="mx-gallery-item">
                                      <div class="grid-item-holder">
                                          <div class="box-item" onclick="$(\'#imggaleria1\').click()"
                                              style="background-image: url('.url('public/backoffice/tienda/'.$tienda->id.'/producto/'.$s_productogaleria->imagen).');
                                                        background-repeat: no-repeat;
                                                        background-size: contain;
                                                        background-position: center;
                                                        height: 245px;">
                                              <a href="'.url('public/backoffice/tienda/'.$tienda->id.'/producto/'.$s_productogaleria->imagen).'" id="imggaleria1"  class="gal-link popup-image-detalle">
                                              <i class="fa fa-search"></i></a>
                                          </div>
                                      </div>
                                  </div>';
                  
                                  $s_productogalerias = DB::table('s_productogaleria')
                                      ->join('s_producto','s_producto.id','s_productogaleria.s_idproducto')
                                      ->where('s_producto.idtienda',$tienda->id)
                                      ->where('s_productogaleria.s_idproducto',$s_producto->id)
                                      ->where('s_productogaleria.id','<>',$s_productogaleria->id)
                                      ->orderBy('s_productogaleria.orden','asc')
                                      ->get();
                           
                                  foreach($s_productogalerias as $value){
                                      $imagenes = $imagenes.'<div class="gallery-items" style="display: none;">
                                            <div class="grid-item-holder">
                                                <div class="box-item">
                                                  <a href="'.url('public/backoffice/tienda/'.$tienda->id.'/producto/'.$value->imagen).'" class="gal-link popup-image-detalle">
                                                    <i class="fa fa-search"></i></a>
                                                </div>
                                            </div>
                                        </div>';
                                  }
                      $imagenes = $imagenes.'</div>
                              <script>
                                  $(".lightgallery").lightGallery({
                                      selector: ".lightgallery a.popup-image-detalle",
                                      cssEasing: "cubic-bezier(0.25, 0, 0.25, 1)",
                                      download: false,
                                      loop: false,
                                      counter: false
                                  });
                              </script>';
                }
            }
          
            echo $imagenes;
            ?>
        </div>
        <div class="col-md-6">
            <div class="profile-edit-container">
              <div class="custom-form">
                  <div class="row">
                      <div class="col-md-12">
                          <label>Precio</label>
                          <input type="number" value="{{ $s_producto->precioalpublico }}" disabled>
                      </div>
                      <div class="col-md-12">
                          <label>Cantidad *</label>
                          <input type="number" value="1" min="1" id="cantidadcarritocompra" onclick="redirect_whatsapp()" onkeyup="redirect_whatsapp()">
                      </div>
                      <div class="col-md-12">
                          <label>Total</label>
                          <input type="number" value="{{ $s_producto->precioalpublico }}" disabled="">
                      </div>
                  </div>
              </div>
            </div>
            <div class="profile-edit-container">
              <div class="custom-form">
                  <div class="row">
                      <div class="col-md-6">
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
                )" class="btn transparent-btn float-btn custom-scroll-link" style="width: 100%;margin-right: 10px;font-weight: bold;"><i class="fa fa-check"></i> Agregar a Carrito</a>
                      </div>
                      <div class="col-md-6">
                          <a href="javascript:;" id="redirect_whatsapp" class="btn transparent-btn float-btn custom-scroll-link" style="width: 100%;background-color: #03c100;border-color: #03c100;color: rgb(255 255 255 / 90%);font-weight: bold;" target="_blank"><i class="fa fa-whatsapp"></i> Pedir por Whatsapp</a>
                      </div>
                  </div>
              </div>
            </div>
            
            
            
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <span class="fw-separator"></span>
            <div class="list-single-main-item-title fl-wrap">
                <h3 style="margin-bottom: 0;">Descripción</h3>
            </div>
            <div class="listing-features fl-wrap" style="text-align: left;">
                  {{$s_producto->descripcion}}
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
</div>
<style>
  /*.mx-href {
    float: left !important;
    padding: unset !important;
    margin: unset !important;
    margin-right:2px !important;
  }*/
  /*.list-single-gallery .box-item {
    height: 242px;
}*/
  .list-single-gallery {
    float: none;
}
  .grid-small-pad .grid-item-holder {
    margin-bottom: 10px;
}
</style>
@endsection
@section('tiendascripts')
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
  redirect_whatsapp();
  function redirect_whatsapp(){
    var cantidad = $('#cantidadcarritocompra').val();
    if(cantidad>1){
    $('#redirect_whatsapp').attr('href','https://api.whatsapp.com/send?phone={{ $tienda->codigotelefonicocodigo }}{{ str_replace(' ','',$tienda->numerotelefono) }}&text={{ str_replace(' ','%20','Hola! Deseo adquirir') }} *'+cantidad+'* {{str_replace(' ','%20','productos *'.$s_producto->nombre.'*. '.url($tienda->link.'/producto/'.$s_producto->id)) }}'); 
    }else{
    $('#redirect_whatsapp').attr('href','https://api.whatsapp.com/send?phone={{ $tienda->codigotelefonicocodigo }}{{ str_replace(' ','',$tienda->numerotelefono) }}&text={{ str_replace(' ','%20','Hola! Deseo adquirir') }} *'+cantidad+'* {{str_replace(' ','%20','producto *'.$s_producto->nombre.'*. '.url($tienda->link.'/producto/'.$s_producto->id)) }}'); 
    }   
  }
</script>
@endsection