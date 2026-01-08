<?php
$background = 'background-color:#31353d;';
if(configuracion($tienda->id,'sistema_color')['resultado']=='CORRECTO'){
    $background = 'background-color:'.configuracion($tienda->id,'sistema_color')['valor'].';';
}
?>        
<div class="row">
    <div class="col-md-12">
        <div onclick="cargar_categoria()" class="cont_cabecera" style="float: left;width: 100%;<?php echo $background ?>">
            <div id="numeromesa_texto" class="pedido_cabecera"><i class="fa fa-arrow-left"></i> Atras | {{$categoria->nombre}}</div>
        </div>
    </div>
</div>
<div class="row">
@foreach($productos as $value)
    <div class="col-xs-6 col-sm-6 col-md-6">
        <div class="cont_producto" onclick="cargar_producto('{{$value->id}}','{{$value->nombre}}','{{$value->precioalpublico}}')" style="<?php echo $background ?>">
            <div class="producto_nombre">{{$value->nombre}}</div>
            <div class="producto_precio">{{$value->precioalpublico}}</div>
        </div>
    </div>
@endforeach
<script>
function cargar_producto(idproducto,nombre,precio){
    agregarproducto(
        idproducto,
        nombre,
        precio,
    );
} 
</script>